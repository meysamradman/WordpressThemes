<?php defined('ABSPATH')||exit;$module=\RadmanLibrary\Samples\selected();$framed=\RadmanLibrary\Preview\framed();$canvas=\RadmanLibrary\Preview\canvas(); ?>
<!doctype html><html lang="fa-IR" dir="rtl"><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class('rdc-site'.((($module['surface']??'')==='dark'||str_starts_with($module['slug'],'services-'))?' rdc-inv':'').($framed?' rdl-device-view':'')); ?>><?php wp_body_open(); ?>
<?php if($framed): ?>
<div class="rdl-device-shell"><?php get_template_part('template-parts/library-bar',null,['preview'=>true,'title'=>$module['title']]);get_template_part('template-parts/device-preview',null,['title'=>$module['title']]); ?></div>
<?php else: ?>
<div id="rdc-scr-wrp"><div id="rdc-scr-cnt"><?php if(!$canvas)get_template_part('template-parts/library-bar',null,['preview'=>true,'title'=>$module['title']]); ?><main id="main-content" tabindex="-1" class="rdl-demo rdl-demo--<?php echo esc_attr($module['slug']); ?>">
<?php if(\RadmanLibrary\Portfolio\native()||\RadmanLibrary\Blog\native()||\RadmanLibrary\Music\native()||\RadmanLibrary\Catalog\native()){require get_template_directory().'/modules/'.$module['slug'].'/view.php';}else{while(have_posts()):the_post();$args=['heading_tag'=>'h1'];require get_template_directory().'/modules/'.$module['slug'].'/view.php';endwhile;} ?>
<?php if($module['scroll_gap']??($module['slug']!=='services-faq')): ?><div class="rdl-scr-gap" aria-hidden="true"></div><?php endif; ?>
</main></div></div>
<?php endif; ?>
<?php wp_footer(); ?></body></html>
