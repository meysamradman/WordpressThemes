<?php defined('ABSPATH') || exit;
$rows = \RadmanCT\Biography\rows(get_the_ID());
if (!$rows) return;
?>
<section aria-labelledby="rdc-bio-works-title">
 <h2 id="rdc-bio-works-title" data-rdc-en="heading">آثار تولیدشده:</h2>
 <figure class="rdc-bio-works">
  <table aria-labelledby="rdc-bio-works-title">
   <thead><tr><th scope="col">نام قطعه</th><th scope="col">موضوع</th><th scope="col">سال تولید</th></tr></thead>
   <tbody>
    <?php foreach ($rows as $row): ?>
     <tr><th scope="row"><?php echo esc_html($row['title']); ?></th><td><?php echo esc_html($row['topic']); ?></td><td><?php echo esc_html($row['year']); ?></td></tr>
    <?php endforeach; ?>
   </tbody>
  </table>
 </figure>
</section>
