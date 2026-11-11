<?php defined('ABSPATH')||exit;require_once __DIR__.'/data.php';require_once get_template_directory().'/inc/ui/text-marquee.php'; ?>
<section class="rdl-mtx-demo"><h1 class="screen-reader-text">نوار متن پیوسته با تغییر جهت اسکرول</h1><?php \RadmanLibrary\UI\text_marquee(\RadmanLibrary\Designs\TextLoop\data()); ?></section>
