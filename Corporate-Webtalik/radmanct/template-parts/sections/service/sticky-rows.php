<?php
defined('ABSPATH')||exit;
$process=apply_filters('radmanct/service/sticky-rows',array());
if(empty($process['title'])||empty($process['items']))return;
$title_id=wp_unique_id('rdc-stk-rows-ttl-');
?>
<section class="rdc-stk-rows rdc-ctr rdc-ctr--full" data-rdc-stk-rows aria-labelledby="<?php echo esc_attr($title_id); ?>">
    <h2 class="rdc-stk-rows__ttl" id="<?php echo esc_attr($title_id); ?>"><?php echo esc_html($process['title']); ?></h2>
    <div class="rdc-stk-rows__list">
        <?php foreach($process['items'] as $item): ?>
        <article class="rdc-stk-rows__itm">
            <div class="rdc-stk-rows__cnt">
                <h3><?php echo esc_html($item['title']); ?></h3>
                <p><?php echo esc_html($item['desc']); ?></p>
            </div>
            <div class="rdc-stk-rows__im" aria-hidden="true"></div>
        </article>
        <?php endforeach; ?>
    </div>
</section>
