<?php ob_start(); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Manage Categories</h1>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2 bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category Name</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                <?php if(empty($categories)): ?>
                    <tr><td class="px-6 py-4 text-center text-sm text-gray-500">No categories found.</td></tr>
                <?php else: ?>
                    <?php foreach($categories as $c): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($c['name']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            <form action="<?= APP_URL ?>/categories/delete/<?= $c['id'] ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
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

    <div class="bg-white shadow rounded-lg p-6 h-fit">
        <h2 class="text-lg font-bold mb-4 border-b pb-2">Add Category</h2>
        <form action="<?= APP_URL ?>/categories" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Category Name</label>
                <input type="text" name="name" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <button type="submit" class="w-full bg-brand-600 text-white px-4 py-2 rounded shadow hover:bg-brand-700">Add Category</button>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
