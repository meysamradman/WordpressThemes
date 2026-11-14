# معماری سایت سفارشی

هویت این نسخه **Radman CT سفارشی** با پوشه/text domain برابر `radmanct` و کلاس‌های `rdc-` است؛ قرارداد ثابت نام‌گذاری، مخفف‌ها، استثناهای داده و گالری مدیریت در [naming.md](naming.md) است. نام مشتری در محتوای سایت می‌ماند و شناسهٔ فنی قالب نیست.

## محدوده و مالکیت

PHP و Vanilla، بدون Child، Elementor، پنل تنظیمات، هدر/فوترساز یا کاتالوگ محصول. پروژهٔ Corporate-Catalog مستقل است و فقط برای مقایسهٔ درخواست‌شده خوانده می‌شود؛ منبع یا دیتابیس آن تغییر نمی‌کند.

| مسیر | مسئولیت |
|---|---|
| `radmanct/inc/` | راه‌اندازی، انتخاب دارایی، helper و rendererهای مشترک |
| `radmanct/template-parts/layout/` | هدر، فوتر و عنوان صفحه |
| `radmanct/template-parts/sections/` | سکشن‌های اختصاصی؛ هندسهٔ افکت هر تصویر کنار همان سکشن |
| `radmanct/template-parts/blog/`، `cards/`، `portfolio/` | نمایش محتوای مرتبط |
| `radmanct/src/css/` | tokens، قواعد پایه، چیدمان، UI و صفحات |
| `radmanct/src/js/` | runtime مشترک، UI و افکت‌های منتخب صفحات |
| `radmanct/dist/` | خروجی فشرده و hashدار + manifest؛ ویرایش مستقیم ممنوع |
| `radmanct/inc/content/` | ثبت نمونه‌کار، دسته‌بندی و متادیتای مشترک رسانه |
| `tools/` | build، ترجمه و محیط محلی |

وبلاگ و نمونه‌کار بخش‌های ثابت همین سایت‌اند؛ فعال/غیرفعال‌کردن ماژول یا افزونهٔ محتوای جدا نداریم. ثبت داده داخل قالب و نمایش آن در ماژول مربوط می‌ماند؛ داده و کلیدهای قبلی تغییر نمی‌کنند. کنترل رسانهٔ مدیریت در `src/js/admin/` است و در frontend بارگذاری نمی‌شود.

قواعد بومی و امنیت در [wordpress.md](wordpress.md)؛ فقط کارهای باز در [remaining.md](remaining.md).

## استفاده در پروژهٔ مشتری دیگر

اولویت با رفتار صحیح و سرعت، سپس سادگی و نگهداری و اشتراک واقعی است. Radman CT هویت ثابت محصول است؛ دادهٔ مشتری فعلی در `inc/client.php` قرار دارد و با فیلترهای بومی WordPress به مالک هر بخش تحویل می‌شود. این فایل در بارگذاری قالب فقط callback ثبت می‌کند؛ دادهٔ هر بخش هنگام مصرف ساخته می‌شود، نه با query یا تنظیمات تازه در شروع هر درخواست.

| داده | فیلتر | مالک مصرف |
|---|---|---|
| معرفی و CTA خانه | `radmanct/home/data` | `inc/home.php` |
| محتوای پنل‌های خدمات خانه | `radmanct/home/offerings` | `inc/home.php` و template همان سکشن |
| راه‌های تماس شناور | `radmanct/contact/data` | `inc/ui/contact.php` و `template-parts/ui/contact.php` |
| خدمات و slug/media مرتبط | `radmanct/services/specs` | `inc/services.php`؛ عنوان و excerpt برگهٔ موجود اولویت دارند |
| سؤال‌های متداول خدمات | `radmanct/services/faq` | `template-parts/ui/faq.php` با آرگومان `items` و عنوان اختیاری `title` |
| منتخب‌های دستی مشتری و attachment ID | `radmanct/home/selected-work` | `inc/home/projects.php` |
| تماس، شبکه‌ها، آدرس و ساعت | `radmanct/footer/data` | مالک موجود `inc/footer.php`؛ هدر و پنل نیز مصرف‌کننده‌اند |

در پروژهٔ تازه، دادهٔ `client.php` جایگزین شود؛ پیش‌فرض عمومی معرفی از نام/توضیح WordPress می‌آید و اطلاعات تماس، خدمات و منتخب‌ها بدون دادهٔ مشتری خالی‌اند. لیست خدمات خالی نباید query ناخواسته برای همهٔ برگه‌ها بسازد. فیلترها هنگام بارگذاری قالب ثبت شوند؛ فوتر و خدمات نتیجه را در همان درخواست cache می‌کنند. هیچ option/meta تازه یا مهاجرت خودکاری برای این جداسازی لازم نیست؛ کلیدهای تاریخی `webtalik_media` و `_webtalik_*` قرارداد داده باقی می‌مانند.

برای تفاوت صرفاً بصری، توکن‌های رنگ/فونت/فاصله و CSS محدود به همان layout تغییر کنند؛ override کوچک داخل entry موجود build شود تا درخواست شبکهٔ تازه لازم نباشد. اگر DOM یا رفتار واقعاً متفاوت است، template همان مشتری تغییر کند و فقط rendererها و Runtimeهای مشترک reuse شوند. این مخزن همچنان بدون Child است؛ ساخت Child، تنظیمات، Registry، Feature Flags یا Builder شرط استفادهٔ مجدد نیست.

هدر Transparent/Solid فقط با دو مصرف واقعی، یک markup و modifier محدود ساخته شود؛ JavaScript دوم، Factory یا Header Builder لازم نیست. سایدبار فعلی وبلاگ تا وقتی تنها مصرف‌کننده است به فایل‌های عمومی بیشتر شکسته نشود. تشخیص مسیر خدمات، Assets و Runtimeهای سالم نیز صرفاً برای عمومی‌تر به‌نظررسیدن بازنویسی نشوند. برای مشتری فاقد یک افکت، markup و import همان layout حذف/انتخاب شوند؛ صرف تغییر رنگ نباید منطق موشن را درگیر کند. تغییر صرفاً داده‌ای نباید CSS/JS، درخواست، listener یا observer اضافه کند؛ ثابت‌ماندن Bundle اثبات FPS/INP یا سرعت هاست نیست.

## ساختار صفحه و سکشن

هر صفحهٔ سفارشی از سکشن‌های نام‌دار در `template-parts/sections/<page>/` ترکیب شود؛ داده/helper در `inc/<page>.php`، CSS/JS همان صفحه زیر `src/.../pages/` و رفتار قابل استفادهٔ مجدد در UI/runtime باشد. فایل خالی یا کپی منطق برای تقارن نسازید. منابع صفحه فقط در مسیر مصرف خودش انتخاب شوند؛ تعامل ساده با CSS/Vanilla کافی است و GSAP فقط برای نیاز واقعی اضافه شود.

یک h1 برای عنوان اصلی صفحه، h2 برای سکشن اصلی، h3 برای عنوان خدمات/کارت؛ شماره، آمار و برچسب تزئینی p/span باشند. به خاطر ظاهر، سطح heading عوض نشود. رسانهٔ محتوایی با attachment ID و API وردپرس، اندازهٔ ذاتی، srcset و sizes مصرف شود؛ فایل تصاویر مشتری داخل قالب کپی یا URL محیط قبلی hardcode نشود. تصاویر زیر fold lazy باشند. قالب‌ها بدون JS محتوای معنادار و لینک معتبر داشته باشند؛ RTL/LTR، کیبورد، reduced-motion و cleanup حفظ شوند.

صفحهٔ خدمات از سکشن showcase و چهار زیرصفحهٔ موجود استفاده می‌کند؛ عنوان و خلاصهٔ موجود برگه‌ها اولویت دارند، تصویر از نگاشت رسانهٔ وردپرس می‌آید و آمار از تعداد نمونه‌کارهای منتشرشده خوانده می‌شود. فهرست details بومی است؛ preview و انتخاب با تعامل کاربر بهبود می‌یابد و بدون JS هم متن و لینک‌ها قابل استفاده‌اند.

فوتر تمام‌عرض و چهارستونه است: تماس/برند، خدمات، شبکه‌ها و دعوت به پروژه در انتهای چیدمان (لبهٔ چپ در RTL). لیست خدمات از دادهٔ مشترک Services خوانده و در درخواست cache می‌شود؛ افزودن ستون نباید به محدودکردن عرض یا وسط‌آوردن کل فوتر تبدیل شود.

فوتر یک renderer مشترک در `template-parts/layout/footer.php`، دادهٔ متمرکز در `inc/footer.php` و CSS در `layout/footer.css` دارد. جای فرم خبرنامه، دعوت به شروع پروژه و لینک تماس است؛ اطلاعات نمونه تا تأیید مالک باقی می‌ماند و شماره/ایمیل/شبکهٔ بدون مقدار واقعی، لینک ساختگی ندارد. منوی footer بومی اولویت دارد؛ در نبود آن مسیرهای عمومی موجود مصرف شوند. بازگشت بالا از موتور اسکرول مشترک و fallback بومی استفاده می‌کند؛ آیکون‌ها محلی و بافت تزئینی CSS است.

## Page Title

Only `cover` (selected static media hero) and `split` (approved two-column title) remain in `template-parts/layout/titles/`. `cover` is the default for ordinary internal WordPress pages; client data supplies a featured attachment or the existing local hero attachment as fallback. `split` stays available for explicit future assignment. Removed notched/type/launch/wide/craft layouts must not be restored without a request.

Cover is a single static media hero, not a slider: breadcrumb and a compact Hafez heading sit in a vertically centered copy block; description/shared CTA share a wrapping row. No counters, side labels, tag strip or scroll hint. Match the home hero horizontal geometry: width calc(100% - 2 * --rdc-gut), centered with no max-width cap; copy has the same 680px cap and .75rem safe inline padding as home. Do not use rdc-ctr here: its 1450px cap pulls the copy inward on wide screens. Keep description/CTA close, without a full-width decorative rule, and retain home heading size/color tokens. Existing cover/split owners remain; this redesign does not restore the removed launch template.

`inc/page-title.php::is_used()` gates both the renderer and its CSS: no Page Title on the front page, blog index, archives, search, 404, posts or project details. The small `radmanct/page-title/enabled` client filter currently allows services, contact-us, about-us and every single service page from radmanct/services/specs. Products and all other routes remain unassigned. Keep route exceptions in `inc/client.php`; do not add a settings engine or change stored content. The shared dispatcher returns without markup when disabled.

All assigned public pages consume the same cover renderer, CSS and motion. Client data supplies label, description and a shared-button action on services, each single service, about and contact; service descriptions reuse radmanct/services/specs and stored page excerpts take priority. Contact CTA points to services rather than linking to itself. Do not add route-specific markup/CSS or duplicate service descriptions to make these pages consistent. Private/password guards may intentionally remove secondary content.

Data uses `radmanct/page-title/data` or explicit template args: type/title/accent/desc/label, button/link, image/video attachment IDs and trail. Preserve escaped output, password/private-content guards, local Hafez for the single title h1, Peyda body, logical CSS, shared buttons and intrinsic attachment dimensions/srcset. No separate title is currently assigned to blog/archive/detail routes; their final heading design is deferred.

Title CSS loads only where the title is used. A valid cover image or video loads the page-title entry. Its desktop motion follows Orisa services-1: speed .4 through the existing ScrollSmoother effects owner; scale 1 to 1.15 through the shared media-parallax zoom profile, top bottom to bottom center, scrub 1 and power1.out. Translation belongs to the inner wrapper; scale belongs to image/video, so their transforms do not compete. No text/heading movement or extra pin is added. At widths below 1024px or reduced motion, media is static. Native desktop mode retains zoom without a second scroll engine. Video keeps its lazy local source, image fallback, pause/play, reduced-motion, Save-Data, visibility guards and cleanup. Only cover consumes this motion; split and excluded routes do not load its entry. Reference: https://orisa-html-demo.pages.dev/services-1 and its assets/js/main.js, scroll-scale-up-img section.

زیرصفحهٔ `custom-website-design-development` مالک مستقل `page-custom-website-design-development.php` دارد؛ همان عنوان صفحه، محتوای بومی موجود و renderer مشترک FAQ خدمات را مصرف می‌کند. کلاس `rdc-inv` روی body در دادهٔ مشتری، زمینهٔ مشکی مشترک را حفظ می‌کند. فقط CSS کوچک `pages/service` روی این مسیر بارگذاری می‌شود؛ showcase، marquee و JS صفحهٔ خدمات اصلی به این صفحه تحمیل نمی‌شوند. محتوای رمزدار باید پیش از نمایش محتوای صفحه/FAQ از کنترل وردپرس عبور کند.

## Contact hero artwork

هیروی تماس تصویر شاخص بومی همان برگه را مصرف می‌کند؛ اصل `ChatGPT Image Nov 13, 2026, 07_19_30 PM.png` محفوظ است. فایل نهایی `contact-hero-flat-background.png` زمینهٔ نزدیک به مشکی را در خود پیکسل‌ها با #0e0e0d هماهنگ می‌کند؛ تمام فضای خالی سمت راست همین RGB ثابت را دارد. PNG و اندازه‌های بومی آن برای حفظ مقدار رنگ استفاده می‌شوند. روی تصویر CSS blend/mask/filter نگذارید؛ `media_flat` فقط زمینهٔ قاب را از توکن مشترک می‌گیرد. `media_inset` ارتفاع 100svh و اسکرول مشترک .4 را حفظ می‌کند و zoom مستقل ندارد.

خط‌ها، قوس‌ها و شبکهٔ نقطه‌ای سه‌ستونی/پنج‌ردیفی پایین چپ حفظ شوند. نقطه‌های ثابت روی مسیرهای حرکت در فایل حذف شده‌اند؛ چهار `art_tracks` مستقل همان art-orbit مشترک را مصرف می‌کنند. چهار نقطهٔ ثابت کم‌رنگِ `art_nodes` روی خط‌های جدا قرار دارند؛ قطر ثابت/متحرک مثل خانه `.18%` عرض قاب و opacity ثابت‌ها .45 است. سه نقطهٔ داخل آیکون گفت‌وگو جزو خود آیکون‌اند. این تصویر یا تنظیمات به خانه/خدمات تعمیم داده نشوند.

قالب Cover، متن، CTA، تصویر و renderer تزئیناتِ برگه‌های داخلی یک مالک مشترک دارند؛ renderer تزئینات `template-parts/ui/title-art.php` است. فقط دادهٔ عکس/متن/هندسه در `inc/client.php` تفاوت دارد؛ کپی PHP/CSS/JS برای هر صفحه نسازید. وقتی خط جداگانه رسم می‌شود، خود path حرکت همان خط قابل‌دیدن باشد (`art_draw`) تا شکل و حرکت از هم جدا نشوند. خانه چیدمان اختصاصی خود را دارد و همان art-orbit مشترک را مصرف می‌کند.

## Website service hero artwork

تصویر شاخص `custom-website-design-development` از `Cinematic Web Development Interface.png` و نسخهٔ محلی `website-hero-flat-background.png` می‌آید. خود فایل زمینهٔ #0e0e0d دارد؛ PNG ۱۹۲۰×۱۰۸۰ با طرح ۱۶۸۰×۹۴۵ در مبدا، بدون blend/mask/filter یا تغییر اندازهٔ تصویر با CSS. سه قطعهٔ قوس با endpointهای مشترک، یک بیضی گردِ بسته و نزدیک به سوژه در `art_tracks` می‌سازند؛ سه مسیر جدا و قطع‌شده نباشند. `art_draw=true` همان pathهای حرکت را قابل‌دیدن می‌کند؛ تعریف دوبارهٔ خط یا خط زاویه‌دارِ دور از تصویر مجاز نیست. سه نقطه روی همین سه path حرکت می‌کنند؛ نقطه‌های ثابت روی اتصال قطعات‌اند، جایی که موشن در ابتدا/انتهای مسیر محو می‌شود. شبکهٔ ۳×۵ پایین چپ حفظ می‌شود. قطر نقطه‌ها همان `.18%` خانه/تماس است. فقط custom-website-design-development این هندسه را انتخاب می‌کند؛ فایل/مسیر تماس، خدمات و خانه تغییر نکنند.

## About hero artwork

دربارهٔ ما از `ChatGPT Image Nov 13, 2026, 07_45_41 PM.png` و نسخهٔ محلی `about-hero-flat-background.png` استفاده می‌کند. تصویر مستقل با زمینهٔ واقعی #0e0e0d و قاب ۱۹۲۰×۱۰۸۰؛ سوژه در ۱۶۸۰×۹۴۵ قرار دارد. خط‌ها، آیکون‌ها و شبکهٔ نقطه‌ای خود تصویر حفظ می‌شوند؛ سه مسیر حرکت با هندسهٔ همین عکس نگاشت شده‌اند و art_draw خاموش است تا خط دوباره رسم نشود. تصویر/attachment طراحی وب مستقل است و جابه‌جایی هیرو نباید محتوای FAQ یا قالب مشترک را تغییر دهد.

## WordPress service hero artwork

تصویر `ChatGPT Image Nov 13, 2026, 09_57_18 PM.png` فقط برای `specialized-wordpress-design-development` است؛ نسخهٔ `wordpress-hero-flat-background.png` با زمینهٔ واقعی #0e0e0d و قاب ۱۹۲۰×۱۰۸۰ مصرف می‌شود. خطوط موجود عکس حفظ می‌شوند؛ سه مسیر متحرک روی آن‌ها نگاشت شده‌اند و شش نقطهٔ ثابت از همان renderer مشترک می‌آیند. نقطه‌های تزئینی بزرگ رستریِ این عکس و عکس جدید دربارهٔ ما در آماده‌سازی رسانه حذف و خط زیرشان ترمیم می‌شود؛ نقاط معنایی آیکون‌ها/کنترل‌های داخل تصویر دست‌نخورده‌اند. اصل فایل‌ها حفظ شود.

اندازهٔ نقطهٔ اصلی هیروی خانه و تمام نقطه‌های متحرک/ثابت عنوان صفحه فقط از `--rdc-art-node-sz:.18%` در tokens.css بیاید؛ مقدار مستقل یا clamp جدا برای هر صفحه نسازید. ثابت‌ها همان اندازه و کم‌رنگ‌ترند؛ شبکهٔ نقطه‌ای ریز یک نقش تزئینی مستقل است. CSS تصویر blend/mask/filter ندارد؛ همان renderer و runtime مصرف می‌شوند و هندسهٔ عکس‌ها مستقل می‌ماند.

## Web systems hero artwork

`admin-panel-web-systems` از `ChatGPT Image Nov 13, 2026, 10_07_51 PM.png` و `systems-hero-flat-background.png` استفاده می‌کند: قاب ۱۹۲۰×۱۰۸۰، تصویر در ۱۶۸۰×۹۴۵ و زمینهٔ واقعی #0e0e0d. سه مسیر روی قوس موجود تصویر و شش نقطهٔ ثابت هم‌اندازه از renderer مشترک می‌آیند؛ art_draw خاموش است. پیکسل‌های تصویر اصلی این طرح به‌صورت ۱:۱ داخل قاب کپی شوند؛ resampling انجام نشود و مختصات مسیرها در مقیاس بومی عکس بمانند. اصلاح نزدیک‌مشکی فقط روی نواحیِ زمینهٔ متصل به لبه‌ها انجام شود، نه داخل سطوح/سایه‌های بستهٔ سوژه. فقط نقطه‌های کوچک تزئینیِ رستری هم‌اندازه‌سازی شوند؛ اتصال‌های درخشانِ ماژول‌ها جزو خود تصویرند. خط، شبکهٔ نقطه‌ای، رنگ طلایی و ساختار تصویر حفظ شوند. CSS/JS صفحه یا موتور تازه ندارد.

## Mobile service hero artwork

`mobile-app-design-development` از `تصميم اپلیکیشن موبایل با درخشش طلایی.png` و نسخهٔ `mobile-hero-flat-background.png` استفاده می‌کند. تصویر اصلی به‌صورت ۱:۱ داخل قاب ۱۹۲۰×۱۰۸۰ کپی می‌شود؛ اصلاح رنگ فقط زمینهٔ متصل به لبه‌ها را هدف می‌گیرد و فضای خالی راست #0e0e0d ثابت دارد. سه مسیر روی قوس‌های خود عکس و شش نقطهٔ ثابت از اندازهٔ مشترک مصرف می‌شوند؛ خطوط، نقش نقطه‌ای و آیکون‌های Android/Apple حفظ شوند. عکس، CSS و runtime مصرف‌کنندگان دیگر تغییر نکنند؛ موتور/استایل موازی اضافه نشود.

## Heading semantics

سطح تگ بر اساس ساختار محتوا انتخاب شود، نه اندازه یا فونت: یک H1 برای موضوع اصلی صفحه، H2 برای بخش اصلی واقعی و H3 برای خدمات/کارت‌های زیر آن بخش. برای همهٔ نوشته‌های بزرگ H2 نسازید؛ عنوان واقعی را هم صرفاً برای کاهش تعداد heading به span تبدیل نکنید. برچسب، شماره، آمار و متن تبلیغاتی/دعوت به اقدام از p/span استفاده کنند. span درون عنوان فقط برای محتوای ترکیبی، شکست طراحی یا افکت لازم مجاز است؛ wrapper دور حروف صرفاً برای استایل اضافه نشود. فونت، وزن، اندازه و فاصلهٔ ظاهری با کلاس scoped مستقل از تگ حفظ شوند؛ فونت پایهٔ متن تغییر نکند.

در صفحهٔ خدمات: «خدمات» H1، «راهکارهای ما» و «سؤال‌های متداول» H2، چهار خدمت H3؛ سؤال‌های details متن داخل summary هستند. متن دعوت به گفت‌وگو بالای گالری و دعوت عمومی فوتر p هستند؛ خط‌های نمایشی، شماره‌ها و آیکون‌ها heading جدا نیستند. برچسب‌های تزئینیِ شماره/تصویر از نام دسترس‌پذیر عنوان حذف می‌شوند. متن CTA فوتر در مالک مشترک اصلاح شود، نه با استثنای صفحهٔ خدمات. این قرارداد از اصل عنوان واقعی و DOM کمینهٔ قالب قبلی پیروی می‌کند؛ معماری Elementor آن به این قالب منتقل نمی‌شود.

## رنگ، فونت و تگ‌ها

`src/css/tokens.css` تنها مالک مقادیر رنگ، font-face، خانواده، اندازه و فاصلهٔ پایه است. طلایی اصلی `#C59032`، متن/عنوان روی زمینهٔ تیره `#ece9e2` و متن روی زمینهٔ روشن تیره است؛ رنگ‌های جزئی تصویر و متن ثانویه نقش مستقل دارند. خطوط و border خنثی همهٔ بخش‌های تیره از `--rdc-clr-ln-inv: rgba(236,233,226,.12)` مشترک می‌آیند؛ حالت تعاملی accent/focus مستقل است و رنگ مرز تیره در صفحه‌ها تکرار نشود. دارک‌مود، تشخیص تم سیستم و تغییر تم نداریم. `.rdc-inv` فقط زمینهٔ ثابت تیرهٔ طراحی را مشخص می‌کند؛ بخش روشن مقادیر پایه را می‌گیرد.

زمینهٔ اصلی صفحات تیره مطابق مرجع StudioRS رنگ یکدست `#0e0e0d` از `--rdc-clr-dark` است، نه گرادینت؛ توکن hero نیز به همین مقدار اشاره می‌کند. فوتر استثنای تأییدشده است: زمینهٔ `#090908` از `--rdc-clr-ink` با حفظ تصویر تزئینی، opacity و چیدمان لایه‌های فعلی؛ یکسان‌سازی زمینهٔ صفحات نباید تصویر فوتر را حذف یا بازنویسی کند. لایهٔ نویز مرجع جزو رنگ نیست و در این قالب اضافه نمی‌شود. پس‌زمینهٔ مستقل سکشن فقط با درخواست مالک اضافه شود. در صفحهٔ خدمات، body با کلاس `rdc-inv` مالک زمینه است و سکشن خدمات/کادر رسانه زمینهٔ جدا ندارند. نوع split عنوان نیز گرادینت رنگی مستقل ندارد؛ overlay لازم روی تصویر/ویدئو، دکمه و سطوح تعاملی با زمینهٔ اصلی صفحه یکی فرض نشوند.

`base.css` یک‌بار قواعد body، تگ‌های h1 تا h6 و کنترل‌ها را تعریف می‌کند: Hafez فقط برای h1؛ Peyda برای متن و h2 تا h6. margin/padding بومی تگ‌های متن صفر است؛ فاصلهٔ محتوا فقط در scope محتوای مقاله و فاصلهٔ layout در مالک همان سکشن تعیین می‌شود. span و لینک از والد ارث می‌برند. فونت و رنگ‌های مشترک حتی هنگام تغییر طراحی دوباره کپی نشوند؛ فقط تفاوت واقعی طراحی با scope همان بخش اعمال شود. تفاوت اندازه، ارتفاع خط و رنگ یک سکشن با متغیر/قانون scoped همان سکشن تعریف شود؛ به خاطر یک طرح root یا تگ عمومی بازنویسی نشود. متغیر قطعی fallback تکراری نمی‌خواهد. عنوان اصلی h1 و سطح عنوان‌های دیگر تابع ساختار محتوا است، نه تعداد کارت‌ها.

## بارگذاری و اشتراک

`inc/assets.php` فقط خروجی manifest را انتخاب می‌کند: site مشترک، home فقط خانه، services فقط برگهٔ خدمات، blog مشترک با archive یا single مرتبط، portfolio با archive یا single مرتبط. سورس ماژولار لزوماً به معنی یک درخواست برای هر قطعه نیست؛ بخش‌های همیشه‌همراه در entry مربوط گروه‌بندی شوند. فایل خام، editor، preset بی‌مصرف یا وابستگی سراسری اضافه نشود. `npm run build` minification واقعی می‌کند؛ Node روی هاست لازم نیست.

عنوان و خلاصهٔ کارت‌های وبلاگ/نمونه‌کار از `ui/card-text.css` و کلاس مشترک دوخطی با فضای ثابت استفاده می‌کنند؛ متن کامل ذخیره‌شده دست‌نخورده می‌ماند.

نمایش بزرگ تصویر گالری در `src/js/ui/gallery.js` یک مالک مشترک دارد؛ فقط در جزئیات نمونه‌کارِ بدون حفاظت رمز بارگذاری می‌شود و CSS آن متعلق به همان صفحه است. کد گالری داخل entry سراسری سایت نباشد؛ cleanup باید listenerها و قفل اسکرولِ پنجرهٔ باز را آزاد کند. لینک اصل تصویر بدون JS معتبر می‌ماند.

دکمه، جستجو، دسته‌بندی، آیکون و رسانه renderer/رفتار مشترک دارند؛ نسخهٔ جدا برای هر صفحه نسازید. دستهٔ بدون فرزند فلش ندارد. CPT برابر `project` و taxonomy برابر `project_category` است؛ نام فایل‌ها تابع نقش و template hierarchy است. شناسه‌ها و meta ذخیره‌شده حفظ شوند.

رسانهٔ کارت می‌تواند تصویر با پیش‌نمایش ویدئوی hover/auto باشد؛ کنترل بومی مدیریت فقط در editor بارگذاری می‌شود. reduced-motion، Save-Data، خروج از دید و محدودیت پخش هم‌زمان رعایت شوند. صوت/ویدئو داخل نوشته از editor بومی و badge مرتبط استفاده می‌کند. گالری و صفحه‌بندی بدون JS لینک معتبر دارند؛ جستجو/Load More فقط پاسخ هم‌مبدأ و مجاز را مصرف می‌کنند.

پری‌لودر ثابت سایت همان Dissolve مشکیِ ۲۷ نواری است؛ فقط همین ظاهر در پایه وجود دارد. راه‌اندازیِ کوچک از خروجی فشرده در body-open انجام می‌شود؛ بدون JS مخفی است، در reduced-motion اجرا نمی‌شود و timeout/تعامل یا بازگشت از bfcache آن را آزاد می‌کند. برای آن vendor، preset یا پنل تنظیمات اضافه نشود.

## افکت و کارایی

افکت تازه فقط با درخواست مالک؛ یک رفتار منتخب دکمه و منو، بدون انتقال registry یا استایل‌های متعدد قالب عمومی. استیکی تصویر خدمات و سایدبار وبلاگ از `runtime/sticky.js` مشترک استفاده می‌کنند: pin در محدودهٔ بخش با موتور موجود، fallback بومی در حالت بدون motion، refresh تجمیعی هنگام تغییر اندازه/فونت و cleanup در تغییر breakpoint. CSS sticky تنها برای محتوای transformedِ اسکرول نرم کافی نیست.

GSAP محلی یک instance مشترک دارد و فقط با مصرف افکت وارد می‌شود. ScrollSmoother فقط desktop با pointer دقیق و بدون reduced-motion فعال است. انیمیشن‌ها در خروج از دید/صفحه متوقف و در teardown پاک شوند؛ Load More هم lifecycle دارد.

استیکی خدمات فقط برای فوکوس `:focus-visible` به چیدمان عادی برمی‌گردد و پس از خروج فوکوس دوباره فعال می‌شود؛ کلیک موس به‌تنهایی آن را خاموش نمی‌کند. هنگام حذف pin، فوکوس حفظ و جای عمودی هدف از رویداد `rdc:fcs-aln` به مالک اسکرول مشترک سپرده می‌شود تا در حالت native و ScrollSmoother پرش نسازد. پری‌لودر در هر session تب یک‌بار نمایش داده می‌شود؛ دسترسی ناممکن به sessionStorage به همان اجرای محدود زمانی قبلی برمی‌گردد و gate ورود هیرو مستقل باقی می‌ماند.

پنل مشترک در `layout/menu-panel.php` است: دسکتاپ معرفی کوتاه، نمونه‌کار منتشرشدهٔ بدون رمز و دارای تصویر، دعوت به همکاری و اطلاعات کوچک مشترک فوتر را دارد؛ منوی هدر در آن تکرار نمی‌شود. زیر ۱۰۲۴px، پنل منوی بومی WordPress با زیرمنو و بازگشت مرحله‌ای است. محتوای تصویری دسکتاپ در template غیرفعال می‌ماند و فقط هنگام بازشدن پنل دسکتاپ یک‌بار mount می‌شود؛ موبایل فایل آن را دریافت نمی‌کند. تغییر breakpoint حالت غیرفعال را hidden/inert می‌کند و فوکوس در پنل باز محفوظ می‌ماند. ID منو با هدر متفاوت، لینک‌های والد واقعی و زیرمنوها همان DOM اصلی‌اند؛ reset/cleanup آنها را برمی‌گرداند. موج و ورود پلکانی فقط محتوای فعال را در یک timeline کنترل می‌کنند؛ محتوای زمان گذار inert و reduced-motion حفظ شود.

منوی فعال فقط یک مسیر دارد؛ fragmentهایی مثل `#services` آیتم فعال مستقل نیستند. هاور/فوکوس نباید چند نقطهٔ هم‌زمان بسازد. دکمهٔ بستن پنل همان کادر مشترک بازکننده را دارد؛ موقعیت و اندازه از rect واقعی بازکننده هنگام بازشدن/resize گرفته می‌شود تا RTL/LTR و admin-bar دقیق بمانند. ضربدر با شروع بستن محو می‌شود و بعد از پنل باقی نمی‌ماند؛ چرخش کوتاه آن تابع reduced-motion است. بستن پنل، حتی هنگام import معلق یا کلیک تکراری، باید یک‌بار تا پایان انیمیشن اجرا شود و سپس dialog بسته و قفل اسکرول آزاد شود. پنل dialog بومی Escape و focus را حفظ می‌کند؛ موتور موج فقط هنگام مصرف و بدون reduced-motion درخواست می‌شود.

در هر فریم اندازهٔ سند دوباره خوانده نشود؛ محاسبات هندسه در resize/refresh انجام شوند. cursor یک نوشتن موقعیت در RAF و توپ‌ها یک transform ترکیبی دارند. ابعاد ذاتی تصویر حفظ، محتوای LCP منتظر انیمیشن و بارگیری lazy نشود. build و آزمون کنترلر اثبات FPS یا سرعت هاست نیستند. Cursor behavior lives in shared ui/cursor CSS/Vanilla: shrink only on interactive controls; labels use accessible names or visible control text, with data-rdc-cur-lbl for custom text and data-rdc-cur="off" to opt out. Use data-rdc-cur-tip="off" on a control or container to suppress only its label while retaining cursor shrink; only the desktop header primary navigation and its submenus currently consume this exception; footer navigation, panel navigation and menu controls retain labels. Tooltip uses a translucent ink surface, cream text and the shared inverse border; transparency applies only to the surface. Keep native input cursors, touch/reduced-motion guards, one pending RAF, dialog top-layer support and cleanup; no extra animation engine.

متن تولتیپ ماوس تک‌خطی است؛ عرض به فضای صفحه محدود می‌شود و متن خیلی بلند با سه‌نقطه کوتاه می‌شود، بدون کوچک‌شدن آیکون. جهت نمایش بر اساس نیمهٔ viewport انتخاب می‌شود تا با عرض مجاز هماهنگ باشد؛ اندازه‌گیری DOM در هر فریم برای این کار اضافه نشود.

## محیط و داده

Compose با نام `webtalik`، آدرس `127.0.0.1:8095` و دیتابیس/شبکه/volume مستقل است؛ Start/Stop داده را حفظ می‌کنند. اطلاعات ورود فقط محلی و خارج Git است. رسانهٔ منتقل‌شده و نگاشت‌های شناسه جزو داده‌اند؛ به عنوان فایل اضافی حذف نشوند. آرشیو انتقال و ابزار یک‌بارمصرف جزو معماری جاری نیستند.

ترجمه از `languages/fa_IR.json` با `tools/build-translations.mjs` ساخته می‌شود. تمام منابع اجرای سایت محلی‌اند: Google Fonts، CDN، فونت/آیکون/کتابخانهٔ خارجی، telemetry و دانلود زمان اجرا ممنوع است. فایل اصلی هر فونت فقط در `radmanct/fonts/` نگهداری می‌شود؛ build آن را به dist کپی نمی‌کند و CSS خروجی و preload همان URL محلی را مصرف می‌کنند. افزودن فونت فقط از فایل محلی و تعریف مرکزی انجام شود؛ CSS یا JS صفحه نباید اتصال خارجی بسازد. این قرارداد مجوز تغییر به‌روزرسانی‌های مدیریت WordPress نیست. تست کوتاه مرتبط کافی است؛ بسته، انتشار، تغییر نسخه یا Git push خودکار انجام نشود.

عنوان‌های سکشن‌های استیکی خدمات صفحهٔ اول استثنای طراحی‌اند و از توکن مشترک Hafez استفاده می‌کنند؛ متن، زیرعنوان‌های دیگر و منوی پنل Peyda می‌مانند. تگ معنایی عنوان‌ها تغییر نمی‌کند.

موشن هویت برند از SVG رسانه با renderer محدود `inc/brand.php` و CSS/Vanilla مشترک است؛ IDهای SVG در هر نمونه یکتا و ارجاع خارجی/اسکریپت نامجازند. پس از پایان پری‌لودر و هنگام مشاهده، دو نوار اندکی باز و با تأخیر کوتاه دوباره در هم می‌نشینند و لوزی در پایان قفل می‌شود؛ چرخهٔ ۶٫۵ ثانیه‌ای شامل حدود ۱٫۶ ثانیه حرکت و باقی مکث است. ورود به صفحه و pageshow در بازگشت مرورگر، موشن لوگوی قابل‌دیدن را از ابتدا اجرا می‌کند. لایه‌های گرادینت هر نوار یک حرکت هماهنگ دارند؛ بیرون viewport متوقف است؛ reduced-motion و cleanup حفظ می‌شوند و vendor جدید بارگذاری نمی‌شود.

عنوان‌های Hafez از قاعدهٔ مشترک `h1/.rdc-dsp-ttl` با فضای امن جوهر حروف در دو جهت استفاده می‌کنند؛ این فاصله داخل عرض عنوان است تا مرزهای افکت/سکشن امتداد حروف را قطع نکنند. overflow یا ماسک سکشن برای رفع بریدگی متن به‌صورت سراسری غیرفعال نشود.

رسانهٔ کارت نوشته/نمونه‌کار مشترک است: تصویر شاخص پوستر می‌ماند و ویدئوی رسانه اختیاری با حالت auto/hover، همیشه بی‌صدا و loop پخش می‌شود؛ کارت‌ها دکمهٔ Play/Pause یا کنترل native ندارند. محدودیت دو پیش‌نمایش هم‌زمان، توقف خارج دید، reduced-motion و صرفه‌جویی داده حفظ شوند. گالری جزئیات نمونه‌کار در همان `_webtalik_project.gallery` ذخیره و از انتخابگر بومی رسانه مدیریت می‌شود؛ سایر متای پروژه حفظ شود. گالری محتوای وبلاگ از بلوک بومی editor است. تصاویر خصوصی یا متعلق به محتوای محافظت‌شده در گالری عمومی نمایش داده نشوند. پس‌زمینهٔ تزئینی فوتر WebP شفاف از رسانه است؛ رنگ زمینه فقط از توکن فوتر می‌آید.

کنترل سرریز افقی با `overflow-x:clip` روی ریشهٔ `html` در CSS پایه انجام می‌شود تا عنوان‌ها و جابه‌جایی‌های افکت اسکرول افقی نسازند. این قاعده روی body/wrapper تکرار نشود و برای رفع سرریز، `overflow-y:hidden` سراسری اضافه نشود؛ قفل اسکرول منو و پنجره‌ها مالک مستقل خود را دارد.

اندازه‌های تایپوگرافی و دکمه با rem و توکن‌های مالک در tokens.css تعریف شوند؛ مقدار پیکسلیِ اعلام‌شدهٔ طراحی ابتدا تبدیل شود، نه اینکه در صفحه‌ها تکرار شود. رنگ، فونت، فاصلهٔ داخلی و موشن دکمهٔ اصلی در renderer مشترک inc/ui/button.php و ui/button.css هستند؛ صفحه فقط چیدمان بیرونی مثل تمام‌عرض بودن را تعیین می‌کند. آیکون هر مصرف از طریق آرگومان icon در همان renderer انتخاب می‌شود؛ مشترک‌بودن دکمه به معنی یکسان‌بودن آیکون همهٔ کاربردها نیست. کاتالوگ و ضخامت خط ۱٫۵ در inc/icons.php مشترک‌اند؛ آیکون جهت‌دار تولتیپ نیز همان SVG را مصرف می‌کند و فقط آیکون‌های جهت‌دار در RTL آینه می‌شوند. دکمه‌های آیکونیِ کنترل پنل/پلیر جایگزین این لینک CTA نمی‌شوند. تغییر طراحی مشترک باید روی همین مالک اعمال شود؛ برای دکمه، سایدبار و دسته‌بندی نسخهٔ موازی در هر صفحه نسازید. حرکت آیکون دکمه CSS، سازگار با RTL و reduced-motion است؛ انیمیشن liquid قبلی حذف شده است.

نمونه‌کارهای منتخب صفحهٔ اول در home/projects مالک دارند: فهرست دستی selected_work شامل عنوان، خلاصه، دسته‌های نمایشی و attachment ID است؛ به رکورد نمونه‌کار، taxonomy یا query آن وصل نشود. فقط تصویر از رسانهٔ وردپرس با ابعاد ذاتی و srcset خوانده شود. سکشن تمام‌عرض با گاتر مشترک است؛ نوشتهٔ استیکی سمت راست و تصاویر سمت چپ، در موبایل معرفی سپس تصاویر. کارت لینک جزئیات ندارد؛ تنها CTA به آرشیو می‌رود. استیکی و حرکت تصویر از runtime مشترک‌اند و فقط در صفحهٔ اول مصرف می‌شوند؛ انتخاب محتوا مستقل از موشن است. موشن منتخب: زوم ورود از scale 1.2 و autoAlpha 0 به حالت عادی، duration 2 و power2.out؛ حرکت دوطرفهٔ اسکرول فقط با effects موتور ScrollSmoother موجود و speed .8، بدون tween دستی y و بدون موتور دوم. ثبت اثر انتخابی و cleanup در runtime/scroll.js است؛ موبایل و reduced-motion بدون آن می‌مانند.

موشن متن در ui/text-motion.js مشترک است: addEntrance برای ورود از پایین و addTextFill برای پُرشدن opacity کلمه‌ها. حروف فارسی جدا نشوند؛ فاصله‌ها، نشانه‌های درون متن و گره‌های اصلی هنگام cleanup حفظ/برگردانده شوند. ورود بلوک‌های عادی با IntersectionObserver و تایم‌لاین زمانی مستقل، یک‌بار هنگام دیده‌شدن اجرا می‌شود؛ پنل‌های روی‌همِ استیکی از تشخیص viewport استفاده نکنند و فرمان ورود را فقط از مرحلهٔ مربوط در تایم‌لاین مالک بگیرند. بازسازی استیکی ورود همان DOM را تکرار نمی‌کند. در سکشن‌های پین‌شده فقط پُرشدن متن داخل timeline اسکرول مالک قرار می‌گیرد و scrub رفت‌وبرگشت دارد؛ ورود را به scrub وصل نکنید و ScrollTrigger فرزندِ تودرتو نسازید. چهار خدمات خانه و متن معرفی/متادیتای نمونه‌کارهای خانه همین رفتارها را مصرف می‌کنند؛ scene هنگام resize، reduced-motion، فوکوس یا خروج باید split و استایل‌ها را بازگرداند. موبایل و حالت کاهش حرکت متن عادی و خوانا دارند.

ورود اولیهٔ هیرو از initializeEntrance مشترک با data-rdc-ent/data-rdc-en اجرا می‌شود: یک‌بار پس از پایان preloader، بدون ScrollTrigger و مستقل از rotator، پارالاکس و پُرشدن متن با اسکرول. ورودِ گروه‌های متن روی wrapper انجام شود تا transform واژه‌های چرخشی تداخل نکند؛ تصویر LCP منتظر ورود متن نماند. cleanup و reduced-motion باید استایل‌های اصلی را بازگردانند.

پروفایل ورود مشترک بر مبنای تنظیمات Hub ارسالی است: ease=power4.out و مدت پایهٔ ۱٫۶ ثانیه از مرجع محلی Hub؛ عنوان‌ها کلمه‌به‌کلمه با x=-1.25rem در RTL (معکوس در LTR) و rotationX=10، پاراگراف با y=2.8125rem و rotationX=45، گروه محتوا با همان جابه‌جایی عمودی. stagger پایهٔ کلمه/گروه ۰٫۱۶ ثانیه و تأخیر پاراگراف ۰٫۲ ثانیه است. فقط Hero خانه پروفایل مستقل با duration=1.1، stagger=.07 و transformPerspective=800 دارد؛ سایر ورودها همین مقادیر مشترک را حفظ می‌کنند. تمام پروفایل‌ها در مالک text-motion تعریف شوند، نه CSS/JS هر صفحه. متنِ بیش از ۸۰ کلمه برای ورود و بیش از ۲۰۰ کلمه برای fill، به‌صورت بلوک و بدون ساخت span انبوه حرکت می‌کند. مدت ۱٫۶ در HTML سایت نمونه صریح نبود و مبنای طراحی فعلی است؛ تطابق بصری تأییدشده محسوب نشود.

مالک اسکرول، نمونهٔ واقعی ScrollSmoother را در runtime/scroll-state.js نگه می‌دارد و تغییر آن را با rdc:scr-mode اعلام می‌کند؛ media query به‌تنهایی نشانهٔ فعال‌بودن موتور نیست. Parallax در Smooth از scrub:true و در Native از .5 استفاده می‌کند و تغییر حالت بدون ساخت tween دوم اعمال می‌شود. Sticky عمومی فقط در Smooth نوع transform را تحمیل می‌کند؛ در Native انتخاب با ScrollTrigger است. Reveal یک‌بارمصرف است، immediateRender:false دارد و پس از پایان، transform/opacity/visibility را آزاد می‌کند؛ اگر هنگام رسیدن vendor تصویر وارد دید شده یا از آن گذشته باشد، دوباره مخفی نمی‌شود.

ResizeObserver و MutationObserver در runtime/observers.js بر اساس window مشترک‌اند؛ هر مصرف فقط اشتراک خود را آزاد می‌کند و خطای یک callback، تحویل سایر مصرف‌ها را قطع نمی‌کند. درخواست‌های معمول Refresh در runtime/refresh.js طی یک بازهٔ ۱۰۰ میلی‌ثانیه‌ای تجمیع و با refresh(true) اجرا می‌شوند؛ ایجاد/حذف pin در scene همچنان refresh سراسری فوری دارد و کارهای معلق را هم پوشش می‌دهد. خاموش‌بودن استیکی دسکتاپ در موبایل نیازمند بازسازی با تغییر ارتفاع نوار مرورگر نیست. مقدار smooth:1.6 و Hover منتخب منو بدون شاهد معتبر برای تغییر ظاهر یا کارایی حفظ شوند؛ ignoreMobileResize یا آستانهٔ ارتفاع حدسی فعال نشود.

## Detail continuation

Post/project singles share `inc/content/continuation.php`, `layout/continuation.php` and `css/layout/continuation.css`; mount once after the main detail content, outside the blog sidebar grid. Keep the same container as that detail page. Load the helper and styles only for consuming singles, with no extra JS.

Previous/next uses native WordPress adjacency in the same post type, chronological order and core caching. Scope the temporary where filters to this call and remove them in finally; exclude password-protected and non-published targets even for editors. Hide missing directions; do not wrap first/last items. Private or locked current content renders no continuation.

Recommend up to three other public, unprotected items from category/project_category, excluding the current ID. If none share a category, show recent items under More articles/More projects, never label unrelated results as similar. No random ordering, content mutation or separate recommendation engine. Restore the main post after rendering the secondary query.

Reuse blog/preview and cards/project. Project card base styling lives in ui/project-card.css and is consumed by archive and single styles; archive-only alternating/featured grid rules stay in the archive owner. Reuse existing local attachment/video preview behavior, intrinsic images, shared icons and RTL direction rules. No duplicated cards, per-type navigation markup or global unused bundles.


## Floating contact

The theme owns the floating contact module in inc/ui/contact.php, template-parts/ui/contact.php, src/css/ui/contact.css and src/js/ui/contact.js. Customer channels come from the radmanct/contact/data filter in inc/client.php, with label, url, value and a shared icon catalog name. Keep contact data in that callback and palette in tokens.css.

Render once outside the smooth-scroll wrapper. CSS/JS travel in the existing site entries. Initialize the static root once, release listeners/animations through the site lifecycle, preserve Escape/outside-click/focus handling, inert/hidden closed content and reduced motion. Keep local icons at the shared stroke width. Preserve the Contact Us Local GPL attribution in adapted code.

This custom implementation has no option storage or admin settings. Future channels reuse the data array and existing renderer. Do not reintroduce the retired plugin's shortcode/assets/settings dependency or a whole-page MutationObserver for this static root.

Header initialization: keep #rdc-scr-cnt position:relative in the initial shared CSS. The absolute header must use the content as its containing block before ScrollSmoother adds transforms. This preserves the WordPress admin-bar offset across startup/reload. Do not hide the header or defer an offset correction to JS to mask a first-paint jump.

The floating contact uses --rdc-ctc-off (24px desktop, 16px mobile) plus safe-area insets and stays fixed at that position through the footer. Reserve only the horizontal space needed in the footer bottom row to place return-to-top beside it with a 1rem gap. Derive the reservation from contact offset/size and the footer gutter; do not lift the contact control on scroll or attach a footer visibility observer. Panel size respects viewport insets.


Approved user imagery must not be regenerated or recolored for layout changes. Services selects media_inset as a legacy per-use marker for its approved complete 1920×1080 artwork: keep the hero exactly one viewport high (100svh with 100vh fallback). The approved compact derivative keeps a 1920×1080 canvas, resamples the original uniformly to 1440×810 and places it at (0,50) on a uniform #0e0e0d background. The approved background correction affects only near-black pixels (smooth transition below RGB maximum 36); brighter artwork pixels retain their colors. Never paste a rectangular image onto a mismatched flat background. No global color correction. Keep the user-provided original source outside the media library; unused experimental attachments may be removed after checking all live references. Change artwork size inside the asset, not through image CSS transforms or a taller hero. Restore the existing shared smoother speed .4 on the inner media wrapper for scroll parallax; keep the image and decorative nodes in the same moving frame. No inset thumbnail frame, image zoom, cropping, masks, fades, color filters or darkening overlay on this artwork. Other cover media retain their existing motion. Do not edit source pixels to conceal layout boundaries.

Services orbital nodes reuse runtime/art-orbit.js, extracted unchanged from the home entry (which retains its exports). Client art_tracks supplies at most four paths on existing image grid lines, durations and phases in the current 1920×1080 canvas coordinates; rescale route coordinates with any asset geometry change. template-parts/ui/title-art.php caps output at four; paths stay invisible for baked-in image lines, while art_draw makes the same motion paths visible for selected artwork. Image, SVG and dots share the same 16:9 coordinate frame; node sizes match the visible home nodes. The orbit module loads dynamically only when the art frame is present and retains offscreen/hidden-tab/reduced-motion cleanup. Home artwork, paths, durations and timing remain unchanged.

Services gallery: sections/services/project-marquee.php replaces the retired image strip. Client copy, contact URL and local attachment IDs come from radmanct/services/marquee in inc/client.php. UI CSS/JS live in ui/project-marquee and are imported only by the services entries. Adapted from the independent Radman Design Library project-marquee sample; only its nine reference media were imported (attachment source notes retained), without its shell, registry, data options or scroll runtime. Preserve reference speed (70 seconds per 15-card cycle), bottom-aligned intrinsic image heights, scroll-velocity response and lower blur. Use the existing local GSAP/ScrollTrigger and one scroll owner; pause offscreen, on hidden tabs, keyboard focus. No visible pause/play button in this client design. Reduced motion gets a native horizontally scrollable gallery; matchMedia cleanup removes clones and listeners.

The project-marquee visual reference is https://demo.artureanec.com/themes/azurio/services/ (the “Let’s talk about your project” section); do not confuse it with the retired Orisa services-4 image strip.

Marquee velocity contract: Match the inspected reference: duration = 70 × card count / 15; on each scroll update set timeScale to clamp(abs(velocity / 200),1,6), easing .5s power1.out with overwrite:auto. No peak holding, delayedCall, extended deceleration or invented momentum. The existing ScrollTrigger scrollEnd event returns to timeScale 1 through the same .5s easing. No visible play/pause button in either project-marquee preview or Webtalik; retain focus, hidden-tab, offscreen and reduced-motion guards. Preserve independent project data and local scroll ownership.

Media ownership: offering-web (13) and offering-apps (20) are the original animated home artwork bases, paired with their existing orbital assets and geometry. Service showcase replacements use four separate services-web / services-wordpress / services-systems / services-apps keys with 1600×900 WebP attachments. Never update a home artwork mapping to replace a service-page image; preserve the home image/orbit combination.

Project separation: Radman-Design-Library is the permanent reusable design reference. Corporate-Webtalik is an independent customer implementation with its own editorial data, media mappings, templates and runtime. Transfer only explicitly selected modules and dependency contracts, remap local attachments and review all consumers. No automatic two-way sync, shared mutable customer data, or source-wide overwrite; update each project’s rules when intentionally changing a reusable motion contract.

Media cleanup must inspect template consumers, active media mappings, post content, featured images, gallery/video fields and site/theme options; unattached status alone is not proof of non-use. Preserve animated home bases/orbs and current service art. Remove retired mapping entries with confirmed-unused attachments through native WordPress deletion; source files supplied by the user outside uploads stay untouched.

Hero raster backgrounds use the page RGB(14,14,13), including enclosed near-black background islands between grid lines. Normalize only neutral near-black pixels with a smooth transition; preserve gold/chromatic contours and brighter subject pixels. Keep correction inside the asset, never CSS blend/mask/filter. Serve these hero plates and native responsive derivatives as lossless WebP, preserve intrinsic dimensions and keep the original source. Trace moving node paths against the final consumed plate; changing artwork geometry requires updating its coordinates.

Custom website service composes sections/service/mosaic.php before the shared FAQ. inc/pages/service.php owns its data contract and attachment privacy checks; client copy/media belong to radmanct/service/mosaic in inc/client.php. service-mosaic.css is imported only by the service page entry. The alternating text/media layout is CSS only; local video uses native controls, preload none and no autoplay/loop. The selected gold rim is a masked decorative span with a transform-only 12s CSS light sweep. pages/service.js uses the existing page lifecycle and a native IntersectionObserver to pause individual rims offscreen, on hidden tabs and for reduced motion; load it only on the consuming custom website service.

Ordinary card/panel corners consume --rdc-rad from tokens.css, including service mosaic, FAQ, search, home offering cards and contact panel. Preserve circular/pill controls and intentional editorial cover geometry. Shared light/inverse roles remain --rdc-ink (headings), --rdc-tx (body), --rdc-mut (secondary text), --rdc-ln (lines), --rdc-srf (raised panels). The mosaic rim inherits its box radius and uses --rdc-ln for the stationary line; only the moving light remains gold. Never duplicate those palette/radius values in page CSS.
