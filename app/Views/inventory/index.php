<?php ob_start(); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Current Inventory</h1>
        <p class="text-sm text-gray-500 mt-1">Live stock quantities available for billing.</p>
    </div>
    <div class="flex space-x-4">
        <a href="<?= APP_URL ?>/inventory/dead-stock" class="bg-yellow-500 text-white px-4 py-2 rounded shadow hover:bg-yellow-600">Dead Stock Report</a>
        <a href="<?= APP_URL ?>/inventory/stock-in" class="bg-brand-600 text-white px-4 py-2 rounded shadow hover:bg-brand-700">Stock In</a>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Store</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">In Stock</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($inventory)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No inventory found.</td>
                </tr>
                <?php else: ?>
                    <?php foreach($inventory as $item): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?= htmlspecialchars($item['name']) ?><br>
                            <span class="text-xs text-gray-500">SKU: <?= htmlspecialchars($item['sku']) ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($item['store_name']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold <?= $item['total_qty'] <= 5 ? 'text-red-600' : 'text-green-600' ?>">
                            <?= number_format($item['total_qty'], 2) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
