<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <title><?= htmlspecialchars($meta['title'] ?? 'Invento - Retail Management SaaS') ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta['description'] ?? 'Multi-store retail management software for modern businesses.') ?>">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?= htmlspecialchars($meta['url'] ?? APP_URL) ?>">

    <!-- Open Graph (Facebook/LinkedIn) -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($meta['url'] ?? APP_URL) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($meta['title'] ?? 'Invento - Retail Management SaaS') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($meta['description'] ?? 'Multi-store retail management software for modern businesses.') ?>">
    <meta property="og:image" content="<?= APP_URL ?>/assets/og-image.jpg">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($meta['title'] ?? 'Invento - Retail Management SaaS') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($meta['description'] ?? 'Multi-store retail management software for modern businesses.') ?>">
    <meta name="twitter:image" content="<?= APP_URL ?>/assets/twitter-card.jpg">

    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "SoftwareApplication",
      "name": "Invento",
      "operatingSystem": "Web",
      "applicationCategory": "BusinessApplication",
      "offers": {
        "@type": "Offer",
        "price": "999.00",
        "priceCurrency": "INR"
      },
      "description": "Multi-store retail management SaaS for hardware, paint, and electrical stores."
    }
    </script>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f4f6f8',
                            100: '#e2e8f0',
                            500: '#3b82f6', // modern blue
                            600: '#2563eb',
                            900: '#0f172a', // slate dark
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-nav {
            background: rgba(10, 10, 10, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .smooth-shadow {
            box-shadow: 0 4px 40px rgba(0,0,0,0.5);
        }
        /* Custom Scrollbar for dark theme */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #000000;
        }
        ::-webkit-scrollbar-thumb {
            background: #333333;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #0f766e;
        }
    </style>
</head>
<body class="bg-black text-gray-200 font-sans antialiased flex flex-col min-h-screen selection:bg-brand-600 selection:text-white">

    <!-- Premium Dark Navbar -->
    <header class="glass-nav sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center">
                    <a href="<?= APP_URL ?>/" class="flex items-center gap-2 group">
                        <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center shadow-[0_0_15px_rgba(15,118,110,0.5)] transform group-hover:scale-105 transition-transform duration-200">
                            <span class="text-white font-bold text-lg leading-none">I</span>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-white">Invento</span>
                    </a>
                </div>
                
                <nav class="hidden md:flex space-x-8 items-center">
                    <a href="<?= APP_URL ?>/#showcase" class="text-sm font-medium text-gray-400 hover:text-white transition-colors">Showcase</a>
                    <a href="<?= APP_URL ?>/#workflow" class="text-sm font-medium text-gray-400 hover:text-white transition-colors">Workflow</a>
                    <a href="<?= APP_URL ?>/#features" class="text-sm font-medium text-gray-400 hover:text-white transition-colors">Features</a>
                    <a href="<?= APP_URL ?>/#pricing" class="text-sm font-medium text-gray-400 hover:text-white transition-colors">Pricing</a>
                </nav>

                <div class="hidden md:flex items-center space-x-4">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="<?= APP_URL ?>/app" class="bg-brand-600 text-white px-5 py-2.5 rounded-full text-sm font-bold hover:bg-brand-700 transition-all shadow-[0_0_15px_rgba(15,118,110,0.4)]">Go to Dashboard</a>

                    <?php else: ?>
                        <a href="<?= APP_URL ?>/login" class="text-sm font-medium text-gray-400 hover:text-white transition-colors">Login</a>
                        <a href="<?= APP_URL ?>/#pricing" class="bg-white text-black px-5 py-2.5 rounded-full text-sm font-bold hover:bg-gray-200 transition-all shadow-md transform hover:-translate-y-0.5">Start Free Trial</a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="publicMobileBtn" class="text-gray-400 hover:text-white focus:outline-none p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div id="publicMobileOverlay" class="fixed inset-0 bg-black bg-opacity-80 z-40 hidden transition-opacity duration-300 opacity-0 backdrop-blur-sm"></div>
        <div id="publicMobileDrawer" class="fixed inset-y-0 right-0 w-72 bg-[#0a0a0a] border-l border-gray-800 z-50 transform translate-x-full transition-transform duration-300 ease-in-out shadow-2xl flex flex-col h-full overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-800 flex justify-between items-center">
                <span class="font-bold tracking-tight text-white text-lg">Invento</span>
                <button id="closePublicMobileBtn" class="text-gray-500 hover:text-white p-1">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="flex-1 overflow-y-auto px-4 py-6 space-y-2">
                <a href="<?= APP_URL ?>/#showcase" class="block px-4 py-3 rounded-xl text-base font-medium text-gray-400 hover:bg-gray-900 hover:text-white transition-colors">Showcase</a>
                <a href="<?= APP_URL ?>/#workflow" class="block px-4 py-3 rounded-xl text-base font-medium text-gray-400 hover:bg-gray-900 hover:text-white transition-colors">Workflow</a>
                <a href="<?= APP_URL ?>/#features" class="block px-4 py-3 rounded-xl text-base font-medium text-gray-400 hover:bg-gray-900 hover:text-white transition-colors">Features</a>
                <a href="<?= APP_URL ?>/#pricing" class="block px-4 py-3 rounded-xl text-base font-medium text-gray-400 hover:bg-gray-900 hover:text-white transition-colors">Pricing</a>
                
                <div class="pt-6 pb-2 px-4 border-t border-gray-800">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="<?= APP_URL ?>/app" class="block w-full text-center px-4 py-3 rounded-xl text-base font-medium bg-brand-600 text-white hover:bg-brand-500 transition-colors shadow-lg shadow-brand-600/20">Go to Dashboard</a>

                    <?php else: ?>
                        <a href="<?= APP_URL ?>/login" class="block w-full text-center px-4 py-3 rounded-xl text-base font-medium border border-gray-700 text-gray-300 hover:bg-gray-800 mb-3 transition-colors">Login</a>
                        <a href="<?= APP_URL ?>/#pricing" class="block w-full text-center px-4 py-3 rounded-xl text-base font-medium bg-brand-600 text-white hover:bg-brand-500 transition-colors shadow-lg shadow-brand-600/20">Start Free Trial</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const btn = document.getElementById('publicMobileBtn');
                const closeBtn = document.getElementById('closePublicMobileBtn');
                const drawer = document.getElementById('publicMobileDrawer');
                const overlay = document.getElementById('publicMobileOverlay');
                const links = drawer.querySelectorAll('a');

                function openDrawer() {
                    overlay.classList.remove('hidden');
                    setTimeout(() => {
                        overlay.classList.remove('opacity-0');
                        drawer.classList.remove('translate-x-full');
                    }, 10);
                    document.body.style.overflow = 'hidden';
                }

                function closeDrawer() {
                    drawer.classList.add('translate-x-full');
                    overlay.classList.add('opacity-0');
                    setTimeout(() => {
                        overlay.classList.add('hidden');
                    }, 300);
                    document.body.style.overflow = '';
                }

                if(btn) btn.addEventListener('click', openDrawer);
                if(closeBtn) closeBtn.addEventListener('click', closeDrawer);
                if(overlay) overlay.addEventListener('click', closeDrawer);
                links.forEach(link => link.addEventListener('click', closeDrawer));
            });
        </script>
    </header>

    <!-- Semantic Main Content Area -->
    <main class="flex-grow relative z-10">
        <?= $content ?? '' ?>
    </main>

    <!-- Premium SaaS Footer -->
    <footer class="bg-black text-gray-400 py-16 border-t border-gray-900 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div>
                <a href="<?= APP_URL ?>/" class="flex items-center gap-2 mb-6">
                    <div class="w-6 h-6 bg-brand-600 rounded flex items-center justify-center">
                        <span class="text-white font-bold text-xs leading-none">I</span>
                    </div>
                    <span class="text-lg font-bold tracking-tight text-white">Invento</span>
                </a>
                <p class="text-sm text-gray-500 leading-relaxed mb-6">
                    The premium retail operating system for modern hardware and paint businesses. Built for scale.
                </p>
            </div>
            <div>
                <h3 class="text-white font-bold mb-4 tracking-wider text-sm uppercase">Product</h3>
                <ul class="space-y-3 text-sm">
                    <li><a href="<?= APP_URL ?>/#features" class="hover:text-white transition-colors">Features</a></li>
                    <li><a href="<?= APP_URL ?>/#pricing" class="hover:text-white transition-colors">Pricing</a></li>
                    <li><a href="<?= APP_URL ?>/#screenshots" class="hover:text-white transition-colors">Screenshots</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-white font-bold mb-4 tracking-wider text-sm uppercase">Company</h3>
                <ul class="space-y-3 text-sm">
                    <li><a href="<?= APP_URL ?>/about" class="hover:text-white transition-colors">About Us</a></li>
                    <li><a href="<?= APP_URL ?>/contact" class="hover:text-white transition-colors">Contact</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-white font-bold mb-4 tracking-wider text-sm uppercase">Legal</h3>
                <ul class="space-y-3 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
                    <li><a href="<?= APP_URL ?>/login" class="hover:text-white transition-colors">Login</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 pt-8 border-t border-gray-900 text-sm text-center flex flex-col md:flex-row justify-between items-center text-gray-600">
            <p>&copy; <?= date('Y') ?> Invento SaaS. All rights reserved.</p>
            <div class="mt-4 md:mt-0 space-x-4">
                <a href="#" class="hover:text-white">Twitter</a>
                <a href="#" class="hover:text-white">LinkedIn</a>
                <a href="#" class="hover:text-white">GitHub</a>
            </div>
        </div>
    </footer>

</body>
</html>

