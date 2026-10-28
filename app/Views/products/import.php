<?php ob_start(); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Import Products (XLSX)</h1>
        <p class="mt-2 text-sm text-gray-700">Upload an Excel (.xlsx) file to bulk import products.</p>
    </div>
    <a href="<?= APP_URL ?>/products" class="text-brand-600 hover:text-brand-900 font-medium">← Back to Catalog</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white shadow rounded-lg p-6">
        <form action="<?= APP_URL ?>/products/import" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div>
                <label class="block text-sm font-medium text-gray-700">Select XLSX File</label>
                <input type="file" name="import_file" accept=".xlsx" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <button type="submit" class="w-full bg-brand-600 text-white px-4 py-2 rounded shadow hover:bg-brand-700 font-medium">Upload and Import</button>
        </form>
    </div>

    <div class="bg-gray-50 border rounded-lg p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-2">Required Template Format</h3>
        <p class="text-sm text-gray-600 mb-4">Your XLSX file must have the following columns in exactly this order (no headers required, start data on row 2 if row 1 is header):</p>
        
        <ol class="list-decimal pl-5 text-sm space-y-1 text-gray-700">
            <li><strong>Product Name</strong> (Required)</li>
            <li><strong>SKU</strong> (Optional, must be unique)</li>
            <li><strong>Category Name</strong> (Optional, creates if missing)</li>
            <li><strong>Purchase Price</strong> (Required, e.g. 100)</li>
            <li><strong>Selling Price</strong> (Required, e.g. 150)</li>
            <li><strong>GST Percent</strong> (Required, e.g. 18)</li>
            <li><strong>Tax Mode</strong> ('inclusive' or 'exclusive')</li>
            <li><strong>Opening Stock</strong> (Optional, added to currently selected store)</li>
        </ol>

        <p class="text-xs text-red-600 mt-4">Note: If you provide an Opening Stock, it will be added to the store you currently have selected in your navigation bar.</p>
    </div>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
