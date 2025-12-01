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
      --login-bg: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
      --login-card: rgba(255, 255, 255, 0.95);
      --login-card-blur: rgba(255, 255, 255, 0.1);
      --login-accent: #667eea;
      --login-accent-hover: #5568d3;
      --login-text: #1a202c;
      --login-muted: #718096;
      --login-border: rgba(102, 126, 234, 0.2);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html,
    body {
      height: 100%;
      overflow-x: hidden;
    }

    body.login-page {
      background: var(--login-bg);
      background-size: 400% 400%;
      animation: gradientShift 15s ease infinite;
      color: var(--login-text);
      padding: 1.5rem;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    /* Animated background gradient */
    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* Floating particles effect */
    body.login-page::before {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      background-image: 
        radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
      animation: float 20s ease-in-out infinite;
      pointer-events: none;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(5deg); }
    }

    .form-signin {
      max-width: 440px;
      width: 100%;
      padding: 3rem 2.5rem;
      background: var(--login-card);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(255, 255, 255, 0.5) inset;
      border-radius: 24px;
      position: relative;
      z-index: 1;
      animation: slideUp 0.6s ease-out;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .form-signin:hover {
      transform: translateY(-2px);
      box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.15),
        0 0 0 1px rgba(255, 255, 255, 0.6) inset;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .login-brand {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      letter-spacing: 1px;
      font-weight: 900;
      font-size: 2rem;
      margin-bottom: 0.5rem;
      text-transform: uppercase;
    }

    .login-logo {
      width: 90px;
      height: 90px;
      object-fit: contain;
      border-radius: 20px;
      padding: 12px;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      margin-bottom: 1rem;
    }

    .login-logo:hover {
      transform: scale(1.05) rotate(2deg);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
    }

    .login-title {
      color: var(--login-text);
      font-weight: 600;
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
    }

    .login-footer {
      color: var(--login-muted);
      font-size: 0.875rem;
      margin-top: 2rem;
    }

    .form-floating {
      margin-bottom: 1.5rem;
    }

    .form-floating>label {
      color: var(--login-muted);
      font-weight: 500;
      padding: 1rem 1.25rem;
      font-size: 0.95rem;
    }

    .form-floating .form-control {
      background: rgba(255, 255, 255, 0.9);
      border: 2px solid var(--login-border);
      color: #1a202c;
      padding: 1rem 1.25rem;
      padding-right: 3.5rem;
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 500;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      height: calc(3.5rem + 2px);
    }

    .form-floating .form-control::placeholder {
      color: transparent;
    }

    .form-floating .form-control:focus {
      background: rgba(255, 255, 255, 1);
      border-color: var(--login-accent);
      box-shadow: 
        0 0 0 4px rgba(102, 126, 234, 0.1),
        0 4px 12px rgba(102, 126, 234, 0.15);
      color: #1a202c;
      transform: translateY(-1px);
    }

    .form-floating .form-control.is-valid {
      border-color: #10b981;
      background: rgba(255, 255, 255, 0.95);
      color: #1a202c;
    }

    .form-floating .form-control.is-valid:focus {
      border-color: #10b981;
      background: rgba(255, 255, 255, 1);
      box-shadow: 
        0 0 0 4px rgba(16, 185, 129, 0.1),
        0 4px 12px rgba(16, 185, 129, 0.15);
      color: #1a202c;
    }

    .form-floating .form-control.is-invalid {
      border-color: #ef4444;
      background: rgba(255, 255, 255, 0.95);
      color: #1a202c;
    }

    .form-floating .form-control.is-invalid:focus {
      border-color: #ef4444;
      background: rgba(255, 255, 255, 1);
      box-shadow: 
        0 0 0 4px rgba(239, 68, 68, 0.1),
        0 4px 12px rgba(239, 68, 68, 0.15);
      color: #1a202c;
    }

    .login-submit {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #fff;
      font-weight: 600;
      font-size: 1rem;
      border: none;
      border-radius: 12px;
      padding: 0.875rem 1.5rem;
      box-shadow: 
        0 4px 15px rgba(102, 126, 234, 0.4),
        0 0 0 0 rgba(102, 126, 234, 0.5);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      margin-top: 0.5rem;
    }

    .login-submit::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.3);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }

    .login-submit:hover::before {
      width: 300px;
      height: 300px;
    }

    .login-submit:hover {
      transform: translateY(-2px);
      box-shadow: 
        0 6px 20px rgba(102, 126, 234, 0.5),
        0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .login-submit:active {
      transform: translateY(0);
      box-shadow: 
        0 2px 10px rgba(102, 126, 234, 0.4),
        0 0 0 2px rgba(102, 126, 234, 0.1);
    }

    .login-submit span {
      position: relative;
      z-index: 1;
    }

    .password-toggle {
      right: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      width: 40px;
      height: 40px;
      color: var(--login-muted);
      z-index: 3;
      cursor: pointer;
      background: transparent;
      border: none;
      border-radius: 8px;
      transition: all 0.2s ease;
    }

    .password-toggle:focus {
      outline: 2px solid var(--login-accent);
      outline-offset: 2px;
    }

    .password-toggle:hover {
      color: var(--login-accent);
      background: rgba(102, 126, 234, 0.1);
      transform: translateY(-50%) scale(1.1);
    }

    .password-toggle i {
      pointer-events: none;
      font-size: 1.1rem;
    }

    .login-card .form-floating {
      border-radius: 12px;
    }

    .alert {
      border-radius: 12px;
      border: none;
      backdrop-filter: blur(10px);
      animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .invalid-feedback,
    .valid-feedback {
      font-size: 0.875rem;
      font-weight: 500;
      margin-top: 0.5rem;
      padding-left: 0.25rem;
      animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .valid-feedback {
      color: #10b981;
    }

    .invalid-feedback {
      color: #ef4444;
    }

    @media (max-width: 575.98px) {
      body.login-page {
        padding: 1rem;
      }

      .form-signin {
        padding: 2rem 1.5rem;
        border-radius: 20px;
      }

      .login-brand {
        font-size: 1.75rem;
      }

      .login-logo {
        width: 75px;
        height: 75px;
      }

      .form-floating .form-control {
        padding-left: 1rem;
        padding-right: 3rem;
        font-size: 16px; /* Prevents zoom on iOS */
      }

      .form-floating>label {
        padding-left: 1rem;
      }
    }

    /* Smooth focus transitions */
    .form-control,
    .btn {
      outline: none;
    }

    /* Loading state for button */
    .login-submit.loading {
      pointer-events: none;
      opacity: 0.7;
    }

    .login-submit.loading::after {
      content: '';
      position: absolute;
      width: 16px;
      height: 16px;
      top: 50%;
      left: 50%;
      margin-left: -8px;
      margin-top: -8px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-top-color: white;
      border-radius: 50%;
      animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
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

      <div class="text-center mb-5">
        <div class="mb-4">
          <img src="/GESTACAD/public/images/logo.png" alt="Logo GESTACAD" class="login-logo"
            onerror="this.style.display='none';">
        </div>
        <p class="login-brand mb-2">GESTACAD</p>
        <h2 class="login-title">Iniciar Sesión</h2>
        <p style="color: var(--login-muted); font-size: 0.9rem; margin-top: 0.5rem;">Ingresa tus credenciales para continuar</p>
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
      <button class="btn login-submit w-100 py-3" type="submit">
        <span>Entrar</span>
      </button>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger d-flex align-items-center mt-4" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
          <div class="flex-grow-1">
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
      const form = document.querySelector('form');
      const submitBtn = document.querySelector('.login-submit');
      
      form.addEventListener('submit', function (e) {
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
        } else {
          // Add loading state
          submitBtn.classList.add('loading');
          submitBtn.querySelector('span').textContent = 'Iniciando sesión...';
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
