<?php ob_start(); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Manage Customers</h1>
    </div>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Purchases</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Last Purchase Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Outstanding Credit</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                <?php if(empty($customers)): ?>
                    <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No customers found. Customers are added automatically during billing.</td></tr>
                <?php else: ?>
                    <?php foreach($customers as $c): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($c['name']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($c['phone']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center"><?= (int)$c['total_purchases'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $c['last_purchase_date'] ? date('M d, Y', strtotime($c['last_purchase_date'])) : '-' ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold <?= $c['outstanding_credit'] > 0 ? 'text-red-600' : 'text-gray-900' ?>">
                            ₹<?= number_format((float)$c['outstanding_credit'], 2) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium space-x-2">
                            <a href="<?= APP_URL ?>/customers/<?= $c['id'] ?>" class="text-brand-600 hover:text-brand-900">History</a>
                            <form action="<?= APP_URL ?>/customers/delete/<?= $c['id'] ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
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
