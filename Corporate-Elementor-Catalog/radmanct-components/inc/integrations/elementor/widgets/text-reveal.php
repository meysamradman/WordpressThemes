<?php

namespace RadmanCTComponents\Elementor\Widgets;

defined( 'ABSPATH' ) || exit;
require_once dirname( __DIR__, 3 ) . '/components/text-reveal.php';
require_once dirname( __DIR__ ) . '/presentation.php';

class TextReveal extends \Elementor\Widget_Base {
	public function get_name() { return 'radman-text-reveal'; }
	public function show_in_panel() { return classic_in_panel(); }
	public function hide_on_search() { return ! classic_in_panel(); }
	public function get_title() { return ( __( 'Text', 'radmanct-components' ) ); }
	public function get_keywords() { return array( 'text', 'marquee', 'ticker', 'reveal', 'headline', __( 'Marquee', 'radmanct-components' ), __( 'Rotating words', 'radmanct-components' ) ); }
	public function get_icon() { return 'eicon-animated-headline'; }
	public function get_categories() { return array( 'radmanct-components' ); }
	public function get_style_depends() { return $this->is_type_instance() ? array() : \RadmanCTComponents\Components\style_depends( 'text-reveal', $this->get_settings_for_display() ); }
	public function get_script_depends() { return \RadmanCTComponents\Components\script_depends( 'text-reveal', $this->is_type_instance() ? array() : $this->get_settings_for_display(), $this->is_type_instance() ); }

	protected function register_controls() {
        $fields = \RadmanCTComponents\Components\TextReveal\fields();
        foreach ( \RadmanCTComponents\Components\TextReveal\groups() as $group => $spec ) {
            $this->start_controls_section( 'text_' . $group, array( 'label' => $spec['label'], 'tab' => in_array( $group, array( 'appearance', 'highlight', 'header', 'typography' ), true ) ? \Elementor\Controls_Manager::TAB_STYLE : \Elementor\Controls_Manager::TAB_CONTENT ) );
            foreach ( $spec['fields'] as $key ) {
                $responsive = \RadmanCTComponents\Components\Presentation\controls( 'text-reveal' )[ $key ] ?? null;
                if ( $responsive && ( str_starts_with( $key, 'highlight_' ) || str_starts_with( $key, 'inline_image_' ) ) ) {
                    \RadmanCTComponents\Integrations\Elementor\Presentation\control( $this, 'text-reveal', $key, $responsive, array( 'condition' => $responsive['condition'] ) );
                    continue;
                }
                $field = $fields[ $key ];
                $control = $field + array( 'type' => isset( $field['options'] ) ? \Elementor\Controls_Manager::SELECT : ( is_bool( $field['default'] ) ? \Elementor\Controls_Manager::SWITCHER : ( isset( $field['min'] ) ? \Elementor\Controls_Manager::NUMBER : ( ! empty( $field['multiline'] ) ? \Elementor\Controls_Manager::TEXTAREA : \Elementor\Controls_Manager::TEXT ) ) ) );
                if ( is_bool( $field['default'] ) ) { $control['default'] = $field['default'] ? 'yes' : ''; }
                if ( isset( $control['condition']['header_colors'] ) ) { $control['condition']['header_colors'] = 'yes'; }
                if ( ! empty( $field['color'] ) ) { $control['type'] = \Elementor\Controls_Manager::COLOR; $control['alpha'] = true; }
                if ( 'url' === $key ) { $control['type'] = \Elementor\Controls_Manager::URL; $control['default'] = array( 'url' => '' ); }
                if ( 'text' === $key ) { $control['description'] = __( 'Wrap highlighted words in [[double brackets]].', 'radmanct-components' ); $control['dynamic'] = array( 'active' => true ); }
                if ( 'visible_words' === $key ) { $this->add_responsive_control( 'visible_words_responsive', array( 'label' => $field['label'], 'type' => \Elementor\Controls_Manager::SLIDER, 'range' => array( 'px' => array( 'min' => 1, 'max' => 9 ) ) ) ); } else { $this->add_control( $key, $control ); }
            }
            if ( 'segments' === $group ) {
                $repeater = new \Elementor\Repeater();
                $repeater->start_controls_tabs( 'segment_groups' );
                foreach ( \RadmanCTComponents\Components\TextReveal\segment_groups() as $segment_group => $label ) {
                    $repeater->start_controls_tab( 'segment_' . $segment_group, array( 'label' => $label ) );
                    foreach ( \RadmanCTComponents\Components\TextReveal\segment_fields() as $key => $field ) {
                        if ( $field['group'] !== $segment_group ) { continue; }
                        if ( ! empty( $field['classic_shadow'] ) ) { continue; }
                        if ( ! empty( $field['size'] ) ) {
                            if ( '' !== $field['device'] ) { continue; }
                            $range = array();
                            foreach ( $field['units'] as $unit ) { $range[ $unit ] = ( $field['range'][ $unit ] ?? array( 'min' => $field['min'], 'max' => $field['max'] ) ) + array( 'step' => .1 ); }
                            $repeater->add_responsive_control( $key, array( 'label' => $field['label'], 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => $field['units'], 'range' => $range, 'condition' => $field['condition'] ?? array(), 'devices' => array( 'desktop', 'tablet', 'mobile' ), 'default' => array( 'size' => '', 'unit' => $field['default_unit'] ?? 'px' ) ) );
                            continue;
                        }
                        $control = $field;
                        $control['type'] = ! empty( $field['image'] ) ? \Elementor\Controls_Manager::MEDIA : ( isset( $field['options'] ) ? \Elementor\Controls_Manager::SELECT : ( ! empty( $field['color'] ) ? \Elementor\Controls_Manager::COLOR : ( isset( $field['min'] ) ? \Elementor\Controls_Manager::NUMBER : \Elementor\Controls_Manager::TEXT ) ) );
                        if ( ! empty( $field['image'] ) ) { $control['default'] = array( 'id' => 0, 'url' => '' ); }
                        if ( ! empty( $field['color'] ) ) { $control['alpha'] = true; }
                        unset( $control['image'], $control['group'] );
                        if ( ! empty( $field['responsive'] ) ) {
                            if ( '' !== $field['device'] ) { continue; }
                            unset( $control['responsive'], $control['device'] );
                            $control += array( 'devices' => array( 'desktop', 'tablet', 'mobile' ), 'tablet_default' => '', 'mobile_default' => '' );
                            $repeater->add_responsive_control( $key, $control );
                        } else { $repeater->add_control( $key, $control ); }
                        if ( in_array( $key, array( 'shadow_mode', 'shadow_mode_tablet', 'shadow_mode_mobile' ), true ) ) {
                            $repeater->add_control( str_replace( 'shadow_mode', 'shadow', $key ), array( 'label' => $field['label'], 'type' => \Elementor\Controls_Manager::TEXT_SHADOW, 'default' => array( 'horizontal' => 0, 'vertical' => 0, 'blur' => 10, 'color' => 'rgba(0,0,0,0.3)' ), 'condition' => array( $key => 'custom' ) ) );
                        }
                    }
                    $repeater->end_controls_tab();
                }
                $repeater->end_controls_tabs();
                $this->add_control( 'segments', array( 'label' => __( 'Text segments', 'radmanct-components' ), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'title_field' => '{{{ title }}}', 'condition' => array( 'mode' => 'rich' ) ) );
            }
            if ( 'words' === $group ) {
                $repeater = new \Elementor\Repeater();
                foreach ( \RadmanCTComponents\Components\TextReveal\word_fields() as $key => $field ) { $repeater->add_control( $key, $field + array( 'type' => ! empty( $field['color'] ) ? \Elementor\Controls_Manager::COLOR : \Elementor\Controls_Manager::TEXT, 'alpha' => true ) ); }
                $this->add_control( 'items', array( 'label' => __( 'Rotating words', 'radmanct-components' ), 'type' => \Elementor\Controls_Manager::REPEATER, 'fields' => $repeater->get_controls(), 'title_field' => '{{{ title }}}', 'condition' => array( 'mode' => 'rotate' ) ) );
            }
            $this->end_controls_section();
        }
        $this->start_controls_section( 'style', array( 'label' => __( 'Typography', 'radmanct-components' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
        $this->add_responsive_control( 'alignment', array( 'label' => __( 'Text alignment', 'radmanct-components' ), 'type' => \Elementor\Controls_Manager::SELECT, 'options' => array( '' => __( 'Inherit', 'radmanct-components' ), 'start' => __( 'Reading start', 'radmanct-components' ), 'end' => __( 'Reading end', 'radmanct-components' ), 'center' => __( 'Center', 'radmanct-components' ), 'justify' => __( 'Justify', 'radmanct-components' ) ), 'selectors' => array( '{{WRAPPER}} .rdct-tr' => 'text-align: {{VALUE}};' ) ) );
        foreach ( array( 'font_size', 'word_spacing' ) as $key ) {
            $size = \RadmanCTComponents\Components\Presentation\controls( 'text-reveal' )[ $key ];
            $range = array(); foreach ( $size['units'] as $unit ) { $range[ $unit ] = array( 'min' => $size['min'], 'max' => $size['max'], 'step' => .1 ); }
            $this->add_responsive_control( $key, array( 'label' => $size['label'], 'type' => \Elementor\Controls_Manager::SLIDER, 'size_units' => $size['units'], 'range' => $range ) );
        }
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'typography', 'selector' => '{{WRAPPER}} .rdct-tr', 'exclude' => array( 'font_family', 'font_size', 'font_weight', 'font_style', 'text_transform', 'text_decoration', 'word_spacing' ) ) );
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => 'word_typography', 'label' => __( 'Rotating words', 'radmanct-components' ), 'selector' => '{{WRAPPER}} .rdct-text__word', 'exclude' => array( 'font_family' ) ) );
        $this->add_group_control( \Elementor\Group_Control_Text_Shadow::get_type(), array( 'name' => 'text_shadow', 'selector' => '{{WRAPPER}} .rdct-tr' ) );
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        rdct_text_reveal( is_string( $settings['text'] ?? null ) ? $settings['text'] : '', $settings );
    }
}
