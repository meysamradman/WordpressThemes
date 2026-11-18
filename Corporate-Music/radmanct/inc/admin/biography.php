<?php
namespace RadmanCT\Biography;
defined('ABSPATH') || exit;

function is_biography(\WP_Post $post): bool {
 return $post->post_type === 'page' && $post->post_name === 'about-us';
}

function row_fields(array $row = []): void { ?>
 <tr>
  <td><input class="widefat" type="text" name="rdc_bio_work_title[]" aria-label="نام قطعه" value="<?php echo esc_attr($row['title'] ?? ''); ?>"></td>
  <td><input class="widefat" type="text" name="rdc_bio_work_topic[]" aria-label="موضوع" value="<?php echo esc_attr($row['topic'] ?? ''); ?>"></td>
  <td><input class="widefat" type="text" inputmode="numeric" name="rdc_bio_work_year[]" aria-label="سال تولید" value="<?php echo esc_attr($row['year'] ?? ''); ?>"></td>
  <td><button type="button" class="button" data-rdc-work-remove>حذف ردیف</button></td>
 </tr>
<?php }

add_action('add_meta_boxes_page', static function(\WP_Post $post): void {
 if (!is_biography($post)) return;
 add_meta_box('rdc-bio-works', 'آثار تولیدشده', static function(\WP_Post $page): void {
  wp_nonce_field('rdc_bio_works_'.$page->ID, 'rdc_bio_nonce');
  ?>
  <div data-rdc-works>
   <p>برای هر اثر یک ردیف وارد کنید و در پایان برگه را به‌روزرسانی کنید.</p>
   <table class="widefat striped">
    <thead><tr><th scope="col">نام قطعه</th><th scope="col">موضوع</th><th scope="col">سال تولید</th><th scope="col">عملیات</th></tr></thead>
    <tbody><?php foreach (rows($page->ID) ?: [[]] as $row) row_fields($row); ?></tbody>
   </table>
   <template><?php row_fields(); ?></template>
   <p><button class="button button-secondary" type="button" data-rdc-work-add>+ افزودن اثر</button></p>
   <input type="hidden" name="rdc_bio_works_end" value="1">
  </div>
  <?php
 }, 'page', 'normal', 'high');
});

add_action('admin_enqueue_scripts', static function(string $hook): void {
 $page = get_post();
 if ($hook === 'post.php' && $page && is_biography($page)) \RadmanCT\Assets\script('admin/biography-editor');
});

add_action('save_post_page', static function(int $id, \WP_Post $page): void {
 if (!is_biography($page) || wp_is_post_revision($id) || wp_is_post_autosave($id) || !current_user_can('edit_post', $id)) return;
 $nonce = $_POST['rdc_bio_nonce'] ?? null;
 if (($_POST['rdc_bio_works_end'] ?? '') !== '1' || !is_string($nonce) || !wp_verify_nonce(wp_unslash($nonce), 'rdc_bio_works_'.$id)) return;
 $columns = [];
 foreach (['title', 'topic', 'year'] as $field) {
  $values = $_POST['rdc_bio_work_'.$field] ?? [];
  if (!is_array($values)) return;
  $columns[$field] = wp_unslash($values);
 }
 $rows = [];
 foreach ($columns['title'] as $index => $value) {
  $row = [];
  foreach ($columns as $field => $values) {
   if (!is_string($values[$index] ?? null)) return;
   $row[$field] = sanitize_text_field($values[$index]);
  }
  if (implode('', $row) !== '') $rows[] = $row;
 }
 update_post_meta($id, WORKS_META, wp_slash($rows));
}, 10, 2);
