<?php
require_once __DIR__ . '/../includes/db.php';
if (!isset($page_slug))
    $page_slug = 'home';
$page_data = get_page_data($page_slug);

// Fetch hero/banners for this page
$page_banners = get_banners($page_data['id']);
$page_hero = !empty($page_banners) ? $page_banners[0] : null;
?>
<!DOCTYPE html>
<html class="scroll-smooth" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title><?php
    $seo_suffix = get_setting('seo_title_suffix', ' | Gráfica Emede');
    echo !empty($page_data['meta_title']) ? $page_data['meta_title'] . $seo_suffix : 'Gráfica Emede | ' . $page_data['title'];
    ?></title>

    <meta name="description"
        content="<?php echo htmlspecialchars($page_data['meta_description'] ?? get_setting('seo_default_description')); ?>">
    <meta name="keywords"
        content="<?php echo htmlspecialchars($page_data['meta_keywords'] ?? get_setting('seo_default_keywords')); ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url"
        content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    <meta property="og:title"
        content="<?php echo htmlspecialchars($page_data['meta_title'] ?? $page_data['title']); ?>">
    <meta property="og:description"
        content="<?php echo htmlspecialchars($page_data['meta_description'] ?? get_setting('seo_default_description')); ?>">
    <meta property="og:image"
        content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; ?><?php echo get_setting('og_image', 'media/og-image.jpg'); ?>">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title"
        content="<?php echo htmlspecialchars($page_data['meta_title'] ?? $page_data['title']); ?>">
    <meta property="twitter:description"
        content="<?php echo htmlspecialchars($page_data['meta_description'] ?? get_setting('seo_default_description')); ?>">
    <meta property="twitter:image"
        content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; ?><?php echo get_setting('og_image', 'media/og-image.jpg'); ?>">

    <link rel="canonical"
        href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>" />

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "Gráfica Emede",
      "image": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; ?><?php echo get_setting('site_logo', 'media/logo.png'); ?>",
      "url": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/"; ?>",
      "telephone": "<?php echo get_setting('contact_phone', '+54 11 4250-1234'); ?>",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Madame Curie 1141",
        "addressLocality": "Quilmes",
        "addressRegion": "Buenos Aires",
        "postalCode": "1878",
        "addressCountry": "AR"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": -34.7245, 
        "longitude": -58.2520
      },
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": [
          "Monday",
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday"
        ],
        "opens": "08:00",
        "closes": "18:00"
      },
      "sameAs": [
        "https://www.facebook.com/graficaemede",
        "https://www.instagram.com/graficaemede"
      ]
    }
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#1e3a8a",
                        secondary: "#3b82f6",
                        accent: "#3b82f6",
                        "background-light": "#ffffff",
                        "background-dark": "#0f172a",
                        "navy-dark": "#0a192f",
                        "navy-deep": "#051124",
                    },
                    fontFamily: {
                        display: ["Outfit", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                    },
                },
            },
        };
    </script>
    <style type="text/tailwindcss">
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('<?php echo $page_hero ? $page_hero['image'] : ''; ?>');
            background-size: cover;
            background-position: center;
        }
        /* Custom styles for home swiper */
        .hero-slide { position: relative; width: 100%; height: 100%; }
        .hero-image { position: absolute; inset: 0; background-size: cover; background-position: center; transition: transform 6s ease; }
        .swiper-slide-active .hero-image { transform: scale(1.1); }
        .hero-overlay { position: absolute; inset: 0; background: rgba(0, 0, 0, 0.7); }
        .hero-content { opacity: 0; transform: translateY(30px); transition: all 1s ease 0.5s; }
        .swiper-slide-active .hero-content { opacity: 1; transform: translateY(0); }
        
        #hero-slider {
            clip-path: ellipse(150% 100% at 50% 0%);
        }
        .nav-link { @apply text-[15px] font-medium text-slate-600 hover:text-accent transition-colors; }
        .nav-link.active { @apply text-accent; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        
        /* Swiper custom dots */
        #hero-slider .swiper-pagination-bullet {
            width: 14px;
            height: 14px;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.5);
            opacity: 1;
            margin: 0 6px !important;
            transition: all 0.3s ease;
        }
        #hero-slider .swiper-pagination-bullet-active {
            background: #ffffff;
            border-color: #ffffff;
            transform: scale(1.2);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        }
    </style>
</head>

<body class="font-display bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200">
    <header class="sticky top-0 left-0 w-full z-50 bg-white border-b border-slate-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center">
                <a href="index.php" class="flex items-center gap-2">
                    <?php
                    $logo = get_setting('site_logo');
                    if ($logo): ?>
                        <img src="<?php echo $logo; ?>" alt="Gráfica Emede" class="h-20 w-auto">
                    <?php else: ?>
                        <span class="text-2xl font-bold text-slate-900 tracking-tight">Gráfica Emede.</span>
                    <?php endif; ?>
                </a>
            </div>
            <nav class="hidden md:flex items-center gap-6 lg:gap-8">
                <?php $menu_items = get_menu_items(); ?>
                <?php foreach ($menu_items as $item): ?>
                    <a class="nav-link" href="<?php echo $item['url']; ?>">
                        <?php echo $item['label']; ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <div class="flex items-center">
                <a class="bg-accent hover:bg-blue-600 text-white px-8 py-2.5 rounded-full font-semibold transition-all shadow-md"
                    href="<?php echo get_setting('quote_url', '#'); ?>">
                    Cotizar
                </a>
            </div>
        </div>
    </header>