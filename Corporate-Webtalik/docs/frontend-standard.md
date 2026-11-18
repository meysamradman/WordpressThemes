# پروفایل فرانت‌اند ویبتالیک

## مسیر سریع

- این پروژه [قالب سفارشی](../../WEB-DEVELOPMENT-STANDARDS/wordpress-custom/07-WORDPRESS-CUSTOM-DEVELOPMENT-v1.0.md) است. برای CSS/JS، GSAP، لودینگ، کش و QA فقط ردیف موضوع در [مرجع ریشه](../../WEB-DEVELOPMENT-STANDARDS/00-STANDARDS-INDEX-v1.2.md) را باز کنید.
- [سناریوها](scenarios.md)، [قراردادهای ویژهٔ فرانت‌اند](frontend-contracts.md)، [معماری](architecture.md)، [رسانه](media.md) و [قرارداد وردپرس](wordpress.md) جزئیات این نصب را دارند.

## نسخه و محیط

| موضوع | مقدار ثبت‌شده |
|---|---|
| نوع قالب | Classic PHP مستقل. [نسخه‌ها و حداقل اعلام‌شده](../../WEB-DEVELOPMENT-STANDARDS/00-VERSIONS.md). |
| محیط محلی | webtalik / 8095. تنظیمات اجرا در [Compose](../tools/local-wordpress/compose.yaml) است. |
| ابزار ساخت | از جدول نسخه‌ها و [lockfile](../package-lock.json) استفاده کنید. Node فقط برای build است. |
| تولید و مصرف | ابزار build در tools/build.mjs، loader در radmanct/inc/assets.php و فایل‌های runtime در radmanct/dist. دستور و جزئیات نسخه از فایل مالک خوانده شوند. |
| کش و انتشار | شرایط واقعی از پاسخ دامنه و قرارداد هاست خوانده شوند. نسخهٔ PHP وب، نسخهٔ DB هاست یا TTL از مقادیر محلی نتیجه‌گیری نشوند. |

## تصمیم‌های محلی

- تایپوگرافی H1 با Hafez و متن و H2–H6 با Peyda طبق tokens/base است. دکمه، آیکون، heading و عنوان صفحه از [مالک‌های UI](shared-ui.md) خوانده شوند.
- عنوان کوتاه برای مسیرهای مشمولِ بدون هیرو، و Cover برای صفحات تعیین‌شده استفاده می‌شود. پارالاکس، ورود و اسکرول از مالک‌های [معماری](architecture.md) خوانده شوند.
- Yoast در صورت فعال بودن مالک metadata است. noindex محلی حفظ شود. انطباق کامل صفحات با این ثبت تأیید نشده است. برای بازبینی، مسیر نماینده را با [روش QA](../../WEB-DEVELOPMENT-STANDARDS/shared/12-VERIFICATION.md) و معیارهای سناریوی همان پروژه بررسی کنید.

## مالک‌های این پروژه

| موضوع | مسیر/قرارداد |
|---|---|
| معماری و دادهٔ مشتری | `docs/architecture.md`، `radmanct/inc/client.php`، `radmanct/inc/services.php` (دادهٔ services/specs) |
| متادیتا و fallback | `radmanct/inc/seo.php`، `docs/wordpress.md`؛ Yoast در حالت فعال |
| بخش‌ها و headingهای مشترک | `template-parts/sections/`، `template-parts/ui/content-heading.php` و `docs/shared-ui.md`؛ Page Title مستقل |
| دارایی و حرکت | `radmanct/inc/assets.php`، `radmanct/src/js/runtime/lifecycle.js`، `radmanct/src/js/motion.js` و page entry مصرف‌کننده |
| رسانه | attachmentهای بومی و `docs/media.md`؛ مدار/نقطه‌های Hero و نگاشت تصاویر با مالک فعلی |
| پالت/فونت/دکمه/آیکون | tokens/base و rendererهای ثبت‌شده در `docs/shared-ui.md` |

خانه، خدمات اصلی، چهار زیرصفحهٔ خدمات، درباره/تماس، وبلاگ و نمونه‌کار routeهای نمایندهٔ بازبینی‌اند؛ هیچ‌کدام با این ثبت خودکار تأیید نشده است. فونت، رنگ، timing و طرح تأییدشده طبق skill پروژه حفظ شوند.
