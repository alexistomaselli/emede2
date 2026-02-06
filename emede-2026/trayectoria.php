<?php
$page_slug = 'trayectoria';
include __DIR__ . '/layout/header.php';
$sections = get_page_sections($page_data['id']);
$history_section = null;
foreach ($sections as $s) {
    if ($s['section_key'] === 'historia') {
        $history_section = $s;
        break;
    }
}
?>

<section class="relative h-[450px] flex items-center hero-section">
    <div class="max-w-7xl mx-auto px-6 w-full flex flex-col md:flex-row justify-between items-center">
        <h1 class="text-5xl md:text-7xl font-bold text-white tracking-tight">
            <?php echo $page_hero['title']; ?>
        </h1>
        <div
            class="bg-black/50 backdrop-blur-md px-10 py-6 rounded text-white text-sm font-bold tracking-widest flex items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">home</span>
                <a class="hover:text-accent transition-colors" href="index.php">HOME</a>
            </div>
            <span class="text-white">●</span>
            <span class="text-white border-b border-white pb-0.5 uppercase">
                <?php echo str_replace('HOME ○ ', '', $page_hero['subtitle']); ?>
            </span>
        </div>
    </div>
</section>


<?php
// Sort sections by order
usort($sections, function ($a, $b) {
    return $a['item_order'] <=> $b['item_order'];
});

foreach ($sections as $s):
    switch ($s['section_key']):
        case 'history':
            $items = get_section_items($s['id']); // Certifications/Badges
            ?>
            <section class="py-24 relative overflow-hidden bg-white dark:bg-background-dark">
                <!-- Decorative SVG Background -->
                <div class="absolute left-0 top-0 w-1/4 h-full pointer-events-none opacity-20">
                    <svg class="h-full w-full" viewBox="0 0 400 800" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 0Q100 200 0 400T0 800" fill="none" stroke="#3b82f6" stroke-width="2"></path>
                        <path d="M20 0Q120 200 20 400T20 800" fill="none" opacity="0.5" stroke="#3b82f6" stroke-width="2"></path>
                        <path d="M40 0Q140 200 40 400T40 800" fill="none" opacity="0.3" stroke="#3b82f6" stroke-width="2"></path>
                    </svg>
                </div>

                <div class="max-w-7xl mx-auto px-6 relative">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                        <div class="relative">
                            <div class="relative grid grid-cols-12 grid-rows-12 h-[600px]">
                                <!-- Image 1 (Factory) -->
                                <div class="col-start-1 col-end-10 row-start-1 row-end-9 z-10">
                                    <img alt="Planta de producción" class="w-full h-full object-cover rounded-2xl shadow-xl"
                                        src="<?php echo !empty($s['image']) ? $s['image'] : 'https://placehold.co/600x400'; ?>" />
                                </div>
                                <!-- Image 2 (Team) -->
                                <div
                                    class="col-start-5 col-end-12 row-start-6 row-end-13 z-20 border-8 border-white dark:border-background-dark rounded-3xl overflow-hidden shadow-2xl">
                                    <img alt="Equipo de trabajo" class="w-full h-full object-cover"
                                        src="<?php echo !empty($s['image2']) ? $s['image2'] : 'https://placehold.co/600x400'; ?>" />
                                </div>
                                <!-- Badge Overlay removed -->
                            </div>
                        </div>
                        <div class="space-y-8">
                            <div>
                                <?php if (!empty($s['subtitle'])): ?>
                                    <div
                                        class="bg-blue-50 dark:bg-blue-900/30 text-accent font-bold px-3 py-1 rounded inline-block text-xs uppercase tracking-widest mb-4">
                                        <?php echo htmlspecialchars($s['subtitle']); ?>
                                    </div>
                                <?php endif; ?>
                                <h2 class="text-4xl lg:text-5xl font-bold text-slate-900 dark:text-white leading-[1.1] mb-6">
                                    <?php echo htmlspecialchars($s['title']); ?>
                                </h2>
                                <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                                    <?php echo nl2br(htmlspecialchars($s['content'])); ?>
                                </p>
                            </div>

                            <!-- Badges / Certifications -->
                            <div class="space-y-8">
                                <?php foreach ($items as $item): ?>
                                    <div class="flex gap-6">
                                        <div
                                            class="flex-shrink-0 w-16 h-16 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                                            <span
                                                class="material-symbols-outlined text-accent text-4xl"><?php echo htmlspecialchars($item['image'] ?: 'verified'); ?></span>
                                        </div>
                                        <div>
                                            <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
                                                <?php echo htmlspecialchars($item['title']); ?>
                                            </h4>
                                            <p class="text-slate-600 dark:text-slate-400">
                                                <?php echo htmlspecialchars($item['content']); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Signature block removed -->
                        </div>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'quality_policy':
            ?>
            <section class="py-24 bg-white dark:bg-background-dark">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                        <div class="space-y-8">
                            <div class="flex flex-col gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-0.5 bg-accent"></div>
                                    <span
                                        class="text-accent font-bold tracking-widest text-sm uppercase"><?php echo htmlspecialchars($s['subtitle'] ?? ''); ?></span>
                                </div>
                                <h2 class="text-4xl lg:text-[50px] font-bold text-slate-900 dark:text-white leading-[1.1]">
                                    <?php echo htmlspecialchars($s['title']); ?>
                                </h2>
                            </div>
                            <p class="text-lg text-slate-700 dark:text-slate-300">
                                <?php echo nl2br(htmlspecialchars($s['content'])); ?>
                            </p>
                            <div class="pt-4">
                                <a class="inline-flex bg-accent hover:bg-blue-600 text-white px-10 py-4 rounded-lg font-bold transition-all shadow-lg text-base"
                                    href="<?php echo !empty($s['image2']) ? $s['image2'] : '#'; ?>"
                                    target="<?php echo (!empty($s['image2']) && strpos($s['image2'], '.pdf') !== false) ? '_blank' : '_self'; ?>">
                                    <?php echo !empty($s['image3']) ? htmlspecialchars($s['image3']) : 'Conocer Más +'; ?>
                                </a>
                            </div>
                        </div>
                        <div class="relative">
                            <img alt="Control de impresión profesional"
                                class="w-full rounded-[2.5rem] shadow-2xl object-cover aspect-[4/3]"
                                src="<?php echo !empty($s['image']) ? $s['image'] : 'https://placehold.co/800x600'; ?>" />
                            <div
                                class="absolute -bottom-6 -right-6 w-32 h-32 opacity-10 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] bg-[length:20px_20px] rounded-full -z-10">
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'stats':
            $items = get_section_items($s['id']);
            ?>
            <section class="py-24 bg-slate-50 dark:bg-slate-900/30">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <?php foreach ($items as $item): ?>
                            <div
                                class="bg-white dark:bg-slate-800 p-8 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm transition-all hover:shadow-md flex flex-col items-center text-center">
                                <div
                                    class="w-16 h-16 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center mb-6">
                                    <span
                                        class="material-symbols-outlined text-accent text-3xl"><?php echo htmlspecialchars($item['image'] ?: 'info'); ?></span>
                                </div>
                                <h3 class="text-4xl font-bold text-slate-900 dark:text-white mb-2">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </h3>
                                <p class="text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider text-sm">
                                    <?php echo htmlspecialchars($item['content']); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php
            break;
    endswitch;
endforeach;
?>

<?php include __DIR__ . '/layout/footer.php'; ?>