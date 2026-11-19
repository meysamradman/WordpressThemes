# 18 — WordPress Database & Query Optimization v1.0
**تاریخ:** 2026-11-19 | مشترک؛ مالک روش query، cache داده و هزینه سمت سرور. نحو PHP در 13، کش HTTP در 09، migration در 21. نسخه DB و تنظیم host در Profile/Runbook.

## قانون داده و Query
- ابتدا main query و object/data موجود را مصرف کن. برای تغییر آرشیو با `pre_get_posts`، فقط query هدف را با `$query->is_main_query()` و شرط همان route، frontend/admin درست مشخص کن؛ از تغییر ناخواسته REST/search/editor جلوگیری کن. query مستقل فقط برای بلوک مستقل. Template/renderer بدون query پنهان در هر کارت؛ داده و view-model به آن داده شود.
- `post_type`, `post_status`, `posts_per_page` و ordering قطعی/تساوی مشخص؛ pagination واقعی با لینک crawlable؛ `no_found_rows => true` فقط برای فهرست بدون pagination/count. `fields => 'ids'` وقتی فقط ID لازم است؛ cache priming متا/term/attachment را در صورت مصرف بعدی بی‌دلیل خاموش نکن. `wp_reset_postdata()` بعد از loop فرعیِ `the_post()`؛ از `query_posts()` اجتناب.
- `posts_per_page => -1`، RAND روی مجموعه بزرگ، `meta_query` تو در تو، query در loop و query تکراری header/footer را ممیزی کن. تعداد query جادویی نیست؛ زمان، اندازه داده، memory و cache cold/warm مهم‌اند. حذف query صرفاً چون در trace یک مسیر بی‌مصرف به نظر می‌رسد ممنوع؛ حالت‌های مختلف تست شود.
- از native options/meta/taxonomy و WP CRUD استفاده؛ اتصال موازی PDO/mysqli، prefix ثابت، SQL مختص موتور بدون قرارداد ممنوع. SQL ضروری با `$wpdb->prepare()`، `%d/%s/%f` و `%i` فقط نسخه پشتیبان، نام جدول از WP/allowlist؛ LIKE با `esc_like` سپس prepare. برای شناسه/ORDER BY whitelist و اعتبارسنجی مقدار. nonce جای authorization نیست؛ private/draft/password guards.
- تنها با اندازه‌گیری و نیاز واقعی جدول/index اختصاصی بساز. migration versioned، idempotent، rollback و بکاپ؛ schema change در هر request ممنوع. query plan روی داده نماینده و محیط staging بررسی شود؛ MySQL/MariaDB را با نسخه‌های واقعی ماتریس آزمایش کن؛ اجرای یکی تضمین دیگری نیست.

## مثال محدود (بلوک منتخب بدون صفحه‌بندی)
```php
<?php
$selected = new WP_Query( array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 4,
    'ignore_sticky_posts' => true,
    'no_found_rows'      => true,
) );
while ( $selected->have_posts() ) {
    $selected->the_post();
    // Render existing card with current post ID; no extra loop query.
    get_template_part( 'template-parts/components/card', null, array( 'post_id' => get_the_ID() ) );
}
wp_reset_postdata();
```
مثال فرضی است؛ ساخت فایل یا CPT جدید به معنی الزام نیست. برای عدم تکرار بلوک منتخب در آرشیو ابتدا تصمیم UX/SEO بگیر؛ exclude می‌تواند pagination/count را عوض کند؛ اگر مصوب شد همان query هدف و همه صفحات تست شوند.

## Cache و مقیاس
- Object cache استاندارد، transient نتیجه گران قابل بازسازی و cache HTTP سه لایه مستقل‌اند. cache key با سایت/زبان/filter/نسخه schema و سطح دسترسی درست؛ داده خصوصی در cache عمومی نباشد. invalidation در save/delete/meta/term تغییر مرتبط، نه flush کل سایت. transient ممکن است زود حذف شود؛ false را دقیق بررسی کن. در صورت نیاز stampede/locking طراحی و تست شود.
- dropdown بزرگ admin با search/pagination و capability؛ عملیات bulk با batch محدود؛ job idempotent و retry محدود؛ کار سنگین در frontend ممنوع. index/TTL و limits در Profile با شاهد.
- تست: query count/time، memory، TTFB، page 1/2/last/empty/out-of-range، فارسی، draft/private، roles، cache cold/warm/after edit و رشد داده. Query Monitor فقط محیط مجاز؛ گزارش SQL دارای داده خصوصی منتشر نشود.

## منابع رسمی
- https://developer.wordpress.org/reference/classes/wp_query/
- https://developer.wordpress.org/reference/hooks/pre_get_posts/
- https://developer.wordpress.org/reference/classes/wpdb/
- https://developer.wordpress.org/apis/transients/
- https://developer.wordpress.org/reference/classes/wp_object_cache/
