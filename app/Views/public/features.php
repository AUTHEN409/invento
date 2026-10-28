<?php ob_start(); ?>

<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-6">Comprehensive Retail Features</h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-16">Everything you need to run a hardware or paint store efficiently, without the clutter of complex ERPs.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 text-left">
            <div class="p-6 border border-gray-200 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-2">📦 Multi-Store Inventory</h3>
                <p class="text-gray-600 text-sm">Create an unlimited number of stores under one business account. Keep stock accurately tracked per physical location.</p>
            </div>
            
            <div class="p-6 border border-gray-200 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-2">🧮 Smart GST Billing</h3>
                <p class="text-gray-600 text-sm">Automatically calculate CGST and SGST. Support for both tax-inclusive and tax-exclusive product pricing at checkout.</p>
            </div>

            <div class="p-6 border border-gray-200 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-2">🔄 Strict FIFO Engine</h3>
                <p class="text-gray-600 text-sm">Never lose track of your purchase margins. Our true FIFO system deducts stock from the oldest batches first for accurate accounting.</p>
            </div>

            <div class="p-6 border border-gray-200 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-2">💰 Credit & Udhaar</h3>
                <p class="text-gray-600 text-sm">Automatically generate credit ledger entries for partially paid bills. Track outstanding balances and log partial payments.</p>
            </div>

            <div class="p-6 border border-gray-200 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-2">📱 WhatsApp Reminders</h3>
                <p class="text-gray-600 text-sm">Select overdue customers and instantly generate pre-filled WhatsApp reminders directly from the application.</p>
            </div>

            <div class="p-6 border border-gray-200 rounded-lg shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-2">📊 Data Export & Import</h3>
                <p class="text-gray-600 text-sm">Keep your data portable. Import products directly from Excel (XLSX) files and export 8 different types of business reports.</p>
            </div>
        </div>
    </div>
</section>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/public.php'; 
?>
