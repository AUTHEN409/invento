<?php ob_start(); ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Platform Settings</h1>
    <p class="text-sm text-gray-500 mt-1">Configure global SaaS variables, APIs, and platform policies.</p>
</div>

<?php if(isset($_SESSION['success'])): ?>
<div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 font-medium text-sm">
    <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<form action="<?= APP_URL ?>/admin/settings/save" method="POST" class="space-y-8">
    
    <!-- Branding & Core Settings -->
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-900">Branding & Core</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Platform Name</label>
                <input type="text" name="settings[platform_name]" value="<?= htmlspecialchars($settings['platform_name'] ?? 'Invento SaaS') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Base Currency</label>
                <input type="text" name="settings[currency]" value="<?= htmlspecialchars($settings['currency'] ?? '₹') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Primary Theme Color (Hex)</label>
                <input type="text" name="settings[theme_color]" value="<?= htmlspecialchars($settings['theme_color'] ?? '#0f766e') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Trial Days</label>
                <input type="number" name="settings[trial_days]" value="<?= htmlspecialchars($settings['trial_days'] ?? '7') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-sm">
            </div>
        </div>
    </div>

    <!-- Communication APIs -->
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-900">Communication & APIs</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">SMTP Host</label>
                <input type="text" name="settings[smtp_host]" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-sm" placeholder="smtp.gmail.com">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">SMTP Port</label>
                <input type="text" name="settings[smtp_port]" value="<?= htmlspecialchars($settings['smtp_port'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-sm" placeholder="587">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">SMTP Username</label>
                <input type="text" name="settings[smtp_user]" value="<?= htmlspecialchars($settings['smtp_user'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">SMTP Password</label>
                <input type="password" name="settings[smtp_pass]" value="<?= htmlspecialchars($settings['smtp_pass'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Twilio / SMS API Key</label>
                <input type="text" name="settings[sms_api]" value="<?= htmlspecialchars($settings['sms_api'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">WhatsApp Business API</label>
                <input type="text" name="settings[whatsapp_api]" value="<?= htmlspecialchars($settings['whatsapp_api'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500 text-sm">
            </div>
        </div>
    </div>

    <!-- Feature Toggles -->
    <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-900">Feature Toggles</h3>
        </div>
        <div class="p-6 space-y-4">
            <label class="flex items-center space-x-3 cursor-pointer">
                <input type="checkbox" name="settings[enable_registration]" value="1" <?= ($settings['enable_registration'] ?? '1') == '1' ? 'checked' : '' ?> class="form-checkbox h-5 w-5 text-brand-600 rounded">
                <span class="text-gray-800 font-medium text-sm">Enable Public Registration</span>
            </label>
            <label class="flex items-center space-x-3 cursor-pointer">
                <input type="checkbox" name="settings[enable_maintenance]" value="1" <?= ($settings['enable_maintenance'] ?? '0') == '1' ? 'checked' : '' ?> class="form-checkbox h-5 w-5 text-red-600 rounded">
                <span class="text-gray-800 font-medium text-sm">Maintenance Mode (Block all logins except Super Admin)</span>
            </label>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-brand-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-brand-700 transition-colors shadow-md">
            Save Platform Settings
        </button>
    </div>

</form>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/admin.php'; 
?>
