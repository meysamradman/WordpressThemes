# کاروسل محتوای آزاد

نمونه: `/design/card-carousel/`؛ بررسی جهت معکوس: `?rdl_dir=ltr`. خانوادهٔ `carousel`، زیرشاخهٔ `carousel-cards`، نوع `section`. این طرح کاروسل دستی و محدود است؛ marquee پیوسته یا اسلایدر هیرو نیست.

## مالک و استفادهٔ مجدد

- `radman-library/inc/ui/carousel.php`: تابع `RadmanLibrary\UI\Carousel\render($items, $item_view, $args)` فقط قاب و کنترل‌ها را می‌سازد. callback مورداعتماد PHP محتوای هر کارت را رندر و escape می‌کند؛ هسته از عکس، آیکون، ویدئو یا نوع داده خبر ندارد.
- `src/css/ui/carousel.css`: اندازه با `--rdl-cr-w` و فاصله با `--rdl-cr-gap` قابل انتخاب است؛ گاتر از `--rdc-gut` می‌آید. لبهٔ آغاز گاتر دارد و لبهٔ مقابل کارت بعدی را برش می‌دهد. در انتهای واقعی، کنترل بعدی غیرفعال می‌شود.
- `src/js/ui/carousel.js`: `initializeCarousel(root)` کنترل‌ها را متصل و تابع cleanup برمی‌گرداند. هر مصرف‌کننده این دو asset را وارد و cleanup را به lifecycle موجود وصل کند. نیاز به GSAP، Swiper یا jQuery ندارد؛ smoother صفحه مالک موجود خودش را حفظ می‌کند.
- `direction` در آرگومان renderer برابر `rtl` یا `ltr` و `label` نام دسترس‌پذیر است. جهت پیش‌فرض از WordPress می‌آید. query جهت، adapter آزمایشی همین نمونه است و در مشتری کپی نمی‌شود.

کارت‌ها در `modules/card-carousel/card.php` و داده‌ها در `data.php` متعلق به نمونه‌اند؛ فیلتر `rdl/card-carousel/data` جایگزینی داده را ممکن می‌کند. برای طرح تازه callback/markup/style خودش نوشته شود، نه شرط‌های نوع کارت داخل هسته. تعداد کارت ثابت نیست؛ محاسبهٔ صفحات بر اساس عرض قابل دیدن انجام می‌شود. برای مشتری فقط مالک و مصرف‌کنندهٔ منتخب و رسانه‌های نگاشت‌شده منتقل شوند؛ کل کارگاه کپی نشود.

## تعامل و رسانه

اسکرول بومی با scroll-snap، لمس بومی، کشیدن ماوس، قبلی/بعدی و نقطه‌های صفحه‌بندی؛ کلیدهای چپ/راست مطابق جهت فیزیکی و Home/End فعال‌اند. لینک، دکمه و کنترل ویدئو drag نمی‌شوند. فوکوس کارت خارج قاب، همان کارت را نمایان می‌کند. reduced-motion حرکت نرم را حذف می‌کند؛ بدون JS اسکرول بومی باقی می‌ماند. autoplay، clone و حلقهٔ بی‌پایان ندارد. observer/listener/RAF هنگام teardown پاک می‌شوند.

ویدئوی نمونه کنترل بومی و `preload=none` دارد، خودکار پخش نمی‌شود و خارج دید/تب مخفی متوقف می‌شود. تصاویر از attachmentهای موجود با ابعاد و srcset بومی‌اند. ویدئوی محلی با `tools/register-card-carousel.php` یک‌بار وارد رسانه می‌شود؛ option و ثبت طرح idempotent است.

## منشأ

مرجع بصری: [Pixfort AI Agency](https://essentials.pixfort.com/ai-agency/). ویدئوی مرجع: `https://essentials.pixfort.com/ai-agency/wp-content/uploads/sites/64/2026/03/bg-video-office-3.mp4`؛ منشأ در meta رسانه حفظ شده است. تصاویر از رسانهٔ موجود کتابخانه‌اند. هیچ درخواست runtime به مرجع یا کتابخانهٔ کاروسل آن وجود ندارد؛ مجوز/منشأ دارایی هنگام انتقال به مشتری حفظ شود.
