# Radman — Elementor edition

طراحی محتوا با Elementor؛ تکی/آرشیو با Elementor Pro Theme Builder. هدر و فوترساز بومی، تنظیمات عمومی و کتابخانهٔ Radman حفظ شده‌اند. مسیر طرح PHP مشتری و بستهٔ Child حذف شده‌اند. [قرارداد فعلی](radman-doc/product/essentials.md).

# Radman CT / رادمان

نام محصول **Radman CT — Corporate & Catalog** است؛ [قرارداد نام‌گذاری و سازگاری داده‌های قبلی](radman-doc/engineering/naming.md).



معماری قطعی: PHP Server Rendering، HTML5، Modern CSS، Vanilla JS، انیمیشن محلی اختیاری GSAP/ScrollTrigger و Vite توسعه؛ React برای مدیریت و سازنده‌ها. داده کاتالوگ اختیاری در افزونه مستقل Radman CT Content و نمایش در Theme؛ اجرای مشتری فقط PHP/دیتابیس و بدون Node. WooCommerce و فروشگاه کاملاً خارج از محصول‌اند.

- [مستندات و سناریوها](radman-doc/README.md)
- [راهنمای agent](AGENTS.md)
- [تغییر استایل و صفحه برای مشتری](radman-doc/engineering/client-customization.md)
- [وضعیت پیاده‌سازی و بررسی‌های انجام‌شده](radman-doc/implementation/foundation.md)

توسعه: `npm ci` سپس `npm run build`. بسته نصب: `npm run package`، خروجی در `artifacts/`. هاست مشتری فقط PHP/دیتابیس نیاز دارد؛ Node فقط توسعه است. بررسی محدود PHP/DB با Elementor/Pro ارسالی انجام شده؛ مرورگر/نصب مشتری تأیید نشده و گزارش دقیق در سند پیاده‌سازی است.


برای محصول یا نمونه‌کار، ZIP افزونهٔ `radmanct-content-0.1.6.zip` را نصب و فقط ماژول لازم را در «هستهٔ رادمان» فعال کنید. سپس در «رادمان ← طراحی محتوا» روش آرشیو/جزئیات را انتخاب کنید. طرح المنتوری باید در Theme Builder پرو شرایط نمایش منطبق داشته باشد؛ در نبود آن طراحی اختصاصی نمایش داده می‌شود. [راهنمای ماژول‌ها](radman-doc/engineering/content-modules.md) و [روش طراحی](radman-doc/engineering/renderers.md).

کامپوننت‌های مشترک PHP/Classic/Atomic در افزونهٔ مستقل **Radman CT Components** قرار دارند. همراه قالب 0.1.38، افزونهٔ `radmanct-components-0.1.22.zip` را نصب و فعال کنید؛ Image در افزونه و اسکرول افقی ثابت در موشن کانتینر قالب قرار دارد. [مالکیت و حدود کامپوننت‌ها](radman-doc/engineering/components.md) و [موشن کانتینر](radman-doc/engineering/container-motion.md).


جدول عمومی با نام Radman CT table در کتابخانه و Elementor Classic/Atomic موجود است؛ برای PHP از rdct_table استفاده کنید. دادهٔ ستون/ردیف دلخواه، سه ظاهر و روش مصرف در [قرارداد Table](radman-doc/engineering/components.md) ثبت است.
