<?php
$page_slug = 'home';
include __DIR__ . '/layout/header.php';

$page_home = get_page_data('home');
$banners = get_banners($page_home['id'], true);
$sections = get_page_sections($page_home['id'], true);
?>

<?php if (!empty($banners)): ?>
    <section class="relative h-[85vh] overflow-hidden bg-slate-900" id="hero-slider">
        <div class="swiper-container h-full">
            <div class="swiper-wrapper">
                <?php foreach ($banners as $slide): ?>
                    <div class="swiper-slide hero-slide">
                        <div class="hero-image" style="background-image: url('<?php echo $slide['image']; ?>')"></div>
                        <div class="hero-overlay"></div>
                        <div class="max-w-7xl mx-auto px-6 relative h-full flex items-center justify-center">
                            <div class="max-w-4xl hero-content text-center">
                                <h1 class="text-4xl md:text-7xl font-bold text-white mb-6 leading-tight">
                                    <?php echo $slide['title']; ?>
                                </h1>
                                <p
                                    class="text-xl md:text-2xl text-slate-300 mb-10 leading-relaxed font-light max-w-2xl mx-auto">
                                    <?php echo $slide['subtitle']; ?>
                                </p>
                                <div class="flex flex-wrap justify-center gap-4">
                                    <a href="<?php echo $slide['button_link']; ?>"
                                        class="border-2 border-white bg-transparent hover:bg-white text-white hover:text-navy-dark px-10 py-4 rounded-full font-bold transition-all duration-300 flex items-center gap-2 text-lg">
                                        <?php echo $slide['button_text']; ?>
                                        <span class="material-symbols-outlined">trending_flat</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Controles sutiles -->
            <div class="swiper-pagination !bottom-24"></div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                new Swiper('.swiper-container', {
                    loop: true,
                    effect: 'fade',
                    fadeEffect: { crossFade: true },
                    speed: 1000,
                    autoplay: { delay: 6000, disableOnInteraction: false },
                    pagination: { el: '.swiper-pagination', clickable: true },
                });
            });
        </script>
    </section>
<?php endif; ?>

<?php
// Render dynamic sections in their database order
foreach ($sections as $s):
    $section_id = $s['id'];
    $items = get_section_items($section_id);

    switch ($s['section_key']):
        case 'about':
            ?>
            <section class="py-24 bg-white dark:bg-background-dark">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-24 items-start mb-16">
                        <div>
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-12 h-0.5 bg-accent"></div>
                                <span class="text-accent font-bold tracking-wider text-sm uppercase">
                                    <?php echo !empty($s['subtitle']) ? $s['subtitle'] : '+45 AÑOS DE EXPERIENCIA'; ?>
                                </span>
                            </div>
                            <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white leading-tight">
                                <?php echo $s['title']; ?>
                            </h2>
                        </div>
                        <div class="pt-2 lg:pt-14">
                            <p class="text-lg text-slate-500 dark:text-slate-400 leading-relaxed font-normal">
                                <?php echo nl2br($s['content']); ?>
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <?php for ($i = 2; $i <= 4; $i++):
                            $img_key = 'image' . $i;
                            $img_src = !empty($s[$img_key]) ? $s[$img_key] : null;
                            if (!$img_src) {
                                if ($i == 2)
                                    $img_src = 'https://lh3.googleusercontent.com/aida-public/AB6AXuD573xwkZPERcEuYAsMKSIiFNJvjw2GYwqCoi-KtCBfKpPNMwyVRmHiImGkzJkrvdeer-0XcIRT8jOku89N-ioRvHudTdOfjmfO5CLjF5-WNjqXFeJhZ7BLpQf2fv47t_CjFZ4WL3g2UtpgkhbUGgmAyK42ENJ62TtGmmD2FkuCrrlKXoG1-E8keIL10xoo9I5CfC0DGHlmiWpibBQD5u4V0tZUgCem4liQnPiFIGkSL-Hc6IHASq1PlwJHn8TRgd58HXdrBakkEw6Y';
                                if ($i == 3)
                                    $img_src = 'https://lh3.googleusercontent.com/aida-public/AB6AXuDlqpRLtu1uDEthVvFTtL0TlUlKWENga2E7Hn6IpNsPyycga3g_ag5kpokjoVdzKi_ka1F8Sk1tMHf_r0_zw3ukZKJliD7ak0BQMCNf84bm4Jdn3iX9rOnzzruvPLV8BALkJ8StjYn5yRfyxrP_UZojNxtyMoYtsZxVU82G4hVsOBr2GtNEllPKc3uDEHehSlT2xX-ljW02pmNEyaEB-RTokVSleOO4tulLceBOojsFHZ-0hbCFHxdtu62Aj7K87Nh40E-ZDeAdQfJP';
                                if ($i == 4)
                                    $img_src = 'https://lh3.googleusercontent.com/aida-public/AB6AXuDQRQYTvEl5BIKzaXCi2IEHf0L26AhCFaA5OrGiZDiO8rYVFSSCA-9RODYK_IhNJckceBjQRnNQl2J9ZiP2l33676niCxFojxqN4WvYyV49W1Zt4wiLBTsjXH4rZN7myn5qwo7NRFsk6P-V_Rc_c8djiq5g0gI2tQC9eqGzis6mCqsApj9aIlFlo6ChB_cmX06AogrW4TNoPCxHdJwhRtx4Hs-DN8b2Y1wEVudNpzbkMMqw8Esvr1MjMmWY9gVOsJaqFBgPVIWoa2gI';
                            }
                            ?>
                            <div
                                class="aspect-square rounded-2xl overflow-hidden bg-slate-100 flex items-center justify-center shadow-sm">
                                <img alt="Soluciones gráficas y packaging personalizado - Gráfica Emede <?php echo $i - 1; ?>"
                                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                                    src="<?php echo $img_src; ?>" />
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'brands':
            $brand_count = count($items);
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
            <section class="py-16 bg-slate-50 dark:bg-slate-900/30 border-y border-slate-100 dark:border-slate-800">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="grid <?php echo $grid_class; ?> gap-8 md:gap-12 items-center justify-items-center">
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $brand):
                                $b_src = $brand['image'];
                                ?>
                                <div class="w-full flex items-center justify-center group transition-all duration-300">
                                    <?php if ($b_src): ?>
                                        <img src="<?php echo $b_src; ?>" alt="<?php echo htmlspecialchars($brand['title']); ?>"
                                            title="<?php echo htmlspecialchars($brand['title']); ?>"
                                            class="max-h-16 w-auto max-w-full object-contain opacity-60 grayscale hover:opacity-100 hover:grayscale-0 transition-all duration-300 group-hover:scale-110">
                                    <?php else: ?>
                                        <div
                                            class="w-24 h-24 border-2 border-slate-300 dark:border-slate-700 rounded-full flex items-center justify-center opacity-60 hover:opacity-100 transition-opacity">
                                            <span
                                                class="text-slate-800 dark:text-slate-400 font-bold text-xs text-center px-2 uppercase tracking-tighter"><?php echo htmlspecialchars($brand['title']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'portfolio':
            ?>
            <section class="py-24 bg-white dark:bg-background-dark">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="text-center mb-16">
                        <span
                            class="text-accent font-bold tracking-[0.2em] text-sm uppercase mb-3 block"><?php echo htmlspecialchars($s['title']); ?></span>
                        <h2 class="text-4xl font-bold text-slate-900 dark:text-white"><?php echo htmlspecialchars($s['content']); ?>
                        </h2>
                        <div class="w-12 h-[3px] bg-accent mx-auto mt-4 rounded-full"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php foreach ($items as $item):
                            $p_img = $item['image'];
                            ?>
                            <div
                                class="group relative aspect-[4/3] rounded-2xl overflow-hidden shadow-xl bg-slate-100 dark:bg-slate-800">
                                <img src="<?php echo $p_img; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-8 transform translate-y-4 group-hover:translate-y-0">
                                    <h4 class="text-white font-bold text-xl mb-2"><?php echo htmlspecialchars($item['title']); ?></h4>
                                    <p class="text-white/80 text-sm leading-relaxed">
                                        <?php echo htmlspecialchars($item['content'] ?? ''); ?>
                                    </p>
                                    <?php if (!empty($item['extra_link'])): ?>
                                        <a href="<?php echo htmlspecialchars($item['extra_link']); ?>"
                                            class="mt-4 text-accent text-xs font-black tracking-widest uppercase flex items-center gap-2">Ver
                                            Proyecto <span class="material-symbols-outlined text-sm">arrow_forward</span></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'stats':
            ?>
            <section class="py-20 bg-primary text-white">
                <div class="max-w-7xl mx-auto px-6 text-center">
                    <div class="flex flex-wrap justify-center items-center gap-12 md:gap-24">
                        <?php
                        $count = 0;
                        foreach ($items as $item):
                            ?>
                            <?php if ($count > 0): ?>
                                <div class="hidden md:block w-px h-24 bg-white/20"></div>
                            <?php endif; ?>
                            <div class="flex flex-col items-center">
                                <div class="w-24 h-24 mb-4 flex items-center justify-center bg-white/10 rounded-full">
                                    <?php if (!empty($item['image'])): ?>
                                        <?php if (strpos($item['image'], 'http') === 0 || strpos($item['image'], 'uploads/') === 0): ?>
                                            <img src="<?php echo $item['image']; ?>" class="w-12 h-12 object-contain" alt="">
                                        <?php else: ?>
                                            <span
                                                class="material-symbols-outlined text-5xl"><?php echo htmlspecialchars($item['image']); ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="material-symbols-outlined text-5xl">verified</span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="text-2xl font-bold mb-1"><?php echo htmlspecialchars($item['title']); ?></h3>
                                <p class="text-blue-200"><?php echo htmlspecialchars($item['content']); ?></p>
                            </div>
                            <?php
                            $count++;
                        endforeach;
                        ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'categories':
            ?>
            <section class="py-24 bg-slate-50 dark:bg-slate-900/10">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="text-center mb-16">
                        <span
                            class="text-accent font-bold tracking-[0.2em] text-sm uppercase mb-3 block"><?php echo htmlspecialchars($s['title']); ?></span>
                        <h2 class="text-4xl font-bold text-slate-900 dark:text-white"><?php echo htmlspecialchars($s['content']); ?>
                        </h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <?php foreach ($items as $item):
                            $item_img = $item['image'];
                            ?>
                            <div
                                class="group relative overflow-hidden rounded-3xl bg-white dark:bg-slate-800 shadow-xl shadow-blue-900/5 hover:shadow-blue-900/10 transition-all duration-500 hover:-translate-y-2">
                                <div class="aspect-[4/5] overflow-hidden">
                                    <img src="<?php echo $item_img; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/20 to-transparent opacity-80">
                                    </div>
                                </div>
                                <div class="absolute bottom-0 left-0 w-full p-8 text-white">
                                    <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($item['title']); ?></h3>
                                    <p
                                        class="text-white/70 text-sm mb-6 line-clamp-2 opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-y-4 group-hover:translate-y-0">
                                        <?php echo htmlspecialchars($item['content'] ?? ''); ?>
                                    </p>
                                    <a href="<?php echo htmlspecialchars($item['extra_link'] ?? '#'); ?>"
                                        class="inline-flex items-center gap-2 text-xs font-bold tracking-widest uppercase hover:text-accent transition-colors">Ver
                                        más <span class="material-symbols-outlined text-sm">arrow_forward</span></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'why_choose_us':
            ?>
            <section class="py-24 bg-white dark:bg-background-dark">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="flex flex-col lg:flex-row items-center gap-16">
                        <div class="lg:w-1/2">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-12 h-0.5 bg-accent"></div>
                                <span
                                    class="text-accent font-bold tracking-widest text-sm uppercase"><?php echo htmlspecialchars($s['title']); ?></span>
                            </div>
                            <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-8">
                                <?php echo htmlspecialchars($s['content']); ?>
                            </h2>
                            <ul class="space-y-4 mb-10">
                                <?php foreach ($items as $item): ?>
                                    <li class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-accent text-sm font-bold"
                                                style="font-variation-settings: 'wght' 700;">check</span>
                                        </div>
                                        <span
                                            class="text-slate-600 dark:text-slate-400 font-medium"><?php echo htmlspecialchars($item['title']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="lg:w-1/2">
                            <div class="relative">
                                <img alt="Personal profesional" class="rounded-2xl shadow-2xl w-full object-cover"
                                    src="<?php echo (!empty($s['image']) && strpos($s['image'], 'http') !== 0) ? $s['image'] : ($s['image'] ?: 'https://lh3.googleusercontent.com/aida-public/AB6AXuD1eDGsTqtby46B9QWUyMlJGxwaTUnXfDNvuewVlt12ab0pS2s58X6LQsJLZniiYYqjUcCCM6cZ1pmL_qfobwaYKFrFArfR4cfaShPiHAGfSGlO2BhpFXdNVQ1K2FN1cwTXf1w5HvWqsJwEBfAOATN0Ybxadkl6MVHK5FsX6AIVSUFGT_ZBfzFAdmtWIkasqcrvHPwW21nf4C_QLbB_hkeu5nePy9M3UR2GdxMcHx3uZScRMcFMveAosWIeyhGM19b1jU70I-7apyJ3'); ?>" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php
            break;

        case 'testimonials':
            ?>
            <section class="py-24 bg-white dark:bg-background-dark overflow-hidden">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="text-center mb-20 flex flex-col items-center">
                        <span
                            class="text-accent font-bold tracking-[0.2em] text-sm uppercase mb-3"><?php echo htmlspecialchars($s['title']); ?></span>
                        <h2 class="text-4xl font-bold text-slate-900 dark:text-white mb-4">
                            <?php echo htmlspecialchars($s['content']); ?>
                        </h2>
                        <div class="w-12 h-[3px] bg-accent rounded-full"></div>
                    </div>
                    <div class="relative overflow-hidden" id="testimonial-carousel">
                        <div class="flex transition-transform duration-500 ease-in-out" id="carousel-inner">
                            <?php foreach ($items as $item): ?>
                                <div class="w-full lg:w-1/2 flex-shrink-0 px-4">
                                    <div class="relative">
                                        <div
                                            class="absolute inset-0 bg-blue-50/50 dark:bg-blue-900/10 translate-x-3 translate-y-3 rounded-xl -z-10">
                                        </div>
                                        <div
                                            class="bg-white dark:bg-slate-800 p-10 md:p-12 rounded-xl testimonial-card-shadow flex flex-col items-center text-center md:items-start md:text-left gap-8 border border-slate-50 dark:border-slate-700 min-h-[400px]">
                                            <div class="relative flex-shrink-0">
                                                <div
                                                    class="w-28 h-28 rounded-full overflow-hidden border-4 border-white dark:border-slate-700 shadow-sm">
                                                    <img alt="<?php echo htmlspecialchars($item['title']); ?>"
                                                        class="w-full h-full object-cover"
                                                        src="<?php echo 'https://ui-avatars.com/api/?name=' . urlencode($item['title'] ?? 'User') . '&background=1e3a8a&color=fff&size=128'; ?>" />
                                                </div>
                                                <div
                                                    class="absolute bottom-0 right-0 w-8 h-8 bg-accent rounded-full flex items-center justify-center text-white shadow-md">
                                                    <span class="material-symbols-outlined !text-[18px]"
                                                        style="font-variation-settings: 'FILL' 1;">format_quote</span>
                                                </div>
                                            </div>
                                            <div class="flex-grow">
                                                <div class="flex gap-1 mb-4">
                                                    <?php
                                                    $rating = isset($item['rating']) ? (int) $item['rating'] : 5;
                                                    for ($i = 1; $i <= 5; $i++): ?>
                                                        <span class="material-symbols-outlined text-yellow-400 !text-[20px]"
                                                            style="font-variation-settings: 'FILL' <?php echo $i <= $rating ? 1 : 0; ?>;">star</span>
                                                    <?php endfor; ?>
                                                </div>
                                                <p class="text-lg italic text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                                                    "<?php echo htmlspecialchars($item['content']); ?>"</p>
                                                <h4 class="font-bold text-slate-900 dark:text-white text-lg">
                                                    <?php echo htmlspecialchars($item['title']); ?>
                                                    <?php if (!empty($item['extra_link'])): ?><span
                                                            class="font-normal text-slate-400 mx-1">/</span> <span
                                                            class="text-sm font-normal text-slate-500"><?php echo htmlspecialchars($item['extra_link']); ?></span><?php endif; ?>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="flex justify-center gap-2 mt-12" id="carousel-dots">
                            <?php foreach ($items as $idx => $item): ?>
                                <button
                                    class="w-2.5 h-2.5 rounded-full <?php echo $idx === 0 ? 'bg-accent' : 'bg-slate-200 dark:bg-slate-700'; ?> transition-all duration-300"
                                    data-index="<?php echo $idx; ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <script>
                    (function () {
                        const inner = document.querySelector('#testimonial-carousel #carousel-inner');
                        const dots = document.querySelectorAll('#testimonial-carousel #carousel-dots button');
                        const cards = inner.children;
                        const total = cards.length;
                        let current = 0;

                        function updateCarousel() {
                            const isMobile = window.innerWidth < 1024;
                            const perView = isMobile ? 1 : 2;
                            const maxIndex = Math.max(0, total - perView);
                            if (current > maxIndex) current = maxIndex;
                            const offset = current * (100 / perView);
                            inner.style.transform = `translateX(-${offset}%)`;

                            // Calculate total pages (sets of items)
                            const totalPages = Math.ceil(total / perView); // e.g. 4 items / 2 perView = 2 pages

                            dots.forEach((dot, idx) => {
                                // Logic: If perView is 2, we want dot 0 to correct to index 0, dot 1 to index 2
                                // Current dots are generated for every item (4 dots).
                                // Ideally we show 1 dot per "page" of items.

                                // Simplified approach: Keep dots behavior as-is (1 dot per start item)
                                // but only show valid starting points? 
                                // Actually with current logic (offset based on current index), 
                                // clicking dot 2 sets current=2, showing items 2 and 3. This is fine.
                                // Just need to ensure we don't show dots that would result in empty space if possible,
                                // or the current logic `idx > maxIndex` handles it.
                                // With 4 items, perView 2: maxIndex = 2.
                                // Dots 0, 1, 2 should be shown?
                                // Item 0 -> Shows 0,1
                                // Item 1 -> Shows 1,2
                                // Item 2 -> Shows 2,3
                                // Item 3 -> Hidden by maxIndex logic? No, maxIndex is 2.
                                // dot 3 (index 3) > maxIndex (2) -> Hidden. Correct.

                                if (idx === current) {
                                    dot.classList.add('bg-accent');
                                    dot.classList.remove('bg-slate-200', 'dark:bg-slate-700');
                                } else {
                                    dot.classList.remove('bg-accent');
                                    dot.classList.add('bg-slate-200', 'dark:bg-slate-700');
                                }
                                dot.style.display = idx > maxIndex ? 'none' : 'block';
                            });
                        }
                        dots.forEach(dot => {
                            dot.addEventListener('click', () => {
                                current = parseInt(dot.getAttribute('data-index'));
                                updateCarousel();
                            });
                        });
                        window.addEventListener('resize', updateCarousel);
                        updateCarousel();
                    })();
                </script>
            </section>
            <?php
            break;

        case 'service_cta':
            ?>
            <section class="py-20 bg-accent relative overflow-hidden group">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10">
                </div>
                <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
                    <h2 class="text-3xl md:text-5xl font-bold text-white mb-8"><?php echo htmlspecialchars($s['title']); ?></h2>
                    <p class="text-white/80 text-lg mb-10 max-w-2xl mx-auto"><?php echo htmlspecialchars($s['content']); ?></p>
                    <a href="#contacto"
                        class="inline-flex items-center gap-3 bg-white text-accent px-10 py-4 rounded-full font-bold hover:bg-slate-100 transition-all shadow-xl hover:scale-105">Conversemos
                        hoy <span class="material-symbols-outlined">chat</span></a>
                </div>
            </section>
            <?php
            break;
    endswitch;
endforeach;
?>

<section class="py-24 bg-[#f1f5f9] dark:bg-slate-900/80" id="contacto">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
            <div>
                <h2 class="text-[40px] font-bold text-slate-900 dark:text-white mb-2">Contacto</h2>
                <p class="text-slate-500 dark:text-slate-400 mb-10 text-lg">Completá el formulario para recibir tu
                    cotización</p>
                <form action="#" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input
                            class="w-full px-5 py-4 bg-white dark:bg-slate-800 border-none rounded-lg focus:ring-2 focus:ring-accent shadow-sm"
                            placeholder="Nombre" type="text" />
                        <input
                            class="w-full px-5 py-4 bg-white dark:bg-slate-800 border-none rounded-lg focus:ring-2 focus:ring-accent shadow-sm"
                            placeholder="Email*" type="email" />
                    </div>
                    <input
                        class="w-full px-5 py-4 bg-white dark:bg-slate-800 border-none rounded-lg focus:ring-2 focus:ring-accent shadow-sm"
                        placeholder="Teléfono" type="tel" />
                    <textarea
                        class="w-full px-5 py-4 bg-white dark:bg-slate-800 border-none rounded-lg focus:ring-2 focus:ring-accent shadow-sm"
                        placeholder="Por favor, describí lo que necesitás. *" rows="6"></textarea>
                    <button
                        class="bg-accent hover:bg-blue-600 text-white px-8 py-4 rounded-lg font-bold transition-all shadow-lg text-sm"
                        type="submit">Recibir cotización</button>
                </form>
            </div>
            <div class="lg:pt-4">
                <div class="flex items-center gap-8 mb-10 border-b border-slate-200 dark:border-slate-700">
                    <button
                        class="pb-4 text-[32px] font-bold text-accent border-b-[3px] border-accent">Dirección</button>
                </div>
                <div class="space-y-10">
                    <div class="flex items-center gap-6">
                        <div
                            class="w-20 h-20 bg-white dark:bg-slate-800 rounded-[30%_70%_70%_30%/30%_30%_70%_70%] shadow-xl shadow-blue-100/20 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-accent text-3xl"
                                style="font-variation-settings: 'FILL' 1;">location_on</span>
                        </div>
                        <div>
                            <h5 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-1">NUESTRA
                                UBICACIÓN</h5>
                            <p class="text-slate-500 dark:text-slate-400 text-lg">
                                <?php echo htmlspecialchars(get_setting('contact_address', 'Madame Curie 1141, Quilmes, Buenos Aires')); ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <div
                            class="w-20 h-20 bg-white dark:bg-slate-800 rounded-[70%_30%_30%_70%/70%_70%_30%_30%] shadow-xl shadow-blue-100/20 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-accent text-3xl"
                                style="font-variation-settings: 'FILL' 1;">mail</span>
                        </div>
                        <div>
                            <h5 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-1">ENVÍENOS
                                UN CORREO</h5>
                            <p class="text-slate-500 dark:text-slate-400 text-lg">
                                <?php echo htmlspecialchars(get_setting('contact_email', 'emede@emede.com.ar')); ?>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <div
                            class="w-20 h-20 bg-white dark:bg-slate-800 rounded-[30%_70%_50%_50%/50%_50%_70%_30%] shadow-xl shadow-blue-100/20 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-accent text-3xl"
                                style="font-variation-settings: 'FILL' 1;">call</span>
                        </div>
                        <div>
                            <h5 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-1">LLÁMENOS
                            </h5>
                            <p class="text-slate-500 dark:text-slate-400 text-lg">
                                <?php echo htmlspecialchars(get_setting('contact_phone', '+54 (11) 4250-1234')); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/layout/footer.php'; ?>