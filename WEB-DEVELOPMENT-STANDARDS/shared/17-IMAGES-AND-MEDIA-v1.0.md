# 17 — Images & Media Optimization v1.0
**آخرین بازبینی:** 2026-11-26 | مشترک برای پروژه‌های سفارشی، Elementor و سایر CMSها. طراحی و منابع مشتری در پروفایل پروژه ثبت می‌شوند. MUST = سیاست تیم، SHOULD = پیش‌فرض قابل تغییر با شاهد؛ بودجهٔ تصویر تابع نقش و اندازهٔ نمایش است.

## 1. منبع و نقش تصویر (قبل از نوشتن کد)
| نقش | منبع پیشنهادی | قانون |
|---|---|---|
| artwork ثابت طراحی | فایل محلیِ قالب/asset pipeline | مسیر مدیریت‌شده، نه تصویر شاخص خودکار؛ 07 برای نحوه رندر |
| تصویر قابل ویرایش سکشن | attachment ID یا قرارداد CMS همان بخش | نقش مشخص؛ جدا از تصویر شاخص عمومی برگه |
| کارت مقاله/پروژه/محصول/اثر | تصویر شاخص همان آیتم | اندازه کارت و fallback؛ بدون درخواست جدا برای هر کارت |
| جزئیات محتوایی | کاور، گالری یا تصویر متن | نمایش مطابق طرح؛ حذف یا تکرار بی‌دلیل ممنوع |
| تصویر اشتراک اجتماعی | تصویر منتخب metadata owner | مستقل از نمایش Hero؛ fallback معتبر مرتبط |
| تزئین | CSS background، SVG امن، pseudo-element | بدون alt ساختگی؛ اطلاعات مهم داخل HTML |

- نوع صفحه، منبع، نسبت، focal point، alt، مالک و نیاز LCP را مشخص کن؛ از روی `has_post_thumbnail()` به‌تنهایی تصمیم نمایش Hero نگیر. وجود تصویر در editor مجوز نمایش خودکار نیست. تصویر شاخص در محتوای واقعی و کارت‌ها قابلیت لازم باقی می‌ماند.
- عکس ثابت در source دارایی محلی، نه URL لوکال hardcoded، نه base64 بزرگ در PHP/CSS/JS. تصویر قابل ویرایش در Media Library با attachment ID، نه duplicate file/path و نه ذخیره چند URL ناپایدار. تغییر theme نباید محتوای مشتری را نابود کند.
- محتوایی که باید در Google Images کشف شود با `img` دارای `src` یا `picture` با `img` fallback در HTML؛ CSS background فقط تزئینی یا مورد واقعاً مناسب. تصویر دارای متن کلیدی جای H1، توضیح و لینک واقعی را نگیرد.

## 2. فرمت، اندازه، responsive
- WebP/AVIF برای عکس در صورت پشتیبانی خط پردازش/مرورگر و کیفیت مناسب؛ JPEG/PNG وقتی لازم و SVG فقط برای برداری امن. فایل اصلی رسانه را بدون بکاپ/مجوز destructive تبدیل نکن؛ metadata، شفافیت، EXIF حساس، copyright و orientation بررسی شود. SVG upload عمومی با افزودن MIME به‌تنهایی ممنوع؛ sanitize معتبر، مجوز و شیوه سرو امن. SVG نامطمئن inline نشود.
- اندازه فایل از عرض واقعی CSS × DPR هدف و نسبت تصویر انتخاب شود. مثال کارت 360px: variant تقریباً 360/720؛ تصویر 1200px در desktop نیاز متفاوت دارد. این‌ها مثال هستند؛ بودجه برای هر نقش و شبکه واقعی، نه سقف ساختگی 100KB برای همه. کیفیت بصری و bytes مقایسه شود. تصاویر منبع بسیار بزرگ را در هر کارت نفرست.
- `srcset` با descriptor واقعی، `sizes` برابر عرض نمایش در breakpoint واقعی، نه `100vw` برای همه کارت‌ها. `picture` فقط برای art direction یا format fallback؛ یک img و یک semantic DOM. `width` و `height` نسبت ذاتی درست (یا wrapper با ratio برای crop)؛ CSS responsive مثل `max-inline-size:100%;block-size:auto`. `object-fit:cover` فقط قاب crop شده. برای نسبت متفاوت موبایل فضای صحیح رزرو شود.
- WordPress: `wp_get_attachment_image()` / `get_the_post_thumbnail()` در جای مناسب؛ `add_image_size()` فقط نقش‌های مشترک مورد مصرف، ثبت در `after_setup_theme`. سایز جدید برای uploads قدیمی خودکار تولید نمی‌شود؛ regeneration محدود با بکاپ و تست. سایزهای core را حدسی حذف نکن. URL اصلی فقط برای دانلود/zoom/جزئیات نیازمند؛ srcset بومی و alt از metadata، `sizes` دقیق در renderer. fallback اندازه/عدم تصویر را تست کن.

## 3. اولویت بارگیری و LCP
- ابتدا با trace عنصر LCP را مشخص کن (ممکن است متن باشد). تصویر محتمل LCP از HTML اولیه قابل کشف، بدون lazy و در صورت نیاز `fetchpriority="high"`؛ نه هم‌زمان lazy/high. `loading="eager"` به معنی اولویت بالا نیست. بقیه تصاویر viewport اول بی‌دلیل lazy نشوند؛ تصاویر خارج دید lazy بومی، بدون اسکریپت تکراری. به heuristic core فرصت بده و فقط برای موارد شناخته‌شده override کن.
- preload فقط برای منبع حیاتی که به‌موقع کشف نمی‌شود؛ URL، `imagesrcset`/`imagesizes` و CORS با مصرف نهایی برابر باشند؛ preload همزمان همه اسلایدها/فرمت‌ها/تصاویر موبایل و دسکتاپ ممنوع. `decoding="async"` hint است، نه تضمین؛ Core را بی‌دلیل override نکن.
- اسلاید اول و تصویر visible لازم را اولویت بده؛ اسلایدهای بعدی با بارگیری بومی/شرطی درست و آزمون Network. CSS مخفی یا `display:none` تضمین عدم دانلود تصویر/JS نیست. محتوای ضروری برای ایندکس فقط با click/interaction ایجاد نشود.
- Skeleton/placeholder ابعاد مطابق تصویر، بدون درخواست دوباره فایل. تصویر شکسته/عدم تصویر: placeholder محلی یا قاب ساده بدون لینک خالی، loop `onerror` یا دانلود منبع تکراری ممنوع. تصاویر content را به انتظار پرده/GSAP وابسته نکن.

<a id="image-naming-and-alt"></a>

## 4. نام فایل، متن جایگزین و دادهٔ رسانه

### نام و URL فایل

- برای فایل جدید نام کوتاه و توصیفی با کلمات انگلیسی، حروف کوچک، عدد و خط تیره انتخاب کن. الگو `[topic-]subject[-view][-variant].ext` است. نام پایه با `^[a-z0-9]+(?:-[a-z0-9]+)*$` سازگار باشد. بخش موضوع، برند، نما یا شمارهٔ نسخه فقط در صورت کمک به تشخیص دارایی اضافه شود. از نام دوربین، فاصله، خط زیر، آوانویسی مبهم و عبارات تبلیغاتی پرهیز کن. این قرارداد تیمی برای فایل‌های تازه است. [راهنمای نام تصویر گوگل](https://developers.google.com/search/docs/appearance/google-images)
- نام در محدودهٔ ذخیره‌سازی یکتا باشد و پسوند با فرمت واقعی فایل مطابق باشد. یک دارایی مشترک را با همان attachment و URL استفاده کن. نام thumbnailها، hashها و خروجی optimizer/CDN تابع pipeline است. تغییر H1، عنوان مقاله یا نام غیرایده‌آل، دلیل کافی برای تغییر URL منتشرشده نیست. [سیاست انتقال تصویر](20-TECHNICAL-SEO-SCHEMA-v1.0.md#image-seo)

### Alt بر اساس کاربرد

| کاربرد | خروجی |
| --- | --- |
| عکس، کاور یا اسکرین‌شات اطلاع‌رسان | توضیح کوتاه و طبیعی از اطلاعات قابل‌مشاهدهٔ مفید، به زبان محتوای صفحه |
| تزئین یا عکس تکراری بدون اطلاعات اضافه در همان لینک | `alt=""` صریح روی `img`. CSS برای تزئین هم قابل استفاده است. |
| لوگو یا تصویر تنها در لینک یا دکمه | نام مقصد یا عملکرد؛ اگر همان کنترل نام قابل‌دسترسی کافی دارد، از تکرار پرهیز کن. |
| نمودار، دیاگرام یا متن مهم در تصویر | Alt کوتاه شامل پیام اصلی یا نوشتهٔ ضروری؛ جزئیات، واحدها و دادهٔ واقعی در متن یا جدول قابل‌دسترسی کنار تصویر. |

- پس از دیدن خود تصویر مشخص کن با حذف تصویر چه معنا یا عملی از دست می‌رود. Alt را در همان کاربرد بازبینی کن. H1، H2، slug، نام خام فایل، نام برند، کلمهٔ کلیدی و پیشوند «تصویر از» فقط وقتی بیایند که معنا را بهتر منتقل می‌کنند. محدودیت ثابت طول برای سئو اعمال نشود. برای تصویر محتوایی Alt مفقود اصلاح شود و مقدار عمداً خالی در رندر حفظ شود. [درخت تصمیم Alt در W3C](https://www.w3.org/WAI/tutorials/images/decision-tree/)
- برای کاور مقاله می‌توان از مفهوم slug الهام گرفت. نام تصاویر متن، موضوع خود تصویر یا سکشن را بیان کند. Caption تکمیل زمینه، منبع یا اعتبار است. توضیح نمودار یا پیام اصلی مقاله فقط داخل فایل تصویر قرار نگیرد.

### مالکیت و امنیت

- نام و URL پایدار فایل، عنوان انسانی رسانه، Alt پایه، Alt محل استفاده یا حالت تزئینی، Caption، ابعاد، MIME، مجوز و منبع نقش‌های جدا دارند. عنوان رسانه و ویژگی HTML `title` جای Alt را نمی‌گیرند. پیشنهاد خودکار Alt پیش‌نویس قابل‌ویرایش است و به بازبینی انسانی نیاز دارد. برای تصویر مشترک، اصلاح یک کاربرد نباید Alt سایر مصرف‌کننده‌ها را عوض کند.
- از قرارداد CMS، API و renderer موجود استفاده کن. در WordPress سفارشی و Elementor، Alt پایه از رسانه و کاربرد از کنترل یا رندر موجود بیاید. در Next.js از کامپوننت موجود و قرارداد نسخهٔ نصب‌شده استفاده کن. در پروژهٔ مبتنی بر Django یا API، Alt و ابعاد باید تا HTML مصرف‌کننده منتقل شوند. بررسی فقط در پنل یا دیتابیس کافی نیست. [ویژگی‌های تصویر وردپرس](https://developer.wordpress.org/reference/hooks/wp_get_attachment_image_attributes/) · [کامپوننت تصویر Next.js](https://nextjs.org/docs/app/api-reference/components/image)
- اصالت و مجوز انتشار تصویر یا نمونه‌کار بررسی شوند. دمو، موکاپ و تصویر مفهومی را در صورت احتمال سوءبرداشت مشخص کن. در اسکرین‌شات عمومی دادهٔ مشتری، توکن، شماره تماس خصوصی، اطلاعات مالی و مسیر حساس حذف یا پوشانده شوند. خوانایی متن و جزئیات رابط پس از فشرده‌سازی بررسی شود.
- در رندر attachment وضعیت منبع و قرارداد دسترسی محتوا/والد را بررسی کن؛ خصوصی و رمزدار در JSON/HTML/OG/schema/cache عمومی افشا نشوند. کنترل PHP روی markup دسترسی مستقیم فایل uploads را نمی‌بندد؛ برای رسانه خصوصی سرو محافظت‌شده لازم است. Upload با API بومی، permission، اندازه/MIME/extension واقعی و محدودیت پردازش. URL خارجی با allowlist و محافظت SSRF طبق 10/13؛ fetch remote در render هر کارت ممنوع.

## 5. ویدئو، صوت و گالری
- آرشیوها: کاور سبک و لینک مستقیم جزئیات، بدون دانلود همه فایل‌های مدیا یا iframe. برای پلیر اختیاری façade/انتخاب کاربر، اگر embed اولیه لازم است توجیه و Network ثبت شود؛ قوانین provider رعایت. native `preload="none"` برای رسانه غیرفعال، `metadata` فقط اگر لازم؛ hint است. پوستر جدا دانلود می‌شود. `autoplay` پیش‌فرض خاموش؛ در صورت طراحی ویدئوی تزئینی، muted/playsinline و reduced-motion/دیتای موبایل با fallback؛ هیچ صوت خودکار.
- watch page هدف ایندکس: ویدئوی واقعی/iframe قابل کشف بدون وابستگی به تعامل، thumbnail پایدار و crawlable، title و توضیح واقعی؛ VideoObject طبق 20 فقط در صورت داده معتبر. کشف ویدئو به معنی دانلود کامل نیست. Buffering با رویدادهای native (`waiting`, `playing`, `error` و state واقعی) یا API مجاز provider؛ نشانگر محدود به پلیر. زمان ثابت جای state نیست.
- گالری: thumbnail مناسب برای هر صفحه، full فقط هنگام zoom/download، dialog و keyboard/focus، لینک fallback. صفحه‌بندی و canonical طبق 20؛ از دانلود همه عکس‌ها/همه آلبوم‌ها اجتناب. منابع، مجوز و کیفیت‌های واقعی را حفظ کن؛ کیفیت و اندازه جعلی چاپ نشود.

## 6. تست پذیرش Codex
- [ ] mapping نقش تصویر/منبع/renderer مشخص است؛ بدون نمایش اتوماتیک یا تکرار غیرمجاز cover، بدون src خالی.
- [ ] HTML شامل alt مناسب، width/height و srcset/sizes درست؛ DevTools currentSrc در mobile/desktop/DPR و waterfall، bytes و LCP/CLS بررسی شده.
- [ ] نام و فرمت واقعی فایل جدید، Alt محتوایی و Alt خالی عمدی، نام دسترس‌پذیر لینک تصویری و یک مورد دارایی مشترک در HTML نهایی بررسی شده‌اند. مورد مشکوک مثل Alt تکراری یا نام عمومی ابتدا با مشاهدهٔ عکس سنجیده شود. در ممیزی، URLهای مصرف‌کننده، Alt مفقود و تصاویر سنگین ثبت شوند و فقط اصلاحات مصوب اعمال شوند.
- [ ] بدون eager/lazy اشتباه یا duplicate preload، بدون دانلود iframe/مدیای مخفی بی‌دلیل؛ نبود عکس، حذف attachment، خصوصی/رمزدار، JS خاموش و reduced motion تست شده‌اند.
- [ ] social preview و watch page/metadata بر اساس scope؛ thumbnail قابل fetch، وضعیت HTTP و دسترسی واقعی؛ cache cold/warm و پس از تعویض رسانه. گزارش evidence یا Not run.

## منابع رسمی
- https://developer.wordpress.org/themes/classic-themes/functionality/featured-images-post-thumbnails/
- https://developer.wordpress.org/reference/functions/wp_get_attachment_image/
- https://developer.wordpress.org/apis/responsive-images/
- https://developer.wordpress.org/reference/functions/add_image_size/
- https://developer.wordpress.org/reference/functions/wp_get_loading_optimization_attributes/
- https://developer.wordpress.org/reference/functions/wp_check_filetype_and_ext/
- https://developers.google.com/search/docs/appearance/google-images
- https://developers.google.com/search/docs/appearance/video
- https://web.dev/articles/optimize-lcp
- https://www.w3.org/WAI/tutorials/images/decision-tree/
