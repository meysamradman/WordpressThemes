<?php

namespace RadmanCT\Admin\BuilderPage;

defined( 'ABSPATH' ) || exit;
require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

class Layout_List_Table extends \WP_List_Table {
	private string $kind;
	private array $counts = array( 'all' => 0, 'enabled' => 0, 'disabled' => 0 );
	private bool $translations;
	private array $page_usage = array();

	public function __construct( string $kind ) {
		$this->kind = $kind;
		$this->translations = defined( 'ICL_SITEPRESS_VERSION' );
		parent::__construct( array( 'singular' => 'layout', 'plural' => 'layouts', 'ajax' => false ) );
	}

	public function get_columns() {
		$columns = array( 'title' => __( 'Title' ), 'status' => __( 'Status', 'radmanct' ), 'assignments' => __( 'Used for', 'radmanct' ) );
		if ( $this->translations ) { $columns['translations'] = __( 'Layout translations', 'radmanct' ); }
		return $columns;
	}

	protected function get_sortable_columns() { return array( 'title' => array( 'title', false ) ); }

	public function prepare_items() {
		global $wpdb;
		$usage = $wpdb->get_results( $wpdb->prepare(
			"SELECT pm.meta_value AS layout_id, COUNT(DISTINCT p.ID) AS page_count FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_key = %s AND p.post_type = 'page' AND p.post_status NOT IN ('trash', 'auto-draft') GROUP BY pm.meta_value",
			'_radman_' . $this->kind . '_layout'
		), ARRAY_A );
		$this->page_usage = array_column( (array) $usage, 'page_count', 'layout_id' );
		$items = \RadmanCT\Builder\Storage\listing( $this->kind );
		$status = library_parameter( 'status' );
		$search = library_parameter( 's' );
		foreach ( $items as &$item ) {
			$item['enabled'] = \RadmanCT\Builder\read( $this->kind, $item['id'] )['layout']['enabled'];
			++$this->counts['all'];
			++$this->counts[ $item['enabled'] ? 'enabled' : 'disabled' ];
		}
		unset( $item );
		$items = array_values( array_filter( $items, static fn( $item ) => ( '' === $search || false !== stripos( $item['title'], $search ) ) && ( ! in_array( $status, array( 'enabled', 'disabled' ), true ) || $item['enabled'] === ( 'enabled' === $status ) ) ) );
		if ( 'title' === library_parameter( 'orderby' ) ) {
			$direction = 'desc' === library_parameter( 'order' ) ? -1 : 1;
			usort( $items, static fn( $a, $b ) => $direction * strnatcasecmp( $a['title'], $b['title'] ) );
		}
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns(), 'title' );
		$this->set_pagination_args( array( 'total_items' => count( $items ), 'per_page' => 20 ) );
		$this->items = array_slice( $items, ( $this->get_pagenum() - 1 ) * 20, 20 );
	}

	protected function get_views() {
		$views = array();
		$selected = library_parameter( 'status' );
		foreach ( array( 'all' => __( 'All' ), 'enabled' => __( 'Enabled', 'radmanct' ), 'disabled' => __( 'Disabled', 'radmanct' ) ) as $state => $label ) {
			$url = add_query_arg( array( 'page' => 'rdct-' . $this->kind, 'status' => 'all' === $state ? false : $state ), admin_url( 'admin.php' ) );
			$current = $state === $selected || ( 'all' === $state && ! in_array( $selected, array( 'enabled', 'disabled' ), true ) );
			$views[ $state ] = '<a href="' . esc_url( $url ) . '"' . ( $current ? ' class="current" aria-current="page"' : '' ) . '>' . esc_html( $label ) . ' <span class="count">(' . (int) $this->counts[ $state ] . ')</span></a>';
		}
		return $views;
	}

	public function column_title( $item ) {
		$url = add_query_arg( array( 'page' => 'rdct-' . $this->kind, 'layout' => $item['id'] ), admin_url( 'admin.php' ) );
		$link = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Edit' ) . '</a>';
		$actions = array( 'edit' => $link );
		if ( $this->counts['all'] < 13 ) {
			$copy = add_query_arg( array( 'page' => 'rdct-' . $this->kind, 'new' => 1, 'copy' => $item['id'] ), admin_url( 'admin.php' ) );
			$actions['duplicate'] = '<a href="' . esc_url( $copy ) . '">' . esc_html__( 'Duplicate', 'radmanct' ) . '</a>';
		}
		$rename = add_query_arg( 'rename', 1, $url );
		$actions['rename'] = '<a href="' . esc_url( $rename ) . '">' . esc_html__( 'Rename layout', 'radmanct' ) . '</a>';
		$actions['delete'] = '<button type="button" class="button-link button-link-delete submitdelete" data-rdct-delete="' . esc_attr( $item['id'] ) . '">' . esc_html__( 'Delete Permanently' ) . '</button>';
		return '<strong><a class="row-title" href="' . esc_url( $url ) . '">' . esc_html( $item['title'] ) . '</a></strong>' . $this->row_actions( $actions );
	}

	public function column_status( $item ) { return esc_html( $item['enabled'] ? __( 'Enabled', 'radmanct' ) : __( 'Disabled', 'radmanct' ) ); }

	public function column_assignments( $item ) {
		$defaults = \RadmanCT\Admin\BuilderApi\defaults()[ $this->kind ] ?? array();
		$languages = array( 'all' => __( 'All languages', 'radmanct' ) );
		if ( $this->translations ) { $languages += \RadmanCT\Integrations\Wpml\Builder\languages(); }
		$assignments = array();
		foreach ( $defaults as $language => $id ) { if ( $id === $item['id'] ) { $assignments[] = $languages[ $language ] ?? $language; } }
		if ( empty( $defaults['all'] ) && 'default' === $item['id'] ) { $assignments[] = $languages['all']; }
		if ( ! empty( $this->page_usage[ $item['id'] ] ) ) { $assignments[] = sprintf( __( 'Pages: %d', 'radmanct' ), (int) $this->page_usage[ $item['id'] ] ); }
		return $assignments ? esc_html( implode( ', ', array_unique( $assignments ) ) ) : '&mdash;';
	}

	public function column_translations( $item ) {
		$state = \RadmanCT\Integrations\Wpml\Builder\translation_state( $this->kind, $item['id'] );
		$links = array();
		foreach ( $state['items'] ?? array() as $language ) {
			$url = add_query_arg( array( 'page' => 'rdct-' . $this->kind, 'layout' => $language['id'] ?: $item['id'], 'translations' => 1 ), admin_url( 'admin.php' ) );
			$links[] = '<a href="' . esc_url( $url ) . '" lang="' . esc_attr( $language['code'] ) . '">' . esc_html( ( $language['id'] ? '' : '+ ' ) . $language['title'] ) . '</a>';
		}
		return $links ? implode( ' | ', $links ) : '&mdash;';
	}

	public function no_items() { esc_html_e( 'No layouts found.', 'radmanct' ); }
}
