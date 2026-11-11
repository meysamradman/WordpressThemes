<?php
if ( PHP_SAPI !== 'cli' || ! function_exists( 'imagewebp' ) || count( $argv ) !== 3 ) { fwrite( STDERR, "PHP CLI with GD and source/destination arguments is required.\n" ); exit( 1 ); }
$info = getimagesize( $argv[1] );
if ( ! $info || $info[0] > 8192 || $info[1] > 8192 || abs( $info[0] / $info[1] - 1.6 ) > .01 || filesize( $argv[1] ) > 20971520 ) { fwrite( STDERR, "Use a screenshot with an 8:5 aspect ratio, at most 8192px and 20MB.\n" ); exit( 1 ); }
$loaders = array( IMAGETYPE_PNG => 'imagecreatefrompng', IMAGETYPE_JPEG => 'imagecreatefromjpeg', IMAGETYPE_WEBP => 'imagecreatefromwebp' );
if ( ! isset( $loaders[ $info[2] ] ) ) { exit( 1 ); }
$image = $loaders[ $info[2] ]( $argv[1] ); $output = imagecreatetruecolor( 960, 600 );
imagealphablending( $output, false ); imagesavealpha( $output, true );
imagecopyresampled( $output, $image, 0, 0, 0, 0, 960, 600, $info[0], $info[1] );
if ( ! imagewebp( $output, $argv[2], 84 ) ) { exit( 1 ); }
imagedestroy( $image ); imagedestroy( $output );
