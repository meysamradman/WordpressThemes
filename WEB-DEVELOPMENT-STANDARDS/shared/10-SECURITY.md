# امنیت کد WordPress

> بازبینی بسته 1.3 — 2026-11-19؛ نام مسیر برای سازگاری ارجاع‌های موجود حفظ شده است. تاریخچه در CHANGELOG-v1.3.md.

- ورودی را بر اساس دادهٔ مورد انتظار اعتبارسنجی و خروجی را در محل مصرف escape کنید. برای query از API بومی و SQL ضروری از prepare استفاده کنید. متن جست‌وجو در HTML و attribute مطابق زمینه escape شود. برداشت کاربر از نتیجهٔ خالی در سناریوی همان پروژه ثبت شود.
- عملیات مدیریت و نوشتن به capability مرتبط با منبع و nonce مناسب نیاز دارد. برای REST، مجوز دسترسی در `permission_callback` مشخص شود. ورودی را داخل DOM یا HTML خام قرار ندهید. URL embed و دانلود از منبع/پروتکل مجاز ساخته شود.
- پیش‌نمایش، نوشتهٔ خصوصی/رمزدار، attachment و خروجی شخصی‌سازی‌شده باید در HTML، API و کش رعایت شوند. دسترسی URL مستقیم رسانهٔ واقعاً خصوصی به کنترل سرو فایل نیاز دارد.
- رمزها، dump و تنظیمات سرور خارج از ZIP عمومی و Git بمانند. محدودیت ورود، WAF و مالکیت فایل در راهنمای هاست ثبت شوند. امنیت قالب، نصب وردپرس، افزونه‌ها و هاست جداگانه قابل بررسی‌اند.

[WordPress security](https://developer.wordpress.org/apis/security/) · [راهنمای سخت‌سازی](https://developer.wordpress.org/advanced-administration/security/hardening/)

## کنترل‌های تکمیلی
- shape/type و allowlist پیش از sanitize؛ late context escaping. nonce فقط CSRF نه مجوز؛ در REST/AJAX object-level capability/ownership، public endpoint با محدودسازی ورودی و داده، rate limit متناسب و عدم تغییر state با GET.
- جلوگیری از stored/reflected XSS، DOM XSS (textContent به‌جای innerHTML نامطمئن)، SQL injection (prepare + whitelist identifier)، SSRF (safe WP HTTP + مقصد مجاز، محدودیت redirect/timeout/size)، upload با بررسی MIME/extension و مجوز؛ SVG نامطمئن ممنوع.
- auth و secret، error log و PII، cache private، direct file access و دانلود محافظت‌شده؛ تست منفی مهمان، نقش کم‌اختیار، ورودی array/طول نامعتبر، nonce غایب/منقضی، XSS payload بی‌خطر در محیط تست. امنیت کلی runtime بدون بررسی host/plugins قابل تضمین نیست.
- [مدیا](17-IMAGES-AND-MEDIA-v1.0.md)، [PHP](13-PHP-DEVELOPMENT-STANDARD-v1.1.md)، [داده](18-DATABASE-QUERY-OPTIMIZATION-v1.0.md) و [انتشار](21-DEPLOYMENT-DATA-MIGRATION-v1.0.md) مالک جزئیات مربوط‌اند.
