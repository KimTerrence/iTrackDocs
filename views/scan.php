<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Receive Document | iTrackDocs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom animation for the floating labels */
        .peer:focus ~ .peer-label,
        .peer:not(:placeholder-shown) ~ .peer-label {
            transform: scale(0.85) translateY(-1rem);
            color: #3b82f6; /* text-blue-500 */
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 font-sans">

    <div class="w-full max-w-sm bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-8 text-center relative overflow-hidden z-10">
        
        <?php if (isset($error) && $error): ?>
            <div class="bg-red-50 text-red-600 p-5 rounded-2xl mb-4 border border-red-100">
                <svg class="w-10 h-10 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h2 class="text-lg font-bold leading-tight"><?php echo htmlspecialchars($error); ?></h2>
            </div>
            
        <?php elseif (isset($success) && $success): ?>
            <div class="bg-emerald-50 text-emerald-700 p-6 rounded-2xl border border-emerald-100 relative overflow-hidden">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-100 rounded-full opacity-50"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-white shadow-sm rounded-full flex items-center justify-center mx-auto mb-4 border border-emerald-100">
                        <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h2 class="text-2xl font-black mb-1">Received!</h2>
                    <p class="text-emerald-600/80 font-medium text-sm">The audit trail has been updated.</p>
                </div>
            </div>
            
        <?php else: ?>
            <div class="mb-8">
                <p class="text-slate-400 uppercase tracking-widest text-[10px] font-bold mb-2">Tracking ID</p>
                <div class="inline-flex items-center justify-center gap-2 bg-slate-50 py-2.5 px-5 rounded-xl border border-slate-200">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <h1 class="text-xl font-black text-slate-800 font-mono tracking-tight">
                        <?php echo htmlspecialchars($tracking_id ?? ''); ?>
                    </h1>
                </div>
            </div>

            <form method="POST" action="index.php?action=mark_received" class="text-left" id="receiveForm">
                
                <input type="hidden" name="tracking_id" value="<?php echo htmlspecialchars($tracking_id ?? ''); ?>">

                <div class="space-y-4 mb-6">
                    <div class="relative">
                        <input type="text" id="receiver_name" name="receiver_name" required placeholder=" " class="peer w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 pt-5 pb-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white outline-none transition-all placeholder-transparent">
                        <label for="receiver_name" class="peer-label absolute left-4 top-3.5 text-slate-400 text-sm font-medium transition-all duration-200 pointer-events-none transform origin-left">Received By (Name)</label>
                    </div>

                    <div class="relative">
                        <input type="text" id="office_input" name="location" required placeholder=" " class="peer w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 pt-5 pb-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white outline-none transition-all placeholder-transparent">
                        <label for="office_input" class="peer-label absolute left-4 top-3.5 text-slate-400 text-sm font-medium transition-all duration-200 pointer-events-none transform origin-left">Receiving Office</label>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-lg py-4 px-6 rounded-xl shadow-[0_4px_14px_0_rgba(37,99,235,0.39)] transition-all transform active:scale-[0.98] flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h6a1 1 0 01.707.293L19 8.586a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Receive Document
                </button>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>
