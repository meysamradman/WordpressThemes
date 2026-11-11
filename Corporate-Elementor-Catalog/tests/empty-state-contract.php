<?php

define( 'ABSPATH', '/' );
$searches = 0;
function esc_html_e( $text, $domain ) { echo htmlspecialchars( $text, ENT_QUOTES ); }
function get_search_form() { $GLOBALS['searches']++; echo '<form>Site search</form>'; }
function empty_state( array $args ): string {
	ob_start(); require dirname( __DIR__ ) . '/radmanct/template-parts/content/none.php'; return ob_get_clean();
}
foreach ( array( 'blog' => 'No posts match these filters.', 'catalog' => 'No products match these filters.', 'portfolio' => 'No projects found.' ) as $scope => $message ) {
	$html = empty_state( array( $scope => true ) );
	if ( ! str_contains( $html, $message ) || str_contains( $html, '<form>' ) ) { throw new RuntimeException( 'Scoped empty state leaked a second site search: ' . $scope ); }
}
if ( ! str_contains( empty_state( array() ), '<form>' ) || 1 !== $searches ) { throw new RuntimeException( 'General site search fallback was removed.' ); }
echo "Passed: scoped blog/catalog/portfolio empty states and independent general search fallback.\n";
