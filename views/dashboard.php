<?php
    // Calculate metrics
    $totalDocs = count($documents ?? []);
    $pendingDocs = 0;
    $approvedDocs = 0;
    
    foreach(($documents ?? []) as $d) { 
        if(strtolower($d['status']) === 'approved') {
            $approvedDocs++;
        } else {
            $pendingDocs++;
        }
    }

    // Mock data for new KPI categories (Delayed/Archived) for UI purposes
    $delayedDocs = ($totalDocs > 0) ? rand(0, 2) : 0;
    $archivedDocs = 142; 

    // Extract recent activity from documents for the timeline
    $recentActivity = [];
    foreach(($documents ?? []) as $id => $doc) {
        if(!empty($doc['history'])) {
            $latest = end($doc['history']);
            $recentActivity[] = [
                'id' => $id,
                'title' => $doc['title'],
                'action' => $latest['action'],
                'date' => $latest['date']
            ];
        }
    }
    // Sort so newest is first, grab top 4
    usort($recentActivity, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
    $recentActivity = array_slice($recentActivity, 0, 4);
?>

<div class="animate-fade-in-up space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl ring-1 ring-slate-200 dark:ring-slate-800 shadow-sm">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-1">Dashboard</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>. Here is what's happening.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Generate Report
            </button>
            <button class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                Route Document
            </button>
            <a href="index.php?action=create" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-500 transition-colors shadow-md shadow-brand-500/20">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Create Document
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white"><?= $totalDocs ?></h3>
                <div class="p-2 bg-brand-50 dark:bg-brand-500/10 text-brand-500 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Pending</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white"><?= $pendingDocs ?></h3>
                <div class="p-2 bg-amber-50 dark:bg-amber-500/10 text-amber-500 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Approved</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white"><?= $approvedDocs ?></h3>
                <div class="p-2 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Delayed</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white"><?= $delayedDocs ?></h3>
                <div class="p-2 bg-red-50 dark:bg-red-500/10 text-red-500 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg></div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 p-5 shadow-sm">
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Archived</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white"><?= $archivedDocs ?></h3>
                <div class="p-2 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <div class="xl:col-span-2 bg-white dark:bg-slate-900 shadow-sm ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Active Documents</h2>
                <a href="#" class="text-sm font-semibold text-brand-600 hover:text-brand-500">View All</a>
            </div>
            
            <div class="overflow-x-auto flex-1">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800/60">
                    <thead class="bg-slate-50/80 dark:bg-slate-800/50">
                        <tr>
                            <th class="py-3 pl-6 pr-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tracking ID</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Details</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="py-3 pl-3 pr-6 text-right"><span class="sr-only">Action</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="4" class="py-12 text-center text-slate-500">No active documents found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (array_slice($documents, 0, 5) as $id => $doc): ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors group">
                                    <td class="whitespace-nowrap py-3 pl-6 pr-3 text-sm">
                                        <span class="font-mono font-semibold text-brand-600 dark:text-brand-400"><?= htmlspecialchars($id) ?></span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white truncate max-w-[200px]"><?= htmlspecialchars($doc['title']) ?></p>
                                        <p class="text-xs text-slate-500 truncate max-w-[200px]">Loc: <?= htmlspecialchars($doc['current_location']) ?></p>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-3 text-sm">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider <?= strtolower($doc['status']) === 'approved' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' ?>">
                                            <?= htmlspecialchars($doc['status']) ?>
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap py-3 pl-3 pr-6 text-right text-sm">
                                        <a href="index.php?action=view&tracking_id=<?= urlencode($id) ?>" class="text-brand-600 hover:text-brand-500 font-medium">Review</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 shadow-sm ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Recent Activities</h2>
            </div>
            
            <div class="space-y-6">
                <?php if (empty($recentActivity)): ?>
                    <p class="text-sm text-slate-500 text-center py-4">No recent activity.</p>
                <?php else: ?>
                    <?php foreach ($recentActivity as $index => $activity): ?>
                        <div class="flex gap-4 relative">
                            <?php if ($index !== count($recentActivity) - 1): ?>
                                <div class="absolute left-4 top-8 bottom-[-24px] w-px bg-slate-200 dark:bg-slate-800"></div>
                            <?php endif; ?>
                            
                            <div class="relative z-10 w-8 h-8 rounded-full bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center shrink-0 ring-4 ring-white dark:ring-slate-900">
                                <div class="w-2.5 h-2.5 rounded-full bg-brand-500"></div>
                            </div>
                            
                            <div class="pb-1">
                                <p class="text-sm font-medium text-slate-900 dark:text-white leading-tight">
                                    <?= htmlspecialchars($activity['action']) ?>
                                </p>
                                <p class="text-xs text-slate-500 mt-1">
                                    <span class="font-mono text-brand-600 dark:text-brand-400"><?= htmlspecialchars($activity['id']) ?></span> 
                                    &bull; <?= htmlspecialchars($activity['date']) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <button class="w-full mt-8 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                View Full Audit Log
            </button>
        </div>

    </div>
</div>