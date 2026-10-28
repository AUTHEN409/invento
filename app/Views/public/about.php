<?php ob_start(); ?>
<section class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">About Invento</h1>
        <div class="prose prose-lg text-gray-600">
            <p class="mb-4">Invento was built to solve a very specific problem: hardware, paint, and electrical store owners were forced to choose between complex, overpriced ERP systems or simplistic apps that couldn't handle true multi-store inventory.</p>
            
            <p class="mb-4">Our mission is to bring enterprise-grade retail architecture to everyday business owners. We believe that your software should be fast, secure, and intuitive.</p>

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">Our Philosophy</h2>
            <ul class="list-disc pl-5 space-y-2">
                <li><strong>Speed first:</strong> A slow POS system costs you customers.</li>
                <li><strong>No fake features:</strong> If it's on the screen, it does real work.</li>
                <li><strong>Your data is yours:</strong> We support comprehensive Excel exports anytime you want them.</li>
                <li><strong>Security is invisible:</strong> True multi-tenant isolation happens at the database level.</li>
            </ul>
        </div>
    </div>
</section>
<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/public.php'; 
?>
