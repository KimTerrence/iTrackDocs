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
                <input type="hidden" name="location" id="gps_location" value="">

                <div class="space-y-4 mb-6" id="input_group">
                    <div class="relative">
                        <input type="text" id="receiver_name" name="receiver_name" required placeholder=" " class="peer w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 pt-5 pb-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white outline-none transition-all placeholder-transparent">
                        <label for="receiver_name" class="peer-label absolute left-4 top-3.5 text-slate-400 text-sm font-medium transition-all duration-200 pointer-events-none transform origin-left">Received By (Name)</label>
                    </div>

                    <div class="relative">
                        <input type="text" id="receiver_department" name="receiver_department" required placeholder=" " class="peer w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 pt-5 pb-2 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white outline-none transition-all placeholder-transparent">
                        <label for="receiver_department" class="peer-label absolute left-4 top-3.5 text-slate-400 text-sm font-medium transition-all duration-200 pointer-events-none transform origin-left">Department</label>
                    </div>
                </div>

                <div class="mb-6 min-h-[56px]" id="gps_container">
                    <button type="button" id="request_gps_btn" class="w-full bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-sm py-3.5 px-4 rounded-xl border border-amber-200 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Click to Verify Location
                    </button>
                </div>

                <button type="submit" id="submit_btn" disabled class="w-full bg-slate-100 text-slate-400 cursor-not-allowed font-bold text-lg py-4 px-6 rounded-xl transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Receive Document
                </button>
            </form>
        <?php endif; ?>
    </div>

    <div id="help_modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div class="bg-white w-full max-w-sm rounded-3xl shadow-2xl p-6 text-center animate-fade-in-up">
            <div class="w-16 h-16 bg-red-50 border border-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            
            <h3 id="modal_title" class="text-xl font-black text-slate-900 mb-2">Location Error</h3>
            <div id="modal_text" class="text-slate-500 text-sm mb-6 leading-relaxed">
                Could not acquire GPS.
            </div>
            
            <div class="flex gap-3">
                <button onclick="closeModal()" class="flex-1 bg-slate-50 border border-slate-200 text-slate-600 font-bold py-3 rounded-xl hover:bg-slate-100 transition-colors">Close</button>
                <button onclick="location.reload()" class="flex-1 bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition-colors shadow-md shadow-blue-500/20">Reload Page</button>
            </div>
        </div>
    </div>

    <script>
        const helpModal = document.getElementById('help_modal');
        const modalTitle = document.getElementById('modal_title');
        const modalText = document.getElementById('modal_text');

        function openModal() {
            helpModal.classList.remove('hidden');
        }

        function closeModal() {
            helpModal.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const gpsInput = document.getElementById('gps_location');
            const requestBtn = document.getElementById('request_gps_btn');
            const gpsContainer = document.getElementById('gps_container');
            const submitBtn = document.getElementById('submit_btn');
            const inputs = document.querySelectorAll('#input_group input');

            function toggleInputs(disabled) {
                inputs.forEach(input => {
                    input.disabled = disabled;
                    if (disabled) input.classList.add('opacity-50');
                    else input.classList.remove('opacity-50');
                });
            }

            window.requestLocation = function() {
                // Change UI to Loading State
                toggleInputs(true);
                gpsContainer.innerHTML = `
                    <div class="w-full bg-slate-50 text-slate-500 font-medium text-sm py-3.5 px-4 rounded-xl border border-slate-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 animate-spin text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Acquiring signal...
                    </div>
                `;

                if (!window.isSecureContext) {
                    toggleInputs(false);
                    gpsContainer.innerHTML = `
                        <div class="text-sm font-bold text-red-700 bg-red-50 p-4 rounded-xl border border-red-200 text-center">
                            ⚠️ Insecure HTTP connection.
                        </div>`;
                    return;
                }

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        // SUCCESS Function
                        function(position) {
                            toggleInputs(false);
                            const lat = position.coords.latitude.toFixed(6);
                            const lng = position.coords.longitude.toFixed(6);
                            
                            gpsInput.value = `${lat}, ${lng}`;
                            
                            gpsContainer.innerHTML = `
                                <div class="w-full bg-emerald-50 text-emerald-700 font-bold text-sm py-3.5 px-4 rounded-xl border border-emerald-200 flex items-center justify-center gap-2 transition-all">
                                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 
                                    GPS Verified
                                </div>
                            `;

                            submitBtn.disabled = false;
                            submitBtn.className = "w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-lg py-4 px-6 rounded-xl shadow-[0_4px_14px_0_rgba(37,99,235,0.39)] transition-all transform active:scale-[0.98] flex items-center justify-center gap-2 cursor-pointer";
                            submitBtn.innerHTML = `Receive Document`;
                        },
                        // ERROR Function
                        function(error) {
                            toggleInputs(false);
                            gpsInput.value = "";
                            
                            let errorMsg = "Location access failed.";
                            
                            if (error.code === 1) {
                                errorMsg = "Permission denied by browser.";
                                modalTitle.innerText = "Enable Browser Location";
                                modalText.innerHTML = `
                                    <div class="space-y-3 mt-4">
                                        <div class="flex items-center gap-3 text-left bg-slate-50 p-3 rounded-xl border border-slate-200 shadow-sm">
                                            <div class="shrink-0 w-8 h-8 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center font-black text-sm">1</div>
                                            <p class="text-sm text-slate-600 leading-tight">Tap the <strong class="text-slate-900">Lock icon 🔒</strong> in your address bar.</p>
                                        </div>
                                        <div class="flex items-center gap-3 text-left bg-slate-50 p-3 rounded-xl border border-slate-200 shadow-sm">
                                            <div class="shrink-0 w-8 h-8 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center font-black text-sm">2</div>
                                            <p class="text-sm text-slate-600 leading-tight">Tap <strong class="text-slate-900">Permissions</strong> or <strong class="text-slate-900">Site Settings</strong>.</p>
                                        </div>
                                        <div class="flex items-center gap-3 text-left bg-slate-50 p-3 rounded-xl border border-slate-200 shadow-sm">
                                            <div class="shrink-0 w-8 h-8 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center font-black text-sm">3</div>
                                            <p class="text-sm text-slate-600 leading-tight">Set Location to <strong class="text-slate-900">Allow</strong> and hit Reload.</p>
                                        </div>
                                    </div>
                                `;
                            } else if (error.code === 2 || error.code === 3) {
                                errorMsg = "GPS signal lost or turned off.";
                                modalTitle.innerText = "Turn On Device GPS";
                                modalText.innerHTML = `
                                    <div class="space-y-3 mt-4">
                                        <div class="flex items-center gap-3 text-left bg-slate-50 p-3 rounded-xl border border-slate-200 shadow-sm">
                                            <div class="shrink-0 w-8 h-8 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center font-black text-sm">1</div>
                                            <p class="text-sm text-slate-600 leading-tight">Swipe down from the very top of your screen.</p>
                                        </div>
                                        <div class="flex items-center gap-3 text-left bg-slate-50 p-3 rounded-xl border border-slate-200 shadow-sm">
                                            <div class="shrink-0 w-8 h-8 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center font-black text-sm">2</div>
                                            <p class="text-sm text-slate-600 leading-tight">Tap the <strong class="text-slate-900">Location / GPS</strong> toggle to turn it on.</p>
                                        </div>
                                        <div class="flex items-center gap-3 text-left bg-slate-50 p-3 rounded-xl border border-slate-200 shadow-sm">
                                            <div class="shrink-0 w-8 h-8 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center font-black text-sm">3</div>
                                            <p class="text-sm text-slate-600 leading-tight">Tap the <strong class="text-slate-900">Reload Page</strong> button below.</p>
                                        </div>
                                    </div>
                                `;
                            }
                            
                            openModal();
                            
                            gpsContainer.innerHTML = `
                                <div class="w-full bg-red-50 p-3 rounded-xl border border-red-200 flex flex-col items-center gap-2">
                                    <span class="text-xs font-bold text-red-700 text-center">${errorMsg}</span>
                                    <button type="button" onclick="openModal()" class="text-[10px] uppercase tracking-wider font-bold bg-white text-red-600 px-3 py-1.5 rounded-lg border border-red-200 shadow-sm hover:bg-red-50 transition-colors">
                                        View Instructions
                                    </button>
                                </div>
                            `;
                            
                            submitBtn.disabled = true;
                            submitBtn.className = "w-full bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed font-bold text-lg py-4 px-6 rounded-xl transition-all flex items-center justify-center gap-2";
                            submitBtn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg> LOCKED`;
                        }, // <-- This is where the missing comma/bracket was!
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                } else {
                    toggleInputs(false);
                    gpsContainer.innerHTML = `<div class="text-sm font-bold text-red-700 bg-red-50 p-3 rounded-xl border border-red-200 text-center">GPS Not Supported</div>`;
                }
            };

            if(requestBtn) {
                requestBtn.addEventListener('click', window.requestLocation);
            }
        });
    </script>
</body>
</html>