# ذرات — موتور مشترک PHP و Elementor

## قرارداد و مرجع

CONFIRMED، ۲۰۲۶-۱۰-۲۵: تصویر مالک مربوط به Liquid Particles است. `themes/hub-elementor-addons/elementor/widgets/particles.php`، rule/assets همان ویجت، دو CSS بخش particles، `themes/hub/assets/js/theme.js::LiquidParticles` و vendor آن خوانده شدند. موتور مرجع particles.js 2.0.0 است؛ GSAP برای این canvas لازم نیست و چارچوب/jQuery هاب منتقل نشده است. مستند رسمی [تنظیمات upstream](https://github.com/VincentGarreau/particles.js/blob/2.0.0/README.md) از Context7 با نسخهٔ محلی تطبیق داده شد.

هر ۶۰ فیلد ویجت هاب در schema/adapter رادمان پوشش دارند؛ این شمارش اثبات برابری بصری نیست. چهار بخش پنل:

| بخش | پوشش |
|---|---|
| اصلی | پیشوند شناسه، پس‌زمینهٔ والد، نمایش هنگام هاور والد و ارتفاع؛ ارتفاع تبلت/موبایل نیز مستقل با ارث‌بری |
| ذرات | تعداد/تراکم، تک‌رنگ/چندرنگ/تصادفی و Repeater رنگ، شش شکل با انتخاب ترکیبی، خط دور، ضلع‌های چندضلعی، تصویر و نسبت ابعاد، شفافیت/اندازه و تصادفی/انیمیشن/سرعت/حداقل/Sync هرکدام، خطوط اتصال و فاصله/رنگ/شفافیت/ضخامت، حرکت و سرعت/هشت جهت/تصادفی/مستقیم/خروج/برخورد/جاذبه/Rotate X و Y |
| تعامل | محدودهٔ None/Canvas/Window، هاور Grab/Bubble/Repulse، کلیک Push/Remove/Bubble/Repulse، resize، فاصله و شفافیت Grab، فاصله/اندازه/مدت Bubble، فاصله/مدت Repulse، شمار افزودن/حذف |
| Retina | فعال/غیرفعال؛ نسبت پیکسل حداکثر ۲ |

اشکالات مرجع عمداً منتقل نشدند: cast اشتباه آرایهٔ Density به عدد، شرط Polygon، استخراج مقدار Slider به‌عنوان عدد و اتصال Sync شفافیت به سوییچ اشتباه. مقدارهای صفر معتبرند؛ حداقل شفافیت/اندازه از حداکثرشان بیشتر نمی‌شود. پیش‌فرض‌ها صریح‌اند، نه حاصل merge ضمنی و ناسازگار تنظیمات ویجت و theme.js هاب؛ پیش‌فرض ۴۰ ذره، اندازهٔ ۲۰، حرکت روشن، خطوط و تعامل خاموش است.

## مالکیت و کنترل‌ها

`inc/components/particles.php` مالک schema، validation و renderer است؛ `rdct_particles($options)` و `rdct_prepare()` همان قرارداد دارایی PHP را مصرف می‌کنند. Classic `rdct-particles` و Atomic `rdct-atomic-particles` از همان renderer و موتور استفاده می‌کنند. کارت مستقل در کتابخانه و پیش‌نمایش ایزوله وجود دارد. متن انسانی قابل‌ترجمه در خروجی نیست؛ گزینه‌ها فنی‌اند. ارجاع تصویر با resolver موجود WPML حل می‌شود، نه ترجمهٔ رشته‌ای URL.

Classic از Text، Number، Slider، Color بدون alpha، Switcher، Select/Select2 چندانتخابی، Media و Repeater بومی استفاده می‌کند؛ uploader یا CSS پنل جدید ندارد. پنج Slider شفافیت/حداقل اندازه/شفافیت خط و Grab با مسیر عددی PHP و Atomic از adapter مقدار `size` عبور می‌کنند. Atomic از ImageControl، Color و Repeater بومیِ adapter مشترک و کنترل‌های Text/Number/Switch/Select همان ویرایشگر استفاده می‌کند. SelectControl ارسالی 4.3.5 چندانتخابی ندارد؛ ترکیب شش SwitchControl بومی همهٔ ترکیب‌های شکل را پوشش می‌دهد، بدون picker جدید. فیلدهای شرطی از dependencies بومی استفاده می‌کنند. در Atomic کنترل Number بومی جای Slider قدیمی است؛ برابری تک‌تک زیرکنترل‌های ظاهری ادعا نمی‌شود. پیش‌نمایش Atomic endpoint مشترک با debounce/abort موجود را مصرف می‌کند؛ endpoint یا درخواست تکراری موازی ساخته نشده است.

## بارگذاری و چرخه

بازبینی ادامهٔ سورس ۲۰۲۶-۱۰-۲۵: کلیک بدون mousemove قبلی، از مختصات خود کلیک با لحاظ Retina استفاده می‌کند؛ کلیک برنامه‌ایِ بدون مختصات به مرکز canvas می‌رود. موقعیت عددی ذرات در این مسیر با موتور واقعی و canvas شبیه‌سازی‌شده بررسی شد. کلاس‌های position/isolation فقط در حالت پس‌زمینه یا نمایش وابسته به هاور اضافه می‌شوند؛ حالت معمولی والد را تغییر نمی‌دهد.

CSS فقط با مصرف کامپوننت enqueue می‌شود؛ runtime و موتور دو import انتخابی‌اند. موتور حتی پس از یافتن ویجت، تا نزدیکی viewport و اجازهٔ reduced motion/صفحهٔ نمایان/هاور بارگیری نمی‌شود. نمونه‌ها از یک ماژول موتور importشده استفاده می‌کنند و canvas/lifecycle مستقل دارند. در tab مخفی، بیرون دید و reduced motion نمونه آزاد می‌شود؛ بازگشت شرایط نمونه را از نو ایجاد می‌کند، نه ادامهٔ موقعیت قبلی ذرات. هاور و focus-within هر دو نمایش را فعال می‌کنند؛ canvas تزئینی، aria-hidden و pointer-events:none است و لینک‌های محتوا را نمی‌پوشاند. تعامل از رویدادهای والد یا Window با مختصات محلی canvas خوانده می‌شود.

پس‌زمینه فقط لایهٔ canvas را در والد Elementor/والد علامت‌خورده/section قرار می‌دهد؛ محتوا clone یا جابه‌جا نمی‌شود. در PHP می‌توان ظرف را با `data-rdct-particles-host` مشخص کرد. کلاس موقعیت/isolation والد بین نمونه‌ها شمارش می‌شود و هنگام آخرین cleanup فقط کلاس‌های افزوده‌شده حذف می‌شوند. شناسهٔ ورودی پیشوند است و suffix یکتا از تداخل cloneها جلوگیری می‌کند.

حداکثر ۲۰۰ ذره در تعداد اولیه، density و Push و ۱۶ رنگ اعمال می‌شود؛ تعداد تلاش برای رفع هم‌پوشانی محدود است. RAF، timer، listener، XHR تصویر SVG و Blob URL در destroy پاک می‌شوند؛ globalهای particlesJS/pJSDom، تغییر Object، CDN و JSON loader عمومی وجود ندارند. تصویر فقط attachment با host همین سایت است؛ ورودی URL خارجی مصرف نمی‌شود. snapshot library پیش‌فرض تصویر SVG ندارد و CSP ایزولهٔ آن connect-src:none را حفظ می‌کند.

## منبع موتور و نگهداری

`src/js/vendor/particles.js` مشتق محلی source رسمی برچسب 2.0.0 در upstream است. مجوز MIT با نام Vincent Garreau در `licenses/particles.txt` خارج src نگهداری می‌شود تا در بستهٔ آتی نیز بماند. تغییرات محلی: export کارخانهٔ بدون global، deep merge محلی، listener/timer/RAF/XHR/Blob قابل‌پاک‌سازی، مختصات صحیح canvas، سقف ذره/Retina، برخورد محدود و اجرای انیمیشن opacity/size حتی با حرکت خاموش. به‌روزرسانی upstream باید این تغییرها و تست چندنمونه/cleanup را حفظ کند؛ فایل‌های themes مرجع تغییر نکردند.

## شواهد و پذیرش باز

Build و قرارداد کوتاه Node موتور واقعی با canvas شبیه‌سازی‌شده، lifecycle، تطبیق نام تمام ۶۰ فیلد هاب و graph دارایی بررسی شدند. در ادامه، PHP 8.4.27 موقت آماده شد و lint و قرارداد validation/Slider/رسانه/خروجی ذرات و schema/config واقعی Atomic نیز پاس شدند؛ WordPress در قرارداد PHP stub است. Classic هنگام Atomic فعال از جستجو نیز پنهان می‌ماند. این شواهد پنل واقعی، نمایش/شدت اثر، تمام ترکیب‌های شکل/تصویر، hover/focus، RTL/LTR، موبایل، ذخیره/Undo، WPML رسانه و Network نصب را تأیید نمی‌کنند. نسخه و ZIP ساخته نشد؛ [شواهد جاری PHP](../implementation/foundation.md) مالک جزئیات محیط و محدودیت‌هاست.

نمونهٔ PHP: `rdct_particles(array('color' => '#4263eb', 'number' => 40, 'height' => '450px'));`؛ برای head صحیح، این helper داخل callback آماده‌سازی پیش از `get_header()` اجرا شود.
