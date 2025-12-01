<?php
// Remove session_start() as it's already started in index.php
// functions_util.php is already included in index.php
require_once __DIR__ . "/../controllers/authController.php";

$auth = new AuthController($conn);

if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Si ya tiene un error guardado
if (isset($_SESSION['error_message'])) {
  $iniciado = true;
  include __DIR__ . "/logout.php";
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $error = "Petición inválida.";
  } else {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if (empty($email) || empty($password)) {
      $error = "Debes llenar todos los campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error = "Formato de correo inválido.";
    } else {
      // Sanitizar email
      $email = eemail($email);

      // Intento de login
      if ($auth->login($email, $password)) {
        // Prevenir fijación de sesión
        session_regenerate_id(true);

        // Verificación de rol
        if (isset($_SESSION["usuario_nivel"])) {
          if ($_SESSION["usuario_nivel"] == 4 || $_SESSION["usuario_nivel"] == 1) {
            header("Location: /GESTACAD/dashboard");
          } else {
            header("Location: /GESTACAD/listas");
          }
          exit;
        } else {
          $error = "No se pudo determinar el nivel de usuario.";
        }
      } else {
        // Manejo de intentos fallidos
        if (!isset($_SESSION['login_attempts'])) {
          $_SESSION['login_attempts'] = 0;
        }
        $_SESSION['login_attempts']++;

        if ($_SESSION['login_attempts'] > 5) {
          $error = "Demasiados intentos fallidos. Intenta más tarde.";
        } else {
          $error = "Correo o contraseña incorrectos.";
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es" class="h-100">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root {
      --login-bg: linear-gradient(160deg, #0f172a 0%, #111827 35%, #0b1323 100%);
      --login-card: #0f172a;
      --login-accent: #4f9cf9;
      --login-text: #e5e7eb;
      --login-muted: #9ca3af;
    }

    html,
    body {
      height: 100%;
    }

    body.login-page {
      background: var(--login-bg);
      color: var(--login-text);
      padding: 1.5rem 0;
    }

    .form-signin {
      max-width: 420px;
      padding: 2.25rem 2rem;
      background: var(--login-card);
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
      border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .login-brand {
      color: var(--login-accent);
      letter-spacing: 0.5px;
      font-weight: 800;
    }

    .login-title {
      color: var(--login-text);
      font-weight: 700;
    }

    .login-footer {
      color: var(--login-muted);
      font-size: 0.9rem;
    }

    .form-floating>label {
      color: var(--login-muted);
      font-weight: 500;
    }

    .form-floating .form-control {
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.08);
      color: #000000;
      padding-right: 3.2rem;
      position: relative;
      z-index: 1;
    }

    .form-floating .form-control:focus {
      background: rgba(255, 255, 255, 0.05);
      border-color: var(--login-accent);
      box-shadow: 0 0 0 0.2rem rgba(79, 156, 249, 0.2);
      color: #000000;
    }

    .login-submit {
      background: linear-gradient(135deg, #4f9cf9, #3b82f6);
      color: #fff;
      font-weight: 700;
      border: none;
      box-shadow: 0 10px 25px rgba(79, 156, 249, 0.35);
    }

    .login-submit:hover {
      opacity: 0.95;
    }

    .password-toggle {
      right: 0.6rem;
      top: 50%;
      transform: translateY(-50%);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      width: 36px;
      height: 36px;
      color: var(--login-muted);
      z-index: 3;
      cursor: pointer;
      background: transparent;
      border: none;
    }

    .password-toggle:focus {
      outline: none;
      box-shadow: none;
    }

    .password-toggle:hover {
      color: var(--login-muted) !important;
      transform: translateY(-50%) !important;
    }

    .password-toggle i {
      pointer-events: none;
      font-size: 1.05rem;
    }

    .login-card .form-floating {
      border-radius: 0.75rem;
      overflow: hidden;
    }

    .photo-placeholder {
      width: 70px;
      height: 70px;
      border-radius: 18px;
      background: rgba(255, 255, 255, 0.05);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    @media (max-width: 575.98px) {
      body.login-page {
        padding: 1rem 0.75rem;
      }

      .form-signin {
        padding: 1.5rem 1.25rem;
        border-radius: 1.25rem;
      }

      .form-floating .form-control {
        padding-left: 2.9rem;
        padding-right: 3rem;
      }

      .form-floating>label {
        padding-left: 2.9rem;
      }

      .login-title {
        margin-top: 0.5rem;
      }
    }
  </style>
  <link href="/GESTACAD/public/css/theme.css" rel="stylesheet">
</head>

<body class="d-flex align-items-center py-4 h-100 login-page">

  <main class="form-signin w-100 m-auto login-card rounded-3">
    <form method="POST" novalidate>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

      <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($_SESSION['error_message']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        <?php unset($_SESSION['error_message']); endif; ?>

      <div class="text-center mb-4">
        <p class="h3 mb-3 font-weight-bold login-brand">GESTACAD</p>
        <div class="login-photo-placeholder mb-3">
          <img src="/GESTACAD/public/images/logo.png" alt="Logo GESTACAD" class="login-logo"
            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

        </div>
        <h2 class="h5 mb-4 fw-normal login-title">Iniciar Sesión</h2>
      </div>

      <div class="form-floating mb-3">
        <input type="email" name="email" class="form-control" id="floatingInput" placeholder="ejemplo@correo.com"
          autocomplete="email" spellcheck="false" required aria-describedby="emailHelp">
        <label for="floatingInput"><i class="bi bi-envelope-fill me-2"></i>Correo electrónico</label>
        <div class="invalid-feedback" id="emailError"></div>
        <div class="valid-feedback" id="emailSuccess"></div>
      </div>

      <div class="form-floating mb-3 position-relative">
        <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Contraseña"
          autocomplete="current-password" required aria-describedby="passwordHelp">
        <label for="floatingPassword"><i class="bi bi-key-fill me-2"></i>Contraseña</label>
        <button type="button" class="password-toggle position-absolute" id="togglePassword"
          aria-label="Mostrar/ocultar contraseña">
          <i class="bi bi-eye-slash" id="toggleIcon"></i>
        </button>
        <div class="invalid-feedback" id="passwordError"></div>
      </div>
      <button class="btn login-submit w-100 py-2" type="submit">Entrar</button>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger d-flex align-items-center mt-3" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          <div>
            <?= htmlspecialchars($error) ?>
          </div>
        </div>
      <?php endif; ?>

      <p class="mt-4 mb-3 text-center login-footer">2025 ITSA</p>
    </form>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const emailField = document.getElementById('floatingInput');
      const passwordField = document.getElementById('floatingPassword');
      const togglePasswordBtn = document.getElementById('togglePassword');
      const toggleIcon = document.getElementById('toggleIcon');
      const emailError = document.getElementById('emailError');
      const emailSuccess = document.getElementById('emailSuccess');
      const passwordError = document.getElementById('passwordError');

      // Email validation
      emailField.addEventListener('input', function () {
        const email = this.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email.length === 0) {
          this.classList.remove('is-valid', 'is-invalid');
          emailError.textContent = '';
          emailSuccess.textContent = '';
        } else if (emailRegex.test(email)) {
          this.classList.remove('is-invalid');
          this.classList.add('is-valid');
          emailError.textContent = '';
          emailSuccess.textContent = '✓ Formato de correo válido';
        } else {
          this.classList.remove('is-valid');
          this.classList.add('is-invalid');
          emailSuccess.textContent = '';
          emailError.textContent = 'Formato de correo inválido';
        }
      });


      // Password visibility toggle
      togglePasswordBtn.addEventListener('click', function () {
        if (passwordField.type === 'password') {
          passwordField.type = 'text';
          toggleIcon.className = 'bi bi-eye';
          toggleIcon.setAttribute('aria-label', 'Ocultar contraseña');
        } else {
          passwordField.type = 'password';
          toggleIcon.className = 'bi bi-eye-slash';
          toggleIcon.setAttribute('aria-label', 'Mostrar contraseña');
        }
      });

      // Form submission validation
      document.querySelector('form').addEventListener('submit', function (e) {
        const email = emailField.value.trim();
        const password = passwordField.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        let hasErrors = false;

        // Email validation
        if (!email || !emailRegex.test(email)) {
          emailField.classList.add('is-invalid');
          emailError.textContent = 'Por favor ingresa un correo electrónico válido';
          hasErrors = true;
        }

        // Password validation
        if (!password) {
          passwordField.classList.add('is-invalid');
          passwordError.textContent = 'Por favor ingresa tu contraseña';
          hasErrors = true;
        }

        if (hasErrors) {
          e.preventDefault();
          // Focus on first invalid field
          const firstInvalid = document.querySelector('.is-invalid');
          if (firstInvalid) {
            firstInvalid.focus();
          }
        }
      });

      // Clear validation on focus
      emailField.addEventListener('focus', function () {
        this.classList.remove('is-invalid');
        emailError.textContent = '';
      });

      passwordField.addEventListener('focus', function () {
        this.classList.remove('is-invalid');
        passwordError.textContent = '';
      });
    });
  </script>

</body>

</html>
