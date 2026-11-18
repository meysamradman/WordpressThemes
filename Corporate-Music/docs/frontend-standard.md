# پروفایل فرانت‌اند علی اسدی

## مسیر سریع

- این پروژه [قالب سفارشی](../../WEB-DEVELOPMENT-STANDARDS/wordpress-custom/07-WORDPRESS-CUSTOM-DEVELOPMENT-v1.0.md) است. برای CSS/JS، GSAP، لودینگ، کش و QA فقط ردیف موضوع در [مرجع ریشه](../../WEB-DEVELOPMENT-STANDARDS/00-STANDARDS-INDEX-v1.2.md) را باز کنید.
- [سناریوهای پروژه](scenarios.md)، [معماری](architecture.md)، [رسانه](media.md) و [قرارداد وردپرس](wordpress.md) جزئیات این نصب را دارند.

## نسخه و محیط

| موضوع | مقدار ثبت‌شده |
|---|---|
| نوع قالب | Classic PHP مستقل. [نسخه‌ها و حداقل اعلام‌شده](../../WEB-DEVELOPMENT-STANDARDS/00-VERSIONS.md). |
| محیط محلی | corporate-music / 8098. تنظیمات اجرا در [Compose](../tools/local-wordpress/compose.yaml) است. |
| ابزار ساخت | از جدول نسخه‌ها و [lockfile](../package-lock.json) استفاده کنید. Node فقط برای build است. |
| تولید و مصرف | ابزار build در tools/build.mjs، loader در radmanct/inc/assets.php و فایل‌های runtime در radmanct/dist. دستور و جزئیات نسخه از فایل مالک خوانده شوند. |
| کش و انتشار | شرایط واقعی از پاسخ دامنه و قرارداد هاست خوانده شوند. نسخهٔ PHP وب، نسخهٔ DB هاست یا TTL از مقادیر محلی نتیجه‌گیری نشوند. |

## تصمیم‌های محلی

- Peyda فونت عنوان و بدنه است. تصویر ثابت طراحی برگهٔ اختصاصی از WebP قالب می‌آید. کاور محتوایی از attachment بومی و variantهای وردپرس مصرف می‌شود.
- پردهٔ کوتاه منتظر دانلود موتور اسکرول یا رسانه نمی‌ماند. inc/assets.php وابستگی دسکتاپ را انتخاب می‌کند و src/js/runtime/scroll.js مالک Smooth/Native است. اگر کاربر قبل از آمادگی موتور اسکرول کند، Native برای همان بازدید برقرار می‌ماند.
- site.js نوع ورودی را روی ریشه ثبت می‌کند و base.css کادر لینک و دکمه را برای لمس/کلیک پنهان می‌کند. فوکوس کیبورد، فیلد متنی و بازگشت فوکوس حفظ شوند. [مرجع focus-visible](https://www.w3.org/WAI/WCAG22/Techniques/css/C45.html)
- تصمیم‌های رسانه در [media.md](media.md) و هدر واقعی، کش و رفتار میزبان در [hosting-performance.md](hosting-performance.md) ثبت می‌شوند.
- Yoast در صورت فعال بودن مالک metadata است. noindex محلی حفظ شود. انطباق کامل صفحات با این ثبت تأیید نشده است. برای بازبینی، مسیر نماینده را با [روش QA](../../WEB-DEVELOPMENT-STANDARDS/shared/12-VERIFICATION.md) و معیارهای سناریوی همان پروژه بررسی کنید.

## مالک‌های این پروژه (علی اسدی)

| موضوع | مسیر/قرارداد |
|---|---|
| معماری/دادهٔ مشتری | `docs/architecture.md`، `radmanct/inc/client.php`، `radmanct/inc/pages/music-home.php` و editor بومی |
| SEO | `radmanct/inc/seo.php`، `docs/wordpress.md`؛ Yoast و fallback مستقل از صفحات |
| آهنگ | `radmanct/inc/content/music.php`، `radmanct/inc/music.php`، `template-parts/music/`؛ credits/date/lyrics در editor |
| ویدئو/گالری | `radmanct/inc/content/collections.php`، `radmanct/inc/videos.php`، `radmanct/inc/albums.php`؛ archive/detail بومی و API رسانه |
| پلیر/کاروسل/AJAX | `radmanct/src/js/ui/audio-controls.js`، `music-player.js`، `carousel.js`، `pages/archive.js` و `docs/media.md` |
| Input جست‌وجو | `radmanct/template-parts/layout/header.php` برای آهنگ، `radmanct/searchform.php` برای نوشته، `radmanct/inc/setup.php` برای واگذاری query به وردپرس و کنترل ورودی نامعتبر. رنگ و فوکوس از CSS صاحب همان فرم خوانده شوند. |
| Card و قاب رسانه | `radmanct/inc/music.php` و `radmanct/template-parts/ui/video-grid.php`؛ سطح مشترک در `radmanct/src/css/ui/media-card.css` و قاب، hover و focus در `radmanct/src/css/ui/media-frame.css`. |
| انیمیشن ورود | `radmanct/inc/ui/text-entrance.php`، `radmanct/src/css/ui/text-entrance.css` و `radmanct/src/js/ui/text-entrance.js`. هدف‌های تأییدشده با `data-rdc-en` انتخاب می‌شوند. ردیف‌های کاروسل، شعر و جدول مستقیم نمایش داده می‌شوند. |
| دارایی/حرکت/ظاهر | `radmanct/inc/assets.php`، `radmanct/src/js/runtime/lifecycle.js`، tokens/base و page entryهای واقعی |

مسیرهای نماینده: خانه، بیوگرافی/تماس، آرشیو/جزئیات track، clip و album، taxonomy و صفحه‌بندی آن‌ها. نام/کاور/اطلاعات اثر، متن شعرِ موجود در جزئیات و لینک دانلود عمومی در HTML بمانند؛ این به معنی دریافت خودکار فایل پخش نیست. پلیر ثابت فقط خانه و پلیر inline فقط جزئیات آهنگ، با یک مالک مشترک و preload=none؛ کنترل شعر به dock اضافه نشود. پالت اختصاصی مشتری حفظ و CSS از Webtalik بی‌اجازه کپی نشود. این ثبت اثبات انطباق همهٔ مسیرها نیست.
