<?php ob_start(); ?>

<div class="min-h-[80vh] flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-2xl border-t-4 border-indigo-600 p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Super Admin</h2>
            <p class="text-gray-500 mt-2 text-sm">System Management Access</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-50 text-red-700 p-3 rounded mb-6 text-sm font-semibold border border-red-200">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="<?= APP_URL ?>/admin/login" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Admin Username</label>
                <input type="text" name="username" id="username" required 
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required 
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50">
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition uppercase tracking-wide">
                    Authenticate
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/guest.php'; 
?>
