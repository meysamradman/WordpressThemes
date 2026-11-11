# قرارداد کامپوننت‌های نمایشی مشترک

## کنترل‌ها و داده مشترک

- مالک UI عمومی `radmanct-components/inc/ui/` و `src/css/controls/` است: `fields.php` و `category-tree.php` رندر بومی را می‌سازند؛ `src/js/controls/combobox.js` موتور محلی Tom Select 2.6.2 را فقط با marker مصرفی و plugin منتخب بارگذاری می‌کند. مدیریت/Elementor editor از این UI فرانت مستقل‌اند.
- `RadmanCTComponents\UI\input_field()`، `select_field()`، `searchable_options()`، `field_assets($select,$combobox)`، `category_tree()` و `term_chips()` API پایه‌اند. درخت و chips، دادهٔ آماده و callback لینک می‌گیرند؛ برچسب‌های محصول همان chips را با URL/marker مخصوص فیلتر مصرف می‌کنند و CSS موازی ندارند. `RadmanCT\UI` در Theme adapter سازگار همین API است؛ CSS/JS یا renderer دوم ندارد. آماده‌سازی assets پیش از head انجام شود؛ در Elementor، `style_depends` از requirements مشترک می‌آید.
- Select گزینه‌های `searchable=false|true|auto`، `multiple`، `disabled` و `required` دارد؛ auto از ۸ گزینهٔ غیرخالی فعال است. نام/value بومی، reset، change، وضعیت URL و انتخاب جاری در cleanup حفظ می‌شوند؛ تغییر برنامه‌ای با `rdct:field-sync` اعلام شود. مرتب‌سازی و فهرست کوتاه ساده می‌مانند. picker ساده CSS-only با fallback بومی، ComboBox مستقل و درخت با reduced motion هستند.
- جست‌وجو از `input_field(type=search)` مشترک با آیکون محلی داخل کادر و بدون دکمه استفاده می‌کند؛ `layout=stacked|inline` و `show_label` ظاهر را تعیین می‌کنند. label مخفی برای دسترس‌پذیری در DOM می‌ماند. داخل آرشیو تایپ با مکث ۳۵۰ms و Enter به همان AJAX متصل است؛ بیرون آن Enter فرم GET بومی را ارسال می‌کند. CSS یا renderer موازی برای جست‌وجوی وبلاگ/محصول ساخته نشود.
- `rdct_taxonomy_list()` درخت یا chips را از taxonomy عمومی ثبت‌شده، با حداکثر ۲۰۰ term می‌سازد. `rdct_archive_search()` فرم GET بومی برای نوع محتوای عمومیِ قابل جست‌وجو می‌سازد؛ نوع ناموجود/خصوصی خروجی ندارد. مالک ثبت taxonomy/CPT محصول و نمونه‌کار Content است؛ این کامپوننت‌ها بدون آن نیز برای وبلاگ کار می‌کنند.
- ویجت‌های Classic `rdct-taxonomy-list`/`rdct-archive-search` و Atomic `rdct-atomic-taxonomy-list`/`rdct-atomic-archive-search` همین fields/renderer را مصرف می‌کنند؛ Atomic دارای props بومی، قالب Twig و preview سروری محدود به endpoint احراز‌شدهٔ موجود است. انتخاب نامعتبرِ داده با پیش‌فرض نامرتبط جایگزین نشود. typography در editor بومی می‌ماند؛ frontend خانواده/اندازه را ارث می‌برد و override محلی اختیاری است.
- جست‌وجو فقط داخل مقصد آرشیو رادمان به AJAX آن متصل می‌شود؛ بیرون آن GET معتبر دارد. ساخت ویجت‌های ترکیبی فیلتر/نتایج و adapter برای Loop Grid بومی Pro کار باز جداست؛ این ویجت‌ها جای API یا چرخهٔ query/History المنتور را خودکار نمی‌گیرند. [مرز renderer](renderers.md).

## پیش‌نمایش ویدئویی کارت

`inc/components/card-preview.php` و `src/css/components/card-preview.css` در Components مالک renderer/پوستر/کنترل‌اند؛ `src/js/components/card-preview-entry.js` و `card-preview.js` موتور انتخابی بدون vendor هستند. `CardPreview::frame($imageHtml,$options)` HTML تصویر بومیِ مورد اعتماد و video_id/mode می‌گیرد و `html/control` برمی‌گرداند. control باید بیرون لینک و داخل قاب `rdct-card-preview-owner` قرار گیرد؛ ID هر instance یکتاست. نبود پوستر/ویدئوی معتبر همان HTML تصویر را بدون assets و wrapper اضافه برمی‌گرداند.

Theme در `inc/card-media.php` متای `_rdct_card_video_id` و `_rdct_card_video_mode` را برای post و CPTهای فعال کاتالوگ/نمونه‌کار مالک است؛ Child موتور/دادهٔ دوم ندارد. metabox مشترک `inc/admin/card-media.php` از رسانهٔ بومی و picker مشترک `pickMedia()` استفاده می‌کند؛ `pickImage()` adapter سازگار باقی است. nonce، edit_post، autosave/revision، نوع فایل/موجودیت و mode اعتبارسنجی شوند؛ ذخیرهٔ فاقد فیلد/مجوز یا ورودی نامعتبر انتخاب قبلی را پاک نکند. فایل MP4/WebM/Ogg از uploads محلی انتخاب می‌شود؛ ویدئوی محتوای ویرایشگر مستقل است. متای attachmentهای نتایج و ابزارک تازه‌ها دسته‌ای prime شود؛ تصویرهای بدون ویدئو این هزینه/دارایی را نگیرند. فقدان Components تصویر و دادهٔ قبلی را حفظ می‌کند. WPML شناسهٔ attachment و mode را copy می‌کند.

auto در دید حداقل ۴۵٪ و hover با ماوس دقیق/فوکوس کار می‌کند؛ حداکثر دو preview هم‌زمان، با اولویت پخش صریح کاربر. src در HTML اولیه نیست؛ پوستر همان img/srcset بومی است و video از currentSrc آن استفاده می‌کند. خروج از دید/تب مخفی منبع را آزاد و تصویر را برمی‌گرداند؛ reduced motion و saveData پخش خودکار ندارند و کنترل بومی اجازهٔ پخش صریح می‌دهد. خطا/رد autoplay تصویر را حفظ کند؛ درخواست play قدیمی بعد از خروج/حذف DOM صحنه را فعال نکند. MutationObserver واحد، نتایج AJAX و حذف node را پوشش می‌دهد و lifecycle مشترک cleanup را مالک است.

frontend فقط با marker، runtime را import می‌کند. پاسخ AJAX stylesheet و URL runtime محلی را همراه marker تحویل می‌دهد؛ کنترلر آرشیو هر دو را پیش از commit آماده می‌کند تا ورود از صفحهٔ صرفاً تصویری نیز کار کند. shortcode بومی `[rdct_card_preview]` یا با post_id صریح، همان adapter/renderer را در Gutenberg و widget بومی Shortcode المنتور مصرف می‌کند؛ روی Loop Grid/تصاویر عادی Pro تغییر خودکار اعمال نمی‌شود. مرجع رفتاری محدود [Willows portfolio](https://preview.treethemes.com/elementor/willows/jane/portfolio/) بود؛ source/ویدئو/vendor آن وارد پروژه نشده است.

## رسانهٔ اختیاری و خروجی متنی — CONFIRMED، ۲۰۲۶-۱۱-۰۱

نبود/حذف/نامعتبر بودن آیکون یا تصویر نباید قاب، ستون، فاصله یا افکت معلقِ مخصوص رسانه باقی بگذارد. تنظیم ذخیره‌شده برای بازگرداندن رسانه محفوظ می‌ماند؛ حالت مؤثر خروجی از آن مستقل محاسبه می‌شود. این الزام در renderer مشترک PHP/Classic/Atomic اعمال می‌شود و به CSS سفارشی هر صفحه واگذار نمی‌شود.

دکمه با `display_settings` افکت swap/reveal، ripple و magnetic فقط‌آیکون را در نبود SVG معتبر غیرفعال می‌کند؛ text effect، magnetic کل دکمه و action مستقل حفظ می‌شوند. wrapper متنی مخصوص icon motion و CSS/JS همان قابلیت نیز بی‌دلیل تولید/مصرف نمی‌شود. نام آیکون مفقود و انتخاب افکت در `settings` اصلی پاک نمی‌شوند.

Icon Box گزینهٔ صریح none دارد؛ حذف آیکون یا تصویر نامعتبر نیز همین خروجی متنی را می‌دهد. layout پایه تک‌ستونه است؛ قواعد دو ستون و grid-column فقط در حضور واقعی فرزند icon فعال‌اند. نبود توضیح و CTA، div محتوای خالی نمی‌سازد. بدون رسانه، CSS آیکون و انیمیشن آیکون در requirements وارد نمی‌شوند.

Box در نبود خروجی تصویر، media و overlay خالی نمی‌سازد. تمام طرح‌ها به کارت با ارتفاع طبیعی متن و padding تنظیم‌شده برمی‌گردند؛ نسبت تصویر، ستون خالی، جابه‌جایی منفیِ کپشن و فاصلهٔ بالای icon مطلق در این حالت اعمال نمی‌شوند. ارتفاع صریح و رنگ/تایپوگرافی انتخاب‌شده همچنان معتبرند. سبک s10 که رسانه را نمایش نمی‌دهد، تصویر پنهان را هم درخواست نمی‌کند. انتخابگر CSS بر وجود واقعی markup تکیه می‌کند تا ID تصویر نامعتبر، شکل ظاهری را از رندر جدا نکند.

محتوای PHP ثبت‌شدهٔ Child در slotهای کاروسل، آکاردئون، Curtain، Sticky Sections و Flip Box مصرف می‌شود؛ داده و کنترل Elementor Template قبلی حفظ‌اند. API و حدود دارایی/امنیت در [مالک طراحی اختصاصی](client-customization.md) است؛ اجرای خودسرانهٔ فایل یا PHP ذخیره‌شده در مدیریت اضافه نشده است.

## زمان‌بندی مستقل افکت دکمه — ۲۰۲۶-۱۱-۰۱

مدت پیش‌فرض هر افکت از metadata مشترک می‌آید: Switch/Change برابر ۰٫۸۵ ثانیه با منحنی `.15,.85,.31,1`، Liquid برابر ۰٫۶ ثانیه با منحنی `.23,1,.32,1` و فاصلهٔ شروع ۳۰ms، Marquee افقی ۲ و عمودی ۱ ثانیه. این مقادیر از فایل‌های مستقل Button در مرجع Hub بررسی شدند؛ کپی کامل موتور مرجع انجام نشد. Liquid در ۵۰٪ محو و در ۵۱٪ به سمت مقابل منتقل می‌شود؛ فارسی همچنان کلمه‌محور است تا اتصال حروف حفظ شود. مقدار duration صریح ذخیره‌شده، حتی ۰٫۴ قدیمی، اولویت دارد؛ دادهٔ مشتری مهاجرت نمی‌شود. PHP، سازنده و کنترل خالی Classic/Atomic پیش‌فرض مشترک را مصرف می‌کنند. CSS مربوط به Switch فقط همان دکمه‌های Switch/Change را هدف می‌گیرد؛ حضور آن نباید متن دکمهٔ ساده را در hover محو کند. CustomEase فقط همراه chunk اختیاری Liquid وارد می‌شود. Text و Carousel زمان مستقل خود را دارند و با duration دکمه تغییر نمی‌کنند.

بررسی کد و graph، استقلال موتورهای Text و حالت‌های Carousel و بارگذاری انتخابی navigation/pagination/autoplay را نشان داد. این بررسی تأیید تکمیل تمام کنترل‌ها، شباهت بصری کامل با Hub یا ذخیره/Undo در ویرایشگر واقعی نیست.

## بازبینی اشتراک PHP و Elementor — ۲۰۲۶-۱۰-۲۹

مسیرهای بررسی‌شدهٔ Classic و Atomic، helperهای PHP و requirements/style_depends مشترک را مصرف می‌کنند؛ bootstrap عمومی و handler المنتور از lifecycle مشترک با مالکیت مستقل روی همان root استفاده می‌کنند. موتور دوم یا مهاجرت داده لازم نبود. قراردادهای graph برای جداسازی افکت‌های تصویر، متن، کاروسل و سکشن چسبان پاس شدند؛ این شاهد تکمیل همهٔ کنترل‌ها یا پذیرش بصری Elementor نیست.

خانواده‌هایی که observePreviewMarkers را مصرف می‌کنند اکنون در هر سند یک MutationObserver و صف requestAnimationFrame مشترک دارند. امضای داده و cleanup هر خانواده مستقل است؛ حذف آخرین مصرف‌کننده observer و فریم معلق را پاک می‌کند. خطای callback یک خانواده مانع به‌روزرسانی بقیه نمی‌شود. خروج marker از محدوده نیز cleanup دارد. previewStyle آزادسازی تکراری را بی‌اثر می‌کند تا CSS نمونهٔ دیگر زودتر حذف نشود و stylesheet متعلق به میزبان را حذف نمی‌کند. observerهای تخصصی دیگر در این تغییر ادغام نشده‌اند؛ APIهای موجود، UI بومی، Classic و PHP حفظ شده‌اند.

## ورودی پیش‌نمایش مدیریت — ۲۰۲۶-۱۰-۲۹

endpoint احراز‌شدهٔ `rdct_component_render` پیش از بارگذاری renderer، مجوز edit_posts یا edit_theme_options، POST و `_ajax_nonce` رشته‌ای غیرخالی تا ۱۲۸ بایت با بررسی native را می‌خواهد. payload سقف ۲۵۶KiB و عمق JSON برابر ۳۲ دارد؛ محدودیت کوچک‌تر موجود برای سایر نوع‌ها حفظ است. منابع PHP تابع allowlist قبلی‌اند و مسیر فایل از کاربر گرفته نمی‌شود. قرارداد کوچک capability/method/nonce/JSON پیش از renderer پاس شد؛ مرز امنیتی عمومی در [سناریو](../scenarios/security-access.md) و بررسی واقعی در foundation است. تغییر به permission اسناد Elementor یا WPML اضافه نشد.

## تقدم اندازه‌های واکنش‌گرا و استایل میزبان — ۲۰۲۶-۱۰-۲۸

**تصحیح پذیرش پس از تصویر مالک:** تأیید اندازه‌ها به معنی دیده‌شدن متن/آیکون نبود. قانون عمومی `.rdct-site a` رنگ Solid را می‌پوشاند و متن و پس‌زمینه هر دو `rgb(48,93,168)` شده بودند. specificity رنگ لینک عمومی با `:where(.rdct-site) a` پایین آمد تا سبک خود کامپوننت، شامل رنگ‌های انتخابی و hover، اولویت داشته باشد؛ رنگ دلخواه کاربر بازنویسی نشد. در همان preview پس از build، متن «دکمه» و stroke آیکون سفید `rgb(255,255,255)` روی پس‌زمینهٔ آبی دیده شدند؛ اندازهٔ آیکون همان ۱۰ انتخاب‌شده باقی ماند. جهت preview برای دریافت stylesheet تازه تغییر و به RTL برگردانده شد؛ صفحهٔ دارای تغییرات ذخیره‌نشده reload یا ذخیره نشد. قرارداد رنگ عمومی لینک و Build پاس شدند. پذیرش بصری این نمونه انجام شد؛ ادعای پذیرش تمام طرح‌ها یا حالات hover نمی‌شود.

گزارش مالک از هدرساز: کنترل icon size مقدار ۱۰ داشت ولی SVG به‌دلیل مقدار پایهٔ inline، ۲۰ پیکسل می‌ماند؛ gap نیز ۲۰ در کنترل و ۱۲ در خروجی بود. `Presentation::render()` اکنون فقط propertyهای root را که override معتبر responsive دارند از style پایهٔ inline به ابتدای قاعدهٔ desktop همان نمونه منتقل می‌کند. مقدار عادی در عرض‌های بدون override حفظ می‌شود و قواعد انتخابی desktop/laptop/tablet/mobile می‌توانند آن را عوض کنند؛ propertyهای نامرتبط دست‌نخورده‌اند. مقادیر پایه در hash کلاس هم لحاظ می‌شوند تا دو نمونه با fallback متفاوت مشترک نشوند. ورودی بدون override CSS اضافه نمی‌گیرد؛ selector فرزند و HTML کاربر یا موتور جدید ساخته نشد. مقادیر HTML-entity به‌صورت حدسی decode و وارد style-tag نمی‌شوند.

CSS قدیمی Theme برای دکمه‌های ساده به `.rdct-button:not([data-text-effect])` محدود شد تا reset `font:inherit` و padding/color آن روی Button مشترک اعمال نشود؛ سبک Button متعلق به همان component/preset باقی است. مقدار صفرِ اندازهٔ کادر آیکون در renderer واکنش‌گرای مشترک به auto تبدیل می‌شود، نه کادر صفر پیکسلی.

قرارداد PHP برای Button اندازهٔ متن/آیکون، چهار دستگاه و auto صفر؛ و برای موتور مشترک Flipbox با fallback desktop متفاوت و override mobile پاس شد. قرارداد موجود Icon Box/Curtain و ورودی‌های Classic/Atomic هم پاس شدند. در مرورگر همان هدرساز، icon=11 واقعاً 11px، سپس مقدار قبلی 10px؛ متن آزمایشی 30 واقعاً 30px؛ gap=20px و padding=10px 19px دیده شد. تغییر آزمایشی با Undo به snapshot قبلی بازگشت و وضعیت «تغییر ذخیره‌نشده‌ای ندارید» تأیید شد؛ ذخیرهٔ layout انجام نشد. این شواهد تأیید تمام کنترل‌ها و ویجت‌های واقعی Elementor یا همهٔ کامپوننت‌ها نیست؛ اصلاح تقدم پایه در موتور مشترک برای مصرف‌کننده‌های مربوط اعمال شده است.

## Accordion

پایهٔ مستقل با helper `rdct_accordion($items, $options)` در ۲۰۲۶-۱۰-۲۷ ساخته شد. فیلدهای محتوا و انتخاب Template با `themes/hub-elementor-addons/elementor/widgets/accordion.php` تطبیق داده شدند؛ framework مرجع وارد محصول نشده و ادعای پوشش تمام استایل‌های Hub نداریم. موتور PHP، metadata و requirements بین Classic/Atomic/کتابخانه مشترک‌اند. هر بخش title، rich content یا شناسهٔ Template دارد؛ Template از مسیر موجود با کنترل انتشار، ترجمه و جلوگیری از recursion رندر می‌شود.

باز و بسته‌شدن با `details/summary` بومی، کیبورد و focus مرورگر است؛ aria-expanded دستی یا click handler موازی نیست. برای حالت تک‌بخش، name مشترک یکتا در همان نمونه تولید می‌شود؛ حالت چندبخش name ندارد. حداکثر ۱۰۰ آیتم، عنوان خالی حذف، تگ عنوان محدود و متن پالایش می‌شود. نشانگر مثبت/منفی یا فلش با pseudo-element تزیینی و بدون آیکون‌فونت است. ترتیب منطقی، Flex و padding منطقی با RTL کار می‌کنند. بازبودن اولیه با صفر/شماره تعیین می‌شود.

فایل base قواعد مشترک را دارد؛ `lined.css` و `boxed.css` فقط هنگام انتخاب می‌آیند. هیچ JS/GSAP فرانت برای این رفتار لازم نیست؛ افزودن انیمیشن آینده باید مستقل، محلی و با reduced-motion باشد. ظاهر فعلی رنگ/فونت را از محیط می‌گیرد و تایپوگرافی Classic از کنترل بومی است؛ Atomic از تنظیمات بومی استایل میزبان استفاده می‌کند. موتور repeater موجود با schema جدا مصرف می‌شود.

**OPEN:** تعامل تک‌بخش در مرورگرهای فاقد پشتیبانی `details[name]` به بازبودن چندبخش افت می‌کند؛ محتوا همچنان قابل دسترسی است. پذیرش واقعی موبایل، ویرایشگر، Template تو‌در‌تو و WPML واقعی تأیید نشده‌اند. سبک‌های ویژه و انیمیشن Hub بدون نمونه و درخواست بعدی ادعای تکمیل ندارند.

## Timeline

پایهٔ ۲۰۲۶-۱۰-۲۷ در `inc/components/timeline.php` و helper عمومی `rdct_timeline($items, $options)` است؛ Classic و Atomic همان renderer و metadata را مصرف می‌کنند. آیتم‌ها شامل تصویر رسانه، تاریخ نمایشی، تاریخ میلادی ماشینی اختیاری، عنوان و متن rich پالایش‌شده‌اند؛ حداکثر ۱۰۰ آیتم و خروجی خالی حذف می‌شود. شناسهٔ تصویر از WPML نگاشت و خروجی تصویر از API وردپرس با ابعاد و srcset بومی تولید می‌شود. HTML یک فهرست مرتب با نقش list است؛ figure فقط برای تصویر واقعی و wrapper متن فقط برای گروه‌بندی لازم Grid می‌آیند. ترتیب DOM مراحل تغییر نمی‌کند.

`base.css` فقط قواعد مشترک و `alternating.css` چیدمان Grid، خط/نقطه و موبایل را دارد. سمت شروع منطقی start/end است؛ RTL ترتیب منطقی ستون‌ها را تعیین می‌کند. سبک فعلی تنها `alternating` است؛ گزینهٔ نمایشی برای طرح‌های تأییدنشده اضافه نشده. requirements پایه و فقط CSS سبک منتخب را می‌دهد؛ JS/GSAP/کاروسل dependency نیستند. Repeater اتمیک از موتور موجود با schema مخصوص استفاده می‌کند؛ کنترل و موتور جدید موازی ساخته نشده‌اند.

کنترل‌های پایه: استایل، سمت تصویر اول، تگ عنوان، نمایش خط، فاصلهٔ مراحل/ستون‌ها، نسبت و گوشهٔ تصویر و عنوان دسترس‌پذیر. رنگ و فونت از محیط به ارث می‌رسند. طرح‌ها، حرکت‌ها و کنترل‌های ویژهٔ بعدی پس از نمونهٔ مالک تعیین می‌شوند. پذیرش بصری مرورگر، ویرایش واقعی Atomic/Classic و WPML واقعی باز است؛ build و schema آزمون‌شده جای آن را نمی‌گیرند.

## Button — ۲۰۲۶-۱۰-۲۷

**اصلاح سورس ۲۰۲۶-۱۰-۳۰:** ابعاد مشترک دکمه اکنون px/em/rem می‌پذیرند؛ Classic از واحدهای Slider بومی و Atomic از کنترل responsive موجود و انتخاب واحد بومی استفاده می‌کند. کلیدهای عددی قبلی px می‌مانند. height/width/padding/font-size سفارشی در حالت preset نادیده گرفته می‌شوند تا مقدار مخفی قدیمی طرح انتخاب‌شده را عوض نکند؛ اندازهٔ صفر کادر آیکون همچنان auto است. ارتفاع خط و فاصلهٔ حروف/واژه‌ها به metadata و renderer مشترک اضافه شدند؛ Classic همان Group Typography بومی را نگه داشته و به مقادیر مشترک نگاشت می‌شود، بدون کنترل تکراری. واحد/اعشار/فاصلهٔ منفی و laptop در قرارداد بررسی شدند. این اصلاح شکاف واحد و سه property یادشده در بند قدیمی را می‌بندد؛ Global/Dynamic و پذیرش همهٔ افکت‌ها همچنان مستقل‌اند.

**بازبینی Hub و اصلاح محدود — Components 0.1.46:** کنترل‌های واقعی `params/button.php` و assets/rules دکمهٔ Hub با renderer و adapterهای رادمان مقایسه شدند. وزن، حالت قلم، تبدیل حروف و تزئین متن اکنون در schema و renderer مشترک دکمه‌اند؛ PHP، Atomic و سازنده از همان گزینه‌ها استفاده می‌کنند. Classic کنترل Typography بومی و شناسه‌های ذخیره‌شدهٔ قبلی را نگه می‌دارد و مقادیر `button_typography_*` به گزینه‌های مشترک نگاشت می‌شوند؛ کنترل تکراری برای این چهار مورد ساخته نشد. خانوادهٔ فونت از سیاست محلی قالب ارث می‌برد. رابط responsive دکمه در Classic مسیر laptop را نیز دارد؛ دسترسی آن تابع فعال‌بودن breakpoint بومی Elementor است. Atomic لپ‌تاپ را در schema و ترکیب کنترل بومی می‌پذیرد؛ سایر کامپوننت‌ها همان سه دستگاه قبلی را دارند. تبدیل کران صحیح `PHP_INT_MAX` به float در ثبت کنترل عددی Atomic حذف شد تا هشدار/سرریز ایجاد نکند. این اصلاح ادعای برابری کامل Typography نیست: واحدهای ابعاد دکمه هنوز px و کنترل line-height/letter-spacing/word-spacing در Classic بومی با مسیر مشترک PHP/Atomic یکسان نشده‌اند. Global/Dynamic و پذیرش واقعی ویرایشگر همچنان بازند.

**CONFIRMED:** دکمه در Components است و PHP، Classic، Atomic و هدر/فوترساز از همان renderer و metadata فیلدها استفاده می‌کنند. Elementor برای مصرف PHP لازم نیست. مرجع کنترل‌ها `themes/hub-elementor-addons/elementor/params/button.php` است؛ framework و دارایی‌های آن کپی نمی‌شوند.

اصلاح سازنده، ۲۰۲۶-۱۱-۰۱: مقدار -1000 در schema قدیمیِ فاصله/ارتفاع خط sentinel داخلیِ تنظیم‌نشده است و نباید در number/range دیده شود. ButtonFields با metadata مشترک presentation آن را به مقدار خالی نمایش می‌دهد؛ دادهٔ قدیمی بازنویسی/مهاجرت نشد و renderer همچنان sentinel را از CSS کنار می‌گذارد. واحد نمایشی و بازه از واحد مؤثر هر دستگاه گرفته می‌شود؛ unitless ارتفاع خط ۰٫۱–۱۰، px برابر ۱–۲۰۰ و فاصلهٔ منفی مجاز حفظ است. گام اعشاری و عدد صفر با خالی یکی نیستند؛ انتخاب واحدِ خالی تکرار نمی‌شود. اندازه و واحد/ارتفاع خط/فاصله‌های حروف و واژه‌ها در گروه تایپوگرافی سازنده‌اند. CSS تایپوگرافی خانه به main-content محدود شد تا اندازهٔ صریح دکمهٔ هدر/فوتر را نپوشاند. بررسی چهار Node، قرارداد PHP دکمه شامل همهٔ text_effectهای ثبت‌شده، نگاشت چهار دستگاه و انتخاب دارایی، و build پاس شدند. مرجع Hub برای Liquid/Marquee/تأخیر مرور شد؛ الگوریتم Liquid فارسی به‌عمد کلمه‌محور است تا اتصال حروف نشکند. این شواهد معادل تأیید بصری همهٔ hoverها در مرورگر یا برابری کامل با Hub نیستند. پیش‌نمایش ویرایش سازنده همچنان sandbox/inert است و محیط اجرای کامل JS دکمه نیست.

کنترل‌ها شامل لینک/پنجره/لایت‌باکس محلی/اسکرول، اندازه و عرض، تایپوگرافی محلی، padding و gap واکنش‌گرا، border و shadow، پس‌زمینهٔ رنگ/گرادیان/تصویر در حالت عادی و هاور، رنگ‌های هدر چسبان، چرخش، آیکون محلی و موقعیت/کادر/اندازه/موج/جداکننده، reveal/swap، افکت‌های switch/change/marquee/liquid و حرکت مغناطیسی‌اند. فیلدهای مشترک با سازنده تکرار نمی‌شوند. چهار دستگاه PHP/سازنده و کنترل‌های بومی ویرایشگر نگه داشته شده‌اند؛ margin بیرونی متعلق به چیدمان میزبان است. فونت از سیاست تایپوگرافی محلی می‌آید، نه Google Fonts.

CSS پایه کوچک است؛ skin، متن، آیکون، موج، sticky و dialog مستقل‌اند. دکمهٔ ساده JS ندارد؛ liquid و magnetic فقط روی دستگاه مناسب و بدون درخواست کاهش حرکت، GSAP محلی را می‌گیرند. انیمیشن liquid اتصال حروف فارسی را نمی‌شکند. پنجرهٔ محتوا عنصر موجود را با placeholder جابه‌جا و بازمی‌گرداند؛ قفل اسکرول با پنل جانبی مشترک است. درخواست اسکرول ابتدا به smoother موجود می‌رود. ویدئو/تصویر لایت‌باکس محلی‌اند؛ embed خارجی وارد نشده است.

**OPEN:** تطبیق بصری تمام افکت‌ها با Hub، ویرایش واقعی Classic/Atomic، ذخیره/Undo، موبایل و ترجمهٔ واقعی WPML هنوز تأیید میزبان می‌خواهند. وجود کنترل و build موفق اثبات برابری بصری نیست.

## Image accordion

مدل `layout=images` در خانوادهٔ Curtain/Expandable Panels اضافه شد؛ شناسه‌های PHP/Classic/Atomic قبلی تغییر نکردند. در المنتور: Expandable Panels → Panel layout → Image accordion؛ Trigger type برای حرکت ماوس روی Hover and focus قرار گیرد. نمونهٔ مستقل کتابخانه همین تنظیم را دارد. تصویر هر آیتم از Background image و در نبود آن از Image or logo می‌آید؛ محتوای Text/Template زیر تصویر همواره قابل دسترسی است. `expanded_ratio` بین ۱ و ۸، `image_radius` بین ۰ و ۱۲۰px و height/gap/duration موجود قابل تنظیم‌اند. PHP از `rdct_curtain($items, ['layout'=>'images','trigger'=>'hover'])` استفاده می‌کند.

Renderer مدل در `inc/components/curtain/images.php` و CSS مستقل در `src/css/components/curtain/images.css` است؛ CSS مدل قدیمی روی صفحهٔ تصویری لازم نیست. lifecycle انتخاب/کیبورد/hover با Curtain مشترک است؛ فقط مدل تصویری `curtain/images.js` را dynamic import می‌کند و GSAP محلی مشترک فقط در چیدمان افقی و نبود درخواست کاهش حرکت دریافت می‌شود. `quickTo`ها دوباره مصرف می‌شوند؛ context، breakpoint، preference و حذف کامپوننت cleanup دارند. کپشن‌ها inert/hidden نمی‌شوند؛ انتخاب با aria-pressed اعلام می‌شود. موبایل و بدون JS چیدمان عمودی و محتوا کامل باقی می‌ماند.

مرجع مستقیم [Elementra Digital Agency](https://digital-agency.elementra.themerex.net/) در مرورگر خوانده شد: `trx_elm_image_accordion`، تصویر پوشاننده با گوشهٔ ۲۶px، کپشن زیر تصویر و transition flex برابر ۰٫۴s. framework، تصاویر و اسکریپت مرجع منتقل نشدند. انیمیشن رادمان با GSAP پیاده شده و ادعای تطابق پیکسلی/زمان‌بندی کامل نیست. Build، ۲۷ بررسی JS و قراردادهای PHP/schema بومی بررسی شدند؛ پذیرش بصری نصب واقعی، تغییر/ذخیره در المنتور و WPML زنده هنوز باز است.

## پارالاکس تصویر در قاب ثابت — ادامهٔ سورس ۲۰۲۶-۱۰-۲۶

در Synchro معرفی‌شدهٔ مالک، حرکت تصویر/thumbnail داخل parent با overflow:hidden دیده شد؛ clip-path متحرک در بخش بررسی‌شده اثبات نشد. Image اکنون گروه بومی «Image parallax» و گزینه‌های `parallax`، `parallax_amount` (۱–۳۰٪، پیش‌فرض ۱۲)، `parallax_direction=up|down` و `parallax_min_width` (۰–۱۹۲۰، پیش‌فرض ۷۶۸) دارد. PHP، Classic و Atomic همان metadata/renderer را می‌گیرند؛ کنترل موازی ساخته نشد و فیلدهای فرعی وابسته به switch هستند.

فقط با انتخاب افکت، frame/media داخلی به markup اضافه می‌شوند؛ تصویر، alt، لینک و ID تکرار نمی‌شوند. `image/parallax.css` عرض/ارتفاع و ماسک همان قاب را نگه می‌دارد؛ `image/parallax.js` adapter همان موتور `shared/frontend/media-parallax.js::initializeMediaParallax(root,{frame,target,amount,minWidth,direction},load)` است؛ مصرف‌کننده loader همان vendor محلی را تزریق می‌کند و موتور مستقل از Child/نوع محتواست. موتور فقط media داخلی را با GSAP/ScrollTrigger مشترک جابه‌جا می‌کند. scale متناسب با دامنهٔ دوطرفه و حاشیهٔ کوچک، پوشش قاب را حفظ می‌کند؛ در حالت خاموش/کاهش حرکت transform برمی‌گردد. جهت عمودی به RTL وابسته نیست. float/tilt، overlay و reveal در لایه‌های خودشان می‌مانند و transform کل کانتینر بازنویسی نمی‌شود.

loader فقط هنگام انطباق عرض و no-preference، GSAP محلی را می‌گیرد؛ import دیررس پس از حذف/release mount نمی‌شود. context/revert و listener/observer/RAF هنگام cleanup آزاد می‌شوند؛ load تصویر و تغییر اندازه، refresh همان trigger را تجمیع می‌کنند. editor خروجی ثابت دارد؛ حرکت در preview عادی صفحه دیده می‌شود. image ساده CSS/JS این قابلیت را نمی‌گیرد. Reveal مستقل تصویر و Reveal با clip-path کانتینر همچنان قابلیت‌های جدا هستند؛ ماسک این حالت ثابت است.

build، ۲۵ بررسی کوتاه JS/graph، PHP حدود/markup/دارایی منتخب و schema/کنترل واقعی Atomic موفق‌اند. DOM/GSAP در آزمون رفتار شبیه‌سازی شده‌اند؛ نصب WordPress، ظاهر دقیق، تغییر اندازه/ذخیره/Undo واقعی و سازگاری smooth scrolling هنوز پذیرش نشده‌اند. نسخه/ZIP تغییر نکردند.

خانوادهٔ سکشن‌های چسبان، مدل‌های مرجع و ستون ثابت/اسکرولی در [مالک مستقل Sticky Sections](sticky-sections.md) نگهداری می‌شوند؛ از موشن کانتینر Theme و sticky هدر جدا هستند.

جداسازی CSS/JS خانواده‌های فعلی، شامل قابلیت‌های مستقل Image و Text و اتصال اختیاری Carousel، در سورس ۲۰۲۶-۱۰-۲۶ تکمیل و در [فهرست دارایی‌ها](assets.md) ثبت شد. این وضعیت به معنی برابری کامل امکانات مرجع یا پذیرش همهٔ کنترل‌های ادیتور نیست.

کنترل تصویر داخل Repeater و مصرف رزولوشن در Box/Icon Box/Flip Box/Curtain در ۲۰۲۶-۱۰-۲۵ اصلاح شد؛ [مالک تصویر و بررسی‌های محدود](assets.md) مرجع واحد است. این تغییر مجوز تصویر خارجی یا تغییر schema ذخیره‌شده نیست.

## اصلاح Box و کنترل‌های Atomic — ۲۰۲۶-۱۰-۲۵

روی میزبان، متن Box به علت حذف context.elementId هنگام عبور از کنترل سفارشی نمایش داده نمی‌شد؛ context اکنون بدون بازسازی به InlineEditingControl بومی می‌رسد. همین مسیر در rich text داخل Repeater اصلاح شد. [خطای union آیتم و عرض کانتینر](carousel.md) در همان بررسی بازتولید شدند.

پیش‌نمایش Box از مسیر موجود observeRenderedPreviews و endpoint احرازشدهٔ component-render، همان Box::render و requirements مشترک PHP را مصرف می‌کند؛ تصویر پیوست، HTML مجاز، رنگ‌ها و سبک همان renderer عمومی‌اند. DOM موازی قدیمی که متن را با textContent می‌ساخت حذف شد؛ فهرست boxStyles و fields.box اضافی نیز از config پیش‌نمایش حذف شدند. فقط CSS پایه، آیکون و سبک انتخاب‌شده برمی‌گردند؛ موتور motion و cleanup موجود روی root تازه اجرا می‌شوند. AJAX فقط متعلق به preview ادیتور است؛ frontend مستقل PHP می‌ماند. پذیرش ظاهر نهایی و ذخیره/Undo پس از نصب اصلاح هنوز باقی است.

## Particles — ادامهٔ سورس ۲۰۲۶-۱۰-۲۵

کامپوننت ذرات، helper PHP، Classic/Atomic و کارت کتابخانه با موتور/renderer مشترک اضافه شدند. چهار بخش و ۶۰ فیلد مرجع هاب پوشش دارند؛ ترکیب شش Switch بومی به‌جای Select چندانتخابیِ موجودنبودن Atomic و Number بومی به‌جای Slider قدیمی، استثنای ظاهرند. [مالک Particles](particles.md) شامل فهرست، منبع MIT، validation، بارگذاری، تفاوت‌ها و پذیرش واقعیِ باز است؛ ثبت فیلد به معنی تأیید کامل UI نیست.

## رندر PHP پیش از head — Components 0.1.32

برای صفحهٔ اختصاصی، محتوای واقعی را پیش از هدر آماده و همان خروجی را پس از هدر چاپ کنید. helperها و ویجت‌های Classic/Atomic انتخاب منابع را از `requirements()`/`style_depends()` می‌گیرند؛ [قرارداد دارایی‌ها و محدودیت‌ها](assets.md) مالک جزئیات است. این روش جایگزین اختیاری اعلام دستی `rdct_use()` است؛ اجرای دوبارهٔ partial یا ذخیرهٔ HTML آماده در کش بین زبان‌ها لازم نیست.

```php
$content = rdct_prepare( static function (): void {
    rdct_box( array( 'title' => get_the_title(), 'template' => 's01' ) );
} );
get_header();
echo $content;
get_footer();
```

این API با نصب Components در دسترس است؛ نمونهٔ Child نبود افزونه را نیز پوشش می‌دهد. callback فقط محتوای داخل پوسته را بسازد؛ `get_header()`/`get_footer()` بیرون آن بمانند. helper مستقل پس از head همچنان fallback چاپ یک‌بارهٔ CSS دارد، اما آن مسیر تضمین جلوگیری از تغییر ظاهر اولیه نیست.

## اصلاحات گزارش مالک — ۲۰۲۶-۱۰-۲۴

نیاز JS مسیر PHP/Classic/Atomic طبق predicate مشترک در [قرارداد دارایی‌ها](assets.md) بر اساس رفتار تعیین می‌شود؛ وابستگی عمومیِ ویجت استاتیک در نمونه‌های Box/Image/Text و Atomic Table/Icon Box حذف شد. حالت type-instance ادیتور برای قابلیت‌های قابل تبدیل، adapter را نگه می‌دارد. اثبات Network/کش/چرخهٔ واقعی ویرایشگر هنوز انجام نشده است.

تصویر Box دیگر loading=lazy اجباری نمی‌گیرد؛ `wp_get_attachment_image` مطابق [API رسمی](https://developer.wordpress.org/reference/functions/wp_get_attachment_image/) تصمیم loading/fetchpriority را می‌گیرد و ابعاد/srcset بومی حفظ می‌شوند. مقدار واقعی Hero/LCP نیازمند زمینه و اندازه‌گیری صفحهٔ مشتری است.

**چرخهٔ اجرا — Components 0.1.31:** bootstrap و handlerهای Classic/Atomic در `src/js/runtime.js` یک lifecycle مشترک دارند. هر root/feature یک mount معوق و ownerهای مستقل دارد؛ release یک handler، نمونهٔ مورد استفادهٔ owner دیگر را نمی‌بندد. آخرین release یا حذف root از DOM cleanup می‌کند؛ یک observer فقط حذف rootهای فعال را پیگیری می‌کند و پس از خالی‌شدن registry قطع می‌شود. root حذف‌شده یا import تمام‌شده پس از teardown راه‌اندازی نمی‌شود. خروج به bfcache listener خروج نهایی را مصرف نمی‌کند. Image مانند موتورهای دیگر init تکراری را guard می‌کند. motion/smoother قالب نیز cleanup را به lifecycle خود قالب می‌دهند.

قرارداد کوتاه مالکیت هم‌زمان، حذف/جایگزینی root، cancel حین import، bfcache و استقلال cleanup پاس شد؛ چرخهٔ واقعی editor/AJAX و listenerهای مرورگر هنوز پذیرش می‌خواهند. observer جدید مصرف PHP را به Elementor وابسته نکرده و درج خودکار هر HTML دلخواه توسط AJAX را وعده نمی‌دهد. برای صفحهٔ PHP قابل پیش‌بینی، `rdct_use()` پیش از `get_header()` باقی است؛ CSS دیرهنگام fallback است.

کاروسل عمومی، ارتفاع مستقل آیتم، منابع Template/محتوا، Swiper محلی و مرز طراحی کارت در [قرارداد کاروسل](carousel.md) مالک جدا دارند؛ از همان باکس/کارت موجود استفاده شود، نه renderer تکراری داخل اسلایدر.

## اولویت کنترل بومی در پنل

در ادامهٔ سورس ۲۰۲۶-۱۰-۲۵، نوع ترجمهٔ `text` در Box کلاسیک و `text>value` در Atomic به VISUAL اصلاح شد تا با محتوای HTML و کنترل بومی آن‌ها هماهنگ باشد. نگاشت کاروسل و حدود پذیرش WPML در [مالک کاروسل](carousel.md#ترجمهٔ-اسلایدها) ثبت شده است؛ این تغییر کنترل یا مدل ذخیرهٔ جدیدی ایجاد نمی‌کند.

قرارداد الزام‌آور رابط در [سناریوی کامپوننت‌ها](../scenarios/components.md#elementor-native-controls) است. پیش از ساخت یا تغییر کنترل، API و سورس نسخهٔ هدفِ Classic یا Atomic برای معادل بومی و امکان ترکیب کنترل‌های آماده بررسی شود. ابتدا کنترل بومی ثبت و به schema/renderer مشترک وصل شود؛ CSS یا React موازی برای بازسازی input، dropdown یا کنترل گروهیِ موجود اضافه نشود. اشتراک کد در تعریف و نگاشت تنظیمات انجام شود و استایل بومی پنل حفظ بماند.

برای کنترل اختصاصی لازم، نبود معادل مناسب و دلیل انتخاب در بخش همان کامپوننت ثبت شود. رابط از امکانات و الگوهای خود ویرایشگر برای spacing، اندازه، typography، رنگ، حالت‌ها، دسترس‌پذیری، جهت و پوسته استفاده کند؛ مقادیر ظاهری دلخواه و مستقل از پنل مبنا نباشند. کد مشترک کنترل اختصاصی بین مصرف‌کننده‌ها تکرار نشود و دارایی آن فقط در editor بارگذاری شود.

**وضعیت فعلی — Components 0.1.34:** کنترل‌های استاندارد Classic و Atomic در مسیرهای زیر موجودند، اما استفاده از چند جزء بومی به معنی استانداردشدن کل پنل نیست. بازبینی تازه، uploader دستی در تکرارگرهای Atomic و textarea برای محتوای HTML را پیدا کرد؛ آن دو مسیر اصلاح شدند. پذیرش تعاملی کل پنل، Global/Dynamic و همهٔ استثناهای جدول هنوز باز است؛ build/PHP به‌تنهایی تأیید ظاهر نیست.

### موجودی کنترل‌ها و تصمیم اصلاح

**بازبینی محدود سورس با تأکید مالک — ۲۰۲۶-۱۰-۲۷:** مسیرهای input/number/select/switch/media در Classic از Controls Manager و در Atomic از `editorControls` هستند؛ رنگ Atomic از ColorControl بومی با adapter مقدار، تکرارگر از Repeater بومی، آیکون Classic از Icons بومی و Atomic از دکمهٔ UI بومی و iconManager موجود استفاده می‌کنند. responsive ترکیب کنترل‌های بومی با schema قبلی است، نه مدرک برابری با responsive کامل V4. خانوادهٔ فونت خروجی تابع قرارداد محلی Theme است؛ Group Typography کلاسیک و گزینه‌های مشترک Text/Button به معنی همسانی کامل همهٔ زیرکنترل‌ها نیستند. فایل `elementor-typography.css` فقط قرارداد مصوب فونت فارسی رابط مدیریت را اعمال می‌کند؛ این استثنا مجوز بازطراحی رنگ، فاصله یا اندازهٔ کنترل‌های عمومی نیست.

**اصلاح موارد ناهماهنگ در سورس — ۲۰۲۶-۱۰-۲۸:** `src/js/admin/table-grid.js` در Classic اکنون از کلاس بومی `e-btn e-btn-txt-border` برای دکمه‌ها استفاده می‌کند؛ input/textarea همان استایل بومی Elementor را می‌گیرند. CSS اختصاصی padding/border/background/radius/focus و disabled کنترل‌های پایه از `table.css` حذف شد؛ فقط چیدمان، ابعاد لازم سلول و اسکرول جدول باقی است. Atomic همچنان TextField/Button بومی UI را مصرف می‌کند و override اندازه/padding دلخواه دکمه‌اش حذف شد. شبکهٔ جدولِ فاقد معادل یکپارچه، استثنای چیدمان باقی می‌ماند و داده/ذخیره تغییر نکرده‌اند. shell و CSS کتابخانهٔ داخل editor نیز از پنل مستقل رادمان جدا و به Dialog Manager بومی متصل شدند؛ [مالک کتابخانه و پذیرش باز](component-library.md). Build و قرارداد کوتاه به معنی تأیید popup، روشن/تیره، فونت، کیبورد، disabled یا ذخیره/Undo در ادیتور واقعی نیستند. پنل‌های مستقل رادمان خارج Elementor مالک UI خودشان می‌مانند.

**کاروسل، افزودن گروهی تصاویر — ۲۰۲۶-۱۰-۲۴:** Classic از GALLERY موجود استفاده می‌کند. در Atomic ارسالی، ImageControl فقط تک‌انتخابی و کنترل Gallery غایب است؛ عملیات batch با دکمهٔ UI بومی و رسانهٔ استاندارد WordPress، همان Repeater/Prop تصویر موجود را پر می‌کند. ورودی، uploader، picker هر ردیف یا CSS پنل اختصاصی جدید ساخته نشده است. رفتار و محدودیت در [مالک کاروسل](carousel.md#انتخاب-گروهی-تصاویر--ادامهٔ-سورس-۲۰۲۶-۱۰-۲۴) نگهداری شود؛ وجود این مسیر اثبات پذیرش بصری یا Undo واقعی نیست.

مسیرهای این جدول نسبت به `radmanct-components/` هستند، مگر Theme صریح نام برده شود. این موجودی مسیرهای کنترل‌های فعلی است؛ هر ردیف به معنی بررسی بصری/سازگاری کامل آن نیست. قبل از افزودن کنترل بعدی، نگاشت نیاز → کنترل بومی → نوع داده → رفتار renderer و مرز نسخه/پشتیبانی API همین‌جا یا در مالک تخصصی آن به‌روز شود.

| مسیر/خانواده | تصمیم و وضعیت |
|---|---|
| `inc/integrations/elementor/widgets/*.php`؛ Box/Image/Text/Icon/Icon Box/Flip Box/Curtain/Carousel/Table/Sticky Sections | Classic از کنترل‌های هسته استفاده می‌کند؛ محتوای Box اکنون WYSIWYG است و مقدار پیش‌فرض switcher آن حفظ می‌شود. متن سادهٔ عنوان/متن انیمیشنی عمداً WYSIWYG نیست. Style/Group/Conditions هر ویجت جدا ارزیابی شود؛ برابری همهٔ زیرکنترل‌ها اعلام نشده است. |
| `inc/integrations/elementor/atomic-components/{widget,image,icon-box,box,flipbox}.php` | Text/Select/Switch/Number/Image بومی؛ محتوای HTML Box/Icon Box/دو طرف Flip Box از InlineEditingControl بومی می‌آید. `native-fields.php` و `src/js/admin/atomic-native-fields.js` فقط bridge دادهٔ قبلی string به escaped-html هستند، نه ویرایشگر یا CSS تازه. union مقدار قبلی را نیز می‌پذیرد و تبدیل view به‌تنهایی ذخیره را بازنویسی نمی‌کند. |
| `atomic-components/{curtain,carousel,text,sticky-sections}.php` و `src/js/admin/atomic-repeater-control.js` | ظرف Repeater/PropProvider و فیلدها بومی‌اند. media عددی قدیمی Curtain/Carousel با union تصویر/عدد حفظ شد؛ دکمهٔ uploader دستی حذف و ImageControl بومی جایگزین شد. محتوای HTML همان InlineEditingControl بومی و boolean همان SwitchControl است. از 0.1.35 انتخاب Template در Curtain/Carousel و Flip Box از SelectControl بومی با bridge شناسهٔ عددی استفاده می‌کند؛ Select/MenuItem دستی تکرارگر حذف شد. مسیر Sticky Sections هنوز به معنی قابلیت متصل/پذیرش‌شده نیست و ارجاع‌های helper/schema آن نیاز به تعیین وضعیت دارند. |
| `atomic-components/presentation.php` و `src/js/admin/atomic-responsive-control.js` | ترکیب کنترل‌های بومی برای دادهٔ responsive قبلی و ارث‌بری سه دستگاه حفظ شده؛ امکان جایگزین مستقیم با API عمومی responsive نسخهٔ هدف باید پیش از توسعهٔ بعدی دوباره ارزیابی شود. واحد، breakpoint و دادهٔ قبلی نباید با تعویض UI از بین بروند؛ پذیرش ظاهر هنوز باز است. |
| `atomic-components/{widget,image}.php` و `src/js/admin/atomic-color-control.js` | ColorControl بومی با bridge رنگ string/alpha؛ palette و parser اختصاصیِ نمایشی نداریم. ادغام واقعی Global Colors و رفتار Dynamic بر اساس نوع داده باید جدا پذیرفته شود. |
| `icon-picker-control.php` و `src/js/admin/icon-controls.js` | adapter رشته‌ای Classic از Control_Icons و view بومی، و Atomic از UI بومی و Icons Manager استفاده می‌کند؛ Modal دوم حذف شد. schema رشته‌ای برای سازگاری محفوظ و Dynamic object غیرفعال است؛ فقط آیکون محلی ثبت‌شده پذیرفته می‌شود. پذیرش واقعی ذخیره/Undo و مرز کتابخانه‌ها در [مالک آیکون](icons.md) است. |
| `table-control.php` و `src/js/admin/{table-controls,table-grid}.js`، `src/css/admin/table.css` | ماتریس جدول استثنای واقعی است، نه dropdown/input عمومی؛ Atomic ورودی و دکمهٔ همان UI، Classic کلاس/متغیر بومی و CSS ساختار scopeدار دارد. حذف الزامی نیست؛ روشن/تیره، focus/error و ذخیرهٔ واقعی بازند. |
| Theme: `inc/integrations/elementor/motion-control.php` و `navigation/atomic.php` | کنترل‌های موشن و انتخاب/رنگ ناوبری به‌ترتیب در [موشن کانتینر](container-motion.md) و [ناوبری](navigation.md) بازبینی شوند؛ جدول Components تأیید کامل آن‌ها نیست. |

### معیار بستن اصلاح رابط

**ادامهٔ سورس بدون آماده‌سازی بسته، ۲۰۲۶-۱۰-۲۴:** `widgets/repeater-controls.php::field_control()` نگاشت مشترک Classic است؛ Curtain و Carousel آن را مصرف می‌کنند. media پیش از محدودیت عددی، Template با SELECT2، enum با SELECT، boolean با SWITCHER و default درست، رنگ با COLOR، متن HTML با WYSIWYG و متن سادهٔ چندخطی با TEXTAREA ثبت می‌شوند. شرط/توضیح و min/max/step مربوط حفظ‌اند؛ tabs حالت‌های Curtain و عرض responsive کاروسل تغییری ندارند. این mapper control جدید یا CSS تقلیدی نمی‌سازد و دامنهٔ آن فعلاً همین مصرف‌کننده‌هاست؛ همهٔ ویجت‌ها مهاجرت‌شده اعلام نشوند.

فهرست مشترک Template در `components/template-library.php` با `suppress_filters=false` از فیلتر زبان WPML عبور می‌کند، فقط منتشرشده/بدون رمز و حداکثر ۱۰۰ مورد می‌خواند و cache همان درخواست را با زبان جاری تفکیک می‌کند. cache فارسی پس از preview انگلیسی بازنویسی نمی‌شود؛ نبود WPML همچنان یک cache عادی دارد. الگوی انتخاب Template هاب در flipbox و [رفتار پیش‌فرض get_posts](https://developer.wordpress.org/reference/functions/get_posts/) بررسی شدند. فهرست مجاز جدید موجب مهاجرت شناسه‌های ذخیره‌شده یا تغییر خودکار fallback رندر Template نمی‌شود؛ fallback قبلی همچنان نیاز به پذیرش چندزبانهٔ واقعی دارد.

**Components 0.1.35:** SelectControl اتمیک مقدار string می‌خواند؛ اتصال مستقیم آن به prop عددی Template هم‌نوع نبود. `template_field_prop()` مقدار قبلی number و مقدار string نمای کنترل را می‌پذیرد؛ `TemplateSelectControl`/`createNativeTemplateSelect()` تنها bridge هستند و UI همان SelectControl بومی است. نمایش مقدار قبلی، صفر برای None، انتخاب از فهرست موجود، رد مقدار نامعتبر، disabled و metadata ذخیره حفظ می‌شوند؛ ذخیرهٔ انتخاب جدید همچنان number است. فهرست Template همان فهرست مجاز موجود است و endpoint یا درخواست تازه ندارد. Classic و renderer PHP به Elementor وابستهٔ تازه نشدند. انتخاب Template غایب در فهرست خودکار بازنویسی نمی‌شود.

شرط تکرارگر از equality ساده به رفتار scalar، OR داخل فهرست، AND میان فیلدها و پسوند منفی `!` اصلاح شد؛ مقدار پیش‌فرض schema و boolean false محفوظ‌اند. این بخش با [شرط‌های رسمی کنترل‌ها](https://developers.elementor.com/docs/editor-controls/conditional-display/) از Context7 تطبیق داده شد. bridge رسانه/متن نیز مقدار typed ناشناخته را هنگام ویرایش فیلد مجاور به null/رشتهٔ خالی تبدیل نمی‌کند؛ این حفظ داده به معنی تأیید کارکرد همهٔ Dynamic Tags نیست. بررسی کوتاه مدل/prop پاس شد؛ بازشدن پنل، انتخاب واقعی، Undo/بازگشایی و Dynamic/Globals همچنان پذیرش زنده می‌خواهند.

فهرست بومی‌های موجود از سورس Elementor ارسالی 4.3.5 و مستندات رسمی بررسی شد؛ InlineEditingControl همان کنترل Paragraph اتمیک و ImageControl همان کنترل تصویر exportشدهٔ editor-controls است. bridgeها package/UI دیگری وارد نمی‌کنند. enum/schema، مقدار قدیمی، disabled، metadata ذخیره/Undo و خروجی عمومی برای تغییر کنترل بررسی شوند. بررسی دستی روشن/تیره، RTL، عرض پنل، focus/error، Media Frame، Editor/Preview و ذخیره/بازگشایی واقعی هنوز لازم است و بدون اجرا تأیید نشود. آزمون کوتاه مرتبط جای این پذیرش نیست؛ اجرای طولانی تابع محدودیت بررسی پروژه است.

### گروه پنل و کنترل‌های ترکیبی — Components 0.1.18

`inc/integrations/elementor/panel.php` گروه مشترک `radmanct-components` را ثبت و در config نهایی `initial_document.panel.elements_categories` مقدم می‌کند. فیلتر `elementor/editor/localize_settings` با یک آرگومان مطابق سورس Elementor 4.3.5 استفاده می‌شود؛ add_category در این نسخه آرگومان position ندارد. دادهٔ private مدیر عناصر دست‌کاری نمی‌شود. `elementor-panel.js` در bridge موجود editor، config سند و collection بومی دسته‌ها را در document/preview ready همسو می‌کند؛ فقط جابه‌جایی گروه، نه جابه‌جایی DOM یا widget ذخیره‌شده. ترتیب و تنظیم سایر گروه‌ها حفظ، عملیات تکراری بدون reset اضافه و گروه ناموجود ساخته نمی‌شود. گروه خالی و رفتار Element Manager همچنان بومی‌اند. Atomic فقط عضو همین گروه است تا کارت تکراری در v4-elements نسازد؛ منوی Theme در حضور Components همین گروه و در نصب مستقل گروه بومی قبلی را مصرف می‌کند.

Table ماتریس عمومی دارد و کنترل ترکیبی آن حفظ شده؛ Atomic ورودی/دکمهٔ UI خود Elementor را مصرف می‌کند و CSS سفارشی Table فقط ساختار/scroll و Classic را می‌گیرد. آیکون رشته‌ای اکنون پنجرهٔ مشترک دارد و trigger اتمیک از Button بومی است؛ وجود ICONS بومی برای مجموعهٔ محلی تأیید شده، اما مهاجرت مقدار رشته‌ای خود ویجت‌ها هنوز بسته نشده است. ادعای قبلیِ نبود کنترل آماده برای آیکون، دلیل کافی برای حفظ همیشگی UI اختصاصی نیست. CSS پنل تنظیمات/سازنده داخل Elementor وارد نمی‌شود؛ شناسه‌های ویجت و دادهٔ قدیمی بدون بررسی وابستگی تغییر نکنند. جزئیات آیکون در [مالک خودش](icons.md) است.

بررسی کوتاه: config/collection بدون تکرار و حفظ metadata، اتصال disabled/value کنترل‌های بومی، حفظ سلول مجاور، schema و کنترل واقعی PHP ارسالی پاس شدند. این‌ها model/fixture هستند؛ نمایش واقعی روشن/تیره، RTL، جابه‌جایی سند، ذخیره/Undo و ترتیب پنل روی نصب مشتری هنوز تأیید نشده‌اند.

## Repeater بومی Curtain — Components 0.1.12 / Theme 0.1.21

فهرست آیتم‌ها از `editorControls.Repeater` موجود در Elementor 4.3.5 استفاده می‌کند؛ افزودن/حذف/تکثیر، جابه‌جایی و popover بومی‌اند. متن، متن چندخطی، عدد، انتخاب و رنگ با کنترل‌های بومی و مسیر `items → index → field` وصل می‌شوند. انتخاب Template شناسهٔ عددی می‌ماند؛ تصویر از Media Library وردپرس انتخاب می‌شود. سقف ۱۲، حالت disabled، داده‌های typed و metadata عملیات حفظ شدند؛ `atomic-curtain-controls.css` حذف شد. Reference همان Curtain هاب ثبت‌شده در بخش مرجع و API کنترل‌های نسخهٔ محلی است.

Gap و Padding محتوا از موتور responsive مشترک، برای PHP/Classic/Atomic تنظیم دستگاهی دارند؛ CSS فقط برای override همان نمونه تولید می‌شود. بررسی‌های کوتاه اتصال Repeater، حدود/metadata، حفظ آیتم مجاور، نوع عددی Template، schema بومی، رندر و مقدار صفر فاصله پاس شدند. عملکرد واقعی drag/popover، ذخیره/بازگشایی و ظاهر موبایل/RTL در مرورگر هنوز پذیرش نشده است.

## استفاده از اجزای بومی Atomic — Components 0.1.11 / Theme 0.1.20

در Elementor 4.3.5، `editorControls.ColorControl` با `propTypeUtil` قابل اتصال به String Prop موجود است؛ adapter رنگ اکنون همین کنترل را رندر می‌کند. مقدار RGB/RGBA انتخابگر به hex معتبر تبدیل و alpha حفظ می‌شود؛ قالب رنگ ذخیره‌شده و شناسه‌ها مهاجرت نمی‌کنند. برای قالب‌های رنگ دیگر، تبدیل استاندارد مرورگر با canvas فقط در editor انجام می‌شود. input رنگ/HEX اختصاصی حذف شد؛ اتصال مستقیم Global Variables هنوز پذیرش نشده است.

responsive از `PropProvider`/`PropKeyProvider` و `NumberControl`/`ToggleControl` بومی، همراه Slider و ToggleButtonGroup/Button از UI خود Elementor استفاده می‌کند. ترکیب دستگاه/ارث‌بری همچنان روی schema مشترک قبلی است؛ CSS ظاهری `atomic-colors.css` حذف شد و پوسته/RTL فیلدها را UI بومی تعیین می‌کند. این تغییر API جدید یا کپی از framework مرجع وارد محصول نکرد.

مستندات Atomic Prop Values و سورس خوانای `assets/js/packages/editor-controls/editor-controls.js` در نسخهٔ محلی بررسی شدند. چهار بررسی کوتاه Node برای تبدیل رنگ، اتصال به کنترل بومی، حدود عدد و حفظ override دستگاه دیگر پاس شدند؛ این‌ها شواهد مدل/اتصال هستند، نه اجرای پنل در مرورگر. پذیرش تعاملی ادیتور و موارد باز بالا باقی‌اند.

## کنترل‌های تصویری و responsive — Components 0.1.10 / Theme 0.1.19

مرجع محدود مجدداً بررسی‌شده: `themes/hub-elementor-addons/elementor/widgets/icon-box.php`، بخش‌های Icon، Title، Description، Style و responsive SLIDER/CHOOSE؛ API رسمی Elementor responsive-control و سورس Number/Section/Object/Union PropType در نسخهٔ 4.3.5 مبنا هستند. Icon Box کلاسیک اکنون گروه‌های آیکون، عنوان، توضیح، استایل، تایپوگرافی و SVG motion دارد؛ رنگ‌های Normal/Hover تب جدا، تصویر MEDIA، توضیح WYSIWYG، لینک URL و اندازه/تراز کنترل بومی مناسب دارند. Image جلوه‌ها را در Style و رسانه/لینک/لایت‌باکس را در Content قرار می‌دهد. Box ظاهر را از محتوا و Table تنظیمات نمایش را از داده جدا می‌کند.

انتخابگر رشته‌ای آیکون در Classic/Atomic رادمان از پنجرهٔ تصویری مشترک با سازنده استفاده می‌کند؛ SVG محلی، نام و جستجو دارد و شناسه‌های قبلی حفظ می‌شوند. فایل مدیریت `icon-controls` عمومی نیست. تب رادمان در پنجرهٔ بومی Icons Manager و مرز transformer مستقل Atomic نیز طبق [مالک آیکون](icons.md) اضافه شده‌اند؛ پذیرش واقعی ظاهر/ذخیره/Undo باز است.

`inc/components/presentation.php` مالک انتخاب محدود ویژگی، ارث‌بری و CSS scoped است. Icon اندازه/ضخامت/تراز، Icon Box اندازه/فاصله/تراز/چیدمان/اندازهٔ عنوان و توضیح، Box اندازه‌های متن، Flip Box ارتفاع و Table padding/حداقل عرض/تراز را پوشش می‌دهند. Classic از SLIDER/CHOOSE و دکمهٔ دستگاه بومی استفاده می‌کند؛ Atomic از Object PropType تایپ‌دار `rdct-responsive` و کنترل مشترک دسکتاپ/تبلت/موبایل، اسلایدر یا انتخاب و Reset استفاده می‌کند. Atomic گروه‌های محتوا و ظاهر خودش را دارد؛ Group Controlهای PHP کلاسیک مستقیماً در API Atomic قابل استفاده نیستند. CSS/JS این کنترل‌ها به فرانت عمومی منتقل نمی‌شود.

برای هر دستگاه فقط override تنظیم‌شده تولید می‌شود؛ مقدار صفر معتبر است و خالی یعنی ارث‌بری. breakpointهای tablet/mobile فعال Elementor خوانده می‌شوند؛ مصرف PHP بدون Elementor به‌ترتیب 1024/767 دارد. مقادیر عددی محدود، گزینه‌ها allowlist و CSS scope با hash دادهٔ معتبر تعیین می‌شود. CSS پویا minified و فقط برای همان نمونه دارای override در خروجی است؛ ساختار/variantها همچنان از فایل‌های minified ماژولار می‌آیند. JavaScript عمومی برای responsive اضافه نشده است. نمونهٔ PHP: `'icon_size_responsive' => array( 'desktop' => 48, 'tablet' => 32, 'mobile' => 20 )` و `'layout_responsive' => array( 'mobile' => 'top' )` داخل options همان `rdct_icon_box`؛ نام/متای قبلی بازنویسی نمی‌شود.

بررسی هدفمند: lint PHP، قرارداد inheritance/صفر/حدود/ورودی مخرب/همسانی Classic و Atomic، schema/control/Twig واقعی و Table/KSES پاس شدند. Build minified ES2020 پاس شد. WordPress 7.1.3/PHP 8.4 و Elementor 4.3.5 در محیط موقت، head پیش‌نمایش و رندر Classic/Atomic Icon Box با override موبایل 19px را پاس کرد؛ سایر موارد preview قبلی نیز پاس شدند. ظاهر پنل در مرورگر، drag اسلایدر، Undo/ذخیره/بازگشایی، دستگاه واقعی و تطابق تمام فیلدهای Hub هنوز پذیرش نشده‌اند. افزودن responsive به چند ویژگی به معنی تکمیل همهٔ کنترل‌های همهٔ کامپوننت‌ها نیست؛ فهرست تفاوت‌های مرجع در همین سند باقی است.

## رفع خطای پیش‌نمایش — Components 0.1.9 / Theme 0.1.18

در ۲۰۲۶-۱۰-۲۲ خطای 500 پیش‌نمایش گزارش شد؛ پاسخ هاست پس از شروع head و builder-links به صفحهٔ critical error می‌رسید. در WordPress 7.1.3/PHP 8.4 با Elementor 4.3.5 علت محلی بازتولید شد: `Preview::enqueue_styles` از `Widgets_Manager::enqueue_widgets_styles` وابستگی نوع‌های ثبت‌شده را می‌خواند؛ `get_style_depends` در Box تنظیمات نمونهٔ سند را از prototype می‌خواست و `Controls_Stack::sanitize_settings(null)` خطای TypeError می‌داد.

Box، Flip Box، Icon Box، Image و Table در هر دو adapter Classic/Atomic اکنون روی `is_type_instance()` آرایهٔ خالی برمی‌گردانند؛ انتخاب CSS پایه و variant فقط روی نمونهٔ واقعی انجام می‌شود. CSS تمام استایل‌ها برای عبور از خطا عمومی نشده است. این رفتار با سورس Elementor در `element-base.php`، `managers/widgets.php` و `preview.php` و [قرارداد رسمی وابستگی ویجت](https://developers.elementor.com/docs/widgets/widget-dependencies/) بررسی شد؛ فایل مرجع تغییر نکرد.

در بررسی بومی کتابخانه، schemaهای complex در Atomic توسط Union پوشانده می‌شوند. مدل Table و Curtain اکنون پیش از generate/validate شاخهٔ typed مربوط به خود را صریح انتخاب می‌کنند؛ تشخیص خودکار Union برای array خام کافی نبود. داده و شناسهٔ ذخیره‌شده مهاجرت نکرده‌اند.

`tools/qa-elementor.ps1 -PreviewOnly` در محیط WordPress/DB موقت با شبکهٔ داخلی پاس شد: فعال‌سازی پس از Theme، head واقعی preview با contact icon، رندر Table و Icon کلاسیک و Table و Image بومی Atomic. deprecationهای خود Elementor پنهان نشدند. این بررسی PHP رندر و مدل است؛ UI مرورگر، ویرایش/ذخیرهٔ تعاملی و نصب روی هاست مالک هنوز تأیید نشده‌اند. بسته‌های نصب در artifacts هستند؛ ZIP ریشهٔ کاربر بازنویسی نشد.

## رفع خطای فعال‌سازی — Components 0.1.8 / Theme 0.1.17

در ۲۰۲۶-۱۰-۲۲ مالک خطای جدی فعال‌سازی Components 0.1.7 را گزارش کرد. ترتیب Theme → Plugin محلی دقیقاً `Cannot redeclare function rdct_use()` داد: درخواست مدیریت ابتدا fallbackهای Theme را ساخته بود و `activate_plugin` سپس helperهای همنام افزونه را include می‌کرد. بررسی‌های قبلیِ افزونهٔ از قبل فعال، این ترتیب activation را پوشش نمی‌دادند.

تعریف گروه helperهای Components اکنون با همان guard مالکیت `rdct_use` انجام می‌شود؛ در درخواست activation، fallbackهای ازقبلتعریف‌شده دوباره تعریف نمی‌شوند. درخواست تازه با افزونهٔ فعال طبق ترتیب استاندارد WordPress ابتدا helper اصلی افزونه را بارگذاری می‌کند و Theme fallback را کنار می‌گذارد. fallback `rdct_table` نیز در Theme اضافه شد تا نبود افزونه در PHP سفارشی fatal نسازد. شناسه، متا یا دادهٔ محتوایی تغییر نکرد.

`tests/components-activation-contract.php` هر دو ترتیب theme-first/plugin-first و قرارداد standalone پاس شدند. بررسی هدفمند WordPress 7.1.3/PHP 8.4 و Elementor 4.3.5 در محیط موقت با شبکهٔ داخلی اجرا شد: فعال‌سازی Elementor، فعال‌سازی Components بعد از setup قالب، درخواست تازه، فعال‌ماندن افزونه، تعلق helper به فایل افزونه، ثبت کنترل ماتریس و رندر PHP Table پاس شدند. هشدارهای deprecation خود Elementor در Atomic_Global_Styles و includes/api.php دیده شدند و پنهان یا دست‌کاری نشدند؛ مرورگر/هاست مالک همچنان جداست. اجرای تکرارپذیر این مسیر با `tools/qa-elementor.ps1 -ActivationOnly` ممکن است و جزو بررسی روزمره نیست.

## Table عمومی — Components 0.1.7 / Theme 0.1.16

مرجع محدود: `themes/hub-elementor-addons/elementor/widgets/schedule-table.php` و CSS `assets/css/widgets/liquid-events-table.min.css` برای ظاهر سرستون/سلول و overflow؛ ویجت عمومی `elementor/widgets/table.php` نیز برای تفکیک ساختار جدول بررسی شد. دادهٔ روز/زمان، jQuery و اسکریپت زمان‌بندی مرجع منتقل نشدند. `inc/components/table.php` مالک schema/validation و HTML مشترک است؛ helper `rdct_table($data, $options)` مستقل از نوع محتوا و Elementor کار می‌کند.

ساختار داده `columns: string[]` و `rows: [{cells: string[]}]` است. ردیف‌های کوتاه سلول خالی می‌گیرند؛ سطر/ستون خالی حذف خودکار نمی‌شود. سقف اجرایی ۱۲ ستون، ۱۰۰ ردیف، ۳۰۰۰ نویسهٔ هر سلول و بودجهٔ ۱۲۸KiB محتوای جدول است؛ UI هنگام عبور از بودجه پیام می‌دهد و تغییر حجیم را اعمال نمی‌کند. UTF-8 در محدودکردن سلول قطع نمی‌شود. متن چندخطی، strong/em/small/code و پیوند امن با WordPress KSES پشتیبانی می‌شوند؛ script، attribute رویداد، CSS آزاد و محتوای اجرایی وارد سلول نمی‌شوند. این نسخه محل قراردادن widget/container دلخواه در هر سلول نیست؛ rowspan/colspan، مرتب‌سازی، محاسبه و import فایل اضافه نشده‌اند.

خروجی table/thead/tbody/th/td بومی با caption، scope ستون و عنوان اختیاری ردیف است. سرستون را می‌توان خاموش کرد. wrapper محدود و قابل پیمایش با کیبورد، اسکرول افقی را داخل همان جدول نگه می‌دارد؛ overflow کل سایت پنهان نمی‌شود. جهت از صفحه و تراز start/center/end از تنظیم گرفته می‌شود. رنگ سرستون/خط، padding و حداقل عرض محدود هستند. سه ظاهر `grid`، `striped` و `minimal` در `src/css/components/table/` فایل مستقل دارند؛ فقط base و variant منتخب می‌آیند. نمایش عمومی JavaScript/React/GSAP نمی‌خواهد. Theme جدول کامپوننت را دوباره داخل wrapper جدول محتوای عادی نمی‌پیچد.

Classic `rdct-table` و Atomic `rdct-atomic-table` یک ویرایشگر ماتریسی `table-grid.js` را از React خود WordPress مصرف می‌کنند: افزودن/حذف/جابه‌جایی ستون، افزودن/تکثیر/حذف/بالا/پایین‌بردن ردیف و ویرایش مستقیم متن هر سلول. ستون همراه تمام سلول‌هایش جابه‌جا می‌شود. در Classic از BaseData و setValue بومی و settings همان container استفاده می‌شود؛ در Atomic از useBoundProp و Object/Array PropTypeهای بومی bounded استفاده می‌شود، نه JSON قابل ویرایش. CSS/JS این کنترل فقط در editor است؛ React مستقل نصب نشده است. Classic در حضور Atomic از پنل عادی پنهان و برای سند کلاسیک قابل مصرف می‌ماند؛ مدیریت عناصر خود Elementor حفظ است.

نمونهٔ `table-general`، thumbnail محلی و دادهٔ فارسی/انگلیسی در کتابخانه و درج Classic/Atomic ثبت شده‌اند. preview بومی Atomic از endpoint خواندنی مشترک PHP با nonce/capability، درخواست لغوشونده و CSS منتخب استفاده می‌کند؛ موتور دوم در JS ندارد. در ادامهٔ سورس ۲۰۲۶-۱۰-۲۵، XML WPML علاوه بر caption، عنوان تمام ۱۲ ستون و سلول‌های ردیف‌ها را ثبت می‌کند؛ سقف ۱۲ ستون همان قرارداد جدول است و طول واقعی داده به‌خاطر ترجمه افزایش نمی‌یابد. Classic از مسیرهای `data>columns>0` و `data>rows`/`cells>0` و Atomic از envelopeهای `data>value>columns>value>0>value` و `data>value>rows>value`/`value>cells>value>0>value` استفاده می‌کند. سرستون LINE و سلول VISUAL است؛ نوع envelope، شناسه و تنظیمات ظاهری ترجمه نمی‌شوند. مسیر `>` و خواندن/بازگردانی آن در سورس WPML 5.0.2 ارسالی بررسی شد؛ قرارداد XML/Python تا ۱۲ ستون و ۱۰۰ ردیف پاس شد. hook، اسکن frontend، درخواست یا تبدیل JSON تازه ندارد؛ این نگاشت برای دادهٔ سند Elementor است و آرایهٔ PHP اختصاصی مشتری را خودکار وارد ATE نمی‌کند. استخراج/ترجمه/بازگردانی در نصب WPML هنوز تأیید نشده؛ ویرایش مستقل جدول در سند زبان نیز برقرار است.

بررسی: ۲۱ قرارداد کوتاه Node برای دارایی/کتابخانه/داده، قرارداد PHP جدول با KSES واقعی WordPress، schema/control/config واقعی Elementor 4.3.5 و مدل‌های دو زبان، قرارداد کتابخانه و lint PHPهای جدید پاس شدند. Build minified ES2020 پاس شد. چرخهٔ درج/Undo/ذخیره/بازگشایی در editor واقعی، ظاهر مرورگر/موبایل و WPML واقعی در این تغییر بررسی نشده‌اند.

```php
rdct_table(
    array(
        'columns' => array( 'Feature', 'Value' ),
        'rows' => array(
            array( 'cells' => array( 'Material', '<strong>Steel</strong><small>304</small>' ) ),
            array( 'cells' => array( 'Width', '120 cm' ) ),
        ),
    ),
    array( 'caption' => 'Specifications', 'variant' => 'grid', 'row_headers' => true )
);
```

## Image — Components 0.1.3، ۲۰۲۶-۱۰-۲۱

مرجع محدود و خوانده‌شده: `themes/hub-elementor-addons/elementor/widgets/fancy-image.php`، rule/assets همان ویجت، CSSهای fancy-image-base، float، fixed label و چهار shadow. موتور مستقل `inc/components/image.php` و helper `rdct_image($options)` بین Classic `rdct-image` و native Atomic `rdct-atomic-image` مشترک‌اند. Image در کتابخانهٔ محلی، مدل Classic/Atomic، endpoint read-only مشترک preview و mapping برچسب/لینک WPML اضافه شده است. مدیریت عناصر و پنهان‌کردن کارت تکراری Classic همان مسیر بومی قبلی است؛ اجرای واقعی Element Manager روی هاست هنوز تأیید نشده است.

| بخش Fancy Image | نگاشت رادمان |
|---|---|
| Image / resolution / dimensions / object-fit / CSS filters / percentage / alignment / link | attachment وردپرس با WPML، full/large/medium و ابعاد سفارشی؛ width/max-width/height/عرض ظرف و fit، فیلترهای blur/brightness/contrast/saturation/hue؛ لینک، target/nofollow |
| Opacity / hover | حالت عادی و hover/focus؛ مستقل از فیلتر تصویر |
| Lightbox / group ID | dialog بومی مشترک در هر document، گروه دلخواه، قبلی/بعدی، Escape/بازگرداندن focus و لینک fallback بدون JS؛ کتابخانهٔ Fresco منتقل نشده است |
| Roundness | none/2/4/6/8px/circle و چهار مقدار CSS محدود سفارشی |
| Shadow / custom / animated / delay | چهار عمق مطابق مقادیر CSS مرجع، x/y/blur/spread/color/inset و سایهٔ متحرک با delay |
| Reveal / background / direction / delay | پوشش یک‌باره با چهار جهت فیزیکی و start/end منطقی، solid/linear/radial، دو رنگ/stop/angle/position و delay؛ افکت Image با reveal اسکرولی کانتینر متفاوت است |
| Float from/to/delay/easing/custom | شناوری مستقل با درصد یا px، مدت و تأخیر ثانیه‌ای و easing محدود یا cubic-bezier معتبر؛ حرکت خارج viewport متوقف می‌شود |
| Hover 3D / stacking factor | pointer دقیق و RAF، ضریب عمق، reset هنگام خروج/resize/scroll؛ روی لمس یا reduced motion اجرا نمی‌شود |
| Lines / count / color | تعداد محدود ۱ تا ۲۰ و رنگ |
| Overlay background | رنگ/گرادیان یا تصویر رسانه، اندازه/تکرار/جایگاه؛ تصویر فقط در حالت solid مؤثر است |
| Side label / side / position / typography / overlay / colors | برچسب HTML مجاز، start/end یا چپ/راست، سه جایگاه عمودی، font/size/weight/line-height/spacing، حالت کنار یا روی تصویر و رنگ‌ها؛ زیر تبلت، برچسب بیرونی افقی می‌شود |

Classic برای ابعاد و تراز از responsive controls بومی با واحدها و selector استفاده می‌کند و Typography بومی برچسب دارد. Atomic همان فیلدهای مشترک را به prop/control بومی و image typed تبدیل می‌کند؛ ابعاد override در گروه‌های laptop/tablet/mobile با breakpointهای 1199/991/767 دارد و style عمومی خود Atomic هم باقی است. UI دقیقاً همان PHP گروه‌کنترل‌های Hub نیست؛ فونت برچسب از فونت موجود/بارگذاری‌شده استفاده می‌کند و فونت خارجی تازه دریافت نمی‌شود. اندازهٔ سفارشی فایل با API attachment وردپرس downsize می‌شود؛ تولید crop جدید Elementor و همهٔ زیرگزینه‌های background/typography هاب عیناً بازتولید نشده‌اند. این‌ها تفاوت‌های ثبت‌شده‌اند، نه ادعای برابری کامل پنل یا تطابق پیکسلی.

CSS در `src/css/components/image/{base,motion,lightbox}.css` و رفتار در `src/js/components/image.js` و `image-lightbox.js` است. تصویر ساده JS نمی‌خواهد؛ base و فقط CSS حرکت/لایت‌باکس فعال می‌آیند. فایل لایت‌باکس بعد از کلیک import می‌شود؛ بدون JS لینک اصلی کار می‌کند. تصویر بدون انتخاب رسانه، placeholder SVG محلی کتابخانه دارد؛ تصویر با ID نامعتبر رندر نمی‌شود. موتور محلی jQuery/GSAP تازه ندارد؛ تصاویر با API وردپرس، width/height/srcset و سیاست loading همان وردپرس می‌آیند. رنگ RGBA با کنترل مشترک alpha در Atomic قابل ویرایش است؛ کد کنترل رنگ تکرار نشده است.

**Components 0.1.6:** adapter لایت‌باکس اکنون موتور `shared/frontend/image-dialog.js` مشترک با گالری محتوا را مصرف می‌کند؛ ساخت DOM، قبلی/بعدی، کیبورد/لمس، وضعیت loading/error/شمارهٔ تصویر، قفل و بازگرداندن اسکرول و فوکوس دیگر دو پیاده‌سازی جدا ندارند. متن‌های جدید از PHP و ترجمهٔ افزونه می‌آیند. گروه‌ها فقط اعضای همان شناسه را نشان می‌دهند؛ گروه تک‌تصویر کنترل پیمایش غیرفعال دارد. خطای فایل، بستن یا انتخاب تصویر بعد را مسدود نمی‌کند؛ تصویر خراب با selector صریح hidden پنهان است. لینک download یا `_blank` در adapter Image رهگیری نمی‌شود. نبود dialog و شکست import/showModal مسیر پیوند اصلی را نگه می‌دارد. آزمون کوتاه گروه، خطا/بازیابی، تصویر cacheشده و pagehide پاس شد؛ چرخهٔ واقعی editor و مرورگر در این بازآرایی بررسی نشدند.

بررسی‌های کوتاه و بستهٔ همراه در سند پیاده‌سازی ثبت می‌شوند. کیفیت بصری تمام حالت‌ها، بازشدن واقعی editor، دستگاه لمسی، WPML workflow و جابه‌جایی داخل کتابخانه روی هاست هنوز تأیید نشده‌اند. این feature جای گالری محصول/نمونه‌کار یا مالکیت global motion قالب را نمی‌گیرد.

نمونهٔ PHP مستقل:

```php
rdct_image( array( 'image_id' => 123, 'roundness' => '8px', 'shadow' => 'depth2', 'reveal' => true, 'reveal_direction' => 'start' ) );
```

## Icon Box پایه، تصویر و SVG — ۲۰۲۶-۱۰-۲۱

در Components 0.1.1 با Theme همراه 0.1.9 اضافه شد. مرجع خوانده‌شده: `themes/hub-elementor-addons/elementor/widgets/icon-box.php`، CSSهای base/side/inline و shaped base/lozenge. سه چیدمان top/inline/side و شکل none/square/circle/lozenge نگاشت شده‌اند. موتور `radmanct-components/inc/components/icon-box.php` بین helper `rdct_icon_box($options)`، Classic `rdct-icon-box` و native Atomic `rdct-atomic-icon-box` مشترک است. ورودی محلی آیکون یا attachment تصویر، عنوان/تگ، توضیح HTML مجاز، پیوند/متن لینک/target/nofollow، تراز منطقی، اندازه/فاصله و رنگ/hover دارد. Classic ابعاد responsive و Typography بومی دارد؛ Atomic image typed، کنترل‌های Content/Style و همان renderer PHP را در preview مصرف می‌کند. افزودن جزئیات بیشتر از Hub و برابری تمام sub-controlها جزو نسخهٔ پایه ادعا نمی‌شود؛ مالک امکانات بعدی را مشخص می‌کند.

Grid با columnهای منطقی و start/end، جهت را از صفحه می‌گیرد؛ CSS فیزیکی left/right برای جایگاه آیکون ندارد. تصویر/لوگو همراه عنوان تزئینی است و alt خالی دارد؛ لینک عنوان نام قابل‌فهم خود را دارد. CSS پایه و فقط layout منتخب بارگذاری می‌شوند؛ خروجی عمومی Icon Box هیچ JS/GSAP ندارد. helper، مدل کتابخانه و WPML mapping مستقل از Elementor اصلی‌اند. تصویر با API استاندارد attachment و WPML ID خوانده می‌شود؛ از URL دلخواه درخواست خارجی انجام نمی‌شود.

در Components 0.1.23، `icon_side` آغاز/پایان و `vertical_align` با مقادیر auto/start/center/end مستقل از `layout` اضافه شدند؛ auto تراز پیش‌فرض هر چیدمان را برمی‌گرداند. `align` حالت justify نیز دارد و عنوان/توضیح عرض ستون را می‌گیرند؛ Justify به `justify-items` نامعتبر تبدیل نمی‌شود. Icon مستقل `icon_align` آغاز/وسط/پایان دارد. همه از موتور مشترک responsive و همان کلیدهای `_responsive` استفاده می‌کنند؛ مثلاً `icon_side => end` همراه `layout_responsive => array( mobile => top )`. ستون منطقی با CSS variables در همان variant منتخب جابه‌جا می‌شود؛ تغییر دستگاه نیاز به بارگذاری variant اضافی یا JS ندارد. کنترل Classic بومی CHOOSE و Atomic انتخاب بومی ToggleControl با Reset است؛ ظاهر یکسان پنل‌های Classic و Atomic ادعا نمی‌شود. مرجع Hub موقعیت و CHOOSE دوباره خوانده و API رسمی [responsive control](https://developers.elementor.com/docs/editor-controls/responsive-control/) تطبیق داده شد. تراز عمودی در چیدمان کنار محتوا نسبت به دو ردیف عنوان/توضیح و در حالت هم‌ردیف نسبت به عنوان عمل می‌کند؛ در حالت بالا ردیف مستقل دارد.

انیمیشن اختیاری none/spin/float/pulse/draw با target whole/parts، trigger hover/always و مدت ۰٫۲ تا ۲۰ ثانیه است. CSS انیمیشن فقط وقتی فعال باشد enqueue می‌شود؛ hover شامل focus کیبورد است و روی دستگاه بدون hover اجرا می‌شود. reduced motion آن را خاموش و خطوط را خوانا نگه می‌دارد. SVGهای registry محلی و SVG attachment محلی می‌توانند جزءبه‌جزء حرکت کنند؛ raster فقط حرکت کل تصویر دارد. presetهای CSS اجرا می‌شوند؛ script، style و SMIL دلخواهِ SVG وارد DOM نمی‌شوند. هدف فعلی پشتیبانی امن از حرکت SVG است، نه اجرای هر animation داخل فایل آپلودشده.

`inc/components/svg-image.php` فقط SVG موجود در uploads محلی را با سقف ۱۲۸KB، ۲۰۰۰ عنصر و عمق ۳۲ می‌خواند؛ DOM با NONET و رد DOCTYPE/ENTITY، whitelist هندسه/گرادیان/clipPath و attributeها ساخته می‌شود. event، script، foreignObject، href خارجی و CSS آزاد حذف، ID و ارجاع گرادیان هر نمونه یکتا می‌شوند. این قابلیت آپلود SVG را در کل WordPress فعال نمی‌کند. بدون DOM extension، فایل محلی قابل‌خواندن یا ساختار پشتیبانی‌شده، خروجی استاندارد تصویر باقی است؛ انیمیشن اجزا به SVG inline نیاز دارد. رسانهٔ offloadشده وارد sanitizer محلی نمی‌شود.

پیش‌نمایش read-only مشترک Icon Box/Flip Box در `inc/admin/component-render.php` و `atomic-render-preview.js` است؛ nonce و capability، allowlist نوع، سقف payload، debounce و abort مشترک‌اند. با انتخاب Icon Box در کتابخانه، نمونهٔ فارسی/انگلیسی از JSON زبان خودش می‌آید. فقط schema/control/config واقعی Atomic، render PHP، SVG ایمن و assetهای build به‌صورت کوتاه بررسی شدند؛ مرورگر، ادیتور هاست و workflow WPML هنوز تأیید نشده‌اند.

مصرف سفارشی: `rdct_icon_box( array( 'title' => 'Service', 'icon' => 'check', 'layout' => 'side', 'animation' => 'spin', 'animation_target' => 'parts' ) );`. برای چاپ CSS در head می‌توان پیش از `get_header()`، `rdct_use('icon-box')` و `rdct_use('icon-box-side')` و در صورت نیاز `rdct_use('icon-box-animation')` را اعلام کرد؛ helper چاپ دیرهنگام و deduplication را هم حفظ می‌کند. همهٔ مصرف‌ها از همان تنظیمات whitelist و جهت ارث‌برده استفاده می‌کنند.

## مالکیت مستقل و Flip Box — ۲۰۲۶-۱۰-۲۱

**ادامهٔ چرخهٔ عمر، ۲۰۲۶-۱۰-۲۶:** خروج فوکوس queued پس از destroy می‌توانست aria/inert را روی root آزادشده دوباره بنویسد؛ خطا با قرارداد کوتاه بازتولید و با guard عمر نمونه اصلاح شد. cleanup تکراری بی‌اثر است و remount نمونهٔ تازه می‌گیرد. MediaQuery اشاره‌گر به‌ازای نمونه یک بار ساخته و listener آن هنگام cleanup آزاد می‌شود؛ تغییر به دستگاه فاقد hover، حالت hover را می‌بندد ولی سمت پشتِ دارای فوکوس را پنهان نمی‌کند. کلیک، Escape و لمس حفظ شدند. این قرارداد شبیه‌سازی DOM است، نه پذیرش editor واقعی.

افزونهٔ `radmanct-components/` مالک `inc/components/`، کتابخانه و adapterهای Elementor است. source در `src/css/components/` و `src/js/components/`، تصاویر در `images/components/`، ترجمه در domain مستقل و خروجی در `dist/` خود افزونه قرار دارند. آیکون پایه و موتور از source مشترک با خروجی مستقل Theme/Components مصرف می‌شوند؛ مجموعهٔ مشتری در Child ثبت می‌شود و قالب بدون افزونه نیز آیکون دارد؛ [مالک آیکون](icons.md). تمام مسیرهای نسبیِ بخش‌های نمایشی زیر، نسبت به این افزونه‌اند؛ content-body، جدول، navigation و container-motion همچنان متعلق به Theme هستند. `rdct_use` برای منو به قالب واگذار می‌کند. با افزونهٔ خاموش، helperهای قالب fatal نمی‌دهند؛ سایر قابلیت‌های نمایشی افزونه نیازمند فعال‌بودن آن‌اند.

بارگذاری اولیهٔ افزونه بدون CSS/JS عمومی و بدون Elementor است؛ فقط رجیستری سبک و hookها آماده می‌شوند. CSS پایه کوچک و scopeدار است؛ هر کامپوننت و جهت/سبک منتخب جدا enqueue می‌شود. PHP helperهای فعلی و شناسه‌های Classic/Atomic حفظ شده‌اند. قالب دیگر نیز مسیر asset مستقل و رنگ‌های fallback دارد؛ بررسی عملی روی قالب ثالث انجام نشده است. فونت/رنگ Theme با filterهای `radmanct_components/admin_css` و `preview_css` متصل‌اند. GSAP/ScrollTrigger از یک source vendor در افزونه ساخته می‌شود؛ adapter قالب URL vendor در graph مشترک motion قالب را می‌دهد، و افزونهٔ مستقل fallback build محلی خودش را دارد. ورود به این dependency فقط برای Text Reveal اسکرولی است؛ کارت‌های دیگر GSAP ندارند.

نام موردنظر مالک «Flip Box» بود، نه Flexbox. مرجع خوانده‌شده: `themes/hub-elementor-addons/elementor/widgets/flipbox.php`، rule/asset همان ویجت و ده فایل CSS پوشهٔ flipbox. چهار جهت lr/rl/tb/bt، سه سایه none/always/hover و چهار گوشه none/semi-round/round/circle نگاشت شدند. هر سمت متن HTML مجاز یا template منتشرشدهٔ Elementor، پس‌زمینه/تصویر/گرادیان، overlay و padding جدا دارد. Classic از Group Background و responsive dimensions بومی، Atomic از schema/کنترل‌های native، image typed و سه گروه General/Front/Back استفاده می‌کند؛ HTML در Atomic فعلاً textarea است و همهٔ sub-controlهای gradient/ابعاد بومی Classic در Atomic برابر نیستند. template selector برای پنل حداکثر ۱۰۰ قالب منتشرشده را می‌خواند؛ PHP ID مستقیم را هم می‌پذیرد. template محافظ رمز/غیرمنتشرشده وارد خروجی نمی‌شود؛ WPML ID و recursion guard چهارمرحله‌ای رعایت می‌شوند.

موتور PHP مشترک `inc/components/flipbox.php` و helper `rdct_flipbox($options)` هستند؛ Classic `rdct-flipbox` و native Atomic `rdct-atomic-flipbox`. پایه و CSS جهت منتخب جدا، سایه/گوشه با modifier مشترک و JS مستقل Vanilla است. بدون JS هر دو سمت خواندنی‌اند؛ با JS دکمهٔ keyboard/touch، hover برای mouse، Escape، inert/aria-hidden، reduced motion و cleanup وجود دارد. پشت کارت با grid هم‌اندازهٔ محتوا است و height ثابت باعث بریدن متن نمی‌شود. wrapper عمداً overflow عمومی را پنهان نمی‌کند.

پیش‌نمایش Atomic Flip Box همان renderer PHP را با AJAX محلی و debounce ۲۰۰ms مصرف می‌کند؛ درخواست قدیمی abort و نتیجهٔ حذف‌شده نادیده گرفته می‌شود. endpoint فقط برای کاربر ویرایشگر با nonce است؛ payload حداکثر ۶۰KB و template فقط منتشرشده است. این endpoint read-only است. اسکریپت template در این پیش‌نمایش اجرا نمی‌شود؛ خروجی نهایی از Elementor استاندارد می‌آید. تطبیق بصری زنده با Hub، workflow واقعی WPML و تمام sub-controlهای Atomic هنوز تأیید نشده‌اند. تفاوت‌های قبلی Curtain/Box در ادامهٔ سند همچنان باز هستند؛ انتقال افزونه به معنی رفع آن‌ها نیست.

بستهٔ افزونه 0.1.0 و قالب همراه 0.1.8 است. برای نسخهٔ جدید قالب، افزونه را هم نصب/فعال کنید؛ کد قدیمی کامپوننت داخل قالب حذف شده است. کتابخانه زیر منوی قالب و بدون Radman CT زیر Tools قرار می‌گیرد. مسیر نصب، نسخه و آپدیت Components در Plugins and requirements دیده می‌شود.


## Curtain و Box — سورس جاری، ۲۰۲۶-۱۰-۲۱

**اصلاح پایداری Curtain — Components 0.1.13:** هاور با حرکت واقعی pointer و مکث ۱۲۰ms فعال می‌شود؛ جابه‌جایی مرز کارت زیر ماوس ثابت، فعال‌سازی تازه ایجاد نمی‌کند. خروج، کلیک/فوکوس، تغییر breakpoint و cleanup درخواست معلق را لغو می‌کنند. عرض نهایی محتوا از عرض ظرف، فاصله و rail محاسبه می‌شود و وسط transition نمونه‌برداری نمی‌شود؛ flex-basis کارت‌ها ثابت می‌ماند. رفتار کیبورد/لمس و RTL حفظ شده است. سه بررسی DOM کوتاه شامل ماوس ثابت، خروج و لغو timer پاس شدند؛ پذیرش بصری روی هاست هنوز لازم است. عنوان نمایشی اکنون Expandable Panels است؛ شناسهٔ `rdct-curtain` حفظ شده است.

مرجع Curtain: `themes/hub-elementor-addons/elementor/widgets/curtain.php`، `assets/css/elements/curtain/curtain-base.css` و بخش LiquidCurtain در `themes/hub/assets/js/theme.js` (۱۶۳۴۴ به بعد). آیتم فعال، کلیک/hover، عنوان عمودی، بازشدن محتوا و تبدیل به چیدمان عمودی از این مسیر تطبیق داده شدند. هستهٔ مستقل `inc/components/curtain.php`، CSS اختصاصی و `src/js/components/curtain.js` بین PHP/Classic/Atomic مشترک‌اند. تعداد آیتم‌ها سقف ۱۲ دارد؛ تصویر/لوگو/پس‌زمینه از attachment، آیکون از registry محلی، و محتوا از HTML مجاز می‌آید. بدون JS محتوای همهٔ آیتم‌ها باقی است؛ با JS، aria-expanded/inert، کلیدهای جهت/Home/End، RTL، عدم ربودن focus هنگام hover، اندازه‌گیری RAF و cleanup محلی فعال‌اند. حالت افقی فقط وقتی خود کامپوننت فضای کافی دارد فعال می‌شود.

مرجع Box: `elementor/widgets/fancy-box.php` با نام نمایشی Liquid Box، همراه rule/asset همان ویجت و پوشهٔ `assets/css/elements/fancy-box/`. ۱۳ شناسهٔ s01/s01a/s01b و s02 تا s11 به فایل‌های مستقل `src/css/components/box/` نگاشت شده‌اند. پایهٔ CSS یک handle مستقل دارد و هر نمونه فقط CSS سبک منتخب را enqueue می‌کند؛ پایه در چند سبک تکرار bundle نمی‌شود. `inc/components/box.php` مالک validation/renderer مشترک است. عنوان/تگ/ارث‌بری، زیرعنوان، label، تصویر، پیوند/target/nofollow، متن/دکمه، آیکون، نسبت/ارتفاع، عرض/تراز، padding، رنگ/گرادیان/پوشش و hover در schema مشترک هستند؛ کلاسیک گروه‌های Typography و کنترل‌های responsive ابعاد را دارد. رفتار ساده hover با CSS و حالت‌های نیازمند رفتار با `components/box.js` و RAF محلی مصرف می‌شوند. چارچوب، icon font، jQuery، GSAP یا engine انیمیشن Hub منتقل نشدند.

Atomic دو کلاس `rdct-atomic-curtain` و `rdct-atomic-box` دارد. آیتم‌های Curtain یک Array/Object PropType بومی با مقادیر typed و سقف معتبر دارند؛ کنترل ویرایشگر از registry بومی و useBoundProp/React موجود استفاده می‌کند، نه JSON متنی یا React دوم. Box تصویر بومی Image_Prop_Type دارد. Twig دادهٔ escape‌شده می‌دهد؛ پیش‌نمایش Box و Curtain از renderer PHP مشترک در endpoint موجود استفاده می‌کند. observer و مدیریت عمر stylesheet پیش‌نمایش مشترک‌اند؛ تغییر سبک Box فقط stylesheet انتخاب‌شده را درخواست می‌کند. کد و CSS مدیریت به frontend عادی وارد نمی‌شوند.

دو ویجت Classic با نام‌های جدید `rdct-curtain`/`rdct-box` ثبت‌اند؛ در حضور Atomic برای پنل/جستجو پنهان می‌شوند و برای سند کلاسیک باقی می‌مانند. فیلتر بومی `elementor/widgets/is_widget_enabled` در Element Manager نسخهٔ 4.3.5 مرجع غیرفعال‌کردن است؛ تنظیم موازی ساخته نشده است. PHP به فعال‌بودن Elementor وابسته نیست. هر دو کامپوننت در کتابخانهٔ محلی نمونه دارند و allowlist درج Classic/Atomic گسترش یافته است.

**حدود جاری و کار باقی‌مانده:** تطابق کامل تمام فیلدها/حالت‌های Hub هنوز تأیید نشده است. فیلدهای Template، موقعیت/اندازهٔ آیکون، تگ آیتم و border/gradient در schema فعلی Curtain وجود دارند؛ پذیرش واقعی Template و تطابق تمام زیرکنترل‌ها/overrideهای Normal/Active با مرجع باقی است. Box به همهٔ شناسه‌های سبک و فیلدهای اصلی نگاشت دارد، ولی ترتیب/شرایط تمام کنترل‌ها، موتور دکمهٔ کامل Hub، گرادیان چندمرحله‌ای و برابری بصری/زمان‌بندی انیمیشن‌ها پذیرش نشده‌اند. Atomic برای بعضی کنترل‌های typography/ابعاد از UI بومی wrapper و کنترل‌های ساده استفاده می‌کند؛ ادعای برابری همهٔ sub-controlها با Classic نیست. mapping WPML افزوده شده ولی استخراج/بازگردانی واقعی تکرارهای Atomic هنوز آزمایش نشده است. این بخش تا بستن این تفاوت‌ها «کپی کامل Hub» نامیده نشود.

بررسی کوتاه: Build و ۱۶ بررسی Node مرتبط، قرارداد PHP Curtain، schema/control/config بومی چهار ویجت با کلاس‌های واقعی Elementor 4.3.5 و مدل‌های دو زبان، و قرارداد کتابخانه پاس شدند. بررسی بصری/ادیتور واقعی انجام نشده است؛ بستهٔ مستقل و مالکیت جدید در بخش ابتدای همین سند ثبت شده‌اند.

مصرف فعلی PHP:

```php
rdct_use( 'curtain' );
rdct_use( 'box' );
rdct_use( 'box-s01' );
get_header();
rdct_curtain( $items, array( 'active_item' => 1, 'trigger' => 'click' ) );
rdct_box( array( 'template' => 's01', 'title' => 'Service', 'image_id' => 42 ) );
```

## بدنهٔ محتوای عمومی و جدول — ۲۰۲۶-۱۰-۲۰

قواعد بدنهٔ `.rdct-cnt` از فایل پایه به `src/css/components/content-body.css` منتقل و یک بار از theme.css import می‌شوند؛ برگه، مقاله، محصول، نمونه‌کار و Child همین قرارداد را مصرف می‌کنند. محدودیت خواندن، alignwide/alignfull، عنوان، سلول جدول و pre همان مالک را دارند. متن بلند با overflow-wrap و رسانهٔ داخل این scope با max-inline-size به ظرف محدود می‌شوند؛ overflow کل body مخفی نشده و selectorهای Elementor هدف قرار نگرفته‌اند.

`src/js/components/content-tables.js` فقط پس از مشاهدهٔ `.rdct-cnt table` در frontend به‌صورت lazy وارد می‌شود. جدول کلاسیک یک ظرف محلی می‌گیرد؛ ظرف `.wp-block-table` موجود دوباره ساخته نمی‌شود، alignwide/alignfull، caption، ID جدول و ویژگی‌های دسترس‌پذیری موجود حفظ می‌شوند. جدول تو‌در‌تو ظرف اضافی نمی‌گیرد. ResizeObserver و آماده‌شدن فونت/resize، اندازه‌گیری را با یک requestAnimationFrame هم‌زمان می‌کنند؛ tabindex و region نام‌دار فقط هنگام سرریز واقعی و نبود مقدار اختصاصی اضافه می‌شوند. نام از caption/figcaption یا رشتهٔ ترجمه‌شدهٔ محلی body می‌آید. با جا شدن جدول، صفات اصلی برمی‌گردند؛ cleanup observer/listener را آزاد می‌کند و bfcache آن را زودهنگام نابود نمی‌کند.

بدون JS، جدول و محتوایش در HTML اولیه باقی‌اند و ظرف بومی جدول Gutenberg CSS پیمایش دارد؛ ظرف خودکار جدول کلاسیک و فوکوس مبتنی بر اندازه به این بهبود JS نیاز دارند. درج دلخواه محتوای پویای بعد از بارگذاری، خودکار اسکن نمی‌شود؛ مصرف‌کننده باید initializer و cleanup را مدیریت کند. این ماژول داده، query یا وابستگی خارجی ندارد. الگوی استایل بلوک/بدنه از بررسی محدود `themes/woodmart/css/parts/wp-blocks.css` تطبیق داده شد؛ framework مرجع منتقل نشد.

**تأکید مالک — ۲۰۲۶-۱۰-۲۰:** رفتار و استایل واقعاً مشترک یک مالک داشته باشند؛ برای هر مسیر PHP/سازنده/Elementor نسخهٔ موازی ساخته نشود. adapter فقط تفاوت اتصال را نگه دارد؛ تنظیمات، validation، renderer و lifecycle مشترک تا حد نیاز واقعی استفادهٔ مجدد شوند. جداسازی حالت‌ها به معنی کپی زیرساخت مشترک نیست و اشتراک نیز مجوز بارگذاری همهٔ حالت‌ها در هر صفحه نیست. نمونهٔ اجرایی منو و helper `rdct_menu()` در [مالک ناوبری](navigation.md) است؛ منوی Elementor همچنان مستقل می‌ماند.

وضعیت ۲۰۲۶-۱۰-۱۹: پایهٔ اجرایی با Text Reveal و آیکون ساخته شد. نیاز تأییدشده در [سناریو](../scenarios/components.md) است. PHP مستقل از Elementor و Core کار می‌کند؛ React عمومی یا جدول دادهٔ جدیدی نداریم. شواهد اجرایی و محدودیت‌های مرورگر در ادامه ثبت‌اند.

## لایه‌ها

### دو ویجت native Atomic — ۲۰۲۶-۱۰-۲۰

علاوه بر ویجت‌های کلاسیک با شناسهٔ ذخیره‌شدهٔ `radman-icon` و `radman-text-reveal`، دو ویجت `rdct-atomic-icon` و `rdct-atomic-text-reveal` از `Atomic_Widget_Base` واقعی Elementor 4.3.5 ساخته شدند. ثبت فقط با ماژول فعال Atomic انجام می‌شود؛ دادهٔ قدیمی تبدیل یا بازنویسی نمی‌شود. این دو در گروه Atomic و Radman CT هستند؛ Style/Classes/Variables و responsive بومی Elementor در wrapper خودشان باقی می‌مانند. فهرست فیلد و default در `inc/integrations/elementor/atomic-components.php` و کلاس‌ها/قالب‌های Twig در پوشهٔ `atomic-components/` همان ادغام قرار دارند.

renderer عمومی PHP همان `rdct_icon` و `rdct_text_reveal` است؛ engine یا SVG موازی نداریم. schema تایپ‌دار، classes/attributes بومی، کنترل‌های bound و template configuration برای ویرایشگر تعریف شده‌اند. جهت، تگ امن، برچسب دسترس‌پذیر، رفتار hover/scroll و تنظیمات زمانی/ترتیب/رنگ متن نگاشت می‌شوند. ورودی رنگ افکت فعلاً متن hex شش‌رقمی است و اعتبارسنجی renderer/preview دارد؛ انتخاب فونت و استایل کلی از UI بومی Atomic می‌آید.

Twig فقط adapter نمای ویرایشگر است. `elementor-atomic-components.js` فقط در preview Atomic می‌آید؛ دادهٔ فیلدها و SVGهای امن از همان registry PHP به JSON محلی تبدیل می‌شوند. SVG از آدرس یا HTML کاربر خوانده نمی‌شود. adapter متن با textContent و DOM API ساخته و از موتور مشترک Text Reveal استفاده می‌کند؛ hover/آیکون GSAP را import نمی‌کنند. signature هر marker، RAF، لغو import دیررس و cleanup روی تغییر/حذف از initialize تکراری جلوگیری می‌کنند. صفحهٔ عمومی این observer و کاتالوگ preview را نمی‌گیرد. bridge عمومی handler Atomic متن را نیز می‌شناسد.

گالری اکنون نوع کلاسیک/Atomic را مستقل انتخاب می‌کند و مدل نمونهٔ بومی را از schema واقعی ویجت تولید می‌کند؛ [قرارداد درج و شواهد](component-library.md). allowlist اشتباه قبلی `rdct-text-reveal/rdct-icon` به نام‌های واقعی و ثابت legacy اصلاح شده است. این قابلیت تبدیل خودکار سند موجود نیست.

ترجمهٔ متن و برچسب native Atomic در `wpml-config.xml` با مسیرهای استاندارد `text>value` و `label>value` ثبت شده است؛ mapping کلاسیک حفظ می‌شود. فقط متن انسانی ترجمه‌پذیر است و نوع envelope، نام آیکون، رنگ، تگ، زمان، جهت و شناسه‌ها ترجمه نمی‌شوند. تنظیم صریح widget بر AutoConfig نسخهٔ WPML 5.0.2 محلی اولویت دارد؛ در نتیجه کنترل‌های متنیِ رنگ و موقعیت اسکرول اشتباهاً برای ترجمه کشف نمی‌شوند. مبنای مسیر nested در `AtomicWidgetProcessor.php` و `WPML_Elementor_Translatable_Nodes::get_partial_path` و اولویت تنظیم صریح در `AutoConfig/Generator.php` بررسی شد. adapter عمومی، hook موازی، تغییر postmeta یا اتصال خارجی اضافه نشده است. XML و چهار mapping بررسی شدند؛ چرخهٔ واقعی استخراج/ترجمه/بازگردانی در نصب WPML هنوز پذیرش نشده است.

قرارداد `tests/atomic-components-contract.php` از Atomic base، PropTypes، Controls، Utils و Twig واقعی نسخهٔ محلی استفاده می‌کند؛ پایهٔ WordPress/Widget_Base و تنظیمات نمایش stub هستند. native editor configuration، bound schema، تگ/رنگ امن، renderer مشترک و escape قالب‌ها پاس شدند. پنج بررسی Node رفتار preview، مقادیر نامعتبر، SVG محلی، متن طولانی، edit/remove و import دیررس را پوشش می‌دهند. این شواهد به معنی اجرای واقعی editor/DB، ذخیره/بازگشایی یا پذیرش بصری نیستند.

فهرست مشترک نمونه‌ها، گالری مدیریت و پنجرهٔ درج Elementor مالک مستقل در [کتابخانهٔ محلی](component-library.md) دارند؛ آن‌ها مصرف‌کنندهٔ همین موتور کامپوننت‌اند، نه موتور نمایش موازی.

کامپوننت‌های رابط مدیریت و tokenهای ظاهر پنل مالک جدا در [رابط مشترک مدیریت](admin-ui.md) دارند؛ shared بودن آن‌ها دلیل واردشدن CSS/React مدیریت به صفحات عمومی نیست. قابلیت عمومی فقط هنگام مصرف assets خودش را می‌گیرد. Text Reveal و آیکون ویجت اختصاصی دارند؛ ابزارهای کاتالوگ فعلاً PHP/shortcode طبق [rendererها](renderers.md) هستند، نه ادعای وجود ویجت اختصاصی برای تمام قابلیت‌ها.

1. هستهٔ نمایشی مستقل از Elementor: schema محدود تنظیمات، validator و renderer PHP با خروجی escape‌شده، CSS اختصاصی و ماژول JS برای initialize/destroy یک root. React عمومی یا پایگاه دادهٔ موازی لازم نیست.
2. مصرف PHP/Child: مسیر آماده‌سازی `rdct_prepare()` در ابتدای این سند، یا اعلام صریح `rdct_use('text-reveal')` و `rdct_use('icons')` پیش از `get_header()`، CSS لازم را برای head اعلام می‌کند. helper رندر نیز آن را enqueue می‌کند و اگر اعلام قبلی نشده باشد، CSS دیرهنگام را یک‌بار همان موقع چاپ می‌کند. برای کشف استفاده همهٔ پست‌ها یا JSON المنتور اسکن نمی‌شوند.
3. اتصال اختیاری Elementor: کلاس ویجت فقط پس از حضور Elementor بارگذاری و controls را به schema/renderer مشترک نگاشت کند. assetهای ثبت‌شده با `get_script_depends()` و `get_style_depends()` اعلام شوند؛ کد انیمیشن دوباره نوشته نشود. handler ویرایشگر در root همان ویجت initialize کند و هنگام حذف/بازسازی، رویدادها و انیمیشن قبلی پاک شوند.
4. loader/dependency مشترک: CSS/JS هر قابلیت جدا، manifest معتبر و نسخهٔ minified در runtime؛ GSAP مشترک و محلی و ScrollTrigger در صورت نیاز. import درخواست هم‌زمان یک dependency را مشترک کند؛ تکرار نمونه‌ها موجب ثبت چند callback روی همان root نشود. وضعیت هر نمونه و cleanup آن محلی بماند.

مالک قابلیت نمایشی و اتصال Elementor در نسخهٔ فعلی Theme است. Core و ماژول‌های محصول/نمونه‌کار لازم نیستند. دادهٔ کاتالوگ و مرز مستقل آن تغییر نکرده‌اند.

## مسیرهای پیاده‌سازی و مصرف

| مالک | مسیر |
|---|---|
| فهرست و ثبت CSS/bridge | `radmanct/inc/components.php` |
| API سادهٔ PHP | `radmanct/inc/template-functions.php` |
| renderer متن | `radmanct/inc/components/text-reveal.php` |
| انیمیشن و cleanup | `radmanct/src/js/components/text/reveal.js` |
| اتصال چرخهٔ المنتور | `radmanct/src/js/integrations/elementor-components.js` |
| کلاس‌های ویجت | `radmanct/inc/integrations/elementor/widgets/` |
| CSS مستقل | `radmanct/src/css/components/icons.css` و `text-reveal.css` |
| آیکون و افزودن SVG | [قرارداد آیکون](icons.md) |

```php
rdct_use( 'text-reveal' );
get_header();
rdct_text_reveal( get_the_title(), array( 'tag' => 'h1', 'mode' => 'scroll' ) );
rdct_text_reveal( 'Hover example', array( 'mode' => 'hover', 'direction' => 'ltr' ) );
```

## مرجع Hub و رفتار متن

مرجع دقیق `themes/hub-elementor-addons/elementor/widgets/text-reveal.php` نسخهٔ 6.0.3 است؛ `ld_text_reveal` متنی با `words, chars`، opacity اولیه/نهایی، stagger و ScrollTrigger با `scrub=3` تولید می‌کند. پس این ویجت وابسته به اسکرول است و برداشت اولیهٔ hover اصلاح شد. موتور مشترک در `themes/hub/assets/js/theme.js` است و rule استایل در `hub-elementor-addons/elementor/optimization/widget-assets/rules/text-reveal.php` قرار دارد.

`liquidSplitText` در theme.js برای RTL نوع `chars` را حذف و آماده‌شدن فونت/resize را لحاظ می‌کند؛ در مقابل ویجت opacity خود را روی `.lqd-chars` هدف می‌گیرد. این ناهمخوانی در سورس دیده شد؛ علت قطعی خرابی بصری گزارش‌شده بدون مرورگر تأیید نشده است. framework یا splitter آن وارد رادمان نشد.

رادمان در حالت scroll، HTML اولیهٔ کامل و escape‌شده دارد. فقط در حضور marker scroll، GSAP/ScrollTrigger مشترک و محلی import می‌شوند. آستانه‌های فعلی `top 80%` تا `bottom 35%`، scrub برابر ۰٫۵ و opacity اولیه پیش‌فرض ۰٫۲۵ است. پس از آماده‌شدن فونت، واحدهای جداشده با whitespace، span می‌گیرند؛ نه حروف. نیم‌فاصله و اتصال داخل کلمه، newline و ترتیب متن حفظ می‌شوند؛ جهت از صفحه یا auto/rtl/ltr می‌آید. splitter خط نداریم و resize نیاز به بازسازی متن ندارد؛ ScrollTrigger موقعیت root را تازه می‌کند.

برای screen reader یک متن کامل باقی می‌ماند و نسخهٔ بصری aria-hidden است. WeakMap از initialize تکراری جلوگیری می‌کند؛ در reduced motion یا destroy، context پاک و متن اصلی برمی‌گردد. تکمیل font promise پس از destroy نمونهٔ تازه‌ای نمی‌سازد. بدون JS، متن کامل و خواناست.

حالت hover فقط CSS رنگ اولیه/فعال دارد و focus کیبورد نیز آن را فعال می‌کند؛ روی دستگاه بدون hover و در reduced motion متن با رنگ فعال نمایش داده می‌شود. این حالت به GSAP وابسته نیست. متن بیش از ۱۶۰۰۰ بایت رد می‌شود؛ scroll بیش از ۴۰۰ کلمه static می‌ماند تا DOM و انیمیشن بزرگ ساخته نشود. رنگ‌ها hex شش‌رقمی و tag/direction محدودند؛ HTML دلخواه رندر نمی‌شود. کنتراست نهایی و رنگ مناسب زمینه باید در طراحی پروژه بررسی شوند.

دو ویجت «آشکارسازی متن رادمان» و «آیکون رادمان» در گروه رادمان Elementor همان renderer را مصرف می‌کنند. bridge متن با dependency `elementor-frontend` و type=module، root ویجت را initialize و در onDestroy پاک می‌کند. hover از bridge به GSAP import نمی‌کند؛ آیکون script dependency ندارد. `radmanct/wpml-config.xml` متن و label آیکون را برای ترجمه معرفی می‌کند؛ اجرای واقعی WPML هنوز انجام نشده است.

## کنترل‌های Text Reveal در PHP و Elementor

وضعیت فعلی Text، حالت‌های اضافه، کنترل‌ها و فهرست دقیق اختلاف با Hub در [مالک Text](text.md) است؛ شناسه‌های پیشین حفظ شده‌اند. توضیحات پایهٔ زیر برای reveal قبلی همچنان معتبرند؛ فهرست کامل امکانات فعلی نیستند.

تکمیل ۲۰۲۶-۱۰-۱۹ با الگوی کنترل‌های Hub: انتخاب `p/div/span/h1…h6` از قبل وجود داشت. اکنون هر دو مسیر گزینه‌های `opacity`، `final_opacity`، `start`، `end`، `from`، `scrub` و `stagger` را به همان renderer و موتور مشترک می‌دهند. ترتیب start/end بر اساس ترتیب خواندن کلمات در DOM است، نه چپ/راست فیزیکی؛ center/edges/random نیز قابل انتخاب‌اند. حروف و نیم‌فاصلهٔ فارسی جدا نمی‌شوند.

پیش‌فرض‌های قبلی حفظ شدند: تگ p، شفافیت اولیه 0.25، نهایی 1، شروع `top 80%`، پایان `bottom 35%`، نرمی 0.5 و فاصلهٔ کلمات 0.12. شفافیت 0.1 تا 1، نرمی 0 تا 5 و فاصلهٔ کلمات 0 تا 1 محدودند. موقعیت‌ها فقط دو عبارت top/center/bottom یا درصد 0 تا 100 می‌پذیرند؛ JavaScript، callback یا selector دلخواه پذیرفته نمی‌شود. مقادیر نامعتبر به پیش‌فرض امن برمی‌گردند. این گزینه‌های انیمیشن مخصوص حالت scroll هستند.

در Elementor، تب محتوا مالک متن، تگ، جهت و انیمیشن است؛ تب استایل رنگ، تراز responsive با start/end منطقی و تایپوگرافی را دارد. کنترل قدیمی `font_size` حفظ شده است؛ گروه استاندارد Typography اندازه و font-family تکراری ندارد و وزن، ارتفاع خط، فاصلهٔ حروف و سایر کنترل‌های بومی را ارائه می‌کند. خانوادهٔ فونت از قرارداد مرکزی رادمان پیروی می‌کند؛ این ویجت اتصال Google Fonts جدیدی ایجاد نمی‌کند. فاصلهٔ حروف باید برای فارسی با احتیاط تنظیم شود.

در صفحهٔ سفارشی همین گزینه‌ها در PHP استفاده می‌شوند و اندازه/وزن/فاصله و breakpointها با CSS صفحه یا Child Theme روی `.rdct-tr` تنظیم می‌شوند؛ نیازی به JSON یا CSS المنتور در صفحهٔ سفارشی نیست. گزینهٔ PHP `align` نیز start/end/center/justify را می‌پذیرد. برای استایل متفاوت هر نمونه، selector را زیر container اختصاصی صفحه محدود کنید.

```php
rdct_use( 'text-reveal' );
get_header();
rdct_text_reveal( get_the_title(), array(
    'tag' => 'h2', 'direction' => 'rtl', 'align' => 'start',
    'opacity' => 0.25, 'final_opacity' => 1,
    'start' => 'top 70%', 'end' => 'bottom bottom',
    'from' => 'start', 'scrub' => 0.5, 'stagger' => 0.12,
) );
```

مرجع API: [کنترل گروهی تایپوگرافی Elementor](https://developers.elementor.com/docs/editor-controls/group-control-typography/)، [کنترل responsive](https://developers.elementor.com/docs/editor-controls/responsive-control/) و [موقعیت ScrollTrigger](https://gsap.com/docs/v3/Plugins/ScrollTrigger/). کنترل‌ها با Context7 و سورس محلی تطبیق داده شدند. بررسی‌های کوتاه این تغییر جای پذیرش بصری و چرخهٔ واقعی ویرایشگر Elementor را نمی‌گیرند.

بررسی همین تغییر: Build، ۳۵ تست کوتاه Node، lint دو فایل PHP روی PHP 8.4.26 و قرارداد PHP کامپوننت پاس شدند. قرارداد قدیمی شمارش ثابت دو stylesheet با فهرست واقعی ماژول‌ها هماهنگ شد؛ ثبت assets همچنان هیچ CSS را enqueue نمی‌کند. تست طولانی، محیط DB و بررسی مرورگر دوباره اجرا نشدند.

## چرخه و مرز ادعا

در Components 0.1.5، lightbox کامپوننت Image از helper مشترک swipe تصویر با گالری Theme مصرف می‌کند؛ منطق دوم و dependency تازه ساخته نشده است. helper فقط با بازکردن lightbox بارگذاری/متصل و هنگام بسته‌شدن آزاد می‌شود. جهت RTL/LTR، جلوگیری از ناوبری هنگام pinch/حرکت عمودی و شواهد محدود در [گالری محتوا](content-modules.md) ثبت است. این تغییر افزودن zoom سفارشی یا اسلایدر عمومی نیست.

DOM اولیه، root تازهٔ المنتور و teardown چرخه‌های متفاوت دارند. scan یک‌بارهٔ DOM به‌تنهایی پشتیبانی از preview یا محتوای بعداً درج‌شده نیست. instanceها، event listenerها و GSAP context باید مالک و cleanup روشن داشته باشند. در reduced motion محتوا خوانا و رفتار کم‌حرکت باشد. وجود GSAP در افزونهٔ ثالث، مجوز فرض سازگاری نسخه/اشتراک با bundle آن نیست؛ از globals آن بدون قرارداد استفاده نشود.

CSS در root کامپوننت scope شده و اجرای script/CSS آزاد در تنظیمات نداریم. renderer و قواعد فونت/جهت برای PHP و ویجت مشترک‌اند. SplitText یا کتابخانهٔ splitter اضافه نشده است. advanced rich text، تصویر داخل متن و تمام پارامترهای Hub جزو نسخهٔ پایه نیستند.

بررسی آینده کوتاه و مرتبط باشد: ذخیره/رندر معتبر و نامعتبر، دو نمونهٔ مستقل، نبود asset در صفحهٔ بدون مصرف، dependency مشترک، initialize/destroy و نمونهٔ فارسی/انگلیسی. مرورگر، فونت و لمس با تست استاتیک تأیید نشوند؛ اجرای طولانی فقط با دلیل و تأیید مالک.

## CSS اختصاصی هر عنصر — پیاده‌سازی سورس، ۲۰۲۶-۱۰-۲۵

علاوه بر CSS عمومی تنظیمات، container و ویجت فیلد `rdct_custom_css` دارند. Classic از CODE بومی با زبان CSS و Ace محلی استفاده می‌کند؛ اگر کنترل واقعی `custom_css` در Pro ثبت شده باشد فیلد تازهٔ رادمان اضافه نمی‌شود. تنها استثنا، سند دارای CSS قبلی رادمان است که با عنوان «CSS ذخیره‌شدهٔ رادمان» قابل ویرایش/پاک‌کردن می‌ماند تا نصب Pro آن را غیرقابل‌دسترس نکند. Atomic 4.3.5 کنترل CODE ندارد و از Textarea_Control بومی و String Prop استفاده می‌کند؛ این استثنای صریح قابلیتِ ادیتور است، نه کنترل React سفارشی. پنل کد فقط برای `unfiltered_html` نمایش داده می‌شود؛ خروجی عمومی به قابلیت بازدیدکننده وابسته نیست.

CSS در hook رسمی `elementor/element/parse_css` به stylesheet/cache همان سند اضافه می‌شود و frontend هیچ JS تازه برای آن نمی‌گیرد. wrapper عنصر class مستقل بر پایهٔ ID و hash متن CSS دارد؛ `selector` به `&` بومی CSS nesting تبدیل می‌شود، انتخابگر معمولی به فرزند همان wrapper محدود است و comment/رشته/نام کلاس `.selector` دست‌نخورده می‌مانند. حالت hover، pseudo-element و `@media/@supports/@container/@starting-style` پشتیبانی می‌شوند. این قابلیت از CSS nesting مرورگر استفاده می‌کند؛ parser/transpiler Sass یا تضمین اجرای مرورگر قدیمی نیست. CSS عمدی برای هدف خارج از root، مانند sibling combinator روی `selector`، قرارداد این فیلد نیست؛ این فیلد sandbox کد نامطمئن محسوب نمی‌شود.

حداکثر متن ۱۶ کیلوبایت UTF-8 است؛ ساختار نامتوازن، شکست style/script، import، font-face و keyframes در این فیلد رد می‌شوند. font/keyframes متعلق به استایل سایت/Child هستند. URL معمولی فقط مسیر محلی نسبی/ریشه‌ای می‌پذیرد؛ remote/protocol-relative و image-set/src در این فیلد پذیرفته نیستند. مقدار نامعتبر CSS خروجی نمی‌دهد و دادهٔ ذخیره‌شده خودکار بازنویسی نمی‌شود. با نصب بعدی Pro، CSS قبلی رادمان همچنان قابل ویرایش و رندر است؛ به فیلد Pro کپی نمی‌شود. پس از پاک‌کردن مقدار قدیمی و بارگذاری دوبارهٔ editor، بخش موقت CSS قبلی حذف می‌شود؛ مهاجرت خودکار نداریم.

پیش‌نمایش فقط داخل iframe ادیتور از ماژول جدا استفاده می‌کند: اشتراک تغییر همان setting، style محلی همان عنصر، حذف class قبلی برای بی‌اثرکردن CSS کش‌شده و cleanup روی حذف/تعویض عنصر و pagehide. برای هر کلید درخواست AJAX/REST یا رندر عمومی اضافه نمی‌شود. مرجع مستقیم Hub همان `hooks/section-controls.php` است؛ framework مرجع وارد رادمان نشد. قرارداد PHP و fixture مشترک با JS، کنترل‌های native Classic/Atomic، خروجی مستقل عناصر، متن فارسی، پیش‌نمایش/پاک‌کردن/cleanup و URL محلی را پوشش می‌دهند؛ مرورگر، ذخیره/Undo/کش واقعی و ظاهر دو ادیتور هنوز پذیرش نشده‌اند. [hook رسمی](https://developers.elementor.com/docs/hooks/parse-element-css/)، [CSS nesting](https://developer.mozilla.org/en-US/docs/Web/CSS/Guides/Nesting/Using).

## منابع API

- [وابستگی رسمی ویجت Elementor](https://developers.elementor.com/docs/widgets/widget-dependencies/): handle باید ثبت شده باشد؛ ویجت وابستگی خود را اعلام می‌کند، نه enqueue سراسری همهٔ ماژول‌ها.
- [چرخهٔ آماده‌شدن ویجت](https://developers.elementor.com/docs/hooks/js/) و [handlerهای کنترل](https://developers.elementor.com/docs/editor-controls/frontend-available/).
- [GSAP context](https://gsap.com/docs/v3/GSAP/gsap.context()/) و [matchMedia](https://gsap.com/docs/v3/GSAP/gsap.matchMedia()/): scope، revert و واکنش به کاهش حرکت.

مراجع با Context7 و سورس نسخهٔ ارسالی بررسی شدند. Build و ۱۸ بررسی کوتاه Node پاس شدند؛ قرارداد PHP جدایی assets، SSR فارسی/ZWNJ، ورودی نامعتبر، label آیکون، CSS دیرهنگام و static fallback را پوشش داد.

`tools/qa-elementor.ps1 -ComponentsOnly` با WordPress 7.1.3/PHP 8.4.26، Elementor 4.3.5 و Pro 4.3.2 در محیط موقت بدون شبکهٔ بیرونی پاس شد: Theme تنها، فونت و CSS در head، نبود preload اجباری، ثبت/رندر سند واقعی دو ویجت و dependencyهای آن‌ها. fixture warning مربوط به SERVER_NAME داشت؛ مقدار آن اضافه شد و محیط کامل صرفاً برای این اصلاح دوباره اجرا نشد. deprecationهای nullable قبلاً شناخته‌شدهٔ Elementor/Pro دوباره دیده و پنهان/اصلاح نشدند.

این بررسی PHP/DB و mock، اجرای انیمیشن و handler در مرورگر، کیفیت بصری RTL/فونت، لمس، editor live lifecycle، WPML، کش افزونه‌ها یا سرعت واقعی را اثبات نمی‌کند.
