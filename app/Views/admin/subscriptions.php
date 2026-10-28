<?php ob_start(); ?>

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-0">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Subscription Management</h1>
        <p class="text-sm text-gray-500 mt-1">Global view of all active, expired, and upcoming subscriptions.</p>
    </div>
</div>

<div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Business</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Plan</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Start Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Expiry Date</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <?php if(empty($subscriptions)): ?>
                    <tr><td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">No subscriptions found.</td></tr>
                <?php else: ?>
                    <?php foreach($subscriptions as $sub): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900"><?= htmlspecialchars($sub['business_name']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 font-medium"><?= htmlspecialchars($sub['plan_name']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php
                                $color = $sub['status'] === 'active' ? 'green' : ($sub['status'] === 'expired' ? 'red' : 'gray');
                            ?>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?= $color ?>-100 text-<?= $color ?>-800">
                                <?= ucfirst($sub['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('M d, Y', strtotime($sub['start_date'])) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium <?= strtotime($sub['expiry_date']) < time() ? 'text-red-600' : 'text-gray-900' ?>">
                            <?= date('M d, Y', strtotime($sub['expiry_date'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?= APP_URL ?>/admin/business/<?= $sub['business_id'] ?>" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded transition">Manage</a>
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
include BASE_PATH . 'app/Views/layout/admin.php'; 
?>
