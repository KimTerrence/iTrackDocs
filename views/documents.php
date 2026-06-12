<div class="animate-fade-in-up space-y-6">
    
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Documents</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Manage and monitor all system documents.</p>
        </div>
        <a href="index.php?action=create" class="inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-500 text-white font-bold py-2.5 px-5 rounded-xl transition-all shadow-md shadow-brand-500/20">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Create Document
        </a>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-1">
            <input type="text" placeholder="Search tracking ID or subject..." class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
        </div>
        <select class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-600 dark:text-slate-400 outline-none">
            <option>All Statuses</option>
            <option>Routing</option>
            <option>Approved</option>
        </select>
        <select class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-600 dark:text-slate-400 outline-none">
            <option>All Departments</option>
        </select>
        <div class="flex gap-2">
            <button class="flex-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold rounded-xl text-sm hover:bg-slate-200 transition-colors">Reset</button>
        </div>
    </div>

    <!-- Document Table -->
    <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50/50 dark:bg-slate-800/20">
                    <tr>
                        <th class="py-4 pl-6 text-left text-xs font-bold text-slate-500 uppercase">Tracking Code</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-slate-500 uppercase">Subject</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-slate-500 uppercase">Sender</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-slate-500 uppercase">Type</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="px-4 py-4 text-left text-xs font-bold text-slate-500 uppercase">Office</th>
                        <th class="py-4 pr-6 text-right text-xs font-bold text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($documents as $id => $doc): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 pl-6 text-sm font-mono font-bold text-brand-600"><?= htmlspecialchars($id) ?></td>
                        <td class="px-4 py-4 text-sm font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($doc['title']) ?></td>
                        <td class="px-4 py-4 text-sm text-slate-600 dark:text-slate-400">User Name</td>
                        <td class="px-4 py-4 text-sm text-slate-600 dark:text-slate-400">Letter</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="px-2 py-1 bg-brand-50 text-brand-700 text-xs font-bold rounded-lg uppercase"><?= htmlspecialchars($doc['status']) ?></span>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-600 dark:text-slate-400"><?= htmlspecialchars($doc['current_location']) ?></td>
                        <td class="py-4 pr-6 text-right">
                            <a href="index.php?action=view&tracking_id=<?= urlencode($id) ?>" class="text-brand-600 hover:underline font-bold text-sm">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center text-sm text-slate-500">
            <p>Showing page <?= $pagination['current'] ?> of <?= $pagination['pages'] ?></p>
            <div class="flex gap-2">
                <a href="index.php?action=list&p=<?= max(1, $pagination['current'] - 1) ?>" 
                class="px-3 py-1 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 <?= $pagination['current'] <= 1 ? 'opacity-50 pointer-events-none' : '' ?>">Prev</a>
                
                <?php for ($i = 1; $i <= $pagination['pages']; $i++): ?>
                    <a href="index.php?action=list&p=<?= $i ?>" 
                    class="px-3 py-1 rounded-lg <?= $i == $pagination['current'] ? 'bg-brand-600 text-white' : 'border border-slate-200 dark:border-slate-700 hover:bg-slate-50' ?>">
                    <?= $i ?>
                    </a>
                <?php endfor; ?>

                <a href="index.php?action=list&p=<?= min($pagination['pages'], $pagination['current'] + 1) ?>" 
                class="px-3 py-1 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 <?= $pagination['current'] >= $pagination['pages'] ? 'opacity-50 pointer-events-none' : '' ?>">Next</a>
            </div>
        </div>
    </div>
</div>