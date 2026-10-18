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

    <style>
        /* Mobile sidebar drawer: opened by the hamburger button, floats over the page under the navbar */
        @media (max-width: 1023.98px) {
            aside.sidebar-open {
                display: flex !important; position: fixed !important; top: 73px; left: 0; bottom: 0;
                height: auto !important; width: 18rem; max-width: 85vw; z-index: 40;
                background: #fff; box-shadow: 0 10px 40px rgba(15, 23, 42, .35);
            }
            .dark aside.sidebar-open { background: #18181b; }
        }
    </style>

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
            <div id="sidebar-backdrop" class="hidden lg:hidden fixed inset-x-0 bottom-0 top-[73px] z-30 bg-slate-900/50 backdrop-blur-sm print:hidden"></div>

            <main class="flex-1 w-full min-w-0 overflow-hidden p-4 sm:p-6 lg:p-10">
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
        // Below 1024px the sidebar slides over the page as a drawer (see .sidebar-open in <head>)
        var mobileBtn = document.getElementById('mobile-menu-btn');
        var sidebar = document.querySelector('aside');
        var backdrop = document.getElementById('sidebar-backdrop');
        if (mobileBtn && sidebar) {
            var setSidebar = function(open) {
                sidebar.classList.toggle('sidebar-open', open);
                if (backdrop) backdrop.classList.toggle('hidden', !open);
                document.body.classList.toggle('overflow-hidden', open);
                document.getElementById('menu-hamburger-icon')?.classList.toggle('hidden', open);
                document.getElementById('menu-close-icon')?.classList.toggle('hidden', !open);
            };
            mobileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                setSidebar(!sidebar.classList.contains('sidebar-open'));
            });
            if (backdrop) backdrop.addEventListener('click', function() { setSidebar(false); });
            document.addEventListener('keydown', function(e) { if (e.key === 'Escape') setSidebar(false); });
            window.addEventListener('resize', function() { if (window.innerWidth >= 1024) setSidebar(false); });
        }

        // Logged out there is no sidebar: the button opens the page links instead
        var publicMenu = document.getElementById('public-mobile-menu');
        if (mobileBtn && !sidebar && publicMenu) {
            var setPublicMenu = function(open) {
                publicMenu.classList.toggle('hidden', !open);
                document.getElementById('menu-hamburger-icon')?.classList.toggle('hidden', open);
                document.getElementById('menu-close-icon')?.classList.toggle('hidden', !open);
            };
            mobileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                setPublicMenu(publicMenu.classList.contains('hidden'));
            });
            publicMenu.querySelectorAll('a').forEach(function(a) { a.addEventListener('click', function() { setPublicMenu(false); }); });
            document.addEventListener('click', function(e) { if (!publicMenu.contains(e.target)) setPublicMenu(false); });
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

        // === NOTIFICATIONS (Real database-backed system) ===
        function checkNotifications() {
            var notifBadge = document.getElementById('notif-badge');
            var notifList = document.getElementById('notif-list');
            if (!notifBadge || !notifList) return;
            
            // Fetch unread count
            fetch('index.php?action=api_notification_count')
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    var count = data.count || 0;
                    if (count > 0) {
                        notifBadge.classList.remove('hidden');
                        notifBadge.textContent = count > 9 ? '9+' : count;
                    } else {
                        notifBadge.classList.add('hidden');
                    }
                })
                .catch(function() {
                    notifBadge.classList.add('hidden');
                });
        }
        
        // Fetch notifications when dropdown opens
        function loadNotifications() {
            var notifList = document.getElementById('notif-list');
            if (!notifList) return;
            
            notifList.innerHTML = '<div class="px-4 py-8 text-center text-sm text-slate-500"><svg class="w-5 h-5 animate-spin mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg><p class="text-sm text-slate-400">Loading...</p></div>';
            
            fetch('index.php?action=api_notifications')
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (!data || data.length === 0) {
                        notifList.innerHTML = '<div class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">No notifications yet</div>';
                        return;
                    }
                    
                    var html = '';
                    data.forEach(function(n) {
                        var icon = '';
                        var iconColor = '';
                        var borderColor = '';
                        
                        if (n.type === 'success') {
                            icon = '<svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
                            iconColor = 'bg-emerald-50 dark:bg-emerald-500/10';
                            borderColor = 'border-emerald-500/20';
                        } else if (n.type === 'warning') {
                            icon = '<svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
                            iconColor = 'bg-amber-50 dark:bg-amber-500/10';
                            borderColor = 'border-amber-500/20';
                        } else {
                            icon = '<svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
                            iconColor = 'bg-blue-50 dark:bg-blue-500/10';
                            borderColor = 'border-blue-500/20';
                        }
                        
                        var trackingLink = n.tracking_id 
                            ? '<a href="index.php?action=view&tracking_id=' + encodeURIComponent(n.tracking_id) + '" class="text-xs font-mono text-brand-600 dark:text-brand-400 hover:underline">' + n.tracking_id + '</a>'
                            : '';
                        
                        var unreadClass = n.is_read == 0 ? 'border-l-2 ' + borderColor : '';
                        
                        html += '<div class="px-4 py-3 border-b border-slate-100 dark:border-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors ' + unreadClass + '">' +
                            '<div class="flex gap-3">' +
                                '<div class="w-8 h-8 rounded-full ' + iconColor + ' flex items-center justify-center shrink-0 mt-0.5">' + icon + '</div>' +
                                '<div class="flex-1 min-w-0">' +
                                    '<p class="text-sm font-bold text-slate-900 dark:text-white">' + n.title + '</p>' +
                                    '<p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">' + n.message + '</p>' +
                                    '<div class="flex items-center gap-2 mt-1.5">' +
                                        trackingLink +
                                        '<span class="text-[10px] text-slate-400 font-mono">' + (n.time_ago || n.created_at_formatted) + '</span>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
                    });
                    
                    // Add "Mark all as read" button
                    html += '<div class="px-4 py-2.5 border-t border-slate-100 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-800/50">' +
                        '<button onclick="markAllNotificationsRead()" class="w-full text-center text-xs font-bold text-brand-600 dark:text-brand-400 hover:text-brand-700 transition-colors py-1">Mark All as Read</button>' +
                    '</div>';
                    
                    notifList.innerHTML = html;
                })
                .catch(function() {
                    notifList.innerHTML = '<div class="px-4 py-8 text-center text-sm text-red-500">Error loading notifications</div>';
                });
        }
        
        // Mark all as read
        function markAllNotificationsRead() {
            var formData = new FormData();
            fetch('index.php?action=api_notification_read_all', {
                method: 'POST',
                body: formData
            })
            .then(function(res) { return res.json(); })
            .then(function() {
                checkNotifications();
                loadNotifications();
            })
            .catch(function() {});
        }
        
        // Hook into notification button click to load notifications
        var notifBtn = document.getElementById('notif-btn');
        if (notifBtn) {
            notifBtn.addEventListener('click', function() {
                setTimeout(loadNotifications, 100);
            });
        }
        
        // Run notification check on page load
        if (document.getElementById('notif-badge')) {
            checkNotifications();
            // Re-check every 30 seconds
            setInterval(checkNotifications, 30000);
        }

        // === STOP DROPDOWN CLOSE ON INNER CLICKS ===
        document.querySelectorAll('#user-menu-dropdown, #quick-actions-menu, #notif-menu').forEach(function(el) {
            if (el) {
                el.addEventListener('click', function(e) { e.stopPropagation(); });
            }
        });

        // File inputs with data-max-bytes: refuse oversized files before uploading (the server checks again)
        document.addEventListener('change', function(e) {
            var input = e.target;
            if (!input.matches || !input.matches('input[type="file"][data-max-bytes]') || !input.files.length) return;
            var file = input.files[0];
            if (file.size > Number(input.dataset.maxBytes)) {
                alert('"' + file.name + '" is ' + (file.size / 1048576).toFixed(1) + 'MB. The maximum file size is ' + Math.round(input.dataset.maxBytes / 1048576) + 'MB.');
                input.value = '';
            }
        });
    </script>
</body>
</html>