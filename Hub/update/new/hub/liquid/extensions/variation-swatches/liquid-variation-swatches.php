<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * Class Liquid_Variation_Swatches
 * Handles dynamic variation swatches for WooCommerce.
 */
class Liquid_Variation_Swatches extends Liquid_Base {

    /**
     * Define custom attribute types and their configurations.
     * @var array
     */
    protected $custom_attribute_types = [
        'color' => [
            'label' => 'Hub Color',
            'meta_key' => 'hub_color',
            'render_type' => 'color_picker',
        ],
        'button' => [
            'label' => 'Hub Button',
            'meta_key' => 'hub_label',
            'render_type' => 'none',
        ],
		'image' => [
			'label' => 'Hub Image',
			'meta_key' => 'hub_image',
			'render_type' => 'image',
		],
    ];

	protected string $color_style = '';
	protected string $tooltip_image_meta_key = 'hub_tooltip_image';
	protected bool $tooltip = false;

    public function __construct() {
        $theme_option = get_option('liquid_one_opt');

        if (
            isset( $theme_option['wc-variation-swatches'] ) &&
            $theme_option['wc-variation-swatches'] === 'on' &&
            class_exists( 'WooCommerce' )
        ) {
            $this->init();
			$this->color_style = $theme_option['wc-vs-color-style'] ?? 'circle';
			$this->tooltip = boolval( $theme_option['wc-vs-tooltip'] ?? '0' );
        }
    }

    public function init() {
        // Add custom attribute types to the WooCommerce Attribute UI selector
        add_filter( 'product_attributes_type_selector', [ $this, 'add_attribute_type' ] );

        // Dynamically register hooks for ALL existing custom-typed attributes.
        $attribute_taxonomies = wc_get_attribute_taxonomies();

        if ( $attribute_taxonomies ) {
            foreach ( $attribute_taxonomies as $tax ) {
                if ( isset( $this->custom_attribute_types[ $tax->attribute_type ] ) ) {
                    $taxonomy_name = wc_attribute_taxonomy_name( $tax->attribute_name );

                    // Admin: Render custom fields on "Edit Term" and "Add New Term" screens
                    add_action( $taxonomy_name . '_edit_form_fields', [ $this, 'render_attribute_term_fields' ], 10, 2 );
                    // The '_add_form_fields' hook only passes 1 argument (the taxonomy slug).
                    add_action( $taxonomy_name . '_add_form_fields', [ $this, 'render_attribute_term_fields' ], 10, 1 );

                    // Admin: Save custom fields
                    add_action( 'edited_' . $taxonomy_name, [ $this, 'save_attribute_term_fields' ], 10 );
                    add_action( 'created_' . $taxonomy_name, [ $this, 'save_attribute_term_fields' ], 10 );
                }
            }
        }

        // Admin: Enqueue color picker script dynamically
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );

        // Enqueue color picker script dynamically
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_scripts' ] );

        // Front-end: Display swatches instead of dropdowns
        add_filter( 'woocommerce_dropdown_variation_attribute_options_html', [ $this, 'swatches_frontend_display' ], 20, 2 );

        // Front-end: Display Variations on shop/archive page
        add_action( 'woocommerce_after_shop_loop_item_title', [ $this, 'show_variations_shop_page' ], 99 );

		// Display variation terms on product page
		add_action( 'woocommerce_product_option_terms', [ $this, 'product_option_terms' ], 25, 3 );

		// Body Class for Styles
		add_filter( 'body_class', [ $this, 'body_class' ] );
    }

	// Body Class for Styles
	public function body_class( $classes = [] ) {
		if ( is_product() && liquid_helper()->get_theme_option( 'wc-vs-product-layout' ) == 'inline' ) {
			$classes[] = 'lqd-woo-vs-layout-inline';
		}

		return $classes;
	}

    /**
     * Adds custom attribute types to the WooCommerce attributes screen.
     *
     * @param array $types Existing types.
     * @return array Updated types.
     */
    public function add_attribute_type( $types ) {
        foreach ( $this->custom_attribute_types as $type => $config ) {
            $types[ $type ] = $config['label'];
        }
        return $types;
    }

    /**
     * Helper to get the attribute configuration based on the taxonomy name (e.g., 'pa_color').
     *
     * @param string $taxonomy WooCommerce attribute taxonomy slug (e.g., 'pa_color').
     * @return array|false Attribute configuration array or false if not found.
     */
    private function get_attribute_config_by_taxonomy( $taxonomy ) {
        global $wpdb;
        $attribute_name = substr( $taxonomy, 3 );

        $attribute_type = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT attribute_type FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s",
                $attribute_name
            )
        );

        if ( isset( $this->custom_attribute_types[ $attribute_type ] ) ) {
            return $this->custom_attribute_types[ $attribute_type ];
        }

        return false;
    }

    /**
     * Renders the custom field for the attribute term settings screen.
     *
     * @param WP_Term|string $term_or_taxonomy The term object (edit screen) or taxonomy slug (add screen).
     * @param string $taxonomy The taxonomy slug (only present on edit screen).
     */
    public function render_attribute_term_fields( $term_or_taxonomy, $taxonomy = '' ) {

        if ( is_object( $term_or_taxonomy ) ) {
            $term = $term_or_taxonomy;
            $taxonomy_slug = $taxonomy;
        } else {
            $term = null;
            $taxonomy_slug = $term_or_taxonomy;
        }

        $config = $this->get_attribute_config_by_taxonomy( $taxonomy_slug );

        // Only proceed if a color picker is required for this attribute type
        if ( ! $config ) {
            return;
        }

		$meta_key = $config['meta_key'];
		$term_id  = is_object( $term ) ? $term->term_id : 0;
		$value    = $term_id ? get_term_meta( $term_id, $meta_key, true ) : '';

		// Tooltip Field
		$tooltip_image = $term_id ? get_term_meta( $term_id, $this->tooltip_image_meta_key, true ) : '';

		if ( is_object( $term ) ) {
			?>
				<tr class="form-field term-tooltip-image-wrap">
					<th>
						<label><?php esc_html_e( 'Tooltip Image', 'hub' ); ?></label>
					</th>
					<td>
						<input type="hidden"
							name="<?php echo esc_attr( $this->tooltip_image_meta_key ); ?>"
							value="<?php echo esc_attr( $tooltip_image ); ?>"
							class="hub-tooltip-image-id" />

						<button class="button hub-upload-tooltip-image">
							<?php esc_html_e( 'Upload Tooltip Image', 'hub' ); ?>
						</button>

						<button class="button hub-upload-tooltip-image-delete">
							<?php esc_html_e( 'Delete', 'hub' ); ?>
						</button>

						<div class="hub-tooltip-image-preview" style="margin-top:10px;">
							<?php echo $tooltip_image ? wp_get_attachment_image( $tooltip_image, 'thumbnail' ) : ''; ?>
						</div>

						<p class="description">
							<?php esc_html_e( 'Optional image shown inside the swatch tooltip.', 'hub' ); ?>
						</p>
					</td>
				</tr>
			<?php
		} else {
			?>
				<div class="form-field term-tooltip-image-wrap">
					<label><?php esc_html_e( 'Tooltip Image', 'hub' ); ?></label>

					<input type="hidden"
						name="<?php echo esc_attr( $this->tooltip_image_meta_key ); ?>"
						class="hub-tooltip-image-id" />

					<button class="button hub-upload-tooltip-image">
						<?php esc_html_e( 'Upload Tooltip Image', 'hub' ); ?>
					</button>

					<div class="hub-tooltip-image-preview" style="margin-top:10px;"></div>
				</div>
			<?php
		}

		// Image Field
		if ( $config['render_type'] === 'image' ) {
			?>
			<?php if ( is_object( $term ) ) : ?>
				<tr class="form-field term-image-wrap">
					<th>
						<label><?php esc_html_e( 'Swatch Image', 'hub' ); ?></label>
					</th>
					<td>
						<input type="hidden" name="<?php echo esc_attr( $meta_key ); ?>" value="<?php echo esc_attr( $value ); ?>" class="hub-image-id" />
						<button class="button hub-upload-image">
							<?php esc_html_e( 'Upload Image', 'hub' ); ?>
						</button>
						<button class="button hub-upload-image-delete">
							<?php esc_html_e( 'Delete', 'hub' ); ?>
						</button>
						<div class="hub-image-preview" style="margin-top: 12px;">
							<?php echo $value ? wp_get_attachment_image( $value, 'thumbnail' ) : ''; ?>
						</div>
					</td>
				</tr>
			<?php else : ?>
				<div class="form-field term-image-wrap">
					<label><?php esc_html_e( 'Swatch Image', 'hub' ); ?></label>
					<input type="hidden" name="<?php echo esc_attr( $meta_key ); ?>" class="hub-image-id" />
					<button class="button hub-upload-image">
						<?php esc_html_e( 'Upload Image', 'hub' ); ?>
					</button>
					<div class="hub-image-preview" style="margin-top: 12px;"></div>
				</div>
			<?php endif; ?>
			<?php
			return;
		// Color Picker Field
		} elseif ( $config['render_type'] === 'color_picker' ) {
			if ( is_object( $term ) ) { // Edit screen - uses table rows
				?>
				<tr class="form-field term-<?php echo esc_attr( $config['render_type'] ); ?>-wrap">
					<th><label for="term-<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Color Value', 'hub' ); ?></label></th>
					<td>
						<input type="text" id="term-<?php echo esc_attr( $meta_key ); ?>" name="<?php echo esc_attr( $meta_key ); ?>" value="<?php echo esc_attr( $value ); ?>" class="hub-color-picker" />
						<p class="description"><?php esc_html_e( 'Select the color for this swatch.', 'hub' ); ?></p>
					</td>
				</tr>
				<?php
			} else { // Add new screen - uses div wrappers
				?>
				<div class="form-field term-<?php echo esc_attr( $config['render_type'] ); ?>-wrap">
					<label for="term-<?php echo esc_attr( $meta_key ); ?>"><?php esc_html_e( 'Color Value', 'hub' ); ?></label>
					<input type="text" id="term-<?php echo esc_attr( $meta_key ); ?>" name="<?php echo esc_attr( $meta_key ); ?>" value="<?php echo esc_attr( $value ); ?>" class="hub-color-picker" />
					<p class="description"><?php esc_html_e( 'Select the color for this swatch.', 'hub' ); ?></p>
				</div>
				<?php
			}
		}
	}

    /**
     * Saves the custom field data for the attribute term.
     *
     * @param int $term_id The ID of the term being saved/created.
     */
    public function save_attribute_term_fields( $term_id ) {
        // Determine the full taxonomy name from the hook name.
        $full_taxonomy = str_replace( ['edited_', 'created_'], '', current_filter() );

        // Look up the configuration using the full taxonomy name.
        $config = $this->get_attribute_config_by_taxonomy( $full_taxonomy );

        if ( ! $config ) {
            return;
        }

        // Find the attribute type slug to use for specific sanitation.
        $attribute_type_slug = array_search( $config, $this->custom_attribute_types );

        $meta_key = $config['meta_key'];

        // Only save if the field name is present in POST
        if ( isset( $_POST[ $meta_key ] ) ) {
            $value = sanitize_text_field( wp_unslash( $_POST[ $meta_key ] ) );
            if ( $attribute_type_slug === 'color' ) {
                $value = sanitize_hex_color( $value );
            } elseif ( $attribute_type_slug === 'image' ) {
				$value = absint( $value );
			}
            update_term_meta( $term_id, $meta_key, $value );
        }

		if ( isset( $_POST[ $this->tooltip_image_meta_key ] ) ) {
			$value = sanitize_text_field( wp_unslash( $_POST[ $this->tooltip_image_meta_key ] ) );
			update_term_meta(
				$term_id,
				$this->tooltip_image_meta_key,
				absint( $value )
			);
		}

    }

    /**
     * Enqueues the color picker script and initializes it for the custom field.
     */
    public function enqueue_admin_scripts() {
        $screen = get_current_screen();
        // Only load on attribute term editing screens
        if( false === strpos( $screen->id, 'edit-pa_' ) ) {
            return;
        }

        // Only load if we have a color attribute type active in the configuration
        if ( array_key_exists( 'color', $this->custom_attribute_types ) ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
            wp_add_inline_script(
                'wp-color-picker',
                'jQuery(function ($) {
                    $(".hub-color-picker").each(function() {
                        $(this).wpColorPicker();
                    });
                });'
            );
        }

        // Only load if we have a image attribute type active in the configuration
        if ( array_key_exists( 'image', $this->custom_attribute_types ) ) {
            wp_enqueue_media();
			wp_add_inline_script(
				'jquery',
				'jQuery(function($){
					let frame;
					$(".hub-upload-image").on("click", function(e){
						e.preventDefault();
						const button = $(this);
						const input  = button.parent().find(".hub-image-id");
						const preview = button.parent().find(".hub-image-preview");

						if (frame) {
							frame.open();
							return;
						}

						frame = wp.media({
							title: "Select Image",
							button: { text: "Use image" },
							multiple: false
						});

						frame.on("select", function(){
							const attachment = frame.state().get("selection").first().toJSON();
							input.val(attachment.id);
							preview.html(`<img style="width:150px;height:150px;" src="${attachment.url}" />`);
						});

						frame.open();
					});
					$(".hub-upload-image-delete").on("click", function(e){
						e.preventDefault();
						const button = $(this);
						const input  = button.parent().find(".hub-image-id");
						const preview = button.parent().find(".hub-image-preview");

						input.val("");
						preview.empty();
					});
					$(".hub-upload-tooltip-image").on("click", function(e){
						e.preventDefault();

						const button  = $(this);
						const input   = button.parent().find(".hub-tooltip-image-id");
						const preview = button.parent().find(".hub-tooltip-image-preview");

						const frame = wp.media({
							title: "Select Tooltip Image",
							button: { text: "Use image" },
							multiple: false
						});

						frame.on("select", function(){
							const attachment = frame.state().get("selection").first().toJSON();
							input.val(attachment.id);
							preview.html(`<img style="width:120px;height:120px;" src="${attachment.url}" />`);
						});

						frame.open();
					});

					$(".hub-upload-tooltip-image-delete").on("click", function(e){
						e.preventDefault();
						const wrap = $(this).parent();
						wrap.find(".hub-tooltip-image-id").val("");
						wrap.find(".hub-tooltip-image-preview").empty();
					});
				});'
			);
        }
    }

    /**
     * Enqueues the color picker script and initializes it for the custom field.
     */
    public function enqueue_frontend_scripts() {
		wp_enqueue_style( 'liquid_product_variants', get_template_directory_uri() . '/liquid/extensions/variation-swatches/liquid_product_variants.css', [], null );
        wp_enqueue_script( 'liquid_product_variants', get_template_directory_uri() . '/liquid/extensions/variation-swatches/liquid_product_variants.js', array( 'jquery' ), null, true );
    }

    /**
     * Replaces the default WooCommerce dropdown with custom swatches on the frontend.
     *
     * @param string $html The default dropdown HTML.
     * @param array $args Arguments for the dropdown.
     * @return string Modified HTML with swatches or original HTML.
     */
    public function swatches_frontend_display( $html, $args ) {
        $taxonomy = $args[ 'attribute' ];
        $product = $args[ 'product' ];
        $attribute_name = substr( $taxonomy, 3 );

        $config = $this->get_attribute_config_by_taxonomy( $taxonomy );

        // If it is not a registered custom swatch type, return the default dropdown HTML
        if ( ! $config ) {
            return $html;
        }

        // Hide the original dropdown and prepare to display swatches
        $html = '<div style="display:none">' . $html . '</div>';
        $swatches_html = '';

        // Get all terms associated with this product's attribute
        $terms = wc_get_product_terms( $product->get_id(), $taxonomy );

        // Loop through terms and render the appropriate swatch
        foreach( $terms as $term ) {
            // Check if the term is valid for the current product variation options
            if( in_array( $term->slug, $args[ 'options' ] ) ) {
                $selected = $args[ 'selected' ] === $term->slug ? ' lqd-selected' : ''; // Do NOT change this class
                $swatch_style = '';
                $swatch_classes = 'lqd-wc-swatch'; // Do NOT change this base class
                $swatch_content = '';
                $title = esc_attr( $term->name );

                $attribute_type_slug = array_search( $config, $this->custom_attribute_types );

                if ( $attribute_type_slug === 'color' ) {
                    $meta_key = $this->custom_attribute_types['color']['meta_key'];
                    $hex_color = get_term_meta( $term->term_id, $meta_key, true );

                    $swatch_style = sprintf( 'style="--lqd-wc-swatch-color:%s;"', esc_attr( $hex_color ), esc_attr( $hex_color ) );
                    $swatch_classes .= ' lqd-wc-swatch-color';
                    $swatch_classes .= ' ' . $this->color_style;
                    $swatch_content = '';
                } elseif ( $attribute_type_slug === 'button' ) {
                    $swatch_classes .= ' lqd-wc-swatch-button';
                    $swatch_content = esc_html( $term->name );
				} elseif ( $attribute_type_slug === 'image' ) {
					$meta_key = $this->custom_attribute_types['image']['meta_key'];
					$image_id = get_term_meta( $term->term_id, $meta_key, true );
					$image    = $image_id ? wp_get_attachment_image( $image_id, 'thumbnail' ) : '';

					if ( ! $image ) {
						continue;
					}

					$swatch_classes .= ' lqd-wc-swatch-image';
					$swatch_content = $image;
                } else {
                    continue; // Skip if we don't know how to render it
                }

				if ( $this->tooltip ) {
					$tooltip_image_id = get_term_meta(
						$term->term_id,
						$this->tooltip_image_meta_key,
						true
					);

					$tooltip_content = '';

					if ( $tooltip_image_id ) {
						$tooltip_content .= wp_get_attachment_image(
							$tooltip_image_id,
							'thumbnail'
						);
					}

					$tooltip_content .= '<span>' . esc_html( $term->name ) . '</span>';

					$swatch_content .= sprintf(
						'<span class="lqd-wc-swatch-tooltip">%s</span>',
						$tooltip_content
					);
				}

                $swatches_html .= sprintf(
                    '<span class="%s%s" %s title="%s" data-value="%s">%s</span>',
                    esc_attr( $swatch_classes ),
                    esc_attr( $selected ),
                    $swatch_style,
                    $title,
                    esc_attr( $term->slug ),
                    $swatch_content
                );
            }
        }

        return $html . $swatches_html;
    }

    // Variant shop/archive display
   	public function show_variations_shop_page() {
		global $product;

		if ( ! $product || ! $product->is_type( 'variable' ) ) {
			return;
		}

		if ( ! $variations = $product->get_available_variations() ) {
            return;
        }

        if ( ! function_exists( 'liquid_helper' ) || ! in_array( 'archive', liquid_helper()->get_option( 'wc-vs-display' ) ) ) {
            return;
        }

		$summary_parts = [];
        ?>
        <div class="variations_form lqd_variations_form" data-product_variations="<?php echo wc_esc_json( wp_json_encode( $variations ) ) ?>">
            <?php
                foreach ( $product->get_attributes() as $attribute_name => $attribute ) {
					if (
						! empty( liquid_helper()->get_theme_option('wc-vs-specific-attributes') ) &&
						! in_array( $attribute_name, liquid_helper()->get_theme_option('wc-vs-specific-attributes') )
					) { continue; }
                    ?>
                        <div class="liquid-variation-select value">
                            <?php
                                wc_dropdown_variation_attribute_options(
                                    array(
                                        'show_option_none' => 'Select ' . strtolower( wc_attribute_label( $attribute_name ) ),
                                        'options' => $attribute->get_slugs(),
                                        'attribute' => $attribute_name,
                                        'product' => $product
                                    )
                                );
                            ?>
                        </div>
                    <?php
					$count = count( $attribute->get_options() );
					$label = strtolower( wc_attribute_label( $attribute_name ) );
					if ( $count > 1 && substr( $label, -1 ) !== 's' ) {
						$label .= 's';
					}

					$summary_parts[] = sprintf( '%d %s', $count, $label );
				}

				if ( ! empty( $summary_parts ) ) {
					?>
						<div class="liquid-variation-summary">
							<?php echo esc_html( implode( ', ', $summary_parts ) ); ?>
						</div>
					<?php
				}
				?>
        </div>
        <?php
    }

	public function	product_option_terms( $attribute_taxonomy, $i, $attribute ) {
		if ( ! in_array( $attribute_taxonomy->attribute_type, [ 'color', 'image', 'button' ] ) ) {
			return;
		}

		$options = $attribute->get_options();
		$options = ! empty( $options ) ? $options : array();

		?>
		<select multiple="multiple" data-placeholder="Select color" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo $i ?>][]">
			<?php
				$colors = get_terms( 'pa_' . $attribute_taxonomy->attribute_name, array( 'hide_empty' => 0 ) );
				if( $colors ) {
					foreach ( $colors as $color ) {
						$selected = wc_selected( $color->term_id, $options );
						echo "<option value=\"{$color->term_id}\"{$selected}>{$color->name}</option>";
					}
				}
			?>
		</select>
		<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'hub' ); ?></button>
		<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'hub' ); ?></button>
		<?php
	}

	public static function get_wc_attributes_for_redux() {
		if ( ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
			return array();
		}

		$options    = array();
		$attributes = wc_get_attribute_taxonomies();

		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $attribute ) {
				$taxonomy = wc_attribute_taxonomy_name( $attribute->attribute_name );

				// Key => Value
				$options[ $taxonomy ] = $attribute->attribute_label;
			}
		}

		return $options;
	}
}

// Instantiate the class
new Liquid_Variation_Swatches();