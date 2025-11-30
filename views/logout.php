<?php
// Remove session_start() as it's already started in index.php
session_unset();
session_destroy();

header("Location: /GESTACAD/login");
exit;
?>
