<?php
/**
 * Display an existing uploaded image
 */

// User-specific folder and file
$userFolder = 'u19/p12';
$filename   = '1764241335_instagram-logo_976174-11.jpg copy.png';

// Construct the browser URL
// Important: rawurlencode() to handle spaces and special characters
$baseUploadsUrl = '/~senam.dzomeku/uploads';
$imageUrl = $baseUploadsUrl . '/' . $userFolder . '/' . rawurlencode($filename);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>View Uploaded Image</title>
</head>
<body>
    <h1>Uploaded Image</h1>
    <p>File URL: <a href="<?= htmlspecialchars($imageUrl) ?>"><?= htmlspecialchars($imageUrl) ?></a></p>
    <img src="<?= htmlspecialchars($imageUrl) ?>" alt="Uploaded Image" style="max-width:600px; height:auto; display:block; margin-top:20px;">
</body>
</html>
