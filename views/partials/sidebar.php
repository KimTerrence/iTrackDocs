<aside class="hidden lg:flex flex-col justify-between w-72 shrink-0 border-r border-slate-200 dark:border-zinc-800 bg-white/50 dark:bg-zinc-900/20 h-[calc(100vh-73px)] sticky top-[73px] p-6 print:hidden">
    
    <div class="overflow-y-auto space-y-8 pr-2 custom-scrollbar">
        
        <div>
            <div class="text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4 px-3">Workspace</div>
            <nav class="space-y-1">
                <a href="index.php?action=dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?= ($action ?? '') === 'dashboard' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50' ?> font-semibold transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    Dashboard
                </a>
                <a href="index.php?action=list" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?= ($action ?? '') === 'list' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50' ?> font-semibold transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Documents
                </a>
                <a href="index.php?action=create" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?= ($action ?? '') === 'create' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50' ?> font-semibold transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Create Document
                </a>             
            </nav>
        </div>

        <div>
            <div class="text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4 px-3">Tracking</div>
            <nav class="space-y-1">
                <?php 
                    $role = strtolower($_SESSION['user_role'] ?? '');
                    if ($role === 'admin' || $role === 'dept_head'): 
                ?>
                <a href="index.php?action=logs" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?= ($action ?? '') === 'logs' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50' ?> font-semibold transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Activity Logs
                </a>
                <?php endif; ?>
            </nav>
        </div>

        <div>
            <div class="text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4 px-3">Management</div>
            <nav class="space-y-1">
                <?php 
                    $role = strtolower($_SESSION['user_role'] ?? '');
                    if ($role === 'admin'): 
                ?>
                    <a href="index.php?action=allDocuments" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?= ($action ?? '') === 'allDocuments' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50' ?> font-semibold transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        All Documents
                    </a>
                <?php endif; ?>
            </nav>
        </div>

       <div>
            <div class="text-xs font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4 px-3">System</div>
            <nav class="space-y-1">
                
                <?php 
                    $role = strtolower($_SESSION['user_role'] ?? '');
                    if ($role === 'admin' || $role === 'head'): 
                ?>
                    <a href="index.php?action=users" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?= ($action ?? '') === 'users' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50' ?> font-semibold transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        Users
                    </a>
                <?php endif; ?>

                <a href="index.php?action=settings" class="flex items-center gap-3 px-3 py-2.5 rounded-xl <?= ($action ?? '') === 'settings' ? 'bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50' ?> font-semibold transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Settings
                </a>
            </nav>
        </div>
    </div>

    <div class="pt-6 border-t border-slate-200 dark:border-zinc-800 shrink-0">
        <a href="index.php?action=logout" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-bold text-red-600 dark:text-red-400 bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:hover:bg-red-500/20 rounded-xl transition-all shadow-sm">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            Sign Out
        </a>
    </div>

</aside>