<?php
require_once __DIR__ . '/../includes/db.php';

// Create Packaging Page
$slug = 'packaging';
$title = 'Packaging';

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
    echo "Section '$key' already exists (ID: $existing)\n";
    return $existing;
}

// Helper to add item (Modified for correct schema)
function add_item($db, $section_id, $title, $content = '', $image = '', $extra_link = '')
{
    // Schema: id, section_id, title, content, image, item_order, extra_link, rating
    $stmt = $db->prepare("INSERT INTO section_items (section_id, title, content, image, extra_link, item_order) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->execute([$section_id, $title, $content, $image, $extra_link]);
}

// 1. Hero Section
create_section(
    $db,
    $page_id,
    'hero',
    'ESTUCHES QUE POTENCIAN Y PROTEGEN TUS PRODUCTOS.',
    '',
    '',
    'https://images.unsplash.com/photo-1562654501-a0ccc0fc3fb1?auto=format&fit=crop&q=80&w=2000'
);

// 2. Introduction Section
$intro_id = create_section(
    $db,
    $page_id,
    'introduction',
    'Somos el aliado gráfico de tu proyecto',
    '+45 AÑOS DE EXPERIENCIA',
    'En Gráfica Emede entendemos que el packaging es el primer contacto físico entre tu producto y el consumidor, siendo una herramienta clave en comunicar valor para tu marca. Somos una empresa referente en la fabricación de packaging sobre cartulina, brindando tecnología y profesionalismo en productos para la industria argentina.',
    ''
);
// Add intro images
$stmt = $db->prepare("SELECT COUNT(*) FROM section_items WHERE section_id = ?");
$stmt->execute([$intro_id]);
if ($stmt->fetchColumn() == 0) {
    add_item($db, $intro_id, 'Image 1', '', 'https://lh3.googleusercontent.com/aida-public/AB6AXuD573xwkZPERcEuYAsMKSIiFNJvjw2GYwqCoi-KtCBfKpPNMwyVRmHiImGkzJkrvdeer-0XcIRT8jOku89N-ioRvHudTdOfjmfO5CLjF5-WNjqXFeJhZ7BLpQf2fv47t_CjFZ4WL3g2UtpgkhbUGgmAyK42ENJ62TtGmmD2FkuCrrlKXoG1-E8keIL10xoo9I5CfC0DGHlmiWpibBQD5u4V0tZUgCem4liQnPiFIGkSL-Hc6IHASq1PlwJHn8TRgd58HXdrBakkEw6Y');
    add_item($db, $intro_id, 'Image 2', '', 'https://lh3.googleusercontent.com/aida-public/AB6AXuDlqpRLtu1uDEthVvFTtL0TlUlKWENga2E7Hn6IpNsPyycga3g_ag5kpokjoVdzKi_ka1F8Sk1tMHf_r0_zw3ukZKJliD7ak0BQMCNf84bm4Jdn3iX9rOnzzruvPLV8BALkJ8StjYn5yRfyxrP_UZojNxtyMoYtsZxVU82G4hVsOBr2GtNEllPKc3uDEHehSlT2xX-ljW02pmNEyaEB-RTokVSleOO4tulLceBOojsFHZ-0hbCFHxdtu62Aj7K87Nh40E-ZDeAdQfJP');
    add_item($db, $intro_id, 'Image 3', '', 'https://lh3.googleusercontent.com/aida-public/AB6AXuDQRQYTvEl5BIKzaXCi2IEHf0L26AhCFaA5OrGiZDiO8rYVFSSCA-9RODYK_IhNJckceBjQRnNQl2J9ZiP2l33676niCxFojxqN4WvYyV49W1Zt4wiLBTsjXH4rZN7myn5qwo7NRFsk6P-V_Rc_c8djiq5g0gI2tQC9eqGzis6mCqsApj9aIlFlo6ChB_cmX06AogrW4TNoPCxHdJwhRtx4Hs-DN8b2Y1wEVudNpzbkMMqw8Esvr1MjMmWY9gVOsJaqFBgPVIWoa2gI');
}

// 3. Advisory Section
$advisory_id = create_section(
    $db,
    $page_id,
    'advisory',
    'Asesoría', // Using title for one block, subtitle or items for others? 
    'Asesoría', // Subtitle used? 
    '',
    'https://lh3.googleusercontent.com/aida-public/AB6AXuCqnCL79ihlXvhgLkB7fMmYjdL8A6h7lQMtPYYpzAmkZ6x4CYpA7kzQoBr4XWexkZ4BntCfYt0265CXwUTHW8KjWaM6O3c_aGD5j6SRDMe5-xnXnYQAB_Zwnrxh2sYNy2zDr8lZcBeqqParmXcGbSnYspY6FkBkHjVC0SzfgDh0Axpj-BTiXBa2KgX7LnNkJKCsrOSO9JE5hTyQX8ZX-B-HehAyM8fPQOpaLqEraYufUqmfQJQsPPs6hOgpsDyF7_ai71N3gsq9P2Xk'
);
// This section structure in mockup is: Image Left, Two Right Blocks ("Asesoría", "Análisis Estructural").
// I'll create 2 items for the right blocks.
$stmt = $db->prepare("SELECT COUNT(*) FROM section_items WHERE section_id = ?");
$stmt->execute([$advisory_id]);
if ($stmt->fetchColumn() == 0) {
    add_item($db, $advisory_id, 'Asesoría', 'Acompañamos a nuestros clientes en el desarrollo de piezas impresas con calidad, estética y eficiencia en la maquinabilidad. Empresas farmacéuticas, de cosmética y alimenticias son nuestras principales aliadas.', '', 'Watermark: Asesoría');
    add_item($db, $advisory_id, 'Análisis Estructural', 'Nuestro Departamento de Preprensa analiza la viabilidad estructural del estuche a fabricar, garantizando soluciones para que el packaging proteja, optimice costos y mejore la experiencia del cliente desde el primer contacto con el producto.', '', 'Watermark: Análisis Estructural');
}

// 4. Badges Section
$badges_id = create_section($db, $page_id, 'badges', 'Etiquetas de Calidad');
$stmt = $db->prepare("SELECT COUNT(*) FROM section_items WHERE section_id = ?");
$stmt->execute([$badges_id]);
if ($stmt->fetchColumn() == 0) {
    // Storing icon name in 'image' field (assuming we'll handle it as text in frontend)
    // Or I can use specific image URLs if they were images, but mockup uses material icons?
    // Mockup uses <span class="material-symbols-outlined text-xs">mountain_flag</span>
    // I'll store 'mountain_flag' in image field.
    add_item($db, $badges_id, 'Special Quality', '', 'print');
    add_item($db, $badges_id, 'Graphic', '', 'draw');
    add_item($db, $badges_id, 'Unique', '', 'mountain_flag');
    add_item($db, $badges_id, 'Premium', '', 'star');
    add_item($db, $badges_id, 'Label', '', 'label');
    add_item($db, $badges_id, 'Crafted', '', 'cut');
}

// 5. FAQ Section
$faq_id = create_section($db, $page_id, 'faq', 'Preguntas frecuentes', 'FAQ');
$stmt = $db->prepare("SELECT COUNT(*) FROM section_items WHERE section_id = ?");
$stmt->execute([$faq_id]);
if ($stmt->fetchColumn() == 0) {
    add_item($db, $faq_id, '¿Qué controles previos ejercen antes de la fabricación de un estuche?', 'Realizamos un análisis estructural vía software de cada archivo recepcionado. Se auditan distintas variables tabuladas y las mismas se validan con el cliente antes de la impresión. Garantizamos la confidencialidad y seguridad de la información y el diseño que se nos proporciona.');
    add_item($db, $faq_id, '¿Realizan mediciones de color en pie de máquina?', 'Sí, con coordenadas lab mediante el uso de espectrofotómetro.');
    add_item($db, $faq_id, '¿Qué prácticas sostenibles implementan en su proceso de producción?', 'Utilizamos tinta con bajo contenido en plomo y adquirimos de nuestros proveedores cartulinas que cuenten con certificación FSC.');
}

echo "Seeding completed for Packaging page.\n";
?>