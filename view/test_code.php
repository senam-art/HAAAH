<?php
/**
 * LOCATE AND DISPLAY EXISTING UPLOAD IMAGE
 */

// ✅ Base uploads folder in public_html
$uploadsDir = dirname(__DIR__, 2) . '/uploads'; // e.g., /home/senam.dzomeku/public_html/uploads
$relativeUploads = '../uploads'; // browser-accessible path

// Example: user folder and file
$userFolder = 'u19/p12';
$filename   = '1764241335_instagram-logo_976174-11.jpg copy.png';
$filePath   = $uploadsDir . '/' . $userFolder . '/' . $filename;
$fileUrl    = $relativeUploads . '/' . $userFolder . '/' . $filename;

// ================================
function perms($path) {
    if (!file_exists($path)) return 'Path does not exist';
    return substr(sprintf('%o', fileperms($path)), -4);
}

$results = [];
$results['Uploads dir exists'] = is_dir($uploadsDir) ? 'YES' : 'NO';
$results['Uploads dir perms'] = perms($uploadsDir);
$results['Uploads writable'] = is_writable($uploadsDir) ? 'YES' : 'NO';

$results['User folder exists'] = is_dir($uploadsDir . '/' . $userFolder) ? 'YES' : 'NO';
$results['User folder perms'] = perms($uploadsDir . '/' . $userFolder);
$results['User folder writable'] = is_writable($uploadsDir . '/' . $userFolder) ? 'YES' : 'NO';

$results['File exists'] = file_exists($filePath) ? 'YES' : 'NO';
$results['File perms'] = perms($filePath);
$results['File readable'] = is_readable($filePath) ? 'YES' : 'NO';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Locate Uploaded Image</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        table { border-collapse: collapse; width: 800px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #f1f1f1; text-align: left; }
        .ok { color: green; font-weight: bold; }
        .bad { color: red; font-weight: bold; }
        code { background: #eee; padding: 4px; }
    </style>
</head>
<body>

<h1>📁 Uploaded Image Check</h1>

<table>
<?php foreach ($results as $k => $v): ?>
    <tr>
        <th><?= htmlspecialchars($k) ?></th>
        <td class="<?= in_array($v, ['YES']) ? 'ok' : 'bad' ?>">
            <?= htmlspecialchars($v) ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>

<h2>Filesystem Path</h2>
<code><?= htmlspecialchars($filePath) ?></code>

<h2>Browser Test</h2>
<?php if (file_exists($filePath)): ?>
    <p class="ok">✅ Image loaded successfully</p>
    <img src="<?= htmlspecialchars($fileUrl) ?>" style="max-width: 600px; height: auto;">
<?php else: ?>
    <p class="bad">❌ Image not found</p>
<?php endif; ?>

</body>
</html>
