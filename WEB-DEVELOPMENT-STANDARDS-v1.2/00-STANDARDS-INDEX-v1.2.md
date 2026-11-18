# Web Development Standards — Index v1.2

**تاریخ:** 2026-11-18 | مستقل از برند مشتری | مرجع اجرای Codex. ابتدا Profile همان مسیر را تکمیل کن؛ فقط اسناد مرتبط را بخوان. استاندارد = روش؛ Profile = فناوری و نسخه؛ Scenario = رفتار و پذیرش؛ Runbook = تنظیم/استقرار؛ QA = نتیجه تست. هیچ secret در اسناد.

## مشترک (shared/)

| ID | سند فعال | وضعیت |
|---|---|---|
| 01 | [HTML/SEO](shared/01-HTML-SEO-RESPONSIVE-GUIDE-v1.2.md) | موجود؛ راهنمای ترکیبی عمومی قبلی، جزئیات responsive و motion با 03/05 |
| 02 | [Modern CSS & Minimal DOM](shared/02-CSS-STANDARDS-v1.0.md) | جدید |
| 03 | [Responsive & RTL](shared/03-RESPONSIVE-DESIGN-STANDARD-v1.0.md) | جدید |
| 04 | [Vanilla JS](shared/04-VANILLA-JS-DEVELOPMENT-STANDARD-v1.1.md) | موجود؛ scope هماهنگ |
| 05 | [GSAP](shared/05-GSAP-ANIMATION-STANDARD-v1.1.md) | [پروفایل ورود عنوان و متن مشتری](shared/05-GSAP-ANIMATION-STANDARD-v1.1.md#customer-content-entrance) برای Elementor و قالب سفارشی |
| 08 | Vite & Build | در برنامه؛ قواعد پایه در 02/04/07/15 |
| 09 | Performance | در برنامه؛ قواعد پایه در اسناد مرتبط |
| 10 | Security | در برنامه؛ اصول 04/13 |
| 11 | Accessibility | در برنامه؛ اصول 01–05/16 |
| 12 | Testing & QA | در برنامه؛ پذیرش هر سند فعلاً مستقل |
| 13 | [PHP 8.4+](shared/13-PHP-DEVELOPMENT-STANDARD-v1.1.md) | موجود؛ scope هماهنگ |
| 16 | [Design System & UI Components](shared/16-DESIGN-SYSTEM-COMPONENTS-v1.0.md) | جدید؛ token، font، renderer، variant، reuse |

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
- 13 روش کدنویسی PHP و امنیت مرتبط؛ 07/15 معماری و APIهای مسیر. نسخه هدف PHP 8.4+؛ 8.5 و نسخه‌های بعد فقط پس از ماتریس سازگاری واقعی؛ نسخه WordPress و وابستگی‌ها پیش از اجرا از منبع رسمی بررسی شوند.
- اصول امنیت، دسترس‌پذیری و API رسمی نسخه نصب‌شده مقدم‌اند؛ اختلاف حل‌نشده یا استثنا با دلیل و تست گزارش شود. استانداردها مجوز بازطراحی، ارتقا، نصب library یا انتشار خودکار نیستند. اسناد «در برنامه» موجود نیستند.

## دستور Codex

1. Profile و سناریوی تأییدشده را بخوان؛ source/build/runtime، rendererها، tokens، asset ownership، CSS/JS و componentهای موجود را audit کن. فایل‌های vendor و dist را مستقیم تغییر نده.
2. reuse قبل از ساخت؛ HTML اصلی مشترک؛ CSS برای layout، JS برای رفتار و GSAP برای حرکت. فایل‌های source ماژولار، asset production متناسب با نیاز و cache. تغییر حداقلی و بدون regression ظاهر، داده، URL و دسترس‌پذیری.
3. پیش از تحویل، lint/build، تست چند صفحه/عرض/RTL، keyboard، شبکه کند، lifecycle/AJAX/ورود و خطا؛ Network/Coverage و مقایسه performance در محیط یکسان. نتیجه Pass/Fail/Not run با شواهد، ریسک و rollback. صرف کاهش کد یا فایل اثبات سرعت نیست.

## تاریخچه نسخه

- v1.2: ساخت 02، 03، 07، 15، 16؛ تفکیک واقعی سه پوشه؛ 04/05/06/13/14 و Profileها فقط اصلاح محدوده و ارجاع/نام، بدون بازنویسی نمونه‌های قبلی. 01 بدون تغییر. بررسی اصول عمومی از radman-doc.zip (markup-css، typography، assets، components، component-library، layout و elementor)؛ قراردادهای مشتری/تنظیمات پروژه یا نسخه‌های قدیمی منتقل نشده‌اند.
- v1.1 و قبل: آرشیو مستقل؛ تنها نسخه جاری همین index است. نسخه قدیم را با current مخلوط نکن. 08–12 هنوز در برنامه‌اند؛ سناریوهای واقعی و QA مشتری در مسیر پروژه خودشان.
