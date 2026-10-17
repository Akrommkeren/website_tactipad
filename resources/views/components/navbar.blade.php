<!-- CLEAN UNIFIED NAVIGATION HEADER -->
<header class="navbar-glass sticky top-0 z-30 transition-all duration-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 sm:h-20 flex items-center justify-between relative">
        
        <!-- Brand Title (Kiri) -->
        <a href="{{ route('tactipad.home') }}" class="flex items-center hover:opacity-90 transition z-10">
            <span class="text-xl sm:text-2xl font-black text-white tracking-tight">TACTIPAD</span>
        </a>

        <!-- Navigation Menu Links (Ditengah / Center Navbar di Desktop) -->
        <nav class="hidden md:flex items-center gap-7 md:gap-8 md:absolute md:left-1/2 md:-translate-x-1/2">
            <a href="{{ route('tactipad.home') }}" class="text-sm text-white no-underline transition hover:text-white {{ request()->routeIs('tactipad.home') ? 'font-extrabold' : 'font-semibold opacity-90 hover:opacity-100' }}">
                Braille
            </a>
            <a href="{{ route('tactipad.latihan') }}" class="text-sm text-white no-underline transition hover:text-white {{ request()->routeIs('tactipad.latihan') ? 'font-extrabold' : 'font-semibold opacity-90 hover:opacity-100' }}">
                Latihan Soal
            </a>
            <a href="{{ route('tactipad.kamus') }}" class="text-sm text-white no-underline transition hover:text-white {{ request()->routeIs('tactipad.kamus') ? 'font-extrabold' : 'font-semibold opacity-90 hover:opacity-100' }}">
                Tentang
            </a>
            <a href="{{ route('tactipad.pengaturan') }}" class="text-sm text-white no-underline transition hover:text-white {{ request()->routeIs('tactipad.pengaturan') ? 'font-extrabold' : 'font-semibold opacity-90 hover:opacity-100' }}">
                Pengaturan
            </a>
        </nav>

        <!-- Kanan: Tombol Tema & Hamburger Menu Mobile -->
        <div class="flex items-center justify-end gap-1.5 sm:gap-2 z-10">
            <!-- Tombol Tema (Matahari / Bulan) -->
            <button type="button" id="themeToggleBtn" aria-label="Ganti Tema Gelap / Terang" title="Ganti Tema Gelap / Terang" class="p-2 sm:p-2.5 text-white hover:opacity-80 active:scale-95 flex items-center justify-center transition cursor-pointer focus:outline-none touch-manipulation">
                <!-- Icon Matahari (Mode Normal / Terang) -->
                <svg id="themeIconSun" class="w-5 h-5 sm:w-6 sm:h-6 text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <!-- Icon Bulan (Mode Gelap / Kontras) -->
                <svg id="themeIconMoon" class="w-5 h-5 sm:w-6 sm:h-6 text-white hidden transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

            <!-- Tombol Hamburger Mobile Menu -->
            <button type="button" id="mobileMenuBtn" aria-label="Buka Menu Navigasi" aria-expanded="false" class="md:hidden p-2 text-white hover:opacity-80 active:scale-95 flex items-center justify-center transition cursor-pointer focus:outline-none touch-manipulation">
                <svg id="mobileMenuIconOpen" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg id="mobileMenuIconClose" class="w-6 h-6 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

    </div>

    <!-- Mobile Navigation Drawer -->
    <div id="mobileNavMenu" class="hidden md:hidden px-6 pb-4 pt-2 border-t border-white/20 space-y-1 bg-blue-600/95 dark:bg-[#172554]/95 backdrop-blur-md shadow-xl transition-all">
        <a href="{{ route('tactipad.home') }}" class="block py-2.5 text-sm text-white no-underline transition hover:text-white {{ request()->routeIs('tactipad.home') ? 'font-extrabold' : 'font-semibold text-white/85 hover:text-white hover:font-bold' }}">
            Braille
        </a>
        <a href="{{ route('tactipad.latihan') }}" class="block py-2.5 text-sm text-white no-underline transition hover:text-white {{ request()->routeIs('tactipad.latihan') ? 'font-extrabold' : 'font-semibold text-white/85 hover:text-white hover:font-bold' }}">
            Latihan Soal
        </a>
        <a href="{{ route('tactipad.kamus') }}" class="block py-2.5 text-sm text-white no-underline transition hover:text-white {{ request()->routeIs('tactipad.kamus') ? 'font-extrabold' : 'font-semibold text-white/85 hover:text-white hover:font-bold' }}">
            Tentang
        </a>
        <a href="{{ route('tactipad.pengaturan') }}" class="block py-2.5 text-sm text-white no-underline transition hover:text-white {{ request()->routeIs('tactipad.pengaturan') ? 'font-extrabold' : 'font-semibold text-white/85 hover:text-white hover:font-bold' }}">
            Pengaturan
        </a>
    </div>
</header>
