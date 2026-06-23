<?php
$role = strtolower(trim($role ?? ''));
$user_dept = $user_dept ?? '';
$logs = $logs ?? [];
$userLogs = $userLogs ?? [];
?>

<div class="animate-fade-in-up space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl ring-1 ring-slate-200 dark:ring-slate-800 shadow-sm">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Activity Logs</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Audit trail of all document activity across the system.</p>
        </div>
        <div class="text-xs text-slate-400 font-mono">
            Showing last <?= count($logs) ?> entries
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl overflow-hidden shadow-sm">
        <?php if (empty($logs)): ?>
            <div class="py-16 text-center">
                <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-slate-500 dark:text-slate-400 font-medium">No document activity logged yet.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50/80 dark:bg-slate-800/50">
                        <tr>
                            <th class="py-4 pl-6 pr-3 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Timestamp</th>
                            <th class="px-3 py-4 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Document</th>
                            <th class="px-3 py-4 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Location</th>
                            <th class="px-3 py-4 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Action</th>
                            <th class="py-4 pl-3 pr-6 text-right text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tracking ID</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900">
                        <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm text-slate-500 dark:text-slate-400 font-mono">
                                <?= htmlspecialchars($log['date']) ?>
                            </td>
                            <td class="px-3 py-4 text-sm font-bold text-slate-900 dark:text-white truncate max-w-[250px]">
                                <?= htmlspecialchars($log['title']) ?>
                            </td>
                            <td class="px-3 py-4 text-sm text-slate-600 dark:text-slate-300">
                                <?= htmlspecialchars($log['location']) ?>
                            </td>
                            <td class="px-3 py-4 text-sm text-slate-600 dark:text-slate-300">
                                <?= htmlspecialchars($log['action']) ?>
                            </td>
                            <td class="whitespace-nowrap py-4 pl-3 pr-6 text-right">
                                <a href="index.php?action=view&tracking_id=<?= urlencode($log['tracking_id']) ?>" class="font-mono text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline">
                                    <?= htmlspecialchars($log['tracking_id']) ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl overflow-hidden shadow-sm mt-8">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">User Activity Logs</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Audit trail of system-level user actions (logins, profile changes, etc.).</p>
                </div>
                <div class="text-xs text-slate-400 font-mono">
                    Showing last <?= count($userLogs) ?> entries
                </div>
            </div>
        </div>

        <?php if (empty($userLogs)): ?>
            <div class="py-16 text-center">
                <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-slate-500 dark:text-slate-400 font-medium">No user activity logged yet.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50/80 dark:bg-slate-800/50">
                        <tr>
                            <th class="py-4 pl-6 pr-3 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Timestamp</th>
                            <th class="px-3 py-4 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">User</th>
                            <th class="px-3 py-4 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Action</th>
                            <th class="px-3 py-4 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Description</th>
                            <th class="py-4 pl-3 pr-6 text-right text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900">
                        <?php foreach ($userLogs as $log): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors group">
                            <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm text-slate-500 dark:text-slate-400 font-mono">
                                <?= date('M d, Y h:i A', strtotime($log['created_at'])) ?>
                            </td>
                            <td class="px-3 py-4 text-sm font-bold text-slate-900 dark:text-white truncate max-w-[180px]">
                                <?= htmlspecialchars($log['username']) ?>
                            </td>
                            <td class="px-3 py-4 text-sm text-slate-600 dark:text-slate-300">
                                <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-slate-800 dark:text-slate-200">
                                    <?= htmlspecialchars($log['action']) ?>
                                </span>
                            </td>
                            <td class="px-3 py-4 text-sm text-slate-600 dark:text-slate-300">
                                <?= htmlspecialchars($log['description']) ?>
                            </td>
                            <td class="whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm text-slate-500 dark:text-slate-400 font-mono">
                                <?= htmlspecialchars($log['ip_address'] ?? '-') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
