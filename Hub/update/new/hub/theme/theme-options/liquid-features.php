<?php
/*
 * Features
*/

if ( ! function_exists( 'liquid_parallax_v2_breakpoint_choices' ) ) {
    /**
     * Desktop plus Elementor's active breakpoints.
     * Falls back to mobile and tablet when those breakpoints cannot be read.
     *
     * @return array<string, string>
     */
    function liquid_parallax_v2_breakpoint_choices() {
        $choices = array(
            'desktop' => esc_html__( 'Desktop', 'hub' ),
        );
        $active = array();

        if ( class_exists( '\Elementor\Plugin' ) ) {
            try {
                $plugin = \Elementor\Plugin::$instance;
                if ( is_object( $plugin ) && isset( $plugin->breakpoints ) && is_object( $plugin->breakpoints ) && method_exists( $plugin->breakpoints, 'get_active_breakpoints' ) ) {
                    $breakpoints = $plugin->breakpoints->get_active_breakpoints();
                    if ( is_array( $breakpoints ) ) {
                        foreach ( $breakpoints as $breakpoint_key => $breakpoint ) {
                            $breakpoint_key = sanitize_key( (string) $breakpoint_key );
                            if ( '' === $breakpoint_key || isset( $choices[ $breakpoint_key ] ) ) {
                                continue;
                            }

                            $label = $breakpoint_key;
                            if ( is_object( $breakpoint ) && method_exists( $breakpoint, 'get_label' ) ) {
                                $fetched_label = $breakpoint->get_label();
                                if ( is_string( $fetched_label ) && '' !== $fetched_label ) {
                                    $label = $fetched_label;
                                }
                            } elseif ( is_array( $breakpoint ) && isset( $breakpoint['label'] ) && is_string( $breakpoint['label'] ) && '' !== $breakpoint['label'] ) {
                                $label = $breakpoint['label'];
                            }

                            $active[ $breakpoint_key ] = $label;
                        }
                    }
                }
            } catch ( \Throwable $exception ) {
                $active = array();
            }
        }

        if ( empty( $active ) ) {
            $active = array(
                'mobile' => esc_html__( 'Mobile', 'hub' ),
                'tablet' => esc_html__( 'Tablet', 'hub' ),
            );
        }

        return array_merge( $choices, $active );
    }
}

if ( ! function_exists( 'liquid_parallax_v2_apply_breakpoints_default' ) ) {
    /**
     * Every active breakpoint, including desktop.
     *
     * @return string[]
     */
    function liquid_parallax_v2_apply_breakpoints_default() {
        return array_keys( liquid_parallax_v2_breakpoint_choices() );
    }
}

if ( ! function_exists( 'liquid_normalize_parallax_breakpoint_keys' ) ) {
    /**
     * Reduce a Redux multi-select value to a list of breakpoint key strings.
     * An empty selection becomes an empty array.
     *
     * @param mixed $value Raw option value.
     * @return string[]
     */
    function liquid_normalize_parallax_breakpoint_keys( $value ) {
        if ( is_string( $value ) ) {
            $decoded = json_decode( $value, true );
            if ( is_array( $decoded ) ) {
                $value = $decoded;
            } elseif ( '' === trim( $value ) ) {
                $value = array();
            } else {
                $value = preg_split( '/\s*,\s*/', $value );
            }
        }

        if ( ! is_array( $value ) ) {
            return array();
        }

        $keys = array();
        foreach ( $value as $item ) {
            if ( is_array( $item ) || is_object( $item ) ) {
                continue;
            }

            $item = sanitize_key( (string) $item );
            if ( '' === $item ) {
                continue;
            }

            $keys[ $item ] = $item;
        }

        return array_values( $keys );
    }
}

if ( ! function_exists( 'liquid_stored_apply_breakpoints' ) ) {
    /**
     * Breakpoint list for a v2 Apply on option.
     * An unsaved value uses every active breakpoint. An explicit empty selection stays empty.
     *
     * @param mixed $value Raw option value.
     * @return string[]
     */
    function liquid_stored_apply_breakpoints( $value ) {
        if ( ! is_array( $value ) ) {
            if ( is_string( $value ) && '' !== $value ) {
                $decoded = json_decode( $value, true );
                $value   = is_array( $decoded ) ? $decoded : explode( ',', $value );
            } else {
                $value = liquid_parallax_v2_apply_breakpoints_default();
            }
        }

        return liquid_normalize_parallax_breakpoint_keys( $value );
    }
}

if ( ! function_exists( 'liquid_parallax_v2_preserve_disabled_breakpoints' ) ) {
    /**
     * Keep a cleared Apply on multi-select as [] while its v2 flag is on.
     * The field is hidden while the flag is off, so other saves must not replace its stored list.
     *
     * @param mixed $plugin_options Options being saved.
     * @return mixed
     */
    function liquid_parallax_v2_preserve_disabled_breakpoints( $plugin_options ) {
        if ( ! is_array( $plugin_options ) ) {
            return $plugin_options;
        }

        $pairs = array(
            'enable_parallax_v2'    => 'parallax_apply_breakpoints',
            'enable_text_reveal_v2' => 'text_reveal_apply_breakpoints',
        );

        foreach ( $pairs as $flag => $key ) {
            if ( ! isset( $plugin_options[ $flag ] ) || 'on' !== $plugin_options[ $flag ] ) {
                continue;
            }

            if ( ! array_key_exists( $key, $plugin_options ) || '' === $plugin_options[ $key ] || null === $plugin_options[ $key ] ) {
                $plugin_options[ $key ] = array();
                continue;
            }

            $plugin_options[ $key ] = liquid_normalize_parallax_breakpoint_keys( $plugin_options[ $key ] );
        }

        return $plugin_options;
    }
}

if ( class_exists( 'Liquid_Elementor_Addons' ) && defined( 'ELEMENTOR_VERSION' ) ) {
    $parallax_breakpoint_options = liquid_parallax_v2_breakpoint_choices();
    $parallax_apply_default      = liquid_parallax_v2_apply_breakpoints_default();

    add_filter( 'redux/validate/' . liquid()->get_option_name() . '/before_validation', 'liquid_parallax_v2_preserve_disabled_breakpoints' );

    $this->sections[] = array(
        'title' => esc_html__( 'Features', 'hub' ),
        'icon'  => 'el el-cog',
    );

    $this->sections[] = array(
        'title'      => esc_html__( 'Parallax', 'hub' ),
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enable_parallax_v2',
                'type'     => 'button_set',
                'title'    => esc_html__( 'Parallax v2', 'hub' ),
                'subtitle' => esc_html__( 'Enables multi-step parallax and per-breakpoint values. Existing from/to parallax stays as it is until a widget is edited.', 'hub' ),
                'options'  => array(
                    'on'  => esc_html__( 'On', 'hub' ),
                    'off' => esc_html__( 'Off', 'hub' ),
                ),
                'default'  => 'off',
            ),
            array(
                'id'       => 'parallax_apply_breakpoints',
                'type'     => 'select',
                'multi'    => true,
                'title'    => esc_html__( 'Apply on', 'hub' ),
                'subtitle' => esc_html__( 'Parallax runs on these breakpoints. A widget "Apply on" value replaces this for that element.', 'hub' ),
                'options'  => $parallax_breakpoint_options,
                'default'  => $parallax_apply_default,
                'required' => array(
                    'enable_parallax_v2',
                    '=',
                    'on',
                ),
            ),
        ),
    );

    $this->sections[] = array(
        'title'      => esc_html__( 'Text Reveal', 'hub' ),
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enable_text_reveal_v2',
                'type'     => 'button_set',
                'title'    => esc_html__( 'Text Reveal v2', 'hub' ),
                'subtitle' => esc_html__( 'Enables multi-step text reveal, a color stop, and per-breakpoint values. Existing opacity reveal stays as it is until a widget is edited.', 'hub' ),
                'options'  => array(
                    'on'  => esc_html__( 'On', 'hub' ),
                    'off' => esc_html__( 'Off', 'hub' ),
                ),
                'default'  => 'off',
            ),
            array(
                'id'       => 'text_reveal_apply_breakpoints',
                'type'     => 'select',
                'multi'    => true,
                'title'    => esc_html__( 'Apply on', 'hub' ),
                'subtitle' => esc_html__( 'Text reveal runs on these breakpoints. A widget "Apply on" value replaces this for that element.', 'hub' ),
                'options'  => $parallax_breakpoint_options,
                'default'  => $parallax_apply_default,
                'required' => array(
                    'enable_text_reveal_v2',
                    '=',
                    'on',
                ),
            ),
        ),
    );
}
