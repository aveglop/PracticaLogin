<?php
session_start(); 
require_once 'constantes.php';

$mensaje = filter_input(INPUT_GET, 'error', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // validar campos vacíos
    if ($email === '' || $password === '') {
        $mensaje = "Debes rellenar todos los campos";

    // validar email
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Email no válido";

    } else {

        try {

            $dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST; 'port=' . DB_PORT;
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

            if ($usuario) {

                $passwordValida = false;
                $actualizarHash = false;

                // 1. Password con bcrypt:
                if (password_verify($password, $usuario['password'])) {
                    $passwordValida = true;

                // 2. Para los que había con MD5:
                } elseif ($usuario['password'] === md5($password)) {
                    $passwordValida = true;
                    $actualizarHash = true;
                }

                if ($passwordValida) {

                    // pasar MD5 → bcrypt de forma auto
                    if ($actualizarHash) {
                        $nuevoHash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

                        $update = $pdo->prepare("
                            UPDATE usuarios 
                            SET password = :password 
                            WHERE id = :id
                        ");

                        $update->execute([
                            'password' => $nuevoHash,
                            'id' => $usuario['id']
                        ]);
                    }

                    session_regenerate_id(true);

                    $_SESSION['id'] = $usuario['id'];
                    $_SESSION['name'] = $usuario['name'];
                    $_SESSION['email'] = $usuario['email'];
                    $_SESSION['rol'] = $usuario['rol'];

                    header('Location: principal.php');
                    exit;
                }
            }

            // si llega aquí → fallo login
            $mensaje = "Credenciales incorrectas";

        } catch (PDOException $e) {

            $mensaje = "Error al iniciar sesión";
        }
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