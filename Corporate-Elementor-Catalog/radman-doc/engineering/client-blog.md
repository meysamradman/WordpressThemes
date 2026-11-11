# طراحی وبلاگ در Child

داده و انتقال آرشیو متعلق به WordPress و موتورهای [محتوا](content-modules.md)/[AJAX](archive-ajax.md) است؛ Child فقط چیدمان و استایل این طراحی را مالک است. قواعد عمومی در [سفارشی‌سازی](client-customization.md) و تایپوگرافی در [مالک فونت](typography.md) می‌مانند.

## آرشیو و کارت مشترک

- صفحهٔ وبلاگ، posts page بومی Reading است؛ برگهٔ واسطه با query یا موتور Elementor تکراری ساخته نشود.
- `inc/blog/archive.php`، `journal.php` و `cards.php` و partialهای `template-parts/blog/archive/` آرشیو را می‌سازند. در صفحهٔ اولِ بدون فیلتر، lead تمام‌عرض یک کارت بزرگ و حداکثر دو کارت کناری دارد؛ ترکیب برای RTL آینه می‌شود.
- بخش بعدی جایگاه‌های ۴–۷ query جاری را برای یک feature و حداکثر سه tile می‌خواند؛ عنوان دستهٔ مشترک یا Latest articles است. lead و editorial فقط انتخاب read-only با post ID هستند و Loop اصلی را مصرف نمی‌کنند. feed کاملِ صفحه‌بندی‌شده پایین آن‌ها باقی است؛ تکرار عمدی یک نوشته در برجسته و feed با IDهای DOM متفاوت مجاز است. offset یا query دوم محتوای اصلی ساخته نشود.
- journal تصویر/متن افقی با دسته، عنوان، نویسنده/تاریخ، excerpt و CTA موجود دارد؛ موبایل stack می‌شود. کارت مشترک `shared/post-card.php` برای feature/tile/compact است. تصویر بومی WordPress، ابعاد/srcset، حفاظت رمز و lazy زیر fold حفظ شوند.
- آرشیو یک H1، عنوان سکشن H2، عنوان ابزارک H3 و عنوان کارت H4 دارد؛ single عنوان واقعی H1 را نگه می‌دارد. اندازهٔ بصری محلی، مجوز تغییر بی‌دلیل tag یا چاپ metadata سئوی دوم نیست.

## سایدبار و انتقال

lead تمام‌عرض می‌ماند و سایدبار زیر آن کنار بخش بعدی آغاز می‌شود. `radmanct/sidebar/embedded` پوستهٔ `Sidebars::layout()` مشترک را داخل آرشیو می‌نشاند؛ سایدبار خارج از ناحیهٔ قابل جایگزینی AJAX است. کنترل‌های افقی تکراری با `radmanct/blog/show_controls` حذف و شمار نتایج دسترس‌پذیر حفظ می‌شود. Search/Categories بومی و پیوند واقعی با fallback بدون JS باقی‌اند. سایدبار وبلاگ و محصول از رفتار natural sticky مشترک استفاده می‌کنند؛ ارتفاع محدود و اسکرول داخلی ندارند و محتوای بلند با اسکرول صفحه در دسترس می‌ماند.

ترتیب محلی جستجو/دسته‌ها/تازه‌ترین نوشته‌ها از ابزارک‌های مدیر می‌آید؛ runtime ترتیب یا محتوای ناحیه را seed/overwrite نکند. «تازه‌ترین» جای Most read است. ابزارک `rdct-blog-posts` حداکثر هشت نوشتهٔ منتشرشده، بدون رمز و با no_found_rows می‌خواند؛ WPML native، post ID صریح و Loop سراسری حفظ می‌شوند.

wrapper سایدبار فاصلهٔ ۲rem بین ابزارک و ۱rem بین عنوان/محتوا دارد؛ margin فرم/لیست فقط در همان scope reset می‌شود. دسکتاپ ناحیهٔ داخلی محدود به viewport و قابل فوکوس/اسکرول است، موبایل جریان طبیعی دارد. offset هدر/نوار مدیریت هنگام تغییر اندازه/breakpoint اندازه‌گیری می‌شود؛ scroll listener یا offset انیمیشنی تازه اضافه نشود. در smooth mode همان ScrollTrigger موجود با transform داخل parent اصلی pin می‌کند؛ بدون reparent به body، clone یا GSAP دوم و با cleanup کامل. سیاست محصولِ بدون اسکرول داخلی مستقل و در [سایدبار](sidebars.md) تعریف شده است.

ویجت Categories موجود، تنظیمات/شناسه/فرم بومی خود را نگه می‌دارد؛ در سایدبار وبلاگ، حالت فهرستی از renderer مشترک [درخت دسته](markup-css.md#css-و-مرز-کامپوننت) استفاده می‌کند. dropdown و حالت دارای walker/feed/depth سفارشی یا فیلتر خروجی `wp_list_categories` به خروجی بومی برمی‌گردند تا ادغام افزونه‌ها کنار گذاشته نشود. بلوک Categories مستقل Gutenberg به renderer جدید تحمیل نمی‌شود.

## جزئیات مقاله

`inc/blog/single.php`، `template-parts/blog/single/cover.php` و `src/css/pages/blog/single.css` طرح جزئیات را مالک‌اند. ابزارک native `rdct-article-cover` از ID نوشتهٔ جاری تصویر شاخص، تاریخ و دسته می‌خواند؛ query دوم یا متن/تصویر ثابت جای دادهٔ واقعی نیست. تصویر مربعی با گوشهٔ گرد، badge تاریخ و آیکون محلی عمدی است؛ alt/srcset/ابعاد بومی حفظ شوند.

اگر سایدبار منتخب ابزارک cover دارد، تصویر شاخص و اطلاعات تاریخ/دسته در بدنه دوباره چاپ نشوند؛ خاموش/تعویض‌کردن آن ناحیه، fallback معمول تصویر/meta را برمی‌گرداند. نوشتهٔ رمزدار cover را افشا نکند. متن اصلی، H1، وضعیت، رمز و multipage بومی باقی‌اند؛ ابزارک‌های آرشیو در single کپی نشوند و تنظیمات هر نما مستقل باشد.

## نشان صوت و ویدئو

مالک تشخیص/رندر `radmanct/inc/blog-media.php` و کنترل دستهٔ بومی `inc/admin/blog-media.php` است؛ این hint نمایشیِ وبلاگ به Child، Content یا Elementor وابسته نیست. `_rdct_blog_media_type` روی category با مقادیر `audio|video|standard` ذخیره می‌شود؛ خالی یعنی ارث‌بری. نزدیک‌ترین مقدار در هر شاخه ملاک است؛ دستهٔ والدِ هم‌زمان انتخاب‌شده، override فرزند را خنثی نکند. بین شاخه‌های مستقل ویدئو بر صوت اولویت دارد؛ standard نشان و fallback همان شاخه را متوقف می‌کند. بدون انتخاب دسته، قالب بومی audio/video قبلی fallback است. ذخیرهٔ بومی nonce و مجوز edit_term دارد؛ Quick Edit بدون فیلد، مقدار را پاک نکند. WPML مقدار را با `custom-term-field action="copy"` همگام می‌کند ([مرجع رسمی](https://wpml.org/documentation/support/language-configuration-files/custom-fields-translation-options/))؛ پذیرش واقعی گردش ترجمه جداست.

همهٔ thumbnailهای والد و Child همان `badge()` و آیکون محلی مشترک `custom-music`/`play` را مصرف می‌کنند؛ کارت رمزدار نشانی ندهد. متای دسته‌های نتایج و widget تازه‌ها دسته‌ای prime شود؛ برای تشخیص، متن کامل نوشته یا فایل رسانه واکشی نشود. CSS مستقل `blog-media-badge.css` با یک handle فقط برای مصرف نشان آماده می‌شود؛ مسیر late widget و پاسخ AJAX هم همان stylesheet را تحویل دهند. خودِ نشان، JS، درخواست صوت/ویدئو، font icon یا renderer پلیر اضافه نمی‌کند؛ [پیش‌نمایش ویدئویی اختیاری کارت](components.md#پیش‌نمایش-ویدئویی-کارت) مالک مستقل دارد. محتوای صوت/ویدئو همان بلوک بومی در جزئیات است. این تغییر روی Loop Grid واگذارشده به Elementor Pro دخالت خودکار ندارد.

## CSS/JS

`src/css/pages/blog/{lead,section,journal,single}.css` فقط برای طرح/نمای مربوط و `src/css/blog/{cards,sidebar}.css` برای مصرف واقعی آن جزء enqueue شوند. JS سایدبار از offset/lifecycle مشترک استفاده می‌کند؛ فایل خالی یا موتور archive/font تازه لازم نیست. پاسخ AJAX، CSS منتخب intro/section را پیش از commit آماده کند تا ورود از صفحهٔ بدون آن stylesheet هم درست باشد. کارهای پذیرش واقعی فقط در [نقشهٔ کار](../product/roadmap.md) نگهداری شوند؛ گزارش build/HTTP/نسخه به این سند اضافه نشود.
