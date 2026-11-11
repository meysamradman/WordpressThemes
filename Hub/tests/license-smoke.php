<?php
// Bounded, offline contract checks for the license compatibility class.
define('ABSPATH', __DIR__);
function esc_html__($text, $domain) { return htmlspecialchars($text); }
require dirname(__DIR__) . '/hub/liquid/admin/updater/liquid-register-admin.php';
$register = new Liquid_Register;
ob_start();
$register->messages();
$register->form();
$html = ob_get_clean();
if (strpos($html, 'Hub Local') === false || strpos($html, '<form') !== false) {
    throw new Exception('Dashboard must show local status without an activation form');
}
foreach (array('template_redirect', 'check_domain', 'check_revoke', 'check_license', 'updater') as $method) {
    if (method_exists($register, $method)) {
        throw new Exception('Removed license path still exists: ' . $method);
    }
}
echo "PASS: license view and removed domain/revoke/visitor/updater paths\n";
