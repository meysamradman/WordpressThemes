<?php

namespace RadmanCTComponents\Components\Button;

defined( 'ABSPATH' ) || exit;

function fields(): array {
	$fields = array(
		'gradient_type' => array( 'label' => __( 'Gradient type', 'radmanct-components' ), 'default' => 'linear', 'options' => array( 'linear' => __( 'Linear', 'radmanct-components' ), 'radial' => __( 'Radial', 'radmanct-components' ) ), 'condition' => array( 'background_type' => array( 'gradient' ) ) ),
		'gradient_start' => array( 'label' => __( 'Gradient start', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 100, 'condition' => array( 'background_type' => array( 'gradient' ) ) ),
		'gradient_end' => array( 'label' => __( 'Gradient end', 'radmanct-components' ), 'default' => 100, 'min' => 0, 'max' => 100, 'condition' => array( 'background_type' => array( 'gradient' ) ) ),
		'background_position' => array( 'label' => __( 'Background position', 'radmanct-components' ), 'default' => 'center center', 'options' => array( 'center center' => __( 'Center', 'radmanct-components' ), 'center top' => __( 'Top', 'radmanct-components' ), 'center bottom' => __( 'Bottom', 'radmanct-components' ), 'left center' => __( 'Left', 'radmanct-components' ), 'right center' => __( 'Right', 'radmanct-components' ), 'left top' => __( 'Top left', 'radmanct-components' ), 'right top' => __( 'Top right', 'radmanct-components' ), 'left bottom' => __( 'Bottom left', 'radmanct-components' ), 'right bottom' => __( 'Bottom right', 'radmanct-components' ) ), 'condition' => array( 'background_type' => array( 'image', 'gradient' ) ) ),
		'background_size' => array( 'label' => __( 'Background size', 'radmanct-components' ), 'default' => 'cover', 'options' => array( 'cover' => __( 'Cover', 'radmanct-components' ), 'contain' => __( 'Contain', 'radmanct-components' ), 'auto' => __( 'Auto', 'radmanct-components' ) ), 'condition' => array( 'background_type' => array( 'image' ) ) ),
		'background_repeat' => array( 'label' => __( 'Background repeat', 'radmanct-components' ), 'default' => 'no-repeat', 'options' => array( 'no-repeat' => __( 'No repeat', 'radmanct-components' ), 'repeat' => __( 'Repeat', 'radmanct-components' ), 'repeat-x' => __( 'Repeat horizontally', 'radmanct-components' ), 'repeat-y' => __( 'Repeat vertically', 'radmanct-components' ) ), 'condition' => array( 'background_type' => array( 'image' ) ) ),
		'background_attachment' => array( 'label' => __( 'Background attachment', 'radmanct-components' ), 'default' => 'scroll', 'options' => array( 'scroll' => __( 'Scroll', 'radmanct-components' ), 'fixed' => __( 'Fixed', 'radmanct-components' ) ), 'condition' => array( 'background_type' => array( 'image' ) ) ),
		'shadow_inset' => array( 'label' => __( 'Inset shadow', 'radmanct-components' ), 'default' => false, 'group' => 'normal' ),
		'hover_gradient_type' => array( 'label' => __( 'Hover gradient type', 'radmanct-components' ), 'default' => 'linear', 'options' => array( 'linear' => __( 'Linear', 'radmanct-components' ), 'radial' => __( 'Radial', 'radmanct-components' ) ), 'condition' => array( 'hover_background_type' => array( 'gradient' ) ) ),
		'hover_gradient_start' => array( 'label' => __( 'Hover gradient start', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 100, 'condition' => array( 'hover_background_type' => array( 'gradient' ) ) ),
		'hover_gradient_end' => array( 'label' => __( 'Hover gradient end', 'radmanct-components' ), 'default' => 100, 'min' => 0, 'max' => 100, 'condition' => array( 'hover_background_type' => array( 'gradient' ) ) ),
		'hover_background_position' => array( 'label' => __( 'Hover background position', 'radmanct-components' ), 'default' => 'center center', 'options' => array( 'center center' => __( 'Center', 'radmanct-components' ), 'center top' => __( 'Top', 'radmanct-components' ), 'center bottom' => __( 'Bottom', 'radmanct-components' ), 'left center' => __( 'Left', 'radmanct-components' ), 'right center' => __( 'Right', 'radmanct-components' ), 'left top' => __( 'Top left', 'radmanct-components' ), 'right top' => __( 'Top right', 'radmanct-components' ), 'left bottom' => __( 'Bottom left', 'radmanct-components' ), 'right bottom' => __( 'Bottom right', 'radmanct-components' ) ), 'condition' => array( 'hover_background_type' => array( 'image', 'gradient' ) ) ),
		'hover_background_size' => array( 'label' => __( 'Hover background size', 'radmanct-components' ), 'default' => 'cover', 'options' => array( 'cover' => __( 'Cover', 'radmanct-components' ), 'contain' => __( 'Contain', 'radmanct-components' ), 'auto' => __( 'Auto', 'radmanct-components' ) ), 'condition' => array( 'hover_background_type' => array( 'image' ) ) ),
		'hover_background_repeat' => array( 'label' => __( 'Hover background repeat', 'radmanct-components' ), 'default' => 'no-repeat', 'options' => array( 'no-repeat' => __( 'No repeat', 'radmanct-components' ), 'repeat' => __( 'Repeat', 'radmanct-components' ), 'repeat-x' => __( 'Repeat horizontally', 'radmanct-components' ), 'repeat-y' => __( 'Repeat vertically', 'radmanct-components' ) ), 'condition' => array( 'hover_background_type' => array( 'image' ) ) ),
		'hover_background_attachment' => array( 'label' => __( 'Hover background attachment', 'radmanct-components' ), 'default' => 'scroll', 'options' => array( 'scroll' => __( 'Scroll', 'radmanct-components' ), 'fixed' => __( 'Fixed', 'radmanct-components' ) ), 'condition' => array( 'hover_background_type' => array( 'image' ) ) ),
		'hover_shadow_inset' => array( 'label' => __( 'Hover inset shadow', 'radmanct-components' ), 'default' => false, 'group' => 'hover' ),
		'sticky_icon_border_color' => array( 'label' => __( 'Sticky icon border color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'sticky_separator_color' => array( 'label' => __( 'Sticky separator color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'sticky_ripple_color' => array( 'label' => __( 'Sticky ripple color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'sticky_hover_icon_color' => array( 'label' => __( 'Sticky hover icon color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'sticky_hover_icon_background' => array( 'label' => __( 'Sticky hover icon background', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'sticky_hover_icon_border_color' => array( 'label' => __( 'Sticky hover icon border color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'sticky_hover_separator_color' => array( 'label' => __( 'Sticky hover separator color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'sticky_hover_ripple_color' => array( 'label' => __( 'Sticky hover ripple color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'action' => array( 'label' => __( 'Link action', 'radmanct-components' ), 'default' => 'link', 'options' => array( 'link' => __( 'Simple link', 'radmanct-components' ), 'lightbox' => __( 'Lightbox', 'radmanct-components' ), 'modal' => __( 'Modal window', 'radmanct-components' ), 'scroll' => __( 'Scroll to target', 'radmanct-components' ), 'scroll-next' => __( 'Scroll to next section', 'radmanct-components' ) ) ),
		'anchor_id' => array( 'label' => __( 'Target element ID', 'radmanct-components' ), 'default' => '', 'condition' => array( 'action' => array( 'modal', 'scroll' ) ) ),
		'image_caption' => array( 'label' => __( 'Lightbox caption', 'radmanct-components' ), 'default' => '', 'condition' => array( 'action' => array( 'lightbox' ) ) ),
		'scroll_speed' => array( 'label' => __( 'Scroll duration (seconds)', 'radmanct-components' ), 'default' => 0.8, 'min' => 0, 'max' => 5, 'condition' => array( 'action' => array( 'scroll', 'scroll-next' ) ) ),
		'size' => array( 'label' => __( 'Button size', 'radmanct-components' ), 'default' => 'custom', 'options' => array( 'xs' => __( 'Extra Small', 'radmanct-components' ), 'sm' => __( 'Small', 'radmanct-components' ), 'md' => __( 'Medium', 'radmanct-components' ), 'lg' => __( 'Large', 'radmanct-components' ), 'xl' => __( 'Extra Large', 'radmanct-components' ), 'custom' => __( 'Custom', 'radmanct-components' ) ) ),
		'custom_width' => array( 'label' => __( 'Custom width (zero is automatic)', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 2000, 'condition' => array( 'size' => array( 'custom' ) ) ),
		'custom_height' => array( 'label' => __( 'Minimum height', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 1000, 'condition' => array( 'size' => array( 'custom' ) ) ),
		'rotation' => array( 'label' => __( 'Button rotation', 'radmanct-components' ), 'default' => 0, 'min' => -360, 'max' => 360 ),
		'icon_rotation' => array( 'label' => __( 'Icon rotation', 'radmanct-components' ), 'default' => 0, 'min' => -360, 'max' => 360 ),
		'icon_box_preset' => array( 'label' => __( 'Icon container preset', 'radmanct-components' ), 'default' => 'custom', 'options' => array( 'xs' => __( 'Extra Small', 'radmanct-components' ), 'sm' => __( 'Small', 'radmanct-components' ), 'md' => __( 'Medium', 'radmanct-components' ), 'lg' => __( 'Large', 'radmanct-components' ), 'xl' => __( 'Extra Large', 'radmanct-components' ), 'custom' => __( 'Custom', 'radmanct-components' ) ) ),
		'icon_box_size' => array( 'label' => __( 'Icon container size (zero is automatic)', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 250 ),
		'icon_magnetic' => array( 'label' => __( 'Magnetic icon', 'radmanct-components' ), 'default' => false ),
		'icon_border_width' => array( 'label' => __( 'Icon border width', 'radmanct-components' ), 'default' => 1, 'min' => 0, 'max' => 12 ),
		'border_style' => array( 'label' => __( 'Border style', 'radmanct-components' ), 'default' => 'solid', 'options' => array( 'solid' => __( 'Solid', 'radmanct-components' ), 'dashed' => __( 'Dashed', 'radmanct-components' ), 'dotted' => __( 'Dotted', 'radmanct-components' ), 'none' => __( 'None', 'radmanct-components' ) ) ),
		'background_type' => array( 'label' => __( 'Background type', 'radmanct-components' ), 'default' => 'color', 'options' => array( 'color' => __( 'Color', 'radmanct-components' ), 'gradient' => __( 'Gradient', 'radmanct-components' ), 'image' => __( 'Image', 'radmanct-components' ) ) ),
		'gradient_color' => array( 'label' => __( 'Gradient second color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'background_type' => array( 'gradient' ) ) ),
		'gradient_angle' => array( 'label' => __( 'Gradient angle', 'radmanct-components' ), 'default' => 135, 'min' => 0, 'max' => 360, 'condition' => array( 'background_type' => array( 'gradient' ) ) ),
		'background_image' => array( 'label' => __( 'Background image', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 2147483647, 'media' => true, 'condition' => array( 'background_type' => array( 'image' ) ) ),
		'icon_border_color' => array( 'label' => __( 'Icon border color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'separator_color' => array( 'label' => __( 'Separator color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'ripple_color' => array( 'label' => __( 'Ripple color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'shadow_color' => array( 'label' => __( 'Shadow color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'shadow_x' => array( 'label' => __( 'Shadow horizontal offset', 'radmanct-components' ), 'default' => 0, 'min' => -100, 'max' => 100 ),
		'shadow_y' => array( 'label' => __( 'Shadow vertical offset', 'radmanct-components' ), 'default' => 0, 'min' => -100, 'max' => 100 ),
		'shadow_blur' => array( 'label' => __( 'Shadow blur', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 200 ),
		'shadow_spread' => array( 'label' => __( 'Shadow spread', 'radmanct-components' ), 'default' => 0, 'min' => -100, 'max' => 100 ),
		'hover_background_type' => array( 'label' => __( 'Hover background type', 'radmanct-components' ), 'default' => 'color', 'options' => array( 'color' => __( 'Color', 'radmanct-components' ), 'gradient' => __( 'Gradient', 'radmanct-components' ), 'image' => __( 'Image', 'radmanct-components' ) ) ),
		'hover_gradient_color' => array( 'label' => __( 'Hover gradient second color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'hover_background_type' => array( 'gradient' ) ) ),
		'hover_gradient_angle' => array( 'label' => __( 'Hover gradient angle', 'radmanct-components' ), 'default' => 135, 'min' => 0, 'max' => 360, 'condition' => array( 'hover_background_type' => array( 'gradient' ) ) ),
		'hover_background_image' => array( 'label' => __( 'Hover background image', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 2147483647, 'media' => true, 'condition' => array( 'hover_background_type' => array( 'image' ) ) ),
		'hover_icon_color' => array( 'label' => __( 'Hover icon color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'hover_icon_background' => array( 'label' => __( 'Hover icon background', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'hover_icon_border_color' => array( 'label' => __( 'Hover icon border color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'hover_separator_color' => array( 'label' => __( 'Hover separator color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'hover_ripple_color' => array( 'label' => __( 'Hover ripple color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'hover_shadow_color' => array( 'label' => __( 'Hover shadow color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'hover_shadow_x' => array( 'label' => __( 'Hover shadow horizontal offset', 'radmanct-components' ), 'default' => 0, 'min' => -100, 'max' => 100 ),
		'hover_shadow_y' => array( 'label' => __( 'Hover shadow vertical offset', 'radmanct-components' ), 'default' => 0, 'min' => -100, 'max' => 100 ),
		'hover_shadow_blur' => array( 'label' => __( 'Hover shadow blur', 'radmanct-components' ), 'default' => 0, 'min' => 0, 'max' => 200 ),
		'hover_shadow_spread' => array( 'label' => __( 'Hover shadow spread', 'radmanct-components' ), 'default' => 0, 'min' => -100, 'max' => 100 ),
		'sticky_mode' => array( 'label' => __( 'Sticky header colors', 'radmanct-components' ), 'default' => 'inherit', 'options' => array( 'inherit' => __( 'Inherit', 'radmanct-components' ), 'custom' => __( 'Custom', 'radmanct-components' ) ) ),
		'sticky_color' => array( 'label' => __( 'Sticky text color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'sticky_background' => array( 'label' => __( 'Sticky background color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'sticky_border_color' => array( 'label' => __( 'Sticky border color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'sticky_hover_color' => array( 'label' => __( 'Sticky hover text color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'sticky_hover_background' => array( 'label' => __( 'Sticky hover background', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'sticky_hover_border_color' => array( 'label' => __( 'Sticky hover border color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'sticky_icon_color' => array( 'label' => __( 'Sticky icon color', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'sticky_icon_background' => array( 'label' => __( 'Sticky icon background', 'radmanct-components' ), 'default' => '', 'color' => true, 'condition' => array( 'sticky_mode' => array( 'custom' ) ) ),
		'label' => array( 'label' => __( 'Button text', 'radmanct-components' ), 'default' => __( 'Learn more', 'radmanct-components' ) ),
		'secondary_label' => array( 'label' => __( 'Hover text', 'radmanct-components' ), 'default' => '', 'condition' => array( 'text_effect' => array( 'change-y' ) ) ),
		'tag' => array( 'label' => __( 'Element', 'radmanct-components' ), 'default' => 'a', 'options' => array( 'a' => __( 'Link', 'radmanct-components' ), 'button' => __( 'Button', 'radmanct-components' ) ) ),
		'url' => array( 'label' => __( 'Link', 'radmanct-components' ), 'default' => '#' ),
		'new_tab' => array( 'label' => __( 'Open in new tab', 'radmanct-components' ), 'default' => false ),
		'nofollow' => array( 'label' => __( 'Nofollow', 'radmanct-components' ), 'default' => false ),
		'disabled' => array( 'label' => __( 'Disabled', 'radmanct-components' ), 'default' => false ),
		'style' => array( 'label' => __( 'Appearance', 'radmanct-components' ), 'default' => 'solid', 'options' => array( 'solid' => __( 'Solid', 'radmanct-components' ), 'outline' => __( 'Outline', 'radmanct-components' ), 'plain' => __( 'Plain', 'radmanct-components' ), 'underline' => __( 'Underline', 'radmanct-components' ) ) ),
		'text_effect' => array( 'label' => __( 'Text hover effect', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'switch-x' => __( 'Switch horizontally', 'radmanct-components' ), 'switch-y' => __( 'Switch vertically', 'radmanct-components' ), 'change-y' => __( 'Change text', 'radmanct-components' ), 'marquee-x' => __( 'Marquee horizontally', 'radmanct-components' ), 'marquee-y' => __( 'Marquee vertically', 'radmanct-components' ), 'liquid-x' => __( 'Liquid horizontally', 'radmanct-components' ), 'liquid-y' => __( 'Liquid vertically', 'radmanct-components' ), 'liquid-x-alt' => __( 'Liquid horizontally reversed', 'radmanct-components' ), 'liquid-y-alt' => __( 'Liquid vertically reversed', 'radmanct-components' ) ) ),
		'icon' => array( 'label' => __( 'Icon', 'radmanct-components' ), 'default' => '' ),
		'icon_position' => array( 'label' => __( 'Icon position', 'radmanct-components' ), 'default' => 'end', 'options' => array( 'start' => __( 'Start', 'radmanct-components' ), 'end' => __( 'End', 'radmanct-components' ), 'top' => __( 'Top', 'radmanct-components' ), 'bottom' => __( 'Bottom', 'radmanct-components' ) ) ),
		'icon_style' => array( 'label' => __( 'Icon appearance', 'radmanct-components' ), 'default' => 'plain', 'options' => array( 'plain' => __( 'Plain', 'radmanct-components' ), 'solid' => __( 'Solid', 'radmanct-components' ), 'outline' => __( 'Outline', 'radmanct-components' ) ) ),
		'icon_shape' => array( 'label' => __( 'Icon shape', 'radmanct-components' ), 'default' => 'circle', 'options' => array( 'square' => __( 'Square', 'radmanct-components' ), 'semi-round' => __( 'Semi rounded', 'radmanct-components' ), 'rounded' => __( 'Rounded', 'radmanct-components' ), 'round' => __( 'Pill', 'radmanct-components' ), 'circle' => __( 'Circle', 'radmanct-components' ) ) ),
		'icon_effect' => array( 'label' => __( 'Icon hover effect', 'radmanct-components' ), 'default' => 'none', 'options' => array( 'none' => __( 'None', 'radmanct-components' ), 'reveal' => __( 'Reveal', 'radmanct-components' ), 'swap' => __( 'Swap', 'radmanct-components' ) ) ),
		'icon_ripple' => array( 'label' => __( 'Icon ripple', 'radmanct-components' ), 'default' => false ),
		'icon_separator' => array( 'label' => __( 'Icon separator', 'radmanct-components' ), 'default' => false ),
		'magnetic' => array( 'label' => __( 'Magnetic hover', 'radmanct-components' ), 'default' => false ),
		'gap' => array( 'label' => __( 'Icon gap', 'radmanct-components' ), 'default' => 12, 'min' => 0, 'max' => 100 ),
		'icon_size' => array( 'label' => __( 'Icon size', 'radmanct-components' ), 'default' => 20, 'min' => 8, 'max' => 100 ),
		'padding_x' => array( 'label' => __( 'Horizontal padding', 'radmanct-components' ), 'default' => 24, 'min' => 0, 'max' => 160 ),
		'padding_y' => array( 'label' => __( 'Vertical padding', 'radmanct-components' ), 'default' => 14, 'min' => 0, 'max' => 100 ),
		'radius' => array( 'label' => __( 'Border radius', 'radmanct-components' ), 'default' => 4, 'min' => 0, 'max' => 200 ),
		'border_width' => array( 'label' => __( 'Border width', 'radmanct-components' ), 'default' => 1, 'min' => 0, 'max' => 12 ),
		'font_size' => array( 'label' => __( 'Font size', 'radmanct-components' ), 'default' => 16, 'min' => 8, 'max' => 100 ),
		'font_weight' => array( 'label' => __( 'Font weight', 'radmanct-components' ), 'default' => '', 'options' => array( '' => __( 'Inherit', 'radmanct-components' ), '100' => '100', '200' => '200', '300' => '300', '400' => '400', '500' => '500', '600' => '600', '700' => '700', '800' => '800', '900' => '900' ) ),
		'font_style' => array( 'label' => __( 'Font style', 'radmanct-components' ), 'default' => '', 'options' => array( '' => __( 'Inherit', 'radmanct-components' ), 'normal' => __( 'Normal', 'radmanct-components' ), 'italic' => __( 'Italic', 'radmanct-components' ), 'oblique' => __( 'Oblique', 'radmanct-components' ) ) ),
		'text_transform' => array( 'label' => __( 'Text transform', 'radmanct-components' ), 'default' => '', 'options' => array( '' => __( 'Inherit', 'radmanct-components' ), 'none' => __( 'None', 'radmanct-components' ), 'uppercase' => __( 'Uppercase', 'radmanct-components' ), 'lowercase' => __( 'Lowercase', 'radmanct-components' ), 'capitalize' => __( 'Capitalize', 'radmanct-components' ) ) ),
		'text_decoration' => array( 'label' => __( 'Text decoration', 'radmanct-components' ), 'default' => '', 'options' => array( '' => __( 'Inherit', 'radmanct-components' ), 'none' => __( 'None', 'radmanct-components' ), 'underline' => __( 'Underline', 'radmanct-components' ), 'overline' => __( 'Overline', 'radmanct-components' ), 'line-through' => __( 'Line through', 'radmanct-components' ) ) ),
		'width' => array( 'label' => __( 'Width', 'radmanct-components' ), 'default' => 'auto', 'options' => array( 'auto' => __( 'Fit content', 'radmanct-components' ), '100%' => __( 'Full width', 'radmanct-components' ) ) ),
		'duration' => array( 'label' => __( 'Hover duration (seconds)', 'radmanct-components' ), 'default' => .4, 'min' => .1, 'max' => 2, 'effect_defaults' => array( 'switch-x' => .85, 'switch-y' => .85, 'change-y' => .85, 'marquee-x' => 2, 'marquee-y' => 1, 'liquid-x' => .6, 'liquid-y' => .6, 'liquid-x-alt' => .6, 'liquid-y-alt' => .6 ) ),
		'color' => array( 'label' => __( 'Text color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'background' => array( 'label' => __( 'Background color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'border_color' => array( 'label' => __( 'Border color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'hover_color' => array( 'label' => __( 'Hover text color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'hover_background' => array( 'label' => __( 'Hover background color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'hover_border_color' => array( 'label' => __( 'Hover border color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'icon_color' => array( 'label' => __( 'Icon color', 'radmanct-components' ), 'default' => '', 'color' => true ),
		'icon_background' => array( 'label' => __( 'Icon background', 'radmanct-components' ), 'default' => '', 'color' => true ),
	);
	foreach ( array( 'line_height' => __( 'Line height', 'radmanct-components' ), 'letter_spacing' => __( 'Letter spacing', 'radmanct-components' ), 'word_spacing' => __( 'Word spacing', 'radmanct-components' ) ) as $key => $label ) {
		$fields[ $key ] = array( 'label' => $label, 'default' => -1000, 'min' => -1000, 'max' => 200, 'group' => 'appearance' );
		$units = 'line_height' === $key ? array( '', 'px', 'em' ) : array( 'px', 'em', 'rem' );
		$fields[ $key . '_unit' ] = array( 'label' => $label . ' — ' . __( 'Unit', 'radmanct-components' ), 'default' => $units[0], 'options' => array_combine( $units, array_map( static fn( $unit ) => '' === $unit ? __( 'Unitless', 'radmanct-components' ) : $unit, $units ) ), 'group' => 'appearance' );
	}
	foreach ( array( 'gap', 'icon_size', 'icon_box_size', 'custom_width', 'custom_height', 'padding_x', 'padding_y', 'radius', 'border_width', 'font_size' ) as $key ) {
		$fields[ $key . '_unit' ] = array( 'label' => $fields[ $key ]['label'] . ' — ' . __( 'Unit', 'radmanct-components' ), 'default' => 'px', 'options' => array( 'px' => 'px', 'em' => 'em', 'rem' => 'rem' ) );
		if ( isset( $fields[ $key ]['condition'] ) ) { $fields[ $key . '_unit' ]['condition'] = $fields[ $key ]['condition']; }
	}
	foreach ( $fields as $key => &$field ) {
		if ( str_ends_with( $key, '_unit' ) && in_array( substr( $key, 0, -5 ), array( 'padding_x', 'padding_y', 'font_size', 'custom_width', 'custom_height' ), true ) ) { $field['condition'] = array( 'size' => array( 'custom' ) ); }
		if ( in_array( $key, array( 'padding_x', 'padding_y', 'font_size', 'custom_width', 'custom_height' ), true ) ) { $field['condition'] = array( 'size' => array( 'custom' ) ); }
		if ( 'icon_effect' === $key ) { $field['condition'] = array( 'icon_position' => array( 'start', 'end' ) ); }
		if ( 'icon_magnetic' === $key ) { $field['condition'] = array( 'icon_style' => array( 'solid', 'outline' ) ); }
		if ( 'icon_box_size' === $key ) { $field['condition'] = array( 'icon_box_preset' => array( 'custom' ) ); }
		$field['group'] = $field['group'] ?? ( str_starts_with( $key, 'sticky_' ) ? 'sticky' : ( str_starts_with( $key, 'hover_' ) ? 'hover' : ( ! empty( $field['color'] ) || str_starts_with( $key, 'background_' ) || str_starts_with( $key, 'gradient_' ) || str_starts_with( $key, 'shadow_' ) ? 'normal' : ( in_array( $key, array( 'action', 'anchor_id', 'image_caption', 'scroll_speed', 'label', 'secondary_label', 'tag', 'url', 'new_tab', 'nofollow', 'disabled' ), true ) ? 'content' : ( in_array( $key, array( 'text_effect', 'icon_effect', 'icon_ripple', 'magnetic', 'icon_magnetic', 'duration' ), true ) ? 'interaction' : ( str_starts_with( $key, 'icon_' ) || 'icon' === $key || 'gap' === $key ? 'icon' : 'appearance' ) ) ) ) ) );
	}
	return $fields;
}

function groups(): array {
	return array( 'content' => __( 'Content', 'radmanct-components' ), 'appearance' => __( 'Appearance', 'radmanct-components' ), 'icon' => __( 'Icon', 'radmanct-components' ), 'interaction' => __( 'Hover effects', 'radmanct-components' ), 'normal' => __( 'Colors (Normal)', 'radmanct-components' ), 'hover' => __( 'Colors (Hover)', 'radmanct-components' ), 'sticky' => __( 'Sticky header colors', 'radmanct-components' ) );
}

function presentation(): array {
	$result = array();
	foreach ( array( 'gap', 'icon_size', 'icon_box_size', 'custom_width', 'custom_height', 'padding_x', 'padding_y', 'radius', 'border_width', 'font_size' ) as $key ) {
		$field = fields()[ $key ];
		$result[ $key ] = array( 'component' => 'button', 'selector' => '&', 'css' => '--rdct-button-' . str_replace( '_', '-', $key ) . ':%s;', 'unit_field' => $key . '_unit', 'allowed_units' => array( 'px', 'em', 'rem' ), 'default' => $field['default'], 'min' => $field['min'], 'max' => $field['max'], 'step' => .1 ) + ( isset( $field['condition'] ) ? array( 'condition' => $field['condition'] ) : array() );
		$result[ $key . '_unit' ] = array( 'selector' => '&', 'css' => '', 'options' => array( 'px' => 'px', 'em' => 'em', 'rem' => 'rem' ), 'options_ui' => 'select' ) + ( isset( $field['condition'] ) ? array( 'condition' => $field['condition'] ) : array() );
		$result[ $key ]['range'] = array( 'px' => array( 'min' => $field['min'], 'max' => $field['max'] ), 'em' => array( 'min' => 'font_size' === $key ? .1 : 0, 'max' => 100 ), 'rem' => array( 'min' => 'font_size' === $key ? .1 : 0, 'max' => 100 ) );
		$result[ $key ]['min'] = 'font_size' === $key ? .1 : 0;
	}
	$result['icon_box_size']['zero_css'] = '--rdct-button-icon-box-size:auto;';
	foreach ( array( 'line_height', 'letter_spacing', 'word_spacing' ) as $key ) {
		$units = array_keys( fields()[ $key . '_unit' ]['options'] );
		$result[ $key ] = array( 'component' => 'button', 'selector' => '&', 'css' => str_replace( '_', '-', $key ) . ':%s;', 'unit_field' => $key . '_unit', 'allowed_units' => $units, 'default_unit' => $units[0], 'default' => null, 'min' => 'line_height' === $key ? .1 : -20, 'max' => 200, 'step' => .1 );
		$result[ $key ]['range'] = 'line_height' === $key
			? array( '' => array( 'min' => .1, 'max' => 10 ), 'px' => array( 'min' => 1, 'max' => 200 ), 'em' => array( 'min' => .1, 'max' => 10 ) )
			: array( 'px' => array( 'min' => -20, 'max' => 100 ), 'em' => array( 'min' => -2, 'max' => 10 ), 'rem' => array( 'min' => -2, 'max' => 10 ) );
		$result[ $key . '_unit' ] = array( 'selector' => '&', 'css' => '', 'options' => fields()[ $key . '_unit' ]['options'], 'options_ui' => 'select' );
	}
	$result['width'] = array( 'selector' => '&', 'css' => 'inline-size:%s;', 'options' => array( 'auto' => __( 'Fit content', 'radmanct-components' ), '100%' => __( 'Full width', 'radmanct-components' ) ), 'options_ui' => 'select' );
	return $result;
}
