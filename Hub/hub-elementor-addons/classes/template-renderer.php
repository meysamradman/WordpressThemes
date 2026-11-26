<?php
namespace LiquidElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Template_Renderer
 *
 * Renders Elementor library templates selected inside our widgets.
 *
 * Elementor's own `get_builder_content()` has no cycle protection at all, and
 * `get_builder_content_for_display()` only compares the template ID against
 * `get_the_ID()` - which is the page being viewed, never the template our
 * widgets render. So a template that contains a widget pointing back at itself
 * (A -> A, or A -> B -> A) recurses until PHP exhausts its memory limit.
 *
 * This class keeps a stack of the templates currently being rendered and
 * refuses to re-enter one that is already on it.
 *
 * @since 6.0.4
 */
class Template_Renderer {

	/**
	 * Template IDs currently being rendered, innermost last.
	 *
	 * @var array
	 */
	private static $stack = [];

	/**
	 * Render an Elementor library template.
	 *
	 * Returns an empty string when nothing is selected, when the template no
	 * longer exists, or when rendering it would re-enter a template that is
	 * already being rendered further up the stack.
	 *
	 * @since 6.0.4
	 *
	 * @param mixed $template_id The selected template ID.
	 * @param bool  $with_css    Whether to output the template CSS.
	 * @param bool  $for_display Whether to use Elementor's `_for_display` variant,
	 *                           which suppresses edit mode while rendering.
	 *
	 * @return string Rendered template markup.
	 */
	public static function render( $template_id, $with_css = true, $for_display = false ) {

		$template_id = absint( $template_id );

		if ( ! $template_id || ! get_post( $template_id ) ) {
			return '';
		}

		if ( in_array( $template_id, self::$stack, true ) ) {
			return self::recursion_notice();
		}

		self::$stack[] = $template_id;

		try {
			$frontend = \Elementor\Plugin::instance()->frontend;

			return $for_display
				? $frontend->get_builder_content_for_display( $template_id, $with_css )
				: $frontend->get_builder_content( $template_id, $with_css );
		} finally {
			array_pop( self::$stack );
		}

	}

	/**
	 * Message shown in place of a template that would recurse.
	 *
	 * Silent on the frontend, visible in the editor so the loop can be found
	 * and fixed.
	 *
	 * @since 6.0.4
	 *
	 * @return string
	 */
	private static function recursion_notice() {

		if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return '';
		}

		return '<div class="elementor-alert elementor-alert-danger">' .
			esc_html__( 'This template is already being displayed inside itself. Please choose a different one to avoid an endless loop.', 'hub-elementor-addons' ) .
			'</div>';

	}

	/**
	 * Get the selectable templates for a template control.
	 *
	 * Same list as liquid_helper()->get_elementor_templates(), minus the document
	 * currently being edited - picking that one guarantees a loop.
	 *
	 * @since 6.0.4
	 *
	 * @return array Template options, keyed by post ID.
	 */
	public static function get_template_options() {

		$options = liquid_helper()->get_elementor_templates();
		$current_id = self::get_current_document_id();

		if ( $current_id && isset( $options[ $current_id ] ) ) {
			unset( $options[ $current_id ] );
		}

		return $options;

	}

	/**
	 * Get the ID of the document currently being edited.
	 *
	 * @since 6.0.4
	 *
	 * @return int Post ID, or 0 when it cannot be resolved.
	 */
	private static function get_current_document_id() {

		if ( class_exists( '\Elementor\Plugin' ) ) {

			$documents = \Elementor\Plugin::$instance->documents;

			if ( $documents ) {
				$document = $documents->get_current();
				if ( $document ) {
					return (int) $document->get_main_id();
				}
			}

			if ( \Elementor\Plugin::$instance->editor ) {
				$post_id = (int) \Elementor\Plugin::$instance->editor->get_post_id();
				if ( $post_id ) {
					return $post_id;
				}
			}
		}

		if ( isset( $_REQUEST['post'] ) ) {
			return (int) $_REQUEST['post'];
		}

		return 0;

	}

}
