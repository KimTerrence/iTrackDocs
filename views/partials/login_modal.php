<div id="login-modal" class="<?= isset($error) ? 'flex' : 'hidden' ?> fixed inset-0 z-[100] items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40 dark:bg-slate-950/60 backdrop-blur-sm transition-opacity" onclick="closeLoginModal()"></div>
    <div class="relative w-full max-w-md bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-[2rem] p-8 shadow-2xl animate-fade-in-up">
        <button onclick="closeLoginModal()" class="absolute top-5 right-5 p-2 bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-full transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
        <div class="text-center mb-8 mt-2">
            <div class="w-12 h-12 bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 rounded-xl flex items-center justify-center mx-auto mb-4 ring-1 ring-brand-100 dark:ring-brand-500/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white mb-1">Staff Portal</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Sign in with: admin / password</p>
        </div>
        <?php if (isset($error)): ?>
            <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl mb-6 text-sm text-center font-semibold">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form action="index.php?action=authenticate" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Username</label>
                <input type="text" name="username" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm" required>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Password</label>
                <input type="password" name="password" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm" required>
            </div>
            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-500 text-white py-3.5 rounded-xl font-bold shadow-md hover:shadow-lg transition-all mt-4 flex justify-center items-center gap-2">
                Sign In
            </button>
        </form>
    </div>
</div>