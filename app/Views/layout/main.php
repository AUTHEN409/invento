<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invento - Retail Management</title>
    
    <!-- PWA Manifest & App Icons -->
    <link rel="manifest" href="<?= APP_URL ?>/manifest.json">
    <meta name="theme-color" content="#0d9488">
    <link rel="apple-touch-icon" href="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iIzBkOTQ4OCI+PHBhdGggZD0iTTEyIDJMMSAyMWgyMmwtMTEtMTl6Ii8+PC9zdmc+">
    
    <!-- Tailwind CSS (CDN for simple deployment, can be bundled later) -->
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
<body class="bg-gray-50 text-gray-800 font-sans antialiased">
    
    <?php if(isset($_SESSION['impersonator_admin_id'])): ?>
    <div class="bg-red-600 text-white text-sm font-bold text-center py-2 px-4 shadow-md z-[100] relative flex justify-between items-center">
        <span>âš ï¸ SUPER ADMIN IMPERSONATION ACTIVE: You are logged in as <?= htmlspecialchars($_SESSION['name']) ?>. All actions are audited.</span>
        <a href="<?= APP_URL ?>/admin/impersonate-stop" class="bg-white text-red-600 px-3 py-1 rounded text-xs hover:bg-red-50">Stop Impersonating</a>
    </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['user_id'])): ?>
        <nav class="bg-brand-900 text-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 font-bold text-xl tracking-wider">
                            INVENTO
                        </div>
                        <div class="hidden lg:ml-6 lg:flex lg:space-x-1 items-center">
                            
                            <a href="<?= APP_URL ?>/app" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-brand-600">Dashboard</a>
                            
                            <!-- Sales Group -->
                            <div class="relative group">
                                <button class="px-3 py-2 rounded-md text-sm font-medium hover:bg-brand-600 focus:outline-none">Sales â–¾</button>
                                <div class="absolute left-0 mt-0 w-48 bg-white rounded-md shadow-lg hidden group-hover:block z-50 border border-gray-100 overflow-hidden">
                                    <div class="py-1">
                                        <a href="<?= APP_URL ?>/billing" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-600">New Bill</a>
                                        <a href="<?= APP_URL ?>/invoices" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-600">Bill History</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Inventory Group -->
                            <div class="relative group">
                                <button class="px-3 py-2 rounded-md text-sm font-medium hover:bg-brand-600 focus:outline-none">Inventory â–¾</button>
                                <div class="absolute left-0 mt-0 w-48 bg-white rounded-md shadow-lg hidden group-hover:block z-50 border border-gray-100 overflow-hidden">
                                    <div class="py-1">
                                        <a href="<?= APP_URL ?>/products" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-600">Products</a>
                                        <a href="<?= APP_URL ?>/categories" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-600">Categories</a>
                                        <a href="<?= APP_URL ?>/inventory/stock-in" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-600">Stock In (Purchase)</a>
                                        <a href="<?= APP_URL ?>/inventory" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-600">Current Stock</a>
                                        <a href="<?= APP_URL ?>/suppliers" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-600">Suppliers</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Customers Group -->
                            <div class="relative group">
                                <button class="px-3 py-2 rounded-md text-sm font-medium hover:bg-brand-600 focus:outline-none">Customers â–¾</button>
                                <div class="absolute left-0 mt-0 w-48 bg-white rounded-md shadow-lg hidden group-hover:block z-50 border border-gray-100 overflow-hidden">
                                    <div class="py-1">
                                        <a href="<?= APP_URL ?>/customers" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-600">Customer List</a>
                                        <a href="<?= APP_URL ?>/credit" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-600">Credit Accounts (Khata)</a>
                                    </div>
                                </div>
                            </div>

                            <?php if($_SESSION['role'] === 'owner'): ?>
                            <!-- Management Group -->
                            <div class="relative group">
                                <button class="px-3 py-2 rounded-md text-sm font-medium hover:bg-brand-600 focus:outline-none">Manage â–¾</button>
                                <div class="absolute left-0 mt-0 w-48 bg-white rounded-md shadow-lg hidden group-hover:block z-50 border border-gray-100 overflow-hidden">
                                    <div class="py-1">
                                        <a href="<?= APP_URL ?>/stores" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-600">Stores</a>
                                        <a href="<?= APP_URL ?>/staff" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-600">Staff & Roles</a>
                                        <a href="<?= APP_URL ?>/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-600">Settings</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Reports Group -->
                            <div class="relative group">
                                <button class="px-3 py-2 rounded-md text-sm font-medium hover:bg-brand-600 focus:outline-none">Reports â–¾</button>
                                <div class="absolute left-0 mt-0 w-56 bg-white rounded-md shadow-lg hidden group-hover:block z-50 border border-gray-100 overflow-hidden">
                                    <div class="py-1">
                                        <a href="<?= APP_URL ?>/export/sales-report" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-600">Export Sales Report</a>
                                        <a href="<?= APP_URL ?>/export/inventory" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-600">Export Inventory Data</a>
                                        <a href="<?= APP_URL ?>/export/customers" class="block px-4 py-2 text-sm text-gray-700 hover:bg-brand-50 hover:text-brand-600">Export Customers Data</a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>
                    </div>
                    
                    <!-- Desktop Right Side (Store & Logout) -->
                    <div class="hidden lg:flex items-center space-x-4">
                        <span class="text-sm">Store: <?= htmlspecialchars($_SESSION['current_store_id'] === 'all' ? 'All Stores' : 'Store ID '.$_SESSION['current_store_id']) ?></span>
                        <a href="<?= APP_URL ?>/logout" class="px-3 py-2 rounded-md text-sm font-medium bg-red-600 hover:bg-red-700 transition">Logout</a>
                    </div>

                    <!-- Mobile Hamburger Icon -->
                    <div class="lg:hidden flex items-center">
                        <button id="mobileMenuBtn" class="text-white hover:text-brand-100 focus:outline-none p-2">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Slide-out Drawer Overlay -->
            <div id="mobileDrawerOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden transition-opacity duration-300 opacity-0"></div>
            
            <!-- Mobile Slide-out Drawer -->
            <div id="mobileDrawer" class="fixed inset-y-0 left-0 w-64 bg-brand-900 text-white z-50 transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden shadow-xl flex flex-col h-full overflow-hidden">
                <div class="px-4 py-5 border-b border-brand-700 flex justify-between items-center">
                    <span class="font-bold text-xl tracking-wider">INVENTO</span>
                    <button id="closeMobileMenuBtn" class="text-brand-100 hover:text-white p-1">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="px-4 py-3 bg-brand-800 border-b border-brand-700">
                    <div class="text-xs text-brand-200 uppercase tracking-wider mb-1">Current Store</div>
                    <div class="font-medium text-sm truncate"><?= htmlspecialchars($_SESSION['current_store_id'] === 'all' ? 'All Stores' : 'Store ID '.$_SESSION['current_store_id']) ?></div>
                </div>

                <div class="flex-1 overflow-y-auto px-2 py-4 space-y-1">
                    <a href="<?= APP_URL ?>/app" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700">Dashboard</a>
                    
                    <div class="text-xs font-bold text-brand-200 uppercase tracking-wider px-3 pt-4 pb-1">Sales</div>
                    <a href="<?= APP_URL ?>/billing" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700 pl-6">New Bill</a>
                    <a href="<?= APP_URL ?>/invoices" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700 pl-6">Bill History</a>
                    
                    <div class="text-xs font-bold text-brand-200 uppercase tracking-wider px-3 pt-4 pb-1">Inventory</div>
                    <a href="<?= APP_URL ?>/products" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700 pl-6">Products</a>
                    <a href="<?= APP_URL ?>/categories" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700 pl-6">Categories</a>
                    <a href="<?= APP_URL ?>/inventory/stock-in" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700 pl-6">Stock In</a>
                    <a href="<?= APP_URL ?>/inventory" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700 pl-6">Current Stock</a>
                    <a href="<?= APP_URL ?>/suppliers" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700 pl-6">Suppliers</a>
                    
                    <div class="text-xs font-bold text-brand-200 uppercase tracking-wider px-3 pt-4 pb-1">Customers</div>
                    <a href="<?= APP_URL ?>/customers" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700 pl-6">Customer List</a>
                    <a href="<?= APP_URL ?>/credit" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700 pl-6">Credit Accounts</a>

                    <?php if($_SESSION['role'] === 'owner'): ?>
                    <div class="text-xs font-bold text-brand-200 uppercase tracking-wider px-3 pt-4 pb-1">Manage</div>
                    <a href="<?= APP_URL ?>/stores" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700 pl-6">Stores</a>
                    <a href="<?= APP_URL ?>/staff" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700 pl-6">Staff</a>
                    <a href="<?= APP_URL ?>/settings" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700 pl-6">Settings</a>
                    
                    <div class="text-xs font-bold text-brand-200 uppercase tracking-wider px-3 pt-4 pb-1">Reports</div>
                    <a href="<?= APP_URL ?>/export/sales-report" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700 pl-6">Sales Report</a>
                    <a href="<?= APP_URL ?>/export/inventory" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700 pl-6">Inventory Data</a>
                    <a href="<?= APP_URL ?>/export/customers" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-brand-700 pl-6">Customers Data</a>
                    <?php endif; ?>
                </div>
                
                <div class="p-4 border-t border-brand-700">
                    <a href="<?= APP_URL ?>/logout" class="flex justify-center items-center w-full px-4 py-2 rounded-md text-base font-medium bg-red-600 hover:bg-red-700 transition shadow">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const btn = document.getElementById('mobileMenuBtn');
                    const closeBtn = document.getElementById('closeMobileMenuBtn');
                    const drawer = document.getElementById('mobileDrawer');
                    const overlay = document.getElementById('mobileDrawerOverlay');

                    function openDrawer() {
                        overlay.classList.remove('hidden');
                        setTimeout(() => {
                            overlay.classList.remove('opacity-0');
                            drawer.classList.remove('-translate-x-full');
                        }, 10);
                        document.body.style.overflow = 'hidden'; // Prevent background scroll
                    }

                    function closeDrawer() {
                        drawer.classList.add('-translate-x-full');
                        overlay.classList.add('opacity-0');
                        setTimeout(() => {
                            overlay.classList.add('hidden');
                        }, 300);
                        document.body.style.overflow = '';
                    }

                    if(btn) btn.addEventListener('click', openDrawer);
                    if(closeBtn) closeBtn.addEventListener('click', closeDrawer);
                    if(overlay) overlay.addEventListener('click', closeDrawer);
                });
            </script>
        </nav>
    <?php endif; ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Read-Only Banner -->
        <?php 
        $db = \core\Database::getInstance();
        $st = $db->prepare("SELECT status FROM businesses WHERE id = ?");
        $st->execute([$_SESSION['business_id'] ?? 0]);
        if($st->fetchColumn() === 'expired'): 
        ?>
        <div class="bg-red-600 text-white p-3 rounded-md mb-6 flex justify-between items-center shadow">
            <span><strong>Subscription Expired.</strong> Your account is in READ-ONLY mode. You cannot create new bills or modify data.</span>
        </div>
        <?php endif; ?>

        <?php if(!empty($_SESSION['error_msg'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-6 shadow-sm flex justify-between">
            <span><?= htmlspecialchars($_SESSION['error_msg']) ?></span>
            <button onclick="this.parentElement.style.display='none'" class="text-red-600 font-bold">&times;</button>
        </div>
        <?php unset($_SESSION['error_msg']); ?>
        <?php endif; ?>
        
        <?= $content ?? '' ?>
    </main>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= APP_URL ?>/sw.js')
                    .then(reg => console.log('Service Worker registered'))
                    .catch(err => console.error('Service Worker registration failed:', err));
            });
        }
    </script>
</body>
</html>

