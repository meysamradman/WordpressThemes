<?php defined('ABSPATH')||exit;$logo=\RadmanCT\media_id('logo'); ?>
<header id="rdc-page-st" class="rdc-hdr rdc-inv"><div class="rdc-hdr__inr">
 <div class="rdc-hdr__brd"><a class="rdc-brd" href="<?php echo esc_url(home_url('/')); ?>"><?php if($logo)echo \RadmanCT\brand_logo($logo);else echo esc_html(get_bloginfo('name')); ?></a></div>
 <nav class="rdc-nav-desk" data-rdc-menu data-menu-hover="roll" aria-label="فهرست اصلی"><?php wp_nav_menu(array('theme_location'=>'primary','container'=>false,'fallback_cb'=>false,'menu_class'=>'rdc-menu','depth'=>2)); ?></nav>
 <form class="rdc-hdr__search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"><label class="screen-reader-text" for="rdc-header-search">جستجوی آهنگ</label><input id="rdc-header-search" type="search" name="s" placeholder="جستجو…"><input type="hidden" name="post_type" value="track"><button type="submit" aria-label="جستجوی آهنگ"><?php echo \RadmanCT\icon('search'); ?></button></form>
 <button type="button" class="rdc-menu-tgl" data-rdc-menu-open aria-haspopup="dialog" aria-controls="rdc-menu-dlg" aria-expanded="false" aria-label="بازکردن فهرست"><?php echo \RadmanCT\icon('menu'); ?></button>
</div></header>
