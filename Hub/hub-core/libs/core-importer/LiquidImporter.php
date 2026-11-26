<?php

/**
 * Class LiquidImporter
 *
 * This class provides the capability to import demo content as well as import widgets and WordPress menus
 *
 * Some method of this class are written by Franklin M Gitonga
 *
 * @since 1.0.0
 *
 */
#[AllowDynamicProperties]
class LiquidImporter {

	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 2.2.0
	 *
	 * @var object
	 */
	public $theme_options_file;
	public $booking_hub_settings_file;

	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 2.2.0
	 *
	 * @var object
	 */
	public $widgets;

	/**
	 * Set the key to be used to store theme options
	 *
	 * @since 0.0.2
	 *
	 * @var object
	 */
	public $theme_option_name; //set theme options name here
	public $theme_options_file_name = 'theme-options.json';
	public $widgets_file_name     =  'widgets.json';
	public $content_demo_file_name  =  'content.xml';
	public $media_file_name = 'media.xml';
	public $customizer_data_name = 'customizer_data.dat';
	public $demo_settings_name = 'demo_settings.json';
	public $booking_hub_settings_name = 'booking_hub_settings.json';

	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 2.2.0
	 *
	 * @var object
	 */
	public $content_demo;

	/**
	 * Flag imported to prevent duplicates
	 *
	 * @since 2.2.0
	 *
	 * @var object
	 */
	public $flag_as_imported = array();
	public $demo_files_path;
	public $customizer_data;
	public $demo_settings;

	/**
	 * Holds a copy of the widget settings
	 *
	 * @since 0.0.2
	 *
	 * @var object
	 */
	public $widget_import_results;

    /**
     * Holds a copy of the object for easy reference.
     *
     * @since 2.2.0
     *
     * @var object
     */
    private static $instance;

    /**
     * Constructor. Hooks all interactions to initialize the class.
     *
     * @since 2.2.0
     */
    public function __construct($core) {
        if(isset($_POST['demo']) && !empty($_POST['demo'])) {
          $demo = esc_attr($_POST['demo']);
        } else {
          $demo = $this->selected_demo_folder;
        }
        self::$instance = $this;
        $this->core = $core;
        $this->demo_files_path = $this->demo_files_path . $demo.DIRECTORY_SEPARATOR;
        $this->theme_options_file = $this->demo_files_path . $this->theme_options_file_name;
		$this->booking_hub_settings_file = $this->demo_files_path . $this->booking_hub_settings_name;
        $this->widgets = $this->demo_files_path . $this->widgets_file_name;
        $this->content_demo = $this->demo_files_path . $this->content_demo_file_name;
        $this->customizer_data = $this->demo_files_path . $this->customizer_data_name;
        $this->log = $core['LiquidLog'];
    }

    /**
     * add_widget_to_sidebar Import sidebars
     * @param  string $sidebar_slug    Sidebar slug to add widget
     * @param  string $widget_slug     Widget slug
     * @param  string $count_mod       position in sidebar
     * @param  array  $widget_settings widget settings
     *
     * @since 2.2.0
     *
     * @return null
     */
    public function add_widget_to_sidebar($sidebar_slug, $widget_slug, $count_mod, $widget_settings = array()) {

        $sidebars_widgets = get_option('sidebars_widgets');

        if(!isset($sidebars_widgets[$sidebar_slug]))
           $sidebars_widgets[$sidebar_slug] = array('_multiwidget' => 1);

        $newWidget = get_option('widget_'.$widget_slug);

        if(!is_array($newWidget))
            $newWidget = array();

        $count = count($newWidget)+1+$count_mod;
        $sidebars_widgets[$sidebar_slug][] = $widget_slug.'-'.$count;

        $newWidget[$count] = $widget_settings;

        update_option('sidebars_widgets', $sidebars_widgets);
        update_option('widget_'.$widget_slug, $newWidget);

    }


    public function set_demo_data( $file = '', $media = true ) {

      //@todo alret message if ini_set is disabled ( mostly on shared hostings )
      if( function_exists('set_time_limit') ) {
        @set_time_limit(300);
      } else {
        @ini_set( 'max_execution_time', 300 );
      }


      if(empty($file)) {
        $file = $this->content_demo;
      }
	    if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);

        require_once ABSPATH . 'wp-admin/includes/import.php';

        $importer_error = false;

        if ( !class_exists( 'WP_Importer' ) ) {

            $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

            if ( file_exists( $class_wp_importer ) ){

                require_once($class_wp_importer);

            } else {

                $importer_error = true;
            }
        }

        if ( !class_exists( 'WP_Import_Hub', false ) && !class_exists( 'WP_Import', false ) ) {

            $class_wp_import = dirname( __FILE__ ) .'/wordpress-importer.php';

            if ( file_exists( $class_wp_import ) )
                require_once($class_wp_import);
            else
                $importer_error = true;
        }

        if($importer_error){

            die("Error on import");

        } else {
            if(!is_file( $file )) {
                echo "The XML file containing the dummy content is not available or could not be read .. You might want to try to set the file permission to chmod 755.<br/>If this doesn't work please install the official WordPress Importer plugin (Tools &gt; Import &gt; WordPress) and import the XML file (should be located in your download .zip: Sample Content folder) manually ";
            } else {
               // Prefer the Hub subclass (Liquid mega-menu meta support).
               // Fall back to upstream WP_Import when the official
               // wordpress-importer plugin already provided it.
               if ( class_exists( 'WP_Import_Hub', false ) ) {
                   $wp_import = new WP_Import_Hub();
               } else {
                   $wp_import = new WP_Import();
               }
               $wp_import->fetch_attachments = $media;
               // Rewrite old-site URLs to this site in post content/excerpt.
               // Supported by the importer on WordPress 6.7+.
               $rewrite_urls = function_exists( 'get_bloginfo' ) && version_compare( get_bloginfo( 'version' ), '6.7', '>=' );
               $wp_import->import( $file, array( 'rewrite_urls' => $rewrite_urls ) );
               echo ('Contents Imported Successfully. ');

			   $args = array(
					'post_type' => 'elementor_library',
					'name' => 'liquid-kit',
					'meta_query'  => array(
						array(
							'key' => '_elementor_template_type',
							'value' => 'kit',
						),
					),
				);

				$query = get_posts( $args );

				if ( ! empty( $query ) && isset( $query[0] ) && isset( $query[0]->ID ) ) {
					update_option( 'elementor_active_kit', $query[0]->ID );
				}

				// set form data for booking hub
				$this->set_booking_hub_form_data();
         	}
    	}

    }

    public function set_demo_theme_options( $file = '' ) {
      if(empty($file)) {
        $file = $this->theme_options_file;
      }
    	// File exists?
  		if ( ! file_exists( $file ) ) {
        	$this->log->putContent('Faild to import theme options', true, true);
  			wp_die(
  				 esc_html__( 'Theme options Import file could not be found. ', 'landinghub-core' ),
  				'',
  				array( 'back_link' => true )
  			);
  		}

  		// Get file contents and decode
  		$data = file_get_contents( $file );
		$data = trim($data, '###');
  		$data = json_decode( $data, true );
		//var_dump($data);
  		// Have valid data?
  		// If no data or could not decode

  		if ( empty( $data ) || ! is_array( $data ) ) {
        	$this->log->putContent('Faild to import theme options', true, true);
  			wp_die(
  				esc_html__( 'Theme options import data could not be read. Please try a different file.', 'landinghub-core' ),
  				'',
  				array( 'back_link' => true )
  			);
  		} else {
			echo('');
			echo('Theme Options Imported successfully');
			$data = apply_filters( 'radium_theme_import_theme_options', $data );
			update_option($this->theme_option_name, $data);
			$this->log->putContent('Theme options imported', true, true);
		}

  		// Hook before import
		$this->set_booking_hub_options();
    }

	public function set_booking_hub_options() {
		$files = [
			'booking_hub' => $this->booking_hub_settings_file,
			// Add more files here if needed
		];

		foreach( $files as $option => $file ) {
			if ( file_exists( $file ) ) {
				// Get file contents and decode
				$data = file_get_contents( $file );
				$data = trim($data, '###');
				$data = json_decode( $data, true );
				if ( !empty( $data ) || is_array( $data ) ) {
					update_option($option, $data);
					$this->log->putContent( $option . ' imported', true, true );
				}
			}
		}
	}

	public function set_booking_hub_form_data() {
		$bhub_booking_forms = get_posts( array(
			'post_type'      => 'bhub_booking_form',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		) );

		if ( ! empty( $bhub_booking_forms ) ) {
			$imported_services = taxonomy_exists( 'bhub_service' ) ? get_terms( array(
				'taxonomy'   => 'bhub_service',
				'hide_empty' => false,
				'fields'     => 'ids',
			) ) : array();

			$imported_locations = taxonomy_exists( 'bhub_location' ) ? get_terms( array(
				'taxonomy'   => 'bhub_location',
				'hide_empty' => false,
				'fields'     => 'ids',
			) ) : array();

			$imported_users = get_users( array(
				'role__in' => array( 'administrator', 'bhub_staff_member' ),
				'fields'   => 'ID',
			) );

			if ( is_wp_error( $imported_services ) ) {
				$imported_services = array();
			}
			if ( is_wp_error( $imported_locations ) ) {
				$imported_locations = array();
			}

			if ( empty( $imported_users ) ) {
				$fallback_user_id = get_current_user_id();
				if ( empty( $fallback_user_id ) ) {
					$admin_users = get_users( array(
						'role'   => 'administrator',
						'number' => 1,
						'fields' => 'ID',
					) );
					$fallback_user_id = ! empty( $admin_users ) ? (int) $admin_users[0] : 0;
				}
				$imported_users = ! empty( $fallback_user_id ) ? array( (int) $fallback_user_id ) : array();
			}

			$imported_services  = array_map( 'intval', (array) $imported_services );
			$imported_locations = array_map( 'intval', (array) $imported_locations );
			$imported_users     = array_map( 'intval', (array) $imported_users );

			if ( ! empty( $imported_services ) && ! empty ( $imported_users ) ) {
				// Build the price array with service IDs as keys and empty strings as values
				$price_sub_array = array();
				foreach ( $imported_services as $service_id ) {
					$price_sub_array[ $service_id ] = '';
				}

				// Build the services array with nested structures containing 'id'
				$services_sub_array = array();
				foreach ( $imported_services as $service_id ) {
					$services_sub_array[] = array( 'id' => $service_id );
				}

				foreach ( $imported_users as $imported_user_id ) {
					// Wrap them in an extra outer array to match your exact nested structure
					update_user_meta( $imported_user_id, 'bhub_staff_services', array( $services_sub_array ) );
					update_user_meta( $imported_user_id, 'bhub_staff_service_prices', array( $price_sub_array ) );
				}
			}

			foreach ( $bhub_booking_forms as $bhub_booking_form ) {
				$bhub_wizard = get_post_meta( $bhub_booking_form->ID, 'bhub_wizard', true );
				if ( ! is_array( $bhub_wizard ) ) {
					$bhub_wizard = array();
				}
				$mode = $bhub_wizard['mode'] ?? 'time';
				$bhub_wizard['serviceIds'] = $imported_services;
				$bhub_wizard['locationIds'] = $imported_locations;
				$bhub_wizard['staffIds'] = $imported_users;

				// Fix rent mode to set pickup and drop-off locations and default service
				if ( $mode == 'rent' ) {
					$bhub_wizard['settings']['pickupLocations'] = $imported_locations;
					$bhub_wizard['settings']['dropoffLocations'] = $imported_locations;
					if ( class_exists( '\Booking_Hub\Providers\Core\Mode' ) ) {
						$imported_services = [
							\Booking_Hub\Providers\Core\Mode::get_default_service_id( 'rent' )
						];
						$bhub_wizard['serviceIds'] = $imported_services;

					}
					update_post_meta( $bhub_booking_form->ID, 'bhub_rental_pickup_locations', $imported_locations );
					update_post_meta( $bhub_booking_form->ID, 'bhub_rental_drop_off_locations', $imported_locations );
				}

				// Fix hotel mode default services to exclude rent and taxi services if they exist
				if ( $mode == 'hotel' ) {
					if ( class_exists( '\Booking_Hub\Providers\Core\Mode' ) ) {
						$exlude_default_services = [
							\Booking_Hub\Providers\Core\Mode::get_default_service_id( 'rent' ),
							\Booking_Hub\Providers\Core\Mode::get_default_service_id( 'taxi' ),
						];
						$imported_services = array_diff( $imported_services, $exlude_default_services );
						$bhub_wizard['serviceIds'] = $imported_services;

					}
				}

				wp_set_post_terms( $bhub_booking_form->ID, $imported_services, 'bhub_service' );
				wp_set_post_terms( $bhub_booking_form->ID, $imported_locations, 'bhub_location' );
				update_post_meta( $bhub_booking_form->ID, 'bhub_staff_members', $imported_users );
				update_post_meta( $bhub_booking_form->ID, 'bhub_wizard', $bhub_wizard );

				// Generate CSS file based on style settings
				if ( class_exists( '\Booking_Hub\Providers\CSS_Generator' ) ) {
					$style = get_post_meta( $bhub_booking_form->ID, 'bhub_style', true );
					( new \Booking_Hub\Providers\CSS_Generator( $bhub_booking_form->ID, $style ) )->generate();
				}

				// Trigger the action to update the mode for the BookingHub plugin
				do_action( 'booking_hub/mod/change', $mode, $mode );
			}
		}
	}

    /**
     * Available widgets
     *
     * Gather site's widgets into array with ID base, name, etc.
     * Used by export and import functions.
     *
     * @since 2.2.0
     *
     * @global array $wp_registered_widget_updates
     * @return array Widget information
     */
    function available_widgets() {
    	global $wp_registered_widget_controls;
    	$widget_controls = $wp_registered_widget_controls;
    	$available_widgets = array();
    	foreach ( $widget_controls as $widget ) {
    		if ( ! empty( $widget['id_base'] ) && ! isset( $available_widgets[$widget['id_base']] ) ) { // no dupes

    			$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
    			$available_widgets[$widget['id_base']]['name'] = $widget['name'];

    		}

    	}

    	return apply_filters( 'radium_theme_import_widget_available_widgets', $available_widgets );

    }


    /**
     * Process import file
     *
     * This parses a file and triggers importation of its widgets.
     *
     * @since 2.2.0
     *
     * @param string $file Path to .wie file uploaded
     * @global string $widget_import_results
     */
    function process_widget_import_file( $file = '') {

      if(empty($file)) {
        $file = $this->widgets;
      }

    	// File exists?
    	if ( ! file_exists( $file ) ) {
    		wp_die(
    			 esc_html__( 'Widget Import file could not be found. ', 'landinghub-core' ),
    			'',
    			array( 'back_link' => true )
    		);
    	}

    	// Get file contents and decode
    	$data = file_get_contents( $file );
    	$data = json_decode( $data );

    	// Delete import file
    	//unlink( $file );

    	// Import the widget data
    	// Make results available for display on import/export page
    	$this->widget_import_results = $this->import_widgets( $data );

    }

    /**
     * Import widget JSON data
     *
     * @since 2.2.0
     * @global array $wp_registered_sidebars
     * @param object $data JSON widget data from .wie file
     * @return array Results array
     */
    public function import_widgets( $data ) {

    	global $wp_registered_sidebars;

    	// Have valid data?
    	// If no data or could not decode
    	if ( empty( $data ) || ! is_object( $data ) ) {
    		wp_die(
    			esc_html__( 'Widget import data could not be read. Please try a different file.', 'landinghub-core' ),
    			'',
    			array( 'back_link' => true )
    		);
    	}

    	// Hook before import
    	$data = apply_filters( 'radium_theme_import_widget_data', $data );

    	// Get all available widgets site supports
    	$available_widgets = $this->available_widgets();

    	// Get all existing widget instances
    	$widget_instances = array();
    	foreach ( $available_widgets as $widget_data ) {
    		$widget_instances[$widget_data['id_base']] = get_option( 'widget_' . $widget_data['id_base'] );
    	}

    	// Begin results
    	$results = array();

    	// Loop import data's sidebars
    	foreach ( $data as $sidebar_id => $widgets ) {

    		// Skip inactive widgets
    		// (should not be in export file)
    		if ( 'wp_inactive_widgets' == $sidebar_id ) {
    			continue;
    		}

    		// Check if sidebar is available on this site
    		// Otherwise add widgets to inactive, and say so
    		if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
    			$sidebar_available = true;
    			$use_sidebar_id = $sidebar_id;
    			$sidebar_message_type = 'success';
    			$sidebar_message = '';
    		} else {
    			$sidebar_available = false;
    			$use_sidebar_id = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme
    			$sidebar_message_type = 'error';
    			$sidebar_message = __( 'Sidebar does not exist in theme (using Inactive)', 'landinghub-core' );
    		}

    		// Result for sidebar
    		$results[$sidebar_id]['name'] = ! empty( $wp_registered_sidebars[$sidebar_id]['name'] ) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id; // sidebar name if theme supports it; otherwise ID
    		$results[$sidebar_id]['message_type'] = $sidebar_message_type;
    		$results[$sidebar_id]['message'] = $sidebar_message;
    		$results[$sidebar_id]['widgets'] = array();

    		// Loop widgets
    		foreach ( $widgets as $widget_instance_id => $widget ) {

    			$fail = false;

    			// Get id_base (remove -# from end) and instance ID number
    			$id_base = preg_replace( '/-[0-9]+$/', '', $widget_instance_id );
    			$instance_id_number = str_replace( $id_base . '-', '', $widget_instance_id );

    			// Does site support this widget?
    			if ( ! $fail && ! isset( $available_widgets[$id_base] ) ) {
    				$fail = true;
    				$widget_message_type = 'error';
    				$widget_message = __( 'Site does not support widget', 'landinghub-core' ); // explain why widget not imported
            $this->log->putContent('Site does not support widget', true, true);
    			}

    			// Filter to modify settings before import
    			// Do before identical check because changes may make it identical to end result (such as URL replacements)
    			$widget = apply_filters( 'radium_theme_import_widget_settings', $widget );

    			// Does widget with identical settings already exist in same sidebar?
    			if ( ! $fail && isset( $widget_instances[$id_base] ) ) {

    				// Get existing widgets in this sidebar
    				$sidebars_widgets = get_option( 'sidebars_widgets' );
    				$sidebar_widgets = isset( $sidebars_widgets[$use_sidebar_id] ) ? $sidebars_widgets[$use_sidebar_id] : array(); // check Inactive if that's where will go

    				// Loop widgets with ID base
    				$single_widget_instances = ! empty( $widget_instances[$id_base] ) ? $widget_instances[$id_base] : array();
    				foreach ( $single_widget_instances as $check_id => $check_widget ) {

    					// Is widget in same sidebar and has identical settings?
    					if ( in_array( "$id_base-$check_id", $sidebar_widgets ) && (array) $widget == $check_widget ) {

    						$fail = true;
    						$widget_message_type = 'warning';
    						$widget_message = __( 'Widget already exists', 'landinghub-core' ); // explain why widget not imported
                $this->log->putContent('Widget already exists', true, true);

    						break;

    					}

    				}

    			}

    			// No failure
    			if ( ! $fail ) {

    				// Add widget instance
    				$single_widget_instances = get_option( 'widget_' . $id_base ); // all instances for that widget ID base, get fresh every time
    				$single_widget_instances = ! empty( $single_widget_instances ) ? $single_widget_instances : array( '_multiwidget' => 1 ); // start fresh if have to
    				$single_widget_instances[] = (array) $widget; // add it

    					// Get the key it was given
    					end( $single_widget_instances );
    					$new_instance_id_number = key( $single_widget_instances );

    					// If key is 0, make it 1
    					// When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it)
    					if ( '0' === strval( $new_instance_id_number ) ) {
    						$new_instance_id_number = 1;
    						$single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
    						unset( $single_widget_instances[0] );
    					}

    					// Move _multiwidget to end of array for uniformity
    					if ( isset( $single_widget_instances['_multiwidget'] ) ) {
    						$multiwidget = $single_widget_instances['_multiwidget'];
    						unset( $single_widget_instances['_multiwidget'] );
    						$single_widget_instances['_multiwidget'] = $multiwidget;
    					}

    					// Update option with new widget
    					update_option( 'widget_' . $id_base, $single_widget_instances );

    				// Assign widget instance to sidebar
    				$sidebars_widgets = get_option( 'sidebars_widgets' ); // which sidebars have which widgets, get fresh every time
    				$new_instance_id = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance
    				$sidebars_widgets[$use_sidebar_id][] = $new_instance_id; // add new instance to sidebar
    				update_option( 'sidebars_widgets', $sidebars_widgets ); // save the amended data

    				// Success message
    				if ( $sidebar_available ) {
    					$widget_message_type = 'success';
    					$widget_message = esc_html__( 'Widgets Imported', 'landinghub-core' );
						$this->log->putContent('Widgets Imported', true, true);
    				} else {
    					$widget_message_type = 'warning';
    					$widget_message = esc_html__( 'Imported to Inactive', 'landinghub-core' );
    				}

    			}

    			// Result for widget instance
    			$results[$sidebar_id]['widgets'][$widget_instance_id]['name'] = isset( $available_widgets[$id_base]['name'] ) ? $available_widgets[$id_base]['name'] : $id_base; // widget name or ID if name not available (not supported by site)
    			$results[$sidebar_id]['widgets'][$widget_instance_id]['title'] = $widget->title ? $widget->title : esc_html__( 'No Title', 'landinghub-core' ); // show "No Title" if widget instance is untitled
    			$results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
    			$results[$sidebar_id]['widgets'][$widget_instance_id]['message'] = $widget_message;

    		}

    	}
      echo $widget_message;
    	// Hook after import
    	do_action( 'radium_theme_import_widget_after_import' );

    	// Return results
    	return apply_filters( 'radium_theme_import_widget_results', $results );

    }

}

?>