<?php ob_start(); ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Platform Audit Logs</h1>
    <p class="text-sm text-gray-500 mt-1">Immutable ledger of all critical actions performed on the platform.</p>
</div>

<div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Timestamp</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tenant</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actor</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                <?php if(empty($logs)): ?>
                    <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No logs found.</td></tr>
                <?php else: ?>
                    <?php foreach($logs as $log): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('y-m-d H:i:s', strtotime($log['created_at'])) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600"><?= htmlspecialchars($log['business_name'] ?? 'System') ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($log['username'] ?? 'System') ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-800 uppercase"><?= htmlspecialchars($log['action']) ?></span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 max-w-md truncate" title="<?= htmlspecialchars($log['description']) ?>">
                            <?= htmlspecialchars($log['description']) ?>
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
