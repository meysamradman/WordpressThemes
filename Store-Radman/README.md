# Store Radman
فروشگاه مستقل وردپرس + ووکامرس با قالب سفارشی سبک و وبلاگ. این نسخه فقط پایهٔ توسعه است و طراحی نهایی ندارد.

- شروع: WordPress-Start.cmd
- توقف با حفظ داده‌ها: WordPress-Stop.cmd
- وضعیت: WordPress-Status.cmd
- سایت: http://127.0.0.1:8100/
- مدیریت: http://127.0.0.1:8100/wp-admin/
- نام کاربری و رمز اولیه: artifacts/local-wordpress-access.txt (خصوصی، خارج از Git).
- نصب نخست افزونه‌ها اینترنت می‌خواهد؛ اجراهای بعدی محتوا، رمز و تنظیمات را بازنشانی نمی‌کنند.
- افزونه‌ها: WooCommerce، Yoast SEO و All-in-One WP Migration از مخزن رسمی. مجوز تجاری یا افزونهٔ پولی نصب نشده است.
- .env.local-wordpress و volumeها را نگه دارید؛ Stop فقط متوقف می‌کند. از down -v استفاده نکنید.
- هیچ دیتابیس یا رسانه‌ای از پروژه‌های دیگر وارد نشده است.

مرجع اجرا: docs/architecture.md و ../WEB-DEVELOPMENT-STANDARDS/00-STANDARDS-INDEX-v1.3.md.
