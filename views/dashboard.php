<?php
    // REAL METRICS CALCULATION
    $totalDocs = count($documents ?? []);
    $pendingDocs = 0;
    $returnedDocs = 0;
    $approvedDocs = 0;
    $delayedDocs = 0;
    $archivedDocs = 0;
    
    $now = time();
    $recentActivity = [];

    foreach(($documents ?? []) as $id => $doc) { 
        $status = strtolower($doc['status'] ?? 'pending');
        
        // 1. Calculate Statuses
        if (in_array($status, ['approved', 'completed', 'received'])) {
            $approvedDocs++;
        } elseif ($status === 'returned') {
            $returnedDocs++;
        } elseif ($status === 'archived') {
            $archivedDocs++;
        } elseif ($status === 'void') {
            // Void docs are excluded from active counters
            // (They still get added to recent activity below)
        } else {
            // It is Pending or Routing
            $pendingDocs++;
            
            // 2. Calculate Delays (If older than 2 days and not approved)
            if (!empty($doc['created_at'])) {
                $daysOld = ($now - strtotime($doc['created_at'])) / 86400; // 86400 seconds in a day
                if ($daysOld > 1) {
                    $delayedDocs++;
                }
            }
        }

        // 3. Extract recent activity for the timeline
        if(!empty($doc['history'])) {
            $latest = end($doc['history']);
            $recentActivity[] = [
                'id' => $id,
                'title' => $doc['title'],
                'action' => $latest['action'],
                'date' => $latest['date'],
                'timestamp' => strtotime($latest['date'])
            ];
        } else {
            // Fallback if history isn't loaded: show document creation/current state
            $recentActivity[] = [
                'id' => $id,
                'title' => $doc['title'],
                'action' => 'Document at ' . $doc['current_location'],
                'date' => date('M d, Y h:i A', strtotime($doc['created_at'] ?? 'now')),
                'timestamp' => strtotime($doc['created_at'] ?? 'now')
            ];
        }
    }

    // Sort timeline so newest is first, grab top 5
    usort($recentActivity, fn($a, $b) => $b['timestamp'] - $a['timestamp']);
    $recentActivity = array_slice($recentActivity, 0, 5);

    // Helper function for dynamic status badge colors
    function getStatusBadge($status) {
        $status = strtolower($status);
        if (in_array($status, ['approved', 'received', 'completed', 'returned'])) return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 ring-1 ring-emerald-600/20';
        if ($status === 'archived') return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 ring-1 ring-slate-500/20';
        if ($status === 'void') return 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400 ring-1 ring-red-600/20';
        if ($status === 'routing') return 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 ring-1 ring-blue-600/20';
        // Default (Pending)
        return 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 ring-1 ring-amber-600/20';
    }
?>

<div class="animate-fade-in-up space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-3xl ring-1 ring-slate-200 dark:ring-slate-800 shadow-sm relative overflow-hidden">
        
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-brand-500/5 rounded-full blur-3xl pointer-events-none"></div>
    
        <div class="relative z-10">
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-1">Dashboard</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Welcome back, <span class="font-bold text-slate-700 dark:text-slate-300"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Staff') ?></span>.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3 relative z-10">
            <button onclick="window.print()" class="print:hidden inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Generate Report
            </button>
            <a href="index.php?action=create" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-500 transition-colors shadow-md shadow-brand-500/20">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Create Document
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 p-5 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Active</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white"><?= $totalDocs ?></h3>
                <div class="p-2 bg-brand-50 dark:bg-brand-500/10 text-brand-500 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 p-5 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Routing / Pending</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white"><?= $pendingDocs ?></h3>
                <div class="p-2 bg-amber-50 dark:bg-amber-500/10 text-amber-500 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 p-5 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Returned</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white"><?= $returnedDocs ?></h3>
                <div class="p-2 bg-orange-50 dark:bg-orange-500/10 text-orange-500 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg></div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 p-5 shadow-sm hover:shadow-md transition-shadow border-b-4 border-red-500">
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Delayed (> 24h)</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-extrabold text-red-600 dark:text-red-400"><?= $delayedDocs ?></h3>
                <div class="p-2 bg-red-50 dark:bg-red-500/10 text-red-500 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg></div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-800 p-5 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Archived</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-extrabold text-slate-900 dark:text-white"><?= $archivedDocs ?></h3>
                <div class="p-2 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <div class="xl:col-span-2 bg-white dark:bg-slate-900 shadow-sm ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/50">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Recent Documents</h2>
                <a href="index.php?action=list" class="text-sm font-semibold text-brand-600 hover:text-brand-500 transition-colors">View All Directory &rarr;</a>
            </div>
            
            <div class="overflow-x-auto flex-1">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800/60">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="py-3 pl-6 pr-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tracking ID</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Document Details</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">User</th>
                            <th class="px-3 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="py-3 pl-3 pr-6 text-right"><span class="sr-only">Action</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 bg-white dark:bg-slate-900">
                        <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="5" class="py-16 text-center">
                                    <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    <p class="text-slate-500 dark:text-slate-400 font-medium">No active documents found.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (array_slice($documents, 0, 6) as $id => $doc): ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors group">
                                    <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm">
                                        <div class="flex items-center gap-2">                                       
                                            <span class="font-mono font-bold text-slate-900 dark:text-white group-hover:text-brand-600 dark:group-hover:text-brand-400 transition-colors"><?= htmlspecialchars($id) ?></span>                                        
                                             <?php if (!empty($doc['file_path'])): ?>
                                                <svg class="w-4 h-4 text-brand-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                            <?php endif; ?></div>
                                    </td>
                                    <td class="px-3 py-4">
                                        <p class="text-sm font-bold text-slate-900 dark:text-white truncate max-w-[250px]"><?= htmlspecialchars($doc['title']) ?></p>
                                        <div class="flex items-center text-xs text-slate-500 mt-0.5 gap-2">
                                            <span class="text-brand-600 dark:text-brand-400 font-medium truncate max-w-[150px]">At: <?= htmlspecialchars($doc['current_location']) ?></span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                            <span class="truncate max-w-[160px] text-slate-700 dark:text-slate-200">
                                                <?= htmlspecialchars($doc['creator_name'] ?? 'User #' . ($doc['creator_id'] ?? '—')) ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider <?= getStatusBadge($doc['status'] ?? 'Pending') ?>">
                                            <?= htmlspecialchars($doc['status'] ?? 'Pending') ?>
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                                        <a href="index.php?action=view&tracking_id=<?= urlencode($id) ?>" class="text-brand-600 dark:text-brand-400 hover:text-brand-800 dark:hover:text-brand-300 bg-brand-50 dark:bg-brand-500/10 px-3 py-1.5 rounded-lg transition-colors">Review</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 shadow-sm ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-6 flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">System Activity</h2>
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
            </div>
            
            <div class="space-y-6 flex-1">
                <?php if (empty($recentActivity)): ?>
                    <p class="text-sm text-slate-500 text-center py-12">No activity logged yet.</p>
                <?php else: ?>
                    <?php foreach ($recentActivity as $index => $activity): ?>
                        <div class="flex gap-4 relative group">
                            <?php if ($index !== count($recentActivity) - 1): ?>
                                <div class="absolute left-[15px] top-8 bottom-[-24px] w-px bg-slate-200 dark:bg-slate-700 group-hover:bg-brand-300 transition-colors"></div>
                            <?php endif; ?>
                            
                            <div class="relative z-10 w-8 h-8 rounded-full bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center shrink-0 ring-4 ring-white dark:ring-slate-900 mt-0.5">
                                <div class="w-2.5 h-2.5 rounded-full bg-brand-500"></div>
                            </div>
                            
                            <div class="pb-1">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white leading-snug">
                                    <?= htmlspecialchars($activity['action']) ?>
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <a href="index.php?action=view&tracking_id=<?= urlencode($activity['id']) ?>" class="font-mono text-xs font-bold text-brand-600 dark:text-brand-400 hover:underline">
                                        <?= htmlspecialchars($activity['id']) ?>
                                    </a>
                                    <span class="text-xs text-slate-400">&bull;</span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap"><?= htmlspecialchars($activity['date']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            </div>
    </div>
</div>