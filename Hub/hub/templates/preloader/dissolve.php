<div class="lqd-preloader-wrap lqd-preloader-dissolve"
data-preloader-options='{ "animationType": "scale", "animationTargets": ".lqd-preloader-dissolve-el", "stagger": 12, "dir": "y", "duration": 600 }'>
	<div class="lqd-preloader-inner d-flex flex-column">

		<?php for ( $i = 0; $i <= 26; $i++ ) : ?>
			<div class="lqd-preloader-dissolve-el w-100 flex-grow-1"></div>
		<?php endfor; ?>

		<div class="lqd-preloader-dissolve-el pos-abs w-100 h-100 pos-tl d-flex flex-column align-items-center justify-content-center"  data-animations='{ "delay": 0.15, "opacity": 0, "scale": 0.975, "duration": 0.45, "ease": "power2.out" }'>
           	<?php liquid_preloader_image(); ?>
		</div>
	</div>
</div>
