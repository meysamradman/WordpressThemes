# سکشن‌ها و ستون‌های چسبان

مالک رفتار [کامپوننت‌ها](../scenarios/components.md) است. این خانواده در Components است؛ اسکرول افقی و موشن عمومی کانتینر در [مالک Theme](container-motion.md) و sticky هدر در ناوبری می‌مانند.

## مدل‌های مستقل — سورس ۲۰۲۶-۱۰-۲۶

| مدل | رفتار و ماژول |
|---|---|
| stack | پشتهٔ Hub با فاصلهٔ لایه و scale مرحله‌ای؛ `sticky-sections/stack.js` |
| overlap | هم‌پوشانی بدون scale؛ pin مشترک در `panels.js` |
| services | پنل‌های سرویس مانند Agntix؛ pin مشترک و CSS سرویس |
| zoom | کارت‌های کوچک‌شونده مانند Elementra؛ موتور stack با فاصلهٔ صفر و عمق ملایم‌تر |
| split | ستون ثابت و محتوای اسکرولی در آغاز/پایان منطقی، سازگار RTL/LTR؛ `split.js` |

هر مدل ویجت Classic/Atomic با IDهای `rdct-sticky-{mode}` و `rdct-atomic-sticky-{mode}` دارد؛ mode ثابت و کنترل نامرتبط حذف/وابسته است. ID عمومی `rdct-sticky-sections` برای دادهٔ قبلی حفظ و از درج جدید پنهان است. کتابخانه نمونهٔ هر پنج مدل را در دو زبان و دو مدل editor درج می‌کند؛ طرح کامل/دارایی سایت‌های مرجع کپی نشده است.

## محتوا و استفاده

برای callback معتبر PHP، گزینهٔ کدنویسی `custom_item=true` پوستهٔ داخلی عمومی و wrapper محتوا را حذف می‌کند؛ callback یک ریشهٔ مستقیم با hook موجود `rdct-ss__inner` می‌دهد و همان عنصر می‌تواند Grid اختصاصی صفحه باشد. این انتخاب بدون callback فعال نمی‌شود، کنترل ذخیره‌شدهٔ Elementor نیست و markup پیش‌فرض مصرف‌کنندگان قبلی را تغییر نمی‌دهد. گروه، section هر پنل و hook لازم حرکت حفظ می‌شوند؛ `pin-spacer` خود GSAP نیز wrapper تزئینی نیست. Child خانه این مسیر را مصرف می‌کند؛ دو wrapper از هر پنل و مجموع هشت wrapper چهار پنل در سورس حذف شدند.

هر کارت و ستون ثابت HTML مجاز یا Template منتشرشدهٔ Elementor می‌گیرند؛ عنوان کارت اختیاری است. رسانه/Template از resolver مشترک و WPML می‌آیند. محتوا/IDها clone نمی‌شوند.

PHP بدون Elementor: `rdct_sticky_sections($items,$options)` و helperهای `rdct_service_panels`، `rdct_zoom_cards` و `rdct_sticky_split` وجود دارند. renderer عمومی دو callback اختیاری برای ستون ثابت و محتوای کارت دارد؛ callback کارت index و دادهٔ نرمال‌شده می‌گیرد. callback فقط callable کد مورد اعتماد PHP است و از JSON اجرا نمی‌شود؛ بافر خروجی در exception آزاد می‌شود. نمونه:

```php
rdct_sticky_split(
    static function () { get_template_part( 'parts/services/intro' ); },
    array( array( 'title' => 'Service A' ), array( 'title' => 'Service B' ) ),
    array( 'aside_position' => 'end', 'padding' => 0 ),
    static function ( $index, $item ) {
        get_template_part( 'parts/services/card', null, array( 'index' => $index ) );
    }
);
```

سقف ۱۲ آیتم است. split با یک محتوای بلند نیز کار می‌کند؛ سایر مدل‌ها حداقل دو آیتم می‌خواهند. کارت بلندتر از viewport پین نمی‌شود؛ ستون ثابت بلند، چیدمان عادی می‌گیرد.

## بارگذاری و چرخهٔ عمر

### انتقال پنل سرویس — سورس ۲۰۲۶-۱۰-۲۹

مالک Orisa index-2 را برای سکشن دوم معرفی کرد. در مرورگر، `scroll-section.vertical-section` با pin-spacer، ورود عمودی پنل‌ها و scale خروجی حدود ۰٫۹ مشاهده شد؛ عنوان و قاب تصویر نیز حرکت داشتند. گزینهٔ اختیاری `transition=slide-scale` برای mode=services افزوده شد؛ مقدار پیش‌فرض pin و رفتار قبلی باقی است. dispatcher فقط برای این انتخاب `sticky-sections/slides.js` را دریافت می‌کند. همان `createStickyScene` مشترک، offset هدر/مدیریت، resize، reduced motion و cleanup استفاده می‌شوند؛ موتور اسکرول موازی وجود ندارد. timeline واحد، pin گروه، ورود از پایین، scale/opacity خروجی و حرکت اختیاری عنوان/تصویر/توضیح را کنترل می‌کند. markerهای محتوایی `data-rdct-panel-title`، `data-rdct-panel-visual` و `data-rdct-panel-copy` اختیاری‌اند؛ محتوا clone یا به spanهای حرفی شکسته نمی‌شود. پنل بلند به جریان عادی برمی‌گردد. این گزینه از schema مشترک استفاده می‌کند، اما ویرایشگر Classic/Atomic آن در این تغییر در مرورگر آزمایش نشده است.

dispatcher فقط موتور منتخب را می‌گیرد؛ GSAP/ScrollTrigger از loader محلی مشترک‌اند و زیر حد عرض یا reduced motion اصلاً دریافت نمی‌شوند. CSS پایه و خانوادهٔ منتخب جدا هستند؛ overlap فقط پایه می‌گیرد. pin، start/end، ارتفاع هدر/نوار مدیریت، context/revert، query دستگاه، ResizeObserver و cleanup در `scene.js` مشترک‌اند؛ scroll-loop دستی قبلی حذف شد. تصویر/فونت/اندازه بازاندازه‌گیری محدود دارند؛ focus همان instance را به جریان عادی برمی‌گرداند. pin زیر گروه فعال تودرتو کنار گذاشته می‌شود.

داخل editor فقط SSR/CSS است؛ pin و اسکرول مصنوعی اجرا نمی‌شوند. preview عادی صفحه/نمونه برای حرکت است. کنترل‌های Classic از کارخانهٔ بومی و Atomic از schema/کنترل مشترک می‌آیند؛ Template به Select بومی وصل است.

## مرجع و پذیرش

در مرورگر، [Agntix](https://wp.aqlova.com/agntix/modern-agency/) دارای پنل سرویس با pin-spacer و [Elementra](https://massage.elementra.themerex.net/) دارای stack/scale دیده شدند. Hub `theme.js::LiquidStickyStack` برای offset/spacer/minScale و ScrollTrigger بررسی شد؛ framework آن وارد محصول نشد. API pin/context با Context7 و GSAP نصب‌شدهٔ 3.15.0 تطبیق داده شد.

build، قرارداد JS حالت‌ها/موبایل/teardown، graph استقلال، PHP محتوای دلخواه/حدود/دارایی منتخب، schema و نمونه‌های typed واقعی Atomic و XML ترجمه بررسی شدند. نصب WordPress، WPML واقعی، ظاهر نهایی، ذخیره/Undo، smooth scrolling و pin در ancestorهای سفارشی transform/overflow هنوز پذیرش می‌خواهند؛ تطابق پیکسلی مرجع ادعا نمی‌شود. نسخه/ZIP تغییر نکردند.
