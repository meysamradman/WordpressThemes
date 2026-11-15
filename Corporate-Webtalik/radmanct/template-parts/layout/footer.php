<?php defined('ABSPATH')||exit;$footer=\RadmanCT\Footer\data();$privacy=get_privacy_policy_url(); ?>
<footer class="rdc-ftr rdc-inv">
 <?php $background=\RadmanCT\media_id('footer_background');if($background)echo wp_get_attachment_image($background,'full',false,array('class'=>'rdc-ftr__bg','alt'=>'','aria-hidden'=>'true','loading'=>'lazy','fetchpriority'=>'low','decoding'=>'async','sizes'=>'100vw')); ?>
 <div class="rdc-ctr">
  <nav class="rdc-ftr__nav" aria-label="<?php esc_attr_e('Footer navigation','radmanct'); ?>">
   <?php if(has_nav_menu('footer'))wp_nav_menu(array('theme_location'=>'footer','container'=>false,'fallback_cb'=>false,'menu_class'=>'rdc-ftr__menu','depth'=>1));else{ ?>
   <ul class="rdc-ftr__menu"><?php foreach($footer['links'] as $link): ?><li><a href="<?php echo esc_url($link['url']); ?>"><?php echo esc_html($link['title']); ?></a></li><?php endforeach; ?></ul>
   <?php } ?>
  </nav>
  <div class="rdc-ftr__cols">
   <div class="rdc-ftr__ctc">
    <a class="rdc-ftr__brd" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>" href="<?php echo esc_url(home_url('/')); ?>"><?php $logo=\RadmanCT\media_id('logo_footer')?:\RadmanCT\media_id('logo');if($logo)echo \RadmanCT\brand_logo($logo);else echo esc_html(get_bloginfo('name')); ?></a>
    <p class="rdc-ftr__brd-copy"><?php esc_html_e('Thoughtful design, reliable development, and room for your business to grow.','radmanct'); ?></p>
    <p class="rdc-ftr__lbl"><?php esc_html_e('Let’s stay in touch','radmanct'); ?></p>
    <address>
     <?php if($footer['phone']): ?><a dir="ltr" href="<?php echo esc_url('tel:'.preg_replace('/[^+0-9]/','',$footer['phone'])); ?>"><?php echo esc_html($footer['phone']); ?></a><?php endif; ?>
     <?php if(is_email($footer['email'])): ?><a dir="ltr" href="<?php echo esc_url('mailto:'.$footer['email']); ?>"><?php echo esc_html($footer['email']); ?></a><?php else: ?><span dir="ltr">hello@example.com</span><?php endif; ?>
     <p><?php echo esc_html($footer['address']); ?></p>
    </address>
    <p class="rdc-ftr__hrs"><?php echo esc_html($footer['hours']); ?></p>
   </div>
   <nav class="rdc-ftr__svcs" aria-label="<?php esc_attr_e('Our services','radmanct'); ?>">
    <p class="rdc-ftr__lbl"><?php esc_html_e('Our services','radmanct'); ?></p>
    <ul><?php foreach($footer['services'] as $service):if(!$service['url'])continue; ?><li><a href="<?php echo esc_url($service['url']); ?>"><?php echo esc_html($service['title']); ?></a></li><?php endforeach; ?></ul>
   </nav>
   <div class="rdc-ftr__socs">
    <p class="rdc-ftr__lbl"><?php esc_html_e('Follow our work','radmanct'); ?></p>
    <ul><?php foreach($footer['socials'] as $social):$url=esc_url($social['url'],array('https','http')); ?>
     <li><?php if($url): ?><a class="rdc-ftr__soc" href="<?php echo $url; ?>" rel="me noopener noreferrer"><?php else: ?><span class="rdc-ftr__soc"><?php endif; ?>
      <?php echo \RadmanCT\icon($social['icon']); ?><span><?php echo esc_html($social['title']); ?></span><?php echo \RadmanCT\icon('arrow-up-right'); ?>
     <?php echo $url?'</a>':'</span>'; ?></li>
    <?php endforeach; ?></ul>
   </div>
   <section class="rdc-ftr__prj" aria-labelledby="footer-project-title">
    <p class="rdc-ftr__lbl"><?php esc_html_e('A new collaboration','radmanct'); ?></p>
    <p class="rdc-ftr__ttl" id="footer-project-title"><?php esc_html_e('Let’s talk about your next project.','radmanct'); ?></p>
    <p class="rdc-ftr__intr"><?php esc_html_e('From the first idea to a website built for your business, we are here to help.','radmanct'); ?></p>
    <?php if($footer['contact']): ?><?php echo \RadmanCT\button(__('Start a conversation','radmanct'),$footer['contact']); ?><?php endif; ?>
   </section>
  </div>
  <div class="rdc-ftr__btm">
   <p><?php echo esc_html(get_bloginfo('name')); ?> <span dir="ltr">© <?php echo esc_html(wp_date('Y')); ?></span></p>
   <div class="rdc-ftr__btm-lnks"><?php if($privacy): ?><a href="<?php echo esc_url($privacy); ?>"><?php esc_html_e('Privacy policy','radmanct'); ?></a><?php endif; ?>
    <a class="rdc-ftr__top" href="#rdc-page-st" data-rdc-scr-top aria-label="<?php esc_attr_e('Back to top','radmanct'); ?>"><?php echo \RadmanCT\icon('chevron-down'); ?></a>
   </div>
  </div>
 </div>
</footer>
