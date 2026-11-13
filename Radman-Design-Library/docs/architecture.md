# معماری مرجع طراحی Radman

سناریو و قواعد اصلی در [scenario.md](scenario.md)؛ نام‌گذاری در [naming.md](naming.md)؛ فهرست نمونه‌ها در [catalog.md](catalog.md). این پروژه مستقل از سایت مشتری است و افزونهٔ سازنده ندارد.

| مالک | مسئولیت |
|---|---|
| `rdl_design` | رکورد هر نمونه، عنوان/خلاصه/تصویر و انتخاب ماژول |
| `rdl_group` / `rdl_kind` / `rdl_fx` | خانوادهٔ سلسله‌مراتبی / سطح سکشن، صفحه، مجموعه / فناوری و حرکت |
| `modules/<slug>/` | view، داده، CSS و JS مستقل؛ ثبت صریح در `inc/samples.json` |
| `shared/radmanct/` | یک snapshot انتخابی از مالکان قالب؛ نه کپی vendor برای هر طرح |
| `inc/samples.php` و `inc/portfolio.php` | adapter داده و query آزمایشی؛ مخصوص کتابخانه |
| `preview.php` + `template-parts/library-bar.php` | هدر مشترک و پیش‌نمایش تمام‌عرض با یک اسکرول |
| `inc/library.php`، `assets/library.js`، `library-sidebar.php` | فهرست AJAX، جستجو، loading، دسته‌های بومی و صفحه‌بندی |
| `src/js/scroll.js` / `motion-view.js` | مالک اسکرول موجود / lifecycle و reduced-motion کوچک مشترک |
| `src/css/sample-base.css` / `client-base.css` | پایهٔ پیش‌نمایش با هدر کارگاه / پایهٔ مشتری بدون رابط کارگاه |
| `dist` / `dist-selected` | build کتابخانه / خروجی انتخابی مشتری؛ ویرایش مستقیم ممنوع |
| `docs/module-map.json` | نقشهٔ فایل‌ها و قرارداد helperها برای برداشتن هر ماژول |

## مسیر و چیدمان

پیش‌نمایش پیش‌فرض URL هر طراحی است؛ `?rdl_info=1` فقط اطلاعات را نشان می‌دهد. هدر کتابخانه حذف نشود. هدر و main از ابتدا داخل همان محتوای relative اسکرول باشند؛ iframe، اصلاح دیرهنگام موقعیت و موتور دوم نداریم. رسانهٔ full bleed تمام‌عرض و گاتر محتوا `clamp(1.25rem,3.15vw,3.75rem)` است؛ فاصله دوباره اعمال نشود. محدودیت عمدی FAQ و محتوای مقاله حفظ شود.

استثنای صریح ابزار دستگاه: `inc/preview.php` و assets/preview CSS/JS فقط متعلق به کارگاه‌اند. حالت آزاد همچنان مستقیم و بدون iframe است؛ `?rdl_view=laptop|tablet|mobile` پوسته‌ای با یک iframe هم‌مبدأ می‌سازد که همان طرح را با `?rdl_canvas=1`، بدون هدر/کنترل کارگاه، نمایش می‌دهد. viewportهای واقعی ۱۴۴۰×۹۰۰، ۷۶۸×۱۰۲۴ و ۳۹۰×۸۴۴ هستند؛ اندازهٔ قاب عوض می‌شود تا media query و svh همان دستگاه اعمال شوند. تغییر میان دستگاه‌ها فقط همان iframe را resize می‌کند و src را عوض نمی‌کند. جاگیری خودکار با یک ResizeObserver رویدادمحور است؛ حلقهٔ اندازه‌گیری، bundle همهٔ طرح‌ها یا اسکرول دوم نداریم. پوسته فقط پایه/کنترل کارگاه را می‌گیرد و دارایی طرح فقط در سند داخلی بارگذاری می‌شود. در canvas ساخت قاب تودرتو ممنوع است. این فایل‌ها در module-map به‌عنوان preview_only مشخص‌اند و خروجی انتخابی از client-base بدون کنترل کارگاه استفاده می‌کند.

آرشیو `/projects/`، taxonomy و جزئیات `project` واقعاً کار می‌کنند؛ مدیریت بومی، گالری، عکس شاخص، excerpt و محتوای editor مستقل است. نمونه‌های library نیز همان renderer را با query آزمایشی و بازگرداندن main query مصرف می‌کنند. pagination بومی، قبلی/بعدی و مشابه مالک مشترک دارند؛ source IDs و اطلاعات مشتری کپی نشده‌اند.

## داده و دارایی

دادهٔ نمایشی از options کوچک کتابخانه و فیلترهای هر ماژول می‌آید؛ این adapterها جزو قالب مشتری نیستند. media در WordPress مقصد نگاشت می‌شود؛ metadata و srcset بومی بمانند. HTML/PHP/JS سفارشی به jQuery/Elementor وابسته نیست؛ media manager مدیریت همان WordPress است.

کتابخانه تعداد زیادی سورس دارد، ولی runtime هر نمونه فقط گراف خودش را می‌گیرد. AJAX فهرست از همان خروجی HTML بومی استفاده می‌کند؛ endpoint/اتصال DB جدید ندارد. فقط یک جستجوی طراحی با debounce و درخواست قابل لغو داریم؛ دسته‌بندی یک درخت مستقل بدون ورودی جستجوی دوم است. فیلترها، history، loading و fallback لینک عادی حفظ شوند. counts دسته‌ها شمارش بومی کل آن دسته‌اند، نه محاسبهٔ جدا برای هر ترکیب فیلتر.

فقط بررسی کوتاهِ تغییر لازم انجام شود. برای تغییر داده/مستندات، benchmark یا تست تکراری مرورگر اجرا نکنید. فایل‌های build، نقشه و catalog بعد از تغییر dependencyها به‌روز شوند؛ منبع مشتری یا نمونهٔ دیگر بازنویسی نشود.

## کاتالوگ محصول
`inc/catalog.php` ثبت CPT/طبقه‌بندی/رندر؛ `inc/catalog/model.php` مدل منتخب منبع، `query.php` فیلتر بومی، `admin.php` و `fields.php` ویرایش امن محتوا؛ `src/js/admin/catalog-editor.js` فقط مدیریت محصول. دو ماژول `catalog-archive` و `catalog-single` مالک نمایش و CSS مشترک `src/css/catalog.css` هستند. گالری از مالک کوچک موجود `ui/gallery.js` و `ui/gallery.css` استفاده می‌کند. قرارداد و منشأ در [catalog-products.md](catalog-products.md).

## ماژول‌های مرجع اسکرولی

`modules/particles/` مالک کامل data/view/style/script/engine و LICENSE خودش است؛ به runtime یا طرح دیگر import ندارد. موتور محلیِ MIT از نسخهٔ قبلی Radman CT اقتباس شده، با سقف ۷۲ ذره در انتخاب نمونه، کاهش تعداد متناسب با مساحت و سقف Retina=2 موتور. بارگیری engine فقط هنگام دید و اجازهٔ حرکت انجام می‌شود؛ توقف دستی، بیرون دید، تب مخفی، reduced-motion و teardown موتور و canvas را آزاد می‌کنند. فقط ResizeObserver همین ماژول resize را مدیریت می‌کند؛ listeners موازی موتور خاموش‌اند. fallback ایستا و متن بدون JS خوانا می‌مانند. schema و پنل Elementor منتقل نشده‌اند.

نمونهٔ `modules/button-roll/` فقط قاب و دادهٔ دکمهٔ فعلی Webtalik است. view همان `RadmanCT\button()` و CSS موجود `shared/radmanct/src/css/ui/button.css` را مصرف می‌کند؛ رفتار hover/focus/reduced-motion کپی یا بازنویسی نمی‌شود. فیلتر `rdl/button-roll/data` متن، URL و نام آیکون را تحویل می‌دهد. پیش‌نمایش روشن/تیره یک طراحی است، نه دو موتور یا دو ماژول؛ JS و اسکرول نرم بارگذاری نمی‌شوند. قرارداد انتقال شامل renderer، آیکون و توکن/CSS مشترک موجود است؛ قاب `rdl-btn-demo` مخصوص کارگاه است.

جدول مستقل `modules/data-table/` شامل data/view/table/style است؛ اسکریپت ندارد. فیلتر `rdl/data-table/data` عنوان و دادهٔ `columns` / `rows[].cells` را تأمین می‌کند. renderer فقط caption، th/td با scope و خروجی escaped دارد؛ خانه‌های کم‌شده خالی می‌مانند و حداکثر ۱۲ ستون/۱۰۰ ردیف پذیرفته می‌شود. قاب بومی برای لمس و کیبورد اسکرول افقی دارد و عنوان ردیف در موبایل sticky است. دادهٔ نمونه، runtime یا ذخیره‌سازی قالب قبلی وارد کتابخانه نمی‌شود؛ انتقال این طرح نیازمند Elementor، Components، JS یا رسانه نیست.

هیروی تایپوگرافیِ بدون اسلایدر در `modules/hero-typography/` مستقل است؛ view/data/style/script خودش را دارد و فقط scroll/lifecycle/GSAP و نگهبان رسانهٔ موجود را مصرف می‌کند. فیلتر `rdl/hero-typography/data` متن، سطرها، تصویر و زمینهٔ attachment محلی را تحویل می‌دهد. ورود یک‌باره روی سطر کامل است؛ فارسی به حرف‌ها شکسته نمی‌شود. بدون JS و در reduced-motion محتوا خواناست. `samples.json/subgroup` جایگاه بدون اسلایدر/اسلایدری را برای ثبت مجدد حفظ می‌کند. رابط کتابخانه، خانوادهٔ دکمهٔ خالی را عمداً نمایش می‌دهد؛ runtime نمونه یا طرح دکمهٔ فرضی اضافه نمی‌شود.

هیروی اسلایدری مالک مستقل `modules/hero-slider/{data.php,view.php,style.css,script.js}` و فیلتر `rdl/hero-slider/data` دارد. دادهٔ نمونه از attachmentهای محلی موجود می‌آید و هنگام انتقال جایگزین می‌شود. Pointer Events پیش‌نمایش پردهٔ جهت‌دار را کنترل می‌کند؛ GSAP محلی فقط حرکت را اجرا می‌کند، بدون Swiper، autoplay یا موتور اسکرول دوم. فلش، کیبورد، برگشت drag کوتاه، لغو pointer/resize و کاهش حرکت حفظ شوند. تنها اسلاید فعال در ترتیب فوکوس است؛ بدون JS همهٔ اسلایدها و لینک‌ها خوانا می‌مانند. ثبت محلی idempotent در `tools/register-hero-slider.php` است؛ خانواده hero با زیرشاخه‌های ثابت/اسلایدری از carousel جداست.

کاروسل محتوای آزاد مالک کوچک PHP/CSS/JS در `inc/ui/carousel.php` و `src/{css,js}/ui/carousel.*` دارد؛ محتوای کارت فقط در مصرف‌کننده است. قرارداد جهت، گاتر، کنترل‌ها و انتقال در [carousel.md](carousel.md).
`column-gallery`، `process-phases`، `media-stretch`، `sticky-cards` و `text-marquee` ماژول مستقل‌اند؛ فقط lifecycle/scroll-state/refresh/observer/vendor و sticky واقعی را از مالکان موجود می‌گیرند. تصاویر attachment محلی هستند و فیلتر data هر ماژول به مشتری مقصد قابل جایگزینی است. مقادیر حرکت، fallback و منشأ در [reference-sections.md](reference-sections.md)؛ فایل‌های انتقال در module-map.json.

مالک مشترک نوار متن: inc/ui/text-marquee.php، src/css/ui/text-marquee.css و src/js/ui/text-marquee.js؛ text-loop نمونهٔ مستقل و text-marquee مصرف‌کنندهٔ ترکیبی همان مالک‌اند. کارت فهرست فقط icon محلی samples.json را رندر می‌کند؛ thumbnail یا عکس نمایشی در فهرست درخواست نمی‌شود.

رنگ و کارت‌های رابط فهرست فقط در `assets/shell.css` و توکن‌های `--rdl-*` نگهداری شوند؛ ظاهر نمونه‌های مشتری تغییر نکند. سایدبار فهرست sticky بومی CSS با فاصلهٔ admin bar، ارتفاع محدود به viewport و اسکرول داخلی دسته‌های بلند است؛ زیر ۹۰۰ پیکسل جریان عادی دارد. برای آن pin، GSAP، listener اسکرول یا مالک موازی نسازید؛ جایگزینی AJAX همان کلاس‌ها را حفظ می‌کند.

project-marquee motion: Match the inspected reference: duration = 70 × card count / 15; on each scroll update set timeScale to clamp(abs(velocity / 200),1,6), easing .5s power1.out with overwrite:auto. No peak holding, delayedCall, extended deceleration or invented momentum. The existing ScrollTrigger scrollEnd event returns to timeScale 1 through the same .5s easing. No visible play/pause button in either project-marquee preview or Webtalik; retain focus, hidden-tab, offscreen and reduced-motion guards. Preserve independent project data and local scroll ownership.
