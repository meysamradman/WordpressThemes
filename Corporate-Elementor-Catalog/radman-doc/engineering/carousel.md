# کاروسل مشترک

بررسی محدود ۲۰۲۶-۱۰-۳۰: قراردادهای generic/responsive کاروسل و ارتفاع بومی/وراثت/عرض متغیر پاس شدند؛ مدل کتابخانه در هر دو Classic و Atomic در WordPress مشترک ذخیره و بازخوانی/رندر شد. نقص تازهٔ مشخصی در این کنترل‌ها در دامنهٔ بررسی پیدا نشد؛ قابلیت پیشرفتهٔ تازه و selector آزاد اضافه نشد. درج UI/Undo به‌دلیل Loading ادیتور همچنان پذیرش نشده‌اند؛ [شواهد و مرز](elementor.md).

## همگام‌سازی پیوسته — ۲۰۲۶-۱۰-۲۷

کنترل مشترک `sync_mode=index|progress` به PHP و Classic/Atomic اضافه شد؛ index پیش‌فرض و رفتار داده‌های قبلی است. progress فاصلهٔ طی‌شدهٔ کل مسیر ۰ تا ۱ را با API عمومی setProgress و رویدادهای setTranslate/setTransition نسخهٔ محلی Swiper 14.3.0 منتقل می‌کند؛ این حالت تطابق پیکسلیِ مسیر دو کاروسل با عرض متفاوت نیست. حالت هر controller مبنای انتشار همان تراکنش در زنجیره است؛ چرخه/ID مبهم، مقصد دیررس، بازسازی و cleanup همان موتور قبلی را دارند. overscroll محدود و سرعت صفر reduced-motion مقصد حفظ است. Controller vendor یا CSS جدید نداریم؛ sync فقط با ID/target مصرف‌شده import می‌شود.

مرورگر واقعی محلی، جابه‌جایی هم‌زمان دو نمونه در LTR دسکتاپ و RTL موبایل ۳۹۰ پیکسل را تأیید کرد؛ خروج افقی صفحه صفر بود. آزمون‌های رویداد شامل progress میانی drag، انتقال duration، چرخه، ورودی نامعتبر، مقصد دیررس و cleanup هستند. تست لمس فیزیکی یا drag واقعی با ماوس اجرا نشده و از آزمون رویداد نتیجه‌گیری نمی‌شود. ذخیره/بازخوانی و خروجی واقعی Elementor و WPML محلی پاس شدند؛ ATE آنلاین و Undo ادیتور هنوز جدا هستند.

## کنترل‌های قابل ویرایش نظرات — ۲۰۲۶-۱۰-۲۶

نظر ثابت و کاروسلی اکنون تنظیم مشترک responsive برای اندازهٔ متن/نام/سمت، ارتفاع خط نظر، وزن متن/نام/سمت، اندازهٔ avatar، عرض/ارتفاع لوگو، اندازهٔ امتیاز و نشان quote دارند. حالت unset به همان preset ارث می‌برد؛ صفر و اعشار معتبرند و overrideهای تبلت/موبایل حفظ می‌شوند. اندازه‌ها در این قرارداد px و ارتفاع خط بدون واحد است؛ وزن از enum محدود انتخاب می‌شود. فونت خانواده‌ای جدید تحمیل نمی‌شود و همچنان از سایت/والد می‌آید. avatar-size برای تصویر کوچک نویسنده و portrait کوچک s04 است؛ تصویر تمام‌قد s07/s16 تابع ستون همان چیدمان می‌ماند.

metadata ظاهری در `testimonial/appearance.php` و تولید CSS با Presentation مشترک است. Classic از Slider/Select responsive بومی و Atomic از همان ترکیب بومی ResponsiveControl/Number/Toggle پروژه استفاده می‌کند؛ schema/registry آن برای testimonial و carousel-testimonials وصل است. گروه‌بندی کاروسل پسوند `_responsive` را برای تطبیق با نام پایه می‌خواند تا کنترل از پنل حذف نشود. مسیر direct در resolver، payload بومی Slider/Select Classic و object دستگاهی Atomic/PHP را یکسان می‌خواند. enum رشته‌ای با کلید عددی نیز رشته می‌ماند؛ وزن ۷۰۰ به‌علت مقایسهٔ strict نامعتبر نمی‌شود.

رنگ مستقل سمت، منبع، حاشیه، تأکید و نشان quote اضافه شد. `source_icon` فقط انتخاب برندهای موجود registry محلی است؛ SVG تنها هنگام انتخاب رندر می‌شود، font/CDN/درخواست شبکه ندارد و با URL/نام دلخواه ساخته نمی‌شود. در منبع بدون برچسب، نام برند محلی متن قابل دسترسی است؛ خود SVG تزئینی و aria-hidden است. source link همان اعتبارسنجی قبلی را حفظ می‌کند. فیلدهای متنی WPML تغییر نکرده‌اند؛ آیکون دادهٔ فنی است.

کاروسل stylesheet واکنش‌گرای مشترک نظرات را با collector یک‌بار تولید می‌کند؛ برای ۵۰ نظر ۵۰ style یکسان نمی‌نویسد. نظر ثابت همچنان JS نمی‌گیرد و هر preset فقط CSS خودش را دارد. Build، ۳۲ بررسی JS/asset/library و قراردادهای PHP profileها، direct Classic/Atomic، dedup CSS، آیکون محلی/برچسب امن و کنترل/schema واقعی Atomic پاس شدند. پذیرش ادیتور واقعی، ذخیره/Undo و WPML زنده همچنان باز است؛ همهٔ جزئیات واحدهای آزاد/فونت مستقل Group Typography هاب ادعا نشده است.

## تکمیل چیدمان‌های نظر مشتری — ۲۰۲۶-۱۰-۲۶

۱۹ preset جدید `s01` تا `s19` با بازخوانی `themes/hub-elementor-addons/elementor/params/testimonials.php` و class/styleهای همان مرجع اضافه شد؛ شش شناسهٔ قبلی card/bubble/centered/split/outline/minimal برای داده‌های موجود حفظ‌اند. اکنون ۲۵ گزینهٔ معتبر وجود دارد و کتابخانه برای نظر ثابت و کاروسلی مدل‌های متناظر را از registry مشترک می‌سازد. renderer مستقل `testimonial/presets.php` ترتیب/بسته‌بندی quote، author، rating، logo و portrait را از قطعات امن مشترک می‌سازد؛ ۱۹ موتور یا فایل PHP خالی ساخته نشد. Classic/Atomic همان metadata/enum و PHP renderer را مصرف می‌کنند.

| preset | الگوی ساختاری مرجع |
|---|---|
| s01 / s08 / s14 | نویسنده در بالا، با اندازه/فاصلهٔ معمولی، فشرده یا بزرگ |
| s02 / s15 / s17 | حباب کامل، نقل‌قول درشت بدون avatar، یا حباب مرکزی با نویسنده در پایین |
| s03 / s09 | کارت با جداکننده، یا نویسندهٔ کادردار در بالا |
| s04 / s07 / s16 | تصویر کوچک کنار نظر، portrait با ستون ۴۰٪، یا portrait با ستون ۵۰٪ و لوگو |
| s05 / s19 | لوگو در بالا؛ مدل دوم نویسندهٔ هم‌ردیف و امتیاز نشان‌دار دارد و avatar ندارد |
| s06 / s10 / s11 | نقل‌قول مرکزی با فضای باز، نشان quote، یا امتیاز در بالا |
| s12 / s13 / s18 | نشان quote در ابتدا، نقل‌قول تحریریه‌ای درشت، یا نوار تأکید و لوگو |

CSS پایه فقط قطعات مشترک را دارد؛ هر preset فایل مستقل `testimonial/sXX.css` می‌گیرد. نظر ثابت JS ندارد؛ کاروسل همان موتور حرکت قبلی را دارد. ستون‌های portrait در موبایل عمودی و در تصویر ناموجود تک‌ستونی‌اند؛ تصویر absolute در قاب مانع تحمیل ارتفاع ذاتی عکس به کارت می‌شود. خصوصیات جهت‌دار logical هستند. هم‌ترازی Auto از چیدمان می‌آید و انتخاب صریح Start/Center/End اولویت دارد. نگاشت WPML نیاز به کلید متنی تازه ندارد؛ کلیدهای متن/رسانهٔ موجود حفظ شده‌اند.

Build، ۳۲ بررسی کوتاه JS/asset/library، قرارداد PHP تمام presetها و profileها و schema/control بومی Atomic پاس شدند. بررسی منفی ثابت می‌کند فقط پایه و CSS سبک انتخابی مصرف می‌شوند؛ مستنداتِ register با دانلود همهٔ سبک‌ها یکی نیست. این چیدمان‌ها بازپیاده‌سازی ساختاری‌اند و ادعای تطابق پیکسلی، همهٔ کنترل‌های typography/social هاب، ذخیره/Undo یا پذیرش تصویری نصب واقعی ندارند. سورس/framework/تصاویر هاب وارد بسته نشدند؛ quote تزئینی متن محلی است.

## سطح و سایهٔ دکمهٔ فلش — ۲۰۲۶-۱۰-۲۶

براساس `carousel.php` هاب، فیلدهای navfill/navshape/navshadow و Normal/Hover رنگ پس‌زمینه دوباره بررسی شدند. رادمان `arrow_fill=default|outline|solid|none` و `arrow_shadow=none|normal|hover` و رنگ مستقل background/border در Normal/Hover را به metadata مشترک PHP/Classic/Atomic و تمام profileهای مرتبط اضافه کرد. نوع shape قبلی circle/square/minimal و آیکون/موقعیت مستقل حفظ است؛ حالت default ظاهر قبلی، از جمله سطح overlay، را نگه می‌دارد. کنترل‌های جدید فقط با فلش فعال و خارج از Marquee ظاهر می‌شوند؛ hover برای focus-visible نیز اعمال می‌شود و دکمهٔ disabled رفتار هاور فعال نمی‌گیرد.

`carousel/navigation-surface.css` و `carousel/navigation-shadow.css` فایل‌های مستقل و مشروط‌اند؛ تنظیم پیش‌فرض، فلش خاموش و Marquee آن‌ها را نمی‌گیرند. موتور/JS/افکت انیمیشنی تازه ساخته نشد. رنگ‌ها همان sanitizer محدود قبلی را دارند؛ CSS یا URL آزاد وارد خروجی نمی‌شود. Build، قراردادهای SSR/دارایی کاروسل و profileها و native Atomic و ۲۵ بررسی JS/graph پاس شدند. تطبیق پیکسلی تمام سبک‌های Hub و پذیرش نصب واقعی همچنان باز است؛ این ادامه صرفاً گزینهٔ تزئینی بدون اجرا نیست.

## ماژول‌های انتخابی موتور — ادامهٔ سورس ۲۰۲۶-۱۰-۲۶

Autoplay/Navigation/Pagination دیگر import اجباری `carousel/engine.js` نیستند. `carousel/modules.js` از گزینه‌های معتبر موجود، فقط ماژول‌های انتخاب‌شده را پیش از constructor می‌آورد؛ entryهای کوچک `carousel/modules/{autoplay,navigation,pagination}.js` مرز chunk مستقل‌اند، نه موتور یا UI تکراری. Core و A11y مشترک می‌مانند. Fade و بازسازی Stack همین مجموعهٔ انتخابی را به موتور می‌دهند؛ cleanup حین دریافت مانع mount دیررس است. Marquee تنظیمات خودش در `data-rdct-marquee` را حفظ می‌کند و این loader یا Swiper را نمی‌گیرد.

API modules با مستندات Context7 و constructor نسخهٔ نصب‌شدهٔ Swiper 14.3.0 تطبیق داده شد. build و ۳۹ بررسی کوتاه رفتار/اتصال/انتخاب ماژول و graph موفق‌اند؛ graph استاتیک موتور و dispatcher فاقد این سه entry است و هر entry، دو قابلیت دیگر را وارد نمی‌کند. این شواهد اندازه‌گیری سرعت صفحه یا پذیرش واقعی لمس/ذخیره/ظاهر نیستند؛ موارد باز قبلی محفوظ‌اند. نسخه/ZIP تغییر نکردند.

## تفکیک بر اساس کاربرد — CONFIRMED، ۲۰۲۶-۱۰-۲۵

[تصمیم مالک](../scenarios/components.md) با registry داخلی `inc/components/carousel/profiles.php` پیاده شد: کارت، لوگو، اسلایدر بزرگ، مارکوئی، کارت لایه‌ای و نظرات مشتریان. helperهای PHP مستقل `rdct_card_carousel`، `rdct_logo_carousel`، `rdct_slider`، `rdct_marquee`، `rdct_stacked_cards` و `rdct_testimonials` همان renderer و موتور حرکت موجود را مصرف می‌کنند. Classic/Atomic برای هر کاربرد ID مستقل و کنترل مرتبط دارند؛ mode/variant ثابت دوباره در رابط انتخاب نمی‌شوند. schema و Repeater مشترک‌اند؛ فهرست Template و metadata پایه برای هر کاربرد تکثیر نمی‌شود.

IDهای `rdct-carousel` و `rdct-atomic-carousel` و حالت‌های ذخیره‌شدهٔ قبلی معتبر مانده‌اند و فقط از درج جدید/جستجوی پنل پنهان‌اند؛ مهاجرت خودکار نداریم. کتابخانه نمونه‌ها را با ID کاربرد مربوط و مدل native درج می‌کند؛ شناسهٔ نمونه‌های قبلی محفوظ است. فلش، sync، کنترل بیرونی، Template، انتخاب گروهی، ارتفاع و breakpoint از موتور مشترک می‌آیند. عرض متغیر آیتم برای slider/stack/marquee و شرح متن برای لوگو از کنترل‌های آن کاربرد حذف شده‌اند. Hero فقط CSS caption و پوشش تصویر اضافه می‌کند؛ selector تصویر/لوگو فقط رسانهٔ خود کاروسل را هدف می‌گیرد و ابعاد تصویر Template دلخواه را تغییر نمی‌دهد. Marquee همچنان Swiper نمی‌گیرد. [فولدر و بارگذاری](assets.md).

### نظرات مشتریان

در سورس ۲۰۲۶-۱۰-۲۶، نظر ثابت نیز ویجت مستقل `rdct-testimonial` و `rdct-atomic-testimonial` دارد؛ پنل محتوا و ظاهر جداست و شش نمونهٔ ثابت `review-*` در کتابخانه، از نمونه‌های کاروسلی مستقل‌اند. Classic در حضور نسخهٔ Atomic از درج جدید پنهان می‌شود. کنترل تصویر/لوگو در هر دو بومی است؛ Atomic مقدار Image و اندازهٔ بومی را بدون تبدیل زودهنگام به ID به renderer مشترک می‌دهد. پیوند منبع با URL بومی Classic و Link بومی Atomic تنظیم می‌شود؛ renderer رشتهٔ PHP قدیمی و مقادیر حل‌شدهٔ هر دو کنترل را می‌پذیرد و پیوند پنجرهٔ تازه `noopener noreferrer` دارد.

نظر ثابت فقط CSS پایهٔ Testimonial و فایل سبک منتخب را می‌گیرد؛ حتی prototype ویجت JS فرانت‌اند اعلام نمی‌کند. پیش‌نمایش Atomic از مسیر احراز هویت‌شدهٔ مشترک PHP استفاده می‌کند و initializer حرکت ندارد. نگاشت WPML برای متن و مسیر بومی پیوند اضافه شد. ساخت، ۳۶ بررسی کوتاه JS/asset، قرارداد schema/کنترل واقعی Atomic، نمونه‌های دو زبان، دارایی انتخابی و renderer با توابع WordPress شبیه‌سازی‌شده بررسی شدند؛ نصب، ظاهر، ذخیره/Undo و ATE واقعی هنوز پذیرش نشده‌اند.

`inc/components/testimonial/renderer.php` مالک نام، سمت/شرکت، متن قالب‌دار، تصویر شخص، لوگوی مشتری، امتیاز ۰–۵، تاریخ، منبع و پیوند است. رسانه فقط پیوست معتبر با نگاشت WPML است؛ امتیاز صفر ستاره ندارد و نمونه‌ها متن راهنمای جایگزینی‌اند. `rdct_testimonial` نظر ثابت را بدون موتور حرکت رندر می‌کند؛ `rdct_testimonials` همین محتوا را به اسلایدهای موتور مشترک می‌دهد. خود renderer نظر به Swiper/GSAP وابسته نیست.

شش چیدمان اولیه card/bubble/centered/split/outline/minimal محفوظ‌اند؛ ۱۹ چیدمان مرجع‌محور طبق بخش ابتدای سند به آن‌ها اضافه شد. کنترل‌های typography/شبکهٔ اجتماعی هاب و برابری پیکسلی هنوز کامل تأیید نشده‌اند؛ کد آماده و پذیرش دیداری جدا هستند. mapping متن widgetها در XML WPML موجود است؛ چرخهٔ واقعی ATE/Media تأیید نشده است.

## دکمه‌های بیرونی — ۲۰۲۶-۱۰-۲۵

دو فیلد Text بومی `external_previous_class` و `external_next_class` در بخش «دکمه‌های بیرونی» PHP/Classic/Atomic اضافه شدند. هر فیلد یک کلاس یکتا با حرف آغازین و حداکثر ۶۴ نویسه می‌گیرد؛ نقطهٔ اول اختیاری در سرور حذف می‌شود، selector ترکیبی/آزاد پذیرفته نیست. نمونه: کلاس hero-prev و hero-next را در تنظیمات کاروسل وارد کنید و همین کلاس‌ها را در Advanced → CSS Classes ویجت دکمهٔ Elementor بگذارید. در HTML، کلاس روی button یا a یا wrapper آن قرار می‌گیرد. دکمهٔ هدف نیازی به لینک مقصد ندارد. فلش‌های داخلی می‌توانند خاموش باشند؛ این اتصال به آن‌ها وابسته نیست. Marquee این دو فیلد را مصرف نمی‌کند.

ماژول carousel-external از slidePrev/slideNext عمومی همان نمونهٔ Swiper استفاده می‌کند؛ RTL، گروه‌بندی، rewind و سرعت reduced motion به موتور موجود واگذار می‌شوند. سیاست توقف پس از تعامل همان کاروسل است و slideChange همچنان همگام‌سازی موجود را فعال می‌کند. سند/iframe مستقل، کلاس مبهم میان دو کاروسل بدون مقصد انتخابی و با aria-disabled است؛ disabled اولیهٔ خود دکمه حفظ می‌شود. Enter بومی anchor/button دوباره اجرا نمی‌شود؛ Space برای anchor/role button و Enter برای role button پشتیبانی می‌شود.

listenerهای مشترک هر document و observer فقط هنگام مصرف این قابلیت ساخته می‌شوند؛ مشاهده به childList و تغییر class محدود و تغییر نامرتبط class بدون query دوباره رد می‌شود. دکمهٔ دیررس، حذف/تعویض کلاس، lock/resize و بازسازی Stack پوشش دارند. aria-disabled قبلی هنگام آزادشدن اتصال برمی‌گردد؛ HTML دکمه و استایل آن جابه‌جا نمی‌شوند. مرجع Hub navappend/custom_id بررسی شد؛ Hub فلش تولیدی را در محل دیگر قرار می‌دهد، رادمان دکمهٔ موجودِ طراحی مشتری را متصل می‌کند. این قابلیت معادل sync پیکسلی یا جابه‌جایی DOM کنترل‌های Hub نیست.

ساخت، ۲۲ بررسی کوتاه کاروسل/اتصال/همگام‌سازی، ۱۷ بررسی asset، قرارداد PHP و schema/کنترل Atomic پاس شدند. سند API از Context7 با سورس نصب‌شده مقایسه شد. پذیرش لمس/ظاهر/ذخیره/Undo و کارکرد نصب جدید همچنان باز است.

رزولوشن ImageControl داخل اسلایدها در ۲۰۲۶-۱۰-۲۵ به اندازه‌های بومی وردپرس و رندر همان اندازه متصل شد؛ مقدار عددی قدیمی پیش‌فرض variant را حفظ می‌کند. [قرارداد مشترک تصویر و حدود پذیرش](assets.md).

## ایرادهای ادیتور واقعی — ۲۰۲۶-۱۰-۲۵

روی صفحهٔ مالک، بازکردن آیتم Repeater خطای Parent prop type is not supported و توقف پنل را بازتولید کرد. schema چاپ‌شدهٔ خود ادیتور برای item_prop_type از نوع union است؛ آداپتور اکنون شاخهٔ object با کلید itemType را برای PropProvider آیتم انتخاب می‌کند. context ادیتور نیز به InlineEditingControl داخل آیتم منتقل می‌شود. این اصلاح مشترک Repeaterهای رادمان است و دادهٔ ذخیره‌شده و شناسه‌ها را مهاجرت نمی‌دهد.

عرض کاروسل در کانتینر Atomic با outer display:contents به بیش از صد هزار پیکسل می‌رسید و با resize دوباره رشد می‌کرد؛ wrapper میانی flex-item با min-width:auto بود. wrapper PHP و Twig کاروسل و Box اکنون rdct-atomic-surface دارد؛ CSS پایهٔ کم‌اولویت inline-size:100%، min-inline-size:0 و max-inline-size:100% را روی همان سطح اعمال می‌کند. overflow کل صفحه مخفی نشده و تنظیم عرض بومی wrapper حفظ می‌شود.

ساخت، ۴۳ بررسی کوتاه JS/asset، قرارداد PHP کاروسل و lint مسیرهای تغییر پاس شدند. آزمون native PropKeyProvider از سورس ارسالی، شکست union خام و پذیرش object حل‌شده را بررسی می‌کند. نصب و بازکردن مجدد کنترل‌ها، لمس/RTL/Undo و نبود رشد عرض پس از اصلاح روی میزبان هنوز تأیید نشده‌اند.

## همگام‌سازی چند کاروسل — ۲۰۲۶-۱۰-۲۵

در PHP/Classic/Atomic، `controller_id` نام یکتای اختیاری و `controlled_ids` فهرست حداکثر ۱۶ مقصد با ویرگول است. نام با حرف انگلیسی آغاز می‌شود و حداکثر ۶۴ حرف/عدد/خط تیره/زیرخط دارد؛ مقصد با یا بدون # پذیرفته و تکرارها حذف می‌شوند. کنترل‌ها Text بومی در بخش مستقل Synchronization هستند؛ Marquee این قابلیت را ندارد. نمونه: تصویر با `controller_id=hero-images, controlled_ids=hero-text` و متن با `controller_id=hero-text`؛ برای کنترل دوطرفه، مقصد متن را نیز hero-images بگذارید. ترتیب اسلایدها باید متناظر باشد؛ برای پخش هماهنگ، autoplay فقط روی کنترل‌کننده روشن شود.

مرجع Hub `carousel_controller_id` و `controllingcarousels` در `elementor/widgets/carousel.php` بررسی شد. رادمان به‌جای selector آزاد/کلاس، فقط نام کاروسل‌های ثبت‌شده در همان document را هدف می‌گیرد؛ iframe ادیتور مستقل است و id تازه‌ای به DOM تحمیل نمی‌شود. `slideChange` و `slideTo(index,speed,true)` از API عمومی Swiper، طبق Context7 و declaration نسخهٔ محلی 14.3.0، مصرف می‌شوند؛ Controller vendor، موتور، CSS یا framework تازه نداریم. مقصد همان شمارهٔ اسلاید را با سرعت/جهت موتور خودش می‌گیرد؛ کمبود اسلاید به آخرین اسلاید موجود محدود و reduced motion مقصد حفظ می‌شود. این همگام‌سازی index است، نه progress پیوستهٔ drag.

ثبت مقصد دیررس و بازسازی Stack به نمونهٔ فعال دوباره وصل می‌شوند؛ چرخه/زنجیره با visited و transaction guard یک‌بار پیمایش می‌شود. نام تکراری مبهم انتخاب نمی‌شود؛ cleanup listener و ثبت نمونه را پیش از destroy آزاد می‌کند. پخش دستی/خودکار یا وضعیت توقف مقصد بازنویسی نمی‌شود. دکمهٔ مستقل با کلاس محدود ساخته شد؛ selector آزاد و تطابق پیکسلی ساخته نشده‌اند. حالت progress و پذیرش محدود واقعی طبق بخش جاری ابتدای سند تکمیل‌اند؛ لمس/drag فیزیکی و Undo خود ادیتور بازند.

**breakpoint — Components 0.1.31:** resolver مشترک Presentation مرز فعال mobile/tablet Elementor را با همان API ارسالی می‌خواند؛ برای Swiper/Marquee از max-width به min-width با ۱+ تبدیل می‌کند. تعداد ستون، ارتفاع، عرض متغیر و سرعت Marquee مرز یکسان دارند؛ CSS scoped فقط برای مرز متفاوت از پیش‌فرض تولید می‌شود. بدون Elementor، 768/1024 قبلی حفظ می‌شوند؛ ورودی نامعتبر/نامتناهی یا مرز معکوس fallback دارد. کنترل دستگاه اضافه نشده؛ laptop/tablet_extra و دستگاه‌های دیگر همچنان override مستقل ندارند. این اصلاح بر پایهٔ عرض viewport است؛ container query اضافه نشده است.

قرارداد کوتاه PHP/JS برای مرز سفارشی، لبهٔ دقیق، height/width، Marquee و fallback پاس شد؛ تغییر breakpoint در نصب واقعی، RTL/LTR و ذخیرهٔ Classic/Atomic هنوز پذیرش می‌خواهند.

## مالکیت و مرجع

موتور PHP: `radmanct-components/inc/components/carousel.php`؛ حرکت: `src/js/components/carousel/engine.js`؛ CSS: `src/css/components/carousel/`. PHP، Classic و Atomic همین موتور را مصرف می‌کنند. کاروسل ظرف نمایش است؛ query و دادهٔ محصول/وبلاگ/نمونه‌کار و طراحی هر کارت در مالک خود باقی می‌مانند.

سورس محدود Hub: `themes/hub-elementor-addons/elementor/widgets/carousel.php`، columns/spacing، `carousel_items_custom_height`، content_type/templates، navigation و pagination؛ موتور آن Flickity است. WoodMart: `inc/shortcodes/nested-carousel.php` و `brands.php` با Swiper. چارچوب/دارایی مرجع منتقل نشده است.

Swiper دقیقاً `14.3.0` از npm/lockfile و Vite محلی است؛ Core و Navigation/Pagination/Autoplay/A11y فقط با مصرف کاروسل وارد می‌شوند. CSS vendor هنگام build با `tools/carousel-css.mjs` به `.rdct-crs` محدود و مجوز در `licenses/swiper.txt` همراه افزونه است. نسخهٔ جهانی یا Swiper خود Elementor جایگزین نمی‌شود؛ CDN، jQuery، React عمومی یا GSAP اجباری ندارد.

## محتوا و کنترل‌ها

بارگذاری تصویرهای خود کاروسل از قرارداد مشترک `image_loading` پیروی می‌کند؛ eager اجباری اولین تصویر حذف و اسلاید آغازین autoplay معکوس لحاظ شد. کنترل انتخابی برای تصویر داخل Template جایگزین کنترل همان Template نیست؛ [مالک سیاست و پذیرش LCP](assets.md#آمادهسازی-php-و-رسانه).

### کارت‌های لایه‌ای — ادامهٔ سورس ۲۰۲۶-۱۰-۲۵

حالت `mode=stack` به همان Carousel افزوده شد؛ ویجت، query، renderer یا موتور دوم ندارد. مرجع محدود این بخش `themes/hub-elementor-addons/elementor/widgets/carousel-stack.php`، CSS `carousel-stack-base.css` و `theme.js::LiquidCarouselStack` است: محتوا/Template، یک کارت فعال، عمق سه لایه، خروج جانبی کارت، پخش/فلش و fallback موبایل بررسی شدند. در رادمان، محتوای همان Repeater/گالری/Template و helper PHP موجود مصرف می‌شود؛ تعداد در همهٔ دستگاه‌ها یک، gap صفر، گروه‌بندی و عرض متغیر خاموش و تراز آغاز است. انتخاب از Display mode بومی Classic/Atomic انجام می‌شود؛ شناسهٔ ویجت‌ها تغییر نکرد.

`carousel-stack.js` wrapper همان Swiper است. EffectCreative از همان Swiper قفل‌شده و chunk مستقل فقط در عرض تبلت به بالا و نبود reduced motion وارد می‌شود. CSS مستقل `stack.css` با همان scoper به رادمان محدود است. موبایل و reduced motion، اسلاید معمولی تک‌ستونهٔ همان موتور را می‌گیرند؛ تغییر preference/breakpoint طبق محدودیت beforeInit اثر Swiper، نمونهٔ قبلی را کامل destroy و بازسازی می‌کند. snapshot داخلی، index و قصد پخش کاربر را حفظ می‌کند؛ توقف دستی/فوکوس/لمس با این تغییر دوباره فعال نمی‌شود. نتیجهٔ import پس از حذف/cleanup اعمال نمی‌شود. بدون JS، همان HTML قابل پیمایش قبلی باقی می‌ماند. پیش‌نمایش کتابخانه نیز CSS را از `Carousel::assets()` مشترک می‌گیرد؛ فهرست موازی variantها برای آن وجود ندارد.

مقادیر فیزیکی هاب عیناً ادعا نمی‌شوند: مرجع از GSAP و Draggabilly و عمق‌های ۰/۸۰-/۱۸۰- استفاده می‌کند؛ رادمان از EffectCreative بومی با گام ۸۰px، perspective 1200 و مبدأ 50%/200% استفاده می‌کند. مسیر خروج با RTL معکوس می‌شود؛ تفاوت curve و عمق لایهٔ سوم و drag آستانه‌ای اختصاصی Hub همچنان تفاوت تطبیق‌اند. کلاس `is-stack` و overflow:clip از خروج افقی صفحه جلوگیری می‌کنند. تنها اسلاید فعال برای فوکوس و screen reader فعال می‌ماند؛ صفات اصلی و tabindex در cleanup بازگردانده می‌شوند. loop تکثیرکننده و React/jQuery/GSAP دوم اضافه نشده‌اند. مرجع API از Context7 و `effect-creative.mjs` نسخهٔ نصب‌شده بررسی شد.

دو رنگ فلش Normal و Hover/Focus همان تنظیمات مشترک ناوبری‌اند؛ Classic از Color بومی بدون alpha و Atomic از picker موجود استفاده می‌کند. مقدار خالی ارث‌بری است؛ فقط hex معتبر وارد CSS variable محلی فلش می‌شود. stylesheet مشترک navigation تنها با نیاز واقعی بارگذاری می‌شود؛ رنگ پیش‌فرض همهٔ کاروسل‌های قدیمی عوض نشده است. نمونهٔ `carousel-stack` در کتابخانهٔ Classic/Atomic با ارتفاع adaptive و duration=600 ثبت شد. پذیرش ظاهر، drag/لمس، ذخیره/Undo، محتوای Template چندارتفاع و Network واقعی هنوز باز است.

### انتخاب گروهی تصاویر — ادامهٔ سورس، ۲۰۲۶-۱۰-۲۴

الگوی Hub در `carousel.php` با `carousel_type=gallery` و `Controls_Manager::GALLERY` دوباره بررسی شد. Classic رادمان انتخاب مستقل «اسلایدهای سفارشی / گالری تصاویر» دارد و از GALLERY بومی استفاده می‌کند؛ تغییر منبع، اسلایدهای قبلی را پاک نمی‌کند. فقط ID پیوست مصرف و URL چسبانده‌شده در دادهٔ گالری نادیده گرفته می‌شود. helper مشترک `Carousel\gallery_items()` ترتیب، حذف ID تکراری و سقف ۵۰ ورودی را رعایت می‌کند؛ renderer موجود اعتبار تصویر و ارجاع رسانهٔ WPML را اعمال می‌کند. نمونهٔ PHP:

```php
rdct_carousel( \RadmanCTComponents\Components\Carousel\gallery_items( [12, 18, 25] ), ['variant' => 'images'] );
```

Atomic در Elementor ارسالی 4.3.5 کنترل GALLERY بومی ندارد؛ ImageControl آن فقط `multiple:false` دارد. به‌جای کنترل/آپلودکنندهٔ تازه، دکمهٔ Button بومی UI همان ویرایشگر، پنجرهٔ رسانهٔ استاندارد WordPress را با انتخاب چندتایی باز می‌کند و تصویرها را به همان Repeater بومی اضافه می‌کند. مدل ذخیرهٔ `items` و union تصویر/شناسه تغییر نکرده‌اند. نگاشت تصویر، ImageControl، ترتیب، حذف و تاریخچه همچنان متعلق به همان ویرایشگرند؛ این یک عملیات افزودن گروهی است، نه picker موازی هر تصویر. سقف با ظرفیت باقی‌مانده سنجیده و اسلایدهای موجود حفظ می‌شوند. در نبود API دکمه/رسانه، Repeater قبلی قابل استفاده می‌ماند.

`shared/admin/image-picker.js` مسیر واحد انتخاب رسانهٔ مدیریت است؛ حالت تک‌تصویر سازنده حفظ شده و multiple فقط با درخواست صریح فعال می‌شود. release و لغو idempotent هستند؛ callback پس از بسته‌شدن پنجره اثر ندارد. افزودن Atomic از آخرین rows/disabled استفاده می‌کند تا تغییر هم‌زمان را بازنویسی نکند؛ unmount پنجره را آزاد می‌کند. frontend فایل، framework، query یا renderer جدید ندارد؛ انتخاب گروهی به‌تنهایی Swiper/GSAP بیشتری بارگذاری نمی‌کند. مبنای API Classic [مستند رسمی Gallery](https://developers.elementor.com/docs/editor-controls/control-gallery/) از Context7 و سورس نسخهٔ محلی است. پذیرش واقعی پنجره، Undo/ذخیره و WPML رسانه هنوز باز است.

- هر اسلاید متن/رسانه/پیوند یا Template منتشرشدهٔ Elementor است؛ Template می‌تواند هر طراحی داشته باشد، نه فقط Content Box. انتشار، رمز و recursion از محافظ مشترک TemplateLibrary پیروی می‌کنند. PHP همچنین Closure محلی برای markup چاپ‌شده یا برگشتی می‌پذیرد؛ JSON به Closure تبدیل نمی‌شود. سقف ۵۰ اسلاید و بدون query خودکار.
- ارتفاع آیتم کاروسل مستقل از محتوای Template است: px/em/%/vh، auto و ارث‌بری دستگاه‌ها. Classic از Slider responsive و Atomic از Size_Control/Size_Prop_Type بومی هر دستگاه استفاده می‌کند؛ هم مقدار آرایه‌ای و هم خروجی resolveشده مثل `70vh` محدود و تبدیل می‌شوند. درصد به ارتفاع مشخص والد نیاز دارد؛ تنظیمات فرزندان Template بازنویسی نمی‌شوند و محتوای بلند قابل پیمایش است.
- تعداد نمایان اعشاری مثل ۳٫۲، فاصلهٔ اسلاید، duration و autoplay delay مستقل‌اند. `variant=content` برای کامپوننت موجود فقط CSS پایه دارد؛ cards/images/brands CSS جدا و انتخابی دارند. `Box template=cover` نمونهٔ کارت بلند با همان renderer باکس و CSS مستقل است؛ نوع اجباری اسلاید نیست.
- فلش با انتخاب آیکون محلی، نقطه، عدد و current/total و جای بالا/وسط/پایین در پایه حاضرند. جهت از صفحه می‌آید؛ prev/next منطقی و SVG در RTL mirror می‌شوند. کنترل‌ها به instance خودشان محدودند.
- بدون JS، HTML و پیمایش افقی باقی می‌مانند. loop تکثیرکننده خاموش و rewind فعال است؛ ID/فرم اسلاید کپی نمی‌شود. Autoplay پیش‌فرض خاموش، توقف روی تعامل/فوکوس و کنترل پخش دارد؛ reduced motion پخش را متوقف و duration را صفر می‌کند. teardown و preview Elementor cleanup دارند؛ درج AJAX دلخواه initializer صریح می‌خواهد.

پنل‌ها از کنترل‌های بومی استفاده می‌کنند؛ Repeater اتمیک ترکیب مشترک موجود Curtain است، بدون CSS پنل یا React دوم. گروه‌ها: اسلایدها، چیدمان، ارتفاع، کنترل حرکت و زمان‌بندی. نمونه‌های کتابخانه دو زبان و مدل Classic/Atomic دارند.

تکمیل ۲۰۲۶-۱۰-۲۳: حالت carousel یا slider تک‌اسلاید، چیدمان start/center، ارتفاع equal/natural/adaptive، drag، rewind اختیاری و حرکت گروهی موجودند. کلید ذخیره‌شدهٔ `position` حفظ شده؛ فلش‌ها و صفحه‌بندی جای مستقل بالا/وسط/پایین و تراز start/center/end دارند، فلش spread نیز دارد. حالت slider همهٔ تعدادها را ۱ و فاصله را صفر می‌کند؛ کنترل‌ها داخل frame قرار می‌گیرند تا `height=100vh` با کنترل‌های بیرونی بلندتر نشود. autoplay delay، hover pause، ادامه پس از swipe، جهت معکوس و توقف انتهایی مستقل‌اند؛ فوکوس برای دسترسی توقف می‌دهد و ازسرگیری صریح دارد. این قابلیت‌ها از فیلدهای همان carousel مرجع Hub تطبیق داده شدند؛ تغییر رفتار بی‌صدای سند قبلی نداریم.

Ken Burns انتخابی: none، zoom-in، zoom-out و زوم با مبدأ آغاز/پایان خواندن، duration و scale محدود. CSS `carousel/motion.css` فقط هنگام فعال‌بودن مصرف می‌شود؛ افکت فقط روی تصویر مستقیم `.rdct-crs__media > img` در اسلاید فعال است، نه کل Template و متن/فرم آن. تصویر عادی این wrapper را می‌گیرد؛ در Template دلخواه طراح باید لایهٔ تصویر را با همین class مشخص کند. reduced motion افکت را خاموش می‌کند. Ken Burns با گذار میان اسلایدها مستقل است.

## گذار انتخابی و چرخهٔ پخش — ۲۰۲۶-۱۰-۲۳

حالت `slider` کنترل بومی `transition=slide|fade` دارد؛ پیش‌فرض اسناد قبلی slide است. شرط native فیلد در Classic/Atomic از schema مشترک می‌آید. در carousel چندتایی و Marquee مقدار fade اعمال و فایل آن بارگذاری نمی‌شود؛ تنظیم ذخیره‌شدهٔ سند بازنویسی نمی‌شود. رفتار مرجع، گزینهٔ `fadeeffect` در carousel هاب است؛ پیاده‌سازی رادمان از EffectFade نسخهٔ قفل‌شدهٔ Swiper با crossFade استفاده می‌کند. Context7 با سورس محلی همان نسخه، `effect-fade.mjs` و `autoplay.mjs` تطبیق داده شد.

`carousel-fade.js` فقط برای slider/fade وارد می‌شود و ماژول افکت را به همان موتور می‌دهد؛ موتور دوم نداریم. CSS مستقل `carousel/fade.css` از vendor build و به scope رادمان محدود می‌شود؛ در graph استاتیک کاروسل معمولی حضور ندارد. fade برای Template دلخواه نیز کار می‌کند و DOM/فرم/ID را تکثیر نمی‌کند. اسلاید پنهان inert/aria-hidden می‌گیرد؛ پیش از پنهان‌کردن مقصد فوکوس، فوکوس به viewport منتقل می‌شود و cleanup صفات اولیه را بازمی‌گرداند. بدون JS، HTML قابل پیمایش قبلی باقی است؛ reduced motion مدت گذار را صفر می‌کند.

Autoplay با IntersectionObserver بیرون دید و هنگام document.hidden متوقف می‌شود؛ بازگشت فقط وقتی مجاز است که کاربر/تعامل/فوکوس/پایان اسلاید آن را متوقف نکرده باشد. hover اختیاری و انتخاب ادامه پس از swipe حفظ شدند. نبود IntersectionObserver مانع پیمایش/پخش عادی نیست؛ cleanup observer و listenerها را آزاد و callback دیررس را بی‌اثر می‌کند. این توقف موقت با توقف صریح کاربر یک حالت نیست. پذیرش واقعی مرورگر/لمس/کیبورد همچنان باز است؛ قراردادهای کوتاه فقط state/lifecycle و مرز منابع را بررسی می‌کنند.

## Marquee مشترک متن و کاروسل — ۲۰۲۶-۱۰-۲۳

مرجع Hub همان `carousel.php` با `marquee`، `marquee_speed` و `marquee_pauseautoplayonhover` است؛ `themes/hub/assets/js/theme.js` در `marqueeUpdate()` موقعیت Flickity را در RAF با سرعت هر فریم کم می‌کند و observer/hover/focus آن را متوقف می‌کنند. این موتور jQuery/Flickity کپی نشده است.

رادمان: `inc/components/marquee.php` و `src/js/components/marquee.js`، یک موتور برای `Text mode=marquee` و `Carousel mode=marquee`. سرعت به پیکسل بر ثانیه با حدود ۵–۵۰۰ است و به refresh rate وابسته نیست؛ تبلت/موبایل override و مقدار ۱- برای ارث‌بری دارند. فاصله، جهت start/end/left/right، توقف hover و محوشدن لبه‌ها مستقل‌اند. جهت حرکت از computed direction ریشه حل می‌شود؛ گروه متن RTL باقی می‌ماند و حروف/ترتیب متن معکوس نمی‌شوند. Text کارت یا ویجت تکراری ندارد؛ حالت از Effect و کاروسل از Display mode انتخاب می‌شود.

SSR فقط یک نسخه از محتوای اصلی می‌سازد. برای محتوای ثابت، پس از آماده‌شدن فونت و اندازه‌گیری viewport، نسخه‌های **نمایشی** محدود برای پرکردن عرض ساخته می‌شوند؛ inert/aria-hidden هستند، شناسه‌ها و ارجاع داخلی SVG namespace تازه می‌گیرند و script/metadata اجرایی تکرار نمی‌شود. سقف ۲۴ تکرار و ۴۰۹۶ گرهٔ نمایشی، مانع تکثیر بی‌حد است. فرم، لینک تعاملی، iframe، صوت/ویدئو، custom element یا کاروسل/Flip Box تو‌در‌تو به اسکرول دستی برمی‌گردند؛ marquee خودکار فعلی مخصوص متن، لوگو و Template/کارت **ثابت** است، نه clone تعاملی. چرخهٔ عادی carousel برای محتوای تعاملی حفظ شده است.

حرکت transform با Web Animations API خطی است؛ دکمهٔ پخش/توقف، hover اختیاری، فوکوس، visibility و IntersectionObserver پخش را کنترل می‌کنند. فوکوس محتوای اصلی، حرکت و نسخه‌ها را برمی‌گرداند تا مقصد فوکوس بیرون قاب نماند. reduced motion و نبود API لازم، HTML قابل پیمایش را نگه می‌دارند. ResizeObserver روی viewport/آیتم اصلی، بارگذاری تصویر و font-ready اندازه را تازه می‌کنند؛ بازسازی با RAF تجمیع می‌شود و CSS/DOM هر فریم در JS محاسبه نمی‌شوند. cleanup، انیمیشن/observer/رویداد/نسخه‌ها را آزاد می‌کند.

فقط `marquee.css` و variant انتخابی کاروسل مصرف می‌شوند؛ `carousel-entry.js` بین موتور اسلایدی و پیوسته انتخاب می‌کند، بنابراین Marquee به Swiper یا GSAP نیاز ندارد. PHP: `rdct_text_reveal( 'Featured works ©', ['mode'=>'marquee','marquee_speed'=>60,'marquee_direction'=>'start'] )` یا `rdct_carousel( $items, ['mode'=>'marquee','variant'=>'brands'] )`. API با [Element.animate](https://developer.mozilla.org/en-US/docs/Web/API/Element/animate) و [Animation.pause](https://developer.mozilla.org/en-US/docs/Web/API/Animation/pause) از Context7 تطبیق داده شد.

Build، قرارداد کوتاه SSR/دارایی/props واقعی Atomic و جهت/سرعت/cleanup بررسی شده‌اند؛ حرکت بی‌درز بصری، تغییر فونت/عرض واقعی، موبایل و ذخیره/Undo editor هنوز پذیرش نشده‌اند.

## ترجمهٔ اسلایدها

در ادامهٔ سورس ۲۰۲۶-۱۰-۲۵، `wpml-config.xml` افزونه برای Classic و Atomic برچسب دسترس‌پذیری، عنوان، محتوای HTML و لینک اسلاید را ثبت می‌کند. مسیر Atomic از envelopeهای واقعی `items>value` و `value>content>value` عبور می‌کند؛ نوع داده، شناسهٔ ردیف، تصویر و Template، اندازه‌ها و تنظیمات موشن ترجمهٔ متنی نمی‌شوند. ترجمهٔ ارجاع تصویر و Template همچنان با resolver مشترک موجود انجام می‌شود؛ hook یا درخواست frontend جدید اضافه نشده است. قرارداد کوتاه Python مسیرهای XML و حفظ دادهٔ فنی را شبیه‌سازی می‌کند؛ استخراج/بازگردانی واقعی WPML و ترجمهٔ رسانه هنوز پذیرش نشده‌اند.

## باقی‌مانده و شواهد

**Components 0.1.25 — عرض متغیر و ظاهر ناوبری:** `variable_width=true` فقط در حالت carousel به `slidesPerView=auto` همان Swiper می‌رود؛ شمار ستون‌ها پنهان می‌شود و حرکت گروهی، در صورت انتخاب، گروه خودکار آیتم‌های نمایان است. مرجع محدود Hub `columns_auto_width` و `navarrow` دوباره خوانده شد؛ API Context7 با سورس نصب‌شدهٔ Swiper 14.3.0، `updateSlides`، `slidesPerGroupAuto` و `onLoad` تطبیق داده شد. هر آیتم `width` صفر برای اندازهٔ محتوا یا ۱–۲۰۰۰ px دارد؛ `width_tablet`/`width_mobile` با مقدار خالی یا ۱- ارث می‌برند و صفر به اندازهٔ محتوا برمی‌گردد. پهنا از viewport بیشتر نمی‌شود؛ Template با عرض درصدی یا محتوای بلند ممکن است به عرض صریح نیاز داشته باشد. Classic کنترل دستگاه بومی NUMBER و Atomic سه NumberControl بومی در همان Repeater دارد؛ موتور PHP مستقل است. حالت slider همیشه یک اسلاید تمام‌عرض می‌ماند و Marquee مسیر خودش را دارد.

`carousel-width.css` فقط هنگام فعال‌بودن این حالت مصرف می‌شود؛ نسخهٔ بدون JS نیز عرض‌های آیتم و پیمایش دستی را حفظ می‌کند. ResizeObserver محدود به اسلایدهای همین نمونه و font-ready، اندازه‌گیری مجدد را در RAF تجمیع می‌کنند؛ رویداد load تصویر از Swiper است. cleanup، observer و RAF را آزاد و نتیجهٔ font-ready منقضی را نادیده می‌گیرد. DOM یا ID تکثیر نمی‌شود؛ محور حرکت و فلش از همان جهت موروثی موتور می‌آیند.

فلش‌ها `arrow_style=circle|square|minimal`، اندازهٔ دکمهٔ ۴۴–۱۲۰ px، آیکون ۸–۶۴ px و فاصلهٔ ۰–۶۴ px دارند. Classic بخش ظاهر در تب Style و Atomic بخش بومی هم‌نام را از schema مشترک می‌گیرند؛ اندازهٔ آیکون در SVG همان renderer اعمال می‌شود. فقط شکل/اندازه/فاصلهٔ غیراستاندارد فایل `carousel-navigation.css` را لازم دارند؛ پیش‌فرض قدیمی حفظ شده است. شکل فلش، جهت و جایگاه/تراز سه تنظیم مستقل‌اند؛ این گزینه‌ها معادل همهٔ طرح‌های اختصاصی Hub نیستند.

این پایه برابری کامل Hub نیست: stack طبق بخش بالا اضافه شده و تفاوت‌های curve/drag آن بازند؛ همگام‌سازی شمارهٔ اسلاید و دکمهٔ کنترل خارجی با کلاس محدود طبق بخش‌های بالای سند ساخته شدند. selector آزاد، تمام استایل‌های پیشرفتهٔ فلش و query خودکار ساخته نشده‌اند. انتخاب گروهی تصاویر در سورس اضافه شد؛ پذیرش واقعی آن باقی است. fade تک‌اسلاید پیاده شد؛ محوشدن گروه چندتایی معادل کامل Hub نیست. استخراج/بازگردانی تکرارها در WPML واقعی هم تأیید نشده است؛ اولویت بعدی از نمونهٔ مالک تعیین شود.

Build/قرارداد دارایی، lint PHP 8.4، schema/Size و مدل‌های بومی دو زبان، کتابخانه، قرارداد PHP محتوا/ارتفاع/حدود و دو بررسی JS با Swiper شبیه‌سازی‌شده اجرا شدند. لمس و drag واقعی، ظاهر ارتفاع و فلش RTL/LTR، ذخیره/Undo واقعی Elementor و عملکرد اندازه‌گیری‌شده هنوز پذیرش می‌خواهند.
