<?php
namespace Elementor {
	class Widget_Base {
		public array $test_settings = array();
		public function __construct( $data = array(), $args = null ) { $this->test_settings = $data['settings'] ?? array(); }
		public function get_id() { return 'atomic-test'; }
		public function get_initial_config() { return array(); }
	}
}
namespace Elementor\Modules\AtomicWidgets {
	class Module { public static bool $active = true; public static function is_active() { return self::$active; } }
}
namespace {
require_once __DIR__ . '/fixtures/components-url.php';
define( 'ABSPATH', '/usr/src/wordpress/' );
function __( $text, $domain ) { return $text; }
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function apply_filters( $hook, $value, ...$args ) { return $value; }
function esc_attr( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $text ) { return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' ); }
function sanitize_text_field( $text ) { return strip_tags( $text ); }
function get_intermediate_image_sizes() { return array( 'thumbnail', 'medium', 'medium_large', 'large' ); }
function wp_get_additional_image_sizes() { return array(); }
function post_type_exists( $type ) { return false; }
function get_posts( $args ) { return array(); }
function get_option( $key ) { return 800; }
function expect( $condition, $message ) { if ( ! $condition ) { throw new \RuntimeException( $message ); } }
function rdct_icon( $name, $options ) { echo \RadmanCTComponents\Icons\render( $name, $options ); }
function rdct_text_reveal( $text, $options ) { echo \RadmanCTComponents\Components\TextReveal\render( $text, $options ); }
spl_autoload_register( static function ( $class ) {
	$prefix = 'Elementor\\';
	if ( ! str_starts_with( $class, $prefix ) ) { return; }
	$parts = explode( '\\', substr( $class, strlen( $prefix ) ) );
	$parts = array_map( static fn( $part ) => strtolower( str_replace( '_', '-', preg_replace( '/([a-z0-9])([A-Z])/', '$1-$2', $part ) ) ), $parts );
	$file = dirname( __DIR__ ) . '/themes/elementor/' . implode( '/', $parts ) . '.php';
	if ( is_file( $file ) ) { require $file; }
} );
require dirname( __DIR__ ) . '/radmanct-components/inc/icons.php';
require dirname( __DIR__ ) . '/themes/elementor/includes/utils.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/components/text-reveal.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/widgets.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components/widget.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components/curtain.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components/timeline.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components/accordion.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components/sticky-sections.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components/box.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components/flipbox.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components/icon-box.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components/button.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components/image.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components/table.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components/carousel.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components/particles.php';
require dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components/testimonial.php';

$review = new \RadmanCTComponents\Elementor\AtomicComponents\Testimonial();
$review_schema = $review::get_props_schema();
expect( $review_schema['source_icon']->validate( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'brand-github' ) ), 'Review source icon does not use the local native enum.' );
$review_responsive = \RadmanCTComponents\Elementor\AtomicComponents\ResponsiveValues::generate( array( 'desktop' => \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::generate( 27.5 ), 'mobile' => \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::generate( 0 ) ) );
expect( $review_schema['testimonial_quote_size_responsive']->validate( $review_responsive ), 'Native review typography lost fractional values or zero.' );
expect( array() === $review->get_script_depends(), 'Static review enqueued a frontend engine.' );
$review_sections = ( new ReflectionMethod( $review, 'define_atomic_controls' ) )->invoke( $review );
expect( 2 === count( $review_sections ), 'Review content and appearance are not separated.' );
$review_controls = array();
foreach ( $review_sections as $section ) { foreach ( $section->get_items() as $control ) { $review_controls[ $control->get_bind() ] = $control; } }
expect( isset( $review_controls['testimonial_quote_size_responsive'], $review_controls['testimonial_name_weight_responsive'] ), 'Standalone review responsive controls are absent.' );
$review_carousel = new \RadmanCTComponents\Elementor\AtomicComponents\Testimonials();
$review_carousel_sections = ( new ReflectionMethod( $review_carousel, 'define_atomic_controls' ) )->invoke( $review_carousel );
$review_carousel_controls = array(); foreach ( $review_carousel_sections as $section ) { foreach ( $section->get_items() as $control ) { $review_carousel_controls[] = $control->get_bind(); } }
expect( in_array( 'testimonial_quote_size_responsive', $review_carousel_controls, true ) && in_array( 'testimonial_name_weight_responsive', $review_carousel_controls, true ), 'Carousel grouping dropped bound responsive typography controls.' );
foreach ( array( 'image_id', 'client_image_id' ) as $key ) { expect( $review_controls[ $key ] instanceof \Elementor\Modules\AtomicWidgets\Controls\Types\Image_Control && $review_schema[ $key ] instanceof \Elementor\Modules\AtomicWidgets\PropTypes\Image_Prop_Type, 'Review media lost the native image control/schema.' ); }
expect( $review_controls['link'] instanceof \Elementor\Modules\AtomicWidgets\Controls\Types\Link_Control && $review_schema['link'] instanceof \Elementor\Modules\AtomicWidgets\PropTypes\Link_Prop_Type, 'Review source lost its native link control/schema.' );

$particles = new \RadmanCTComponents\Elementor\AtomicComponents\Particles();
$particle_schema = $particles::get_props_schema();
$particle_sections = ( new ReflectionMethod( $particles, 'define_atomic_controls' ) )->invoke( $particles );
expect( count( $particle_sections ) === 4, 'Particles must expose all four native control groups.' );
expect( isset( $particle_schema['multi_color_values'], $particle_schema['image'], $particle_schema['shape_polygon'], $particle_schema['enable_anim_sync'] ), 'Particles lost native structured fields.' );
$carousel_schema = \RadmanCTComponents\Elementor\AtomicComponents\Carousel::get_props_schema();
foreach ( array( 'controller_id' => 'hero-images', 'controlled_ids' => 'hero-text,details' ) as $key => $value ) {
	expect( isset( $carousel_schema[ $key ] ) && $carousel_schema[ $key ]->validate( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( $value ) ), 'Native carousel synchronization field lost its string schema.' );
}
$sync_widget = new \RadmanCTComponents\Elementor\AtomicComponents\Carousel();
$sync_controls = ( new ReflectionMethod( $sync_widget, 'define_atomic_controls' ) )->invoke( $sync_widget );
$sync_sections = array_filter( $sync_controls, static fn( $section ) => 'rdct_carousel_synchronization' === $section->get_id() );
expect( 1 === count( $sync_sections ) && 3 === count( reset( $sync_sections )->get_items() ), 'Carousel synchronization controls are missing from the native section.' );
expect( $carousel_schema['sync_mode']->validate( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'progress' ) ) && ! $carousel_schema['sync_mode']->validate( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'invalid' ) ), 'Carousel synchronization method is not a bounded enum.' );
foreach ( array( \RadmanCTComponents\Elementor\AtomicComponents\Image::class, \RadmanCTComponents\Elementor\AtomicComponents\Box::class, \RadmanCTComponents\Elementor\AtomicComponents\Carousel::class ) as $image_widget ) {
	$loading_prop = $image_widget::get_props_schema()['image_loading'];
	foreach ( array( 'auto', 'high', 'lazy' ) as $policy ) { expect( $loading_prop->validate( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( $policy ) ), 'Native image loading policy failed schema validation.' ); }
	expect( ! $loading_prop->validate( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'invalid' ) ), 'Native image loading policy accepted an unknown value.' );
}
$height = \Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type::generate( array( 'size' => 70, 'unit' => 'vh' ) );
expect( $carousel_schema['height']->validate( $height ), 'Carousel height is not a native size prop.' );
$carousel = new \RadmanCTComponents\Elementor\AtomicComponents\Carousel();
$carousel_sections = ( new ReflectionMethod( $carousel, 'define_atomic_controls' ) )->invoke( $carousel );
$carousel_ids = array_map( static fn( $section ) => $section->get_id(), $carousel_sections );
foreach ( array( 'slides', 'layout', 'height', 'navigation', 'navigation_style', 'marquee', 'timing' ) as $group ) { expect( in_array( 'rdct_carousel_' . $group, $carousel_ids, true ), 'Carousel lost a required compact group: ' . $group ); }
expect( count( $carousel_ids ) === count( array_unique( $carousel_ids ) ), 'Carousel has duplicate control groups.' );
$table_schema = \RadmanCTComponents\Elementor\AtomicComponents\Table::get_props_schema();
$table_sample = \RadmanCTComponents\Components\Table\sample();
$table_typed = \RadmanCTComponents\Elementor\AtomicComponents\TableData::generate( \RadmanCTComponents\Elementor\AtomicComponents\Table::typed_data( $table_sample ) );
expect( $table_schema['data']->validate( $table_typed ), 'Native Atomic table schema rejected valid data.' );
$table_sample['columns'] = array_fill( 0, 13, 'Column' );
$table_invalid = \RadmanCTComponents\Elementor\AtomicComponents\TableData::generate( \RadmanCTComponents\Elementor\AtomicComponents\Table::typed_data( $table_sample ) );
expect( ! $table_schema['data']->validate( $table_invalid ), 'Native Atomic table schema accepted too many columns.' );
expect( \RadmanCTComponents\Elementor\Widgets\classic_in_panel(), 'Classic widgets must remain accessible with Atomic enabled.' );
\Elementor\Modules\AtomicWidgets\Module::$active = false;
expect( \RadmanCTComponents\Elementor\Widgets\classic_in_panel(), 'Classic widgets must return when Atomic is disabled.' );
\Elementor\Modules\AtomicWidgets\Module::$active = true;
 $classic_manager = new class {
	public array $widgets = array();
	public function register( $widget ): void { $this->widgets[ $widget->get_name() ] = $widget; }
 };
 \RadmanCTComponents\Elementor\Widgets\register( $classic_manager );
 foreach ( $classic_manager->widgets as $name => $widget ) {
	$legacy = in_array( $name, array( 'rdct-carousel', 'rdct-sticky-sections' ), true );
	expect( $legacy || ( $widget->show_in_panel() && ! $widget->hide_on_search() ), 'Classic widget hidden with Atomic enabled: ' . $name );
	expect( str_ends_with( $widget->get_title(), ' (V3)' ), 'Classic label does not identify V3: ' . $name );
 }
 expect( str_ends_with( ( new \RadmanCTComponents\Elementor\AtomicComponents\Image() )->get_title(), ' (Atomic)' ), 'Atomic label is ambiguous beside V3.' );
class AtomicIconTest extends \RadmanCTComponents\Elementor\AtomicComponents\Icon { public function get_atomic_settings(): array { return $this->test_settings; } public function output() { $this->render(); } }
class AtomicTextTest extends \RadmanCTComponents\Elementor\AtomicComponents\TextReveal { public function get_atomic_settings(): array { return $this->test_settings; } public function output() { $this->render(); } }
foreach ( array( new AtomicIconTest(), new AtomicTextTest(), new \RadmanCTComponents\Elementor\AtomicComponents\Testimonial(), new \RadmanCTComponents\Elementor\AtomicComponents\Curtain(), new \RadmanCTComponents\Elementor\AtomicComponents\StickySections(), new \RadmanCTComponents\Elementor\AtomicComponents\ServicePanels(), new \RadmanCTComponents\Elementor\AtomicComponents\ZoomCards(), new \RadmanCTComponents\Elementor\AtomicComponents\StickySplit(), new \RadmanCTComponents\Elementor\AtomicComponents\Box(), new \RadmanCTComponents\Elementor\AtomicComponents\FlipBox(), new \RadmanCTComponents\Elementor\AtomicComponents\IconBox(), new \RadmanCTComponents\Elementor\AtomicComponents\Image(), new \RadmanCTComponents\Elementor\AtomicComponents\Table(), new \RadmanCTComponents\Elementor\AtomicComponents\Carousel(), new \RadmanCTComponents\Elementor\AtomicComponents\Particles() ) as $widget ) {
	expect( $widget instanceof \Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Widget_Base, 'Widget is not native Atomic.' );
	$schema = $widget::get_props_schema();
	$config = $widget->get_initial_config();
	expect( true === $config['atomic'] && isset( $config['twig_main_template'], $config['twig_templates'], $config['atomic_props_schema'] ), 'Native editor config is incomplete.' );
	expect( isset( $config['twig_templates']['radmanct/attributes'] ), 'Native client config omitted the shared attribute boundary.' );
	expect( isset( $schema['classes'], $schema['attributes'], $schema['_cssid'] ), 'Native styles/attributes schema missing.' );
	$controls = ( new ReflectionMethod( $widget, 'define_atomic_controls' ) )->invoke( $widget );
	foreach ( $controls[0]->get_items() as $control ) { expect( isset( $schema[ $control->get_bind() ] ), 'Control is not bound to a native prop.' ); }
	$templates = ( new ReflectionMethod( $widget, 'get_templates' ) )->invoke( $widget );
	foreach ( $templates as $path ) { expect( is_file( $path ) && str_contains( file_get_contents( $path ), 'rdct_attributes.render(settings)' ), 'Safe native preview template missing.' ); }
}
$icon = new AtomicIconTest( array( 'settings' => array( 'name' => 'phone', 'size' => 999, 'stroke' => 2, 'label' => '<b>Phone</b>', 'mirror' => false, 'classes' => array( 'local-class' ) ) ) );
ob_start(); $icon->output(); $html = ob_get_clean();
expect( str_contains( $html, 'local-class' ) && str_contains( $html, 'width="128"' ) && str_contains( $html, 'aria-label="Phone"' ), 'Atomic icon did not use the safe shared renderer.' );
$text = new AtomicTextTest( array( 'settings' => array( 'text' => '<script>unsafe</script>', 'tag' => 'h2', 'mode' => 'hover', 'direction' => 'rtl', 'idle' => 'red;background:url(bad)', 'classes' => array() ) ) );
ob_start(); $text->output(); $html = ob_get_clean();
expect( str_contains( $html, '&lt;script&gt;' ) && ! str_contains( $html, '<script>' ) && ! str_contains( $html, 'url(bad)' ) && str_contains( $html, 'tabindex="0"' ) && str_contains( $html, 'dir="rtl"' ), 'Atomic text bypassed shared escaping, keyboard behavior or bounds.' );
expect( 'rdct-atomic-icon' === $icon->get_name() && 'rdct-atomic-text-reveal' === $text->get_name(), 'Native names conflict with legacy widgets.' );
spl_autoload_register( static function ( $class ) {
	$prefix = 'ElementorDeps\\Twig\\';
	if ( ! str_starts_with( $class, $prefix ) ) { return; }
	$file = dirname( __DIR__ ) . '/themes/elementor/vendor_prefixed/twig/twig/twig/src/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
	if ( is_file( $file ) ) { require $file; }
} );
$templates = array( 'elementor/macros' => file_get_contents( dirname( __DIR__ ) . '/themes/elementor/modules/atomic-widgets/elements/base/_macros.html.twig' ) );
$templates['radmanct/attributes'] = $config['twig_templates']['radmanct/attributes'];
foreach ( array( 'icon', 'text-reveal' ) as $name ) {
	$templates[ $name ] = file_get_contents( dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/atomic-components/' . $name . '.html.twig' );
}
$twig = new \ElementorDeps\Twig\Environment( new \ElementorDeps\Twig\Loader\ArrayLoader( $templates ) );
foreach ( array( 'icon', 'text-reveal' ) as $name ) {
	$settings = array_column( \RadmanCTComponents\Elementor\AtomicComponents\fields( $name ), 'default' );
	$settings = array_combine( array_keys( \RadmanCTComponents\Elementor\AtomicComponents\fields( $name ) ), $settings );
	$settings['text'] = '"><script>bad</script>'; $settings['label'] = $settings['text']; $settings['classes'] = array( 'local-class' );
	$markup = $twig->render( $name, array( 'settings' => $settings, 'interaction_id' => 'test' ) );
	expect( str_contains( $markup, 'icon' === $name ? 'data-rdct-atomic-icon' : 'data-rdct-atomic-text-reveal' ) && ! str_contains( $markup, '<script>' ), 'Real Twig template failed or unescaped text reached preview.' );
}
require dirname( __DIR__ ) . '/radmanct-components/inc/components/library.php';
foreach ( \RadmanCTComponents\Components\CarouselProfiles\definitions() as $profile => $definition ) {
	$class = '\\RadmanCTComponents\\Elementor\\AtomicComponents\\' . $definition['class'];
	$widget = new $class();
	expect( $widget->show_in_panel(), 'Purpose widget hidden from insertion.' );
	$sections = ( new ReflectionMethod( $widget, 'define_atomic_controls' ) )->invoke( $widget );
	$fields = \RadmanCTComponents\Components\CarouselProfiles\fields( $profile );
	foreach ( $sections as $section ) { foreach ( $section->get_items() as $control ) { expect( empty( $fields[ $control->get_bind() ]['internal'] ), 'Profile exposes an internal mode selector.' ); } }
	$schema = $class::get_props_schema();
	expect( $schema['items']->validate( \RadmanCTComponents\Elementor\AtomicComponents\CarouselItems::generate( $class::typed_items( \RadmanCTComponents\Components\CarouselProfiles\sample_items( $profile, 'en' ) ) ) ), 'Purpose repeater violates its native schema.' );
}
expect( ! ( new \RadmanCTComponents\Elementor\AtomicComponents\Carousel() )->show_in_panel(), 'Legacy generic carousel still appears beside purpose widgets.' );
foreach ( \RadmanCTComponents\Components\Library\catalog() as $id => $item ) {
	foreach ( array( 'fa', 'en' ) as $language ) {
		$model = \RadmanCTComponents\Components\Library\model( $id, $language, 'atomic' );
		expect( 'e-flexbox' === ( $model['elType'] ?? '' ) && '0.0' === ( $model['version'] ?? '' ), 'Atomic sample has a classic parent: ' . $id );
		$child = $model['elements'][0];
		$class = array( 'accordion' => \RadmanCTComponents\Elementor\AtomicComponents\Accordion::class, 'timeline' => \RadmanCTComponents\Elementor\AtomicComponents\Timeline::class, 'sticky-sections' => \RadmanCTComponents\Elementor\AtomicComponents\StickySections::class, 'testimonial' => \RadmanCTComponents\Elementor\AtomicComponents\Testimonial::class, 'particles' => \RadmanCTComponents\Elementor\AtomicComponents\Particles::class, 'carousel' => \RadmanCTComponents\Elementor\AtomicComponents\Carousel::class, 'table' => \RadmanCTComponents\Elementor\AtomicComponents\Table::class, 'image' => \RadmanCTComponents\Elementor\AtomicComponents\Image::class, 'icons' => \RadmanCTComponents\Elementor\AtomicComponents\Icon::class, 'text-reveal' => \RadmanCTComponents\Elementor\AtomicComponents\TextReveal::class, 'curtain' => \RadmanCTComponents\Elementor\AtomicComponents\Curtain::class, 'box' => \RadmanCTComponents\Elementor\AtomicComponents\Box::class, 'flipbox' => \RadmanCTComponents\Elementor\AtomicComponents\FlipBox::class, 'button' => \RadmanCTComponents\Elementor\AtomicComponents\Button::class, 'icon-box' => \RadmanCTComponents\Elementor\AtomicComponents\IconBox::class )[ $item['component'] ];
		if ( 'carousel' === $item['component'] && ! empty( $item['profile'] ) ) { $class = '\\RadmanCTComponents\\Elementor\\AtomicComponents\\' . \RadmanCTComponents\Components\CarouselProfiles\definitions()[ $item['profile'] ]['class']; }
		if ( 'sticky-sections' === $item['component'] ) { $class = '\\RadmanCTComponents\\Elementor\\AtomicComponents\\' . $item['atomic_class']; }
		expect( $class::get_element_type() === $child['widgetType'], 'Atomic sample uses a legacy widget.' );
		$schema = $class::get_props_schema();
		foreach ( $child['settings'] as $key => $prop ) { expect( $schema[ $key ]->validate( $prop ), 'Sample typed prop fails the actual widget schema: ' . $key ); }
		if ( 'icons' === $item['component'] ) { expect( true === $child['settings']['mirror']['value'] && 'boolean' === $child['settings']['mirror']['$$type'], 'Classic yes/no leaked into Atomic boolean.' ); }
		elseif ( 'text-reveal' === $item['component'] ) { expect( ( 'fa' === $language ? 'rtl' : 'ltr' ) === $child['settings']['direction']['value'], 'Typed sample language direction changed.' ); }
	}
}
\Elementor\Modules\AtomicWidgets\Module::$active = false;
expect( array() === \RadmanCTComponents\Components\Library\model( 'icon-phone', 'fa', 'atomic' ), 'Disabled Atomic returned an insertable sample.' );
expect( 'container' === \RadmanCTComponents\Components\Library\model( 'icon-phone', 'fa' )['elType'], 'Disabling Atomic broke classic samples.' );
function esc_html__( $text, $domain ) { return esc_html( $text ); }
function esc_attr__( $text, $domain ) { return esc_attr( $text ); }
function esc_url( $url ) { return esc_attr( $url ); }
function get_template_directory_uri() { return '/wp-content/themes/radmanct'; }
function admin_url( $path ) { return '/wp-admin/' . $path; }
function wp_create_nonce( $action ) { return 'local-test'; }
function add_query_arg( $args, $url ) { return $url . '?' . http_build_query( $args ); }
function is_rtl() { return true; }
function wp_json_encode( $value, $flags = 0 ) { return json_encode( $value, $flags ); }
require dirname( __DIR__ ) . '/radmanct-components/inc/admin/components-page.php';
\Elementor\Modules\AtomicWidgets\Module::$active = true;
ob_start(); \RadmanCTComponents\Admin\ComponentsPage\library( true ); $library = ob_get_clean();
expect( str_contains( $library, '<select data-library-mode>' ) && str_contains( $library, '<option value="atomic">' ), 'Atomic mode control missing from the editor.' );
expect( ! str_contains( $library, 'data-library-models>' ) && str_contains( $library, 'data-library-model-url=' ), 'Opening the library eagerly generated all renderer/language models.' );
$atomic_model = \RadmanCTComponents\Components\Library\model( 'icon-phone', 'fa', 'atomic' );
$classic_model = \RadmanCTComponents\Components\Library\model( 'icon-phone', 'fa', 'classic' );
expect( 'e-flexbox' === $atomic_model['elType'] && 'container' === $classic_model['elType'], 'Selected local model lost a renderer variant.' );
ob_start(); \RadmanCTComponents\Admin\ComponentsPage\library( false ); $gallery = ob_get_clean();
expect( ! str_contains( $gallery, '<select data-library-mode>' ) && ! str_contains( $gallery, 'data-library-models>' ), 'Atomic insertion payload leaked into the standalone gallery.' );
$word_schema = \RadmanCTComponents\Elementor\AtomicComponents\TextReveal::get_props_schema()['items'];
$word = \RadmanCTComponents\Elementor\AtomicComponents\TextWord::generate( array( 'title' => \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'English' ), 'color' => \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( '#172b3a' ) ) );
expect( $word_schema->validate( \RadmanCTComponents\Elementor\AtomicComponents\TextWords::generate( array( $word ) ) ) && ! $word_schema->validate( \RadmanCTComponents\Elementor\AtomicComponents\TextWords::generate( array_fill( 0, 31, $word ) ) ), 'Native word repeater shape or bounds failed.' );
expect( 1 === count( array_filter( \RadmanCTComponents\Components\Library\catalog(), static fn( $item ) => 'text-reveal' === $item['component'] ) ), 'Duplicate Text card remains.' );
expect( str_contains( $library, '<select data-library-mode><option value="atomic">' ), 'Atomic is not the default insertion type.' );
echo "Passed: real Atomic base/props/controls, native configuration/Twig, shared rendering and typed bilingual library samples.\n";
require_once dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/hover-border.php';
$border_schema = \RadmanCTComponents\Elementor\HoverBorder\schema( array() );
expect( $border_schema['rdct_border_enabled']->validate( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Boolean_Prop_Type::generate( true ) ), 'Border does not use native boolean props.' );
$border_controls = \RadmanCTComponents\Elementor\HoverBorder\controls( array(), null );
expect( count( $border_controls[0]->get_items() ) === 6, 'Native border controls are incomplete.' );
expect( isset( \RadmanCTComponents\Elementor\AtomicComponents\TextReveal::get_props_schema()['marquee_speed'] ) && isset( \RadmanCTComponents\Elementor\AtomicComponents\Carousel::get_props_schema()['marquee_speed'] ), 'Text and carousel do not share native marquee fields.' );
echo "Passed: native Atomic border and shared marquee schemas/controls.\n";
$highlight_schema = \RadmanCTComponents\Elementor\AtomicComponents\TextReveal::get_props_schema();
expect( isset( $highlight_schema['highlight_width_responsive'], $highlight_schema['highlight_inline_offset_responsive'], $highlight_schema['highlight_duration'] ), 'Native Text highlight schema is incomplete.' );
$text_widget = new \RadmanCTComponents\Elementor\AtomicComponents\TextReveal();
expect( array( 'radmanct-components' ) === $text_widget->get_categories(), 'Atomic Text is duplicated into another panel category.' );
$panel_config = \RadmanCTComponents\Integrations\Elementor\Panel\editor_settings( array( 'initial_document' => array( 'panel' => array( 'elements_categories' => array( 'favorites' => array( 'title' => 'Favorites' ), 'radmanct-components' => array( 'title' => 'Radman CT' ), 'basic' => array( 'active' => false ) ) ) ) ) );
expect( array( 'radmanct-components', 'favorites', 'basic' ) === array_keys( $panel_config['initial_document']['panel']['elements_categories'] ) && false === $panel_config['initial_document']['panel']['elements_categories']['basic']['active'], 'Initial native panel order or other category settings changed.' );
$text_sections = ( new ReflectionMethod( $text_widget, 'define_atomic_controls' ) )->invoke( $text_widget );
$highlight_sections = array_filter( $text_sections, static fn( $section ) => 'rdct_text_highlight' === $section->get_id() );
expect( 1 === count( $highlight_sections ) && 16 === count( reset( $highlight_sections )->get_items() ), 'Native Text highlight controls lost their compact group.' );
$segment_schema = $text_widget::get_props_schema()['segments'];
$rich_schema = \RadmanCTComponents\Elementor\AtomicComponents\Box::get_props_schema()['text'];
$template_schema = \RadmanCTComponents\Elementor\AtomicComponents\FlipBox::get_props_schema()['front_template'];
require_once dirname( __DIR__ ) . '/radmanct-components/inc/integrations/elementor/widgets/repeater-controls.php';
require_once dirname( __DIR__ ) . '/themes/elementor/includes/managers/controls.php';
$media_control = \RadmanCTComponents\Elementor\Widgets\field_control( 'photo', array( 'label' => 'Photo', 'default' => 0, 'media' => true, 'min' => 0, 'max' => PHP_INT_MAX ) );
expect( \Elementor\Controls_Manager::MEDIA === $media_control['type'] && array( 'id' => 0, 'url' => '' ) === $media_control['default'], 'Numeric attachment validation replaced the native Media control.' );
$rich_control = \RadmanCTComponents\Elementor\Widgets\field_control( 'body', array( 'label' => 'Body', 'default' => '', 'multiline' => true, 'rich' => true ) );
$plain_control = \RadmanCTComponents\Elementor\Widgets\field_control( 'body', array( 'label' => 'Body', 'default' => '', 'multiline' => true ) );
expect( \Elementor\Controls_Manager::WYSIWYG === $rich_control['type'] && \Elementor\Controls_Manager::TEXTAREA === $plain_control['type'], 'Plain and formatted content no longer use their native control types.' );
$switch_control = \RadmanCTComponents\Elementor\Widgets\field_control( 'enabled', array( 'label' => 'Enabled', 'default' => true ) );
expect( \Elementor\Controls_Manager::SWITCHER === $switch_control['type'] && 'yes' === $switch_control['default'] && 'yes' === $switch_control['return_value'], 'Native switcher lost the enabled default.' );
expect( $template_schema->validate( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::generate( 13 ) ) && $template_schema->validate( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( '13' ) ), 'Native template selection lost numeric compatibility or its string UI bridge.' );
expect( $rich_schema->validate( \Elementor\Modules\AtomicWidgets\PropTypes\Escaped_Html_Prop_Type::generate( '<strong>New</strong>' ) ) && $rich_schema->validate( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'Existing' ) ), 'Native HTML content lost the legacy string compatibility.' );
$media_schema = \RadmanCTComponents\Elementor\AtomicComponents\CurtainItem::make()->get_shape()['image_id'];
expect( $media_schema->validate( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::generate( 7 ) ), 'Native media field rejected an existing attachment id.' );
expect( ! $rich_schema->validate( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::generate( 7 ) ), 'Rich content accepted a numeric prop.' );
$segment = \RadmanCTComponents\Elementor\AtomicComponents\TextSegment::generate( array( 'title' => \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'Text' ) ) );
expect( $segment_schema->validate( \RadmanCTComponents\Elementor\AtomicComponents\TextSegments::generate( array( $segment ) ) ) && ! $segment_schema->validate( \RadmanCTComponents\Elementor\AtomicComponents\TextSegments::generate( array_fill( 0, 31, $segment ) ) ), 'Native rich text optional-image schema or count limit failed.' );
$segment['value']['image'] = \Elementor\Modules\AtomicWidgets\PropTypes\Image_Prop_Type::generate( array( 'src' => \Elementor\Modules\AtomicWidgets\PropTypes\Image_Src_Prop_Type::generate( array( 'id' => \Elementor\Modules\AtomicWidgets\PropTypes\Image_Attachment_Id_Prop_Type::generate( 7 ) ) ), 'size' => \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'large' ) ) );
expect( $segment_schema->validate( \RadmanCTComponents\Elementor\AtomicComponents\TextSegments::generate( array( $segment ) ) ), 'Native rich text image structure was not accepted.' );
$segment['value']['mask_image'] = $segment['value']['image'];
$segment['value']['line_height'] = \Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type::generate( array( 'size' => 1.4, 'unit' => 'em' ) );
foreach ( array( 'width', 'top', 'bottom', 'start', 'end' ) as $edge ) {
	foreach ( array( '', '_tablet', '_mobile' ) as $suffix ) {
		$key = 'image_border_' . $edge . $suffix;
		$prop = \RadmanCTComponents\Elementor\AtomicComponents\TextSegment::make()->get_shape()[ $key ];
		$value = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::generate( 2.5 );
		expect( $prop->validate( $value ) && 2.5 === $prop->sanitize( $value )['value'], 'Native border number lost decimal precision or its device schema: ' . $key );
	}
}
foreach ( array( 'start_start', 'start_end', 'end_start', 'end_end' ) as $corner ) {
	foreach ( array( '', '_tablet', '_mobile' ) as $suffix ) {
		$key = 'image_radius_' . $corner . $suffix;
		$native_size = \Elementor\Modules\AtomicWidgets\PropTypes\Size_Prop_Type::generate( array( 'size' => 0, 'unit' => '%' ) );
		expect( \RadmanCTComponents\Elementor\AtomicComponents\TextSegment::make()->get_shape()[ $key ]->validate( $native_size ), 'Native Atomic corner Size/% prop failed: ' . $key );
	}
}
expect( \RadmanCTComponents\Elementor\AtomicComponents\TextSegment::make()->get_shape()['line_height']->validate( $segment['value']['line_height'] ), 'Native line-height Size/em value was not accepted.' );
$segment['value']['font_style'] = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'italic' );
$segment['value']['text_transform'] = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'uppercase' );
$segment['value']['text_decoration'] = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'underline' );
$segment['value']['mask_resolution'] = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'medium' );
$segment['value']['paint'] = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( 'image' );
expect( $segment_schema->validate( \RadmanCTComponents\Elementor\AtomicComponents\TextSegments::generate( array( $segment ) ) ), 'Native text fill image or its paint enum was not accepted.' );
$gradient_shape = \RadmanCTComponents\Elementor\AtomicComponents\TextSegment::make()->get_shape();
foreach ( array( 'gradient_start_position', 'gradient_end_position', 'gradient_angle' ) as $key ) {
	$decimal = \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::generate( 12.5 );
	expect( $gradient_shape[ $key ]->validate( $decimal ) && 12.5 === $gradient_shape[ $key ]->sanitize( $decimal )['value'], 'Native gradient number truncated a decimal: ' . $key );
}
$gradient_string = static fn( $value ) => \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\String_Prop_Type::generate( $value );
expect( $gradient_shape['gradient_type']->validate( $gradient_string( 'radial' ) ) && ! $gradient_shape['gradient_type']->validate( $gradient_string( 'invalid' ) ), 'Gradient type is not a bounded native enum.' );
expect( \RadmanCTComponents\Elementor\AtomicComponents\ResponsiveValues::make()->get_shape()['laptop']->validate( \Elementor\Modules\AtomicWidgets\PropTypes\Primitives\Number_Prop_Type::generate( 12.5 ) ), 'Atomic button laptop value was rejected.' );
foreach ( array( '', '_tablet', '_mobile' ) as $suffix ) {
	expect( $gradient_shape[ 'gradient_position' . $suffix ]->validate( $gradient_string( 'bottom right' ) ) && ! $gradient_shape[ 'gradient_position' . $suffix ]->validate( $gradient_string( 'url(bad)' ) ), 'Native gradient position enum failed.' );
}
}
