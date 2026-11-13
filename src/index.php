<?php
$host = getenv('DB_HOST');
$name = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');

$mensaje = '';
$error = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$name;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('Error de conexión con la base de datos.');
}

//crea la tabla
$pdo->exec('CREATE TABLE IF NOT EXISTS alumnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL
) CHARACTER SET utf8mb4');

//registra alumno
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email    = trim($_POST['email'] ?? '');

    if ($nombre === '' || $apellido === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Completá todos los campos con un correo válido.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO alumnos (nombre, apellido, email) VALUES (?, ?, ?)');
        $stmt->execute([$nombre, $apellido, $email]);
        header('Location: index.php?ok=1');
        exit;
    }
}

if (isset($_GET['ok'])) {
    $mensaje = 'Alumno registrado correctamente.';
}

$alumnos = $pdo->query('SELECT id, nombre, apellido, email FROM alumnos ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro de alumnos</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 2rem; color: #222; }
        .contenedor { max-width: 760px; margin: 0 auto; }
        h1 { text-align: center; }
        .tarjeta { background: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,.1); margin-bottom: 1.5rem; }
        form { display: grid; gap: .8rem; }
        label { font-weight: bold; font-size: .9rem; }
        input { width: 100%; padding: .6rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { padding: .7rem; background: #2563eb; color: #fff; border: 0; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background: #1d4ed8; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: .6rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f1f5f9; }
        .ok { color: #15803d; } .error { color: #b91c1c; }
    </style>
</head>
<body>
<div class="contenedor">
    <h1>Registro de alumnos</h1>

    <div class="tarjeta">
        <h2>Nuevo alumno</h2>
        <?php if ($mensaje): ?><p class="ok"><?= htmlspecialchars($mensaje) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form method="post" action="index.php">
            <div><label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" maxlength="100" required></div>
            <div><label for="apellido">Apellido</label>
                <input type="text" id="apellido" name="apellido" maxlength="100" required></div>
            <div><label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" maxlength="150" required></div>
            <button type="submit">Registrar</button>
        </form>
    </div>

    <div class="tarjeta">
        <h2>Alumnos registrados (<?= count($alumnos) ?>)</h2>
        <table>
            <thead><tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Email</th></tr></thead>
            <tbody>
            <?php foreach ($alumnos as $a): ?>
                <tr>
                    <td><?= (int)$a['id'] ?></td>
                    <td><?= htmlspecialchars($a['nombre']) ?></td>
                    <td><?= htmlspecialchars($a['apellido']) ?></td>
                    <td><?= htmlspecialchars($a['email']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$alumnos): ?>
                <tr><td colspan="4">Todavía no hay alumnos registrados.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
