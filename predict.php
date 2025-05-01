<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];

    // ✅ Check if the file is an image
    if ($file['type'] !== 'image/jpeg' && $file['type'] !== 'image/png') {
        die("❌ Only JPG and PNG files are allowed.");
    }

    // ✅ Move file to a temporary location
    $tempPath = $file['tmp_name'];

    // ✅ API URL (Flask Backend)
    $url = "http://127.0.0.1:5000/predict";

    // ✅ Prepare the file for sending
    $cfile = new CURLFile($tempPath, $file['type'], $file['name']);
    $postData = ['image' => $cfile];

    // ✅ Send request to Flask API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    // ✅ Decode JSON response
    $result = json_decode($response, true);

    if ($result) {
        echo "<h3>🔍 Prediction Result:</h3>";
        echo "<p><strong>Disease:</strong> " . $result['disease'] . "</p>";
        echo "<p><strong>Confidence:</strong> " . round($result['confidence'], 2) . "%</p>";
    } else {
        echo "❌ Error: Could not get a response from the AI model.";
    }
}
?>
