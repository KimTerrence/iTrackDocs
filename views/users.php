<div class="animate-fade-in-up space-y-6 flex flex-col h-full">
    
    <div class="flex justify-between items-center shrink-0">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">User Management</h1>
            <p class="text-slate-500 text-sm">Control system access and permissions.</p>
        </div>
        
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <button onclick="openUserModal()" class="bg-brand-600 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-brand-500 transition-colors shadow-md">
                Add New User
            </button>
        <?php endif; ?>
    </div>

    <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl overflow-hidden shadow-sm flex flex-col flex-1">
        <div class="flex-1 overflow-y-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50/80 dark:bg-slate-800/50 sticky top-0 z-10">
                    <tr>
                        <th class="py-4 pl-6 text-left text-xs font-bold text-slate-500 uppercase">User</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-slate-500 uppercase">Role</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="py-4 pr-6 text-right text-xs font-bold text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 pl-6 text-sm font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($user['name']) ?>
                            <div class="text-xs text-slate-500 font-normal">@<?= htmlspecialchars($user['username']) ?></div>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-600">
                            <select class="bg-transparent border-none font-bold text-brand-600 outline-none cursor-pointer">
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="staff" <?= $user['role'] == 'staff' ? 'selected' : '' ?>>Staff</option>
                                <option value="head" <?= $user['role'] == 'head' ? 'selected' : '' ?>>Department Head</option>
                            </select>
                        </td>
                        <td class="px-4 py-4">
                            <span class="px-2 py-1 bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 text-xs font-bold rounded-lg uppercase">Active</span>
                        </td>
                        <td class="py-4 pr-6 text-right text-sm">
                            <button class="text-slate-400 hover:text-red-600 mr-3 transition-colors">Delete</button>
                            <button type="button" 
                                onclick="openEditUserModal('<?= $user['id'] ?? '' ?>', '<?= htmlspecialchars($user['name'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($user['username'] ?? '', ENT_QUOTES) ?>', '<?= $user['role'] ?? 'staff' ?>')" 
                                class="text-brand-600 font-bold hover:text-brand-500 transition-colors">
                                Edit
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require __DIR__ . '/partials/add_user_modal.php'; ?>
    <?php require __DIR__ . '/partials/add_user_modal.php'; ?>
    <?php require __DIR__ . '/partials/edit_user_modal.php'; ?>
    
</div> <script>
    // EDIT MODAL LOGIC
    const editModal = document.getElementById('edit_user_modal');
    
    function openEditUserModal(id, name, username, role) {
        // 1. Populate the hidden ID field
        document.getElementById('edit_user_id').value = id;
        
        // 2. Populate the text inputs
        document.getElementById('edit_user_name').value = name;
        document.getElementById('edit_user_username').value = username;
        
        // 3. Set the correct role in the dropdown
        document.getElementById('edit_user_role').value = role;
        
        // 4. Show the modal
        editModal.classList.remove('hidden');
    }

    function closeEditUserModal() {
        editModal.classList.add('hidden');
    }
</script>
    
</div>