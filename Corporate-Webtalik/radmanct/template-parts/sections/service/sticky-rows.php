<?php
defined('ABSPATH')||exit;
$process=apply_filters('radmanct/service/sticky-rows',array());
if(empty($process['title'])||empty($process['items']))return;
$title_id=wp_unique_id('rdc-stk-rows-ttl-');
?>
<section class="rdc-stk-rows rdc-ctr rdc-ctr--full" data-rdc-stk-rows aria-labelledby="<?php echo esc_attr($title_id); ?>">
    <header class="rdc-heading">
        <div class="rdc-heading__inr">
            <?php if(!empty($process['label'])): ?><p class="rdc-heading__lbl"><?php echo esc_html($process['label']); ?></p><?php endif; ?>
            <h2 class="rdc-heading__ttl" id="<?php echo esc_attr($title_id); ?>"><?php echo esc_html($process['title']); ?></h2>
        </div>
        <?php if(!empty($process['desc'])): ?><p class="rdc-heading__desc"><?php echo esc_html($process['desc']); ?></p><?php endif; ?>
    </header>
    <div class="rdc-stk-rows__list">
        <?php foreach($process['items'] as $item): ?>
        <article class="rdc-stk-rows__itm">
            <div class="rdc-stk-rows__cnt">
                <h3><?php echo esc_html($item['title']); ?></h3>
                <?php if(!empty($item['subtitle'])): ?><p class="rdc-stk-rows__sub"><?php echo esc_html($item['subtitle']); ?></p><?php endif; ?>
                <p><?php echo esc_html($item['desc']); ?></p>
            </div>
            <div class="rdc-stk-rows__im" aria-hidden="true"></div>
        </article>
        <?php endforeach; ?>
    </div>
</section>
