<?php ob_start(); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Manage Stores</h1>
        <p class="mt-2 text-sm text-gray-700">A list of all stores in your business.</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    
    <!-- Stores Table -->
    <div class="md:col-span-2 bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Store Name</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Code</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Phone</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <?php if (empty($stores)): ?>
                        <tr><td colspan="4" class="px-3 py-4 text-sm text-gray-500 text-center">No stores found.</td></tr>
                    <?php else: ?>
                        <?php foreach($stores as $store): ?>
                        <tr>
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6"><?= htmlspecialchars($store['store_name']) ?></td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"><?= htmlspecialchars($store['store_code']) ?></td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"><?= htmlspecialchars($store['phone'] ?? 'N/A') ?></td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                <span class="inline-flex rounded-full bg-<?= $store['status'] === 'active' ? 'green' : 'red' ?>-100 px-2 text-xs font-semibold leading-5 text-<?= $store['status'] === 'active' ? 'green' : 'red' ?>-800">
                                    <?= ucfirst($store['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Store Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-bold mb-4 border-b pb-2">Add New Store</h2>
        <form action="<?= APP_URL ?>/stores" method="POST" class="space-y-4">
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Store Name</label>
                <input type="text" name="store_name" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Store Code</label>
                <input type="text" name="store_code" placeholder="e.g. MAIN01" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" name="phone" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
            </div>

            <button type="submit" class="w-full bg-brand-600 text-white px-4 py-2 rounded shadow hover:bg-brand-700">Add Store</button>
        </form>
    </div>

</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
