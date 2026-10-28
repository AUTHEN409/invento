<?php ob_start(); ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Super Admin Profile</h1>
    <p class="text-sm text-gray-500 mt-1">Manage your administrator credentials.</p>
</div>

<div class="bg-white shadow-sm rounded-xl border border-gray-100 max-w-2xl">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800">Account Details</h3>
    </div>
    <div class="p-6 space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" readonly value="<?= htmlspecialchars($admin['name']) ?>" class="mt-1 block w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm text-gray-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Email Address</label>
            <input type="text" readonly value="<?= htmlspecialchars($admin['email']) ?>" class="mt-1 block w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm text-gray-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" readonly value="<?= htmlspecialchars($admin['username']) ?>" class="mt-1 block w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm text-gray-600">
        </div>

        <div class="pt-4 border-t border-gray-100">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 transition">Update Password (Coming Soon)</button>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/admin.php'; 
?>
