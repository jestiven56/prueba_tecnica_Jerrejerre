<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GEMA SAS - Carga de Archivo</title>
    <link rel="stylesheet" href="datos/css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>GEMA SAS - Carga de Archivo</h1>
        </header>
        
        <main>
            <div class="form-container">
                <h2>Formulario de Carga</h2>
                
                <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
                <?php endif; ?>
                
                <form action="carga/procesar.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="archivo">Seleccione un archivo de texto:</label>
                        <input type="file" id="archivo" name="archivo" accept=".txt" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Cargar Archivo</button>
                    </div>
                </form>
            </div>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> GEMA SAS. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>