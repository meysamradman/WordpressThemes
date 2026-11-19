# Web Development Standards — Index v1.2

**به‌روزرسانی:** 2026-11-18 | مرجع واحد مستندات توسعه در مخزن. پروژه را مشخص کنید، از جدول موضوع یک مالک پیدا کنید و همان بخش را بخوانید. [چارچوب Profile، Scenario، Runbook و QA](14-PROJECT-PROFILES-AND-SCENARIOS-v1.1.md) مرز هر نوع سند را مشخص می‌کند.

**نسخه‌های مهم:** [جدول نسخه‌های سه پروژه](00-VERSIONS.md) مرجع سریع نسخه‌های اعلام‌شده، نصب‌شده و آزموده است.

## پروژه را پیدا کنید

| پروژه | مسیر و نقطهٔ شروع |
|---|---|
| ویبتالیک | قالب سفارشی. [پروفایل و مالک‌های فرانت‌اند](../Corporate-Webtalik/docs/frontend-standard.md) |
| علی اسدی | قالب سفارشی. [پروفایل و مالک‌های فرانت‌اند](../Corporate-Music/docs/frontend-standard.md) |
| Corporate-Elementor-Catalog | Elementor. [پروفایل نسخه‌ها](../Corporate-Elementor-Catalog/radman-doc/product/profile.md) و [فهرست قراردادها](../Corporate-Elementor-Catalog/radman-doc/README.md) |

اسناد Hub و Radman-Design-Library طبق قواعد پروژه‌های خودشان مصرف می‌شوند. این فهرست استانداردهای بالا را خودکار به آن پروژه‌ها اعمال نمی‌کند.

## مشترک (shared/)

| ID | سند فعال | وضعیت |
|---|---|---|
| 01 | [HTML/SEO](shared/01-HTML-SEO-RESPONSIVE-GUIDE-v1.2.md) | موجود؛ راهنمای ترکیبی عمومی قبلی، جزئیات responsive و motion با 03/05 |
| 02 | [Modern CSS & Minimal DOM](shared/02-CSS-STANDARDS-v1.0.md) | جدید |
| 03 | [Responsive & RTL](shared/03-RESPONSIVE-DESIGN-STANDARD-v1.0.md) | جدید |
| 04 | [Vanilla JS](shared/04-VANILLA-JS-DEVELOPMENT-STANDARD-v1.1.md) | موجود؛ scope هماهنگ |
| 05 | [GSAP](shared/05-GSAP-ANIMATION-STANDARD-v1.1.md) | [انیمیشن ورود عنوان و متن](shared/05-GSAP-ANIMATION-STANDARD-v1.1.md#customer-content-entrance)، جدا از پارالاکس و پرده |
| 08 | [نسخه‌ها، ماژول‌های CSS/JS و Build](shared/08-BUILD-AND-VERSIONS.md) | نسخهٔ اعلام‌شده، نصب‌شده و آزموده، entry، manifest، تحویل بر اساس دامنه و اصلاح محدود دیتابیس |
| 09 | [عملکرد و کش](shared/09-PERFORMANCE-AND-CACHE.md) | waterfall، LCP/TTFB، کش فایل/HTML/ویجت و purge |
| 10 | [امنیت WordPress](shared/10-SECURITY.md) | ورودی، خروجی، مجوز، دادهٔ خصوصی و بسته |
| 11 | [دسترس‌پذیری](shared/11-ACCESSIBILITY.md) | فوکوس، لمس، RTL، فرم، کاروسل و reduced-motion |
| 12 | [تأیید و QA](shared/12-VERIFICATION.md) | انتخاب کوتاه‌ترین بررسی برای دامنهٔ تغییر |
| 13 | [PHP 8.4+](shared/13-PHP-DEVELOPMENT-STANDARD-v1.1.md) | موجود؛ scope هماهنگ |
| 16 | [Design System & UI Components](shared/16-DESIGN-SYSTEM-COMPONENTS-v1.0.md) | [Input و فرم](shared/16-DESIGN-SYSTEM-COMPONENTS-v1.0.md#ui-input)، [Card](shared/16-DESIGN-SYSTEM-COMPONENTS-v1.0.md#ui-card)، token، renderer و variant |

## مسیر WordPress Custom (wordpress-custom/)

- [07 — Custom Development](wordpress-custom/07-WORDPRESS-CUSTOM-DEVELOPMENT-v1.0.md) — معماری اختصاصی PHP، renderer، template و asset.
- [06 — Loading System](wordpress-custom/06-LOADING-SYSTEM-v1.1.md) — orchestration پرده/entrance/scroll/skeleton/AJAX/media.
- [Project Profile](wordpress-custom/PROJECT-PROFILE-TEMPLATE-v1.1.md) — نسخه‌ها و تصمیم هر پروژه.

## مسیر WordPress Elementor (wordpress-elementor/)

- [15 — Elementor Development](wordpress-elementor/15-WORDPRESS-ELEMENTOR-DEVELOPMENT-v1.0.md) — Global Styles، widgets، dependencies، editor lifecycle، مالکیت و تست.
- [Project Profile](wordpress-elementor/PROJECT-PROFILE-TEMPLATE-v1.1.md) — نسخه‌ها و تصمیم هر پروژه.

## روش انتخاب و مرز مسئولیت

[14 — Project Profiles & Scenarios](14-PROJECT-PROFILES-AND-SCENARIOS-v1.1.md) چارچوب مشترک ثبت سناریوهاست. برای Custom فقط shared و wordpress-custom (به علاوه 14)، برای Elementor فقط shared و wordpress-elementor (به علاوه 14). 04/05 فقط برای کد خود تیم و در صورت نیاز؛ 06 مخصوص مسیر Custom است و نباید روی builder اعمال شود. در 01 قواعد عمومی loading/motion توصیه تجربه کاربرند، نه الزام اجرای موتور دوم. در پروژه hybrid فقط با scope تأییدشده مالک هر صفحه/feature تعیین شود.

- 16 مالک نقش‌های طراحی، component registry و reuse؛ 02 مالک syntax/cascade/layout/CSS delivery؛ 03 مالک responsive؛ 04 مالک JS behavior/data؛ 05 مالک motion؛ 06 مالک orchestration اختصاصی و 15 مالک lifecycle ویجت. یک source of truth و یک runtime/owner برای هر قابلیت. استایل یا تنظیمات برند در Profile همان پروژه، نه اسناد مشترک.
- 08 مالک قرارداد نسخه و build، 09 مالک کش و روش سنجش، 10/11/12 مالک قواعد امنیت، دسترس‌پذیری و بررسی‌اند. 13 روش PHP و 07/15 معماری همان مسیر را تکمیل می‌کنند. PHP 8.4+ هدف توسعه است. نسخه و حداقل فعلی هر پروژه از منابع همان Profile خوانده شوند.
- اصول امنیت، دسترس‌پذیری و API رسمی نسخه نصب‌شده مقدم‌اند؛ اختلاف حل‌نشده یا استثنا با دلیل و تست گزارش شود. استانداردها مجوز بازطراحی، ارتقا، نصب library یا انتشار خودکار نیستند. اسناد «در برنامه» موجود نیستند.

## دستور Codex

1. Profile و سناریوی تأییدشده را بخوان؛ source/build/runtime، rendererها، tokens، asset ownership، CSS/JS و componentهای موجود را audit کن. فایل‌های vendor و dist را مستقیم تغییر نده.
2. reuse قبل از ساخت؛ HTML اصلی مشترک؛ CSS برای layout، JS برای رفتار و GSAP برای حرکت. فایل‌های source ماژولار، asset production متناسب با نیاز و cache. تغییر حداقلی و بدون regression ظاهر، داده، URL و دسترس‌پذیری.
3. از [روش بررسی متناسب](shared/12-VERIFICATION.md) برای انتخاب تست استفاده کنید. فقط هنگام تغییر API و سازگاری نسخه به مستند رسمی همان فناوری رجوع کنید. مرور مستندات به build یا تست انتشار نیاز ندارد.

برای درخواست مشخص ممیزی سرعت، waterfall یا کش از [Skill مشترک WordPress performance review](../.agents/skills/wordpress-performance-review/SKILL.md) استفاده کنید. مسیر پیاده‌سازی با Skill همان پروژه انتخاب می‌شود.

## تاریخچه نسخه

- v1.2: ساخت 02، 03، 07، 15، 16؛ تفکیک واقعی سه پوشه؛ 04/05/06/13/14 و Profileها فقط اصلاح محدوده و ارجاع/نام، بدون بازنویسی نمونه‌های قبلی. 01 بدون تغییر. بررسی اصول عمومی از radman-doc.zip (markup-css، typography، assets، components، component-library، layout و elementor)؛ قراردادهای مشتری/تنظیمات پروژه یا نسخه‌های قدیمی منتقل نشده‌اند.
- تکمیل v1.2: اسناد 08 تا 12 فعال شدند. منبع HTML/SEO در 01، قواعد اجرایی هر مسیر در 06/07/15 و سناریوهای واقعی در پروژه‌های مربوط قرار دارند.
- v1.1 و قبل: آرشیو مستقل. این فهرست مسیر ورود نسخهٔ جاری است.
