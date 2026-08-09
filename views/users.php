<?php
// Helper function for styling User Roles
function getRoleBadge($role) {
    $r = strtolower($role ?? 'staff');
    if ($r === 'admin') return 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400 ring-1 ring-purple-600/20';
    if ($r === 'head') return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 ring-1 ring-emerald-600/20';
    return 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 ring-1 ring-blue-600/20';
}

// Helper function for styling User Status
function getStatusBadge($status) {
    $s = strtolower($status ?? 'active');
    if ($s === 'blocked') return 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400 ring-1 ring-red-600/20';
    return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 ring-1 ring-emerald-600/20';
}
?>

<div class="animate-fade-in-up space-y-6 flex flex-col h-full">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl ring-1 ring-slate-200 dark:ring-slate-800 shadow-sm shrink-0">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">User Management</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Control system access, roles, and permissions.</p>
        </div>
        
       <?php 
            $role = strtolower($_SESSION['user_role'] ?? '');
            if ($role === 'admin' || $role === 'head'): 
        ?>
            <button onclick="openUserModal()" class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-500 text-white font-bold py-2.5 px-5 rounded-xl transition-all shadow-md shadow-brand-500/20">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                Add New User
            </button>
        <?php endif; ?>
    </div>

    <form method="GET" action="index.php" id="userFilterForm" class="bg-white dark:bg-slate-900 p-4 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 shadow-sm flex flex-col md:flex-row gap-4 shrink-0">
        <input type="hidden" name="action" value="users">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input type="text" name="search" id="userSearch" placeholder="Search by name or email..." value="<?= htmlspecialchars($search ?? '') ?>"
                   class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none text-slate-900 dark:text-white placeholder-slate-400 transition-shadow">
        </div>
        <select name="role" id="userRole" class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 outline-none focus:ring-2 focus:ring-brand-500 transition-shadow w-full md:w-48">
            <option value="">All Roles</option>
            <option value="admin" <?= ($currentRole ?? '') === 'admin' ? 'selected' : '' ?>>Administrators</option>
            <option value="head" <?= ($currentRole ?? '') === 'head' ? 'selected' : '' ?>>Department Heads</option>
            <option value="staff" <?= ($currentRole ?? '') === 'staff' ? 'selected' : '' ?>>Staff</option>
        </select>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('userFilterForm');
        const inputs = form.querySelectorAll('input, select');
        let debounceTimer;

        function submitForm() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                form.submit();
            }, 400);
        }

        inputs.forEach(function(input) {
            if (input.type === 'text') {
                input.addEventListener('input', submitForm);
            }
            if (input.tagName === 'SELECT') {
                input.addEventListener('change', submitForm);
            }
        });
    });
    </script>

    <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl overflow-hidden shadow-sm flex flex-col flex-1">
        <div class="flex-1 overflow-y-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50/80 dark:bg-slate-800/50 sticky top-0 z-10 backdrop-blur-md">
                    <tr>
                        <th class="py-4 pl-6 pr-3 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">User Details</th>
                        <th class="px-3 py-4 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Department</th>
                        <th class="px-3 py-4 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">System Role</th>
                        <th class="px-3 py-4 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="py-4 pl-3 pr-6 text-right text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                <p class="text-slate-500 dark:text-slate-400 font-medium text-lg">No users found.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors group">
                            
                            <td class="py-4 pl-6 pr-3">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&background=random&color=fff&rounded=true" alt="Avatar" class="w-10 h-10 rounded-full ring-2 ring-white dark:ring-slate-800 shadow-sm shrink-0">
                                    <div>
                                        <div class="text-sm font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($user['name']) ?></div>
                                        <div class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-0.5"><?= htmlspecialchars($user['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-3 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                    <?= htmlspecialchars($user['department'] ?? 'Unassigned') ?>
                                </span>
                            </td>
                            
                            <td class="px-3 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider <?= getRoleBadge($user['role']) ?>">
                                    <?= htmlspecialchars(ucfirst($user['role'] ?? 'Staff')) ?>
                                </span>
                            </td>
                            
                            <td class="px-3 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider <?= getStatusBadge($user['status'] ?? 'active') ?>">
                                    <?= htmlspecialchars(ucfirst($user['status'] ?? 'Active')) ?>
                                </span>
                            </td>
                            
                            <td class="py-4 pl-3 pr-6 text-right whitespace-nowrap text-sm font-medium">
                                <?php 
                                    $isBlocked = strtolower($user['status'] ?? 'active') === 'blocked';
                                    $currentUserId = $_SESSION['user_id'] ?? null;
                                ?>
                                <?php if ($currentUserId != $user['id']): ?>
                                <form action="index.php?action=block_user" method="POST" class="inline mr-2">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <input type="hidden" name="action" value="<?= $isBlocked ? 'unblock' : 'block' ?>">
                                    <button type="submit" class="<?= $isBlocked 
                                        ? 'text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300' 
                                        : 'text-red-400 dark:text-red-500 hover:text-red-600 dark:hover:text-red-400' ?> 
                                        transition-colors font-bold text-xs">
                                        <?= $isBlocked ? 'Unblock' : 'Block' ?>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <button type="button" 
                                    onclick="openEditUserModal(<?= htmlspecialchars(json_encode([
                                        'id' => $user['id'] ?? '',
                                        'name' => $user['name'] ?? '',
                                        'email' => $user['email'] ?? '',
                                        'role' => strtolower($user['role'] ?? 'staff'),
                                        'department' => $user['department'] ?? ''
                                    ]), ENT_QUOTES, 'UTF-8') ?>)"
                                    class="text-brand-600 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300 bg-brand-50 dark:bg-brand-500/10 px-4 py-2 rounded-lg transition-colors inline-block">
                                    Edit
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require __DIR__ . '/partials/add_user_modal.php'; ?>
    <?php require __DIR__ . '/partials/edit_user_modal.php'; ?>
    
    <script>
        // EDIT MODAL LOGIC
        function openEditUserModal(userData) {
            const editModal = document.getElementById('edit_user_modal');
            if(editModal) {
                document.getElementById('edit_user_id').value = userData.id;
                document.getElementById('edit_user_name').value = userData.name;
                // Username removed - email is used for login
                document.getElementById('edit_user_role').value = userData.role;
                editModal.classList.remove('hidden');
                editModal.classList.add('flex');
            }
        }
    </script>
</div>