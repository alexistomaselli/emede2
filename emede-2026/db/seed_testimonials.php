<?php
$db_path = __DIR__ . '/../db/database.sqlite';

try {
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Get Home Page ID
    $stmt = $db->prepare("SELECT id FROM pages WHERE slug = 'home'");
    $stmt->execute();
    $home_id = $stmt->fetchColumn();

    if (!$home_id) {
        die("Error: Home page not found.\n");
    }

    // 2. Get or Create Testimonials Section
    $stmt = $db->prepare("SELECT id FROM page_sections WHERE page_id = ? AND section_key = 'testimonials'");
    $stmt->execute([$home_id]);
    $section_id = $stmt->fetchColumn();

    if (!$section_id) {
        echo "Creating 'testimonials' section...\n";
        $sql = "INSERT INTO page_sections (page_id, section_key, title, content, item_order) VALUES (?, 'testimonials', 'TESTIMONIALS', 'Lo que dicen nuestros clientes', 4)";
        $db->prepare($sql)->execute([$home_id]);
        $section_id = $db->lastInsertId();
    } else {
        echo "Found 'testimonials' section ID: $section_id\n";
    }

    // 3. Clear existing items in this section
    $db->prepare("DELETE FROM section_items WHERE section_id = ?")->execute([$section_id]);
    echo "Cleared existing testimonials.\n";

    // 4. Insert Mockup Data
    $testimonials = [
        [
            'title' => 'María Eugenia Vigna',
            'content' => 'Muy profesionales. Atentos al detalle de cada impresión',
            'image' => '',
            'extra_link' => 'Diseño y comunicación - Laboratorio Merlino',
            'rating' => 5
        ],
        [
            'title' => 'Florencia Crivella',
            'content' => "Desde el primer contacto, la atención fue muy buena. Son super prolijos y responsables para trabajar. Los pedidos que fueron planificados cumplieron con los plazos de entrega. Siempre que tuvimos dudas sobre los gramajes de los papeles nos ayudaron a elegir lo mejor que se adaptaba a nuestra necesidad. Excelente desde la calidad humana, hasta el producto. ¡Súper recomendados!",
            'image' => '',
            'extra_link' => 'Silvetex',
            'rating' => 5
        ],
        [
            'title' => 'Paula Oliveros',
            'content' => 'Gráfica Emede se destaca especialmente por la personalización en la atención, siempre dispuestos a escuchar nuestras necesidades y adaptarse a lo que buscamos. Valoramos mucho su predisposición para mejorar continuamente y el compromiso que demuestran en cada etapa del servicio.',
            'image' => '', // Will fallback to UI Avatar
            'extra_link' => 'Responsable de compras y Diseño - Laboratorio Géminis',
            'rating' => 5
        ],
        [
            'title' => 'Eliana Paesani',
            'content' => 'Gráfica EMEDE nos acompaña desde hace más de cinco años en el suministro de estuches farmacéuticos. Su capacidad de adaptación y compromiso con la calidad los ha convertido en un socio estratégico esencial para cumplir con nuestra visión y estándares.',
            'image' => '', // Will fallback to UI Avatar
            'extra_link' => 'Líder de Compras - Savant',
            'rating' => 5
        ]
    ];

    $insertStmt = $db->prepare("INSERT INTO section_items (section_id, title, content, image, extra_link, rating) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($testimonials as $t) {
        $insertStmt->execute([
            $section_id,
            $t['title'],
            $t['content'],
            $t['image'],
            $t['extra_link'],
            $t['rating']
        ]);
        echo "Inserted testimonial: " . $t['title'] . "\n";
    }

    echo "Seeding completed successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
