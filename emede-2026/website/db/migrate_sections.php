<?php
$db_path = __DIR__ . '/../db/database.sqlite';

try {
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Creando tabla page_sections...\n";
    $db->exec("CREATE TABLE IF NOT EXISTS page_sections (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        page_id INTEGER,
        section_key TEXT,
        title TEXT,
        content TEXT,
        image TEXT,
        item_order INTEGER DEFAULT 0,
        FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
    )");

    // Initial seed for Home 'About' section if it doesn't exist
    $home_id = $db->query("SELECT id FROM pages WHERE slug = 'home'")->fetchColumn();
    $stmt_check = $db->prepare("SELECT id FROM page_sections WHERE page_id = ? AND section_key = ?");
    $stmt_check->execute([$home_id, 'about']);
    
    if (!$stmt_check->fetch()) {
        $db->prepare("INSERT INTO page_sections (page_id, section_key, title, content, image) VALUES (?, ?, ?, ?, ?)")
           ->execute([
               $home_id, 
               'about', 
               'Somos el aliado gráfico de tu proyecto', 
               'Nos enorgullece ser parte de la historia en cada uno de los proyectos que se nos confía, demostrando nuestro compromiso con la excellence, la innovación y una marcada atención personalizada.',
               'https://lh3.googleusercontent.com/aida-public/AB6AXuD1eDGsTqtby46B9QWUyMlJGxwaTUnXfDNvuewVlt12ab0pS2s58X6LQsJLZniiYYqjUcCCM6cZ1pmL_qfobwaYKFrFArfR4cfaShPiHAGfSGlO2BhpFXdNVQ1K2FN1cwTXf1w5HvWqsJwEBfAOATN0Ybxadkl6MVHK5FsX6AIVSUFGT_ZBfzFAdmtWIkasqcrvHPwW21nf4C_QLbB_hkeu5nePy9M3UR2GdxMcHx3uZScRMcFMveAosWIeyhGM19b1jU70I-7apyJ3'
           ]);
        echo "Sección 'about' de Home creada.\n";
    }

    echo "Migración completada con éxito.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
