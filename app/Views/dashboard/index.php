<?php ob_start(); ?>

<div class="mb-6 flex flex-col md:flex-row justify-between items-center bg-white p-4 shadow rounded-lg">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Welcome back, <?= htmlspecialchars($_SESSION['name']) ?>.</p>
    </div>
    
    <?php if($_SESSION['role'] === 'owner'): ?>
    <div class="mt-4 md:mt-0 flex items-center space-x-2">
        <label class="text-sm font-medium text-gray-700">Viewing Context:</label>
        <select onchange="window.location.href='<?= APP_URL ?>/switch-store/'+this.value" class="border-gray-300 rounded-md shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="all" <?= $_SESSION['current_store_id'] === 'all' ? 'selected' : '' ?>>All Stores (Consolidated)</option>
            <?php foreach($stores as $s): ?>
                <option value="<?= $s['id'] ?>" <?= $_SESSION['current_store_id'] == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['store_name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php else: ?>
    <div class="mt-4 md:mt-0">
        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-brand-100 text-brand-800">
            Store: <?= htmlspecialchars($stores[0]['store_name'] ?? 'Assigned Store') ?>
        </span>
    </div>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white overflow-hidden shadow rounded-lg border-t-4 border-brand-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-brand-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Today's Sales</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-bold text-gray-900">₹<?= number_format($stats['total_sales'] ?? 0, 2) ?></div>
                        </dd>
                        <dd class="text-xs text-gray-400 mt-1"><?= $stats['total_invoices'] ?> invoices generated</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg border-t-4 border-blue-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Current Stock Value</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-bold text-gray-900">₹<?= number_format($stats['stock_value'] ?? 0, 2) ?></div>
                        </dd>
                        <dd class="text-xs text-gray-400 mt-1">Based on purchase prices</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg border-t-4 border-red-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Outstanding Credit</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-bold text-red-600">₹<?= number_format($stats['outstanding_credit'] ?? 0, 2) ?></div>
                        </dd>
                        <dd class="text-xs text-gray-400 mt-1">Total pending from customers</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white overflow-hidden shadow rounded-lg border-t-4 border-yellow-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Dead Stock Value (90+ Days)</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-bold text-gray-900">₹<?= number_format($stats['dead_stock_value'] ?? 0, 2) ?></div>
                        </dd>
                        <dd class="text-xs text-gray-400 mt-1">Capital tied up in aged inventory</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white overflow-hidden shadow rounded-lg border-t-4 border-indigo-500">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Product Catalog</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-bold text-gray-900"><?= number_format($stats['total_products'] ?? 0) ?></div>
                        </dd>
                        <dd class="text-xs text-gray-400 mt-1">Global products registered</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="<?= APP_URL ?>/billing" class="text-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 flex flex-col items-center">
            <span class="text-2xl mb-2">🛒</span>
            <span class="text-sm font-medium text-gray-900">New Bill</span>
        </a>
        <a href="<?= APP_URL ?>/inventory/stock-in" class="text-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 flex flex-col items-center">
            <span class="text-2xl mb-2">📦</span>
            <span class="text-sm font-medium text-gray-900">Stock In</span>
        </a>
        <a href="<?= APP_URL ?>/credit" class="text-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 flex flex-col items-center">
            <span class="text-2xl mb-2">💰</span>
            <span class="text-sm font-medium text-gray-900">Collect Payment</span>
        </a>
        <a href="<?= APP_URL ?>/settings" class="text-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 flex flex-col items-center">
            <span class="text-2xl mb-2">⬇️</span>
            <span class="text-sm font-medium text-gray-900">Download Reports</span>
        </a>
    </div>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
