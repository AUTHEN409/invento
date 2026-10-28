<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Invento SaaS</title>
    
    <!-- Tailwind CSS (CDN for simple deployment) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#0d9488',
                            900: '#134e4a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased">
    
    <div class="flex h-screen bg-gray-100 overflow-hidden">
        
        <!-- Mobile sidebar backdrop -->
        <div id="sidebarBackdrop" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 hidden md:hidden transition-opacity" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white flex flex-col shadow-xl transform -translate-x-full transition-transform duration-300 md:relative md:translate-x-0">
            <div class="h-16 flex items-center justify-between px-6 border-b border-gray-800">
                <span class="text-xl font-black tracking-wider text-indigo-400">INVENTO <span class="text-white text-sm font-medium ml-1">ADMIN</span></span>
                <!-- Close button on mobile -->
                <button class="md:hidden text-gray-400 hover:text-white focus:outline-none" onclick="toggleSidebar()">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <p class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Platform</p>
                <a href="<?= APP_URL ?>/admin" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-800 hover:text-white transition">Dashboard</a>
                <a href="<?= APP_URL ?>/admin/businesses" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-800 hover:text-white transition">Businesses</a>
                <a href="<?= APP_URL ?>/admin/subscriptions" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-800 hover:text-white transition">Subscriptions</a>
                
                <p class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Entities</p>
                <a href="<?= APP_URL ?>/admin/users" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-800 hover:text-white transition">Users</a>
                <a href="<?= APP_URL ?>/admin/stores" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-800 hover:text-white transition">Stores</a>
                
                <p class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">System</p>
                <a href="<?= APP_URL ?>/admin/cms" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-800 hover:text-white transition text-teal-400">Website CMS</a>
                <a href="<?= APP_URL ?>/admin/reports" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-800 hover:text-white transition">Reports</a>
                <a href="<?= APP_URL ?>/admin/audit-logs" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-800 hover:text-white transition">Audit Logs</a>
                <a href="<?= APP_URL ?>/admin/support" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-800 hover:text-white transition">Support Tools</a>
            </nav>
            <div class="p-4 border-t border-gray-800">
                <a href="<?= APP_URL ?>/admin/settings" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-800 transition">Settings</a>
                <a href="<?= APP_URL ?>/admin/profile" class="flex items-center px-2 py-2 text-sm font-medium rounded-md hover:bg-gray-800 transition">Profile</a>
                <a href="<?= APP_URL ?>/admin/logout" class="flex items-center px-2 py-2 mt-2 text-sm font-medium rounded-md bg-red-900 text-red-100 hover:bg-red-800 transition">Logout</a>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden w-full">
            <!-- Top Header -->
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-4 md:px-8 border-b border-gray-200 shrink-0">
                <div class="flex items-center">
                    <!-- Hamburger Menu Button -->
                    <button class="md:hidden mr-4 text-gray-600 hover:text-gray-900 focus:outline-none" onclick="toggleSidebar()">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h2 class="text-lg md:text-xl font-semibold text-gray-800 truncate">Super Admin Console</h2>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-xs md:text-sm font-medium text-gray-600 hidden sm:inline-block">Welcome, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
                    <span class="text-xs md:text-sm font-medium text-gray-600 sm:hidden"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
                </div>
            </header>

            <!-- Main Scrollable Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 md:p-8">
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }
    </script>
</body>
</html>

