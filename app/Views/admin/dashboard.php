<?php ob_start(); ?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Platform Overview</h1>
    <p class="text-sm text-gray-500 mt-1">Real-time health and telemetry of the Invento SaaS platform.</p>
</div>

<!-- Key Metrics -->
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 sm:gap-6 mb-8">
    <div class="bg-white p-6 shadow-sm rounded-xl border border-gray-100 flex flex-col justify-center">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Tenants</p>
        <p class="text-3xl font-black text-gray-800"><?= $stats['total'] ?></p>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 shadow-sm rounded-xl text-white flex flex-col justify-center">
        <p class="text-xs font-bold text-green-100 uppercase tracking-wider mb-1">Active Accounts</p>
        <p class="text-3xl font-black text-white"><?= $stats['active'] ?></p>
    </div>
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 shadow-sm rounded-xl text-white flex flex-col justify-center">
        <p class="text-xs font-bold text-blue-100 uppercase tracking-wider mb-1">In Trial</p>
        <p class="text-3xl font-black text-white"><?= $stats['trial'] ?></p>
    </div>
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 p-6 shadow-sm rounded-xl text-white flex flex-col justify-center">
        <p class="text-xs font-bold text-yellow-100 uppercase tracking-wider mb-1">Expired</p>
        <p class="text-3xl font-black text-white"><?= $stats['expired'] ?></p>
    </div>
    <div class="bg-gradient-to-br from-red-500 to-red-600 p-6 shadow-sm rounded-xl text-white flex flex-col justify-center">
        <p class="text-xs font-bold text-red-100 uppercase tracking-wider mb-1">Suspended</p>
        <p class="text-3xl font-black text-white"><?= $stats['suspended'] ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
    
    <!-- Left Column: Businesses & Subscriptions -->
    <div class="lg:col-span-2 space-y-8">
        
        <!-- Expiring Subscriptions (Actionable) -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-red-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span>
                    Expiring Subscriptions (Next 14 Days)
                </h3>
                <a href="<?= APP_URL ?>/admin/subscriptions" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-wider">View All</a>
            </div>
            <div class="divide-y divide-gray-100">
                <?php if(empty($expiring_subs)): ?>
                    <div class="p-6 text-center text-sm text-gray-500">No subscriptions expiring soon.</div>
                <?php else: ?>
                    <?php foreach($expiring_subs as $sub): ?>
                        <div class="px-4 sm:px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0 hover:bg-gray-50 transition">
                            <div>
                                <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($sub['business_name']) ?></p>
                                <p class="text-xs text-gray-500 mt-1">Expires: <span class="font-semibold text-red-600"><?= date('M d, Y', strtotime($sub['expiry_date'])) ?></span></p>
                            </div>
                            <a href="<?= APP_URL ?>/admin/business/<?= $sub['business_id'] ?>" class="text-sm font-medium text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full hover:bg-indigo-100 transition">Renew</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Registrations -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <h3 class="text-lg font-bold text-gray-800">Recent Onboarding</h3>
                <a href="<?= APP_URL ?>/admin/businesses" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-wider">View All</a>
            </div>
            <div class="divide-y divide-gray-100">
                <?php if(empty($recent_businesses)): ?>
                    <div class="p-6 text-center text-sm text-gray-500">No recent signups.</div>
                <?php else: ?>
                    <?php foreach($recent_businesses as $b): ?>
                        <div class="px-4 sm:px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 sm:gap-0 hover:bg-gray-50 transition">
                            <div>
                                <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($b['business_name']) ?></p>
                                <p class="text-xs text-gray-500 mt-1">Owner: <?= htmlspecialchars($b['owner_name']) ?> • Joined <?= date('M d', strtotime($b['created_at'])) ?></p>
                            </div>
                            <?php
                                $color = 'gray';
                                if($b['status'] == 'active') $color = 'green';
                                if($b['status'] == 'trial') $color = 'blue';
                            ?>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?= $color ?>-100 text-<?= $color ?>-800">
                                <?= ucfirst($b['status']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Right Column: Activity & Health -->
    <div class="space-y-8">
        
        <!-- Platform Health -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Platform Health</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700">Database Load</span>
                        <span class="text-green-600 font-bold">Optimal</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-green-500 h-1.5 rounded-full" style="width: 12%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700">API Response Time</span>
                        <span class="text-green-600 font-bold">~45ms</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-green-500 h-1.5 rounded-full" style="width: 25%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700">Storage Usage</span>
                        <span class="text-indigo-600 font-bold">14%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-indigo-500 h-1.5 rounded-full" style="width: 14%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <h3 class="text-lg font-bold text-gray-800">Global Activity Stream</h3>
                <a href="<?= APP_URL ?>/admin/audit-logs" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-wider">Audit Log</a>
            </div>
            <div class="p-6">
                <ul class="space-y-4">
                    <?php if(empty($recent_activity)): ?>
                        <li class="text-sm text-gray-500 text-center">No recent activity recorded.</li>
                    <?php else: ?>
                        <?php foreach($recent_activity as $act): ?>
                            <li class="relative flex items-start space-x-3">
                                <div class="relative w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <span class="text-gray-500 text-xs font-bold"><?= substr($act['action'], 0, 1) ?></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div>
                                        <p class="text-sm text-gray-900 font-medium">
                                            <?= htmlspecialchars($act['business_name'] ?? 'System') ?>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <?= date('H:i A', strtotime($act['created_at'])) ?>
                                        </p>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-700">
                                        <?= htmlspecialchars($act['description']) ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

    </div>
</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/admin.php'; 
?>
