<?php ob_start(); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($customer['name']) ?>'s Profile</h1>
    </div>
    <a href="<?= APP_URL ?>/customers" class="text-brand-600 hover:underline">← Back to Customers</a>
</div>

<div class="bg-white shadow rounded-lg p-6 mb-6">
    <h2 class="text-lg font-bold mb-4 border-b pb-2">Customer Details</h2>
    <div class="grid grid-cols-2 gap-4">
        <div><span class="text-gray-500">Phone:</span> <span class="font-medium"><?= htmlspecialchars($customer['phone']) ?></span></div>
        <div><span class="text-gray-500">Address:</span> <span class="font-medium"><?= htmlspecialchars($customer['address'] ?? 'N/A') ?></span></div>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-bold">Purchase History</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                <?php if(empty($invoices)): ?>
                    <tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No purchases found.</td></tr>
                <?php else: ?>
                    <?php foreach($invoices as $inv): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-brand-600">
                            <a href="<?= APP_URL ?>/invoices/<?= $inv['id'] ?>"><?= htmlspecialchars($inv['invoice_number']) ?></a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($inv['invoice_date']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $inv['total_items'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">₹<?= number_format($inv['grand_total'], 2) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 rounded text-xs <?= $inv['payment_status'] === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                <?= ucfirst($inv['payment_status']) ?>
                            </span>
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
