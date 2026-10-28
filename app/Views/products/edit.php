<?php ob_start(); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Edit Product</h1>
    </div>
    <a href="<?= APP_URL ?>/products" class="text-brand-600 hover:text-brand-900 font-medium">← Back to Catalog</a>
</div>

<div class="bg-white shadow rounded-lg p-6 max-w-3xl">
    <form action="<?= APP_URL ?>/products/update/<?= $product['id'] ?>" method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Product Name *</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($product['name']) ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-lg p-2 border">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Category</label>
                <select name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border">
                    <option value="">-- None --</option>
                    <?php foreach($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">SKU / Barcode</label>
                <input type="text" name="sku" value="<?= htmlspecialchars($product['sku'] ?? '') ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Purchase Price (₹) *</label>
                <input type="number" name="purchase_price" required min="0" step="0.01" value="<?= htmlspecialchars($product['purchase_price']) ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Selling Price (₹) *</label>
                <input type="number" name="selling_price" required min="0" step="0.01" value="<?= htmlspecialchars($product['selling_price']) ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border border-brand-300 bg-brand-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">GST Percentage (%)</label>
                <input type="number" name="gst_percent" min="0" max="100" step="0.01" value="<?= htmlspecialchars($product['gst_percent']) ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tax Mode</label>
                <select name="tax_mode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border">
                    <option value="exclusive" <?= $product['tax_mode'] === 'exclusive' ? 'selected' : '' ?>>Exclusive (Added to Selling Price)</option>
                    <option value="inclusive" <?= $product['tax_mode'] === 'inclusive' ? 'selected' : '' ?>>Inclusive (Included in Selling Price)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border">
                    <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $product['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-200">
            <button type="submit" class="bg-brand-600 text-white px-6 py-2 rounded shadow hover:bg-brand-700 text-lg font-medium">Update Product</button>
        </div>
    </form>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
