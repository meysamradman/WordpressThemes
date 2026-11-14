<?php defined('ABSPATH')||exit; ?>
<dialog class="rdc-lb rdc-inv" aria-label="نمایش تصاویر">
 <button type="button" class="rdc-lb__close" data-rdc-lb-cls aria-label="بستن"><?php echo \RadmanCT\icon('x'); ?></button>
 <div class="rdc-lb__stage"><img alt="" decoding="async"><button type="button" class="rdc-lb__previous" data-rdc-lb-prev aria-label="عکس قبلی"><?php echo \RadmanCT\icon('chevron-right'); ?></button><button type="button" class="rdc-lb__next" data-rdc-lb-next aria-label="عکس بعدی"><?php echo \RadmanCT\icon('chevron-left'); ?></button></div>
 <div class="rdc-lb__info"><output data-rdc-lb-count aria-live="polite"></output><p data-rdc-lb-caption></p><p data-rdc-lb-status role="status" aria-live="polite"></p></div>
</dialog>
