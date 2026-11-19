# PHP 8.4+ Development Standard — v1.1

> بازبینی بسته 1.3 — 2026-11-19؛ نام مسیر برای سازگاری ارجاع‌های موجود حفظ شده است. تاریخچه در CHANGELOG-v1.3.md.

**بازبینی منابع:** 2026-11-18 | **مخاطب:** Codex / توسعه‌دهنده | **دامنه:** کد PHP اختصاصی وب‌سایت‌ها، قالب‌ها و افزونه‌های وردپرس مشتریان. مستقل از برند و صفحه‌ساز. این سند استاندارد کدنویسی است، نه دستور تنظیم هاست یا نصب افزونه.

**MUST:** قرارداد الزامی تیم؛ **SHOULD:** پیش‌فرض قابل تغییر با دلیل و تست. ابتدا سورس و نسخه واقعی پروژه را بررسی کن؛ معماری موجود را بی‌دلیل بازنویسی نکن.

## 1. نسخه‌ها و سیاست سازگاری

| فناوری | سیاست | وضعیت بررسی 2026-11-18 |
|---|---|---|
| PHP 8.4 | حداقل مجاز برای کد جدید؛ آخرین patch موجود | Active تا 2026-12-31؛ Security تا 2028-12-31 |
| PHP 8.5 | هدف ترجیحی اجرا پس از تست کل Stack؛ کد مشترک همچنان روی 8.4 اجرا شود | Active تا 2027-12-31؛ Security تا 2029-12-31 |
| PHP 8.6 و بالاتر | بدون فرض سازگاری؛ بعد از انتشار پایدار، بررسی migration guide و CI اضافه شود | خارج از ماتریس فعلی |
| WordPress | آخرین stable patch سازگار؛ نسخه نصب‌شده در profile ثبت شود | 7.1.4 (2026-10-28)؛ 7.2 RC1 آزمایشی (2026-11-17). ادعای 7.3.2 در آرشیو رسمی تأیید نشد. |

- سازگاری رسمی Core وردپرس 7.1 با PHP 8.4 و 8.5 به معنی سازگاری همه قالب‌ها، افزونه‌ها و extensionها نیست. نسخه دقیق production/CI و حداقل WordPress را از profile بگیر؛ PHP حداقل 8.4 تصمیم تیم است، نه حداقل اجباری WordPress.
- کد مشترک باید با 8.4 parse و اجرا شود. از syntax مخصوص 8.5 مانند pipe operator در کد مشترک استفاده نکن؛ `if ( PHP_VERSION_ID ... )` در همان فایل راه‌حل parse syntax ناسازگار نیست. استفاده از قابلیت جدیدتر فقط با تصمیم نسخه و فایل سازگار جدا.
- به نسخه ثابت patch در این استاندارد وابسته نشو. تاریخ و نسخه‌ها را هنگام ایجاد پروژه و هر ارتقا دوباره از PHP.net، WordPress release archive، compatibility matrix و release notes وابستگی‌ها بررسی کن.
- در theme header `style.css` و plugin header فیلد `Requires PHP: 8.4` و `Requires at least` مطابق حداقل واقعی WP ثبت شود. در Composer در صورت استفاده `"php": "^8.4"`؛ این constraint به معنی آزمون همه نسخه‌های آینده نیست.
- پروژه legacy زیر 8.4 را بدون بررسی، پشتیبان و تأیید ارتقا نده. گزارش بده و migration plan جدا ایجاد کن. نه PHP قدیمی را برای همه پروژه‌ها هدف بگیر و نه compatibility را به زور دور بزن.

## 2. معماری، خوانایی و استاندارد نوشتن

- برای کد WordPress از WordPress Coding Standards (WPCS) و PHP_CodeSniffer استفاده کن؛ convention وردپرس (indent با tab، فاصله‌گذاری، snake_case برای توابع و متغیرها، نام کلاس خوانا، `array()` در کد تابع WPCS) را با PSR-12 بی‌قاعده مخلوط نکن. برای پروژه PHP مستقل، سبک repository و استاندارد انتخابی خودش (مثلاً PSR-12) رعایت شود. قواعد امنیت و سازگاری WP حتی در سبک متفاوت باقی می‌مانند.
- UTF-8 بدون BOM، `<?php`، پایان `?>` در فایل PHP خالص حذف شود. templateهای ترکیبی HTML/PHP از syntax خوانا استفاده کنند. از echo رشته‌های عظیم HTML، include پویا از ورودی کاربر و فایل `functions.php` غول‌پیکر اجتناب کن.
- Namespace یا prefix یکتا برای کد جدید؛ توابع public و hooks/options/CPT/REST handleها نام قابل تشخیص داشته باشند. نام hookهای موجود را بدون بررسی consumer تغییر نده. کد کوچک را بی‌دلیل به framework یا چندین لایه کلاس تبدیل نکن.
- type declarations و return types روشن؛ nullable صریح `?string` یا `string|null`، typed properties و پشتیبانی `readonly` در مدل‌های مناسب. برای `mixed` دلیل قراردادی داشته باش. PHPDoc برای array shape، generics تحلیلی، مقادیر خارجی و قراردادهای غیرقابل بیان با type؛ توضیح بدیهیات لازم نیست.
- `declare(strict_types=1);` در فایل منطق جدید و مستقل در صورت سازگاری؛ به callbackهای WP با ورودی‌های متنوع type سخت‌گیرانه تحمیل نکن. ورودی خارجی را قبل از ورود به منطق normalize/validate کن. strict_types در محل فراخوانی روی coercion نوع scalar اثر دارد؛ جایگزین اعتبارسنجی نیست.
- `===` / `!==`، `in_array(..., true)` و guard clause برای وضعیت‌های خروج؛ مقدار `false`، `0`، `''` و `null` را بدون دلیل یکی ندان. `??` فقط وقتی fallback منطقی است. `match` و enum برای وضعیت محدود در صورت بهبود خوانایی؛ قابلیت‌های تازه زبان اجباری نیستند.
- dynamic property ایجاد نکن (از 8.2 deprecated با استثناهای زبان)؛ property تعریف‌شده؛ implicit nullable مانند `string $x = null` را به `?string $x = null` تبدیل کن (8.4). در 8.5 از `null` به‌عنوان کلید آرایه یا ورودی `array_key_exists()` اجتناب کن؛ migration guides برای موارد بیشتر. از API قدیمی/حذف‌شده، `@` برای پنهان‌کردن خطا، `eval`، unserialize داده غیرقابل اعتماد و error suppression سراسری استفاده نکن.
- خطاها را در مرز مناسب به `WP_Error`/پاسخ HTTP یا exception قابل مدیریت تبدیل کن؛ exception را بی‌دلیل swallow نکن. داده حساس در پیام عمومی یا log قرار نگیرد. `is_wp_error` و قرارداد `false`/`null` APIها حتماً بررسی شوند.

## 3. معماری وردپرس و مرز مسئولیت

- قالب مسئول نمایش و templateها است؛ CPT، taxonomy، business logic، REST endpoints و داده‌ای که پس از تغییر قالب باید باقی بماند، ترجیحاً در plugin اختصاصی سایت. MU-plugin فقط برای قابلیت واقعاً لازم و همیشه فعال، با مسیر update مشخص. هیچ هسته یا افزونه vendor مستقیم ویرایش نشود.
- از Template Hierarchy (`front-page.php`, `home.php`, `page.php`, `single.php`, `archive.php`, `404.php`, `index.php` و انواع اختصاصی در صورت نیاز)، `get_template_part()` و template tags استفاده کن. تنها فایل‌های مورد نیاز ساخته شوند. قرارداد دقیق classic/block و صفحه‌ساز در profile جداگانه.
- `functions.php` ترکیب‌کننده ماژول‌ها؛ `after_setup_theme` برای supports/menu، `init` برای ثبت type/taxonomy، `wp_enqueue_scripts` برای دارایی frontend، `admin_enqueue_scripts` محدود به صفحه مدیریت مورد نیاز، `rest_api_init` برای REST. query main را در `pre_get_posts` با guard مناسب frontend/main query تغییر بده. `flush_rewrite_rules()` در هر درخواست ممنوع؛ فقط activation/migration کنترل‌شده در زمان مناسب.
- از `wp_head()`, `wp_body_open()`, `wp_footer()`, `language_attributes()`, `body_class()` در قالب classic حسب محل صحیح استفاده کن؛ hook یا wrapper را دوباره تولید نکن. خروجی سئو فقط یک مالک؛ جزئیات سند 01.
- متن frontend به صورت SSR قابل خواندن باشد؛ لینک و pagination استاندارد قبل از JS. PHP داده معتبر و markup می‌سازد؛ Vanilla JS رفتار و AJAX، GSAP حرکت، چرخه Loading طبق قرارداد مسیر پروژه (06 برای قالب PHP اختصاصی). روی PHP لودینگ ساختگی یا انتظار GSAP نساز.
- از WordPress asset API با handle یکتا و dependencies درست استفاده کن؛ کد صفحه فقط همان‌جا enqueue شود. نسخه‌بندی asset با build manifest در صورت استفاده از Vite؛ core dependency را حدسی حذف نکن. جزئیات Build در [08](08-BUILD-AND-VERSIONS.md).

## 4. امنیت — در هر ورودی و خروجی

| سطح | اقدام لازم |
|---|---|
| Request | شکل داده، نوع، طول، enum/allowlist و محدوده را validate؛ برای superglobal وردپرس طبق قرارداد `wp_unslash()` و سپس sanitize متناسب. مقادیر آرایه را به تابع متن رشته‌ای نسپار. |
| ذخیره | API استاندارد WP؛ normalize و validate قبل از ذخیره؛ sanitize مناسب نوع مثل `sanitize_text_field()` یا `sanitize_email()` (با `is_email()` برای اعتبار واقعی). داده را به قصد ذخیره HTML-escape نکن. |
| خروجی | escape دیرهنگام و متناسب با context: `esc_html`, `esc_attr`, `esc_url`, `wp_kses_post` برای HTML محدود. JSON با WP API / `wp_json_encode` و context امن؛ داده خام مستقیم داخل JS یا attribute قرار نگیرد. |
| SQL | اول WP API؛ اگر SQL دستی ضروری است، `$wpdb->prepare()` با placeholderهای صحیح؛ identifier از allowlist یا `%i` در نسخه پشتیبانی‌شده، نه user string. مقدار ORDER BY جهت/ستون از allowlist؛ LIKE با `esc_like()` قبل از prepare. |
| Authentication / Authorization | `current_user_can()` با capability و شناسه منبع در عملیات خصوصی؛ nonce برای CSRF، نه مجوز. REST route با `permission_callback`؛ `__return_true` فقط برای داده عمداً عمومی. |
| Upload | WP Media API، MIME/extension/size/permission، جلوگیری از اجرای فایل؛ نام فایل یا مسیر دلخواه کاربر مستقیم استفاده نشود. |
| HTTP خارجی | API استاندارد WP، HTTPS، timeout و status/error؛ برای URL کاربر `wp_safe_remote_get/request`، allowlist مقصد در صورت امکان، محدودیت پاسخ و retry کنترل‌شده؛ TLS verification غیرفعال نشود. |
| Secrets | از environment یا config امن خارج از repository/public root طبق profile؛ رمز، token، nonce، اطلاعات حساس کاربر در log، HTML عمومی یا JS bundle قرار نگیرد. |

- برای REST با Cookie authentication از nonce REST صحیح و permission callback استفاده کن؛ برای فرم/AJAX تغییر وضعیت `check_ajax_referer` یا `wp_verify_nonce` به همراه مجوز. فرم عمومی به شکل جدا rate-limit/spam control، اندازه payload و اعتبارسنجی داشته باشد؛ nonce عمومی امنیت کامل نمی‌دهد. GET بدون side effect معمولی؛ عملیات تغییر داده با method مناسب.
- نمونه آموزشی خواندن پارامتر text از `$_GET` به صورت اختیاری؛ فقط برای ورودی واقعاً متنی. برای عدد/enum اعتبارسنجی جدا لازم است. این مثال جایگزین validation فرم یا مجوز نیست.

```php
<?php
$raw = $_GET['q'] ?? '';
$term = is_string( $raw ) ? sanitize_text_field( wp_unslash( $raw ) ) : '';
?>
<p><?php echo esc_html( $term ); ?></p>
```

- نمونه REST خصوصی (در plugin پروژه، namespace واقعی تنظیم شود). مجوز فقط برای مدیرانی که `manage_options` دارند؛ برای داده اختصاصی کاربر object-level permission لازم است.

```php
<?php
add_action( 'rest_api_init', static function (): void {
	register_rest_route( 'project/v1', '/status', array(
		'methods'             => WP_REST_Server::READABLE,
		'callback'            => static function (): WP_REST_Response {
			return rest_ensure_response( array( 'ready' => true ) );
		},
		'permission_callback' => static function (): bool {
			return current_user_can( 'manage_options' );
		},
	) );
} );
```

## 5. سرعت، دیتابیس، Cache و منابع

- سرعت را اندازه‌گیری کن: TTFB (cold/warm)، query count/time، slow query، memory peak، PHP errors، HTTP calls و زمان HTML server rendering. هیچ تعداد جادویی برای query یا زمان PHP تعیین نکن. CWV سمت کاربر در سند 09/01.
- از main query و APIهای WP استفاده کن؛ `WP_Query` جدا برای بخش مستقل. `posts_per_page` محدود و pagination منظم؛ `no_found_rows => true` فقط وقتی شمارش کل/pagination لازم نیست؛ `fields => 'ids'` فقط اگر واقعاً IDs نیاز است؛ meta/term cache را زمانی خاموش کن که بعداً خوانده نمی‌شود. در غیر این صورت می‌تواند N+1 ایجاد کند. بعد از `the_post()` در loop فرعی، `wp_reset_postdata()`.
- برای `get_post_meta` یا taxonomy در loop از cache استاندارد/priming آگاه باش؛ query دستی در هر iteration، meta_query چندلایه بی‌دلیل، `posts_per_page => -1` روی داده بزرگ، sort تصادفی بزرگ، query مجدد برای داده موجود ممنوع مگر نیاز و اندازه‌گیری. index/جدول اختصاصی فقط با توجیه، migration و backup.
- Transient برای نتیجه گران قابل بازسازی بین درخواست‌ها؛ ممکن است قبل از TTL هم ناپدید شود. `false === get_transient(...)` را بررسی کن؛ نام کلید و نسخه schema و invalidation هنگام تغییر داده مشخص؛ private/personal data در public/shared cache نباشد. object cache، full-page cache، CDN و OPcache لایه‌های متفاوت هستند؛ حضور Redis/OPcache را بدون بررسی فرض نکن.
- درخواست شبکه پرهزینه در هر بازدید frontend انجام نشود؛ cache نتیجه، timeout و fallback مناسب. کار طولانی را job جدا یا WP-Cron/queue با idempotency و محدودیت اجرای همزمان طبق نیاز انجام بده؛ زمان‌بندی واقعی سرور در profile/runbook، نه این سند.
- فایل و مدیای بزرگ را یکجا در حافظه نخوان؛ از API upload/stream مناسب استفاده کن. محدودیت منابع واقعی هاست، اندازه فایل و payload در profile. از session سراسری غیرضروری که caching را مختل کند، خودداری کن.
- OPcache در production در صورت پشتیبانی هاست بررسی شود؛ پارامترهای `php.ini`، FPM workers، memory limit، max execution time و cache TTL با اندازه‌گیری و شرایط هاست در runbook تنظیم شوند. PHP 8.4/8.5 یا JIT به‌تنهایی تضمین سرعت نیست.

### نمونه Loop فرعی محدود بدون pagination

```php
<?php
$recent = new WP_Query( array(
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 3,
	'no_found_rows'  => true,
) );
?>
<?php if ( $recent->have_posts() ) : ?>
	<ul>
		<?php while ( $recent->have_posts() ) : $recent->the_post(); ?>
			<li><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></li>
		<?php endwhile; ?>
	</ul>
<?php endif; ?>
<?php wp_reset_postdata(); ?>
```

جزئیات query و توسعه‌پذیری داده در [18](18-DATABASE-QUERY-OPTIMIZATION-v1.0.md)، کیفیت و حذف کد در [19](19-CODE-QUALITY-REFACTORING-v1.0.md)؛ نمونه‌های این سند مرجع نحو PHP هستند.

## 6. وابستگی‌ها، نسخه‌بندی و ابزار کیفیت

- Composer در صورت نیاز پروژه: PSR-4 autoload برای کلاس‌ها، قفل نسخه‌های dependency برای build پروژه (`composer.lock`)، ثبت extensionهای لازم در `require`، بررسی license/security، بدون دانلود کد ناشناس هنگام هر درخواست. تولید artifact با نسخه قفل‌شده؛ نه `composer update` خودسرانه روی production. برای پروژه WordPress دارای چند plugin احتمال تضاد namespace/vendor بررسی شود.
- ابزارهای کیفیت را در dev/CI نگه دار: PHP lint، PHPCS با WPCS و در صورت پشتیبانی نسخه‌ای PHPCompatibilityWP، PHPStan با stubs/extension مناسب WP در صورت نیاز، unit/integration test. نسخه ابزارها و `testVersion` را با حداقل 8.4 و ماتریس تست هماهنگ کن؛ ابزار static به‌تنهایی سازگاری را اثبات نمی‌کند.
- نمونه دستورها (مسیرهای `src`, `inc`, `tests` را با پروژه واقعی جایگزین کن؛ ابزار باید قبلاً نصب شده باشد):

```sh
php -v
php -l path/to/file.php
vendor/bin/phpcs --standard=WordPress path/to/source
vendor/bin/phpcs --standard=PHPCompatibilityWP --runtime-set testVersion 8.4-8.5 path/to/source
composer validate
composer audit
composer check-platform-reqs
```

- برای release، lint تمام فایل‌های owned PHP روی هر نسخه هدف؛ تست integration با WP واقعی و افزونه‌های نصب‌شده؛ smoke test روی CLI و SAPI واقعی هاست (ممکن است نسخه PHP CLI و FPM فرق کنند). تست ابزار compatibility با نسخه ruleset تأییدشده، نه دور زدن خطاها. اگر Composer استفاده نمی‌شود، دستورهای مربوط اجباری نیستند.
- `WP_DEBUG`/logging در local/staging برای خطاها و deprecation؛ نمایش خطا و log عمومی در production ممنوع. مسیر/دسترسی log و retention در runbook. logs باید بدون secret/PII باشند. تنظیم واقعی محیط را بدون تأیید تغییر نده.

## 7. معیار پذیرش Codex

- [ ] ثبت نسخه نصب‌شده واقعی WP/PHP/extensionها و بررسی سازگاری theme/plugin؛ PHP 8.4 و 8.5 در ماتریس یا گزارش دلیل/مانع تست.
- [ ] PHP parse/lint، zero unexpected warnings/deprecations در کد تغییرکرده؛ خطاهای dependency جدا مستند و حل یا پذیرش رسمی ریسک.
- [ ] WPCS/تحلیل static طبق ابزار موجود؛ suppression محدود و مستند؛ typeها و `WP_Error` درست.
- [ ] تست frontend/admin/REST/فرم با کاربر مهمان و نقش‌های لازم؛ nonce نامعتبر، بدون permission، داده نامعتبر و خروجی escape‌شده.
- [ ] تست 404/archive/search/pagination، عدم تغییر URL بی‌مجوز، template/hook و asset تکراری وجود ندارد؛ SEO/HTML طبق 01.
- [ ] تست حجم داده واقعی، cache cold/warm و invalidation، عدم cache داده خصوصی، slow API/error fallback، memory/time measurement در صورت دسترسی.
- [ ] backup/rollback و سازگاری staging قبل از deploy؛ هیچ تغییر production بدون مجوز.
- [ ] گزارش فایل تغییرکرده، تصمیم‌ها، تست واقعاً اجراشده (با نسخه/نتیجه)، موارد Not run و ریسک. کد نمونه این سند صرفاً نقطه شروع است و تست WordPress واقعی لازم دارد.

## منابع رسمی (بازبینی 2026-11-18)

- PHP supported versions: https://www.php.net/supported-versions.php
- PHP 8.4 migration: https://www.php.net/manual/en/migration84.php
- PHP 8.5 migration: https://www.php.net/manual/en/migration85.php
- PHP type declarations: https://www.php.net/manual/en/language.types.declarations.php
- WordPress releases: https://wordpress.org/download/releases/
- Core compatibility: https://make.wordpress.org/core/handbook/references/php-compatibility-and-wordpress-versions/
- PHP WPCS: https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/
- Theme vs plugin: https://developer.wordpress.org/themes/getting-started/what-is-a-theme/
- Template hierarchy: https://developer.wordpress.org/themes/classic-themes/basics/template-hierarchy/
- Security: https://developer.wordpress.org/apis/security/ — https://developer.wordpress.org/apis/security/escaping/ — https://developer.wordpress.org/apis/security/nonces/
- REST: https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
- Query: https://developer.wordpress.org/reference/classes/wp_query/
- Cache: https://developer.wordpress.org/apis/transients/ — https://developer.wordpress.org/reference/classes/wp_object_cache/
- HTTP: https://developer.wordpress.org/reference/functions/wp_safe_remote_get/ — https://developer.wordpress.org/apis/making-http-requests/performance/
- Debug: https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
- OPcache: https://www.php.net/manual/en/opcache.configuration.php
- Composer: https://getcomposer.org/doc/03-cli.md
- WPCS tools: https://github.com/WordPress/WordPress-Coding-Standards — https://github.com/PHPCompatibility/PHPCompatibilityWP

**تاریخچه:** v1.0 — استاندارد اولیه PHP 8.4+، ماتریس نسخه، قواعد امنیت و عملکرد. زمان‌بندی و تنظیمات هاست در scope نیستند.
