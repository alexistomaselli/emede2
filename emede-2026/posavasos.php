<?php
$page_slug = 'posavasos';
include __DIR__ . '/layout/header.php';

// Fetch sections
$sections = get_page_sections($page_data['id']);
$hero = null;
$intro = null;
$advisory = null;
$badges = null;
$faq = null;

foreach ($sections as $s) {
    switch ($s['section_key']) {
        case 'hero':
            $hero = $s;
            break;
        case 'introduction':
            $intro = $s;
            break;
        case 'advisory':
            $advisory = $s;
            break;
        case 'badges':
            $badges = $s;
            break;
        case 'faq':
            $faq = $s;
            break;
    }
}
?>

<style>
    .watermark-text {
        position: absolute;
        top: 0;
        left: 0;
        color: rgba(255, 255, 255, 0.05);
        font-size: 6rem;
        font-weight: 900;
        text-transform: uppercase;
        pointer-events: none;
        user-select: none;
        transform: translateY(-1rem);
    }
</style>

<!-- Hero Section -->
<?php if ($hero): ?>
    <section class="relative h-[450px] flex items-center hero-section"
        style="background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('<?php echo !empty($hero['image']) ? $hero['image'] : 'https://placehold.co/1920x600'; ?>'); background-size: cover; background-position: center;">
        <div class="max-w-7xl mx-auto px-6 w-full flex flex-col md:flex-row justify-between items-center">
            <h1 class="text-5xl md:text-7xl font-bold text-white tracking-tight">
                <?php echo htmlspecialchars($hero['title'] ?? ''); ?>
            </h1>
            <div
                class="bg-black/50 backdrop-blur-md px-10 py-6 rounded text-white text-sm font-bold tracking-widest flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">home</span>
                    <a class="hover:text-accent transition-colors" href="index.php">HOME</a>
                </div>
                <span class="text-white">○</span>
                <span class="text-white border-b border-white pb-0.5">POSAVASOS</span>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Introduction Section -->
<?php if ($intro): ?>
    <?php $intro_items = get_section_items($intro['id']); ?>
    <section class="py-24 bg-white dark:bg-background-dark">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24 items-start mb-16">
                <div>
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-0.5 bg-accent"></div>
                        <span class="text-accent font-bold tracking-wider text-sm uppercase">
                            <?php echo htmlspecialchars($intro['subtitle'] ?? '10.000.000 posavasos fabricados por año'); ?>
                        </span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white leading-tight">
                        <?php echo htmlspecialchars($intro['title'] ?? ''); ?>
                    </h2>
                </div>
                <div class="pt-2 lg:pt-14">
                    <p class="text-lg text-slate-500 dark:text-slate-400 leading-relaxed font-normal">
                        <?php echo nl2br(htmlspecialchars($intro['content'] ?? '')); ?>
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($intro_items as $item): ?>
                    <div class="group relative aspect-square rounded-2xl overflow-hidden shadow-md">
                        <img alt="<?php echo htmlspecialchars($item['title'] ?? ''); ?>"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            src="<?php echo htmlspecialchars($item['image'] ?? ''); ?>" />
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent flex flex-col justify-end p-8">
                            <h3 class="text-white text-2xl font-bold mb-3 leading-tight">
                                <?php echo htmlspecialchars($item['title'] ?? ''); ?>
                            </h3>
                            <?php if (!empty($item['content'])): ?>
                                <p class="text-slate-200 text-sm font-medium leading-relaxed opacity-90">
                                    <?php echo htmlspecialchars($item['content'] ?? ''); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Advisory Section -->
<?php if ($advisory): ?>
    <?php $advisory_items = get_section_items($advisory['id']); ?>
    <section class="flex flex-col lg:flex-row w-full min-h-[600px]">
        <div class="lg:w-1/2 w-full h-[400px] lg:h-auto">
            <img alt="Advisory Image" class="w-full h-full object-cover"
                src="<?php echo htmlspecialchars($advisory['image'] ?? ''); ?>" />
        </div>
        <div class="lg:w-1/2 w-full flex flex-col">
            <?php foreach ($advisory_items as $index => $item):
                $bg_class = ($index % 2 == 0) ? 'bg-navy-deep' : 'bg-navy-dark';
                $watermark = !empty($item['extra_link']) && strpos($item['extra_link'], 'Watermark:') === 0 ? trim(substr($item['extra_link'], 10)) : ($item['title'] ?? '');
                ?>
                <div class="flex-1 <?php echo $bg_class; ?> p-12 lg:p-20 relative overflow-hidden flex flex-col justify-center">
                    <span class="watermark-text">
                        <?php echo htmlspecialchars($watermark); ?>
                    </span>
                    <div class="relative z-10">
                        <h3 class="text-3xl font-bold text-white mb-6">
                            <?php echo htmlspecialchars($item['title'] ?? ''); ?>
                        </h3>
                        <p class="text-slate-300 text-lg leading-relaxed mb-8 max-w-xl">
                            <?php echo htmlspecialchars($item['content'] ?? ''); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<!-- Badges/Logos Section -->
<?php if ($badges): ?>
    <section class="py-16 bg-slate-50 dark:bg-slate-900/30 border-y border-slate-100 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <?php
            $badges_items = get_section_items($badges['id']);
            $brand_count = count($badges_items);
            if ($brand_count == 0)
                $grid_class = 'grid-cols-2 md:grid-cols-3';
            elseif ($brand_count == 1)
                $grid_class = 'grid-cols-1';
            elseif ($brand_count == 2)
                $grid_class = 'grid-cols-2';
            elseif ($brand_count == 3)
                $grid_class = 'grid-cols-3';
            elseif ($brand_count == 4)
                $grid_class = 'grid-cols-2 md:grid-cols-4';
            elseif ($brand_count <= 6)
                $grid_class = 'grid-cols-2 md:grid-cols-3 lg:grid-cols-6';
            elseif ($brand_count <= 8)
                $grid_class = 'grid-cols-2 md:grid-cols-4 lg:grid-cols-8';
            else
                $grid_class = 'grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6';
            ?>
            <div
                class="grid <?php echo $grid_class; ?> gap-8 md:gap-12 items-center justify-items-center opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
                <?php foreach ($badges_items as $item): ?>
                    <div class="w-full flex items-center justify-center group transition-all duration-300">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?php echo htmlspecialchars($item['image'] ?? ''); ?>"
                                alt="<?php echo htmlspecialchars($item['title'] ?? ''); ?>"
                                title="<?php echo htmlspecialchars($item['title'] ?? ''); ?>"
                                class="max-h-16 w-auto max-w-full object-contain opacity-60 grayscale hover:opacity-100 hover:grayscale-0 transition-all duration-300 group-hover:scale-110">
                        <?php else: ?>
                            <div class="flex flex-col items-center">
                                <div
                                    class="w-24 h-24 border-2 border-slate-300 dark:border-slate-700 rounded-full flex items-center justify-center flex-col gap-1">
                                    <span
                                        class="text-slate-800 dark:text-slate-400 font-bold text-[10px] text-center px-2 uppercase tracking-tighter">
                                        <?php echo htmlspecialchars($item['title'] ?? ''); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- FAQ Section -->
<?php if ($faq): ?>
    <?php $faq_items = get_section_items($faq['id']); ?>
    <section class="py-24 bg-white dark:bg-background-dark">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                <div class="lg:col-span-5 flex flex-col items-start">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-0.5 bg-secondary"></div>
                        <span class="text-secondary font-bold tracking-widest text-sm uppercase">
                            <?php echo htmlspecialchars($faq['subtitle'] ?? ''); ?>
                        </span>
                    </div>
                    <h2 class="text-4xl lg:text-[48px] font-bold text-slate-900 dark:text-white leading-tight mb-8">
                        <?php echo htmlspecialchars($faq['title'] ?? ''); ?>
                    </h2>
                    <a class="bg-secondary hover:bg-blue-600 text-white px-10 py-4 rounded font-bold transition-all shadow-md"
                        target="_blank" href="https://posavasos.com.ar/">
                        Ver Más
                    </a>
                </div>
                <div class="lg:col-span-7 space-y-4">
                    <?php foreach ($faq_items as $item): ?>
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-4">
                            <button class="w-full flex justify-between items-center text-left group"
                                onclick="this.nextElementSibling.classList.toggle('hidden');">
                                <span
                                    class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-secondary transition-colors">
                                    <?php echo htmlspecialchars($item['title'] ?? ''); ?>
                                </span>
                                <span
                                    class="material-symbols-outlined text-slate-400 group-hover:text-secondary transition-colors">expand_more</span>
                            </button>
                            <div class="mt-4 text-slate-500 dark:text-slate-400 leading-relaxed hidden">
                                <?php echo htmlspecialchars($item['content'] ?? ''); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php include __DIR__ . '/layout/footer.php'; ?>