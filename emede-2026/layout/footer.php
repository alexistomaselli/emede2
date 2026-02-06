<footer class="text-white pt-20 pb-10" style="background-color: #001d4c;">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
            <div class="col-span-1 md:col-span-1">
                <div class="flex items-center gap-2 mb-6">
                    <?php
                    $footer_logo = get_setting('site_logo_footer');
                    if ($footer_logo): ?>
                        <img src="<?php echo $footer_logo; ?>" alt="Gráfica Emede" class="h-20 w-auto">
                    <?php else: ?>
                        <div class="w-8 h-8 bg-primary rounded flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-sm">print</span>
                        </div>
                        <span class="text-xl font-bold">Gráfica Emede</span>
                    <?php endif; ?>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed mb-6">
                    Comprometidos con la excelencia en packaging y servicios gráficos industriales desde hace más de
                    4 décadas.
                </p>
                <div class="flex gap-4">
                    <a class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary transition-colors"
                        href="#">
                        <span class="material-symbols-outlined text-base">social_leaderboard</span>
                    </a>
                    <a class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary transition-colors"
                        href="#">
                        <span class="material-symbols-outlined text-base">photo_camera</span>
                    </a>
                    <a class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary transition-colors"
                        href="#">
                        <span class="material-symbols-outlined text-base">mail</span>
                    </a>
                </div>
            </div>
            <div>
                <h5 class="font-bold mb-6">Empresa</h5>
                <ul class="space-y-4 text-slate-400 text-sm">
                    <li><a class="hover:text-white transition-colors" href="trayectoria.php">Sobre Nosotros</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Nuestra Planta</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Maquinaria</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Sustentabilidad</a></li>
                </ul>
            </div>
            <div>
                <h5 class="font-bold mb-6">Servicios</h5>
                <ul class="space-y-4 text-slate-400 text-sm">
                    <li><a class="hover:text-white transition-colors" href="packaging.php">Estuches</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Prospectos</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Exhibidores</a></li>
                    <li><a class="hover:text-white transition-colors" href="#">Cajas Microcorrugado</a></li>
                </ul>
            </div>
            <div>
                <h5 class="font-bold mb-6">Contacto</h5>
                <ul class="space-y-4 text-slate-400 text-sm">
                    <li class="flex gap-3">
                        <span class="material-symbols-outlined text-white text-sm">location_on</span>
                        Madame Curie 1141, Quilmes, Buenos Aires
                    </li>
                    <li class="flex gap-3">
                        <span class="material-symbols-outlined text-white text-sm">phone</span>
                        +54 (11) 4250-1234
                    </li>
                    <li class="flex gap-3">
                        <span class="material-symbols-outlined text-white text-sm">email</span>
                        emede@emede.com.ar
                    </li>
                </ul>
            </div>
        </div>
        <div
            class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-slate-500 text-xs">
            <p>© 2026 Gráfica Emede S.A. Todos los derechos reservados.</p>
            <div class="flex gap-6">
                <a class="hover:text-white" href="#">Política de Privacidad</a>
                <a class="hover:text-white" href="#">Términos y Condiciones</a>
            </div>
        </div>
    </div>
</footer>
</body>

</html>