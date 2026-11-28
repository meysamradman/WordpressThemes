<?php
declare(strict_types=1);
namespace RadmanStore\Pagination;
defined('ABSPATH') || exit;
function render(array $options=[]): void {
    global $wp_query;
    $total=(int)($options['total']??$wp_query->max_num_pages);
    if ($total<2) { return; }
    $current=max(1,(int)($options['current']??get_query_var('paged')));
    $mode=apply_filters('radmanstore/pagination/mode',$options['mode']??'numbers');
    echo '<nav class="rds-pagination" aria-label="'.esc_attr__('صفحه‌بندی','radmanstore').'" data-rds-pager="'.esc_attr($mode==='more'?'more':'numbers').'">';
    if ($mode==='more') {
        if ($current<$total) {
            $next=get_pagenum_link($current+1);
            echo '<a rel="next" href="'.esc_url($next).'">'.esc_html__('نمایش بیشتر','radmanstore').'</a><p role="status" aria-live="polite"></p>';
            wp_enqueue_script('radmanstore-pagination');
        }
    } else {
        echo wp_kses_post(paginate_links(['total'=>$total,'current'=>$current,'mid_size'=>1,'prev_text'=>__('قبلی','radmanstore'),'next_text'=>__('بعدی','radmanstore')]));
    }
    echo '</nav>';
}
