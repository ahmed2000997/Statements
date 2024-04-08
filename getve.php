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

    $results = [];
    foreach ($data as $item) {
        $actor_name = $item['actor']['name'];
        $verb_id = $item['verb']['id'];
        $verb_parts = explode('/', $verb_id);
        $verb = end($verb_parts);

        $object_name = $item['object']['definition']['name']['en'];

        $activity = "{$actor_name} {$verb} {$object_name}";

        $timestamp = $item['timestamp'];

        $full_json = json_encode($item, JSON_PRETTY_PRINT);
        $results[] = [
            'activity' => $activity,
            'actor' => $actor_name,
            'verb' => $verb,
            'object' => $object_name,
            'timestamp' => $timestamp,
            'json_data' => json_decode($full_json)
        ];
    }

    // Convert the results array to JSON
    $json_results = json_encode($results, JSON_PRETTY_PRINT);
    echo $json_results;
} else {
    echo "Error fetching data from Watershed LRS.";
}

curl_close($ch);
?>
