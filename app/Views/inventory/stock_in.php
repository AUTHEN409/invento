<?php ob_start(); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Stock In</h1>
        <p class="text-sm text-gray-500 mt-1">Add new inventory batches to a store.</p>
    </div>
</div>

<div class="bg-white shadow rounded-lg p-6 max-w-3xl">
    <form action="<?= APP_URL ?>/inventory/stock-in" method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Store -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Store *</label>
                <select name="store_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    <?php foreach($stores as $s): ?>
                        <!-- Only allow selection if owner, otherwise force their store -->
                        <?php if($_SESSION['role'] === 'owner' || $_SESSION['current_store_id'] == $s['id']): ?>
                        <option value="<?= $s['id'] ?>" <?= $_SESSION['current_store_id'] == $s['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['store_name']) ?>
                        </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Product -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Product *</label>
                <select name="product_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">Select Product...</option>
                    <?php foreach($products as $p): ?>
                        <option value="<?= $p['id'] ?>" data-price="<?= $p['purchase_price'] ?>">
                            <?= htmlspecialchars($p['name'] . ($p['sku'] ? ' ('.$p['sku'].')' : '')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Quantity -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Quantity *</label>
                <input type="number" name="quantity" required min="0.001" step="0.001" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <!-- Purchase Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Purchase Price (Per Unit) *</label>
                <input type="number" name="purchase_price" required min="0" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <!-- Supplier -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Supplier</label>
                <div class="mt-1 flex space-x-2">
                    <select name="supplier_id" id="supplier_id" onchange="toggleNewSupplier()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border">
                        <option value="">-- None --</option>
                        <option value="new">+ Add New Supplier</option>
                        <?php foreach($suppliers as $sup): ?>
                            <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="new_supplier" id="new_supplier" placeholder="New Supplier Name" class="hidden block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 p-2 border">
                </div>
                <script>
                    function toggleNewSupplier() {
                        const sel = document.getElementById('supplier_id');
                        const inp = document.getElementById('new_supplier');
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

            <!-- Batch Number -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Batch Number (Optional)</label>
                <input type="text" name="batch_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <!-- Purchase Date -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Purchase Date *</label>
                <input type="date" name="purchase_date" required value="<?= date('Y-m-d') ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

        </div>

        <div class="flex justify-end pt-4 border-t">
            <button type="submit" class="bg-brand-600 text-white px-6 py-2 rounded shadow hover:bg-brand-700">Save Stock In</button>
        </div>
    </form>
</div>

<script>
    // Auto-fill price based on product selection
    document.querySelector('select[name="product_id"]').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const price = option.getAttribute('data-price');
        if(price) {
            document.querySelector('input[name="purchase_price"]').value = price;
        }
    });
</script>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
