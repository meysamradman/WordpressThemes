# جلوهٔ مشترک عنصر

## حاشیهٔ چرخان با هاور

مالک: `radmanct-components/inc/components/hover-border.php`، `src/js/components/hover-border.js` و `src/css/components/hover-border.css`. مستقل از Theme و GSAP است. قابلیت روی wrapper هر باکس/آیکون‌باکس/کانتینر HTML عادی اعمال می‌شود؛ SVG/img و کنترل ورودی باید داخل wrapper باشند. فعال‌سازی پیش‌فرض خاموش، دو رنگ محلی، ضخامت ۰٫۵–۱۲ پیکسل، مدت ۱–۳۰ ثانیه و جهت auto/clockwise/counterclockwise دارد. auto در RTL معکوس می‌شود؛ متن/محتوا جابه‌جا نمی‌شوند. شکل و radius از عنصر میزبان ارث می‌برند.

لایهٔ تزئینی اختصاصی `span.rdct-bdr-ring` روی همان ریشه اضافه می‌شود؛ `pointer-events:none`، inert و aria-hidden دارد و pseudo-element میزبان، زمینه، لینک و فرم را تصاحب نمی‌کند. فقط میزبان با position محاسبه‌شدهٔ static کلاس relative می‌گیرد. mask وسط را شفاف و conic-gradient حلقه را می‌سازد؛ `@property` زاویه را نرم می‌چرخاند. hover/focus-within محرک‌اند؛ در لمس و reduced motion حلقه ثابت است. مرورگر فاقد امکانات لازم به حاشیهٔ ثابت برمی‌گردد. observer یک‌بار در document ایجاد و تغییر تنظیم/حذف عنصر cleanup می‌شود؛ JS در هر فریم یا رویداد mousemove اجرا نمی‌شود.

این الگو پس از جستجوی نمونه‌ها با مستندهای اصلی [conic-gradient](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Values/gradient/conic-gradient)، [mask](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Properties/mask) و [@property](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/At-rules/@property) تطبیق داده شد؛ پکیج/کد سرویس ثالث وارد محصول نشد.

## Elementor و PHP

Classic: Advanced > Animated hover border با SWITCHER/COLOR/NUMBER/SELECT بومی؛ Atomic: Section/props/dependencies و کنترل‌های بومی همان نسخه، با wrapper رنگ مشترک موجود افزونه. تنظیمات زیر `rdct_border_*` هستند. adapter مستقل `inc/integrations/elementor/hover-border.php` نخستین تگ خروجی را برای هر دو نوع decorate می‌کند؛ buffer با اولویت‌های موشن Theme هم‌تراز است. Interactions یا موشن transform عنصر حذف نمی‌شوند. preview به settings سند متصل و teardown می‌شود؛ global element-ready برای عناصر دیررس Elementor نیز initializer را فراخوانی می‌کند.

```php
<div class="client-card" <?php rdct_border_attributes( [
    'enabled' => true,
    'color_start' => '#64e8c6', 'color_end' => '#8b5cf6',
    'width' => 2, 'duration' => 4, 'direction' => 'auto',
] ); ?>>Content</div>
```

helper فقط attribute چاپ می‌کند؛ `<link>` داخل تگ باز نمی‌نویسد. CSS/JS فقط برای مصرف فعال enqueue می‌شوند؛ CSS دیررس در footer اولویت ۹ چاپ می‌شود. صفحهٔ سفارشی به Elementor وابسته نیست؛ در نبود افزونه، helper خالی Theme خروجی صفحه را سالم نگه می‌دارد. تزریق AJAX خارج از چرخهٔ Elementor نیازمند مصرف stylesheet/entry و initializer صریح است.

## وضعیت و مرز بررسی

Build، قرارداد دارایی/minify، PHP 8.4، schema/controlهای واقعی Elementor 4.3.5 و بررسی کوتاه JS برای حدود ورودی، جهت، عدم تکرار لایه و cleanup انجام می‌شوند؛ پذیرش بصری سرور، لمس/مرورگر واقعی و تعامل با همهٔ استایل‌های مشتری هنوز انجام نشده است. Marquee موتور دیگری با مالک [کاروسل](carousel.md) دارد؛ با حاشیه یا GSAP ادغام نمی‌شود.
