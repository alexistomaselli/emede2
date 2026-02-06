<?php
$page_slug = 'galeria';
include __DIR__ . '/layout/header.php';

// Fetch sections
$sections = get_page_sections($page_data['id']);
$hero = null;
$gallery = null;

foreach ($sections as $s) {
    switch ($s['section_key']) {
        case 'hero':
            $hero = $s;
            break;
        case 'gallery':
            $gallery = $s;
            break;
    }
}
?>

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
                <span class="text-white border-b border-white pb-0.5 uppercase">GALERÍA</span>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Gallery Section -->
<?php if ($gallery): ?>
    <?php $gallery_items = get_section_items($gallery['id']); ?>
    <section class="py-24 bg-white dark:bg-background-dark">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($gallery_items as $item): ?>
                    <div class="relative group overflow-hidden rounded-xl cursor-pointer"
                        onclick="openLightbox('<?php echo htmlspecialchars($item['image'] ?? ''); ?>')">
                        <img alt="<?php echo htmlspecialchars($item['title'] ?? ''); ?>"
                            class="w-full h-[500px] object-cover transition-transform duration-700 group-hover:scale-110"
                            src="<?php echo htmlspecialchars($item['image'] ?? ''); ?>" />

                        <!-- Overlay on Hover -->
                        <div
                            class="absolute bottom-6 left-0 flex items-end opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 ease-out">
                            <div class="bg-white dark:bg-slate-800 px-8 py-5 min-w-[200px] shadow-2xl">
                                <h4 class="text-[#001d4c] dark:text-white font-bold text-xl uppercase tracking-tight">
                                    <?php echo htmlspecialchars($item['title'] ?? ''); ?>
                                </h4>
                                <?php if (!empty($item['content'])): ?>
                                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">
                                        <?php echo htmlspecialchars($item['content']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <button
                                class="bg-blue-600 w-16 h-16 flex items-center justify-center text-white transition-all hover:bg-black">
                                <span class="material-symbols-outlined text-2xl">add</span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Simple Lightbox Modal -->
    <div id="lightbox"
        class="fixed inset-0 z-[9999] bg-black/95 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <button onclick="closeLightbox()" class="absolute top-8 right-8 text-white hover:text-blue-500 transition-colors">
            <span class="material-symbols-outlined text-4xl">close</span>
        </button>
        <div class="max-w-5xl w-full max-h-[90vh] flex items-center justify-center">
            <img id="lightbox-img" src=""
                class="max-w-full max-h-full object-contain rounded-lg shadow-2xl animate-in fade-in zoom-in duration-300">
        </div>
    </div>

    <script>
        function openLightbox(imgUrl) {
            const lightbox = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            img.src = imgUrl;
            lightbox.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }

        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.add('hidden');
            document.body.style.overflow = ''; // Restore scrolling
        }

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeLightbox();
        });

        // Close when clicking outside the image
        document.getElementById('lightbox').addEventListener('click', (e) => {
            if (e.target.id === 'lightbox') closeLightbox();
        });
    </script>
<?php endif; ?>

<?php include __DIR__ . '/layout/footer.php'; ?>