# ممیزی دو بسته مستندات — ۲۰ نوامبر ۲۰۲۶

**مبنای بررسی:** `Corporate-Webtalik.zip` (۲۷ فایل)، `WordpressTheme(1).zip` (۲۹ فایل). این ممیزی دربارهٔ **محتوای مستندات** است، نه اجرای کامل کد/DB/خروجی HTTP سایت.

## نتیجه

- **استانداردهای مشترک:** ۲۹ فایل ساختار مناسبی دارند؛ اما دو بخش بالا هنوز استثناهای یک پروژهٔ دیگر را با راهنمای عمومی آمیخته‌اند. این بسته **استانداردهای مشترک را تغییر نداده** تا تنظیمات مشتری دیگر بی‌اجازه تغییر نکند.
- **ساختار قابل حفظ است:** استانداردهای مشترک در `WEB-DEVELOPMENT-STANDARDS`، قراردادهای فنی نصب در `docs/` و محتوا در `docs/content/` از هم جدا هستند. اسناد جزئیات هر صفحه (خدمات/محصولات) مرجع متن همان صفحه می‌مانند.
- **ناسازگاری مهم برطرف‌شده در این نسخه:** `AGENTS.md` قبلاً «Do not add products» می‌گفت ولی `docs/content/products/` سه لندینگ آماده داشت. حالا لندینگ محتوایی از ساخت CPT/WooCommerce تفکیک شده و معماری ذخیرهٔ داده نیازمند تأیید است.
- **توضیح شناسهٔ فنی/برند:** `radmanct`/`RadmanCT`/`rdc-` شناسهٔ سازگاری‌اند، نه نام برند نمایش‌داده‌شده. نام عمومی سایت «وبتالیک» باقی می‌ماند. برای جلوگیری از شکستن متا یا فایل‌ها، خود شناسه‌ها تغییر نکرده‌اند.
- **تصویر:** کدهای صفحهٔ داخلی نباید featured image برگه را خودکار در Hero/Page Title بگذارند. دادهٔ محتوایی Media Library و طراحی تزئینی ثابت بر اساس مالک بخش جدا تعریف می‌شوند؛ قرارداد فعلی compact title بدون تغییر اجرا می‌ماند.
- **محتوا تقویت شد:** دستور نگارش، دفتر تصمیم‌ها، پذیرش صفحه، چارچوب صفحه خانه/درباره/تماس، و نقشهٔ بازبینی وبلاگ اضافه شد. متن نهایی یا جزئیات فناوریِ بدون شاهد ساخته نشد.

## موارد باز که Codex نباید خودکار حل کند

| اولویت | مورد | شاهد در فایل | اقدام لازم |
|---|---|---|---|
| P0 | آرشیو نمونه‌کار در لوکال `/projects/` است اما `/portfolio/` قبلاً پیشنهاد شده بود | `docs/content/portfolio.md` و `docs/content/migration-urls.md` | تأیید آدرس نهایی؛ تست تغییر rewrite و ریدایرکت‌های موردی |
| P0 | URLهای نهایی سه محصول هنوز تصویب انتشار نشده‌اند | `docs/content/products/*.md` | تأیید Slug و H1/SEO Title پیش از مهاجرت |
| P0 | نقشهٔ اجرایی کامل URLهای قدیمی وجود ندارد؛ جدول فعلی نمونه‌ای است | `docs/content/migration-urls.md` | خروجی کامل WordPress + Search Console + Sitemap + بک‌لینک جمع‌آوری و با هر URL تطبیق شود |
| P0 | دوره‌های قدیمی و دسترسی خریداران قبل از حذف تعیین تکلیف نشده | `docs/content/migration.md` | تصمیم حقوق دسترسی/دانلود و پاسخ URLها قبل از خاموش‌کردن فروش |
| P1 | ۳ خدمت و ۳ محصول هنوز متن کامل تأییدشده/تصاویر واقعی ندارند | `docs/content/services/` و `docs/content/products/` | برای هر صفحه brief و evidence، سپس copy مصوب و QA |
| P1 | استانداردهای **مشترک** GSAP و Elementor هنوز مثال/تنظیم اختصاصی `Hub / رحیمی` دارند؛ قانون عمومی و استثنای مشتری باید جدا شوند، بدون تغییر رفتار قبلی | `WEB-DEVELOPMENT-STANDARDS/shared/05-GSAP-ANIMATION-STANDARD-v1.1.md` و `wordpress-elementor/15-WORDPRESS-ELEMENTOR-DEVELOPMENT-v1.0.md` | انتقال پروفایل موردی به سناریوی پروژهٔ مالک پس از بازبینی؛ نسخه عمومی بی‌طرف بماند |
| P1 | موارد برند/هویت Radman در مستندات فنی legacy باقی‌اند | `AGENTS.md`, `docs/naming.md`, `docs/architecture.md` | فقط public-facing copy را از legacy پاک کنید؛ به شناسه‌های فنی دست نزنید |
| P1 | وضعیت Sitemap قدیمی `Couldn't fetch` و تأخیر داده Search Console تأیید نشده | `docs/content/migration.md` | گزارش تازه و بررسی HTTP و Sitemap قبل از انتشار |
| P1 | پوشش مقاله‌های قدیمی و ۴ ایدهٔ جدید بدون بررسی هم‌پوشانی هنوز کامل نیست | `docs/content/blog.md` | inventory واقعی، طرح موضوعی و شواهد فنی را تکمیل کنید |
| P2 | ارجاع به برخی مسیرهای کد/ابزار در بستهٔ مستندات موجود نیست | `docs/frontend-standard.md`, `docs/naming.md` | در مخزن واقعی بررسی شود؛ نبودن در ZIP به معنی نبودن در Git نیست |

## جلوگیری از تکرار

- استاندارد فنی عام را **فقط** از فهرست مشترک بخوانید؛ در فایل وب‌تالیک فقط استثنای برند، نسخه، محتوا و سناریوی مربوط بنویسید.
- متن هر خدمت در فایل خودش، متن محصول در فایل خودش، مقاله در خود WordPress/پیش‌نویس همان مقاله، URL انتقال در `migration-urls.md`، وضعیت تأیید در `DECISION-REGISTER.md` و قواعد عمومی نگارش در `EDITORIAL-QUALITY.md`.
- `docs/architecture.md` قرارداد اجرای قالب فعلی است؛ آن را با متن صفحات یا تصمیم ساخت CPT محصولات جایگزین نکنید. برای هر تغییر فقط فایل‌های مالک همان دامنه خوانده شوند.

## تغییرات این بسته

`AGENTS.md` (رفع تعارض و مسیر تحویل محتوا)، `docs/content/README.md`، `docs/content/pages.md`، `docs/content/blog.md`، `docs/content/portfolio.md`؛ فایل‌های جدید `docs/content/DECISION-REGISTER.md`، `EDITORIAL-QUALITY.md` و `PAGE-ACCEPTANCE.md`. سایر مدارک از ZIP اصلی بدون بازنویسی عمده منتقل شده‌اند.

## منابع رسمی برای بازبینی دوره‌ای

- People-first / AI content: https://developers.google.com/search/docs/fundamentals/creating-helpful-content و https://developers.google.com/search/docs/fundamentals/using-gen-ai-content
- URLs and redirects: https://developers.google.com/search/docs/crawling-indexing/site-move-with-url-changes
- WordPress CPT: https://developer.wordpress.org/plugins/post-types/registering-custom-post-types/
- Image SEO: https://developers.google.com/search/docs/appearance/google-images
- Internal links: https://developers.google.com/search/docs/crawling-indexing/links-crawlable
