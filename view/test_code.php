<?php
/**
 * PERMISSION + FILE WRITE TEST (FIXED PATH)
 */

// ✅ uploads folder lives in public_html
$uploadsDir = dirname(__DIR__, 2) . '/uploads';
$relativeUploads = '../uploads'; // for browser access
$testSubDir = $uploadsDir . '/test_folder';

// ================================
function perms($path) {
    if (!file_exists($path)) return 'Path does not exist';
    return substr(sprintf('%o', fileperms($path)), -4);
}

$results = [];

$results['Current script dir'] = __DIR__;
$results['Resolved uploads dir'] = $uploadsDir;
$results['Uploads dir exists'] = is_dir($uploadsDir) ? 'YES' : 'NO';
$results['Uploads dir perms'] = perms($uploadsDir);
$results['Uploads writable'] = is_writable($uploadsDir) ? 'YES' : 'NO';

// ================================
// CREATE SUBFOLDER
// ================================
if (is_writable($uploadsDir)) {
    if (!is_dir($testSubDir)) {
        if (mkdir($testSubDir, 0775, true)) {
            $results['Test folder created'] = 'YES';
        } else {
            $results['Test folder created'] = 'FAILED';
        }
    } else {
        $results['Test folder created'] = 'ALREADY EXISTS';
    }
} else {
    $results['Test folder created'] = 'SKIPPED (uploads not writable)';
}

$results['Test folder perms'] = perms($testSubDir);
$results['Test folder writable'] = is_writable($testSubDir) ? 'YES' : 'NO';

// ================================
// CREATE TEST IMAGE
// ================================
$imagePath = $testSubDir . '/permission_test.png';
$imageCreated = false;

if (is_writable($testSubDir)) {
    $img = imagecreatetruecolor(240, 120);
    $bg  = imagecolorallocate($img, 70, 160, 220);
    $txt = imagecolorallocate($img, 255, 255, 255);

    imagefill($img, 0, 0, $bg);
    imagestring($img, 5, 40, 50, 'WRITE OK', $txt);

    if (imagepng($img, $imagePath)) {
        $imageCreated = true;
        $results['Image created'] = 'YES';
    } else {
        $results['Image created'] = 'FAILED';
    }
    imagedestroy($img);
} else {
    $results['Image created'] = 'SKIPPED (no write access)';
}

// ================================
// BROWSER PATH
// ================================
$imageUrl = $relativeUploads . '/test_folder/permission_test.png';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Server Permission Test</title>
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

<h1>✅ Server Permission & Path Resolution Test</h1>

<table>
<?php foreach ($results as $k => $v): ?>
    <tr>
        <th><?= htmlspecialchars($k) ?></th>
        <td class="<?= in_array($v, ['YES','ALREADY EXISTS']) ? 'ok' : 'bad' ?>">
            <?= htmlspecialchars($v) ?>
        </td>
    </tr>
<?php endforeach; ?>
</table>

<h2>Filesystem</h2>
<code><?= htmlspecialchars($uploadsDir) ?></code>

<h2>Browser Test</h2>
<?php if ($imageCreated): ?>
    <p class="ok">✅ Image loaded successfully</p>
    <img src="<?= htmlspecialchars($imageUrl) ?>">
<?php else: ?>
    <p class="bad">❌ Image not created</p>
<?php endif; ?>

</body>
</html>
