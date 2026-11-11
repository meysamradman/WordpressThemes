# قرارداد WordPress

## سازگاری MySQL و MariaDB

قالب باید روی WordPress جدید با هر دو خانوادهٔ MySQL و MariaDB کار کند. baseline پیشنهادی هاست طبق [نیازمندی رسمی WordPress](https://wordpress.org/about/requirements/) برابر MySQL 8.0+ یا MariaDB 10.11+ است؛ نسخهٔ WordPress نصب‌شده و افزونه‌های سایت نیز باید با هاست سازگار باشند.

خواندن و نوشتن از `WP_Query`، APIهای metadata/options/taxonomy و در صورت نیاز واقعی همان `$wpdb` وردپرس انجام شود. اتصال موازی PDO/mysqli، مشخصات اتصال داخل قالب، نام جدول یا prefix ثابت، و SQL/توابع مختص یک موتور اضافه نشوند. SQL ضروری باید با `$wpdb->prepare()` و نام جدول/charset/collation از WordPress ساخته شود. انتخاب موتور، پشتیبان‌گیری و تبدیل دیتابیس مسئولیت محیط WordPress/هاست است؛ قالب نباید با تشخیص موتور داده را بازنویسی یا مهاجرت خودکار اجرا کند. [مرجع wpdb](https://developer.wordpress.org/reference/classes/wpdb/).

تست واقعی روی یک موتور، اثبات اجرای موتور دیگر نیست. محیط محلی اصلی MariaDB 11.4 است و داده/volume آن برای آزمون موتور دیگر جایگزین یا reset نشود.

ابزار `tools/local-wordpress/check-database.php` فقط برای دیتابیس آزمایشیِ جدا با نام `rdct_check` و متغیر `RDCT_DB_CHECK=1` است؛ روی دیتابیس مشتری اجرا نشود. حالت `RDCT_DB_INSTALL=1` فقط دیتابیس خالی را نصب می‌کند؛ اجرای عادی پس از نصب، فعال‌سازی قالب، متن فارسی، نمونه‌کار، دسته‌بندی، ترتیب گالری، ویدئو، nonce و options را بررسی می‌کند. برای بررسی هر موتور، همان نسخهٔ WordPress/PHP و سورس قالب با پیشوند جدول متفاوت استفاده شود؛ دیتابیس، شبکه و کانتینر موقت پس از بررسی حذف شوند. آزمون سازگاری جای benchmark یا بررسی کامل محیط هاست را نمی‌گیرد.

- هستهٔ WordPress و مدیریت بومی برای نوشته‌ها، رسانه، فهرست و نمونه‌کار حفظ می‌شوند. حذف پنل قالب به معنی حذف مدیریت WordPress نیست.
- ثبت نمونه‌کار و متادیتای رسانه داخل `inc/content/` قالب است؛ افزونهٔ محتوای جدا یا سوییچ ماژول نداریم. محیط فعلی دیتابیس تازه دارد و شناسه‌ها با پروژهٔ قبلی یکی فرض نشوند.
- محتوا با editor بومی و API وردپرس مصرف شود. صفحهٔ رمزدار/خصوصی، تصویر و excerpt محافظت‌شده را عمومی نکند؛ خروجی با esc_html/esc_attr/esc_url و HTML مجاز متناسب با محل چاپ شود.
- نوشتن در مدیریت به capability + nonce نیاز دارد؛ endpoint سفارشی بدون قرارداد مجوز و محدودیت ساخته نشود. ورود مدیر از مسیر استاندارد wp-admin است؛ فرم عضویت عمومی فعلاً نداریم.
- نصب محلی فقط loopback است؛ ایمیل و HTTP خارجی محدودند. رمزها، uploads، دیتابیس و backup وارد Git نشوند. انتقال سرور شامل رسانه و دیتابیسِ مجاز به شکل جدا از سورس است.
- PHP 8.x و منابع محلی مطابق قرارداد دارایی‌های architecture.md؛ Node فقط build. srcset، ابعاد ذاتی، بارگذاری انتخابی رسانه و افکت، cleanup و reduced-motion حفظ شوند.

- برچسب‌ها در sidebar و جزئیات نوشته نمایش داده نمی‌شوند؛ حذف نمایشی مجوز حذف دادهٔ taxonomy نیست. سئوی پایه در `inc/seo.php` است: title بومی WordPress، توضیح از خلاصه/محتوای عمومی یا توضیح دسته، canonical مستقل هر صفحهٔ آرشیو و صفحه‌بندی، robots بومی با حفاظت جستجو/preview/محتوای خصوصی. توضیح خالی یا محتوای رمزدار چاپ نشود. خلاصهٔ بومی برگه برای متن متا قابل ویرایش است. با افزونهٔ سئوی شناخته‌شده یا فیلتر `radmanct/seo/enabled` خروجی قالب واگذار شود؛ meta تکراری و اتصال Google/سرویس خارجی ساخته نشود. noindex محیط محلی حفظ شود.

SEO ownership: when Yoast is active it owns SEO title/description, canonical, social metadata, schema and sitemap output. Put Search Console verification in Yoast Site connections once. Analytics is a separate integration, added only when the owner supplies the chosen ID/provider; do not install the same tag through both theme and another injector. Keep wp_head/wp_body_open/wp_footer intact and local noindex unchanged.

Yoast Site connections accepts verification tokens, not Analytics scripts. Its Site Kit dashboard integration is distinct from installing the tracking tag; let the selected Analytics integration own tag placement. Rank Math can place the tag through its Analytics module's Install Analytics Code option. Enable only one tracking-code owner and verify actual tag output and collection after real IDs are configured; do not insert placeholder IDs or enable external tracking locally by default.

This ownership includes custom PHP pages backed by WordPress pages. Edit their SEO fields in the plugin; the theme must not fill empty plugin descriptions or override saved SEO values. The theme description fallback runs only without an active recognized SEO plugin. Plugin content-analysis scores may omit template-owned copy/media; maintain semantic rendered content, headings and image alternatives in their actual template/media owners.

The fallback description filter radmanct/seo/description consumes public client page/hero copy only after privacy checks and only when the saved excerpt/content is empty. Plain H1 headings live in template-parts/ui/content-heading.php for content/archive templates without the media Page Title. Keep exactly one page H1 and preserve h2/h3 section/card hierarchy.
