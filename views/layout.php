<!DOCTYPE html>
<html lang="en" class="light"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iTrackDocs System</title>
    
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
                setTimeout(() => { document.querySelector('input[name="username"]')?.focus(); }, 50);
            }
        }
        function closeLoginModal() {
            if(loginModal) {
                loginModal.classList.add('hidden');
                loginModal.classList.remove('flex');
            }
        }
    </script>
</body>
</html>