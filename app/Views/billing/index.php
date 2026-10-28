<?php ob_start(); ?>

<div class="h-full flex flex-col md:flex-row gap-6">
    <!-- Left Panel: Search and Cart -->
    <div class="w-full md:w-2/3 flex flex-col bg-white shadow rounded-lg h-[80vh] overflow-hidden">
        
        <!-- Search Bar -->
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="relative">
                <input type="text" id="productSearch" placeholder="Search product by name or SKU..." autocomplete="off"
                    class="block w-full pl-4 pr-10 py-3 text-lg border-gray-300 rounded-md focus:ring-brand-500 focus:border-brand-500 shadow-sm">
                <div id="searchResults" class="absolute z-10 w-full bg-white mt-1 rounded-md shadow-lg hidden max-h-60 overflow-auto border border-gray-200">
                    <!-- Search results injected here via JS -->
                </div>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-auto p-4">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-sm font-semibold text-gray-500 border-b">
                        <th class="pb-2">Product</th>
                        <th class="pb-2 w-24">Price</th>
                        <th class="pb-2 w-24">Qty</th>
                        <th class="pb-2 w-24 text-right">Total</th>
                        <th class="pb-2 w-10"></th>
                    </tr>
                </thead>
                <tbody id="cartTable" class="divide-y divide-gray-100">
                    <!-- Cart items injected here via JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Panel: Checkout / Summary -->
    <div class="w-full md:w-1/3 bg-white shadow rounded-lg p-6 flex flex-col h-[80vh]">
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Order Summary</h2>
        
        <div class="flex-1 overflow-y-auto space-y-4">
            <!-- Customer Information -->
            <div class="space-y-3 bg-gray-50 p-3 rounded border border-gray-100">
                <label class="block text-sm font-bold text-gray-700 border-b pb-1 border-gray-200">Customer Details</label>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700">Phone Number (Required for Credit)</label>
                    <input type="text" id="customerPhone" placeholder="Mobile Number" class="mt-1 block w-full rounded-md border-gray-300 py-2 px-3 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700">Customer Name</label>
                    <input type="text" id="customerName" placeholder="Full Name" class="mt-1 block w-full rounded-md border-gray-300 py-2 px-3 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
                </div>
            </div>

            <!-- Invoice Discount -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Discount (₹)</label>
                <input type="number" id="invoiceDiscount" value="0" min="0" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 py-2 px-3 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
            </div>

            <!-- Totals -->
            <div class="bg-gray-50 rounded p-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal:</span>
                    <span id="summarySubtotal">₹0.00</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Tax (GST):</span>
                    <span id="summaryTax">₹0.00</span>
                </div>
                <div class="flex justify-between font-bold text-lg text-gray-900 border-t pt-2 mt-2">
                    <span>Grand Total:</span>
                    <span id="summaryTotal">₹0.00</span>
                </div>
            </div>

            <!-- Payment -->
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700">Payment Mode</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="border rounded p-2 text-center cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="cash" checked class="mr-1"> Cash
                    </label>
                    <label class="border rounded p-2 text-center cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="upi" class="mr-1"> UPI
                    </label>
                    <label class="border rounded p-2 text-center cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_method" value="credit" class="mr-1"> Credit
                    </label>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Amount Received (₹)</label>
                    <input type="number" id="paidAmount" min="0" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 py-2 px-3 shadow-sm focus:border-brand-500 focus:ring-brand-500 text-lg font-bold">
                    <p class="text-xs text-red-500 mt-1" id="dueText"></p>
                </div>
            </div>
        </div>

        <button id="btnComplete" class="mt-4 w-full bg-brand-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-lg font-medium text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transition">
            Complete Bill
        </button>
    </div>
</div>

<script>
    let cart = [];
    const searchInput = document.getElementById('productSearch');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    // Search Logic
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const q = e.target.value.trim();
        if(q.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch('<?= APP_URL ?>/billing/search?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                if(data.length === 0) {
                    searchResults.innerHTML = `
                        <div class="p-4 text-center">
                            <div class="text-gray-500 text-sm mb-2">No products found or out of stock.</div>
                            <a href="<?= APP_URL ?>/products/create" target="_blank" class="inline-block bg-brand-100 text-brand-700 hover:bg-brand-200 px-3 py-1 rounded text-sm font-medium">
                                + Quick Add Product
                            </a>
                        </div>
                    `;
                } else {
                    searchResults.innerHTML = '';
                    data.forEach(p => {
                        const div = document.createElement('div');
                        div.className = 'p-3 hover:bg-brand-50 cursor-pointer border-b last:border-b-0 flex justify-between items-center';
                        
                        const isOutOfStock = p.available_qty <= 0 || p.available_qty === null;
                        
                        div.innerHTML = `
                            <div>
                                <div class="font-bold text-gray-800">${p.name}</div>
                                <div class="text-xs text-gray-500">${p.sku ? 'SKU: '+p.sku : ''} | Stock: ${isOutOfStock ? '<span class="text-red-500 font-bold">0</span>' : p.available_qty}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-brand-600">₹${p.selling_price}</div>
                                <div class="text-xs text-gray-500">GST: ${p.gst_percent}% (${p.tax_mode})</div>
                            </div>
                        `;
                        
                        if(!isOutOfStock) {
                            div.onclick = () => addToCart(p);
                        } else {
                            div.classList.add('opacity-50', 'cursor-not-allowed');
                        }
                        
                        searchResults.appendChild(div);
                    });
                }
                searchResults.classList.remove('hidden');
            })
            .catch(err => {
                const errorMsg = err.message || "Error searching products.";
                searchResults.innerHTML = `<div class="p-4 text-red-500 text-sm font-bold">Error: ${errorMsg}</div>`;
                searchResults.classList.remove('hidden');
                console.error(err);
            });
        }, 300);
    });

    document.addEventListener('click', e => {
        if(!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });

    function addToCart(p) {
        const existing = cart.find(i => i.product_id === p.id);
        if(existing) {
            if(existing.quantity + 1 > p.available_qty) {
                alert("Cannot exceed available stock: " + p.available_qty);
                return;
            }
            existing.quantity++;
        } else {
            cart.push({
                product_id: p.id,
                name: p.name,
                price: parseFloat(p.selling_price),
                gst_percent: parseFloat(p.gst_percent),
                tax_mode: p.tax_mode,
                quantity: 1,
                max_qty: parseFloat(p.available_qty)
            });
        }
        searchInput.value = '';
        searchResults.classList.add('hidden');
        renderCart();
    }

    function updateQty(id, qty) {
        const item = cart.find(i => i.product_id === id);
        if(item) {
            let q = parseFloat(qty) || 1;
            if(q > item.max_qty) {
                alert("Only " + item.max_qty + " available.");
                q = item.max_qty;
            }
            if(q <= 0) q = 1;
            item.quantity = q;
            renderCart();
        }
    }

    function removeItem(id) {
        cart = cart.filter(i => i.product_id !== id);
        renderCart();
    }

    function renderCart() {
        const tbody = document.getElementById('cartTable');
        tbody.innerHTML = '';
        
        let subtotal = 0;
        let totalGst = 0;

        cart.forEach(item => {
            const rowTotal = item.price * item.quantity;
            
            let itemTaxable, itemGst;
            if(item.tax_mode === 'inclusive') {
                itemTaxable = rowTotal / (1 + (item.gst_percent / 100));
                itemGst = rowTotal - itemTaxable;
            } else {
                itemTaxable = rowTotal;
                itemGst = itemTaxable * (item.gst_percent / 100);
            }
            
            subtotal += itemTaxable;
            totalGst += itemGst;

            const finalRowTotal = itemTaxable + itemGst;

            tbody.innerHTML += `
                <tr>
                    <td class="py-3">
                        <div class="font-medium text-gray-900">${item.name}</div>
                        <div class="text-xs text-gray-500">GST: ${item.gst_percent}%</div>
                    </td>
                    <td class="py-3">
                        <input type="number" value="${item.price}" min="0" step="0.01" 
                            onchange="updatePrice(${item.product_id}, this.value)"
                            class="w-24 border border-gray-300 rounded px-2 py-1">
                    </td>
                    <td class="py-3">
                        <input type="number" value="${item.quantity}" min="1" max="${item.max_qty}" 
                            onchange="updateQty(${item.product_id}, this.value)"
                            class="w-16 border border-gray-300 rounded text-center py-1">
                    </td>
                    <td class="py-3 text-right font-medium">₹${finalRowTotal.toFixed(2)}</td>
                    <td class="py-3 text-right">
                        <button onclick="removeItem(${item.product_id})" class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </td>
                </tr>
            `;
        });

        const discount = parseFloat(document.getElementById('invoiceDiscount').value) || 0;
        const grandTotalRaw = subtotal + totalGst - discount;
        const grandTotal = Math.round(grandTotalRaw); // Round off

        document.getElementById('summarySubtotal').innerText = '₹' + subtotal.toFixed(2);
        document.getElementById('summaryTax').innerText = '₹' + totalGst.toFixed(2);
        document.getElementById('summaryTotal').innerText = '₹' + grandTotal.toFixed(2);

        // Update Due Text
        const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
        const due = grandTotal - paid;
        if(due > 0) {
            document.getElementById('dueText').innerText = `Due: ₹${due.toFixed(2)} (Will be saved as Credit)`;
        } else if (due < 0) {
            document.getElementById('dueText').innerText = `Change: ₹${Math.abs(due).toFixed(2)}`;
        } else {
            document.getElementById('dueText').innerText = "";
        }
    }

    function updatePrice(id, newPrice) {
        const item = cart.find(i => i.product_id === id);
        if(item) {
            let p = parseFloat(newPrice);
            if(isNaN(p) || p < 0) p = 0;
            item.price = p;
            renderCart();
        }
    }

    document.getElementById('invoiceDiscount').addEventListener('input', renderCart);
    document.getElementById('paidAmount').addEventListener('input', renderCart);

    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    paymentRadios.forEach(r => r.addEventListener('change', function() {
        const paidInput = document.getElementById('paidAmount');
        if(this.value === 'credit') {
            paidInput.value = 0;
            paidInput.disabled = true;
        } else {
            paidInput.disabled = false;
        }
        renderCart();
    }));

    // Submission
    document.getElementById('btnComplete').addEventListener('click', function() {
        if(cart.length === 0) {
            alert("Cart is empty!"); return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerText = 'Processing...';

        const discount = parseFloat(document.getElementById('invoiceDiscount').value) || 0;
        const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        const cPhone = document.getElementById('customerPhone').value.trim();
        const cName = document.getElementById('customerName').value.trim();

        // Validation for credit
        const grandTotalRaw = parseFloat(document.getElementById('summaryTotal').innerText.replace('₹',''));
        if ((grandTotalRaw - paid) > 0 && !cPhone) {
            alert("Customer Phone Number is required for Credit (due balance) billing.");
            btn.disabled = false;
            btn.innerText = 'Complete Bill';
            return;
        }

        const formData = new URLSearchParams();
        formData.append('csrf_token', '<?= $csrf_token ?>');
        formData.append('customer_phone', cPhone);
        formData.append('customer_name', cName);
        formData.append('items', JSON.stringify(cart));
        formData.append('payment_method', method);
        formData.append('paid_amount', paid);
        formData.append('invoice_discount', discount);

        fetch('<?= APP_URL ?>/billing/process', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: formData.toString()
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                window.location.href = '<?= APP_URL ?>/invoices/' + data.invoice_id;
            } else {
                alert("Error: " + data.error);
                btn.disabled = false;
                btn.innerText = 'Complete Bill';
            }
        })
        .catch(err => {
            alert("Network error.");
            btn.disabled = false;
            btn.innerText = 'Complete Bill';
        });
    });
</script>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/main.php'; 
?>
