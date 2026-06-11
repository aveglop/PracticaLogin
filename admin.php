<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php?error=Debe iniciar sesión para continuar');
    exit;
}

if ($_SESSION['rol'] !== 'ROLE_ADMIN') {
    header('Location: no-autorizado.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
</head>
<body>

<h1>Panel de administración</h1>

</body>
</html>

