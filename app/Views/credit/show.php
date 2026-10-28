<?php ob_start(); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Credit Ledger: <?= htmlspecialchars($customer['name']) ?></h1>
        <p class="text-sm text-gray-500 mt-1">Phone: <?= htmlspecialchars($customer['phone']) ?></p>
    </div>
    <a href="<?= APP_URL ?>/credit" class="text-brand-600 hover:text-brand-900 font-medium">← Back to Credit List</a>
</div>

<?php 
$total_credit = 0;
$total_paid = 0;
foreach($ledger as $l) {
    if($l['transaction_type'] === 'credit_sale') $total_credit += $l['amount'];
    if($l['transaction_type'] === 'payment') $total_paid += $l['amount'];
}
$outstanding = $total_credit - $total_paid;
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white shadow rounded-lg p-5 border-t-4 border-gray-400">
        <p class="text-sm font-medium text-gray-500">Total Credit</p>
        <p class="text-2xl font-bold text-gray-900">₹<?= number_format($total_credit, 2) ?></p>
    </div>
    <div class="bg-white shadow rounded-lg p-5 border-t-4 border-green-500">
        <p class="text-sm font-medium text-gray-500">Total Paid</p>
        <p class="text-2xl font-bold text-gray-900">₹<?= number_format($total_paid, 2) ?></p>
    </div>
    <div class="bg-white shadow rounded-lg p-5 border-t-4 <?= $outstanding > 0 ? 'border-red-500' : 'border-green-500' ?>">
        <p class="text-sm font-medium text-gray-500">Current Outstanding</p>
        <p class="text-2xl font-bold <?= $outstanding > 0 ? 'text-red-600' : 'text-green-600' ?>">₹<?= number_format($outstanding, 2) ?></p>
    </div>
</div>

<div class="flex flex-col md:flex-row gap-6">
    <!-- Ledger Table -->
    <div class="w-full md:w-2/3 bg-white shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Transaction History</h3>
        </div>
        <div class="overflow-x-auto max-h-[60vh]">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach($ledger as $tx): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= date('d-m-Y', strtotime($tx['created_at'])) ?><br>
                            <span class="text-xs"><?= date('H:i:s', strtotime($tx['created_at'])) ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php if($tx['transaction_type'] === 'credit_sale'): ?>
                                <span class="text-red-600 font-medium">Credit Issued</span>
                            <?php else: ?>
                                <span class="text-green-600 font-medium">Payment Received</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php if($tx['invoice_number']): ?>
                                <a href="<?= APP_URL ?>/invoices/<?= $tx['invoice_id'] ?>" class="text-brand-600 hover:underline"><?= htmlspecialchars($tx['invoice_number']) ?></a>
                            <?php else: ?>
                                <?= htmlspecialchars($tx['notes'] ?? '-') ?>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right <?= $tx['transaction_type'] === 'credit_sale' ? 'text-red-600' : 'text-green-600' ?>">
                            <?= $tx['transaction_type'] === 'credit_sale' ? '+' : '-' ?>₹<?= number_format($tx['amount'], 2) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($tx['user_name']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payment Collection Form -->
    <div class="w-full md:w-1/3 space-y-6">
        <?php if($outstanding > 0): ?>
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Record Payment</h3>
            <form action="<?= APP_URL ?>/credit/pay" method="POST" id="paymentForm" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="customer_id" value="<?= $customer['id'] ?>">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Amount Received (₹)</label>
                    <input type="number" id="payAmount" name="amount" required min="1" max="<?= $outstanding ?>" step="0.01" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-lg font-bold">
                    <p class="text-xs text-gray-500 mt-1">Max: ₹<?= number_format($outstanding, 2) ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Payment Mode</label>
                    <select name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="cash">Cash</option>
                        <option value="upi">UPI</option>
                        <option value="bank">Bank Transfer</option>
                    </select>
                </div>

                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                    Save Payment
                </button>
            </form>
        </div>
        <?php else: ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-green-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h3 class="text-lg font-medium text-green-800">Account Cleared</h3>
            <p class="text-sm text-green-600 mt-1">There is no outstanding balance for this customer.</p>
            
            <button id="btnSendClearance" class="mt-4 w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                Send Clearance WhatsApp
            </button>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Auto-open WhatsApp if cleared parameter is passed
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('cleared') === '1') {
        const msg = urlParams.get('msg');
        const phone = urlParams.get('phone');
        if (msg && phone) {
            const url = `https://wa.me/91${phone.replace(/\D/g, '')}?text=${encodeURIComponent(msg)}`;
            window.open(url, '_blank');
            // Remove params from URL to prevent reopening on refresh
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }

    <?php if($outstanding == 0): ?>
    document.getElementById('btnSendClearance')?.addEventListener('click', async () => {
        const name = <?= json_encode($customer['name']) ?>;
        const phone = <?= json_encode($customer['phone']) ?>;
        
        const msg = `Namaste ${name},\n\nAapka outstanding balance successfully clear ho gaya hai.\n\nThank you,\nBusiness Name`; // Simplified for MVP since we don't pass business name here directly
        
        const formData = new URLSearchParams();
        formData.append('csrf_token', <?= json_encode($csrf_token) ?>);
        formData.append('customer_id', <?= json_encode($customer['id']) ?>);
        formData.append('message_text', msg);
        formData.append('message_type', 'credit_cleared');

        await fetch('<?= APP_URL ?>/credit/remind', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: formData.toString()
        });

        const url = `https://wa.me/91${phone.replace(/\D/g, '')}?text=${encodeURIComponent(msg)}`;
        window.open(url, '_blank');
        window.location.reload();
    });
    <?php endif; ?>
</script>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
