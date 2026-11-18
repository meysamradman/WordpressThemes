<?php
namespace RadmanCT;
defined('ABSPATH') || exit;

function entrance_words(string $title): void {
    $parts = preg_split('/(\s+)/u', $title, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    if (!$parts || count($parts) > 23 || $title !== wp_strip_all_tags($title)) {
        echo wp_kses_post($title);
        return;
    }
    $word = 0;
    foreach ($parts as $part) {
        if (preg_match('/^\s+$/u', $part)) {
            echo esc_html($part);
        } else {
            printf('<span class="rdc-tx-en__wd" style="--rdc-en-i:%d">%s</span>', $word++, esc_html($part));
        }
    }
}
