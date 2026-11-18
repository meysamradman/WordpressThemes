# WordPress Custom Theme Development — v1.0

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

## 2. کامپوننت‌ها و Design System

- قرارداد مشترک 02/03/16 اجرا شود: token مرکزی، CSS پایه کوچک، component variants، font registry، renderer reusable. PHP `get_template_part()` با args یا render helper برای خانواده‌های واقعی؛ صفحه فقط data/layout/variant بدهد. هدر، فوتر، عنوان صفحه، کارت blog و آرشیو دوباره از صفر کپی نشوند؛ قطعه اختصاصی واقعی در مسیر feature خودش بماند.
- مثال: `get_template_part( 'template-parts/components/card', null, $card_args );`؛ schema ورودی، variant مجاز، tag عنوان whitelist و escaping در renderer. renderer نباید query پنهان در هر کارت ایجاد کند. تعریف مشترک به معنی یک فایل عظیم نیست؛ چند family با primitive مشترک ممکن است.
- برای token قابل تنظیم از API معتبر و validate/sanitize و escaping مناسب CSS context استفاده کن؛ استایل ثابت در source، فقط مقادیر dynamic معتبر scoped. داده و فونت عمومی یک مالک داشته باشند. پنل مدیریت فقط assets خود را دریافت کند. یک DOM ریسپانسیو RTL/LTR، نه layout دوم.

## 3. CSS/JS و Build

- `wp_enqueue_style/script` و dependency handles یکتا؛ از تزریق script/link در چند template پرهیز. در صورت Vite از manifest و CSS لازمِ entry/static dependencies استفاده کن؛ runtime import فقط قابلیت لازم. CSS حیاتی markup SSR باید قبل از نمایش آماده شود. enqueue همه chunkها ممنوع، `dist` دستی تغییر نکند، Node dev server در prod نباشد. بدون build نیز enqueue صحیح و versioning لازم است.
- پیش از `wp_head` نیازهای صفحه را با template/route و داده قابل اتکا تشخیص بده؛ به اجرا شدن دیرهنگام renderer برای CSS حیاتی متکی نباش. asset کوچکِ واقعاً سراسری می‌تواند مشترک باشد؛ تصمیم با Network/Coverage. تمایز style.css metadata و CSS خروجی را رعایت کن. handle/dependency هسته را حدسی حذف نکن.
- Vanilla JS 04 مالک DOM behavior و data، GSAP 05 صرفاً motion؛ Loading 06 مرجع curtain/entrance/scroll/AJAX/media. فقط یک entry orchestration و یک نسخه هر کتابخانه؛ mount/dispose در تغییر root. `matchMedia` برای اجرا و dynamic import برای جلوگیری از دانلود کد غیرضروری. از global listener روی همه nodeها و ساختن موتور دوم منو/کاروسل/scroll-lock خودداری کن.
- برای ورود عنوان و متن منتخب مشتری از [پروفایل مشترک ورود](../shared/05-GSAP-ANIMATION-STANDARD-v1.1.md#customer-content-entrance) و یک مالک مشترک استفاده کن. پارالاکس دامنه و محرک مستقل دارد. پیاده‌سازی ساده می‌تواند با CSS و یک IntersectionObserver مشترک انجام شود و به Elementor یا Hub وابسته نیست.
- رسانه از API بومی WordPress؛ image sizes/srcset/sizes/alt/dimensions، hero priority بر اساس LCP و lazy رسانه پایین صفحه، font policy در 16؛ هیچ ویدئو/فونت/فایل سنگین صرفاً به خاطر وجود در پروژه بارگذاری نشود.

## 4. پذیرش Codex

- [ ] Template Hierarchy واقعی، چند نوع صفحه، no-JS، 404، archive/search/pagination، RTL/LTR، mobile و admin تست شده؛ URL/داده حفظ شده.
- [ ] renderer و token تکراری، asset نامرتبط، duplicate metadata، query اضافی و dependency دو نسخه بررسی و اصلاح شده؛ بدون حذف قابلیت لازم.
- [ ] PHP 8.4/8.5، lint/build و test محیط واقعی (با نسخه stack ثبت‌شده)، security و performance baseline؛ موارد اجرا نشده Not run. Lighthouse و build اثبات کافی برای همه رفتارها نیستند.
- [ ] قبل از انتشار، backup و rollback در Runbook همان پروژه؛ هیچ تنظیم سرور یا نصب کتابخانه خودکار بدون تأیید.

## منابع رسمی

- [Theme structure](https://developer.wordpress.org/themes/classic-themes/basics/organizing-theme-files/)، [Template hierarchy](https://developer.wordpress.org/themes/templates/template-hierarchy/)، [get_template_part](https://developer.wordpress.org/reference/functions/get_template_part/)، [Including assets](https://developer.wordpress.org/themes/core-concepts/including-assets/)
- [Vite features](https://vite.dev/guide/features.html)، [web.dev resource loading](https://web.dev/learn/performance/optimize-resource-loading)

منبع داخلی اصول reuse/asset: قراردادهای مشترک 02 و 16؛ تنظیمات و نام‌گذاری یک مشتری به معماری عمومی تحمیل نشده است.
