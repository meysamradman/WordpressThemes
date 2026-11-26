<?php

defined( 'ABSPATH' ) || die();

$el_id = $element->get_id();
$mask_id = 'lqd-cutout-mask-' . $el_id;

$placement = $element->get_settings( 'lqd_shape_cutout_placement_2' ) ?: 'b';
$placements = 'tb' === $placement ? [ 't', 'b' ] : [ $placement ];

$element->add_render_attribute( 'shape-cutout', [
	'class' => [ 'lqd-cutout', 'lqd-cutout-2', 'lqd-cutout-' . $placement, 'lqd-cutout-' . $el_id, 'pointer-events-none' ],
]);

// Mask is composited with `exclude`: white = hole, black = keep.
$half = '
<rect class="lqd-cutout-mask-rect" width="100" height="100" rx="20" fill="white"/>
<rect class="lqd-cutout-mask-shoulder" width="100" height="100" fill="white"/>
<circle class="lqd-cutout-mask-shoulder-clip" cx="50" cy="50" r="50" fill="black"/>';

?>

<style>
	.elementor-element-<?php echo esc_attr($el_id) ?> {
		mask: linear-gradient(0deg, black, black), url(#<?php echo esc_attr($mask_id) ?>);
		mask-composite: exclude;
	}
</style>

<div <?php $element->print_render_attribute_string('shape-cutout') ?>>
	<svg
		 class="lqd-cutout-svg pos-abs pos-tl w-100 h-100 w-full h-full pointer-events-none z-index--1"
		 role="none"
		 width="100"
		 height="100"
		 fill="none"
		 xmlns="http://www.w3.org/2000/svg">
		<defs>
			<mask id="<?php echo esc_attr($mask_id) ?>">
				<?php foreach ( $placements as $p ) : ?>
				<g class="lqd-cutout-mask-g-wrap lqd-cutout-mask-g-wrap-<?php echo esc_attr( $p ) ?>">
					<g class="lqd-cutout-mask-g">
						<g class="lqd-cutout-mask-half lqd-cutout-mask-half-r">
							<?php echo $half; ?>
						</g>
						<g class="lqd-cutout-mask-half-l">
							<g class="lqd-cutout-mask-half">
								<?php echo $half; ?>
							</g>
						</g>
					</g>
				</g>
				<?php endforeach; ?>
			</mask>
		</defs>
	</svg>
</div>
