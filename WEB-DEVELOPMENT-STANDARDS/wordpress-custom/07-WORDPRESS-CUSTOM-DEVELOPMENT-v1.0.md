# WordPress Custom Theme Development — v1.0

> بازبینی بسته 1.3 — 2026-11-19؛ نام مسیر برای سازگاری ارجاع‌های موجود حفظ شده است. تاریخچه در CHANGELOG-v1.3.md.

**تاریخ:** 2026-11-18 | **دامنه:** قالب‌های واقعاً اختصاصی WordPress با PHP/HTML/CSS، Vanilla JS و در صورت نیاز GSAP/Vite. PHP 8.4+ طبق 13؛ نسخه دقیق WordPress و ابزارها در Profile. این فایل برای پروژه‌های صفحه‌ساز نیست.

## 1. معماری و مالکیت

- ابتدا معماری پروژه مصوب (classic PHP یا custom block theme) و قابلیت‌های موجود را بررسی کن؛ تغییر نوع قالب خودسرانه ممنوع. برای classic، PHP و Template Hierarchy مرجع رندر است؛ برای block، templates/parts و theme.json طبق API بومی. نمونه زیر classic است، نه دستور ایجاد همه فایل‌ها.

```text
my-theme/
  style.css
  functions.php
  header.php / footer.php
  index.php / front-page.php / home.php
  page.php / single.php / archive.php / search.php / 404.php
  inc/                  # setup, assets, template helpers
  template-parts/
    components/         # button, card, form, skeleton
    sections/           # reusable page sections
  src/css/              # tokens, base, layout, components, features
  src/js/               # core, components, features, motion
  dist/                 # generated if build pipeline used
```

- Theme مالک presentation، قالب، global styles، render helper و asset manifest خود؛ plugin اختصاصی سایت مالک CPT، taxonomy، business rules، داده پایدار، API و migration. امنیت و روش PHP طبق 13؛ هسته و vendor ویرایش مستقیم نشوند. از ساخت framework داخلی غیرضروری یا قالب خالی وابسته به JS پرهیز کن.
- `functions.php` bootstrap کوچک؛ `after_setup_theme`, `init`, `wp_enqueue_scripts`, `admin_enqueue_scripts`, `rest_api_init` طبق دامنه؛ `language_attributes()`, `body_class()`, `wp_head()`, `wp_body_open()`, `wp_footer()` در محل صحیح. صفحه/آرشیو از query اصلی و Loop مناسب استفاده کند؛ 404 واقعی، pagination و URL استاندارد؛ rewrite flush در هر درخواست ممنوع.
- عنوان و متادیتا، canonical، sitemap، schema و robots یک مالک داشته باشند؛ تکرار خروجی theme/plugin ممنوع. داده و متن اصلی SSR؛ JS enhancement. routing، لینک‌های مهم و ساختار heading مطابق 01؛ حفظ URLهای موجود مگر مهاجرت تأییدشده.
- برای پروژه‌ای که CPT یا دادهٔ موجود در قالب دارد، مالکیت و API ذخیره‌شده مطابق Profile حفظ شوند. انتقال داده یا CPT به plugin مستقل کار مهاجرت جداگانه است. noindex محلی، حریم محتوای رمزدار و خروجی Yoast یا افزونهٔ SEO فعال طبق قرارداد پروژه حفظ شوند. Analytics با شناسهٔ واقعی و یک integration، جدا از Search Console بررسی شود.

## 1.1. قرارداد قطعی تصویر برگه‌های با طراحی اختصاصی (بازبینی 2026-11-19)
- در `page.php`، templateهای اختصاصی درباره/تماس/خدمات/صفحه اصلی و Page Title renderer، از `the_post_thumbnail()` یا `get_post_thumbnail_id()` به‌عنوان fallback خودکار Hero/Background استفاده نکن. عنوان/H1 و توضیح مستقل از وجود عکس؛ تصویر نداشتن طرح یک حالت معتبر است. برگه همچنان می‌تواند featured image برای social/کارت مستقل داشته باشد؛ داده را حذف یا global support را غیرفعال نکن.
- artwork ثابت بخش از asset path و ابعاد واقعی در کد؛ از `get_theme_file_uri()` برای فایل ثابت و manifest helper مالک پروژه برای فایل build استفاده کن. برای تصویر قابل تنظیم فقط فیلد همان section با attachment ID، validate و fallback مصوب. مسیر parent/child مطابق مالکیت واقعی. مسیر مطلق localhost/uploads hardcode نشود.
- آرشیو و کارت نمونه‌کار/مقاله/محصول/اثر: تصویر شاخص همان آیتم، نه تصویر شاخص برگه آرشیو؛ single detail طبق طرح می‌تواند همان کاور و گالری را داشته باشد. Header/Footer/Page Title عمومی نباید هم‌زمان کاور تکراری چاپ کنند. تصاویر تزئینی از content image جدا باشند. جزئیات حفاظت و بهینه‌سازی در [17](../shared/17-IMAGES-AND-MEDIA-v1.0.md).

نمونه تصویر ثابت در PHP (فایل و اندازه مثال هستند؛ تطبیق با asset واقعی):
```php
<img src="<?php echo esc_url( get_theme_file_uri( 'images/pages/about.webp' ) ); ?>"
     width="800" height="600" alt="" loading="lazy" decoding="async">
```
این مثال برای تصویر تزئینی پایین صفحه است؛ برای تصویر محتوایی alt مناسب، برای LCP بدون lazy و اولویت اندازه‌گیری‌شده. اگر تصویر LCP است از همان asset واقعی و نه تصویر شاخص برگه استفاده کن. برای چند اندازه ثابت `srcset` واقعی تولید کن؛ تصویر ساختگی به catalog اضافه نکن.

نمونه کارت محتوایی از ID پست عمومیِ از پیش تأییدشده در query/guard:
```php
<?php
$cover_id = get_post_thumbnail_id( $post_id );
if ( $cover_id ) {
    // WordPress builds src/srcset, dimensions and loading attributes.
    echo wp_get_attachment_image( $cover_id, 'medium_large', false, array(
        'class' => 'card__image',
        'sizes' => '(max-width: 600px) 100vw, 360px',
    ) );
}
?>
```
`sizes` و class فقط مثال‌اند؛ width واقعی کارت، سیاست alt و fallback پروژه تعیین‌کننده‌اند. guard عمومی بودن پست/attachment قبل از renderer؛ برای رسانه خصوصی URL مستقیم نیز محافظت شود. در renderer واحد کارت، نبود کاور با placeholder تزئینی/طرح بدون عکس مدیریت شود. خروجی HTML از API بومی دوباره `esc_html` نشود؛ attributes پویا قبل از ارسال validate و API escape را رعایت کنند.

پذیرش: یک برگه سفارشی با featured image و بدون آن باز شود؛ ظاهر Hero و Page Title ثابت بماند. کارت و detail همان داده در مسیرهای مصوب عکس درست داشته باشند. HTTP/HTML/Network بدون تصویر تکراری، alt/width/height/sizes درست و no-JS؛ WP editor و social metadata دست نخورده. این قرارداد ممنوعیت تصویر شاخص در تمام WordPress نیست.

## 2. کامپوننت‌ها و Design System

- قرارداد مشترک 02/03/16 اجرا شود: token مرکزی، CSS پایه کوچک، component variants، font registry، renderer reusable. PHP `get_template_part()` با args یا render helper برای خانواده‌های واقعی؛ صفحه فقط data/layout/variant بدهد. هدر، فوتر، عنوان صفحه، کارت blog و آرشیو دوباره از صفر کپی نشوند؛ قطعه اختصاصی واقعی در مسیر feature خودش بماند.
- مثال: `get_template_part( 'template-parts/components/card', null, $card_args );`؛ schema ورودی، variant مجاز، tag عنوان whitelist و escaping در renderer. renderer نباید query پنهان در هر کارت ایجاد کند. تعریف مشترک به معنی یک فایل عظیم نیست؛ چند family با primitive مشترک ممکن است.
- برای token قابل تنظیم از API معتبر و validate/sanitize و escaping مناسب CSS context استفاده کن؛ استایل ثابت در source، فقط مقادیر dynamic معتبر scoped. داده و فونت عمومی یک مالک داشته باشند. پنل مدیریت فقط assets خود را دریافت کند. یک DOM ریسپانسیو RTL/LTR، نه layout دوم.

## 3. CSS/JS و Build

- `wp_enqueue_style/script` و dependency handles یکتا؛ از تزریق script/link در چند template پرهیز. در صورت Vite از manifest و CSS لازمِ entry/static dependencies استفاده کن؛ runtime import فقط قابلیت لازم. CSS حیاتی markup SSR باید قبل از نمایش آماده شود. enqueue همه chunkها ممنوع، `dist` دستی تغییر نکند، Node dev server در prod نباشد. بدون build نیز enqueue صحیح و versioning لازم است.
- پیش از `wp_head` نیازهای صفحه را با template/route و داده قابل اتکا تشخیص بده؛ به اجرا شدن دیرهنگام renderer برای CSS حیاتی متکی نباش. asset کوچکِ واقعاً سراسری می‌تواند مشترک باشد؛ تصمیم با Network/Coverage. تمایز style.css metadata و CSS خروجی را رعایت کن. handle/dependency هسته را حدسی حذف نکن.
- Vanilla JS 04 مالک DOM behavior و data، GSAP 05 صرفاً motion؛ Loading 06 مرجع curtain/entrance/scroll/AJAX/media. فقط یک entry orchestration و یک نسخه هر کتابخانه؛ mount/dispose در تغییر root. `matchMedia` برای اجرا و dynamic import برای جلوگیری از دانلود کد غیرضروری. از global listener روی همه nodeها و ساختن موتور دوم منو/کاروسل/scroll-lock خودداری کن.
- برای ورود عنوان و متن منتخب مشتری از [پروفایل مشترک ورود](../shared/05-GSAP-ANIMATION-STANDARD-v1.1.md#customer-content-entrance) و یک مالک مشترک استفاده کن. پارالاکس دامنه و محرک مستقل دارد. پیاده‌سازی ساده می‌تواند با CSS و یک IntersectionObserver مشترک انجام شود و به موتور یا پروژه دیگری وابسته نیست.
- رسانه از API بومی WordPress؛ image sizes/srcset/sizes/alt/dimensions، hero priority بر اساس LCP و lazy رسانه پایین صفحه، font policy در 16؛ هیچ ویدئو/فونت/فایل سنگین صرفاً به خاطر وجود در پروژه بارگذاری نشود.
- [قرارداد نسخه و بسته](../shared/08-BUILD-AND-VERSIONS.md) و [کش و سنجش شبکه](../shared/09-PERFORMANCE-AND-CACHE.md) در هر محیط با Profile و Runbook همان پروژه تطبیق داده شوند. در WordPress دارای script module، importهای static و dynamic همان manifest حفظ شوند.

## 4. پذیرش Codex

- برای ممیزی قالب مشتری، ابتدا مسیر واقعی و مالک هر قابلیت را مشخص کن. H1 مستقل از دادهٔ اختیاری باشد. متا و اسکیما یک مالک داشته باشند. کوئری، asset و شاخهٔ قدیمی فقط پس از بررسی مصرف مستقیم و پویا حذف شوند. تغییرات بصری، رفتار پخش تأییدشده و جابه‌جایی CPT تصمیم جداگانه با تست مرتبط می‌خواهند.
- کنترل دسترسی رندر را معادل حفاظت URL مستقیم فایل در uploads در نظر نگیر. قبل از پاک‌سازی فایل‌های تحویل، سالم بودن نسخهٔ نهایی و هدف دقیق حذف را بررسی کن. بکاپ بازیابی، دادهٔ اصلی مشتری و فایل منبع یکتا حفظ شوند.

- [ ] Template Hierarchy واقعی، چند نوع صفحه، no-JS، 404، archive/search/pagination، RTL/LTR، mobile و admin تست شده؛ URL/داده حفظ شده.
- [ ] renderer و token تکراری، asset نامرتبط، duplicate metadata، query اضافی و dependency دو نسخه بررسی و اصلاح شده؛ بدون حذف قابلیت لازم.
- [ ] نسخهٔ هدف و محیط متناسب با تغییر مطابق Profile بررسی شده‌اند. سازگاری نسخهٔ جدید با تست هدف تأیید می‌شود. نوع تست و محدودهٔ نتیجه طبق [سند بررسی](../shared/12-VERIFICATION.md) گزارش شود.
- [ ] قبل از انتشار، backup و rollback در Runbook همان پروژه؛ هیچ تنظیم سرور یا نصب کتابخانه خودکار بدون تأیید.

## منابع رسمی

- [Theme structure](https://developer.wordpress.org/themes/classic-themes/basics/organizing-theme-files/)، [Template hierarchy](https://developer.wordpress.org/themes/templates/template-hierarchy/)، [get_template_part](https://developer.wordpress.org/reference/functions/get_template_part/)، [Including assets](https://developer.wordpress.org/themes/core-concepts/including-assets/)
- [Vite features](https://vite.dev/guide/features.html)، [web.dev resource loading](https://web.dev/learn/performance/optimize-resource-loading)

منبع داخلی اصول reuse/asset: قراردادهای مشترک 02 و 16؛ تنظیمات و نام‌گذاری یک مشتری به معماری عمومی تحمیل نشده است.
