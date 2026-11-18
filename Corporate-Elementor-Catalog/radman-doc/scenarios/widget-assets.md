# سناریوی دارایی ویجت Elementor

**دامنه:** ویجت‌ها و adapterهای Radman در فرانت، iframe پیش‌نمایش و پنل ادیتور. قرارداد نسخه از [پروفایل](../product/profile.md)، مالک ثبت و build از [دارایی‌ها](../engineering/assets.md) و نحوهٔ اتصال از [قرارداد Elementor](../engineering/elementor.md) خوانده شوند.

1. در صفحهٔ عمومی دارای ویجت، فقط CSS/JS ضروری ویجت و وابستگی‌های ثبت‌شده بارگیری شوند. دو نمونهٔ ویجت یک dependency مشترک را دو بار اجرا نکنند.
2. در صفحهٔ بدون ویجت، asset اختصاصی آن درخواست نشود. فایل مختص پنل ادیتور به فرانت و iframe پیش‌نمایش سرایت نکند.
3. در ادیتور، درج/ویرایش/حذف و رندر مجدد را روی خانوادهٔ واقعاً مصرف‌شده بررسی کنید. listener، observer و tween هر instance پاک‌سازی شوند. هنگام تغییر یک مالک مشترک، مسیر Classic و Atomic هر دو بررسی شوند.
4. پس از build یا تغییر کش، فایل CSS تولیدی و manifest در فرانت و Preview معتبر باشند. تنظیمات و شناسه‌های ذخیره‌شده و نمایش محتوای قبلی حفظ شوند.
5. در تغییر [Input](../../../WEB-DEVELOPMENT-STANDARDS/shared/16-DESIGN-SYSTEM-COMPONENTS-v1.0.md#ui-input) یا [Card](../../../WEB-DEVELOPMENT-STANDARDS/shared/16-DESIGN-SYSTEM-COMPONENTS-v1.0.md#ui-card)، کنترل بومی ادیتور و نشانه‌گذاری/استایل فرانت هرکدام از مالک موجود خوانده شوند. داده و variant به renderer خانواده برسد و برای کاروسل یا preview نسخهٔ تکراری از CSS/JS ساخته نشود.

معیار پذیرش، بررسی درخواست‌های Network و رفتار ویجت در نسخهٔ هدف است. [مستند رسمی Elementor](https://developers.elementor.com/docs/widgets/widget-dependencies/)
