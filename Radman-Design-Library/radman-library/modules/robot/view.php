<?php defined('ABSPATH')||exit;require_once __DIR__.'/data.php';$data=\RadmanLibrary\Designs\Robot\data(); ?>
<section class="rdl-bot" data-rdl-bot data-rdl-bot-model="<?php echo esc_url($data['model']); ?>" data-rdl-bot-cfg="<?php echo esc_attr(wp_json_encode($data['config'])); ?>">
 <div class="rdl-bot__cnt"><p class="rdl-bot__eb">Radman / Interactive</p><h1 class="rdl-bot__ttl"><?php echo esc_html($data['title']); ?></h1><p class="rdl-bot__desc"><?php echo esc_html($data['description']); ?></p><span class="rdl-bot__lbl">ربات سه‌بعدی تعاملی</span></div>
 <div class="rdl-bot__md" role="img" aria-label="ربات سبز سه‌بعدی با چشم‌های روشن؛ نگاه آن نشانگر را دنبال می‌کند."><img class="rdl-bot__pst" src="<?php echo esc_url($data['poster']); ?>" width="640" height="640" alt="" decoding="async"><canvas class="rdl-bot__cvs" aria-hidden="true"></canvas><span class="rdl-bot__halo" aria-hidden="true"></span></div>
</section>
