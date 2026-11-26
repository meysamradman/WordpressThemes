# WordPress Elementor Development — v1.0

> بازبینی بسته 1.3 — 2026-11-19؛ نام مسیر برای سازگاری ارجاع‌های موجود حفظ شده است. تاریخچه در CHANGELOG-v1.3.md.

**تاریخ:** 2026-11-18 | **دامنه:** فقط پروژه‌های Elementor و توسعه ویجت/افزونه/قالب پایه مرتبط. مستقل از برند. اسناد مشترک برای کد متعلق به تیم اعمال می‌شوند، نه بازنویسی markup/runtime تولیدشده توسط Elementor.

## 1. مرز پروژه و نسخه

- Profile مستقل: نسخه WP/PHP، Elementor/Pro و license، theme/child، افزونه‌ها، معماری editor (Classic/V3، Atomic یا نسخه‌های فعال در نصب واقعی)، browser matrix، asset owner و محدوده. PHP 8.4+ هدف تیم؛ سازگاری واقعی نسخه‌ها و افزونه‌ها روی staging، نه فرض سازگاری همه. فعال‌سازی feature آزمایشی، conversion Classic/Atomic و upgrade بدون درخواست ممنوع.
- Theme پایه و Theme Builder در صورت استفاده دارای مالکیت مشخص هدر، فوتر، single و archive باشند؛ دو renderer هم‌زمان نساز. قالب Canvas/Full Width یا templateهای خاص فقط برحسب نیاز. fallback و شرایط نمایش هر template آزمایش شوند. CPT/داده پایدار در plugin؛ vendor و core مستقیم تغییر نکنند.
- ابتدا native widget/control/template/global styles کافی را استفاده کن؛ custom widget فقط برای رفتار/ظاهر واقعاً لازم. محتوای اصلی و رابط مدیریت موجود حفظ شود. schema/IDs داده‌های ذخیره‌شده، dynamic tags، responsive overrides، preview و localization با تغییر کد نشکنند.

## 2. Design System و CSS

- Site Settings / Global Colors / Global Fonts یا registry مصوب سایت منبع مالک باشد. هر رنگ/فونت ثابت در تک تک widgetها کپی نشود؛ native references به global style ترجیح دارد. برای custom widgetها از API بومی کنترل‌های typography/colors و مقادیر global استفاده کن؛ CSS custom properties محلی در صورت نیاز، با mapping یک‌طرفه مشخص. دو پنل مستقل برای تنظیم یک رنگ/فونت ایجاد نکن.
- 02/03/16 برای کد خودمان: CSS scope به root ویجت، variantها، Flex/Grid مناسب و RTL؛ DOM بومی موردنیاز ویرایشگر را بی‌دلیل حذف نکن. از selector عمومی علیه همه `.elementor-*` و `!important` انبوه دوری کن. CSS editor و front جدا؛ جلوگیری از Google Fonts/face تکراری یا فونت‌های بدون استفاده با روش سازگار و آزمون؛ حذف وابستگی داخلی خودسرانه ممنوع.
- Containerهای بومی، responsive controls و layout مشترک اولویت؛ دو سکشن کامل desktop/mobile با hide نامناسب‌اند. Widgetهای تکرارشونده با renderer مشترک و داده ورودی، نه copy/paste HTML و CSS. Template reuse و component primitive وقتی واقعاً یک family هستند؛ برای طراحی ویژه، variant محدود و scope‌شده. دکمه، کارت، input، skeleton مطابق 16؛ رفتار editor preview را حفظ کن.

## 3. Assets و Lifecycle ویجت

نسخه، manifest، به‌روزرسانی assets و اندازه‌گیری کش تابع [قرارداد build](../shared/08-BUILD-AND-VERSIONS.md) و [قرارداد کش](../shared/09-PERFORMANCE-AND-CACHE.md) هستند. وابستگی ویجت در front و iframe پیش‌نمایش لازم است. asset مختص پنل ادیتور با hook ادیتور ثبت و مصرف شود.

- دارایی widget را با `wp_register_script()` / `wp_register_style()` ثبت کن، سپس handleهای لازم را از `get_script_depends()` / `get_style_depends()` برگردان. dependencyهای لازم از جمله `elementor-frontend` در صورت نیاز صحیح ثبت شوند؛ فایل‌های widget در صفحات بدون آن نباید بی‌دلیل سراسری enqueue شوند. CSS عمومی فقط موارد واقعاً مشترک؛ editor-only با API/hook مربوط. مثال قرارداد؛ نسخه نصب شده و روش ثبت/مسیر plugin خود پروژه تعیین می‌کند.

```php
public function get_script_depends(): array {
    return array( 'client-custom-widget' );
}

public function get_style_depends(): array {
    return array( 'client-custom-widget' );
}
```

- handler frontend با lifecycle رسمی نسخه نصب‌شده؛ برای Classic widget، `frontend/element_ready/{widget-name}.default` یا روش handler رسمی، root همان instance. ثبت hook پس از آماده شدن runtime و یک‌بار؛ `$scope` در API ممکن است jQuery object باشد، adapter مناسب؛ منطق اختصاصی می‌تواند Vanilla JS باشد. dependency داخلی runtime را به اسم Vanilla حذف نکن. در editor/preview، render مجدد و destroy باید listener/observer/timer و GSAP context قبلی را آزاد کند؛ callback دیرهنگام به root حذف‌شده وصل نشود. برای Atomic از API رسمی همان نسخه و capability check استفاده کن، نه فرض یکسان بودن Classic lifecycle.
- هر instance state مستقل؛ runtime و کتابخانه یک بار. کاروسل/منو/فرم موجود با API معتبر reuse شود؛ نسخه دوم GSAP، Swiper یا scroll manager اضافه نشود. native motion و GSAP روی یک property/عنصر هم‌زمان مالک نباشند. GSAP مشترک 05 برای کد ما؛ سراسری ScrollSmoother/curtain روی builder DOM تحمیل نشود. Loading محلی/Skeleton به درخواست واقعی وصل شود؛ page transition موجود تنها مالک چرخه نمایش و scroll lock باشد. Reduced motion و preview بدون پرده مسدودکننده.
- Build/Vite اختیاری فقط برای moduleهای متعلق به تیم؛ Dev server در prod ممنوع. manifest و chunk dependencyهای معتبر؛ CSS SSR ضروری قبل از paint. تنظیمات Performance/Cache خودکار فعال/غیرفعال نشوند؛ اثر هر گزینه در staging با محتوای واقعی اندازه‌گیری شود.

### Hub و تنظیم ورود محتوا در Elementor

- روی Container منتخب، `Animations & Parallax → Animate contents = On`، `Animation Targets = All contents` و `Include inner containers content? = On` مطابق مرجع ارسالی. انتخاب `All contents` فقط در کانتینری انجام شود که همهٔ محتوای آن برای ورود انتخاب شده است. اعداد دقیق `Settings` و `Animate from` در [پروفایل مشترک ورود مشتری](../shared/05-GSAP-ANIMATION-STANDARD-v1.1.md#customer-content-entrance) ثبت شده‌اند.
- `Play animations` برای پیش‌نمایش ویرایشگر است. اولین paint، ورود با اسکرول، opacity اولیه و خوانایی فارسی در frontend واقعی هم بررسی شوند. برای همین پروفایل `Parallax = Off` است. اگر طرح پارالاکس جدا دارد، آن را روی لایهٔ مستقل فعال کن و مالکیت transform را به همان لایه بده. تنظیمات بومی Hub کفایت می‌کند و اجرای دوبارهٔ همان افکت با اسکریپت تیم لازم نیست.

## 4. تست و تحویل

- [ ] نسخه نصب‌شده و license/requirements ثبت؛ PHP lint و build؛ frontend و editor/preview، اضافه/حذف/duplicate widget، responsive controls، ذخیره/بازگشایی، dynamic content، حالت RTL/LTR و خطاهای console.
- [ ] صفحه بدون widget سنگین در Network منابع مخصوص آن را دریافت نمی‌کند، مگر dependency مشترک لازم و مستند؛ تکرار چند widget باعث listener/trigger/CSS تکراری نمی‌شود.
- [ ] Global Colors/Fonts تغییر آزمایشی در staging و propagation صحیح؛ بدون hardcode تکراری، بدون تغییر داده مشتری یا حذف style لازم. هدر/فوتر/SEO یک مالک.
- [ ] Accessibility، keyboard، loading/error، reduced motion و mobile؛ CWV/bytes before-after در محیط یکسان یا Not run. rollback، cache purge مورد نیاز و نسخه‌ها در Runbook پروژه، نه استاندارد عمومی.

## منابع رسمی

- [Elementor widget dependencies](https://developers.elementor.com/docs/widgets/widget-dependencies/)، [Widget scripts](https://developers.elementor.com/docs/scripts-styles/widget-scripts/)، [Widget styles](https://developers.elementor.com/docs/scripts-styles/widget-styles/)
- [Global styles](https://developers.elementor.com/docs/data-structure/global-styles/)، [Site settings](https://elementor.com/help/site-settings/)، [Requirements](https://elementor.com/help/requirements/)
- [JS hooks](https://developers.elementor.com/docs/hooks/js.html)، [Widget JavaScript](https://developers.elementor.com/add-javascript-to-elementor-widgets/)

منبع داخلی: radman-doc/engineering/elementor.md، architecture.md، typography.md و components.md برای اصول تفکیک ownership و preview؛ ویژگی‌های محصول قبلی، کلیدها و شماره نسخه‌ها به این سند عمومی منتقل نشده‌اند.

## تکمیل قرارداد media و SEO
[17](../shared/17-IMAGES-AND-MEDIA-v1.0.md) برای نقش و اولویت تصویر و [نام فایل و Alt](../shared/17-IMAGES-AND-MEDIA-v1.0.md#image-naming-and-alt)، [20](../shared/20-TECHNICAL-SEO-SCHEMA-v1.0.md#image-seo) برای سئوی تصویر. تصویر شاخص فقط وقتی dynamic tag/template شرط مصوب دارد در Hero نمایش داده شود. کنترل‌های image بومی و attachment ID/widget renderer متناسب با نسخه؛ editor data و Global Styles حفظ شود. Alt را در HTML رندرشدهٔ frontend کنترل کن. موتور رندر یا carousel دوم اضافه نشود. تست frontend/preview؛ wrapper/asset خود builder کوکورانه حذف نشود.
