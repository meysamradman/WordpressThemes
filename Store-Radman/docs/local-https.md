# HTTPS لوکال فروشگاه

آدرس اصلی https://127.0.0.1:8143/ و مدیریت همان آدرس با /wp-admin/ است. HTTP قدیمی روی 8100 به HTTPS هدایت می‌شود. هر دو پورت فقط روی loopback ویندوز bind شده‌اند؛ دسترسی عمومی یا HTTPS هاست از این تنظیم نتیجه نمی‌شود.

Apache همان کانتینر موجود TLS را ارائه می‌کند؛ سرویس proxy یا image جدید لازم نیست. tools/local-wordpress/apache.conf مالک TLS است. MySQL، volumeها، افزونه‌ها و اطلاعات ورود حفظ می‌شوند. STORE_SITE_URL در فایل خصوصی محیط و WP_HOME/WP_SITEURL باید HTTPS یکسان داشته باشند. FORCE_SSL_ADMIN فعال است.

## گواهی

mkcert رسمی از [مخزن اصلی](https://github.com/FiloSottile/mkcert) تهیه می‌شود. ابزار محلی در .cache/mkcert.exe نگه داشته می‌شود؛ اگر جای دیگری نصب شده است، به tls.ps1 با -MkcertPath مسیر بدهید. اجرای tools/local-wordpress/tls.ps1 گواهی localhost، 127.0.0.1 و ::1 را می‌سازد و CA را فقط در Root کاربر فعلی ویندوز ثبت می‌کند. نصب machine-wide یا خاموش‌کردن بررسی SSL لازم نیست. ممکن است مرورگر باز برای شناسایی trust جدید نیاز به راه‌اندازی دوباره داشته باشد.

گواهی/کلید در .cache/local-tls/server و CA در .cache/local-tls/ca هستند؛ همه خارج Git و بستهٔ هاست. کلید خصوصی CA هرگز به Docker mount نمی‌شود. فقط گواهی عمومی CA و کلید/گواهی سرور به کانتینر می‌روند. پوشهٔ .cache/local-tls را هنگام پاک‌سازی cache حذف نکنید؛ Start عمداً در نبود گواهی متوقف می‌شود. برای تمدید، tls.ps1 و سپس Start اجرا شود. گواهی‌ها مخصوص توسعه‌اند؛ روی سرور منتشر نشوند.

CA عمومی به trust سیستم کانتینر اضافه می‌شود. چون WordPress ممکن است bundle جدا مصرف کند، local.php فقط برای همان host/port محلی sslcertificates را روی CA محلی قرار می‌دهد و sslverify=true باقی می‌ماند. اتصال‌های خارجی تغییر نمی‌کنند. localhost داخل کانتینر با پورت 8143 نیز همان Apache است تا loopback و Cron بدون دورزدن اعتبارسنجی کار کنند.

## جابه‌جایی آدرس

فقط یک‌بار پیش از تغییر، بکاپ دیتابیس مستقل بگیرید. با WP-CLI search-replace و --all-tables-with-prefix --skip-columns=guid ابتدا --dry-run و سپس اجرای واقعی، http://127.0.0.1:8100 را به https://127.0.0.1:8143 تبدیل کنید. این روش serialized data را حفظ می‌کند؛ SQL replace خام یا reset داده ممنوع است. GUIDها تغییر نکنند. Start این عملیات مهاجرت را خودکار تکرار نمی‌کند و رمزها را تغییر نمی‌دهد.

کنترل کوتاه: گواهی با اعتبارسنجی روشن، redirect قدیمی، مدیریت/REST/تصویر/سبد و loopback با WordPress HTTP API. اطلاعات ورود، سفارش و موجودی نباید بازنشانی شوند. HTTPS لوکال مشکل خاموش‌کردن SSL در vendor اسنپ‌پی یا سازگاری Checkout Blocks آن را حل نمی‌کند؛ افزونه فعلاً غیرفعال می‌ماند.
