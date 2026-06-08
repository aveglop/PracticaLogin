<?php
session_start();
require_once 'constantes.php';

$mensaje = filter_input(INPUT_GET, 'error', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {

        $dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST;
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $consulta = $pdo->prepare(
                "SELECT id, name, email, password, rol
             FROM usuarios
             WHERE email = :email"
        );

        $consulta->execute([
            'email' => $email
        ]);

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {

            session_regenerate_id(true);

            $_SESSION['id'] = $usuario['id'];
            $_SESSION['name'] = $usuario['name'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['rol'] = $usuario['rol'];

            header('Location: principal.php');
            exit;
        } else {

            $mensaje = "Credenciales incorrectas.";
        }
    } catch (PDOException $e) {

        $mensaje = "Error al iniciar sesión";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
    </head>
    <body>

        <h2>Iniciar sesión</h2>

        <?php if ($mensaje): ?>
            <p><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit">Entrar</button>

        </form>

    </body>
</html>