<?php
namespace RadmanCT\Biography;
defined('ABSPATH') || exit;

const WORKS_META = '_rdc_bio_works';

function rows(int $id): array {
 $rows = get_post_meta($id, WORKS_META, true);
 return is_array($rows) ? $rows : [];
}

if (is_admin()) require_once dirname(__DIR__).'/admin/biography.php';
