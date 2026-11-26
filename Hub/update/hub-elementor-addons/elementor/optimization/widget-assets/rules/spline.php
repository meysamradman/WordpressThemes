<?php
 if ( $element_name === 'ld_spline' ) {

    $widget_utils['lqdsep-utils-pos-rel'] = array();
    $widget_utils['lqdsep-utils-overflow-hidden'] = array();
    $widget_utils['lqdsep-utils-block-d'] = array(
        'conditions' =>
            $element->get_settings('embed_type') !== 'iframe'
    );
    $widget_utils['lqdsep-utils-w-100'] = array(
        'conditions' =>
            $element->get_settings('embed_type') !== 'iframe'
    );
    $widget_utils['lqdsep-utils-h-100'] = array(
        'conditions' =>
            $element->get_settings('embed_type') !== 'iframe'
    );
    $widget_utils['lqdsep-utils-overlay'] = array(
        'conditions' =>
            $element->get_settings('embed_type') === 'iframe' ||
            ! empty( $element->get_settings('poster')['url'] )
    );
    $widget_utils['lqdsep-utils-pointer-events-none'] = array(
        'conditions' =>
            $element->get_settings('disable_interaction') === 'yes' ||
            ! empty( $element->get_settings('poster')['url'] )
    );

};
