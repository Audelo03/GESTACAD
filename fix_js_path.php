<?php
/**
 * Script to fix the tutorias.js path in footer.php
 */

$footerFile = __DIR__ . '/views/objects/footer.php';
$content = file_get_contents($footerFile);

// Replace the incorrect path with the correct one
$content = str_replace(
    '<script src="/GESTACAD/assets/js/tutorias.js"></script>',
    '<script src="/GESTACAD/public/js/tutorias.js"></script>',
    $content
);

file_put_contents($footerFile, $content);

echo "Successfully updated tutorias.js path in footer.php\n";
?>