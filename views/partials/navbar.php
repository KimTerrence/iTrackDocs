<nav class="print:hidden border-b border-slate-200 dark:border-zinc-800 bg-white dark:bg-zinc-900/50 p-4 shadow-sm dark:shadow-none backdrop-blur-md sticky top-0 z-50 transition-colors duration-200">
    <div class="w-full mx-auto flex justify-between items-center px-2 sm:px-4 lg:px-8">
        
        <a href="index.php" class="text-xl font-bold text-slate-800 dark:text-white tracking-tight flex items-center">
            iTrack<span class="text-brand-500">Docs</span>
        </a>
        
        <?php if (!isset($_SESSION['user_name'])): ?>
            <div class="hidden md:flex items-center gap-8 absolute left-1/2 -translate-x-1/2">
                <a href="#home" class="text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Home</a>
                <a href="#features" class="text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Features</a>
                <a href="#how-it-works" class="text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-brand-400 transition-colors">How it Works</a>
            </div>
        <?php endif; ?>

        <div class="text-sm font-medium flex items-center gap-3 md:gap-4">
            
            <button id="theme-toggle" class="text-slate-500 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 p-2 rounded-lg transition-colors">
                <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 4.22a1 1 0 011.415 0l.708.708a1 1 0 01-1.414 1.414l-.708-.708a1 1 0 010-1.414zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM14.22 15.78a1 1 0 010 1.415l-.708.708a1 1 0 01-1.414-1.414l.708-.708a1 1 0 011.415 0zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-4.22-4.22a1 1 0 01-1.415 0l-.708-.708a1 1 0 011.414-1.414l.708.708a1 1 0 010 1.415zM2 10a1 1 0 011-1h1a1 1 0 110 2H3a1 1 0 01-1-1zm4.22-4.22a1 1 0 010-1.415l-.708-.708a1 1 0 01-1.414 1.414l.708.708a1 1 0 011.415 0zM10 5a5 5 0 100 10 5 5 0 000-10z"></path></svg>
                <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
            </button>

            <?php if (isset($_SESSION['user_name'])): ?>
                <a href="index.php?action=dashboard" class="text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-brand-600 dark:hover:text-brand-400 transition-colors mr-3 md:mr-6">
                    Dashboard
                </a>
                <div class="h-6 w-px bg-slate-200 dark:bg-zinc-700 hidden sm:block"></div>
                <div class="flex items-center gap-3 ml-2">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name']) ?>&background=2563eb&color=fff&rounded=true" alt="Avatar" class="w-8 h-8 rounded-full ring-2 ring-white dark:ring-zinc-800">
                    <div class="hidden sm:flex flex-col">
                        <span class="text-sm font-semibold text-slate-900 dark:text-white leading-none"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                        <a href="index.php?action=logout" class="text-xs text-slate-500 hover:text-brand-600 transition-colors mt-1">Sign out</a>
                    </div>
                </div>
            <?php else: ?>
                <button onclick="openLoginModal()" class="bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-white px-4 py-2 rounded-lg hover:bg-slate-200 dark:hover:bg-white/10 transition-all font-semibold shadow-sm">
                    Staff Login
                </button>
            <?php endif; ?>

        </div>
    </div>
</nav>