<?php

namespace RadmanCTComponents\Components\Testimonial;

defined( 'ABSPATH' ) || exit;

function preset( string $style, array $item, array $values, string $content, string $stars, string $source, string $align, string $css ): string {
	$number = (int) substr( $style, 1 );
	$portrait_style = in_array( $number, array( 4, 7, 16 ), true );
	$avatar = in_array( $number, array( 15, 19 ), true ) ? '' : image( $item['image_id'] ?? 0, $portrait_style ? 'rdct-tst__portrait-image' : 'rdct-tst__avatar', $portrait_style ? 'medium_large' : 'thumbnail' );
	$logo = image( $item['client_image_id'] ?? 0, 'rdct-tst__logo' );
	$detail = $values['date'] . ( $values['date'] && $source ? ' &middot; ' : '' ) . $source;
	$author = '<div class="rdct-tst__author"><cite>' . $values['title'] . '</cite><span>' . $values['position'] . '</span>' . ( $detail ? '<small>' . $detail . '</small>' : '' ) . '</div>';
	$info = '<figcaption class="rdct-tst__info">' . ( $portrait_style || in_array( $number, array( 15, 19 ), true ) ? '' : $avatar ) . $author . '</figcaption>';
	$quote = '<blockquote class="rdct-tst__quote">' . $content . '</blockquote>';
	$mark = in_array( $number, array( 10, 12, 16 ), true ) ? '<span class="rdct-tst__quote-mark" aria-hidden="true">&#8220;</span>' : '';
	$before_logo = in_array( $number, array( 5, 19 ), true );
	$before_rating = in_array( $number, array( 11, 18 ), true );
	$review = '<div class="rdct-tst__review">' . ( $before_logo ? $logo : '' ) . $mark . ( $before_rating ? $stars : '' ) . $quote . ( ! $before_rating ? $stars : '' ) . ( ! $before_logo ? $logo : '' ) . '</div>';
	$portrait = $portrait_style && $avatar ? '<div class="rdct-tst__portrait">' . $avatar . '</div>' : '';
	$first = in_array( $number, array( 1, 4, 8, 9, 14 ), true );
	$body = $first ? $info . $review . $portrait : $portrait . $review . $info;
	return '<figure class="rdct-tst rdct-tst--' . $style . ( $portrait ? ' has-portrait' : '' ) . '" data-align="' . $align . '" style="' . esc_attr( $css ) . '">' . $body . '</figure>';
}
