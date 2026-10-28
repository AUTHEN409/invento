<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Invento SaaS</title>
    
    <!-- PWA Manifest & App Icons -->
    <link rel="manifest" href="<?= APP_URL ?>/manifest.json">
    <meta name="theme-color" content="#0d9488">
    <link rel="apple-touch-icon" href="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iIzBkOTQ4OCI+PHBhdGggZD0iTTEyIDJMMSAyMWgyMmwtMTEtMTl6Ii8+PC9zdmc+">
    
    <!-- Tailwind CSS (CDN for simple deployment) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#0d9488',
                            900: '#134e4a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">
    
    <!-- Guest Content wrapper -->
    <main class="min-h-screen">
        <?= $content ?? '' ?>
    </main>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= APP_URL ?>/sw.js')
                    .catch(err => console.error('Service Worker registration failed:', err));
            });
        }
    </script>
</body>
</html>

