<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $imagePath = $_FILES["image"]["tmp_name"];

    $url = "http://127.0.0.1:5000/predict"; // Flask API URL
    $postData = [
        "file" => new CURLFile($imagePath) // ✅ Ensure Flask receives "file" instead of "image"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    $prediction = $result["disease"] ?? "Error in prediction"; // ✅ Correct key
    $confidence = isset($result["confidence"]) ? round($result["confidence"], 2) : "0"; // ✅ Ensure rounding
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disease Prediction</title>
</head>
<body>
    <h2>Disease Prediction</h2>
    
    <form method="POST" enctype="multipart/form-data">
        <label>Upload Crop Image:</label>
        <input type="file" name="image" required>
        <button type="submit">Predict</button>
    </form>

    <?php if (isset($prediction) && $prediction !== "Error in prediction"): ?>
        <h3>Prediction: <?php echo htmlspecialchars($prediction); ?></h3>
        <p>Confidence: <?php echo htmlspecialchars($confidence); ?>%</p>
    <?php elseif (isset($prediction)): ?>
        <h3 style="color: red;"><?php echo htmlspecialchars($prediction); ?></h3>
    <?php endif; ?>
</body>
</html>
