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
    $result = [];

    foreach ($data as $item) {
        $roles = $item['context']['extensions']['http://vocab.xapi.fr/extensions/user-role'];
        $name = $item['actor']['name'];
        if (strpos($roles, 'student') !== false) {
            $result[] = $name;
        }
    }

    // Convert the result array to JSON format
    $json_result = json_encode($result, JSON_PRETTY_PRINT);
   

    // Count interactions for each student
    $interactions_count = [];
    foreach ($result as $name) {
        $interactions_count[$name] = 0;
        foreach ($data as $item) {
            if ($item['actor']['name'] === $name) {
                $interactions_count[$name]++;
            }
        }
    }

    // Convert the interactions count array to JSON format
    $json_interactions_count = json_encode($interactions_count, JSON_PRETTY_PRINT);

    // Format the interactions count array as desired
    $formatted_results = [];
    foreach ($interactions_count as $name => $count) {
        $formatted_results[] = [$name, $count];
    }

    // Sort the formatted results array by interactions count in descending order
    usort($formatted_results, function ($a, $b) {
        return $b[1] - $a[1];
    });

    // Convert the formatted results array to JSON format
    $json_formatted_results = json_encode($formatted_results, JSON_PRETTY_PRINT);
    echo $json_formatted_results;
} else {
    echo "error";
}

curl_close($ch);
?>
