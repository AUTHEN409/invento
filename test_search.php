<?php
define('BASE_PATH', __DIR__ . '/');
require 'config/config.php';
require 'core/Database.php';

try {
    $db = \core\Database::getInstance();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT p.id, p.name, p.sku, p.selling_price, p.gst_percent, p.tax_mode, 
                   c.name as category_name, SUM(b.remaining_quantity) as available_qty
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN inventory_batches b ON p.id = b.product_id AND b.store_id = :sid
            WHERE p.business_id = :bid AND p.status = 'active'
            AND (p.name LIKE :q OR p.sku LIKE :q)
            GROUP BY p.id, p.name, p.sku, p.selling_price, p.gst_percent, p.tax_mode, c.name
            LIMIT 20";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':sid' => 1,
        ':bid' => 1,
        ':q' => "%test%"
    ]);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Query OK. Found " . count($results) . " items.";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
