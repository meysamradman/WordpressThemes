# طراحی اختصاصی در Child

این سند قرارداد عمومی سفارشی‌سازی است. رفتار صفحات در [سناریوی صفحات](../scenarios/pages.md)، دارایی در [assets](assets.md) و تایپوگرافی در [typography](typography.md) نگهداری می‌شود. جزئیات طراحی [خانه](client-home.md) و [وبلاگ](client-blog.md) مالک مستقل دارند؛ برای تغییر محصول لازم نیست آن‌ها را بخوانید.

## مرز والد، Child و داده

- `radmanct-child` محل طراحی همان مشتری است؛ طرح‌های مشتریان به والد منتقل نشوند. داده، query، فیلتر، گالری، مشخصات، ترجمه و engine مشترک دوباره ساخته نشوند.
- فایل طراحی صفحه/نوشته/آرشیو واقعی WordPress را مصرف کند: Loop، وضعیت/رمز/صفحه‌بندی، `get_header/get_footer`، `wp_head/wp_footer` و hookهای SEO حفظ شوند. H1 نمایشی با Title سند یکی نیست؛ Title/Robots/Canonical/OG/schema دستی و تکراری چاپ نشوند. محیط لوکال برای حل ظاهر به index تغییر نکند.
- هر طراحی entry CSS و فقط در صورت نیاز JS خودش را دارد؛ بخش بزرگ می‌تواند زیرپوشهٔ همان صفحه داشته باشد. base فقط مشترکات واقعی را نگه دارد؛ JS خالی، vendor/فونت تکراری و CSS صفحهٔ دیگر اضافه نشود.
- طرح، تنظیمات عمومی و پروفایل همان محتوا را مصرف کند. استثنای عمدی فقط در scope عنصر و ترجیحاً با token موجود قابل حذف باشد؛ موتور، کنترل یا !important موازی ساخته نشود. مرز Elementor و پنل مدیریت مستقل است.
- فایل‌های Elementor تنها برای محتوای Elementor واقعاً مصرف‌شده لازم‌اند؛ dequeue عمومی بر اساس نام فایل یا صرفاً PHP بودن صفحه ممنوع است.

## مسیرهای اصلی

| مصرف | محل |
|---|---|
| bootstrap و defaultهای مشتری | `functions.php`؛ فیلتر `radmanct/settings/defaults` فقط پیش‌فرض می‌دهد، نه overwrite مقدار مدیر |
| base کوچک مشتری | `src/css/client.css` |
| صفحهٔ سفارشی عمومی | `templates/custom-page.php`، `template-parts/client/page.php`، `src/css/pages/custom-page.css` |
| خانه | [قرارداد خانه](client-home.md) |
| وبلاگ | [قرارداد وبلاگ](client-blog.md) |
| کاتالوگ | `template-parts/catalog/` و `inc/catalog/`؛ قرارداد زیر |
| نمونه‌کار | `template-parts/portfolio/{archive,single,card}.php`؛ engine محتوا و منابع پایهٔ والد |
| ورودی build و loader | target Child در `tools/build.mjs` و `RadmanCT\Assets` با scope=`child` |

## محصول سفارشی

- `catalog/archive.php` پوسته است؛ `archive/header.php` عنوان/توضیح دسته و `archive/section.php` ناحیهٔ نتایج AJAX را نگه می‌دارند. `template-parts/content/catalog-results.php` و `archive/results-status.php` از query مشترک نتیجه، شمار، حذف فیلتر و pagination را می‌گیرند؛ query موازی ساخته نشود.
- `catalog/single.php` Loop، رمز، لینک بازگشت و ترتیب اجزا را نگه می‌دارد. `single/overview.php` رسانه/خلاصه و `single/content.php` متن/صفحه‌بندی محتواست. دسته/برند/ویژگی/تگ، قیمت/تماس، گالری و مشخصات از renderer مشترک می‌آیند.
- `catalog/card.php` ورودی سازگار و `shared/product-card.php` markup مشترک کارت است؛ پاسخ معمولی و fragment آن را یکسان مصرف می‌کنند.
- سایدبار: `inc/catalog/sidebar.php`، ابزارک `widgets/archive.php` و partialهای `sidebar/{search,categories,tags,recent}.php`. `content/sidebar.php` scope محصول را به `catalog/sidebar.php` واگذار می‌کند. تنظیمات/ابزارک واقعی از مدیر می‌آیند و runtime آن‌ها را seed/overwrite نکند.
- CSS سایدبار در `src/css/catalog/sidebar.css` و JS آن در `src/js/catalog/sidebar.js` فقط برای ناحیهٔ فعال و پُر است. پایهٔ کوچک سایدبار/offset مشترک می‌ماند؛ layout، درخت دسته و sticky در [سایدبار](sidebars.md)، داده در [Content](content-modules.md) و کنترل انتقال در [AJAX](archive-ajax.md) تعریف می‌شود.

## قطعهٔ قابل استفادهٔ مجدد در PHP

رجیستری `radmanct/inc/custom-content.php` فقط تعریف کد مورد اعتماد را نگه می‌دارد؛ CPT/بلوک‌ساز جدید یا اجرای مسیر/callback از تنظیمات/HTTP مجاز نیست. `rdct_register_content(id, ['title'=>…, 'render'=>callback, 'assets'=>callback])` روی `after_setup_theme` پس از آماده‌شدن Child ثبت شود؛ نمونهٔ `site-navigation` در `inc/content/navigation.php` و `template-parts/content/navigation.php` است و منوی primary واقعی را مصرف می‌کند. ثبت نمونه، انتخاب فعال سایت را خودکار عوض نکند.

ID با حرف کوچک لاتین شروع، فقط حروف کوچک/عدد/خط تیره و حداکثر ۸۰ کاراکتر است. تعریف تکراری اولی را overwrite نمی‌کند؛ ID مفقود خروجی خالی دارد و از دادهٔ ذخیره‌شده حذف نمی‌شود. هر مصرف `instance_id` مستقل برای ID/ARIA دارد؛ recursion و عمق بیش از ۸ مهار و buffer/state در خطا آزاد شوند. callback مورد اعتماد escaping خودش را انجام می‌دهد؛ KSES سراسری نباید SVG/markup معتبر renderer را خراب کند.

| slot | کلید/قرارداد |
|---|---|
| مستقیم PHP | `rdct_render_content(id, context)`؛ wrapper اجباری ندارد |
| پنل هدر/فوتر | منبع `custom` و `panelCustomContent`؛ انتخاب از metadata رجیستری |
| Carousel، Accordion، هر دو Curtain و آیتم Sticky Sections | `custom_content` در همان آیتم |
| ستون ثابت Sticky Sections | `aside_custom_content` در options |
| دو طرف Flip Box | `front_custom_content` و `back_custom_content` |

کلیدهای slot اختصاصی PHP، field تازهٔ Classic/Atomic نمی‌سازند. callableهای قبلی اولویت قبلی را حفظ می‌کنند؛ ارجاع صریح custom بر متن/Template همان slot مقدم است. callback قبلی `rdct_panel(content, options)` و Template المنتور باقی‌اند؛ دادهٔ wp_block/Template مهاجرت یا حذف نشود.

assets callback هر ID فقط یک‌بار و هنگام مصرف اجرا شود؛ برای مصرف مستقیم، `rdct_prepare_content(id)` در enqueue همان صفحه CSS را پیش از head آماده کند. renderer fallback آماده‌سازی دارد؛ callback مسئول late CSS است. کامپوننت و vendor از requirements/URL مشترک مصرف شوند، نه با کپی GSAP/Swiper/فونت.

## زبان و طراحی مستقل

هر زبان، رکورد WordPress واقعیِ مستقل با عنوان/محتوا/تصویر خود دارد؛ template از رکورد جاری می‌خواند و برای هر زبان کپی PHP لازم نیست. WPML Languages برای ایجاد/اتصال/ویرایش مصرف شود؛ Duplicate متصل با sync فعال استقلال محتوا را تضمین نمی‌کند. برای template متفاوت دو زبان، مدیر همگام‌سازی بومی page template را تنظیم می‌کند؛ Theme آن را خودکار تغییر نمی‌دهد. تخصیص زبان هدر/فوتر از resolver موجود می‌آید. رجیستری PHP مترجم یا fallback زبان تازه نمی‌سازد؛ قرارداد زبان در [مالک زبان‌ها](../scenarios/languages.md) و موارد باز در [نقشهٔ کار](../product/roadmap.md) است.

برای یافتن دلیل یک تصمیم قدیمی فقط [آرشیو این سند](../archive/2026-11-02-documentation-cleanup/radman-doc--engineering--client-customization.md) را به‌صورت هدفمند جست‌وجو کنید؛ تاریخچه مرجع رفتار امروز نیست.
