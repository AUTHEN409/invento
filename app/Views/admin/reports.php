<?php ob_start(); ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Platform Reports</h1>
    <p class="text-sm text-gray-500 mt-1">Financial and growth analytics for the SaaS platform.</p>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-6 shadow-sm rounded-xl border border-gray-100">
        <div class="text-sm text-gray-500 mb-1">Total Tenants</div>
        <div class="text-3xl font-black text-gray-900"><?= $total_businesses ?></div>
    </div>
    <div class="bg-white p-6 shadow-sm rounded-xl border border-gray-100">
        <div class="text-sm text-gray-500 mb-1">Total Stores</div>
        <div class="text-3xl font-black text-gray-900"><?= $total_stores ?></div>
    </div>
    <div class="bg-white p-6 shadow-sm rounded-xl border border-gray-100">
        <div class="text-sm text-gray-500 mb-1">Total Users</div>
        <div class="text-3xl font-black text-gray-900"><?= $total_users ?></div>
    </div>
    <div class="bg-white p-6 shadow-sm rounded-xl border border-gray-100">
        <div class="text-sm text-gray-500 mb-1">Total Sub Revenue</div>
        <div class="text-3xl font-black text-green-600">₹<?= number_format($total_revenue, 2) ?></div>
    </div>
</div>

<div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6">
    <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Tenants by Plan</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subscribed Businesses</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                <?php foreach($plan_breakdown as $pb): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($pb['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $pb['count'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/admin.php'; 
?>
