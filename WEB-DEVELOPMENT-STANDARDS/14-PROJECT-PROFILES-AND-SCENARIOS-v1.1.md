# Project Profiles & Scenario Framework — v1.1

**تاریخ:** 2026-11-18 | **مخاطب:** Codex / توسعه‌دهنده | مستقل از مشتری. این سند روش دسته‌بندی و انتخاب اسناد است، نه تنظیمات پنل هاست.

## 1. پنج نوع سند؛ با هم مخلوط نشوند

| نوع | کاربرد | نمونه |
|---|---|---|
| Standard | قانون پایدارِ روش ساخت، مستقل از مشتری | PHP 8.4+، GSAP lifecycle، HTML semantics |
| Profile | قرارداد فنی و نسخه‌های واقعی یک پروژه | نوع قالب، WordPress/PHP، Vite، مرورگر و افزونه موردنیاز |
| Scenario | رفتار یک قابلیت از ابتدا تا پایان و معیار پذیرش | فیلتر AJAX آرشیو، فرم سفارش، مهاجرت URL |
| Runbook / Configuration | مراحل اجرا در محیط مشخص؛ قابل تغییر با هاست/پروژه | تنظیم کش، DNS، OPcache، استقرار، Backup/Restore |
| QA Report | نتیجه واقعی تست؛ نه صرفاً برنامه | نسخه تست، Pass/Fail/Not run، evidence |

تنظیمات در Profile/Runbook ثبت می‌شوند؛ سناریو دقیقاً تعریف رفتار، state، failure و پذیرش قابلیت است. مثال: زمان timeout یک مقدار تنظیماتی است، اما رفتار بعد از timeout یک سناریو است. تنظیمات ثابت هاست و افزونه‌ها را وارد Standard نکن.

## 2. مسیرهای مستقل توسعه

| دسته | WordPress Custom | WordPress Elementor |
|---|---|---|
| مالک frontend | قالب اختصاصی classic/block طبق پروژه | قالب پایه و Elementor، Theme Builder در صورت وجود |
| استاندارد مشترک | HTML/SEO، CSS، responsive، Design System، PHP، امنیت، accessibility، performance | همان اهداف مشترک برای کد متعلق به تیم؛ HTML خروجی واقعی audit شود |
| JS/GSAP | ماژول‌های خودمان با lifecycle مشخص | فقط کد سفارشی، با lifecycle و dependencies سازگار با frontend/editor؛ سند ویژه لازم |
| Loading | سند 06 برای معماری اختصاصی | پس از audit owner موجود؛ پرده/loader دوم نساز |
| پیاده‌سازی | سند 07 اختصاصی (موجود) | سند 15 Elementor (موجود؛ مسیر جدا) |

- قبل از هر کار، یکی از profileهای پوشه مسیر انتخاب‌شده را کپی و برای همان پروژه تکمیل کن. پروژه hybrid استثناست: مالک هر صفحه/کامپوننت مشخص و تأیید شود؛ نه ترکیب خودکار دو معماری.
- GSAP و Vite انتخاب اختیاری مطابق پروژه‌اند. کد موجود Elementor/افزونه‌ها را برای اجرای قرارداد Vanilla از بین نبر؛ قراردادهای 04/05 بر کد خودمان اعمال می‌شوند. حذف dependency داخلی Elementor یا تغییر markup تولیدشده آن بدون آزمون ممنوع.
- برای نسخه‌های Elementor/Pro، theme و افزونه‌ها سازگاری دقیق با WP/PHP را در منابع رسمی و staging بررسی کن؛ اعداد ثابت از نسخه‌های قبلی کپی نشوند. منابع: https://elementor.com/help/requirements/ و https://developers.elementor.com/docs/widgets/widget-dependencies و https://developers.elementor.com/docs/themes/.

## 3. پروفایل نسخه‌ها

برای هر پروژه ثبت: نام/شناسه؛ نوع پروژه و مالک قالب؛ نسخه دقیق core، PHP CLI و PHP web، DB، Node/Vite/GSAP فقط در صورت استفاده؛ فهرست plugin/theme با نسخه؛ extensionهای PHP؛ مرورگر/عرض/RTL؛ source/lockfile/build command؛ هاست/webserver/cache/CDN؛ وضعیت local/staging/prod؛ قرارداد URL/SEO؛ داده و permission؛ منابع/تاریخ تأیید نسخه و نتیجه smoke test. مقدار نامشخص `TBD` است، نه حدس.

برای پروژه جدید هدف PHP 8.4+ مطابق 13 و latest stable WordPress سازگار؛ قبل از اجرا نسخه واقعی و patchها را recheck کن. نسخه‌های ابزار را در lockfile و profile نگه دار. فایل secrets و credential در markdown یا repository ثبت نشوند.

## 4. قالب سناریو (برای هر قابلیت یک فایل در پروژه)

```markdown
# SC-XXX — نام قابلیت
- Track: Custom | Elementor
- Scope / URL / component:
- Related standards:
- Required versions / prerequisites:
- Existing owner / source files:
- Input / authorization / expected data:
- Initial SSR / default state:
- Main flow (step-by-step):
- Loading / empty / error / abort / retry:
- Mobile / keyboard / reduced motion:
- Security / SEO / performance constraints:
- Cleanup / refresh / cache invalidation:
- Acceptance tests (Given / When / Then):
- Evidence / test results: Not run
- Excluded changes / rollback:
```

### نمونه سناریوهای قابل ساخت (نه دستور نصب قابلیت در تمام مشتری‌ها)

| شناسه | سناریو | خروجی مورد انتظار | وابستگی |
|---|---|---|---|
| SC-01 | رندر اولیه صفحات/آرشیو | HTML SSR، لینک و pagination قابل دسترس؛ JS اختیاری | 01، 13، 07 |
| SC-02 | انتقال صفحه و GSAP | پرده اختیاری، عدم تداخل/گیر، BFCache و no-JS | 04، 05، 06 |
| SC-03 | فیلتر آرشیو AJAX | نتایج قبلی در انتظار، آخرین درخواست برنده، خطا/empty، History و cleanup | 04، 06، 13 |
| SC-04 | فرم تماس/سفارش | validate/permission/CSRF متناسب، پیام موفقیت واقعی، retry امن، anti-spam | 04، 10، 13 |
| SC-05 | مدیا و کاروسل | لودینگ محلی، کنترل لمس/کیبورد، reduced motion، بدون دانلود بی‌دلیل | 01، 05، 06 |
| SC-06 | CPT و داده قابل انتقال | مدل داده در plugin و نمایش در theme؛ عدم حذف داده با تغییر قالب | 13، 07 |
| SC-07 | تغییر URL و مهاجرت | mapping واقعی، 301/404/410 مرتبط، Sitemap و Canonical؛ بدون تغییر production تا تأیید | 01، سند مهاجرت مخصوص مشتری |
| SC-08 | ارتقای PHP/WP و deploy | backup و staging، ماتریس سازگاری، smoke test و rollback | 08، 12، 13 |
| SC-09 | ویجت سفارشی Elementor | وابستگی فقط هنگام استفاده، init/dispose در editor/frontend؛ در track جدا | 15 |

این جدول نقشه سناریوها است، نه داکیومنت اجرایی کامل هر قابلیت. فقط برای قابلیت‌های واقعی پروژه، سناریوی مستقل با معیار و تست بنویس.

### مثال مختصر معیار پذیرش SC-03 (Custom)

- Given آرشیو SSR با لینک واقعی، When JS خاموش است، Then فیلتر/صفحه‌بندی معمول سرور کار می‌کند (اگر قابلیت فیلتر در scope است).
- Given فیلتر A و B سریع پشت هم، When پاسخ A دیرتر می‌رسد، Then فقط B وارد DOM شود؛ busy متعلق به B باقی بماند تا پایان خودش.
- Given درخواست ناموفق، When timeout/HTTP error، Then محتوای قبلی در دسترس، پیام و امکان retry وجود دارد، پرده سراسری فعال نیست.
- Given نتایج جدید commit شده، When ماژول motion دوباره راه‌اندازی می‌شود، Then trigger و listener تکراری ایجاد نشود؛ refresh محلی/سراسری فقط در صورت نیاز پس از layout.
- نتیجه واقعی این مثال: Not run؛ باید در پروژه مشتری اجرا شود.

## 5. دستور Codex و ترتیب مرجع

1. index فعال را بخوان. profile و سناریوی مورد نیاز را شناسایی کن. اگر نسخه/مالک نامشخص است، از فایل‌های واقعی استخراج یا به‌عنوان مانع گزارش کن.
2. اسناد مرتبط را انتخاب کن؛ همه فایل‌ها را بدون توجه به scope اعمال نکن. در Elementor فقط اهداف مشترک و سند مخصوص آن؛ در Custom بدون الزام page builder.
3. audit سورس، تصمیم‌گیری محدود، پیاده‌سازی، تست و گزارش. هیچ تنظیم/افزونه/ارتقای اجباری یا deploy خودکار بدون تأیید.
4. امنیت و قرارداد رسمی نسخه نصب‌شده و الزامات پروژه مقدم‌اند؛ تعارض استانداردها گزارش شود. نسخه profile، scenario و standard مستقل است.

**تاریخچه:** v1.0 — چارچوب تفکیک Standard/Profile/Scenario/Runbook/QA. سناریوهای فهرست‌شده هنوز اسناد اجرایی مستقل نیستند.
