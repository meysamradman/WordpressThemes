<?php defined('ABSPATH')||exit;
$wide=get_theme_file_uri('images/pages/home-social.webp');
$medium=get_theme_file_uri('images/pages/home-social-768.webp');
?>
<figure class="rdc-home-promo">
 <img src="<?php echo esc_url($wide); ?>"
  srcset="<?php echo esc_url($medium); ?> 768w, <?php echo esc_url($wide); ?> 1600w"
  sizes="(max-width: 1348px) 95vw, 1300px"
  width="1600" height="721" alt="علی اسدی، موسیقی متعهد به حقیقت" loading="lazy" decoding="async">
</figure>
