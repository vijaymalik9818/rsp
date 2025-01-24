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
        $accessToken = env('BRIDGE_ACCESS_TOKEN');
        $baseUrl = env('BRIDGE_LOGIN_URL');
        $batchSize = 100;
        $fileName = "RaeListingsCron.php";
        $className = 'Listings';
        $cronTablename = 'properties_cron_log';
    
        $mappingKeys = [
            'ModificationTimestamp' => 'ModificationTimestamp',

            'ListingKeyNumeric' => 'ListingKeyNumeric',
            'BathroomsFull' => 'BathroomsFull',
            'BathroomsHalf' => 'BathroomsHalf',
            'BathroomsTotaltext' => 'BathroomsTotalInteger',
            'BedroomsTotal' => 'BedroomsTotal',
            'BuildingAreaTotal' => 'LotSizeArea',
            'City' => 'City',
            'CoListAgentFullName' => 'CoListAgentLastName',
            'CoListAgentKeyNumeric' => 'CoListAgentKeyNumeric',
            'CoListAgentEmail' => 'CoListAgentEmail',
            'CoListOfficeName' => 'CoListOfficeName',
            'CoListOfficePhone' => 'CoListOfficePhone',
            'Latitude' => 'Latitude',
            'ListAgentFullName' => 'ListAgentFullName',
            'ListAgentKeyNumeric' => 'ListAgentKeyNumeric',
            'ListAgentDirectPhone' => 'ListAgentPreferredPhone',
            'ListAgentEmail' => 'ListAgentEmail',
            'ListOfficeName' => 'ListOfficeName',
            'ListOfficePhone' => 'ListOfficePhone',
            'ListPrice' => 'ListPrice',
            'Longitude' => 'Longitude',
            'PropertySubType' => 'PropertySubType',
            'PropertyType' => 'PropertyType',
            'PublicRemarks' => 'PublicRemarks',
            'StateOrProvince' => 'StateOrProvince',
            'StreetDirPrefix' => 'StreetDirPrefix',
            'StreetName' => 'StreetName',
            'StreetNumber' => 'StreetNumber',
            'StreetSuffix' => 'StreetSuffix',
            'YearBuilt' => 'YearBuilt',
            'Appliances' => 'Appliances',
            'Basement' => 'Basement',
            'CommunityFeatures' => 'CommunityFeatures',
            'ExteriorFeatures' => 'ExteriorFeatures',
            'FireplaceFeatures' => 'FireplaceFeatures',
            'FoundationDetails' => 'FoundationDetails',
            'LotSizeAcres' => 'LotSizeAcres',
            'ParkingFeatures' => 'ParkingFeatures',
            'Sewer' => 'Sewer',
            'SubdivisionName' => 'SubdivisionName',
            'WaterSource' => 'WaterSource',
            'Zoning' => 'Zoning',
            'LivingAreaSF' => 'LivingArea',
            'CoListOfficeEmail' => 'CoListOfficeEmail',
            'ListingId' => 'ListingId',
            'InternetEntireListingDisplayYN' => 'InternetEntireListingDisplayYN',
            'CoListOfficeKeyNumeric' => 'CoListOfficeKeyNumeric',
            'ListOfficeKeyNumeric' => 'ListOfficeKeyNumeric',
            'PostalCode' => 'PostalCode',
            'BuildingType' => 'RAE_LFD_BUILDINGTYPE_74',
            'StoriesTotal' => 'StoriesTotal',
            'UnparsedAddress' => 'UnparsedAddress',
            'UnitNumber' => 'UnitNumber',
        ];
        
    
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
    
        $startDate = null;
    
        if (!$lastCronData) {
            echo "First execution: Fetching all data without timestamp filters.\n";
        } else {
            $startDate = $lastCronData->cron_end_time ?: date("Y-m-d H:i:s", strtotime('-1 month'));
            echo "Subsequent execution: Fetching data from $startDate to current date.\n";
        }
    
        $endDate = $startDate ? date("Y-m-d H:i:s", strtotime('+1 month', strtotime($startDate))) : null;
        $countUrl = $baseUrl . "?access_token=$accessToken";
    
        if ($startDate) {
            $countUrl .= "&ModificationTimestamp.gte=" . urlencode($startDate) .
                "&ModificationTimestamp.lte=" . urlencode($endDate);
        }
    
        echo "Checking total properties count with URL: $countUrl\n";
        $response = $this->makeCurlRequest($countUrl, $accessToken);
    
        if (!$response || !isset($response['total'])) {
            echo "Error fetching total count from the API.\n";
            return;
        }
    
        $totalCount = $response['total'];
        echo "Total properties to process: $totalCount\n";
    
        for ($i = 0; ; $i++) {
            $offset = $i * $batchSize;
            $currUrl = $baseUrl . "?access_token=$accessToken";
    
            if ($startDate) {
                $currUrl .= "&ModificationTimestamp.gte=" . urlencode($startDate) .
                    "&ModificationTimestamp.lte=" . urlencode($endDate);
            }
    
            $currUrl .= "&limit=$batchSize&offset=$offset";
            echo "Fetching batch " . ($i + 1) . ": $currUrl\n";
            $response = $this->makeCurlRequest($currUrl, $accessToken);
    
            if (!$response || !isset($response['bundle'])) {
                echo "Error fetching data from the API.\n";
                break;
            }
    
            $data = $response['bundle'];
    
            if (empty($data)) {
                echo "No more data in this batch.\n";
                break;
            }
    
            foreach ($data as $item) {
                $listingId = $item['ListingId'];
                $mappedItem = [];
    
                foreach ($mappingKeys as $mappedKey => $apiKey) {
                    $mappedItem[$mappedKey] = $item[$apiKey] ?? null;
                }
                
    
                $unmappedKeys = array_diff_key($item, $mappedItem);
                $mappedItem['otherColumns'] = json_encode($unmappedKeys);
                $mappedItem['mls_type'] = 1;
    
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
                        'is_imported' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    echo "Media for ListingId $listingId inserted.\n";
                }
    
                $existingRecord = DB::table('properties_all_data')->where('ListingId', $listingId)->first();
    
                unset($item['Media']);

                $mappedItem['ModificationTimestamp'] = (new \DateTime($item['ModificationTimestamp']))->format('Y-m-d H:i:s');

    
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
    
            if ($offset + $batchSize >= $totalCount) {
                echo "All properties processed.\n";
                break;
            }
        }
    
        DB::table($cronTablename)->where('id', $currLogId)->update([
            'steps_completed' => 2,
            'success' => 0,
            'cron_end_time' => date('Y-m-d H:i:s'),
        ]);
        echo "Cron job completed.\n";
    }
    

    public function fetchRaeListings_bkup()
    {
        date_default_timezone_set('America/New_York');
        $slugify = new Slugify();
        $accessToken = env('BRIDGE_ACCESS_TOKEN');
        $baseUrl = env('BRIDGE_LOGIN_URL');
        $batchSize = 100;
        $fileName = "RaeListingsCron.php";
        $className = 'Listings';
        $cronTablename = 'properties_cron_log';
    
        $mappingKeys = [
            'ModificationTimestamp' => 'ModificationTimestamp',
            'ListingKeyNumeric' => 'ListingKeyNumeric',
            'BathroomsFull' => 'BathroomsFull',
            'BathroomsHalf' => 'BathroomsHalf',
            'BathroomsTotaltext' => 'BathroomsTotalInteger',
            'BedroomsTotal' => 'BedroomsTotal',
            'BuildingAreaTotal' => 'LotSizeArea',
            'City' => 'City',
            'CoListAgentFullName' => 'CoListAgentLastName',
            'CoListAgentKeyNumeric' => 'CoListAgentKeyNumeric',
            'CoListAgentEmail' => 'CoListAgentEmail',
            'CoListOfficeName' => 'CoListOfficeName',
            'CoListOfficePhone' => 'CoListOfficePhone',
            'Latitude' => 'Latitude',
            'ListAgentFullName' => 'ListAgentFullName',
            'ListAgentKeyNumeric' => 'ListAgentKeyNumeric',
            'ListAgentEmail' => 'ListAgentEmail',
            'ListOfficeName' => 'ListOfficeName',
            'ListOfficePhone' => 'ListOfficePhone',
            'ListAgentDirectPhone' => 'ListAgentPreferredPhone',
            'ListPrice' => 'ListPrice',
            'Longitude' => 'Longitude',
            'PropertySubType' => 'PropertySubType',
            'PropertyType' => 'PropertyType',
            'PublicRemarks' => 'PublicRemarks',
            'StateOrProvince' => 'StateOrProvince',
            'StreetDirPrefix' => 'StreetDirPrefix',
            'StreetName' => 'StreetName',
            'StreetNumber' => 'StreetNumber',
            'StreetSuffix' => 'StreetSuffix',
            'YearBuilt' => 'YearBuilt',
            'Appliances' => 'Appliances',
            'Basement' => 'Basement',
            'CommunityFeatures' => 'CommunityFeatures',
            'ExteriorFeatures' => 'ExteriorFeatures',
            'FireplaceFeatures' => 'FireplaceFeatures',
            'FoundationDetails' => 'FoundationDetails',
            'LotSizeAcres' => 'LotSizeAcres',
            'ParkingFeatures' => 'ParkingFeatures',
            'Sewer' => 'Sewer',
            'SubdivisionName' => 'SubdivisionName',
            'WaterSource' => 'WaterSource',
            'Zoning' => 'Zoning',
            'LivingAreaSF' => 'LivingArea',
            'CoListOfficeEmail' => 'CoListOfficeEmail',
            'ListingId' => 'ListingId',
            'InternetEntireListingDisplayYN' => 'InternetEntireListingDisplayYN',
            'CoListOfficeKeyNumeric' => 'CoListOfficeKeyNumeric',
            'ListOfficeKeyNumeric' => 'ListOfficeKeyNumeric',
            'PostalCode' => 'PostalCode',
            'BuildingType' => 'RAE_LFD_BUILDINGTYPE_74',
            'StoriesTotal' => 'StoriesTotal',
            'UnparsedAddress' => 'UnparsedAddress',
            'UnitNumber' => 'UnitNumber',
        ];
        
    
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
    
        $startDate = null;
    
        // if (!$lastCronData) {
        //     echo "First execution: Fetching all data without timestamp filters.\n";
        // } else {
        //     $startDate = $lastCronData->cron_end_time ?: date("Y-m-d H:i:s", strtotime('-1 month'));
        //     echo "Subsequent execution: Fetching data from $startDate to current date.\n";
        // }
    
        // $endDate = $startDate ? date("Y-m-d H:i:s", strtotime('+1 month', strtotime($startDate))) : null;
        $countUrl = $baseUrl . "?access_token=$accessToken";
    
        // if ($startDate) {
        //     $countUrl .= "&ModificationTimestamp.gte=" . urlencode($startDate) .
        //         "&ModificationTimestamp.lte=" . urlencode($endDate);
        // }
    
        echo "Checking total properties count with URL: $countUrl\n";
        $response = $this->makeCurlRequest($countUrl, $accessToken);
    
        if (!$response || !isset($response['total'])) {
            echo "Error fetching total count from the API.\n";
            return;
        }
    
        $totalCount = $response['total'];
        echo "Total properties to process: $totalCount\n";

        $processedListingIds = []; // To keep track of API ListingIds

    
        for ($i = 0; ; $i++) {
            $offset = $i * $batchSize;
            $currUrl = $baseUrl . "?access_token=$accessToken";
    
            // if ($startDate) {
            //     $currUrl .= "&ModificationTimestamp.gte=" . urlencode($startDate) .
            //         "&ModificationTimestamp.lte=" . urlencode($endDate);
            // }
    
            $currUrl .= "&limit=$batchSize&offset=$offset";
            echo "Fetching batch " . ($i + 1) . ": $currUrl\n";
            $response = $this->makeCurlRequest($currUrl, $accessToken);
    
            if (!$response || !isset($response['bundle'])) {
                echo "Error fetching data from the API.\n";
                break;
            }
    
            $data = $response['bundle'];
    
            if (empty($data)) {
                echo "No more data in this batch.\n";
                break;
            }
    
            foreach ($data as $item) {
                $listingId = $item['ListingId'];
                $mappedItem = [];

                $processedListingIds[] = $listingId;

    
                foreach ($mappingKeys as $mappedKey => $apiKey) {
                    $mappedItem[$mappedKey] = $item[$apiKey] ?? null;
                }
                
    
                $unmappedKeys = array_diff_key($item, $mappedItem);
                $mappedItem['otherColumns'] = json_encode($unmappedKeys);
                $mappedItem['mls_type'] = 1;
    
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
                            'updated_at' => now(),
                        ]);
                    echo "Media for ListingId $listingId updated.\n";
                } else {
                    DB::table('bridge_property_images_json')->insert([
                        'listing_id' => $listingId,
                        'images_json' => $imagesJson,
                        'is_imported' => 0,
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

                $mappedItem['ModificationTimestamp'] = (new \DateTime($item['ModificationTimestamp']))->format('Y-m-d H:i:s');

    
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
    
            if ($offset + $batchSize >= $totalCount) {
                echo "All properties processed.\n";
                break;
            }
        }



        DB::table('bridge_property_images_json')
            ->whereNotIn('listing_id', $processedListingIds)
            ->delete();

        DB::table('properties_all_data')
            ->whereNotIn('ListingId', $processedListingIds)
            ->where('mls_type', 1)
            ->delete();


    
        DB::table($cronTablename)->where('id', $currLogId)->update([
            'steps_completed' => 2,
            'success' => 0,
            'cron_end_time' => date('Y-m-d H:i:s'),
        ]);
        echo "Cron job completed.\n";
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


    public function processBridgePropertyImages()
    {
        try {
            $s3 = new S3Client([
                'version' => 'latest',
                'region' => env('AWS_DEFAULT_REGION'),
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            // Fetch records where is_imported is 1
            $properties = DB::table('bridge_property_images_json')
                ->where('is_imported', 0)
                ->orWhere('is_imported', 1)
                ->get(['listing_id', 'images_json', 'last_processed_index']);

            $totalPropertiesCount = $properties->count();
            $remainingCount = $totalPropertiesCount;

            foreach ($properties as $property) {
                $listingId = $property->listing_id;
                $imagesJson = json_decode($property->images_json, true);
                $lastProcessedIndex = $property->last_processed_index ?? 0;

                if (empty($imagesJson)) {
                    echo "ListingId {$listingId} has no images to process." . PHP_EOL;
                    $remainingCount--;
                    continue;
                }

                $skipFirstImage = $lastProcessedIndex == 0; // Skip first image if starting fresh
                $index = $lastProcessedIndex + 1;

                foreach (array_slice($imagesJson, $lastProcessedIndex) as $imageUrl) {
                    if ($skipFirstImage) {
                        $skipFirstImage = false;
                        continue;
                    }

                    try {
                        $imageContent = file_get_contents($imageUrl);
                        $filename = "photo-{$listingId}-{$index}.jpeg";
                        $result = $s3->putObject([
                            'Bucket' => env('AWS_BUCKET'),
                            'Key' => "property-images/{$listingId}/{$filename}",
                            'Body' => $imageContent,
                        ]);

                        $imageUrlOnS3 = $result['ObjectURL'];

                        // Insert or update the image in the property_images table
                        DB::table('property_images')->updateOrInsert(
                            [
                                'listingid' => $listingId,
                                'image_url' => $imageUrlOnS3,
                            ],
                            [
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );

                        echo "Index: {$index} :: Downloaded for ListingId {$listingId}" . PHP_EOL;

                        // Update last processed index in the database
                        DB::table('bridge_property_images_json')
                            ->where('listing_id', $listingId)
                            ->update(['last_processed_index' => $index]);

                        $index++;
                    } catch (\Exception $e) {
                        echo "Error processing image for ListingId {$listingId} at Index {$index}: {$e->getMessage()}" . PHP_EOL;
                        continue;
                    }
                }

                // Update the is_imported status to 2 for the processed listing_id
                DB::table('bridge_property_images_json')
                    ->where('listing_id', $listingId)
                    ->update(['is_imported' => 2]);

                echo "TotalProperties: {$totalPropertiesCount} :: Remaining: {$remainingCount} :: Downloaded for ListingId {$listingId}" . PHP_EOL;

                $remainingCount--;
            }

            echo "Processing completed for all properties." . PHP_EOL;

        } catch (\Exception $e) {
            echo "Error: {$e->getMessage()}" . PHP_EOL;
        }
    }



}


