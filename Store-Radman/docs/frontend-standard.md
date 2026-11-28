# پروفایل Store Radman
## هویت و محیط
قالب مستقل PHP classic و سفارشی با ووکامرس، بدون Child یا Elementor؛ شناسه radmanstore، namespace برابر RadmanStore و پیشوند rds-. فقط فروشگاه و وبلاگ.
محیط ثبت‌شده: WordPress 7.1.3، PHP 8.4.26، MySQL 8.4.13، WooCommerce 11.3.1، Yoast SEO 28.9 و All-in-One WP Migration 7.114. نسخه‌ها وضعیت نصب اولیه هستند، نه pin افزونه‌ها؛ به‌روزرسانی دستی باز است.

## مالک‌های مشترک داخل قالب
- assets/tokens.css: فونت Peyda محلی، رنگ، فاصله، شعاع؛ assets/base.css: تایپوگرافی پایه و کانتینرهای ریسپانسیو RTL/LTR.
- header.php / footer.php: layout بومی با wp_head، wp_body_open و wp_footer؛ یک H1 در هر صفحه.
- template-parts/blog/card.php: کارت مقالات؛ home.php، archive.php، search.php از blog.php مشترک استفاده می‌کنند. single.php جزئیات با محافظت رمز، کاور responsive و دیدگاه بومی است.
- inc/pagination.php، assets/pagination.css و assets/pagination.js: یک renderer برای عددی و نمایش بیشتر؛ وبلاگ عددی، آرشیو محصول اسکرول/کلیک. fallback بومی، URL واقعی صفحه، جلوگیری از درخواست موازی، اعلام loading/error و retry حفظ شوند.
- ووکامرس مالک قالب پایه محصول و حساب/سبد/پرداخت است. فعلاً هیچ کپی vendor یا طراحی نهایی ندارد؛ wrapperها با hook تنظیم شده‌اند.
- عکس محتوایی از Media Library، با ابعاد ذاتی و srcset؛ فونت لازم خود قالب در assets/fonts محلی است.

## قواعد سراسری و مرحله بعد
مرجع مشترک: ../../WEB-DEVELOPMENT-STANDARDS/00-STANDARDS-INDEX-v1.3.md؛ همان مالک‌های HTML/SEO، CSS، responsive، امنیت، دسترس‌پذیری، رسانه، query و migration مصرف می‌شوند و اینجا تکرار نمی‌شوند.
الگوی وبلاگ و فونت از Corporate-Music و Webtalik با اجازهٔ کاربر بررسی شد؛ پالت مشتری، داده و runtime کامل آن‌ها منتقل نشده‌اند.
طراحی نهایی خانه/فروشگاه/محصول/حساب، اطلاعات واقعی درباره/تماس، قوانین حقوقی مشتری، سیاست حمل‌ونقل/مالیات/مرجوعی و سئوی نهایی محتوایی هنوز باید تعیین شوند. HPOS در نصب پایه فعلاً فعال نیست؛ هنگام انتخاب تنظیمات سفارش از مسیر بومی ووکامرس بررسی شود.
