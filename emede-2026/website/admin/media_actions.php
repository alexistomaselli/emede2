<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

// Ensure we only output JSON
header('Content-Type: application/json');

if (!isset($_SESSION['admin_auth'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

try {
    // Detect post_max_size overflow
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_FILES) && empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
        throw new Exception('El archivo excede el tamaño máximo permitido por el servidor (post_max_size).');
    }

    // 1. Upload logic
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $err_msg = 'Error de subida PHP: ' . $file['error'];
            if ($file['error'] == 1 || $file['error'] == 2)
                $err_msg = 'El archivo es demasiado grande.';
            throw new Exception($err_msg);
        }

        $upload_dir = __DIR__ . '/../uploads/';

        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                throw new Exception('No se pudo crear el directorio de subidas.');
            }
        }

        if (!is_writable($upload_dir)) {
            throw new Exception('El directorio de subidas no tiene permisos de escritura.');
        }

        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file['name']));
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $media_id = save_media_meta('uploads/' . $filename, $file['name'], $file['type'], $file['size']);
            echo json_encode([
                'success' => true,
                'filename' => 'uploads/' . $filename,
                'media_id' => $media_id
            ]);
        } else {
            throw new Exception('Error al mover el archivo al destino final.');
        }
        exit;
    }

    // 2. Delete logic
    if (isset($_POST['delete_media_id'])) {
        $id = $_POST['delete_media_id'];
        $stmt = $db->prepare("SELECT filename FROM media WHERE id = ?");
        $stmt->execute([$id]);
        $filename = $stmt->fetchColumn();

        if ($filename) {
            $full_path = __DIR__ . '/../' . $filename;
            if (file_exists($full_path)) {
                unlink($full_path);
            }
            $stmt = $db->prepare("DELETE FROM media WHERE id = ?");
            $stmt->execute([$id]);
        }
        echo json_encode(['success' => true]);
        exit;
    }

    // 3. Fetch all (for picking)
    if (isset($_GET['fetch_media'])) {
        echo json_encode(get_all_media());
        exit;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
