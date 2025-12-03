<?php
/**
 * PERMISSION + FILE WRITE TEST
 * Safe for school servers
 */

// ================================
// CONFIG – adjust ONLY this if needed
// ================================
$relativeUploads = 'uploads'; // the ONLY writable folder
$uploadsDir = __DIR__ . '/' . $relativeUploads;
$testSubDir = $uploadsDir . '/test_folder';

// ================================
// HELPER: convert perms to readable
// ================================
function perms($path) {
    if (!file_exists($path)) return 'Path does not exist';
    return substr(sprintf('%o', fileperms($path)), -4);
}

// ================================
// RESULTS CONTAINER
// ================================
$results = [];

$results['Current script dir'] = __DIR__;
$results['Uploads dir exists'] = is_dir($uploadsDir) ? 'YES' : 'NO';
$results['Uploads dir perms'] = perms($uploadsDir);
$results['Uploads writable'] = is_writable($uploadsDir) ? 'YES' : 'NO';

// ================================
// CREATE SUB FOLDER
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
    $bg  = imagecolorallocate($img, 60, 180, 120);
    $txt = imagecolorallocate($img, 255, 255, 255);

    imagefill($img, 0, 0, $bg);
    imagestring($img, 5, 30, 50, 'WRITE OK', $txt);

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
// BUILD BROWSER URL
// ================================
$imageUrl = $relativeUploads . '/test_folder/permission_test.png';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Server Permission Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        table { border-collapse: collapse; width: 700px; }
        td, th { border: 1px solid #ccc; padding: 8px; }
        th { background: #f4f4f4; text-align: left; }
        .ok { color: green; font-weight: bold; }
        .bad { color: red; font-weight: bold; }
        code { background: #eee; padding: 3px 6px; }
    </style>
</head>
<body>

<h1>✅ Server Permission & Write Test</h1>

<h2>Permission Checks</h2>
<table>
    <?php foreach ($results as $label => $value): ?>
    <tr>
        <th><?php echo htmlspecialchars($label); ?></th>
        <td class="<?php echo ($value === 'YES' || $value === 'ALREADY EXISTS') ? 'ok' : 'bad'; ?>">
            <?php echo htmlspecialchars($value); ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<h2>Filesystem Paths</h2>
<p><strong>Uploads directory:</strong></p>
<code><?php echo htmlspecialchars($uploadsDir); ?></code>

<p><strong>Test image path:</strong></p>
<code><?php echo htmlspecialchars($imagePath); ?></code>

<h2>Browser Access Test</h2>

<?php if ($imageCreated): ?>
    <p class="ok">✅ Image successfully created and loaded via browser:</p>
    <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="Permission Test Image">
<?php else: ?>
    <p class="bad">❌ Image not created — check permissions above</p>
<?php endif; ?>

</body>
</html>
