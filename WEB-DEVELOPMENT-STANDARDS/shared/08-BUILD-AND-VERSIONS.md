# نسخه‌ها، CSS/JS و ساخت بسته

- حداقل اعلام‌شدهٔ قالب، نسخهٔ هدف، نسخهٔ نصب‌شده و نسخه‌ای که واقعاً تست شده چهار دادهٔ جدا هستند. نسخه و منبع واقعی در Profile همان پروژه ثبت شود. runtime را از PHP وب/CLI و محیط نصب‌شده بررسی کنید و ابزار ساخت را از manifest و lockfile بخوانید. مقدار تأییدنشده را مشخص کنید. تغییر نسخه، مهاجرت یا تولید بسته بخشی از یک ویرایش عادی نیست.
- CSS یک پایهٔ سبک و مالک token دارد. استایل هر کامپوننت و صفحه کنار همان ماژول بماند. JS سراسری فقط زیرساختی را داشته باشد که مصرف سراسری دارد. import ثابت برای وابستگی لازم همان entry، import پویا برای قابلیت شرطی و cleanup با مالک فعلی. تعداد فایل به‌تنهایی معیار معماری نیست.
- انتخاب entry و ثبت وابستگی‌ها با مسیر [قالب سفارشی](../wordpress-custom/07-WORDPRESS-CUSTOM-DEVELOPMENT-v1.0.md) یا [Elementor](../wordpress-elementor/15-WORDPRESS-ELEMENTOR-DEVELOPMENT-v1.0.md) انجام شود. CSS لازم برای HTML اولیه به‌موقع برسد و نبود مصرف‌کننده به معنی نبود درخواست کد اختیاری باشد.
- در صورت Vite، manifest و فایل‌های هش‌دار همان build با هم بررسی و تحویل شوند. CSS و زنجیرهٔ importهای ثابت/پویا باید در بسته موجود باشند. dist خروجی ساخت است. Node ابزار توسعه و build است. فایل‌های runtime را از روش رسمی WordPress enqueue کنید.
- ZIP به‌روزرسانی فقط فایل‌های لازم را دارد. اطلاعات اتصال، `wp-config.php`، فایل نمونهٔ آن، محیط توسعه، بکاپ و SQL در ZIP عمومی قرار نگیرند. SQL فقط برای مهاجرت دادهٔ درخواست‌شده و جداگانه تحویل شود. برای هاست یونیکس، مجوز معمول ZIP پوشه `0755` و فایل `0644` است. مجوز واقعی و مالکیت پس از استخراج بررسی شود.

[WordPress enqueue](https://developer.wordpress.org/reference/functions/wp_enqueue_script/) · [Vite manifest](https://vite.dev/guide/backend-integration.html) · [مجوز فایل WordPress](https://developer.wordpress.org/advanced-administration/server/file-permissions/)
