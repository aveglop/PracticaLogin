<?php
require_once 'constantes.php';
//Cabecera CSP
header("Content-Security-Policy: default-src 'self';");
//mensaje vacío para que en el form esté vacío y solo aparezca cuando ya te registraste
$mensaje = '';
$error = 0;
// SI EnVíaN datos ddEl formulArIo, entOnces rEcogEmOs loS Datos y lo iNstrtAmOs eN la DB
//Solo se ejecuta el siguiente bloque si el user lo ha enviado el formulario.
//Triple igual para comparar valor y tipo que sean iguales
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //el trim para quitar espacios antes y despues, recoges los datos del form de abajo
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars($_POST['password']);

    if (strlen($name) > 25 || strlen($name) < 3) {
        $error = 1;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 2;
    } else {

        $hash = password_hash(
                $password,
                //Aqui vi que estaba bcrypt también, pero con default te aseguras que es el que mejor hay actualmente segun php
                PASSWORD_DEFAULT,
                ['cost' => 12]
        );

        try {
            $dsn = 'mysql:dbname=' . DB_NAME . ';host=' . DB_HOST. ';port=' . DB_PORT;
            $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $consultaDB = $pdo->prepare(
                    "INSERT INTO USUARIOS
        (name, email, password)
        VALUES
        (:name, :email, :password)"
            );

            $consultaDB->execute([
                'name' => $name,
                'email' => $email,
                'password' => $hash
            ]);

            $mensaje = "Usuario registrado correctamente";
        } catch (PDOException $e) {

            $mensaje = $e->getMessage(); //"Registro de usuario incorrecto"
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Registro</title>
    </head>
    <body>
        <h2>Registro</h2>
        <?php if ($mensaje): ?>
            <p><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">

            <label>Usuario:</label>
            <input type="text" name="name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit"> Registrarse </button>
        </form>
    </body>
</html>

