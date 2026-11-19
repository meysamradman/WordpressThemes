# قرارداد WordPress

## سازگاری MySQL و MariaDB

قالب باید روی WordPress جدید با هر دو خانوادهٔ MySQL و MariaDB کار کند. baseline پیشنهادی هاست طبق [نیازمندی رسمی WordPress](https://wordpress.org/about/requirements/) برابر MySQL 8.4+ یا MariaDB 10.11+ است؛ نسخهٔ WordPress نصب‌شده و افزونه‌های سایت نیز باید با هاست سازگار باشند.

خواندن و نوشتن از `WP_Query`، APIهای metadata/options/taxonomy و در صورت نیاز واقعی همان `$wpdb` وردپرس انجام شود. اتصال موازی PDO/mysqli، مشخصات اتصال داخل قالب، نام جدول یا prefix ثابت، و SQL/توابع مختص یک موتور اضافه نشوند. SQL ضروری باید با `$wpdb->prepare()` و نام جدول/charset/collation از WordPress ساخته شود. انتخاب موتور، پشتیبان‌گیری و تبدیل دیتابیس مسئولیت محیط WordPress/هاست است؛ قالب نباید با تشخیص موتور داده را بازنویسی یا مهاجرت خودکار اجرا کند. [مرجع wpdb](https://developer.wordpress.org/reference/classes/wpdb/).

تست واقعی روی یک موتور، اثبات اجرای موتور دیگر نیست. محیط محلی اصلی MySQL 8.4.13 LTS با image ثابت و collation مشترک utf8mb4_unicode_520_ci است. سازگاری MariaDB نیز قرارداد محصول باقی می‌ماند؛ داده/volume قبلی MariaDB نگه داشته شود و هیچ موتور دیگری روی فایل‌های همان volume اجرا نشود. تغییر موتور فقط با بکاپ و بازیابی منطقی در volume مستقل انجام می‌شود.

ابزار `tools/local-wordpress/check-database.php` فقط برای دیتابیس آزمایشیِ جدا با نام `rdct_check` و متغیر `RDCT_DB_CHECK=1` است؛ روی دیتابیس مشتری اجرا نشود. حالت `RDCT_DB_INSTALL=1` فقط دیتابیس خالی را نصب می‌کند؛ اجرای عادی پس از نصب، فعال‌سازی قالب، متن فارسی، نمونه‌کار، دسته‌بندی، ترتیب گالری، ویدئو، nonce و options را بررسی می‌کند. برای بررسی هر موتور، همان نسخهٔ WordPress/PHP و سورس قالب با پیشوند جدول متفاوت استفاده شود؛ دیتابیس، شبکه و کانتینر موقت پس از بررسی حذف شوند. آزمون سازگاری جای benchmark یا بررسی کامل محیط هاست را نمی‌گیرد.

- هستهٔ WordPress و مدیریت بومی برای نوشته‌ها، رسانه، فهرست و نمونه‌کار حفظ می‌شوند. حذف پنل قالب به معنی حذف مدیریت WordPress نیست.
- ثبت نمونه‌کار و متادیتای رسانه داخل `inc/content/` قالب است؛ افزونهٔ محتوای جدا یا سوییچ ماژول نداریم. محیط فعلی دیتابیس تازه دارد و شناسه‌ها با پروژهٔ قبلی یکی فرض نشوند.
- محتوا با editor بومی و API وردپرس مصرف شود. صفحهٔ رمزدار/خصوصی، تصویر و excerpt محافظت‌شده را عمومی نکند؛ خروجی با esc_html/esc_attr/esc_url و HTML مجاز متناسب با محل چاپ شود.
- نوشتن در مدیریت به capability + nonce نیاز دارد؛ endpoint سفارشی بدون قرارداد مجوز و محدودیت ساخته نشود. ورود مدیر را WordPress مدیریت می‌کند. در صورت نیاز به تغییر نشانی ورود روی هاست، AIOS مالک این تنظیم است و قالب مسیر ورود مستقلی نمی‌سازد. فرم عضویت عمومی فعلاً نداریم.
- جست‌وجوی عمومی با `WP_Query` و پارامتر `s` انجام می‌شود. عبارت خالی یا ورودی‌ای که هسته به‌دلیل نوع یا طول نامعتبر خالی کرده است، نتیجهٔ خالی دارد. عبارت فقط در محل چاپ با `esc_html` برای عنوان و `esc_attr` برای فیلد فرم escape می‌شود. [مرجع escaping وردپرس](https://developer.wordpress.org/apis/security/escaping/).
- در علی اسدی، مقصدهای وبِ شبکه‌های اجتماعی و پیام‌رسان هماهنگی با `target="_blank"` و `rel="noopener noreferrer"` باز می‌شوند. تماس شمارهٔ بین‌المللی را از `Client\\booking()` در لینک بومی `tel:` می‌گیرد و شکل محلیِ گروه‌بندی‌شده را نمایش می‌دهد. شمارهٔ عمومی در HTML برای تماس در دسترس است و فاصله‌گذاری صرفاً خوانایی را بهتر می‌کند.
- نصب محلی فقط loopback است؛ ایمیل و HTTP خارجی محدودند. رمزها، uploads، دیتابیس و backup وارد Git نشوند. انتقال سرور شامل رسانه و دیتابیسِ مجاز به شکل جدا از سورس است.
- PHP 8.x و منابع محلی مطابق قرارداد دارایی‌های architecture.md؛ Node فقط build. srcset، ابعاد ذاتی، بارگذاری انتخابی رسانه و افکت، cleanup و reduced-motion حفظ شوند.

- برچسب‌ها در sidebar و جزئیات نوشته نمایش داده نمی‌شوند؛ حذف نمایشی مجوز حذف دادهٔ taxonomy نیست. سئوی پایه در `inc/seo.php` است: title بومی WordPress، توضیح از خلاصه/محتوای عمومی یا توضیح دسته، canonical مستقل هر صفحهٔ آرشیو و صفحه‌بندی، robots بومی با حفاظت جستجو/preview/محتوای خصوصی. توضیح خالی یا محتوای رمزدار چاپ نشود. خلاصهٔ بومی برگه برای متن متا قابل ویرایش است. با افزونهٔ سئوی شناخته‌شده یا فیلتر `radmanct/seo/enabled` خروجی قالب واگذار شود؛ meta تکراری و اتصال Google/سرویس خارجی ساخته نشود. noindex محیط محلی حفظ شود.

SEO ownership: when Yoast is active it owns SEO title/description, canonical, social metadata, schema and sitemap output. Put Search Console verification in Yoast Site connections once. Analytics is a separate integration, added only when the owner supplies the chosen ID/provider; do not install the same tag through both theme and another injector. Keep wp_head/wp_body_open/wp_footer intact and local noindex unchanged.

Yoast Site connections accepts verification tokens, not Analytics scripts. Its Site Kit dashboard integration is distinct from installing the tracking tag; let the selected Analytics integration own tag placement. Rank Math can place the tag through its Analytics module's Install Analytics Code option. Enable only one tracking-code owner and verify actual tag output and collection after real IDs are configured; do not insert placeholder IDs or enable external tracking locally by default.

This ownership includes custom PHP pages backed by WordPress pages. Edit their SEO fields in the plugin; the theme must not fill empty plugin descriptions or override saved SEO values. The theme description fallback runs only without an active recognized SEO plugin. Plugin content-analysis scores may omit template-owned copy/media; maintain semantic rendered content, headings and image alternatives in their actual template/media owners.

The fallback description filter radmanct/seo/description consumes public client page/hero copy only after privacy checks and only when the saved excerpt/content is empty. Plain H1 headings live in template-parts/ui/content-heading.php for content/archive templates without the media Page Title. Keep exactly one page H1 and preserve h2/h3 section/card hierarchy.


Music edition SEO: public track/album/clip and their native taxonomy/archive/detail routes use the same Yoast ownership as WordPress pages. With a recognized SEO plugin active, inc/seo.php emits no competing description/canonical or schema; edit the real fields in the plugin. Without it, the native archive fallback supports /music/, /videos/, /gallery/ and their paginated canonicals, using post-type labels for empty archive descriptions. Keep one content H1 and local noindex. wp_head/wp_body_open/wp_footer remain the integration points; do not repeat snippets in custom templates. Search Console verification belongs to the chosen SEO integration once. Analytics requires the owner's actual provider/ID and one tag owner; no placeholder IDs, tracking script, automatic external collection or duplicate theme/plugin injection are enabled by this setup. Actual Analytics collection is not verified until a real integration is configured.

On public clip detail pages, the verified clip can extend Yoast's existing graph with one VideoObject, or use the theme fallback when there is no SEO plugin. Existing VideoObject pieces take precedence. Use the visible player source, an allowed thumbnail and a real publication date. The Aparat import preserves the source date in the WordPress publication date. A manually entered Aparat link by itself has no verified publication date for this purpose.

Album photo pages complement the same album and share its base canonical. The first page uses the album URL. Invalid, redundant or out-of-range gallery_page values redirect to the normalized album URL or last valid photo page. AJAX archives keep working per-URL HTML and update the validated final same-origin URL, title and SEO head together.

## قواعد بستهٔ هاست

- [استاندارد مشترک تحویل وردپرس](../../WEB-DEVELOPMENT-STANDARDS/shared/08-BUILD-AND-VERSIONS.md#تحویل-وردپرس-بر-اساس-دامنه) برای دامنهٔ `aliasadioriginal.ir` اجرا می‌شود. بستهٔ نصب تازه، SQL آمادهٔ دامنه و `public_html.zip` را جداگانه دارد. بعد از بررسی بستهٔ جدید، خروجی قبلی در پوشهٔ تحویل جایگزین می‌شود و بکاپ‌های بازیابی در مسیر جدا می‌مانند.
- برای سایت نصب‌شده فقط فایل‌های موردنیازِ به‌روزرسانی بسته‌بندی شوند. برای تغییرات قالب، ZIP فقط مسیر `wp-content/themes/radmanct/` و فایل‌های مصرف‌شدهٔ manifest را داشته باشد. دیتابیس، رسانه، هسته، افزونه‌ها و قواعد `.htaccess` هاست جدا بمانند. هنگام تغییر خروجی قالب نیازی به ایمپورت مجدد SQL نیست.
- برای اصلاح محتوای سایت فعال، فایل اصلاحی فقط روی دادهٔ تعیین‌شده و مطابق بکاپ جاری هاست اجرا می‌شود. نمونهٔ ترتیب آهنگ‌ها فقط `post_date` و `post_date_gmt` را برای ۲۴ رکورد تطبیق‌یافته در `rdct_posts` تغییر می‌دهد و تنظیمات امنیت، Yoast و نوشته‌های تازه را حفظ می‌کند.
- `wp-config.php` روی هاست مدیریت می‌شود. آن را همراه نمونهٔ پیکربندی، متغیرهای محیطی و رمزهای محیط توسعه از ZIP تحویل حذف کنید. فایل پیکربندی فعلی سرور هنگام به‌روزرسانی حفظ شود.
- داخل ZIP مجوز یونیکس پوشه‌ها `0755` و فایل‌ها `0644` باشد. بعد از استخراج، مالکیت و مجوز مؤثر روی هاست با AIOS یا فایل‌منیجر بررسی شود. برای `wp-config.php` مجوز پیشنهادی هاست یا AIOS را همراه با امکان خواندن فایل توسط PHP بررسی کنید. [مرجع مجوزهای وردپرس](https://developer.wordpress.org/advanced-administration/server/file-permissions/)

Frontend implementation and staged review: [frontend-standard.md](frontend-standard.md). Release decisions for the audit are recorded in [release-audit.md](release-audit.md).
