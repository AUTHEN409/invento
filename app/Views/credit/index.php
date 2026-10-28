<?php ob_start(); ?>

<div class="mb-6 sm:flex sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Credit Management</h1>
        <p class="mt-2 text-sm text-gray-700">Track outstanding balances and send reminders.</p>
    </div>
</div>

<!-- Reminder Actions -->
<div class="mb-4 flex space-x-4 bg-white p-4 shadow rounded-lg items-center">
    <button id="btnRemindSelected" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 flex items-center space-x-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
        <span>Generate Reminders</span>
    </button>
    <span class="text-sm text-gray-500" id="selectedCount">0 selected</span>
</div>

<div class="flex flex-col">
    <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="relative w-12 px-6 sm:w-16 sm:px-8">
                                <input type="checkbox" id="selectAll" class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 sm:left-6">
                            </th>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Customer</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Phone</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total Credit</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total Paid</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-red-600">Outstanding</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Last Reminder</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">View</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php if(empty($credits)): ?>
                        <tr><td colspan="8" class="px-3 py-4 text-center text-sm text-gray-500">No outstanding credit.</td></tr>
                        <?php else: ?>
                            <?php foreach($credits as $c): ?>
                            <tr>
                                <td class="relative w-12 px-6 sm:w-16 sm:px-8">
                                    <input type="checkbox" class="rowCheckbox absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 sm:left-6"
                                        data-id="<?= $c['id'] ?>"
                                        data-name="<?= htmlspecialchars($c['name']) ?>"
                                        data-phone="<?= htmlspecialchars($c['phone']) ?>"
                                        data-outstanding="<?= $c['outstanding_balance'] ?>">
                                </td>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6"><?= htmlspecialchars($c['name']) ?></td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"><?= htmlspecialchars($c['phone']) ?></td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">₹<?= number_format($c['total_credit'], 2) ?></td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">₹<?= number_format($c['total_paid'], 2) ?></td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-bold text-red-600">₹<?= number_format($c['outstanding_balance'], 2) ?></td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <?= $c['last_reminder'] ? date('d-m-Y H:i', strtotime($c['last_reminder'])) : 'Never' ?>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a href="<?= APP_URL ?>/credit/<?= $c['id'] ?>" class="text-brand-600 hover:text-brand-900">Ledger & Pay</a>
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

<!-- Reminder Queue Modal -->
<div id="reminderModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900" id="modalTitle">Confirm Reminders</h3>
            <button id="btnCancelModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Confirm Step -->
        <div id="confirmStep" class="p-6 text-center">
            <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2" id="confirmText">You are about to generate reminders for X customers.</h2>
            <p class="text-gray-500 mb-6">This will log a reminder in their history and prepare the WhatsApp messages.</p>
            <div class="flex justify-center space-x-4">
                <button id="btnCancelConfirm" class="px-6 py-2 border border-gray-300 rounded shadow-sm text-gray-700 bg-white hover:bg-gray-50 font-medium">Cancel</button>
                <button id="btnGenerate" class="px-6 py-2 bg-brand-600 text-white rounded shadow hover:bg-brand-700 font-bold">Generate</button>
            </div>
        </div>

        <!-- Processing & Queue Step -->
        <div id="queueStep" class="hidden flex-col h-full overflow-hidden">
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center text-sm">
                <span class="font-bold text-gray-700" id="queueStatusText">Processing...</span>
                <div class="flex space-x-4">
                    <span class="text-green-600 font-semibold" id="successCount">Success: 0</span>
                    <span class="text-red-600 font-semibold" id="failCount">Failed: 0</span>
                </div>
            </div>
            <div class="p-4 overflow-y-auto flex-1 bg-gray-100 space-y-4" id="queueContainer">
                <!-- Queue Items inserted here -->
            </div>
            <div class="p-4 border-t border-gray-200 bg-white text-right">
                <button id="btnDone" class="px-6 py-2 bg-gray-800 text-white rounded shadow hover:bg-gray-900 font-bold hidden">Done (Reload Page)</button>
            </div>
        </div>

    </div>
</div>

<script>
    const businessName = <?= json_encode($business['business_name'] ?? 'Our Business') ?>;
    const csrfToken = <?= json_encode($csrf_token) ?>;
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.rowCheckbox');
    const btnRemind = document.getElementById('btnRemindSelected');
    const countDisplay = document.getElementById('selectedCount');

    // Modal elements
    const modal = document.getElementById('reminderModal');
    const confirmStep = document.getElementById('confirmStep');
    const queueStep = document.getElementById('queueStep');
    const confirmText = document.getElementById('confirmText');
    const btnCancelModal = document.getElementById('btnCancelModal');
    const btnCancelConfirm = document.getElementById('btnCancelConfirm');
    const btnGenerate = document.getElementById('btnGenerate');
    const queueContainer = document.getElementById('queueContainer');
    const successCountEl = document.getElementById('successCount');
    const failCountEl = document.getElementById('failCount');
    const queueStatusText = document.getElementById('queueStatusText');
    const btnDone = document.getElementById('btnDone');

    function updateCount() {
        const count = document.querySelectorAll('.rowCheckbox:checked').length;
        countDisplay.innerText = count + " selected";
    }

    selectAll.addEventListener('change', e => {
        checkboxes.forEach(cb => cb.checked = e.target.checked);
        updateCount();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', updateCount));

    function closeModal() {
        modal.classList.add('hidden');
    }

    btnCancelModal.addEventListener('click', closeModal);
    btnCancelConfirm.addEventListener('click', closeModal);
    
    btnDone.addEventListener('click', () => {
        window.location.reload();
    });

    let selectedData = [];

    btnRemind.addEventListener('click', () => {
        const checked = document.querySelectorAll('.rowCheckbox:checked');
        if(checked.length === 0) {
            alert("Please select at least one customer.");
            return;
        }

        selectedData = Array.from(checked).map(cb => ({
            id: cb.getAttribute('data-id'),
            name: cb.getAttribute('data-name'),
            phone: cb.getAttribute('data-phone'),
            outstanding: cb.getAttribute('data-outstanding')
        }));

        confirmText.innerText = `You are about to generate reminders for ${selectedData.length} customer(s).`;
        
        // Reset modal state
        confirmStep.classList.remove('hidden');
        queueStep.classList.add('hidden');
        queueStep.classList.remove('flex');
        queueContainer.innerHTML = '';
        btnDone.classList.add('hidden');
        successCountEl.innerText = `Success: 0`;
        failCountEl.innerText = `Failed: 0`;
        queueStatusText.innerText = `Processing...`;

        modal.classList.remove('hidden');
    });

    btnGenerate.addEventListener('click', async () => {
        confirmStep.classList.add('hidden');
        queueStep.classList.remove('hidden');
        queueStep.classList.add('flex');

        let successCounter = 0;
        let failCounter = 0;

        for (let i = 0; i < selectedData.length; i++) {
            const customer = selectedData[i];
            const amountStr = parseFloat(customer.outstanding).toLocaleString('en-IN');
            
            // Validate Phone
            const rawPhone = customer.phone.replace(/\D/g, '');
            if (!rawPhone || rawPhone.length < 10) {
                failCounter++;
                failCountEl.innerText = `Failed: ${failCounter}`;
                
                queueContainer.innerHTML += `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex justify-between items-center shadow-sm">
                        <div>
                            <h4 class="font-bold text-gray-900">${customer.name}</h4>
                            <p class="text-sm text-red-600 font-medium mt-1">Error: Missing or invalid phone number (${customer.phone})</p>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-gray-900">₹${amountStr}</span>
                        </div>
                    </div>
                `;
                continue;
            }

            const msg = `Namaste ${customer.name},\n\nAapke account me ₹${amountStr} baki hain.\nKripya payment karein.\n\nRegards,\n${businessName}`;
            
            try {
                // Log it asynchronously
                const formData = new URLSearchParams();
                formData.append('csrf_token', csrfToken);
                formData.append('customer_id', customer.id);
                formData.append('message_text', msg);

                await fetch('<?= APP_URL ?>/credit/remind', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData.toString()
                });

                successCounter++;
                successCountEl.innerText = `Success: ${successCounter}`;
                
                const waUrl = `https://wa.me/91${rawPhone}?text=${encodeURIComponent(msg)}`;
                
                queueContainer.innerHTML += `
                    <div class="bg-white border border-gray-200 rounded-lg p-4 flex justify-between items-center shadow-sm">
                        <div>
                            <h4 class="font-bold text-gray-900">${customer.name}</h4>
                            <p class="text-xs text-gray-500 font-mono mt-1">${rawPhone}</p>
                            <p class="text-sm text-gray-700 mt-2 italic bg-gray-50 p-2 rounded border border-gray-100 max-w-md break-words">"${msg.replace(/\n/g, '<br>')}"</p>
                        </div>
                        <div class="text-right flex flex-col items-end">
                            <span class="text-lg font-bold text-red-600 mb-3">₹${amountStr}</span>
                            <a href="${waUrl}" target="_blank" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded font-bold shadow flex items-center space-x-2 text-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.82 9.82 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                                <span>Open WhatsApp</span>
                            </a>
                        </div>
                    </div>
                `;
            } catch (e) {
                failCounter++;
                failCountEl.innerText = `Failed: ${failCounter}`;
                queueContainer.innerHTML += `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex justify-between items-center shadow-sm">
                        <div>
                            <h4 class="font-bold text-gray-900">${customer.name}</h4>
                            <p class="text-sm text-red-600 font-medium mt-1">Error: Failed to save reminder log</p>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-gray-900">₹${amountStr}</span>
                        </div>
                    </div>
                `;
            }
        }

        queueStatusText.innerText = `Completed!`;
        btnDone.classList.remove('hidden');
    });
</script>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
