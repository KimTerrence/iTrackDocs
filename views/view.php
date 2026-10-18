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

// 4. Build the public QR URL (read-only document details)
$detailsUrl = $protocol . $domain . $path . "?action=scan&tracking_id=" . urlencode($tracking_id);

// Generate the QR Code
$qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($detailsUrl);
?>

<div class="w-full animate-fade-in-up print:hidden">
    
    <a href="index.php?action=dashboard" class="inline-flex items-center text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-500 mb-6 transition-colors">
        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        Back to Dashboard
    </a>

    <!-- Stacked on phones/tablets, two columns from xl (the sidebar column needs the room) -->
    <div class="flex flex-col xl:flex-row xl:items-start gap-6">
        
        <div class="w-full xl:flex-1 min-w-0 space-y-6">
            
            <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-5 sm:p-8 shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6 mb-8">
                    <div>
                        <span class="font-mono text-brand-600 dark:text-brand-400 font-bold px-3 py-1 bg-brand-50 dark:bg-brand-500/10 rounded-lg text-sm"><?= htmlspecialchars($tracking_id) ?></span>
                        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-2"><?= htmlspecialchars($document['title']) ?></h1>
                        <div class="mt-2.5 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400 ring-1 ring-purple-600/20">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                                <?= htmlspecialchars(\Models\Document::categoryLabel($document['category'] ?? '')) ?>
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
                            <div class="flex items-center gap-3 pl-3 pr-4 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
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
                                            echo htmlspecialchars(implode(' · ', $trackMeta) !== '' ? implode(' · ', $trackMeta) : 'No department / email');
                                        ?>
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>


            <?php if (\Models\Document::usesTemplate($document['category'] ?? '')): ?>
                <?php
                    $previewUrl = 'index.php?action=letter_preview&tracking_id=' . urlencode($tracking_id);
                    $letterSaved = file_exists(\Models\Document::letterPath($tracking_id, 'json'));
                    $hasDocx = file_exists(\Models\Document::letterPath($tracking_id));
                    // Same rule as DocumentController::canEditLetter()
                    $canEditLetter = ($document['creator_id'] == ($_SESSION['user_id'] ?? null) || strtolower($_SESSION['user_role'] ?? '') === 'admin')
                        && strtolower($document['status'] ?? '') !== 'void';
                ?>
                <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-5 sm:p-8 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Letter Preview</h2>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="index.php?action=edit_letter&tracking_id=<?= urlencode($tracking_id) ?>" class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white font-bold text-sm rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                <?= $canEditLetter ? 'Edit Letter' : 'Open Letter' ?>
                            </a>
                            <?php if ($hasDocx): ?>
                                <a href="index.php?action=letter_file&tracking_id=<?= urlencode($tracking_id) ?>&download=1" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-sm rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    Download .docx
                                </a>
                            <?php endif; ?>
                            <?php if ($letterSaved): ?>
                                <button type="button" id="letterPrintBtn" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-sm rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 002 2z" /></svg>
                                    Print
                                </button>
                                <a href="<?= htmlspecialchars($previewUrl) ?>" target="_blank" class="px-4 py-2 bg-brand-50 text-brand-700 font-bold text-sm rounded-lg hover:bg-brand-100 transition-colors">
                                    Open Full Screen
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if ($letterSaved): ?>
                        <iframe id="letterPreviewFrame" src="<?= htmlspecialchars($previewUrl) ?>" title="Letter preview" class="w-full rounded-2xl border border-slate-200 dark:border-slate-800" style="height: 600px;"></iframe>
                    <?php else: ?>
                        <p class="text-sm text-slate-500 dark:text-slate-400">No letter yet. Open the editor to start from the CICS template.</p>
                    <?php endif; ?>
                </div>
                <?php if ($letterSaved): ?>
                    <script>
                        (function () {
                            const frame = document.getElementById('letterPreviewFrame');
                            // The preview reports its height so the whole page shows without an inner scrollbar
                            window.addEventListener('message', function (e) {
                                if (e.origin === window.location.origin && e.source === frame.contentWindow && e.data && e.data.type === 'letter-preview-height') {
                                    frame.style.height = Math.ceil(e.data.height) + 'px';
                                }
                            });
                            // Prints only the letter (the preview page has its own print layout)
                            document.getElementById('letterPrintBtn').addEventListener('click', function () {
                                frame.contentWindow.focus();
                                frame.contentWindow.print();
                            });
                        })();
                    </script>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!empty($document['file_path'])): ?>
                <?php 
                    $fileExtension = strtolower(pathinfo($document['file_path'], PATHINFO_EXTENSION));
                    $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']);
                    $canPreview = $isImage || $fileExtension === 'pdf';
                ?>
                <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-5 sm:p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white"><?= $canPreview ? 'Scanned Attachment' : 'Attachment' ?></h2>
                        <a href="<?= htmlspecialchars($document['file_path']) ?>" <?= $canPreview ? 'target="_blank"' : 'download' ?> class="px-4 py-2 bg-brand-50 text-brand-700 font-bold text-sm rounded-lg hover:bg-brand-100 transition-colors">
                            <?= $canPreview ? 'Open Full Screen' : 'Download ' . strtoupper(htmlspecialchars($fileExtension)) ?>
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
                                <p class="text-slate-600 font-medium">This file type cannot be previewed. Use Download to open it.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-5 sm:p-8 shadow-sm">
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

        <div class="w-full xl:w-64 xl:min-w-[240px] xl:shrink-0 space-y-6 xl:sticky xl:top-6">
            
            <?php
                $user_id = $_SESSION['user_id'] ?? null;
                $is_sender = ($document['creator_id'] == $user_id);
                $isVoid = strtolower($document['status'] ?? '') === 'void';
            ?>

            <?php if ($isVoid): ?>
                <div class="flex items-start gap-3 bg-red-50 dark:bg-red-500/10 ring-1 ring-red-200 dark:ring-red-500/30 rounded-3xl p-5">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                    <div>
                        <p class="font-bold text-red-700 dark:text-red-300 text-sm">This document is void</p>
                        <p class="text-xs text-red-600/90 dark:text-red-300/80 mt-1 leading-relaxed">
                            It can no longer be returned, voided again or changed, and its QR can't be printed because the tracking code may be reused. You can still view and download it.
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($is_sender): ?>
                <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-6 shadow-sm">
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2">Manual Route Update</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                        You created this document. Click below to mark it as <strong>returned</strong>.
                    </p>
                    <form action="index.php?action=mark_returned" method="POST">
                        <fieldset <?= $isVoid ? 'disabled' : '' ?>>
                        <input type="hidden" name="tracking_id" value="<?= htmlspecialchars($tracking_id) ?>">
                        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2.5 rounded-xl transition-all text-sm shadow-md disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none">
                            Mark as Returned
                        </button>
                        </fieldset>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($is_sender): ?>
            <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-6 shadow-sm">
                <h3 class="font-bold text-slate-900 dark:text-white mb-2">Void Document</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                    Mark this document as <strong>void</strong>. Its tracking code will be reused for the next letter in this category.
                </p>
                <form action="index.php?action=mark_void" method="POST" onsubmit="return confirm('Are you sure you want to mark this document as VOID? Its tracking code will be given to the next letter in this category.');">
                    <fieldset <?= $isVoid ? 'disabled' : '' ?>>
                    <input type="hidden" name="tracking_id" value="<?= htmlspecialchars($tracking_id) ?>">
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 rounded-xl transition-all text-sm shadow-md disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none">
                        Mark as Void
                    </button>
                    </fieldset>
                </form>
            </div>
            <?php endif; ?>

            <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-6 shadow-sm">
                <h3 class="font-bold text-slate-900 dark:text-white mb-4">Upload Attachment</h3>
                <form action="index.php?action=upload_attachment" method="POST" enctype="multipart/form-data">
                    <fieldset class="space-y-4" <?= $isVoid ? 'disabled' : '' ?>>
                    <input type="hidden" name="tracking_id" value="<?= htmlspecialchars($tracking_id) ?>">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2">Select File</label>
                        <input type="file" name="scanned_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png" data-max-bytes="5242880" required
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-500 dark:text-slate-400
                            file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-100 file:text-brand-700 hover:file:bg-brand-200 cursor-pointer transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <p class="text-[11px] text-slate-400 mt-2 leading-snug">PDF, Word, Excel, PowerPoint, JPG or PNG · max 5MB</p>
                    </div>
                    <button type="submit" class="w-full flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-500 text-white font-bold py-2.5 rounded-xl transition-all text-sm shadow-md shadow-brand-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                        Upload File
                    </button>
                    </fieldset>
                </form>
            </div>

            <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-3xl p-6 shadow-sm">
                <h3 class="font-bold text-slate-900 dark:text-white mb-4">Quick Actions</h3>
                <button onclick="window.print()" <?= $isVoid ? 'disabled' : '' ?> class="disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none w-full flex items-center justify-center gap-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold py-3 rounded-xl transition-all border border-slate-200 dark:border-slate-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 002 2z" /></svg>
                    Print Document QR
                </button>
            </div>
            
        </div>
    </div>
</div>

<div class="hidden print:flex flex-col items-center pt-20 w-full h-screen bg-white text-black font-sans text-center">
    <div class="space-y-1 mb-8">
        <h1 class="text-2xl font-black uppercase tracking-widest text-black">Document QR</h1>
        <p class="text-lg font-bold text-gray-700">Scan to view document details</p>
    </div>

    <img src="<?= htmlspecialchars($qrCodeUrl) ?>" class="w-56 h-56 border-4 border-black p-3 mb-6" alt="Tracking QR">

    <div class="space-y-2 mt-2">
        <p class="font-mono font-bold text-2xl tracking-widest text-black"><?= htmlspecialchars($tracking_id) ?></p>
        <p class="text-base font-semibold text-gray-600 max-w-lg mx-auto"><?= htmlspecialchars($document['title']) ?></p>
    </div>
</div>