<?php
// Helper function for dynamic status badge colors (same as Dashboard)
function getStatusBadge($status) {
    $status = strtolower($status ?? 'pending');
    if (in_array($status, ['approved', 'received', 'completed'])) return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 ring-1 ring-emerald-600/20';
    if ($status === 'archived') return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 ring-1 ring-slate-500/20';
    if ($status === 'routing') return 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 ring-1 ring-blue-600/20';
    return 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 ring-1 ring-amber-600/20';
}
?>

<div class="animate-fade-in-up space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl ring-1 ring-slate-200 dark:ring-slate-800 shadow-sm">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Document Directory</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Search, filter, and manage all system records.</p>
        </div>
        <a href="index.php?action=create" class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-500 text-white font-bold py-2.5 px-5 rounded-xl transition-all shadow-md shadow-brand-500/20">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Create Document
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 shadow-sm grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="md:col-span-1 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input type="text" placeholder="Search tracking ID or subject..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none text-slate-900 dark:text-white placeholder-slate-400 transition-shadow">
        </div>
        <select class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 outline-none focus:ring-2 focus:ring-brand-500 transition-shadow">
            <option>All Statuses</option>
            <option>Routing</option>
            <option>Received</option>
            <option>Approved</option>
            <option>Returned</option>
            <option>Archived</option>
        </select>
        <select class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 outline-none focus:ring-2 focus:ring-brand-500 transition-shadow">
            <option>All Categories</option>
            <option>Memorandum</option>
            <option>Letter</option>
            <option>Report</option>
            <option>Resolution</option>
            <option>Order</option>
            <option>Notice</option>
            <option>Certificate</option>
            <option>Contract</option>
            <option>Form</option>
            <option>Other</option>
        </select>
        <select class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300 outline-none focus:ring-2 focus:ring-brand-500 transition-shadow">
            <option>All Departments</option>
            <option>Human Resources</option>
            <option>Finance</option>
            <option>IT Department</option>
        </select>
        <div class="flex gap-2">
            <button class="flex-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold rounded-xl text-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors shadow-sm border border-slate-200 dark:border-slate-700">Filter Results</button>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl overflow-hidden shadow-sm flex flex-col">
        <div class="overflow-x-auto flex-1">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50/80 dark:bg-slate-800/50">
                    <tr>
                        <th class="py-4 pl-6 pr-3 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tracking Code</th>
                        <th class="px-3 py-4 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Subject</th>
                        <th class="px-3 py-4 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Category</th>
                        <th class="px-3 py-4 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Origin</th>
                        <th class="px-3 py-4 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Location</th>
                        <th class="px-3 py-4 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-3 py-4 text-left text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Date Created</th>
                        <th class="py-4 pl-3 pr-6 text-right text-xs font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900">
                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <p class="text-slate-500 dark:text-slate-400 font-medium text-lg">No documents found.</p>
                                <p class="text-slate-400 dark:text-slate-500 text-sm mt-1">Try adjusting your search or filters.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($documents as $id => $doc): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors group">
                            
                            <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm">
                                <div class="flex items-center gap-2">                               
                                    <span class="font-mono font-bold text-slate-900 dark:text-white group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors"><?= htmlspecialchars($id) ?></span>
                                    <?php if (!empty($doc['file_path'])): ?>
                                        <svg class="w-4 h-4 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                    <?php endif; ?>
                                </div>
                            </td>
                            
                            <td class="px-3 py-4 text-sm font-bold text-slate-900 dark:text-white max-w-[250px] truncate">
                                <?= htmlspecialchars($doc['title']) ?>
                            </td>
                            
                            <td class="px-3 py-4 text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400 ring-1 ring-purple-600/20">
                                    <?= htmlspecialchars($doc['category'] ?? 'Uncategorized') ?>
                                </span>
                            </td>
                            
                            <td class="px-3 py-4 text-sm font-medium text-slate-600 dark:text-slate-300">
                                <?= htmlspecialchars($doc['origin']) ?>
                            </td>
                            
                            <td class="px-3 py-4 text-sm font-medium text-slate-600 dark:text-slate-300">
                                <?= htmlspecialchars($doc['current_location']) ?>
                            </td>
                            
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider <?= getStatusBadge($doc['status']) ?>">
                                    <?= htmlspecialchars($doc['status'] ?? 'Pending') ?>
                                </span>
                            </td>
                            
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                                <?= date('M d, Y', strtotime($doc['created_at'] ?? 'now')) ?>
                            </td>
                            
                            <td class="whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                                <a href="index.php?action=view&tracking_id=<?= urlencode($id) ?>" class="text-brand-600 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300 bg-brand-50 dark:bg-brand-500/10 px-4 py-2 rounded-lg transition-colors inline-block">Review Document</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50 dark:bg-slate-900/50">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                Showing page <span class="font-bold text-slate-900 dark:text-white"><?= $pagination['current'] ?></span> of <span class="font-bold text-slate-900 dark:text-white"><?= max(1, $pagination['pages']) ?></span>
            </p>
            
            <div class="flex gap-2">
                <a href="index.php?action=list&p=<?= max(1, $pagination['current'] - 1) ?>" 
                class="px-3 py-1.5 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 transition-colors shadow-sm <?= $pagination['current'] <= 1 ? 'opacity-50 pointer-events-none' : '' ?>">
                    Prev
                </a>
                
                <div class="hidden sm:flex gap-1">
                    <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
                        <a href="index.php?action=list&p=<?= $i ?>" 
                        class="px-3.5 py-1.5 rounded-lg text-sm font-bold transition-colors shadow-sm <?= $i == $pagination['current'] ? 'bg-brand-600 text-white ring-1 ring-brand-600' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800' ?>">
                        <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <a href="index.php?action=list&p=<?= min($pagination['pages'], $pagination['current'] + 1) ?>" 
                class="px-3 py-1.5 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-800 transition-colors shadow-sm <?= $pagination['current'] >= $pagination['pages'] ? 'opacity-50 pointer-events-none' : '' ?>">
                    Next
                </a>
            </div>
        </div>
    </div>
</div>