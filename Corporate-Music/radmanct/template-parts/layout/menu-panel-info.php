<?php defined('ABSPATH')||exit;$panel=$args['panel']??\RadmanCT\Footer\data();$compact=!empty($args['compact']); ?>
  <aside class="rdc-pnl__info" aria-label="<?php esc_attr_e('Contact and links','radmanct'); ?>">
   <div class="rdc-pnl__grp" data-rdc-pnl-rvl>
    <p class="rdc-pnl__lbl"><?php esc_html_e('Say hello','radmanct'); ?></p>
    <?php if(is_email($panel['email'])): ?><a class="rdc-pnl__mail" dir="ltr" href="<?php echo esc_url('mailto:'.$panel['email']); ?>"><?php echo esc_html($panel['email']); ?></a><?php else: ?><span class="rdc-pnl__mail" dir="ltr">hello@example.com</span><?php endif; ?>
   </div>
   <div class="rdc-pnl__grp" data-rdc-pnl-rvl>
    <p class="rdc-pnl__lbl"><?php esc_html_e('Elsewhere','radmanct'); ?></p>
    <ul class="rdc-pnl__socs"><?php foreach($panel['socials'] as $social):$url=esc_url($social['url'],array('http','https')); ?><li><?php if($url): ?><a href="<?php echo $url; ?>" rel="me noopener noreferrer"><?php echo esc_html($social['title']); ?></a><?php else: ?><span><?php echo esc_html($social['title']); ?></span><?php endif; ?></li><?php endforeach; ?></ul>
   </div>
   <?php if(!$compact): ?><div class="rdc-pnl__grp" data-rdc-pnl-rvl>
    <p class="rdc-pnl__lbl"><?php esc_html_e('A new collaboration','radmanct'); ?></p>
    <p class="rdc-pnl__sts"><?php if($panel['contact']): ?><a href="<?php echo esc_url($panel['contact']); ?>"><?php esc_html_e('Let’s hear your next idea.','radmanct'); ?></a><?php else: ?><span><?php esc_html_e('Let’s hear your next idea.','radmanct'); ?></span><?php endif; ?></p>
   </div>
   <?php endif; ?>
  </aside>