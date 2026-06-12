<footer class="bg-white dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800/60 pt-16 pb-8 mt-12 transition-colors">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
            
            <div>
                <a href="#" class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center mb-4">
                    iTrack<span class="text-brand-600 dark:text-brand-500">Docs</span>
                </a>
                <p class="text-slate-500 dark:text-slate-400 text-sm">
                    Modernizing document routing and tracking for efficient enterprise workflows.
                </p>
            </div>

            <div>
                <h3 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-xs mb-4">Platform</h3>
                <ul class="space-y-3">
                    <li><a href="#tracking" class="text-slate-500 dark:text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 text-sm transition-colors">Track Document</a></li>
                    <li><button onclick="openLoginModal()" class="text-slate-500 dark:text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 text-sm transition-colors">Staff Portal</button></li>
                    <li><a href="#features" class="text-slate-500 dark:text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 text-sm transition-colors">Features</a></li>
                </ul>
            </div>

            <div>
                <h3 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-xs mb-4">Contact</h3>
                <ul class="space-y-3">
                    <li class="text-slate-500 dark:text-slate-400 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        support@itrackdocs.com
                    </li>
                    <li class="text-slate-500 dark:text-slate-400 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        1-800-TRACKING
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-slate-200 dark:border-slate-800/60 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-slate-400 dark:text-slate-500 text-xs">
                &copy; <?= date('Y') ?> iTrackDocs System. All rights reserved.
            </p>
        </div>
    </div>
</footer>