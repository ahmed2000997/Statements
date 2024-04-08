<?php

include('config.php');

if (isset($_GET['hedares'])) {
    $param_value = $_GET['hedares'];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "{$ws_key}:{$ws_secret}");
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);

    $response = curl_exec($ch);

    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
        $data = json_decode($response, true);
        
        // Filter interactions based on the value of 'hedares'
        $filtered_data = array_filter($data, function($entry) use ($param_value) {
            $timestamp = strtotime($entry['timestamp']);
            $today = strtotime('today');
            if ($param_value == 'today') {
                return $timestamp >= $today;
            } elseif ($param_value == 'last_month') {
                $first_day_last_month = strtotime('first day of last month');
                $last_day_last_month = strtotime('last day of last month');
                return $timestamp >= $first_day_last_month && $timestamp <= $last_day_last_month;
            } elseif ($param_value == 'last_year') {
                $first_day_last_year = strtotime('first day of last year');
                $last_day_last_year = strtotime('last day of last year');
                return $timestamp >= $first_day_last_year && $timestamp <= $last_day_last_year;
            } elseif ($param_value == 'last_week') {
                $start_last_week = strtotime('last week monday');
                $end_last_week = strtotime('last week sunday');
                return $timestamp >= $start_last_week && $timestamp <= $end_last_week;
            } elseif ($param_value == 'all') {
                return true; // Return true to include all entries
            } else {
                // Add any additional conditions based on 'hedares' value here
                return false; // Return false if the condition is not met
            }
        });

        // Encode the filtered data in a pretty format without numeric index
        $output = json_encode(array_values($filtered_data), JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK);
        echo $output;
    } else {
        echo "error";
    }

    curl_close($ch);
} else {
    echo "Parameter 'hedares' is missing.";
}
?>
