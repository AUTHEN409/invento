<?php ob_start(); ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Global User Directory</h1>
    <p class="text-sm text-gray-500 mt-1">Directory of all owners, managers, and staff across all tenants.</p>
</div>

<div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Business</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Store Restriction</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Last Login</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <?php if(empty($users)): ?>
                    <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No users found.</td></tr>
                <?php else: ?>
                    <?php foreach($users as $user): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($user['name']) ?></div>
                            <div class="text-xs text-gray-500"><?= htmlspecialchars($user['username']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($user['business_name']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $user['store_name'] ? htmlspecialchars($user['store_name']) : '<span class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-600">Global (All Stores)</span>' ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase <?= $user['role'] === 'owner' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= $user['role'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= $user['last_login_at'] ? date('M d, H:i', strtotime($user['last_login_at'])) : 'Never' ?>
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
