<?php defined('ABSPATH')||exit;$logo=\RadmanCT\media_id('logo');$contact=\RadmanCT\Footer\data()['contact']; ?>
<header id="rdc-page-st" class="rdc-hdr rdc-inv"><div class="rdc-hdr__inr">
<div class="rdc-hdr__brd"><button type="button" class="rdc-menu-tgl" data-rdc-menu-open aria-haspopup="dialog" aria-controls="rdc-menu-dlg" aria-expanded="false" aria-label="<?php esc_attr_e('Open menu','radmanct'); ?>"><?php echo \RadmanCT\icon('menu'); ?></button><a class="rdc-brd" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>"><?php if($logo)echo \RadmanCT\brand_logo($logo);else bloginfo('name'); ?></a></div>
<nav data-rdc-cur-tip="off" class="rdc-nav-desk rdc-menu--indicator-dot" data-rdc-menu data-menu-hover="roll" aria-label="<?php esc_attr_e('Primary menu','radmanct'); ?>"><?php wp_nav_menu(array('theme_location'=>'primary','container'=>false,'fallback_cb'=>false,'menu_class'=>'rdc-menu','depth'=>3)); ?></nav>
<?php if($contact)echo \RadmanCT\button(__('Contact us','radmanct'),$contact,array('class'=>'rdc-hdr__ctc','variant'=>'outline','icon'=>'headset','icon_position'=>'start')); ?>
</div></header>
