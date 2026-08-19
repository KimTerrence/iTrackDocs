<?php if (isset($_GET['error']) && $_GET['error'] === 'access_denied'): ?>
    <div id="accessDeniedPopup" class="fixed top-10 left-1/2 transform -translate-x-1/2 z-50 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-400 px-6 py-4 rounded-xl shadow-2xl flex items-center gap-4 min-w-[320px] animate-fade-in-down">
        
        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>

        <div>
            <h4 class="font-extrabold text-lg">Access Denied</h4>
            <p class="text-sm font-medium opacity-90">Please log in to view the dashboard.</p>
        </div>

        <button onclick="document.getElementById('accessDeniedPopup').style.display='none'" class="ml-auto text-red-500 hover:text-red-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
<?php endif; ?>

<div class="animate-fade-in-up">
    
    <section id="home" class="relative pt-20 pb-32 text-center overflow-hidden">
        <div class="absolute top-0 left-1/2 w-full -translate-x-1/2 h-full z-0 pointer-events-none">
            <div class="absolute top-10 left-1/4 w-96 h-96 bg-brand-400/20 dark:bg-brand-500/10 rounded-full blur-3xl mix-blend-multiply"></div>
            <div class="absolute top-20 right-1/4 w-96 h-96 bg-indigo-400/20 dark:bg-indigo-500/10 rounded-full blur-3xl mix-blend-multiply"></div>
        </div>

        <div class="relative z-10 max-w-4xl mx-auto px-6">
            <h1 class="text-5xl md:text-7xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-6">
                Smart Document Coding <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-600 dark:from-brand-400 dark:to-indigo-400">and Monitoring Platform</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 mb-10 max-w-2xl mx-auto">
                Securely route, monitor, and manage your enterprise documents in real-time using QR codes and automated workflows.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#tracking" class="w-full sm:w-auto px-8 py-3.5 bg-brand-600 hover:bg-brand-500 text-white font-bold rounded-xl shadow-lg shadow-brand-500/30 transition-all active:scale-95 text-center">
                    Track a Document
                </a>
                <a href="#features" class="w-full sm:w-auto px-8 py-3.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold rounded-xl ring-1 ring-slate-200 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm transition-all text-center">
                    Learn More
                </a>
            </div>
        </div>
    </section>

    <section id="tracking" class="relative z-20 max-w-3xl mx-auto px-6 -mt-16 mb-24">
        <div class="bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 shadow-2xl rounded-3xl p-8 md:p-12 overflow-hidden transition-all duration-500">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2 text-center">Track Your Document</h2>
            <p class="text-slate-500 dark:text-slate-400 text-center text-sm mb-8">Enter your tracking ID to see its current status and history.</p>
            
            <form id="trackForm" class="flex flex-col sm:flex-row gap-3 relative z-10">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input type="text" name="tracking_id" id="tracking_input" placeholder="e.g., DOC-2026-A1B2" required
                        class="w-full pl-11 pr-4 py-4 bg-slate-50 dark:bg-slate-950 border-0 ring-1 ring-inset ring-slate-300 dark:ring-slate-700/50 rounded-xl focus:ring-2 focus:ring-brand-500 text-slate-900 dark:text-white font-mono uppercase transition-all">
                </div>
                
                <button type="submit" id="trackSubmitBtn" class="px-8 py-4 bg-slate-900 dark:bg-brand-600 hover:bg-slate-800 dark:hover:bg-brand-500 text-white font-bold rounded-xl transition-all shadow-md flex items-center justify-center min-w-[120px]">
                    <span>Search</span>
                </button>
            </form>

            <div id="trackResults" class="hidden mt-8 pt-8 border-t border-slate-100 dark:border-slate-800 text-left">
                </div>
        </div>
    </section>

    <script>
        document.getElementById('trackForm').addEventListener('submit', async function(e) {
            e.preventDefault(); // Stop page reload
            
            const input = document.getElementById('tracking_input').value.trim();
            const btn = document.getElementById('trackSubmitBtn');
            const resultsDiv = document.getElementById('trackResults');
            
            if (!input) return;

            // 1. Loading State
            btn.disabled = true;
            btn.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>`;
            resultsDiv.classList.add('hidden');

            try {
                // 2. Fetch data from our new API endpoint
                const response = await fetch(`index.php?action=api_track&tracking_id=${encodeURIComponent(input)}`);
                const data = await response.json();

                // 3. Render Results
                resultsDiv.classList.remove('hidden');
                resultsDiv.classList.add('animate-fade-in-up');

                if (data.error) {
                    resultsDiv.innerHTML = `
                        <div class="bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 p-4 rounded-2xl text-center">
                            <h3 class="font-bold">Document Not Found</h3>
                            <p class="text-sm mt-1">Please check the ID and try again.</p>
                        </div>`;
                } else {
                    // Build the Timeline HTML
                    let historyHtml = '';
                    if (data.history && data.history.length > 0) {
                        data.history.reverse().forEach(log => {
                            historyHtml += `
                                <div class="flex gap-4 relative">
                                    <div class="w-6 h-6 rounded-full bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center shrink-0 z-10">
                                        <div class="w-2 h-2 rounded-full bg-brand-500"></div>
                                    </div>
                                    <div class="pb-6 border-l-2 border-slate-100 dark:border-slate-800 absolute left-3 top-3 h-full z-0"></div>
                                    <div class="pb-6 w-full z-10 bg-white dark:bg-slate-900">
                                        <p class="text-slate-900 dark:text-white font-bold text-sm">${log.action}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">${log.date}</p>
                                    </div>
                                </div>`;
                        });
                    }

                    resultsDiv.innerHTML = `
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 dark:text-white">${data.title}</h3>
                                <p class="text-sm font-bold text-brand-600 dark:text-brand-400 mt-1">Current: ${data.current_location}</p>
                            </div>
                            <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-full text-xs font-bold uppercase tracking-wider border border-slate-200 dark:border-slate-700">
                                ${data.status}
                            </span>
                        </div>
                        <div class="space-y-0 relative">
                            ${historyHtml}
                        </div>
                    `;
                }
            } catch (err) {
                resultsDiv.classList.remove('hidden');
                resultsDiv.innerHTML = `<div class="text-red-500 text-center text-sm font-bold">A network error occurred. Please try again.</div>`;
            } finally {
                // Restore button
                btn.disabled = false;
                btn.innerHTML = `<span>Search</span>`;
            }
        });
    </script>

    <section id="features" class="py-20 border-t border-slate-100 dark:border-slate-800/50">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">Enterprise Features</h2>
                <p class="mt-4 text-slate-500 dark:text-slate-400">Everything you need to digitize your routing workflow.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl ring-1 ring-slate-200 dark:ring-slate-800 shadow-sm hover:shadow-xl transition-all group">
                    <div class="w-12 h-12 bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">QR Tracking</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">Instantly update document locations by scanning auto-generated QR codes directly from your smartphone.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl ring-1 ring-slate-200 dark:ring-slate-800 shadow-sm hover:shadow-xl transition-all group">
                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Real-Time Routing</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">Watch documents move through the system live. Track bottlenecks and maintain strict SLA compliance.</p>
                </div>
                <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl ring-1 ring-slate-200 dark:ring-slate-800 shadow-sm hover:shadow-xl transition-all group">
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Smart Notifications</h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed">Receive instant in-app alerts the moment your requested documents arrive at their destination.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="py-20 bg-slate-50 dark:bg-zinc-900/50">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">How It Works</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 relative">
                <div class="hidden lg:block absolute top-12 left-24 right-24 h-0.5 bg-slate-200 dark:bg-slate-800 z-0"></div>

                <div class="relative z-10 text-center">
                    <div class="w-24 h-24 mx-auto bg-white dark:bg-slate-900 rounded-full flex items-center justify-center text-2xl font-black text-brand-600 dark:text-brand-400 shadow-xl ring-1 ring-slate-200 dark:ring-slate-800 mb-6">1</div>
                    <h4 class="font-bold text-slate-900 dark:text-white mb-2">Create</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Log in and input the initial document details.</p>
                </div>
                <div class="relative z-10 text-center">
                    <div class="w-24 h-24 mx-auto bg-white dark:bg-slate-900 rounded-full flex items-center justify-center text-2xl font-black text-brand-600 dark:text-brand-400 shadow-xl ring-1 ring-slate-200 dark:ring-slate-800 mb-6">2</div>
                    <h4 class="font-bold text-slate-900 dark:text-white mb-2">Generate QR</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Print the auto-generated routing slip and attach it.</p>
                </div>
                <div class="relative z-10 text-center">
                    <div class="w-24 h-24 mx-auto bg-white dark:bg-slate-900 rounded-full flex items-center justify-center text-2xl font-black text-brand-600 dark:text-brand-400 shadow-xl ring-1 ring-slate-200 dark:ring-slate-800 mb-6">3</div>
                    <h4 class="font-bold text-slate-900 dark:text-white mb-2">Route & Scan</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Receiving desks scan the QR to update its location.</p>
                </div>
                <div class="relative z-10 text-center">
                    <div class="w-24 h-24 mx-auto bg-white dark:bg-slate-900 rounded-full flex items-center justify-center text-2xl font-black text-brand-600 dark:text-brand-400 shadow-xl ring-1 ring-slate-200 dark:ring-slate-800 mb-6">4</div>
                    <h4 class="font-bold text-slate-900 dark:text-white mb-2">Track Live</h4>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Monitor the full audit trail securely online.</p>
                </div>
            </div>
        </div>
    </section>

</div>