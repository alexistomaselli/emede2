<?php
$db_path = __DIR__ . '/../db/database.sqlite';

try {
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $settings = [
        [
            'key' => 'contact_address',
            'value' => 'Madame Curie 1141, Quilmes, Buenos Aires',
            'label' => 'Dirección de la Empresa',
            'type' => 'text'
        ],
        [
            'key' => 'contact_email',
            'value' => 'emede@emede.com.ar',
            'label' => 'Email de Contacto',
            'type' => 'text'
        ],
        [
            'key' => 'contact_phone',
            'value' => '+54 (11) 4250-1234',
            'label' => 'Teléfono de Contacto',
            'type' => 'text'
        ]
    ];

    $checkStmt = $db->prepare("SELECT id FROM settings WHERE key = ?");
    $insertStmt = $db->prepare("INSERT INTO settings (key, value, label, type) VALUES (?, ?, ?, ?)");

    foreach ($settings as $s) {
        $checkStmt->execute([$s['key']]);
        if (!$checkStmt->fetch()) {
            $insertStmt->execute([$s['key'], $s['value'], $s['label'], $s['type']]);
            echo "Added setting: {$s['key']}\n";
        } else {
            echo "Setting {$s['key']} already exists.\n";
        }
    }

    echo "Settings update completed.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
