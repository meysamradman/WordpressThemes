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

آرشیو `/projects/`، taxonomy و جزئیات `project` واقعاً کار می‌کنند؛ مدیریت بومی، گالری، عکس شاخص، excerpt و محتوای editor مستقل است. نمونه‌های library نیز همان renderer را با query آزمایشی و بازگرداندن main query مصرف می‌کنند. pagination بومی، قبلی/بعدی و مشابه مالک مشترک دارند؛ source IDs و اطلاعات مشتری کپی نشده‌اند.

## داده و دارایی

دادهٔ نمایشی از options کوچک کتابخانه و فیلترهای هر ماژول می‌آید؛ این adapterها جزو قالب مشتری نیستند. media در WordPress مقصد نگاشت می‌شود؛ metadata و srcset بومی بمانند. HTML/PHP/JS سفارشی به jQuery/Elementor وابسته نیست؛ media manager مدیریت همان WordPress است.

کتابخانه تعداد زیادی سورس دارد، ولی runtime هر نمونه فقط گراف خودش را می‌گیرد. AJAX فهرست از همان خروجی HTML بومی استفاده می‌کند؛ endpoint/اتصال DB جدید ندارد. فقط یک جستجوی طراحی با debounce و درخواست قابل لغو داریم؛ دسته‌بندی یک درخت مستقل بدون ورودی جستجوی دوم است. فیلترها، history، loading و fallback لینک عادی حفظ شوند. counts دسته‌ها شمارش بومی کل آن دسته‌اند، نه محاسبهٔ جدا برای هر ترکیب فیلتر.

فقط بررسی کوتاهِ تغییر لازم انجام شود. برای تغییر داده/مستندات، benchmark یا تست تکراری مرورگر اجرا نکنید. فایل‌های build، نقشه و catalog بعد از تغییر dependencyها به‌روز شوند؛ منبع مشتری یا نمونهٔ دیگر بازنویسی نشود.

## کاتالوگ محصول
`inc/catalog.php` ثبت CPT/طبقه‌بندی/رندر؛ `inc/catalog/model.php` مدل منتخب منبع، `query.php` فیلتر بومی، `admin.php` و `fields.php` ویرایش امن محتوا؛ `src/js/admin/catalog-editor.js` فقط مدیریت محصول. دو ماژول `catalog-archive` و `catalog-single` مالک نمایش و CSS مشترک `src/css/catalog.css` هستند. گالری از مالک کوچک موجود `ui/gallery.js` و `ui/gallery.css` استفاده می‌کند. قرارداد و منشأ در [catalog-products.md](catalog-products.md).

## ماژول‌های مرجع اسکرولی
`column-gallery`، `process-phases`، `media-stretch`، `sticky-cards` و `text-marquee` ماژول مستقل‌اند؛ فقط lifecycle/scroll-state/refresh/observer/vendor و sticky واقعی را از مالکان موجود می‌گیرند. تصاویر attachment محلی هستند و فیلتر data هر ماژول به مشتری مقصد قابل جایگزینی است. مقادیر حرکت، fallback و منشأ در [reference-sections.md](reference-sections.md)؛ فایل‌های انتقال در module-map.json.

مالک مشترک نوار متن: inc/ui/text-marquee.php، src/css/ui/text-marquee.css و src/js/ui/text-marquee.js؛ text-loop نمونهٔ مستقل و text-marquee مصرف‌کنندهٔ ترکیبی همان مالک‌اند. کارت فهرست فقط icon محلی samples.json را رندر می‌کند؛ thumbnail یا عکس نمایشی در فهرست درخواست نمی‌شود.

رنگ و کارت‌های رابط فهرست فقط در `assets/shell.css` و توکن‌های `--rdl-*` نگهداری شوند؛ ظاهر نمونه‌های مشتری تغییر نکند. سایدبار فهرست sticky بومی CSS با فاصلهٔ admin bar، ارتفاع محدود به viewport و اسکرول داخلی دسته‌های بلند است؛ زیر ۹۰۰ پیکسل جریان عادی دارد. برای آن pin، GSAP، listener اسکرول یا مالک موازی نسازید؛ جایگزینی AJAX همان کلاس‌ها را حفظ می‌کند.

project-marquee motion: Match the inspected reference: duration = 70 × card count / 15; on each scroll update set timeScale to clamp(abs(velocity / 200),1,6), easing .5s power1.out with overwrite:auto. No peak holding, delayedCall, extended deceleration or invented momentum. The existing ScrollTrigger scrollEnd event returns to timeScale 1 through the same .5s easing. No visible play/pause button in either project-marquee preview or Webtalik; retain focus, hidden-tab, offscreen and reduced-motion guards. Preserve independent project data and local scroll ownership.
