<?php ob_start(); ?>

<div class="max-w-4xl mx-auto bg-white p-8 shadow-sm border border-gray-200" id="printableInvoice">
    <!-- Header -->
    <div class="border-b pb-6 mb-6">
        <h1 class="text-2xl font-bold text-center tracking-widest uppercase mb-4">TAX INVOICE</h1>
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($invoice['business_name']) ?></h2>
                <p class="text-sm font-semibold text-gray-700"><?= htmlspecialchars($invoice['store_name']) ?></p>
                <p class="text-sm text-gray-600 mt-1 w-64"><?= nl2br(htmlspecialchars($invoice['store_address'])) ?></p>
                <p class="text-sm text-gray-600">Phone: <?= htmlspecialchars($invoice['store_phone']) ?></p>
                <?php if($invoice['store_gst']): ?>
                <p class="text-sm text-gray-600">GSTIN: <?= htmlspecialchars($invoice['store_gst']) ?></p>
                <?php endif; ?>
            </div>
            <div class="text-right">
                <p class="text-sm font-bold text-gray-800">Invoice No: <span class="text-gray-900"><?= htmlspecialchars($invoice['invoice_number']) ?></span></p>
                <p class="text-sm text-gray-600 mt-1">Date: <?= date('d-m-Y', strtotime($invoice['invoice_date'])) ?></p>
                <p class="text-sm text-gray-600">Time: <?= $invoice['invoice_time'] ?></p>
            </div>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="mb-6">
        <h3 class="text-sm font-bold text-gray-700 uppercase border-b pb-1 mb-2">Billed To</h3>
        <?php if($invoice['customer_id']): ?>
            <p class="text-base font-semibold text-gray-900"><?= htmlspecialchars($invoice['customer_name']) ?></p>
            <p class="text-sm text-gray-600">Phone: <?= htmlspecialchars($invoice['customer_phone']) ?></p>
        <?php else: ?>
            <p class="text-base text-gray-900">Walk-in Customer</p>
        <?php endif; ?>
    </div>

    <!-- Items Table -->
    <table class="w-full text-left text-sm mb-6 border-collapse">
        <thead>
            <tr class="bg-gray-100 text-gray-800 uppercase text-xs border-b border-t border-gray-300">
                <th class="py-2 px-2 border-r border-gray-300">Description</th>
                <th class="py-2 px-2 border-r border-gray-300">Category</th>
                <th class="py-2 px-2 text-center border-r border-gray-300">Qty</th>
                <th class="py-2 px-2 text-right border-r border-gray-300">Rate</th>
                <th class="py-2 px-2 text-right border-r border-gray-300">GST %</th>
                <th class="py-2 px-2 text-right border-r border-gray-300">Taxable</th>
                <th class="py-2 px-2 text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $item): ?>
            <tr class="border-b border-gray-200">
                <td class="py-2 px-2 border-r border-gray-300"><?= htmlspecialchars($item['product_name_snapshot']) ?></td>
                <td class="py-2 px-2 text-gray-600 border-r border-gray-300"><?= htmlspecialchars($item['category_name_snapshot'] ?: 'N/A') ?></td>
                <td class="py-2 px-2 text-center border-r border-gray-300"><?= (float)$item['quantity'] ?></td>
                <td class="py-2 px-2 text-right border-r border-gray-300">₹<?= number_format($item['rate'], 2) ?></td>
                <td class="py-2 px-2 text-right border-r border-gray-300"><?= (float)$item['gst_percent'] ?>%</td>
                <td class="py-2 px-2 text-right border-r border-gray-300">₹<?= number_format($item['taxable_amount'], 2) ?></td>
                <td class="py-2 px-2 text-right font-medium">₹<?= number_format($item['line_total'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Totals -->
    <div class="flex justify-end mb-8">
        <div class="w-64 text-sm">
            <div class="flex justify-between py-1 border-b border-dashed border-gray-300">
                <span class="text-gray-600">Subtotal:</span>
                <span class="font-medium">₹<?= number_format($invoice['subtotal'], 2) ?></span>
            </div>
            <?php if($invoice['discount_amount'] > 0): ?>
            <div class="flex justify-between py-1 border-b border-dashed border-gray-300 text-red-600">
                <span>Discount:</span>
                <span class="font-medium">-₹<?= number_format($invoice['discount_amount'], 2) ?></span>
            </div>
            <?php endif; ?>
            <div class="flex justify-between py-1 border-b border-dashed border-gray-300">
                <span class="text-gray-600">Taxable Amount:</span>
                <span class="font-medium">₹<?= number_format($invoice['taxable_amount'], 2) ?></span>
            </div>
            <?php if($invoice['cgst_amount'] > 0): ?>
            <div class="flex justify-between py-1 text-gray-500">
                <span>CGST:</span>
                <span>₹<?= number_format($invoice['cgst_amount'], 2) ?></span>
            </div>
            <div class="flex justify-between py-1 text-gray-500 border-b border-dashed border-gray-300">
                <span>SGST:</span>
                <span>₹<?= number_format($invoice['sgst_amount'], 2) ?></span>
            </div>
            <?php endif; ?>
            <div class="flex justify-between py-1 border-b border-gray-300">
                <span class="text-gray-600">Round Off:</span>
                <span>₹<?= number_format($invoice['round_off'], 2) ?></span>
            </div>
            <div class="flex justify-between py-2 border-b-2 border-gray-800 text-lg font-bold">
                <span>Grand Total:</span>
                <span>₹<?= number_format($invoice['grand_total'], 2) ?></span>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-200 space-y-1">
                <div class="flex justify-between">
                    <span class="text-gray-600">Paid Amount:</span>
                    <span class="font-bold text-green-600">₹<?= number_format($invoice['paid_amount'], 2) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Due Amount:</span>
                    <span class="font-bold <?= $invoice['due_amount'] > 0 ? 'text-red-600' : 'text-gray-900' ?>">₹<?= number_format($invoice['due_amount'], 2) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-xs text-gray-500 border-t pt-4 flex justify-between">
        <div>
            <p>Created By: <span class="font-semibold text-gray-700"><?= htmlspecialchars($invoice['created_by_name'] ?? 'System') ?></span></p>
            <p>Generated On: <?= date('d-m-Y H:i:s') ?></p>
        </div>
        <div class="text-right">
            <p class="font-medium uppercase tracking-wider text-gray-400 text-lg"><?= $invoice['payment_status'] ?></p>
        </div>
    </div>
</div>

<!-- Non-printable Actions -->
<div class="max-w-4xl mx-auto mt-6 flex justify-end space-x-4 print:hidden">
    <a href="<?= APP_URL ?>/billing" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">Back to Billing</a>
    <button onclick="window.print()" class="px-4 py-2 bg-brand-600 text-white rounded hover:bg-brand-700">Print Invoice</button>
</div>

<style>
    @media print {
        body { background: white; }
        nav, main > div:not(#printableInvoice) { display: none !important; }
        #printableInvoice { border: none; box-shadow: none; padding: 0; }
    }
</style>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
