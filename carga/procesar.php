<?php
// Configuración de la base de datos
require_once '../tablas/config.php';

// Función para validar el formato del archivo
function validarFormato($linea) {
  
    // Dividir la línea por comas
    $datos = explode(',', $linea);
    
    // Verificar que tenga al menos los campos requeridos (email y código, revisor)
    if (count($datos) < 5) {
        return false;
    }
    
    // Verificar que el email sea válido
    $email = trim($datos[0]);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    // Verificar que el código sea un número y esté entre 1 y 3
    $codigo = trim($datos[3]);
    if (!is_numeric($codigo) || $codigo < 1 || $codigo > 3) {
        return false;
    }

    // Verificar que el revisor sea un número y exista en la tabla de revisores
    $revisor = trim($datos[4]);
    if (!is_numeric($revisor)) {
        return false;
    }
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si el revisor existe en la tabla de revisores
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM revisores WHERE id = ?");
    $stmt->execute([$revisor]);
    $existe_revisor = $stmt->fetchColumn() > 0;
    if (!$existe_revisor) {
        return false;
    }
    
    return true;
}

// Procesar el archivo subido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se subió un archivo
    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        header('Location: ../index.php?error=Error al subir el archivo');
        exit;
    }
    
    // Verificar si es un archivo de texto
    $tipo = mime_content_type($_FILES['archivo']['tmp_name']);
    if ($tipo !== 'text/csv') {
        header('Location: ../index.php?error=El archivo debe ser de texto plano (.txt), el tipo actual es: ' . $tipo);
        exit;
    }
    
    try {
        // Abrir el archivo
        $archivo = fopen($_FILES['archivo']['tmp_name'], 'r');
        $registros_validos = [];
        $hay_errores = false;
        $linea_numero = 0;
        
        // Primera pasada: validar todas las líneas
        while (($linea = fgets($archivo)) !== false) {
            $linea_numero++;
            $linea = trim($linea);
            
            // Saltar líneas vacías
            if (empty($linea)) {
                continue;
            }
            
            // Validar el formato de la línea
            if (!validarFormato($linea)) {
                // Si hay al menos un error
                $hay_errores = true;
                break; // Terminamos la validación al primer error
            }
            
            // Si la línea es válida, obtener los datos y almacenarlos en el arreglo
            $datos = explode(',', $linea);
            $registros_validos[] = [
                'email' => trim($datos[0]),
                'nombre' => isset($datos[1]) ? trim($datos[1]) : '',
                'apellido' => isset($datos[2]) ? trim($datos[2]) : '',
                'codigo' => trim($datos[3]),
                'revisor' => trim($datos[4])
            ];
        }
        
        // Cerrar el archivo después de la primera lectura
        fclose($archivo);
        
        // Si hay errores, redirigir al index con mensaje de error
        if ($hay_errores) {
            header('Location: ../index.php?error=El archivo no tiene el formato correcto. Error en la línea ' . $linea_numero);
            exit;
        }
        
        // Si no hay errores, procedemos a guardar en la base de datos
        if (count($registros_validos) > 0) {
            // Conectar a la base de datos
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Preparar la consulta SQL para insertar datos
            $stmt = $pdo->prepare("INSERT INTO usuarios (email, nombre, apellido, codigo, revisor_id) VALUES (?, ?, ?, ?, ?) 
                                  ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), apellido = VALUES(apellido), codigo = VALUES(codigo), revisor_id = VALUES(revisor_id)");
            
            // Insertar todos los registros válidos
            $lineas_procesadas = 0;
            
            foreach ($registros_validos as $registro) {
                $stmt->execute([
                    $registro['email'],
                    $registro['nombre'],
                    $registro['apellido'],
                    $registro['codigo'],
                    $registro['revisor']
                ]);
                $lineas_procesadas++;
            }
            
            // Redirigir a la página de visualización
            header('Location: visualizar.php?procesados=' . $lineas_procesadas . '&errores=0');
            exit;
        } else {
            // Si no hay registros válidos (archivo vacío)
            header('Location: ../index.php?error=El archivo no contiene registros válidos');
            exit;
        }
        
    } catch (PDOException $e) {
        header('Location: ../index.php?error=Error en la base de datos: ' . urlencode($e->getMessage()));
        exit;
    } catch (Exception $e) {
        header('Location: ../index.php?error=Error al procesar el archivo: ' . urlencode($e->getMessage()));
        exit;
    }
} else {
    // Si no es una solicitud POST, redirigir al formulario
    header('Location: ../index.php');
    exit;
}