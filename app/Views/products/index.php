<?php ob_start(); ?>

<div class="mb-6 sm:flex sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Products Catalog</h1>
        <p class="mt-2 text-sm text-gray-700">Manage your business product catalog and view consolidated stock.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="<?= APP_URL ?>/products/create" class="inline-flex items-center justify-center rounded-md border border-transparent bg-brand-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-brand-700 sm:w-auto">
            + Add Product
        </a>
    </div>
</div>

<div class="flex flex-col">
    <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Product</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Category</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">SKU</th>
                            <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Selling Price</th>
                            <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">GST</th>
                            <th class="px-3 py-3.5 text-right text-sm font-semibold text-brand-600">Total Stock (All Stores)</th>
                            <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php if(empty($products)): ?>
                        <tr><td colspan="6" class="px-3 py-4 text-center text-sm text-gray-500">No products found.</td></tr>
                        <?php else: ?>
                            <?php foreach($products as $p): 
                                $total_qty = $stocks[$p['id']] ?? 0;
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6"><?= htmlspecialchars($p['name']) ?></td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"><?= htmlspecialchars($p['category_name'] ?: '-') ?></td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"><?= htmlspecialchars($p['sku'] ?: '-') ?></td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 text-right font-medium">₹<?= number_format($p['selling_price'], 2) ?></td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-right">
                                    <?= (float)$p['gst_percent'] ?>% <br><span class="text-xs text-gray-400"><?= ucfirst($p['tax_mode']) ?></span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-bold text-brand-600 text-right">
                                    <?= $total_qty > 0 ? (float)$total_qty : '<span class="text-red-500">Out of Stock</span>' ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-right space-x-2">
                                    <a href="<?= APP_URL ?>/products/edit/<?= $p['id'] ?>" class="text-brand-600 hover:text-brand-900">Edit</a>
                                    <form action="<?= APP_URL ?>/products/delete/<?= $p['id'] ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to archive this product?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-900">Archive</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
