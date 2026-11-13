<?php
namespace RadmanLibrary\Designs\ButtonRoll;
defined('ABSPATH')||exit;

function data():array {
 return apply_filters('rdl/button-roll/data',array(
  'title'=>'یک اشاره، یک حرکت.',
  'description'=>'دکمه با متن چرخشی و فلش مورب؛ همان طراحی منتخب Radman CT در زمینهٔ روشن و تیره.',
  'label'=>'شروع یک پروژه',
  'url'=>home_url('/projects/'),
  'icon'=>'arrow-up-right',
 ));
}
