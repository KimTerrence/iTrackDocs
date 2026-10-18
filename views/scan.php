<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Details | iTrackDocs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 font-sans">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-6 sm:p-8 relative overflow-hidden z-10">
        
        <?php if (isset($error) && $error): ?>
            <div class="bg-red-50 text-red-600 p-5 rounded-2xl border border-red-100 text-center">
                <svg class="w-10 h-10 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h2 class="text-lg font-bold leading-tight"><?php echo htmlspecialchars($error); ?></h2>
            </div>
            
        <?php else: ?>
            <?php
                $catLabel = \Models\Document::categoryLabel($document['category'] ?? '');

                $s = strtolower($document['status'] ?? '');
                if (in_array($s, ['approved', 'received', 'completed', 'returned'])) $statusClass = 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20';
                elseif ($s === 'void') $statusClass = 'bg-red-50 text-red-700 ring-1 ring-red-600/20';
                elseif ($s === 'routing') $statusClass = 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20';
                else $statusClass = 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20';
            ?>

            <div class="text-center mb-6">
                <p class="text-slate-400 uppercase tracking-widest text-[10px] font-bold mb-2">Tracking ID</p>
                <div class="inline-flex items-center justify-center gap-2 bg-slate-50 py-2.5 px-5 rounded-xl border border-slate-200">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span class="text-xl font-black text-slate-800 font-mono tracking-tight"><?php echo htmlspecialchars($tracking_id); ?></span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-4 leading-tight break-words"><?php echo htmlspecialchars($document['title']); ?></h1>
                <span class="inline-flex items-center mt-3 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider <?php echo $statusClass; ?>">
                    <?php echo htmlspecialchars($document['status'] ?? ''); ?>
                </span>
            </div>

            <dl class="grid grid-cols-2 gap-3 mb-6 text-left">
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                    <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Category</dt>
                    <dd class="text-sm font-semibold text-slate-800 mt-0.5"><?php echo htmlspecialchars($catLabel); ?></dd>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                    <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Current Location</dt>
                    <dd class="text-sm font-semibold text-slate-800 mt-0.5"><?php echo htmlspecialchars($document['current_location'] ?? '-'); ?></dd>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                    <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Origin</dt>
                    <dd class="text-sm font-semibold text-slate-800 mt-0.5"><?php echo htmlspecialchars($document['origin'] ?? '-'); ?></dd>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                    <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Created</dt>
                    <dd class="text-sm font-semibold text-slate-800 mt-0.5"><?php echo !empty($document['created_at']) ? date('M d, Y', strtotime($document['created_at'])) : '-'; ?></dd>
                </div>
            </dl>

            <h2 class="text-sm font-bold text-slate-900 mb-3">Routing History</h2>
            <?php if (empty($document['history'])): ?>
                <p class="text-sm text-slate-500">No history recorded yet.</p>
            <?php else: ?>
                <ol class="relative border-l-2 border-slate-200 ml-2 space-y-4">
                    <?php foreach (array_reverse($document['history']) as $log): ?>
                        <li class="ml-4">
                            <span class="absolute -left-[7px] mt-1.5 w-3 h-3 rounded-full bg-blue-600 ring-4 ring-white"></span>
                            <p class="text-xs font-bold text-blue-600"><?php echo htmlspecialchars($log['date']); ?></p>
                            <p class="text-sm text-slate-800 font-medium leading-snug"><?php echo nl2br(htmlspecialchars($log['action'])); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <p class="text-xs text-slate-400 mt-6">iTrackDocs &middot; Read-only document view</p>

</body>
</html>
