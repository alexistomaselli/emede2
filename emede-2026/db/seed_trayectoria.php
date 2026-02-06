<?php
$db_path = __DIR__ . '/../db/database.sqlite';

try {
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Get Trayectoria Page ID
    $stmt = $db->prepare("SELECT id FROM pages WHERE slug = 'trayectoria'");
    $stmt->execute();
    $page_id = $stmt->fetchColumn();

    if (!$page_id) {
        // Create Trayectoria page if not exists
        echo "Creating 'trayectoria' page...\n";
        $db->exec("INSERT INTO pages (title, slug, template) VALUES ('Trayectoria Emede', 'trayectoria', 'standard')");
        $page_id = $db->lastInsertId();
    } else {
        echo "Found 'trayectoria' page ID: $page_id\n";
    }

    // 2. Set Hero Data (Similar to mockup hero)
    // Title: Trayectoria (already set in pages table mostly, but let's assume we use banners table for hero if standard template uses it. 
    // Actually our system seems to use `pages` table title/subtitle or `banners` table. 
    // Let's check `get_page_hero` logic or similar. In `trayectoria.php` it creates `$page_hero` from `$page_data`?
    // Looking at `homepage.php` or `index.php` routing... `includes/db.php` has `get_page_data`.
    // It seems `trayectoria.php` uses `$page_hero['title']` which implies it might be coming from `layout/header.php` logic.
    // Let's update `pages` entry to match mockup hero if needed.
    // Mockup has Title: "Trayectoria", Breadcrumb: "HOME ● TRAYECTORIA". 
    // The previous `migrations` added `hero_title`, `hero_subtitle`, `hero_image` columns to `pages`? 
    // Wait, let's check `pages` schema or look at `db/migrate_pages.php` if available or `PRAGMA table_info(pages)`

    // For now let's focus on SECTIONS.

    // 3. Create "Nuestra Historia" Section (Mockup Section 2)
    // "Más de cuatro décadas dedicándose con pasión a la impresión offset..."
    // Layout: Image 1 (Factory), Image 2 (Team), "45 Años", Title, Content, Certifications.
    // This is a complex layout. We might need a specific `section_key` = 'history_complex' or just 'history'.
    // `page_sections` table has: page_id, section_key, title, content, image, image2, image3, image4, subtitle.

    $sections = [
        [
            'key' => 'history',
            'title' => 'Más de cuatro décadas dedicándose con pasión a la impresión offset.',
            'subtitle' => 'DESDE 1978', // Use subtitle field for small top text
            'content' => 'A lo largo del tiempo, hemos sabido reinventarnos y fundamentalmente evolucionar escuchando al mercado, respondiendo así a las exigencias planteadas por la demanda. Apuntamos a la plena optimización en todas las etapas productivas, ofreciendo precios competitivos y utilizando las mejores materias primas disponibles.',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCLQt9lsCQAAG36DbFJ-Vf_-Mr8oLwzMEhKmQLkbFRvmEoFer6KqLDsVU927UMtX_ys8Nk3-EpRe9YGHepu0iMumTCkAFRaK12-ggXnU2xVj2W7RAIeQiqYP5q7qpBh2eFSqLOFYFhAmc6z1W_hPsl3Jo0WCpElWrevncZoeCq6jAmTkvKBEBwLsEmJWGfLplbDeILNcZGQBG8FuyCwXpb4EpyrBLBvXTqO3E5nNZOPFVwnaZzla4TIIm4UMAqht2wP6okwFLmfDdF3', // Factory image
            'image2' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBFfUFPtGh_Oa6NvBpUq99gP_HZJUwenTXwXZRFpJWlGQgKDt1pYKGCXz9NYKTspnUX-IbZoF066JSjqAThguPv6_DB6mu_9pbf1TC7b2xbF3sRUvEFO7gJOLzJRUmjvra3idb3ZR66SQzudmJPQwAHkQ78vQJGWtKK0KnAvTBpPK7BtNCiJ6U7evySOPYvF-NWZbCtCES8jiuA6sib4ZP5bKNThBHOmX-QFxR4ubVplqZ1ttrllZv3yym0j5m__BYr-umq4USCRtAr', // Team image
            'order' => 1
        ],
        [
            'key' => 'quality_policy',
            'title' => 'Política de calidad',
            'subtitle' => 'WORKS ABOUT',
            'content' => 'Todos nuestros procesos de fabricación se desarrollan bajo Sistema de Gestión de Calidad certificado por la norma ISO 9001:2015.',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDEgBagsmAikqLs5ogZfMEqPnfUBVEaR71JmnpNKny4Fo7-oxUX03v-N5JgHs-DGfp7c4NIN5--QIwtDRQY4pfKKQk7mtNLOaJBv8HIlCwLcOgxAytCdhZjPS5uctwhJ9w8FPzfTcFydtImJ6AIq8FLxw9KxVl88zkVUO5XTB6ux4Zsh7sfT1598scl59PrEDrglYqNYP2HY1sqElaN6qw4SNUd7lpPxfaaQjron_GlsqbCoT4uZB4rmBcbOj63B_ZwITV-Fvqoj_mt',
            'order' => 2
        ],
        [
            'key' => 'stats',
            'title' => 'Estadísticas',
            'content' => '',
            'order' => 3
        ]
    ];

    $checkStmt = $db->prepare("SELECT id FROM page_sections WHERE page_id = ? AND section_key = ?");
    $insertStmt = $db->prepare("INSERT INTO page_sections (page_id, section_key, title, subtitle, content, image, image2, item_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $updateStmt = $db->prepare("UPDATE page_sections SET title = ?, subtitle = ?, content = ?, image = ?, image2 = ?, item_order = ? WHERE id = ?");

    foreach ($sections as $s) {
        $checkStmt->execute([$page_id, $s['key']]);
        $existing_id = $checkStmt->fetchColumn();

        if ($existing_id) {
            echo "Updating section '{$s['key']}'...\n";
            $updateStmt->execute([
                $s['title'],
                $s['subtitle'] ?? null,
                $s['content'],
                $s['image'] ?? null,
                $s['image2'] ?? null,
                $s['order'],
                $existing_id
            ]);
            $section_id = $existing_id; // For adding items if needed
        } else {
            echo "Creating section '{$s['key']}'...\n";
            $insertStmt->execute([
                $page_id,
                $s['key'],
                $s['title'],
                $s['subtitle'] ?? null,
                $s['content'],
                $s['image'] ?? null,
                $s['image2'] ?? null,
                $s['order']
            ]);
            $section_id = $db->lastInsertId();
        }

        // Add items for 'history' section (Certifications/Badges)
        if ($s['key'] === 'history') {
            // Clear existing items
            $db->prepare("DELETE FROM section_items WHERE section_id = ?")->execute([$section_id]);

            $items = [
                [
                    'title' => 'Empresa Certificada',
                    'content' => 'Cumplimos con los más altos estándares internacionales bajo la norma ISO 9001:2015, asegurando trazabilidad y calidad total.',
                    'image' => 'verified' // Using as icon name
                ],
                [
                    'title' => 'Socio Estratégico',
                    'content' => 'Somos el aliado clave para industrias críticas como la farmacéutica y alimenticia, donde la precisión y el cumplimiento son fundamentales.',
                    'image' => 'stars' // Using as icon name
                ]
            ];
            $insertItem = $db->prepare("INSERT INTO section_items (section_id, title, content, image) VALUES (?, ?, ?, ?)");
            foreach ($items as $item) {
                $insertItem->execute([$section_id, $item['title'], $item['content'], $item['image']]);
            }
        }

        // Add items for 'stats' section
        if ($s['key'] === 'stats') {
            // Clear existing items
            $db->prepare("DELETE FROM section_items WHERE section_id = ?")->execute([$section_id]);

            $items = [
                [
                    'title' => '27M',
                    'content' => 'Estuches fabricados por año',
                    'image' => 'inventory_2'
                ],
                [
                    'title' => '25',
                    'content' => 'Profesionales expertos',
                    'image' => 'groups'
                ],
                [
                    'title' => 'ISO 9001',
                    'content' => 'Certificación de Calidad',
                    'image' => 'verified'
                ]
            ];
            $insertItem = $db->prepare("INSERT INTO section_items (section_id, title, content, image) VALUES (?, ?, ?, ?)");
            foreach ($items as $item) {
                $insertItem->execute([$section_id, $item['title'], $item['content'], $item['image']]);
            }
        }
    }

    echo "Trayectoria page seeded successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
