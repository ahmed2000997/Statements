<?php

$url = 'http://localhost/lrs/get.php';
$hedares_value = isset($_GET['hedares']) ? $_GET['hedares'] : 'all';

$url .= '?hedares=' . urlencode($hedares_value);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
    $data = json_decode($response, true);

    // Initialize an array to store interactions count for each day
    $interactions_count_per_day = [];

    // Loop through the data to count interactions per day
    foreach ($data as $item) {
        $timestamp = date('Y-m-d', strtotime($item['timestamp']));

        // Check if the timestamp already exists in the array, if not, initialize it to zero
        if (!isset($interactions_count_per_day[$timestamp])) {
            $interactions_count_per_day[$timestamp] = 0;
        }

        // Increment the interactions count for the corresponding day
        $interactions_count_per_day[$timestamp]++;
    }

    // Convert the interactions count per day array to the desired format
    $formatted_interactions_count_per_day = [];
    foreach ($interactions_count_per_day as $date => $count) {
        $formatted_interactions_count_per_day[] = ['date' => $date, 'interactions' => $count];
    }

    // Convert the formatted interactions count per day array to JSON format
    $json_formatted_interactions_count_per_day = json_encode($formatted_interactions_count_per_day, JSON_PRETTY_PRINT);

    echo $json_formatted_interactions_count_per_day;
} else {
    echo "error";
}

curl_close($ch);
?>
