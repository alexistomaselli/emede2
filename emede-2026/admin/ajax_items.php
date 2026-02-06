<?php
session_start();
require_once '../includes/db.php';

// Verificar que el usuario esté autenticado (usando admin_auth como el resto del sistema)
if (!isset($_SESSION['admin_auth'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

header('Content-Type: application/json');

global $db;
$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'add_item':
            $section_id = $_POST['section_id'] ?? 0;
            $stmt = $db->prepare("INSERT INTO section_items (section_id, title, content, image) VALUES (?, ?, ?, ?)");
            $stmt->execute([$section_id, 'Nuevo elemento', '', '']);
            $new_id = $db->lastInsertId();

            echo json_encode([
                'success' => true,
                'message' => 'Elemento agregado',
                'item_id' => $new_id
            ]);
            break;

        case 'delete_item':
            $item_id = $_POST['item_id'] ?? 0;
            $stmt = $db->prepare("DELETE FROM section_items WHERE id = ?");
            $stmt->execute([$item_id]);

            echo json_encode([
                'success' => true,
                'message' => 'Elemento eliminado'
            ]);
            break;

        case 'update_items':
            $items = $_POST['items'] ?? [];

            if (empty($items)) {
                echo json_encode(['success' => false, 'message' => 'No hay elementos para actualizar']);
                break;
            }

            $db->beginTransaction();

            $stmt = $db->prepare("UPDATE section_items SET title = ?, content = ?, image = ?, extra_link = ?, rating = ? WHERE id = ?");

            foreach ($items as $id => $data) {
                $stmt->execute([
                    $data['title'] ?? '',
                    $data['content'] ?? '',
                    $data['image'] ?? '',
                    $data['extra_link'] ?? null,
                    $data['rating'] ?? 5,
                    $id
                ]);
            }

            $db->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Elementos actualizados correctamente'
            ]);
            break;

        case 'reorder_sections':
            $order = $_POST['order'] ?? [];

            if (empty($order)) {
                echo json_encode(['success' => false, 'message' => 'No hay orden para actualizar']);
                break;
            }

            $db->beginTransaction();
            $stmt = $db->prepare("UPDATE page_sections SET item_order = ? WHERE id = ?");

            foreach ($order as $index => $id) {
                $stmt->execute([$index, $id]);
            }

            $db->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Orden actualizado correctamente'
            ]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
