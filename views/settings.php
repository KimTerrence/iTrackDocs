<div class="animate-fade-in-up space-y-6 w-full">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Account Settings</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Manage your personal information and security preferences.</p>
        </div>
    </div>

    <div class="w-full">
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
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 w-full items-start">

        <div class="bg-white dark:bg-slate-900 rounded-3xl ring-1 ring-slate-200 dark:ring-slate-800 shadow-sm overflow-hidden flex flex-col h-full">
            
            <div class="px-6 sm:px-8 py-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 flex items-center gap-5">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['name'] ?? 'U') ?>&background=2563eb&color=fff&rounded=true&size=128" alt="Profile" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full ring-4 ring-white dark:ring-slate-900 shadow-sm shrink-0">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($user['name'] ?? 'User') ?></h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5"><?= htmlspecialchars($user['email'] ?? 'email') ?></p>
                </div>
            </div>

            <form action="index.php?action=update_settings" method="POST" class="p-6 sm:p-8 flex flex-col flex-1">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Profile Details</h3>

                <div class="grid grid-cols-1 gap-y-6 mb-8 flex-1">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Full Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 outline-none transition-shadow shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 outline-none transition-shadow shadow-sm">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Department</label>
                            <input type="text" name="department" value="<?= htmlspecialchars($user['department'] ?? '') ?>" required 
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 outline-none transition-shadow shadow-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2 flex items-center gap-1">
                                System Role
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            </label>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 rounded-xl px-4 py-3 font-bold cursor-not-allowed uppercase text-sm shadow-inner">
                                <?= htmlspecialchars($user['role'] ?? 'User') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex justify-end mt-auto">
                    <button type="submit" class="w-full sm:w-auto px-8 py-2.5 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl shadow-md shadow-brand-600/20 transition-all">
                        Save Profile Changes
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-3xl ring-1 ring-slate-200 dark:ring-slate-800 shadow-sm overflow-hidden flex flex-col h-full">
            <form action="index.php?action=update_password" method="POST" class="p-6 sm:p-8 flex flex-col flex-1">
                
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-12 h-12 rounded-full bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Security Settings</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Update your account password and secure your account.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-y-6 mb-8 flex-1">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Current Password</label>
                        <input type="password" name="current_password" required 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 outline-none transition-shadow shadow-sm">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-slate-100 dark:border-slate-800 pt-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">New Password</label>
                            <input type="password" name="new_password" required minlength="8"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 outline-none transition-shadow shadow-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Confirm New Password</label>
                            <input type="password" name="confirm_password" required minlength="8"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-500 outline-none transition-shadow shadow-sm">
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-xl border border-slate-200 dark:border-slate-700">
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Password Requirements:</p>
                        <ul class="text-xs text-slate-500 dark:text-slate-400 space-y-1 list-disc list-inside">
                            <li>Must be at least 8 characters long.</li>
                            <li>Cannot match your previous password.</li>
                        </ul>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex justify-end mt-auto">
                    <button type="submit" class="w-full sm:w-auto px-8 py-2.5 bg-slate-800 dark:bg-white hover:bg-slate-700 dark:hover:bg-slate-200 text-white dark:text-slate-900 font-bold rounded-xl shadow-md transition-all">
                        Update Password
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>