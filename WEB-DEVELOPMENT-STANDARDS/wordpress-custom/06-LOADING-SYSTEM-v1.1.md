# استاندارد عمومی لودینگ، انتقال صفحه و انیمیشن قالب‌های اختصاصی وردپرس

> بازبینی بسته 1.3 — 2026-11-19؛ نام مسیر برای سازگاری ارجاع‌های موجود حفظ شده است. تاریخچه در CHANGELOG-v1.3.md.

**نسخه:** 1.1 | **بازبینی منابع:** 2026-11-18  
**مخاطب:** Codex و توسعه‌دهنده قالب‌های مشتریان | **Stack:** WordPress / PHP SSR / CSS / Vanilla JS / GSAP (و Vite در صورت استفاده).  
**دامنه:** تمامی قالب‌های مشتریان؛ مستقل از برند، ظاهر و نوع محتوا. مکمل استاندارد عمومی HTML/SEO/Responsive، نه جایگزین آن.

> اصل قطعی پروژه: یک HTML اصلی ریسپانسیو، رندر اولیه سمت سرور، اسکرول طبیعی موبایل و عدم وابستگی محتوای حیاتی به انیمیشن. زمان‌های زیر پیش‌فرض پیشنهادی تیم‌اند، نه الزام گوگل. هر افکت اختیاری است و باید با آزمون واقعی توجیه شود.

## 1. تفکیک مسئولیت‌ها (تصمیم اجرایی)

| قابلیت | وظیفه | پیش‌فرض |
|---|---|---|
| Page Preloader | انتظار آماده‌شدن یک منبع ضروری برای یک تجربه خاص | خاموش؛ برای صفحات معمولی SSR لازم نیست |
| Page Transition / Curtain | افکت تزئینی ورود/خروج بین صفحات | اختیاری، سبک، غیرضروری برای عملکرد |
| Entrance / Reveal | ورود بصری متن، تصویر و کارت | کوتاه، یک‌بار، مستقل از دریافت داده |
| Scroll Animation | انیمیشن وابسته به موقعیت اسکرول | محدود؛ ScrollTrigger در صورت نیاز |
| Smooth Scroll | نرم‌کردن حرکت اسکرول | اختیاری دسکتاپ؛ موبایل native |
| Skeleton | جای‌نگهدار ساختار داده‌ای که واقعاً هنوز نرسیده | محلی و مشروط |
| Inline Loading | انتظار عملیات واقعی (AJAX، فرم، بارگذاری بیشتر) | فقط در محدوده عملیات |
| Media Buffering | انتظار پخش/جست‌وجوی صوت و ویدئو | فقط در پلیر مربوطه |
| Resource Preload | اولویت دریافت فایل ضروری (`rel=preload`) | فقط منابع واقعاً حیاتی؛ هیچ ارتباطی با پرده ندارد |

**ممنوع:** یک Loader سراسری برای همه موارد؛ تایمر نمایشی اجباری؛ انتظار دانلود تمام تصاویر، فونت‌ها، مدیا یا ماژول‌های تزئینی؛ مخفی‌کردن کل `body` یا `main` تا تکمیل JS. هیچ عملیات محلی نباید پرده صفحه را باز/بسته کند.

## 2. کجا چه چیزی استفاده شود؟

| نوع صفحه / عملیات | رفتار مناسب |
|---|---|
| صفحه اصلی، خدمات، درباره، تماس، محصول، مقاله | محتوای واقعی با PHP از ابتدا؛ بدون Skeleton/Preloader سراسری؛ Entrance کوتاه اختیاری |
| ورود مستقیم، Reload | نمایش فوری HTML؛ پیش‌فرض بدون پرده اجباری؛ افکت غیرمسدودکننده اختیاری |
| کلیک داخلی بین صفحات | ناوبری معمولی MPA؛ پرده کوتاه فقط در قالب‌هایی که این هویت بصری را دارند |
| آرشیو بلاگ، محصول، نمونه‌کار، ویدئو، موسیقی | سری اول کارت‌ها با PHP؛ لینک صفحه‌بندی واقعی؛ بدون لودینگ تمام صفحه |
| فیلتر/جست‌وجوی AJAX آرشیو | حفظ نتایج قبلی + نشانگر محلی؛ یا Skeleton هم‌اندازه در جای نتایج، اگر ضروری است |
| Load more | وضعیت انتظار روی دکمه/انتهای لیست؛ افزودن کارت‌های جدید؛ عدم حذف نتایج قبلی |
| داده غیرهمزمان بدون HTML اولیه | Skeleton محلی، تا پاسخ واقعی؛ Empty/Error مستقل |
| ارسال فرم/سبد سفارش | وضعیت انتظار دکمه/فرم؛ جلوگیری از ارسال دوباره؛ نمایش نتیجه واقعی |
| صوت/ویدئو در صفحه جزئیات و آرشیو | پوستر/کاور؛ Buffering روی پلیر یا دکمه پخش همان آیتم |
| Hero سه‌بعدی، WebGL یا ویدئو | متن و CTA فوری؛ پوستر سبک؛ Loading فقط داخل ویژوال در صورت نیاز |
| Back/Forward / BFCache | بازیابی صفحه و اسکرول؛ بدون تکرار اجباری پرده یا انیمیشن ورود |

## 3. معماری ماژول‌ها و مالکیت وضعیت

نام‌ها نمونه‌اند؛ ابتدا ساختار موجود پروژه را بررسی و تطبیق بده، نه ساخت ماژول‌های تکراری.

```text
src/js/
  runtime/bootstrap.js       # init یک‌بار و چرخه حیات
  runtime/page-transition.js # فقط پرده و ناوبری کامل
  runtime/motion.js          # GSAP context, entrance, reveal
  runtime/scroll.js          # ScrollSmoother/ScrollTrigger، refresh
  ui/loading-state.js       # وضعیت محلی loading/skeleton/error
  pages/archive.js          # fetch، فیلتر، pagination، history
  ui/carousel.js            # کاروسل و controls
  ui/media-player.js        # وضعیت واقعی audio/video
src/css/ui/
  transition.css
  loading.css
  motion.css
```

- هر ماژول `init(root)` و `destroy()` یا disposer معادل داشته باشد؛ راه‌اندازی مجدد idempotent باشد. Listenerها، observerها، timerها، RAFها و timelineهای متعلق به همان محدوده پاک شوند.
- تنها یک مالک برای پرده، یک مالک برای scroll lock و یک ScrollSmoother سراسری. Modal/menu/transition در صورت نیاز از lock مشترک توکنی استفاده کنند؛ آزادکردن یک lock نباید lock دیگری را آزاد کند. اسکرول و استایل قبلی بازیابی شوند.
- وضعیت صفحه: `idle → leaving → navigating` در صفحه مبدأ و `entering → ready` در مقصد؛ حالت عادی بدون پرده مستقیم `ready`. مسیر خطا/عدم موفقیت/restore همیشه به وضعیت قابل استفاده بازگردد.
- وضعیت عملیات محلی: `idle → pending → success | empty | error | aborted → idle`؛ مستقل از وضعیت صفحه و GSAP.
- قرارداد مشترک پیشنهادی: `theme:page-ready` (DOM و ساختار پایه آماده)، `theme:transition-end` (پرده کنار رفته یا اصلاً فعال نیست)، `theme:content-updated` (پس از commit واقعی DOM). این نام‌ها استاندارد کتابخانه نیستند. فقط یک قرارداد فعال در هر قالب؛ رویدادهای legacy را پس از بررسی مصرف‌کنندگان حذف یا migrate کن.
- رویداد به تنهایی کافی نیست: وضعیت/Promise یک‌بار resolve شونده expose شود تا Dynamic Import یا listener دیرهنگام رویداد ready را از دست ندهد. رویداد ready در حالت بدون انیمیشن نیز کامل شود. هر handler خطا باید fail-open باشد.

## 4. ترتیب اجرا بدون تداخل GSAP و پرده

1. PHP محتوای اصلی، H1، لینک‌ها، اندازه کارت/تصویر و مسیرهای واقعی را رندر می‌کند. CSS پایه محتوا را قابل مشاهده نگه می‌دارد. Overlay به‌صورت پیش‌فرض غیرفعال/غیرقابل‌کلیک باشد.
2. Bootstrap با `document.readyState`/`DOMContentLoaded` (یا اجرای module/defer) فقط یک‌بار؛ منتظر `window.load` نماند. پلاگین‌های مورد نیاز یک‌بار register شوند. منو، فرم و قابلیت‌های ضروری مستقل از GSAP فعال شوند.
3. وضعیت reduced-motion، pointer و breakpoint را بررسی کن. در صورت نیاز ScrollSmoother را **قبل از ساخت ScrollTriggerهای وابسته** بساز. در مدت پرده فعال، اگر لازم بود موقتاً pause/lock شود؛ در مسیر skip نیز صحیح آزاد شود.
4. ScrollTriggerهای layout/pin را در ترتیب طبیعی سند بساز تا هندسه پایدار شود؛ انیمیشن‌های entrance مستقل را تا `transition-end` آماده نگه دار. محتوای حیاتی (به‌خصوص H1/LCP/CTA) نباید تا این رویداد نامرئی یا معطل بماند. افکت تزئینی هیرو می‌تواند پس از آن اجرا شود.
5. پرده به محض آماده بودن مسیر ورود، با زمان کوتاه باز شود؛ پایان واقعی CSS animation/GSAP timeline + fallback ایمنی تعیین‌کننده است، نه دو تایمر متناقض. اگر پرده‌ای وجود ندارد، transition-end فوراً تکمیل شود.
6. قفل مربوط به پرده آزاد؛ Entranceهای مجاز یک بار اجرا شوند. Revealهای نواحی پایین صفحه به اسکرول طبیعی پاسخ دهند؛ هنگام refresh یا بازگشت از کش، همه را دوباره از opacity صفر شروع نکن.
7. بعد از تغییر واقعی ابعاد (AJAX commit، فونت، تصویر بدون اندازه از پیش رزروشده، accordion، چرخش دستگاه)، refresh را تجمیع کن. فونت و تصویر مانع نمایش اولیه نیستند؛ آماده‌شدن آن‌ها در صورت تغییر هندسه refresh تکمیلی می‌خواهد.

```js
// نمونه helper؛ در ماژول scroll، بعد از registerPlugin.
let refreshFrame = 0;
export function requestScrollRefresh(ScrollTrigger) {
  if (!ScrollTrigger || refreshFrame) return;
  refreshFrame = requestAnimationFrame(() => {
    refreshFrame = 0;
    ScrollTrigger.refresh(true);
  });
}
// در destroy، RAF معلق را cancel و refreshFrame را صفر کن.
```

`refresh()` موقعیت‌ها را دوباره محاسبه می‌کند؛ `update()` صرفاً پیشرفت را با موقعیت فعلی اسکرول هماهنگ می‌کند. هر فریم scroll/resize یا هر کارت جداگانه refresh نکن. روی کانتینر ثابت از ResizeObserver تنها با شرط تغییر واقعی اندازه و تجمیع استفاده کن؛ از حلقه refresh ناشی از pin spacer جلوگیری کن.

## 5. پرده، Preloader و انتقال کامل بین صفحات (MPA)

- پیش‌فرض: **بدون Preloader اجباری**. پرده فقط انتخاب طراحی است، مستقل از سرعت دریافت فایل‌ها. روی موبایل کوتاه‌تر یا غیرفعال؛ reduced-motion بدون پرده. اگر JS/GSAP خطا کرد، سایت عادی کار کند.
- Overlay تزئینی با `aria-hidden="true"`، خارج از `smooth-wrapper` و `smooth-content`، `position:fixed; inset:0`؛ بعد از پایان `hidden`/`pointer-events:none`. در CSS پایه نباید صفحه را بپوشاند. پرده صرفاً با `transform`/`opacity` تا حد ممکن؛ DOM کم و بدون صدها عنصر غیرضروری.
- برای پردهٔ خطی، یک عنصر و keyframe مبتنی بر `transform` کافی است. منحنی، مدت حرکت و مکث را در همان CSS نگه دارید. مکث اختیاریِ مقصد با `animation-delay` و `animation-fill-mode: both` فریم بسته را از ابتدا نگه می‌دارد. این مکث یک تصمیم بصری است و به آمادگی تصویر، فونت یا پلیر وابسته نیست. پایان واقعی انیمیشن و مهلت بازیابی مالک ناوبری‌اند. پروفایل مکث‌دار فقط با تأیید پروژه فعال شود و اثرش بر زمان نمایش محتوای اصلی جداگانه سنجیده شود.
- اگر خروج دستی با JS انتخاب شده، تنها کلیک اصلی، بدون modifier، روی لینک واقعی داخلی همان origin، مقصد HTML و قابل ناوبری در همان tab را intercept کن. `defaultPrevented`، hash همان صفحه، `download`، `target` متفاوت، لینک خارجی، mailto/tel، فرم، مسیر مدیریت، لینک‌های پخش، AJAX و کنترل‌های تعاملی را مستثنی کن. در صورت ابهام ناوبری native باشد. لینک URL واقعی برای سئو حفظ شود.
- پس از حداکثر زمان کوتاه انیمیشن خروج، `location.assign(href)` اجرا شود؛ چند کلیک ناخواسته فقط یک ناوبری بسازند. اگر GSAP/CSS callback نیامد، fallback ناوبری داشته باشد؛ اگر خود ناوبری انجام نشد، پرده و قفل را باز کن و اجازه تلاش مجدد بده. Timeout شبکه مرورگر را جعل یا با reload بی‌پایان جایگزین نکن.
- اگر نیاز به علامت انتقال در مقصد است، marker کوتاه‌عمر و یک‌بارمصرف در همان tab و وابسته به مقصد در نظر بگیر؛ در redirect/مقصد نامعتبر/هر خطای storage، نمایش عادی. marker نام‌دار و دارای expiry باشد و cache HTML کاربران را تغییر ندهد.
- `pageshow` با `event.persisted` برای BFCache: overlay را reset، timer معلق را لغو، lock خود را آزاد و وضعیت را بازیابی کن؛ دوباره init سراسری اجرا نکن و به زور `scrollTo(0,0)` نزن. برای cleanup مناسب `pagehide`؛ `unload` استفاده نشود. بازگشت تاریخچه AJAX از `popstate` جداست.
- گزینه جایگزین پیشرفته: CSS View Transition بین سندهای same-origin با feature detection/fallback؛ هم‌زمان با پرده سفارشی روی همان ناوبری اجرا نشود. WordPress برای این کار نیاز به SPA ندارد.
- Preloader واقعی فقط برای جزء ضروری که بدون آماده‌شدن قابل استفاده نیست (مثلاً تجربه تعاملی خاص)؛ وضعیت واقعی readiness/error/timeout، راه خروج و fallback داشته باشد؛ درصد ساختگی تولید نکن. برای انتظار طولانی روی بخش واقعی پیام وضعیت بده، نه پرده تزئینی طولانی.

## 6. قواعد GSAP، ScrollTrigger و ScrollSmoother

- `gsap.matchMedia()` برای طراحی و reduced-motion؛ `gsap.context()`/`revert()` برای محدوده ماژول و پاکسازی. در Dynamic Import تغییر breakpoint در زمان دانلود را دوباره بررسی کن. Animation/Triggerهایی که دیرتر ساخته می‌شوند صریحاً به context اضافه یا مستقل cleanup شوند. برای listenerهای native، cleanup جداگانه لازم است.
- فقط یکی از CSS/GSAP/Carousel مالک هر property روی هر عنصر باشد. اگر Reveal و Hover و Scrub هم‌زمان لازم‌اند wrapperهای تو در تو (مثلاً `motion-outer` و `motion-inner`) استفاده کن. `overwrite` جایگزین معماری نیست. `clearProps: 'all'` روی عنصر مشترک ممنوع؛ فقط استایل‌های متعلق به همان افکت برگردند.
- محتوای اصلی نباید به `opacity:0`, `visibility:hidden`, `clip-path` کامل یا `display:none` دائمی وابسته باشد. در صورت استفاده از حالت from برای تزئین یا کارت خارج نمای اولیه، JS فقط هنگام اجرای موفق افکت حالت اولیه را اعمال کند و در خطا/cleanup آن را بردارد. H1 و LCP را با opacity صفر یا پرده بلند معطل نکن.
- `transform` و `opacity` اولویت؛ از انیمیشن مکرر `width/height/top/left`، blur/filter/box-shadow سنگین، لایه‌های عظیم و `will-change` سراسری اجتناب. در صورت نیاز performance trace بگیر. loop/Raf تزئینی خارج viewport و در tab پنهان متوقف شود.
- ScrollTriggerهای جدید را فقط برای DOM جدید بساز؛ قبل از حذف آن DOM، context همان بخش را revert کن تا pin spacer و inline style باقی نماند. `ScrollTrigger.killAll()` روی AJAX محلی ممنوع؛ ممکن است هدر/بخش‌های دیگر را نابود کند.
- یک ScrollSmoother، قبل از triggerهای وابسته؛ عناصر `fixed` مانند هدر ثابت، پرده، modal و پلیر شناور خارج از wrapper/content تبدیل‌شده باشند. هدر غیرثابت می‌تواند داخل جریان باشد. محتوای pin شده را مستقیماً transform نکن؛ فرزند آن را animate کن.
- CSS `scroll-behavior:smooth` را هم‌زمان با ScrollToPlugin/موتور اسکرول روی یک scroll root فعال نکن؛ اسکرول و لینک hash با یک مالک، احترام به focus و scroll restoration. هیچ wheel/touch hijack اجباری.
- موبایل: HTML مشترک، Native Scroll، `smoothTouch` پیش‌فرض خاموش، Reveal کم و کوتاه، Pin/Parallax طولانی و افکت ماوس حذف یا ساده؛ کامپوننت‌های کارکردی و متن حفظ شوند. `prefers-reduced-motion: reduce` → پرش مستقیم به حالت نهایی، حذف scrub/pin تزئینی، autoplay و smooth غیرضروری.
- متن متحرک/تایپوگرافی: H1 و محتوای معنایی ثابت در HTML، لایه دکوری جدا یا با دسترسی مناسب؛ اعلان مداوم screen reader نداشته باشد. فضای طولانی‌ترین عبارت را رزرو کن؛ Ctrl/Cmd+F، انتخاب متن، RTL، بزرگنمایی و focus خراب نشود.

## 7. زمان‌بندی پیشنهادی (پروفایل قابل تنظیم)

| مورد | پیش‌فرض پیشنهادی | توضیح |
|---|---:|---|
| انتظار اجباری Preloader | 0 ms | ممنوع برای صفحه معمولی |
| پرده خروج (اختیاری) | 120–200 ms | شروع ناوبری را بی‌دلیل به تأخیر نینداز |
| پرده ورود (اختیاری) | 200–350 ms | موبایل 0–200 ms؛ بدون پوشاندن طولانی H1 |
| fallback پایان افکت | مدت timeline + 100–250 ms | خروج/ورود مستقل؛ ایمنی، نه minimum نمایش |
| watchdog ناوبری دستی | حدود 8 s | صفحه مبدأ اگر هنوز زنده است reset شود؛ تنظیم بر اساس پروژه |
| Reveal کارت/عنصر تزئینی | 300–600 ms | موبایل 200–400 ms |
| stagger | 40–70 ms | سقف کل گروه حدود 700–900 ms؛ نه انتظار برای همه سکشن |
| ظاهرشدن نشانگر AJAX | 150–250 ms | فقط اگر همان درخواست هنوز pending باشد |
| timeout AJAX معمولی | 10–15 s | عملیات طولانی سیاست جدا؛ پیام خطا/Retry |
| Loading مدیا | رویداد واقعی | تأخیر بصری کوتاه اختیاری؛ timeout و Retry مناسب شبکه |

**تایمرهای ایمنی، حد پایین مدت نمایش نیستند.** پاسخ سریع، loader را قبل از ظاهرشدن لغو کند؛ داده آماده را به خاطر تایمر نمایش معطل نکن. برای پردازش طولانی واقعی، پیشرفت اندازه‌گیری‌شده را نشان بده یا وضعیت نامعین با پیام روشن.

## 8. Skeleton / AJAX / آرشیوها

- اولین صفحه و مسیرهای pagination از PHP با HTML واقعی، عنوان‌ها، لینک‌ها، کاور و وضعیت empty رندر شوند. AJAX فقط enhancement است. فیلتر/صفحه‌بندی GET قابل استفاده بدون JS؛ صفحات ایندکس‌پذیر لینک `<a href>` واقعی، نه فقط کلیک JS. سیاست canonical/noindex فیلترها بر اساس پروژه.
- پیش از `fetch`، `aria-busy="true"` روی container نتایج؛ `role="status"`/`aria-live="polite"` جدا از container busy برای اعلان کوتاه. Skeleton تزئینی و غیرقابل focus؛ تعداد و ابعاد تقریبی کارت‌ها برابر خروجی؛ نسبت تصویر/حداقل ارتفاع رزرو شود تا CLS کم شود. shimmer در reduced-motion ثابت؛ نشانگر سریع بهتر از skeleton برای یک عملیات کوچک است.
- هنگام فیلتر: ترجیحاً کارت‌های قبلی باقی بمانند و indicator محلی ظاهر شود. اگر Skeleton جایگزین می‌کنی، ارتفاع ظرف و وضعیت focus را مدیریت کن؛ داده/لینک‌های قابل استفاده را بی‌دلیل حذف نکن. Load More فقط بخش انتهایی را pending کن. جست‌وجوی تایپی debounce حدود 250–350ms؛ Enter بدون تأخیر.
- هر منطقه AJAX مالک AbortController، requestId و تایمرهای خود باشد. درخواست قبلی لغو، پاسخ دیررس نادیده؛ `response.ok` و payload اعتبارسنجی؛ خطا/timeout/empty/success تفکیک شوند. در `finally` فقط درخواست فعال spinner و busy را پاک کند. retry برای GET امن یا پس از تأیید کاربر برای عملیات تغییر‌دهنده؛ ارسال دوباره فرم/سفارش ممنوع.
- Commit اتمیک نتیجه → destroy افکت/پلیرهای حذف‌شده (پخش جاری طبق سیاست محصول) → جایگزینی/append DOM معتبر → init فقط بخش جدید → یک refresh تجمیع‌شده. در خطا، نتایج قبلی و امکان Retry یا ناوبری معمولی حفظ شوند. URL و History پس از commit موفق به‌روزرسانی؛ `popstate` بازگشت بدون push مجدد. focus و نتیجه قابل اعلان باشند؛ با Load More فوکوس کاربر بی‌دلیل جابه‌جا نشود.
- وردپرس: REST API یا endpoint موجود، نه انتخاب اجباری API تازه. URL از PHP config، sanitize/validate ورودی، escape خروجی، مجوز واقعی و nonce مناسب برای عملیات محافظت‌شده؛ nonce جای authorization نیست. HTML از قالب‌های مشترک PHP با خروجی امن یا JSON و ساخت DOM امن؛ داده غیرمطمئن را مستقیم `innerHTML` نکن. کلید/اطلاعات خصوصی در JS قرار نگیرد.

## 9. لودینگ موسیقی، ویدئو، تصاویر و فونت

- آرشیو: کاور/پوستر سبک با اندازه ثابت؛ فایل صوت/ویدئو برای هر کارت از ابتدا کامل دانلود نشود؛ `preload="none"` برای آیتم‌های غیرفعال یا `metadata` فقط در صورت نیاز واقعی. preload یک hint است؛ autoplay/مرورگر ممکن است رفتار را تغییر دهد. برای مدت آهنگ بهتر است metadata ذخیره‌شده سمت سرور استفاده شود.
- پلیر: وضعیت `play()` promise و رویدادها را کنترل کن. `waiting` و `seeking` در صورت انتظار واقعی → نشانگر محلی؛ `playing` و `seeked` با بررسی state/readyState → بازگشت؛ `pause`, `ended`, `error`, عوض شدن source و Abort → لغو وضعیت قبلی و پاکسازی. `canplay` تنها یعنی امکان شروع، نه تضمین پخش کامل؛ `stalled` را با وضعیت پخش و timeout بررسی کن، نه spinner دائمی. در ردشدن `play()` خطای مناسب/دکمه تلاش دوباره. دانلود صوت با page readiness ارتباط ندارد.
- پوستر و دکمه play از ابتدا؛ اگر پخش محتوایی است controls قابل استفاده با کیبورد و caption/transcript متناسب. پخش صدادار خودکار ممنوع؛ تزئینی muted/playsinline اختیاری و سبک با fallback. پلی‌لیست و پلیر سراسری اگر بین صفحات کامل باید پخش بی‌وقفه داشته باشند، نیاز به معماری جداگانه دارند؛ Page Transition معمولی آن را فراهم نمی‌کند.
- ویدئوهای دارای هدف SEO: صفحه جزئیات مستقل، ویدئو و thumbnail قابل کشف، اطلاعات واقعی، بررسی VideoObject و شرایط ایندکس؛ محتوا صرفاً پس از کلیک از هیچ ساخته نشود. این به معنی دانلود کامل ویدئو در ورود نیست. برای آرشیو/iframeهای پایین صفحه deferred/lazy در صورت حمایت مرورگر و بدون تخریب کشف محتوای اصلی.
- تصویر LCP/hero: در HTML و قابل کشف، بدون lazy؛ `fetchpriority="high"` فقط در صورت نیاز و با کنترل waterfall. `srcset/sizes/picture` برای responsive؛ عرض/ارتفاع یا aspect-ratio. فونت ضروری محدود، WOFF2 در صورت مناسب بودن، `font-display` و fallback سازگار، preload فقط فونت واقعاً ضروری. `document.fonts.ready` یا decode برای refresh تکمیلی، نه شرط بازشدن پرده.

## 10. کاروسل، Sticky و موبایل

- کاروسل دستی پیش‌فرض؛ CSS scroll-snap برای حالت ساده، GSAP/کتابخانه فقط در صورت نیاز. اسلایدها و لینک‌ها در HTML؛ بدون ساخت دوباره کل DOM برای موبایل؛ slide clone در صورت loop نباید لینک/heading تکراری قابل focus یا ID تکراری داشته باشد. RTL، drag/swipe، keyboard، کنترل قبلی/بعدی، focus visible و شمارش مناسب.
- اگر autoplay ضروری است، pause/resume قابل دسترس؛ توقف در hover/focus، بعد از focus بدون درخواست کاربر خودکار شروع نشود؛ زیر reduced-motion و tab پنهان اجرا نشود. محتوای متحرک خودکار طولانی طبق WCAG نیازمند کنترل است. تصویر اسلاید بالای صفحه را سریع بارگذاری کن؛ اسلایدهای دیگر طبق وضعیت واقعی viewport. ظرف ثابت و حرکت با transform؛ nested carousel نباید اسکرول عمودی/zoom را بگیرد.
- Sticky ساده اول با CSS؛ ScrollTrigger pin فقط برای نیاز طراحی واقعی و با تست. موبایل عمودی و native، بدون سکشن استیکی تمام‌صفحه اجباری. منوی موبایل با همان لینک‌ها؛ `100svh/dvh` در جای لازم و safe area؛ تست تغییر orientation، آدرس‌بار و کیبورد موبایل. دو نسخه کامل HTML دسکتاپ/موبایل ممنوع.

## 11. WordPress، Build و عملکرد

- `wp_enqueue_scripts`، `wp_enqueue_script/style` و در پروژه‌های سازگار script modules؛ handle/dependency درست، `defer` برای اسکریپت‌های مناسب، `async` فقط مستقل از ترتیب؛ `wp_head()` و `wp_footer()` حفظ شوند. GSAP و پلاگین‌ها یک بار از مسیر موجود/manifest بارگذاری شوند، نه CDN + bundle هم‌زمان. نسخه‌بندی asset با manifest/hash. بدون وابستگی جدید به jQuery یا صفحه‌ساز.
- GSAP و ماژول‌های سنگین را فقط در صفحات/شرایط لازم enqueue یا dynamic import کن. `matchMedia` تنها اجرا را تغییر می‌دهد و به خودی خود بایت دانلود را کم نمی‌کند. در Network تأیید کن کد/مدیای desktop-only واقعاً روی موبایل دانلود نمی‌شود. Cache و minify/defer plugin با ترتیب وابستگی تست شود.
- محتوای مهم و navigation از SSR قابل دسترسی، بدون اجرای انیمیشن و کاربر مصنوعی. Googlebot برای دریافت متن اصلی به click/swipe نیاز نداشته باشد. فونت/تصویر/JS به‌خاطر loader از خزنده پنهان نشود. SEO و سرعت مهم‌تر از افکت تزئینی‌اند.
- اهداف واقعی Core Web Vitals (صدک 75 کاربران، جداگانه موبایل/دسکتاپ): **LCP ≤ 2.5s، INP ≤ 200ms، CLS ≤ 0.1**. Lighthouse تست آزمایشگاهی است، جای داده واقعی CrUX/RUM را نمی‌گیرد؛ برای INP تعامل واقعی را تست کن (TBT معادل INP نیست). هیچ امتیاز ثابت Lighthouse یا مدت animation تضمین رتبه نیست.

## 12. چک‌لیست پذیرش Codex (قبل از تحویل هر قالب)

- [ ] ابتدا سورس/manifest و مالک فعلی loader، lock، events، GSAP و AJAX را شناسایی کن؛ منطق legacy و duplicate را با دلیل اصلاح کن. بدون نیاز کتابخانه/SPA اضافه نکن؛ طراحی یا URL مشتری را خودسرانه تغییر نده.
- [ ] ورود مستقیم، Reload، کلیک لینک داخلی، Back/Forward واقعی (با و بدون BFCache)، hash، external، Ctrl/Cmd+click، target blank، download، فرم، 404 و redirect تست شده؛ پرده گیر نمی‌کند و ناوبری دو بار رخ نمی‌دهد.
- [ ] JS غیرفعال/خطای عمدی GSAP/شکست import/CSS animation callback از دست‌رفته: HTML، لینک و H1 قابل استفاده؛ پرده و lock باقی نمی‌مانند.
- [ ] در موبایل Android Chrome/iOS Safari واقعی و دسکتاپ Chrome/Firefox/Safari، RTL در صورت نیاز، عرض باریک، orientation، scroll restoration، pinch zoom، mouse/touch/keyboard و reduced-motion؛ focus مخفی نمی‌شود.
- [ ] ورود متن پشت پرده از دست نمی‌رود؛ H1/LCP ظاهر فوری؛ GSAP context و triggerها بعد از AJAX/resize دوباره تکثیر نمی‌شوند؛ pin spacer یا transform قدیمی باقی نمی‌ماند؛ modal/menu/پلیر ثابت با Smoother تداخل ندارد.
- [ ] AJAX: پاسخ فوری، شبکه کند، قطع اینترنت، 404/500، timeout، پاسخ خارج از ترتیب، filter سریع، Load More، نتیجه خالی و Retry؛ فقط عملیات خود busy است؛ کنترل قدیمی در `finally` درخواست جدید را پاک نمی‌کند؛ History درست.
- [ ] مدیا: play/pause سریع، buffering، seek، error، source swap، تغییر صفحه و ردشدن `play()`؛ spinner فقط پلیر فعال؛ مدیای غیرفعال بی‌جهت دانلود نشود.
- [ ] کاروسل: swipe طبیعی/keyboard، pause/resume، reduced-motion، بدون جابه‌جایی ارتفاع؛ منو/ذخیره اسکرول خراب نشود.
- [ ] DevTools Network/Performance و Lighthouse: cold/warm cache، throttling موبایل، long task، layout/paint، LCP element، CLS، تعداد JS requests؛ مقایسه قبل/بعد با افکت خاموش برای تشخیص هزینه واقعی. پس از انتشار RUM/CrUX اگر داده کافی بود.
- [ ] گزارش تحویل: فایل‌های تغییرکرده، تصمیم‌های افکت هر صفحه، مقادیر timing و دلیل، سناریوهای تست‌شده با نتیجه، مشکلات حل‌نشده؛ هیچ نتیجه‌ای بدون اجرای واقعی Pass اعلام نشود.

## منابع بررسی‌شده (مبنای فنی؛ اعداد زمان‌بندی بالا پیشنهاد پروژه‌اند)

- Google Search Central — [Mobile-first indexing](https://developers.google.com/search/docs/crawling-indexing/mobile/mobile-sites-mobile-first-indexing)، [Lazy-loaded content](https://developers.google.com/search/docs/crawling-indexing/javascript/lazy-loading)، [Video SEO](https://developers.google.com/search/docs/appearance/video)، [Pagination](https://developers.google.com/search/docs/specialty/ecommerce/pagination-and-incremental-page-loading).
- web.dev — [Optimize LCP](https://web.dev/articles/optimize-lcp)، [Web Vitals](https://web.dev/articles/vitals)، [High-performance animations](https://web.dev/articles/animations-guide)، [Carousels](https://web.dev/articles/carousel-best-practices)، [BFCache](https://web.dev/articles/bfcache).
- GSAP — [ScrollTrigger](https://gsap.com/docs/v3/Plugins/ScrollTrigger/)، [Refresh](https://gsap.com/docs/v3/Plugins/ScrollTrigger/static.refresh%28%29/)، [ScrollSmoother](https://gsap.com/docs/v3/Plugins/ScrollSmoother/)، [ScrollToPlugin](https://gsap.com/docs/v3/Plugins/ScrollToPlugin/)، [matchMedia](https://gsap.com/docs/v3/GSAP/gsap.matchMedia%28%29/)، [context](https://gsap.com/docs/v3/GSAP/gsap.context%28%29/).
- WordPress Developer — [Enqueue scripts](https://developer.wordpress.org/reference/functions/wp_enqueue_script/)، [REST endpoints](https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/)، [Nonces](https://developer.wordpress.org/apis/security/nonces/).
- MDN — [DOMContentLoaded](https://developer.mozilla.org/en-US/docs/Web/API/Document/DOMContentLoaded_event)، [pageshow](https://developer.mozilla.org/en-US/docs/Web/API/Window/pageshow_event)، [Fetch](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch)، [HTMLMediaElement](https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement)، [video](https://developer.mozilla.org/en-US/docs/Web/HTML/Reference/Elements/video)، [aria-busy](https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/Reference/Attributes/aria-busy)، [View Transition](https://developer.mozilla.org/en-US/docs/Web/API/View_Transition_API/Using)، [CSS Scroll Snap](https://developer.mozilla.org/en-US/docs/Web/CSS/Guides/Scroll_snap).
- W3C WAI — [Carousel animations](https://www.w3.org/WAI/tutorials/carousels/animations/)، [WCAG Pause, Stop, Hide](https://www.w3.org/WAI/WCAG22/Understanding/pause-stop-hide).
- IBM Carbon Design System — [Loading patterns](https://www.carbondesignsystem.com/building-blocks/core/patterns/loading).

**مرز اعتبار:** این فایل مشخصات عمومی و معیار پذیرش است، نه کد اجرایی آماده نصب و نه تأیید عملکرد قالب خاص. اجرای واقعی و تست هر پروژه الزامی است.

## ارجاع به قرارداد تصاویر و استثنای هماهنگی پرده
[17](../shared/17-IMAGES-AND-MEDIA-v1.0.md) مالک نوع و منبع/اولویت تصویر و سیاست مدیا است؛ این سند مالک state و چرخه لودینگ. شروع هم‌زمان کنترل‌شده Entrance با بازشدن پرده فقط با readiness state صریح و آزمون قابل مشاهده بودن افکت و محتوای حیاتی؛ نه delay ثابت از تایمر مستقل. اگر پرده نیست/لغو شده یا reduced-motion، محتوا بلاواسطه؛ مسیر BFCache بازبینی شود. هیچ استثنا مجوز تأخیر عمدی LCP یا overlay گیرکرده نیست.
