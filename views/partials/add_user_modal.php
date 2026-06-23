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

            <div class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Full Name</label>
                <input type="text" name="name" placeholder="" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
                <input type="email" name="email" placeholder="" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Temporary Password</label>
                <input type="password" name="password" placeholder="••••••••" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">System Role</label>
                    <select name="role" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm cursor-pointer">
                        <option value="staff">Staff</option>
                        <?php if (strtolower($_SESSION['user_role'] ?? '') === 'admin'): ?>
                            <option value="head">Department Head</option>
                            <option value="admin">System Admin</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Department</label>
                    <?php if (strtolower($_SESSION['user_role'] ?? '') === 'admin'): ?>
                        <select name="department" id="user_department" required onchange="handleDepartmentChange()" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm cursor-pointer">
                            <option value="" disabled selected>Select...</option>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" name="department" id="other_department" placeholder="Enter department name" style="display: none;" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500 transition-all shadow-sm mt-2">
                    <?php else: ?>
                        <input type="text" name="department" value="<?= htmlspecialchars($_SESSION['user_department'] ?? '') ?>" readonly 
                            class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 rounded-xl px-4 py-3 cursor-not-allowed font-bold outline-none shadow-sm">
                    <?php endif; ?>
                </div>
                </div>
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
    let existingDepartments = [];
    
    <?php
    $userModel = new \Models\User();
    $allUsers = $userModel->getUsers();
    $departments = array();
    if ($allUsers) {
        foreach ($allUsers as $u) {
            if (!empty($u['department']) && !in_array($u['department'], $departments)) {
                $departments[] = $u['department'];
            }
        }
        sort($departments);
    }
    foreach ($departments as $dept) {
        echo 'existingDepartments.push("' . addslashes($dept) . '");' . "\n";
    }
    ?>
    
    function populateDepartments() {
        const select = document.getElementById('user_department');
        if (!select) return;
        
        // Clear existing options except first and "Other"
        select.innerHTML = '<option value="" disabled selected>Select...</option><option value="Other">Other</option>';
        
        // Add existing departments
        existingDepartments.forEach(function(dept) {
            const option = document.createElement('option');
            option.value = dept;
            option.textContent = dept;
            select.appendChild(option);
        });
    }
    
    function handleDepartmentChange() {
        const select = document.getElementById('user_department');
        const otherField = document.getElementById('other_department');
        
        if (select.value === 'Other') {
            otherField.style.display = 'block';
            otherField.required = true;
        } else {
            otherField.style.display = 'none';
            otherField.required = false;
        }
    }
    
    // On form submit, if "Other" is selected, copy the input value to the select
    document.querySelector('#user-modal form').addEventListener('submit', function(e) {
        const select = document.getElementById('user_department');
        const otherField = document.getElementById('other_department');
        
        if (select && otherField && select.value === 'Other' && otherField.value.trim()) {
            // Create a hidden input with the correct department value
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'department';
            hiddenInput.value = otherField.value.trim();
            
            // Remove the select's name attribute so it doesn't submit "Other"
            select.removeAttribute('name');
            
            this.appendChild(hiddenInput);
        }
    });
    
    function openUserModal() {
        if(userModal) {
            userModal.classList.remove('hidden');
            userModal.classList.add('flex');
            setTimeout(() => { document.querySelector('input[name="name"]')?.focus(); }, 50);
            populateDepartments();
        }
    }
    
    function closeUserModal() {
        if(userModal) {
            userModal.classList.add('hidden');
            userModal.classList.remove('flex');
            document.querySelector('#user-modal form').reset();
            document.getElementById('other_department').style.display = 'none';
        }
    }
</script>
