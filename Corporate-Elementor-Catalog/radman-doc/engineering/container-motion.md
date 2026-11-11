# موشن کانتینر مشترک و adapterهای Elementor

## بازخورد تصویر تازهٔ Hub — ۲۰۲۶-۱۰-۲۵

مالک دوباره بر Animate محتوا مستقل از پارالاکس، Targets، محتوای داخلی، Play، Settings و Animate from/to تأکید کرد؛ تصویر تازه صرفاً شاهد مرجع است و تأیید نصب رادمان نیست. ترتیب هر دو adapter اکنون Parallax و popoverهای آن، سپس جداکننده و Animate محتواست. عنوان عمومی Initial/Final state به Animate from/to تغییر کرد و Play از حالت موفق بومی استفاده می‌کند. شناسه‌ها و مقدار ذخیره‌شده عوض نشدند؛ From/To محتوا در preset سفارشی و تنظیمات پارالاکس فقط با کلید خودش دیده می‌شوند. Animate به خاموش/روشن‌بودن پارالاکس وابسته نیست.

قرارداد `container-motion-contract.php --controls-only` با PropTypes/Section/Control واقعی Elementor ارسالی و WordPress stub، همهٔ فیلدهای Classic، schema اتمیک و شرط استقلال Animate/Targets/Inner/Play/From/To را بررسی می‌کند؛ این مسیر عمداً پیش از نیاز به HTML parser وردپرس خارج می‌شود. ۲۵ تست کوتاه موشن، ترتیب پنل Atomic و Play/Reset/target/teardown پاس شدند. نصب مالک ممکن است هنوز بستهٔ قدیمی داشته باشد؛ دلیل دقیق پنل گزارش‌شده و پوشش بصری/Undo بدون بررسی همان نصب تأیید نمی‌شوند. هیچ ادعای «برابری کامل بصری با Hub» از این تست‌ها حاصل نمی‌شود.

## تطبیق پیش‌فرض‌ها و مالکیت بومی — ادامهٔ سورس، ۲۰۲۶-۱۰-۲۵

مقایسهٔ مجدد `params/animations.php` و `params/parallax.php` نشان داد صرفِ داشتن فیلدها کافی نبود؛ چند پیش‌فرض و محدوده با Hub فرق داشتند. preset پیش‌فرض اکنون `custom` است؛ حالت آغاز/پایان قابل دسترس و خنثی‌اند: انتقال/چرخش صفر و scale/opacity یک. پارالاکس نیز بدون تعیین محور، جابه‌جایی اجباری ‎−۴۰/+۴۰ ندارد؛ easing پیش‌فرض آن `linear` است و alias قبلی `none` همچنان معتبر است. مدت صفر، Stagger تا ۱۰ ثانیه و Scale تا ۵ پذیرفته می‌شوند. مقادیر صریح ذخیره‌شدهٔ preset یا from/to تغییر نمی‌کنند، اما سندی که فقط به پیش‌فرض ضمنی قبلی متکی بوده اکنون پیش‌فرض جدید را می‌گیرد.

رنج نمایشی کنترل‌های native برای Translate پیکسلی ±۵۰۰ و Rotate ±۳۶۰ است؛ `ui_min/ui_max` از مرز اعتبارسنجی داده جداست تا مقدار صریح قدیمیِ معتبر تا ±۱۰۰۰ بی‌دلیل بریده نشود. محدودهٔ واحدهای نسبی قبلی و origin محفوظ‌اند. Classic و Atomic همین metadata را مصرف می‌کنند. TextControl اتمیک نسخهٔ 4.3.5 پراپ `maxLength` را مصرف نمی‌کند؛ پراپ بی‌اثر حذف شد، سقف/grammar در normalizer مشترک برقرار است و جهت متن فنی با `sx` خود کنترل native، LTR می‌شود.

تشخیص اولویت native اکنون envelopeهای String/Boolean را با نوع و disabled معتبر باز می‌کند. `none`، مقدار خالی، نوع نامعتبر و native غیرفعال مالکیت موشن را از رادمان نمی‌گیرند؛ entrance/Pro motion یا فهرست interactions فعال همچنان اولویت دارند. `items` خراب/غیرآرایه در interactions فعال فرض نمی‌شود. fixture مشترک `native-motion-values.json` شانزده حالت را در قراردادهای PHP/JS نگه می‌دارد؛ اجرای تازهٔ PHP هنوز انجام نشده است. در قرارداد PHP ثبت native همهٔ فیلدهای schema در Classic و حضور هم‌تعداد props اتمیک نیز کنترل می‌شود؛ این قرارداد جای مشاهدهٔ پنل واقعی نیست.

## اصلاح صریح مالک: Animate محتوا، Play و Reset — ادامهٔ سورس

مالک تجربهٔ قبلی را کامل و هم‌تراز Hub ندانست؛ وجود نام کنترل در سورس به‌تنهایی پذیرش رابط نیست. `params/animations.php`، `params/parallax.php` و نگاشت presetها در `hooks/section-controls.php` مرجع دوبارهٔ این ادامه‌اند. تغییرهای زیر در سورس پیاده شدند؛ نصب واقعی، نمایش پنل و ذخیره/Undo همچنان پذیرش جدا می‌خواهند.

| مورد | اصلاح |
|---|---|
| Animate contents | کلید Animate مستقل از Parallax حفظ شد؛ هدف همهٔ محتوا/کانتینر داخلی/خود عنصر و شمول محتوای داخلی برای layout بیرون از popover تنظیمات، مانند ترتیب Hub، دیده می‌شوند. ویجت مستقل همچنان خودش را هدف می‌گیرد. |
| Play | Animate، پارالاکسِ تنها و Reveal Play دارند؛ Play پارالاکس در preview از from تا to بدون وابستگی به موقعیت scroll پخش می‌شود؛ frontend همچنان ScrollTrigger دارد. |
| Reset preview | دکمهٔ بومی Classic/Atomic فقط GSAP همان عنصر را revert می‌کند؛ تنظیم/پیش‌فرض ذخیره‌شده تغییر نمی‌کند. نمونه تا Play یا تغییر تنظیم دوباره شروع نمی‌شود؛ pending import و callback منقضی نمونه را برنمی‌گردانند. |
| Reset مقدار | Atomic از `useBoundProp().resetValue()` همان نسخه استفاده می‌کند تا initial/default و history بومی رعایت شوند؛ fallback برای API فاقد آن باقی است. Reset مقدار با Reset preview متفاوت است. |
| preset | Fade در مرجع ±۱۵۰px است؛ Flip X با y=150 و rotationX=-30 و Flip Y با x=150 و rotationY=30، Scale با ۰٫۷۵/۱٫۲۵ تطبیق داده شد. نگاشت قبلی رادمان ۴۰px/۷۰deg/۰٫۸۵–۱٫۱۵ بود و مطابق مرجع نبود. شناسه‌ها تغییر نکردند ولی ظاهر presetهای ذخیره‌شده عمداً به مرجع نزدیک شده است؛ حالت Custom و تنظیم محورهای ذخیره‌شده بازنویسی نمی‌شوند. |
| Trigger سفارشی | start/end قبلی حفظ و گزینهٔ Custom با TextControl/TEXT بومی اضافه شد. کلیدهای `parallax_start_custom` و `parallax_end_custom` جایگاه‌هایی مانند `top bottom-=25%`، `50% 50%` یا `+=300` را با سقف ۱۲۰ کاراکتر و grammar یکسان PHP/JS می‌پذیرند؛ تابع/JS/HTML اجرا نمی‌شود. |
| افزایش/کاهش مسافت | `parallax_distance` در بازهٔ ±۵۰۰ با px/% مانند کنترل Duration مرجع، پایان preset را به `bottom+=… top` یا `bottom-=… top` تغییر می‌دهد؛ پایان Custom خودش مرجع است و این کنترل برای آن پنهان می‌شود. scrub زمانی مستقل باقی است. |
| رابط و درخواست | Select/Switch/Number/Text/Slider/Button/Popover و labelها بومی همان ویرایشگرند؛ slider و عدد با Box/Stack بومی عرض مشخص دارند. Classic با `render_type=none` از bridge زنده استفاده می‌کند و برای هر تغییر عدد رندر کامل درخواست نمی‌کند. CSS پنل موازی، jQuery یا موتور GSAP دوم اضافه نشده است. |

کنترل‌های بومی Interactions اولویت قبلی خود را حفظ می‌کنند؛ این ادامه ادعای سازگاری تمام افکت‌های هم‌زمان، تطبیق pixel به pixel یا تأیید هاست نیست. syntax جایگاه با [ScrollTrigger رسمی](https://gsap.com/docs/v3/Plugins/ScrollTrigger/) از Context7 و سورس GSAP 3.15.0 تطبیق داده شد. شواهد اجرای کوتاه در [foundation](../implementation/foundation.md) است.

## تطبیق دوباره با کد Hub — ادامهٔ سورس، ۲۰۲۶-۱۰-۲۴

مرجع مستقیم: `themes/hub-elementor-addons/elementor/hooks/section-controls.php` بخش Liquid Animations و `params/animations.php` / `params/parallax.php`. Hub پنل را از hook عمومی پایان سکشن، نه فقط نام ثابت چند عنصر، اضافه می‌کند. رادمان اکنون `elementor/element/after_section_end` را با همان شرط section_layout کانتینر یا section_advanced/_section_style مصرف می‌کند؛ guard موجود از ثبت دوباره جلوگیری و عنصر نامرتبط رد می‌شود. این اصلاح پوشش ثبت روی ویجت دارای سکشن Advanced متفاوت را کامل‌تر می‌کند؛ علت قطعی پنل دیده‌نشدهٔ هاست بدون بررسی آن نصب ادعا نمی‌شود.

| بخش مرجع | وضعیت فعلی رادمان |
|---|---|
| کلید مستقل Animate/Parallax، هدف‌ها، preset، ترتیب، Play و popover تنظیم/آغاز/پایان | مسیر موجود حفظ شد؛ Classic از کنترل بومی و Atomic از UI/SettingsField بومی خودش استفاده می‌کند. |
| easingهای Animate و Parallax | ۳۱ انتخاب Hub و alias قبلی none در `inc/components/motion-easings.json` مشترک‌اند؛ PHP و JS همان ۳۲ مقدار را می‌خوانند. تنظیم‌های قدیمی و پیش‌فرض‌ها حفظ شده‌اند؛ رشتهٔ آزاد اجرا نمی‌شود. |
| اسلایدر حالت آغاز/پایان | Translate/Rotate/Scale/Opacity در Classic با SLIDER بومی و در Atomic با Slider/Number بومی ارائه می‌شوند. کلید اسکالر قبلی و alias کنترل UI جدا باقی‌اند؛ خالی به مقدار قبلی برمی‌گردد و صفر معتبر است. تنظیم زمان‌ها نیز همین روال را دارد. |
| Transform origin | X/Y با px یا درصد و Z با px برای هر چهار حالت from/to و parallax_from/to مستقل‌اند. کلید بومی Custom transform origin پیش‌فرض خاموش است تا CSS طراحی موجود حفظ شود؛ در حالت روشن، GSAP از نقطهٔ اعتبارسنجی‌شده استفاده می‌کند. |
| واحد Translate X/Y | px، درصد، vw و vh در هر چهار حالت؛ دادهٔ عددی قبلی px می‌ماند. محور Z همچنان px است. preset آماده تنظیم‌های سفارشی from/to را اعمال نمی‌کند؛ پارالاکس مستقل می‌ماند. |
| Perspective پارالاکس | مقدار ۰ تا ۲۰۰۰ px، پیش‌فرض صفر؛ فقط مقدار مثبت به transformPerspective در حالت آغاز GSAP می‌رود. سبک/markup اضافی یا مخفی‌سازی اولیه ندارد و cleanup همان context موجود است. |
| پیش‌نمایش | فهرست sliderهای معتبر از PHP می‌آید؛ تغییر زندهٔ محورها و Play همان عنصر از مقدار جدید استفاده می‌کنند. بدون import عمومی GSAP برای عناصر غیرفعال. |
| تفاوت‌های هنوز باز | ظاهر زندهٔ popover، مقایسهٔ دیداری تمام presetها/زیرکنترل‌های مرجع، مسیر هاست، Undo و برابری بصری پذیرش نشده‌اند؛ اضافه‌شدن محور و واحد، تأیید کامل پنل نیست. |

نوع کنترل، bounds فعلی و شناسه‌های `rm_motion_` حفظ شده‌اند؛ موتور دوم یا وابستگی jQuery/چارچوب Hub منتقل نشده است. easingها با GSAP قفل‌شده و [parseEase](https://gsap.com/docs/v3/GSAP/gsap.parseEase()/) بررسی شدند؛ Perspective از قابلیت [CSSPlugin](https://gsap.com/docs/v3/GSAP/CorePlugins/CSS/) استفاده می‌کند. قواعد منبع محلی و استقلال PHP/Classic/Atomic پابرجاست. افزایش نسخه و ZIP در این ادامه انجام نمی‌شود.

**تکمیل واحدها و مبدأ در ادامهٔ سورس:** مرجع origin در `params/animations.php` حدود px برابر ۵۰۰± و درصد ۱۰۰± و Z پیکسلی دارد؛ همان محدودیت‌ها اعمال شدند. Translate X/Y از واحدهای مرجع استفاده می‌کند؛ دامنهٔ px قبلی رادمان برای حفظ دادهٔ موجود باقی است و واحدهای نسبی ۱۰۰± هستند. ورودی رشته‌ای آزاد برای CSS وجود ندارد. مبدأ فیزیکی X/Y در RTL آینه نمی‌شود؛ طراح نسخهٔ همان زبان آن را تنظیم می‌کند.

Classic از SLIDER/انتخاب واحد خود Elementor و کلید scalar پشتیبان استفاده می‌کند؛ خالی‌شدن slider هم مقدار و هم واحد ذخیره‌شده را حفظ می‌کند. Atomic از Switch/Slider/Number/Select و SettingsField بومی، با شرط نمایش فیلدها و bounds متناسب واحد استفاده می‌کند. schema، preview و helper PHP همان نام‌ها را می‌خوانند. `transformOrigin` فقط با اختیار صریح ساخته می‌شود و preset آماده آن را از حالت‌های سفارشی ورود دریافت نمی‌کند؛ context فعلی GSAP cleanup را نگه می‌دارد. پارالاکس `invalidateOnRefresh` دارد تا اندازه‌های وابسته به viewport در refresh بومی دوباره محاسبه شوند؛ listener جداگانهٔ resize اضافه نشد. رفتار واحدها با [CSSPlugin](https://gsap.com/docs/v3/GSAP/CorePlugins/CSS/) و [ScrollTrigger](https://gsap.com/docs/v3/Plugins/ScrollTrigger/) تطبیق داده شد.

نمونهٔ PHP: `rdct_motion_attributes(['animate'=>true,'preset'=>'custom','from_x'=>20,'from_x_unit'=>'vw','from_origin'=>true,'from_originX'=>0,'from_originY'=>0,'from_originZ'=>-100])`. خاموشی origin، styles موجود را تغییر نمی‌دهد و خاموشی تمام موشن همچنان marker/موتور اضافه نمی‌کند. ۲۰ بررسی کوتاه مدل/اتصال، قرارداد واقعی PHP/Props و build پاس شدند؛ بررسی زندهٔ مرورگر انجام نشده است.

نیاز و تصاویر مالک در [سناریوی موشن](../scenarios/motion.md) مرجع‌اند. درخواست ۲۰۲۶-۱۰-۲۰ شامل مصرف مستقل PHP، کنترل کلاسیک مشابه Hub و پیاده‌سازی **مستقل Atomic** است؛ Hub برای Atomic شاهد پذیرش محسوب نمی‌شود.

## مرجع تازه — آشکارشدن افقی سکشن

بررسی read-only HTML عمومی WorldQuant Foundry در ۲۰۲۶-۱۰-۲۱: سکشن حاوی `id="ethos"` یک `gsap.timeline` با ScrollTrigger دارد؛ `start: 'top bottom-=10%'`، `end: 'top center'` و `scrub: 1`. انیمیشن `from` مقدار `clipPath: 'inset(0 100% 0 0)'` و duration=1.2 دارد؛ در reduced motion زود خارج می‌شود. فایل GSAP نیز صریحاً در صفحه بارگذاری می‌شود. بنابراین شاهد سورس برای GSAP و reveal مبتنی بر برش وجود دارد؛ این مشاهده، اندازه‌گیری سرعت یا تأیید بصری زنده نیست. سورس/دارایی سایت به پروژه منتقل نشده است.

**IMPLEMENTED — ۲۰۲۶-۱۰-۲۱:** حالت مستقل `reveal` به موتور فعلی و فهرست مشترک فیلدهای PHP/Classic/Atomic اضافه شد؛ موتور دوم یا ویجت محتوایی اجباری ندارد. `reveal_direction` شامل start/end/left/right است؛ start/end از computed direction خود کانتینر حل می‌شوند. `reveal_start` و `reveal_end` درصد ارتفاع viewport هستند، نه درصد پیشرفت سند؛ پیش‌فرض 90/50، فاصلهٔ حداقل ۵ واحد و smoothing پیش‌فرض ۱ با حدود 0.1 تا 5 دارند. افکت روی clip-path و انیمیشن محتوا/پارالاکس روی transformهای خود کار می‌کنند؛ timeline reveal easing خطی دارد و نرمی از scrub می‌آید. برگشت اسکرول معکوس است و sticky/pin اضافه نمی‌شود.

همان marker و observer قبلی فقط برای کانتینر فعال موتور را lazy-load می‌کنند. reduced motion و غیرفعال‌سازی با چرخهٔ عمر مشترک revert می‌شوند؛ focusin افکت را متوقف و clip-path اصلی را برمی‌گرداند تا لینک/کنترل زیر برش نماند. قبل از اجرای JS هیچ مخفی‌سازی CSS نداریم؛ transform افقی/عرض اضافی ایجاد نمی‌شود. در editor افکت reveal خودکار اجرا نمی‌شود؛ Play پنل همان عنصر آن را در 1.2 ثانیه برای بررسی پخش می‌کند. کنترل‌ها در Advanced > Scroll reveal هستند؛ در Atomic همان گروه بومی به layoutهای پشتیبانی‌شده افزوده می‌شود و تنظیمات typed و renderer مشترک را مصرف می‌کند.

نمونهٔ PHP بدون Elementor:

```php
<section <?php rdct_motion_attributes( array(
    'reveal' => true,
    'reveal_direction' => 'start',
    'reveal_start' => 90,
    'reveal_end' => 50,
    'reveal_scrub' => 1,
) ); ?>>
    <!-- Any server-rendered content. -->
</section>
```

بررسی کوتاه: ۱۱ تست Node تنظیمات/جهت/فوکوس/Play/cleanup/manager و قرارداد PHP با schema و controls واقعی Elementor ارسالی 4.3.5 پاس شد؛ PHP 8.4 lint و build نیز پاس شدند. اجرای زندهٔ editor، موبایل، حالت ترکیبی با ScrollSmoother، clip-path سفارشی از قبل موجود و برابری بصری سایت مرجع هنوز تأیید نشده‌اند. نیازی به افزودن وابستگی خارجی یا CSS عمومی تازه نبود.

مبنای API: [ScrollTrigger scrub](https://gsap.com/docs/v3/Plugins/ScrollTrigger/) پیشرفت انیمیشن را به اسکرول وصل می‌کند؛ مقدار عددی زمان رسیدن نرم به موقعیت اسکرول است. [gsap.matchMedia](https://gsap.com/docs/v3/GSAP/gsap.matchMedia/) برای شرایط واکنش‌گرا و revert چرخهٔ عمر استفاده شود؛ مدت 1.2 در timeline دارای scrub به معنی زمان ثابت انتظار صفحه نیست.

## معماری و وضعیت کد

### اسکرول افقی ثابت — ۲۰۲۶-۱۰-۲۱

مرجع مستقیم خوانده‌شده: HTML صفحهٔ Creative Wellness و `https://d3f86pfw66amx.cloudfront.net/uncode/wp-content/themes/uncode/library/js/wrapperSticky.min.js?ver=2.12.2`. این فایل track محتوا می‌سازد، فاصله را از scrollWidth منهای عرض ظرف می‌گیرد و `gsap.to` با `pin`، `scrub` و end برابر فاصله دارد؛ markup صفحه `data-disable-sticky-tablet="yes"` دارد. کتابخانه‌های GSAP/ScrollTrigger صریحاً در صفحه‌اند. درخواست مستقیم بدون query ابتدا 403 داد؛ HTML عمومی با query خوانا و فایل اختصاصی بالا دریافت شدند. فایل/دارایی/چارچوب Uncode به محصول منتقل نشده است. [مستند رسمی سازنده](https://support.undsgn.com/hc/en-us/articles/35532190458781-Horizontal-Scrolling) نیز همین الگوی Sticky Scroll و پنل با محتوای آزاد را توضیح می‌دهد.

موتور مستقل `src/js/components/horizontal-scroll.js` و CSS همان قابلیت در `src/css/components/horizontal-scroll.css` از طریق import شرطی به موتور container-motion وصل شدند؛ dependency GSAP مشترک است. `horizontal=true` یک mode کانتینر است، نه widget محتوایی؛ هنگام فعال‌بودن بر reveal/animate/parallax همان ریشه اولویت دارد تا pin با transform یا clip خود ریشه رقابت نکند. داخل هر بخش همچنان محتوا دلخواه است. جهت auto از computed direction ریشه می‌آید؛ track با حرکت مثبت در RTL و منفی در LTR جلو می‌رود و direction متن هر panel مستقل حفظ می‌شود.

schema مشترک، کنترل‌های Advanced > Pinned horizontal scroll در Classic/Atomic و helper PHP دارند: width=90%، gap=24px، alignment=start، smoothing=1، min-width=1024px و offset=0. فاصلهٔ toolbar ثابت و هدر چسبان رادمان به offset افزوده می‌شود. طول اسکرول از عرض اضافهٔ واقعی track محاسبه و با تغییر اندازه/محتوا/بارگذاری تصویر دوباره ساخته می‌شود؛ listener ماوس/wheel مانع اسکرول عادی نداریم. با پایان pin spacing صفحه به جریان طبیعی برمی‌گردد. اسکرول نرم رادمان در صفحهٔ دارای marker فعال horizontal کنار گذاشته می‌شود تا wrapper متحرک با pin تداخل نکند؛ تنظیم ذخیره‌شده عوض نمی‌شود.

در runtime wrapperهای viewport/track/panel و placeholder موقت ساخته می‌شوند؛ داده و HTML ذخیره‌شدهٔ Elementor عوض نمی‌شوند. cleanup ترتیب اولیه را بازمی‌گرداند و عنصر منتقل‌شده/حذف‌شدهٔ بیرونی را دوباره زنده نمی‌کند. marker مالک موقت مانع rebuild بی‌پایان observer عمومی بر اثر DOM خود موتور است؛ observer اختصاصی خود mode تغییر محتوای track را مدیریت می‌کند. reduced motion، عرض زیر حد، نبود overflow، کمتر از دو بخش یا ارتفاع بزرگ‌تر از viewport قابل استفاده، خروجی عمودی عادی دارند. focus چیدمان عمودی را برای ادامهٔ همان instance فعال می‌کند و هدف را قابل دیدن نگه می‌دارد. pin تو‌در‌تو عمداً کنار گذاشته می‌شود.

ویرایشگر همیشه عمودی است؛ Play هم pin را داخل editor فعال نمی‌کند. برای دیدن افکت، صفحهٔ preview عادی باز شود؛ این تصمیم با رفتار مستند سازنده هم‌راستاست. اجرای واقعی روی هاست، ancestor با transform/overflow سفارشی، ابزارهای ثالث و ظاهر دستگاه‌ها هنوز پذیرش نشده‌اند؛ بررسی کوتاه schema/هندسه/cleanup جای اثبات بصری runtime را نمی‌گیرد.

```php
<section <?php rdct_motion_attributes( array(
    'horizontal' => true,
    'horizontal_direction' => 'auto',
    'horizontal_width' => 90,
    'horizontal_gap' => 24,
    'horizontal_min_width' => 1024,
) ); ?>>
    <article>First panel: any PHP content.</article>
    <article>Second panel: any PHP content.</article>
    <article>Third panel: any PHP content.</article>
</section>
```

### تطابق خروجی Atomic و پیش‌نمایش — ۲۰۲۶-۱۰-۲۰

adapter مقدار envelope را فقط با نوع منطبق با schema و `disabled !== true` مصرف می‌کند؛ مقدار غیرفعال یا نوع نادرست به default مشترک برمی‌گردد. این تصمیم برای PHP و bridge یکی است. در نسخهٔ قبل، preview مقدار غیرفعال را کنار می‌گذاشت ولی PHP می‌توانست آن را اجرا کند. metadata نوع boolean/string/number از فهرست فیلدهای PHP به config ویرایشگر می‌رود؛ `elementor-motion-values.js` فقط بازکردن envelope را انجام می‌دهد و validator عمومی/موتور GSAP مستقل می‌مانند. تغییر مقدار native Interactions ایجاد نمی‌شود.

قالب `{ $$type, value, disabled }` با [مستند رسمی Atomic Prop Values](https://github.com/elementor/elementor-developers-docs/blob/master/src/data-structure/atomic-prop-values.md) از Context7 بررسی شد؛ رفتار دقیق کنارگذاشتن `disabled === true` از `themes/elementor/modules/atomic-widgets/props-resolver/render-props-resolver.php` نسخهٔ ارسالی 4.3.5 تطبیق داده شد. پشتیبانی نسخه‌های دیگر از این مشاهده نتیجه نمی‌شود.

تبدیل عددی JS اکنون فقط number یا رشتهٔ عدد دهدهی/علمی متناهی می‌پذیرد؛ array/object، whitespace و رشتهٔ hex به مقدار پیش‌فرض برمی‌گردند. `inner: null` مانند PHP از default ارث می‌برد. fixture مشترک `tests/fixtures/motion-values.json` در Node و PHP اجرا می‌شود تا مسیر PHP اختصاصی، کلاسیک و Atomic اختلاف پیش‌فرض/تبدیل نداشته باشند. این تطابق validation است، نه تأیید بصری ویرایشگر واقعی.

`inc/components/container-motion.php` مالک فهرست فیلدها، defaults، allowlist و bounds مشترک PHP است. helper عمومی `rdct_motion_attributes()` فقط attribute امن برای container کدنویسی‌شده چاپ می‌کند و بدون Elementor کار می‌کند. renderer محتوا یا wrap عمومی تازه ندارد. نمونهٔ PHP:

```php
<section <?php rdct_motion_attributes( array(
    'animate' => true,
    'preset' => 'fade-up',
    'duration' => 1.6,
    'stagger' => 0.16,
    'parallax' => false,
) ); ?>>
    <div data-rdct-motion-item>First item</div>
    <div data-rdct-motion-item>Second item</div>
</section>
```

`inc/integrations/elementor/container-motion.php` کنترل Classic را روی container/section/column و ویجت‌های مشترک در Advanced ثبت می‌کند؛ guard از ثبت دوباره جلوگیری می‌کند. Atomic با schema بومی boolean/string/number و `Motion_Control` از نوع element-control کار می‌کند. schema hook نسخهٔ 4.3.5 آرگومان نوع عنصر ندارد. شناسه‌های ذخیره‌شدهٔ `rm_motion_` حفظ شده‌اند؛ Interactions و Motion Effects بومی تغییر نمی‌کنند.

Theme 0.1.22: دو گروه «حرکت و انیمیشن» و «اسکرول پیشرفته» جای هشت بخش را گرفته‌اند؛ دومی فقط برای layout است. Classic از POPOVER_TOGGLE و popover بومی استفاده می‌کند؛ زمان‌ها SLIDER بومی با کلید UI جدا دارند تا اعداد اسکالر قبلی از بین نروند؛ مقدار خالی به زمان قبلی برمی‌گردد. Atomic با `SettingsField`/`useElement` خود editor-editing-panel و Switch/Select/Number و Popover/Slider بومی UI ترکیب می‌شود؛ نوشتن prop، undo و validation از مسیر خود Elementor است. این ترکیب لازم است چون Section PHP اتمیک API popover ندارد. React و پنل فقط در editor لود می‌شوند.

خروجی هر عنصر فعال یا دارای موشن بومی میان before/after_render همان عنصر capture و با HTML Tag Processor وردپرس روی اولین تگ annotate می‌شود؛ markup تازهٔ wrapper ساخته نمی‌شود. بافر غیرفعال باز نمی‌شود؛ HTML بیش از ۲ MiB دست‌نخورده می‌ماند. عمق بافر کنترل می‌شود تا بافر افزونهٔ دیگر ناخواسته بسته نشود. این مسیر و اثر cache باید با اجرای واقعی Elementor نیز بررسی شوند.

موتور `src/js/components/container-motion.js` از GSAP/ScrollTrigger همان build استفاده می‌کند؛ renderer مستقل است. `container-motion-options.js` فقط دادهٔ مجاز عددی/enum را می‌پذیرد. Animate contents روی فرزندان و Parallax روی root اعمال می‌شوند؛ ویجت تکی اجباراً `targets=self` دارد و Reveal/Horizontal آن خاموش‌اند. هدف دارای مالک مستقل یا موشن بومی، هدف دارای فرزند مستقل، و هدف زیر هدف انتخاب‌شدهٔ دیگر کنار گذاشته می‌شوند تا transform دوباره نوشته نشود. روی root بومی، `data-rdct-native-motion` مانع موشن رادمان است؛ entrance کلاسیک، Pro scrolling/mouse و Atomic interactions فعال تشخیص داده می‌شوند. ورود self همراه parallax اجرا نمی‌شود؛ پارالاکس اولویت دارد و داده‌ها حفظ می‌شوند. هدف‌ها حداکثر ۲۰۰ عنصرند. wrapper داخلی boxed شناخته می‌شود؛ ترتیب start/end از DOM و جهت‌های فیزیکی left/right از X می‌آیند.

`container-motion-manager.js` تنها یک observer فعال برای هر document دارد؛ GSAP فقط با marker فعال و no-preference import می‌شود. تغییر attribute، حذف DOM، تغییر reduced motion و پایان صفحه cleanup دارند. از مشاهدهٔ style صرف‌نظر می‌شود تا تغییرات GSAP حلقهٔ observer نسازند. صفحهٔ عمومی بدون marker این manager را دریافت نمی‌کند؛ shortcode/AJAX آینده‌ای که بعد از بارگذاری اولیه برای اولین بار موشن اضافه کند هنوز integration جدا می‌خواهد.

تغییر فرزندان یا مالکیت marker، مالک‌های موشن بالادست را برای بازسازی هدف‌ها علامت می‌زند؛ اضافه/حذف محتوا نیز هدف‌های قدیمی را آزاد می‌کند. cleanup مدیر و GSAP یک‌باره است؛ import دیررس پس از حذف عنصر یا پایان مشاهده اجازهٔ شروع انیمیشن ندارد. bridge هنگام تغییر data-id و نبود مدل متناظر، اتصال مدل و marker قبلی را آزاد می‌کند؛ مدل جدید مستقل وصل می‌شود.

## پیش‌نمایش Elementor

bridge مستقل `elementor-motion-preview.js` فقط در hook preview بارگذاری می‌شود؛ تغییر settings و interactions مدل هر root کلاسیک/Atomic دنبال می‌شود و typed value به attribute پیش‌نمایش می‌رود. تعویض/حذف root اشتراک قبلی را آزاد می‌کند. با اصلاح صریح مالک در Theme 0.1.42، دکمهٔ شناور Play عمومی و CSS آن حذف شدند؛ پخش فقط از پنل عنصر انتخاب‌شده است. بخش «انیمیشن و پارالاکس» شامل کلیدهای روشن/خاموش، Play همان عنصر و ردیف مدادی تنظیمات/حالت آغاز/حالت پایان است. هدف محتوا/عنصر و شمول کانتینر داخلی داخل تنظیمات قرار دارند؛ حالت آغاز/پایان فقط برای preset سفارشی باز می‌شوند. Atomic از IconButton/Popover/SettingsField/Number/Select بومی و Reset هر مقدار از useBoundProp همان فیلد استفاده می‌کند تا تغییر و Undo به Elementor سپرده شود؛ بخش اضافهٔ بی‌رفتار یا پنل مستقل نداریم. کنترل بومی Interactions دستکاری نمی‌شود. پذیرش بصری پنل، RTL/LTR و چرخهٔ واقعی ذخیره/Undo روی هاست هنوز انجام نشده است.

در Theme 0.1.37، bridge شناسهٔ Classic را از `ControlBaseView.container.id` و Atomic را از `useElement().element.id` می‌گیرد؛ قرارداد event دکمه با مستند رسمی و سورس ارسالی Elementor 4.3.5 تطبیق داده شد. شناسهٔ نامعتبر، root حذف‌شده، مالک native یا نبود مدل، به Play سراسری تبدیل نمی‌شود. فیلدهای ذخیره‌شده تغییر نکرده‌اند. Atomic برای Scroll Reveal نیز Button بومی انتخابی دارد.

مدیر مشترک `rdct:motion:play` بدون detail را برای پخش عمومی و `{detail:{root}}` را برای یک root زنده در همان scope می‌پذیرد. درخواست‌های هم‌زمان تجمیع می‌شوند؛ انتخاب والد، مالک مستقل فرزند/خواهر را دوباره راه نمی‌اندازد. درخواست هنگام import معوق، entry منتخب قبلی را منقضی می‌کند؛ پس از cleanup هیچ اجرای تازه‌ای شروع نمی‌شود. reduced motion، اولویت native و استثنای preview اسکرول افقی ثابت حفظ شدند. این‌ها قرارداد مدل/سورس هستند؛ اجرای واقعی editor همچنان پذیرش جدا می‌خواهد.

حالت بدون JS هیچ opacity پنهان اولیه‌ای ندارد. GSAP context در reduced motion و cleanup revert می‌شود. تغییر native Interactions یا transformهای مستقل دیگر روی همان target می‌تواند مالکیت متعارض ایجاد کند؛ هم‌زمانی واقعی آن‌ها هنوز پذیرش نشده است. هدف این ماژول جایگزینی global smooth scrolling یا motion effects خود Elementor نیست.

## مراجع و پذیرش

فقط `themes/hub-elementor-addons/elementor/params/animations.php` و `parallax.php` برای toggleها، targets، تنظیم زمان و from/to خوانده شدند؛ موتور jQuery، چارچوب controls، سورس/دارایی کامل و سرویس خارجی منتقل نشدند. API Atomic از Elementor 4.3.5 در `elements/base/has-atomic-base.php`، `prop-types/primitives/` و `controls/` بررسی شد؛ activation و حالت ترکیبی در [قرارداد Elementor](elementor.md) ثبت‌اند. GSAP matchMedia/revert/fromTo با Context7 و مستند رسمی تطبیق داده شد.

قرارداد PHP با PropTypes/Controls واقعی نسخهٔ ارسالی و HTML processor وردپرس، ۱۴ بررسی کوتاه Node برای تنظیمات، هدف‌ها، اولویت native، GSAP، bridge، تغییر مالک فرزند، import دیررس و cleanup، و ۱۱ قرارداد assets پاس شدند. build محلی و ZIP نسخهٔ 0.1.22 بررسی شدند؛ [وضعیت پیاده‌سازی](../implementation/foundation.md). اجرای واقعی Atomic editor، ذخیره/بازگشایی، timelineهای native، CSS/cache، لمس و کیفیت بصری/برابری با Hub تأییدشده نیستند. تست مرورگر/دیتابیس طولانی خودکار اجرا نشود. پوشش کلاسیک هرگز به‌عنوان اثبات Atomic گزارش نشود.
