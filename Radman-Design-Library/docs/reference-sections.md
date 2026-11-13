# سکشن‌های مرجع مستقل

## جدول مشخصات — `data-table`

مرجع محلیِ خواندنی: `Corporate-Elementor-Catalog/radmanct-components/inc/components/table.php` و `src/css/components/table/base.css`. قرارداد انتخاب‌شده، جدول دادهٔ بومی با `columns` / `rows[].cells`، caption، سرستون و عنوان ردیف است. نسخهٔ کتابخانه یک طراحی مستقل با سرستون سبز، ردیف راه‌راه و ستون عنوان ثابت در موبایل دارد. داده‌ها از فیلتر خود ماژول می‌آیند و متن ساده به‌صورت escaped رندر می‌شود؛ ویرایشگر، variants، schema، assets یا افزونهٔ Components منتقل نشده‌اند. جدول قیمت‌گذاری، مرتب‌سازی، جستجو و پنل مدیریت جزو این نمونه نیستند.

## هیرو با تیتر بزرگ و تصاویر میان متن — `hero-typography`

تیتر این طرح به درخواست مالک، Peyda با وزن ۸۰۰ و مقیاس پهن‌تر است تا فرم ضخیم مرجع را حفظ کند؛ این تفاوت فقط در scope همین طرح اعمال می‌شود. متن کوچک معرفی در دسکتاپ با لبهٔ چپ تصویر هم‌راستاست و در موبایل به جریان عادی RTL برمی‌گردد.

مرجع: [Elementra Creative Agency، هیروی ابتدای صفحه](https://creative-agency.elementra.themerex.net/). این طرح هیرو بدون اسلایدر است؛ با home-hero و هیروی اسلایدری مالک مشترکِ طرح ندارد. ترکیب دو ستون، تصویر عمودی چپ با گوشهٔ ۴۰px، تیتر راست و دو عکس دایره‌ای و ستارهٔ چهارپر میان متن حفظ شده است. متن فارسی و فونت‌های موجود کتابخانه استفاده می‌شوند. عرض‌های کوچک به یک ستون تبدیل می‌شوند؛ شبکه‌های اجتماعیِ نمونه بدون URL، لینک ساختگی نیستند.

مرجع برای ورود تیتر `ta_fadeinup` با مدت ۱ ثانیه دارد. نسخهٔ محلی فقط سطرهای کامل را یک‌بار با GSAP موجود، y=40، duration=1، stagger=.08 و power2.out وارد می‌کند؛ زمان و ease جزئی اقتباس محلی است، نه ادعای تطابق فریم‌به‌فریم. بدون pin، drag، autoplay یا موشن تکرارشونده. چهار رسانهٔ `custom-img-19-copyright.jpg`، `custom-img-20-copyright.jpg`، `custom-img-21-copyright.jpg` و `background-01.jpg` در WordPress کتابخانه ذخیره شده‌اند؛ URL منبع و نام copyright در metadata باقی می‌مانند. رسانهٔ محلی و نگهبان attachment جایگزین درخواست runtime خارجی است. قرارداد انتقال: data filter و attachmentها به مقصد نگاشت شوند، نه options و رابط کارگاه.

این دو ماژول از روی بخش مشخص‌شدهٔ مرجع بازسازی شده‌اند و به قالب اصلی Webtalik اضافه نشده‌اند. فقط در کتابخانه فعال‌اند. فونت، RTL، گاتر، هدر کتابخانه، دکمه و مالک اسکرول از زیرساخت موجود استفاده می‌کنند.

## مراحل همکاری — `process-steps`

مرجع: [Orisa index-14-dark، Our process](https://orisa-html-demo.pages.dev/index-14-dark).

چیدمان: معرفی دو ستون، عنوان دوخطی، توضیح و CTA، پنج مرحله با شماره و نقطه، خط جداکننده و زمان در پایین کارت. دسکتاپ پنج ستون، تبلت دو ستون و موبایل یک ستون دارد. گاتر ۶۰px دسکتاپ بزرگ و حداقل ۲۰px مطابق توکن قالب است؛ از گاتر ثابت ۱۰۰px مرجع استفاده نشده است.

`data.php` مالک متن، مراحل، مدت و CTA با فیلتر بومی `rdl/process/data` است. `view.php` فقط رندر می‌کند. heading در پیش‌نمایش H1 و هنگام مصرف مستقل به‌صورت پیش‌فرض H2 است؛ در فراخوانی می‌توان `heading_tag` را H1/H2 داد. شماره‌ها تزئینی‌اند و لیست `ol` ترتیب واقعی مراحل را حفظ می‌کند.

موشن مرجع با مشاهدهٔ `assets/js/main.js`: fade از جابه‌جایی ۲۰px، duration .75، ease power2.out، start top 85% و تأخیر مرحله‌ها .1 تا .5. همین الگو با ScrollTrigger مشترک و ورود یک‌بارمصرف اجرا می‌شود؛ جهت افقی در RTL آینه می‌شود. عنوان فارسی به حرف‌های جدا شکسته نمی‌شود تا اتصال حروف حفظ شود؛ ورود آن روی بلوک است. CSS قبل از بارگذاری JS محتوا را مخفی نمی‌کند؛ reduced-motion کاملاً خوانا و ایستا است.

## گالری دعوت به همکاری — `project-marquee`

مرجع: [Azurio Services، Let's talk about your project / moving list](https://demo.artureanec.com/themes/azurio/services/).

چیدمان: عنوان دوخطی وسط، فاصلهٔ معرفی، کارت‌های با عرض ۳۰۰px و ارتفاع طبیعی تصویر، تراز پایین و gap ۳۰px؛ موبایل ۲۲۰px و gap ۲۰px. یک محوشدگی محدود در پایین خود گالری حس مرجع را حفظ می‌کند؛ لایه‌های blur سراسری و چندگانهٔ مرجع وارد نشده‌اند. هدر و محتوای سایر بخش‌ها زیر این افکت نمی‌روند.

۹ تصویر خود مرجع در رسانهٔ کتابخانه‌اند؛ نسبت، ابعاد ذاتی، srcset و lazy-loading از WordPress می‌آید. نام، URL اصلی و فایل هر تصویر در `fixtures/azurio/media.json` و meta `_rdl_reference_url` حفظ شده‌اند؛ حق و منشأ رسانه همان مرجع باقی است. `data.php` با فیلتر `rdl/marquee/data` عنوان، لینک و آرایهٔ تصاویر را تحویل می‌دهد. رسانه‌ها در option مستقل `rdl_reference_media` به attachment ID مقصد اشاره می‌کنند.

موشن از بررسی theme.js مرجع: حرکت پیوستهٔ ۷۰ثانیه‌ای، ease none و افزایش timeScale در بازهٔ ۱ تا ۶ بر اساس abs(scrollVelocity/200)، با گذار .5 و ease power1.out. این رفتار با GSAP و ScrollTrigger موجود بازسازی شده است؛ jQuery، Elementor، plugin یا موتور دوم وارد نشده‌اند.

فقط یک گروه برای حلقه clone می‌شود؛ گروه clone از دسترس‌پذیری با aria-hidden/inert کنار گذاشته می‌شود و هنگام cleanup حذف می‌شود. حرکت بیرون دید، در تب مخفی، روی فوکوس گالری یا با دکمهٔ توقف متوقف می‌شود. دکمه `aria-pressed` دارد. reduced-motion بدون clone و حرکت خودکار، اسکرول افقی بومی دارد. cleanup، tween سرعت، ScrollTrigger، listenerها و transform را آزاد می‌کند.

## مالکیت مشترک

هر ماژول `view.php`، `data.php`، CSS و JS خود را دارد و در `inc/samples.json` ثبت شده است. `src/js/motion-view.js` فقط مسئول مشترک فعال‌کردن موشن طبق prefers-reduced-motion و lifecycle این دو ماژول است. `src/js/scroll.js` همان ScrollSmoother قبلی را یک‌بار راه‌اندازی می‌کند؛ vendorها در build یک نسخه دارند. فهرست کتابخانه، صفحهٔ اطلاعات و نمونه‌های دیگر CSS/JS این دو طرح را نمی‌گیرند.

`tools/register-references.php` یک installer محلی صریح برای رسانه و دو رکورد است؛ هنگام Start یا نمایش صفحه دانلود/نوشتن/همگام‌سازی انجام نمی‌شود. import یا build مجدد نمونه‌های منبع نباید registry ماژول‌های مرجع را حذف کند.


## کارت‌های Wellness — `wellness-scroll`

مرجع: https://undsgn.com/uncode/homepages/creative-wellness/ ، بخش cs-wrapper-sticky با سه کارت. wrapperSticky مرجع حرکت x با distance واقعی، scrub true، pin و ease none دارد؛ زیر 960px استیکی غیرفعال است. همین رفتار با ScrollTrigger موجود بازسازی شده، بدون slider plugin یا موتور دوم. کارت‌ها عرض حدود 90vw، عکس/متن دو ستون و سه سطح هلویی، آبی و بنفش دارند؛ موبایل چیدمان تک‌ستون و اسکرول افقی بومی دارد. سه تصویر همان مرجع به media محلی نگاشت شده‌اند؛ provenance در fixtures/wellness/media.json و _rdl_reference_url است. data.php / rdl/wellness/data داده را مستقل از حرکت می‌دهد؛ ResizeObserver و refresh مشترک مصرف می‌شوند و pin در cleanup آزاد می‌شود.

## آکاردئون تصویری — Elementra

مرجع: [Creative Agency / Creative Showcase](https://creative-agency.elementra.themerex.net/). رفتار مشاهده‌شده: سه تصویر، عرض فعال ۳ و بقیه ۱، transition flex برابر .4s ease، ارتفاع دسکتاپ 720px، گوشه 40px و gap 20px؛ با خروج ماوس کارت اول فعال است. این یک آکاردئون hover است، نه کاروسل یا حرکت اسکرولی.

ترتیب تصویرها مطابق مرجع از چپ به راست است و متن باکس‌ها RTL می‌ماند.

دو طرح مستقل: `image-accordion` فقط تصویر؛ `card-accordion` تصویر با باکس متن، عنوان و معرفی. هر کدام view/data/style/script و فیلتر دادهٔ جدا (`rdl/image-accordion/data` و `rdl/card-accordion/data`) دارند و به ماژول دیگری import نمی‌کنند. فقط مسئولیت کوچک تعامل در `src/js/ui/image-accordion.js` و هندسهٔ پایه در `src/css/ui/image-accordion.css` مشترک است؛ این دو فایل و وابستگی lifecycle را همراه طرح منتخب بردارید. GSAP فقط مالک اسکرول عمومی موجود است؛ بازشدن با CSS/Vanilla است و موتور/افزونهٔ مرجع وارد نشده.

کیبورد: Tab و فوکوس تصویر را باز می‌کند؛ Left/Right با جهت RTL/LTR، Home/End و Enter/Space پشتیبانی می‌شوند؛ وضعیت با aria-pressed مشخص است. لمس در چیدمان افقی انتخاب می‌کند. زیر 768px تصاویر کامل زیرهم‌اند و دکمهٔ بی‌اثر مخفی است. reduced-motion گذار را حذف می‌کند؛ بدون JS همهٔ عکس‌ها و متن‌ها خوانا هستند. listenerها در lifecycle پاک می‌شوند.

سه عکس خود مرجع در رسانهٔ WordPress محلی‌اند: provenance در `fixtures/elementra/media.json` و `_rdl_reference_url` حفظ شده؛ copyright نام فایل‌ها حذف نشده و حقوق تصاویر متعلق به منبع است. `rdl_elementra_media` فقط adapter رسانهٔ این کارگاه است؛ مقصد با attachment IDهای خودش فیلتر داده را جایگزین می‌کند. `tools/register-accordions.php` ورود محلی صریح و تکرارپذیر است، بدون دانلود زمان اجرا یا بازنویسی دادهٔ دستی. یک طرح فقط به فایل‌های مشترک کوچک نقشه نیاز دارد، نه طرح دوم.

## ستون‌های خلاف‌جهت — `column-gallery`

مرجع: [Orisa Services 1، at-gallery-wrapper](https://orisa-html-demo.pages.dev/services-1). سه ستون با ۱۸ جای تصویر و ۱۵ فایل یکتا؛ سرعت‌های -0.1 / 0.8 / -0.1 و lag صفر با effects همان ScrollSmoother موجود. ستون‌ها موتور دوم یا autoplay ندارند. اگر smoother نباشد، ScrollTrigger با جابه‌جایی اندازه‌گیری‌شده و محدود به محتوای ستون‌ها جایگزین می‌شود. گاتر بیرونی 12px عمداً full-bleed است؛ فاصلهٔ داخلی 30px، شعاع قاب 30px و عکس 20px؛ ارتفاع 1110px در دسکتاپ و 630px در تبلت. زیر 768px، بدون JS یا reduced-motion همهٔ تصاویر به‌صورت ایستا و بدون مخفی‌شدن ادامهٔ ستون‌ها دیده می‌شوند. زوم hover برابر 1.1 طی .6s است. mode/resize/refresh و cleanup مالک موجود را مصرف می‌کنند؛ effects فقط برای همین ستون‌ها kill می‌شوند. فایل‌های مستقل view/data/style/script؛ داده با `rdl/column-gallery/data` و attachment ID مقصد جایگزین می‌شود. منشأ رسانه در `fixtures/orisa-columns/media.json` و meta `_rdl_reference_url` است؛ `tools/register-columns.php` ورود صریح محلی است.

## مراحل با زمان‌بندی — `process-phases`

مرجع: [Orisa index-13، timeline پنج‌ستونه](https://orisa-html-demo.pages.dev/index-13). فقط خود timeline درخواست‌شده، نه صفحهٔ کامل: شماره، هفته، شرح و سه خروجی هر مرحله. Grid پنج‌ستونه با خط عمودی، زمینهٔ شبکه‌ای کم‌رنگ و نشان مرحلهٔ اول؛ زیر 1200px دو ستون و زیر 600px تک‌ستون. ورود از کنار 24px، مدت .75s و تأخیر .1 + .15 × index با power2.out، شروع top 85% و یک‌بار است. نشان pulse برابر 1.8s با تأخیر حلقهٔ دوم .9s؛ بیرون دید/تب مخفی متوقف می‌شود، دکمهٔ توقف دارد و reduced-motion ایستاست. فیلتر `rdl/process-phases/data` محتوا را از موشن جدا نگه می‌دارد؛ active نخست یک وضعیت نمایشی است، نه مراحل تکمیل‌شدهٔ واقعی.

## بازشدن قاب تصویر — `media-stretch`

مرجع: [Willows Iris، تصویر editorial-photo و stretch](https://preview.treethemes.com/elementor/willows/iris/). قاب اولیه 500px با شعاع 250px، ارتفاع 110svh و تصویر object-fit:cover؛ از top top پین می‌شود و با scrub 1 و ease power1.out به عرض 100% و شعاع صفر می‌رسد. مرجع padding 395px و pinSpacing:false دارد؛ ما فاصلهٔ همین پین را صریحاً با pinSpacing و بازهٔ .395 × ارتفاع viewport مدیریت می‌کنیم تا در طرح مستقل قابل‌رسیدن و responsive بماند. پین همان مالک GSAP/اسکرول موجود را دارد؛ روی تغییر mode بازسازی و هنگام cleanup آزاد می‌شود. موبایل، reduced-motion و نبود JS تصویر کامل و ایستا دارند. فیلتر `rdl/media-stretch/data` مالک تصویر/alt است.

## ستون معرفی ثابت — `sticky-cards`

مرجع: [Willows Jane، What we do](https://preview.treethemes.com/elementor/willows/jane/). در نسخهٔ فارسی معرفی در راست و کارت‌های تصویری در چپ‌اند؛ گاتر با هدر مشترک تراز است. مرجع رفتار تکمیلی: Willows jane-dark/services. نقطه روی خط همراه header ثابت می‌ماند؛ نشانگر درصد پیشرفت نیست و tween وابسته به scrub ندارد. زیر 768px مخفی است. خط میانی و فاصله‌ها scoped هستند؛ عنوان با Hafez محلی و باقی متن با Peyda است. `initializeSticky` موجود، معرفی را با فاصلهٔ 100px تا پایان فهرست پین می‌کند؛ روی موبایل/کاهش حرکت خوانایی بدون پین حفظ می‌شود. تصاویر با scale 1.5 و opacity صفر به 1، مدت 1.5s، تأخیر .3s و power2.out وارد می‌شوند. تصویر و متن/لیست هر کارت در data.php و فیلتر `rdl/sticky-cards/data` مستقل‌اند. SVG تزئینی فقط به شکل img از رسانهٔ محلی مصرف می‌شود؛ اجازهٔ MIME آن فقط در installer CLI است و سیاست آپلود سایت تغییر نمی‌کند.

## نوار متن و قاب تصویر — `text-marquee`

مرجع: [Aziz Khaldi، first-story-section و infinit-text](https://azizkhaldi.com/). ساختار و مقادیر از HTML و JS عمومی صفحه/ماژول 9601 بررسی شده؛ دسترسی مستقیم مرورگر مرجع هنگام بررسی با محدودیت 403 مواجه بود. نوار یک دورهٔ 20ثانیه‌ای دارد و جهتش با جهت اسکرول عوض می‌شود؛ ورود y=300 طی 1.2s با power3.out. قاب داستان از عرض 80%، شعاع 34px و y=60 به عرض نزدیک تمام‌صفحه با حاشیهٔ 12px، شعاع صفر و جای اصلی با scrub 1 می‌رسد. offset کوچک‌ترِ پیش‌نمایش فاصلهٔ خالی زیر هدر را کم می‌کند. end پس از start محاسبه و به maxScroll محدود می‌شود؛ دو clamp یا += نسبت به start منفی نباید بازه را صفر کنند. عکس مستقل از قاب، از عرض 60% دسکتاپ / 80% موبایل و scale .95 به عرض 100% و scale 1 با power3.out و scrub 3.5 باز می‌شود؛ trigger آن anchor ثابت است. گاتر نهایی عکس و متا همان گاتر هدر است. گل و پرترهٔ همان مرجع در رسانهٔ محلی هستند؛ دادهٔ نمایشی و هویت مشتری با `rdl/text-marquee/data` جایگزین شود. یک clone بصری aria-hidden/inert است؛ متن دسترس‌پذیر تکرار نمی‌شود. توقف کاربر، فوکوس، بیرون دید و تب مخفی حلقه را متوقف می‌کنند؛ reduced-motion/بدون JS متن معمولیِ قابل‌خواندن بدون clone دارند. cleanup تمام tween/trigger/listener/cloneهای خود ماژول را آزاد می‌کند؛ پاک‌کردن همهٔ ScrollTriggerهای صفحهٔ مرجع کپی نشده است.

این چهار طرح هرکدام view/data/style/script و فیلتر مستقل دارند. `rdl_section_media` فقط adapter attachmentهای کارگاه است؛ برای مشتری فیلتر داده و ID مقصد را جایگزین کنید. منشأ/حقوق تصاویر در `fixtures/new-sections/media.json` و `_rdl_reference_url` حفظ است؛ فونت، jQuery، Elementor، React/Next و پلاگین‌های سایت‌های مرجع وارد نشده‌اند. `tools/register-new-sections.php` فقط ورود محلی صریح و idempotent است؛ runtime دانلود یا نصب انجام نمی‌دهد.

## نوار متن مستقل و مشترک

text-loop نمونهٔ مستقل است؛ text-marquee همان renderer/CSS/JS مشترک inc/ui/text-marquee.php و src/css/ui/text-marquee.css و src/js/ui/text-marquee.js را مصرف می‌کند. دادهٔ هر مصرف‌کننده فیلتر مستقل دارد. هیچ ماژولی از ماژول دیگر import نمی‌کند. مالک نوار H1 اضافه نمی‌کند؛ توقف، visibility، focus، clone inert و fallback خوانا را یک‌جا نگه می‌دارد. عکس‌های خود نمونه‌ها باقی‌اند؛ کارت‌های فهرست فقط SVG مرتبط با نوع طرح دارند و thumbnail رندر نمی‌کنند.


## ورود سه‌بعدی متن — `text-3d`

مرجع: [Willows Jane Dark](https://preview.treethemes.com/elementor/willows/jane-dark/)؛ متن Evolution / Through / Design. کد عمومی text-3d.css و text-3d-trigger.js بررسی شد: افکت اصلی flip دووجهی CSS است، نه پرشدن اسکرولی. perspective برابر 1000px، hinge پایینِ وجه اول و بالای وجه دوم؛ وجه اول y=-100% و rotateX=90، وجه دوم از y=100% و rotateX=-90 به صفر. جابه‌جایی قاب 3.25rem، مدت 1.25s، منحنی cubic-bezier(.8,0,.2,1)، تأخیرهای .05/.25/.45 و IntersectionObserver با threshold=.2 و حاشیهٔ پایین -8%؛ خروج از دید آن را برای ورود بعدی آماده می‌کند.

ماژول مستقل modules/text-3d با data/view/style/script؛ فیلتر rdl/text-3d/data مالک lines/duration/delay/stagger/pointer است. یک H1 با Hafez و متن فارسی دست‌نخورده؛ هر وجه دوم aria-hidden و شماره‌ها تزئینی‌اند. JS/کاهش حرکت غیرفعال باشد، متن اصلی خواناست. نامگذاری کاتالوگ Radman، دستهٔ text/text-3d، سطح section؛ به Three.js وابسته نیست.

درخواست ماوس به‌صورت قابلیت جدا افزوده شده؛ در افکت flip مرجع، pointer handler پیدا نشد. با pointer=false خاموش می‌شود. wrapper ماوس از wrapper چرخش جداست؛ GSAP quickTo موجود با duration=.7، power3.out و سقف 20px افقی/10px عمودی/1.5deg کار می‌کند. روی لمس فعال نیست؛ هیچ حلقهٔ RAF یا ticker دائمی ندارد. توقف/بازگشت هنگام خروج، blur، تب مخفی و cleanup lifecycle موجود انجام می‌شود. داده و CSS ماژول همراه گراف انتخابی مشتری منتقل شوند؛ Elementor/jQuery/فونت یا موتور مرجع کپی نشوند.


## ذرات پیوندخورده — `particles`

موتور محلی particles.js نسخهٔ 2.0.0 با آداپتر lifecycle از `Corporate-Elementor-Catalog/radmanct-components/src/js/vendor/particles.js` به‌عنوان مرجع فقط‌خواندنی منتقل شده؛ مجوز MIT و نام Vincent Garreau در `modules/particles/LICENSE.txt` و سربرگ موتور حفظ شده‌اند. ماژول مالک مستقل data/view/style/script/engine است و از طرح دیگری import نمی‌کند. موتور هنگام ورود به دید به‌صورت lazy بارگذاری می‌شود؛ شمار ذرات متناسب با مساحت و حداکثر ۷۲ است. توقف کاربر، خروج از دید، تب مخفی و reduced-motion موتور را آزاد می‌کنند؛ fallback ایستا خوانایی را حفظ می‌کند. resize فقط یک مالک رویدادمحور دارد و teardown ناظرها، listenerها و زمان‌بندی‌های خود ماژول را آزاد می‌کند.

## متن با عکس‌های داخل خط
`text-images` / `rdl-tx-im` در گروه `text/text-inline`، سطح section است؛ مرجع: https://undsgn.com/uncode/pages/services-wellness/ . چهار عبارت و تصویر تزئینی در جریان طبیعی یک عنوان قرار می‌گیرند؛ تصاویر 3:2، اندازهٔ نسبی بر پایهٔ em و شکست خطوط واکنش‌گرا دارند. داده از `rdl/text-images/data` (آرایهٔ text/image با attachment ID محلی) یا آرگومان `items` می‌آید؛ `heading_tag` یکی از h1/h2/h3 است، با قرارداد Hafez فقط H1 و Peyda برای بقیه. رنگ‌های سطح و متن با `--rdl-tx-im-bg` / `--rdl-tx-im-clr` قابل جایگزینی‌اند.
مالک مستقل `modules/text-images/{view,data,style,script}` است. تصاویر مرجع به‌صورت WebP کوچک در رسانهٔ همین کتابخانه نگهداری می‌شوند و `_rdl_reference_url` منبعشان را ثبت می‌کند. ورود متن 1s و زوم تصاویر .6s با تأخیر .2s یک‌بار در دید اجرا می‌شود؛ از GSAP/ScrollTrigger، lifecycle و اسکرول موجود استفاده می‌کند، بدون موتور یا listener اسکرول تازه. بدون JS یا در reduced-motion محتوا خواناست؛ تصاویر تزئینی از متن دسترس‌پذیر عنوان حذف‌اند. برای مشتری فقط ماژول، مالک‌های مشترک مصرف‌شده و نگاشت رسانه منتقل شوند؛ دادهٔ کارگاه، Elementor/Uncode و سرویس خارجی منتقل نشوند.
