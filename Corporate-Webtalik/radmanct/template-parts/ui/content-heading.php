<?php
defined('ABSPATH')||exit;
if(\RadmanCT\CompactTitle\is_used())return;
$title=\RadmanCT\page_heading();
if(is_singular(['post','project']))\RadmanCT\Breadcrumb\render(\RadmanCT\Breadcrumb\data($title));
?>
<h1 class="rdc-cnt-ttl"><?php echo esc_html($title); ?></h1>
