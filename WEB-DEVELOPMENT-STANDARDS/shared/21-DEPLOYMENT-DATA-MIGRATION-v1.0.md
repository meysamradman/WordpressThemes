# 21 — Deployment & Database Migration v1.0
**تاریخ:** 2026-11-19 | مشترک؛ قرارداد فرایند، نه تنظیم واقعی سرور. 08 مالک Build/artifact، 09 کش، 18 query و 20 SEO. اطلاعات هاست، command، نسخه، پنجره انتشار و rollback در Runbook همان پروژه. هیچ deploy، push، عملیات destructive یا live change بدون درخواست/مجوز.

## 1. نوع تحویل را تفکیک کن
| وضعیت | دامنه |
|---|---|
| نصب تازه | بسته runtime، media و DB تأییدشده جدا، config امن محیط مقصد |
| تغییر فقط قالب | theme package و dependency runtime مصرف‌شده؛ نه DB import، نه تغییر core/plugin |
| تغییر محتوا یا تنظیمات | migration/patch محدود به رکورد و کلید مصوب؛ حفظ داده جدید مشتری |
| مهاجرت کامل | files/uploads/DB/URL mapping/verification و secrets خارج artifact عمومی |

- قبل از تغییر نسخه نصب‌شده واقعی PHP web/CLI، WP، DB، plugins و lockfile را ثبت؛ هدف PHP 8.4+ برای توسعه جدید، PHP 8.5 بعد از تست stack؛ نسخه‌افزایی خودکار ممنوع. backup سازگار فایل و DB در مسیر امن، قابلیت restore روی محیط مستقل آزموده شود؛ اطلاعات کاربران، uploads و backup خارج Git/ZIP عمومی. نسخه موجود AGENTS/workflow repo دست نخورده؛ commit/push دامنه‌دار و مجاز.
- Staging ایزوله، credentials/SMTP/third-party hooks و payment/tracking خاموش/محدود مطابق نیاز، noindex + authentication مناسب. از داده مشتری فقط با مجوز و ترجیحاً anonymized. secrets تولیدی کپی به local ممنوع. PHP web و CLI ممکن است متفاوت؛ compatibility و test matrix ثبت شوند.
- Build با lockfile و Node/Vite واقعی; runtime package تمام manifest/import/CSS/fonts/images/languages و فایل لازم را دارد؛ source archive جدا در صورت نیاز. dist دستکاری دستی ممنوع. فایل هش قدیمی تا نسخه cache HTML مصرف‌کننده حذف نشده نگه دار. فایل config فعال/htaccess/nginx rule از بسته قالب overwrite نشود. Permission و مالکیت واقعی هاست، نه یک عدد جهان‌شمول؛ world-writable ممنوع.

## 2. داده و تغییر دامنه
- بکاپ قبل از patch، target DB/table prefix و environment را verify؛ WP API/CLI مناسب و serialized-aware مانند `wp search-replace` با `--dry-run`، فهرست جدول محدود و بررسی GUID (به‌طور معمول GUID تغییر نکند). command نمونه باید پس از مشخص بودن old/new/site و approval آماده شود؛ جایگزینی خام SQL کل DB ممنوع. برای DB engine دیگر فایل data volume را مستقیم reuse نکن؛ export/import منطقی و compatibility آزمایشی. patch schema و داده idempotent و قابل rollback، dry-run count و records قبل/بعد.
- روی سایت زنده DB لوکال را wholesale وارد نکن؛ سفارش/کاربر/نوشته تازه حفظ شود. اگر schema migration غیرقابل بازگشت است توقف و approval. hostname، HTTPS، redirects، canonical و SEO verification طبق 20؛ محتوای خصوصی، webhook/job، cron و cache keys بررسی شوند.

## 3. پس از انتشار
- smoke test HTTP، home/internal/archive/detail، admin/login، forms، رسانه، mobile، cache anonymous/private و error logs؛ manifest/current URLs، CSS/JS بدون 404 و fallback؛ social/meta/sitemap/robots و canonical. purge هدفمند پس از تغییر data/asset؛ نه flush بدون نیاز.
- در failure از rollback مصوب و هماهنگ فایل/DB استفاده کن؛ در صورت ورود داده جدید پس از release، restore DB کامل بدون تصمیم ممنوع. معیار توقف، مسئول، زمان و backup بصورت runbook واقعی تعیین شوند. هیچ ادعای live verification بدون evidence.

## پذیرش و منابع
- [ ] دامنه تغییر و مالک، version matrix، backup/restore، dry-run، artifact list، تست و rollback ثبت شده‌اند؛ Not run مشخص است.
- https://developer.wordpress.org/advanced-administration/upgrade/migrating/
- https://developer.wordpress.org/cli/commands/search-replace/
- https://developer.wordpress.org/advanced-administration/server/file-permissions/
- https://vite.dev/guide/backend-integration.html
- https://developers.google.com/search/docs/crawling-indexing/site-move-with-url-changes
