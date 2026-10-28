<?php ob_start(); ?>

<!-- Scroll Animations & Interactivity -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const observerOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('opacity-100', 'translate-y-0');
                    entry.target.classList.remove('opacity-0', 'translate-y-12');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.reveal').forEach(el => {
            el.classList.add('opacity-0', 'translate-y-12', 'transition-all', 'duration-1000', 'ease-out');
            observer.observe(el);
        });

        // Screenshot Carousel Logic
        window.currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const indicators = document.querySelectorAll('.carousel-indicator');
        
        window.showSlide = function(index) {
            slides.forEach((s, i) => {
                s.style.opacity = i === index ? '1' : '0';
                s.style.zIndex = i === index ? '10' : '0';
                s.style.transform = i === index ? 'scale(1)' : 'scale(0.98)';
            });
            indicators.forEach((ind, i) => {
                if(i === index) {
                    ind.classList.add('bg-teal-500', 'w-8');
                    ind.classList.remove('bg-gray-600', 'w-3');
                } else {
                    ind.classList.remove('bg-teal-500', 'w-8');
                    ind.classList.add('bg-gray-600', 'w-3');
                }
            });
            window.currentSlide = index;
        };

        if(slides.length > 0) showSlide(0);

        setInterval(() => {
            if(slides.length > 0) {
                let next = (window.currentSlide + 1) % slides.length;
                showSlide(next);
            }
        }, 5000);
    });
</script>

<style>
    :root {
        --teal-glow: rgba(15, 118, 110, 0.4);
        --blue-glow: rgba(37, 99, 235, 0.3);
    }
    .bg-grid-dark {
        background-image: linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px),
                          linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px);
        background-size: 50px 50px;
    }
    .glass-panel {
        background: rgba(20, 20, 20, 0.6);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .glow-shadow {
        box-shadow: 0 0 40px var(--teal-glow);
    }
    .text-gradient {
        background: linear-gradient(135deg, #fff 0%, #a1a1aa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .teal-gradient-text {
        background: linear-gradient(135deg, #2dd4bf 0%, #0f766e 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    /* Hide scrollbar for horizontal scrolling containers */
    .hide-scroll::-webkit-scrollbar {
        display: none;
    }
    .hide-scroll {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<div class="bg-black overflow-hidden font-sans text-gray-300">

    <!-- HERO SECTION -->
    <section class="relative min-h-screen flex items-center pt-24 pb-20 overflow-hidden bg-grid-dark">
        <!-- Ambient Background Glows -->
        <div class="absolute top-1/4 left-0 w-96 h-96 bg-teal-900 rounded-full mix-blend-screen filter blur-[120px] opacity-40 animate-pulse"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-blue-900 rounded-full mix-blend-screen filter blur-[150px] opacity-30"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-teal-800 rounded-full mix-blend-screen filter blur-[200px] opacity-20"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                
                <!-- Left: Typography & CTA -->
                <div class="reveal">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-teal-500/30 bg-teal-500/10 text-teal-400 text-xs font-bold uppercase tracking-widest mb-8 backdrop-blur-md">
                        <span class="flex h-2 w-2 rounded-full bg-teal-500 shadow-[0_0_8px_#14b8a6]"></span>
                        Multi-Store Retail SaaS
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-black text-white tracking-tighter leading-[1.1] mb-6 text-gradient">
                        Control Every Store. <br>
                        Track Every Sale. <br>
                        <span class="teal-gradient-text">Manage Everything.</span>
                    </h1>
                    
                    <p class="text-xl text-gray-400 mb-10 leading-relaxed max-w-xl font-light">
                        Inventory, billing, customers, credit, staff and analytics from one centralized, lightning-fast platform.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 mb-12">
                        <a href="<?= APP_URL ?>/#pricing" class="bg-white text-black px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-200 transition-all shadow-[0_0_20px_rgba(255,255,255,0.15)] transform hover:-translate-y-1 text-center">Start Free Trial</a>
                        <a href="<?= APP_URL ?>/contact" class="glass-panel text-white border border-gray-700 px-8 py-4 rounded-xl font-bold text-lg hover:border-gray-500 hover:bg-gray-900 transition-all text-center">Book Demo</a>
                    </div>

                    <div class="flex items-center gap-8 text-sm font-semibold text-gray-500">
                        <div class="flex flex-col">
                            <span class="text-white text-xl font-black">500+</span>
                            <span>Active Stores</span>
                        </div>
                        <div class="h-8 w-px bg-gray-800"></div>
                        <div class="flex flex-col">
                            <span class="text-white text-xl font-black">1M+</span>
                            <span>Products Managed</span>
                        </div>
                        <div class="h-8 w-px bg-gray-800"></div>
                        <div class="flex flex-col">
                            <span class="text-white text-xl font-black">99.9%</span>
                            <span>Platform Uptime</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Live Dashboard Mockup -->
                <div class="relative reveal hidden lg:block perspective-1000">
                    <div class="absolute inset-0 bg-gradient-to-tr from-teal-500 to-blue-600 rounded-[2rem] blur-2xl opacity-20 animate-pulse"></div>
                    <div class="relative glass-panel rounded-[2rem] border border-gray-700 shadow-2xl overflow-hidden transform rotate-y-[-5deg] rotate-x-[5deg] hover:rotate-0 transition-transform duration-700 aspect-[4/3] flex flex-col">
                        
                        <!-- Mac OS Top Bar -->
                        <div class="h-10 bg-[#1a1a1a] border-b border-gray-800 flex items-center px-4 gap-2">
                            <div class="w-3 h-3 rounded-full bg-[#ff5f56]"></div>
                            <div class="w-3 h-3 rounded-full bg-[#ffbd2e]"></div>
                            <div class="w-3 h-3 rounded-full bg-[#27c93f]"></div>
                            <div class="ml-4 flex-1 bg-black rounded h-6 border border-gray-800 flex items-center px-3">
                                <svg class="w-3 h-3 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span class="text-[10px] text-gray-500 font-mono">app.invento.com</span>
                            </div>
                        </div>

                        <!-- Dashboard Content Mock -->
                        <div class="flex-1 bg-[#0a0a0a] p-6 flex flex-col gap-4 relative overflow-hidden">
                            <!-- Sidebar Mock -->
                            <div class="absolute left-0 top-0 bottom-0 w-16 border-r border-gray-800 bg-[#111] flex flex-col items-center py-4 space-y-6">
                                <div class="w-8 h-8 rounded bg-teal-600 mb-4"></div>
                                <div class="w-6 h-6 rounded bg-gray-800"></div>
                                <div class="w-6 h-6 rounded bg-gray-800"></div>
                                <div class="w-6 h-6 rounded bg-gray-800"></div>
                                <div class="w-6 h-6 rounded bg-gray-800"></div>
                            </div>
                            
                            <div class="ml-16 flex-1 flex flex-col gap-4">
                                <!-- Top Stats -->
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="bg-[#111] border border-gray-800 rounded-xl p-4 flex flex-col justify-center relative overflow-hidden">
                                        <div class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Today's Sales</div>
                                        <div class="text-2xl font-bold text-white">₹ 42,500</div>
                                        <div class="absolute right-2 bottom-2 text-[10px] text-green-400">+12%</div>
                                    </div>
                                    <div class="bg-[#111] border border-gray-800 rounded-xl p-4 flex flex-col justify-center">
                                        <div class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Pending Credit</div>
                                        <div class="text-2xl font-bold text-red-400">₹ 18,200</div>
                                    </div>
                                    <div class="bg-teal-900/30 border border-teal-800/50 rounded-xl p-4 flex flex-col justify-center">
                                        <div class="text-[10px] text-teal-500 uppercase tracking-widest mb-1">Low Stock Alerts</div>
                                        <div class="text-2xl font-bold text-teal-400">14 Items</div>
                                    </div>
                                </div>
                                
                                <!-- Chart Area -->
                                <div class="flex-1 bg-[#111] border border-gray-800 rounded-xl p-4 flex flex-col">
                                    <div class="text-xs text-gray-500 mb-4 uppercase tracking-widest">Revenue Overview</div>
                                    <div class="flex-1 flex items-end justify-between gap-2 px-2">
                                        <div class="w-full bg-teal-500/20 rounded-t h-[40%] hover:bg-teal-500/40 transition-colors"></div>
                                        <div class="w-full bg-teal-500/20 rounded-t h-[60%] hover:bg-teal-500/40 transition-colors"></div>
                                        <div class="w-full bg-teal-500/40 rounded-t h-[30%] hover:bg-teal-500/60 transition-colors"></div>
                                        <div class="w-full bg-teal-500/60 rounded-t h-[80%] glow-shadow border-t border-teal-400"></div>
                                        <div class="w-full bg-teal-500/20 rounded-t h-[50%] hover:bg-teal-500/40 transition-colors"></div>
                                        <div class="w-full bg-teal-500/20 rounded-t h-[70%] hover:bg-teal-500/40 transition-colors"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- SECTION 2 - LIVE SOFTWARE SHOWCASE (Horizontal Scroll Cards) -->
    <section id="showcase" class="py-24 border-y border-gray-900 bg-[#050505]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12 reveal">
            <h2 class="text-3xl lg:text-4xl font-black text-white tracking-tight text-gradient">Built for Speed & Scale</h2>
            <p class="text-gray-500 mt-2">Every module is interconnected for a flawless retail experience.</p>
        </div>

        <div class="flex overflow-x-auto hide-scroll gap-6 px-4 sm:px-6 lg:px-8 pb-8 snap-x snap-mandatory">
            <?php 
            $showcase = [
                ['title' => 'Lightning Billing', 'desc' => 'Barcode ready POS with instant search.', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['title' => 'Real-time Inventory', 'desc' => 'Track stock movement globally.', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                ['title' => 'Credit Tracking', 'desc' => 'Never lose track of udhaar again.', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['title' => 'Multi Store', 'desc' => 'Centralized control for all branches.', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ['title' => 'Customer Profiles', 'desc' => 'Detailed purchase histories.', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                ['title' => 'Advanced Reports', 'desc' => 'Excel exports and profit tracking.', 'icon' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z']
            ];
            foreach($showcase as $item): ?>
            <div class="snap-center shrink-0 w-80 glass-panel p-8 rounded-3xl border border-gray-800 hover:border-teal-500/50 hover:bg-gray-900/50 transition-all duration-500 group relative overflow-hidden cursor-pointer">
                <!-- Hover Glow -->
                <div class="absolute inset-0 bg-gradient-to-br from-teal-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="w-12 h-12 bg-gray-800 rounded-xl flex items-center justify-center mb-6 group-hover:bg-teal-500/20 group-hover:text-teal-400 transition-colors border border-gray-700">
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-teal-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?= $item['icon'] ?>" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2 relative z-10"><?= $item['title'] ?></h3>
                <p class="text-sm text-gray-500 relative z-10"><?= $item['desc'] ?></p>
                
                <!-- Mini UI Mockup inside Card -->
                <div class="mt-8 h-20 bg-black border border-gray-800 rounded-t-xl relative overflow-hidden opacity-50 group-hover:opacity-100 group-hover:translate-y-[-5px] transition-all duration-500">
                    <div class="h-2 bg-gray-800 w-full mb-2"></div>
                    <div class="px-2 space-y-2">
                        <div class="h-1.5 bg-gray-800 w-3/4 rounded"></div>
                        <div class="h-1.5 bg-gray-800 w-1/2 rounded"></div>
                        <div class="h-1.5 bg-teal-900 w-full rounded"></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- SECTION 3 - HOW INVENTO WORKS -->
    <section id="workflow" class="py-32 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal">
            <div class="text-center mb-24">
                <h2 class="text-sm font-bold text-teal-500 tracking-[0.2em] uppercase mb-4">The Pipeline</h2>
                <h3 class="text-4xl lg:text-5xl font-black text-white text-gradient">A connected business workflow.</h3>
            </div>
            
            <div class="relative max-w-4xl mx-auto mt-12">
                <!-- Vertical Line for Mobile, Horizontal for Desktop -->
                <div class="absolute left-[1.15rem] md:left-0 md:top-5 w-1 h-full md:w-full md:h-1 bg-gray-800 z-0 rounded-full">
                    <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b md:bg-gradient-to-r from-teal-500 to-blue-500 opacity-60 shadow-[0_0_15px_rgba(20,184,166,0.6)]"></div>
                </div>

                <div class="flex flex-col md:flex-row justify-between relative z-10 space-y-12 md:space-y-0">
                    <?php 
                    $flow = ['Store Setup', 'Add Inventory', 'Billing', 'Credit tracking', 'Reports'];
                    foreach($flow as $index => $step): ?>
                    <div class="flex flex-row md:flex-col items-center gap-6 md:gap-4 group ml-0 cursor-default flex-1">
                        <div class="w-10 h-10 rounded-full bg-[#0a0a0a] border-2 border-teal-500/50 flex items-center justify-center font-bold text-teal-400 group-hover:border-teal-400 group-hover:text-white group-hover:shadow-[0_0_20px_rgba(20,184,166,0.6)] group-hover:bg-teal-500/20 transition-all duration-300 relative z-10 shrink-0 backdrop-blur-sm">
                            <?= $index + 1 ?>
                        </div>
                        <span class="text-lg md:text-sm font-semibold text-gray-300 group-hover:text-white transition-colors text-center"><?= $step ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 4 - REAL SOFTWARE SCREENSHOTS (Carousel) -->
    <section id="screenshots" class="py-24 bg-[#050505] border-y border-gray-900 relative">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 reveal">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-black text-white mb-4">See It In Action</h2>
                <p class="text-gray-500">Beautiful, intuitive interfaces that your staff will learn in minutes.</p>
            </div>

            <!-- Browser Carousel Container -->
            <div class="relative glass-panel rounded-2xl border border-gray-700 shadow-2xl aspect-video overflow-hidden group">
                <!-- Mac Bar -->
                <div class="absolute top-0 left-0 w-full h-10 bg-[#111] border-b border-gray-800 flex items-center px-4 gap-2 z-20">
                    <div class="w-3 h-3 rounded-full bg-gray-700"></div>
                    <div class="w-3 h-3 rounded-full bg-gray-700"></div>
                    <div class="w-3 h-3 rounded-full bg-gray-700"></div>
                </div>

                <!-- Slides Container -->
                <div class="relative w-full h-full pt-10 bg-black">
                    <!-- Slide 1 -->
                    <div class="carousel-slide absolute inset-0 pt-10 flex items-center justify-center transition-all duration-700 ease-in-out opacity-100 z-10">
                        <div class="text-center border border-dashed border-gray-700 p-12 rounded-xl bg-gray-900/30">
                            <span class="text-gray-500 text-sm font-mono">[ Placeholder: Upload Dashboard.png ]</span>
                        </div>
                    </div>
                    <!-- Slide 2 -->
                    <div class="carousel-slide absolute inset-0 pt-10 flex items-center justify-center transition-all duration-700 ease-in-out opacity-0 z-0 scale-95">
                        <div class="text-center border border-dashed border-gray-700 p-12 rounded-xl bg-gray-900/30">
                            <span class="text-gray-500 text-sm font-mono">[ Placeholder: Upload Billing.png ]</span>
                        </div>
                    </div>
                    <!-- Slide 3 -->
                    <div class="carousel-slide absolute inset-0 pt-10 flex items-center justify-center transition-all duration-700 ease-in-out opacity-0 z-0 scale-95">
                        <div class="text-center border border-dashed border-gray-700 p-12 rounded-xl bg-gray-900/30">
                            <span class="text-gray-500 text-sm font-mono">[ Placeholder: Upload Inventory.png ]</span>
                        </div>
                    </div>
                </div>

                <!-- Carousel Controls -->
                <div class="absolute bottom-4 left-0 w-full flex justify-center gap-2 z-20">
                    <button onclick="showSlide(0)" class="carousel-indicator w-8 h-1.5 rounded-full bg-teal-500 transition-all"></button>
                    <button onclick="showSlide(1)" class="carousel-indicator w-3 h-1.5 rounded-full bg-gray-600 transition-all"></button>
                    <button onclick="showSlide(2)" class="carousel-indicator w-3 h-1.5 rounded-full bg-gray-600 transition-all"></button>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 5 - FEATURES GRID -->
    <section id="features" class="py-32 relative bg-grid-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal">
            <div class="mb-16">
                <h2 class="text-3xl lg:text-5xl font-black text-white text-gradient">Comprehensive toolset. <br> Zero clutter.</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php 
                $gridFeatures = [
                    ['title' => 'GST Billing', 'desc' => 'Inclusive/Exclusive support.'],
                    ['title' => 'Stock Alerts', 'desc' => 'Never run out of top sellers.'],
                    ['title' => 'Profit Analytics', 'desc' => 'Track margins instantly.'],
                    ['title' => 'Staff Accounts', 'desc' => 'Role-based access control.'],
                    ['title' => 'Customer History', 'desc' => 'Full purchase tracking.'],
                    ['title' => 'Supplier Directory', 'desc' => 'Manage wholesale orders.'],
                    ['title' => 'Excel Exports', 'desc' => 'One-click data download.'],
                    ['title' => 'Cloud Sync', 'desc' => 'Data secured in real-time.']
                ];
                foreach($gridFeatures as $gf): ?>
                <div class="glass-panel p-6 rounded-2xl hover:bg-gray-800/50 hover:border-gray-600 transition-all cursor-default group">
                    <h4 class="text-white font-bold mb-2 group-hover:text-teal-400 transition-colors"><?= $gf['title'] ?></h4>
                    <p class="text-sm text-gray-500"><?= $gf['desc'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- SECTION 6 - BUSINESS OWNER TESTIMONIALS -->
    <section class="py-24 bg-[#050505] border-y border-gray-900 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal">
            <h2 class="text-3xl font-black text-white text-center mb-16 text-gradient">Trusted by real business owners.</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php 
                $testimonials = [
                    ['name' => 'Rajeev Sharma', 'business' => 'Sharma Hardware Store', 'text' => 'Switched from Excel. The multi-store feature alone saved me hours of phone calls every day.'],
                    ['name' => 'Amit Patel', 'business' => 'Patel Paints & Traders', 'text' => 'The credit ledger (Udhaar) tracking is flawless. I have finally recovered money I forgot about.'],
                    ['name' => 'Sunil Kumar', 'business' => 'Kumar Electronics', 'text' => 'Fastest billing software I have used. Staff learned it in 10 minutes without any training.']
                ];
                foreach($testimonials as $t): ?>
                <div class="glass-panel p-8 rounded-3xl border border-gray-800">
                    <div class="flex gap-1 text-teal-500 mb-6">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <p class="text-gray-400 mb-8 italic">"<?= $t['text'] ?>"</p>
                    <div class="flex items-center gap-4 border-t border-gray-800 pt-6">
                        <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center font-bold text-white"><?= substr($t['name'], 0, 1) ?></div>
                        <div>
                            <div class="text-white font-bold text-sm"><?= $t['name'] ?></div>
                            <div class="text-gray-500 text-xs"><?= $t['business'] ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- SECTION 7 - PRICING -->
    <section id="pricing" class="py-32 relative">
        <!-- Background Glow -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-teal-900 rounded-full mix-blend-screen filter blur-[150px] opacity-20 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal relative z-10">
            <div class="text-center mb-20">
                <h2 class="text-4xl lg:text-5xl font-black text-white text-gradient">Simple Pricing</h2>
                <p class="mt-4 text-xl text-gray-400">Everything you need, nothing you don't.</p>
            </div>

            <div class="max-w-lg mx-auto">
                <div class="bg-gradient-to-b from-gray-900 to-black p-[1px] rounded-[2rem]">
                    <div class="bg-black rounded-[2rem] p-10 flex flex-col items-center relative overflow-hidden">
                        <!-- Glow -->
                        <div class="absolute top-0 w-full h-1 bg-gradient-to-r from-transparent via-teal-500 to-transparent"></div>
                        
                        <div class="text-teal-500 font-bold tracking-widest uppercase text-sm mb-4">Unlimited Plan</div>
                        <div class="flex items-end gap-1 mb-8">
                            <span class="text-5xl font-black text-white">Contact Us</span>
                        </div>
                        
                        <ul class="space-y-4 mb-10 w-full text-left">
                            <li class="flex items-center text-gray-300"><svg class="w-5 h-5 text-teal-500 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Unlimited Stores</li>
                            <li class="flex items-center text-gray-300"><svg class="w-5 h-5 text-teal-500 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Unlimited Staff Accounts</li>
                            <li class="flex items-center text-gray-300"><svg class="w-5 h-5 text-teal-500 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> All Premium Modules Included</li>
                            <li class="flex items-center text-gray-300"><svg class="w-5 h-5 text-teal-500 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Free Cloud Backups</li>
                        </ul>
                        
                        <a href="<?= APP_URL ?>/contact" class="w-full py-4 bg-white text-black text-center font-bold rounded-xl hover:bg-gray-200 transition-colors shadow-[0_0_20px_rgba(255,255,255,0.2)]">Book Demo & Pricing</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<?php 
$content = ob_get_clean();
include BASE_PATH . 'app/Views/layout/public.php'; 
?>
