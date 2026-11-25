<?php defined('ABSPATH')||exit;
$wide_path='images/pages/home-social.webp';
$medium_path='images/pages/home-social-768.webp';
$wide=add_query_arg('ver',(string)filemtime(get_theme_file_path($wide_path)),get_theme_file_uri($wide_path));
$medium=add_query_arg('ver',(string)filemtime(get_theme_file_path($medium_path)),get_theme_file_uri($medium_path));
?>
<figure class="rdc-home-promo">
 <img src="<?php echo esc_url($wide); ?>"
  srcset="<?php echo esc_url($medium); ?> 768w, <?php echo esc_url($wide); ?> 1600w"
  sizes="<?php echo esc_attr(\RadmanCT\MusicHome\ART_SIZES); ?>"
  width="1600" height="721" alt="علی اسدی، موسیقی متعهد به حقیقت" loading="lazy" decoding="async">
</figure>
