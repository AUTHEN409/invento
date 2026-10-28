<?php ob_start(); ?>
<section class="bg-white py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-8 text-center">Contact Sales & Support</h1>
        
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 shadow-sm">
            <form action="#" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Business Name</label>
                    <input type="text" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Message</label>
                    <textarea rows="4" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
                <button type="button" class="w-full bg-brand-600 text-white font-bold py-3 rounded-md hover:bg-brand-700">Send Message</button>
            </form>
        </div>
    </div>
</section>
<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/public.php'; 
?>
