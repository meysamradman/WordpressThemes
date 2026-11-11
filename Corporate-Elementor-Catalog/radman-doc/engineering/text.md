# Text مشترک

## مرز ماژول‌ها — بررسی ۲۰۲۶-۱۰-۲۷

اصلاح ۲۰۲۶-۱۰-۳۰: resolver مشترک Presentation قبلاً فرم Slider بومی Classic در desktop و override بومی mobile/tablet را برای propertyهای دارای units نمی‌خواند. اکنون اندازهٔ بومی، ورودی عددی قدیمی با واحد پیش‌فرض و Size تایپ‌دار Atomic همگی به CSS محدود یکسان می‌رسند. ترکیب responsive Atomic برای این propertyها SizeControl خود Elementor دارد؛ schema موجود به‌صورت افزایشی size را کنار number/string می‌پذیرد و CSS آزاد رد می‌شود. تست native desktop/mobile، Size تایپ‌دار و واحد تزریقی و قرارداد UI کنترل پاس شدند. این کار موتور Text یا DOM جدیدی اضافه نکرده است.

مرجع Hub در `optimization/widget-assets/assets/fancy-heading.php` پایه، گرادیان، outline، highlight و rotator را تفکیک می‌کند. رادمان هم CSS پایهٔ کوچک و CSS انتخابی highlight/fill/rotate/list/segment دارد؛ dispatcher متن، rotator، Reveal کلمه‌ای، SplitText خطوط، Marquee و تغییر رنگ هدر جدا هستند. static/hover فقط CSS دارند و SplitText/ScrollTrigger برای rotator لازم نیستند. فایل metadata یا PHP سمت سرور با یک bundle عمومی مرورگر یکی نیست و جابه‌جایی ظاهری آن‌ها به پوشهٔ بیشتر به‌تنهایی صفحه را سبک نمی‌کند.

یک وابستگی اضافی اصلاح شد: `text-reveal-entry.js` اکنون فقط موتور کلمه یا خط انتخاب‌شده را dynamic import می‌کند. `reveal-settings.js` صرفاً تنظیمات کوچک مشترک tween را دارد؛ موتور خطوط دیگر برای گرفتن آن، موتور کامل کلمه‌ای را وارد نمی‌کند. graph خروجی بررسی می‌کند موتورهای خط/کلمه یکدیگر را static نیاورند. CSS پایهٔ text-effects در build فعلی ۱۶۳۱ بایت و gzip محاسبه‌شدهٔ محلی آن ۴۳۹ بایت است؛ ریزکردن بیشتر همین قواعد کوچک به requestهای تازه لازم نیست. این اعداد اندازهٔ artifact هستند، نه اندازه‌گیری سرعت یا Network میزبان.

## تکمیل واحد هایلایت و حرکت چرخشی — ۲۰۲۶-۱۰-۲۷

پنج کنترل هندسهٔ هایلایت اکنون واحد مستقل px/%/em/rem/vw/vh و ارث‌بری سه دستگاه دارند. storage عددی قبلی و نام کنترل‌ها حفظ‌اند؛ واحدهای جدید از فیلدهای `highlight_*_unit_responsive` می‌آیند. تغییر صرف واحد، مقدار دستگاه بزرگ‌تر را حفظ می‌کند؛ صفر، اعشار و offset منفی معتبر و واحد/CSS نامعتبر رد می‌شوند. Classic از Slider بدون واحد تحمیلی و Select بومی و Atomic از همان ترکیب responsive با Select بومی فشرده استفاده می‌کند؛ موتور Presentation و CSS شکل انتخابی مشترک‌اند. Wave همچنان text-decoration است و کنترل هندسهٔ نامرتبط نمی‌گیرد. این بخش شکاف واحد هندسه را می‌بندد؛ تطابق عین SVGهای Hub ادعا نمی‌شود.

طبق اصلاح مالک، rotator از GSAP مشترک با حرکت slide شامل yPercent ±65 و rotationX ±95 و ease برابر power4.inOut استفاده می‌کند. مرجع دقیق `LiquidTextRotator` و `text-rotator-type-slide.css` هاب بررسی شد؛ framework، SplitText یا Draggabilly آن منتقل نشد. پیش‌فرض جدید مکث ۳٫۵ ثانیه و حرکت ۰٫۸ ثانیه است؛ مکث پس از زمان حرکت حساب می‌شود و تنظیم صریح قبلی حفظ است. fade/list و توقف خارج دید، فوکوس/هاور، صفحهٔ مخفی و reduced-motion محفوظ‌اند. کنترل Play/Pause همچنان در نمای عادی مخفی است.

`gsap-vendor` یک ورودی کوچک مشترک و مستقل از ScrollTrigger است؛ URL قالب اولویت دارد و افزونه بدون قالب fallback محلی دارد. ایراد build حذف exportهای ورودی قالب با `preserveEntrySignatures=exports-only` اصلاح شد. قرارداد تولیدی، وجود gsap/ScrollTrigger و هویت مشترک GSAP دو ورودی هر بسته را با import واقعی بررسی می‌کند؛ رندر موفق سورس به‌تنهایی شاهد این ایراد نبود. اجرای واقعی GSAP با کلاس has-word-motion، مکث/مدت جدید و CSS هایلایت موبایل در مرورگر محلی تأیید شد. ذخیره/بازخوانی PHP/Classic/Atomic و نگاشت واقعی WPML پاس شدند؛ Undo خود ادیتور طبق [شواهد جاری](../implementation/foundation.md) هنوز تأیید نشده است.

## کنترل توقف چرخش — اصلاح تصویر مالک، ۲۰۲۶-۱۰-۲۷

دکمهٔ Play/Pause نباید کنار عنوان چرخشی در طرح عادی دیده شود یا فضای چیدمان بگیرد. renderer و کنترل کیبورد مشترک حفظ‌اند، اما CSS انتخابی rotate آن را به‌صورت visually hidden نگه می‌دارد و فقط هنگام focus-visible کیبورد آشکار می‌کند. توقف با هاور، فوکوس، reduced motion و صفحهٔ مخفی همان رفتار قبل است. این تغییر متعلق به Text مشترک PHP/Classic/Atomic است و override مخصوص Child یا موتور موازی ندارد. ساختار `txt-rotate-keywords` در fancy-heading هابِ افزونهٔ `hub-elementor-addons` و stylesheet کوچک `text-rotator-base.css` بررسی شد؛ سورس آن کپی نشده است. ظاهر اصلاح‌شدهٔ نصب مالک هنوز تأیید نشده است.

## سایهٔ چندلایه و دستگاهی — ۲۰۲۶-۱۰-۲۶

شکاف سایهٔ چندلایه/دستگاهی در سورس بسته شد: هر قطعه حداکثر سه لایه دارد و desktop/tablet/mobile حالت مستقل inherit/none/custom دارند. inherit در تبلت از دسکتاپ و در موبایل از آخرین دستگاه بزرگ‌تر ارث می‌برد؛ none سایهٔ همان دستگاه را حذف می‌کند؛ custom می‌تواند تعداد، واحد و مقادیر خودش را داشته باشد. `shadow_layers` رشتهٔ enum محدود ۱–۳ است؛ هر لایه رنگ/افست افقی/عمودی/blur و واحد px/em/rem دارد. کلیدهای لایهٔ اول و `shadow` بومی Classic قبلی محفوظ‌اند؛ لایه‌های بعد `shadow_2_*` و `shadow_3_*` و دستگاه‌ها پسوندهای استاندارد دارند.

هسته/metadata سایه به `inc/components/text/shadows.php` جدا شد؛ APIهای قبلی همان namespace را دارند. Classic از Text Shadow بومی برای لایهٔ اول هر دستگاه و ترکیب بومی Number/Color/Select برای لایه‌های اضافه استفاده می‌کند؛ Atomic از Propهای عددی اعشاری/رنگ/enum موجود استفاده می‌کند. داخل Repeater یک تب مستقل «Text shadow» اضافه شد تا فیلدها با تایپوگرافی مخلوط نشوند؛ لایه‌ها فقط با count و حالت custom مربوط دیده می‌شوند. Repeater تو‌در‌تو، JSON متنی یا کنترل React جدید ساخته نشد.

خروجی هر دستگاه با موتور Presentation و متغیر `--rdct-segment-shadow` ساخته می‌شود؛ CSS/JS عمومی تازه یا GSAP ندارد. قطعهٔ قدیمی بدون override همان text-shadow قبلی را می‌دهد؛ متن خالی rule سایه تولید نمی‌کند. متن تصویر و alt را سایه نمی‌زند و fallback چاپ/forced-colors قبلی معتبر است. Build، ۲۵ بررسی JS/asset، قرارداد SSR لایه/واحد/صفر/حد/ارث‌بری و schema/control واقعی Atomic پاس شدند؛ ادیتور و ذخیره/Undo نصب واقعی هنوز پذیرش نشده‌اند.

## واحد حاشیهٔ تصویر و سایهٔ متن — ۲۰۲۶-۱۰-۲۶

شکاف border غیر px بسته شد: `image_border_unit` با گزینه‌های px/em/rem و override مستقل `_tablet`/`_mobile` از SELECT بومی Classic و Atomic مصرف می‌شود. عرض مشترک/چهار لبه همان کلیدهای عددی قبلی‌اند؛ مهاجرت یا تغییر Number Prop لازم نیست. تغییر صرف واحد در یک دستگاه، عرض معتبر به‌ارث‌رسیده را با واحد همان دستگاه بازنویسی می‌کند؛ صفر صریح و ۱- برای ارث‌بری حفظ‌اند. بازهٔ عرض ۰–۲۰ در واحد انتخابی است؛ درصد برای border-width معتبر نیست و عرضه نمی‌شود. نام فیلدهای عددی دیگر px را تحمیل نمی‌کند. renderer Presentation با `unit_field` همان CSS variableهای قبلی را تولید می‌کند؛ برای تصویر ناموجود rule دستگاهی ساخته نمی‌شود.

`shadow_unit` نیز px/em/rem دارد و مقدارهای اعشاری منفی/مثبت افست و blur غیرمنفی در Atomic حفظ می‌شوند. Classic کنترل Text Shadow بومی و SELECT واحد دارد؛ مقادیر native `shadow` و فیلدهای مستقل Atomic/PHP به renderer یکسان می‌روند. واحد نامعتبر به px برمی‌گردد. سایهٔ چندلایه و override دستگاهی در ادامهٔ بالای سند تکمیل شدند.

بدون CSS/JS یا وابستگی عمومی تازه؛ Build، ۲۴ بررسی JS/دارایی، قرارداد SSR متن و schema/control واقعی Atomic پاس شدند. بررسی ورودی مخرب، واحد نامعتبر، ارث‌بری تغییر صرف واحد، صفر و اعشار انجام شد. پذیرش ذخیره/Undo و نمایش واقعی نصب مالک هنوز باز است.

## ادامهٔ Typography، Background و وضعیت هدر — ۲۰۲۶-۱۰-۲۶

اندازهٔ مستقل فونت و فاصلهٔ واژهٔ هر قطعه اضافه شد؛ اندازهٔ فونت واحدهای px/%/em/rem/vw/vh و فاصلهٔ واژه px/em/rem دارد و هر دو از همان Presentation در سه دستگاه، SizeControl/Size Prop اتمیک و SLIDER responsive کلاسیک استفاده می‌کنند. فاصلهٔ منفی em/rem حروف نیز معتبر شد. در سطح کل متن، font_size/line_height/letter_spacing/word_spacing از renderer مشترک و override سه دستگاه می‌آیند؛ font_weight/style/transform/decoration بین PHP و دو ادیتور مشترک شد. فونت خالی همچنان ارث‌بری از سایت است و تنها registry محلی انتخاب مستقل می‌دهد.

برای تصویر داخل حروف، `mask_size=custom` با mask_width/height و `mask_position=custom` با mask_x/y بومی responsive اضافه شد؛ صفر و مقدار منفی موقعیت حفظ می‌شوند، خالی ارث می‌برد و مختصات سفارشی فیزیکی‌اند. حالت‌های start/end قدیمی منطقی و RTL باقی‌اند. mask_attachment از scroll/fixed انتخاب می‌شود. این قابلیت‌ها در همان stylesheet اختیاری segment-mask هستند، URL همچنان فقط attachment محلی معتبر است؛ برای متن solid دارایی تصویر یا CSS مختصات تازه لازم نیست.

گزینهٔ `header_colors` با سه رنگ اختیاری sticky/light/dark اضافه شد. فقط در `.rdct-bld--header` یا wrapper صریح `[data-rdct-header]` فعال می‌شود؛ section زیر هدر باید `data-rdct-header-tone="light|dark"` داشته باشد؛ رنگ‌سنجی تصویر/پس‌زمینه و حدس خودکار انجام نمی‌شود. sticky معتبر اولویت دارد و رنگ خالی به حالت پایه برمی‌گردد. تشخیص گیرکردن sticky از sentinel صفرارتفاع و موقعیت واقعی هدر است، نه یک آستانهٔ ثابت سراسری. تمام متن‌های یک هدر از یک listener/RAF و ResizeObserver مشترک استفاده می‌کنند و با آخرین cleanup حذف می‌شوند. CSS و JS header-state فقط با گزینهٔ فعال درخواست می‌شوند؛ GSAP برای تشخیص وضعیت یا تغییر بدون انیمیشن رنگ دریافت نمی‌شود. gradient/mask مستقل قطعه‌ها حفظ می‌شوند و با رنگ تک‌رنگ هدر پوشانده نمی‌شوند. ادیتور ثابت می‌ماند؛ رفتار اسکرول برای خروجی نهایی است.

مرجع‌های دقیق advanced-text::Typography/Background و fancy-heading::sticky_colors/sticky_light_colors/sticky_dark_colors دوباره با سورس ارسالی و مستند responsive/SLIDER بومی تطبیق داده شدند. این ادامه شکاف اندازه/واژه، هندسهٔ سفارشی mask و اتصال رنگ وضعیت هدر را می‌بندد؛ واحد حاشیه و سایه و سایهٔ چندلایه/دستگاهی در ادامهٔ بالای سند تکمیل شدند. برابری تمام واحدهای آزاد و ظاهر تمام SVGهای Hub همچنان OPEN است. این موارد حذف یا تکمیل‌شده فرض نشوند. پذیرش واقعی ذخیره/Undo، Global/Dynamic، WPML و نمایش RTL/LTR روی نصب مالک نیز باز است.

بررسی: Build، ۳۰ بررسی Node مرتبط با Text/asset graph، قرارداد SSR متن و schema/control واقعی Atomic پاس شدند. هیچ نسخه یا ZIP تازه‌ای ساخته نشد؛ مرورگر و تست طولانی اجرا نشد.

ادامهٔ ۲۰۲۶-۱۰-۲۶: rotator به ماژول شرطی `src/js/components/text/rotate.js` منتقل شد؛ reveal و lines نیز در همین پوشه‌اند. CSS متن rich به پایهٔ کوچک و سه قابلیت inline-image/segment-gradient/segment-mask تقسیم شد و requirements فقط موارد مصرف‌شده در حداکثر ۳۰ segment را انتخاب می‌کند. [فهرست خانواده‌ها و قرارداد بارگذاری](assets.md). مدل ذخیره‌شده و renderer تغییر نکردند؛ پذیرش دیداری نصب تازه همچنان باز است.

## تفکیک CSS بر اساس مصرف — ۲۰۲۶-۱۰-۲۵

text-effects.css فقط قواعد مشترک متن را دارد. fill، rotator و list آن و پایهٔ highlight به فایل‌های مستقل `src/css/components/text/` منتقل شدند؛ شکل‌های fill/wave/double/circle/brush فقط هنگام انتخاب همان شکل بارگذاری می‌شوند و underline از پایهٔ مشترک استفاده می‌کند. resolver دارایی مشترک PHP/Classic/Atomic و preview از تنظیم معتبر استفاده می‌کند؛ مرز موتور و دادهٔ ذخیره‌شده عوض نشده است. [قاعده و بررسی منفی بارگذاری](assets.md).

## حاشیهٔ مستقل تصویر در سه دستگاه — ۲۰۲۶-۱۰-۲۵

عرض مشترک و چهار لبهٔ تصویر rich با همان کلیدهای عددی `image_border_width/top/bottom/start/end` اکنون `_tablet` و `_mobile` دارند. Classic از NUMBER responsive بومی و Atomic از NumberControl/Number Prop موجود در همان Repeater استفاده می‌کنند؛ صفر، اعشار و id/دادهٔ قبلی حفظ می‌شوند. مقدار ۱- یا خالیِ دستگاه از مقدار دستگاه بزرگ‌تر ارث می‌برد؛ مقدار ۱- لبهٔ پایه همچنان از عرض مشترک ارث می‌برد. محدودهٔ خروجی ۰–۲۰ px است؛ CSS آزاد/واحد/مقدار نامتناهی پذیرفته نیست. رنگ انتخابی و alpha آن حتی با حاشیهٔ فقط موبایل و عرض صفر دسکتاپ حفظ می‌شوند.

مرجع Hub `advanced-text.php::border` با DIMENSIONS responsive بررسی شد؛ API همان `add_responsive_control` و پسوندهای استاندارد قبلی است. رادمان برای start/end منطقی و حفظ storage عددی چهار NUMBER بومی را نگه می‌دارد؛ کنترل سفارشی یا CSS پنل تازه ندارد. این ادامه responsive را می‌بندد، نه همهٔ واحدها یا رابط فیزیکی DIMENSIONS هاب. CSS روی همان تصویر و با breakpoint مشترک Presentation چاپ می‌شود؛ بدون override معتبر یا بدون تصویر، stylesheet تازه تولید نمی‌شود. registry مربوط به این قطعه‌ها یک‌بار در هر رندر فهرست گرفته می‌شود؛ دارایی/JS/رسانهٔ تازه نداریم. ذخیره/Undo و ظاهر واقعی سه دستگاه با نصب بعدی مالک پذیرش می‌شوند.

## شفافیت رنگ در تمام حالت‌های Text — ۲۰۲۶-۱۰-۲۵

validator محدود رنگ قطعه اکنون رنگ‌های عمومی idle/active، هایلایت، outline، گرادیان و رنگ مستقل واژه‌های Rotator را نیز پوشش می‌دهد؛ مسیر سادهٔ PHP و renderer حالت‌های دارای افکت قرارداد یکسان دارند. کنترل‌های COLOR بومی Classic در سطح ویجت و واژه alpha فعال دارند؛ Atomic همان adapter رشته‌ای قبلی را نگه می‌دارد. Hex شش‌رقمی قبلی و fallback رنگ نامعتبر حفظ می‌شوند؛ ID/ذخیره/فونت/حرکت/منابع عمومی تغییر نکردند. مرجع محدود `fancy-heading.php` هاب، رنگ Rotator و هایلایت، و قرارداد Color بومی مبنا بودند. این ادامه رنگ وابسته به وضعیت هدر، واحدهای آزاد و سایر جزئیات Background/Typography را تکمیل نمی‌کند؛ ظاهر واقعی و ذخیره/Undo همچنان باز است.

## شفافیت رنگ قطعه‌های متن — ۲۰۲۶-۱۰-۲۵

رنگ متن، دو رنگ گرادیان، حاشیهٔ تصویر و سایهٔ قطعه در حالت rich اکنون validator مشترک `segment_color()` دارند: HEX سه/چهار/شش/هشت‌رقمی، RGB/RGBA عددی با کانال ۰–۲۵۵ و alpha صفر–یک و `transparent` پذیرفته می‌شوند. مقدار خالی رنگ متن از والد ارث می‌برد؛ رنگ نامعتبر گرادیان/حاشیه/سایه به پیش‌فرض همان فیلد برمی‌گردد. مقدار صفر alpha حفظ می‌شود؛ CSS آزاد، var، URL، کانال خارج از محدوده و ورودی غیررشته‌ای رد می‌شوند. شفافیت رنگ با opacity کل قطعه یکی نیست و تصویر مجاور را محو نمی‌کند.

مرجع محدود Hub `elementor/params/advanced-text.php`، کنترل‌های advanced_text_color و Group Background و [قرارداد رسمی Color](https://developers.elementor.com/docs/editor-controls/control-color/) از Context7 با سورس Elementor 4.3.5 تطبیق داده شدند. alpha کنترل COLOR بومی Repeater کلاسیک روشن شد؛ Atomic از همان ColorControl و adapter رشته‌ای قبلی استفاده می‌کند که alpha را به HEX هشت‌رقمی تبدیل می‌کند. ID، schema ذخیره، JS/CSS و وابستگی تازه نداریم؛ رنگ‌های شش‌رقمی قبلی حفظ‌اند. رنگ‌های عمومی ویجت و واژه‌های Rotator نیز در ادامه طبق بخش بالای سند به همین validator متصل شدند.

خروجی SSR، پیش‌فرض‌ها، صفر alpha و ورودی مخرب با PHP و adapter بومی Atomic با بررسی کوتاه Node پوشش داده شدند؛ ظاهر واقعی، Global/Dynamic، ذخیره/Undo و WPML هنوز پذیرش نشده‌اند. سایر واحدها و جزئیات Typography/Background هاب و رنگ وضعیت هدر همچنان بازند.

## گرادیان شعاعی و محل توقف رنگ — ۲۰۲۶-۱۰-۲۵

مقایسهٔ `advanced-text.php` هاب و Group_Control_Background نسخهٔ ارسالی Elementor نشان داد حالت خطی به‌تنهایی معادل تنظیمات نوع/توقف/موقعیت نیست. در موتور مشترک قطعه‌های rich، `gradient_type` با پیش‌فرض `linear` و گزینهٔ `radial`، موقعیت دو رنگ بین ۰–۱۰۰ درصد و مرکز شعاعی با ۹ موقعیت فیزیکی اضافه شد. موقعیت شعاعی override تبلت/موبایل دارد و مقدار خالی ارث‌بری می‌کند؛ مرکز فیزیکی گرافیک خودکار با RTL معکوس نمی‌شود. مقدارهای قبلی بدون تغییر همان خطی ۹۰ درجه و توقف‌های ضمنی ۰/۱۰۰ درصد را می‌گیرند.

فیلدها به همان metadata و Repeater قبلی افزوده‌اند: Select/Number/Color بومی در Classic و SelectControl/NumberControl/ColorControl همان Atomic. موقعیت شعاعی Classic از `add_responsive_control(SELECT)` استفاده می‌کند؛ پسوندهای دستگاه مطابق قرارداد دادهٔ خود Elementor هستند. کنترل زاویه فقط در حالت خطی و کنترل مرکز فقط در حالت شعاعی دیده می‌شود؛ با تعویض نوع، مقدار قبلی پاک نمی‌شود. Number Prop مربوط به زاویه/توقف‌ها به‌صورت float sanitize می‌شود تا مقدار اعشاری مثل ۱۲٫۵ به ۱۲ بریده نشود. Group Control پیچیدهٔ Classic به مدل Atomic تحمیل نشده است.

مرکز از enum محدود و مقدارهای درصد از normalizer مشترک مصرف می‌شوند؛ رشتهٔ CSS آزاد وارد خروجی نمی‌شود. حالت solid/image برای تنظیمات گرادیان ذخیره‌شدهٔ غیرفعال، CSS موقعیت تازه تولید نمی‌کند. CSS شعاعی در همان فایل انتخابی rich است، بدون JS/GSAP/درخواست اضافه در frontend؛ ویرایش از همان چرخهٔ native فعلی preview استفاده می‌کند. selector نوع شعاعی با `:where` وزن بیشتری از fallback چاپ/forced-colors نمی‌گیرد. شفافیت رنگ‌ها در ادامه طبق بخش بالای سند تکمیل شد؛ این تغییر همچنان برابری تمام ویژگی‌های Background هاب نیست.

قرارداد PHP متن، schema/validator/sanitizer واقعی Atomic و Repeater بومی، نوع/موقعیت معتبر و نامعتبر، عدد اعشاری/حدود، حالت‌های خطی قدیمی، ارث‌بری سه دستگاه و شرط نمایش فیلدها را پوشش می‌دهند. نمایش واقعی گرادیان فارسی، چاپ/forced-colors و ذخیره/Undo در editor هنوز پذیرش نشده‌اند.

## گردی مستقل تصویر قطعه و CSS مشترک — ۲۰۲۶-۱۰-۲۵

شکاف چهار گوشهٔ تصویر rich در سورس بسته شد: کلیدهای `image_radius_start_start/start_end/end_start/end_end` هرکدام اندازه/واحد و override تبلت/موبایل دارند؛ خالی یعنی ارث‌بری و صفر یک مقدار صریح است. مقدار مشترک قدیمی `image_radius` حفظ شد. مرجع `params/advanced-text.php::border_radius` هاب DIMENSIONS فیزیکی با واحد مشترک است؛ در رادمان چهار SLIDER responsive بومی Classic و SizeControl/Size Prop بومی Atomic از همان metadata مشترک مصرف می‌شوند تا معنای گوشه‌های منطقی RTL/LTR و واحد مستقل هر گوشه روشن باشد. کنترل یا پنل سفارشی تازه نوشته نشد؛ این تطبیق فیزیکیِ عین UI هاب نیست.

گردی مشترک و گوشه‌ها متغیرهای CSS مستقل دارند؛ تغییر مقدار مشترک در موبایل، مقدار مستقلِ به‌ارث‌رسیدهٔ گوشه را reset نمی‌کند. CSS از `border-start-start-radius` و سه گوشهٔ منطقی دیگر استفاده می‌کند و در نبود override همان ظاهر قدیمی را دارد. Size غیرفعال Atomic نیز در مسیر مشترک Presentation مقدار تازه چاپ نمی‌کند. ورودی نامعتبر/واحد ناشناخته رد و خروجی عددی در محدودهٔ مشترک نگه داشته می‌شود.

Presentation می‌تواند stylesheetهای یک fragment را به collector همان رندر بدهد. Text Segments قواعد یکسانِ قطعه‌های یک متن را یک‌بار چاپ می‌کند؛ collector عمومی/سراسری نیست تا fragment مستقلِ کش‌شدهٔ Elementor بدون stylesheet نماند. دو متن مستقل هرکدام خروجی کامل خودشان را دارند. stylesheet ثابت همچنان فقط در rich و بدون JS اضافه مصرف می‌شود؛ قرارداد ۳۰ قطعه با ظاهر برابر فقط یک style در هر fragment و کامل‌بودن رندر مستقل بعدی را بررسی می‌کند.

چهار گوشه، سه دستگاه، واحدهای px/%/em/rem، صفر/خالی/غیرفعال، ورودی نامعتبر، schema واقعی Size اتمیک و Repeater/SizeControl بومی بررسی شدند. قرارداد قدیمی Atomic برای مدل‌های embedشدهٔ کتابخانه با معماری فعلیِ دریافت مدل منتخب هماهنگ شد و پوشش هر دو renderer حفظ شد. ظاهر واقعی، RTL/LTR محاسبه‌شده، ذخیره/Undo و کش نصب مشتری هنوز پذیرش نشده‌اند.

## آشکارشدن خط‌به‌خط و ماسک — ادامهٔ سورس، ۲۰۲۶-۱۰-۲۴

کنترل‌های `enable_split/split_type/use_mask` در `themes/hub-elementor-addons/elementor/widgets/fancy-heading.php` مرجع محدود این تکمیل بودند؛ موتور Hub یا SVG آن کپی نشد. در حالت Scroll همان Text، `split_type=words|lines` با پیش‌فرض سازگار words و کلید `line_mask` با پیش‌فرض خاموش اضافه شد. SELECT/SWITCHER خود Classic و schema/Select/Switch بومی Atomic از fields/groups مشترک مصرف می‌شوند؛ کنترل ماسک فقط برای scroll/lines نشان داده می‌شود. PHP نیز همان گزینه‌ها را دارد؛ مسیر ساده و مسیر دارای لینک/ظاهر از `split_attributes` مشترک استفاده می‌کنند. Fill/Static/Rotator/Rich این گزینه‌ها را فعال نمی‌کنند؛ حروف فارسی جدا نمی‌شوند.

`text-reveal-entry.js` فقط برای scroll/lines ماژول `text-lines.js` را import می‌کند؛ SplitText همان GSAP قفل‌شدهٔ 3.15.0 محلی است و به vendor عمومی یا مسیر کلمه‌ای اضافه نشده است. از vendor مشترک GSAP/ScrollTrigger و تنظیمات مشترک `revealTween` استفاده می‌شود؛ موتور دوم اسکرول، وابستگی جدید npm، listener resize اختصاصی یا فونت خارجی نداریم. [مستند رسمی SplitText](https://gsap.com/docs/v3/Plugins/SplitText/) با Context7 و سورس نصب‌شده تطبیق داده شد: type فقط lines، autoSplit، حفظ whitespace، onSplit با برگرداندن tween و revert بومی برای بازتقسیم/پاک‌سازی است. ماسک خط از yPercent=100 به صفر و opacity تنظیم‌شده می‌آید؛ بدون ماسک فقط opacity خطی است. ترتیب، stagger، start/end و scrub همان کنترل‌های موجودند.

متن کامل برای screen reader مستقل و نسخهٔ بصری aria-hidden است؛ SplitText روی نسخهٔ بصری aria=none دارد. پیش از آماده‌شدن فونت، در reduced motion، خطای بارگذاری یا لغو، متن خوانا باقی است؛ teardown و تغییر reduced motion، split/انیمیشن/observer بومی را revert و متن اولیه را برمی‌گردانند. سقف قبلی ۴۰۰ کلمه/۱۶۰۰۰ بایت حفظ شد. autoSplit محاسبهٔ خطوط بعد از تغییر عرض/فونت را انجام می‌دهد؛ صحت شکل واقعی فارسی، خطوط چندزبانه و ماسک در مرورگر هنوز پذیرش می‌خواهد. [شواهد فعلی و محدودیت PHP](../implementation/foundation.md).

## ارتفاع خط و فاصلهٔ حروف قطعه — ادامهٔ سورس، ۲۰۲۶-۱۰-۲۴

Group Typography در `themes/hub-elementor-addons/elementor/params/advanced-text.php` و فیلدهای `line_height`/`letter_spacing` در `themes/elementor/includes/controls/groups/typography.php` مرجع این تکمیل بودند. هر قطعه اکنون این دو مقدار را مستقل در دسکتاپ/تبلت/موبایل دارد. مقدار خالی ارث‌بری را حفظ می‌کند؛ فاصلهٔ حروف صفر و منفی px معتبر است. ارتفاع خط px/em/rem/lh/rlh و فاصلهٔ حروف px/em/rem را با حدود متناسب هر واحد می‌پذیرند؛ CSS دلخواه/custom unit پذیرفته نیست.

Classic از SLIDER responsive و Atomic از SizeControl/Size Prop بومی استفاده می‌کنند؛ خط در Atomic نیز واحد پیش‌فرض em دارد. فیلدها در گروه متن قطعه‌اند؛ تصویر/اندازهٔ آن مستقل می‌ماند. همان registry/renderer responsive موجود مصرف می‌شود؛ نام داخلی قدیمی `text-segment-image` برای سازگاری حفظ شد و اکنون قواعد متن همان قطعه را نیز نگه می‌دارد. فقط قواعد دارای مقدار و مقصد معتبر به stylesheet محدود همان قطعه می‌روند؛ نبود تصویر مانع تنظیم متن نیست و تصویر حذف‌شده CSS بی‌مصرف تصویر نمی‌سازد. فایل CSS عمومی، JS، درخواست preview یا dependency تازه ندارد.

قرارداد SSR برای ارث‌بری، صفر، مقدار منفی، واحد، responsive و ورودی مخرب و schema واقعی Atomic با Size/em پاس شد؛ build/ترجمه و بررسی کوتاه Repeater انجام شدند. ظاهر خط و فاصلهٔ واقعی، RTL/LTR و ذخیره/Undo ادیتور هنوز پذیرش نشده‌اند. این تکمیل به معنی همهٔ جزئیات Typography/Background هاب نیست.

## ظاهر قطعه و رزولوشن تصویر داخل حروف — Components 0.1.33

مرجع محدود `themes/hub-elementor-addons/elementor/params/advanced-text.php`، بخش Group Typography و Gradient/Mask دوباره خوانده شد. هر قطعه اکنون font_style، text_transform و text_decoration مستقل دارد؛ حالت خالی ارث‌بری را حفظ می‌کند و گزینهٔ none/normal امکان بازنشانی صریح دارد. وزن‌های ۱۰۰/۲۰۰/۳۰۰ نیز به فهرست قبلی اضافه شدند. CSS محدود فقط روی span متن اعمال می‌شود؛ تصویر میان‌خطی تغییر نمی‌کند و متن اصلی HTML برای تبدیل ظاهری حروف بازنویسی نمی‌شود. همهٔ مقادیر enum هستند و CSS آزاد پذیرفته نیست. کنترل‌ها SELECT بومی Classic و SelectControl همان Repeater بومی Atomic‌اند؛ control/panel اختصاصی تازه‌ای ساخته نشد. برای مشترک‌ماندن schema و renderer مستقل PHP، Group Typography مخصوص Classic به Atomic تحمیل نشده است؛ ارتفاع خط و فاصلهٔ حروف در ادامهٔ سورس طبق بخش بالا اضافه شدند؛ همهٔ جزئیات Typography مرجع هنوز معادل کامل نیستند.

`mask_resolution` اندازهٔ فایل تصویر داخل حروف را از نام اندازه‌های ثبت‌شدهٔ WordPress یا full می‌گیرد؛ با `mask_size` مربوط به cover/contain فرق دارد. پیش‌فرض large اسناد قبلی حفظ شده؛ انتخاب اندازهٔ کوچک‌تر یا اندازهٔ اختصاصی مشتری ممکن است و ورودی نامعتبر در PHP به large برمی‌گردد. فهرست در همان درخواست cache می‌شود؛ ساخت فایل/دانلود یا درخواست شبکه برای پرکردن فهرست نداریم. attachment پس از نگاشت WPML از API بومی حل می‌شود و فقط قطعهٔ دارای متن و paint=image سراغ URL تصویر می‌رود. اگر مشتق تصویر موجود نباشد، fallback استاندارد WordPress ممکن است فایل اصلی باشد؛ انتخاب یک اندازه تضمین حجم نهایی نیست. [API اندازه‌های ثبت‌شده](https://developer.wordpress.org/reference/functions/get_intermediate_image_sizes/) و [URL تصویر](https://developer.wordpress.org/reference/functions/wp_get_attachment_image_url/) با Context7 تطبیق داده شدند.

هر دو adapter فیلدها را از همان schema می‌گیرند؛ preview اتمیک از renderer مشترک موجود استفاده می‌کند و endpoint/درخواست جدیدی اضافه نشد. CSS/JS تازه‌ای برای حالت rich لازم نیست. قرارداد کوتاه SSR، ورودی مخرب، اندازهٔ اختصاصی، حفظ متن و schema typed بومی پاس شد؛ ظاهر/ذخیره/Undo واقعی و WPML واقعی همچنان پذیرش نشده‌اند.

مالک موتور: `radmanct-components/inc/components/text-{fields,effects,reveal}.php`؛ قرارداد بومی کنترل‌ها در [کامپوننت‌ها](components.md). نام نمایشی **Radman CT Text** است؛ IDهای ذخیره‌شدهٔ `radman-text-reveal` و `rdct-atomic-text-reveal` و helper قدیمی حفظ شده‌اند. کتابخانه یک نمونهٔ Text دارد؛ انتخاب حالت در همان ویجت است.

## مرجع و رفتار فعلی

سورس بررسی‌شده: `themes/hub-elementor-addons/elementor/widgets/fancy-heading.php`، `text-reveal.php` و `elementor/params/advanced-text.php`. موتور، framework و SVGهای اختصاصی Hub کپی نشده‌اند.

| بخش مرجع | پیاده‌سازی فعلی |
|---|---|
| متن، تگ، جهت، لینک | PHP و Classic/Atomic؛ لینک خارجی با noopener و گزینهٔ nofollow |
| Reveal | opacity نهایی/اولیه، start/end، scrub، stagger و ترتیب؛ کلمه یا خط کامل با ماسک اختیاری خط، بدون جداکردن حروف فارسی |
| پرشدن | رنگ اولیه/نهایی و پرشدن هر کلمه با ScrollTrigger؛ جهت آغاز با RTL/LTR |
| Rotator | slide، fade و list؛ Repeater واژه/عبارت و رنگ هر مورد، رنگ و گرادیان فعال، hold/duration، تعداد نمایان responsive، حداکثر ۳۰ مورد |
| Marquee | حالت همان Text، با موتور مشترک کاروسل؛ سرعت، جهت، چرخه و حدود محتوای ثابت فقط در [مالک Marquee](carousel.md) تعریف شده‌اند |
| Highlight | نشانه‌گذاری `[[عبارت]]`؛ underline، fill، wave، double، circle و brush با CSS رادمان؛ آغاز منطقی/پایین/fade و پُرشدن با هاور/فوکوس؛ اندازه/offset/گردی responsive و زمان/تأخیر مستقل |
| Outline | خاموش، دائم یا هاور/فوکوس، ضخامت و رنگ |
| ظاهر | gradient، nowrap، نوشتار افقی/عمودی؛ Typography بومی Classic برای متن/واژه و text shadow؛ سبک‌های بومی Atomic و اندازه/تراز responsive مستقل |
| متن و تصویر میان‌خطی | حالت `rich` در همان ویجت؛ حداکثر ۳۰ قطعه با متن، تصویر رسانه، قبل/بعد منطقی، تراز عمودی، رنگ، خانوادهٔ محلی، وزن و اندازهٔ نسبی هر قطعه؛ عرض/گردی مشترک تصاویر responsive |
| فونت | انتخاب مستقل متن/واژه از خانواده‌های محلی که همین حالا در تنظیمات نقش‌های رادمان استفاده می‌شوند؛ انتخاب خانوادهٔ جدید از تنظیمات فونت، بدون بارگیری تمام registry |

`rdct_text_reveal( 'Our [[work]]', ['mode'=>'static','highlight'=>'underline','tag'=>'h2'] )` برای PHP؛ برای تغییر واژه: `mode=rotate` و `items=[['title'=>'Design','color'=>'#172b3a'],['title'=>'Build']]`. متن اولیه و همهٔ واژه‌ها بدون JS خوانا هستند. واژهٔ تنها با prefix خالی نیز مجاز است. `font_family` و `word_font_family` باید از گزینه‌های مجاز همان سایت باشند؛ رشتهٔ CSS آزاد پذیرفته نمی‌شود.

## کنترل و چرخه

Classic: SELECT/URL/COLOR/REPEATER/SLIDER responsive و Group Typography/Text Shadow خود Elementor. Atomic: schema/props/Section/controlهای بومی؛ تکرارگر واژه ترکیب **همان Repeater و PropProvider بومی** با wrapper مشترک Curtain/Carousel است، بدون CSS ورودی یا React دوم. رنگ‌ها از picker بومی می‌آیند. JSON schema و دادهٔ preview یک renderer PHP دارند؛ Twig فقط marker تایپ‌دار و escape‌شده می‌سازد. شرط‌های وابسته به حالت با native dependencies اعمال می‌شوند.

CSS پایه از `text-reveal.css`؛ CSS افکت‌ها فقط هنگام انتخاب از `text-effects.css`. JS تغییر واژه فقط هنگام مصرف؛ GSAP فقط برای scroll/fill به‌صورت import شرطی. Rotator بدون GSAP است، بیرون viewport/صفحهٔ مخفی، هاور، فوکوس و reduced motion توقف دارد؛ دکمهٔ پخش/توقف و متن قابل دسترس دارد. cleanup، درخواست preview منقضی و انتظار font-ready کنترل می‌شوند. سقف reveal برابر ۴۰۰ کلمه است. هنگام reveal/fill نشانهٔ هایلایت حذف و خود متن پیوسته حفظ می‌شود؛ بافت word splitting با هایلایت دیگری ترکیب نمی‌شود.

## هندسه و زمان هایلایت

Classic بخش «Text highlight» در تب Style با Slider responsive بومی دارد؛ Atomic همان schema و کنترل responsive مشترک مبتنی بر UI بومی را در یک Section مصرف می‌کند. پنج کلید `highlight_{width,height,offset,inline_offset,radius}_responsive` در PHP نیز آرایهٔ desktop/tablet/mobile می‌پذیرند. عرض ۰–۲۰۰ درصد، ارتفاع ۰–۲۴۰ px، offset پایین/ابتدای خواندن ‎−۲۰۰ تا ۲۰۰ px و گردی ۰–۲۰۰ px است. offset آغاز با logical property در RTL/LTR اعمال می‌شود؛ صفر معتبر، خالی ارث‌بری و CSS آزاد نامعتبر است. بدون override ظاهر قبلی شکل حفظ می‌شود. Wave از text-decoration استفاده می‌کند و این هندسه را ندارد؛ کنترل‌ها فقط برای شکل‌های CSS و حالت‌های static/hover/rotate/marquee نمایش داده می‌شوند.

`highlight_duration` برابر ۰٫۱–۱۰ ثانیه با پیش‌فرض ۰٫۷ و `highlight_delay` برابر ۰–۱۰ با پیش‌فرض صفر است. فقط لایهٔ تزئینی زمان‌بندی می‌شود؛ متن منتظر انیمیشن نمی‌ماند. reduced motion انیمیشن و تأخیر را حذف می‌کند. تنظیمات روی CSS انتخابی موجود اعمال می‌شوند، بدون موتور یا فایل JS جدید. نمونهٔ PHP: `['mode'=>'static','highlight'=>'underline','highlight_width_responsive'=>['desktop'=>110,'mobile'=>100],'highlight_inline_offset_responsive'=>['desktop'=>-4],'highlight_duration'=>1]`.

## برابری باقی‌مانده و پذیرش

### اندازهٔ مستقل تصویر قطعه — Components 0.1.30، ۲۰۲۶-۱۰-۲۴

کار نیمه‌تمام پیش از بررسی گزارش‌ها تکمیل شد: image_width و image_radius برای هر قطعه با مقدار `{size,unit}` و پسوندهای `_tablet` و `_mobile` مستقل‌اند؛ خالی از اندازهٔ قبلی/تنظیم مشترک ارث می‌برد و صفر صریح حفظ می‌شود. px، %، em و rem با عدد محدود پذیرفته‌اند؛ width در PHP به ۰–۲۰۰۰ و radius به ۰–۲۰۰ محدود می‌شود. max-inline-size قبلی تصویر/figure حفظ است. واحد یا عبارت CSS آزاد و مقدار نامتناهی رد می‌شوند؛ بدون تصویر یا بدون override، CSS اضافه تولید نمی‌شود.

Classic برای اندازه/گردی از SLIDER responsive بومی و Atomic برای هر دستگاه از SizeControl بومی با Size_Prop_Type و واحدهای محدود استفاده می‌کنند؛ ویژگی enablePropTypeUnits/disableCustom با سورس Elementor 4.3.5 تطبیق داده شد. قرارداد native SizeControl سقف عددی UI ندارد؛ سقف خروجی در PHP اعمال می‌شود. مسیر responsive مشترک Presentation تنظیم breakpointهای بومی tablet/mobile را، در صورت حضور Elementor، مصرف می‌کند؛ موتور یا JS عمومی دوم ساخته نشد. مرجع Hub advanced-text عرض/گردی و [قرارداد رسمی responsive](https://developers.elementor.com/docs/editor-controls/responsive-control/) با Context7 بررسی شدند. گردی مستقل چهار گوشه در ادامهٔ سورس بالای این سند اضافه شد؛ این بخش هنوز تضمین همهٔ واحدها/رابط فیزیکی مرجع نیست.

نمونهٔ PHP: `['image'=>['id'=>7],'image_width'=>['size'=>12.5,'unit'=>'em'],'image_width_mobile'=>['size'=>50,'unit'=>'%'],'image_radius_mobile'=>['size'=>0,'unit'=>'px']]` داخل segments. مقدار typed native Size و دادهٔ ساده از یک validator/renderer عبور می‌کنند. آزمون محدود SSR و Repeater ارث‌بری/صفر/اعشار/واحد/ورودی مخرب را پوشش داد؛ ذخیره/Undo، viewport واقعی و cache ادیتور پذیرش نشده‌اند.

درخواست همهٔ فیلدهای Hub هنوز **کامل نشده**: پایهٔ advanced-text، گرادیان خطی/شعاعی، mask تصویری، سایهٔ تک‌لایه، حاشیه و گردی مستقل و split/mask خطی ساخته شده‌اند. ادامهٔ Typography/Background و اتصال وضعیت هدر در بخش ابتدای همین سند ثبت است؛ فهرست OPEN جاری نیز همان‌جاست. انیمیشن عمومی عنصر از [موتور حرکت مشترک](container-motion.md) می‌آید؛ کنترل بدون رفتار واقعی اضافه نشود.

### سایهٔ مستقل متن قطعه — Components 0.1.29

`shadow_mode=inherit|none|custom` پیش‌فرض inherit دارد؛ دادهٔ قبلی تغییری نمی‌کند، none سایهٔ موروثی همان متن را حذف می‌کند و custom یک سایه با offset افقی/عمودی ‎−۱۰۰ تا ۱۰۰ px و blur صفر تا ۱۰۰ px می‌سازد. رنگ hex شامل alpha و rgb/rgba محدود پذیرفته می‌شود؛ CSS آزاد، URL، رنگ نامعتبر و مقدار نامتناهی رد یا به مقدار امن برمی‌گردند. سایه روی `.rdct-text__part` اعمال می‌شود و تصویر کنار متن را هدف نمی‌گیرد. در چاپ و forced-colors سایهٔ سفارشی خاموش است؛ JS، dependency یا فایل عمومی تازه ندارد.

Classic از کنترل آمادهٔ `Controls_Manager::TEXT_SHADOW` با دادهٔ بومی `shadow={horizontal,vertical,blur,color}` استفاده می‌کند. Atomic از NUMBER/ColorControl بومی در همان Repeater مشترک و فیلدهای `shadow_horizontal/vertical/blur/color` با schema تایپ‌دار موجود استفاده می‌کند؛ renderer هر دو نمایش داده را به یک CSS محدود تبدیل می‌کند. حالت غیرسفارشی کنترل‌ها را پنهان می‌کند، نه اینکه مقدار ذخیره‌شده را پاک کند. PHP هر دو ساختار را می‌پذیرد؛ نمونه: `['title'=>'Work','shadow_mode'=>'custom','shadow_horizontal'=>2,'shadow_vertical'=>4,'shadow_blur'=>8,'shadow_color'=>'#0000004d']` داخل segments.

سورس محدود `themes/hub-elementor-addons/elementor/params/advanced-text.php` و `widgets/fancy-heading.php` مرور شد؛ import کلاس سایه در آن‌ها به‌تنهایی وجود کنترل فعال سایهٔ هر قطعه را ثابت نمی‌کند. جزئیات کنترل Classic از [Text Shadow رسمی](https://developers.elementor.com/docs/editor-controls/control-text-shadow/) با Context7 و `themes/elementor/includes/controls/text-shadow.php` نسخهٔ 4.3.5 تطبیق داده شد. Atomic کنترل مستقل متن‌سایهٔ معادل عرضه نمی‌کند؛ BoxShadowRepeaterControl شامل spread/inset نامرتبط است، بنابراین ترکیب کنترل‌های بومی عدد و رنگ انتخاب شد و پوستهٔ اختصاصی ساخته نشد. این پیاده‌سازی سایهٔ تک‌لایه و کنترل px است؛ سایهٔ چندلایه، واحدهای آزاد و override دستگاهی در این مرحله ساخته نشده‌اند.

قرارداد PHP حالت قبلی/خاموش/سفارشی، دو ساختار Classic/Atomic، دامنهٔ متن در برابر تصویر، alpha و ورودی مخرب را پوشش داد. بررسی JS شرط نمایش و حفظ مقدار هنگام تغییر حالت، همراه ۱۵ بررسی کوتاه Repeater/دارایی، Build و lint سه PHP پاس شدند. این‌ها پذیرش واقعی پنل، ذخیره/Undo، فونت/ظاهر در مرورگر یا WPML نیستند.

### حاشیهٔ مستقل تصویر قطعه — Components 0.1.28

چهار کلید `image_border_top/bottom/start/end` با حدود ۰–۲۰ px و پیش‌فرض ۱- برای ارث‌بری از `image_border_width` اضافه شدند. صفر، حذف صریح همان لبه است؛ انتخاب یک لبه حتی با عرض مشترک صفر کار می‌کند. رنگ مشترک قبلی حفظ و CSS logical برای start/end مصرف می‌شود؛ رسانه/کوئری/JS جدید نداریم و CSS همچنان فقط در حالت rich مصرف می‌شود. داده‌های قبلی بدون override همان خروجی یکنواخت را دارند. responsive مستقل عرض مشترک و هر لبه طبق بخش بالای سند تکمیل شد؛ واحدهای غیر px هنوز ساخته نشده‌اند.

مرجع `themes/hub-elementor-addons/elementor/params/advanced-text.php` کنترل DIMENSIONS فیزیکی با واحدهای آزاد دارد. [مستند رسمی Dimensions](https://developers.elementor.com/docs/editor-controls/control-dimensions/) با Context7 بررسی شد؛ برای معنای منطقی start/end و ارث‌بری مستقل نسبت به عرض مشترک، چهار کنترل NUMBER خود Classic و NumberControl خود Atomic از schema مشترک مصرف شدند، بدون کنترل یا stylesheet اختصاصی پنل. schema تایپ‌دار Atomic همان Number Prop موجود را مصرف می‌کند. قرارداد PHP صفر/ارث‌بری/لبهٔ مستقل و ورودی مخرب را پوشش داد؛ پذیرش ظاهر کنترل، ذخیره/Undo و RTL/LTR واقعی باقی است.

## قطعه‌های متن و تصویر — ۲۰۲۶-۱۰-۲۳

موتور مستقل `text-segments.php` در حالت `rich` فقط از renderer مشترک فراخوانی می‌شود؛ ویجت یا کارت تکراری ندارد. Classic از Repeater/Media/Select/Color/Number بومی و Atomic از همان Repeater/PropProvider مشترک با `ImageControl` و `Image_Prop_Type` خود Elementor 4.3.5 استفاده می‌کند. قطعهٔ بدون تصویر معتبر است؛ افزودن/تکثیر/مرتب‌سازی به Repeater بومی سپرده می‌شود. شناسه‌های قبلی Text و واژه‌های متغیر حفظ شدند. پیش‌فرض عرض/گردی تصاویر از Slider responsive مشترک برای سه دستگاه و PHP می‌آید؛ هر قطعه از نسخهٔ 0.1.30 override مستقل اندازه دارد؛ اندازهٔ نسبی متن از اندازهٔ responsive عنوان ارث می‌برد. محدودیت‌های موتور قبل از کار رسانه اعمال می‌شوند؛ CSS آزاد/HTML آزاد یا URL تصویر دلخواه پذیرفته نمی‌شود.

تصویر با attachment ID، نگاشت attachment زبان جاری و `wp_get_attachment_image` رندر می‌شود؛ alt، ابعاد و srcset بومی حفظ و تصمیم loading به WordPress واگذار می‌شود. Classic به‌صورت پایه medium است؛ ابعاد resolution حل‌شدهٔ Atomic به انتخاب مشتق بومی منتقل می‌شود. حالت rich علاوه بر CSS مشترک Text، فقط `text-segments.css` را مصرف می‌کند؛ قواعد قطعه/تصویر از CSS افکت‌های عمومی جدا شدند و Text معمولی این فایل را نمی‌گیرد. JS/GSAP عمومی تازه ندارد. اگر قطعه‌ها معتبر نباشند، متن اصلی fallback است. فیلدهای متن تکرارها برای Classic و ساختار typed Atomic در WPML config ثبت شدند؛ این ثبت اثبات ترجمهٔ واقعی نیست.

**Components 0.1.22:** هر قطعه در Classic تب‌های native و در Atomic ToggleButtonGroup همان UI با سه گروه محتوا/متن/تصویر دارد؛ تغییر گروه مقدار فیلد پنهان را پاک نمی‌کند. renderer مشترک گرادیان دو‌رنگ با زاویهٔ ۰–۳۶۰ درجه را فقط روی متن اعمال می‌کند؛ تصویر و alt وارد text clipping نمی‌شوند. در نبود background-clip، چاپ و forced colors، رنگ خوانا باقی می‌ماند. حاشیهٔ تصویر ضخامت ۰–۲۰ px و رنگ محدود دارد؛ صفر پیش‌فرض ظاهر قبلی را حفظ می‌کند و box-sizing جلوی افزایش عرض تعیین‌شده را می‌گیرد. فیلدهای رنگ/عدد/انتخاب، کنترل بومی یا ترکیب UI بومی هستند؛ پنل یا React مستقل نداریم. این کنترل‌های محدود جای همهٔ جزئیات Group Background/Border مرجع نیستند. تنظیمات رنگ قدیمی بدون paint جدید، همچنان solid هستند.

نمونهٔ سفارشی: `rdct_text_reveal('', ['mode'=>'rich','segments'=>[['title'=>'Design '],['image'=>['id'=>7],'title'=>'[[Together]]','image_position'=>'start','font_weight'=>'700']], 'inline_image_width_responsive'=>['desktop'=>96,'mobile'=>56]])`. فاصله و متن فارسی شکسته نمی‌شوند؛ ترتیب start/end در جریان منطقی زبان قرار دارد. این حالت splitting اسکرولی یا Rotator را هم‌زمان روی قطعه‌ها اعمال نمی‌کند؛ حرکت عمومی wrapper از موتور مشترک می‌آید.

**Components 0.1.24 — پرکردن متن با تصویر:** `paint=image` و `mask_image` از Media کلاسیک و ImageControl/Image_Prop_Type اتمیک همان تکرارگر استفاده می‌کنند؛ تصویر میان‌خطی مستقل می‌ماند. `mask_size` محدود به cover/contain/auto، `mask_repeat` به no-repeat/repeat/repeat-x/repeat-y و `mask_position` به center/start/end/top/bottom است؛ آغاز/پایان در RTL معکوس می‌شوند، خود نوشته و تصویر آینه نمی‌شوند. انتخاب و شرط‌ها ترکیب کنترل‌های بومی‌اند؛ Group Background کلاسیک به API اتمیک تحمیل نمی‌شود. مرجع محدود `advanced-text.php` هاب، Group Background و text clipping دوباره خوانده شد؛ API [تصویر attachment](https://developer.wordpress.org/reference/functions/wp_get_attachment_image_url/) با مستند رسمی تطبیق داده شد.

موتور مشترک فقط attachment تصویر معتبر را پس از نگاشت WPML به URL اندازهٔ large حل می‌کند؛ URL آزاد ورودی پذیرفته نمی‌شود. URL در رشتهٔ CSS نقل‌قول‌دار escape و کنترل‌کاراکتر رد می‌شود؛ هیچ درخواست PHP برای دانلود تصویر وجود ندارد. تصویر در پس‌زمینهٔ حروف تزئینی است، خود متن HTML قابل‌خواندن می‌ماند. تصویر گم‌شده به solid برمی‌گردد؛ هنگام خطای بارگیری، background-color زیر تصویر رنگ حروف را حفظ می‌کند. بدون background-clip، در چاپ و forced-colors متن رنگ معمولی دارد. قطعهٔ بدون متن یا paint غیرفعال کار رسانهٔ mask انجام نمی‌دهد. CSS در همان فایل انتخابی rich است و JS تازه ندارد. نمونه: `['title'=>'Together','paint'=>'image','mask_image'=>['id'=>7],'mask_position'=>'start']` داخل segments. اندازهٔ large پیش‌فرض است و از Components 0.1.33 رزولوشن از اندازه‌های ثبت‌شده قابل انتخاب است؛ تمام حالت‌های attachment/responsive Background هاب هنوز معادل کامل ندارند.

Build و قراردادهای کوتاه جدا از پذیرش واقعی‌اند. تراز بصری، نوع درج/Undo/ذخیره در editor واقعی، مرورگر موبایل، WPML واقعی و برابری تمام ظاهرهای Hub همچنان تأیید نشده‌اند؛ Node/schema/PHP جای آن‌ها نیستند.


### Header observer sharing - 2026-11-01

Text header-state retains its explicit per-component light/dark/sticky color fields, but subscribes to the same optional header-tone manager as the Theme header contrast. Lifecycle and asset boundaries are specified in [builder](builder.md#shared-section-contrast-for-headers---2026-11-01). Ordinary text outside a header creates no observer.
