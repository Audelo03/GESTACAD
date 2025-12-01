<?php
/**
 * Script to add tutoring modals and JavaScript to footer.php
 */

$footerFile = __DIR__ . '/views/objects/footer.php';
$content = file_get_contents($footerFile);

// Check if already added
if (strpos($content, 'tutorias_modals.php') !== false) {
    echo "Modals already included in footer.php\n";
    exit(0);
}

// Find the </body> tag and add our includes before it
$includes = "\n<?php include __DIR__ . '/../tutorias/tutorias_modals.php'; ?>\n<script src=\"/GESTACAD/assets/js/tutorias.js\"></script>\n\n</body>";

$content = str_replace('</body>', $includes, $content);

file_put_contents($footerFile, $content);

echo "Successfully added tutoring modals and JavaScript to footer.php\n";
?>