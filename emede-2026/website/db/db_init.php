<?php
$db_path = __DIR__ . '/../db/database.sqlite';

try {
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Drop existing tables for a clean migration
    $db->exec("DROP TABLE IF EXISTS banners");
    $db->exec("DROP TABLE IF EXISTS menus");
    $db->exec("DROP TABLE IF EXISTS pages");

    // 1. Pages table (Purely metadata & routes)
    $db->exec("CREATE TABLE IF NOT EXISTS pages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        slug TEXT UNIQUE,
        title TEXT,
        template TEXT DEFAULT 'internal' -- 'home' or 'internal'
    )");

    // 2. Banners table (Associated with pages)
    // For 'home': many banners (slider)
    // For 'internal': exactly one banner (hero)
    $db->exec("CREATE TABLE IF NOT EXISTS banners (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        page_id INTEGER,
        title TEXT,
        subtitle TEXT,
        image TEXT,
        button_text TEXT,
        button_link TEXT,
        item_order INTEGER DEFAULT 0,
        FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
    )");

    // 3. Menus table (Unchanged)
    $db->exec("CREATE TABLE IF NOT EXISTS menus (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        label TEXT,
        url TEXT,
        menu_order INTEGER DEFAULT 0,
        is_active INTEGER DEFAULT 1
    )");

    // 4. Media table (WordPress-style)
    $db->exec("CREATE TABLE IF NOT EXISTS media (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        filename TEXT,
        original_name TEXT,
        mime_type TEXT,
        file_size INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Reset for migration
    $db->exec("DELETE FROM pages");
    $db->exec("DELETE FROM banners");
    $db->exec("DELETE FROM menus");
    $db->exec("DELETE FROM media");

    // Seed Pages
    $pages = [
        ['home', 'Inicio', 'home'],
        ['trayectoria', 'Trayectoria', 'internal'],
        ['packaging', 'Packaging', 'internal'],
        ['posavasos', 'Posavasos', 'internal'],
        ['comercial', 'Comercial', 'internal'],
        ['galeria', 'Galería', 'internal']
    ];
    $stmt = $db->prepare("INSERT INTO pages (slug, title, template) VALUES (?, ?, ?)");
    foreach ($pages as $p)
        $stmt->execute($p);

    // Seed Banners for Home (Slider)
    $home_id = $db->query("SELECT id FROM pages WHERE slug = 'home'")->fetchColumn();
    $home_banners = [
        [
            $home_id,
            'Especialistas en <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-white">Packaging</span>',
            'Más de 45 años transformando ideas en soluciones gráficas de alta calidad para las industrias más exigentes.',
            'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&q=80&w=2000',
            'Cotizar',
            '#',
            1
        ],
        [
            $home_id,
            'Líderes en <span class="text-accent text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-300">Gráfica Industrial</span>',
            'Tecnología de última generación para proyectos de alta complejidad y grandes volúmenes de producción.',
            'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&q=80&w=2000',
            'Nuestras Máquinas',
            '#',
            2
        ]
    ];
    $stmt = $db->prepare("INSERT INTO banners (page_id, title, subtitle, image, button_text, button_link, item_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($home_banners as $b)
        $stmt->execute($b);

    // Seed Banners for Internal Pages (Heroes)
    $internal_pages = $db->query("SELECT id, slug, title FROM pages WHERE template = 'internal'")->fetchAll();
    foreach ($internal_pages as $p) {
        $img = ($p['slug'] == 'galeria') ? 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&q=80&w=2000' : 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&q=80&w=2000';
        $stmt->execute([
            $p['id'],
            $p['title'],
            'HOME ○ ' . strtoupper($p['slug']),
            $img,
            '',
            '',
            1
        ]);
    }

    // Seed Menus (WordPress style)
    $menus = [
        ['Inicio', 'index.php', 1],
        ['Trayectoria', 'trayectoria.php', 2],
        ['Packaging', 'packaging.php', 3],
        ['Posavasos', 'posavasos.php', 4],
        ['Comercial', 'comercial.php', 5],
        ['Galería', 'galeria.php', 6]
    ];
    $stmt_menu = $db->prepare("INSERT INTO menus (label, url, menu_order) VALUES (?, ?, ?)");
    foreach ($menus as $m)
        $stmt_menu->execute($m);

    echo "CMS reestructurado correctamente con lógica de Páginas/Banners unificada.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
