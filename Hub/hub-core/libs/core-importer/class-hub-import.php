<?php
/**
 * Hub demo importer subclass.
 *
 * Extends the vendored upstream WordPress Importer (0.9.6, see
 * vendor/wordpress-importer/) with the Hub-specific menu item metadata
 * (Liquid mega-menu options) that upstream does not know about.
 *
 * Everything else (posts, terms, attachments, parsers, URL rewriting) is
 * inherited untouched so future upstream updates only require replacing the
 * contents of vendor/wordpress-importer/.
 *
 * @package Hub_Core
 * @subpackage Importer
 */

if ( ! class_exists( 'WP_Import_Hub', false ) ) {

	class WP_Import_Hub extends WP_Import {

		/**
		 * Liquid menu item meta keys persisted after import.
		 *
		 * @var string[]
		 */
		protected $liquid_menu_meta_keys = array(
			'_menu_item_liquid_megaprofile',
			'_menu_item_liquid_submenu_color',
			'_menu_item_liquid_icon',
			'_menu_item_liquid_icon_position',
			'_menu_item_liquid_badge',
			'_menu_item_liquid_badge_style',
		);

		/**
		 * Create a menu item via the upstream importer, then persist the
		 * Liquid mega-menu meta that upstream does not know about.
		 *
		 * When the item is skipped (draft, missing/invalid menu slug) or
		 * deferred (associated object not imported yet, retried later via
		 * backfill_parents()), the parent records nothing in
		 * $processed_menu_items and there is no meta to persist.
		 *
		 * @param array $item Menu item details from WXR file.
		 */
		public function process_menu_item( $item ) {
			parent::process_menu_item( $item );

			if ( ! isset( $this->processed_menu_items[ intval( $item['post_id'] ) ] ) ) {
				return;
			}

			if ( empty( $item['postmeta'] ) || ! is_array( $item['postmeta'] ) ) {
				return;
			}

			$id = $this->processed_menu_items[ intval( $item['post_id'] ) ];

			foreach ( $item['postmeta'] as $meta ) {
				if ( ! isset( $meta['key'], $meta['value'] ) ) {
					continue;
				}
				if ( ! in_array( $meta['key'], $this->liquid_menu_meta_keys, true ) ) {
					continue;
				}
				update_post_meta( $id, $meta['key'], $meta['value'] );
			}
		}
	}
}
