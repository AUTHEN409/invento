<?php

namespace app\Controllers;

use core\Controller;

class PublicController extends Controller {

    public function __construct() {
        // The root domain must ALWAYS show the public landing page.
    }

    public function index() {
        $meta = [
            'title' => 'Invento - Retail Management SaaS for Hardware, Paint, and Electrical Stores',
            'description' => 'The simplest, fastest, and most secure billing and inventory management software designed explicitly for multi-store retail businesses.',
            'url' => APP_URL . '/'
        ];
        $this->view('public/index', ['meta' => $meta]);
    }

    public function features() {
        $meta = [
            'title' => 'Features - Invento Retail Management',
            'description' => 'Explore Invento\'s powerful features: multi-store inventory, real-time GST billing, automated credit ledgers, and dead stock reporting.',
            'url' => APP_URL . '/features'
        ];
        $this->view('public/features', ['meta' => $meta]);
    }

    public function pricing() {
        // We can fetch plans from the DB for dynamic pricing
        $db = \core\Database::getInstance();
        $stmt = $db->query("SELECT * FROM plans WHERE status = 'active' ORDER BY price ASC");
        $plans = $stmt->fetchAll();

        $meta = [
            'title' => 'Pricing - Invento Retail Management',
            'description' => 'Affordable, transparent pricing for retail businesses of all sizes. Start managing your inventory, billing, and staff today.',
            'url' => APP_URL . '/pricing'
        ];
        $this->view('public/pricing', ['plans' => $plans, 'meta' => $meta]);
    }

    public function about() {
        $meta = [
            'title' => 'About Us - Invento',
            'description' => 'Learn about the team behind Invento. We are dedicated to bringing enterprise-grade technology to everyday retail business owners.',
            'url' => APP_URL . '/about'
        ];
        $this->view('public/about', ['meta' => $meta]);
    }

    public function contact() {
        $meta = [
            'title' => 'Contact Us - Invento',
            'description' => 'Get in touch with the Invento team for sales inquiries, support, or partnership opportunities.',
            'url' => APP_URL . '/contact'
        ];
        $this->view('public/contact', ['meta' => $meta]);
    }

    public function robots() {
        header('Content-Type: text/plain');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Allow: /features\n";
        echo "Allow: /pricing\n";
        echo "Allow: /about\n";
        echo "Allow: /contact\n\n";
        echo "Disallow: /admin/\n";
        echo "Disallow: /dashboard/\n";
        echo "Disallow: /billing/\n";
        echo "Disallow: /products/\n";
        echo "Disallow: /inventory/\n";
        echo "Disallow: /invoices/\n";
        echo "Disallow: /credit/\n";
        echo "Disallow: /customers/\n";
        echo "Disallow: /suppliers/\n";
        echo "Disallow: /settings/\n";
        echo "Disallow: /staff/\n";
        echo "Disallow: /export/\n";
        echo "Disallow: /login\n";
        echo "Disallow: /switch-store/\n\n";
        echo "Sitemap: " . APP_URL . "/sitemap.xml\n";
        exit;
    }

    public function sitemap() {
        header('Content-Type: application/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        $urls = [
            ['loc' => '/', 'freq' => 'weekly', 'pri' => '1.0'],
            ['loc' => '/features', 'freq' => 'monthly', 'pri' => '0.8'],
            ['loc' => '/pricing', 'freq' => 'monthly', 'pri' => '0.8'],
            ['loc' => '/about', 'freq' => 'monthly', 'pri' => '0.6'],
            ['loc' => '/contact', 'freq' => 'monthly', 'pri' => '0.6']
        ];
        
        foreach($urls as $u) {
            echo "    <url>\n";
            echo "        <loc>" . APP_URL . $u['loc'] . "</loc>\n";
            echo "        <changefreq>" . $u['freq'] . "</changefreq>\n";
            echo "        <priority>" . $u['pri'] . "</priority>\n";
            echo "    </url>\n";
        }
        
        echo '</urlset>';
        exit;
    }
}
