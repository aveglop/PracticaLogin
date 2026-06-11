<?php
session_start();

if (!isset($_SESSION['id'])) {

    header('Location: login.php?error=Debe iniciar sesión para continuar');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¡Bienvenido!</title>
</head>
<body>

<h1>Bienvenido <?= htmlspecialchars($_SESSION['name']) ?></h1>

<a href="logout.php">Cerrar sesión</a>

</body>
</html>
