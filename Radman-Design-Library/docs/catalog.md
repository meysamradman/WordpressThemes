# فهرست طرح‌های مرجع

فهرست نمونه‌های مرجع؛ هر نمونه URL و دارایی مصرفی مستقل دارد. دستهٔ خالی صرفاً ظرفیت توسعه است. ثبت فعال طرح‌ها در `inc/samples.json`، قرارداد انتقال در [scenario.md](scenario.md) و نقشهٔ فایل‌ها در [module-map.json](module-map.json) است.

| طرح | خانواده | کاربرد / حرکت |
|---|---|---|
| [ذرات پیوندخوردهٔ تعاملی · Radman](http://127.0.0.1:8097/design/particles/) | particles | Canvas محلی مستقل؛ حلقهٔ آرام، خطوط، تعامل نشانگر، توقف دستی و چرخهٔ اجرای محدود |
| [دکمه با متن چرخشی و فلش مورب · Radman](http://127.0.0.1:8097/design/button-roll/) | button | همان دکمهٔ Webtalik؛ renderer و CSS مشترک موجود، hover و focus-visible، روشن/تیره؛ بدون JS |
| [جدول مشخصات با سرستون رنگی · Radman](http://127.0.0.1:8097/design/data-table/) | table | جدول بومی و مستقل؛ سرستون رنگی، ردیف‌های راه‌راه، caption و اسکرول افقی موبایل؛ بدون JS |
| [هیرو با تیتر بزرگ و تصاویر میان متن · Radman](http://127.0.0.1:8097/design/hero-typography/) | hero / hero-static | هیرو بدون اسلایدر؛ متن فارسی، تصاویر دایره‌ای میان تیتر، عکس عمودی و زمینهٔ روشن؛ ورود یک‌بارهٔ GSAP |
| [هیروی اسلایدری با پردهٔ جهت‌دار · Radman](http://127.0.0.1:8097/design/hero-slider/) | hero / hero-slider | GSAP + Pointer Events · drag، سوایپ و فلش؛ انتقال پرده‌ای، پارالاکس محدود و ورود هماهنگ متن؛ بدون autoplay |
| [هیرو با ورود متن و عبارت چرخشی · Radman](http://127.0.0.1:8097/design/home-hero/) | hero / hero-static | GSAP Core · ورود متن، چرخش عبارت و حرکت جزئیات تصویر؛ بدون تعویض اسلاید |
| [پنل‌های اسکرولی با پین · Radman](http://127.0.0.1:8097/design/home-services/) | panels | GSAP Core + ScrollTrigger + ScrollSmoother · پنل‌های پین‌شده و جزئیات گرافیکی |
| [گالری با متن استیکی و ورود تصاویر · Radman](http://127.0.0.1:8097/design/home-projects/) | gallery | GSAP Core + ScrollTrigger + ScrollSmoother · متن استیکی و ورود تصاویر |
| [عنوان صفحه با تصویر یا ویدئو · Radman](http://127.0.0.1:8097/design/services-cover/) | page-title | ScrollTrigger + ScrollSmoother · پارالاکس و زوم تصویر/ویدئو |
| [فهرست با پیش‌نمایش تصویر · Radman](http://127.0.0.1:8097/design/services-list/) | showcase | Vanilla JS + sticky مشترک · تغییر تصویر با انتخاب خدمت |
| [نوار پیوستهٔ تصاویر · Radman](http://127.0.0.1:8097/design/services-strip/) | gallery | GSAP Core + ScrollTrigger · حرکت افقی پیوسته و کشیدن |
| [آکاردئون شماره‌دار · Radman](http://127.0.0.1:8097/design/services-faq/) | accordion | CSS + details بومی · باز و بسته شدن؛ بدون GSAP |
| [مراحل شماره‌دار · Radman](http://127.0.0.1:8097/design/process-steps/) | process | GSAP · ورود تدریجی پنج مرحله و معرفی مسیر همکاری |
| [گالری پیوسته با تیتر · Radman](http://127.0.0.1:8097/design/project-marquee/) | gallery | GSAP · گالری پیوسته با سرعت واکنش‌پذیر به اسکرول و امکان توقف |
| [آرشیو نمونه‌کار · Radman](http://127.0.0.1:8097/design/portfolio-archive/) | portfolio | آرشیو بومی، بارگذاری بیشتر AJAX و پارالاکس GSAP |
| [جزئیات نمونه‌کار · Radman](http://127.0.0.1:8097/design/portfolio-single/) | portfolio | جزئیات، گالری، قبلی/بعدی و مشابه؛ اسکرول مشترک |
| [هدر و منوی کامل · Radman](http://127.0.0.1:8097/design/header-radman/) | header | هدر با منوی Roll و پنل Wave؛ GSAP |
| [فوتر کامل · Radman](http://127.0.0.1:8097/design/footer-radman/) | footer | فوتر داده‌محور، لوگوی CSS و بازگشت بالا |
| [کارت‌های افقی با اسکرول · Radman](http://127.0.0.1:8097/design/wellness-scroll/) | panels | GSAP ScrollTrigger · پین افقی با اسکرول عمودی؛ fallback لمسی بومی |
| [متن · ورود با شروع صفحه · Radman](http://127.0.0.1:8097/design/text-start/) | text | ورود اولیهٔ متن؛ همان پروفایل Hero قالب |
| [متن · ورود هنگام اسکرول · Radman](http://127.0.0.1:8097/design/text-scroll/) | text | ورود یک‌بارمصرف هنگام رسیدن متن به دید |
| [متن · پُرشدن با اسکرول · Radman](http://127.0.0.1:8097/design/text-fill/) | text | روشن‌شدن ترتیبی کلمه‌ها با اسکرول؛ بازهٔ متصل به متن و قابل تکمیل تا انتهای صفحه |
| [آرشیو وبلاگ · Radman](http://127.0.0.1:8097/design/blog-archive/) | blog | رندر بومی نوشته، تصویر و دسته‌بندی مستقل |
| [جزئیات وبلاگ · Radman](http://127.0.0.1:8097/design/blog-single/) | blog | رندر بومی نوشته، تصویر و دسته‌بندی مستقل |
| [آرشیو موسیقی · Radman](http://127.0.0.1:8097/design/music-archive/) | music | آهنگ، هنرمند، کاور، دسته‌بندی و فایل صوتی؛ مستقل از وبلاگ |
| [جزئیات موسیقی · Radman](http://127.0.0.1:8097/design/music-single/) | music | آهنگ، هنرمند، کاور، دسته‌بندی و فایل صوتی؛ مستقل از وبلاگ |
| [آرشیو کاتالوگ محصول · Radman](http://127.0.0.1:8097/design/catalog-archive/) | catalog | کاتالوگ بدون فروش، گالری و مشخصات؛ PHP و Vanilla مستقل از المنتور |
| [جزئیات محصول کاتالوگ · Radman](http://127.0.0.1:8097/design/catalog-single/) | catalog | کاتالوگ بدون فروش، گالری و مشخصات؛ PHP و Vanilla مستقل از المنتور |
| [آکاردئون تصویری · Radman](http://127.0.0.1:8097/design/image-accordion/) | accordion | بازشدن عرضی ۳ به ۱ با hover، بازگشت به کارت اول؛ CSS .4s و Vanilla؛ لمس و کیبورد |
| [آکاردئون تصویر و باکس متن · Radman](http://127.0.0.1:8097/design/card-accordion/) | accordion | بازشدن عرضی ۳ به ۱ با hover، بازگشت به کارت اول؛ CSS .4s و Vanilla؛ لمس و کیبورد |
| [گالری ستون‌های خلاف‌جهت · Radman](http://127.0.0.1:8097/design/column-gallery/) | gallery | سه ستون با سرعت‌های −۰٫۱ / ۰٫۸ / −۰٫۱؛ حرکت وابسته به اسکرول و زوم hover، بدون اسلایدر |
| [مراحل پنج‌ستونه با زمان‌بندی · Radman](http://127.0.0.1:8097/design/process-phases/) | process | ورود مرحله‌ها از کنار، خط زمانی، زمان‌بندی و خروجی هر مرحله |
| [بازشدن قاب تصویر با اسکرول · Radman](http://127.0.0.1:8097/design/media-stretch/) | gallery | قاب باریک با شعاع ۲۵۰ به تصویر تمام‌عرض؛ پین و scrub 1 |
| [ستون معرفی ثابت و کارت‌های تصویری · Radman](http://127.0.0.1:8097/design/sticky-cards/) | panels | معرفی ثابت در راست، کارت‌ها در چپ، خط و نقطهٔ ثابت همراه معرفی |
| [نوار متن متحرک و قاب تصویر · Radman](http://127.0.0.1:8097/design/text-marquee/) | text | نوار متن مشترک + قاب بازشونده و زوم عکس ۶۰ تا ۱۰۰ درصد با اسکرول |
| [نوار متن پیوسته با تغییر جهت · Radman](http://127.0.0.1:8097/design/text-loop/) | text | نوار متن مستقل؛ حرکت ۲۰ثانیه‌ای و تغییر جهت اسکرول، با توقف و reduced-motion |

فهرست فقط یک جستجوی طراحی و یک درخت دسته‌بندی دارد؛ AJAX حمل‌ونقل همین لینک‌هاست، دسته/فیلتر نمایشی جدا نیست.

محتوای مستقل برای بررسی آرشیو/جزئیات: ۱۰ نوشته، ۱۰ نمونه‌کار، ۳ آهنگ و ۸ محصول نمایشی با تصاویر موجود. فایل صوتی واقعی هنوز انتخاب نشده است.

کاتالوگ بدون فروش و منشأ کدها: [catalog-products.md](catalog-products.md). منابع طراحی: [reference-sections.md](reference-sections.md). دادهٔ نمایشی هنگام انتقال جایگزین شود.

| [ربات سه‌بعدی تعاملی · Radman](http://127.0.0.1:8097/design/robot/) | interactive-3d | مدل اولیهٔ مستقل GLB، Three.js، نگاه نرم سر، رندر درخواستی؛ [قرارداد انتقال](robot.md) |

| [ورود سه‌بعدی متن و حرکت نشانگر · Radman](http://127.0.0.1:8097/design/text-3d/) | text / text-3d | flip دووجهی با ورود به دید؛ پارالاکس اختیاری ماوس، اتصال درست حروف فارسی |

media-mosaic: independent gallery/section module, alternating text/media grid with three local WebP images and one native controlled video. Owns view.php, data.php and style.css; no JS or sibling-design dependency. rdl/media-mosaic/data supplies editorial copy and remapped local attachment IDs; fixtures/media-mosaic/PROVENANCE.md records selected demo media and layout reference. Mobile preserves each text/media pair; the video uses preload none and no autoplay.
