<?php
require_once __DIR__ . '/../controllers/AuthController.php';

$error = "";

// Si el formulario fue enviado, procesar el login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Completa todos los campos.";
    } else {
        $auth = new AuthController();
        $error = $auth->login($email, $password);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FerreSystem — Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-card {
            background: #fff;
            border-radius: 12px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 2px 16px rgba(0,0,0,0.08);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-logo h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #C0392B;
            margin: 0;
        }
        .login-logo p {
            font-size: 0.85rem;
            color: #888;
            margin: 4px 0 0;
        }
        .btn-login {
            background-color: #C0392B;
            border: none;
            color: #fff;
            width: 100%;
            padding: 0.65rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.2s;
        }
        .btn-login:hover {
            background-color: #a93226;
            color: #fff;
        }
        .form-control:focus {
            border-color: #C0392B;
            box-shadow: 0 0 0 0.2rem rgba(192,57,43,0.15);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <h1>J&S Ferretería</h1>
            <p>Panel de administración</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:0.9rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Correo electrónico</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    placeholder="correo@ejemplo.com"
                    required
                    autofocus
                >
            </div>
            <div class="mb-4">
                <label for="password" class="form-label fw-semibold">Contraseña</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    placeholder="••••••••"
                    required
                >
            </div>
            <button type="submit" class="btn-login">Ingresar</button>
        </form>
    </div>
</body>
</html>