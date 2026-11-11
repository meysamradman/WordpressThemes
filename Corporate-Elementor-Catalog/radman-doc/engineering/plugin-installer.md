# نصب افزونه‌های قالب

## بررسی بسته و مرز نصب اولیه — ۲۰۲۶-۱۰-۲۹

بازبینی محدود درخواست مالک، دو فاصلهٔ واقعی نشان داد: source-selection اختصاصی فقط برای update ثبت می‌شد و نصب اولیهٔ بستهٔ همراه کنترل basename/version همان عملیات را نداشت؛ download اختصاصی نیز reply قبلیِ upgrader_pre_download را رعایت نمی‌کرد. رفتار با [قرارداد رسمی download_package](https://developer.wordpress.org/reference/classes/wp_upgrader/download_package/) و [install](https://developer.wordpress.org/reference/classes/plugin_upgrader/install/) از Context7 تطبیق داده شد. الگوی مجوز/nonce و basename ثابت WoodMart بررسی‌شده همچنان مرجع است؛ دانلود یا framework مرجع منتقل نشد.

- `inc/admin/plugins/packages.php` مالک allowlist بسته‌های Components/Content/MCP، metadata محدود و بررسی checksum شد. نام namespace و helperهای قبلی حفظ شدند؛ installer رابط/اطلاعات و لینک native را مالک است و updater چرخهٔ مشترک دریافت/اعتبارسنجی نصب و آپدیت را. سرور، uploader یا unzip اختصاصی نداریم.
- کارت و اطلاعات غیرعملیاتی فقط metadata می‌خوانند؛ hash کامل هنگام کلیک نصب و در مرحلهٔ دریافت واقعی انجام می‌شود، نه در نمایش عادی هر کارت. metadata به معنی تأیید checksum نیست.
- فقط tokenهای allowlist آپدیت یا مسیر دقیق ZIP همراه در عملیات native plugin/install شناخته می‌شوند. نصب `install_plugins` و آپدیت `update_plugins` می‌خواهد؛ مسیر/URL دلخواه و ZIP افزونه‌های ثالث تصاحب نمی‌شود. nonce و filesystem/rollback همچنان مالک native WordPress هستند.
- checksum بسته و کپی موقت پیش از استخراج بررسی می‌شوند. نصب اولیه نیز عملیات scoped به همان instance دارد؛ source-selection پیش از جایگزینی، پوشه، نام فایل اصلی و نسخهٔ manifest را بررسی می‌کند. operation پس از خطا/مصرف پاک می‌شود و عدم تطابق درخواست خطاست. reply غیر false فیلتر قبلی، از جمله WP_Error امنیتی، دست‌نخورده می‌ماند.

قرارداد کوتاه برای هر سه بسته، رد نصب بدون مجوز، تفاوت install/update capability، حفظ خطای قبلی، کپی موقت، ریشهٔ غلط نصب اولیه، checksum مخدوش و مسیرهای update قبلی را پاس کرد. قرارداد کارت‌ها و lint سه PHP هم پاس شدند. این شواهد قراردادی‌اند؛ در این مرحله افزونه‌ای روی نصب مشترک نصب/جایگزین نشد و Multisite/FTP/rollback واقعی یا اسکن جامع امنیت اجرا نشدند. نسخه و ZIP همراه بازتولید نشدند.

## تطبیق بسته با سورس — ۲۰۲۶-۱۰-۲۴

گزارش مالک نشان داد SHA-256 معتبر به‌تنهایی یکسان‌بودن بسته با سورس را ثابت نمی‌کند؛ دقیقاً چهار فایل Components در ورودی با ZIP همراه متفاوت بودند. `tools/verify-packages.ps1` اکنون از انتهای bundle-content و پیش از تولید ZIP نهایی قالب فراخوانی می‌شود. هر سه افزونه از نظر version header/manifest/constant موجود، hash ZIP، فهرست کامل فایل‌های runtime و محتوای تک‌تک فایل‌ها با سورس جاری تطبیق داده می‌شوند؛ فایل قدیمی، اضافه/تکراری، حذف‌شده یا اختلاف نسخه خطا می‌دهد و package نهایی ادامه پیدا نمی‌کند. قانون انتخاب runtime همان قرارداد bundle است؛ src و ابزار توسعه وارد بستهٔ نصب نمی‌شوند.

نسخهٔ Content به 0.1.8 با constant برابر header اصلاح شد. آزمون `tests/package-parity-contract.ps1` با fixture موقت، اختلاف سورس با checksum معتبر و اختلاف constant را رد کرد. این کنترل یک snapshot محلی است؛ امضای ناشر، یکسانی باینری ZIPهای تکرارشده یا تعلق به commit ذخیره‌نشده را ثابت نمی‌کند. commit/tag، CI و انتشار خودکار اضافه نشدند.

سیاست local-only updater حفظ شد؛ منع auto-update سه بستهٔ خود رادمان به معنی کنترل updater افزونه‌های ثالث نیست. ZIP مستقل patch با basename ثابت از مسیر نصب/جایگزینی native قابل اقدام مدیر است؛ مسیر توزیع/اعلان وصلهٔ مستقل از Theme هنوز تصمیم عملیاتی مالک می‌خواهد. hash محلی تطبیق فایل را بررسی می‌کند و امضای ناشر یا اعتبار تمام زنجیرهٔ توزیع محسوب نمی‌شود.

Components با basename ثابت `radmanct-components/radmanct-components.php` در registry، ZIP محلی، هش، کارت وضعیت و مسیر آپدیت بومی موجود است. طبق اصلاح مالک در ۲۰۲۶-۱۰-۲۷ برای قالب ضروری است؛ dependency اجباری روی Elementor یا Content ندارد. کارت ضروری و پیام مدیریت نبود افزونه اضافه شده‌اند؛ نصب/فعال‌سازی خودکار و fatal فرانت نداریم. نصب خارج از Radman CT نیز با بارگذاری ZIP استاندارد انجام می‌شود. برای عرضهٔ هر نسخه، main file و slug ثابت و version افزایشی حفظ شوند.

## قرارداد جاری فهرست و آپدیت — ۲۰۲۶-۱۰-۲۱

فهرست مهم و اختیاری شامل Radman CT Content، Radman CT MCP، پیش‌نیاز MCP Adapter و فقط Elementor رایگان است. با تصحیح صریح مالک، Elementor Pro و افزونه‌های پولی/فرعی در فهرست نمایش داده نمی‌شوند؛ این تغییر به معنی حذف سازگاری محصول با Pro یا WPML و Slider Revolution نیست. چایلدتم در فهرست افزونه‌ها قرار نمی‌گیرد؛ مالک نام و مسیر آن [قرارداد نام‌گذاری](naming.md) است.

هر کارت وضعیت نصب/فعال‌بودن، نسخهٔ نصب‌شده و نسخهٔ موجود در صورت شناختن آن را نشان می‌دهد. Content و MCP از registry مشترک، ZIP/manifest محلی همراه قالب و updater مشترک استفاده می‌کنند؛ نسخهٔ جدیدتر، اعلان native وردپرس و دکمهٔ آپدیت می‌گیرد، نسخهٔ برابر/جدیدتر downgrade نمی‌شود. شناسهٔ پایدار هر دو افزونه حفظ می‌شود، داده و تنظیمات پاک نمی‌شوند و مهاجرت از بسته‌های آزمایشی سابق اضافه نشده است. نصب/فعال‌سازی/آپدیت خودکار نیست.

برای افزونه‌های ثالث، کارت فقط نتیجهٔ transient بومی WordPress را می‌خواند و لینک آپدیت native می‌دهد؛ درخواست خارجی جدید، updater جایگزین یا نسخهٔ حدسی ندارد. نبود اعلان، اثبات آخرین‌بودن نسخه نیست. نصب اولیهٔ ثالث با Upload ZIP انجام می‌شود. حداقل WordPress در metadata هر بسته مستقل است: Content برابر 6.6 و MCP برابر 6.9. صفحات عمومی برای این فهرست asset یا بررسی checksum دریافت نمی‌کنند.

قرارداد کوتاه updater برای هر دو بسته، مجوز/nonce، حفظ transient ثالث، نسخه‌ها، checksum، header/root و رد downgrade پاس شد. این شواهد جایگزین تست آپدیت روی سایت مالک یا Multisite واقعی نیست. بخش‌های تاریخ‌دار پایین، شواهد مراحل قبلی‌اند؛ دامنهٔ جاری هر دو افزونه است.

نیاز و وضعیت محصول در [تنظیمات](../scenarios/settings.md) است. کارت‌ها PHP هستند؛ از Dashicons محلی وردپرس و CSS مستقل `src/css/admin/requirements.css` فقط در صفحهٔ خودشان استفاده می‌کنند. جاوااسکریپت، TGMPA و موتور نصب سفارشی اضافه نشده‌اند. مفهوم تفکیک افزونه/پیش‌نیاز و آیکون از بررسی محدود setup wizard وودمارت گرفته شده؛ سورس آن منتقل نشده است.

## عملیات و امنیت

`inc/admin/plugin-installer.php` لینک‌های استاندارد وردپرس را فقط برای موارد registry می‌سازد. نصب به `update.php?action=install-plugin` با nonce وابسته به slug می‌رود؛ فعال‌سازی به `plugins.php?action=activate` با nonce وابسته به فایل افزونه. نصب به `install_plugins` و فعال‌سازی به `activate_plugin` برای همان فایل نیاز دارد. مجوز نهایی، nonce، بررسی بسته، filesystem credentials و خطاها را خود وردپرس دوباره کنترل می‌کند. فعال‌سازی شبکه بدون کلیک صریح مسیر native انجام نمی‌شود؛ رابط کامل شبکه و هاست FTP در این مرحله تست نشده‌اند.

طبق اصلاح مالک در ۲۰۲۶-۱۰-۲۰، Elementor نیز مانند Pro، WPML، String Translation و Slider Revolution فقط به بارگذاری ZIP خود مدیر هدایت می‌شود؛ مسیر نصب مخزن از کارت حذف شد. مسیر دلخواه دانلود، URL واردشده یا بستهٔ تجاری مرجع دریافت/نصب نمی‌شود. افزونهٔ وابسته بدون پیش‌نیاز فعال از کارت فعال نمی‌شود. خواندن صفحه دانلودی ایجاد نمی‌کند. رفتار مستقل بررسی آپدیت/ترجمهٔ هسته و افزونه‌های ثالث، متعلق به خود وردپرس است.

## بستهٔ محلی Core

ZIP و manifest در `radmanct/bundled-plugins/radmanct-content.zip` و `radmanct-content.json` هستند. فقط درخواست native نصب `radmanct-content` در `update.php`، با مجوز و nonce معتبر، از فیلتر `plugins_api` اطلاعات همین مسیر ثابت محلی را می‌گیرد. نسخه از manifest محدودشده و SHA-256 خود ZIP بررسی می‌شود؛ نبود بسته یا عدم تطبیق به WP_Error منتهی می‌شود، نه جست‌وجو در مخزن خارجی. سقف manifest برابر ۲ KiB و ZIP برابر ۱۰ MiB است. checksum یکپارچگی فایل آماده را بررسی می‌کند؛ جای مجوز نصب یا اعتماد به کد را نمی‌گیرد.

پس از تغییر Core و Build فایل‌های آن، `powershell -NoProfile -File tools/bundle-content.ps1` بسته را تازه می‌کند. این ابزار فقط `radmanct-content.php`، `inc/`، `dist/` و `languages/` را با ریشهٔ `radmanct-content/` و مسیرهای ZIP استاندارد می‌گیرد؛ `src/` و ابزار توسعه وارد نصب نمی‌شوند. `tools/package.ps1` پیش از بسته‌بندی Theme این مرحله را انجام می‌دهد. Build عادی Vite به این مرحلهٔ PowerShell وابسته نشده است. Core همچنان افزونه‌ای جدا می‌ماند و وجود ZIP داخل Theme، کد آن را اجرا نمی‌کند.

## CONFIRMED — به‌روزرسانی Core همراه قالب

### هویت ثابت نصب و آپدیت قالب — تکمیل مالک، ۲۰۲۶-۱۰-۲۰

Core نصب‌شده با basename ثابت `radmanct-content/radmanct-content.php` به‌روزرسانی شود؛ تغییر نسخه نباید پوشهٔ `radmanct-content-<version>` یا نصب دوم بسازد. کارت فعلی برای افزونهٔ نصب‌شده مسیر نصب مجدد ندارد: نسخهٔ جدیدتر اقدام native upgrade، نسخهٔ برابر/جدیدتر بدون پیشنهاد downgrade و افزونهٔ غیرفعال امکان فعال‌سازی دارد. نصب نسخهٔ جدید قالب به‌تنهایی Core را بی‌صدا جایگزین نمی‌کند؛ مدیر اقدام آپدیت Core را انجام می‌دهد و داده/تنظیمات ماژول‌ها باقی می‌مانند.

قالب اصلی نیز همیشه پوشهٔ `radmanct/` دارد. ZIP نسخهٔ جدید از مسیر بومی بارگذاری قالب و تأیید جایگزینی نسخهٔ موجود نصب می‌شود؛ updater اینترنتی یا سرور اختصاصی برای قالب نداریم. شناسهٔ stylesheet، text domain و کلیدهای داده به خاطر نسخه تغییر نکنند. Child Theme با `Template: radmanct` مستقل است و هنگام آپدیت parent جایگزین نمی‌شود؛ تغییرات مشتری در parent قرار نگیرند. نام فایل ZIP می‌تواند نسخه داشته باشد، اما ریشهٔ داخلی بسته ثابت است. `tools/package.ps1` اکنون پسوند ZIP را از Version همان Theme/Core می‌گیرد؛ شمارهٔ ثابت `0.1.0` حذف شد. فقط افزایش واقعی Version داخل Core/manifest باعث پیشنهاد آپدیت Core می‌شود، نه صرف تغییر نسخهٔ Theme.

این قرارداد برای نصب‌های فعلی با نام نهایی است؛ انتقال تاریخی `radman-core` به `radmanct-content` طبق [سازگاری نام](naming.md) مهاجرت جداست و با آپدیت معمول اشتباه نشود. مسیر بومی جایگزینی ZIP در [Plugin_Upgrader](https://developer.wordpress.org/reference/classes/plugin_upgrader/install/) و [Theme_Upgrader](https://developer.wordpress.org/reference/classes/theme_upgrader/install/) با Context7 تطبیق داده شد؛ الگوی basename ثابت و مقایسهٔ نسخهٔ WoodMart دوباره بررسی شد. اجرای واقعی upgrade روی سایت مالک هنوز اثبات نشده است.

درخواست ۲۰۲۶-۱۰-۱۹: برای افزونه‌های متعلق به رادمان، فعلاً Radman CT Core، وقتی نسخهٔ جدید همراه قالب نصب شد، وردپرس باید نسخهٔ همراه را با نسخهٔ نصب‌شده مقایسه و نیاز به به‌روزرسانی را در مدیریت افزونه‌ها/به‌روزرسانی‌ها و کارت رادمان نمایش دهد. آپدیت Core از مسیر استاندارد WordPress با اقدام و مجوز مدیر انجام شود؛ نصب Theme نباید بی‌صدا افزونه را جایگزین، فعال یا ماژول‌های خاموش را روشن کند. نقش Core برای قابلیت‌های وابسته طبق معماری فعلی است؛ این نیاز، Core را برای سایت پایه اجباری نمی‌کند.

منبع، ZIP و manifest محلی همراه Theme است؛ سرور لایسنس، updater اینترنتی، CDN و درخواست خارجی برای تشخیص یا دریافت Core ایجاد نشود. نسخهٔ نصب‌شدهٔ برابر/جدیدتر نباید اعلان آپدیت یا downgrade بگیرد. نبود/خرابی بسته و عدم تطبیق version/checksum باید به وضعیت روشن و حفظ افزونهٔ فعلی منتهی شود. نسخهٔ حداقل سازگار و نسخهٔ پیشنهادی در صورت تعریف جدا باشند؛ به‌روزرسانی فقط از روی تغییر نسخهٔ Theme فرض نشود.

مقایسهٔ metadata سبک و cache/invalidation متناسب با تغییر بسته/نسخه باشد؛ hash کامل ZIP یا اسکن پوشه‌ها در هر درخواست frontend اجرا نشود. بررسی یکپارچگی پیش از عملیات نصب/آپدیت محفوظ بماند؛ nonce، capability `update_plugins`، قفل عملیات، filesystem credentials و خطاهای upgrader از مسیر native WordPress رعایت شوند. فایل افزونه/slug allowlist ثابت باشد و URL یا مسیر آزاد کاربر پذیرفته نشود. وضعیت Multisite/Network و حفظ داده/فعال‌بودن افزونه در قرارداد اجرایی بررسی شود؛ چیزی برای افزونه‌های ثالث مثل Elementor Pro/WPML به‌جای updater خودشان تحمیل نشود.

### پیاده‌سازی محلی — ۲۰۲۶-۱۰-۲۰

`inc/admin/plugin-updater.php` با فیلترهای خواندن/نوشتن `update_plugins` فقط مورد ثابت Core را از metadata محلی می‌سازد؛ نسخهٔ واقعی header نصب‌شده ملاک است، نه checked قدیمی. تغییر نسخه/بسته در خواندن بعدی و بدون انتظار انقضای کش دیده می‌شود. فقط manifest کوچک و stat فایل خوانده می‌شوند؛ hash کامل در این مسیر یا frontend محاسبه نمی‌شود. با تعویض Theme کش افزونه‌ها پاک می‌شود تا نشانگر محلیِ updater باقی نماند. ماژول فقط در مدیریت و cron بارگذاری می‌شود؛ auto-update خود Core صریحاً غیرفعال است و رفتار افزونه‌های ثالث تغییر نمی‌کند.

بستهٔ پیشنهادی شناسهٔ محلی ثابت دارد؛ `upgrader_pre_download` با capability و allowlist، checksum را بررسی و کپی موقتِ دوباره hashشده می‌سازد. ZIP همراه Theme به پاک‌سازی upgrader سپرده نمی‌شود. `upgrader_source_selection` از filesystem وردپرس برای تطبیق ریشهٔ `radmanct-content/` و Version داخل header با نسخهٔ همان عملیات، پیش از جایگزینی فایل‌ها استفاده می‌کند. خطای بسته/نسخه، عملیات را متوقف می‌کند. صفحهٔ جزئیات Core نیز پاسخ محلی `plugins_api` می‌گیرد و به مخزن بیرونی fallback ندارد. کارت Core برای نسخهٔ جدیدتر، فعال یا غیرفعال، اقدام native همراه nonce و URL شبکه در اختیار مدیر مجاز می‌گذارد؛ نسخهٔ برابر/جدیدتر پیشنهاد downgrade نمی‌گیرد. تنظیمات ماژول‌ها نوشته نمی‌شوند.

hookهای download/source با Context7 و سورس WordPress موجود در image محلی تطبیق داده شدند؛ الگوی مقایسهٔ نسخه و ثبت response از `themes/woodmart/inc/classes/class-autoupdates.php::register_core_plugin_update` بررسی شد. بخش API/لایسنس/دانلود اینترنتی مرجع منتقل نشد. قفل، nonce مسیر native، backup/rollback، filesystem credentials و حفظ وضعیت فعال‌سازی همچنان مالک native upgrader هستند؛ تست قرارداد جای اجرای واقعی آن‌ها را نمی‌گیرد.

قرارداد کوتاه `tests/plugin-updater-contract.php` نسخه‌ها، دادهٔ transient قبلی، مجوز، لینک شبکه، کپی موقت، ZIP خراب، header ناسازگار، ریشهٔ نامعتبر و جدایی auto-update ثالث را بررسی می‌کند. `tests/requirements-contract.php` نیز نصب محلی Core و ZIP-only بودن Elementor را پوشش می‌دهد. به‌روزرسانی واقعی نصب‌شده، Multisite، FTP و مرورگر برای این تغییر اجرا نشده‌اند. طبق اصلاح ۲۰۲۶-۱۰-۲۹، checksum در مرحلهٔ اقدام بررسی می‌شود؛ کارت و پیشنهاد سبک مدیریت فقط metadata می‌خوانند و سلامت کامل ZIP را پیشاپیش اثبات نمی‌کنند.

## شواهد نصب فعلی

APIهای [Plugin_Upgrader::install](https://developer.wordpress.org/reference/classes/plugin_upgrader/install/) و [Plugin_Installer_Skin](https://developer.wordpress.org/reference/classes/plugin_installer_skin/) با Context7 و سورس native وردپرس بررسی شدند.

`tools/qa-installer.ps1` در محیط WordPress/PHP 8.4 و MariaDB موقت، بدون دسترسی بیرونی، پاس شد: `plugins_api` بومی بستهٔ محلی را گرفت، `Plugin_Upgrader` آن را نصب کرد و `activate_plugin` آن را فعال کرد. ZIP اصلی باقی ماند و ماژول‌های محصول/نمونه‌کار خاموش ماندند. nonce آرایه‌ای، کاربر بدون مجوز و عدم دخالت در slug نامرتبط نیز بررسی شدند. نصب واقعی Elementor از اینترنت یا بسته‌های تجاری اجرا نشد. قرارداد `tests/requirements-contract.php` مسیر نصب/فعال‌سازی/ZIP، مجوز دکمه‌ها، پیش‌نیاز و رد ZIP تغییریافته را پوشش می‌دهد. ظاهر کارت‌ها، شبکه و شرایط هاست مشتری هنوز بازبینی واقعی لازم دارند.
