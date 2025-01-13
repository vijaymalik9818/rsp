<?php

namespace App\Http\Controllers;

use App\Models\Properties;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PHRETS\Configuration;
use PHRETS\Session;
use Illuminate\Support\Facades\Hash;
use Cocur\Slugify\Slugify;
use Illuminate\Support\Facades\Mail;
use App\Mail\ListingAlertEmail;
use Illuminate\Support\Facades\Log;
use Aws\S3\S3Client;

class RETSController extends Controller
{

    private function getRetsConfiguration()
    {
        $config = new Configuration;
        $config->setLoginUrl(env('RETS_LOGIN_URL'))
            ->setUsername(env('RETS_USERNAME'))
            ->setPassword(env('RETS_PASSWORD'))
            ->setRetsVersion(env('RETS_VERSION'));

        return $config;
    }

    public function retrieveData()
    {
        date_default_timezone_set('America/New_York');
        $slugify = new Slugify();
        $newvariableForCount = 0;

        $config = $this->getRetsConfiguration();
        $rets = new Session($config);

        $properties_all_data = 'properties_all_data';
        $file_name = "RetsController.php";
        $curr_mls_id = 1;

        try {
            $connect = $rets->Login();

            $table = "<table border='1'><tr><th>ListingId</th><th>Status</th></tr>";

            $resourceName = 'Property';
            $className = 'Property';
            $last_cron_data = [];

            $cron_tablename = 'properties_cron_log';
            $curr_date = date('Y-m-d H:i:s');
            $values = array(
                'cron_file_name' => $file_name,
                'cron_start_time' => $curr_date,
                'property_class' => $className,
                'mls_no' => $curr_mls_id,
                'steps_completed' => 1
            );

            $curr_log_id = DB::table($cron_tablename)->insertGetId($values);

            $last_cron_data = DB::table($cron_tablename)
                ->where("cron_file_name", $file_name)
                ->where("property_class", $className)
                ->where("mls_no", $curr_mls_id)
                ->where("success", 1)
                ->where("cron_end_time", "<>", "0000-00-00 00:00:00")
                ->where("properties_download_end_time", "<>", '0000-00-00 00:00:00')
                ->orderBy("id", "DESC")
                ->limit(1)->get();

            $start_pull = false;
            $start_pull_time = date("Y-m-d\TH:i:s");

            $property_query_end_time = date("Y-m-d\TH:i:s");

            if (!count($last_cron_data) || empty($last_cron_data)) {

                $default_date = $start_pull_time;
                $start_pull = true;
                $property_query_start_time = $default_date;
                $last_success_cron_end_time = $default_date;

                $query = '(MlsStatus=A,I,P)';
            } else {

                $last_success_cron_end_time = $last_cron_data[0]->properties_download_end_time;

                if ($last_success_cron_end_time == '' || $last_success_cron_end_time == '0000-00-00 00:00:00' || $last_success_cron_end_time == '0000-00-00' || $last_success_cron_end_time == "1000-01-01 00:00:00") {
                    $property_query_start_time = strtotime($start_pull_time, time());
                    $property_query_start_time = date("Y-m-d\TH:i:s", $property_query_start_time);
                } else {
                    $property_query_start_time = date("Y-m-d\TH:i:s", strtotime($last_success_cron_end_time));
                }

                $twentyDaysLater = strtotime("+20 days", strtotime($property_query_start_time));

                if ($twentyDaysLater > time()) {
                    $query = '((MlsStatus=A,I,P),(ModificationTimestamp=' . $property_query_start_time . '+))';
                } else {
                    $property_query_end_time = date("Y-m-d\TH:i:s", $twentyDaysLater);
                    $query = '((MlsStatus=A,I,P),(ModificationTimestamp=' . $property_query_start_time . '-' . $property_query_end_time . '))';
                }
                
            }

            // $query = '((ListingId=A2116879))';
            //$query = '(MlsStatus=A,I,P)';
            //$query = '((MlsStatus=A,I,P),(ModificationTimestamp=2024-09-24T00:00:00+))';

            $cron_update_data = array(
                'properties_download_start_time' => $property_query_start_time,
                'steps_completed' => 2,
                'rets_query' => $query
            );

            DB::table($cron_tablename)->where('id', $curr_log_id)->update($cron_update_data);

            $batchSize = 100;
            $offset = 1;
            $totalResults = $rets->Search(
                $resourceName,
                $className,
                $query,
                [
                    'QueryType' => 'DMQL2',
                    'Count' => 2,
                    'Format' => 'COMPACT-DECODED',
                ]
            );

            $totalCount = $totalResults->getTotalResultsCount();
            $totalPrintCount = $totalCount;
            echo $query . PHP_EOL;
            echo "Total properties for importing are $totalCount" . PHP_EOL;
            //exit;
            $counter = 0;
            $insertCount = 0;
            $updateCount = 0;
            $offset = 0;
            $limit = 100;

            do {
                $cron_update_data = array(
                    'properties_count_from_mls' => $totalCount,
                    'steps_completed' => 3
                );
                DB::table($cron_tablename)->where('id', $curr_log_id)->update($cron_update_data);

                $results = $rets->Search(
                    $resourceName,
                    $className,
                    $query,
                    [
                        'QueryType' => 'DMQL2',
                        'Limit' => $limit,
                        'Offset' => $offset,
                        'Format' => 'COMPACT-DECODED',
                    ]
                );
                $count = count($results);
                echo PHP_EOL . "Offset:" . $offset . ' - ' . $count;
                $offset = $offset + $limit;
                $start = $offset;
                $end = $offset + $count - 1;

                $total_count_from_mls = $results->getTotalResultsCount();
                $s3 = new S3Client([
                    'version' => 'latest',
                    'region' => env('AWS_DEFAULT_REGION'),
                    'credentials' => [
                        'key' => env('AWS_ACCESS_KEY_ID'),
                        'secret' => env('AWS_SECRET_ACCESS_KEY'),
                    ],
                ]);

                $rets_prop_mapping = [
                    "id" => "id",
                    "ListingKeyNumeric" => "ListingKeyNumeric",
                    "BathroomsFull" => "BathroomsFull",
                    "BathroomsHalf" => "BathroomsHalf",
                    "BathroomsTotaltext" => "BathroomsTotaltext",
                    "BedroomsTotal" => "BedroomsTotal",
                    "BelowGradeFinishedArea" => "BelowGradeFinishedArea",
                    "BelowGradeFinishedAreaUnits" => "BelowGradeFinishedAreaUnits",
                    "BuildingAreaTotal" => "BuildingAreaTotal",
                    "City" => "City",
                    "CoListAgentFullName" => "CoListAgentFullName",
                    "CoListAgentKeyNumeric" => "CoListAgentKeyNumeric",
                    "CoListAgentEmail" => "CoListAgentEmail",
                    "CoListAgentDirectPhone" => "CoListAgentDirectPhone",
                    "CoListOfficeName" => "CoListOfficeName",
                    "CoListOfficePhone" => "CoListOfficePhone",
                    "Cooling" => "Cooling",
                    "CountyOrParish" => "CountyOrParish",
                    "DaysOnMarket" => "DaysOnMarket",
                    "Heating" => "Heating",
                    "Latitude" => "Latitude",
                    "ListAgentFullName" => "ListAgentFullName",
                    "ListAgentKeyNumeric" => "ListAgentKeyNumeric",
                    "ListAgentEmail" => "ListAgentEmail",
                    "ListAgentDirectPhone" => "ListAgentDirectPhone",
                    "ListOfficeName" => "ListOfficeName",
                    "ListOfficePhone" => "ListOfficePhone",
                    "ListPrice" => "ListPrice",
                    "Longitude" => "Longitude",
                    "MlsStatus" => "MlsStatus",
                    "PropertySubType" => "PropertySubType",
                    "PropertyType" => "PropertyType",
                    "PublicRemarks" => "PublicRemarks",
                    "StandardStatus" => "StandardStatus",
                    "StateOrProvince" => "StateOrProvince",
                    "StreetDirPrefix" => "StreetDirPrefix",
                    "StreetDirSuffix" => "StreetDirSuffix",
                    "StreetName" => "StreetName",
                    "StreetNumber" => "StreetNumber",
                    "StreetSuffix" => "StreetSuffix",
                    "UnitNumber" => "UnitNumber",
                    "YearBuilt" => "YearBuilt",
                    "AccessibilityFeatures" => "AccessibilityFeatures",
                    "Appliances" => "Appliances",
                    "AssociationAmenities" => "AssociationAmenities",
                    "Basement" => "Basement",
                    "BuilderName" => "BuilderName",
                    "BusinessName" => "BusinessName",
                    "CarportSpaces" => "CarportSpaces",
                    "CommonWalls" => "CommonWalls",
                    "CommunityFeatures" => "CommunityFeatures",
                    "ConstructionMaterials" => "ConstructionMaterials",
                    "ElementarySchoolDistrict" => "ElementarySchoolDistrict",
                    "ExteriorFeatures" => "ExteriorFeatures",
                    "FireplaceFeatures" => "FireplaceFeatures",
                    "FoundationDetails" => "FoundationDetails",
                    "GarageSpaces" => "GarageSpaces",
                    "HighSchoolDistrict" => "HighSchoolDistrict",
                    "InteriorFeatures" => "InteriorFeatures",
                    "LandLeaseAmount" => "LandLeaseAmount",
                    "LeaseTerm" => "LeaseTerm",
                    "Levels" => "Levels",
                    "LotFeatures" => "LotFeatures",
                    "LotSizeAcres" => "LotSizeAcres",
                    "LotSizeSquareFeet" => "LotSizeSquareFeet",
                    "ParkingFeatures" => "ParkingFeatures",
                    "PatioAndPorchFeatures" => "PatioAndPorchFeatures",
                    "PetsAllowed" => "PetsAllowed",
                    "PoolFeatures" => "PoolFeatures",
                    "RoomsTotal" => "RoomsTotal",
                    "Sewer" => "Sewer",
                    "StructureType" => "StructureType",
                    "SubdivisionName" => "SubdivisionName",
                    "TaxLegalDescription" => "TaxLegalDescription",
                    "Utilities" => "Utilities",
                    "WaterfrontFeatures" => "WaterfrontFeatures",
                    "WaterSource" => "WaterSource",
                    "YardSize" => "YardSize",
                    "Zoning" => "Zoning",
                    "BuildingAreaTotalMetres" => "BuildingAreaTotalMetres",
                    "BuildingAreaTotalSF" => "BuildingAreaTotalSF",
                    "DOMIncrementing" => "DOMIncrementing",
                    "FootprintSQFT" => "FootprintSQFT",
                    "LotSizeNotCultivated" => "LotSizeNotCultivated",
                    "LotSizeSeeded" => "LotSizeSeeded",
                    "LotSizeTameHay" => "LotSizeTameHay",
                    "LotSizeTreed" => "LotSizeTreed",
                    "LivingAreaMetres" => "LivingAreaMetres",
                    "LivingAreaSF" => "LivingAreaSF",
                    "MainLevelFinishedAreaMetres" => "MainLevelFinishedAreaMetres",
                    "MainLevelFinishedAreaSF" => "MainLevelFinishedAreaSF",
                    "UpperLevelFinishedAreaMetres" => "UpperLevelFinishedAreaMetres",
                    "UpperLevelFinishedAreaSF" => "UpperLevelFinishedAreaSF",
                    "CondoName" => "CondoName",
                    "BedroomsBelowGrade" => "BedroomsBelowGrade",
                    "CoListOfficeEmail" => "CoListOfficeEmail",
                    "ParkingEnclosed" => "ParkingEnclosed",
                    "URL3DImage" => "URL3DImage",
                    "URLBrochure" => "URLBrochure",
                    "URLSoundByte" => "URLSoundByte",
                    "FrontageFt" => "FrontageFt",
                    "HeatingExpense" => "HeatingExpense",
                    "ListingContractDate" => "ListingContractDate",
                    "ListingId" => "ListingId",
                    "ListingService" => "ListingService",
                    "InternetEntireListingDisplayYN" => "InternetEntireListingDisplayYN",
                    "OtherColumns" => "OtherColumns",
                    "slug_url" => "slug_url",
                    "created_at" => "created_at",
                    "updated_at" => "updated_at",
                    "CoListOfficeKeyNumeric" => "CoListOfficeKeyNumeric",
                    "featured" => "featured",
                    "diamond" => "diamond",
                    "ListOfficeKeyNumeric" => "ListOfficeKeyNumeric",
                    "UnparsedAddress" => "UnparsedAddress",
                    "TransactionType" => "TransactionType",
                    "DOMDate" => "DOMDate",
                    "ModificationTimestamp" => "ModificationTimestamp",
                    "LeaseMeasure" => "LeaseMeasure",
                    "LeaseAmount" => "LeaseAmount",
                    "LeaseAmountFrequency" => "LeaseAmountFrequency",
                    "PostalCode" => "PostalCode",
                    "ArchitecturalStyle" => "ArchitecturalStyle",
                    "StoriesTotal" => "StoriesTotal",
                ];
                
                foreach ($results as $record) {
                    echo "\n remaning count : " . $totalPrintCount . ', done count : ' . $newvariableForCount;
                    $newvariableForCount++;
                    $totalPrintCount--;
                    try {
                        
                        $values = $record->toArray();
                        $data = [];
                        
                        foreach ($rets_prop_mapping as $rets_key => $db_column) {
                            if (isset($values[$rets_key])) {
                                $data[$db_column] = $values[$rets_key] === '' ? null : $values[$rets_key];
                            }
                        }
                        
                        $table .= "<tr><td>{$data['ListingKeyNumeric']}</td><td>{$data['MlsStatus']}</td></tr>";
                        $data['OtherColumns'] = json_encode(array_diff_key($values, $data));
                        if($data['UnparsedAddress']==null || $data['UnparsedAddress']=='')
                        {
                            $data['slug_url'] = null;
                        }
                        else{
                            $slug_url = $slugify->slugify($data['UnparsedAddress'] . '-' . $data['ListingId']);
                            $data['slug_url'] = $slug_url;
                        }
                        
                        $data['is_active'] = 1;
                        
                        
                        if (in_array($data['ListOfficeKeyNumeric'], ['1297353', '1298083']) && $data['ListPrice'] >= 1000000) {
                            $data['diamond'] = 1;
                        } else if (in_array($data['ListOfficeKeyNumeric'], ['1297353', '1298083'])) {
                            $data['featured'] = 1;
                        }
                        
                        $existingRecord = DB::table($properties_all_data)->where('ListingKeyNumeric', $data['ListingKeyNumeric'])->first();
                        $listingId = $data['ListingKeyNumeric'];
                        
                        $roomsQuery = "(ListingKeyNumeric={$data['ListingKeyNumeric']})";
                            $roomsResults = $rets->Search(
                                'PropertyRooms',
                                'PropertyRooms',
                                $roomsQuery,
                                [
                                    'QueryType' => 'DMQL2',
                                    'Format' => 'COMPACT-DECODED',
                                    ]
                                );
                                
                                $rooms = [];
                                foreach ($roomsResults as $room) {
                                    $roomData = $room->toArray();
                                    $rooms[] = $roomData;
                                }
                                $data['property_rooms'] = json_encode($rooms);

                        if ($existingRecord) {
                            $shouldUpdateImage = false;
                            $otherColumns = json_decode($existingRecord->OtherColumns, true);
                            $imageModified = $otherColumns['PhotosChangeTimestamp'];
                            
                            if ($imageModified != $record['PhotosChangeTimestamp']) {
                                $shouldUpdateImage = true;
                            }

                            if ($shouldUpdateImage) {
                                $imageObject = $rets->GetObject('Property', 'XLarge', $listingId, 1);
                                if (!empty($imageObject)) {
                                    $photo = $imageObject[0];
                                    $img_n = $photo->getContent();
                                    $img_n_id = $photo->getContentId();
                                    if ($img_n_id !== null) {
                                        try {
                                            $filename = "photo-$listingId-0.jpeg";
                                            $key = "property-images-first/{$listingId}/{$filename}";
                                            $result = $s3->putObject([
                                                'Bucket' => env('AWS_BUCKET'),
                                                'Key' => $key,
                                                'Body' => $img_n,
                                            ]);
                                            $imageUrl = $result['ObjectURL'];
                                            
                                            $data['image_url'] = $imageUrl;
                                            DB::table('properties_all_data')
                                            ->where('ListingKeyNumeric', $listingId)
                                            ->update(['is_images_downloaded' => 0]);
                                        } catch (\Exception $exception) {
                                            Log::error("Failed to save property photo for ListingKeyNumeric {$listingId}: " . $exception->getMessage());
                                            //continue;
                                        }
                                    }
                                }
                            }
                            
                            DB::table($properties_all_data)->where('ListingKeyNumeric', $data['ListingKeyNumeric'])->update($data);
                            //DB::table($properties_all_data)->where('ListingKeyNumeric', $data['ListingKeyNumeric'])->update(['is_active'=>'1']);
                            //$updateCount++;
                            echo " : Property with ListingId {$data['ListingKeyNumeric']} has been updated." . PHP_EOL;
                        } else {
                           
                            $imageObject = $rets->GetObject('Property', 'XLarge', $listingId, 1);
                            if (!empty($imageObject)) {
                                $photo = $imageObject[0];
                                $img_n = $photo->getContent();
                                $img_n_id = $photo->getContentId();
                                if ($img_n_id !== null) {
                                    try {
                                        $filename = "photo-$listingId-0.jpeg";
                                        $key = "property-images-first/{$listingId}/{$filename}";
                                        $result = $s3->putObject([
                                            'Bucket' => env('AWS_BUCKET'),
                                            'Key' => $key,
                                            'Body' => $img_n,
                                        ]);
                                        $imageUrl = $result['ObjectURL'];

                                        $data['image_url'] = $imageUrl;
                                    } catch (\Exception $exception) {
                                        Log::error("Failed to save property photo for ListingKeyNumeric {$listingId}: " . $exception->getMessage());
                                        //continue;
                                    }
                                }
                            }

                            DB::table($properties_all_data)->insert($data);
                            $insertCount++;
                            echo " : Property with ListingId {$data['ListingKeyNumeric']} has been inserted." . PHP_EOL;
                        }

                        $counter++;
                        if ($counter % 100 == 0 || $counter == $count) {
                            echo "Properties from $start to " . min($start + $counter - 1, $total_count_from_mls) . " imported." . PHP_EOL;
                            $start += $counter;
                        }
                    } catch (\Exception $e) {
                        Log::error("Error processing property with ListingKeyNumeric {$record->get('ListingKeyNumeric')}: " . $e->getMessage());

                    }
                }

            } while (count($results) > 0);

            $table .= "</table>";

            $cron_update_data4 = array(
                'cron_end_time' => date('Y-m-d H:i:s'),
                'steps_completed' => 4,
                'success' => 1,
                'properties_count_from_mls' => $totalCount,
                'property_inserted' => $insertCount,
                'property_updated' => $updateCount,
                'properties_download_end_time' => $property_query_end_time,
                'properties_count_actual_downloaded' => $counter
            );
            $step4 = DB::table($cron_tablename)
                ->where('id', $curr_log_id)
                ->update($cron_update_data4);
            echo "Listings Have Been Downloaded Successfully";

            // $this->checkSoldProperties();
        } catch (\Exception $e) {

            Log::error("Error: " . $e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }

        $rets->Disconnect();

    }

public function retrieveData_ALLNEW()
    {
        date_default_timezone_set('America/New_York');
        $slugify = new Slugify();
        $newvariableForCount = 0;

        $config = $this->getRetsConfiguration();
        $rets = new Session($config);

        $properties_all_data = 'properties_all_data';
        $file_name = "RetsController.php";
        $curr_mls_id = 1;

        try {
            $connect = $rets->Login();

            $table = "<table border='1'><tr><th>ListingId</th><th>Status</th></tr>";

            $resourceName = 'Property';
            $className = 'Property';
            $last_cron_data = [];

            $cron_tablename = 'properties_cron_log';
            $curr_date = date('Y-m-d H:i:s');
            $values = array(
                'cron_file_name' => $file_name,
                'cron_start_time' => $curr_date,
                'property_class' => $className,
                'mls_no' => $curr_mls_id,
                'steps_completed' => 1
            );

            $curr_log_id = DB::table($cron_tablename)->insertGetId($values);

            $last_cron_data = DB::table($cron_tablename)
                ->where("cron_file_name", $file_name)
                ->where("property_class", $className)
                ->where("mls_no", $curr_mls_id)
                ->where("success", 1)
                ->where("cron_end_time", "<>", "0000-00-00 00:00:00")
                ->where("properties_download_end_time", "<>", '0000-00-00 00:00:00')
                ->orderBy("id", "DESC")
                ->limit(1)->get();

            $start_pull = false;
            $start_pull_time = date("Y-m-d\TH:i:s");

            $property_query_end_time = date("Y-m-d\TH:i:s");

            if (!count($last_cron_data) || empty($last_cron_data)) {

                $default_date = $start_pull_time;
                $start_pull = true;
                $property_query_start_time = $default_date;
                $last_success_cron_end_time = $default_date;

                $query = '(MlsStatus=A,I,P)';
            } else {

                $last_success_cron_end_time = $last_cron_data[0]->properties_download_end_time;

                if ($last_success_cron_end_time == '' || $last_success_cron_end_time == '0000-00-00 00:00:00' || $last_success_cron_end_time == '0000-00-00' || $last_success_cron_end_time == "1000-01-01 00:00:00") {
                    $property_query_start_time = strtotime($start_pull_time, time());
                    $property_query_start_time = date("Y-m-d\TH:i:s", $property_query_start_time);
                } else {
                    $property_query_start_time = date("Y-m-d\TH:i:s", strtotime($last_success_cron_end_time));
                }

                $twentyDaysLater = strtotime("+20 days", strtotime($property_query_start_time));

                if ($twentyDaysLater > time()) {
                    $query = '((MlsStatus=A,I,P),(ModificationTimestamp=' . $property_query_start_time . '+))';
                } else {
                    $property_query_end_time = date("Y-m-d\TH:i:s", $twentyDaysLater);
                    $query = '((MlsStatus=A,I,P),(ModificationTimestamp=' . $property_query_start_time . '-' . $property_query_end_time . '))';
                }
                
            }

            // $query = '((ListingId=A2116879))';
             $query = '(MlsStatus=A,I,P)';
            //$query = '((MlsStatus=A,I,P),(ModificationTimestamp=2024-09-24T00:00:00+))';

            $cron_update_data = array(
                'properties_download_start_time' => $property_query_start_time,
                'steps_completed' => 2,
                'rets_query' => $query
            );

            DB::table($cron_tablename)->where('id', $curr_log_id)->update($cron_update_data);

            $batchSize = 100;
            $offset = 1;
            $totalResults = $rets->Search(
                $resourceName,
                $className,
                $query,
                [
                    'QueryType' => 'DMQL2',
                    'Count' => 2,
                    'Format' => 'COMPACT-DECODED',
                ]
            );

            $totalCount = $totalResults->getTotalResultsCount();
            $totalPrintCount = $totalCount;
            echo $query . PHP_EOL;
            echo "Total properties for importing are $totalCount" . PHP_EOL;

            $counter = 0;
            $insertCount = 0;
            $updateCount = 0;
            $offset = 0;
            $limit = 100;

            do {
                $cron_update_data = array(
                    'properties_count_from_mls' => $totalCount,
                    'steps_completed' => 3
                );
                DB::table($cron_tablename)->where('id', $curr_log_id)->update($cron_update_data);

                $results = $rets->Search(
                    $resourceName,
                    $className,
                    $query,
                    [
                        'QueryType' => 'DMQL2',
                        'Limit' => $limit,
                        'Offset' => $offset,
                        'Format' => 'COMPACT-DECODED',
                    ]
                );
                $count = count($results);
                echo PHP_EOL . "Offset:" . $offset . ' - ' . $count;
                $offset = $offset + $limit;
                $start = $offset;
                $end = $offset + $count - 1;

                $total_count_from_mls = $results->getTotalResultsCount();
                $s3 = new S3Client([
                    'version' => 'latest',
                    'region' => env('AWS_DEFAULT_REGION'),
                    'credentials' => [
                        'key' => env('AWS_ACCESS_KEY_ID'),
                        'secret' => env('AWS_SECRET_ACCESS_KEY'),
                    ],
                ]);

                $rets_prop_mapping = [
                    "id" => "id",
                    "ListingKeyNumeric" => "ListingKeyNumeric",
                    "BathroomsFull" => "BathroomsFull",
                    "BathroomsHalf" => "BathroomsHalf",
                    "BathroomsTotaltext" => "BathroomsTotaltext",
                    "BedroomsTotal" => "BedroomsTotal",
                    "BelowGradeFinishedArea" => "BelowGradeFinishedArea",
                    "BelowGradeFinishedAreaUnits" => "BelowGradeFinishedAreaUnits",
                    "BuildingAreaTotal" => "BuildingAreaTotal",
                    "City" => "City",
                    "CoListAgentFullName" => "CoListAgentFullName",
                    "CoListAgentKeyNumeric" => "CoListAgentKeyNumeric",
                    "CoListAgentEmail" => "CoListAgentEmail",
                    "CoListAgentDirectPhone" => "CoListAgentDirectPhone",
                    "CoListOfficeName" => "CoListOfficeName",
                    "CoListOfficePhone" => "CoListOfficePhone",
                    "Cooling" => "Cooling",
                    "CountyOrParish" => "CountyOrParish",
                    "DaysOnMarket" => "DaysOnMarket",
                    "Heating" => "Heating",
                    "Latitude" => "Latitude",
                    "ListAgentFullName" => "ListAgentFullName",
                    "ListAgentKeyNumeric" => "ListAgentKeyNumeric",
                    "ListAgentEmail" => "ListAgentEmail",
                    "ListAgentDirectPhone" => "ListAgentDirectPhone",
                    "ListOfficeName" => "ListOfficeName",
                    "ListOfficePhone" => "ListOfficePhone",
                    "ListPrice" => "ListPrice",
                    "Longitude" => "Longitude",
                    "MlsStatus" => "MlsStatus",
                    "PropertySubType" => "PropertySubType",
                    "PropertyType" => "PropertyType",
                    "PublicRemarks" => "PublicRemarks",
                    "StandardStatus" => "StandardStatus",
                    "StateOrProvince" => "StateOrProvince",
                    "StreetDirPrefix" => "StreetDirPrefix",
                    "StreetDirSuffix" => "StreetDirSuffix",
                    "StreetName" => "StreetName",
                    "StreetNumber" => "StreetNumber",
                    "StreetSuffix" => "StreetSuffix",
                    "UnitNumber" => "UnitNumber",
                    "YearBuilt" => "YearBuilt",
                    "AccessibilityFeatures" => "AccessibilityFeatures",
                    "Appliances" => "Appliances",
                    "AssociationAmenities" => "AssociationAmenities",
                    "Basement" => "Basement",
                    "BuilderName" => "BuilderName",
                    "BusinessName" => "BusinessName",
                    "CarportSpaces" => "CarportSpaces",
                    "CommonWalls" => "CommonWalls",
                    "CommunityFeatures" => "CommunityFeatures",
                    "ConstructionMaterials" => "ConstructionMaterials",
                    "ElementarySchoolDistrict" => "ElementarySchoolDistrict",
                    "ExteriorFeatures" => "ExteriorFeatures",
                    "FireplaceFeatures" => "FireplaceFeatures",
                    "FoundationDetails" => "FoundationDetails",
                    "GarageSpaces" => "GarageSpaces",
                    "HighSchoolDistrict" => "HighSchoolDistrict",
                    "InteriorFeatures" => "InteriorFeatures",
                    "LandLeaseAmount" => "LandLeaseAmount",
                    "LeaseTerm" => "LeaseTerm",
                    "Levels" => "Levels",
                    "LotFeatures" => "LotFeatures",
                    "LotSizeAcres" => "LotSizeAcres",
                    "LotSizeSquareFeet" => "LotSizeSquareFeet",
                    "ParkingFeatures" => "ParkingFeatures",
                    "PatioAndPorchFeatures" => "PatioAndPorchFeatures",
                    "PetsAllowed" => "PetsAllowed",
                    "PoolFeatures" => "PoolFeatures",
                    "RoomsTotal" => "RoomsTotal",
                    "Sewer" => "Sewer",
                    "StructureType" => "StructureType",
                    "SubdivisionName" => "SubdivisionName",
                    "TaxLegalDescription" => "TaxLegalDescription",
                    "Utilities" => "Utilities",
                    "WaterfrontFeatures" => "WaterfrontFeatures",
                    "WaterSource" => "WaterSource",
                    "YardSize" => "YardSize",
                    "Zoning" => "Zoning",
                    "BuildingAreaTotalMetres" => "BuildingAreaTotalMetres",
                    "BuildingAreaTotalSF" => "BuildingAreaTotalSF",
                    "DOMIncrementing" => "DOMIncrementing",
                    "FootprintSQFT" => "FootprintSQFT",
                    "LotSizeNotCultivated" => "LotSizeNotCultivated",
                    "LotSizeSeeded" => "LotSizeSeeded",
                    "LotSizeTameHay" => "LotSizeTameHay",
                    "LotSizeTreed" => "LotSizeTreed",
                    "LivingAreaMetres" => "LivingAreaMetres",
                    "LivingAreaSF" => "LivingAreaSF",
                    "MainLevelFinishedAreaMetres" => "MainLevelFinishedAreaMetres",
                    "MainLevelFinishedAreaSF" => "MainLevelFinishedAreaSF",
                    "UpperLevelFinishedAreaMetres" => "UpperLevelFinishedAreaMetres",
                    "UpperLevelFinishedAreaSF" => "UpperLevelFinishedAreaSF",
                    "CondoName" => "CondoName",
                    "BedroomsBelowGrade" => "BedroomsBelowGrade",
                    "CoListOfficeEmail" => "CoListOfficeEmail",
                    "ParkingEnclosed" => "ParkingEnclosed",
                    "URL3DImage" => "URL3DImage",
                    "URLBrochure" => "URLBrochure",
                    "URLSoundByte" => "URLSoundByte",
                    "FrontageFt" => "FrontageFt",
                    "HeatingExpense" => "HeatingExpense",
                    "ListingContractDate" => "ListingContractDate",
                    "ListingId" => "ListingId",
                    "ListingService" => "ListingService",
                    "InternetEntireListingDisplayYN" => "InternetEntireListingDisplayYN",
                    "OtherColumns" => "OtherColumns",
                    "slug_url" => "slug_url",
                    "created_at" => "created_at",
                    "updated_at" => "updated_at",
                    "CoListOfficeKeyNumeric" => "CoListOfficeKeyNumeric",
                    "featured" => "featured",
                    "diamond" => "diamond",
                    "ListOfficeKeyNumeric" => "ListOfficeKeyNumeric",
                    "UnparsedAddress" => "UnparsedAddress",
                    "TransactionType" => "TransactionType",
                    "DOMDate" => "DOMDate",
                    "ModificationTimestamp" => "ModificationTimestamp",
                    "LeaseMeasure" => "LeaseMeasure",
                    "LeaseAmount" => "LeaseAmount",
                    "LeaseAmountFrequency" => "LeaseAmountFrequency",
                    "PostalCode" => "PostalCode",
                    "ArchitecturalStyle" => "ArchitecturalStyle",
                    "StoriesTotal" => "StoriesTotal",
                ];
                
                foreach ($results as $record) {
                    echo "\n remaning count : " . $totalPrintCount . ', done count : ' . $newvariableForCount;
                    $newvariableForCount++;
                    $totalPrintCount--;
                    try {
                        
                        $values = $record->toArray();
                        $data = [];
                        
                        foreach ($rets_prop_mapping as $rets_key => $db_column) {
                            if (isset($values[$rets_key])) {
                                $data[$db_column] = $values[$rets_key] === '' ? null : $values[$rets_key];
                            }
                        }
                        
                        $table .= "<tr><td>{$data['ListingKeyNumeric']}</td><td>{$data['MlsStatus']}</td></tr>";
                        $data['OtherColumns'] = json_encode(array_diff_key($values, $data));
                        if($data['UnparsedAddress']==null || $data['UnparsedAddress']=='')
                        {
                            $data['slug_url'] = null;
                        }
                        else{
                            $slug_url = $slugify->slugify($data['UnparsedAddress'] . '-' . $data['ListingId']);
                            $data['slug_url'] = $slug_url;
                        }
                        
                        $data['is_active'] = 1;
                        
                        
                        if (in_array($data['ListOfficeKeyNumeric'], ['1297353', '1298083']) && $data['ListPrice'] >= 1000000) {
                            $data['diamond'] = 1;
                        } else if (in_array($data['ListOfficeKeyNumeric'], ['1297353', '1298083'])) {
                            $data['featured'] = 1;
                        }
                        
                        $existingRecord = DB::table($properties_all_data)->where('ListingKeyNumeric', $data['ListingKeyNumeric'])->first();
                        $listingId = $data['ListingKeyNumeric'];
                        
                        // $roomsQuery = "(ListingKeyNumeric={$data['ListingKeyNumeric']})";
                        // $roomsResults = $rets->Search(
                        //     'PropertyRooms',
                        //     'PropertyRooms',
                        //     $roomsQuery,
                        //     [
                        //         'QueryType' => 'DMQL2',
                        //         'Format' => 'COMPACT-DECODED',
                        //         ]
                        //     );
                            
                        //     $rooms = [];
                        //     foreach ($roomsResults as $room) {
                        //         $roomData = $room->toArray();
                        //         $rooms[] = $roomData;
                        //     }
                        //     $data['property_rooms'] = json_encode($rooms);

                        if ($existingRecord) {
                            /*$shouldUpdateImage = false;
                            $otherColumns = json_decode($existingRecord->OtherColumns, true);
                            $imageModified = $otherColumns['PhotosChangeTimestamp'];
                            
                            if ($imageModified != $record['PhotosChangeTimestamp']) {
                                $shouldUpdateImage = true;
                            }

                            if ($shouldUpdateImage) {
                                $imageObject = $rets->GetObject('Property', 'XLarge', $listingId, 1);
                                if (!empty($imageObject)) {
                                    $photo = $imageObject[0];
                                    $img_n = $photo->getContent();
                                    $img_n_id = $photo->getContentId();
                                    if ($img_n_id !== null) {
                                        try {
                                            $filename = "photo-$listingId-0.jpeg";
                                            $key = "property-images-first/{$listingId}/{$filename}";
                                            $result = $s3->putObject([
                                                'Bucket' => env('AWS_BUCKET'),
                                                'Key' => $key,
                                                'Body' => $img_n,
                                            ]);
                                            $imageUrl = $result['ObjectURL'];
                                            
                                            $data['image_url'] = $imageUrl;
                                            DB::table('properties_all_data')
                                            ->where('ListingKeyNumeric', $listingId)
                                            ->update(['is_images_downloaded' => 0]);
                                        } catch (\Exception $exception) {
                                            Log::error("Failed to save property photo for ListingKeyNumeric {$listingId}: " . $exception->getMessage());
                                            //continue;
                                        }
                                    }
                                }
                            }
                            // dd('yes');
                            DB::table($properties_all_data)->where('ListingKeyNumeric', $data['ListingKeyNumeric'])->update($data);*/
                            DB::table($properties_all_data)->where('ListingKeyNumeric', $data['ListingKeyNumeric'])->update(['is_active'=>'1']);
                            $updateCount++;
                            echo " : Property with ListingId {$data['ListingKeyNumeric']} has been updated." . PHP_EOL;
                        } else {
                            /*delete from here*/
                            $roomsQuery = "(ListingKeyNumeric={$data['ListingKeyNumeric']})";
                        $roomsResults = $rets->Search(
                            'PropertyRooms',
                            'PropertyRooms',
                            $roomsQuery,
                            [
                                'QueryType' => 'DMQL2',
                                'Format' => 'COMPACT-DECODED',
                                ]
                            );
                            
                            $rooms = [];
                            foreach ($roomsResults as $room) {
                                $roomData = $room->toArray();
                                $rooms[] = $roomData;
                            }
                            $data['property_rooms'] = json_encode($rooms);
                            /*end delete*/
                           
                            
                            $imageObject = $rets->GetObject('Property', 'XLarge', $listingId, 1);
                            if (!empty($imageObject)) {
                                $photo = $imageObject[0];
                                $img_n = $photo->getContent();
                                $img_n_id = $photo->getContentId();
                                if ($img_n_id !== null) {
                                    try {
                                        $filename = "photo-$listingId-0.jpeg";
                                        $key = "property-images-first/{$listingId}/{$filename}";
                                        $result = $s3->putObject([
                                            'Bucket' => env('AWS_BUCKET'),
                                            'Key' => $key,
                                            'Body' => $img_n,
                                        ]);
                                        $imageUrl = $result['ObjectURL'];

                                        $data['image_url'] = $imageUrl;
                                    } catch (\Exception $exception) {
                                        Log::error("Failed to save property photo for ListingKeyNumeric {$listingId}: " . $exception->getMessage());
                                        //continue;
                                    }
                                }
                            }

                            DB::table($properties_all_data)->insert($data);
                            $insertCount++;
                            echo " : Property with ListingId {$data['ListingKeyNumeric']} has been inserted." . PHP_EOL;
                        }

                        $counter++;
                        if ($counter % 100 == 0 || $counter == $count) {
                            echo "Properties from $start to " . min($start + $counter - 1, $total_count_from_mls) . " imported." . PHP_EOL;
                            $start += $counter;
                        }
                    } catch (\Exception $e) {
                        Log::error("Error processing property with ListingKeyNumeric {$record->get('ListingKeyNumeric')}: " . $e->getMessage());

                    }
                }

            } while (count($results) > 0);

            $table .= "</table>";

            $cron_update_data4 = array(
                'cron_end_time' => date('Y-m-d H:i:s'),
                'steps_completed' => 4,
                'success' => 1,
                'properties_count_from_mls' => $totalCount,
                'property_inserted' => $insertCount,
                'property_updated' => $updateCount,
                'properties_download_end_time' => $property_query_end_time,
                'properties_count_actual_downloaded' => $counter
            );
            $step4 = DB::table($cron_tablename)
                ->where('id', $curr_log_id)
                ->update($cron_update_data4);
            echo "Listings Have Been Downloaded Successfully";

            // $this->checkSoldProperties();
        } catch (\Exception $e) {

            Log::error("Error: " . $e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }

        $rets->Disconnect();

    }

    public function checkSoldProperties()
    {//return true;
        $properties_all_data = 'properties_all_data';
        $property_sold_data = 'property_sold_data';
        $cron_tablename = 'properties_cron_log';

        date_default_timezone_set('America/New_York');

        echo "Initializing configuration and session..." . PHP_EOL;
        $config = $this->getRetsConfiguration();
        $rets = new Session($config);

        $file_name = "RetsController.php";
        $curr_mls_id = 1;

        try {
            echo "Logging in to RETS server..." . PHP_EOL;
            $connect = $rets->Login();

            echo "Setting is_active to 0 for all properties..." . PHP_EOL;

            $resourceName = 'Property';
            $className = 'Property';

            $query = '((MlsStatus=A,I,P))';
            $offset = 0;
            $limit = 100;
            $totalPropertiesRetrieved = 0;
            $updatedCount = 0;

            echo "Fetching total property count..." . PHP_EOL;
            $initialResults = $rets->Search(
                $resourceName,
                $className,
                $query,
                [
                    'QueryType' => 'DMQL2',
                    'Limit' => 1,
                    'Format' => 'COMPACT-DECODED',
                    'Select' => 'ListingKeyNumeric,MlsStatus'
                ]
            );
            $totalCountFromMls = $initialResults->getTotalResultsCount();
            echo "Total properties from MLS: $totalCountFromMls" . PHP_EOL;

            if ($totalCountFromMls < 6000) {
                Log::warning("Total properties from MLS is less than 6,000: $totalCountFromMls");
                echo "Total properties from MLS is less than 6,000. Exiting..." . PHP_EOL;
                return response()->json(['error' => 'Total properties from MLS is less than 6,000'], 500);
            }

            DB::table($properties_all_data)->update(['is_active' => 0]);

            echo "Starting property status check..." . PHP_EOL;
            do {
                echo "Querying RETS server for properties (Offset: $offset, Limit: $limit)..." . PHP_EOL;
                $results = $rets->Search(
                    $resourceName,
                    $className,
                    $query,
                    [
                        'QueryType' => 'DMQL2',
                        'Limit' => $limit,
                        'Offset' => $offset,
                        'Format' => 'COMPACT-DECODED',
                        'Select' => 'ListingKeyNumeric,MlsStatus'
                    ]
                );
                $count = count($results);
                echo PHP_EOL . "Offset:" . $offset . ' - ' . $count;
                $offset += $limit;
                $totalPropertiesRetrieved += $count;

                echo "Processing $count properties..." . PHP_EOL;
                foreach ($results as $record) {
                    try {
                        $values = $record->toArray();
                        $listingId = $values['ListingKeyNumeric'];
                        $mlsStatus = $values['MlsStatus'];

                        $existingRecord = DB::table($properties_all_data)->where('ListingKeyNumeric', $listingId)->first();
                        if ($existingRecord) {
                            DB::table($properties_all_data)->where('ListingKeyNumeric', $listingId)->update(['is_active' => 1]);
                            $updatedCount++;
                            echo "Updated property with ListingKeyNumeric: $listingId" . PHP_EOL;
                        }
                    } catch (\Exception $e) {
                        Log::error("Error processing property with ListingKeyNumeric {$record->get('ListingKeyNumeric')}: " . $e->getMessage());
                        continue;
                    }
                }
            } while ($count > 0);

            echo "Active properties checked and updated: $updatedCount" . PHP_EOL;

            echo "Calling deleteSoldProperties function..." . PHP_EOL;
            $this->deleteSoldProperties();

        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            echo "Error occurred: " . $e->getMessage() . PHP_EOL;
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteSoldProperties()
    {
        $properties_all_data = 'properties_all_data';
        $property_sold_data = 'property_sold_data';
        $property_images = 'property_images';

        try {
            echo "Checking number of inactive properties..." . PHP_EOL;
            $inactivePropertiesCount = DB::table($properties_all_data)
                ->where('mls_type', 0)
                ->where('is_active', 0)
                ->count();

            if ($inactivePropertiesCount >= 6000) {
                Log::warning("Number of inactive properties is 6,000 or more: $inactivePropertiesCount");
                echo "Number of inactive properties is 15,000 or more. Exiting..." . PHP_EOL;
                return response()->json(['error' => 'Number of inactive properties is 6,000 or more'], 500);
            }

            echo "Fetching inactive properties for deletion..." . PHP_EOL;
            $inactiveProperties = DB::table($properties_all_data)
                ->where('mls_type', 0)
                ->where('is_active', 0)
                ->select('ListingKeyNumeric', 'UnparsedAddress', 'ListingId', 'ListPrice')
                ->get();

            $deletedCount = 0;
            $totalProperties = count($inactiveProperties);

            $s3 = new S3Client([
                'version' => 'latest',
                'region' => env('AWS_DEFAULT_REGION'),
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            foreach ($inactiveProperties as $property) {
                echo "Deleting property with ListingKeyNumeric: $property->ListingKeyNumeric" . PHP_EOL;

                DB::table($property_sold_data)->updateOrInsert(
                    ['ListingKeyNumeric' => $property->ListingKeyNumeric],
                    [
                        'UnparsedAddress' => $property->UnparsedAddress,
                        'ListingId' => $property->ListingId,
                        'ListPrice' => $property->ListPrice,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $images = DB::table($property_images)
                    ->where('listingid', $property->ListingKeyNumeric)
                    ->pluck('image_url');

                foreach ($images as $image_url) {
                    $existingKey = str_replace(env('AWS_BUCKET_URL') . '/', '', $image_url);
                    try {
                        $s3->deleteObject([
                            'Bucket' => env('AWS_BUCKET'),
                            'Key' => $existingKey,
                        ]);
                        echo "Deleted image from S3: {$existingKey}" . PHP_EOL;
                    } catch (\Exception $e) {
                        Log::error("Failed to delete image from S3 for ListingKeyNumeric {$property->ListingKeyNumeric}: " . $e->getMessage());
                    }
                }

                DB::table($property_images)
                    ->where('listingid', $property->ListingKeyNumeric)
                    ->delete();

                DB::table($properties_all_data)
                    ->where('ListingKeyNumeric', $property->ListingKeyNumeric)
                    ->delete();

                $deletedCount++;
                $remainingCount = $totalProperties - $deletedCount;
                echo "Remaining count: $remainingCount, Done count: $deletedCount : Property with ListingId {$property->ListingId} has been deleted." . PHP_EOL;
            }

            echo "Deleted inactive properties: $deletedCount" . PHP_EOL;

        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function retrieveMemberData()
    {
        date_default_timezone_set('America/New_York');

        $config = new Configuration;
        $config = $this->getRetsConfiguration();

        $rets = new Session($config);

        try {
            $connect = $rets->Login();
            $system = $rets->GetSystemMetadata();
            $resources = $system->getResources();

            foreach ($resources as $resource) {
                $resourceName = $resource->getResourceID();
                if ($resourceName == 'Member') {
                    $classes = $resource->getClasses();

                    foreach ($classes as $class) {
                        $className = $class->getClassName();

                        $query = '(MemberStatus=A),(OfficeKeyNumeric=1297353,1298083)';

                        $results = $rets->Search(
                            $resourceName,
                            $className,
                            $query,
                            [
                                'QueryType' => 'DMQL2',
                                'Count' => 1,
                                'Format' => 'COMPACT-DECODED',
                            ]
                        );

                        foreach ($results as $record) {
                            $values = $record->toArray();

                            $officePhone = null;
                            $officeKey = $values['OfficeKeyNumeric'];
                            $officeQuery = "(OfficeKeyNumeric={$officeKey})";

                            $officeResults = $rets->Search(
                                'Office',
                                'Office',
                                $officeQuery,
                                [
                                    'QueryType' => 'DMQL2',
                                    'Count' => 1,
                                    'Format' => 'COMPACT-DECODED',
                                ]
                            );

                            foreach ($officeResults as $officeRecord) {
                                $officeValues = $officeRecord->toArray();
                                $officePhone = $officeValues['OfficePhone'] ?? null;
                            }

                            $name = $values['MemberFullName'];
                            $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
                            $slug = $baseSlug;

                            $existingUsersCount = User::where('slug_url', $baseSlug)->count();
                            if ($existingUsersCount > 0) {

                                $i = 1;
                                do {
                                    $slug = $baseSlug . '-' . $i;
                                    $i++;
                                } while (User::where('slug_url', $slug)->exists());
                            }

                            $phone = !empty($values['MemberDirectPhone']) ? str_replace('-', '', $values['MemberDirectPhone']) : null;

                            if ($phone === '') {
                                $phone = null;
                            }

                            $userData = [
                                'name' => $values['MemberFullName'],
                                'email' => $values['MemberEmail'],
                                'status' => $values['MemberStatus'] === 'Active' ? 1 : 0,
                                'fax_no' => $values['MemberFax'],
                                'mls_id' => $values['MemberMlsId'],
                                'phone' => $phone,
                                'facebook' => $values['WebFacebook'],
                                'instagram' => $values['WebInstagram'],
                                'twitter' => $values['WebTwitter'],
                                'linkedin' => $values['WebLinkedIn'],
                                'language' => $values['MemberLanguages'],
                                'city' => $values['MemberCity'],
                                'state' => $values['MemberStateOrProvince'],
                                'position' => $values['MemberType'] !== null ? $values['MemberType'] : 'Agent',
                                'password' => Hash::make($phone),
                                'agent_key' => $values['MemberKeyNumeric'],
                                'office_key' => $values['OfficeKeyNumeric'],
                                'office_no' => $officePhone,
                                'slug_url' => $slug
                            ];

                            $objects = $rets->GetObject($resourceName, 'AgentPhoto', $values['MemberKeyNumeric'], '*');

                            $photo = $objects[0];
                            $img_n = $photo->getContent();
                            $img_n_id = $photo->getContentId();

                            if ($img_n_id !== null) {
                                try {
                                    $img_dir = "public/images/Photo-" . $values['MemberMlsId'] . ".jpeg";
                                    $result = Storage::disk('local')->put($img_dir, $img_n);
                                    $img_dir = str_replace("public", "storage", $img_dir);
                                    $userData['profile_picture'] = $img_dir;
                                } catch (\Exception $exception) {
                                    return response("Failed to save agent photo", 500);
                                }
                            } else {
                                $userData['profile_picture'] = null;
                            }

                            $user = User::updateOrCreate(['mls_id' => $userData['mls_id']], $userData);
                            if ($user->wasRecentlyCreated) {
                                echo "User with MLS ID {$userData['mls_id']} has been inserted." . PHP_EOL;
                            } else {
                                echo "User with MLS ID {$userData['mls_id']} has been updated." . PHP_EOL;
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            fwrite(STDERR, "Error: " . $e->getMessage() . PHP_EOL);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storePropertyImages()
    {
        try {
            date_default_timezone_set('America/New_York');

            $config = new Configuration;
            $config = $this->getRetsConfiguration();
            $rets = new Session($config);

            $connect = $rets->Login();
            $system = $rets->GetSystemMetadata();
            $resources = $system->getResources();

            $s3 = new S3Client([
                'version' => 'latest',
                'region' => env('AWS_DEFAULT_REGION'),
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $page = 1;
            $chunkSize = 100;

            $totalPropertiesCount = DB::table('properties_all_data')
                ->where('is_images_downloaded', 0)
                ->count();

            $remainingCount = 300;//$totalPropertiesCount;

            do {
                $officeProperties = DB::table('properties_all_data')
                    ->where('mls_type', 0)
                    ->where('is_images_downloaded', 0)
                    ->orderBy('DOMDate', 'desc')
                    ->limit($chunkSize)
                    ->pluck('ListingKeyNumeric')
                    ->toArray();

                foreach ($officeProperties as $listingId) {
                    // Delete existing images from S3
                    $existingImages = DB::table('property_images')
                        ->where('listingid', $listingId)
                        ->pluck('image_url')
                        ->toArray();

                    foreach ($existingImages as $imageUrl) {
                        $parsedUrl = parse_url($imageUrl);
                        $key = ltrim($parsedUrl['path'], '/');

                        $s3->deleteObject([
                            'Bucket' => env('AWS_BUCKET'),
                            'Key' => $key,
                        ]);
                    }

                    // Delete existing images from the database
                    DB::table('property_images')
                        ->where('listingid', $listingId)
                        ->delete();

                    // Get and store new images
                    $imageObjects = $rets->GetObject('Property', 'XLarge', $listingId);
                    $skipFirstImage = true;
                    $index = 1;

                    foreach ($imageObjects as $photo) {
                        if ($skipFirstImage) {
                            $skipFirstImage = false;
                            continue;
                        }

                        $img_n = $photo->getContent();
                        $filename = "photo-$listingId-$index.jpeg";

                        $result = $s3->putObject([
                            'Bucket' => env('AWS_BUCKET'),
                            'Key' => "property-images/{$listingId}/{$filename}",
                            'Body' => $img_n,
                        ]);

                        $imageUrl = $result['ObjectURL'];

                        DB::table('property_images')->insert([
                            'image_url' => $imageUrl,
                            'listingid' => $listingId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        echo "TotalProperties: {$totalPropertiesCount} :: Remaining: {$remainingCount} :: Downloaded for ListingId {$listingId} saved: {$imageUrl}" . PHP_EOL;

                        $index++;
                    }

                    DB::table('properties_all_data')
                        ->where('ListingKeyNumeric', $listingId)
                        ->update(['is_images_downloaded' => 1]);

                    $remainingCount--;
                }

                $page++;
            } while ($remainingCount > 0);

        } catch (\Exception $e) {
            Log::error('Error storing property images: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cleanUpPropertyImages()
    {
        try {
            date_default_timezone_set('America/New_York');

            $s3 = new S3Client([
                'version' => 'latest',
                'region' => env('AWS_DEFAULT_REGION'),
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            $listingKeys = DB::table('properties_all_data')
                ->pluck('ListingKeyNumeric')
                ->toArray();

            // Get all folders in the S3 bucket
            $bucket = env('AWS_BUCKET');
            $folders = [];
            $continuationToken = null;

            do {
                $params = [
                    'Bucket' => $bucket,
                    'Prefix' => 'property-images/',
                    'Delimiter' => '/',
                    'MaxKeys' => 1000,
                ];

                if ($continuationToken) {
                    $params['ContinuationToken'] = $continuationToken;
                }

                $result = $s3->listObjectsV2($params);

                if (isset($result['CommonPrefixes'])) {
                    foreach ($result['CommonPrefixes'] as $prefix) {
                        $folderName = str_replace('property-images/', '', rtrim($prefix['Prefix'], '/'));
                        $folders[] = $folderName;
                    }
                }

                $continuationToken = isset($result['NextContinuationToken']) ? $result['NextContinuationToken'] : null;

            } while ($continuationToken);

            $deletedFoldersCount = 0;
            $deletedImagesCount = 0;
            $skippedFoldersCount = 0;
            $i = 1;

            // Compare folders with listing keys and delete non-matching folders and their entries in property_images
            foreach ($folders as $folder) {
                // $folderss = $folder.'-'.$i ;
                //     print_r($folderss);
                //     continue;
                if (!in_array($folder, $listingKeys)) {

                    // Delete all images in the folder
                    $objects = $s3->listObjectsV2([
                        'Bucket' => $bucket,
                        'Prefix' => "property-images/{$folder}/",
                    ]);

                    if (isset($objects['Contents'])) {
                        foreach ($objects['Contents'] as $object) {
                            $s3->deleteObject([
                                'Bucket' => $bucket,
                                'Key' => $object['Key'],
                            ]);
                            $deletedImagesCount++;
                        }
                    }

                    // Delete the folder itself
                    $s3->deleteObject([
                        'Bucket' => $bucket,
                        'Key' => "property-images/{$folder}/",
                    ]);

                    // Delete entries from property_images
                    DB::table('property_images')
                        ->where('listingid', $folder)
                        ->delete();

                    // Save deleted ListingId in properties_deleted_images_data table
                    DB::table('properties_deleted_images_data')->insert([
                        'ListingId' => $folder,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $deletedFoldersCount++;
                    echo "Deleted folder and images for ListingId {$folder}" . PHP_EOL;
                } else {
                    $skippedFoldersCount++;
                    echo "Skipped folder for ListingId {$folder}" . PHP_EOL;
                }
            }

            echo "Total folders checked: " . count($folders) . PHP_EOL;
            echo "Deleted folders: {$deletedFoldersCount}" . PHP_EOL;
            echo "Deleted images: {$deletedImagesCount}" . PHP_EOL;
            echo "Skipped folders: {$skippedFoldersCount}" . PHP_EOL;

        } catch (\Exception $e) {
            Log::error('Error deleting unwanted property images: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateDownloadedImagesFlag()
    {
        try {
            // Step 1: Mark all images as not downloaded
            DB::table('properties_all_data')->update(['is_images_downloaded' => 0]);
    
            // Step 2: Initialize counters
            $totalProperties = 0;
            $checkedProperties = 0;
            $markedImages = 0;
    
            // Step 3: Retrieve properties where images are not downloaded
            $properties = DB::table('properties_all_data')
                ->where('is_images_downloaded', 0)
                ->get(['ListingKeyNumeric']);
    
            $totalProperties = $properties->count();
    
            // Step 4: Process each property
            foreach ($properties as $property) {
                $listingId = $property->ListingKeyNumeric;
                $checkedProperties++;
    
                $hasImages = DB::table('property_images')
                    ->where('listingid', $listingId)
                    ->exists();
    
                if ($hasImages) {
                    DB::table('properties_all_data')
                        ->where('ListingKeyNumeric', $listingId)
                        ->update(['is_images_downloaded' => 1]);
    
                    $markedImages++;
    
                    echo "Total Properties: {$totalProperties} : Checked: {$checkedProperties} : MarkedImages: {$markedImages} : ListingKey: {$listingId}" . PHP_EOL;
                } else {
                    echo "Total Properties: {$totalProperties} : Checked: {$checkedProperties} : MarkedImages: {$markedImages} : ListingKey: {$listingId} (No images found)" . PHP_EOL;
                }
            }
    
        } catch (\Exception $e) {
            Log::error('Error updating images downloaded flag: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function updateProperties()
    {
        $slugify = new Slugify();
        $config = $this->getRetsConfiguration();
        $rets = new Session($config);
    
        $properties_all_data = 'properties_all_data';
    
        try {
            $connect = $rets->Login();
    
            $resourceName = 'Property';
            $className = 'Property';
            $query = '(MlsStatus=A,I,P)';
    
            $limit = 1000; // Number of records to fetch per batch
            $offset = 1;
            $totalUpdatedRecords = 0;
    
            while (true) {
                $results = $rets->Search(
                    $resourceName,
                    $className,
                    $query,
                    [
                        'QueryType' => 'DMQL2',
                        'Select' => 'ListingKeyNumeric,ArchitecturalStyle,LeaseMeasure,LeaseAmount,LeaseAmountFrequency,UnparsedAddress,ListingId,StoriesTotal',
                        'Format' => 'COMPACT-DECODED',
                        'Limit' => $limit,
                        'Offset' => $offset,
                    ]
                );
    
                $numResults = count($results);
                if ($numResults == 0) {
                    break; // Exit the loop if no more results are returned
                }
    
                foreach ($results as $record) {
                    $listingKey = $record['ListingKeyNumeric'];
                    $updateData = [
                        'ArchitecturalStyle' => $record['ArchitecturalStyle'] ?? null,
                        'StoriesTotal' => $record['StoriesTotal'] ?? null,
                        'LeaseMeasure' => $record['LeaseMeasure'] ?? null,
                        'LeaseAmount' => $record['LeaseAmount'] ?? null,
                        'LeaseAmountFrequency' => $record['LeaseAmountFrequency'] ?? null,
                        'slug_url' => $slugify->slugify($record['UnparsedAddress'] . '-' . $record['ListingId']),
                    ];
    
                    DB::table($properties_all_data)->where('ListingKeyNumeric', $listingKey)->update($updateData);
                    $totalUpdatedRecords++;
                }
    
                echo "Batch completed. Total updated records so far: $totalUpdatedRecords" . PHP_EOL;
    
                // Increment offset for next batch
                $offset += $limit;
            }
    
            $rets->Disconnect();
    
            echo "Selected columns updated successfully. Total updated records: $totalUpdatedRecords.";
    
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
    
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function retrieveDataNew_backup_15july()
    {
        date_default_timezone_set('America/New_York');
        $slugify = new Slugify();
        $newvariableForCount = 0;

        $config = $this->getRetsConfiguration();
        $rets = new Session($config);

        $properties_all_data = 'properties_all_data';
        $file_name = "RetsController.php";
        $curr_mls_id = 1;

        try {
            $connect = $rets->Login();

            $table = "<table border='1'><tr><th>ListingId</th><th>Status</th></tr>";

            $resourceName = 'Property';
            $className = 'Property';
            $last_cron_data = [];

            $cron_tablename = 'properties_cron_log';
            $curr_date = date('Y-m-d H:i:s');
            $values = array(
                'cron_file_name' => $file_name,
                'cron_start_time' => $curr_date,
                'property_class' => $className,
                'mls_no' => $curr_mls_id,
                'steps_completed' => 1
            );

            $curr_log_id = DB::table($cron_tablename)->insertGetId($values);

            $last_cron_data = DB::table($cron_tablename)
                ->where("cron_file_name", $file_name)
                ->where("property_class", $className)
                ->where("mls_no", $curr_mls_id)
                ->where("success", 1)
                ->where("cron_end_time", "<>", "0000-00-00 00:00:00")
                ->where("properties_download_end_time", "<>", '0000-00-00 00:00:00')
                ->orderBy("id", "DESC")
                ->limit(1)->get();

            $start_pull = false;
            $start_pull_time = date("Y-m-d\TH:i:s");

            $property_query_end_time = date("Y-m-d\TH:i:s");

            if (!count($last_cron_data) || empty($last_cron_data)) {

                $default_date = $start_pull_time;
                $start_pull = true;
                $property_query_start_time = $default_date;
                $last_success_cron_end_time = $default_date;

                $query = '(MlsStatus=A,I,P)';
            } else {

                $last_success_cron_end_time = $last_cron_data[0]->properties_download_end_time;

                if ($last_success_cron_end_time == '' || $last_success_cron_end_time == '0000-00-00 00:00:00' || $last_success_cron_end_time == '0000-00-00' || $last_success_cron_end_time == "1000-01-01 00:00:00") {
                    $property_query_start_time = strtotime($start_pull_time, time());
                    $property_query_start_time = date("Y-m-d\TH:i:s", $property_query_start_time);
                } else {
                    $property_query_start_time = date("Y-m-d\TH:i:s", strtotime($last_success_cron_end_time));
                }

                $twentyDaysLater = strtotime("+20 days", strtotime($property_query_start_time));

                if ($twentyDaysLater > time()) {
                    $query = '((MlsStatus=A,I,P),(ModificationTimestamp=' . $property_query_start_time . '+))';
                } else {
                    $property_query_end_time = date("Y-m-d\TH:i:s", $twentyDaysLater);
                    $query = '((MlsStatus=A,I,P),(ModificationTimestamp=' . $property_query_start_time . '-' . $property_query_end_time . '))';
                }
                
            }

            $query = '(MlsStatus=A,I,P)';

            // $query = '((ListingId=A2116879))';
            

            $cron_update_data = array(
                'properties_download_start_time' => $property_query_start_time,
                'steps_completed' => 2,
                'rets_query' => $query
            );

            DB::table($cron_tablename)->where('id', $curr_log_id)->update($cron_update_data);

            $batchSize = 100;
            $offset = 1;
            $totalResults = $rets->Search(
                $resourceName,
                $className,
                $query,
                [
                    'QueryType' => 'DMQL2',
                    'Count' => 2,
                    'Format' => 'COMPACT-DECODED',
                ]
            );

            $totalCount = $totalResults->getTotalResultsCount();
            $totalPrintCount = $totalCount;
            echo $query . PHP_EOL;
            echo "Total properties for importing are $totalCount" . PHP_EOL;

            // exit;
            $counter = 0;
            $insertCount = 0;
            $updateCount = 0;
            $offset = 0;
            $limit = 100;

            do {
                $cron_update_data = array(
                    'properties_count_from_mls' => $totalCount,
                    'steps_completed' => 3
                );
                DB::table($cron_tablename)->where('id', $curr_log_id)->update($cron_update_data);

                $results = $rets->Search(
                    $resourceName,
                    $className,
                    $query,
                    [
                        'QueryType' => 'DMQL2',
                        'Limit' => $limit,
                        'Offset' => $offset,
                        'Format' => 'COMPACT-DECODED',
                    ]
                );
                $count = count($results);
                echo PHP_EOL . "Offset:" . $offset . ' - ' . $count;
                $offset = $offset + $limit;
                $start = $offset;
                $end = $offset + $count - 1;

                $total_count_from_mls = $results->getTotalResultsCount();
                $s3 = new S3Client([
                    'version' => 'latest',
                    'region' => env('AWS_DEFAULT_REGION'),
                    'credentials' => [
                        'key' => env('AWS_ACCESS_KEY_ID'),
                        'secret' => env('AWS_SECRET_ACCESS_KEY'),
                    ],
                ]);

                $rets_prop_mapping = [
                    "id" => "id",
                    "ListingKeyNumeric" => "ListingKeyNumeric",
                    "BathroomsFull" => "BathroomsFull",
                    "BathroomsHalf" => "BathroomsHalf",
                    "BathroomsTotaltext" => "BathroomsTotaltext",
                    "BedroomsTotal" => "BedroomsTotal",
                    "BelowGradeFinishedArea" => "BelowGradeFinishedArea",
                    "BelowGradeFinishedAreaUnits" => "BelowGradeFinishedAreaUnits",
                    "BuildingAreaTotal" => "BuildingAreaTotal",
                    "City" => "City",
                    "CoListAgentFullName" => "CoListAgentFullName",
                    "CoListAgentKeyNumeric" => "CoListAgentKeyNumeric",
                    "CoListAgentEmail" => "CoListAgentEmail",
                    "CoListAgentDirectPhone" => "CoListAgentDirectPhone",
                    "CoListOfficeName" => "CoListOfficeName",
                    "CoListOfficePhone" => "CoListOfficePhone",
                    "Cooling" => "Cooling",
                    "CountyOrParish" => "CountyOrParish",
                    "DaysOnMarket" => "DaysOnMarket",
                    "Heating" => "Heating",
                    "Latitude" => "Latitude",
                    "ListAgentFullName" => "ListAgentFullName",
                    "ListAgentKeyNumeric" => "ListAgentKeyNumeric",
                    "ListAgentEmail" => "ListAgentEmail",
                    "ListAgentDirectPhone" => "ListAgentDirectPhone",
                    "ListOfficeName" => "ListOfficeName",
                    "ListOfficePhone" => "ListOfficePhone",
                    "ListPrice" => "ListPrice",
                    "Longitude" => "Longitude",
                    "MlsStatus" => "MlsStatus",
                    "PropertySubType" => "PropertySubType",
                    "PropertyType" => "PropertyType",
                    "PublicRemarks" => "PublicRemarks",
                    "StandardStatus" => "StandardStatus",
                    "StateOrProvince" => "StateOrProvince",
                    "StreetDirPrefix" => "StreetDirPrefix",
                    "StreetDirSuffix" => "StreetDirSuffix",
                    "StreetName" => "StreetName",
                    "StreetNumber" => "StreetNumber",
                    "StreetSuffix" => "StreetSuffix",
                    "UnitNumber" => "UnitNumber",
                    "YearBuilt" => "YearBuilt",
                    "AccessibilityFeatures" => "AccessibilityFeatures",
                    "Appliances" => "Appliances",
                    "AssociationAmenities" => "AssociationAmenities",
                    "Basement" => "Basement",
                    "BuilderName" => "BuilderName",
                    "BusinessName" => "BusinessName",
                    "CarportSpaces" => "CarportSpaces",
                    "CommonWalls" => "CommonWalls",
                    "CommunityFeatures" => "CommunityFeatures",
                    "ConstructionMaterials" => "ConstructionMaterials",
                    "ElementarySchoolDistrict" => "ElementarySchoolDistrict",
                    "ExteriorFeatures" => "ExteriorFeatures",
                    "FireplaceFeatures" => "FireplaceFeatures",
                    "FoundationDetails" => "FoundationDetails",
                    "GarageSpaces" => "GarageSpaces",
                    "HighSchoolDistrict" => "HighSchoolDistrict",
                    "InteriorFeatures" => "InteriorFeatures",
                    "LandLeaseAmount" => "LandLeaseAmount",
                    "LeaseTerm" => "LeaseTerm",
                    "Levels" => "Levels",
                    "LotFeatures" => "LotFeatures",
                    "LotSizeAcres" => "LotSizeAcres",
                    "LotSizeSquareFeet" => "LotSizeSquareFeet",
                    "ParkingFeatures" => "ParkingFeatures",
                    "PatioAndPorchFeatures" => "PatioAndPorchFeatures",
                    "PetsAllowed" => "PetsAllowed",
                    "PoolFeatures" => "PoolFeatures",
                    "RoomsTotal" => "RoomsTotal",
                    "Sewer" => "Sewer",
                    "StructureType" => "StructureType",
                    "SubdivisionName" => "SubdivisionName",
                    "TaxLegalDescription" => "TaxLegalDescription",
                    "Utilities" => "Utilities",
                    "WaterfrontFeatures" => "WaterfrontFeatures",
                    "WaterSource" => "WaterSource",
                    "YardSize" => "YardSize",
                    "Zoning" => "Zoning",
                    "BuildingAreaTotalMetres" => "BuildingAreaTotalMetres",
                    "BuildingAreaTotalSF" => "BuildingAreaTotalSF",
                    "DOMIncrementing" => "DOMIncrementing",
                    "FootprintSQFT" => "FootprintSQFT",
                    "LotSizeNotCultivated" => "LotSizeNotCultivated",
                    "LotSizeSeeded" => "LotSizeSeeded",
                    "LotSizeTameHay" => "LotSizeTameHay",
                    "LotSizeTreed" => "LotSizeTreed",
                    "LivingAreaMetres" => "LivingAreaMetres",
                    "LivingAreaSF" => "LivingAreaSF",
                    "MainLevelFinishedAreaMetres" => "MainLevelFinishedAreaMetres",
                    "MainLevelFinishedAreaSF" => "MainLevelFinishedAreaSF",
                    "UpperLevelFinishedAreaMetres" => "UpperLevelFinishedAreaMetres",
                    "UpperLevelFinishedAreaSF" => "UpperLevelFinishedAreaSF",
                    "CondoName" => "CondoName",
                    "BedroomsBelowGrade" => "BedroomsBelowGrade",
                    "CoListOfficeEmail" => "CoListOfficeEmail",
                    "ParkingEnclosed" => "ParkingEnclosed",
                    "URL3DImage" => "URL3DImage",
                    "URLBrochure" => "URLBrochure",
                    "URLSoundByte" => "URLSoundByte",
                    "FrontageFt" => "FrontageFt",
                    "HeatingExpense" => "HeatingExpense",
                    "ListingContractDate" => "ListingContractDate",
                    "ListingId" => "ListingId",
                    "ListingService" => "ListingService",
                    "InternetEntireListingDisplayYN" => "InternetEntireListingDisplayYN",
                    "OtherColumns" => "OtherColumns",
                    "slug_url" => "slug_url",
                    "created_at" => "created_at",
                    "updated_at" => "updated_at",
                    "CoListOfficeKeyNumeric" => "CoListOfficeKeyNumeric",
                    "featured" => "featured",
                    "diamond" => "diamond",
                    "ListOfficeKeyNumeric" => "ListOfficeKeyNumeric",
                    "UnparsedAddress" => "UnparsedAddress",
                    "TransactionType" => "TransactionType",
                    "DOMDate" => "DOMDate",
                    "ModificationTimestamp" => "ModificationTimestamp",
                    "LeaseMeasure" => "LeaseMeasure",
                    "LeaseAmount" => "LeaseAmount",
                    "LeaseAmountFrequency" => "LeaseAmountFrequency",
                    "PostalCode" => "PostalCode",
                    "ArchitecturalStyle" => "ArchitecturalStyle",
                    "StoriesTotal" => "StoriesTotal",
                ];
                
                foreach ($results as $record) {
                    echo "\n remaning count : " . $totalPrintCount . ', done count : ' . $newvariableForCount;
                    $newvariableForCount++;
                    $totalPrintCount--;
                    try {
                        
                        $values = $record->toArray();
                        $data = [];
                        
                        foreach ($rets_prop_mapping as $rets_key => $db_column) {
                            if (isset($values[$rets_key])) {
                                $data[$db_column] = $values[$rets_key] === '' ? null : $values[$rets_key];
                            }
                        }
                        
                        $table .= "<tr><td>{$data['ListingKeyNumeric']}</td><td>{$data['MlsStatus']}</td></tr>";
                        $data['OtherColumns'] = json_encode(array_diff_key($values, $data));
                        if($data['UnparsedAddress']==null || $data['UnparsedAddress']=='')
                        {
                            $data['slug_url'] = null;
                        }
                        else{
                            $slug_url = $slugify->slugify($data['UnparsedAddress'] . '-' . $data['ListingId']);
                            $data['slug_url'] = $slug_url;
                        }
                        
                        
                        if (in_array($data['ListOfficeKeyNumeric'], ['1297353', '1298083']) && $data['ListPrice'] >= 1000000) {
                            $data['diamond'] = 1;
                        } else if (in_array($data['ListOfficeKeyNumeric'], ['1297353', '1298083'])) {
                            $data['featured'] = 1;
                        }
                        
                        $existingRecord = DB::table($properties_all_data)->where('ListingKeyNumeric', $data['ListingKeyNumeric'])->first();
                        $listingId = $data['ListingKeyNumeric'];
                        
                        

                        if ($existingRecord) {
                            // $shouldUpdateImage = false;
                            // $otherColumns = json_decode($existingRecord->OtherColumns, true);
                            // $imageModified = $otherColumns['PhotosChangeTimestamp'];
                            
                            // if ($imageModified != $record['PhotosChangeTimestamp']) {
                            //     $shouldUpdateImage = true;
                            // }

                            // if ($shouldUpdateImage) {
                            //     $imageObject = $rets->GetObject('Property', 'XLarge', $listingId, 1);
                            //     if (!empty($imageObject)) {
                            //         $photo = $imageObject[0];
                            //         $img_n = $photo->getContent();
                            //         $img_n_id = $photo->getContentId();
                            //         if ($img_n_id !== null) {
                            //             try {
                            //                 $filename = "photo-$listingId-0.jpeg";
                            //                 $key = "property-images-first/{$listingId}/{$filename}";
                            //                 $result = $s3->putObject([
                            //                     'Bucket' => env('AWS_BUCKET'),
                            //                     'Key' => $key,
                            //                     'Body' => $img_n,
                            //                 ]);
                            //                 $imageUrl = $result['ObjectURL'];
                                            
                            //                 $data['image_url'] = $imageUrl;
                            //                 DB::table('properties_all_data')
                            //                 ->where('ListingKeyNumeric', $listingId)
                            //                 ->update(['is_images_downloaded' => 0]);
                            //             } catch (\Exception $exception) {
                            //                 Log::error("Failed to save property photo for ListingKeyNumeric {$listingId}: " . $exception->getMessage());
                            //                 //continue;
                            //             }
                            //         }
                            //     }
                            // }
                            // // dd('yes');
                            // DB::table($properties_all_data)->where('ListingKeyNumeric', $data['ListingKeyNumeric'])->update($data);
                            $updateCount++;
                            echo " : Property with ListingId {$data['ListingKeyNumeric']} has been updated." . PHP_EOL;
                        } else {
                            $roomsQuery = "(ListingKeyNumeric={$data['ListingKeyNumeric']})";
                        $roomsResults = $rets->Search(
                            'PropertyRooms',
                            'PropertyRooms',
                            $roomsQuery,
                            [
                                'QueryType' => 'DMQL2',
                                'Format' => 'COMPACT-DECODED',
                                ]
                            );
                            
                            $rooms = [];
                            foreach ($roomsResults as $room) {
                                $roomData = $room->toArray();
                                $rooms[] = $roomData;
                            }
                            $data['property_rooms'] = json_encode($rooms);
                            
                            $imageObject = $rets->GetObject('Property', 'XLarge', $listingId, 1);
                            if (!empty($imageObject)) {
                                $photo = $imageObject[0];
                                $img_n = $photo->getContent();
                                $img_n_id = $photo->getContentId();
                                if ($img_n_id !== null) {
                                    try {
                                        $filename = "photo-$listingId-0.jpeg";
                                        $key = "property-images-first/{$listingId}/{$filename}";
                                        $result = $s3->putObject([
                                            'Bucket' => env('AWS_BUCKET'),
                                            'Key' => $key,
                                            'Body' => $img_n,
                                        ]);
                                        $imageUrl = $result['ObjectURL'];

                                        $data['image_url'] = $imageUrl;
                                    } catch (\Exception $exception) {
                                        Log::error("Failed to save property photo for ListingKeyNumeric {$listingId}: " . $exception->getMessage());
                                        //continue;
                                    }
                                }
                            }

                            DB::table($properties_all_data)->insert($data);
                            $insertCount++;
                            echo " : Property with ListingId {$data['ListingKeyNumeric']} has been inserted." . PHP_EOL;
                        }

                        $counter++;
                        if ($counter % 100 == 0 || $counter == $count) {
                            echo "Properties from $start to " . min($start + $counter - 1, $total_count_from_mls) . " imported." . PHP_EOL;
                            $start += $counter;
                        }
                    } catch (\Exception $e) {
                        Log::error("Error processing property with ListingKeyNumeric {$record->get('ListingKeyNumeric')}: " . $e->getMessage());

                    }
                }

            } while (count($results) > 0);

            $table .= "</table>";

            $cron_update_data4 = array(
                'cron_end_time' => date('Y-m-d H:i:s'),
                'steps_completed' => 4,
                'success' => 1,
                'properties_count_from_mls' => $totalCount,
                'property_inserted' => $insertCount,
                'property_updated' => $updateCount,
                'properties_download_end_time' => $property_query_end_time,
                'properties_count_actual_downloaded' => $counter
            );
            $step4 = DB::table($cron_tablename)
                ->where('id', $curr_log_id)
                ->update($cron_update_data4);
            echo "Listings Have Been Downloaded Successfully";

            // $this->checkSoldProperties();
        } catch (\Exception $e) {

            Log::error("Error: " . $e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }

        $rets->Disconnect();

    }
    
     public function storeFirstPropertyImage()
    {
        $config = $this->getRetsConfiguration();
        $rets = new Session($config);
        $connect = $rets->Login();
    
        $s3 = new S3Client([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    
        // Select only listings where image_url is NULL
        $allListings = DB::table('properties_all_data')
            ->select('ListingKeyNumeric')
            ->whereNull('image_url')
            ->get();
    
        if ($allListings->isEmpty()) {
            echo "No listings found with NULL image_url." . PHP_EOL;
            return;
        }
    
        foreach ($allListings as $listing) {
            $listingId = $listing->ListingKeyNumeric;
    
            $imageObject = $rets->GetObject('Property', 'XLarge', $listingId);
    
            if (!empty($imageObject)) {
                $photo = $imageObject[1];
                $img_n = $photo->getContent();
                $img_n_id = $photo->getContentId();
    
                if ($img_n_id !== null) {
                    try {
                        $filename = "photo-$listingId-0.jpeg";
                        $key = "property-images-first/{$listingId}/{$filename}";
                        $result = $s3->putObject([
                            'Bucket' => env('AWS_BUCKET'),
                            'Key' => $key,
                            'Body' => $img_n,
                        ]);
                        $imageUrl = $result['ObjectURL'];
    
                        DB::table('properties_all_data')
                            ->where('ListingKeyNumeric', $listingId)
                            ->update([
                                'image_url' => $imageUrl
                            ]);
    
                        echo "Image for ListingId {$listingId} has been updated." . PHP_EOL;
                    } catch (\Exception $exception) {
                        Log::error("Failed to update property image for ListingKeyNumeric {$listingId}: " . $exception->getMessage());
                    }
                } else {
                    echo "Image for ListingId {$listingId} has not changed." . PHP_EOL;
                }
            } else {
                echo "No image available for ListingId {$listingId}." . PHP_EOL;
            }
        }
    }



}
