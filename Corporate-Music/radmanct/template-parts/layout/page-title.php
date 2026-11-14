<?php
defined('ABSPATH')||exit;
if(!\RadmanCT\PageTitle\is_used())return;
$title_data=\RadmanCT\PageTitle\data($args??array());
get_template_part('template-parts/layout/titles/'.$title_data['type'],null,$title_data);
