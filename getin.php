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
        $name = $item['actor']['name'];
        if (!isset($result[$name])) {
            $result[$name] = 0;
        }
        $result[$name]++;
    }

    // Sort the result array in descending order
    arsort($result);

    // Convert the result array to Grafana Table format
    $grafana_table = [];
    foreach ($result as $name => $count) {
        $grafana_table[] = [$name, $count];
    }

    // Convert the Grafana Table array to JSON format
    $json_result = json_encode($grafana_table, JSON_PRETTY_PRINT);
    echo $json_result;
} else {
    echo "error";
}

curl_close($ch);
?>
