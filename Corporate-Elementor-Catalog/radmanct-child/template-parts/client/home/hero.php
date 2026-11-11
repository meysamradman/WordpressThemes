<?php
defined( 'ABSPATH' ) || exit;
$rdct_brand = \RadmanCT\Settings\values()['brand_color'];
$rdct_hero = \RadmanCTChild\Home\data( get_the_ID() );
?>
<section class="rdct-clt-hero" data-rdct-header-tone="dark" aria-label="<?php echo esc_attr( $rdct_hero['heading'] ); ?>">
	<?php if ( \RadmanCTChild\Home\image_id( get_the_ID() ) ) : ?><div class="rdct-clt-hero__media" aria-hidden="true"><?php get_template_part( 'template-parts/client/home/hero-art' ); ?></div><?php endif; ?>
	<div class="rdct-clt-hero__inner">
		<div class="rdct-clt-hero__copy" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
			<div class="rdct-clt-hero__title"><?php \RadmanCTChild\Home\heading( $rdct_hero['heading'], 'h1', '#e9e6dc' ); ?></div>
			<div class="rdct-clt-hero__subtitle"><?php \RadmanCTChild\Home\heading( $rdct_hero['subheading'], 'h2', '#e9e6dc' ); ?></div>
			<div class="rdct-clt-hero__accent"><?php \RadmanCTChild\Home\heading( $rdct_hero['accent'], 'h2', $rdct_brand, true ); ?></div>
			<?php if ( $rdct_hero['description'] ) : ?><div class="rdct-clt-hero__description"><?php \RadmanCTChild\Home\heading( $rdct_hero['description'], 'p', '#99978f' ); ?></div><?php endif; ?>
			<?php
			if ( $rdct_hero['button_url'] && $rdct_hero['button_label'] && function_exists( 'rdct_button' ) ) {
				rdct_button( array(
					'label' => $rdct_hero['button_label'], 'url' => $rdct_hero['button_url'],
					'style' => 'outline', 'size' => 'custom', 'custom_height' => 44,
					'padding_x' => 20, 'padding_y' => 8, 'font_size' => 14, 'radius' => 200,
					'icon' => 'brand-whatsapp', 'icon_position' => 'start', 'icon_size' => 16, 'gap' => 8,
					'color' => '#e9e6dc', 'background' => '#00000000', 'border_color' => '#777467',
					'hover_color' => $rdct_brand, 'hover_background' => '#00000000', 'hover_border_color' => $rdct_brand,
				) );
			}
			?>
		</div>
	</div>
</section>
