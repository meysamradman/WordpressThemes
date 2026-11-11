# Radman CT / رادمان

نام محصول **Radman CT — Corporate & Catalog** است؛ [قرارداد نام‌گذاری و سازگاری داده‌های قبلی](radman-doc/engineering/naming.md).


قالب مستقل وردپرس برای سایت شرکتی و کاتالوگ؛ پایه نسخه ۰٫۱٫۰، Child Theme نمونه، پنل تنظیمات، هدر/فوترساز و بلوک مشترک اولیه ساخته شده‌اند. نشانگر ماوس، پیشرفت اسکرول و پری‌لودر اختیاری و ماژولارند؛ Core اختیاری با دو کلید مستقل محصول/نمونه‌کار، مدیریت اطلاعات و نمایش PHP پایه نیز ساخته شده؛ روش طراحی single/archive هر بخش بین اختصاصی و Elementor Pro انتخاب می‌شود. اتصال MCP در نقشه راه مرحلهٔ بعد است.

معماری قطعی: PHP Server Rendering، HTML5، Modern CSS، Vanilla JS، انیمیشن محلی اختیاری GSAP/ScrollTrigger و Vite توسعه؛ React برای مدیریت و سازنده‌ها. داده کاتالوگ اختیاری در افزونه مستقل Radman CT Content و نمایش در Theme؛ اجرای مشتری فقط PHP/دیتابیس و بدون Node. WooCommerce و فروشگاه کاملاً خارج از محصول‌اند.

- [مستندات و سناریوها](radman-doc/README.md)
- [راهنمای agent](AGENTS.md)
- [تغییر استایل و صفحه برای مشتری](radman-doc/engineering/client-customization.md)
- [وضعیت پیاده‌سازی و بررسی‌های انجام‌شده](radman-doc/implementation/foundation.md)

توسعه: `npm ci` سپس `npm run build`. بسته نصب: `npm run package`، خروجی در `artifacts/`. هاست مشتری فقط PHP/دیتابیس نیاز دارد؛ Node فقط توسعه است. بررسی محدود PHP/DB با Elementor/Pro ارسالی انجام شده؛ مرورگر/نصب مشتری تأیید نشده و گزارش دقیق در سند پیاده‌سازی است.

مخزن workflow یا انتشار/استقرار خودکار ندارد؛ stage/commit/push با کاربر است. نمونه‌های Hub و WoodMart فقط محلی‌اند و در Git قرار نمی‌گیرند. `radmanct/` والد و `radmanct-child/` لایه مستقل سفارشی‌سازی مشتری است. بررسی عادی کوتاه و مرتبط باشد؛ تست طولانی خودکار تکرار نشود.

برای محصول یا نمونه‌کار، ZIP افزونهٔ `radmanct-content-0.1.6.zip` را نصب و فقط ماژول لازم را در «هستهٔ رادمان» فعال کنید. سپس در «رادمان ← طراحی محتوا» روش آرشیو/جزئیات را انتخاب کنید. طرح المنتوری باید در Theme Builder پرو شرایط نمایش منطبق داشته باشد؛ در نبود آن طراحی اختصاصی نمایش داده می‌شود. [راهنمای ماژول‌ها](radman-doc/engineering/content-modules.md) و [روش طراحی](radman-doc/engineering/renderers.md).

کامپوننت‌های مشترک PHP/Classic/Atomic در افزونهٔ مستقل **Radman CT Components** قرار دارند. همراه قالب 0.1.38، افزونهٔ `radmanct-components-0.1.22.zip` را نصب و فعال کنید؛ Image در افزونه و اسکرول افقی ثابت در موشن کانتینر قالب قرار دارد. [مالکیت و حدود کامپوننت‌ها](radman-doc/engineering/components.md) و [موشن کانتینر](radman-doc/engineering/container-motion.md).

نسخهٔ بسته‌های فعلی: Theme 0.1.38، Content 0.1.6، Components 0.1.22، MCP 0.1.3 و Child 0.1.1. قرارداد JavaScript: سورس ES2022+، خروجی محلی minified با Vite 8.3.4 و هدف ES2020، GSAP دقیقاً 3.15.0 و شرطی، بدون dependency مستقیم jQuery. مدیریت از React وردپرس با `wp-element` استفاده می‌کند؛ React مستقل نصب نشده و نمایش عمومی به React نیاز ندارد. [قرارداد منابع](radman-doc/scenarios/local-assets.md) و [React مدیریت](radman-doc/engineering/admin-ui.md).

جدول عمومی با نام Radman CT table در کتابخانه و Elementor Classic/Atomic موجود است؛ برای PHP از rdct_table استفاده کنید. دادهٔ ستون/ردیف دلخواه، سه ظاهر و روش مصرف در [قرارداد Table](radman-doc/engineering/components.md) ثبت است.
