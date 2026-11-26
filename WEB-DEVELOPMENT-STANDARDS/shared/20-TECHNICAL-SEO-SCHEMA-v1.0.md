# 20 — Technical SEO, Metadata & Schema v1.0
**تاریخ:** 2026-11-19 | مشترک. مالک وضعیت HTTP/index، metadata، structured data و سیاست مهاجرت URL. 01 مالک semantic HTML و اصول محتوا، 17 تصاویر. تصمیم کلمات/URL و داده کسب‌وکار در Profile هر مشتری.

## قرارداد هر route
| مسیر | سیاست پیش‌فرض |
|---|---|
| خانه/خدمات/برگه مستقل/جزئیات دارای محتوا | 200، title و H1 روشن، canonical خود، در صورت عمومی و ارزشمند index؛ لینک داخلی واقعی |
| آرشیو/CPT/taxonomy با محتوای مفید | 200، H1 مستقل از داده خالی، pagination و canonical طبق سیاست |
| search داخلی، فیلتر بی‌ارزش، preview/staging | noindex یا حفاظت مناسب؛ index policy و crawl جدا؛ محیط خصوصی با authentication |
| URL قدیمی منتقل‌شده | 301/308 مستقیم به مقصد مرتبط 200؛ نه زنجیره/loop |
| حذف بدون جایگزین | 404/410 واقعی؛ صفحه خطای مفید، نه redirect homepage |

- canonical سیگنال است، دستور تضمینی نیست؛ URL absolute نهایی، scheme/host/slash و query policy هماهنگ. صفحات pagination دارای محتوای جدا هرکدام self-canonical؛ همه را به صفحه اول canonical نکن. «یک موجودیت» بودن دلیل کافی برای duplicate بودن نیست؛ استثنای viewهای واقعاً معادل فقط با شواهد و تصمیم ثبت‌شده. لینک‌های `<a href>` صفحه‌بندی، بدون اتکا به scroll/interaction، و route مستقیم SSR/HTML معتبر. فیلترهای متعدد با سیاست URL/robots، جلوگیری از crawl trap؛ robots.txt ابزار noindex یا امنیت نیست.
- Sitemap شامل URL نهایی canonical، 200 و قابل index؛ lastmod واقعی، نه تغییر روزانه ساختگی. wp-sitemap یا SEO owner یکتا؛ URL مسدود/ریدایرکت/noindex در فهرست نباشد. robots meta/X-Robots-Tag هماهنگ؛ local/staging با access control و noindex در صورت نیاز، قبل از انتشار کنترل شود noindex اشتباهی باقی نمانده باشد.
- metadata فقط یک مالک: افزونه SEO موجود یا fallback معتبر؛ title/description/canonical/robots/OG/Twitter/Sitemap/schema/verification تکراری نشوند. `wp_head`, `wp_body_open`, `wp_footer` حفظ. نام‌ها و URL واقعی از owner؛ SEO plugin تحلیل محتوا ممکن است متن PHP سفارشی را نبیند، معیار rendered HTML و کاربر است. H1 ثابت و heading در 01؛ meta keywords غیرضروری. برای تصویر OG/social، تصویر شاخص مناسب یا asset عمومی مصوب مستقل از رندر Hero؛ URL absolute معتبر و اندازه/نوع هماهنگ. نبود تصویر به معنی اجبار placeholder یا عکس شاخص نمایشی نیست.

<a id="image-seo"></a>

## سئوی تصویر و پایداری URL

- موضوع عکس، متن کنار آن و Alt به فهم تصویر کمک می‌کنند. عکس محتوایی از HTML اولیه یا رندر قابل کشف با `img` و `src` معتبر یا `picture` با `img` داخلی بیاید. دریافت عکس عمومی به ورود، کلیک، اسکرول دستی یا بازکردن پنجره وابسته نباشد. کشف و دریافت URL تصویر عمومی با پاسخ موفق و MIME درست بررسی شود. رسانهٔ خصوصی به کنترل دسترسی واقعی نیاز دارد. [سئوی تصاویر گوگل](https://developers.google.com/search/docs/appearance/google-images) · [بارگذاری تنبل و کشف محتوا](https://developers.google.com/search/docs/crawling-indexing/javascript/lazy-loading) · [نام و Alt تصویر](17-IMAGES-AND-MEDIA-v1.0.md#image-naming-and-alt)
- Image Sitemap زمانی استفاده شود که کشف تصاویر مهم نیاز به کمک دارد. رسانهٔ عمومی معتبر به sitemap مالک فعلی افزوده شود یا sitemap تصویری جدا، بر حسب نیاز پروژه، ساخته شود. برای تصویر روی CDN بیرونی، اجازهٔ crawl و تأیید دامنه‌ها در Search Console بررسی شود. [راهنمای Image Sitemap](https://developers.google.com/search/docs/crawling-indexing/sitemaps/image-sitemaps)
- تصویر مقاله یا محصول در OG و دادهٔ ساختاریافته فقط از رسانهٔ واقعی، مناسب همان محتوا و قابل‌دریافت انتخاب شود. `og:image:alt` در صورت پشتیبانی در همان مالک متادیتا تنظیم شود. Alt بدنه، عنوان رسانه و توضیح اجتماعی هرکدام جدا ارزیابی شوند. تصویر محصول مجوز ساخت Offer، Review یا قیمت فرضی نیست.
- در رسانه‌های قدیمی ابتدا URL، کاربردها، Alt و دادهٔ Image Search Console را، در صورت دسترسی، ثبت کن. اصلاح Alt یا Caption را در URL موجود انجام بده. هنگام جابه‌جایی ضروری و مصوب، نگاشت قدیم به تصویر معادل، همهٔ ارجاع‌های HTML/CSS/CMS/API و OG/schema/sitemap، thumbnailها، CDN و کش بررسی شوند. برای انتقال دائمی، در صورت نیاز و امکان، 301/308 مستقیم تنظیم و مقصد با GET و MIME معتبر آزموده شود. HTML رندرشده و URL Inspection صفحهٔ نمونه، ارجاع‌های شکسته و گزارش Image در صورت وجود داده پس از انتشار بررسی شوند. [شیوهٔ انتقال URL](https://developers.google.com/search/docs/crawling-indexing/site-move-with-url-changes)

## Structured data
- گراف واحد JSON-LD از داده واقعی/public و مطابق محتوای قابل مشاهده. Organization/WebSite، BreadcrumbList در مسیر واقعی، Article/BlogPosting برای مقاله، Product فقط محصول واقعی، VideoObject در watch page با thumbnail/name/uploadDate معتبر و در صورت موجود embedUrl/contentUrl/duration. schema.org type معنایی و شرایط نمایش rich result گوگل دو موضوع‌اند؛ قیمت، Offer، ستاره، نویسنده، تاریخ، آدرس، review و موجودی ساختگی ممنوع. Service/SoftwareApplication بدون وعده rich result؛ FAQ در صورت کاربرد، نه ترفند رتبه.
- صفحه محصول قابل سفارش و بدون خرید مستقیم می‌تواند landing معرفی باشد؛ Offer ساختگی نگذار. thumbnailها و URL رسانه public و قابل fetch؛ محتوای خصوصی/رمزدار در schema/description/OG افشا نشود. HTML text و links در SSR، GSAP صرفاً نمایش؛ bot نباید برای کشف محتوای مهم کلیک کند.
- Analytics جدا از verification؛ ثبت شناسه واقعی با مجوز، یک مالک tag، consent و قوانین حوزه مشتری در Profile؛ داده شخصی در analytics payload یا log ممنوع.

## تغییر URL و انتشار
- موجودی URL از sitemap/crawl/GSC/backlinks موجود ثبت؛ تصمیم keep/move/remove، map دقیق URL قدیم→جدید و تست مقصد، percent encoding/پارامتر/slash و عکس/دانلودهای مهم. ریدایرکت server-side ترجیحی، fallback app مستقل از طراحی؛ فقط در cutover مصوب. ریدایرکت مرتبط حداقل یک سال و ترجیحاً پایدار. دامنه ثابت/تغییر path از Change of Address استفاده نمی‌کند. نسخه‌های تاریخی رتبه تضمین نمی‌شوند.
- لینک‌ها، canonical، hreflang واقعی اگر چندزبانه، sitemap، verification و noindex قبل/بعد هماهنگ؛ تست GET و status/location، rich results و URL Inspection در محیط مجاز؛ GSC monitor و rollback طبق 21. AJAX pagination/filter هنگام تغییر URL باید title/canonical/robots و محتوای همان URL را همگام کند و Back/Forward درست؛ تغییر head بدون سیاست canonical/type معتبر ممنوع.

## پذیرش
- [ ] HTML مستقیم و rendered برای هر نوع route (mobile/no-js) بررسی شده، H1 مستقل، head یکتا و status واقعی؛ data schema قابل اعتبارسنجی و بدون fabrication.
- [ ] canonical/index/sitemap/redirect map و تداخل plugin/theme کنترل شده؛ تصویر شاخص به زور در برگه ظاهر نشده؛ محتوای indexable و private جدا.
- [ ] تصویر عمومی در HTML و URL معتبر دیده می‌شود. Alt و حالت تزئینی در HTML نهایی، مالک تصویر اجتماعی و schema و در انتقال ضروری نگاشت و دسترسی URL قدیم و جدید بررسی شوند.
- [ ] GSC/live تست فقط با مجوز؛ نتایج SEO/PASS وعده رتبه قطعی نیستند؛ موارد اجرا نشده گزارش شوند.

## منابع رسمی
- https://developers.google.com/search/docs/appearance/title-link
- https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls
- https://developers.google.com/search/docs/specialty/ecommerce/pagination-and-incremental-page-loading
- https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data
- https://developers.google.com/search/docs/appearance/structured-data/video
- https://developers.google.com/search/docs/appearance/video
- https://developers.google.com/search/docs/crawling-indexing/site-move-with-url-changes
- https://developers.google.com/search/docs/crawling-indexing/301-redirects
