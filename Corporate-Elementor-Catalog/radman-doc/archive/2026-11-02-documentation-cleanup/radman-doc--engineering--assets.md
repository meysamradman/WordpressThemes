> آرشیو تاریخی پیش از جمع‌وجورکردن اسناد؛ مرجع قواعد جاری نیست. لینک‌ها به محل اصلی مقصدها اشاره می‌کنند.

# ساختار دارایی‌ها و مالکیت ماژول‌ها

## ممیزی محدود ماژولار بودن و تعداد درخواست‌ها — ۲۰۲۶-۱۱-۰۲

درخواست مالک: سایت ممکن است فقط وبلاگ یا کاتالوگ+وبلاگ داشته باشد؛ منابع اختصاصی ماژول/کامپوننت مصرف‌نشده نباید وارد صفحات سفارشی شوند. معماری فعلی در محدودهٔ بررسی با این قرارداد سازگار بود. پنج اجرای مستقل WordPress با فیلتر پیش از bootstrap، حالت‌های فقط وبلاگ، کاتالوگ+وبلاگ، فقط نمونه‌کار و فعال‌بودن هر دو را آزمودند؛ ثبت post type و منابع اختصاصی تابع ماژول/نمای جاری بود. گزینهٔ ماژول‌ها و محتوای سایت تغییر نکرد. ابزار موقت در `artifacts/module-assets-audit.php` است؛ این تنظیم runtime عمومی نیست.

ثبت handle به معنی دریافت نیست: ثبت رجیستری Components به‌تنهایی CSS/JS مصرفی ایجاد نکرد. جدول striped فقط پایه/سبک خودش و بدون JS، تصویر ساده فقط CSS پایه و بدون JS، Box s02 پایه/سبک منتخب و بدون JS، و Box s06/تصویر tilt منابع انتخابی و JS گرفتند. دو بار enqueue همان مورد handle تکراری نساخت؛ dependency پایهٔ مشترک نیز از مدیریت استاندارد WordPress می‌آید. نه بررسی graph منتخب در assets.test.mjs برای bootstrap، preset، تصویر/متن/کاروسل، SplitText و sticky پاس شدند. این نمونه‌ها اثبات همهٔ ترکیب‌های PHP/Classic/Atomic در مرورگر نیستند.

HTML عمومیِ درباره‌ما/وبلاگ/محصول/نمونه‌کار به‌ترتیب ۲۱/۲۷/۲۳/۲۱ stylesheet متعلق به بسته‌های رادمان داشت؛ مجموع فایل‌های minified محلی به‌ترتیب ۵۱۱۱۴/۶۹۳۸۵/۶۵۱۵۲/۵۴۰۰۹ بایت بود. این اندازهٔ خامِ فایل‌هاست، نه حجم فشردهٔ شبکه و نه اندازهٔ همهٔ درخواست‌های پویا/ثالث. CSS اختصاصی سه محتوا به صفحات دیگر نشت نکرد؛ بخش عمدهٔ پایه مربوط به هدر/منو/دکمه و جلوه‌های فعال فعلی بود. Components در هدر نیز مصرف معتبر است؛ حضور آن روی صفحهٔ بدون کامپوننت در متن، به‌خودی‌خود نشت نیست. CSS ساخته‌شدهٔ بررسی‌شده @import اجرایی نداشت؛ import سورس هنگام build حل شده بود.

تعداد کمتر فایل به‌تنهایی هدف نیست: [web.dev دربارهٔ code splitting](https://web.dev/learn/performance/code-split-javascript) هم بارگذاری هنگام نیاز و هم پرهیز از گراف بزرگِ ماژول‌های بدون bundle را توصیه می‌کند؛ [راهنمای منابع](https://web.dev/learn/performance/optimize-resource-loading) حذف CSS نامرتبط و پرهیز از زنجیرهٔ @import زمان اجرا را توضیح می‌دهد. [HTTP/2 در MDN](https://developer.mozilla.org/en-US/docs/Glossary/HTTP_2) multiplexing را توضیح می‌دهد؛ پروتکل/کش/فشرده‌سازی سرور نهایی در این ممیزی احراز نشد. گروه‌بندی فایل‌های ریزِ همیشه‌همراه باید براساس اندازه و waterfall واقعی تصمیم‌گیری شود؛ ادغام همهٔ ماژول‌های خاموش در یک bundle یا شکستن هر declaration به یک درخواست مجاز نیست.

**باز:** چند قاعدهٔ فیلتر افقی قدیمی هنوز از catalog-filters.css در bundle کاتالوگ می‌آید؛ تمام آن فایل بی‌مصرف نیست، چون شمار/فیلترهای فعال و fallback آن مصرف دارند. فرصت پاک‌سازی/تفکیک کوچک است، نه اثبات کندی. تعداد CSS پایه/هدر نیز نیازمند سنجش واقعی برای تصمیم دربارهٔ گروه‌بندی است. مرورگر، Network/Coverage، cold/warm cache، LCP/INP/CLS و بنچمارک موبایل/میزبان اجرا نشدند. نتیجهٔ این بررسی «مرزهای اصلی صحیح و بررسی‌های محدود پاس» است، نه تأیید نهایی سرعت کل سایت. سورس runtime و build در این ممیزی تغییر نکردند.

## تصاویر محتوای Child — اصلاح ۲۰۲۶-۱۱-۰۲

تصاویر محتوایی Home از attachmentهای رسانهٔ WordPress مصرف می‌شوند؛ نسخهٔ دوم آن‌ها نباید همراه Child بسته‌بندی شود. ۱۶ فایل بدون مصرف runtime از `radmanct-child/images/client/` به `artifacts/design/client-media/` منتقل شدند؛ این مسیر فقط منبع توسعه است و برای انتقال سایت جایگزین uploads و دیتابیس نیست. تصویر معرفی قالب `screenshot.png` باقی است. ابزار import مسیر منبع صریح می‌گیرد و قرارداد هندسهٔ تصویر نیز منبع توسعه را می‌خواند. وجود فایل رسانه برای Hero و هفت نگاشت مصرف‌شدهٔ گرافیک Home بررسی شد؛ رسانه و شناسه‌های دیتابیس تغییر نکردند. Build لازم نبود و سرور مالک در این مرحله تغییر نکرد.

## دارایی هدر/فوتر منتخب صفحه — بازبینی ۲۰۲۶-۱۱-۰۱

`Builder::enqueue()` فقط انتخاب نهایی هر نوع را پس از override برگه و پیش‌فرض سایت/زبان می‌خواند؛ فهرست همهٔ طرح‌های ذخیره‌شده پیمایش نمی‌شود. طرح غیرفعال و none منبع سازنده ندارند. CSS مشترک چیدمان/دستگاه/اندازه یک‌بار برای هدر و فوتر enqueue می‌شود؛ خروجی min فعلی ۱۳۹۱۴ بایت و برآورد gzip محلی آن ۲۰۶۷ بایت بود، نه اندازه‌گیری انتقال شبکه. این پایهٔ مشترک به تعداد طرح‌ها تکرار یا به فایل جدا برای هر طرح تبدیل نمی‌شود. مقادیر همان طرح در CSS variables markup منتخب می‌آیند.

sticky، شفافیت و رنگ وضعیت هدر CSS مستقل مشروط دارند. walker عناصر همان طرح فقط منابع منو/پوسته/هاور، پنل، دکمه، آیکون/لینک و کلید رنگِ مصرف‌شده را آماده می‌کند؛ محتوای template یا بلوک واقعاً انتخاب‌شده نیز طبق قرارداد خودش منابع می‌گیرد. bootstrap عمومی Theme سبک و مشترک باقی است؛ قابلیت‌های JS از روی markerهای موجود صفحه import پویا می‌شوند. منو manager مشترک دارد، drawer هنگام درخواست بازشدن می‌آید و curtain/wave فقط برای افکت منتخب دریافت می‌شوند. sticky پیشرفته و roll ممکن است GSAP مشترک را نیاز داشته باشند؛ این وابستگی به معنی دریافت همهٔ افکت‌ها نیست. فایل‌های React، رسانه‌گیر و CSS مدیریت در گراف فرانت نیستند. دارایی responsive طرح برای دستگاه‌های پشتیبانی‌شدهٔ همان صفحه مشترک است؛ این بازبینی ادعای حذف همهٔ دانلودهای مربوط به breakpoint غیرفعال ندارد.

ایراد محدود یافت‌شده: برگهٔ `elementor_canvas` بدون خروجی هدر/فوتر Theme هنوز منابع انتخاب ذخیره‌شده را enqueue می‌کرد. gate مشترک `Builder::active()` اکنون این برگه را کنار می‌گذارد؛ انتخاب ذخیره‌شده و نسخه‌های WPML تغییر نمی‌کنند و `elementor_header_footer` همچنان هدر/فوتر قالب را می‌گیرد. templateهای سفارشی که عمداً shell قالب را حذف می‌کنند باید از قرارداد موجود `radmanct/builder/allowed` استفاده کنند؛ کشف خودکار همهٔ templateهای ثالث ادعا نمی‌شود.

شواهد محدود: PHP lint، قرارداد stub انتخاب/دارایی برای Home و سایر برگه‌ها، زبان، پایهٔ مشترک، none، Canvas و Elementor full-width، به‌علاوه ۶ بررسی گراف دارایی پاس شدند. وضعیت انتقال واقعی در Network، مرورگر، WPML واقعی، cache/CDN و اندازه‌گیری سرعت این نوبت بررسی نشدند. فایل CSS/JS تغییر نکرد؛ build و ZIP تازه لازم نبودند. مرجع معماریِ انتخاب طرح: WoodMart `class-frontend.php::get_current_id/print_header_styles`؛ شناسهٔ Canvas با ماژول page-templates خود Elementor ارسالی تطبیق داده شد. framework یا asset مرجع وارد محصول نشد.

## بازبینی خانواده‌های دیگر — سورس ۲۰۲۶-۱۰-۲۷

تصویر، کاروسل، ذرات، Curtain، Flip Box، سکشن‌های چسبان، دکمه و خانواده‌های ثابت از روی dispatcher، requirements و graph خروجی بررسی شدند. تصویر برای parallax/reveal/tilt/lightbox ورودی انتخابی دارد؛ کاروسل، Marquee، fade/stack و کنترل‌های autoplay/navigation/pagination را تفکیک می‌کند؛ vendor ذرات در مسیر عمومی نیست. Boxهای hover با CSS به JS نیاز ندارند و pointer tilt ورودی اختصاصی دارد. Timeline، Accordion، جدول، نظر ثابت و Icon Box عادی موتور JS اضافه ندارند. پایه‌های کوچک و CSS شکل‌های ساده برای افزایش تعداد فایل‌ها خرد نشدند.

وابستگی اضافی دکمه اصلاح شد: `button/actions.js` دیگر `content-dialog.js` و قفل اسکرول آن را static نمی‌آورد؛ پنجرهٔ محتوا/ویدئو فقط هنگام کلیک همان عمل import می‌شود، همان‌طور که image dialog و scroll جدا هستند. پایان عمر دکمه قبل از تمام‌شدن import اجازهٔ ایجاد پنجرهٔ دیررس نمی‌دهد. graph آزمون‌شده، dispatcherهای دکمه/ذرات/Curtain را نیز از موتورهای اختیاری جدا بررسی می‌کند. قرارداد سبک‌های انتخابی به دکمه، Timeline و Accordion گسترش یافت. تأیید browser Network، سرعت واقعی و تطابق کامل همهٔ کنترل‌های Hub از این بررسی نتیجه نمی‌شود.

قواعد Modern CSS، DOM کمینه، تراز رسانه و بازبینی selectorها در [قرارداد markup/CSS](../../engineering/markup-css.md) نگهداری می‌شوند؛ این فایل مالک پوشه‌ها، وابستگی‌ها و بارگذاری است.

کنترل‌های ظاهر نظرات در metadata مستقل `testimonial/appearance.php` و CSS scoped موتور Presentation هستند؛ کاروسل collector دارد و rule مشترک را یک‌بار چاپ می‌کند. آیکون منبع SVG محلیِ انتخابی است، بدون فونت/JS/stylesheet شبکه. [قرارداد کنترل‌ها](../../engineering/carousel.md).

نظرات مشتریان ۱۹ preset مرجع‌محور s01–s19 و شش مدل قبلی دارند. registry PHP و build برای هر کدام stylesheet مستقل دارند؛ requirements فقط پایه و یک سبک منتخب را می‌گیرد و نظر ثابت JS اعلام نمی‌کند. renderer/preset مشترک قطعات را ترکیب می‌کند و فولدرهای خالی یا ۱۹ موتور تکراری ندارد. [نگاشت، کنترل‌ها و حدود پذیرش](../../engineering/carousel.md).

سایهٔ چندلایهٔ Text هستهٔ PHP مستقل `components/text/shadows.php` دارد و از Presentation موجود استفاده می‌کند؛ CSS/JS عمومی جدید ندارد. سطح/سایهٔ فلش کاروسل در دو stylesheet انتخابی `carousel/navigation-surface.css` و `carousel/navigation-shadow.css` است؛ پیش‌فرض و Marquee آن‌ها را نمی‌گیرند. [Text](../../engineering/text.md)، [کاروسل](../../engineering/carousel.md).

Text: تنظیم هندسهٔ mask و اندازهٔ قطعه در Presentation مشترک است؛ CSS mask همان chunk انتخابی قبلی است. رنگ وضعیت هدر فقط با `header_colors`، CSS/JS مستقل `text/header-state` را می‌گیرد؛ متن عادی این chunk را نمی‌گیرد و آن chunk GSAP/Swiper ندارد. [قرارداد جاری Text](../../engineering/text.md).

Image accordion خانوادهٔ Curtain از renderer/CSS مستقل `curtain/images.php` و `curtain/images.css` و chunk اختیاری `curtain/images.js` استفاده می‌کند. انتخاب CSS بین مدل تصویری و مدل قبلی انحصاری است؛ دریافت GSAP تابع عرض و preference است. [قرارداد مدل](../../engineering/components.md#image-accordion).

پارالاکس داخلی تصویر نیز ادامهٔ ۲۰۲۶-۱۰-۲۶ مستقل شد: `image/parallax.js` و `image/parallax.css` فقط با انتخاب قابلیت مصرف می‌شوند؛ GSAP تا انطباق viewport/preference دریافت نمی‌شود. frame ثابت و media متحرک از Tilt/Float/Reveal جدا هستند؛ [کنترل‌ها و شواهد](../../engineering/components.md).

خانوادهٔ Sticky Sections در ادامهٔ ۲۰۲۶-۱۰-۲۶ از فایل‌های ثبت‌نشده به رجیستری/PHP/Classic/Atomic وصل شد. dispatcher کوچک، scene/pin مشترک و سه موتور مستقل stack/panels/split پنج کاربرد را پوشش می‌دهند. CSS پایه/منتخب و GSAP محلی شرطی‌اند؛ [فهرست و حدود پذیرش](../../engineering/sticky-sections.md).

هدر/ناوبری در ادامهٔ ۲۰۲۶-۱۰-۲۶ همین قرارداد را گرفتند: خانوادهٔ JS زیر `src/js/navigation/`، layout کناری/تمام‌صفحه/تقسیم‌شده و افکت‌ها CSS انتخابی دارند؛ کد افکت GSAP فقط در اولین درخواست بازشدن پنل مربوط می‌آید. پنل مستقلِ خالی، CSS منوی آماده نمی‌گیرد و preview المنتور نیز از ورودی عمومی جداست. [فهرست فایل‌ها، Template و چرخهٔ عمر](../../engineering/navigation.md).

## تکمیل جداسازی خانواده‌های فعلی — سورس ۲۰۲۶-۱۰-۲۶

این بررسی دربارهٔ استقلال فایل و dependency خانواده‌های موجود است؛ تکمیل همهٔ امکانات مرجع و پذیرش نصب واقعی موضوع دیگری است. فایل ورودی پایدار می‌تواند dispatcher کوچک بماند؛ ایجاد wrapper خالی برای هر فایل الزامی نیست.

| خانواده | مرز بارگذاری فعلی |
|---|---|
| Icon / Table / Testimonial ثابت | CSS مربوط و فقط سبک انتخابی؛ بدون موتور JS فرانت‌اند |
| Box / Flip Box / Icon Box | پایهٔ همان خانواده و preset/جهت/چیدمان منتخب؛ JS فقط برای رفتار تعاملی لازم؛ قواعد پنهان‌کردن overlay اختصاصی Box به فایل همان preset منتقل شدند |
| Tilt مشترک تصویر/Box — ادامهٔ ۲۰۲۶-۱۰-۲۶ | `shared/frontend/pointer-tilt.js` موتور کوچک pointer/RAF/reset مشترک است؛ شدت و CSS variable هر خانواده در adapter خودش می‌ماند. Box فقط s06 marker تعاملی می‌گیرد؛ observer بی‌اثر s02/s03/s08 حذف شد و این سه سبک با CSS خود کار می‌کنند. بدون ماژول خالی reveal یا موتور تکراری. |
| Image | پایه؛ label فقط هنگام داشتن متن برچسب؛ float/shadow/tilt/reveal/lightbox هرکدام CSS مستقل. dispatcher تصویر، visibility مشترک فقط برای حرکت وابسته به دید، reveal فقط هنگام فعال‌بودن، tilt فقط هنگام انتخاب و dialog فقط پس از کلیک می‌گیرد؛ تصویر lightbox بدون حرکت observer یا listener اشاره‌گر نمی‌سازد |
| Text | dispatcher کوچک؛ rotator، reveal، SplitText خطوط و Marquee شرطی‌اند. متن rich پایهٔ کوچک مشترک دارد و inline-image، gradient و image-mask از `segments` محدودشده انتخاب و deduplicate می‌شوند؛ CSS افکت/شکل‌های قبلی نیز انتخابی است |
| Carousel / Slider / Logos / Stack / Testimonials | موتور مشترک؛ Marquee مستقل از Swiper؛ fade/stack و CSS مربوط فقط در همان حالت؛ sync و دکمهٔ بیرونی import شرطی دارند و پس از destroy نمی‌توانند اتصال دیررس ثبت کنند |
| کنترل‌های اختیاری Swiper — ادامهٔ ۲۰۲۶-۱۰-۲۶ | Autoplay، Navigation و Pagination entry مستقل و import انتخابی قبل از ساخت نمونه دارند؛ Core/A11y مشترک و بازسازی Stack با ماژول‌های همان نمونه انجام می‌شود. [قرارداد کاروسل](../../engineering/carousel.md) |
| Curtain / Particles / Hover border | هرکدام ماژول اختصاصی و marker خود دارند؛ vendor ذرات فقط از مسیر ذرات و hover border فقط برای marker مربوط import می‌شود. Curtain یک رفتار سازگارشونده دارد، نه مجموعهٔ presetهای مستقل مخلوط |

JS داخلی خانواده‌های بزرگ در `src/js/components/carousel/`، `image/` و `text/` قرار گرفت؛ dispatcherهای کوچک و IDهای عمومی PHP/Classic/Atomic حفظ شدند. مسیرهای import، preview، ابزار QA، قراردادها و manifest همراه جابه‌جایی اصلاح شدند. قواعد کوتاه واقعاً مشترک مثل focus، reduced motion، breakpoint و تنظیم سادهٔ سایه فایل جدا برای هر declaration نمی‌گیرند.

ساخت و قرارداد graph خروجی، سبک‌های PHP، انتخاب ماژول تصویر، جلوگیری از initialize دیررس، cleanup اتصال کاروسل و رفتارهای موجود بررسی شدند. این شواهد ثابت نمی‌کنند فایل‌ها روی نصب قدیمی مالک هم عوض شده‌اند یا عملکرد مرورگر اندازه‌گیری شده است؛ ZIP و نسخه تغییر نکرده‌اند.

## قرارداد ماژولار کامپوننت‌ها — CONFIRMED، ۲۰۲۶-۱۰-۲۵

تأکید مجدد مالک در ۲۰۲۶-۱۰-۲۷: تقسیم متعادل و بارگذاری انتخابی مهم است، نه تعداد فایل‌ها. در بررسی Text، تنها وابستگی موتور خط به موتور کلمه حذف شد؛ پایهٔ کوچک مشترک خرد نشد. [شرح و شواهد](../../engineering/text.md#مرز-ماژولها--بررسی-۲۰۲۶-۱۰-۲۷). بررسی قرارداد سبک‌های Box/Table/Flip Box/Icon Box/Testimonial و graphهای تصویر/کاروسل/سکشن چسبان انجام شد؛ این محدوده ادعای تکمیل تمام رفتارهای ویرایشگر نیست.

نیاز مالک در [سناریوی منابع](../../scenarios/local-assets.md) است. مالک هر renderer و رفتار معلوم باشد؛ registry فقط مسیر/metadata را اعلام کند و تمام موتورهای اختصاصی را در bootstrap عمومی import نکند. فقط منطق یا استایل واقعاً مشترک در base/shared بماند؛ فایل مشترک نباید محل جمع‌کردن شاخه‌های مستقل باشد. بارگذاری هم‌زمان چند حالت در PHP، Classic، Atomic و preview از یک انتخاب‌گر requirements پیروی کند.

الگوی پوشه‌ها بر حسب اندازهٔ واقعی ماژول:

| مسیر | مالکیت |
|---|---|
| `inc/components/<component>.php` | renderer کوچک یا ورودی پایدار PHP همان کامپوننت |
| `inc/components/<component>/` | renderer/metadata/profile و helperهای داخلی کامپوننت بزرگ؛ مثلاً `carousel/profiles.php` و `testimonial/fields.php` و `testimonial/renderer.php` |
| `src/css/components/<component>/base.css` | قواعد مشترک همان کامپوننت؛ بدون import تمام سبک‌های آن |
| `src/css/components/<component>/<style>.css` | فقط سبک انتخاب‌شده؛ افزودن سبک جدید دریافت آن در صفحات قبلی را مجاز نمی‌کند |
| `src/js/components/<component>.js` یا زیرپوشهٔ هم‌نام | رفتار اختصاصی؛ زیرماژول و effect سنگین از import شرطی و cleanup مشترک استفاده کند |
| `inc/integrations/elementor/widgets/` و `atomic-components/` | آداپتورهای بومی Classic و Atomic؛ بدون موتور دوم و تغییر شناسه‌های قدیمی |
| `src/js/admin/` و `src/css/admin/` | کنترل‌ها و کتابخانهٔ مدیریت؛ فقط روی صفحهٔ لازم |
| `dist/` | فایل‌های خروجی hashدار و manifest تولیدشده؛ تغییر مستقیم یا وابستگی به مسیر توسعه ممنوع |

پوشه‌ها خودشان request ایجاد نمی‌کنند. renderer ثابت می‌تواند JS نداشته باشد؛ style ساده می‌تواند markup مشترک داشته باشد. base کوچک تایپوگرافی/box-sizing مجاز است، ولی Swiper/GSAP، استایل‌های دیگر و رفتار دیگر کامپوننت‌ها نباید dependency اجباری آن شوند. از خردکردن هر قاعدهٔ کوچک مشترک به request جدا پرهیز شود؛ variant یا رفتار مستقل مرز جداسازی است. جابه‌جایی فایل داخلی باید تمام require/import/manifest/preview و بررسی‌های همان مسیر را همراه ببرد؛ نام helperها و ID دادهٔ ذخیره‌شده حفظ شوند.

معیار بررسی کوتاه: requirements هر سبک فقط base و variant خودش را بدهد؛ graph استاتیک bootstrap موتور نامرتبط نداشته باشد؛ نیاز JS جدا از نیاز CSS محاسبه شود؛ cleanup listener/instance و عدم دریافت مدیریت در frontend حفظ شوند. ثبت handle به معنی دریافت نیست، و build/graph جای Network و اندازه‌گیری سرعت واقعی نیست.

### وضعیت بررسی سورس — ۲۰۲۶-۱۰-۲۵

Box، جدول، Icon Box و Flip Box از قبل انتخاب variant مستقل داشتند؛ قرارداد محدود اکنون همهٔ گزینه‌های این خانواده‌ها و ۲۵ سبک Testimonial را بررسی می‌کند. Text یک stylesheet شامل highlight/fill/rotator داشت؛ آن رفتارها به `src/css/components/text/` تفکیک شدند و پنج شکل غیراصلی highlight نیز فایل انتخابی دارند. `text-effects.css` فقط قواعد مشترک سبک متن را نگه می‌دارد. نظر مشتری ثابت فقط CSS پایه/سبک دارد؛ خانوادهٔ متحرک از همان Carousel استفاده می‌کند. bootstrap Theme/Components و Marquee در بررسی graph نباید Swiper/GSAP/موتور تصویر و ذرات را به‌صورت static وارد کنند. ثبت این قرارداد به معنی تأیید Network یا سرعت همهٔ صفحات نصب مشتری نیست.

## رزولوشن تصویر در کنترل‌های Atomic — ۲۰۲۶-۱۰-۲۵

### تصویر بدون ID در بوم — اصلاح ۲۰۲۶-۱۰-۲۷

سورس واقعی `themes/elementor/assets/js/packages/editor-canvas/editor-canvas.js` نشان می‌دهد transformer تصویر در کلاینت فقط `src/width/height/alt` می‌دهد؛ برخلاف transformer PHP که `id` هم دارد. رندر AJAX نباید از این دو خروجی یک شکل فرض کند. علت گزارش جای‌نگهدار تصویر انتخاب‌شده همین اختلاف در مرز پیش‌نمایش بود؛ ثبت schema به‌تنهایی آن را پوشش نمی‌داد.

`inc/components/image-media.php` تبدیل مشترک دارد: ID موجود حفظ می‌شود؛ در نبود آن فقط URL زیر uploads همین نصب به attachment نگاشت می‌شود. نام اندازه‌دار به فایل اصلی برمی‌گردد، lookup در همان درخواست cache دارد، URL خارجی پذیرفته نمی‌شود و هیچ دریافت شبکه‌ای اضافه نشده است. پیش از رندر AJAX همهٔ تصاویر حل‌شده، شامل repeater و پس‌زمینه، normalize می‌شوند؛ ذخیرهٔ بومی ویرایشگر/Undo دست‌کاری نمی‌شود. Timeline مستقیم همین helper را با ابعاد بومی مصرف می‌کند. خروجی تصویر همچنان از WordPress و مسیر ترجمهٔ موجود است.

قرارداد محدود `tests/editor-image-media-contract.php` مقدار واقعیِ بدون ID، thumbnail محلی، cache، رد آدرس خارجی، آیتم تکرارشونده، جایگزینی placeholder و Timeline را پوشش می‌دهد. نمایش میزبان، تصاویر تغییریافته توسط افزونهٔ تبدیل فرمت/CDN و WPML واقعی تأیید نشده‌اند؛ ادعای پذیرش کامل تمام فیلدها از این بررسی نشود.

ImageControl داخل Repeaterهای کاروسل، پنل بازشونده و قطعه‌های متن اکنون props بومی Image_Control::get_props را دریافت می‌کند؛ sizes شامل اندازه‌های ثبت‌شدهٔ همان وردپرس است، نه فهرست ثابت موازی. config فقط یک‌بار در اسکریپت مدیریت چاپ و به آداپتور مشترک منتقل می‌شود.

transformer تصویر Elementor ارسالی، نام اندازه را در خروجی نگه نمی‌دارد و id/src/width/height می‌دهد. helper مستقل و بدون وابستگی Elementor در `components/image-media.php` ابعاد معتبر ۱–۸۱۹۲ را به size تابع تصویر وردپرس می‌رساند. کاروسل، Box، Icon Box، Flip Box و دو تصویر Curtain از آن استفاده می‌کنند؛ PHP/Classic با ID قدیمی و بدون ابعاد همان اندازهٔ پیش‌فرض قبلی را حفظ می‌کنند. متن rich از قبل ابعاد resolved را مصرف می‌کرد. دادهٔ ذخیره‌شده مهاجرت نمی‌کند؛ src ارسالی جایگزین پیوست معتبر نمی‌شود و نگاشت WPML، srcset و سیاست loading موجود حفظ می‌شوند. Image مستقل تنظیم رزولوشن خودش را دارد و مالک آن عوض نشده است.

قراردادهای کوتاه، اندازهٔ واقعی ارسال‌شده به WP برای این پنج renderer، استقلال دو وجه/دو تصویر، ورودی ناقص و نامعتبر و fallback قدیمی را بررسی می‌کنند. انتخاب و ذخیره/بازگشایی رزولوشن در ادیتور نصب تازه و Network واقعی هنوز تأیید نشده‌اند.

## حذف import تکراری پیش‌نمایش جدول — ۲۰۲۶-۱۰-۲۵

`elementor-atomic-components.js` همان ماژول `atomic-render-preview.js` را هم static و هم dynamic می‌گرفت و Vite هشدار می‌داد که import پویای آن chunk مستقلی نمی‌سازد. Table اکنون مانند Icon از observer مشترک از قبل import‌شده استفاده می‌کند؛ watcher میانی و flagهای Promise تکراری حذف شدند. درخواست preview فقط برای marker موجود/افزوده‌شده صادر می‌شود؛ cleanup همان observer درخواست/زمان‌سنج/منابع را آزاد می‌کند. خروجی build نهایی بدون هشدار ثبت‌شدهٔ dynamic import ساخته شد؛ پذیرش editor واقعی همچنان باز است.

## بارگذاری تصویر و اولویت LCP — ادامهٔ سورس ۲۰۲۶-۱۰-۲۵

`radmanct-components/inc/components/image-loading.php` قرارداد واحد `image_loading=auto|high|lazy` را برای Image، Box و Carousel نگه می‌دارد. هر سه renderer PHP و کنترل Select بومی Classic/Atomic همان schema را مصرف می‌کنند. Auto پیش‌فرض است: برای تصویر مستقل و تصویر اسلاید آغازین، loading/fetchpriority تحمیل نمی‌شود و تصمیم به `wp_get_attachment_image()` و سیاست استاندارد WordPress می‌رسد. Lazy فقط loading=lazy می‌دهد؛ High فقط با انتخاب صریح برای تصویر اصلی ابتدای صفحه، eager و fetchpriority=high می‌دهد. ترکیب lazy/high تولید نمی‌شود. انتخاب High برای تمام تصاویر توصیه یا خودکار نمی‌شود؛ راهنمای کوتاه کنار کنترل ثبت شده است. Box بدون تصویر s10 این فیلد را نشان نمی‌دهد. مقدار قدیمی/نامعتبر به Auto می‌رسد.

خطای قبلیِ eager اجباری برای اولین تصویر تمام کاروسل‌ها حذف شد. سایر اسلایدها lazy می‌مانند؛ در autoplay معکوس، اسلاید آغازین آخرین اسلاید معتبر است، نه آخرین ورودی خالی/Template ناموجود. renderer ابتدا محتوای هر اسلاید را فقط یک‌بار آماده و سپس اولویت تصویرهای خودش را تعیین می‌کند؛ closure/Template دوباره رندر نمی‌شود. محتوای تصویری داخل Template یا closure سیاست مستقل همان طرح را دارد و با انتخاب کاروسل بازنویسی نمی‌شود. تصویر، لینک، عرض آیتم و ترتیب گالری حفظ می‌شوند. تنظیم فقط به img متعلق به کامپوننت می‌رسد؛ preload عمومی، اسکن HTML صفحه، JS، CSS، dependency و درخواست شبکهٔ جدید ندارد.

تصویر اصلی محصول/نمونه‌کار در `Content::gallery_link()` از قبل به سیاست بومی WordPress سپرده شده و thumbnailها lazy هستند؛ این مسیر به اولویت اجباری یا کنترل موازی تغییر نکرد. لوگوی هدر/فوتر طبق [مالک رسانه](../../engineering/media.md) سیاست مستقل دارد. مرجع Image هاب `fancy-image.php::get_attachment_image_html` و تفکیک تصویر/thumbnail وودمارت بررسی شد؛ قرارداد loading/fetchpriority از [API رسمی WordPress](https://developer.wordpress.org/reference/functions/wp_get_loading_optimization_attributes/) با Context7 گرفته شد؛ منطق WooCommerce/loader مرجع وارد محصول نشد.

قرارداد PHP فراخوانی image API، حالات سه‌گانه، لینک تصویر تنها، اسلاید معکوس/خالی و رندر یک‌باره، و schema/config واقعی Atomic بررسی شد. WordPress در قرارداد renderer stub است؛ انتخاب نهایی loading در نصب، کش تصویر و LCP/Network واقعی هنوز اندازه‌گیری نشده‌اند. این تغییر عدد بهبود سرعت یا کشف خودکار Hero را اثبات نمی‌کند.

**قاعدهٔ آماده‌سازی — CONFIRMED، ۲۰۲۶-۱۰-۲۴:** در ادامهٔ توسعه، شمارهٔ نسخهٔ بسته‌ها تغییر نکند و ZIP/افزونهٔ همراه دوباره ساخته نشود. آماده‌سازی بسته فقط با درخواست صریح مالک انجام شود؛ build مرتبط برای بررسی تغییر سورس مجاز است. ZIP موجود نمایندهٔ تغییرات بسته‌بندی‌نشدهٔ بعدی نیست و تا درخواست آماده‌سازی دست‌نخورده می‌ماند.

## loader انیمیشن سازگار با هدف build — Components 0.1.33

Build هشدار TOLERATED_TRANSFORM برای await سطح ماژول در `animation.js` داشت؛ وجود خروجی ساخته‌شده این ناسازگاری با هدف es2020 را رفع نمی‌کرد. loader اکنون `loadAnimation()` با Promise مشترک است و `text-reveal-entry.js` وابستگی آماده را به موتور هم‌زمان Text می‌دهد. adapter عمومی و Atomic هر دو همین مسیر را مصرف می‌کنند؛ پس از teardown، import دیررس موتور را روی root اجرا نمی‌کند. شکست بارگذاری Promise مشترک را آزاد و به گزارشگر محلی موجود منتقل می‌شود؛ درخواست بعدی امکان تلاش مجدد دارد. URL مشترک vendor قالب و fallback محلی افزونه حفظ شده‌اند؛ حالت rich/static/hover همچنان این مسیر را مصرف نمی‌کند.

Build بدون هشدار یادشده و ۱۰ بررسی کوتاه Node شامل بارگذاری مشترک، retry، cleanup متن/فونت، reduced motion، Rotator و adapter Atomic پاس شدند. fixture قدیمی Rotator با import گزارشگر lifecycle هماهنگ شد؛ رفتار runtime آن برای گذراندن آزمون تغییر نکرد. این نتیجه ادعای نبود دریافت تکراری GSAP در تمام افزونه‌ها یا سازگاری تمام مرورگرهای قدیمی نیست؛ target خروجی نیز افزایش داده نشد.

## آماده‌سازی صفحات PHP — Components 0.1.32

گزارش جدید مالک دربارهٔ دیررس‌بودن CSS پس از `get_header()` معتبر است؛ fallback چاپ دیرهنگام، تضمین حضور CSS در head نیست. مسیر پیشنهادی طراحی اختصاصی اکنون `rdct_prepare(callable)` پیش از هدر است: callback فقط محتوای صفحه را یک‌بار رندر می‌کند؛ helperهای همان رندر منابع لازم را enqueue می‌کنند؛ HTML آماده پس از هدر چاپ می‌شود. اسکن فایل PHP، رندر آزمایشی دوم، فهرست دستی ده‌ها enqueue یا بافر کل سند وجود ندارد. حافظهٔ موقت متناسب با HTML محتوای همان صفحه است؛ این API برای پاسخ streaming نیست. callback نباید هدر/فوتر، exit یا مدیریت بافر مالک بیرونی را اجرا کند. خطای رندر، بافرهای داخلی را پاک و همان خطا را دوباره پرتاب می‌کند؛ فراخوانی پس از head رد می‌شود.

`Components::requirements()` انتخاب CSS پایه/variant و نیاز JS را مشترک می‌کند؛ `style_depends()` برای Classic/Atomic و `enqueue()` برای helperهای PHP از آن استفاده می‌کنند. registry موجود handle/manifest حفظ شده است؛ تنظیمات Image/Carousel/Text از resolver خود همان موتور عبور می‌کنند. این قرارداد جایگزین schema مستقل Builder نیست: هدر/فوتر همچنان منابع ضروری Theme را مستقل از Components دارند. preview پویای Atomic نیز adapter و lifecycle خودش را حفظ می‌کند؛ پذیرش تغییر زنده و Network هنوز باز است.

نمونهٔ `radmanct-child/templates/custom-page.php` با Components فعال از آماده‌سازی استفاده می‌کند و بدون افزونه روال عادی PHP را حفظ می‌کند. templateهای قدیمی دیگر خودکار بازنویسی نمی‌شوند؛ تا هنگام پذیرش این قرارداد، `rdct_use()` پیش از هدر یا fallback دیرهنگام آن‌ها برقرار است. ترتیب چاپ با [hook رسمی wp_head](https://developer.wordpress.org/reference/hooks/wp_head/) تطبیق داده شد. بارگذاری عمومی bootstrap قالب فعلاً برقرار است؛ حذف آن بدون قرارداد کامل کشف منو/جدول محتوای آزاد/موشن مجاز نیست. دو فایل vendor برای استقلال بسته‌ها وجود دارند، ولی adapter قالب URL مشترک را به Components می‌دهد؛ دریافت تکراری باید در نصب واقعی اندازه‌گیری شود، نه صرفاً از وجود دو فایل نتیجه گرفته شود.

بررسی کوتاه بافر، رندر یک‌باره، CSS پیش از head، variant Classic، وابستگی استاتیک/تعاملی و build انجام شد؛ Network، LCP/CLS و editor واقعی بررسی نشده‌اند. API و نمونهٔ مصرف در [کامپوننت‌ها](../../engineering/components.md) است.

## نیاز JS بر اساس رفتار — ۲۰۲۶-۱۰-۲۴

`Components::needs_script()` و `script_depends()` در افزونه predicate مشترک PHP/Classic/Atomic هستند. Box در سورس جاری فقط s06 (s02/s03/s08 از بررسی بعدی CSS-only هستند)، Image فقط lightbox یا حرکت فعال، Text فقط scroll/fill/rotate/marquee، و Carousel/Curtain/Flip Box JS می‌خواهند؛ Icon/Table/Icon Box و متن static/hover/rich CSS-only می‌مانند. متدهای عمومی widget از تنظیم همان instance استفاده می‌کنند. type-instance ویرایشگر برای قابلیت‌های قابل تبدیل، adapter را حفظ می‌کند تا تغییر حالت در ادیتور بی‌handler نماند؛ این استثنا ادعای بهینه‌شدن کل editor نیست.

handleها و engine/variantها عوض نشدند؛ ثبت handle با enqueue آن متفاوت است. Element_Base::enqueue_scripts ارسالی dependency هر instance را مصرف می‌کند؛ API رسمی با Context7 تطبیق داده شد. قرارداد محدود Classic و predicate مشترک پاس شد؛ کش دارایی Elementor، ویرایش واقعی Atomic و Network هنوز پذیرش می‌خواهند. پیش از بررسی نصب جدید کش صفحات/دارایی‌های قبلی پاک شود.

## محدودیت‌های بررسی بارگذاری

bootstrap عمومی Theme با تشخیص نشانگر DOM، قابلیت‌ها را import می‌کند؛ وجود این entry به معنی دریافت تمام chunkها نیست. اشتراک URL فروشندهٔ انیمیشن میان Theme و Components وجود دارد؛ تکرار واقعی GSAP و ترتیب دریافت CSS حرکت افقی باید در Network و graph خروجی بررسی شوند، نه از جمع حجم دو پوشه استنتاج شوند. بدون شاهد، preload عمومی یا loader موازی اضافه نشود.

**پیاده‌سازی‌شده — ۲۰۲۶-۱۰-۲۴:** bootstrap قالب و Components از `shared/frontend/lifecycle.js` استفاده می‌کنند؛ import پس از teardown اجرا نمی‌شود و cleanup خروج واقعی صفحه متمرکز است. خطای import/init/cleanup فقط با `WP_DEBUG` در console محلی گزارش می‌شود؛ Theme از attribute همان script module و Components از config موجود flag می‌گیرد. Text، Carousel، motion افقی و preview Atomic نیز خطای import را به همین گزارشگر می‌دهند؛ telemetry یا پیام عمومی اضافه نشده است. پذیرش Network/کش/Elementor و اندازه‌گیری LCP/CLS همچنان باز است؛ [چرخهٔ کامپوننت](../../engineering/components.md).

پذیرش بارگذاری باید سه شاهد جدا داشته باشد: handleهای enqueue وردپرس، فایل‌های واقعی Network شامل importهای JS/CSS، و تعداد instance/listener پس از init/cleanup. جدول زیر معیار پذیرش است؛ اجرای آن هنوز ثبت نشده است.

| صفحه | انتظار از دارایی‌های رادمان |
|---|---|
| PHP ساده یا Elementor بدون ویجت رادمان | بدون feature اختصاصی Components، GSAP یا Swiper؛ bootstrap عمومی Theme استثنای فعلی است |
| Box استاتیک در PHP/Elementor | CSS پایه و variant مصرف‌شده؛ بدون رفتار Box و bridge عمومی Components در نبود رفتار دیگر؛ اعلام PHP پیش از head |
| Box حرکتی یا Carousel | فقط رفتار و variant فعال؛ Swiper یک‌بار و fade/marquee فقط در حالت مرتبط |
| ترکیب motion قالب و Text متحرک | بررسی دریافت و instance واقعی GSAP، نه فقط نام handle |
| ویرایش مجدد، AJAX، RTL یا غیرفعال‌بودن Components/Elementor | cleanup صحیح، نبود listener/instance تکراری و ادامهٔ کار مستقل Theme |

نسخهٔ اجرایی کتابخانه‌های JS از `package.json` و `package-lock.json` می‌آید؛ وضعیت فعلی و استک در [معماری](../../engineering/architecture.md) و نسخهٔ موتور اسلاید در [کاروسل](../../engineering/carousel.md) است. قراردادهای کوتاه manifest/lock/نصب‌شدهٔ Vite، GSAP و Swiper از drift جلوگیری می‌کنند. CSS شماره‌نسخهٔ مستقل ندارد: نام hashدار فایل و manifest پس از build عوض می‌شوند، PHP همان خروجی minified را enqueue می‌کند و نسخهٔ خوانا هم‌زمان وارد سایت نمی‌شود. تغییر ظاهر editor، فایل عمومیِ variantهای بدون مصرف را فعال نمی‌کند.

وضعیت: **CONFIRMED — اصلاح مالک در ۲۰۲۶-۱۰-۲۰**. دارایی‌ها پراکنده یا هم‌معنا در چند پوشه نگهداری نشوند؛ هر فایل یک مالک و مسیر روشن داشته باشد. این قرارداد مکمل [منابع محلی](../../scenarios/local-assets.md) و [معماری](../../engineering/architecture.md) است.

## قرارداد مسیر

| مسیر | مالکیت و کاربرد |
|---|---|
| `radmanct/images/` | تصویر ثابتِ متعلق به محصول؛ دسته‌بندی قابلیتی مثل `preloader/` و `map-marker/` |
| `radmanct/images/radman_logo.svg` | لوگوی محلی مؤلف، با مسیر موجود و مصرف مشخص تنظیمات/ابزار تصویر معرفی |
| `radmanct-components/` | مالک مستقل کامپوننت‌ها؛ `inc/components/`، `src/css/components/`، `src/js/components/`، `images/components/` و `dist/` مستقل؛ آیکون پایه از source مشترک طبق ردیف بعد |
| `shared/icons/` | source واحد SVGهای پایه، LICENSE/provenance، موتور PHP و CSS کوچک؛ generator نسخهٔ نصب مستقل در `images/icons/` و `inc/icons/` هر دو بسته می‌سازد. این کپی‌های تولیدی دستی ویرایش نشوند؛ [قرارداد آیکون](../../engineering/icons.md). |
| `shared/elementor/` | source مشترک مرز attributes؛ build محلی helper PHP و macro در `inc/integrations/elementor/` هر دو بسته می‌سازد. کپی‌های نصب وابستگی به پوشهٔ shared یا ابزار Node روی هاست ندارند؛ [قرارداد Elementor](../../engineering/elementor.md). |
| `radmanct/fonts/` | فایل فونت محلی، تفکیک زبان و خانواده طبق registry تایپوگرافی |
| `radmanct/src/css/` و `src/js/` | سورس توسعه؛ ماژول‌های مدیریت، components، motion و integrations جدا و با entry صریح |
| `radmanct/dist/` | خروجی تولیدشدهٔ CSS/JS و manifest، با نسخهٔ readable/minified؛ مستقیم ویرایش نشود |
| `radmanct/inc/assets.php` | loader کد PHP برای manifest و enqueue؛ نام فایل است، پوشهٔ نگهداری تصویر نیست |
| `shared/admin/` | سورس مشترک واقعی پنل در workspace؛ هنگام build در بستهٔ مالک قرار می‌گیرد، وابستگی مسیر توسعه در نصب ایجاد نشود |
| `shared/frontend/` | رفتار کوچک واقعاً مشترک بین بسته‌های مستقل؛ از سورس import و با Vite داخل dist هر مالک بسته‌بندی شود، بدون نیاز به پوشهٔ shared روی هاست |
| `radmanct-child/` | دارایی و طراحی مخصوص همان مشتری، طبق همین تفکیک و قرارداد Child |
| `radmanct-content/` | سورس و خروجی پنلِ ماژول‌های Core؛ محصول مستقل، بدون دریافت assets غیرلازم از Theme |
| `radmanct-mcp/` | افزونهٔ اتصال مستقل؛ ابزارها در `inc/abilities/`، مدیریت در `inc/admin/` و ادغام‌ها در `inc/integrations/`؛ بدون asset عمومی اختصاصی در نسخهٔ فعلی |
| `radmanct/bundled-plugins/` | بستهٔ ZIP و manifest افزونه‌های Content، Components و MCP برای نصب محلی؛ با تصاویر یا سورس JS مخلوط نشود |
| `radmanct/screenshot.png` | محل استاندارد تصویر معرفی WordPress در ریشهٔ Theme |

تصویر ثابت جدید در پوشهٔ عمومی موازی `assets/`، داخل `dist/` یا کنار فایل PHP قرار نگیرد. thumbnailهای کامپوننت‌ها از `assets/components/` به `images/components/` منتقل شدند و URL گالری و قرارداد بررسی آن تغییر کرد. تصویر مشتری/محتوا در Media Library یا Child متعلق به همان مشتری می‌ماند؛ به والد اضافه نشود. وجود دارایی ارسالی مثل marker، مجوز ساخت سرویس نقشه یا enqueue آن نیست.

ماژولار بودن فقط تعداد پوشه‌ها نیست: PHP، CSS، JS و تصویر هر قابلیت مالک معلوم دارند؛ کد و تنظیمات در `inc/` و سورس‌های مربوط، تصویر در شاخهٔ همان قابلیت در `images/` و خروجی در manifest ثبت می‌شود. دارایی مشترک فقط یک بار نگهداری شود. بدون نیاز واقعی، پوشه یا helper عمومی تازه ساخته نشود. بارگذاری مدیریت از عمومی جدا و قابلیت خاموش/صفحهٔ نامرتبط فاقد dependency اختصاصی باشد.

موتور dialog تصویر در `shared/frontend/image-dialog.js` و gesture در `swipe.js` یک سورس دارند؛ گالری Theme و Image افزونه adapter نازک خود را مصرف می‌کنند. Vite سورس مشترک را در dist مستقل هر بسته قرار می‌دهد؛ این فایل اشتراکی مجوز وابسته‌کردن نصب Components به Theme نیست. CSS پوستهٔ هر محصول جدا می‌ماند و موتور، stylesheet مدیریت یا framework تازه ندارد. در Image، import موتور همچنان پس از کلیک و در گالری پس از marker مصرف است.

## Build، بسته و تغییر مسیر

### قرارداد همیشگی runtime — ۲۰۲۶-۱۰-۲۱

- `tools/build.mjs` خروجی readable و minified جدا تولید می‌کند؛ `dist/manifest.json` فقط نام‌های hashدار `.min.css` و `.min.js` را به loader runtime می‌دهد. `manifest-readable.json` برای خواندن/توسعه است. loaderهای Theme و Components فایل غیرminified را قبول نمی‌کنند؛ `SCRIPT_DEBUG` مسیر runtime محصول را عوض نمی‌کند. بسته‌ها از همین dist استفاده کنند، نه src.
- component دارای چند سبک باید پایهٔ لازم و تنها فایل سبک/جهت/رفتار منتخب را enqueue کند. کلاس variant صرفاً نام نمایشی نباشد؛ registry واقعی به asset منتخب نگاشت شود. minify جای حذف dependency بدون مصرف را نمی‌گیرد؛ یک باندل شامل تمام سبک‌ها نسازید. JS نیز chunk و import شرطی و dependency مشترک داشته باشد.
- RTL/LTR در حالت عادی همان CSS منطقی کوچک را شریک می‌شوند؛ این راه از دو کپی کامل همسان جلوگیری می‌کند. برای استثنای واقعی جهت، loader از entry صریح manifest همان جهت استفاده کند. WordPress [rtl replace](https://developer.wordpress.org/reference/functions/wp_style_add_data/) را پشتیبانی می‌کند؛ اما نام hashدار build را نمی‌توان با حدس `-rtl` ساخت. در صورت استفاده از API خودکار، نام‌گذاری و suffix باید دقیقاً با فایل موجود مطابق شوند؛ در غیر این صورت مسیر واقعی manifest انتخاب شود. فایل قابل‌خواندن یا هر دو جهت کامل به‌عنوان fallback پنهان enqueue نشوند.
- bootstrap کنترل‌های Atomic در `src/js/admin/atomic-colors.js` و adapter رنگ مشترک در `atomic-color-control.js` است. از Components 0.1.11 رنگ و ورودی‌های responsive اجزای UI خود Elementor را مصرف می‌کنند؛ stylesheet ظاهری موازی ندارند. Image و سایر کامپوننت‌ها همان adapter رنگ را مصرف می‌کنند و به کنترل‌های Curtain وابسته نیستند؛ [وضعیت و حدود](../../engineering/components.md).

در ۲۰۲۶-۱۰-۲۱ مصرف مستقیم jQuery از bridge کتابخانهٔ editor حذف شد. `component-editor.js` از رویداد native `elementor/init`، Promise رسمی loader و رویدادهای آماده‌شدن سند/preview استفاده می‌کند؛ Promise در سورس ارسالی Elementor `core/editor/loader/js/editor-loader.js` بررسی شد. قرارداد کوتاه readiness و بررسی عدم وابستگی مستقیم کدهای اختصاصی اجرا شد؛ این شاهد ورود موفق کتابخانه در سایت واقعی نیست. مصرف jQuery خود Elementor/WordPress یا افزونه‌های مشتری خاموش نشود. سیاست قطعی در [منابع محلی](../../scenarios/local-assets.md) است.

سورس اختصاصی ES2022+ است و `tools/build.mjs` برای تمام بسته‌ها `target: ['es2020']` دارد؛ minify فقط در خروجی Production فعال است. ES Modules و importهای شرطی حفظ می‌شوند. طبق [مستندات Vite](https://vite.dev/guide/build.html#browser-compatibility)، target تبدیل syntax است و API مرورگر را polyfill نمی‌کند؛ پشتیبانی API/CSS و عملکرد موبایل جدا ارزیابی شوند.

GSAP در manifest، lockfile و dependency نصب‌شده روی `3.15.0` یکسان و دقیق قفل است. Theme و Components از importهای npm محلی `gsap` و `gsap/ScrollTrigger` استفاده می‌کنند؛ pluginها پیش از مصرف ثبت می‌شوند. loader عمومی بر اساس marker/تنظیم قابلیت dynamic import می‌کند؛ Components در حضور Theme از URL vendor همان Theme و در نصب مستقل از vendor خودش استفاده می‌کند. entry مشترک انیمیشن، پیش‌بارگذاری عمومی GSAP نیست. بررسی نسخه در `tests/assets.test.mjs` از ارتقای ناخواسته جلوگیری می‌کند. نسخهٔ مصوب و سیاست ارتقا در [منابع محلی](../../scenarios/local-assets.md) قرار دارد.

هدر فایل عمومی `/wp-includes/js/jquery/jquery.min.js` سایت radman.webtalik.com در ۲۰۲۶-۱۰-۲۱ نسخهٔ 3.7.1 را نشان داد؛ این شاهد فایل سرو‌شده است، نه تضمین نبود جایگزینی jQuery توسط افزونه‌ای دیگر در هر صفحه. نسخهٔ سایت‌های دیگر یا وردپرس بعدی از آن استنباط نشود.

Build فعلی فقط CSS/JS را تولید می‌کند. تصویرها و فونت‌های ثابت از مسیر واقعی خود خوانده و همراه قالب بسته‌بندی می‌شوند؛ کپی دومشان داخل dist ساخته نشود. `tools/package.ps1` سورس `src/` را کنار می‌گذارد و تصویر/فونت/dist runtime را همراه محصول می‌برد. Core bundler فقط فایل‌های runtime خود افزونه را می‌گیرد. مرز مجوز دارایی‌ها و licenseهای لازم حفظ شود.

استثنای PHP runtime: sanitizer کوچک و نسخه‌قفل‌شده در `radmanct/vendor/svg-sanitize/` با LICENSE و manifest/hash همراه Theme بسته‌بندی و در Git مستثنا از ignore عمومی vendor است؛ Composer یا دریافت اینترنتی روی هاست مشتری ندارد. [نسخه، بازسازی و بارگذاری lazy](../../engineering/media.md).

پیش از انتقال، مصرف‌کننده‌های PHP/CSS/JS، ابزار تولید، تست مرتبط و مستند canonical پیدا شوند؛ از مقصد موجود یا فایل کاربر عبور نکنید. مسیرهای پایگاه داده، Media Library و فایل‌های مرجع با rename دارایی محصول تغییر نکنند. انتقال فقط فایل‌های شناخته‌شده باشد؛ حذف گروهی یا پاک‌سازی حدسی مجاز نیست.

## مرجع و شواهد

در WoodMart پوشه‌های مستقل `images/`، `fonts/`، `css/` و `js/` موجودند؛ `themes/woodmart/functions.php` نیز `WOODMART_IMAGES` را به `/images` متصل می‌کند. الگوی تفکیک نوع فایل و زیرگروه آیکون بررسی شد؛ استک فعلی Vite و src/dist رادمان حفظ می‌شود. مرجع ثالث تغییر نکرد و فایل‌هایش به محصول منتقل نشدند. شواهد تغییر در [وضعیت پیاده‌سازی](../../implementation/foundation.md) ثبت می‌شود.
