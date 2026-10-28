<?php ob_start(); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dead Stock Report</h1>
        <p class="text-sm text-gray-500 mt-1">Capital trapped in inventory older than 90 days.</p>
    </div>
    <a href="<?= APP_URL ?>/inventory" class="text-brand-600 hover:underline">← Back to Inventory</a>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Store</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Old</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Trapped Capital</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($dead_stock)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Good news! No dead stock found.</td>
                </tr>
                <?php else: ?>
                    <?php $total = 0; foreach($dead_stock as $item): $total += $item['stock_value']; ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?= htmlspecialchars($item['name']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($item['store_name']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= number_format($item['remaining_quantity'], 2) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-bold"><?= $item['days_old'] ?> days</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-gray-900">₹<?= number_format($item['stock_value'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($dead_stock)): ?>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right text-gray-900">Total Trapped Capital:</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-right text-red-600">₹<?= number_format($total, 2) ?></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
