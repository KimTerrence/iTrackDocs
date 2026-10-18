<?php
// 1. Detect Protocol (Now perfectly handles Ngrok's secure tunnels!)
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $protocol = "https://";
} else {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
}

// 2. Detect Domain (Automatically grabs your Ngrok URL!)
$domain = $_SERVER['HTTP_HOST'];

// 3. Detect the exact path
$path = strtok($_SERVER["REQUEST_URI"], '?'); 

// 4. Build the final universal URL (view document details only â€” no receiving action)
$receiveUrl = $protocol . $domain . $path . "?action=view&tracking_id=" . urlencode($tracking_id);

// Generate the QR Code (links directly to the document detail page)
$qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($receiveUrl);
?>

<div class="w-full animate-fade-in-up print:hidden">
    
    <a href="index.php?action=dashboard" class="inline-flex items-center text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-500 mb-6 transition-colors">
        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        Back to Dashboard
    </a>

    <div class="flex flex-row flex-nowrap items-start gap-6" style="display:flex;flex-direction:row;flex-wrap:nowrap;align-items:flex-start;gap:1.5rem;">
        
        <div class="flex-1 min-w-0 space-y-6" style="flex:1 1 0%;min-width:0;">
            
            <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-8 shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6 mb-8">
                    <div>
                        <span class="font-mono text-brand-600 dark:text-brand-400 font-bold px-3 py-1 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-sm"><?= htmlspecialchars($tracking_id) ?></span>
                        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-2"><?= htmlspecialchars($document['title']) ?></h1>
                        <div class="mt-2.5 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400 ring-1 ring-purple-600/20">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                                <?php
                                    $cat = $document['category'] ?? 'Uncategorized';
                                    if ($cat === 'IC') echo 'IC - Internal';
                                    elseif ($cat === 'EC') echo 'EC - External';
                                    else echo htmlspecialchars($cat); ?>
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 ring-1 ring-slate-200 dark:ring-slate-700">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <?= htmlspecialchars($document['current_location']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="shrink-0 flex flex-col items-start sm:items-end gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold uppercase tracking-wider 
                            <?php 
                                $s = strtolower($document['status']);
                                if (in_array($s, ['approved', 'received', 'completed', 'returned'])) echo 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20';
                                elseif ($s === 'void') echo 'bg-red-50 text-red-700 ring-1 ring-red-600/20';
                                elseif ($s === 'routing') echo 'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20';
                                else echo 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20';
                            ?>">
                            <?= htmlspecialchars($document['status']) ?>
                        </span>

                        <?php if (!empty($creator)): ?>
                            <?php
                                $trackUserName = $creator['name'] ?? ('User #' . $document['creator_id']);
                                $trackDept = $creator['department'] ?? '';
                                $trackEmail = $creator['email'] ?? '';
                                $trackRole = $creator['role'] ?? '';
                                $initials = '';
                                foreach (preg_split('/\s+/', trim($trackUserName)) as $part) {
                                    if ($part !== '') { $initials .= strtoupper(function_exists('mb_substr') ? mb_substr($part, 0, 1) : substr($part, 0, 1)); }
                                    if (strlen($initials) >= 2) { break; }
                                }
                            ?>
                                <!-- Avatar with initials + presence dot -->
                                <div class="relative shrink-0">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-white font-bold shadow-md shadow-brand-500/25 ring-2 ring-white dark:ring-slate-900">
                                        <?= htmlspecialchars($initials !== '' ? $initials : 'U') ?>
                                    </div>
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-900" title="Active user"></span>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Tracking User</p>
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <p class="text-slate-900 dark:text-white font-bold text-sm truncate max-w-[160px]"><?= htmlspecialchars($trackUserName) ?></p>
                                        <?php if (!empty($trackRole)): ?>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-brand-100 dark:bg-brand-500/15 text-brand-700 dark:text-brand-400">
                                                <?= htmlspecialchars(ucfirst($trackRole)) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 truncate max-w-[180px]">
                                        <?php
                                            $trackMeta = array_filter([$trackDept, $trackEmail]);
                                            echo htmlspecialchars(implode(' Â· ', $trackMeta) !== '' ? implode(' Â· ', $trackMeta) : 'No department / email');
                                            echo htmlspecialchars(implode(" ", $trackMeta) !== "" ? implode(" ", $trackMeta) : "No department / email"); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>


            <?php if (!empty($document['file_path'])): ?>
                <?php 
                    $fileExtension = strtolower(pathinfo($document['file_path'], PATHINFO_EXTENSION));
                    $isImage = in_array($fileExtension, ["jpg", "jpeg", "png", "gif"]); ?>
                <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Scanned Attachment</h2>
                        <a href="<?= htmlspecialchars($document['file_path']) ?>" target="_blank" class="px-4 py-2 bg-brand-50 text-brand-700 font-bold text-sm rounded-lg hover:bg-brand-100 transition-colors">
                            Open Full Screen
                        </a>
                    </div>
                    
                    <div class="bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex items-center justify-center p-2">
                        <?php if ($isImage): ?>
                            <img src="<?= htmlspecialchars($document['file_path']) ?>?v=<?= time() ?>" alt="Scanned Document" class="max-w-full h-auto rounded-xl shadow-sm">
                        <?php elseif ($fileExtension === 'pdf'): ?>
                            <iframe src="<?= htmlspecialchars($document['file_path']) ?>?v=<?= time() ?>" class="w-full h-[600px] rounded-xl" type="application/pdf"></iframe>
                        <?php else: ?>
                            <div class="py-12 text-center">
                                <svg class="w-12 h-12 text-slate-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <p class="text-slate-600 font-medium">This file type cannot be previewed.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-8 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Audit Trail & Routing History</h2>
                <div class="space-y-6 relative before:absolute before:inset-0 before:ml-4 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 dark:before:via-slate-700 before:to-transparent">
                    <?php foreach (array_reverse($document['history']) as $log): ?>
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full border border-white dark:border-slate-900 bg-brand-100 dark:bg-brand-900 text-brand-500 dark:text-brand-400 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10">
                                <div class="w-2.5 h-2.5 rounded-full bg-brand-600 dark:bg-brand-500"></div>
                            </div>
                            
                            <div class="w-[calc(100%-3rem)] md:w-[calc(50%-2.5rem)] bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-bold text-brand-600 dark:text-brand-400"><?= htmlspecialchars($log['date']) ?></span>
                                </div>
                                <p class="text-slate-900 dark:text-white text-sm font-medium leading-snug">
                                    <?= nl2br(htmlspecialchars($log['action'])) ?>
                                                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        </div>

        <div class="w-64 min-w-[240px] shrink-0 space-y-6 sticky top-6" style="flex:0 0 auto;width:16rem;min-width:240px;position:sticky;top:1.5rem;">
            
            

            <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-6 shadow-sm">
                <h3 class="font-bold text-slate-900 dark:text-white mb-2">Void Document</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                    Mark this document as <strong>void</strong>. This action cannot be undone easily.
                </p>
                <form action="index.php?action=mark_void" method="POST" onsubmit="return confirm('Are you sure you want to mark this document as VOID?');">
                    <input type="hidden" name="tracking_id" value="<?= htmlspecialchars($tracking_id) ?>">
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl transition-all text-sm shadow-md">
                        Mark as Void
                    </button>
                </form>
            <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-6 shadow-sm">
                <h3 class="font-bold text-slate-900 dark:text-white mb-4">Upload Attachment</h3>
                <form action="index.php?action=upload_attachment" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="tracking_id" value="<?= htmlspecialchars($tracking_id) ?>">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2">Select File</label>
                        <input type="file" name="scanned_file" accept=".pdf,.jpg,.jpeg,.png" required 
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-500 dark:text-slate-400 
                            file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-100 file:text-brand-700 hover:file:bg-brand-200 cursor-pointer transition-all">
                    </div>
                    <button type="submit" class="w-full flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-500 text-white font-bold py-2.5 rounded-xl transition-all text-sm shadow-md shadow-brand-500/20">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                        Upload File
                    </button>
                </form>
            </div>

            <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-6 shadow-sm">
                <h3 class="font-bold text-slate-900 dark:text-white mb-4">Quick Actions</h3>
                <button onclick="window.print()" class="w-full flex items-center justify-center gap-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold py-3 rounded-xl transition-all border border-slate-200 dark:border-slate-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 002 2z" /></svg>
                    Print Routing QR
                </button>
            </div>
            
        </div>
    </div>
</div>

<div class="hidden print:flex flex-col items-center pt-20 w-full h-screen bg-white text-black font-sans text-center">
    <div class="space-y-1 mb-8">
        <h1 class="text-2xl font-black uppercase tracking-widest text-black">Document Details</h1>
        <p class="text-lg font-bold text-gray-700">View Online</p>
    </div>

    <img src="<?= htmlspecialchars($qrCodeUrl) ?>" class="w-56 h-56 border-4 border-black p-3 mb-6" alt="Tracking QR">

    <div class="space-y-2 mt-2">
        <p class="font-mono font-bold text-2xl tracking-widest text-black"><?= htmlspecialchars($tracking_id) ?></p>
        <p class="text-base font-semibold text-gray-600 max-w-lg mx-auto"><?= htmlspecialchars($document['title']) ?></p>
    </div>
</div>

