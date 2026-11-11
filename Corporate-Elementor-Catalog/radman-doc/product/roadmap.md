# کارهای باقی‌مانده

این سند فقط کار یا تصمیم باز را نگه می‌دارد؛ گزارش کارهای انجام‌شده و خروجی تست در آن نوشته نشود. تکمیل یک مورد، همان ردیف را حذف/اصلاح می‌کند. جزئیات در مالک فنی می‌ماند؛ تست بلند و مرورگر فقط با درخواست مالک اجرا می‌شوند.

## ادامهٔ تکمیل

| وضعیت | کار مشخص | مالک |
|---|---|---|
| OPEN | دیزاین بعدی Child و صفحات محتوا طبق ورودی مالک؛ تعیین URL نهایی CTAهای Home و سیاست رنگ بوم تیره باز است. ایرادهای بصری فعلی فقط در همان scope اصلاح شوند. | [مشتری](../engineering/client-customization.md)، [محتوا](../scenarios/content.md) |
| OPEN | پذیرش واقعی Classic/Atomic و کتابخانه: بازکردن، درج، ذخیره/بازخوانی، Undo و خطای React 130 گزارش‌شده؛ موفقیت build/schema به معنی بسته‌شدن نیست. | [Elementor](../engineering/elementor.md)، [کتابخانه](../engineering/component-library.md) |
| OPEN | شکاف‌های مشخص کنترل‌های بومی و برابری Text/Carousel و دکمه؛ فقط موارد بازِ inventory، نه بازنویسی قابلیت‌های حاضر. | [کامپوننت‌ها](../engineering/components.md)، [Text](../engineering/text.md)، [Carousel](../engineering/carousel.md) |
| OPEN | پذیرش موبایل، RTL/LTR، کیبورد/Focus/Escape، reduced motion، sticky و Back/Forward آرشیوها در کاربرد واقعی. | [سایدبار](../engineering/sidebars.md)، [AJAX](../engineering/archive-ajax.md)، [ناوبری](../engineering/navigation.md) |
| OPEN | پذیرش تعاملی کنترل‌های عمومی و ویجت‌های Taxonomy/Search در Classic/Atomic و صفحه‌خوان، IME و مرورگرهای هدف؛ build/ثبت/schema جای این پذیرش نیست. | [کامپوننت‌ها](../engineering/components.md#کنترلها-و-داده-مشترک) |
| OPEN | ویجت‌های ترکیبی فیلتر/نتایج و اتصال صریح به مقصد مشترک؛ adapter و پذیرش Loop Grid بومی Pro جدا از AJAX اختصاصی، بدون query/History موازی. | [renderer](../engineering/renderers.md)، [AJAX](../engineering/archive-ajax.md) |
| OPEN | بازبینی budget قدیمی ورودی‌های مدیریت در `tests/assets.test.mjs` با حجم/graph واقعی، بدون بالا بردن بی‌دلیل سقف؛ تکمیل ترجمهٔ موجود `Font family` در catalog ترجمه‌ها. | [دارایی‌ها](../engineering/assets.md)، [مدیریت](../engineering/admin-ui.md) |
| OPEN | تفکیک واقعی رندر/requirements هدر موبایل از دسکتاپ؛ وضعیت فعلی بخشی از DOM را با CSS پنهان و assets را از کل layout جمع می‌کند. راهبرد کش و تغییر viewport پیش از اجرای تفکیک روشن شود. | [سازنده](../engineering/builder.md#مرز-نمای-موبایل-و-منابع) |
| OPEN | پاک‌سازی محدود قواعد فیلتر افقی قدیمی و تصمیم دربارهٔ گروه‌بندی فایل‌های همیشه‌همراه بر مبنای اندازه‌گیری، بدون شکستن مرز ماژول‌ها. | [دارایی‌ها](../engineering/assets.md) |
| OPEN؛ پس از درخواست بررسی | سازگاری نسخه‌های PHP/DB هدف، نصب/آپدیت بسته‌های مستقل، کش و افزونه‌های منتخب؛ بررسی کوتاه جای پذیرش میزبان نیست. | [سازگاری](../scenarios/compatibility.md)، [معماری](../engineering/architecture.md)، [نصب افزونه](../engineering/plugin-installer.md) |
| OPEN؛ پس از درخواست بررسی | baseline سرعت با نسخه/داده/هاست مشخص، کش سرد/گرم، Network/Coverage، درخواست‌ها و TTFB؛ بودجهٔ فایل‌ها پس از سنجش تعیین شود. اهداف LCP≤2.5s، INP≤200ms و CLS≤0.1 پیشنهادی‌اند، نه نتیجهٔ تأییدشده. | [دارایی‌ها](../engineering/assets.md) |
| OPEN؛ اتصال انتخابی مالک | پذیرش اتصال کلاینت MCP و مرز Elementor؛ موفقیت ابزار محلی به معنی اثبات transport نیست. | [MCP](../engineering/mcp.md) |

## وابسته به تصمیم بعدی یا موکول‌شده

| وضعیت | موضوع | مالک |
|---|---|---|
| DEFERRED | WPML: ادامهٔ نگاشت اختصاصی، ترجمهٔ مستقل/متصل، رسانه، کش زبان و پذیرش ATE/Undo؛ فعلاً اولویت اجرای جاری نیست. | [زبان‌ها](../scenarios/languages.md)، [Elementor](../engineering/elementor.md) |
| OPEN؛ تصمیم محصول | PDF، schema/گروه‌بندی پیشرفتهٔ مشخصات و فیلترهای پیشرفتهٔ کاتالوگ. | [محتوا](../scenarios/content.md)، [ماژول‌ها](../engineering/content-modules.md) |
| DEFERRED؛ پس از درخواست مالک | طراحی نهایی مگامنو و محتوای غنی/تصویر، با انتخاب نوع هر آیتم و امکان ترکیب با زیرمنوی ساده؛ metadata و اتصال موجود حفظ شوند. | [ناوبری](../engineering/navigation.md#زیرمنوی-ساده-و-مسیر-مگامنو) |
| DEFERRED | فرم، ثبت/ارسال درخواست و اعلان‌ها؛ کمبود نسخهٔ فعلی محسوب نشوند. | [محتوا](../scenarios/content.md) |
| خارج از ادامهٔ خودکار | افزایش نسخه، ZIP نهایی، Git save، انتقال و انتشار؛ فقط با درخواست صریح همان اقدام. | [قواعد پروژه](../../AGENTS.md) |

فروشگاه/WooCommerce، سبد/پرداخت، لایسنس اختصاصی، Headless/React SPA/سرور Node و فیلدساز عمومی جزو backlog این محصول نیستند؛ مرجع دامنه [معماری](../engineering/architecture.md) است.
