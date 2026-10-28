<?php ob_start(); ?>

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-0">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Website CMS</h1>
        <p class="text-sm text-gray-500 mt-1">Manage public landing page content, reviews, screenshots, and global pricing.</p>
    </div>
    <a href="<?= APP_URL ?>/" target="_blank" class="px-4 py-2 bg-gray-900 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition shadow">Preview Live Site</a>
</div>

<?php if(isset($_SESSION['success'])): ?>
<div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 font-medium text-sm">
    <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<!-- Tabs -->
<div class="mb-6 border-b border-gray-200">
    <nav class="-mb-px flex space-x-6 sm:space-x-8 overflow-x-auto scrollbar-hide" aria-label="Tabs">
        <button onclick="switchTab('hero')" class="cms-tab active border-brand-500 text-brand-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Hero & General</button>
        <button onclick="switchTab('pricing')" class="cms-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Global Pricing</button>
        <button onclick="switchTab('reviews')" class="cms-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Reviews & Testimonials</button>
        <button onclick="switchTab('media')" class="cms-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Screenshots</button>
    </nav>
</div>

<!-- Tab: Hero & General Content -->
<div id="tab-hero" class="cms-content-section block space-y-6">
    <form action="<?= APP_URL ?>/admin/cms/update" method="POST" class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <input type="hidden" name="section" value="hero">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-900">Hero Section Content</h3>
        </div>
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Headline</label>
                <input type="text" name="cms[headline]" value="<?= htmlspecialchars($cms['hero']['headline'] ?? 'Control Every Store. Track Every Sale. Manage Everything.') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Sub-headline</label>
                <textarea name="cms[subheadline]" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 text-sm"><?= htmlspecialchars($cms['hero']['subheadline'] ?? 'Inventory, billing, customers, credit, staff and analytics from one centralized platform.') ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Call to Action (Primary Button)</label>
                <input type="text" name="cms[cta_text]" value="<?= htmlspecialchars($cms['hero']['cta_text'] ?? 'Start Free Trial') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 text-sm">
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
            <button type="submit" class="bg-brand-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-brand-700 transition">Save Changes</button>
        </div>
    </form>
</div>

<!-- Tab: Global Pricing -->
<div id="tab-pricing" class="cms-content-section hidden space-y-6">
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0">
            <h3 class="font-bold text-gray-900">Global Subscription Plans</h3>
            <button class="bg-gray-900 text-white px-4 py-2 rounded text-sm font-semibold">Create New Plan</button>
        </div>
        <div class="p-0 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Plan Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Cycle</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Limits</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <?php foreach($plans ?? [] as $plan): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900"><?= htmlspecialchars($plan['name']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">₹<?= number_format($plan['price'], 2) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize"><?= htmlspecialchars($plan['billing_cycle']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Stores: <?= $plan['max_stores'] ?> | Users: <?= $plan['max_users'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <button class="text-indigo-600 hover:text-indigo-900 font-semibold mr-3">Edit</button>
                            <button class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($plans)): ?>
                    <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No active plans. Create one to allow businesses to sign up.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Reviews -->
<div id="tab-reviews" class="cms-content-section hidden space-y-6">
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0">
            <h3 class="font-bold text-gray-900">Platform Testimonials</h3>
            <button class="bg-gray-900 text-white px-4 py-2 rounded text-sm font-semibold">Add Manual Review</button>
        </div>
        <div class="p-0 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Reviewer</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Business</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Review</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <?php foreach($reviews ?? [] as $rev): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900"><?= htmlspecialchars($rev['reviewer_name']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($rev['business_name']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate"><?= htmlspecialchars($rev['review_text']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 rounded text-xs font-bold <?= $rev['status'] === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?> uppercase"><?= htmlspecialchars($rev['status']) ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <button class="text-green-600 hover:text-green-900 font-semibold mr-3">Approve</button>
                            <button class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($reviews)): ?>
                    <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No reviews found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab: Media / Screenshots -->
<div id="tab-media" class="cms-content-section hidden space-y-6">
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 sm:gap-0">
            <h3 class="font-bold text-gray-900">Landing Page Screenshots (Carousel)</h3>
            <button class="bg-gray-900 text-white px-4 py-2 rounded text-sm font-semibold">Upload Image</button>
        </div>
        <div class="p-6">
            <?php if(empty($screenshots)): ?>
            <div class="text-center py-12 border-2 border-dashed border-gray-200 rounded-xl">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No screenshots</h3>
                <p class="mt-1 text-sm text-gray-500">Upload software UI images to populate the landing page carousel.</p>
            </div>
            <?php else: ?>
                <!-- Grid of images -->
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.cms-content-section').forEach(el => el.classList.add('hidden'));
    document.getElementById('tab-' + tabId).classList.remove('hidden');
    
    document.querySelectorAll('.cms-tab').forEach(el => {
        el.classList.remove('border-brand-500', 'text-brand-600');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    event.currentTarget.classList.remove('border-transparent', 'text-gray-500');
    event.currentTarget.classList.add('border-brand-500', 'text-brand-600');
}
</script>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/admin.php'; 
?>
