<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

// Simple password protection
$admin_pass = 'emede2026';
if (isset($_POST['login'])) {
    if ($_POST['password'] === $admin_pass) {
        $_SESSION['admin_auth'] = true;
    } else {
        $error = "Contraseña incorrecta";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['admin_auth'])) {
    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Admin Login - Gráfica Emede</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap" rel="stylesheet">
    </head>

    <body class="bg-[#0f172a] h-screen flex items-center justify-center font-['Outfit']">
        <div class="bg-white/10 backdrop-blur-xl p-8 rounded-3xl shadow-2xl w-full max-w-md border border-white/20">
            <div class="text-center mb-10">
                <div
                    class="w-16 h-16 bg-blue-600 rounded-2xl mx-auto mb-4 flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <span class="material-symbols-outlined text-white text-3xl">admin_panel_settings</span>
                </div>
                <h1 class="text-2xl font-bold text-white uppercase tracking-widest">Emede CMS</h1>
            </div>
            <?php if (isset($error))
                echo "<p class='text-red-400 text-center mb-4 text-sm'>$error</p>"; ?>
            <form method="POST" class="space-y-4">
                <input type="password" name="password" placeholder="Contraseña Maestro"
                    class="w-full p-4 bg-white/5 border border-white/10 rounded-xl text-white outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                <button name="login"
                    class="w-full bg-blue-600 text-white p-4 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg hover:shadow-blue-500/40">Acceder</button>
            </form>
        </div>
    </body>

    </html>
    <?php
    exit;
}

$section = $_GET['section'] ?? 'dashboard';

// --- Actions ---

// Save Page & Banners
if (isset($_POST['save_page'])) {
    $page_id = $_POST['page_id'];
    $db->beginTransaction();
    try {
        // 1. Update banners data
        if (isset($_POST['banners'])) {
            $stmt = $db->prepare("UPDATE banners SET title = ?, subtitle = ?, image = ?, button_text = ?, button_link = ?, status = ? WHERE id = ?");
            foreach ($_POST['banners'] as $id => $data) {
                $stmt->execute([
                    $data['title'],
                    $data['subtitle'],
                    $data['image'],
                    $data['button_text'],
                    $data['button_link'],
                    isset($data['status']) ? 1 : 0,
                    $id
                ]);
            }
        }

        // 2. Update order if provided
        if (!empty($_POST['banner_order'])) {
            $order = explode(',', $_POST['banner_order']);
            $stmt_order = $db->prepare("UPDATE banners SET item_order = ? WHERE id = ?");
            foreach ($order as $index => $id) {
                $stmt_order->execute([$index + 1, $id]);
            }
        }

        // 3. Update sections
        if (isset($_POST['sections'])) {
            $stmt_sec = $db->prepare("UPDATE page_sections SET title = ?, content = ?, image = ?, subtitle = ?, image2 = ?, image3 = ?, image4 = ?, show_image = ?, status = ? WHERE id = ?");
            foreach ($_POST['sections'] as $id => $data) {
                $stmt_sec->execute([
                    $data['title'],
                    $data['content'],
                    $data['image'],
                    $data['subtitle'] ?? null,
                    $data['image2'] ?? null,
                    $data['image3'] ?? null,
                    $data['image4'] ?? null,
                    isset($data['show_image']) ? 1 : 0,
                    isset($data['status']) ? 1 : 0,
                    $id
                ]);
            }
        }

        $db->commit();
        $_SESSION['admin_msg'] = "Contenido de página actualizado correctamente.";
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['admin_err'] = "Error: " . $e->getMessage();
    }
    header("Location: ?section=pages&edit=" . $page_id);
    exit;
}

// Delete Section
if (isset($_GET['delete_section'])) {
    $section_id = $_GET['delete_section'];
    $page_id = $_GET['page_id'];
    $stmt = $db->prepare("DELETE FROM page_sections WHERE id = ?");
    $stmt->execute([$section_id]);
    $_SESSION['admin_msg'] = "Sección eliminada permanentemente.";
    header("Location: ?section=pages&edit=" . $page_id);
    exit;
}


// Add Banner (Home only)
if (isset($_GET['add_banner'])) {
    $page_id = $_GET['add_banner'];
    $stmt = $db->prepare("INSERT INTO banners (page_id, title, subtitle, image, item_order) VALUES (?, 'Nuevo Slide', 'Descripción...', '', 99)");
    $stmt->execute([$page_id]);
    header("Location: ?section=pages&edit=" . $page_id);
    exit;
}

// Delete Banner
if (isset($_GET['delete_banner'])) {
    $banner_id = $_GET['delete_banner'];
    $page_id = $_GET['page_id'];
    $stmt = $db->prepare("DELETE FROM banners WHERE id = ?");
    $stmt->execute([$banner_id]);
    header("Location: ?section=pages&edit=" . $page_id);
    exit;
}

// Save Navigation
if (isset($_POST['save_menu'])) {
    $db->beginTransaction();
    try {
        $order = explode(',', $_POST['menu_order_ids']);
        $stmt_order = $db->prepare("UPDATE menus SET menu_order = ? WHERE id = ?");
        foreach ($order as $index => $id) {
            $stmt_order->execute([$index + 1, $id]);
        }
        $stmt = $db->prepare("UPDATE menus SET label = ?, url = ?, is_active = ? WHERE id = ?");
        foreach ($_POST['menu'] as $id => $data) {
            $stmt->execute([$data['label'], $data['url'], isset($data['is_active']) ? 1 : 0, $id]);
        }
        $db->commit();
        $_SESSION['admin_msg'] = "Navegación actualizada.";
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['admin_err'] = $e->getMessage();
    }
    header("Location: ?section=nav");
    exit;
}

// Save Section Items
if (isset($_POST['save_items'])) {
    $section_id = $_POST['section_id'];
    $page_id = $_POST['page_id'];
    $db->beginTransaction();
    try {
        if (isset($_POST['items'])) {
            $stmt = $db->prepare("UPDATE section_items SET title = ?, content = ?, image = ?, extra_link = ? WHERE id = ?");
            foreach ($_POST['items'] as $id => $data) {
                $stmt->execute([$data['title'] ?? '', $data['content'] ?? '', $data['image'] ?? '', $data['extra_link'] ?? '', $id]);
            }
        }
        $db->commit();
        $_SESSION['admin_msg'] = "Elementos de sección actualizados.";
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['admin_err'] = $e->getMessage();
    }
    header("Location: ?section=pages&edit=" . $page_id);
    exit;
}

// Add Section Item
if (isset($_GET['add_item'])) {
    $section_id = $_GET['add_item'];
    $page_id = $_GET['page_id'];
    $stmt = $db->prepare("INSERT INTO section_items (section_id, title) VALUES (?, 'Nuevo Elemento')");
    $stmt->execute([$section_id]);
    header("Location: ?section=pages&edit=" . $page_id);
    exit;
}

// Delete Section Item
if (isset($_GET['delete_item'])) {
    $item_id = $_GET['delete_item'];
    $page_id = $_GET['page_id'];
    $stmt = $db->prepare("DELETE FROM section_items WHERE id = ?");
    $stmt->execute([$item_id]);
    header("Location: ?section=pages&edit=" . $page_id);
    exit;
}

// Save General Settings
if (isset($_POST['save_settings'])) {
    $db->beginTransaction();
    try {
        $stmt = $db->prepare("UPDATE settings SET value = ? WHERE key = ?");
        foreach ($_POST['settings'] as $key => $value) {
            $stmt->execute([$value, $key]);
        }
        $db->commit();
        $_SESSION['admin_msg'] = "Ajustes actualizados correctamente.";
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['admin_err'] = $e->getMessage();
    }
    header("Location: ?section=settings");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Emede Admin | <?php echo ucfirst($section); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .sidebar-link.active {
            @apply bg-blue-600 text-white shadow-lg shadow-blue-500/30;
        }

        .sidebar-link:not(.active) {
            @apply text-slate-400 hover:bg-slate-800 hover:text-white;
        }

        .ghost-slide {
            @apply opacity-40 bg-blue-600/10;
        }
    </style>
</head>

<body class="bg-[#0b1120] text-slate-300 min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-72 bg-[#0f172a] border-r border-white/5 flex flex-col fixed h-full z-50">
        <div class="p-8 pb-10 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                <span class="material-symbols-outlined text-white text-xl">rocket_launch</span>
            </div>
            <span class="text-xl font-bold text-white tracking-tight">Emede CMS.</span>
        </div>

        <div class="px-4 mb-6">
            <a href="../" target="_blank"
                class="flex items-center justify-center gap-2 p-3.5 rounded-xl bg-blue-600/10 hover:bg-blue-600/20 text-blue-400 border border-blue-500/20 transition-all font-bold text-xs uppercase tracking-widest">
                <span class="material-symbols-outlined text-sm">open_in_new</span> Ver Sitio Web
            </a>
        </div>

        <nav class="flex-grow px-4 space-y-2">
            <a href="?section=dashboard"
                class="sidebar-link flex items-center gap-3 p-3.5 rounded-xl transition-all <?php echo $section == 'dashboard' ? 'active' : ''; ?>">
                <span class="material-symbols-outlined">dashboard</span> Dashboard
            </a>
            <div class="pt-4 pb-2 px-3 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Sitio Web</div>
            <a href="?section=pages"
                class="sidebar-link flex items-center gap-3 p-3.5 rounded-xl transition-all <?php echo $section == 'pages' ? 'active' : ''; ?>">
                <span class="material-symbols-outlined">description</span> Páginas
            </a>
            <a href="?section=media"
                class="sidebar-link flex items-center gap-3 p-3.5 rounded-xl transition-all <?php echo $section == 'media' ? 'active' : ''; ?>">
                <span class="material-symbols-outlined">image</span> Medios
            </a>
            <a href="?section=nav"
                class="sidebar-link flex items-center gap-3 p-3.5 rounded-xl transition-all <?php echo $section == 'nav' ? 'active' : ''; ?>">
                <span class="material-symbols-outlined">menu</span> Navegación
            </a>
            <a href="?section=settings"
                class="sidebar-link flex items-center gap-3 p-3.5 rounded-xl transition-all <?php echo $section == 'settings' ? 'active' : ''; ?>">
                <span class="material-symbols-outlined">settings</span> Ajustes
            </a>
        </nav>

        <div class="p-6 border-t border-white/5">
            <a href="?logout=1"
                class="flex items-center gap-3 p-3.5 rounded-xl text-slate-500 hover:bg-red-500/10 hover:text-red-500 transition-all font-medium">
                <span class="material-symbols-outlined">logout</span> Salir
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow ml-72 p-10">
        <!-- Messages -->
        <?php
        $msg = $_SESSION['admin_msg'] ?? null;
        $err = $_SESSION['admin_err'] ?? null;
        unset($_SESSION['admin_msg'], $_SESSION['admin_err']);
        if ($msg)
            echo "<div class='bg-green-500/10 border border-green-500/20 text-green-400 p-4 rounded-xl mb-8 flex items-center gap-3'><span class='material-symbols-outlined text-sm'>check_circle</span>$msg</div>";
        if ($err)
            echo "<div class='bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-8 flex items-center gap-3'><span class='material-symbols-outlined text-sm'>error</span>$err</div>";
        ?>

        <?php if ($section == 'dashboard' || ($section == 'pages' && !isset($_GET['edit']))): ?>
            <header class="flex justify-between items-center mb-10">
                <h2 class="text-2xl font-bold text-white uppercase tracking-tight">Páginas Disponibles</h2>
                <a href="../index.php" target="_blank"
                    class="px-5 py-2.5 bg-white/5 hover:bg-white/10 text-white rounded-xl text-sm font-medium border border-white/10 flex items-center gap-2 transition-all">
                    <span class="material-symbols-outlined text-sm">visibility</span> Ver Web
                </a>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php $pages = $db->query("SELECT * FROM pages")->fetchAll();
                foreach ($pages as $p): ?>
                    <a href="?section=pages&edit=<?php echo $p['id']; ?>"
                        class="group bg-[#0f172a] p-8 rounded-3xl border border-white/5 hover:border-blue-500/40 transition-all relative overflow-hidden">
                        <div class="flex items-center justify-between mb-4">
                            <span
                                class="px-3 py-1 bg-blue-500/10 text-blue-400 rounded-full text-[10px] font-black uppercase tracking-widest"><?php echo $p['template']; ?></span>
                            <span
                                class="material-symbols-outlined text-slate-600 group-hover:text-blue-500 transition-colors">edit_square</span>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2"><?php echo $p['title']; ?></h3>
                        <p class="text-sm text-slate-500 font-mono">/<?php echo ($p['slug'] == 'home') ? '' : $p['slug']; ?></p>
                        <div
                            class="absolute bottom-0 right-0 p-8 transform translate-x-4 translate-y-4 opacity-10 group-hover:translate-x-0 group-hover:translate-y-0 group-hover:opacity-20 transition-all">
                            <span class="material-symbols-outlined text-6xl">description</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

        <?php elseif ($section == 'pages' && isset($_GET['edit'])): ?>
            <?php
            $page_id = $_GET['edit'];
            $page = $db->prepare("SELECT * FROM pages WHERE id = ?");
            $page->execute([$page_id]);
            $p = $page->fetch();
            // Fetch banners associated with this page, ordered by item_order
            $banners_stmt = $db->prepare("SELECT * FROM banners WHERE page_id = ? ORDER BY item_order ASC");
            $banners_stmt->execute([$page_id]);
            $banners = $banners_stmt->fetchAll();

            // If it's not the home page and no banners exist, create a default one for hero management
            if ($p['template'] != 'home' && empty($banners)) {
                $db->prepare("INSERT INTO banners (page_id, title, subtitle, image, item_order) VALUES (?, 'Título de la Página', 'Subtítulo descriptivo', '', 1)")->execute([$page_id]);
                $banners_stmt->execute([$page_id]); // Re-fetch after insert
                $banners = $banners_stmt->fetchAll();
            }
            ?>

            <header class="flex items-center gap-4 mb-10">
                <a href="?section=pages"
                    class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-white/10 transition-all">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-white">Editando Página: <?php echo $p['title']; ?></h2>
                    <p class="text-slate-500 text-sm">Gestiona el hero y/o slider de esta página.</p>
                </div>
            </header>

            <form method="POST">
                <input type="hidden" name="page_id" value="<?php echo $page_id; ?>">
                <input type="hidden" name="banner_order" id="banner_order"
                    value="<?php echo implode(',', array_column($banners, 'id')); ?>">

                <div class="space-y-6" id="sortable-banners">
                    <?php if (empty($banners)): ?>
                        <div class="p-10 border-2 border-dashed border-white/5 rounded-3xl text-center">
                            <p class="text-slate-500 mb-4">No hay banners configurados para esta página.</p>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($banners as $b): ?>
                        <div class="bg-[#0f172a] p-8 rounded-3xl border border-white/5 relative group/card"
                            data-id="<?php echo $b['id']; ?>">
                            <div class="flex items-start justify-between mb-8">
                                <div class="flex items-center gap-4">
                                    <div class="cursor-move text-slate-600 hover:text-blue-500 transition-colors">
                                        <span class="material-symbols-outlined">drag_indicator</span>
                                    </div>
                                    <h4 class="text-xs font-bold text-blue-500 font-mono tracking-widest uppercase">
                                        <?php echo ($p['template'] == 'home') ? 'Slide #' . $b['id'] : 'Hero Principal'; ?>
                                    </h4>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2 pr-4 border-r border-white/5">
                                        <span
                                            class="text-[10px] font-bold text-slate-500 uppercase tracking-widest"><?php echo $b['status'] ? 'Visible' : 'Oculto'; ?></span>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="banners[<?php echo $b['id']; ?>][status]" value="1"
                                                <?php echo $b['status'] ? 'checked' : ''; ?> class="sr-only peer">
                                            <div
                                                class="w-10 h-5 bg-slate-700 peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600">
                                            </div>
                                        </label>
                                    </div>
                                    <?php if ($p['template'] == 'home'): ?>
                                        <a href="?section=pages&edit=<?php echo $page_id; ?>&delete_banner=<?php echo $b['id']; ?>&page_id=<?php echo $page_id; ?>"
                                            onclick="return confirm('¿Eliminar este slide?')"
                                            class="text-red-500/50 hover:text-red-500 p-2 hover:bg-red-500/10 rounded-lg transition-all">
                                            <span class="material-symbols-outlined">delete</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                                <div class="space-y-6">
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-slate-500 uppercase mb-3 tracking-widest">Título
                                            Prinicipal</label>
                                        <input type="text" name="banners[<?php echo $b['id']; ?>][title]"
                                            value='<?php echo htmlspecialchars($b['title']); ?>'
                                            class="w-full p-4 bg-white/5 border border-white/10 rounded-xl text-white outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[10px] font-bold text-slate-500 uppercase mb-3 tracking-widest">Subtítulo
                                            / Texto descriptivo</label>
                                        <textarea name="banners[<?php echo $b['id']; ?>][subtitle]"
                                            class="w-full p-4 bg-white/5 border border-white/10 rounded-xl text-white outline-none focus:ring-2 focus:ring-blue-500 h-24"><?php echo htmlspecialchars($b['subtitle']); ?></textarea>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <div class="space-y-6">
                                        <div>
                                            <label
                                                class="block text-[10px] font-bold text-slate-500 uppercase mb-3 tracking-widest">Imagen
                                                de fondo</label>
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-32 h-20 rounded-xl bg-slate-800 border-2 border-dashed border-white/5 overflow-hidden flex-shrink-0 relative group/img">
                                                    <img src="<?php echo (!empty($b['image']) && strpos($b['image'], 'http') !== 0) ? '../' . $b['image'] : $b['image']; ?>"
                                                        class="w-full h-full object-cover <?php echo empty($b['image']) ? 'hidden' : ''; ?>"
                                                        id="preview-<?php echo $b['id']; ?>">
                                                    <div
                                                        class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity">
                                                        <span
                                                            class="material-symbols-outlined text-white text-sm">photo_library</span>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="banners[<?php echo $b['id']; ?>][image]"
                                                    value="<?php echo $b['image']; ?>" id="input-<?php echo $b['id']; ?>">
                                                <button type="button"
                                                    onclick="openMediaPicker('input-<?php echo $b['id']; ?>', 'preview-<?php echo $b['id']; ?>')"
                                                    class="px-5 py-3 bg-white/5 hover:bg-white/10 text-white rounded-xl text-xs font-bold border border-white/10 transition-all">
                                                    Seleccionar Imagen
                                                </button>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label
                                                    class="block text-[10px] font-bold text-slate-500 uppercase mb-3 tracking-widest">Texto
                                                    Botón</label>
                                                <input type="text" name="banners[<?php echo $b['id']; ?>][button_text]"
                                                    value="<?php echo $b['button_text']; ?>"
                                                    class="w-full p-4 bg-white/5 border border-white/10 rounded-xl text-white outline-none focus:ring-2 focus:ring-blue-500">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-[10px] font-bold text-slate-500 uppercase mb-3 tracking-widest">Link
                                                    Botón</label>
                                                <input type="text" name="banners[<?php echo $b['id']; ?>][button_link]"
                                                    value="<?php echo $b['button_link']; ?>"
                                                    class="w-full p-4 bg-white/5 border border-white/10 rounded-xl text-white outline-none focus:ring-2 focus:ring-blue-500">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Page Sections (Content) -->
                    <div class="mt-16 pt-16 border-t border-white/10">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-xl font-bold text-white uppercase tracking-tight">Secciones de Contenido
                            </h3>
                        </div>

                        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
                        <div id="sections-container" class="space-y-8">
                            <?php
                            $sections = get_page_sections($page_id);
                            if (empty($sections)): ?>
                                <div class="p-10 border-2 border-dashed border-white/5 rounded-3xl text-center">
                                    <p class="text-slate-500">No hay secciones de contenido adicionales para esta página.
                                    </p>
                                </div>
                            <?php endif; ?>

                            <?php foreach ($sections as $s): ?>
                                <div id="section-<?php echo $s['section_key']; ?>" data-id="<?php echo $s['id']; ?>"
                                    class="bg-[#0f172a] p-8 rounded-3xl border border-white/5 relative scroll-mt-24 <?php echo !$s['status'] ? 'opacity-50 grayscale' : ''; ?>">
                                    <div class="flex items-center justify-between mb-6">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="cursor-move text-slate-600 hover:text-green-500 transition-colors handle">
                                                <span class="material-symbols-outlined">drag_indicator</span>
                                            </div>
                                            <h4 class="text-xs font-bold text-green-500 font-mono tracking-widest uppercase">
                                                Sección: <?php echo $s['section_key']; ?>
                                            </h4>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span
                                                class="text-[10px] font-bold text-white uppercase tracking-widest"><?php echo $s['status'] ? 'Activa' : 'Desactivada'; ?></span>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="sections[<?php echo $s['id']; ?>][status]"
                                                    value="1" <?php echo $s['status'] ? 'checked' : ''; ?> class="sr-only peer">
                                                <div
                                                    class="w-11 h-6 bg-slate-700 peer-focus:ring-2 peer-focus:ring-green-500 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600">
                                                </div>
                                            </label>
                                            <a href="?delete_section=<?php echo $s['id']; ?>&page_id=<?php echo $page_id; ?>"
                                                onclick="return confirm('¿Estás seguro de que quieres eliminar esta sección permanentemente? Esto no se puede deshacer.')"
                                                class="w-8 h-8 rounded-full bg-red-500/10 flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white transition-all">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="space-y-8">
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                            <div class="space-y-6">
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 uppercase mb-3 tracking-widest">Título
                                                        de la Sección</label>
                                                    <input type="text" name="sections[<?php echo $s['id']; ?>][title]"
                                                        value="<?php echo htmlspecialchars($s['title']); ?>"
                                                        class="w-full p-4 bg-white/5 border border-white/10 rounded-xl text-white outline-none focus:ring-2 focus:ring-blue-500">
                                                </div>
                                                <?php if ($s['section_key'] === 'about'): ?>
                                                    <div>
                                                        <label
                                                            class="block text-[10px] font-bold text-slate-500 uppercase mb-3 tracking-widest">Subtítulo
                                                            (ej: +45 AÑOS DE...)</label>
                                                        <input type="text" name="sections[<?php echo $s['id']; ?>][subtitle]"
                                                            value="<?php echo htmlspecialchars($s['subtitle'] ?? ''); ?>"
                                                            class="w-full p-4 bg-white/5 border border-white/10 rounded-xl text-white outline-none focus:ring-2 focus:ring-blue-500">
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 uppercase mb-3 tracking-widest">Contenido
                                                        / Párrafo</label>
                                                    <textarea name="sections[<?php echo $s['id']; ?>][content]"
                                                        class="w-full p-4 bg-white/5 border border-white/10 rounded-xl text-white outline-none focus:ring-2 focus:ring-blue-500 h-40"><?php echo htmlspecialchars($s['content']); ?></textarea>
                                                </div>
                                            </div>

                                            <div>
                                                <!-- Toggle to show/hide image field -->
                                                <div
                                                    class="mb-6 flex items-center justify-between p-4 bg-white/5 rounded-xl border border-white/5">
                                                    <div class="flex items-center gap-3">
                                                        <span class="material-symbols-outlined text-blue-400">image</span>
                                                        <div>
                                                            <label class="text-sm font-bold text-white cursor-pointer"
                                                                for="toggle-img-<?php echo $s['id']; ?>">
                                                                Esta sección tiene imagen
                                                            </label>
                                                            <p class="text-xs text-slate-500 mt-0.5">Activa si necesitas una
                                                                imagen principal</p>
                                                        </div>
                                                    </div>
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" id="toggle-img-<?php echo $s['id']; ?>"
                                                            name="sections[<?php echo $s['id']; ?>][show_image]" value="1" <?php echo (!isset($s['show_image']) || $s['show_image']) ? 'checked' : ''; ?> class="sr-only peer"
                                                            onchange="toggleImageField(<?php echo $s['id']; ?>)">
                                                        <div
                                                            class="w-11 h-6 bg-slate-700 peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                                        </div>
                                                    </label>
                                                </div>

                                                <?php if ($s['section_key'] === 'quality_policy'): ?>
                                                    <div class="mt-6 p-4 bg-white/5 rounded-xl border border-white/5">
                                                        <label
                                                            class="block text-[10px] font-bold text-amber-500 uppercase mb-3 tracking-widest font-mono">Configuración
                                                            del Botón</label>
                                                        <div class="flex gap-4">
                                                            <div class="w-1/3">
                                                                <label class="text-xs text-slate-400 mb-1 block">Texto del
                                                                    Botón</label>
                                                                <input type="text" name="sections[<?php echo $s['id']; ?>][image3]"
                                                                    value="<?php echo htmlspecialchars($s['image3'] ?? ''); ?>"
                                                                    placeholder="Ej: Conocer Más +"
                                                                    class="w-full p-3 bg-slate-900/50 border border-white/10 rounded-lg text-white text-sm outline-none focus:ring-2 focus:ring-blue-500">
                                                            </div>
                                                            <div class="flex-grow">
                                                                <label class="text-xs text-slate-400 mb-1 block">Enlace /
                                                                    Archivo</label>
                                                                <div class="flex gap-2">
                                                                    <input type="text"
                                                                        name="sections[<?php echo $s['id']; ?>][image2]"
                                                                        value="<?php echo htmlspecialchars($s['image2'] ?? ''); ?>"
                                                                        id="input-url-<?php echo $s['id']; ?>"
                                                                        placeholder="https://... o selecciona un archivo"
                                                                        class="w-full p-3 bg-slate-900/50 border border-white/10 rounded-lg text-white text-sm outline-none focus:ring-2 focus:ring-blue-500">
                                                                    <button type="button"
                                                                        onclick="openMediaPicker('input-url-<?php echo $s['id']; ?>', 'dummy-preview')"
                                                                        class="px-3 bg-white/10 hover:bg-white/20 rounded-lg text-white transition-colors"
                                                                        title="Seleccionar archivo">
                                                                        <span
                                                                            class="material-symbols-outlined text-lg">attach_file</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <div id="image-field-<?php echo $s['id']; ?>"
                                                    class="<?php echo (isset($s['show_image']) && !$s['show_image']) ? 'hidden' : ''; ?>">
                                                    <label
                                                        class="block text-[10px] font-bold text-slate-500 uppercase mb-3 tracking-widest">Imagen
                                                        Principal / Derecha</label>
                                                    <div class="space-y-4">
                                                        <div
                                                            class="aspect-video rounded-xl bg-slate-800 border-2 border-dashed border-white/5 overflow-hidden relative group/img">
                                                            <img src="<?php echo (!empty($s['image']) && strpos($s['image'], 'http') !== 0) ? '../' . $s['image'] : $s['image']; ?>"
                                                                class="w-full h-full object-cover <?php echo empty($s['image']) ? 'hidden' : ''; ?>"
                                                                id="preview-sec-<?php echo $s['id']; ?>">
                                                        </div>
                                                        <input type="hidden" name="sections[<?php echo $s['id']; ?>][image]"
                                                            value="<?php echo $s['image']; ?>"
                                                            id="input-sec-<?php echo $s['id']; ?>">
                                                        <button type="button"
                                                            onclick="openMediaPicker('input-sec-<?php echo $s['id']; ?>', 'preview-sec-<?php echo $s['id']; ?>')"
                                                            class="w-full px-5 py-4 bg-white/5 hover:bg-white/10 text-white rounded-xl text-xs font-bold border border-white/10 transition-all flex items-center justify-center gap-2">
                                                            <span class="material-symbols-outlined text-sm">image</span>
                                                            Cambiar
                                                            Imagen
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if ($s['section_key'] === 'about'): ?>
                                            <div class="pt-8 border-t border-white/5">
                                                <label
                                                    class="block text-[10px] font-bold text-blue-500 uppercase mb-6 tracking-widest font-mono">Galería
                                                    de 3 Imágenes (Debajo del texto)</label>
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                                    <?php for ($i = 2; $i <= 4; $i++):
                                                        $img_key = 'image' . $i;
                                                        $val = $s[$img_key] ?? '';
                                                        ?>
                                                        <div class="space-y-4">
                                                            <div
                                                                class="aspect-square rounded-xl bg-slate-800 border-2 border-dashed border-white/5 overflow-hidden relative group/img">
                                                                <img src="<?php echo (!empty($val) && strpos($val, 'http') !== 0) ? '../' . $val : $val; ?>"
                                                                    class="w-full h-full object-cover <?php echo empty($val) ? 'hidden' : ''; ?>"
                                                                    id="preview-sec-<?php echo $s['id']; ?>-<?php echo $i; ?>">
                                                            </div>
                                                            <input type="hidden"
                                                                name="sections[<?php echo $s['id']; ?>][<?php echo $img_key; ?>]"
                                                                value="<?php echo $val; ?>"
                                                                id="input-sec-<?php echo $s['id']; ?>-<?php echo $i; ?>">
                                                            <button type="button"
                                                                onclick="openMediaPicker('input-sec-<?php echo $s['id']; ?>-<?php echo $i; ?>', 'preview-sec-<?php echo $s['id']; ?>-<?php echo $i; ?>')"
                                                                class="w-full px-4 py-3 bg-white/5 hover:bg-white/10 text-white rounded-xl text-[10px] font-bold border border-white/10 transition-all flex items-center justify-center gap-2">
                                                                <span class="material-symbols-outlined text-xs">image</span> Foto
                                                                <?php echo ($i - 1); ?>
                                                            </button>
                                                        </div>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($s['section_key'] === 'history'): ?>
                                            <div class="pt-8 border-t border-white/5">
                                                <label
                                                    class="block text-[10px] font-bold text-blue-500 uppercase mb-6 tracking-widest font-mono">Imagen
                                                    Secundaria (Superpuesta)</label>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <?php $i = 2;
                                                    $img_key = 'image' . $i;
                                                    $val = $s[$img_key] ?? '';
                                                    ?>
                                                    <div class="space-y-4">
                                                        <div
                                                            class="aspect-video rounded-xl bg-slate-800 border-2 border-dashed border-white/5 overflow-hidden relative group/img">
                                                            <img src="<?php echo (!empty($val) && strpos($val, 'http') !== 0) ? '../' . $val : $val; ?>"
                                                                class="w-full h-full object-cover <?php echo empty($val) ? 'hidden' : ''; ?>"
                                                                id="preview-sec-<?php echo $s['id']; ?>-<?php echo $i; ?>">
                                                        </div>
                                                        <input type="hidden"
                                                            name="sections[<?php echo $s['id']; ?>][<?php echo $img_key; ?>]"
                                                            value="<?php echo $val; ?>"
                                                            id="input-sec-<?php echo $s['id']; ?>-<?php echo $i; ?>">
                                                        <button type="button"
                                                            onclick="openMediaPicker('input-sec-<?php echo $s['id']; ?>-<?php echo $i; ?>', 'preview-sec-<?php echo $s['id']; ?>-<?php echo $i; ?>')"
                                                            class="w-full px-4 py-3 bg-white/5 hover:bg-white/10 text-white rounded-xl text-[10px] font-bold border border-white/10 transition-all flex items-center justify-center gap-2">
                                                            <span class="material-symbols-outlined text-xs">image</span> Cambiar
                                                            Imagen Secundaria
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Multi-items Manager (Logos, Specialties, etc) -->
                                        <?php
                                        $has_items = in_array($s['section_key'], ['brands', 'categories', 'portfolio', 'gallery', 'why_choose_us', 'stats', 'testimonials', 'introduction', 'advisory', 'badges', 'faq']);
                                        if ($has_items):
                                            $items = get_section_items($s['id']);
                                            ?>
                                            <div class="pt-8 border-t border-white/5">
                                                <div class="flex items-center justify-between mb-6">
                                                    <label
                                                        class="block text-[10px] font-bold text-amber-500 uppercase tracking-widest font-mono">
                                                        <?php echo ($s['section_key'] === 'brands') ? 'Logos / Imágenes' : 'Elementos de esta sección (Listado dinámico)'; ?>
                                                    </label>
                                                    <button type="button"
                                                        onclick="addItem(<?php echo $s['id']; ?>, '<?php echo $s['section_key']; ?>')"
                                                        class="text-xs font-bold text-blue-400 hover:text-blue-300 flex items-center gap-1 transition-all">
                                                        <span class="material-symbols-outlined text-sm">add_circle</span>
                                                        <?php echo ($s['section_key'] === 'brands') ? 'Añadir Logo' : 'Añadir Elemento'; ?>
                                                    </button>
                                                </div>

                                                <?php if (in_array($s['section_key'], ['brands', 'badges'])): ?>
                                                    <!-- Simplified grid for logos (brands section) -->
                                                    <div id="items-grid-<?php echo $s['id']; ?>"
                                                        class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                                        <?php foreach ($items as $item): ?>
                                                            <div
                                                                class="bg-white/5 p-4 rounded-2xl border border-white/5 group/item hover:border-blue-500/30 transition-all">
                                                                <div class="space-y-3">
                                                                    <div
                                                                        class="aspect-square rounded-lg bg-slate-800 overflow-hidden border border-white/10 relative group">
                                                                        <img src="<?php echo (!empty($item['image']) && strpos($item['image'], 'http') !== 0) ? '../' . $item['image'] : $item['image']; ?>"
                                                                            class="w-full h-full object-cover <?php echo empty($item['image']) ? 'hidden' : ''; ?>"
                                                                            id="preview-item-<?php echo $item['id']; ?>">
                                                                        <?php if (empty($item['image'])): ?>
                                                                            <div class="absolute inset-0 flex items-center justify-center">
                                                                                <span
                                                                                    class="material-symbols-outlined text-4xl text-slate-600">image</span>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>

                                                                    <input type="hidden" name="items[<?php echo $item['id']; ?>][title]"
                                                                        value="Logo <?php echo $item['id']; ?>">
                                                                    <input type="hidden" name="items[<?php echo $item['id']; ?>][content]"
                                                                        value="">
                                                                    <input type="hidden" name="items[<?php echo $item['id']; ?>][image]"
                                                                        value="<?php echo $item['image']; ?>"
                                                                        id="input-item-<?php echo $item['id']; ?>">

                                                                    <div class="flex gap-2">
                                                                        <button type="button"
                                                                            onclick="openMediaPicker('input-item-<?php echo $item['id']; ?>', 'preview-item-<?php echo $item['id']; ?>')"
                                                                            class="flex-1 py-2 bg-blue-600/20 hover:bg-blue-600/40 rounded-lg flex items-center justify-center gap-2 text-[10px] font-bold border border-blue-500/20 transition-all text-blue-300">
                                                                            <span class="material-symbols-outlined text-xs">image</span>
                                                                            Cambiar
                                                                        </button>
                                                                        <button type="button"
                                                                            onclick="deleteItem(<?php echo $item['id']; ?>, '¿Eliminar este logo?', this)"
                                                                            class="px-3 py-2 bg-red-500/10 hover:bg-red-500/30 rounded-lg flex items-center justify-center border border-red-500/20 transition-all group">
                                                                            <span
                                                                                class="material-symbols-outlined text-sm text-red-400">delete</span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>

                                                        <?php if (empty($items)): ?>
                                                            <div class="col-span-full text-center py-12 text-slate-500">
                                                                <span
                                                                    class="material-symbols-outlined text-5xl opacity-30 mb-3 block">image</span>
                                                                <p class="text-sm">No hay logos agregados aún</p>
                                                                <p class="text-xs mt-1">Haz clic en "Añadir Logo" para comenzar</p>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php elseif ($s['section_key'] === 'why_choose_us'): ?>
                                                    <!-- Simplified list for Why Choose Us (Checklist) -->
                                                    <div id="items-grid-<?php echo $s['id']; ?>" class="space-y-3">
                                                        <?php foreach ($items as $item): ?>
                                                            <div
                                                                class="bg-white/5 p-4 rounded-xl border border-white/5 flex gap-4 items-center group/item hover:border-blue-500/30 transition-all">
                                                                <div
                                                                    class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-500/10 flex items-center justify-center">
                                                                    <span
                                                                        class="material-symbols-outlined text-blue-400 text-sm">check</span>
                                                                </div>
                                                                <div class="flex-grow">
                                                                    <input type="text" name="items[<?php echo $item['id']; ?>][title]"
                                                                        value="<?php echo htmlspecialchars($item['title']); ?>"
                                                                        class="w-full bg-transparent text-white font-medium outline-none border-b border-transparent focus:border-blue-500/50 py-1"
                                                                        placeholder="Escribe el beneficio aquí...">
                                                                    <input type="hidden" name="items[<?php echo $item['id']; ?>][content]"
                                                                        value="">
                                                                    <input type="hidden" name="items[<?php echo $item['id']; ?>][image]"
                                                                        value="">
                                                                </div>
                                                                <button type="button"
                                                                    onclick="deleteItem(<?php echo $item['id']; ?>, '¿Eliminar este elemento?', this)"
                                                                    class="text-red-500/30 hover:text-red-500 transition-all p-2">
                                                                    <span class="material-symbols-outlined text-sm">delete</span>
                                                                </button>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        <?php if (empty($items)): ?>
                                                            <div
                                                                class="text-center py-6 text-slate-500 border-2 border-dashed border-white/5 rounded-xl">
                                                                <p class="text-xs">No hay elementos configurados</p>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php elseif ($s['section_key'] === 'testimonials'): ?>
                                                <!-- Custom grid for Testimonials -->
                                                <div id="items-grid-<?php echo $s['id']; ?>"
                                                    class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <?php foreach ($items as $item): ?>
                                                        <div class="bg-white/5 p-5 rounded-2xl border border-white/5 group/item">
                                                            <div class="flex justify-between items-start mb-4">
                                                                <div class="flex items-center gap-3">
                                                                    <div
                                                                        class="w-12 h-12 rounded-full bg-slate-800 overflow-hidden border border-white/10">
                                                                        <img src="<?php echo 'https://ui-avatars.com/api/?name=' . urlencode($item['title'] ?? 'User') . '&background=1e3a8a&color=fff&size=64'; ?>"
                                                                            class="w-full h-full object-cover">
                                                                    </div>
                                                                    <div class="flex-grow">
                                                                        <input type="text" name="items[<?php echo $item['id']; ?>][title]"
                                                                            value="<?php echo htmlspecialchars($item['title']); ?>"
                                                                            class="w-full bg-transparent text-white font-bold outline-none border-b border-transparent focus:border-blue-500 text-sm"
                                                                            placeholder="Nombre del Cliente...">
                                                                    </div>
                                                                    <select name="items[<?php echo $item['id']; ?>][rating]"
                                                                        class="bg-white/5 border border-white/5 rounded text-xs text-yellow-500 font-bold outline-none p-1 w-14 text-center">
                                                                        <?php for ($r = 5; $r >= 1; $r--): ?>
                                                                            <option value="<?php echo $r; ?>" <?php echo ($item['rating'] ?? 5) == $r ? 'selected' : ''; ?>><?php echo $r; ?>★</option>
                                                                        <?php endfor; ?>
                                                                    </select>
                                                                </div>
                                                                <button type="button"
                                                                    onclick="deleteItem(<?php echo $item['id']; ?>, '¿Eliminar este testimonio?', this)"
                                                                    class="text-red-500/30 group-hover/item:text-red-500 transition-all">
                                                                    <span class="material-symbols-outlined text-sm">delete</span>
                                                                </button>
                                                            </div>
                                                            <div class="space-y-3">
                                                                <textarea name="items[<?php echo $item['id']; ?>][content]"
                                                                    class="w-full bg-white/5 border border-white/5 p-2 rounded text-xs outline-none focus:border-blue-500/50 text-slate-300 h-20"
                                                                    placeholder="Testimonio..."><?php echo htmlspecialchars($item['content'] ?? ''); ?></textarea>

                                                                <div class="flex gap-2">
                                                                    <div class="flex-grow">
                                                                        <input type="text"
                                                                            name="items[<?php echo $item['id']; ?>][extra_link]"
                                                                            value="<?php echo htmlspecialchars($item['extra_link'] ?? ''); ?>"
                                                                            class="w-full bg-white/5 border border-white/5 p-2 rounded text-xs outline-none focus:border-blue-500/50"
                                                                            placeholder="Empresa / Cargo (opcional)...">
                                                                    </div>
                                                                    <input type="hidden" name="items[<?php echo $item['id']; ?>][image]"
                                                                        value="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <!-- Standard grid for other sections -->
                                                <div id="items-grid-<?php echo $s['id']; ?>"
                                                    class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <?php foreach ($items as $item): ?>
                                                        <div class="bg-white/5 p-5 rounded-2xl border border-white/5 group/item">
                                                            <div class="flex justify-between items-start mb-4">
                                                                <div class="flex items-center gap-3">
                                                                    <div
                                                                        class="w-12 h-12 rounded-lg bg-slate-800 overflow-hidden border border-white/10">
                                                                        <img src="<?php echo (!empty($item['image']) && strpos($item['image'], 'http') !== 0) ? '../' . $item['image'] : $item['image']; ?>"
                                                                            class="w-full h-full object-cover <?php echo empty($item['image']) ? 'hidden' : ''; ?>"
                                                                            id="preview-item-<?php echo $item['id']; ?>">
                                                                    </div>
                                                                    <input type="text" name="items[<?php echo $item['id']; ?>][title]"
                                                                        value="<?php echo htmlspecialchars($item['title']); ?>"
                                                                        class="bg-transparent text-white font-bold outline-none border-b border-transparent focus:border-blue-500 text-sm"
                                                                        placeholder="Título...">
                                                                </div>
                                                                <a href="?section=pages&edit=<?php echo $page_id; ?>&delete_item=<?php echo $item['id']; ?>&page_id=<?php echo $page_id; ?>"
                                                                    onclick="return confirm('¿Eliminar este elemento?')"
                                                                    class="text-red-500/30 group-hover/item:text-red-500 transition-all">
                                                                    <span class="material-symbols-outlined text-sm">delete</span>
                                                                </a>
                                                            </div>
                                                            <div class="space-y-3">
                                                                <input type="text" name="items[<?php echo $item['id']; ?>][content]"
                                                                    value="<?php echo htmlspecialchars($item['content'] ?? ''); ?>"
                                                                    class="w-full bg-white/5 border border-white/5 p-2 rounded text-xs outline-none focus:border-blue-500/50"
                                                                    placeholder="<?php echo ($s['section_key'] === 'gallery') ? 'Categoría (ej: Industria)...' : 'Contenido / Valor...'; ?>">
                                                                <div class="flex gap-2">
                                                                    <input type="hidden" name="items[<?php echo $item['id']; ?>][image]"
                                                                        value="<?php echo $item['image']; ?>"
                                                                        id="input-item-<?php echo $item['id']; ?>">
                                                                    <button type="button"
                                                                        onclick="openMediaPicker('input-item-<?php echo $item['id']; ?>', 'preview-item-<?php echo $item['id']; ?>')"
                                                                        class="flex-grow py-2 bg-white/5 hover:bg-white/10 rounded flex items-center justify-center gap-2 text-[10px] font-bold border border-white/5 transition-all text-slate-400">
                                                                        <span class="material-symbols-outlined text-xs">image</span>
                                                                        Imagen/Icono
                                                                    </button>
                                                                    <?php if (in_array($s['section_key'], ['portfolio', 'categories', 'advisory'])): ?>
                                                                        <input type="text" name="items[<?php echo $item['id']; ?>][extra_link]"
                                                                            value="<?php echo htmlspecialchars($item['extra_link'] ?? ''); ?>"
                                                                            class="flex-grow bg-white/5 border border-white/5 p-2 rounded text-[10px] outline-none focus:border-blue-500/50"
                                                                            placeholder="Link (opcional)...">
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($items)): ?>
                                                <div class="mt-4 flex justify-end">
                                                    <button type="button" onclick="updateItems<?php echo $s['id']; ?>()"
                                                        id="save-btn-<?php echo $s['id']; ?>"
                                                        class="bg-amber-500/10 hover:bg-amber-500/20 text-amber-500 border border-amber-500/20 px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">Actualizar
                                                        Elementos de esta Sección</button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <!-- Generate update functions for each section with items -->
                        <script>
                            <?php foreach ($sections as $s):
                                if (in_array($s['section_key'], ['brands', 'categories', 'portfolio', 'gallery', 'testimonials', 'introduction', 'advisory', 'badges', 'faq'])): ?>
                                    function updateItems<?php echo $s['id']; ?>() {
                                        const button = document.getElementById('save-btn-<?php echo $s['id']; ?>');
                                        const originalText = button.textContent;
                                        button.disabled = true;
                                        button.textContent = 'Guardando...';

                                        const formData = new FormData();
                                        formData.append('action', 'update_items');

                                        // Collect all items data
                                        const items = {};
                                        document.querySelectorAll('#items-grid-<?php echo $s['id']; ?> input, #items-grid-<?php echo $s['id']; ?> textarea, #items-grid-<?php echo $s['id']; ?> select').forEach(input => {
                                            const matches = input.name.match(/items\[(\d+)\]\[(\w+)\]/);
                                            if (matches) {
                                                const id = matches[1];
                                                const field = matches[2];
                                                if (!items[id]) items[id] = {};
                                                items[id][field] = input.value;
                                            }
                                        });

                                        // Append items to formData
                                        for (const [id, data] of Object.entries(items)) {
                                            formData.append(`items[${id}][title]`, data.title || '');
                                            formData.append(`items[${id}][content]`, data.content || '');
                                            formData.append(`items[${id}][image]`, data.image || '');
                                            formData.append(`items[${id}][extra_link]`, data.extra_link || '');
                                            formData.append(`items[${id}][rating]`, data.rating || 5);
                                        }

                                        fetch(getEndpoint('ajax_items.php'), {
                                            method: 'POST',
                                            body: formData
                                        })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.success) {
                                                    showNotification(data.message, 'success');
                                                    button.textContent = originalText;
                                                    button.disabled = false;
                                                } else {
                                                    alert('Error: ' + data.message);
                                                    button.textContent = originalText;
                                                    button.disabled = false;
                                                }
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                                alert('Error al actualizar');
                                                button.textContent = originalText;
                                                button.disabled = false;
                                            });
                                    }
                                <?php endif;
                            endforeach; ?>
                        </script>
                    </div>
                </div>

                <div
                    class="mt-10 flex justify-between items-center bg-[#0b1120] sticky bottom-0 py-6 border-t border-white/5 z-40">
                    <?php if ($p['template'] == 'home'): ?>
                        <a href="?section=pages&edit=<?php echo $page_id; ?>&add_banner=<?php echo $page_id; ?>"
                            class="px-6 py-4 border-2 border-dashed border-white/10 text-slate-400 hover:border-blue-500 hover:text-blue-500 rounded-2xl font-bold transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined">add_circle</span> Añadir Slide
                        </a>
                    <?php else:
                        echo "<div></div>";
                    endif; ?>

                    <button name="save_page"
                        class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-2xl shadow-blue-500/20 flex items-center gap-3">
                        <span class="material-symbols-outlined">save</span> Guardar Cambios
                    </button>
                </div>
            </form>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const el = document.getElementById('sortable-banners');
                    if (el) {
                        Sortable.create(el, {
                            animation: 150, handle: '.cursor-move', ghostClass: 'ghost-slide',
                            onEnd: () => {
                                const ids = Array.from(el.children).map(card => card.dataset.id).filter(id => id);
                                document.getElementById('banner_order').value = ids.join(',');
                            }
                        });
                    }
                });
            </script>

        <?php elseif ($section == 'media'): ?>
            <header class="flex justify-between items-center mb-10">
                <div>
                    <h2 class="text-2xl font-bold text-white uppercase tracking-tight">Biblioteca de Medios</h2>
                    <p class="text-slate-500 text-sm">Sube y gestiona tus imágenes.</p>
                </div>
                <!-- Upload Dropzone -->
                <div class="flex items-center gap-4">
                    <button type="button" onclick="document.getElementById('upload-input').click()"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold cursor-pointer shadow-lg shadow-blue-500/20 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">upload</span> Subir Archivos
                    </button>
                    <input type="file" multiple id="upload-input" accept="image/*"
                        class="absolute w-0 h-0 opacity-0 pointer-events-none">
                </div>
            </header>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6" id="media-grid">
                <?php $media = get_all_media();
                foreach ($media as $m): ?>
                    <div class="group relative aspect-square bg-[#0f172a] rounded-2xl border border-white/5 overflow-hidden hover:border-blue-500/40 transition-all"
                        data-id="<?php echo $m['id']; ?>">
                        <img src="../<?php echo $m['filename']; ?>"
                            class="w-full h-full object-cover transition-transform group-hover:scale-110">
                        <div
                            class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                            <button onclick="deleteMedia(<?php echo $m['id']; ?>)"
                                class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const uploadInput = document.getElementById('upload-input');
                    if (uploadInput) {
                        console.log('Media upload listener attached');
                        uploadInput.addEventListener('change', async (e) => {
                            const files = e.target.files;
                            if (files.length === 0) return;

                            console.log('Files selected:', files.length);

                            // Create progress container
                            const progressContainer = document.createElement('div');
                            progressContainer.className = 'fixed bottom-8 right-8 bg-slate-900 border border-white/10 rounded-lg p-4 shadow-2xl z-50 min-w-[300px]';
                            progressContainer.innerHTML = `
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-white text-sm font-bold">Subiendo archivos...</span>
                                    <span class="text-blue-400 text-xs" id="upload-progress">0/${files.length}</span>
                                </div>
                                <div class="w-full bg-slate-700 rounded-full h-2">
                                    <div id="upload-bar" class="bg-blue-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                            `;
                            document.body.appendChild(progressContainer);

                            let successCount = 0;
                            let failCount = 0;

                            for (let i = 0; i < files.length; i++) {
                                const file = files[i];
                                const formData = new FormData();
                                formData.append('file', file);

                                try {
                                    const res = await fetch(getEndpoint('media_actions.php'), { method: 'POST', body: formData });
                                    if (!res.ok) {
                                        throw new Error(`HTTP Error: ${res.status}`);
                                    }
                                    const text = await res.text();
                                    let data;
                                    try {
                                        data = JSON.parse(text);
                                    } catch (e) {
                                        console.error('Non-JSON response:', text);
                                        throw new Error('El servidor no devolvió una respuesta válida (JSON).');
                                    }

                                    if (data.success) {
                                        successCount++;
                                        // Add new image to grid dynamically
                                        addMediaToGrid(data.media_id, data.filename);
                                    } else {
                                        failCount++;
                                        console.error('Error al subir:', file.name, data.error);
                                    }
                                } catch (err) {
                                    failCount++;
                                    console.error('Fetch error:', err, 'File:', file.name);
                                }

                                // Update progress
                                const progress = ((i + 1) / files.length) * 100;
                                document.getElementById('upload-bar').style.width = progress + '%';
                                document.getElementById('upload-progress').textContent = `${i + 1}/${files.length}`;
                            }

                            // Show completion message
                            setTimeout(() => {
                                progressContainer.remove();
                                if (failCount > 0) {
                                    alert(`Subida completada: ${successCount} exitosos, ${failCount} fallidos`);
                                } else {
                                    showUploadNotification(`${successCount} archivo(s) subido(s) exitosamente`);
                                }
                            }, 500);

                            // Clear input
                            e.target.value = '';
                        });
                    }
                });

                function addMediaToGrid(mediaId, filename) {
                    const grid = document.getElementById('media-grid');
                    const newItem = document.createElement('div');
                    newItem.className = 'group relative aspect-square bg-[#0f172a] rounded-2xl border border-white/5 overflow-hidden hover:border-blue-500/40 transition-all';
                    newItem.setAttribute('data-id', mediaId);
                    newItem.style.opacity = '0';
                    newItem.style.transform = 'scale(0.8)';

                    newItem.innerHTML = `
                        <img src="../${filename}" class="w-full h-full object-cover transition-transform group-hover:scale-110">
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                            <button onclick="deleteMedia(${mediaId})" class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    `;

                    grid.appendChild(newItem);

                    // Animate in
                    setTimeout(() => {
                        newItem.style.transition = 'opacity 0.3s, transform 0.3s';
                        newItem.style.opacity = '1';
                        newItem.style.transform = 'scale(1)';
                    }, 10);
                }

                function showUploadNotification(message) {
                    const notification = document.createElement('div');
                    notification.className = 'fixed bottom-8 right-8 px-6 py-4 bg-green-500/90 text-white rounded-lg shadow-2xl z-50 transform transition-all duration-300';
                    notification.textContent = message;
                    document.body.appendChild(notification);

                    setTimeout(() => {
                        notification.style.opacity = '0';
                        notification.style.transform = 'translateY(20px)';
                        setTimeout(() => notification.remove(), 300);
                    }, 3000);
                }

                async function deleteMedia(id) {
                    if (!confirm('¿Eliminar esta imagen permanentemente?')) return;
                    try {
                        const formData = new FormData();
                        formData.append('delete_media_id', id);
                        const res = await fetch(getEndpoint('media_actions.php'), { method: 'POST', body: formData });
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        const data = await res.json();
                        if (data.success) {
                            document.querySelector(`[data-id="${id}"]`).remove();
                        } else {
                            alert('Error al eliminar: ' + (data.error || 'Desconocido'));
                        }
                    } catch (err) {
                        alert('Error al eliminar: ' + err.message);
                        console.error('Delete error:', err);
                    }
                }
            </script>

        <?php elseif ($section == 'settings'): ?>
            <header class="mb-10">
                <h2 class="text-2xl font-bold text-white uppercase tracking-tight">Ajustes Generales</h2>
                <p class="text-slate-500 text-sm">Configura los elementos globales del sitio web.</p>
            </header>

            <form method="POST">
                <div class="bg-[#0f172a] p-8 rounded-3xl border border-white/5 space-y-8">
                    <?php $settings = get_all_settings();
                    foreach ($settings as $s): ?>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                            <div>
                                <label class="block text-sm font-bold text-white mb-1"><?php echo $s['label']; ?></label>
                                <p class="text-xs text-slate-500">Clave: <code
                                        class="bg-white/5 px-1 rounded"><?php echo $s['key']; ?></code></p>
                            </div>
                            <div class="md:col-span-2">
                                <?php if ($s['type'] === 'image'): ?>
                                    <div class="flex items-center gap-6">
                                        <div
                                            class="w-24 h-24 bg-slate-800 rounded-xl border border-white/10 overflow-hidden flex-shrink-0 relative group/img">
                                            <img src="<?php echo (!empty($s['value']) && strpos($s['value'], 'http') !== 0) ? '../' . $s['value'] : $s['value']; ?>"
                                                id="preview-<?php echo $s['key']; ?>"
                                                class="w-full h-full object-contain p-2 <?php echo !$s['value'] ? 'hidden' : ''; ?>">
                                            <div
                                                class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                                <span class="material-symbols-outlined text-white text-sm">photo_library</span>
                                            </div>
                                        </div>
                                        <div class="flex-grow flex items-center gap-3">
                                            <input type="hidden" name="settings[<?php echo $s['key']; ?>]"
                                                id="input-<?php echo $s['key']; ?>" value="<?php echo $s['value']; ?>">
                                            <button type="button"
                                                onclick="openMediaPicker('input-<?php echo $s['key']; ?>', 'preview-<?php echo $s['key']; ?>')"
                                                class="px-5 py-3 bg-white/5 hover:bg-white/10 text-white rounded-xl text-xs font-bold border border-white/10 transition-all flex items-center gap-2">
                                                <span class="material-symbols-outlined text-sm">photo_library</span> Seleccionar
                                                Imagen
                                            </button>
                                            <button type="button"
                                                onclick="clearImage('input-<?php echo $s['key']; ?>', 'preview-<?php echo $s['key']; ?>')"
                                                class="px-5 py-3 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-xl text-xs font-bold border border-red-500/10 transition-all flex items-center gap-2 <?php echo !$s['value'] ? 'hidden' : ''; ?>"
                                                id="remove-btn-input-<?php echo $s['key']; ?>">
                                                <span class="material-symbols-outlined text-sm">delete</span> Quitar
                                            </button>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <input type="text" name="settings[<?php echo $s['key']; ?>]"
                                        value="<?php echo htmlspecialchars($s['value']); ?>"
                                        class="w-full p-4 bg-white/5 border border-white/10 rounded-xl text-white outline-none focus:ring-2 focus:ring-blue-500">
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (end($settings) !== $s): ?>
                            <div class="border-t border-white/5"></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <div class="flex justify-end mt-10">
                    <button name="save_settings"
                        class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-2xl shadow-blue-500/20 flex items-center gap-3">
                        <span class="material-symbols-outlined">save</span> Guardar Ajustes
                    </button>
                </div>
            </form>

        <?php elseif ($section == 'nav'): ?>
            <header class="flex justify-between items-center mb-10">
                <h2 class="text-2xl font-bold text-white uppercase tracking-tight">Menú de Navegación</h2>
            </header>

            <form method="POST">
                <?php $menus = $db->query("SELECT * FROM menus ORDER BY menu_order ASC")->fetchAll(); ?>
                <input type="hidden" name="menu_order_ids" id="menu_order_ids"
                    value="<?php echo implode(',', array_column($menus, 'id')); ?>">
                <div class="bg-[#0f172a] rounded-3xl border border-white/5 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-white/5 border-b border-white/10 text-xs font-bold text-slate-500 uppercase">
                            <tr>
                                <th class="p-6 w-12"></th>
                                <th class="p-6">Etiqueta</th>
                                <th class="p-6">URL del enlace</th>
                                <th class="p-6 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-menu" class="divide-y divide-white/5">
                            <?php foreach ($menus as $m): ?>
                                <tr class="group" data-id="<?php echo $m['id']; ?>">
                                    <td class="p-6 text-center cursor-move"><span
                                            class="material-symbols-outlined text-slate-600">drag_indicator</span></td>
                                    <td class="p-6"><input type="text" name="menu[<?php echo $m['id']; ?>][label]"
                                            value="<?php echo $m['label']; ?>"
                                            class="bg-transparent text-white font-bold outline-none w-full border-b border-transparent focus:border-blue-500">
                                    </td>
                                    <td class="p-6"><input type="text" name="menu[<?php echo $m['id']; ?>][url]"
                                            value="<?php echo $m['url']; ?>"
                                            class="bg-transparent text-slate-400 outline-none w-full border-b border-transparent focus:border-blue-500">
                                    </td>
                                    <td class="p-6 text-center">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="menu[<?php echo $m['id']; ?>][is_active]" <?php echo $m['is_active'] ? 'checked' : ''; ?> class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-slate-700 rounded-full peer peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                                            </div>
                                        </label>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end mt-10">
                    <button name="save_menu"
                        class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-bold hover:bg-blue-700 shadow-2xl">Guardar
                        Navegación</button>
                </div>
            </form>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Sortable.create(document.getElementById('sortable-menu'), {
                        animation: 150, handle: '.cursor-move',
                        onEnd: () => {
                            const ids = Array.from(document.querySelectorAll('#sortable-menu tr')).map(tr => tr.dataset.id);
                            document.getElementById('menu_order_ids').value = ids.join(',');
                        }
                    });
                });
            </script>
        <?php endif; ?>
    </main>

    <!-- Media Picker Modal -->
    <div id="media-picker-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-[#0b1120]/90 backdrop-blur-sm" onclick="closeMediaPicker()"></div>
        <div
            class="bg-[#0f172a] w-full max-w-5xl h-[80vh] rounded-3xl border border-white/10 shadow-2xl relative z-10 flex flex-col overflow-hidden">
            <header class="p-8 border-b border-white/10 flex justify-between items-center">
                <div class="flex items-center gap-6">
                    <h3 class="text-xl font-bold text-white">Seleccionar Imagen</h3>
                    <button type="button" onclick="document.getElementById('picker-upload-input').click()"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">upload</span> Subir Nueva
                    </button>
                    <input type="file" multiple id="picker-upload-input" accept="image/*" class="hidden">
                </div>
                <button onclick="closeMediaPicker()" class="text-slate-500 hover:text-white"><span
                        class="material-symbols-outlined">close</span></button>
            </header>
            <div class="flex-grow overflow-y-auto p-8 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4"
                id="picker-grid">
                <!-- Media items will be loaded here -->
            </div>
            <footer class="p-8 border-t border-white/10 flex justify-end">
                <button onclick="closeMediaPicker()"
                    class="px-8 py-3 bg-white/5 text-white rounded-xl font-bold">Cancelar</button>
            </footer>
        </div>
    </div>

    <script>
        // Unified tool to handle the correct endpoint path regardless of trailing slashes in URL
        // Unified tool to handle the correct endpoint path regardless of trailing slashes in URL
        const getEndpoint = (file) => {
            const path = window.location.pathname;
            // If URL is .../admin (no trailing slash), relative path needs 'admin/' prefix
            if (path.endsWith('/admin')) return 'admin/' + file;
            return file;
        };

        let currentTargetInput = null;
        let currentTargetPreview = null;

        async function openMediaPicker(inputId, previewId) {
            currentTargetInput = document.getElementById(inputId);
            currentTargetPreview = document.getElementById(previewId);
            const grid = document.getElementById('picker-grid');
            grid.innerHTML = '<div class="col-span-full py-20 text-center text-slate-500">Cargando biblioteca...</div>';
            document.getElementById('media-picker-modal').classList.remove('hidden');

            try {
                const res = await fetch(getEndpoint('media_actions.php') + '?fetch_media=1');
                const text = await res.text();
                let media;
                try {
                    media = JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON response from media_actions:', text);
                    grid.innerHTML = '<div class="col-span-full py-20 text-center text-red-400">Error: El servidor no devolvió JSON.</div>';
                    return;
                }

                grid.innerHTML = '';
                if (media.length === 0) {
                    grid.innerHTML = '<div class="col-span-full py-20 text-center text-slate-500">No hay imágenes en la biblioteca.</div>';
                }
                media.forEach(m => {
                    const div = document.createElement('div');
                    div.className = 'aspect-square bg-[#1e293b] rounded-xl overflow-hidden cursor-pointer hover:ring-4 hover:ring-blue-500 transition-all';
                    if (m.filename.toLowerCase().endsWith('.pdf')) {
                        div.innerHTML = `<div class="w-full h-full flex flex-col items-center justify-center bg-slate-800 text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2 text-red-400">picture_as_pdf</span>
                            <span class="text-[10px] font-mono px-2 text-center break-all text-slate-500 leading-tight">${m.filename.split('/').pop()}</span>
                        </div>`;
                    } else {
                        div.innerHTML = `<img src="../${m.filename}" class="w-full h-full object-cover">`;
                    }
                    div.onclick = () => selectMedia(m.filename);
                    grid.appendChild(div);
                });
            } catch (err) {
                console.error('Fetch error:', err);
                grid.innerHTML = '<div class="col-span-full py-20 text-center text-red-400">Error de conexión al cargar biblioteca.</div>';
            }
        }

        // Handle upload from inside the picker
        document.getElementById('picker-upload-input').addEventListener('change', async (e) => {
            const files = e.target.files;
            if (files.length === 0) return;

            // Show progress for multiple files
            let progressContainer = null;
            if (files.length > 1) {
                progressContainer = document.createElement('div');
                progressContainer.className = 'fixed bottom-8 right-8 bg-slate-900 border border-white/10 rounded-lg p-4 shadow-2xl z-[110] min-w-[300px]';
                progressContainer.innerHTML = `
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-white text-sm font-bold">Subiendo archivos...</span>
                        <span class="text-blue-400 text-xs" id="picker-upload-progress">0/${files.length}</span>
                    </div>
                    <div class="w-full bg-slate-700 rounded-full h-2">
                        <div id="picker-upload-bar" class="bg-blue-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                `;
                document.body.appendChild(progressContainer);
            }

            let successCount = 0;
            let failCount = 0;
            let lastUploadedFilename = null;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const formData = new FormData();
                formData.append('file', file);

                try {
                    const res = await fetch(getEndpoint('media_actions.php'), { method: 'POST', body: formData });
                    const data = await res.json();

                    if (data.success) {
                        successCount++;
                        lastUploadedFilename = data.filename;
                        // Add to picker grid dynamically
                        addMediaToPickerGrid(data.media_id, data.filename);
                    } else {
                        failCount++;
                        console.error('Error al subir:', file.name, data.error);
                    }
                } catch (err) {
                    failCount++;
                    console.error('Fetch error:', err, 'File:', file.name);
                }

                // Update progress
                if (progressContainer) {
                    const progress = ((i + 1) / files.length) * 100;
                    document.getElementById('picker-upload-bar').style.width = progress + '%';
                    document.getElementById('picker-upload-progress').textContent = `${i + 1}/${files.length}`;
                }
            }

            // Show completion message
            if (progressContainer) {
                setTimeout(() => {
                    progressContainer.remove();
                    if (failCount > 0) {
                        alert(`Subida completada: ${successCount} exitosos, ${failCount} fallidos`);
                    }
                }, 500);
            }

            // If single file or want to auto-select the last one uploaded
            if (files.length === 1 && lastUploadedFilename) {
                selectMedia(lastUploadedFilename);
            }

            // Clear input
            e.target.value = '';
        });

        function addMediaToPickerGrid(mediaId, filename) {
            const grid = document.getElementById('picker-grid');
            if (!grid) return;

            const newItem = document.createElement('div');
            newItem.className = 'group relative aspect-square bg-[#0f172a] rounded-2xl border border-white/5 overflow-hidden hover:border-blue-500/40 transition-all cursor-pointer';
            newItem.setAttribute('data-id', mediaId);
            newItem.style.opacity = '0';
            newItem.style.transform = 'scale(0.8)';
            newItem.onclick = () => selectMedia(filename);

            if (filename.toLowerCase().endsWith('.pdf')) {
                newItem.innerHTML = `
                    <div class="w-full h-full flex flex-col items-center justify-center bg-slate-800 text-slate-400 group-hover:bg-slate-700 transition-colors">
                        <span class="material-symbols-outlined text-4xl mb-2 text-red-400">picture_as_pdf</span>
                        <span class="text-[10px] font-mono px-2 text-center break-all text-slate-500 leading-tight group-hover:text-slate-300">${filename.split('/').pop()}</span>
                    </div>
                `;
            } else {
                newItem.innerHTML = `
                    <img src="../${filename}" class="w-full h-full object-cover transition-transform group-hover:scale-110">
                `;
            }

            grid.appendChild(newItem);

            // Animate in
            setTimeout(() => {
                newItem.style.transition = 'opacity 0.3s, transform 0.3s';
                newItem.style.opacity = '1';
                newItem.style.transform = 'scale(1)';
            }, 10);
        }

        function selectMedia(filename) {
            if (currentTargetInput) {
                currentTargetInput.value = filename;
                const removeBtn = document.getElementById('remove-btn-' + currentTargetInput.id);
                if (removeBtn) removeBtn.classList.remove('hidden');
            }
            if (currentTargetPreview) {
                if (filename.toLowerCase().endsWith('.pdf')) {
                    currentTargetPreview.src = 'https://placehold.co/600x400/1e293b/ef4444/png?text=PDF+SELECTED';
                } else {
                    currentTargetPreview.src = '../' + filename;
                }
                currentTargetPreview.classList.remove('hidden');
            }
            closeMediaPicker();
        }

        function clearImage(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const removeBtn = document.getElementById('remove-btn-' + inputId);

            if (input) input.value = '';
            if (preview) {
                preview.src = '';
                preview.classList.add('hidden');
            }
            if (removeBtn) removeBtn.classList.add('hidden');
        }

        function toggleImageField(sectionId) {
            const checkbox = document.getElementById(`toggle-img-${sectionId}`);
            const imageField = document.getElementById(`image-field-${sectionId}`);

            if (checkbox.checked) {
                imageField.classList.remove('hidden');
            } else {
                imageField.classList.add('hidden');
            }
        }

        // AJAX Functions for Items Management
        function addItem(sectionId, sectionKey) {
            const formData = new FormData();
            formData.append('action', 'add_item');
            formData.append('section_id', sectionId);

            fetch(getEndpoint('ajax_items.php'), {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const itemId = data.item_id;
                        const grid = document.getElementById(`items-grid-${sectionId}`);

                        // Check if empty message exists and remove it
                        const emptyMsg = grid.querySelector('.col-span-full');
                        if (emptyMsg) {
                            emptyMsg.remove();
                        }

                        // Create new item HTML
                        const newItem = document.createElement('div');

                        if (sectionKey === 'brands' || sectionKey === 'badges') {
                            newItem.className = 'bg-white/5 p-4 rounded-2xl border border-white/5 group/item hover:border-blue-500/30 transition-all';
                            newItem.innerHTML = `
                                <div class="space-y-3">
                                    <div class="aspect-square rounded-lg bg-slate-800 overflow-hidden border border-white/10 relative group">
                                        <img src="" class="w-full h-full object-cover hidden" id="preview-item-${itemId}">
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-4xl text-slate-600">image</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="items[${itemId}][title]" value="Logo ${itemId}">
                                    <input type="hidden" name="items[${itemId}][image]" value="" id="input-item-${itemId}">
                                    <div class="flex gap-2">
                                        <button type="button" onclick="openMediaPicker('input-item-${itemId}', 'preview-item-${itemId}')"
                                            class="flex-1 py-2 bg-blue-600/20 hover:bg-blue-600/40 rounded-lg flex items-center justify-center gap-2 text-[10px] font-bold border border-blue-500/20 transition-all text-blue-300">
                                            <span class="material-symbols-outlined text-xs">image</span> Cambiar
                                        </button>
                                        <button type="button" onclick="deleteItem(${itemId}, '¿Eliminar este logo?', this)"
                                            class="px-3 py-2 bg-red-500/10 hover:bg-red-500/30 rounded-lg flex items-center justify-center border border-red-500/20 transition-all">
                                            <span class="material-symbols-outlined text-sm text-red-400">delete</span>
                                        </button>
                                    </div>
                                </div>
                            `;
                        } else if (sectionKey === 'why_choose_us') {
                            newItem.className = 'bg-white/5 p-4 rounded-xl border border-white/5 flex gap-4 items-center group/item hover:border-blue-500/30 transition-all';
                            newItem.innerHTML = `
                                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-500/10 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-blue-400 text-sm">check</span>
                                </div>
                                <div class="flex-grow">
                                    <input type="text" name="items[${itemId}][title]" value="" 
                                        class="w-full bg-transparent text-white font-medium outline-none border-b border-transparent focus:border-blue-500/50 py-1"
                                        placeholder="Escribe el beneficio aquí...">
                                </div>
                                <button type="button" onclick="deleteItem(${itemId}, '¿Eliminar este elemento?', this)"
                                    class="text-red-500/30 hover:text-red-500 transition-all p-2">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            `;
                        } else if (sectionKey === 'testimonials') {
                            newItem.className = 'bg-white/5 p-5 rounded-2xl border border-white/5 group/item animated-in';
                            newItem.innerHTML = `
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-full bg-slate-800 overflow-hidden border border-white/10">
                                            <img src="" class="w-full h-full object-cover hidden" id="preview-item-${itemId}">
                                        </div>
                                        <div class="flex-grow">
                                            <input type="text" name="items[${itemId}][title]" value=""
                                                class="w-full bg-transparent text-white font-bold outline-none border-b border-transparent focus:border-blue-500 text-sm"
                                                placeholder="Nombre del Cliente...">
                                        </div>
                                        <select name="items[${itemId}][rating]" class="bg-white/5 border border-white/5 rounded text-xs text-yellow-500 font-bold outline-none p-1 w-14 text-center">
                                            <option value="5">5★</option>
                                            <option value="4">4★</option>
                                            <option value="3">3★</option>
                                            <option value="2">2★</option>
                                            <option value="1">1★</option>
                                        </select>
                                    </div>
                                    <button type="button" onclick="deleteItem(${itemId}, '¿Eliminar este testimonio?', this)"
                                        class="text-red-500/30 hover:text-red-500 transition-all">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </div>
                                <div class="space-y-3">
                                    <textarea name="items[${itemId}][content]"
                                        class="w-full bg-white/5 border border-white/5 p-2 rounded text-xs outline-none text-slate-300 h-20"
                                        placeholder="Testimonio..."></textarea>
                                    <input type="text" name="items[${itemId}][extra_link]" value=""
                                        class="w-full bg-white/5 border border-white/5 p-2 rounded text-xs outline-none"
                                        placeholder="Empresa / Cargo...">
                                    <input type="hidden" name="items[${itemId}][image]" value="" id="input-item-${itemId}">
                                    <button type="button" onclick="openMediaPicker('input-item-${itemId}', 'preview-item-${itemId}')"
                                        class="w-full py-2 bg-white/5 hover:bg-white/10 rounded flex items-center justify-center gap-2 text-[10px] font-bold border border-white/5 text-slate-400">
                                        <span class="material-symbols-outlined text-xs">image</span> Foto del Cliente
                                    </button>
                                </div>
                            `;
                        } else {
                            newItem.className = 'bg-white/5 p-5 rounded-2xl border border-white/5 group/item animated-in';
                            newItem.innerHTML = `
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-lg bg-slate-800 overflow-hidden border border-white/10">
                                            <img src="" class="w-full h-full object-cover hidden" id="preview-item-${itemId}">
                                        </div>
                                        <input type="text" name="items[${itemId}][title]" value=""
                                            class="bg-transparent text-white font-bold outline-none border-b border-transparent focus:border-blue-500 text-sm"
                                            placeholder="Título...">
                                    </div>
                                    <button type="button" onclick="deleteItem(${itemId}, '¿Eliminar este elemento?', this)"
                                        class="text-red-500/30 hover:text-red-500 transition-all">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </div>
                                <div class="space-y-3">
                                    <input type="text" name="items[${itemId}][content]" value=""
                                        class="w-full bg-white/5 border border-white/5 p-2 rounded text-xs outline-none"
                                        placeholder="${sectionKey === 'gallery' ? 'Categoría (ej: Industria)...' : 'Contenido / Valor...'}">
                                    <input type="hidden" name="items[${itemId}][image]" value="" id="input-item-${itemId}">
                                    <button type="button" onclick="openMediaPicker('input-item-${itemId}', 'preview-item-${itemId}')"
                                        class="w-full py-2 bg-white/5 hover:bg-white/10 rounded flex items-center justify-center gap-2 text-[10px] font-bold border border-white/5 text-slate-400">
                                        <span class="material-symbols-outlined text-xs">image</span> Imagen/Icono
                                    </button>
                                </div>
                            `;
                        }

                        newItem.style.opacity = '0';
                        newItem.style.transform = 'scale(0.9)';
                        grid.appendChild(newItem);

                        // Animate in
                        setTimeout(() => {
                            newItem.style.transition = 'opacity 0.3s, transform 0.3s';
                            newItem.style.opacity = '1';
                            newItem.style.transform = 'scale(1)';
                        }, 10);

                        showNotification('Elemento agregado correctamente', 'success');
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al añadir el elemento');
                });
        }

        function deleteItem(itemId, confirmMessage, button) {
            if (!confirm(confirmMessage)) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'delete_item');
            formData.append('item_id', itemId);

            // Disable button during request
            button.disabled = true;
            button.style.opacity = '0.5';

            fetch(getEndpoint('ajax_items.php'), {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the item card with animation
                        const card = button.closest('.bg-white\\/5');
                        card.style.transition = 'opacity 0.3s, transform 0.3s';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            card.remove();
                            showNotification('Elemento eliminado correctamente', 'success');
                        }, 300);
                    } else {
                        alert('Error: ' + data.message);
                        button.disabled = false;
                        button.style.opacity = '1';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al eliminar el elemento');
                    button.disabled = false;
                    button.style.opacity = '1';
                });
        }

        // Notification system
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed bottom-8 right-8 px-6 py-4 rounded-lg shadow-2xl z-50 transform transition-all duration-300 ${type === 'success' ? 'bg-green-500/90 text-white' : 'bg-red-500/90 text-white'
                }`;
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(20px)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Initialize Section Sorting
        const sectionsContainer = document.getElementById('sections-container');
        if (sectionsContainer) {
            new Sortable(sectionsContainer, {
                handle: '.handle',
                animation: 150,
                ghostClass: 'opacity-2         0',
                onEnd: function () {
                    const order = Array.from(sectionsContainer.children)
                        .filter(el => el.hasAttribute('data-id'))
                        .map(el => el.getAttribute('data-id'));

                    const formData = new FormData();
                    formData.append('action', 'reorder_sections');
                    order.forEach((id, index) => {
                        formData.append(`order[${index}]`, id);
                    });

                    fetch(getEndpoint('ajax_items.php'), {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('Orden guardado', 'success');
                            } else {
                                showNotification('Error al guardar orden', 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            showNotification('Error de conexión', 'error');
                        });
                }
            });
        }

        function closeMediaPicker() {
            document.getElementById('media-picker-modal').classList.add('hidden');
        }
    </script>
</body>

</html>