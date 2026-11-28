# معماری پایهٔ Store Radman
- پروژه مستقل فروشگاهی با قالب classic PHP به نام radmanstore و ووکامرس؛ وبلاگ بومی وردپرس. نمونه‌کار و دادهٔ مشتریان دیگر ندارد.
- این مرحله فقط زیرساخت و قالب پایه است. طراحی آرشیو، جزئیات محصول، سبد/پرداخت و حساب کاربری پس از تأیید طرح انجام می‌شود.
- ووکامرس مالک محصول، تنوع، قیمت، موجودی، سبد، سفارش و حساب است؛ سفارش‌ها فقط از CRUD ووکامرس. HPOS و hookهای استاندارد حفظ شوند. هیچ جدول یا موتور خرید موازی ساخته نشود.
- قالب مالک نمایش است؛ با hookهای بومی و کمترین override. فایل‌های افزونه دستکاری نشوند. نصب Node یا build روی هاست لازم نیست.
- Yoast SEO مالک عنوان، canonical، robots، sitemap و schema سئو است. قالب title-tag و wp_head/wp_footer را حفظ می‌کند. All-in-One WP Migration افزونهٔ بکاپ/مهاجرت است؛ افزونهٔ SEO دوم نیست.
- سازگاری اولیه یعنی نصب/فعال‌سازی و مسیرهای بومی؛ تنظیم نهایی سئوی محصول، Product schema، پرداخت، حمل‌ونقل، مالیات، ایمیل و مهاجرت واقعی نیاز به سناریو و کنترل جدا دارد.
- محیط local فقط روی loopback: HTTPS اصلی 127.0.0.1:8143 و HTTP قدیمی 8100 با redirect؛ دیتابیس store_radman_local، شبکهٔ store-radman و volumeهای store-radman-database / store-radman-wordpress مستقل. گواهی/اجرای TLS فقط ابزار لوکال است؛ قرارداد در [local-https.md](local-https.md).
- local noindex و ایمیل واقعی غیرفعال است. نصب، افزودن و به‌روزرسانی دستی افزونه‌ها و درخواست‌های بومی HTTP باز هستند؛ افزونه‌ها در volume قابل‌نوشتن قرار دارند. فقط به‌روزرسانی خودکار غیرفعال است. Cron بومی برای صف ووکامرس حفظ می‌شود. محدودیت local هرگز به هاست منتقل نشود.
- قواعد مشترک امنیت، رسانه، query، SEO، تحویل و refactor از فهرست استاندارد ریشه مصرف شوند؛ کپی کامل قواعد و کد پروژه‌های دیگر لازم نیست.

## مالک‌های اجرایی
| مسیر | مسئولیت |
|---|---|
| radmanstore/functions.php | فقط بارگذاری ownerهای theme؛ کد تکراری runtime ندارد |
| radmanstore/inc/setup.php | پشتیبانی‌های theme و منوی بومی |
| radmanstore/inc/assets.php | enqueue مشروط سایت و فونت محلی پیشخوان |
| radmanstore/inc/woocommerce.php | adapter بومی commerce، wrapper، query size، pagination مصرف‌کننده و حذف asset غیرمصرفی |
| radmanstore/inc/pagination.php | renderer واحد عددی/نمایش بیشتر |
| radmanstore/template-parts/blog/ | کارت، آرشیو و sidebar وبلاگ |
| radmanstore/assets/ | tokens، base، admin و CSS/JS مصرف‌کننده؛ فایل font-face یکی است |
| docs/ و .agents/skills/store-work/ | قراردادهای نگهداری؛ خارج از theme قابل‌نصب |
| tools/local-wordpress/ | اجرای لوکال مستقل؛ خارج از runtime هاست |

قرارداد کامل مسیرها، رندر مشترک محصول، حساب/سبد/پرداخت، مصرف CSS، اتصال‌های خارجی، Yoast و Analytics و سناریوی درگاه در [wordpress.md](wordpress.md) است. طرح نهایی همچنان با نمونهٔ کاربر تعیین می‌شود؛ این تفکیک مجوز بازطراحی یا کپی همهٔ templateهای ووکامرس نیست.
