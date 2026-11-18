# پروفایل Corporate-Elementor-Catalog

| موضوع | قرارداد و منبع |
|---|---|
| نوع و مالکیت | Elementor برای صفحه‌ها و قالب محتوا، سازندهٔ بومی Radman برای هدر/فوتر و Components برای ویجت‌ها و کتابخانه. Classic/V3 و Atomic داده و شناسهٔ موجود خودشان را حفظ می‌کنند. [خلاصهٔ محصول](essentials.md) · [معماری](../engineering/architecture.md) |
| نسخه و محیط | [جدول نسخه‌ها](../../../WEB-DEVELOPMENT-STANDARDS/00-VERSIONS.md). محیط `radman-local` از [Compose](../../tools/local-wordpress/compose.yaml) می‌آید. در تغییر API، نسخهٔ نصب‌شده و فعال‌بودن قابلیت Atomic را برای همان نصب بررسی کنید. |
| ساخت | [package.json](../../package.json) و lockfile مبنای Vite/GSAP و سایر ابزارها هستند. `inc/assets.php` و manifest هر بسته مصرف CSS/JS را تعیین می‌کنند. [دارایی‌ها](../engineering/assets.md) |
| میزبان و کش | نسخهٔ PHP وب، DB، افزونه‌ها، کش/TTL و CDN برای هر نصب تأیید شوند. [راهنمای سرور تست](../engineering/test-server.md) و [استاندارد کش](../../../WEB-DEVELOPMENT-STANDARDS/shared/09-PERFORMANCE-AND-CACHE.md) |

برای کار Elementor از [استاندارد مسیر Elementor](../../../WEB-DEVELOPMENT-STANDARDS/wordpress-elementor/15-WORDPRESS-ELEMENTOR-DEVELOPMENT-v1.0.md) و [فهرست مالک‌های پروژه](../README.md) استفاده کنید.
