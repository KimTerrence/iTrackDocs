<div id="user-modal" class="hidden fixed inset-0 z-[100] items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/40 dark:bg-slate-950/60 backdrop-blur-sm transition-opacity" onclick="closeUserModal()"></div>
    
    <form action="index.php?action=add_user" method="POST" class="relative w-full max-w-md bg-white dark:bg-slate-900 p-8 rounded-[2rem] shadow-2xl animate-fade-in-up ring-1 ring-slate-200 dark:ring-slate-800">
        
        <button type="button" onclick="closeUserModal()" class="absolute top-5 right-5 p-2 bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-full transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <div class="mb-6 mt-2">
            <div class="w-12 h-12 bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 rounded-xl flex items-center justify-center mb-4 ring-1 ring-brand-100 dark:ring-brand-500/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white mb-1">Create New User</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Add a new staff member to the system.</p>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Full Name</label>
                <input type="text" name="name" placeholder="e.g., Jane Doe" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Username</label>
                <input type="text" name="username" placeholder="e.g., janedoe" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Temporary Password</label>
                <input type="password" name="password" placeholder="••••••••" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">System Role</label>
                <select name="role" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm cursor-pointer">
                    <option value="staff">Staff (Basic Access)</option>
                    <option value="head">Department Head</option>
                    <option value="admin">System Admin</option>
                </select>
            </div>
        </div>

        <button type="submit" class="w-full mt-8 bg-brand-600 hover:bg-brand-500 text-white py-3.5 rounded-xl font-bold shadow-md hover:shadow-lg transition-all flex justify-center items-center gap-2">
            Save User Account
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
        </button>
    </form>
</div>

<script>
    const userModal = document.getElementById('user-modal');
    
    function openUserModal() {
        if(userModal) {
            userModal.classList.remove('hidden');
            userModal.classList.add('flex');
            setTimeout(() => { document.querySelector('input[name="name"]')?.focus(); }, 50);
        }
    }
    
    function closeUserModal() {
        if(userModal) {
            userModal.classList.add('hidden');
            userModal.classList.remove('flex');
            document.querySelector('#user-modal form').reset();
        }
    }
</script>