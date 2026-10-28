<?php ob_start(); ?>

<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-900">Business Settings</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-bold mb-4 border-b pb-2">Business Profile</h2>
        <form action="<?= APP_URL ?>/settings" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div>
                <label class="block text-sm font-medium text-gray-700">Business Name</label>
                <input type="text" name="business_name" value="<?= htmlspecialchars($business['business_name']) ?>" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Owner Name</label>
                <input type="text" name="owner_name" value="<?= htmlspecialchars($business['owner_name']) ?>" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($business['phone']) ?>" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($business['email']) ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">GSTIN</label>
                <input type="text" name="gstin" value="<?= htmlspecialchars($business['gstin']) ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <textarea name="address" class="mt-1 block w-full p-2 border border-gray-300 rounded-md"><?= htmlspecialchars($business['address']) ?></textarea>
            </div>

            <button type="submit" class="w-full bg-brand-600 text-white px-4 py-2 rounded shadow hover:bg-brand-700">Save Profile</button>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-bold mb-4 border-b pb-2">Data Export (Excel/CSV)</h2>
        <p class="text-sm text-gray-600 mb-4">Download your business data for backup or accounting purposes. Store-specific exports will respect your currently selected store.</p>
        
            <?php
            $exports = [
                ['url' => '/export/products', 'label' => 'Products Catalog'],
                ['url' => '/export/inventory', 'label' => 'Inventory (Current Store)'],
                ['url' => '/export/customers', 'label' => 'Customers'],
                ['url' => '/export/sales-report', 'label' => 'Sales Report (Current Store)'],
                ['url' => '/export/billing', 'label' => 'Billing Summary (Current Store)'],
                ['url' => '/export/billing-detailed', 'label' => 'Billing Detailed (Current Store)'],
                ['url' => '/export/credit', 'label' => 'Credit Report'],
                ['url' => '/export/dead-stock', 'label' => 'Dead Stock (Current Store)', 'color' => 'text-red-600'],
                ['url' => '/export/staff', 'label' => 'Staff Report'],
                ['url' => '/export/stores', 'label' => 'Stores Report']
            ];
            foreach($exports as $exp):
                $color = $exp['color'] ?? 'text-gray-700';
            ?>
            <div class="flex items-center justify-between border border-gray-200 rounded p-3 hover:bg-gray-50">
                <span class="font-medium <?= $color ?>">⬇️ <?= $exp['label'] ?></span>
                <div class="flex space-x-2">
                    <a href="<?= APP_URL ?><?= $exp['url'] ?>?format=excel" class="text-xs bg-green-100 text-green-800 px-3 py-1 rounded hover:bg-green-200 font-bold border border-green-200">Excel</a>
                    <a href="<?= APP_URL ?><?= $exp['url'] ?>?format=csv" class="text-xs bg-blue-100 text-blue-800 px-3 py-1 rounded hover:bg-blue-200 font-bold border border-blue-200">CSV</a>
                    <a href="<?= APP_URL ?><?= $exp['url'] ?>?format=pdf" class="text-xs bg-red-100 text-red-800 px-3 py-1 rounded hover:bg-red-200 font-bold border border-red-200">PDF</a>
                </div>
            </div>
            <?php endforeach; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
