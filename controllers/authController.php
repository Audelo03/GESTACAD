<?php
/**
 * CONTROLADOR DE AUTENTICACIÓN - GORA
 * 
 * Maneja el proceso de login, logout y verificación de autenticación
 * de usuarios en el sistema.
 */

// session_start() ya se llama en index.php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Usuario.php";

class AuthController {
    public $usuario;

    /**
     * Constructor del controlador de autenticación
     * @param PDO $conn - Conexión a la base de datos
     */
    public function __construct($conn) {
        $this->usuario = new Usuario($conn);
    }

    /**
     * Autentica un usuario con email y contraseña
     * @param string $email - Email del usuario
     * @param string $password - Contraseña del usuario
     * @return bool - True si la autenticación es exitosa, false si no
     */
    public function login($email, $password) {
        $user = $this->usuario->getByEmail($email);
        if ($user && (password_verify($password, $user["password"]) || $password === $user["password"])) {
            // Establecer variables de sesión del usuario autenticado
            $_SESSION["usuario_id"] = $user["id_usuario"];
            $_SESSION["usuario_nombre"] = $user["nombre"];
            $_SESSION["usuario_nivel"] = $user["niveles_usuarios_id_nivel_usuario"];
            $_SESSION["usuario_apellido_paterno"] = $user["apellido_paterno"];
            $_SESSION["usuario_apellido_materno"] = $user["apellido_materno"];
            return true;
        }
        return false;
    }

    /**
     * Cierra la sesión del usuario y redirige al login
     */
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: /GESTACAD/login");
        exit;
    }

    /**
     * Verifica si el usuario está autenticado
     * Redirige al login si no está autenticado
     */
    public function checkAuth() {
        if (!isset($_SESSION["usuario_id"])) {
            header("Location: /GESTACAD/login");
            exit;
        }
    }

    /**
     * Muestra la página de login
     */
    public function showLogin() {
        include __DIR__ . '/../views/login.php';
    }

    /**
     * Muestra el dashboard principal
     * Verifica autenticación antes de mostrar
     */
    public function showDashboard() {
        $this->checkAuth();
        include __DIR__ . '/../views/dashboard.php';
    }
}
?>
