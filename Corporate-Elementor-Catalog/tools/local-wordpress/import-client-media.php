<?php
if ( PHP_SAPI !== 'cli' || '1' !== getenv( 'RADMAN_LOCAL_SITE' ) ) { exit( 1 ); }
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
$admin = get_user_by( 'login', getenv( 'RADMAN_ADMIN_USER' ) );
if ( ! $admin || ! user_can( $admin, 'upload_files' ) ) { throw new RuntimeException( 'Local media administrator not available.' ); }
wp_set_current_user( $admin->ID );

$root = getenv( 'RADMAN_CLIENT_MEDIA_DIR' );
if ( ! is_string( $root ) || '' === $root ) { throw new RuntimeException( 'Set RADMAN_CLIENT_MEDIA_DIR to the development media source directory.' ); }
if ( ! is_dir( $root ) ) { throw new RuntimeException( 'Client image source directory is missing.' ); }
$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
$imported = array();
foreach ( $files as $file ) {
    if ( ! $file->isFile() || ! in_array( strtolower( $file->getExtension() ), array( 'jpg', 'jpeg', 'png', 'webp', 'avif', 'gif' ), true ) ) { continue; }
    $relative = substr( $file->getPathname(), strlen( $root ) + 1 );
    $existing = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'fields' => 'ids', 'posts_per_page' => 1, 'meta_key' => '_rdct_client_source', 'meta_value' => $relative ) );
    $id = (int) ( $existing[0] ?? 0 );
    if ( ! $id || ! wp_attachment_is_image( $id ) || ! is_file( get_attached_file( $id ) ) ) {
        $temporary = wp_tempnam( $file->getFilename() );
        if ( ! $temporary || ! copy( $file->getPathname(), $temporary ) ) { throw new RuntimeException( 'Cannot prepare local image import.' ); }
        $id = media_handle_sideload( array( 'name' => $file->getFilename(), 'tmp_name' => $temporary ), 0, $file->getBasename( '.' . $file->getExtension() ) );
        if ( is_wp_error( $id ) ) {
            if ( is_file( $temporary ) ) { unlink( $temporary ); }
            throw new RuntimeException( $id->get_error_message() );
        }
        update_post_meta( $id, '_rdct_client_source', $relative );
    }
    $imported[ $relative ] = $id;
}

echo wp_json_encode( array( 'media' => $imported ), JSON_PRETTY_PRINT ) . "\n";
