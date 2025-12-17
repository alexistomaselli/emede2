<?php
require_once 'config.php';

// Remove old DB to recreate cleanly
if (file_exists(DB_FILE)) {
    unlink(DB_FILE);
    echo "Old database removed.<br>";
}

echo "Initializing Database...<br>";

$db = getDB();

// Create Settings Table
$db->exec("CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT
)");
echo "Table 'settings' created.<br>";

// Create Pages Table (With Template Column)
$db->exec("CREATE TABLE IF NOT EXISTS pages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug TEXT UNIQUE,
    title TEXT,
    template TEXT,
    content TEXT,
    in_menu INTEGER DEFAULT 1,
    menu_order INTEGER DEFAULT 0
)");
echo "Table 'pages' created.<br>";

// Insert Default Settings
$settings = [
    'site_title' => 'Gráfica Emede',
    'logo_text' => 'Gráfica Emede.',
    'phone' => '+456 456 4443',
    'email' => 'emede@emede.com.ar',
    'address' => 'Madame Curie 1141, Quilmes, Buenos Aires',
    'hero_title' => 'Más de 45 años <span class="highlight">imprimiendo confianza</span>',
    'hero_desc' => 'Somos el aliado gráfico de tu proyecto. Especialistas en packaging y soluciones impresas de alta calidad.',
    'hero_cta' => 'Cotizar'
];

foreach ($settings as $key => $val) {
    set_setting($key, $val);
}
echo "Global Settings initialized.<br>";

// Insert Pages with Templates
// We seed the content as JSON where applicable for structured fields

// 0. Inicio (Start Template)
$start_content = json_encode([
    'hero_title' => 'Más de 45 años <span class="highlight">imprimiendo confianza</span>',
    'hero_desc' => 'Somos el aliado gráfico de tu proyecto. Especialistas en packaging y soluciones impresas de alta calidad.',
    'hero_cta' => 'Cotizar',
    'hero_image' => 'extracted_assets/image_p2_0.png'
]);
$db->prepare("INSERT INTO pages (slug, title, template, content, in_menu, menu_order) VALUES (?, ?, ?, ?, 0, 0)") // in_menu=0 for home usually, or 1? Let's say 0 if logo links there, but user might want it. Let's set 0 as it's the root.
    ->execute(['inicio', 'Inicio', 'start', $start_content]);

// 1. Trayectoria (About Template)
// Structure: { "history_title": "", "history_text": "", "mission_title": "", "mission_text": "", "image_url": "" }
$about_content = json_encode([
    'history_title' => 'Nuestra Historia',
    'history_text' => 'A lo largo del tiempo, hemos sabido reinventarnos y evolucionar escuchando al mercado. Apuntamos a la plena optimización en todas las etapas productivas.',
    'mission_title' => 'Política de Calidad',
    'mission_text' => 'Todos nuestros procesos de fabricación se desarrollan bajo Sistema de Gestión de Calidad certificado por la norma ISO 9001:2015.',
    'image_url' => 'extracted_assets/image_p2_0.png'
]);
$db->prepare("INSERT INTO pages (slug, title, template, content, in_menu, menu_order) VALUES (?, ?, ?, ?, 1, 0)")
    ->execute(['trayectoria', 'Trayectoria', 'about', $about_content]);

// 2. Packaging (Services Template)
// Structure: { "subtitle": "", "intro": "", "services": [ { "title": "", "image": "" }, ... ] }
// Simplified for V1: Just main text and a few hardcoded fields in the template or JSON.
// Let's store the main Intro text and 3 service blocks in JSON.
$services_content = json_encode([
    'subtitle' => 'Servicios',
    'intro' => 'Estuches que potencian y protegen tus productos. El primer contacto físico entre tu producto y el consumidor.',
    'service_1_title' => 'Industria Farmacéutica',
    'service_1_image' => 'extracted_assets/image_p6_0.png',
    'service_2_title' => 'Gastronomía',
    'service_2_image' => 'extracted_assets/image_p6_1.png',
    'service_3_title' => 'Cosmética',
    'service_3_image' => 'extracted_assets/image_p6_2.png',
    'extra_title_1' => 'Asesoría',
    'extra_text_1' => 'Acompañamos a nuestros clientes en el desarrollo de piezas impresas con calidad.',
    'extra_title_2' => 'Análisis Estructural',
    'extra_text_2' => 'Nuestro Departamento de Preprensa analiza la viabilidad estructural.'
]);
$db->prepare("INSERT INTO pages (slug, title, template, content, in_menu, menu_order) VALUES (?, ?, ?, ?, 1, 1)")
    ->execute(['packaging', 'Packaging', 'services', $services_content]);

// 3. Posavasos (Services Template - Reused)
$posavasos_content = json_encode([
    'subtitle' => 'The Coaster Company',
    'intro' => '10.000.000 posavasos fabricados por año. Utilizamos pasta mecánica que absorbe tres veces su peso en agua.',
    'service_1_title' => 'Vida útil superior (8 usos)',
    'service_1_image' => 'extracted_assets/image_p9_0.png', // Main image here
    'service_2_title' => 'Formatos Circulares y Cuadrados',
    'service_2_image' => '', // Optional
    'service_3_title' => 'Materia prima sustentable',
    'service_3_image' => '', // Optional
    'extra_title_1' => '',
    'extra_text_1' => '',
    'extra_title_2' => '',
    'extra_text_2' => ''
]);
$db->prepare("INSERT INTO pages (slug, title, template, content, in_menu, menu_order) VALUES (?, ?, ?, ?, 1, 2)")
    ->execute(['posavasos', 'Posavasos', 'services', $posavasos_content]);

// 4. Comercial (Services Template - Reused)
$comercial_content = json_encode([
    'subtitle' => 'Material Comercial',
    'intro' => 'Catálogos, etiquetas y material POP encuentran en nuestra gráfica el respaldo técnico y creativo necesario.',
    'service_1_title' => 'Catálogos',
    'service_1_image' => '',
    'service_2_title' => 'Etiquetas',
    'service_2_image' => '',
    'service_3_title' => 'Material POP',
    'service_3_image' => '',
    'extra_title_1' => '',
    'extra_text_1' => '',
    'extra_title_2' => '',
    'extra_text_2' => ''
]);
$db->prepare("INSERT INTO pages (slug, title, template, content, in_menu, menu_order) VALUES (?, ?, ?, ?, 1, 3)")
    ->execute(['comercial', 'Comercial', 'services', $comercial_content]);

// 5. Galeria (Gallery Template)
// Structure: { "images": [ "url1", "url2", ... ] }
$gallery_content = json_encode([
    'images' => [
        'extracted_assets/image_p13_0.png',
        'extracted_assets/image_p11_0.png',
        'extracted_assets/image_p11_1.png',
        'extracted_assets/image_p8_0.png',
        'extracted_assets/image_p8_1.png',
        'extracted_assets/image_p9_1.png'
    ]
]);
$db->prepare("INSERT INTO pages (slug, title, template, content, in_menu, menu_order) VALUES (?, ?, ?, ?, 1, 4)")
    ->execute(['galeria', 'Galería', 'gallery', $gallery_content]);


echo "Pages and Templates initialized.<br>";
echo "Database Setup Complete (v2 - Templates). Delete this file in production.";
?>