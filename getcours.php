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
    $courses = [];

    foreach ($data as $item) {
        $activity_type = $item['object']['definition']['type'];
        if ($activity_type == 'http://vocab.xapi.fr/activities/course') {
            $course_name = $item['object']['definition']['name']['en'];
            if (!isset($courses[$course_name])) {
                $courses[$course_name] = 0;
            }
            $courses[$course_name]++;
        }
    }

    // Sort the courses by interaction count in descending order
    arsort($courses);

    // Format the result as an array of arrays
    $result = [];
    foreach ($courses as $name => $count) {
        $result[] = [$name, $count];
    }

    // Convert the result to JSON
    $json_result = json_encode($result, JSON_PRETTY_PRINT);
    echo $json_result;
} else {
    echo "Error fetching data from Watershed LRS.";
}


curl_close($ch);

?>
