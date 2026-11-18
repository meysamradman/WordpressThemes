<?php defined('ABSPATH')||exit;
$image=\RadmanCT\MusicHome\image((int)get_post_thumbnail_id(),'full',[
 'alt'=>'','loading'=>'lazy','decoding'=>'async','sizes'=>'(max-width: 1348px) 95vw, 1300px',
]);
if($image): ?>
 <figure class="rdc-home-promo"><?php echo $image; ?></figure>
<?php endif; ?>
