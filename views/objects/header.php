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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/GESTACAD/public/css/sidebar.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.0/dist/style.css" />

  <!-- Tema Dorado/Negro compilado -->
  <link href="/GESTACAD/public/css/theme.css" rel="stylesheet">

  <!-- Script principal de la aplicación -->
  <script src="/GESTACAD/public/js/theme-toggle.js"></script>
  <script src="/GESTACAD/public/js/app.js" defer></script>

</head>

<body>

  <div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <main id="app-content" class="flex-grow-1  collapsed">
      <?php include "navbar.php" ?>