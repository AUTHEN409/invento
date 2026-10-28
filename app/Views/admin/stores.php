<?php ob_start(); ?>

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Global Store Directory</h1>
        <p class="text-sm text-gray-500 mt-1">Hierarchical directory of businesses and their physical stores.</p>
    </div>
    <button onclick="openStoreModal('add')" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 font-medium text-sm">+ Add Store</button>
</div>

<!-- Search Form -->
<div class="bg-white shadow rounded-lg p-4 mb-6">
    <form action="<?= APP_URL ?>/admin/stores" method="GET" class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1 w-full">
            <input type="text" name="q" value="<?= htmlspecialchars($search_query ?? '') ?>" placeholder="Search by Business Name, Store Name, Mobile, or Store Code..." class="w-full border-gray-300 rounded-md p-2 border shadow-sm text-sm">
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded shadow hover:bg-indigo-700 text-sm font-medium">Search</button>
        <?php if(!empty($search_query)): ?>
            <a href="<?= APP_URL ?>/admin/stores" class="bg-gray-200 text-gray-800 px-4 py-2 rounded shadow hover:bg-gray-300 text-sm font-medium">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-10"></th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Business Info</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Owner & Mobile</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Subscription</th>
                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Total Stores</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            
            <?php if(empty($businesses)): ?>
            <tbody>
                <tr><td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">No businesses/stores found.</td></tr>
            </tbody>
            <?php else: ?>
                <?php foreach($businesses as $bid => $b): ?>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <!-- Parent Row (Business) -->
                    <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="toggleStores(<?= $bid ?>)">
                        <td class="px-6 py-4 text-center">
                            <svg id="icon-<?= $bid ?>" class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($b['name']) ?></div>
                            <div class="text-xs text-gray-500">ID: <?= $bid ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <div><?= htmlspecialchars($b['owner']) ?></div>
                            <div class="text-xs text-gray-500"><?= htmlspecialchars($b['mobile']) ?></div>
                        </td>
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
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-900">
                            <?= count($b['stores']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="event.stopPropagation(); openStoreModal('add', <?= $bid ?>)" class="text-indigo-600 hover:text-indigo-900">Add Store</button>
                        </td>
                    </tr>

                    <!-- Child Stores Container (Hidden by default) -->
                    <tr id="stores-<?= $bid ?>" class="hidden bg-gray-50">
                        <td colspan="6" class="p-0">
                            <?php if(empty($b['stores'])): ?>
                                <div class="p-4 text-center text-sm text-gray-500 italic">No stores registered for this business.</div>
                            <?php else: ?>
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="pl-16 pr-6 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Store Name</th>
                                            <th class="px-6 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Store Code</th>
                                            <th class="px-6 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Contact & Address</th>
                                            <th class="px-6 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                            <th class="px-6 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <?php foreach($b['stores'] as $store): ?>
                                        <tr class="hover:bg-white">
                                            <td class="pl-16 pr-6 py-3 whitespace-nowrap text-sm font-bold text-gray-800">
                                                <?= htmlspecialchars($store['name']) ?>
                                            </td>
                                            <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600">
                                                <?= htmlspecialchars($store['code']) ?>
                                            </td>
                                            <td class="px-6 py-3 text-sm text-gray-600 max-w-xs truncate">
                                                <div><?= htmlspecialchars($store['phone']) ?></div>
                                                <div class="text-xs text-gray-400 truncate"><?= htmlspecialchars($store['address']) ?></div>
                                            </td>
                                            <td class="px-6 py-3 whitespace-nowrap text-sm">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $store['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                                    <?= ucfirst($store['status']) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex flex-wrap gap-2 justify-end">
                                                <button onclick="openStoreModal('edit', <?= $bid ?>, <?= htmlspecialchars(json_encode($store)) ?>)" class="text-blue-600 hover:text-blue-900">Edit</button>
                                                
                                                <?php if($store['status'] === 'active'): ?>
                                                    <button onclick="submitStoreAction('suspend', <?= $store['id'] ?>)" class="text-yellow-600 hover:text-yellow-900">Suspend</button>
                                                <?php else: ?>
                                                    <button onclick="submitStoreAction('activate', <?= $store['id'] ?>)" class="text-green-600 hover:text-green-900">Activate</button>
                                                <?php endif; ?>
                                                
                                                <button onclick="submitStoreAction('delete', <?= $store['id'] ?>)" class="text-red-600 hover:text-red-900">Delete</button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- Add/Edit Store Modal -->
<div id="storeModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden max-h-[90vh] flex flex-col mx-4 sm:mx-0">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-bold text-gray-900" id="modalTitle">Add New Store</h3>
            <button onclick="closeStoreModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form action="<?= APP_URL ?>/admin/stores/action" method="POST" class="p-6 space-y-4 overflow-y-auto">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
            <input type="hidden" name="action" id="modalAction" value="add">
            <input type="hidden" name="store_id" id="modalStoreId" value="">
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Select Business</label>
                <select name="business_id" id="modalBusinessId" required class="mt-1 block w-full border-gray-300 rounded-md p-2 border shadow-sm">
                    <?php foreach($all_businesses as $ab): ?>
                        <option value="<?= $ab['id'] ?>"><?= htmlspecialchars($ab['business_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Store Name</label>
                <input type="text" name="store_name" id="modalStoreName" required class="mt-1 block w-full border-gray-300 rounded-md p-2 border shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Store Code</label>
                <input type="text" name="store_code" id="modalStoreCode" required class="mt-1 block w-full border-gray-300 rounded-md p-2 border shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" id="modalPhone" required class="mt-1 block w-full border-gray-300 rounded-md p-2 border shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <textarea name="address" id="modalAddress" rows="2" class="mt-1 block w-full border-gray-300 rounded-md p-2 border shadow-sm"></textarea>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 font-medium">Save Store</button>
            </div>
        </form>
    </div>
</div>

<form id="actionForm" method="POST" action="<?= APP_URL ?>/admin/stores/action" class="hidden">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    <input type="hidden" name="action" id="afAction">
    <input type="hidden" name="store_id" id="afStoreId">
</form>

<script>
    function toggleStores(bid) {
        const row = document.getElementById('stores-' + bid);
        const icon = document.getElementById('icon-' + bid);
        if (row.classList.contains('hidden')) {
            row.classList.remove('hidden');
            icon.classList.add('rotate-90');
        } else {
            row.classList.add('hidden');
            icon.classList.remove('rotate-90');
        }
    }

    function openStoreModal(action, bid = null, store = null) {
        document.getElementById('storeModal').classList.remove('hidden');
        document.getElementById('modalAction').value = action;
        
        const bSelect = document.getElementById('modalBusinessId');
        if(bid) bSelect.value = bid;
        
        if (action === 'edit' && store) {
            document.getElementById('modalTitle').innerText = 'Edit Store';
            document.getElementById('modalStoreId').value = store.id;
            document.getElementById('modalStoreName').value = store.name;
            document.getElementById('modalStoreCode').value = store.code;
            document.getElementById('modalPhone').value = store.phone;
            document.getElementById('modalAddress').value = store.address;
            bSelect.disabled = true; // prevent changing business during edit
            
            // Re-enable before submit so value is sent
            bSelect.closest('form').onsubmit = () => bSelect.disabled = false;
        } else {
            document.getElementById('modalTitle').innerText = 'Add New Store';
            document.getElementById('modalStoreId').value = '';
            document.getElementById('modalStoreName').value = '';
            document.getElementById('modalStoreCode').value = '';
            document.getElementById('modalPhone').value = '';
            document.getElementById('modalAddress').value = '';
            bSelect.disabled = false;
            bSelect.closest('form').onsubmit = null;
        }
    }

    function closeStoreModal() {
        document.getElementById('storeModal').classList.add('hidden');
    }

    function submitStoreAction(action, storeId) {
        let msg = `Are you sure you want to ${action} this store?`;
        if (action === 'delete') {
            msg = "WARNING: Deleting this store will permanently remove it. Are you absolutely sure?";
        }
        
        if(confirm(msg)) {
            document.getElementById('afAction').value = action;
            document.getElementById('afStoreId').value = storeId;
            document.getElementById('actionForm').submit();
        }
    }
</script>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/admin.php'; 
?>
