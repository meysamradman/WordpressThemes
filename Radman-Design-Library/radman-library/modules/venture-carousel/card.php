<?php
defined('ABSPATH')||exit;
$marks=[
 'ribbon'=>'<path d="M9 25 30 5v20L51 5v20L72 5v50L51 75V55L30 75V55L9 75Z"/>',
 'star'=>'<path d="M35 3h10c0 24 8 32 32 32v10c-24 0-32 8-32 32H35c0-24-8-32-32-32V35c24 0 32-8 32-32Z"/>',
 'steps'=>'<path d="M25 4h30v15h15v15H40v15h30v15H55v12H25V61H10V46h30V31H10V16h15Z"/>',
 'orbit'=>'<path fill-rule="evenodd" d="M40 6a34 34 0 1 0 0 68 34 34 0 0 0 0-68Zm0 13a21 21 0 1 1 0 42 21 21 0 0 1 0-42Z"/><path d="m14 55 42-42 10 10-42 42Z"/>',
 'grid'=>'<path d="M6 6h28v28H6Zm40 0h28v28H46ZM6 46h28v28H6Zm40 0h28v28H46Z"/>',
];
?>
<article class="rdl-venture__card" data-rdl-venture-card="<?php echo (int)$index; ?>" style="--rdl-venture-accent:<?php echo esc_attr(sanitize_hex_color($card['color'])?:'#7863ef'); ?>;--rdl-venture-ink:<?php echo esc_attr(sanitize_hex_color($card['ink']??'')?:'#191919'); ?>">
 <h3 class="rdl-venture__ttl"><button class="rdl-venture__pick" type="button" data-rdl-venture-pick="<?php echo (int)$index; ?>" aria-expanded="true" aria-controls="<?php echo esc_attr($detail_id); ?>"><span class="screen-reader-text">نمایش </span><?php echo esc_html($card['name']); ?></button></h3>
 <svg class="rdl-venture__mark" viewBox="0 0 80 80" aria-hidden="true" focusable="false"><?php echo $marks[$card['mark']]??$marks['grid']; ?></svg>
 <div class="rdl-venture__detail" id="<?php echo esc_attr($detail_id); ?>" data-rdl-venture-detail>
  <p><?php echo esc_html($card['desc']); ?></p>
  <?php if(!empty($card['link'])): ?><a href="<?php echo esc_url($card['link']); ?>">مشاهدهٔ وب‌سایت <span aria-hidden="true">↗</span></a><?php else: ?><span class="rdl-venture__soon">پروژهٔ نمایشی</span><?php endif; ?>
 </div>
 <span class="rdl-venture__corner rdl-venture__corner--top" aria-hidden="true"></span><span class="rdl-venture__corner rdl-venture__corner--bottom" aria-hidden="true"></span>
</article>
