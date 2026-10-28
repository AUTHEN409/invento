<?php ob_start(); ?>

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0">
    <h1 class="text-2xl font-bold text-gray-900">Manage: <?= htmlspecialchars($business['business_name']) ?></h1>
    <a href="<?= APP_URL ?>/admin/businesses" class="text-indigo-600 hover:underline">← Back</a>
</div>

<?php if(!empty($_SESSION['temp_msg'])): ?>
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-6">
        <?= htmlspecialchars($_SESSION['temp_msg']) ?>
    </div>
    <?php unset($_SESSION['temp_msg']); ?>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Business Details -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-bold mb-4 border-b pb-2">Business Details</h2>
        <form action="<?= APP_URL ?>/admin/business/<?= $business['id'] ?>/update" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Business Name</label>
                    <input type="text" name="business_name" value="<?= htmlspecialchars($business['business_name']) ?>" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Owner Name</label>
                    <input type="text" name="owner_name" value="<?= htmlspecialchars($business['owner_name']) ?>" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($business['phone']) ?>" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($business['email']) ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div class="col-span-1 sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">GSTIN</label>
                    <input type="text" name="gstin" value="<?= htmlspecialchars($business['gstin'] ?? '') ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
            </div>

            <button type="submit" class="w-full bg-gray-800 text-white px-4 py-2 rounded shadow hover:bg-gray-900">Update Details</button>
        </form>

        <!-- Quick Status Override -->
        <form action="<?= APP_URL ?>/admin/business/<?= $business['id'] ?>/status" method="POST" class="mt-6 border-t pt-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <label class="block text-sm font-medium text-gray-700 mb-2">Override Status</label>
            <div class="flex flex-col sm:flex-row gap-2">
                <select name="status" class="block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="active" <?= $business['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="trial" <?= $business['status'] == 'trial' ? 'selected' : '' ?>>Trial</option>
                    <option value="expired" <?= $business['status'] == 'expired' ? 'selected' : '' ?>>Expired (Read-Only)</option>
                    <option value="suspended" <?= $business['status'] == 'suspended' ? 'selected' : '' ?>>Suspended (Blocked)</option>
                </select>
                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded">Update</button>
            </div>
        </form>

        <!-- Access Permissions (Super Admin Override) -->
        <form action="<?= APP_URL ?>/admin/business/<?= $business['id'] ?>/permissions" method="POST" class="mt-6 border-t pt-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <h3 class="text-sm font-bold text-gray-900 mb-2">Owner Access Control (Features)</h3>
            <p class="text-xs text-gray-500 mb-4">Toggle what CMS features this specific business owner can access.</p>
            
            <?php 
                $perms = json_decode($business['permissions'] ?? '{}', true) ?: []; 
            ?>
            <div class="space-y-3 mb-4">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="perm_screenshot" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500" <?= !empty($perms['screenshot_management']) ? 'checked' : '' ?>>
                    <span class="text-sm text-gray-700">Screenshot Management</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="perm_review" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500" <?= !empty($perms['review_management']) ? 'checked' : '' ?>>
                    <span class="text-sm text-gray-700">Review Management</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="perm_theme" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500" <?= !empty($perms['theme_management']) ? 'checked' : '' ?>>
                    <span class="text-sm text-gray-700">Theme Management</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="perm_cms" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500" <?= !empty($perms['website_cms']) ? 'checked' : '' ?>>
                    <span class="text-sm text-gray-700">Website CMS</span>
                </label>
            </div>
            <button type="submit" class="w-full bg-brand-600 text-white px-4 py-2 rounded shadow hover:bg-brand-700 text-sm font-semibold">Save Permissions</button>
        </form>
    </div>

    <!-- Add Subscription -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-bold mb-4 border-b pb-2">Assign Subscription / Extend</h2>
        <form action="<?= APP_URL ?>/admin/business/<?= $business['id'] ?>/subscription" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Plan</label>
                <select name="plan_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <?php foreach($plans as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $business['plan_id'] == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?> (<?= $p['duration_days'] ?> Days)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
                <input type="date" name="expiry_date" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Subscription Status</label>
                <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="active">Active</option>
                    <option value="expired">Expired</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 font-medium">Activate Subscription</button>
        </form>
    </div>
</div>

<!-- Subscription History -->
<div class="mt-6 bg-white shadow rounded-lg p-6">
    <h2 class="text-lg font-bold mb-4 border-b pb-2">Subscription Ledger</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recorded At</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach($subscriptions as $s): ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium"><?= htmlspecialchars($s['plan_name']) ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('d-M-Y', strtotime($s['start_date'])) ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold"><?= date('d-M-Y', strtotime($s['expiry_date'])) ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= ucfirst($s['status']) ?></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('d-M-Y H:i', strtotime($s['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/admin.php';
?>
