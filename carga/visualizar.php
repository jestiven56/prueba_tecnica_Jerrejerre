<?php
// Configuración de la base de datos
require_once '../tablas/config.php';

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obtener los usuarios activos y sus revisores
    $activos = $pdo->query("SELECT u.*, r.nombre AS revisor_nombre, r.apellido AS revison_apellido FROM usuarios u LEFT JOIN revisores r ON u.revisor_id = r.id WHERE u.codigo = 1 ORDER BY u.fecha_carga DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener los usuarios inactivos
    $inactivos = $pdo->query("SELECT u.*, r.nombre AS revisor_nombre, r.apellido AS revison_apellido FROM usuarios u LEFT JOIN revisores r ON u.revisor_id = r.id WHERE u.codigo = 2 ORDER BY u.fecha_carga DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener los usuarios en espera
    $espera = $pdo->query("SELECT u.*, r.nombre AS revisor_nombre, r.apellido AS revison_apellido FROM usuarios u LEFT JOIN revisores r ON u.revisor_id = r.id WHERE u.codigo = 3 ORDER BY u.fecha_carga DESC")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Error en la base de datos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GEMA SAS - Visualización de Datos</title>
    <link rel="stylesheet" href="../datos/css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>GEMA SAS - Visualización de Datos</h1>
            <nav>
                <a href="../index.php" class="btn-secondary">Volver al Formulario</a>
            </nav>
        </header>
        
        <main>
            <?php if (isset($_GET['procesados']) && isset($_GET['errores'])): ?>
            <div class="notification">
                <p>Archivo procesado correctamente.</p>
                <p>Registros procesados: <?php echo htmlspecialchars($_GET['procesados']); ?></p>
                <p>Registros con errores: <?php echo htmlspecialchars($_GET['errores']); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php else: ?>
            
            <div class="tables-container">
                <!-- Tabla de usuarios activos -->
                <section class="table-section">
                    <h2>Usuarios Activos</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Revisor</th>
                                <th>Fecha de Carga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($activos)): ?>
                            <tr>
                                <td colspan="4" class="no-data">No hay usuarios activos</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($activos as $usuario): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['apellido']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['revisor_nombre'] . ' ' . $usuario['revison_apellido']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_carga'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>
                
                <!-- Tabla de usuarios inactivos -->
                <section class="table-section">
                    <h2>Usuarios Inactivos</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Revisor</th>
                                <th>Fecha de Carga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($inactivos)): ?>
                            <tr>
                                <td colspan="4" class="no-data">No hay usuarios inactivos</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($inactivos as $usuario): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['apellido']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['revisor_nombre'] . ' ' . $usuario['revison_apellido']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_carga'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>
                
                <!-- Tabla de usuarios en espera -->
                <section class="table-section">
                    <h2>Usuarios en Espera</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Revisor</th>
                                <th>Fecha de Carga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($espera)): ?>
                            <tr>
                                <td colspan="4" class="no-data">No hay usuarios en espera</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($espera as $usuario): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['apellido']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['revisor_nombre'] . ' ' . $usuario['revison_apellido']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_carga'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>
            </div>
            <?php endif; ?>
            
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> GEMA SAS. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>