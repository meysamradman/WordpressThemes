<?php

namespace RadmanCT\Fonts;

defined( 'ABSPATH' ) || exit;

function roles(): array {
	return array( 'headings', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'link', 'span' );
}

function parent_role( string $role ): string {
	return in_array( $role, array( 'headings', 'link', 'span' ), true ) ? 'body' : 'headings';
}

function primary_group( array $settings ): string {
	$language = apply_filters( 'wpml_current_language', '' );
	if ( ! is_string( $language ) || '' === $language || 'all' === $language ) {
		if ( in_array( $settings['font_language'] ?? '', array( 'persian', 'latin' ), true ) ) { return $settings['font_language']; }
		$language = get_locale();
	}
	return preg_match( '/^(fa|ar|ur|ps)(?:[_-]|$)/i', $language ) ? 'persian' : 'latin';
}

function active_groups( array $settings ): array {
	return ! empty( $settings['mixed_language_fonts'] ) ? array( 'persian', 'latin' ) : array( primary_group( $settings ) );
}

function registry(): array {
	return array(
		'iransansx' => array( 'group' => 'persian', 'label' => 'IRANSansX', 'family' => 'Radman CT IRANSansX', 'files' => array( array( 'persian/IRANSans/IRANSansXV.woff2', '100 1000' ) ) ),
		'peyda' => array( 'group' => 'persian', 'label' => 'Peyda', 'family' => 'Radman CT Peyda', 'files' => array( array( 'persian/Peyda/PeydaWebVF.woff2', '100 1000' ) ) ),
		'gramophone' => array( 'group' => 'persian', 'label' => 'Gramophone — 700', 'family' => 'Radman CT Gramophone', 'files' => array( array( 'persian/Gramophone/GramophoneFaNum-CleanCnd.woff2', '700' ) ) ),
		'iranyekan' => array( 'group' => 'persian', 'label' => 'IRANYekan', 'family' => 'Radman CT IRANYekan', 'files' => array( array( 'persian/Yekan/IRANYekanWebLight.woff2', '300' ), array( 'persian/Yekan/IRANYekanWebRegular.woff2', '400' ), array( 'persian/Yekan/IRANYekanWebMedium.woff2', '500' ), array( 'persian/Yekan/IRANYekanWebBold.woff2', '700' ), array( 'persian/Yekan/IRANYekanWebExtraBold.woff2', '800' ) ) ),
		'nouzdah' => array( 'group' => 'persian', 'label' => 'Nouzdah AOL FaNum', 'family' => 'Radman CT Nouzdah', 'files' => array( array( 'persian/Nouzdah/NouzdahAOLFaNum-Regular.woff2', '400' ), array( 'persian/Nouzdah/NouzdahAOLFaNum-Medium.woff2', '500' ), array( 'persian/Nouzdah/NouzdahAOLFaNum-SemiBold.woff2', '600' ), array( 'persian/Nouzdah/NouzdahAOLFaNum-Bold.woff2', '700' ) ) ),
		'pesteh' => array( 'group' => 'persian', 'label' => 'Pesteh', 'family' => 'Radman CT Pesteh', 'files' => array( array( 'persian/Peste/Pesteh-Regular.woff2', '400' ), array( 'persian/Peste/Pesteh-Medium.woff2', '500' ), array( 'persian/Peste/Pesteh-Bold.woff2', '700' ), array( 'persian/Peste/Pesteh-ExtraBold.woff2', '800' ), array( 'persian/Peste/Pesteh-Heavy.woff2', '900' ) ) ),
		'pinar' => array( 'group' => 'persian', 'label' => 'Pinar', 'family' => 'Radman CT Pinar', 'files' => array( array( 'persian/Pinar/Pinar-VF[DSTY,KSHD,wght].woff2', '300 900' ) ) ),
		'pinar-fd' => array( 'group' => 'persian', 'label' => 'Pinar FD', 'family' => 'Radman CT Pinar FD', 'files' => array( array( 'persian/Pinar/Pinar-VF-FD[DSTY,KSHD,wght].woff2', '300 900' ) ) ),
		'dana' => array( 'group' => 'persian', 'label' => 'Dana', 'family' => 'Radman CT Dana', 'files' => array( array( 'persian/Dana/DanaFaNum-Light.woff2', '300' ), array( 'persian/Dana/DanaFaNum-Regular.woff2', '400' ), array( 'persian/Dana/DanaFaNum-Medium.woff2', '500' ), array( 'persian/Dana/DanaFaNum-Bold.woff2', '700' ), array( 'persian/Dana/DanaFaNum-ExtraBold.woff2', '800' ) ) ),
		'aria' => array( 'group' => 'persian', 'label' => 'Aria', 'family' => 'Radman CT Aria', 'files' => array( array( 'persian/Aria/AriaWebFaNum-Regular.woff2', '400' ), array( 'persian/Aria/AriaWebFaNum-Medium.woff2', '500' ), array( 'persian/Aria/AriaWebFaNum-Bold.woff2', '700' ), array( 'persian/Aria/AriaWebFaNum-ExtraBold.woff2', '800' ), array( 'persian/Aria/AriaWebFaNum-Heavy.woff2', '900' ) ) ),
		'aria-normal' => array( 'group' => 'persian', 'label' => 'Aria Normal', 'family' => 'Radman CT Aria Normal', 'files' => array( array( 'persian/Aria/AriaWebFaNum-Normal.woff2', '400' ) ) ),
		'hafez' => array( 'group' => 'persian', 'label' => 'Hafez FD', 'family' => 'Radman CT Hafez FD', 'files' => array( array( 'persian/Hafez/Hafez-FD-Regular.woff2', '400' ) ) ),
		'gt-walsheim' => array( 'group' => 'latin', 'label' => 'GT Walsheim', 'family' => 'Radman CT GT Walsheim', 'files' => array( array( 'english/Walsheim/GT-Walsheim-Regular.woff2', '400' ), array( 'english/Walsheim/GT-Walsheim-Medium.woff2', '500' ), array( 'english/Walsheim/GT-Walsheim-Bold.woff2', '700' ) ) ),
		'manrope' => array( 'group' => 'latin', 'label' => 'Manrope', 'family' => 'Radman CT Manrope', 'files' => array( array( 'english/Manrope/Manrope.woff2', '200 800' ) ) ),
	);
}

function valid( $value, string $group ): bool {
	$fonts = registry();
	return is_string( $value ) && isset( $fonts[ $value ] ) && $group === $fonts[ $value ]['group'];
}

function choices( string $group ): array {
	$result = array();
	foreach ( registry() as $key => $font ) {
		if ( $group === $font['group'] ) {
			$result[] = array( $key, $font['label'] );
		}
	}
	return $result;
}

function weights( string $key ): array {
	$font = registry()[ $key ] ?? null;
	if ( ! $font ) { return array(); }
	$result = array();
	foreach ( $font['files'] as $file ) {
		$range = array_map( 'intval', explode( ' ', $file[1] ) );
		foreach ( count( $range ) > 1 ? range( $range[0], $range[1], 100 ) : $range as $weight ) { $result[] = (string) $weight; }
	}
	return array_values( array_unique( $result ) );
}

function role_font( array $settings, string $role, string $group ): string {
	if ( 'body' === $role ) { return $settings[ $group . '_font' ]; }
	return $settings[ $role . '_' . $group . '_font' ] ?: role_font( $settings, parent_role( $role ), $group );
}
