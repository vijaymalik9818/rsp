<?php

namespace App\Http\Controllers;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Cocur\Slugify\Slugify;

class BridgePropertyController extends Controller
{
    public function fetchRaeListings()
    {
        date_default_timezone_set('America/New_York');
        $slugify = new Slugify();
        $accessToken =  env('BRIDGE_ACCESS_TOKEN');
        $baseUrl =  env('BRIDGE_LOGIN_URL');
        $batchSize = 100;
        $fileName = "RaeListingsCron.php";
        $className = 'Listings';
        $cronTablename = 'properties_cron_log';
        
        // Cron job data
        $currDate = date('Y-m-d H:i:s');
        $values = [
            'cron_file_name' => $fileName,
            'cron_start_time' => $currDate,
            'property_class' => $className,
            'mls_no' => 1,
            'steps_completed' => 1,
        ];
        
        $currLogId = DB::table($cronTablename)->insertGetId($values);
        
        $s3 = new S3Client([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        $lastCronData = DB::table($cronTablename)
            ->where("cron_file_name", $fileName)
            ->where("property_class", $className)
            ->where("mls_no", 1)
            ->where("success", 1)
            ->where("cron_end_time", "<>", "0000-00-00 00:00:00")
            ->orderBy("id", "DESC")
            ->first();
    
        $startDate = $lastCronData ? $lastCronData->cron_end_time : date("Y-m-d H:i:s", strtotime('-1 months'));
        if (empty($startDate) || $startDate == '0000-00-00 00:00:00') {
            $startDate = date("Y-m-d H:i:s", strtotime('-1 months'));
        }
    
        while (true) {
            $endDate = date("Y-m-d H:i:s", strtotime('+1 month', strtotime($startDate)));
            if (strtotime($endDate) > strtotime($currDate)) {
                $endDate = $currDate;
            }
    
            echo "Processing data from $startDate to $endDate\n";
    
            for ($i = 0; ; $i++) {
                $offset = $i * $batchSize;
                $currUrl = "$baseUrl?access_token=$accessToken&ModificationTimestamp.gte=" . urlencode($startDate) . "&ModificationTimestamp.lte=" . urlencode($endDate) . "&limit=$batchSize&offset=$offset";
                echo "Fetching batch " . ($i + 1) . ": $currUrl\n";
                $response = $this->makeCurlRequest($currUrl, $accessToken);
    
                if (!$response || !isset($response['bundle'])) {
                    echo "Error fetching data from the API.\n";
                    break 2;
                }
    
                $data = $response['bundle'];
    
                if (empty($data)) {
                    echo "No more data in this batch.\n";
                    break;
                }
    
                foreach ($data as $item) {
                    $listingId = $item['ListingId'];
                    $mappedItem = [
                        'ListingKeyNumeric' => $item['ListingKeyNumeric'],
                        'BathroomsFull' => $item['BathroomsFull'],
                        'BathroomsHalf' => $item['BathroomsHalf'],
                        'BathroomsTotaltext' => $item['BathroomsTotalInteger'],
                        'BedroomsTotal' => $item['BathroomsTotalInteger'],
                        'BuildingAreaTotal' => $item['LotSizeArea'],
                        'City' => $item['City'],
                        'CoListAgentFullName' => $item['CoListAgentLastName'],
                        'CoListAgentKeyNumeric' => $item['CoListAgentKeyNumeric'],
                        'CoListAgentEmail' => $item['CoListAgentEmail'],
                        'CoListOfficeName' => $item['CoListOfficeName'],
                        'CoListOfficePhone' => $item['CoListOfficePhone'],
                        'Latitude' => $item['Latitude'],
                        'ListAgentFullName' => $item['ListAgentFullName'],
                        'ListAgentKeyNumeric' => $item['ListAgentKeyNumeric'],
                        'ListAgentEmail' => $item['ListAgentEmail'],
                        'ListOfficeName' => $item['ListOfficeName'],
                        'ListOfficePhone' => $item['ListOfficePhone'],
                        'ListPrice' => $item['ListPrice'],
                        'Longitude' => $item['Longitude'],
                        'PropertySubType' => $item['PropertySubType'],
                        'PropertyType' => $item['PropertyType'],
                        'PublicRemarks' => $item['PublicRemarks'],
                        'StateOrProvince' => $item['StateRegion'],
                        'StreetDirPrefix' => $item['StreetDirPrefix'],
                        'StreetName' => $item['StreetName'],
                        'StreetNumber' => $item['StreetNumber'],
                        'StreetSuffix' => $item['StreetSuffix'],
                        'YearBuilt' => $item['YearBuilt'],
                        'Appliances' => $item['Appliances'],
                        'Basement' => $item['Basement'],
                        'CommunityFeatures' => $item['CommunityFeatures'],
                        'ExteriorFeatures' => $item['ExteriorFeatures'],
                        'FireplaceFeatures' => $item['FireplaceFeatures'],
                        'FoundationDetails' => $item['FoundationDetails'],
                        'LotSizeAcres' => $item['LotSizeAcres'],
                        'ParkingFeatures' => $item['ParkingFeatures'],
                        'Sewer' => $item['Sewer'],
                        'SubdivisionName' => $item['SubdivisionName'],
                        'WaterSource' => $item['WaterSource'],
                        'Zoning' => $item['Zoning'],
                        'LivingAreaSF' => $item['LivingArea'],
                        'CoListOfficeEmail' => $item['CoListOfficeEmail'],
                        'ListingId' => $item['ListingId'],
                        'InternetEntireListingDisplayYN' => $item['InternetEntireListingDisplayYN'],
                        'CoListOfficeKeyNumeric' => $item['CoListOfficeKeyNumeric'],
                        'ListOfficeKeyNumeric' => $item['ListOfficeKeyNumeric'],
                        'PostalCode' => $item['PostalCode'],
                        'BuildingType' => $item['RAE_LFD_BUILDINGTYPE_74'],
                        'StoriesTotal' => $item['StoriesTotal'],
                        'UnparsedAddress' => $item['UnparsedAddress'],
                        'UnitNumber' => $item['UnitNumber'],
                        'mls_type' => 1
                    ];
                
                    if ($item['UnparsedAddress'] == null || $item['UnparsedAddress'] == '') {
                        $mappedItem['slug_url'] = null;
                    } else {
                        $slug_url = $slugify->slugify($item['UnparsedAddress'] . '-' . $item['ListingId']);
                        $mappedItem['slug_url'] = $slug_url;
                    }
                    $mediaUrls = [];
                    $firstImageUrl = null;
                
                    if (isset($item['Media']) && is_array($item['Media'])) {
                        foreach ($item['Media'] as $index => $media) {
                            if (isset($media['MediaURL'])) {
                                if ($index == 0 && !$firstImageUrl) {
                                    $firstImageUrl = $media['MediaURL'];
                                }
                                $mediaUrls[] = $media['MediaURL'];
                            }
                        }
                    }
                
                    if ($firstImageUrl) {
                        $imageContents = file_get_contents($firstImageUrl);
                        $filename = "photo-$listingId-0.jpg";
                        $key = "property-images-first/{$listingId}/{$filename}";
                        $result = $s3->putObject([
                            'Bucket' => env('AWS_BUCKET'),
                            'Key' => $key,
                            'Body' => $imageContents,
                            'ContentType' => 'image/jpeg',
                        ]);
                
                        $imageUrl = $result['ObjectURL'];
                        $mappedItem['image_url'] = $imageUrl;  
                    }
                
                    $imagesJson = json_encode($mediaUrls);
                
                    $existingMediaRecord = DB::table('bridge_property_images_json')->where('listing_id', $listingId)->first();
                
                    if ($existingMediaRecord) {
                        DB::table('bridge_property_images_json')
                            ->where('listing_id', $listingId)
                            ->update([
                                'images_json' => $imagesJson,
                                'is_imported' => 1,
                                'updated_at' => now(),
                            ]);
                        echo "Media for ListingId $listingId updated.\n";
                    } else {
                        DB::table('bridge_property_images_json')->insert([
                            'listing_id' => $listingId,
                            'images_json' => $imagesJson,
                            'is_imported' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        echo "Media for ListingId $listingId inserted.\n";
                    }
                
                    $existingRecord = DB::table('properties_all_data')->where('ListingId', $listingId)->first();
                
                    unset($item['Media']);  
                
                    foreach ($mappedItem as $key => $value) {
                        if (is_array($value)) {
                            $mappedItem[$key] = empty($value) ? null : implode(',', $value);
                        } elseif (is_object($value)) {
                            $mappedItem[$key] = json_encode($value);
                        } else {
                            $mappedItem[$key] = $value;
                        }
                    }
                
                    if ($existingRecord) {
                        DB::table('properties_all_data')
                            ->where('ListingId', $listingId)
                            ->update($mappedItem);
                        echo "ListingId $listingId updated\n";
                    } else {
                        DB::table('properties_all_data')->insert($mappedItem);
                        echo "ListingId $listingId created\n";
                    }
                }
                
                
            }
    
            if (strtotime($endDate) >= strtotime($currDate)) {
                break;
            }
    
            $startDate = $endDate;
        }
    
        $cronUpdateData = [
            'cron_end_time' => date('Y-m-d H:i:s'),
            'steps_completed' => 2,
            'success' => 1,
        ];
    
        DB::table($cronTablename)->where('id', $currLogId)->update($cronUpdateData);
        echo "Cron job completed successfully.\n";
    }
    
    
    
    
    private function makeCurlRequest($url, $accessToken)
    {
        $curl = curl_init();
    
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $accessToken"
            ],
        ]);
    
        $response = curl_exec($curl);
    
        if (curl_errno($curl)) {
            echo "cURL Error: " . curl_error($curl) . "\n";
            curl_close($curl);
            return null;
        }
    
        curl_close($curl);
    
        return json_decode($response, true);
    }
    
    
    
}


