<?php
require_once __DIR__ . '/../includes/db.php';

// Create Comercial Page
$slug = 'comercial';
$title = 'Comercial';

$stmt = $db->prepare("SELECT id FROM pages WHERE slug = ?");
$stmt->execute([$slug]);
$page_id = $stmt->fetchColumn();

if (!$page_id) {
    $stmt = $db->prepare("INSERT INTO pages (slug, title) VALUES (?, ?)");
    $stmt->execute([$slug, $title]);
    $page_id = $db->lastInsertId();
    echo "Created page '$title' (ID: $page_id)\n";
} else {
    echo "Page '$title' already exists (ID: $page_id)\n";
}

// Check and add menu item
$menu_label = 'Comercial';
$menu_url = 'comercial.php';

$stmt = $db->prepare("SELECT id FROM menus WHERE label = ?");
$stmt->execute([$menu_label]);
if (!$stmt->fetchColumn()) {
    // Find next order
    $order = $db->query("SELECT MAX(menu_order) FROM menus")->fetchColumn() + 1;
    $stmt = $db->prepare("INSERT INTO menus (label, url, menu_order) VALUES (?, ?, ?)");
    $stmt->execute([$menu_label, $menu_url, $order]);
    echo "Created menu item '$menu_label'.\n";
} else {
    echo "Menu item '$menu_label' already exists.\n";
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
    // Update existing section just in case title/subtitle changed
    $stmt = $db->prepare("UPDATE page_sections SET title = ?, subtitle = ?, content = ? WHERE id = ?");
    $stmt->execute([$title, $subtitle, $content, $existing]);

    echo "Section '$key' exists (ID: $existing) - Updated info.\n";
    return $existing;
}

// Helper to add item
function add_item($db, $section_id, $title, $content = '', $image = '', $extra_link = '')
{
    // Check if item exists to avoid duplicates on re-run
    $stmt = $db->prepare("SELECT id FROM section_items WHERE section_id = ? AND title = ?");
    $stmt->execute([$section_id, $title]);
    if ($stmt->fetchColumn())
        return;

    $stmt = $db->prepare("INSERT INTO section_items (section_id, title, content, image, extra_link, item_order) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->execute([$section_id, $title, $content, $image, $extra_link]);
}

// 1. Hero Section
create_section(
    $db,
    $page_id,
    'hero',
    'Material comercial de calidad',
    '',
    '',
    'media/comercial-hero.png', // Extracted from PDF
    'https://placehold.co/1920x600' // Fallback
);

// 2. Introduction Section
$intro_id = create_section(
    $db,
    $page_id,
    'introduction',
    'Soluciones Integrales de Impresión',
    'Impresión Comercial',
    'Ofrecemos una amplia gama de servicios de impresión comercial para satisfacer todas las necesidades de su empresa. Desde folletos y catálogos hasta papelería corporativa y material promocional, garantizamos la máxima calidad y precisión en cada trabajo.',
    ''
);
// Items for intro images
$stmt = $db->prepare("DELETE FROM section_items WHERE section_id = ?"); // Clear old items to re-seed correctly
$stmt->execute([$intro_id]);

// Add placeholders for intro images
add_item($db, $intro_id, 'Folletería', 'Diseño e impresión de folletos de alto impacto', 'https://placehold.co/400x400?text=Foletería');
add_item($db, $intro_id, 'Papelería', 'Tarjetas, carpetas y hojas membretadas', 'https://placehold.co/400x400?text=Papelería');
add_item($db, $intro_id, 'Catálogos', 'Impresión de catálogos y revistas', 'https://placehold.co/400x400?text=Catálogos');

// 3. Badges/Logos Section
$badges_id = create_section($db, $page_id, 'badges', 'Nuestros Clientes Comerciales');
// Clear old items to start fresh
$stmt = $db->prepare("DELETE FROM section_items WHERE section_id = ?");
$stmt->execute([$badges_id]);

// Add logos from media/logos-comercial
$logos = [
    'Oligra' => 'media/logos-comercial/oligra.png',
    'Romyl' => 'media/logos-comercial/Romyl.png',
    'Lovely Denim' => 'media/logos-comercial/Lovely Denim.png',
    'Sonne' => 'media/logos-comercial/sonne.png',
    'Fibran Sur' => 'media/logos-comercial/Fibransur.png',
    'TF3' => 'media/logos-comercial/tf3.png',
    'Heinz' => 'media/logos-comercial/Heinz.png',
    'Philips' => 'media/logos-comercial/Philips.png'
];

foreach ($logos as $title => $img) {
    add_item($db, $badges_id, $title, '', $img);
}

// 4. FAQ Section
$faq_id = create_section($db, $page_id, 'faq', 'Preguntas frecuentes', 'FAQ');
// Clear old items
$stmt = $db->prepare("DELETE FROM section_items WHERE section_id = ?");
$stmt->execute([$faq_id]);

add_item($db, $faq_id, '¿Qué tipo de materiales y terminaciones puedo elegir para mis proyectos de impresión?', 'Ofrecemos una amplia variedad de papeles. Podés consultar por opciones como laminados, barnices y troquelados.');
add_item($db, $faq_id, '¿Pueden ayudarme a diseñar o adaptar mis ideas para que funcionen mejor en papel?', 'No ofrecemos el servicio de diseño pero nuestro personal te ofrecerá el mejor asesoramiento para cada proceso productivo. Así garantizamos que tu pieza se destaque visualmente.');
add_item($db, $faq_id, '¿Cuál es el tiempo estimado de producción y entrega para mi material gráfico?', 'Los plazos varían según la cantidad, la complejidad y los acabados solicitados. Al realizar tu pedido, te informamos el tiempo estimado y mantenemos comunicación constante hasta la entrega para que tu proyecto llegue en fecha y en perfectas condiciones.');

echo "Seeding completed for Comercial page.\n";
?>