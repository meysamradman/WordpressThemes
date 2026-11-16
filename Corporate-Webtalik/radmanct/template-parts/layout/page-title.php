<?php
defined('ABSPATH')||exit;
if(!\RadmanCT\PageTitle\is_used()){
 if(\RadmanCT\CompactTitle\is_used())get_template_part('template-parts/layout/titles/compact');
 return;
}
$title_data=\RadmanCT\PageTitle\data($args??array());
get_template_part('template-parts/layout/titles/cover',null,$title_data);
