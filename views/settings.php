<div class="animate-fade-in-up space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Account Settings</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Manage your personal information and preferences.</p>
        </div>
    </div>

    <div class="max-w-4xl">

        <?php if (isset($_GET['success'])): ?>
            <div class="mb-6 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 p-4 rounded-2xl border border-emerald-200 dark:border-emerald-800 flex items-center gap-3 shadow-sm animate-fade-in-up">
                <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-500/20 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <span class="font-bold text-sm">Your profile has been successfully updated!</span>
            </div>
        <?php endif; ?>

        <?php if (isset($error) && $error): ?>
            <div class="mb-6 bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 p-4 rounded-2xl border border-red-200 dark:border-red-800 flex items-center gap-3 shadow-sm animate-fade-in-up">
                <div class="w-8 h-8 bg-red-100 dark:bg-red-500/20 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="font-bold text-sm"><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-white dark:bg-slate-900 rounded-3xl ring-1 ring-slate-200 dark:ring-slate-800 shadow-sm overflow-hidden">
            
            <div class="px-6 sm:px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 flex items-center gap-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-brand-100 dark:bg-brand-500/20 text-brand-600 dark:text-brand-400 border-4 border-white dark:border-slate-900 shadow-sm flex items-center justify-center text-2xl font-black uppercase shrink-0">
                    <?= substr(htmlspecialchars($user['name'] ?? 'U'), 0, 1) ?>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($user['name'] ?? 'User') ?></h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">Profile Information</p>
                </div>
            </div>

            <form action="index.php?action=update_settings" method="POST" class="p-6 sm:p-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 mb-8">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Full Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 outline-none transition-all shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 outline-none transition-all shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Department</label>
                        <input type="text" name="department" value="<?= htmlspecialchars($user['department'] ?? '') ?>" required 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 outline-none transition-all shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2 flex items-center gap-1">
                            System Role
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </label>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 rounded-xl px-4 py-3 font-bold cursor-not-allowed uppercase text-sm shadow-inner">
                            <?= htmlspecialchars($user['role'] ?? 'User') ?>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1.5 font-medium">Contact an administrator to change your permissions.</p>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex flex-col-reverse sm:flex-row justify-end gap-3">
                    <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl shadow-md shadow-brand-600/20 transition-all active:scale-[0.98]">
                        Save Changes
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>