<?php
require_once __DIR__ . '/../includes/db.php';

// Create Galeria Page
$stmt = $db->prepare("SELECT id FROM pages WHERE slug = 'galeria'");
$stmt->execute();
$page_id = $stmt->fetchColumn();

if (!$page_id) {
    $stmt = $db->prepare("INSERT INTO pages (title, slug, menu_order) VALUES ('Galería', 'galeria', 6)");
    $stmt->execute();
    $page_id = $db->lastInsertId();
    echo "Created Page 'Galería' (ID: $page_id)\n";
} else {
    echo "Page 'Galería' already exists (ID: $page_id)\n";
}

// Ensure it's in the menu
$stmt = $db->prepare("SELECT id FROM menus WHERE url = 'galeria.php'");
$stmt->execute();
if (!$stmt->fetchColumn()) {
    $stmt = $db->prepare("INSERT INTO menus (label, url, menu_order) VALUES ('Galería', 'galeria.php', 6)");
    $stmt->execute();
    echo "Menu item 'Galería' created.\n";
} else {
    echo "Menu item 'Galería' already exists.\n";
}

// Helper to create section
function create_section($db, $page_id, $key, $title, $subtitle = '', $content = '', $image = '', $image2 = '')
{
    $stmt = $db->prepare("SELECT id FROM page_sections WHERE page_id = ? AND section_key = ?");
    $stmt->execute([$page_id, $key]);
    $existing = $stmt->fetchColumn();

    if (!$existing) {
        $stmt = $db->prepare("INSERT INTO page_sections (page_id, section_key, title, subtitle, content, image, image2) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$page_id, $key, $title, $subtitle, $content, $image, $image2]);
        $id = $db->lastInsertId();
        echo "Created section '$key' (ID: $id)\n";
        return $id;
    }
    // Update existing section
    $stmt = $db->prepare("UPDATE page_sections SET title = ?, subtitle = ?, content = ?, image = ? WHERE id = ?");
    $stmt->execute([$title, $subtitle, $content, $image, $existing]);

    echo "Section '$key' exists (ID: $existing) - Updated info.\n";
    return $existing;
}

// Helper to add section item
function add_item($db, $section_id, $title, $content = '', $image = '', $extra_link = '')
{
    $stmt = $db->prepare("INSERT INTO section_items (section_id, title, content, image, extra_link) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$section_id, $title, $content, $image, $extra_link]);
}

// 1. Hero Section
create_section(
    $db,
    $page_id,
    'hero',
    'Conocé nuestra gráfica',
    '',
    '',
    'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&q=80&w=2000'
);

// 2. Gallery Section
$gallery_id = create_section($db, $page_id, 'gallery', 'Nuestra Galería');

// Clear old items
$stmt = $db->prepare("DELETE FROM section_items WHERE section_id = ?");
$stmt->execute([$gallery_id]);

// Add items from mockup
$items = [
    [
        'title' => 'Packaging Farmacéutico',
        'content' => 'Industria',
        'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC06n9v-B4jTSlH8jWs_FRfcsv_MN7ijmpk1_lQ8qMOdVr_6MOv1uIfTKVTxAvgn7l5se91nHjKQj6qSWKyOgZBF9eY7nIC-Zow7ouv4uhDhwzwcIffpwDtQWyIJH-KfqDV44m6xpg3n02jPL4d73H4quPcqyEpBTddiR3LWN-7TnvKvSs4KVOtBo7OhYJYPy4LSub5SnW9CEtm3WAyObwm1Vi-FcN4AlLWzGBrglbZs42pbT1yNgchHh1r6UCMdMUkmkjPthc98-1r'
    ],
    [
        'title' => 'Folletos Comerciales',
        'content' => 'Comercial',
        'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAsA8BscyLHLAHVE3QaJgqaD_UNvlH603hE3HBy12AB5ALbM7t1lfZRyQF9MT9Vl6eqGkw5jNQv-YnUbOhHPo8U2sUjkV2yAFh8FrmBWBKxzaa4YWV14FxKYJLaelfKNqyEGIX8ohqJXGlaHynnrtmjXf3hx9PKaF4cGZZ8W1YpQ8wCvSfPtvvoW4dI70-_0FOLo7HHXt7biJvr5DWR9WSHzfqR0qvBG4S13dHiiowMZPoYLzc76I76xr8uB1BOWqnA8MUBPIFASmOn'
    ],
    [
        'title' => 'Posavasos Personalizados',
        'content' => 'Gastronomía',
        'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBXnvqbzPJCyHt1UfzIjxtFYmzqlmzMLIoDQGiTaFe8OcYmbuVQEfZ_XZff8hnLFaWydm2nKzWVas0tJ9PtbA4CcXkFvMrJKJ5y3MAG8QGKsAySuwxc3A9zSInEuyg681PR6u6E3fMSzGiBpnl7cb3anyQP013qS-v2reRGSLUmUqH6hyAYlWYyQ4i6yO1JafJpB_-leQBvPEvB-opWzg0sOOQvoBHGVG-m5fN5H544-Z-YROIIrm07cihxfN1If09SjokkznOgbUYf'
    ],
    [
        'title' => 'Estuches Premium',
        'content' => 'Cosmética',
        'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuB3F8HP1TfwToFDiIGiaSOUHj4ciNqSePKd9Xa6bNIHXQPDu_I-2ItN5xGKkHc87LLOTnr5t0mxWRtRe4eJmXzA1hG-kG59TjA5cSO-TmRef_ES4vXBtU2chiS9F6gy3edHgwlrIgYXkr6ibIF4SswIDWnFA_5fFNhbjWWjpp2xGlCFBhUcu5JOp5BVqriVeSCquIe_PpFyM-TInsf1pkrIAnBoYJEqOWkl60HeZiVpuLvITOOh2VOgyzNlWaUJOZ2nuhMm-2ycSLil'
    ],
    [
        'title' => 'Prospectos Médicos',
        'content' => 'Industria',
        'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAwsWQVeKFbgeP5-KX9XKdsGEQcyB6RrD-3ZiHlxrRcOaAYxn-TseDb1RwvMluHdLU1icgSfP0Avua7ukn1PZ8dPlW2BkpLZiVyN0tXh_hlYzrAREFm3OU_MUSdTm24jXdfAXygZimvfIFXWylKDweb-ktnbBuMWMUwTkyLU46oqVmIdsf3eJDMaMawaQG7Ao_77rcmp9pJP1L7eH0lLqGmN3iDldpiZxCOoBQo-MdgG_IG7lZx_ptE3APAsoNlUq-Q14kFwno08tfR'
    ],
    [
        'title' => 'Cajas de Envío',
        'content' => 'E-commerce',
        'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCrw0yZYPK14uv_sEbLDIA6Axg5NBea770db8eCRrEC79JI_jicvo7tGY67DK9XPOJzgJ89I6e-ajiVqrqvd-VOmbUN4PE0O-7Fp7fLdF0muCYqIWYhuV5bOP_mRNrgYEO3GIcDwK5LRQKhbdDTOmDVlEgpTXsSwkG1up_6XY6yPbUgBH8QBsxQjp8Ov10MlXLM0sHSpV3b42bhpCe12dx6BJwj0xEO7ZKfTP0VT9c1YZKcmNCmPbW4-fgaKW7YIq7QSO0t4Ue6XhO4'
    ],
    [
        'title' => 'Maquinaria de Precisión',
        'content' => 'Producción',
        'image' => 'media/galeria/11.png'
    ],
    [
        'title' => 'Control de Calidad',
        'content' => 'Logística',
        'image' => 'media/galeria/8.png'
    ],
    [
        'title' => 'Preparación de Tintas',
        'content' => 'Laboratorio',
        'image' => 'media/galeria/13.png'
    ],
    [
        'title' => 'Proceso de Impresión',
        'content' => 'Producción',
        'image' => 'media/galeria/10.png'
    ],
    [
        'title' => 'Tecnología Industrial',
        'content' => 'Tecnología',
        'image' => 'media/galeria/12.png'
    ],
    [
        'title' => 'Material Terminado',
        'content' => 'Producción',
        'image' => 'media/galeria/9.png'
    ]
];

foreach ($items as $item) {
    add_item($db, $gallery_id, $item['title'], $item['content'], $item['image']);
}

echo "Seeding completed for Gallery page.\n";
