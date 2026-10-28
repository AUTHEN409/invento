<?php ob_start(); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Staff Management</h1>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2 bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned Store</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                <?php foreach($staff as $u): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($u['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($u['username']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 uppercase"><?= htmlspecialchars($u['role']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $u['store_name'] ? htmlspecialchars($u['store_name']) : 'All Stores' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-bold mb-4 border-b pb-2">Add Staff</h2>
        <form action="<?= APP_URL ?>/staff" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div>
                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" name="name" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                    <option value="cashier">Cashier</option>
                    <option value="manager">Manager</option>
                    <option value="owner">Owner</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Assigned Store</label>
                <select name="store_id" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                    <option value="">-- None (Owner Only) --</option>
                    <?php foreach($stores as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['store_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="w-full bg-brand-600 text-white px-4 py-2 rounded shadow hover:bg-brand-700">Add Staff</button>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
