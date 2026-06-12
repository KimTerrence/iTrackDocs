<div class="max-w-3xl mx-auto animate-fade-in-up mt-10">
    <a href="index.php" class="text-brand-600 dark:text-brand-400 font-semibold text-sm mb-6 inline-block">&larr; Back to Search</a>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 p-6 rounded-2xl text-center">
            <h2 class="text-xl font-bold mb-2">Document Not Found</h2>
            <p><?= htmlspecialchars($error) ?></p>
        </div>
    <?php else: ?>
        <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-8 shadow-xl">
            <div class="border-b border-slate-100 dark:border-slate-800 pb-6 mb-8 text-center">
                <span class="inline-block bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 font-mono font-bold px-4 py-1.5 rounded-lg mb-4 ring-1 ring-brand-600/20"><?= htmlspecialchars($tracking_id) ?></span>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white mb-2"><?= htmlspecialchars($document['title']) ?></h1>
                <p class="text-slate-500">Currently at: <strong class="text-slate-900 dark:text-white"><?= htmlspecialchars($document['current_location']) ?></strong></p>
            </div>

            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Routing Timeline</h3>
            <div class="space-y-6">
                <?php foreach (array_reverse($document['history']) as $log): ?>
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 rounded-full bg-brand-500 mt-1.5"></div>
                            <div class="w-px h-full bg-slate-200 dark:bg-slate-700 my-1"></div>
                        </div>
                        <div class="pb-6">
                            <p class="text-slate-900 dark:text-white font-semibold text-sm"><?= htmlspecialchars($log['action']) ?></p>
                            <p class="text-slate-500 dark:text-slate-400 text-xs mt-1"><?= htmlspecialchars($log['date']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>