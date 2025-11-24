<?php 
if (!isset($modificacion_ruta)) {
    $modificacion_ruta = "";
}
?>
<nav id="navbar1" class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container-fluid">
        <?php 
            $page_label = $page_title ?? '';
            if ($page_label === '' || $page_label === null) {
                $uri = $_SERVER['REQUEST_URI'] ?? '';
                $base_path = '/GORA/';
                if (strpos($uri, $base_path) === 0) {
                    $path = explode('?', substr($uri, strlen($base_path)))[0];
                    $path = trim($path, '/');
                    $page_label = $path !== '' ? $path : 'dashboard';
                } else {
                    $page_label = 'dashboard';
                }
                $page_label = preg_replace('/\.php$/', '', $page_label);
                $page_label = str_replace('-', ' ', $page_label);
                $page_label = ucfirst($page_label);
            }
        ?>
        <span class="navbar-brand small fw-normal breadcrumb-brand">
            <i class="bi bi-house-door me-1 text-muted"></i>
            <span class="text-muted d-none d-sm-inline">GORA</span>
            <i class="bi bi-chevron-right mx-1 text-muted d-none d-sm-inline"></i>
            <span class="brand-page text-primary text-uppercase"><?php echo htmlspecialchars($page_label); ?></span>
        </span>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-4 me-2"></i> 
                        <span class="d-none d-sm-inline"><?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?></span>
                        <span class="d-sm-none">Usuario</span>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" aria-labelledby="navbarUserDropdown">
                        <li>
                            <a class="dropdown-item" href="/GORA/profile.php">
                                <i class="bi bi-person me-2"></i>Mi Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="/GORA/logout" id="logout-link">
                                <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

