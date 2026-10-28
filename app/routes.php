<?php

/** @var \core\Router $router */

// Public SEO & Marketing Routes
$router->get('/', 'PublicController@index');
$router->get('/features', 'PublicController@features');
$router->get('/pricing', 'PublicController@pricing');
$router->get('/about', 'PublicController@about');
$router->get('/contact', 'PublicController@contact');

// Dynamic SEO Files
$router->get('/robots.txt', 'PublicController@robots');
$router->get('/sitemap.xml', 'PublicController@sitemap');
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// Super Admin Auth routes
$router->get('/admin/login', 'AdminAuthController@loginForm');
$router->post('/admin/login', 'AdminAuthController@login');
$router->get('/admin/logout', 'AdminAuthController@logout');

// Super Admin Protected routes (Middleware: AdminAuth)
$router->get('/admin', 'AdminController@dashboard', ['AdminAuth']);
$router->get('/admin/businesses', 'AdminController@businesses', ['AdminAuth']);
$router->post('/admin/businesses', 'AdminController@storeBusiness', ['AdminAuth']);
$router->post('/admin/businesses/bulk', 'AdminController@bulkAction', ['AdminAuth']);
$router->post('/admin/businesses/delete/{id}', 'AdminController@deleteBusiness', ['AdminAuth']);
$router->get('/admin/subscriptions', 'AdminController@subscriptions', ['AdminAuth']);
$router->get('/admin/users', 'AdminController@users', ['AdminAuth']);
$router->get('/admin/stores', 'AdminController@stores', ['AdminAuth']);
$router->post('/admin/stores/action', 'AdminController@storeAction', ['AdminAuth']);
$router->get('/admin/reports', 'AdminController@reports', ['AdminAuth']);
$router->get('/admin/audit-logs', 'AdminController@auditLogs', ['AdminAuth']);
$router->get('/admin/support', 'AdminController@support', ['AdminAuth']);
$router->get('/admin/cms', 'AdminController@cms', ['AdminAuth']);
$router->post('/admin/cms/update', 'AdminController@updateCms', ['AdminAuth']);
$router->get('/admin/impersonate/{id}', 'AdminController@impersonateUser', ['AdminAuth']);
$router->get('/admin/impersonate-stop', 'AdminController@stopImpersonation', []);
$router->get('/admin/settings', 'AdminController@settings', ['AdminAuth']);
$router->post('/admin/settings/save', 'AdminController@saveSettings', ['AdminAuth']);
$router->get('/admin/profile', 'AdminController@profile', ['AdminAuth']);

// Business Protected routes (Middleware: BusinessAuth)
$router->get('/dashboard', 'DashboardController@index', ['BusinessAuth']);
$router->get('/app', 'DashboardController@index', ['BusinessAuth']);
$router->get('/switch-store/{id}', 'DashboardController@switchStore', ['BusinessAuth']);

// Stores
$router->get('/stores', 'StoreController@index', ['BusinessAuth', 'OwnerAuth']);
$router->post('/stores', 'StoreController@store', ['BusinessAuth', 'OwnerAuth']);
$router->post('/stores/update/{id}', 'StoreController@update', ['BusinessAuth', 'OwnerAuth']);
$router->post('/stores/delete/{id}', 'StoreController@destroy', ['BusinessAuth', 'OwnerAuth']);

// Categories
$router->get('/categories', 'CategoryController@index', ['BusinessAuth']);
$router->post('/categories', 'CategoryController@store', ['BusinessAuth']);
$router->post('/categories/delete/{id}', 'CategoryController@destroy', ['BusinessAuth']);

// Products
$router->get('/products', 'ProductController@index', ['BusinessAuth']);
$router->get('/products/create', 'ProductController@create', ['BusinessAuth']);
$router->post('/products/store', 'ProductController@store', ['BusinessAuth']);
$router->get('/products/edit/{id}', 'ProductController@edit', ['BusinessAuth']);
$router->post('/products/update/{id}', 'ProductController@update', ['BusinessAuth']);
$router->post('/products/delete/{id}', 'ProductController@destroy', ['BusinessAuth']);
$router->get('/products/import', 'ProductController@importForm', ['BusinessAuth']);
$router->post('/products/import', 'ProductController@importData', ['BusinessAuth']);

// Inventory / Stock In
$router->get('/inventory', 'InventoryController@index', ['BusinessAuth']);
$router->get('/inventory/stock-in', 'InventoryController@stockInForm', ['BusinessAuth']);
$router->post('/inventory/stock-in', 'InventoryController@stockIn', ['BusinessAuth']);
$router->get('/inventory/dead-stock', 'InventoryController@deadStock', ['BusinessAuth']);

// Suppliers
$router->get('/suppliers', 'SupplierController@index', ['BusinessAuth']);
$router->post('/suppliers', 'SupplierController@store', ['BusinessAuth']);
$router->post('/suppliers/delete/{id}', 'SupplierController@destroy', ['BusinessAuth']);

// Customers
$router->get('/customers', 'CustomerController@index', ['BusinessAuth']);
$router->post('/customers', 'CustomerController@store', ['BusinessAuth']);
$router->get('/customers/{id}', 'CustomerController@show', ['BusinessAuth']);
$router->post('/customers/delete/{id}', 'CustomerController@destroy', ['BusinessAuth']);

// Billing
$router->get('/billing', 'BillingController@index', ['BusinessAuth']);
$router->get('/billing/search', 'BillingController@searchProduct', ['BusinessAuth']);
$router->post('/billing/process', 'BillingController@process', ['BusinessAuth']);

// Invoices (History)
$router->get('/invoices', 'InvoiceController@index', ['BusinessAuth']);
$router->get('/invoices/{id}', 'InvoiceController@show', ['BusinessAuth']);

// Credit Management
$router->get('/credit', 'CreditController@index', ['BusinessAuth']);
$router->get('/credit/{customer_id}', 'CreditController@show', ['BusinessAuth']);
$router->post('/credit/pay', 'CreditController@pay', ['BusinessAuth']);
$router->post('/credit/remind', 'CreditController@logReminder', ['BusinessAuth']);

// Staff Management
$router->get('/staff', 'StaffController@index', ['BusinessAuth', 'OwnerAuth']);
$router->post('/staff', 'StaffController@store', ['BusinessAuth', 'OwnerAuth']);
$router->post('/staff/delete/{id}', 'StaffController@destroy', ['BusinessAuth', 'OwnerAuth']);

// Settings
$router->get('/settings', 'SettingsController@index', ['BusinessAuth', 'OwnerAuth']);
$router->post('/settings', 'SettingsController@update', ['BusinessAuth', 'OwnerAuth']);

// Exports
$router->get('/export/products', 'ExportController@products', ['BusinessAuth']);
$router->get('/export/inventory', 'ExportController@inventory', ['BusinessAuth']);
$router->get('/export/customers', 'ExportController@customers', ['BusinessAuth']);
$router->get('/export/billing', 'ExportController@billing', ['BusinessAuth']);
$router->get('/export/billing-detailed', 'ExportController@billingDetailed', ['BusinessAuth']);
$router->get('/export/sales-report', 'ExportController@salesReport', ['BusinessAuth']);
$router->get('/export/credit', 'ExportController@credit', ['BusinessAuth']);
$router->get('/export/dead-stock', 'ExportController@deadStock', ['BusinessAuth']);
$router->get('/export/staff', 'ExportController@staff', ['BusinessAuth']);
$router->get('/export/stores', 'ExportController@stores', ['BusinessAuth']);

// Super Admin Management Actions
$router->get('/admin/business/{id}', 'AdminController@showBusiness', ['AdminAuth']);
$router->post('/admin/business/{id}/update', 'AdminController@updateBusiness', ['AdminAuth']);
$router->post('/admin/business/{id}/status', 'AdminController@updateStatus', ['AdminAuth']);
$router->post('/admin/business/{id}/subscription', 'AdminController@updateSubscription', ['AdminAuth']);
$router->post('/admin/business/{id}/permissions', 'AdminController@updatePermissions', ['AdminAuth']);
