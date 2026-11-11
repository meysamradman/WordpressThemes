# آیکون‌های SVG محلی

افزودن ۲۰۲۶-۱۱-۰۱: چهارخانهٔ تصویر با `layout-grid` موجود Lucide منطبق است. منوی سه‌خطی کوتاه/بلند/کوتاه هم‌تراز انتها، با menu/text-align-end فایل‌های Lucide بررسی‌شده یکسان نبود؛ `custom-menu-offset` با نسبت خطوط تصویر و metadata جهت ساخته شد و به نام Lucide معرفی نمی‌شود. `sun` و `moon` از همان نسخهٔ pinned Lucide و tarball با SHA-512 معتبر به منبع محلی اضافه شدند؛ کاتالوگ منتخب اکنون ۹۳ تعریف دارد. SVGها همچنان فقط هنگام مصرف رندر می‌شوند؛ UI رنگ از موتور آیکون مشترک استفاده می‌کند.

## اصلاح انتخابگر — ۲۰۲۶-۱۰-۲۵

callback مشترک loader بومی در درخواست‌های موازی می‌تواند نتیجهٔ Font Awesome را جای تب رادمان بنویسد. آداپتور پیش از بازکردن picker کتابخانه‌ها را ترتیبی آماده می‌کند؛ انتظار کل حداکثر ده ثانیه و قابل تلاش مجدد است. کنترل حذف‌شده پنجرهٔ دیررس باز نمی‌کند. بازیابی metadata با fetchJson به admin-ajax همان سایت، nonce، مجوز edit_posts و GET محدود است؛ فقط نام‌های معتبر برمی‌گردد. کد vendor تغییر نکرده است. قراردادهای loader و endpoint پاس شدند؛ تأیید میزبان باقی است.

وضعیت ۲۰۲۶-۱۰-۲۴: مجموعهٔ پایه در Theme مستقل از Components کار می‌کند؛ Components همان منبع مشترک را برای نصب مستقل و ویجت‌های اختیاری مصرف می‌کند. پنجرهٔ تصویری سازنده و تب بومی Elementor اضافه شدند؛ Font Awesome/Elementor مستقل باقی‌اند.

## ساختار

### اصلاح مالکیت و مجموعهٔ پایه — CONFIRMED، ۲۰۲۶-۱۰-۲۳

آیکون‌های ضروری قالب و هدر/فوتر بدون Components قابل استفاده باشند. مجموعهٔ پایه شامل فلش‌ها، کنترل منو/موبایل، تماس، رسانه و لینک‌های اجتماعی است؛ با اصلاح مالک در ۲۰۲۶-۱۱-۰۱، ضخامت پیش‌فرض آیکون خطی ۱ است؛ پیش‌فرض ۱٫۵ قبلی جایگزین شد. پیش‌فرض PHP، Classic/Atomic، انتخابگر و نمونه‌های جدید کتابخانه یکسان است؛ مقدار صریح ذخیره‌شده همچنان از کنترل خودش خوانده می‌شود. نسخهٔ خطی برندهای Tabler نیز همین ضخامت را مصرف می‌کند؛ SVG توپر مشتری fill خود را حفظ می‌کند و به outline تبدیل نمی‌شود. منبع عمومی Lucide محلی است؛ برندها از Tabler با provenance مستقل می‌آیند. دانلود فقط ابزار توسعه است، نه نصب یا runtime مشتری.

### پک عمومی و پک‌های اختیاری مشتری — CONFIRMED، ۲۰۲۶-۱۱-۰۱

شناسهٔ پک پایه `general` و برندها `social` است؛ این metadata گروه‌بندی است، نه تغییر نام آیکون‌ها یا ارسال دو کتابخانهٔ کامل به مرورگر. شناسه‌های فعلی مثل `phone` و `brand-instagram` برای سازگاری حفظ شدند. ساخت کامل پک‌های حوزه‌ای در این مرحله درخواست نشده است؛ موتور و ابزار افزودن آن‌ها آماده باشد.

پک حوزه‌ای فقط در پروژهٔ مصرف‌کننده فعال شود: شناسه‌هایی مانند `real-estate` و `retail`، و برای پک مخصوص یک مشتری `client-<slug>`. نام آیکون جدید باید با `<pack>-` شروع شود؛ مثل `real-estate-building` یا `retail-basket`. پک‌ها حق بازنویسی آیکون پایه/پک دیگر ندارند. پنج آیکون فعلی خدمات Child به پک `client` منتقل شدند؛ این نام و شناسه‌های `client-code` و مشابه آن استثنای سازگاری قدیمی‌اند و تغییر نمی‌کنند.

مسیر پیشنهادی هر پک در Child: `images/icons/<pack>/` برای SVGهای محلی و LICENSE/provenance، `inc/icons/packs/<pack>.php` برای کاتالوگ تولیدشده؛ manifest توسعه بیرون runtime در `tools/icon-packs/<pack>.json` باشد. پوشه یا فایل خالی صرفاً برای حوزه‌های احتمالی ساخته نشود. نام عمومی `shop` در این قرارداد هیچ ماژول فروشگاه/تجارت جدیدی فعال نمی‌کند.

```text
python tools/build-icons.py --source radmanct-child/images/icons/real-estate --manifest tools/icon-packs/real-estate.json --output radmanct-child/inc/icons/packs/real-estate.php --pack real-estate
```

ابزار با `--pack` پیشوند را بررسی می‌کند؛ آیکون خطی جدید باید viewBox استاندارد ۲۴×۲۴ داشته باشد. stroke-width مستقلِ node از خروجی catalog حذف می‌شود تا ضخامت مشترک روی همهٔ خطوط اعمال شود؛ فایل خام upstream و provenance دست‌کاری نمی‌شوند. SVGهای توپر و stroke=none ماهیت خود را حفظ می‌کنند. ابزار بدون `--pack` همچنان قرارداد قبلی viewBox غیر۲۴ را برای دادهٔ قدیمی پشتیبانی می‌کند. script/event/URL خارجی/مسیر بیرون source رد می‌شود و در شکست اعتبارسنجی، آخرین catalog معتبر حفظ می‌شود.

فقط پک مصرفی را در Child فعال کنید؛ مسیر و callback باید ثابت و متعلق به کد پروژه باشند، نه تنظیمات کاربر یا ورودی HTTP:

```php
add_filter( 'radmanct/icons/packs', static function ( array $packs ): array {
    $packs['real-estate'] = static function (): array {
        return require __DIR__ . '/packs/real-estate.php';
    };
    return $packs;
} );
```

callback کاتالوگ هر پک فقط در اولین مصرف catalog در همان درخواست اجرا می‌شود؛ ثبت فیلتر به‌تنهایی فایل پک را نمی‌خواند. پک ثبت‌نشده یا loader غیرفعال خوانده نمی‌شود؛ جستجوی پوشه/اسکن SVG runtime نداریم. Theme و Components از همان موتور استفاده می‌کنند، بنابراین در حالت نصب هم‌زمان نیز بارگذاری پک تکرار نمی‌شود. metadata و نام‌ها در picker مدیریت/Elementor قابل استفاده‌اند؛ در frontend فقط SVG نام انتخاب‌شده و CSS کوچک مشترک چاپ می‌شود. filter قدیمی `radmanct/icons/catalog` برای سازگاری باقی است؛ توسعهٔ جدید از packs استفاده کند. این قابلیت نیازی به افزونه، فونت آیکون، JS آیکون یا اتصال اینترنتی جدید ندارد.

موتور و دادهٔ پایه یک سورس مشترک دارند و برای نصب مستقل در Theme و Components بسته‌بندی می‌شوند؛ ویجت آیکون مصرف‌کنندهٔ موتور است. کتابخانهٔ طراحی متعلق به Components و مجموعهٔ مشتری متعلق به Child همان پروژه است؛ ثبت مجموعهٔ مشتری برای PHP، سازنده و ویجت‌های رادمان مشترک است. **اصلاح مالک، CONFIRMED، ۲۰۲۶-۱۰-۲۴:** مجموعهٔ رادمان و افزوده‌های پروژه در پنجرهٔ بومی آیکون Elementor نیز نمایش داده شوند؛ محدودیت قبلیِ نداشتن این ادغام جایگزین شد. افزونهٔ مستقل جدید برای آیکون ساخته نشود.

۹۳ SVG منتخب شامل ۸۱ آیکون عمومی `lucide-static@1.59.0`، ۱۱ برند خطی `@tabler/icons@3.49.0` و یک منوی محلی مطابق تصویر مالک هستند. فلش و chevron، منو/بستن، موبایل/تبلت/نمایشگر، تلفن/ایمیل/نشانی/گفتگو، زبان، سند/تصویر، فیلتر و کنترل رسانه در پایه‌اند. برندها: Instagram، Telegram، WhatsApp، LinkedIn، Facebook، X، YouTube، GitHub، Pinterest، TikTok و Discord؛ لوگوی برندها در RTL mirror نمی‌شود.

منبع canonical فایل‌ها `shared/icons/images/`، موتور `shared/icons/engine.php` و CSS کوچک `shared/icons/icons.css` است. `tools/icons.json` انتخاب‌ها، نسخه‌ها و metadata جهت را دارد. `python tools/fetch-icons.py` فقط در توسعه فایل‌های منتخب و LICENSE را از tarball رسمی npm با کنترل SHA-512 دریافت می‌کند؛ `sources.json` نشانی، integrity و SHA-256 فایل‌ها را ثبت می‌کند. این فرمان دستی است و در build معمول یا نصب اجرا نمی‌شود. `python tools/build-icons.py` SVGها را اعتبارسنجی و catalog/engine و تصاویر منتخب/مجوز را برای هر دو بسته تولید می‌کند. نسخه‌های داخل `images/icons/` و `inc/icons/` هر بسته خروجی مستقل نصب‌اند؛ دستی ویرایش نشوند. JS، فونت آیکون یا کل مجموعه وارد محصول نشده است. runtime فقط catalog ثابت را می‌خواند و پوشه‌ای را اسکن نمی‌کند.

adapterهای `radmanct/inc/icons.php` و `radmanct-components/inc/icons.php` خروجی inline SVG را با موتور مشترک و فقط از نام مجاز می‌سازند. SVG آزاد، URL یا مسیر دلخواه render نمی‌شود. CSS کوچک با handle مشترک `rdct-icons` فقط هنگام مصرف enqueue می‌شود و به CSS عمومی Components وابسته نیست. آیکون‌های جهت‌دار علامت‌خورده در RTL mirror می‌شوند؛ تلفن/جستجو و آیکون اختصاصی پیش‌فرض mirror ندارند. گزینهٔ `mirror=false` نیز وجود دارد. label خالی، آیکون تزئینی با `aria-hidden` می‌دهد؛ label متنی، `role=img` و برچسب escape‌شده دارد. دکمهٔ صرفاً آیکونی همچنان به نام قابل فهم خود دکمه نیاز دارد؛ آیکون رفتار دکمه ایجاد نمی‌کند.

```php
rdct_use( 'icons' );
get_header();
rdct_icon( 'phone', array( 'size' => 20, 'label' => 'Phone' ) );
rdct_icon( 'arrow-right', array( 'size' => 24, 'mirror' => true ) );
```

ویجت «آیکون» در گروه رادمان Elementor همان فهرست و renderer را مصرف می‌کند؛ اندازه، ضخامت، label، mirror و رنگ دارد و خودش script dependency ندارد. فهرست SVG محلی در عناصر آیکون، تماس و لینک‌های اجتماعی هدر/فوترساز نیز قابل انتخاب است؛ افزودن آیکون به کاتالوگ، آن را بدون فهرست‌نویسی دوباره در سازنده در دسترس می‌گذارد. رفتار سازنده در [قرارداد خودش](builder.md) ثبت شده است.

قالب بدون Components مجموعهٔ پایه را در PHP، سازنده و کتابخانهٔ بومی Elementor عرضه می‌کند؛ هنگام حضور Components، کاتالوگ مشترک همان پایه و افزوده‌های پروژه مصرف می‌شود. ادغام بومی محدود به کنترل‌های مصرف‌کنندهٔ Icons Manager است، نه هر انتخابگر اختصاصی افزونه‌های ثالث. ویجت‌های رشته‌ای رادمان اکنون با adapter بومی همان Icons Manager را باز می‌کنند؛ مقدار ذخیره‌شده مهاجرت نمی‌کند. آیکون اختصاصی واقعی مشتری هنوز اضافه نشده است.

## انتخابگر مدیریت و ادغام بومی — Theme 0.1.48 / Components 0.1.31

**بازبینی الزام‌آور طبق سند مالک؛ adapter در ادامهٔ سورس ۲۰۲۶-۱۰-۲۴ پیاده شد:** کنترل Classic از `Control_Icons` و view خود Icons ارث می‌برد؛ markup، حالت inline، انتخاب و پاک‌کردن بومی‌اند. adapter فقط نام رشته‌ای قبلی را به `library=rdct-local/value` تبدیل و انتخاب معتبر را به نام برمی‌گرداند؛ مسیر setValue خود BaseData و options آن حفظ است. Atomic از Button/Typography بومی و قرارداد control-view همان `open-icon-library.ts` ارسالی برای بازکردن Icons Manager استفاده می‌کند؛ useBoundProp بومی نویسندهٔ مقدار است و callback پس از unmount/disabled یا تغییر path چیزی نمی‌نویسد. پنجرهٔ دوم رادمان در Elementor حذف شد. سازندهٔ مستقل Theme به Elementor وابسته نشده است.

نوع control و schema رشته‌ای قدیمی برای سازگاری باقی‌اند؛ صرفاً در ادامهٔ همین adapter، مقدار native Icon object در storage یا Dynamic با خروجی object پذیرفته نمی‌شود و Dynamic صریحاً غیرفعال است. ویجت‌های SVG مستقل فقط مجموعهٔ محلی ثبت‌شده را مصرف می‌کنند؛ تب Recommended فهرست رادمان/پروژه را دارد، انتخاب فونت یا SVG خارج از آن با پیام روشن پذیرفته نمی‌شود و مقدار قبلی حفظ می‌شود. Upload SVG در Classic این field نمایش داده نمی‌شود؛ افزودن SVG مشتری از registry امن مشترک است. این adapter وعدهٔ تمام کتابخانه‌ها/Global یا Dynamic اختصاصی Pro نیست؛ چنین توسعه‌ای نیازمند قرارداد مدل/renderer است. پذیرش واقعی انتخاب/ذخیره/بازگشایی/Undo همچنان OPEN است.

هدر/فوترساز برای آیکون، تماس و لینک اجتماعی به‌جای dropdown از `shared/admin/icon-picker.js` استفاده می‌کند: تصویر، نام ترجمه‌شده و شناسهٔ ثابت، جستجوی چندکلمه‌ای محلی، بدون آیکون، Cancel/Escape و بازگشت فوکوس. نام ذخیره‌شدهٔ ناموجود تا انتخاب جدید حفظ می‌شود. داده در `iconChoices` فقط روی صفحهٔ مجاز سازنده inline است؛ فهرست فقط هنگام بازبودن پنجره رندر می‌شود و برای مجموعهٔ بزرگ، نمایش مرحله‌ای ۱۶۰تایی بدون شبکه دارد. این Modal اکنون مخصوص سازندهٔ مستقل است؛ editor Components دیگر آن یا CSS آن را وارد نمی‌کند و تنظیماتش فقط نام‌ها/labelها دارد، نه نسخهٔ دوم data URI تمام تصاویر.

مرجع محدود Hub: `elementor/hooks/icon-manager.php` از `elementor/icons_manager/additional_tabs` و فونت Liquid Essentials استفاده می‌کند. رادمان همان hook را با تب `rdct-local`، فهرست نام‌های کاتالوگ و `render_callback` به SVG مشترک وصل می‌کند؛ فونت Hub، sprite کامل یا framework منتقل نشده است. `shared/icons/elementor.php` در هر دو بسته تولید و ثبت hookها یک بار guard می‌شود؛ Theme و Components مستقل کار می‌کنند.

نمونهٔ تصویری کتابخانه و preview بومی، maskهای data URI را فقط در `elementor/editor/after_enqueue_styles` و `elementor/preview/enqueue_styles` می‌گیرند؛ `url=false` فونت آیکون جدا نمی‌سازد. `fetchJson` فقط مسیر بازیابی metadata از همان سایت است؛ دادهٔ آماده در loader بومی استفادهٔ مجدد می‌شود. درخواست کتابخانه‌های اصلی Elementor تصاحب نشده است. سایت عمومی از callback فقط SVG مصرف‌شده و CSS کوچک آیکون می‌گیرد؛ CSS کل انتخابگر عمومی نیست. fill صریح SVG در برابر CSS عمومی آیکون Elementor حفظ می‌شود تا outline توپر نشود.

در Elementor ارسالی 4.3.5، IconControl بومی Atomic نیز همان iconManager را باز می‌کند، ولی transformer سرور آن به خانوادهٔ فونت متکی است. wrapper محدود `elementor-transformer.php` فقط `library=rdct-local` را به SVG محلی با اندازهٔ بومی تبدیل می‌کند؛ سایر کتابخانه‌ها به transformer ثبت‌شدهٔ قبلی واگذار می‌شوند. schema بومی تغییر نکرده و کنترل مجوزدار Pro دور زده نمی‌شود.

قرارداد کوتاه Theme/Components مستقل، ثبت یکتا، آیکون پروژه، CSS محلی مدیریت و SVG انتخابی با HTML API واقعی وردپرس پاس شده؛ state انتخاب/جستجو/Cancel/فوکوس با React شبیه‌سازی‌شده بررسی شد. ظاهر، کلیک/کیبورد و ذخیره/Undo واقعی Classic/Atomic هنوز پذیرش مرورگر می‌خواهند؛ ثبت تب یا قرارداد شبیه‌سازی‌شده جای این پذیرش نیست.

## قرارداد بارگذاری صفحهٔ عمومی — CONFIRMED، ۲۰۲۶-۱۰-۲۳

در صفحهٔ سفارشی PHP، هدر/فوتر و خروجی عمومی Elementor فقط SVG آیکون‌های واقعاً مصرف‌شده داخل HTML قرار بگیرد. نصب مجموعه یا نمایش یک آیکون مجوز ارسال تمام SVGها، JSON انتخابگر، sprite کامل، فونت مجموعه یا JavaScript کتابخانه به بازدیدکننده نیست. صفحهٔ فاقد مصرف، CSS آیکون نیز نگیرد؛ صفحهٔ دارای مصرف فقط CSS کوچک مشترک را یک بار دریافت کند. این قاعده شامل مجموعهٔ مشتری هم هست.

فهرست ثابت PHP در سمت سرور، هنگام نیاز یک بار خوانده و در همان درخواست نگه‌داری می‌شود؛ این خواندن با ارسال تمام مجموعه به مرورگر متفاوت است. renderer با نام انتخاب‌شده یک تعریف را برمی‌گرداند و درخواست شبکهٔ جدا برای هر SVG ایجاد نمی‌کند. تکرار آیکون، SVG همان مصرف را در HTML تکرار می‌کند؛ sprite گزینشی فقط در صورت نیاز و اندازه‌گیری بعدی بررسی شود، نه sprite تمام مجموعه به‌صورت پیش‌فرض.

انتخابگر مدیریت به فهرست/نمونهٔ مجموعه نیاز دارد؛ داده فقط در صفحهٔ مجاز سازنده، editor و preview Elementor قرار می‌گیرد. مجموعهٔ فعلی کوچک است و یک بار در همان سند inline می‌شود؛ با بازکردن پنجره، تایپ یا صفحه‌بندی client درخواست جدید ارسال نمی‌شود. دریافت lazy از سرور ساخته نشده و برای هر آیکون image URL جدا وجود ندارد. این داده در صفحهٔ عمومی enqueue یا inline نشود.

adapterهای PHP، سازنده و ویجت‌های رادمان نام انتخاب‌شده را به renderer می‌دهند؛ callback بومی و transformer Atomic نیز همان renderer را مصرف می‌کنند. بررسی HTML محدود باید فقط SVG مصرف‌شده و فاقد شکل آیکون مصرف‌نشده باشد؛ این شاهد جای بازبینی Network مرورگر یا افزونه‌های ثالث را نمی‌گیرد.

## افزودن SVG اختصاصی

مجموعهٔ مشتری در Child همان پروژه بماند؛ به مجموعهٔ پایه اضافه نشود. فایل‌ها در `images/icons/` و manifest توسعهٔ آن پروژه با ساختار زیر باشند؛ نام‌ها پیشوند پروژه داشته باشند و شناسه‌های پایه را بازنویسی نکنند:

```json
{"icons":{"client-factory":{"file":"factory.svg","label":"Factory","directional":false}}}
```

از ریشهٔ workspace اجرا کنید؛ manifest ابزار توسعه خارج از بستهٔ نصب نیز می‌تواند نگهداری شود:

```text
python tools/build-icons.py --source radmanct-child/images/icons --manifest path/to/client-icons.json --output radmanct-child/inc/icons/catalog.php
```

ثبت در `functions.php` همان Child با hook مشترک زیر است؛ labelهای مشتری در domain خود Child ترجمه شوند. catalog معتبر فقط یک بار خوانده می‌شود؛ این مسیر بدون Elementor و بدون Components نیز کار می‌کند:

```php
add_filter( 'radmanct/icons/catalog', static function ( array $icons ): array {
    static $project = null;
    if ( null === $project ) {
        $project = require __DIR__ . '/inc/icons/catalog.php';
        $project['client-factory']['label'] = __( 'Factory', 'radmanct-child' );
    }
    return $icons + $project;
} );
```

برای افزودن آیکون عمومی تأییدشده، source مشترک و `tools/icons.json` به‌روز و generator اجرا شود؛ کپی نصب در Components یا Theme محل توسعهٔ فایل مشتری نیست.

SVG باید viewBox معتبر داشته باشد؛ شکل‌های `path/circle/rect/line/polyline/polygon/ellipse` و مشخصات محدودشان پذیرفته می‌شوند. groupها را ابتدا به شکل‌های تخت export کنید. script، event attribute، style آزاد، href، تصویر embed، filter/mask و منبع خارجی پذیرفته نیستند. `currentColor` برای تبعیت از رنگ سایت مناسب است؛ رنگ ثابت معتبر نیز حفظ می‌شود و کنترل رنگ ویجت آن را بازنویسی نمی‌کند.

Python فقط ابزار اختیاری توسعه برای افزودن/تغییر SVG است؛ نصب قالب، نمایش سایت و Build معمول Vite به آن وابسته نیستند. شکست اعتبارسنجی پیش از نوشتن catalog اتفاق می‌افتد و نسخهٔ سالم قبلی را حفظ می‌کند. آپلودکنندهٔ عمومی SVG به مدیریت اضافه نشده است.

## شواهد

در `tests/native-icon-manager.test.mjs` متدهای واقعی `onPickerShow` و `handleRecommendedList` از JS نسخهٔ ارسالی با registry ناقص اجرا شدند: خطاهای `displayPrefix` و `icons` بازتولید و پس از اصلاح تعریف محلی رفع شدند؛ بازکردن دوباره پس از normalization نیز پاس شد. renderer React و چرخهٔ کامل editor در این تست اجرا نمی‌شوند. خطای React 130 در عکس کاربر مسئلهٔ مستقلِ element type نامعتبر/undefined است؛ علت دقیق و رفع آن از این تست نتیجه گرفته نمی‌شود.

اصلاح ادیتور ۲۰۲۶-۱۰-۲۵: قبل از بازکردن picker، adapter وجود تعریف `rdct-local` را در فهرست native بررسی می‌کند؛ اگر دادهٔ اولیهٔ editor آن را نداشته باشد، همان مجموعهٔ محلی با prefix و displayPrefix درست اضافه می‌شود. tab موجود و مجموعه‌های دیگر بازنویسی نمی‌شوند؛ این مانع ارسال recommended/selected بدون تعریف مجموعه است که مسیرهای `icons` و `displayPrefix` نسخهٔ ارسالی بدون guard مصرف می‌کنند. مقدار ذخیره‌شده و قرارداد scalar قبلی حفظ می‌شوند. تست محدود نبودن tab، بازکردن دوباره پس از normalization، حفظ tab ثالث و انتخاب/پاک‌کردن پاس شد؛ رفع خطای میزبان هنوز نیازمند نصب سورس تازه و بررسی همان editor است. URLهای `data:image/svg+xml` خود دادهٔ SVG در صفحه‌اند و درخواست خارجی نیستند؛ maskهای کامل انتخابگر فقط برای editor هستند و به frontend منتقل نمی‌شوند.

اصلاح ۲۰۲۶-۱۰-۲۳ — Theme 0.1.44 / Components 0.1.27: build و ترجمه، ۲۱ بررسی کوتاه JS/دارایی/Atomic-preview/builder-elements، lint سیزده فایل PHP با PHP 8.4.26، قرارداد آیکون در سه فرایند مستقل theme/plugin/both و قراردادهای قبلی components و components-plugin پاس شدند. قرارداد generator پروژه viewBox غیر۲۴ و رد script/event/منبع خارجی/خروج از مسیر و حفظ آخرین catalog معتبر را پوشش داد. ZIPهای محلی هر دو بسته از نظر CRC، موتور/catalog، مجوزها و provenance با workspace مطابق‌اند؛ Git save، انتشار یا نصب سرور انجام نشد.

بررسی عمومی `tests/atomic-components-contract.php` در assertion تعداد شش گروه کاروسل متوقف شد؛ با نسخهٔ HEAD adapter پیش از تغییر ضخامت نیز همان assertion شکست خورد و سورس کاروسل در این کار تغییر نکرد. این تست به‌عنوان موفق گزارش نمی‌شود و پذیرش کامل Atomic از بررسی‌های آیکون نتیجه گرفته نمی‌شود. مرورگر، چهار دستگاه واقعی، انتخاب/ذخیره/Undo Elementor، WPML و ظاهر مجموعهٔ مشتری هنوز بررسی نشده‌اند. درج خودکار در تمام pickerهای بومی Elementor ساخته نشده است.

آزمون محدود افزودن SVG، viewBox غیر۲۴، رد script و حفظ catalog قبلی پاس شد. قرارداد PHP label/escape، جهت، asset شرطی و چاپ یک‌بارهٔ CSS دیرهنگام را پوشش داد؛ رندر سند واقعی Elementor نیز پاس شد. ظاهر و لمس در مرورگر بررسی نشده‌اند.

منابع: [Lucide Static](https://lucide.dev/guide/static)، [مجوز Lucide](https://lucide.dev/license)، [SVG و مجوز Tabler](https://github.com/tabler/tabler-icons/tree/main/packages/icons)، [نبود برند در Lucide](https://github.com/lucide-icons/lucide/blob/main/BRAND_LOGOS_STATEMENT.md). مجوز کتابخانه‌ها همراه فایل‌هاست؛ هیچ فعال‌سازی یا اتصال runtime به آن‌ها نداریم. این مجوزها مالکیت علامت تجاری برندها را منتقل نمی‌کنند.
