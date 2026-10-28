<?php ob_start(); ?>

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0">
    <h1 class="text-2xl font-bold text-gray-900">Manage Businesses</h1>
    <a href="<?= APP_URL ?>/admin" class="text-indigo-600 hover:underline">← Back to Dashboard</a>
</div>

<!-- Business Statistics -->
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white shadow rounded-lg p-4 text-center border-t-4 border-gray-500">
        <div class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total</div>
        <div class="text-2xl font-bold text-gray-900"><?= number_format($stats['total'] ?? 0) ?></div>
    </div>
    <div class="bg-white shadow rounded-lg p-4 text-center border-t-4 border-green-500">
        <div class="text-xs text-green-500 uppercase font-bold tracking-wider">Active</div>
        <div class="text-2xl font-bold text-gray-900"><?= number_format($stats['active'] ?? 0) ?></div>
    </div>
    <div class="bg-white shadow rounded-lg p-4 text-center border-t-4 border-blue-500">
        <div class="text-xs text-blue-500 uppercase font-bold tracking-wider">Trial</div>
        <div class="text-2xl font-bold text-gray-900"><?= number_format($stats['trial'] ?? 0) ?></div>
    </div>
    <div class="bg-white shadow rounded-lg p-4 text-center border-t-4 border-yellow-500">
        <div class="text-xs text-yellow-500 uppercase font-bold tracking-wider">Expired</div>
        <div class="text-2xl font-bold text-gray-900"><?= number_format($stats['expired'] ?? 0) ?></div>
    </div>
    <div class="bg-white shadow rounded-lg p-4 text-center border-t-4 border-red-500">
        <div class="text-xs text-red-500 uppercase font-bold tracking-wider">Suspended</div>
        <div class="text-2xl font-bold text-gray-900"><?= number_format($stats['suspended'] ?? 0) ?></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Table & Filters -->
    <div class="lg:col-span-2 space-y-4">
        
        <!-- Filters & Search -->
        <div class="bg-white shadow rounded-lg p-4">
            <form action="<?= APP_URL ?>/admin/businesses" method="GET" class="flex flex-col sm:flex-row gap-4 items-stretch sm:items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Search (Name, Phone, ID)</label>
                    <input type="text" name="q" value="<?= htmlspecialchars($search_query ?? '') ?>" placeholder="Search businesses..." class="w-full border-gray-300 rounded-md p-2 border shadow-sm text-sm">
                </div>
                <div class="w-full sm:w-48">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Status Filter</label>
                    <select name="status" class="w-full border-gray-300 rounded-md p-2 border shadow-sm text-sm">
                        <option value="all" <?= ($status_filter ?? 'all') === 'all' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="active" <?= ($status_filter ?? 'all') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="trial" <?= ($status_filter ?? 'all') === 'trial' ? 'selected' : '' ?>>Trial</option>
                        <option value="expired" <?= ($status_filter ?? 'all') === 'expired' ? 'selected' : '' ?>>Expired</option>
                        <option value="suspended" <?= ($status_filter ?? 'all') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 text-sm font-medium w-full sm:w-auto">Filter</button>
                </div>
            </form>
        </div>

        <!-- Bulk Action Form & Table -->
        <form action="<?= APP_URL ?>/admin/businesses/bulk" method="POST" id="bulkForm" class="bg-white shadow rounded-lg overflow-hidden">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
            
            <div class="bg-gray-50 px-4 py-3 border-b flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-2 sm:space-y-0">
                <div class="flex items-center space-x-2">
                    <select name="action" id="bulkAction" class="border-gray-300 rounded-md py-1 px-2 border text-sm shadow-sm">
                        <option value="">Bulk Actions...</option>
                        <option value="suspend">Suspend Selected</option>
                        <option value="activate">Activate Selected</option>
                        <option value="delete">Delete Selected</option>
                    </select>
                    <button type="button" onclick="submitBulk()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 rounded text-sm font-medium">Apply</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left w-10">
                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Business</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php if(empty($businesses)): ?>
                            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No businesses found matching your criteria.</td></tr>
                        <?php else: ?>
                            <?php foreach($businesses as $b): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="business_ids[]" value="<?= $b['id'] ?>" class="business-cb rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="font-bold text-indigo-600">ID: <?= $b['id'] ?></div>
                                    <span class="font-bold"><?= htmlspecialchars($b['business_name']) ?></span><br>
                                    <span class="text-gray-500 text-xs"><?= htmlspecialchars($b['phone']) ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($b['plan_name'] ?: 'None') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php
                                        $color = 'gray';
                                        if($b['status'] == 'active') $color = 'green';
                                        if($b['status'] == 'trial') $color = 'blue';
                                        if($b['status'] == 'expired') $color = 'yellow';
                                        if($b['status'] == 'suspended') $color = 'red';
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-<?= $color ?>-100 text-<?= $color ?>-800">
                                        <?= ucfirst($b['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex flex-wrap gap-2 justify-end">
                                    <a href="<?= APP_URL ?>/admin/business/<?= $b['id'] ?>" class="text-indigo-600 hover:text-indigo-900">Manage</a>
                                    
                                    <?php if($b['status'] === 'suspended'): ?>
                                        <button type="button" onclick="quickAction(<?= $b['id'] ?>, 'activate')" class="text-green-600 hover:text-green-900">Activate</button>
                                    <?php elseif($b['status'] === 'active'): ?>
                                        <button type="button" onclick="quickAction(<?= $b['id'] ?>, 'suspend')" class="text-yellow-600 hover:text-yellow-900">Suspend</button>
                                    <?php endif; ?>

                                    <button type="button" onclick="deleteBusiness(<?= $b['id'] ?>)" class="text-red-600 hover:text-red-900">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if(($total_pages ?? 1) > 1): ?>
            <div class="px-4 py-3 bg-gray-50 border-t flex flex-col sm:flex-row justify-between items-center space-y-2 sm:space-y-0">
                <div class="text-sm text-gray-700">
                    Page <span class="font-bold"><?= $current_page ?></span> of <span class="font-bold"><?= $total_pages ?></span>
                </div>
                <div class="flex space-x-2">
                    <?php if($current_page > 1): ?>
                        <a href="?page=<?= $current_page - 1 ?>&status=<?= htmlspecialchars($status_filter ?? 'all') ?>&q=<?= urlencode($search_query ?? '') ?>" class="px-3 py-1 bg-white border rounded text-sm hover:bg-gray-50">Previous</a>
                    <?php endif; ?>
                    <?php if($current_page < $total_pages): ?>
                        <a href="?page=<?= $current_page + 1 ?>&status=<?= htmlspecialchars($status_filter ?? 'all') ?>&q=<?= urlencode($search_query ?? '') ?>" class="px-3 py-1 bg-white border rounded text-sm hover:bg-gray-50">Next</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Add Form -->
    <div class="bg-white shadow rounded-lg p-6 h-fit">
        <h2 class="text-lg font-bold mb-4 border-b pb-2">Onboard New Tenant</h2>
        <form action="<?= APP_URL ?>/admin/businesses" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700">Business Name</label>
                <input type="text" name="business_name" required class="mt-1 block w-full border-gray-300 rounded-md p-2 border shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Owner Name</label>
                <input type="text" name="owner_name" required class="mt-1 block w-full border-gray-300 rounded-md p-2 border shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" required class="mt-1 block w-full border-gray-300 rounded-md p-2 border shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md p-2 border shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Assign Plan</label>
                <select name="plan_id" required class="mt-1 block w-full border-gray-300 rounded-md p-2 border shadow-sm">
                    <?php foreach($plans ?? [] as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700">Create Tenant</button>
        </form>
    </div>

</div>

<!-- Hidden Quick Action Forms -->
<form id="quickActionForm" method="POST" action="<?= APP_URL ?>/admin/businesses/bulk" class="hidden">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    <input type="hidden" name="action" id="qa_action" value="">
    <input type="hidden" name="business_ids[]" id="qa_id" value="">
</form>

<form id="deleteForm" method="POST" action="" class="hidden">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
</form>

<script>
    // Select All
    const selectAll = document.getElementById('selectAll');
    const cbs = document.querySelectorAll('.business-cb');
    
    if(selectAll) {
        selectAll.addEventListener('change', (e) => {
            cbs.forEach(cb => cb.checked = e.target.checked);
        });
    }

    function submitBulk() {
        const action = document.getElementById('bulkAction').value;
        if (!action) return alert('Select an action first.');
        
        let checked = false;
        cbs.forEach(cb => { if(cb.checked) checked = true; });
        if (!checked) return alert('Select at least one business.');

        if (action === 'delete') {
            if(!confirm("Are you sure you want to permanently delete selected businesses and all associated data?\n\nThis action cannot be undone.")) return;
        } else {
            if(!confirm(`Are you sure you want to ${action} selected businesses?`)) return;
        }

        document.getElementById('bulkForm').submit();
    }

    function quickAction(id, action) {
        if(!confirm(`Are you sure you want to ${action} this business?`)) return;
        document.getElementById('qa_action').value = action;
        document.getElementById('qa_id').value = id;
        document.getElementById('quickActionForm').submit();
    }

    function deleteBusiness(id) {
        if(confirm("Are you sure you want to permanently delete this business and all associated data?\n\nThis action cannot be undone!")) {
            const form = document.getElementById('deleteForm');
            form.action = '<?= APP_URL ?>/admin/businesses/delete/' + id;
            form.submit();
        }
    }
</script>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/admin.php';
?>
