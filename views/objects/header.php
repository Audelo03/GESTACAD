<?php
if (!isset($modificacion_ruta)) {
  $modificacion_ruta = "";
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title><?php echo $page_title ?? 'GESTACAD'; ?></title>
  <link rel="icon" type="image/png" href="/GESTACAD/public/images/logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/GESTACAD/public/css/sidebar.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.0/dist/style.css" />

  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

  <!-- Tema Dorado/Negro compilado -->
  <link href="/GESTACAD/public/css/theme.css" rel="stylesheet">

  <!-- jQuery (requerido para Select2) -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- Script principal de la aplicación -->
  <script src="/GESTACAD/public/js/theme-toggle.js"></script>
  <script src="/GESTACAD/public/js/app.js" defer></script>

</head>

<body>

  <div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <main id="app-content" class="flex-grow-1  collapsed">
      <?php include "navbar.php" ?>