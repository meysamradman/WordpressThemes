<?php defined('ABSPATH')||exit; ?>
<div data-rdl-prd>
<p><label for="rdl-prd-code">کد محصول</label><input class="widefat" id="rdl-prd-code" name="rdl_prd[code]" maxlength="160" value="<?php echo esc_attr($data['code']); ?>"></p>
<p><label for="rdl-prd-mode">نمایش قیمت</label> <select id="rdl-prd-mode" name="rdl_prd[pricing][mode]"><option value="contact" <?php selected($data['pricing']['mode'],'contact'); ?>>بدون قیمت / تماس</option><option value="price" <?php selected($data['pricing']['mode'],'price'); ?>>قیمت نمایشی</option></select></p>
<p><label for="rdl-prd-price">متن قیمت (اختیاری، بدون فروش آنلاین)</label><input class="widefat" id="rdl-prd-price" name="rdl_prd[pricing][text]" maxlength="80" value="<?php echo esc_attr($data['pricing']['text']); ?>"></p>
<p>گالری محصول، حداکثر ۲۰ تصویر؛ ترتیب در پنجرهٔ رسانه قابل تغییر است.</p><input type="hidden" data-rdl-prd-gal name="rdl_prd[gallery]" value="<?php echo esc_attr(implode(',',$data['gallery'])); ?>">
<div data-rdl-prd-pv><?php foreach($data['gallery'] as $id)if(\RadmanLibrary\Catalog\image($id))echo wp_get_attachment_image($id,'thumbnail'); ?></div>
<p><button type="button" class="button" data-rdl-prd-pick>انتخاب / مرتب‌سازی تصاویر</button> <button type="button" class="button-link-delete" data-rdl-prd-clear>پاک‌کردن انتخاب</button></p><p data-rdl-prd-msg role="status"></p>
<h3>مشخصات فنی</h3><div data-rdl-prd-spec><?php foreach($data['specifications'] as $index=>$row): ?><p data-rdl-prd-row>
<input type="hidden" name="rdl_prd[specifications][<?php echo (int)$index; ?>][id]" value="<?php echo esc_attr($row['id']); ?>">
<?php foreach(['label'=>'عنوان','value'=>'مقدار','unit'=>'واحد'] as $key=>$label): ?><label><?php echo esc_html($label); ?> <input name="rdl_prd[specifications][<?php echo (int)$index; ?>][<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($row[$key]); ?>"></label> <?php endforeach; ?><button type="button" class="button-link-delete" data-rdl-prd-del>حذف ردیف</button></p><?php endforeach; ?></div><button type="button" class="button" data-rdl-prd-add>افزودن مشخصه</button>
<p class="description">نام، معرفی، تصویر اصلی، متن و دسته‌بندی‌ها در بخش‌های بومی وردپرس ثبت می‌شوند. تنظیمات سراسری، سبد و پرداخت وجود ندارد.</p></div>
