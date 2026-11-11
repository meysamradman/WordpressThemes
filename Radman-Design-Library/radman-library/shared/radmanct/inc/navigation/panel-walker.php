<?php
namespace RadmanCT\Navigation;
defined('ABSPATH')||exit;
class PanelWalker extends \Walker_Nav_Menu {
    private array $parents=array();
    public function start_el(&$output,$item,$depth=0,$args=null,$id=0){
        $this->parents[$depth]=(int)$item->ID;
        parent::start_el($output,$item,$depth,$args,$id);
        if((empty($args->depth)||$depth+1<(int)$args->depth)&&in_array('menu-item-has-children',(array)$item->classes,true)){
            $label=sprintf(__('Toggle submenu for %s','radmanct'),wp_strip_all_tags($item->title));
            $output.='<button type="button" class="rdc-pnl-sub-tgl" data-rdc-pnl-tgl aria-expanded="false" aria-controls="rdc-pnl-sub-'.(int)$item->ID.'" aria-label="'.esc_attr($label).'">'.\RadmanCT\icon('plus').'</button>';
        }
    }
    public function start_lvl(&$output,$depth=0,$args=null){
        $id=$this->parents[$depth]??0;
        $output.='<div id="rdc-pnl-sub-'.$id.'" class="rdc-pnl-sub" hidden><ul class="sub-menu">';
    }
    public function end_lvl(&$output,$depth=0,$args=null){$output.='</ul></div>';}
}
