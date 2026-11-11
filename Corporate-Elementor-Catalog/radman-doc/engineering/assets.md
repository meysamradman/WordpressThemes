# ساختار دارایی‌ها و مالکیت ماژول‌ها

رفتار مورد انتظار در [منابع محلی](../scenarios/local-assets.md)، قواعد markup در [markup/CSS](markup-css.md) و کارهای باز در [نقشهٔ کار](../product/roadmap.md) است. این سند قرارداد جاری است؛ گزارش build، ممیزی و تغییرات روزانه به آن اضافه نشود.

## بارگذاری و مرز ماژول‌ها

- کاتالوگ و نمونه‌کار مستقل‌اند. ماژول خاموش نباید ثبت/کوئری/دارایی اختصاصی تحمیل کند؛ ماژول روشن هم منابعش را به صفحهٔ نامرتبط وارد نکند. هدر/فوتر منتخب، مصرف واقعی همان صفحه محسوب می‌شوند.
- Components موتور مشترک PHP/Classic/Atomic دارد؛ requirements همان instance، پایهٔ کوچک و فقط preset/رفتار منتخب را برمی‌گرداند. چند نمونهٔ یکسان یک handle می‌گیرند؛ ثبت handle با enqueue/دریافت آن متفاوت است.
- ابزارهای Common/Finder/App Loader المنتور فقط در مدیریت، ویرایش/پیش‌نمایش یا صفحهٔ واقعاً المنتوری فعال باشند؛ روی صفحهٔ PHP سفارشی حتی برای مدیر از hookهای فرانت همان Common کنار گذاشته شوند. ثبت و منابع رندر Elementor برای قالب/ویجتِ مصرف‌شده حفظ شوند؛ jQuery/React یا وابستگی سایر افزونه‌ها عمومی dequeue نشوند. استثنای مصرف واقعی ابزارها با `radmanct/elementor/frontend_tools` اعلام می‌شود.
- Admin، editor و preview از فرانت جدا باشند. React/کنترل رسانه/جدول مدیریت وارد صفحهٔ PHP نشوند. دارایی Theme مستقل از نصب ابزار build و پوشهٔ shared در هاست نهایی باشد.
- bootstrap عمومی Theme برای کشف markerهای صفحه می‌ماند. رفتارهای اختصاصی با import شرطی، و vendor فقط هنگام نیاز واقعی دریافت شود؛ وجود فایل در dist مجوز enqueue آن نیست. حذف bootstrap به قرارداد کامل کشف قابلیت‌ها نیاز دارد.
- منابع مرتبطِ همیشه‌همراه می‌توانند در خروجی گروه‌بندی شوند؛ ادغام همهٔ ماژول‌ها یا شکستن هر declaration به فایل جدا هدف نیست. تعداد درخواست، حجم انتقالی، هزینهٔ اجرا و کش با هم سنجیده شوند؛ فولدربندی یا build، اثبات سرعت نیست.
- منابع runtime محلی‌اند؛ منبع خارجی فقط در استثنای صریح سناریو، مانند Analytics فعال‌شدهٔ مالک، مجاز است. source/render موتورهای مرجع یا vendor بی‌مصرف کپی نشوند.

## طراحی سفارشی و تغییرات بعدی

- سایت مشتری هنوز در حال تکمیل است؛ طراحی فعلی خانه، وبلاگ، محصول و نمونه‌کار نهایی یا الگوی اجباری مشتری بعدی نیست. تغییر طراحی در Child همان مشتری انجام شود؛ موتور و کنترل مشترک در Components بماند و به Child وابسته نشود.
- CSS خانه/صفحهٔ سفارشی، آرشیو و جزئیات هر نوع محتوا فقط در مصرف‌کنندهٔ مربوط انتخاب شود؛ قواعد طراحی با ریشهٔ همان صفحه/سکشن محدود شوند. پایهٔ کوچک، هدر/فوتر منتخب و کامپوننت واقعاً مشترک می‌توانند میان صفحات مشترک باشند. برای هر صفحه JS خالی یا کپی موتور مشترک ساخته نشود.
- PHP سفارشی و Elementor از renderer/requirements واحد استفاده کنند؛ قابلیت منتخب، CSS/JS لازم را اضافه کند و چند instance همان handle/URL را دوباره دریافت نکنند. محتوای AJAX نیز منابع تازهٔ لازم را پیش از راه‌اندازی رفتار آماده کند؛ instanceهای قبلی دوباره ساخته نشوند.
- پس از تغییر طراحی، منابع طرح کنارگذاشته‌شده از همان مسیر مصرف حذف شوند؛ CSS/JS fallback والد و مصرف‌کنندگان دیگر حفظ شوند. اندازه، رنگ و فونت از تنظیمات/توکن مشترک ارث ببرند؛ override فقط برای استثنای همان scope باشد و با انباشت selector یا `!important` جبران نشود.

### مالکیت افکت‌ها

- معیار اشتراک، کاربرد مستقل و تکرارشونده است؛ استفاده از GSAP به‌تنهایی افکت را عمومی نمی‌کند. افکت عمومی تصویر/متن مانند پارالاکس، reveal و زوم در خانوادهٔ موجود Components و با renderer/requirements مشترک PHP و Elementor پیاده شود؛ صفحهٔ سفارشی مصرف‌کنندهٔ آن باشد.
- ترکیب، ترتیب و زمان‌بندی مختص صفحه و حرکت وابسته به هندسهٔ رسانهٔ خاص، مانند مدار و توپ‌های تصویر خانه، در Child همان مشتری بماند و فقط برای همان مصرف بارگذاری شود؛ موتور عمومی یا vendor دوباره نوشته/بسته‌بندی نشود.
- اگر نیاز اختصاصی کاربرد عمومی پیدا کرد، بخش مستقل به Components منتقل و اتصال صفحه در Child حفظ شود؛ این انتقال نباید به طراحی فعلی، Child یا ماژول اختیاری وابسته باشد. برای هر افکت جزئی کامپوننت موازی نسازید؛ ابتدا قابلیت خانوادهٔ موجود را گسترش دهید.
- افکت اختیاری با انتخاب همان قابلیت منابع بگیرد؛ GSAP/vendor فقط هنگام نیاز، ترجیح حرکت کمتر رعایت و observer/listener/timeline در teardown، تغییر breakpoint و جایگزینی AJAX پاک شود. راه‌اندازی دیررس پس از cleanup و اجرای دوبارهٔ یک instance مجاز نیست.

### روال کوتاه بهینه‌سازی

1. مصرف‌کننده و requirements واقعی را از template/renderer تا manifest دنبال کنید؛ فعال‌بودن ماژول یا وجود فایل در dist کافی نیست. فایل‌های همیشه‌همراه فقط با حفظ انتخاب‌پذیری و جلوگیری از تکرار گروه‌بندی شوند؛ موتور سنگین مستقل بماند.
2. پس از تغییر، خروجی همان صفحه و یک مصرف‌کنندهٔ مرتبط/fallback را کوتاه بررسی کنید: نبود منبع نامرتبط یا تکراری، حفظ ترتیب وابستگی و CSS پیش از نمایش، و باقی‌ماندن import شرطی. تغییر سورس با build همان مالک و hash محتوا منتشرپذیر شود؛ dist دستی اصلاح نشود.
3. تأیید اجرا به Network/Coverage و رفتار واقعی نیاز دارد: بارگذاری اولیه و تعامل/اسکرول، منابع پویا، AJAX و cleanup بررسی شوند. تا زمان دسترسی مرورگر، نتیجه فقط بررسی سورس/خروجی سرور است؛ تعداد فایل به‌تنهایی معیار سرعت یا نبود تداخل نیست.

## مرز خانواده‌ها

| خانواده | قرارداد انتخاب |
|---|---|
| Table، Icon، Icon Box عادی، Testimonial ثابت، Timeline و Accordion پایه | CSS پایه/ظاهر منتخب؛ JS صرفاً به دلیل داشتن خانوادهٔ متحرک اضافه نشود. |
| Box | پایه و preset منتخب؛ pointer tilt فقط s06 به JS نیاز دارد. s02/s03/s08 CSS-only هستند. |
| Image | پایه؛ label فقط با متن، و parallax/reveal/float/shadow/tilt/lightbox فقط با انتخاب همان قابلیت؛ dialog پس از کلیک. |
| Text | dispatcher کوچک؛ word/line reveal، rotator و marquee مستقل؛ rich/gradient/mask/inline-image فقط بر اساس segments مصرف‌شده. SplitText وارد موتور کلمه یا bootstrap GSAP نشود. |
| Carousel | موتور مشترک؛ marquee بدون Swiper؛ fade/stack، sync، autoplay/navigation/pagination و استایل فلش انتخابی. |
| Curtain، Particles و hover border | renderer/marker و chunk خودشان؛ image accordion با پنل قدیمی مخلوط نشود. vendor ذرات عمومی نیست. |
| Sticky Sections | scene/pin مشترک و stack/panels/split مستقل؛ GSAP شرطی. |
| Navigation | فقط طرح/اثر هدر منتخب؛ drawer و افکت اختیاری هنگام نیاز، بدون CSS منوی آماده برای پنل خالی. |
| Button | پایه و استایل/عمل منتخب؛ dialog/video و scroll جدا؛ CSS-only به موتور حرکت یا آیکون خالی نیاز ندارد. |

جزئیات API و شکاف هر خانواده فقط در مالک مربوط به [فهرست اسناد](../README.md) نگهداری شود. IDها، helperهای عمومی و دادهٔ قدیمی هنگام جابه‌جایی فایل‌ها حفظ شوند. base/shared محل جمع‌کردن همهٔ شاخه‌ها نیست؛ فایل/پوشهٔ خالی برای تقارن ساخته نشود.

آرشیو اختصاصی نمونه‌کار با فیلتر `radmanct/portfolio/archive_style` می‌تواند ورودی `src/css/portfolio-shell.css` را مصرف کند؛ ورودی پیش‌فرض کارت/گرید/متادیتای والد را حفظ می‌کند. قاب کوچک متن دکمه در CSS پایه است؛ handle قدیمی `rdct-button-text` alias همان پایه می‌ماند و فایل جدا دریافت نمی‌کند؛ افکت‌ها همچنان انتخابی‌اند.

## قرارداد مسیر

| مسیر | مالکیت و کاربرد |
|---|---|
| `radmanct/images/` | تصویر ثابتِ متعلق به محصول؛ دسته‌بندی قابلیتی مثل `preloader/` و `map-marker/` |
| `radmanct/images/radman_logo.svg` | لوگوی محلی مؤلف، با مسیر موجود و مصرف مشخص تنظیمات/ابزار تصویر معرفی |
| `radmanct-components/` | مالک مستقل کامپوننت‌ها؛ `inc/components/`، `src/css/components/`، `src/js/components/`، `images/components/` و `dist/` مستقل؛ آیکون پایه از source مشترک طبق ردیف بعد |
| `radmanct-components/inc/ui/` و `src/css/controls/`، `src/js/controls/` | مالک واحد فیلدهای عمومی سایت، ComboBox و درخت دسته؛ Theme adapter سازگار است و افزونهٔ Components بدون Theme/Child/Content مصرف می‌شود. [قرارداد](components.md#کنترلها-و-داده-مشترک) |
| `shared/icons/` | source واحد SVGهای پایه، LICENSE/provenance، موتور PHP و CSS کوچک؛ generator نسخهٔ نصب مستقل در `images/icons/` و `inc/icons/` هر دو بسته می‌سازد. این کپی‌های تولیدی دستی ویرایش نشوند؛ [قرارداد آیکون](icons.md). |
| `shared/elementor/` | source مشترک مرز attributes؛ build محلی helper PHP و macro در `inc/integrations/elementor/` هر دو بسته می‌سازد. کپی‌های نصب وابستگی به پوشهٔ shared یا ابزار Node روی هاست ندارند؛ [قرارداد Elementor](elementor.md). |
| `radmanct/fonts/` | فایل فونت محلی، تفکیک زبان و خانواده طبق registry تایپوگرافی |
| `radmanct/src/css/` و `src/js/` | سورس توسعه؛ ماژول‌های مدیریت، components، motion و integrations جدا و با entry صریح |
| `radmanct/dist/` | خروجی تولیدشدهٔ CSS/JS و manifest، با نسخهٔ readable/minified؛ مستقیم ویرایش نشود |
| `radmanct/inc/assets.php` | loader کد PHP برای manifest و enqueue؛ نام فایل است، پوشهٔ نگهداری تصویر نیست |
| `shared/admin/` | سورس مشترک کنترل‌ها، فیلدها، تب‌ها و ظاهر پنل‌های رادمان؛ تنظیمات و هدر/فوترساز مصرف‌کننده‌اند، طبق [کنترل‌های مدیریت](admin-ui.md#کنترلها-و-شکل-داده). هنگام build در بستهٔ مالک قرار می‌گیرد؛ وابستگی مسیر توسعه در نصب ایجاد نشود |
| `shared/frontend/` | رفتار کوچک واقعاً مشترک بین بسته‌های مستقل؛ از سورس import و با Vite داخل dist هر مالک بسته‌بندی شود، بدون نیاز به پوشهٔ shared روی هاست |
| `radmanct-child/` | دارایی و طراحی مخصوص همان مشتری، طبق همین تفکیک و قرارداد Child |
| `radmanct-content/` | سورس و خروجی پنلِ ماژول‌های Core؛ محصول مستقل، بدون دریافت assets غیرلازم از Theme |
| `radmanct-mcp/` | افزونهٔ اتصال مستقل؛ ابزارها در `inc/abilities/`، مدیریت در `inc/admin/` و ادغام‌ها در `inc/integrations/`؛ بدون asset عمومی اختصاصی در نسخهٔ فعلی |
| `radmanct/bundled-plugins/` | بستهٔ ZIP و manifest افزونه‌های Content، Components و MCP برای نصب محلی؛ با تصاویر یا سورس JS مخلوط نشود |
| `radmanct/screenshot.png` | محل استاندارد تصویر معرفی WordPress در ریشهٔ Theme |

پایهٔ کنترل سایت در مالک فرانت Theme و طراحی اختصاصی مصرف‌کننده در Child می‌ماند؛ فقط کد واقعاً مشترک میان بسته‌های مستقل به `shared/frontend/` می‌رود. جداسازی CSS، JS و renderer با مرز قابلیت باشد، نه پوشهٔ خالی یا فایل‌های ریز بی‌مصرف؛ نگاشت کنترل‌ها در [قرارداد markup/CSS](markup-css.md#css-و-مرز-کامپوننت) است.

تصویر ثابت محصول در `images/` با مالک قابلیت باشد؛ پوشهٔ موازی `assets/`، کنار PHP یا داخل dist محل سورس تصویر نیست. تصویر محتوای مشتری در Media Library یا Child همان مشتری می‌ماند؛ به والد اضافه نشود. تصاویر Home از attachmentهای WordPress مصرف می‌شوند و نسخهٔ دوم آن‌ها در Child بسته‌بندی نشود؛ `artifacts/design/client-media/` فقط منبع توسعه است و جایگزین uploads/دیتابیس در انتقال سایت نیست. `screenshot.png` استثنای استاندارد معرفی Theme است.

## آماده‌سازی PHP و رسانه

- template کدنویسی‌شدهٔ مصرف‌کنندهٔ Components، callback را با `rdct_prepare()` پیش از `get_header()` آماده می‌کند؛ خروجی ذخیره‌شده یک‌بار چاپ می‌شود تا CSS پیش از head انتخاب شود. فایل‌های قدیمی بدون تصمیم مهاجرت خودکار بازنویسی نشوند؛ `rdct_use()`/fallback دیرهنگام باقی می‌ماند.
- قطعهٔ ثبت‌شدهٔ Child از `rdct_prepare_content()`/requirements مشترک استفاده می‌کند؛ رجیستری و تقدم slotها در [سفارشی‌سازی](client-customization.md) است. CSS صفحه/سکشن دیگر enqueue نشود.
- رسانه از API بومی WordPress، ابعاد ذاتی، srcset/sizes و resolution منتخب مصرف شود. دادهٔ Atomic ممکن است src/width/height/alt بدون ID داشته باشد؛ adapter مشترک image-media مسئول normalizing و تشخیص attachment محلی است، نه هر ویجت جداگانه.
- تصویر مهم بالای صفحه بی‌دلیل lazy نشود؛ تصویر تعاملی/گالری، caption و fallback مشترک داشته باشد. [رسانه](media.md) و [markup](markup-css.md) مالک جزئیاتند.

## اشتراک runtime و چرخهٔ عمر

- `shared/frontend/lifecycle.js` مالک mount/cleanup است؛ import دیررس پس از teardown نباید instance بسازد. observer/listener/animation در خروج یا تعویض breakpoint آزاد شوند؛ خطا فقط در debug و بدون telemetry گزارش شود.
- `shared/frontend/image-dialog.js` و `swipe.js` موتور مشترک Theme/Image هستند و در dist هر بستهٔ نصب مستقل ساخته می‌شوند؛ stylesheet و adapter هر مصرف‌کننده جدا می‌ماند.
- مصرف‌کنندگان Components از جمله Liquid/Magnetic و اسکرول دکمه، core را از `src/js/animation.js` می‌گیرند؛ import مستقیم GSAP core فقط در `gsap-vendor.js` مجاز است. plugin منتخب روی همان instance ثبت می‌شود و guard چرخهٔ عمر برای import دیررس حفظ می‌شود.
- Theme URL مشترک GSAP/ScrollTrigger محلی را به Components می‌دهد؛ افزونه بدون Theme fallback خودش را دارد. وجود دو نسخهٔ فایل در بسته‌ها به معنی دو دانلود نیست؛ URL و Network معیارند. exportهای vendor باید در build حفظ شوند (`preserveEntrySignatures: exports-only`).
- پنل، هاور و موشن ترجیحات reduced-motion و viewport را رعایت کنند؛ native Elementor Interactions مالک رفتار خودش است. ثبت Classic، پوشش Atomic را ثابت نمی‌کند.

## خروجی و نگه‌داری

Vite از source مستقل، manifest و خروجی readable/minified با hash محتوا می‌سازد. runtime در debug و production خروجی minified همان مالک را می‌خواند؛ dist دستی ویرایش نشود. importهای CSS سورس باید در build حل شوند، نه به زنجیرهٔ @import شبکه تبدیل شوند. فونت/وزن/رنگ از [قرارداد تایپوگرافی](typography.md) می‌آیند؛ CSS طراحی فقط استثنای محدود همان scope است.

نسخه، بسته‌سازی و اتصال‌ها تابع [AGENTS](../../AGENTS.md) و [منابع محلی](../scenarios/local-assets.md) هستند. ابزار، docs، skills، reference themes و منبع طراحی وارد بستهٔ نصب نشوند. برای تغییر مسیر، manifest/import/preview و مصرف‌کنندگان واقعی همان قابلیت هم بررسی شوند.

پذیرش بارگذاری سه سطح دارد: انتخاب handle در WordPress، فایل‌های واقعی Network شامل importهای پویا، و instance/listener بعد از init/cleanup. بررسی یکی جای دو سطح دیگر نیست. موارد باز فقط در [نقشهٔ کار](../product/roadmap.md) نگهداری شوند؛ تاریخچهٔ این سند در [آرشیو](../archive/2026-11-02-documentation-cleanup/radman-doc--engineering--assets.md) است.
