<div id="edit_user_modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm items-center justify-center p-4 transition-opacity">
    <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-3xl shadow-2xl overflow-hidden animate-fade-in-up">
        
        <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 flex justify-between items-center">
            <h3 class="text-xl font-black text-slate-900 dark:text-white">Edit User</h3>
            <button type="button" onclick="closeEditUserModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <form action="index.php?action=edit_user" method="POST" class="p-6">
            
            <input type="hidden" name="user_id" id="edit_user_id" value="">

            <div class="space-y-5 mb-8">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Full Name</label>
                    <input type="text" name="name" id="edit_user_name" required 
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 outline-none transition-all shadow-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Email</label>
                    <input type="email" name="email" id="edit_user_email" required 
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 outline-none transition-all shadow-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">System Role</label>
                    <select name="role" id="edit_user_role" required 
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 outline-none transition-all shadow-sm">
                        <option value="admin">Admin</option>
                        <option value="head">Department Head</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Department</label>
                    <select name="department" id="edit_user_department" required onchange="handleEditDepartmentChange()" 
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 outline-none transition-all shadow-sm">
                        <option value="" disabled selected>Select...</option>
                        <option value="Other">Other</option>
                    </select>
                    <input type="text" name="edit_other_department" id="edit_other_department" placeholder="Enter department name" style="display: none;" 
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 outline-none transition-all shadow-sm mt-2">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Reset Password <span class="text-slate-400 font-normal lowercase">(Leave blank to keep current)</span></label>
                    <input type="password" name="password" placeholder="••••••••" 
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand-500 outline-none transition-all shadow-sm">
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditUserModal()" class="w-full sm:w-auto px-6 py-3 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 font-bold rounded-xl transition-colors">
                    Cancel
                </button>
                <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl shadow-md shadow-brand-600/20 transition-all active:scale-[0.98]">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let existingDepartmentsEdit = [];
    
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
        echo 'existingDepartmentsEdit.push("' . addslashes($dept) . '");' . "\n";
    }
    ?>
    
    function populateEditDepartments() {
        const select = document.getElementById('edit_user_department');
        if (!select) return;
        
        // Clear existing options except first and "Other"
        select.innerHTML = '<option value="" disabled selected>Select...</option><option value="Other">Other</option>';
        
        // Add existing departments
        existingDepartmentsEdit.forEach(function(dept) {
            const option = document.createElement('option');
            option.value = dept;
            option.textContent = dept;
            select.appendChild(option);
        });
    }
    
    function handleEditDepartmentChange() {
        const select = document.getElementById('edit_user_department');
        const otherField = document.getElementById('edit_other_department');
        
        if (select.value === 'Other') {
            otherField.style.display = 'block';
            otherField.required = true;
        } else {
            otherField.style.display = 'none';
            otherField.required = false;
        }
    }
    
    // On form submit, if "Other" is selected, copy the input value to the select
    document.querySelector('#edit_user_modal form').addEventListener('submit', function(e) {
        const select = document.getElementById('edit_user_department');
        const otherField = document.getElementById('edit_other_department');
        
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
    
    function closeEditUserModal() {
        const modal = document.getElementById('edit_user_modal');
        if(modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
</script>