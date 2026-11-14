<?php defined('ABSPATH')||exit;$tracks=array_slice($args['tracks']??array(),0,4); ?>
<svg class="rdc-pt__orbs" viewBox="0 0 1920 1080" aria-hidden="true" focusable="false">
 <?php if(count($args['matrix']??array())===2):for($row=0;$row<5;$row++):for($column=0;$column<3;$column++): ?>
 <circle class="rdc-pt__matrix" cx="<?php echo esc_attr((float)$args['matrix'][0]+$column*17); ?>" cy="<?php echo esc_attr((float)$args['matrix'][1]+$row*17); ?>" r="1.3"/>
 <?php endfor;endfor;endif; ?>
 <?php foreach($tracks as $track): ?>
 <path<?php if(!empty($args['draw']))echo ' class="rdc-pt__line"'; ?> d="<?php echo esc_attr($track[0]); ?>" data-orbit-pipe data-orbit-duration="<?php echo (int)$track[1]; ?>" fill="none"<?php if(empty($args['draw']))echo ' stroke="none"'; ?>/>
 <?php endforeach; ?>
</svg>
<?php foreach($tracks as $index=>$track): ?>
<span class="rdc-pt__dot" data-orbit-track="<?php echo (int)$index; ?>" data-orbit-phase="<?php echo esc_attr($track[2]); ?>" aria-hidden="true"></span>
<?php endforeach; ?>
<?php foreach(array_slice($args['nodes']??array(),0,6) as $node): ?>
<span class="rdc-pt__node" style="left:<?php echo esc_attr(round((float)$node[0]/1920*100,4)); ?>%;top:<?php echo esc_attr(round((float)$node[1]/1080*100,4)); ?>%" aria-hidden="true"></span>
<?php endforeach; ?>
