<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$permalink  = get_permalink();
$post_title = get_the_title();
$providers  = $args['providers'] ?? array();
$show_label = $args['show_label'] ?? false;

if ( empty( $providers ) ) {
	return;
}

foreach ( $providers as $key => $provider ) {
	switch ( $key ) {
		case 'facebook';
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Facebook', 'hub' ) ?>"
					data-sharer="facebook"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" style="width: 1em; height: 1em;">
						<path
							fill="currentColor"
							d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"
						/>
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Facebook', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'x':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'X', 'hub' ) ?>"
					data-sharer="x"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width: 1em; height: 1em;" fill="currentColor">
						<path d="M8 2H1L9.26086 13.0145L1.44995 21.9999H4.09998L10.4883 14.651L16 22H23L14.3917 10.5223L21.8001 2H19.1501L13.1643 8.88578L8 2ZM17 20L5 4H7L19 20H17Z"></path>
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'X', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'pinterest':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Pinterest', 'hub' ) ?>"
					data-sharer="pinterest"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" style="width: 1em; height: 1em;">
						<path
							fill="currentColor"
							d="M204 6.5C101.4 6.5 0 74.9 0 185.6 0 256 39.6 296 63.6 296c9.9 0 15.6-27.6 15.6-35.4 0-9.3-23.7-29.1-23.7-67.8 0-80.4 61.2-137.4 140.4-137.4 68.1 0 118.5 38.7 118.5 109.8 0 53.1-21.3 152.7-90.3 152.7-24.9 0-46.2-18-46.2-43.8 0-37.8 26.4-74.4 26.4-113.4 0-66.2-93.9-54.2-93.9 25.8 0 16.8 2.1 35.4 9.6 50.7-13.8 59.4-42 147.9-42 209.1 0 18.9 2.7 37.5 4.5 56.4 3.4 3.8 1.7 3.4 6.9 1.5 50.4-69 48.6-82.5 71.4-172.8 12.3 23.4 44.1 36 69.3 36 106.2 0 153.9-103.5 153.9-196.8C384 71.3 298.2 6.5 204 6.5z"
						/>
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Pinterest', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'linkedin':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Linkedin', 'hub' ) ?>"
					data-sharer="linkedin"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="width: 1em; height: 1em;">
						<path
							fill="currentColor"
							d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"
						/>
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Linkedin', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'email':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Email', 'hub' ) ?>"
					data-sharer="email"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
					data-subject="<?php echo esc_attr( $post_title ); ?>"
					data-to=""
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="1em"
						height="1em"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					>
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
						<path d="M3 7l9 6l9 -6" />
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Email', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'bluesky':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Bluesky', 'hub' ) ?>"
					data-sharer="bluesky"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="1em"
						height="1em"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					>
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<path d="M6.335 5.144c-1.654 -1.199 -4.335 -2.127 -4.335 .826c0 .59 .35 4.953 .556 5.661c.713 2.463 3.13 2.75 5.444 2.369c-4.045 .665 -4.889 3.208 -2.667 5.41c1.03 1.018 1.913 1.59 2.667 1.59c2 0 3.134 -2.769 3.5 -3.5c.333 -.667 .5 -1.167 .5 -1.5c0 .333 .167 .833 .5 1.5c.366 .731 1.5 3.5 3.5 3.5c.754 0 1.637 -.571 2.667 -1.59c2.222 -2.203 1.378 -4.746 -2.667 -5.41c2.314 .38 4.73 .094 5.444 -2.369c.206 -.708 .556 -5.072 .556 -5.661c0 -2.953 -2.68 -2.025 -4.335 -.826c-2.293 1.662 -4.76 5.048 -5.665 6.856c-.905 -1.808 -3.372 -5.194 -5.665 -6.856z" />
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Bluesky', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'threads':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Threads', 'hub' ) ?>"
					data-sharer="threads"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="1em"
						height="1em"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					>
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<path d="M19 7.5c-1.333 -3 -3.667 -4.5 -7 -4.5c-5 0 -8 2.5 -8 9s3.5 9 8 9s7 -3 7 -5s-1 -5 -7 -5c-2.5 0 -3 1.25 -3 2.5c0 1.5 1 2.5 2.5 2.5c2.5 0 3.5 -1.5 3.5 -5s-2 -4 -3 -4s-1.833 .333 -2.5 1" />
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Threads', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'whatsapp':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'WhatsApp', 'hub' ) ?>"
					data-sharer="whatsapp"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="1em"
						height="1em"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					>
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" />
						<path d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" />
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'WhatsApp', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'telegram':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Telegram', 'hub' ) ?>"
					data-sharer="telegram"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="1em"
						height="1em"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					>
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<path d="M15 10l-4 4l6 6l4 -16l-18 7l4 2l2 6l3 -4" />
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Telegram', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'viber':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Viber', 'hub' ) ?>"
					data-sharer="viber"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="1em" height="1em">
						<path
							fill="currentColor"
							d="M508.3 113.9C495.6 102.2 444.2 64.9 329.6 64.4C329.6 64.4 194.5 56.3 128.7 116.7C92.1 153.3 79.2 207 77.8 273.5C76.4 340 74.7 464.6 194.8 498.4L194.9 498.4L194.8 550C194.8 550 194 570.9 207.8 575.1C224.4 580.3 234.2 564.4 250.1 547.3C258.8 537.9 270.8 524.1 279.9 513.6C362.1 520.5 425.2 504.7 432.4 502.4C449 497 542.9 485 558.1 360.4C573.9 231.8 550.5 150.6 508.3 113.9zM522.2 351C509.3 455 433.2 461.6 419.2 466.1C413.2 468 357.7 481.8 288 477.3C288 477.3 236 540 219.8 556.3C214.5 561.6 208.7 561.1 208.8 550.6C208.8 543.7 209.2 464.9 209.2 464.9L209.2 464.9C107.4 436.7 113.4 330.6 114.5 275.1C115.6 219.6 126.1 174.1 157.1 143.5C212.8 93 327.5 100.5 327.5 100.5C424.4 100.9 470.8 130.1 481.6 139.9C517.3 170.5 535.5 243.7 522.2 351zM383.2 270.2C383.6 278.8 370.7 279.4 370.3 270.8C369.2 248.8 358.9 238.1 337.7 236.9C329.1 236.4 329.9 223.5 338.4 224C366.3 225.5 381.8 241.5 383.2 270.2zM403.5 281.5C404.5 239.1 378 205.9 327.7 202.2C319.2 201.6 320.1 188.7 328.6 189.3C386.6 193.5 417.5 233.4 416.4 281.8C416.3 290.4 403.3 290 403.5 281.5zM450.5 294.9C450.6 303.5 437.6 303.6 437.6 295C437 213.5 382.7 169.1 316.8 168.6C308.3 168.5 308.3 155.7 316.8 155.7C390.5 156.2 449.8 207.1 450.5 294.9zM439.2 393L439.2 393.2C428.4 412.2 408.2 433.2 387.4 426.5L387.2 426.2C366.1 420.3 316.4 394.7 285 369.7C268.8 356.9 254 341.8 242.6 327.3C232.3 314.4 221.9 299.1 211.8 280.7C190.5 242.2 185.8 225 185.8 225C179.1 204.2 200 184 219.1 173.2L219.3 173.2C228.5 168.4 237.3 170 243.2 177.1C243.2 177.1 255.6 191.9 260.9 199.2C265.9 206 272.6 216.9 276.1 223C282.2 233.9 278.4 245 272.4 249.6L260.4 259.2C254.3 264.1 255.1 273.2 255.1 273.2C255.1 273.2 272.9 340.5 339.4 357.5C339.4 357.5 348.5 358.3 353.4 352.2L363 340.2C367.6 334.2 378.7 330.4 389.6 336.5C404.3 344.8 423 357.7 435.4 369.4C442.4 375.1 444 383.8 439.2 393z"
						/>
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Viber', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'tumblr':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Tumblr', 'hub' ) ?>"
					data-sharer="tumblr"
					data-caption="<?php echo esc_attr( $post_title ); ?>"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="1em"
						height="1em"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					>
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<path d="M14 21h4v-4h-4v-6h4v-4h-4v-4h-4v1a3 3 0 0 1 -3 3h-1v4h4v6a4 4 0 0 0 4 4" />
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Tumblr', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'hackernews':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Hackernews', 'hub' ) ?>"
					data-sharer="hackernews"
					data-caption="<?php echo esc_attr( $post_title ); ?>"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="1em" height="1em">
						<path
							fill="currentColor"
							d="M544 160C544 124.7 515.3 96 480 96L160 96C124.7 96 96 124.7 96 160L96 480C96 515.3 124.7 544 160 544L480 544C515.3 544 544 515.3 544 480L544 160zM117 293.2C117 293.2 117.1 293.1 117.1 293C117.1 292.9 117.2 292.8 117.3 292.8C117.3 292.9 117.3 293.1 117.2 293.2L117 293.2zM335.2 448L303.8 448L303.8 345.3L224 192L261.3 192C302.8 269.7 309.4 287.8 315.4 304C317 308.3 318.5 312.5 320.6 317.6C323.8 310.6 325.7 305.7 327.7 300.3C333.6 285 340.5 267.1 381.2 192L416 192L335.2 347.1L335.2 448z"
						/>
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Hackernews', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'reddit':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Reddit', 'hub' ) ?>"
					data-sharer="reddit"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="1em"
						height="1em"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					>
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<path d="M12 8c2.648 0 5.028 .826 6.675 2.14a2.5 2.5 0 0 1 2.326 4.36c0 3.59 -4.03 6.5 -9 6.5c-4.875 0 -8.845 -2.8 -9 -6.294l-1 -.206a2.5 2.5 0 0 1 2.326 -4.36c1.646 -1.313 4.026 -2.14 6.674 -2.14z" />
						<path d="M12 8l1 -5l6 1" />
						<path d="M19 4m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
						<circle cx="9" cy="13" r=".5" fill="currentColor" />
						<circle cx="15" cy="13" r=".5" fill="currentColor" />
						<path d="M10 17c.667 .333 1.333 .5 2 .5s1.333 -.167 2 -.5" />
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Reddit', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'vk':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'VK.com', 'hub' ) ?>"
					data-sharer="vk"
					data-caption="<?php echo esc_attr( $post_title ); ?>"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="1em"
						height="1em"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					>
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<path d="M14 19h-4a8 8 0 0 1 -8 -8v-5h4v5a4 4 0 0 0 4 4h0v-9h4v4.5l.03 0a4.531 4.531 0 0 0 3.97 -4.496h4l-.342 1.711a6.858 6.858 0 0 1 -3.658 4.789h0a5.34 5.34 0 0 1 3.566 4.111l.434 2.389h0h-4a4.531 4.531 0 0 0 -3.97 -4.496v4.5z" />
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'VK.com', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'buffer':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Buffer', 'hub' ) ?>"
					data-sharer="buffer"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="1em" height="1em">
						<path
							fill="currentColor"
							d="M524.2 444.7L327.7 542.5C325.4 543.5 322.9 544 320.4 544C317.9 544 315.4 543.5 313.1 542.5L116.5 444.7C112.5 442.7 112.5 439.4 116.5 437.4L163.6 414C165.9 413 168.4 412.5 170.9 412.5C173.4 412.5 175.9 413 178.2 414L313 481C315.3 482 317.8 482.5 320.3 482.5C322.8 482.5 325.3 482 327.6 481L462.4 414C464.7 413 467.2 412.5 469.7 412.5C472.2 412.5 474.7 413 477 414L524.1 437.4C528.1 439.4 528.1 442.6 524.1 444.6zM524.2 308.2L477.1 284.8C474.8 283.8 472.3 283.3 469.8 283.3C467.3 283.3 464.8 283.8 462.5 284.8L327.7 351.8C325.4 352.8 322.9 353.3 320.4 353.3C317.9 353.3 315.4 352.8 313.1 351.8L178.3 284.7C176 283.7 173.5 283.2 171 283.2C168.5 283.2 166 283.7 163.7 284.7L116.5 308.1C112.5 310.1 112.5 313.4 116.5 315.4L313 413.2C315.3 414.2 317.8 414.7 320.3 414.7C322.8 414.7 325.3 414.2 327.6 413.2L524.1 315.4C528.1 313.4 528.1 310.1 524.1 308.1zM116.5 194.4L313 284.7C317.7 286.6 323 286.6 327.7 284.7L524.2 194.4C528.2 192.5 528.2 189.5 524.2 187.7L327.7 97.4C323 95.5 317.7 95.5 313 97.4L116.5 187.7C112.5 189.5 112.5 192.6 116.5 194.4z"
						/>
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Buffer', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'xing':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Xing', 'hub' ) ?>"
					data-sharer="xing"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="1em" height="1em">
						<path
							fill="currentColor"
							d="M290.7 274C288.9 277.3 265.5 318.4 220.6 397.5C215.7 405.8 209.8 410 202.9 410L137.8 410C130.1 410 125.7 402.5 129.3 395.6L198.3 274.3C198.5 274.3 198.5 274.2 198.3 274L154.4 198.4C150.1 190.6 154.7 184.3 162.9 184.3L228 184.3C235.3 184.3 241.3 188.4 246 196.5L290.7 274zM510.6 110.1L366.6 363.1L366.6 363.4L458.2 530C462.1 537.1 458.4 544.1 449.7 544.1L384.5 544.1C376.9 544.1 370.9 540.1 366.5 531.9L274.1 363.4C277.4 357.6 325.6 272.6 418.9 108.2C423.5 100.1 429.3 96 436.4 96L502.1 96C510.1 96 514.4 102.7 510.6 110.1z"
						/>
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Xing', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'line':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Line', 'hub' ) ?>"
					data-sharer="line"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="1em" height="1em">
						<path
							fill="currentColor"
							d="M375 260.8L375 342.1C375 344.2 373.4 345.8 371.3 345.8L358.3 345.8C357 345.8 355.9 345.1 355.3 344.3L318 294L318 342.2C318 344.3 316.4 345.9 314.3 345.9L301.3 345.9C299.2 345.9 297.6 344.3 297.6 342.2L297.6 260.9C297.6 258.8 299.2 257.2 301.3 257.2L314.2 257.2C315.3 257.2 316.6 257.8 317.2 258.8L354.5 309.1L354.5 260.9C354.5 258.8 356.1 257.2 358.2 257.2L371.2 257.2C373.3 257.1 375 258.8 375 260.7L375 260.8zM281.3 257.1L268.3 257.1C266.2 257.1 264.6 258.7 264.6 260.8L264.6 342.1C264.6 344.2 266.2 345.8 268.3 345.8L281.3 345.8C283.4 345.8 285 344.2 285 342.1L285 260.8C285 258.9 283.4 257.1 281.3 257.1zM249.9 325.2L214.3 325.2L214.3 260.8C214.3 258.7 212.7 257.1 210.6 257.1L197.6 257.1C195.5 257.1 193.9 258.7 193.9 260.8L193.9 342.1C193.9 343.1 194.2 343.9 194.9 344.6C195.6 345.2 196.4 345.6 197.4 345.6L249.6 345.6C251.7 345.6 253.3 344 253.3 341.9L253.3 328.9C253.3 327 251.7 325.2 249.8 325.2L249.9 325.2zM443.6 257.1L391.3 257.1C389.4 257.1 387.6 258.7 387.6 260.8L387.6 342.1C387.6 344 389.2 345.8 391.3 345.8L443.5 345.8C445.6 345.8 447.2 344.2 447.2 342.1L447.2 329C447.2 326.9 445.6 325.3 443.5 325.3L408 325.3L408 311.7L443.5 311.7C445.6 311.7 447.2 310.1 447.2 308L447.2 294.9C447.2 292.8 445.6 291.2 443.5 291.2L408 291.2L408 277.5L443.5 277.5C445.6 277.5 447.2 275.9 447.2 273.8L447.2 260.8C447.1 258.9 445.5 257.1 443.5 257.1L443.6 257.1zM576 157.4L576 483.4C575.9 534.6 533.9 576.1 482.6 576L156.6 576C105.4 575.9 63.9 533.8 64 482.6L64 156.6C64.1 105.4 106.2 63.9 157.4 64L483.4 64C534.6 64.1 576.1 106.1 576 157.4zM505.6 297.5C505.6 214.1 421.9 146.2 319.2 146.2C216.5 146.2 132.8 214.1 132.8 297.5C132.8 372.2 199.1 434.9 288.7 446.8C310.5 451.5 308 459.5 303.1 488.9C302.3 493.6 299.3 507.3 319.2 499C339.1 490.7 426.5 435.8 465.7 390.8C492.7 361.1 505.6 331 505.6 297.7L505.6 297.5z"
						/>
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Line', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'instapaper':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Instapaper', 'hub' ) ?>"
					data-sharer="instapaper"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-caption="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="1em" height="1em" viewBox="0 0 32 32">
						<path d="M19.688 27.010c0 2.427 0.359 2.786 3.911 3.057v1.932h-15.198v-1.932c3.557-0.271 3.911-0.63 3.911-3.057v-22.068c0-2.375-0.359-2.786-3.911-3.057v-1.885h15.198v1.885c-3.552 0.271-3.911 0.677-3.911 3.057z" />
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Instapaper', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'pocket':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Pocket', 'hub' ) ?>"
					data-sharer="pocket"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="1em" height="1em">
						<path
							fill="currentColor"
							d="M503.6 128L136.6 128C114.5 128 96 146.5 96 168.6L96 303.8C96 428.5 195.7 528 320.2 528C444.2 528 544 428.5 544 303.8L544 168.6C544 146.2 526.3 128 503.6 128zM341.6 396.5C329.2 408.3 310.2 407.6 299.2 396.5C185.5 287.6 184.3 291.4 184.3 273.3C184.3 256.4 198.1 242.6 215 242.6C232 242.6 231.1 246.4 320.2 331.9C410.8 245 408.8 242.6 425.7 242.6C442.6 242.6 456.4 256.4 456.4 273.3C456.4 291.1 453.5 289 341.6 396.5z"
						/>
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Pocket', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'flipboard':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Flipboard', 'hub' ) ?>"
					data-sharer="flipboard"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="1em" height="1em">
						<path
							fill="currentColor"
							d="M96 96L96 544L544 544L544 96L96 96zM454.4 275.2L364.8 275.2L364.8 364.8L275.2 364.8L275.2 454.4L185.6 454.4L185.6 185.6L454.4 185.6L454.4 275.2z"
						/>
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Flipboard', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'weibo':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Weibo', 'hub' ) ?>"
					data-sharer="weibo"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="1em" height="1em">
						<path
							fill="currentColor"
							d="M471 241.6C478.6 217.6 457.6 194.8 433.6 199.9C411.6 204.7 404.8 171.8 426.5 167.1C476.6 156.2 518.8 204.2 503 251.9C496.2 273.1 464.2 262.7 471 241.6zM278.8 510.7C172.5 510.7 64 459.3 64 374.4C64 330.1 92 279 140.3 230.7C240 131 343.5 129.8 313.9 225C309.9 238.1 326.2 230.7 326.2 231C405.7 197.4 466.7 214.2 440.2 282.4C436.5 291.8 441.3 293.3 448.5 295.5C584.2 337.8 483.3 510.7 278.8 510.7zM422.5 364.4C417.1 308.7 344 270.4 259.1 278.7C174.3 287.3 110.3 339 115.7 394.7C121.1 450.4 194.2 488.7 279.1 480.4C363.9 471.8 427.9 420.1 422.5 364.4zM411.9 99.1C386 104.7 395.1 142.8 420.2 137.4C492.5 122.2 555 190.2 531.9 261.4C524.5 285.6 561 298.4 569.3 273.4C601.2 173.6 514.2 77.5 411.9 99.1zM333.4 410.1C316.3 448.9 266.6 470.1 224.3 456.4C183.5 443.3 166.3 403 184 366.7C201.7 331.3 247.1 311.3 287.4 321.6C329.4 332.4 350.5 371.8 333.4 410.1zM247.1 380.1C234.2 374.7 217.1 380.4 209.1 393C200.8 405.9 204.8 421 217.7 427C230.8 433 248.5 427.3 256.8 414.1C264.8 401 260.5 385.8 247.1 380.1zM279.7 366.7C274.6 365 268.3 367.3 265.4 372.1C262.5 377.2 264 382.7 269.1 385C274.2 387 280.8 384.7 283.7 379.6C286.5 374.4 284.8 368.7 279.7 366.7z"
						/>
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Weibo', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'blogger':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Blogger', 'hub' ) ?>"
					data-sharer="blogger"
					data-description="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="1em" height="1em">
						<path
							fill="currentColor"
							d="M258.4 260C263.2 255.1 264.6 254.9 294.8 254.9C322 254.9 322.9 255 326.9 257C332.7 259.9 335.2 264 335.2 270.6C335.2 276.5 332.8 280.6 327.6 284C324.8 285.8 323.1 285.9 296.5 286.1C280.1 286.2 267 285.9 265 285.3C254.7 282.4 250.9 267.6 258.4 260zM319.8 354.5C265.9 354.5 264 354.7 259.6 358.6C256.1 361.7 253.9 368 254.5 372.5C255.2 377.2 259.3 382.6 263.7 384.5C265.9 385.5 277.8 386.2 320 385.7L367.9 385.1L377.1 383.6C386.1 378.5 387.6 366.2 380.2 359.2C374.9 354.5 375.2 354.5 319.8 354.5zM543.2 484.6C539.7 513 520.2 535 492.1 542.1C484.9 543.9 482.4 544 319.2 543.9C161.4 543.9 153.3 543.8 147.2 542.1C138.8 539.9 131.6 536.6 124.9 532.1C119.3 528.3 111 520.3 107.9 515.7C104.1 510.1 99.7 500.4 97.9 493.7C96.1 487 96 484.3 96 320.3C96 157.2 96 153.7 97.8 146.6C104.1 121.9 123.7 103 149 97.4C156.3 95.8 481.1 95.5 489 97.1C510.2 101.4 526.9 114.2 536.6 133.5C544.3 148.8 543.6 132 543.9 314.1C544.1 429.9 543.9 478.6 543.2 484.6zM457.8 299.4C456.7 294.4 453.6 289.8 450.1 287.9C449 287.3 442.1 286.6 434.6 286.2C422.2 285.6 420.8 285.4 416.8 283.1C410.6 279.5 408.9 275.5 408.8 264.8C408.8 244.4 400.3 225.4 383.5 208.3C371.5 196.1 358.2 187.8 342.9 183.2C339.3 182.1 331.1 181.7 303.7 181.4C260.8 180.9 251.2 181.8 236.6 187.6C209.6 198.3 190.3 221 183.2 250C181.9 255.4 181.6 264.2 181.3 314.3C180.9 377.1 181.3 386.4 185.3 398.8C195 429.5 222.4 452.2 249.9 457.2C259.1 458.9 372.1 459.3 383.6 457.7C403.7 455 419.5 446.9 434.3 431.8C445 420.9 451.7 409 456.1 393.3C459.3 382.4 459 304.9 457.8 299.4z"
						/>
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Blogger', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'baidu':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Baidu', 'hub' ) ?>"
					data-sharer="baidu"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="1em"
						height="1em"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					>
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<path d="M5 9.5m-1 0a1 1.5 0 1 0 2 0a1 1.5 0 1 0 -2 0" />
						<path d="M14.463 11.596c1.282 1.774 3.476 3.416 3.476 3.416s1.921 1.574 .593 3.636c-1.328 2.063 -4.892 1.152 -4.892 1.152s-1.416 -.44 -3.06 -.088c-1.644 .356 -3.06 .22 -3.06 .22s-2.055 -.22 -2.47 -2.304c-.416 -2.084 1.918 -3.638 2.102 -3.858c.182 -.222 1.409 -.966 2.284 -2.394c.875 -1.428 3.337 -2.287 5.027 .221z" />
						<path d="M9 4.5m-1 0a1 1.5 0 1 0 2 0a1 1.5 0 1 0 -2 0" />
						<path d="M15 4.5m-1 0a1 1.5 0 1 0 2 0a1 1.5 0 1 0 -2 0" />
						<path d="M19 9.5m-1 0a1 1.5 0 1 0 2 0a1 1.5 0 1 0 -2 0" />
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Baidu', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'okru':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'OK.ru', 'hub' ) ?>"
					data-sharer="okru"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="1em"
						height="1em"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					>
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<path d="M12 9m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
						<path d="M20 12c0 8 0 8 -8 8s-8 0 -8 -8s0 -8 8 -8s8 0 8 8z" />
						<path d="M9.5 13c1.333 .667 3.667 .667 5 0" />
						<path d="M9.5 17l2.5 -3l2.5 3" />
						<path d="M12 13.5v.5" />
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'OK.ru', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'evernote':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Evernote', 'hub' ) ?>"
					data-sharer="evernote"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="1em"
						height="1em"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					>
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<path d="M4 8h5v-5" />
						<path d="M17.9 19c.6 -2.5 1.1 -5.471 1.1 -9c0 -4.5 -2 -5 -3 -5c-1.906 0 -3 -.5 -3.5 -1c-.354 -.354 -.5 -1 -1.5 -1h-2l-5 5c0 6 2.5 8 5 8c1 0 1.5 -.5 2 -1.5s1.414 -.326 2.5 0c1.044 .313 2.01 .255 2.5 .5c1 .5 2 1.5 2 3c0 .5 0 3 -3 3s-3 -3 -1 -3" />
						<path d="M15 10h1" />
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Evernote', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'skype':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Skype', 'hub' ) ?>"
					data-sharer="skype"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="1em"
						height="1em"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
						class="icon icon-tabler icons-tabler-outline icon-tabler-brand-skype"
					>
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<path d="M12 3a9 9 0 0 1 8.603 11.65a4.5 4.5 0 0 1 -5.953 5.953a9 9 0 0 1 -11.253 -11.253a4.5 4.5 0 0 1 5.953 -5.954a8.987 8.987 0 0 1 2.65 -.396z" />
						<path d="M8 14.5c.5 2 2.358 2.5 4 2.5c2.905 0 4 -1.187 4 -2.5c0 -1.503 -1.927 -2.5 -4 -2.5s-4 -1 -4 -2.5c0 -1.313 1.095 -2.5 4 -2.5c1.642 0 3.5 .5 4 2.5" />
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Skype', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'trello':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Trello', 'hub' ) ?>"
					data-sharer="trello"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="1em"
						height="1em"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
					>
						<path stroke="none" d="M0 0h24v24H0z" fill="none" />
						<path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
						<path d="M7 7h3v10h-3z" />
						<path d="M14 7h3v6h-3z" />
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Trello', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;

		case 'diaspora':
			?>
			<li>
				<a
					rel="nofollow"
					href="javascript:void(0);"
					aria-label="<?php echo esc_attr__( 'Diaspora', 'hub' ) ?>"
					data-sharer="diaspora"
					data-title="<?php echo esc_attr( $post_title ); ?>"
					data-description="<?php echo esc_attr( $post_title ); ?>"
					data-url="<?php echo esc_url( $permalink ); ?>"
				>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="1em" height="1em">
						<path
							fill="currentColor"
							d="M315.7 418.6C314.3 418.6 227.7 538.5 227 538.5C226.3 538.5 140.4 478 140 477.3C139.6 476.6 226.6 351.5 226.6 349.8C226.6 347.6 97 305.8 89 302.7C87.7 302.2 120.4 200.9 120.7 200.6C121.3 199.9 265.1 247.6 266.2 247.6C266.6 247.6 267.1 247 267.2 246.3C267.6 244.3 268.2 97.7 268.9 96.7C269.7 95.5 373.4 96 374 96.4C375.5 97.4 377.5 252.5 380.1 252.5C381.5 252.5 518.8 205.5 519.4 206.2C520.2 207.1 551.3 308.4 550.9 308.8C550 309.7 410.7 355.9 410.3 357.6C410 359 493.1 479.7 492.8 480.5C492.5 481.3 407.3 544 406.5 544C405.5 543.8 317.5 418.5 315.6 418.5L315.6 418.5z"
						/>
					</svg>
					<?php if ( $show_label === 'with-text-outline' )
						echo '<span style="margin-inline-start: 1em;">' . esc_html__( 'Diaspora', 'hub' ) . '</span>' ?>
				</a>
			</li>
			<?php
			break;
	}
}