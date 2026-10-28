<?php ob_start(); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Add Product</h1>
    </div>
    <a href="<?= APP_URL ?>/products" class="text-brand-600 hover:text-brand-900 font-medium">← Back to Catalog</a>
</div>

<div class="bg-white shadow rounded-lg p-6 max-w-3xl">
    <form action="<?= APP_URL ?>/products/store" method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Product Name *</label>
                <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-lg p-2 border">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Category</label>
                <div class="mt-1 flex space-x-2">
                    <select name="category_id" id="category_id" onchange="toggleNewCategory()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border">
                        <option value="">-- None --</option>
                        <option value="new">+ Add New Category</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="new_category" id="new_category" placeholder="New Category Name" class="hidden block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border">
                </div>
                <script>
                    function toggleNewCategory() {
                        const sel = document.getElementById('category_id');
                        const inp = document.getElementById('new_category');
                        if (sel.value === 'new') {
                            sel.classList.add('w-1/3');
                            sel.classList.remove('w-full');
                            inp.classList.remove('hidden');
                            inp.required = true;
                        } else {
                            sel.classList.remove('w-1/3');
                            sel.classList.add('w-full');
                            inp.classList.add('hidden');
                            inp.required = false;
                            inp.value = '';
                        }
                    }
                </script>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">SKU / Barcode</label>
                <input type="text" name="sku" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Purchase Price (₹) *</label>
                <input type="number" name="purchase_price" required min="0" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Selling Price (₹) *</label>
                <input type="number" name="selling_price" required min="0" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border border-brand-300 bg-brand-50">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">GST Percentage (%)</label>
                <input type="number" name="gst_percent" value="0" min="0" max="100" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tax Mode</label>
                <select name="tax_mode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border">
                    <option value="exclusive">Exclusive (Added to Selling Price)</option>
                    <option value="inclusive">Inclusive (Included in Selling Price)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Opening Stock</label>
                <input type="number" name="opening_stock" min="0" step="0.01" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border">
                <p class="text-xs text-gray-500 mt-1">Initial stock will be added to the currently active store.</p>
            </div>
        </div>

        <div class="flex justify-end pt-4 border-t border-gray-200">
            <button type="submit" class="bg-brand-600 text-white px-6 py-2 rounded shadow hover:bg-brand-700 text-lg font-medium">Save Product</button>
        </div>
    </form>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
