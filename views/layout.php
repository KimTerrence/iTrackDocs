<!DOCTYPE html>
<html lang="en" class="light"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iTrackDocs | Smart Document Coding</title>
    
    <script>
        // Check for Dark Mode Preference
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        // Custom Brand Colors
        tailwind.config = { 
            darkMode: 'class',
            theme: { 
                extend: { 
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { 
                        brand: { 50: '#eff6ff', 100: '#dbeafe', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' } 
                    } 
                } 
            } 
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-zinc-950 text-slate-900 dark:text-slate-300 font-sans min-h-screen transition-colors duration-200 flex flex-col">
    
    <?php require __DIR__ . '/partials/navbar.php'; ?>

    <?php if (isset($_SESSION['user_name'])): ?>
        
        <div class="flex w-full flex-1">
            <?php require __DIR__ . '/partials/sidebar.php'; ?>
            
            <main class="flex-1 w-full overflow-hidden p-6 lg:p-10">
                <?= $content ?? '' ?>
            </main>
        </div>

    <?php else: ?>
        
        <main class="w-full flex-1">
            <?= $content ?? '' ?>
        </main>

    <?php endif; ?>

    <?php if (!isset($_SESSION['user_name'])): ?>
        <?php require __DIR__ . '/partials/footer.php'; ?>
    <?php endif; ?>

    <?php require __DIR__ . '/partials/login_modal.php'; ?>

    <script>
        // === THEME TOGGLE ===
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (themeToggleDarkIcon && themeToggleLightIcon) {
            if (document.documentElement.classList.contains('dark')) {
                themeToggleLightIcon.classList.remove('hidden');
            } else {
                themeToggleDarkIcon.classList.remove('hidden');
            }

            var themeToggleBtn = document.getElementById('theme-toggle');
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    themeToggleDarkIcon.classList.toggle('hidden');
                    themeToggleLightIcon.classList.toggle('hidden');

                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    }
                });
            }
        }

        const loginModal = document.getElementById('login-modal');
        function openLoginModal() {
            if(loginModal) {
                loginModal.classList.remove('hidden');
                loginModal.classList.add('flex');
                setTimeout(() => { document.querySelector('input[name="email"]')?.focus(); }, 50);
            }
        }
        function closeLoginModal() {
            if(loginModal) {
                loginModal.classList.add('hidden');
                loginModal.classList.remove('flex');
            }
        }

        // === GLOBAL DROPDOWN UTILITY ===
        function setupDropdown(btnId, menuId) {
            var btn = document.getElementById(btnId);
            var menu = document.getElementById(menuId);
            if (!btn || !menu) return;
            
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.classList.toggle('hidden');
            });
        }

        // Close all dropdowns when clicking outside
        document.addEventListener('click', function() {
            document.querySelectorAll('[id$="-menu"], [id$="-dropdown"], [id="search-results"]').forEach(function(el) {
                if (!el.classList.contains('hidden')) {
                    el.classList.add('hidden');
                }
            });
        });

        // === MOBILE HAMBURGER MENU ===
        var mobileBtn = document.getElementById('mobile-menu-btn');
        var sidebar = document.querySelector('aside');
        if (mobileBtn && sidebar) {
            mobileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('hidden');
                sidebar.classList.toggle('flex');
                sidebar.classList.toggle('fixed');
                sidebar.classList.toggle('inset-0');
                sidebar.classList.toggle('z-40');
                sidebar.classList.toggle('w-72');
                sidebar.classList.toggle('h-full');
                document.getElementById('menu-hamburger-icon')?.classList.toggle('hidden');
                document.getElementById('menu-close-icon')?.classList.toggle('hidden');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(e) {
                if (window.innerWidth < 1024 && !sidebar.classList.contains('hidden')) {
                    if (!sidebar.contains(e.target) && e.target !== mobileBtn && !mobileBtn.contains(e.target)) {
                        sidebar.classList.add('hidden');
                        sidebar.classList.remove('flex', 'fixed', 'inset-0', 'z-40', 'w-72', 'h-full');
                        document.getElementById('menu-hamburger-icon')?.classList.remove('hidden');
                        document.getElementById('menu-close-icon')?.classList.add('hidden');
                    }
                }
            });
        }

        // === USER MENU DROPDOWN ===
        setupDropdown('user-menu-btn', 'user-menu-dropdown');

        // === QUICK ACTIONS DROPDOWN ===
        setupDropdown('quick-actions-btn', 'quick-actions-menu');

        // === NOTIFICATIONS DROPDOWN ===
        setupDropdown('notif-btn', 'notif-menu');

        // === GLOBAL SEARCH ===
        var searchInput = document.getElementById('global-search');
        var searchResults = document.getElementById('search-results');
        var searchTimeout = null;

        if (searchInput && searchResults) {
            searchInput.addEventListener('input', function() {
                var query = this.value.trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length < 1) {
                    searchResults.classList.add('hidden');
                    return;
                }

                searchTimeout = setTimeout(function() {
                    fetch('index.php?action=api_search&q=' + encodeURIComponent(query))
                        .then(function(res) { return res.json(); })
                        .then(function(data) {
                            searchResults.innerHTML = '';
                            
                            if (!data || data.length === 0) {
                                searchResults.innerHTML = '<div class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400">No documents found</div>';
                            } else {
                                data.forEach(function(doc) {
                                    var statusColor = 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400';
                                    if (['approved','received','completed'].indexOf(doc.status.toLowerCase()) !== -1) {
                                        statusColor = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400';
                                    } else if (doc.status.toLowerCase() === 'returned') {
                                        statusColor = 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400';
                                    }
                                    
                                    var item = document.createElement('a');
                                    item.href = doc.url;
                                    item.className = 'flex items-center gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors border-b border-slate-100 dark:border-zinc-800 last:border-0';
                                    item.innerHTML = '<div class="flex-1 min-w-0"><p class="text-sm font-semibold text-slate-900 dark:text-white truncate">' + doc.title + '</p><div class="flex items-center gap-2 mt-0.5"><span class="font-mono text-xs text-brand-600 dark:text-brand-400">' + doc.tracking_id + '</span><span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold ' + statusColor + '">' + doc.status + '</span></div></div>';
                                    searchResults.appendChild(item);
                                });
                            }
                            
                            searchResults.classList.remove('hidden');
                        })
                        .catch(function() {
                            searchResults.innerHTML = '<div class="px-4 py-6 text-center text-sm text-red-500">Search error. Try again.</div>';
                            searchResults.classList.remove('hidden');
                        });
                }, 300);
            });

            // Prevent search from closing when clicking inside the results
            searchResults.addEventListener('click', function(e) {
                e.stopPropagation();
            });
            
            // Close search on escape
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    searchResults.classList.add('hidden');
                    this.blur();
                }
            });
        }

        // === NOTIFICATIONS (Check for pending/returned docs) ===
        function checkNotifications() {
            var notifBadge = document.getElementById('notif-badge');
            var notifList = document.getElementById('notif-list');
            if (!notifBadge || !notifList) return;
            
            // Fetch dashboard data or check via a simple endpoint
            fetch('index.php?action=api_track&tracking_id=check')
                .then(function(res) { return res.json(); })
                .catch(function() {
                    // If it fails, show demo notifications based on page context
                    var pendingItems = document.querySelectorAll('[class*="pending"]').length;
                    var returnedItems = document.querySelectorAll('[class*="returned"]').length;
                    var total = pendingItems + returnedItems;
                    
                    if (total > 0) {
                        notifBadge.classList.remove('hidden');
                        notifBadge.textContent = total > 9 ? '9+' : total;
                        notifList.innerHTML = '';
                        
                        if (returnedItems > 0) {
                            notifList.innerHTML += '<div class="px-4 py-3 border-b border-slate-100 dark:border-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-800"><p class="text-sm font-semibold text-orange-600 dark:text-orange-400">' + returnedItems + ' returned document(s)</p><p class="text-xs text-slate-500 mt-0.5">Requires attention</p></div>';
                        }
                        if (pendingItems > 0) {
                            notifList.innerHTML += '<div class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-zinc-800"><p class="text-sm font-semibold text-amber-600 dark:text-amber-400">' + pendingItems + ' pending document(s)</p><p class="text-xs text-slate-500 mt-0.5">Awaiting action</p></div>';
                        }
                    } else {
                        notifList.innerHTML = '<div class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">No new notifications</div>';
                    }
                });
        }
        
        // Run notification check on page load
        if (document.getElementById('notif-badge')) {
            setTimeout(checkNotifications, 1000);
        }

        // === STOP DROPDOWN CLOSE ON INNER CLICKS ===
        document.querySelectorAll('#user-menu-dropdown, #quick-actions-menu, #notif-menu').forEach(function(el) {
            if (el) {
                el.addEventListener('click', function(e) { e.stopPropagation(); });
            }
        });
    </script>
</body>
</html>