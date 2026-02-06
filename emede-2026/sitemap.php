<?php
header("Content-Type: application/xml; charset=utf-8");
require_once __DIR__ . '/includes/db.php';

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>
            <?php echo $base_url; ?>/
        </loc>
        <priority>1.0</priority>
        <changefreq>weekly</changefreq>
    </url>
    <?php
    $stmt = $db->query("SELECT slug FROM pages WHERE slug != 'home'");
    while ($row = $stmt->fetch()) {
        $url = $base_url . '/' . $row['slug'];
        echo "    <url>\n";
        echo "        <loc>" . $url . "</loc>\n";
        echo "        <priority>0.8</priority>\n";
        echo "        <changefreq>monthly</changefreq>\n";
        echo "    </url>\n";
    }
    ?>
</urlset>