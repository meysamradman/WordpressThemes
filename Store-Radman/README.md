# Store Radman
فروشگاه مستقل وردپرس + ووکامرس با قالب سفارشی سبک و وبلاگ. این نسخه فقط پایهٔ توسعه است و طراحی نهایی ندارد.

- شروع: WordPress-Start.cmd
- توقف با حفظ داده‌ها: WordPress-Stop.cmd
- وضعیت: WordPress-Status.cmd
- سایت: http://127.0.0.1:8100/
- مدیریت: http://127.0.0.1:8100/wp-admin/
- نام کاربری و رمز اولیه: artifacts/local-wordpress-access.txt (خصوصی، خارج از Git).
- اطلاعات ورود در .env.local-wordpress با STORE_ADMIN_URL، STORE_ADMIN_USER و STORE_ADMIN_PASSWORD ثبت شده است. این مقادیر با رمز دیتابیس فرق دارند. پس از تغییر دستی رمز در وردپرس، نسخهٔ خصوصی اطلاعات ورود را نیز به‌روز کنید؛ Start رمز موجود را بازنشانی نمی‌کند.
- نصب نخست افزونه‌ها اینترنت می‌خواهد؛ اجراهای بعدی محتوا، رمز و تنظیمات را بازنشانی نمی‌کنند.
- افزونه‌ها: WooCommerce، Yoast SEO، All-in-One WP Migration و افزونهٔ رسمی زرین‌پال از مخزن وردپرس. بدون شناسهٔ پذیرنده، روش پرداخت خاموش و Sandbox آماده است؛ تراکنش واقعی هنوز آزموده نشده است.
- .env.local-wordpress و volumeها را نگه دارید؛ Stop فقط متوقف می‌کند. از down -v استفاده نکنید.
- اسنپ‌پی رسمی WooCommerce 1.4.2 نصب اما غیرفعال است؛ موانع SSL، ارتباط خارجی activation، پرداخت بلوکی و update دستی در docs/wordpress.md ثبت شده‌اند. فعلاً برای تراکنش آماده نیست.
- هیچ دیتابیس یا رسانه‌ای از پروژه‌های دیگر وارد نشده است.

مرجع اجرا: docs/architecture.md و ../WEB-DEVELOPMENT-STANDARDS/00-STANDARDS-INDEX-v1.3.md.
قرارداد مسیرها، کارت مشترک محصول، CSS مصرف‌کننده، ارتباط‌های خارجی، Yoast/Analytics و سبد/پرداخت/حساب: docs/wordpress.md.
