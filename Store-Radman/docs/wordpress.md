# قرارداد وردپرس و فروشگاه
این پروژه فروشگاه سفارشی با WooCommerce است؛ موتور موازی محصول، خرید، کاربر یا سفارش ندارد. قواعد عمومی امنیت/مدیا/query/SEO/cache از [فهرست مشترک](../../WEB-DEVELOPMENT-STANDARDS/00-STANDARDS-INDEX-v1.3.md) مصرف می‌شوند.

## مسیرها و مالک رندر
| مسیر | مالک |
|---|---|
| / | front-page.php؛ سکشن‌های آینده بر اساس طرح مصوب، با کوئری محدود |
| /shop/ و /shop/page/N/ | آرشیو بومی محصول و main query ووکامرس؛ ۸ نتیجهٔ اولیه، نمایش بیشتر افزایشی |
| /product/{slug}/ | جزئیات بومی محصول، تنوع و انتخاب‌های همان WC_Product |
| /product-category/{parents}/{slug}/ و /brand/{slug}/ | taxonomy بومی product_cat / product_brand؛ همان کارت و pagination کاتالوگ |
| /blog/، /blog/page/N/ و /category/{parents}/{slug}/ | نوشته و دستهٔ بومی؛ صفحه‌بندی عددی و SSR مستقل |
| جزئیات نوشته | single.php با متن Gutenberg، محافظت رمز و دیدگاه بومی |
| /about-us/، /contact-us/، /terms-and-conditions/ | برگهٔ بومی و ویرایشگر؛ متن واقعی مشتری بعداً تکمیل می‌شود |
| /cart/ و /checkout/ | برگه‌های تعیین‌شده در تنظیمات WooCommerce؛ فعلاً بلوک‌های بومی Cart/Checkout |
| /my-account/ و endpointهای آن | برگه و endpoint بومی WooCommerce؛ سفارش‌ها، آدرس، ویرایش حساب و بازیابی رمز |
| نشانی نامعتبر | پاسخ واقعی 404 از 404.php، بدون redirect عمومی به خانه |

نام و محتوای صفحه فارسی؛ slug انگلیسی. لینک‌ها با get_permalink، get_term_link، wc_get_page_permalink و wc_get_account_endpoint_url ساخته شوند، نه دامنهٔ localhost یا ID ثابت. pagination هر صفحه URL واقعی خود را حفظ می‌کند؛ جست‌وجوی محصول، مرتب‌سازی و فیلترها باید state و query اصلی را حفظ کنند. تغییر مسیر موجود نیازمند تصمیم و migration است.

## اشتراک نمایش محصول و فرم‌ها
- در وضعیت فعلی، کارت آرشیو و مرتبط‌ها از content-product.php خود ووکامرس و hookهای استاندارد همان renderer می‌آید. در سکشن محصول خانه نیز همین renderer مصرف شود؛ کارت دوم با HTML/JS/CSS موازی نسازید. دادهٔ هر مصرف‌کننده با wc_get_products یا WC_Product_Query محدود و انتخاب‌پذیر است؛ آرشیو query اصلی خود را دارد.
- اگر طرح مصوب به markup دیگری نیاز داشت، hookها را بررسی کنید و فقط در صورت ضرورت یک override مالک کارت در woocommerce/content-product.php ایجاد شود. فایل vendor دستکاری نشود، نسخهٔ template و update notices حفظ شوند. تا پیش از نیاز واقعی فایل خالی، registry یا variant framework ساخته نشود.
- کارت مشترک باید وضعیت متغیر/ناموجود/حراج، قیمت، دکمهٔ مناسب، نام و تصویر native را از WC_Product بخواند. هیچ کنترل انتخاب تنوع یا افزودن به سبدِ مستقل از ووکامرس ایجاد نشود.
- حساب، ثبت‌نام/ورود، آدرس، دیدگاه و پرداخت منطق و nonce/validation/notice/session بومی خود را حفظ می‌کنند. فقط ظاهر و markup ضروری پس از مشخص شدن طرح تغییر می‌کند. فرم‌های مشترک، دکمه و فاصله/رنگ/فونت یک مالک استایل دارند؛ icon fontها با فونت متن جایگزین نشوند.
- PHP کاتالوگ و بلوک‌های Cart/Checkout دو مسیر رسمی متفاوت‌اند. CSS و hook قالب classic خودبه‌خود جایگزین بلوک‌ها نیست. انتخاب مسیر سفارشی سبد/پرداخت پیش از اجرای طرح ثبت شود و پشتیبانی درگاه در همان مسیر بررسی شود؛ صفحهٔ checkout برای یکپارچه‌کردن ظاهر خودسرانه تبدیل نشود.
- جزئیات پرداخت، کلید سفارش، callback، nonce، حساب و مسیرهای رمز/سفارش از قواعد کش عمومی و اسکرول آرشیو مستثنا هستند. عملیات سفارش با CRUD ووکامرس انجام شود؛ سازگاری HPOS حفظ و فعال بودن آن جدا ثبت شود.

## دارایی و ارتباط‌های خارجی
مالک سیاست بارگذاری: inc/woocommerce.php؛ مالک enqueue: inc/assets.php.
- فونت Peyda از assets/fonts و یک font-face در tokens.css برای سایت و پیشخوان مصرف می‌شود. CSS token با dependency و نسخهٔ مستقل enqueue می‌شود؛ runtime import زنجیره‌ای و کپی فونت اضافه نشود.
- سه CSS عمومی ووکامرس و JS عمومی سبد/محصول روی مسیر بدون مصرف حذف می‌شوند؛ روی shop، taxonomy محصول، جزئیات محصول، cart، checkout، همهٔ endpointهای حساب و محتوای دارای بلوک/shortcode ووکامرس حفظ می‌شوند.
- برای محصول سفارشی در home/header یا رندر PHP که پیش از enqueue قابل تشخیص نیست، مالک همان مصرف‌کننده باید filter رادمان radmanstore/commerce/assets را هنگام bootstrap فعال کند. حذف global همهٔ استایل‌ها، jQuery، Store API، wc-ajax، nonce، نشست یا scriptهای درگاه ممنوع است.
- بعد از آماده‌شدن CSS جایگزین مصوب، فقط stylesheet واقعاً جایگزین‌شده حذف شود؛ رفتار responsive، تغییر تنوع، loading/error، تخفیف، ناموجود، فرم، سبد و پرداخت در همان scope کنترل شود. کاهش درخواست/bytes با شواهد گزارش شود؛ حذف نام WooCommerce به‌تنهایی اثبات سرعت نیست.
- پیشنهادهای Marketplace و telemetry اختیاری WooCommerce غیرفعال‌اند؛ Gravatar با تنظیم بومی show_avatars غیرفعال است. نصب و update دستی WordPress/افزونه‌ها باز می‌ماند و اعلان update مخفی نمی‌شود.
- هیچ مسدودسازی سراسری HTTP یا allowlist دامنهٔ ثابت برای فروشگاه اضافه نشود. درخواست واقعی درگاه، callback/verify، webhook، ارسال، مالیات، احراز هویت سرویس و افزونهٔ مجاز ممکن است لازم باشد. هر سرویس فقط با شاهد به‌عنوان غیرضروری حذف شود؛ admin و frontend جدا بررسی شوند.
- tracking مشتری بدون provider/ID واقعی فعال نشود. خاموش‌کردن telemetry خود ووکامرس با Google Analytics یا گزارش سفارش‌های داخلی ووکامرس یکی نیست؛ WC Admin و گزارش فروش را برای حذف پیشنهاد تبلیغاتی از کار نیندازید.

## سئو، وبمستر و آمار
- Yoast مالک title، description، canonical، robots، OG، verification و graph سئویی است؛ wp_head/wp_body_open/wp_footer حفظ شوند. موتور یا فیلد موازی در قالب ساخته نشود.
- BreadcrumbList از graph یواست می‌آید؛ خروجی تکراری همان schema از WooCommerce فقط هنگام فعال بودن Yoast حذف می‌شود. schema بومی Product/Offer/Brand ووکامرس و قیمت/موجودی واقعی آن حفظ می‌شود؛ Review/Rating ساختگی افزوده نشود.
- تأیید Search Console و سایر وبمسترها در Yoast > Settings > Site connections، یک بار با token واقعی مالک است. قالب خودکار token خالی یا نمونه وارد نمی‌کند.
- نمایش آمار در Yoast با اتصال Site Kit با نصب کد Analytics یکی نیست. Analytics به یک مالک جدا مانند Site Kit یا GTM و شناسهٔ واقعی نیاز دارد؛ همان tag از قالب و افزونه هم‌زمان نصب نشود. بدون این اتصال، جمع‌آوری آمار تأییدشده نیست.
- local با noindex و X-Robots-Tag باقی می‌ماند؛ نبود canonical یا sitemap قابل index در این حالت نباید با snippet موازی دور زده شود. هنگام انتشار واقعی، robots، sitemap، canonical آرشیو صفحه‌بندی، متای محصول/دسته/برند، OG و schema بر مبنای دامنه و محتوای واقعی کنترل شوند.
- search، فیلترهای کم‌ارزش و مسیرهای شخصی خرید/حساب طبق سیاست Yoast/WooCommerce بررسی شوند؛ noindex ابزار حفاظت اطلاعات حساب نیست.

## زرین‌پال
افزونهٔ رسمی zarinpal-woocommerce-payment-gateway نسخهٔ ثبت‌شده 5.1.2 فعال است. gateway بومی آن WC_ZPal است؛ کد vendor تغییر نکرده و package پشتیبانی Cart/Checkout Blocks و HPOS را اعلام می‌کند. این اعلام جای آزمون واقعی نیست.
بدون merchantcode، روش پرداخت محلی enabled=no و sandbox=yes است؛ Start تنظیمات پذیرندهٔ موجود را reset نمی‌کند. تست بعدی باید با مشخصات sandbox واقعی و کنترل مبلغ/واحد تومان یا ریال، ایجاد سفارش، redirect، callback، verify و callback تکراری انجام شود. این مرحله فقط نصب/فعال‌سازی را تأیید می‌کند، نه تراکنش موفق.
صفحهٔ تنظیمات: wp-admin/admin.php?page=wc-settings&tab=checkout&section=wc_zpal. اطلاعات پذیرنده و token در سند، Git و خروجی عمومی درج نشوند.

## اسنپ‌پی
بستهٔ رسمی WooCommerce نسخهٔ 1.4.2 از لینک مستقیم آکادمی نصب شده و عمداً غیرفعال است. بستهٔ دیگر همان مقاله، snapppay-lastest-version-1.zip، برای PrestaShop است و نباید نصب شود. نصب اولیه این بسته را اضافه می‌کند ولی خودکار فعال نمی‌کند؛ Start وضعیت و تنظیمات موجود را تغییر نمی‌دهد.

پیش از فعال‌سازی باید این موانع در نسخهٔ رسمی اصلاح یا با راهکار محدود و بررسی‌شده حل شوند؛ فایل vendor دستکاری و HTTP کل فروشگاه مسدود نشود:
- hooks.php اعتبارسنجی HTTPS را با https_ssl_verify و callback سراسری http_api_curl خاموش می‌کند؛ این رفتار به درخواست پرداخت خود افزونه محدود نیست.
- activation در hooks.php، track_event را اجرا می‌کند؛ functions.php نام/آدرس سایت و اطلاعات محیط را به Google Apps Script می‌فرستد و IP سرور را از سرویس اسنپ‌پی می‌گیرد. لوگوی اعلان مدیریت نیز خارجی است. فعال‌سازی صرفاً با روش پرداخت خاموش، این رفتارها را حذف نمی‌کند.
- بسته HPOS را compatible اعلام می‌کند، اما پیاده‌سازی/ثبت Cart/Checkout Blocks در آن یافت نشد. checkout فعلی بلوکی است؛ برای نمایش این روش باید نسخهٔ رسمی سازگار یا تصمیم مستقل دربارهٔ مسیر checkout وجود داشته باشد. فعلاً بلوک‌ها و HPOS حفظ شوند.
- خود vendor با Update URI: false و فیلتر update، اعلان به‌روزرسانی خودش را حذف کرده است. طبق مقالهٔ رسمی، نسخهٔ جدید از آکادمی دستی گرفته می‌شود؛ ابزار نگهداری نباید این استثنا را به سایر افزونه‌ها تعمیم دهد.

شناسهٔ gateway بومی WC_Gateway_SnappPay و option تنظیمات woocommerce_WC_Gateway_SnappPay_settings است. مشخصات پذیرندگی، endpoint تست و کمیسیون دسته‌ها باید از اسنپ‌پی واقعی گرفته شوند؛ مقدار فرضی وارد نشود. نصب بسته با آمادگی تراکنش یا تأیید امنیت/سازگاری نسخهٔ فعلی WooCommerce برابر نیست. تراکنش، callback/verify، مبلغ/واحد پول، مرجوعی و سفارش تکراری هنوز آزموده نشده‌اند.

## مرجع رسمی
- [WooCommerce classic themes](https://developer.woocommerce.com/docs/theming/theme-development/classic-theme-developer-handbook/)
- [Yoast Site connections](https://yoast.com/help/yoast-seo-settings-site-connections/)
- [Site Kit Analytics](https://sitekit.withgoogle.com/documentation/supported-services/analytics/)
- [افزونهٔ رسمی زرین‌پال](https://wordpress.org/plugins/zarinpal-woocommerce-payment-gateway/)
- [بستهٔ رسمی WooCommerce اسنپ‌پی](https://academy.snapppay.ir/wp-content/uploads/2026/03/snapppay-woocommerce-gateway-v1.4.2.zip)
- [متن رسمی مقالهٔ اسنپ‌پی از API آکادمی، شناسهٔ 603](https://academy.snapppay.ir/wp-json/wp/v2/posts/603)
