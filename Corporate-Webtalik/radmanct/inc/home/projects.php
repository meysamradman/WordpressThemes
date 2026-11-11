<?php
namespace RadmanCT\Home;
defined('ABSPATH')||exit;
function selected_work():array {
 return apply_filters('radmanct/home/selected-work',array());
}
