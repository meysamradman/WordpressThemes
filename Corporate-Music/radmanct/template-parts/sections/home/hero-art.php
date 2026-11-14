<?php
defined( 'ABSPATH' ) || exit;
$rdct_art_image = wp_get_attachment_image_url( \RadmanCT\Home\image_id( get_the_ID() ), 'full' );
$rdct_art_clean = wp_get_attachment_image_url( \RadmanCT\media_id( 'hero-clean' ), 'full' );
if ( ! $rdct_art_clean ) { \RadmanCT\Home\media( get_the_ID() ); return; }
$rdct_art_vendor = \RadmanCT\Assets\entry( 'src/js/gsap-vendor.js' );
$rdct_art_id = wp_unique_id( 'rdc-hero-art-' );
$rdct_labels = array(
	array( 937, 278, 65, 14, 0 ), array( 937, 298, 65, 14, .22 ),
	array( 937, 318, 65, 14, .44 ), array( 937, 338, 65, 14, .66 ),
	array( 91, 499, 126, 17, .8 ), array( 91, 523, 126, 17, 1.02 ),
	array( 91, 547, 126, 17, 1.24 ), array( 91, 571, 126, 17, 1.46 ),
);
$rdct_nodes = array(
	array( 412, 233, 4, 0, 0, 'matrix(1 -.125 0 1 -12 53.5)' ), array( 551, 276, 4, 11, 9 ), array( 784, 276, 4, 10, 11 ),
	array( 938, 703, 6, -2, 13 ), array( 891, 814, 4, 0, 10 ), array( 252, 791, 4, -10, -11 ), array( 277, 814, 4, -10, -9 ),
	array( 95, 378, 4, 0, -12 ), array( 95, 474, 5, 0, 12 ), array( 863, 378, 4, 0, 14 ),
	array( 911, 260, 4, 11, 0 ), array( 924, 260, 5, 0, -14 ), array( 924, 360, 4, 0, 12 ),
);
$rdct_tracks = array(
	array( 'M260 254 L300 218 L350 187 L400 166 L450 154 L500 148 L550 150 L600 158 L650 175 L700 202 L750 239 L800 294', 32, .32 ),
	array( 'M863 377 V300 L891 260 H911', 18, .62 ),
	array( 'M350 250 L375 241 L400 235 L412 233 L430 233 L450 234 L475 239 L500 247 L525 259 L550 274', 24, .28 ),
	array( 'M938 703 L927 740 L914 770 L896 800 L873 830 L840 860 L799 890 L736 920', 30, .38 ),
	array( 'M95 378 V474 H12', 20, .3 ), array( 'M924 260 V360', 19, .55 ),
);
?>
<div class="rdc-hero__cvs" <?php if ( $rdct_art_vendor ) { echo 'data-rdc-ofr-art data-gsap-src="' . esc_url( \RadmanCT\Assets\url( $rdct_art_vendor['file'] ) ) . '"'; } ?>>
	<?php \RadmanCT\Home\media( get_the_ID() ); ?>
	<svg class="rdc-hero__dtl" viewBox="0 0 1920 1080" aria-hidden="true" focusable="false">
		<defs>
			<image id="<?php echo esc_attr( $rdct_art_id ); ?>-source" href="<?php echo esc_url( $rdct_art_image ); ?>" width="1920" height="1080" />
			<image id="<?php echo esc_attr( $rdct_art_id ); ?>-clean" href="<?php echo esc_url( $rdct_art_clean ); ?>" width="1920" height="1080" preserveAspectRatio="none" />
			<filter id="<?php echo esc_attr( $rdct_art_id ); ?>-ink" color-interpolation-filters="sRGB">
				<feComponentTransfer><feFuncR type="linear" slope="6" intercept="-.35" /><feFuncG type="linear" slope="6" intercept="-.35" /><feFuncB type="linear" slope="6" intercept="-.35" /></feComponentTransfer>
			</filter>
			<clipPath id="<?php echo esc_attr( $rdct_art_id ); ?>-patches">
				<rect x="152" y="804" width="24" height="68" />
				<?php foreach ( $rdct_labels as $rdct_label ) : ?>
				<rect x="<?php echo (int) $rdct_label[0] - 2; ?>" y="<?php echo (int) $rdct_label[1] - 2; ?>" width="<?php echo (int) $rdct_label[2] + 4; ?>" height="<?php echo (int) $rdct_label[3] + 4; ?>" />
				<?php endforeach; ?>
			</clipPath>
			<?php foreach ( $rdct_nodes as $rdct_index => $rdct_node ) : ?>
			<clipPath id="<?php echo esc_attr( $rdct_art_id . '-node-' . $rdct_index ); ?>"><circle cx="<?php echo (int) $rdct_node[0]; ?>" cy="<?php echo (int) $rdct_node[1]; ?>" r="<?php echo (int) $rdct_node[2]; ?>" /></clipPath>
			<?php endforeach; ?>
			<?php foreach ( $rdct_labels as $rdct_index => $rdct_label ) : ?>
			<clipPath id="<?php echo esc_attr( $rdct_art_id . '-lbl-' . $rdct_index ); ?>"><rect x="<?php echo (int) $rdct_label[0]; ?>" y="<?php echo (int) $rdct_label[1]; ?>" width="<?php echo (int) $rdct_label[2]; ?>" height="<?php echo (int) $rdct_label[3]; ?>" /></clipPath>
			<mask id="<?php echo esc_attr( $rdct_art_id . '-mask-' . $rdct_index ); ?>" maskUnits="userSpaceOnUse" x="<?php echo (int) $rdct_label[0]; ?>" y="<?php echo (int) $rdct_label[1]; ?>" width="<?php echo (int) $rdct_label[2]; ?>" height="<?php echo (int) $rdct_label[3]; ?>" style="mask-type:luminance">
				<use href="#<?php echo esc_attr( $rdct_art_id ); ?>-source" filter="url(#<?php echo esc_attr( $rdct_art_id ); ?>-ink)" clip-path="url(#<?php echo esc_attr( $rdct_art_id . '-lbl-' . $rdct_index ); ?>)" />
			</mask>
			<?php endforeach; ?>
		</defs>
		<g clip-path="url(#<?php echo esc_attr( $rdct_art_id ); ?>-patches)"><use href="#<?php echo esc_attr( $rdct_art_id ); ?>-clean" /></g>
		<?php foreach ( $rdct_nodes as $rdct_index => $rdct_node ) : ?>
		<g clip-path="url(#<?php echo esc_attr( $rdct_art_id . '-node-' . $rdct_index ); ?>)"><use href="#<?php echo esc_attr( $rdct_art_id ); ?>-source" x="<?php echo (int) $rdct_node[3]; ?>" y="<?php echo (int) $rdct_node[4]; ?>"<?php if ( isset( $rdct_node[5] ) ) { echo ' transform="' . esc_attr( $rdct_node[5] ) . '"'; } ?> /></g>
		<?php endforeach; ?>
		<?php foreach ( $rdct_labels as $rdct_index => $rdct_label ) : ?>
		<g data-art-label data-art-label-delay="<?php echo esc_attr( $rdct_label[4] ); ?>"><use href="#<?php echo esc_attr( $rdct_art_id ); ?>-source" mask="url(#<?php echo esc_attr( $rdct_art_id . '-mask-' . $rdct_index ); ?>)" /></g>
		<?php endforeach; ?>
		<?php foreach ( $rdct_tracks as $rdct_track ) : ?>
		<path d="<?php echo esc_attr( $rdct_track[0] ); ?>" data-orbit-pipe data-orbit-duration="<?php echo (int) $rdct_track[1]; ?>" fill="none" stroke="none" />
		<?php endforeach; ?>
	</svg>
	<?php foreach ( $rdct_tracks as $rdct_index => $rdct_track ) : ?>
	<span class="rdc-hero__dot<?php echo in_array( $rdct_index, array( 1, 4, 5 ), true ) ? ' rdc-hero__dot--muted' : ''; ?>" data-orbit-track="<?php echo (int) $rdct_index; ?>" data-orbit-phase="<?php echo esc_attr( $rdct_track[2] ); ?>"></span>
	<?php endforeach; ?>
</div>
