<?php ob_start(); ?>

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-center text-gray-900 mb-4">Pricing Plans</h1>
        <p class="text-xl text-center text-gray-600 mb-16">Simple, transparent pricing. No hidden fees.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach($plans as $plan): ?>
            <div class="border border-gray-200 rounded-lg shadow-sm p-8 bg-white flex flex-col relative <?= strtolower($plan['name']) === 'pro' ? 'ring-2 ring-brand-500' : '' ?>">
                <?php if(strtolower($plan['name']) === 'pro'): ?>
                    <span class="absolute top-0 right-0 bg-brand-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg uppercase tracking-wider">Most Popular</span>
                <?php endif; ?>
                
                <h2 class="text-2xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($plan['name']) ?></h2>
                <p class="text-gray-500 mb-6 min-h-[48px]"><?= htmlspecialchars($plan['description']) ?></p>
                
                <div class="mb-6">
                    <span class="text-4xl font-black text-gray-900">₹<?= number_format($plan['price'], 0) ?></span>
                    <span class="text-gray-500">/<?= $plan['duration_days'] ?> days</span>
                </div>
                
                <ul class="space-y-4 mb-8 flex-1 text-gray-700">
                    <li class="flex items-center"><span class="text-green-500 mr-2">✓</span> Up to <?= $plan['max_stores'] ?> Store(s)</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✓</span> Up to <?= $plan['max_users'] ?> Staff Account(s)</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✓</span> Unlimited Products</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✓</span> Unlimited Invoices</li>
                    <li class="flex items-center"><span class="text-green-500 mr-2">✓</span> Data Exports</li>
                </ul>
                
                <a href="<?= APP_URL ?>/register?plan=<?= $plan['id'] ?>" class="block w-full py-3 px-4 text-center rounded-md font-bold transition <?= strtolower($plan['name']) === 'pro' ? 'bg-brand-600 text-white hover:bg-brand-700 shadow-md' : 'bg-brand-50 text-brand-700 hover:bg-brand-100' ?>">
                    Get Started
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/public.php'; 
?>
