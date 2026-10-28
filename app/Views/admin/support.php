<?php ob_start(); ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Support Tools</h1>
    <p class="text-sm text-gray-500 mt-1">Impersonation Engine: Access any business account as an owner or staff.</p>
</div>

<!-- Impersonation Engine -->
<div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0">
        <h3 class="font-bold text-gray-900">User Directory</h3>
        <span class="text-xs font-semibold bg-blue-100 text-blue-700 px-2 py-1 rounded">Impersonation Active</span>
    </div>
    <div class="p-0 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Business</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <?php foreach ($users ?? [] as $user): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($user['business_name']) ?></div>
                        <div class="text-xs text-gray-500">ID: <?= $user['business_id'] ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['name']) ?></div>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($user['username']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if($user['role'] === 'owner'): ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded bg-purple-100 text-purple-800 uppercase">Owner</span>
                        <?php else: ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800 uppercase">Staff</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <a href="<?= APP_URL ?>/admin/impersonate/<?= $user['user_id'] ?>" onclick="return confirm('You are about to log in as <?= htmlspecialchars($user['name']) ?>. Every action you take will be logged. Continue?')" class="inline-flex items-center gap-1 bg-brand-50 text-brand-700 hover:bg-brand-600 hover:text-white px-3 py-1.5 rounded text-sm font-semibold transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            Impersonate
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($users)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">No users found in the system.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/admin.php'; 
?>
