<?php
require_once __DIR__ . '/../includes/db.php';

try {
    // Add columns to pages table for SEO
    $db->exec("ALTER TABLE pages ADD COLUMN meta_title TEXT");
    $db->exec("ALTER TABLE pages ADD COLUMN meta_description TEXT");
    $db->exec("ALTER TABLE pages ADD COLUMN meta_keywords TEXT");

    echo "Columns added to 'pages' table successfully.\n";

    // Add site-wide SEO settings
    $settings = [
        ['seo_title_suffix', ' | Gráfica Emede', 'Sufijo de Título SEO', 'text'],
        ['seo_default_description', 'Especialistas en packaging personalizado, posavasos para gastronomía y soluciones gráficas integrales. Más de 30 años de trayectoria.', 'Descripción SEO por defecto', 'textarea'],
        ['seo_default_keywords', 'packaging, posavasos, gráfica, imprenta, cajas personalizadas, diseño gráfico', 'Keywords por defecto', 'text'],
        ['og_image', 'media/og-image.jpg', 'Imagen para redes sociales (OG)', 'image']
    ];

    $stmt = $db->prepare("INSERT OR IGNORE INTO settings (key, value, label, type) VALUES (?, ?, ?, ?)");
    foreach ($settings as $s) {
        $stmt->execute($s);
    }

    echo "SEO settings added to 'settings' table successfully.\n";

    // Update existing pages with better default SEO data
    $seo_data = [
        'home' => [
            'meta_title' => 'Especialistas en Packaging y Posavasos Personalizados',
            'meta_description' => 'Gráfica Emede: Líderes en fabricación de packaging, posavasos para gastronomía y soluciones gráficas para empresas. Calidad y trayectoria en cada impresión.',
            'meta_keywords' => 'packaging personalizado, posavasos gastronomía, imprenta buenos aires, cajas de cartón, gráfica industrial'
        ],
        'packaging' => [
            'meta_title' => 'Packaging Personalizado y Soluciones de Envases',
            'meta_description' => 'Diseño y fabricación de packaging a medida. Cajas para e-commerce, estuches de cosmética, cajas de envío y envases industriales con la mejor terminación.',
            'meta_keywords' => 'packaging a medida, cajas personalizadas, envases de cartón, diseño estuches, packaging argentina'
        ],
        'posavasos' => [
            'meta_title' => 'Posavasos para Gastronomía y Eventos | Alta Calidad',
            'meta_description' => 'Fabricamos los mejores posavasos personalizados para bares, restaurantes y eventos. Materiales absorbentes y duraderos con impresión Full Color.',
            'meta_keywords' => 'posavasos personalizados, posavasos para bares, posavasos absorbentes, merchandising gastronomía'
        ],
        'comercial' => [
            'meta_title' => 'Gráfica Comercial e Impresión para Empresas',
            'meta_description' => 'Soluciones gráficas integrales: folletos, catálogos, papelería institucional y gráfica de gran formato. Potenciamos la imagen de tu marca.',
            'meta_keywords' => 'gráfica comercial, imprenta para empresas, folletos urgentes, papelería institucional, catálogos'
        ],
        'galeria' => [
            'meta_title' => 'Galería de Trabajos y Proyectos | Gráfica Emede',
            'meta_description' => 'Explora nuestro portfolio de packaging, posavasos y soluciones gráficas comerciales. Calidad garantizada en cada proyecto realizado.',
            'meta_keywords' => 'portfolio gráfica, fotos packaging, ejemplos posavasos, trabajos realizados emede'
        ],
        'trayectoria' => [
            'meta_title' => 'Nuestra Historia y Experiencia en el Sector Gráfico',
            'meta_description' => 'Conoce la trayectoria de Gráfica Emede. Más de tres décadas dedicadas a la innovación en el sector del packaging y la impresión industrial.',
            'meta_keywords' => 'historia emede, trayectoria gráfica, planta industrial emede, quienes somos'
        ]
    ];

    $updateStmt = $db->prepare("UPDATE pages SET meta_title = ?, meta_description = ?, meta_keywords = ? WHERE slug = ?");
    foreach ($seo_data as $slug => $data) {
        $updateStmt->execute([$data['meta_title'], $data['meta_description'], $data['meta_keywords'], $slug]);
    }

    echo "Existing pages updated with SEO data.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
