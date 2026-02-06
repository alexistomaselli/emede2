<?php
require_once __DIR__ . '/../includes/db.php';

// Create Posavasos Page
$slug = 'posavasos';
$title = 'Posavasos';

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
    // Update existing section just in case title/subtitle changed
    $stmt = $db->prepare("UPDATE page_sections SET title = ?, subtitle = ? WHERE id = ?");
    $stmt->execute([$title, $subtitle, $existing]);

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
    'El apoyo que tu marca necesita',
    '',
    '',
    '',
    'https://images.unsplash.com/photo-1574169208507-84376144848b?auto=format&fit=crop&q=80&w=2000'
);

// 2. Introduction Section
$intro_id = create_section(
    $db,
    $page_id,
    'introduction',
    'Líderes en la fabricación de posavasos',
    '10.000.000 posavasos fabricados por año',
    'Utilizamos pasta mecánica (groundwood pulp) del mayor productor mundial, que absorbe tres veces su peso en agua. Esto hace que la vida útil de nuestros posavasos sea mayor al resto, ya que se pueden utilizar hasta 8 veces promedio sin que se deformen.',
    ''
);
// Items for intro images
$stmt = $db->prepare("DELETE FROM section_items WHERE section_id = ?"); // Clear old items to re-seed correctly
$stmt->execute([$intro_id]);

add_item($db, $intro_id, 'Posavasos 1', '', 'https://lh3.googleusercontent.com/aida-public/AB6AXuD573xwkZPERcEuYAsMKSIiFNJvjw2GYwqCoi-KtCBfKpPNMwyVRmHiImGkzJkrvdeer-0XcIRT8jOku89N-ioRvHudTdOfjmfO5CLjF5-WNjqXFeJhZ7BLpQf2fv47t_CjFZ4WL3g2UtpgkhbUGgmAyK42ENJ62TtGmmD2FkuCrrlKXoG1-E8keIL10xoo9I5CfC0DGHlmiWpibBQD5u4V0tZUgCem4liQnPiFIGkSL-Hc6IHASq1PlwJHn8TRgd58HXdrBakkEw6Y');
add_item($db, $intro_id, 'Posavasos 2', '', 'https://lh3.googleusercontent.com/aida-public/AB6AXuDlqpRLtu1uDEthVvFTtL0TlUlKWENga2E7Hn6IpNsPyycga3g_ag5kpokjoVdzKi_ka1F8Sk1tMHf_r0_zw3ukZKJliD7ak0BQMCNf84bm4Jdn3iX9rOnzzruvPLV8BALkJ8StjYn5yRfyxrP_UZojNxtyMoYtsZxVU82G4hVsOBr2GtNEllPKc3uDEHehSlT2xX-ljW02pmNEyaEB-RTokVSleOO4tulLceBOojsFHZ-0hbCFHxdtu62Aj7K87Nh40E-ZDeAdQfJP');
add_item($db, $intro_id, 'Posavasos 3', '', 'https://lh3.googleusercontent.com/aida-public/AB6AXuDQRQYTvEl5BIKzaXCi2IEHf0L26AhCFaA5OrGiZDiO8rYVFSSCA-9RODYK_IhNJckceBjQRnNQl2J9ZiP2l33676niCxFojxqN4WvYyV49W1Zt4wiLBTsjXH4rZN7myn5qwo7NRFsk6P-V_Rc_c8djiq5g0gI2tQC9eqGzis6mCqsApj9aIlFlo6ChB_cmX06AogrW4TNoPCxHdJwhRtx4Hs-DN8b2Y1wEVudNpzbkMMqw8Esvr1MjMmWY9gVOsJaqFBgPVIWoa2gI');


// 3. Badges/Logos Section
$badges_id = create_section($db, $page_id, 'badges', 'Nuestros Clientes');
// Clear old items to start fresh
$stmt = $db->prepare("DELETE FROM section_items WHERE section_id = ?");
$stmt->execute([$badges_id]);

// Add logos from media/logos-posavasos
$logos = [
    'Imperial' => 'media/logos-posavasos/Imperial.png',
    'La Paloma' => 'media/logos-posavasos/la paloma.png',
    'Kraken' => 'media/logos-posavasos/Kraken.png',
    'Baum' => 'media/logos-posavasos/Baum.png',
    'Heineken' => 'media/logos-posavasos/Heineken.png',
    'Antares' => 'media/logos-posavasos/Antares.png',
    'Jägermeister' => 'media/logos-posavasos/Jägermeister.png',
    'Stella' => 'media/logos-posavasos/Stella.png',
    'Jensen' => 'media/logos-posavasos/Jensen.png'
];

foreach ($logos as $title => $img) {
    add_item($db, $badges_id, $title, '', $img);
}

// 4. FAQ Section
$faq_id = create_section($db, $page_id, 'faq', 'Conocé The Coaster Company', 'FAQ');
// Clear old items
$stmt = $db->prepare("DELETE FROM section_items WHERE section_id = ?");
$stmt->execute([$faq_id]);

add_item($db, $faq_id, '¿De dónde proviene la materia prima utilizada?', 'De bosques reforestables. Nos permite producirlos naturalmente, sin ningún compuesto agregado.');
add_item($db, $faq_id, '¿En qué formatos se presentan?', 'Manejamos dos formatos y dos medidas standard: circulares y cuadrados de 9 cms y 10 cms. Recibimos consultas para entregas con formas especiales.');
add_item($db, $faq_id, '¿Qué opciones de personalización están disponibles?', 'Según la cantidad solicitada, te asesoramos sobre cuántos motivos podrás combinar en el pliego de impresión.');

echo "Seeding completed for Posavasos page (Updated).\n";
?>