# اجزای مشترک و قواعد استفاده

این سند فهرست مالک‌های موجود در Corporate-Webtalik است. قبل از تغییر UI، ردیف مرتبط و سورس مصرف‌شده را بررسی کنید؛ وجود فایل به معنی استفادهٔ سراسری آن نیست. مسیرهای جدول نسبت به `radmanct/` هستند. جزئیات معماری در [architecture.md](architecture.md)، نام‌گذاری در [naming.md](naming.md) و بارگذاری رسانه در [media.md](media.md) است.

| مورد مشترک | مالک موجود | قاعدهٔ مصرف |
|---|---|---|
| رنگ، فونت، اندازه، گردی و خطوط | `src/css/tokens.css` | نقش‌های `ink/tx/mut/ln/srf/acc` و `--rdc-rad` مصرف شوند؛ رنگ و گردی مشترک در صفحه‌ها کپی نشوند. کنترل‌های دایره‌ای و هندسهٔ اختصاصی تصویر استثنای طراحی‌اند. |
| تایپوگرافی تگ‌ها و بدنه | `src/css/base.css` | Hafez برای H1 و Peyda برای بدنه و H2–H6؛ سطح عنوان از محتوا تعیین شود، نه اندازهٔ بصری. |
| عنوان سکشن | `src/css/ui/section-heading.css` | دو مدل زیر؛ اشتراک فقط CSS، بدون renderer یا JS جدید. متن در مالک سکشن باقی می‌ماند. |
| Page Title و H1 محتوایی | `inc/page-title.php`، `template-parts/layout/page-title.php`، `template-parts/ui/content-heading.php` | مستقل از عنوان سکشن؛ cover/split و قواعد نمایش فعلی حفظ شوند. H1 محتوایی جایگزین Page Title حذف‌شده است، نه H2 سکشن. |
| دکمه و آیکون CTA | `inc/ui/button.php`، `src/css/ui/button.css` | renderer موجود با گزینه‌های خودش؛ رفتار hover و markup جدا در هر صفحه نسازید. |
| آیکون‌ها | `inc/icons.php`، `inc/icons/catalog.php`، `src/css/ui/icon.css` | SVG محلی از catalog، اندازه/ضخامت پایه از tokens؛ فقط نماد جهت‌دار در RTL معکوس شود. |
| متن و پیش‌نمایش کارت | `src/css/ui/card-text.css`، `src/css/ui/card-preview.css`، `src/js/ui/card-preview.js` | متن کامل حفظ شود؛ clamp و رفتار رسانه دوباره نوشته نشوند. برای پخش و منابع، media.md را رعایت کنید. |
| سؤال‌های متداول | `template-parts/ui/faq.php`، `src/css/ui/faq.css` | یک renderer با items/title/label/desc مستقل در هر مصرف؛ details/summary بومی و گروه یکتا حفظ شوند. |
| دسته‌بندی و سایدبار | `inc/ui/category-tree.php`، `src/css/ui/category-tree.css`، `src/css/ui/sidebar.css` | مالک موجود و لینک‌های واقعی استفاده شوند؛ منطق جدا برای هر آرشیو نسازید. |
| جستجو و صفحه‌بندی آرشیو | `src/js/pages/archive.js` | رفتار موجود با لینک بومی، history و پاسخ هم‌مبدأ؛ query اصلی، حفاظت محتوا و lifecycle حفظ شوند. |
| ادامهٔ جزئیات | `inc/content/continuation.php`، `template-parts/layout/continuation.php` | داده و نمایش مشترک نوشته/نمونه‌کار؛ قواعد نوع محتوا و fallback فعلی حفظ شوند. |
| هدر، منو و فوتر | `template-parts/layout/`، `src/css/layout/`، `src/js/ui/header.js`، `src/js/ui/menu-panel.js`، `src/js/ui/footer.js` | مالک‌های مستقل فعلی؛ برای تغییر یک صفحه، نسخهٔ دیگری از هدر/فوتر یا کنترل منو نسازید. |
| تماس شناور و cursor | `inc/ui/contact.php`، `src/css/ui/contact.css`، `src/js/ui/contact.js`، `src/css/ui/cursor.css`، `src/js/ui/cursor.js` | رفتار، دسترس‌پذیری، tooltip و cleanup از مالک موجود؛ دادهٔ مشتری در inc/client.php. |
| لوگو و حرکت برند | `inc/brand.php`، `src/css/ui/brand.css`، `src/js/ui/brand.js` | sanitizer، شناسه‌های یکتای SVG و کنترل visibility/reduced-motion مشترک؛ شکل و ترتیب حرکت W و نشان شخصی مستقل بمانند. |
| ورود متن و ابزارهای اسکرول | `src/js/ui/text-motion.js`، `src/js/runtime/` | lifecycle، scroll-state، refresh، observers و sticky موجود؛ موتور، RAF یا observer موازی برای همان مسئولیت نسازید. |
| تزئین هیرو و رسانه | `template-parts/ui/title-art.php`، `src/js/runtime/art-orbit.js`، `inc/content/media.php` | اندازهٔ مشترک نقطه‌ها از tokens؛ مسیر حرکت مطابق تصویر خودش. رسانه از attachment ID و با قرارداد media.md مصرف شود. |

## دو مدل عنوان سکشن

- **دوطرفه:** `rdc-heading`، گروه `rdc-heading__inr` شامل برچسب p با `__lbl` و عنوان واقعی H2/H3 با `__ttl`، و توضیح p با `__desc` در سمت مقابل. نخستین مصرف، بالای mosaic صفحهٔ طراحی وب‌سایت اختصاصی است. رنگ تدریجیِ ثابت عنوان با CSS روی کل متن اعمال می‌شود.
- **کناری:** همان کلاس‌ها همراه `rdc-heading--stack`؛ عنوان و توضیح زیر هم، کنار محتوای سکشن. FAQ همین مدل را در renderer مشترکش مصرف می‌کند؛ عنوان آن رنگ یکدست دارد و کنار آکاردئون است. در نمایش باریک، معرفی بالای سؤال‌ها قرار می‌گیرد.

فونت و رنگ از tokens/base، اندازهٔ عنوان از `--rdc-sz-section` و آیکون برچسب از catalog خوانده می‌شوند. حروف فارسی را جدا نکنید؛ برچسب و توضیح را برای ظاهر به heading تبدیل نکنید. Page Title و عنوان کارت‌ها خودکار وارد این دو مدل نمی‌شوند. `faq.css` این فایل مشترک را import می‌کند؛ در entryهای service/services دوباره import نشود. مصرف‌کنندهٔ دیگری که FAQ ندارد می‌تواند آن را یک‌بار در entry خودش وارد کند.

## قواعد تغییر و کارایی

1. قبل از ساخت کلاس، رنگ یا رفتار جدید، مالک و مصرف‌کننده‌های موجود را پیدا کنید. برای یک طراحی یکسان، همان کلاس یا گزینهٔ موجود را مصرف کنید؛ تغییر واقعی طراحی، modifier محدود خودش را دارد.
2. اشتراک CSS الزاماً اشتراک PHP/JS نیست. محتوای متفاوت را یکسان نکنید و برای دو ظاهر، registry، تنظیمات، renderer بزرگ یا وابستگی تازه نسازید.
3. هنگام انتقال یک مصرف به مالک مشترک، قانون قدیمیِ همان مصرف را حذف کنید؛ override تکراری یا alias موقت باقی نگذارید. بخش‌های نامرتبط را خودکار بازطراحی نکنید.
4. CSS/JS فقط از entry و مسیرهای مصرف فعلی انتخاب شود؛ سورس ماژولار به معنی درخواست HTTP مستقل برای هر قطعه نیست. خروجی dist را مستقیم ویرایش نکنید.
5. RTL/LTR، موبایل، معنای تگ‌ها، ابعاد تصویر، reduced-motion و cleanup حفظ شوند. بررسی کوتاه متناسب با تغییر کافی است؛ build اثبات FPS یا سرعت هاست نیست.
6. مالک مشترک یا قرارداد مصرف که تغییر کرد، همین فهرست را به‌روز کنید. AGENTS و skill به این سند ارجاع می‌دهند؛ فهرست کامل را در سه جا کپی نکنید. کتابخانهٔ طراحی و پروژه‌های مشتری runtime مستقل دارند؛ این سند مجوز همگام‌سازی خودکار بین پروژه‌ها نیست.
