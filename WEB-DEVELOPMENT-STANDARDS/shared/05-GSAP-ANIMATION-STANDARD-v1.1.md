# استاندارد توسعه انیمیشن با GSAP — v1.1

**بازبینی منابع:** 2026-11-18 | **مخاطب:** Codex / توسعه‌دهنده فرانت‌اند
**دامنه:** تمام وب‌سایت‌های مشتریان؛ HTML/CSS/Vanilla JS، قالب اختصاصی WordPress و Vite در صورت استفاده. مستقل از برند. این سند مخصوص Motion است، نه معماری کامل سایت.

> MUST = قرارداد اجرایی تیم؛ SHOULD = پیش‌فرض قابل تغییر با دلیل و تست. زمان‌ها و بودجه‌های عملکردی پیشنهادی تیم‌اند، نه شرط رتبه‌بندی گوگل. برای ترتیب لودینگ و انتقال صفحه، قرارداد مسیر پروژه مرجع است؛ 06 فقط برای قالب PHP اختصاصی. 02 و 16 مالک توکن‌ها و کامپوننت‌ها هستند.

## 1. انتخاب ابزار و معماری

- CSS برای hover/focus، تغییر ساده رنگ، opacity و transition مستقل؛ GSAP برای timeline، stagger، choreography، ScrollTrigger، حرکت‌های تعاملی پیچیده. هر افکت ساده الزاماً GSAP نمی‌خواهد.
- GSAP فقط مالک نمایش و حرکت است؛ وضعیت فرم، درخواست API، منو، فیلتر، ناوبری و دسترس‌پذیری با Vanilla JS و HTML مدیریت می‌شوند. هیچ عملیات اصلی وابسته به پایان یک انیمیشن نباشد.
- یک نسخه GSAP در هر runtime؛ نسخه بسته‌ها قفل شود. `gsap.registerPlugin()` برای پلاگین‌های واقعاً مصرفی؛ از CDN و npm هم‌زمان استفاده نشود. واردکردن همه پلاگین‌ها یا ساخت engine سفارشی تکراری ممنوع.
- از ESM و Vite (در صورت وجود) استفاده کن. ماژول GSAP/پلاگین غیرضروری در صفحات بدون افکت درخواست نشود. برای موبایل، `matchMedia` فقط اجرای افکت را مدیریت می‌کند؛ برای حذف دانلود نیاز به import شرطی و کنترل گراف bundle است.
- معماری پیشنهادی، نه اجبار به تغییر مسیرهای موجود:

```text
src/js/motion/
  index.js         # orchestration and lifecycle
  entrance.js      # hero / reveals
  scroll.js        # ScrollTrigger / optional ScrollSmoother
  text.js          # SplitText and rotating text, if needed
  interactions.js  # complex hover / pointer effects
```

- از hookهای `data-motion` برای انتخاب هدف استفاده کن؛ selector عمومی روی تمام `h2`، `.card` یا `body *` ممنوع. هر کامپوننت فقط در root خودش انتخاب و مدیریت شود. مدل HTML اصلی مشترک و responsive باشد.

## 2. قواعد Tween و Timeline

| موضوع | استاندارد تیم |
|---|---|
| Tween | `to` برای حرکت به مقصد معلوم؛ `from/fromTo` تنها با وضعیت شروع روشن و در زمان درست. |
| Timeline | چند حرکت مرتبط را با label/position هماهنگ کن؛ زنجیره `setTimeout` برای زمان‌بندی بصری نساز. |
| مالکیت | یک مالک برای هر property هر عنصر در یک زمان. برای چند حرکت مستقل wrapperهای تودرتو استفاده کن. |
| تداخل | برای تعامل سریع، tween قبلی همان هدف/property را مدیریت کن؛ `overwrite: 'auto'` وقتی مناسب است، نه تنظیم سراسری کورکورانه. |
| وضعیت اولیه | HTML/CSS پایه خوانا و قابل استفاده، بدون JS نیز قابل مشاهده؛ محتوای مهم را از پیش با opacity:0 سراسری مخفی نکن. |
| تکرار | loop بی‌پایان تزئینی پیش‌فرض خاموش؛ در صورت نیاز فقط هنگامی که دیده می‌شود و قابلیت توقف دارد. |
| cleanup | هر ماژول disposer دارد؛ context/matchMedia revert، listener و observer و timer حذف شوند. |

- `from`/`fromTo` ممکن است وضعیت اولیه را هنگام ایجاد اعمال کنند (`immediateRender`)؛ ساخت هم‌زمان چند tween شروع روی یک property می‌تواند پرش ایجاد کند. قبل از ترکیب timelineها ترتیب را بررسی کن؛ `immediateRender:false` فقط در موارد لازم.
- پایان واقعی افکت با `onComplete` یا callback مالک هماهنگ شود، نه timer مستقل. در صورت kill/cancel/error نیز مسیر آزادسازی وضعیت داشته باش. افکت مالک ناوبری یا شبکه نیست.
- برای تطبیق حرکت با مرجع، `easing` (نحوهٔ تغییر سرعت)، `duration` (مدت حرکت) و `delay` یا مکث (مدت ثابت‌ماندن) را جدا ثبت و تست کن. اگر `CustomEase` مرجع چهار مختصات Bézier دارد، برای حرکت سادهٔ CSS همان مقادیر را به `cubic-bezier()` بده. برای منحنی چندبخشی یا مکث واقعی، شکل مسیر یا توقف صریح را هم بررسی کن. قرارداد پرده در [لودینگ سفارشی](../wordpress-custom/06-LOADING-SYSTEM-v1.1.md) است.
- از `gsap.context()` برای scope و rollback استفاده کن. tweenهایی که بعدها در event یا callback ساخته می‌شوند باید با `context.add()` یا callback مدیریت‌شده ثبت شوند، یا صریحاً kill/revert شوند. برای responsive از `gsap.matchMedia()` (دارای context داخلی) استفاده کن؛ ثبت listener خارجی و timer همچنان cleanup صریح می‌خواهد. استفاده از `killAll()` سراسری در ماژول محلی ممنوع.

## 3. ScrollTrigger و ScrollSmoother

- ابتدا CSS `position: sticky`، IntersectionObserver یا اسکرول طبیعی را ارزیابی کن. ScrollTrigger برای هماهنگی واقعی scroll/motion، نه هر رفتار ساده.
- `ScrollTrigger` را یک‌بار در محل ثبت پلاگین ثبت کن. حداقل trigger لازم، scope دقیق، start/end مشخص؛ مقدار responsive و وابسته به ابعاد در صورت نیاز function-based + `invalidateOnRefresh:true`. `markers` فقط development.
- برای timeline scrubbed، یک ScrollTrigger بر timeline والد؛ به tweenهای داخل timeline همان کنترل playhead را دوباره نده. برای scrub معمولاً ease خطی مناسب است؛ شروع/پایان و easing طبق طراحی تست شوند.
- عنصر pinned را مستقیماً transform نکن؛ فرزند داخلی را حرکت بده تا محاسبه pin پایدار بماند. nested pin را پیش‌فرض حذف کن. اثر `pinSpacing`، Flex، overflow، transform اجداد، ارتفاع موبایل و تغییر layout را بررسی کن.
- بعد از آماده‌شدن layout/ساخت triggerها و تغییر واقعی ابعاد (مثلاً AJAX، بازشدن بخش یا فونت مؤثر)، refresh لازم را به‌صورت یک‌بار/تجمیعی انجام بده. `ScrollTrigger.refresh()` اندازه‌گیری دوباره است، `update()` جایگزین آن نیست. refresh در هر scroll یا frame ممنوع.
- ScrollSmoother اختیاری، فقط یک instance در صفحه؛ قبل از triggerهای وابسته ساخته شود. اسکرول native موبایل و reduced motion پیش‌فرض است. برای حالت خاموش wrapper نباید overflow/height محدودکننده باقی بگذارد. هدر fixed، dialog/overlay، پرده و پلیر شناور بیرون از محتوای transform‌شده باشند. با Lenis/engine مشابه هم‌زمان کنترل scroll نساز.
- اگر پس از AJAX تنها بخشی تغییر می‌کند، فقط ماژول‌های همان root را dispose و mount کن؛ global smoother باقی بماند؛ refresh پس از mount موفق. اگر کل root تعویض می‌شود، مالک smoother طبق چرخه صفحه cleanup کند. قبل از نصب دوباره، listenerها و pin spacerهای قبلی بررسی شوند.
- Tab، anchor/hash، back/forward، لینک skip، focus و اسکرول صفحه را با/بدون smoothing تست کن؛ اسکرول کاربر قفل دائمی یا به زور reset نشود.

## 4. متن، هیرو، لودینگ و دسترس‌پذیری

- H1، متن واقعی و لینک‌ها سمت سرور در HTML موجود باشند. SplitText/GSAP برای جلوه است، نه ساخت محتوای مختص گوگل یا تکرار کلمات. عنوان اصلی و متن مهم حتی بدون اجرای JS خوانا باشند.
- LCP احتمالی (تصویر/عنوان هیرو) را با delay، opacity صفر یا پوشش طولانی مخفی نکن؛ ابتدا محتوا نمایش داده شود و افکت روی embellishment یا عنصر غیربحرانی اجرا شود. در صفحات خاص استثنا باید با trace سنجیده شود.
- یک مالک برای Page Transition/curtain و lock اسکرول. Entrance فقط بعد از مجوز نمایش مطابق قرارداد لودینگ مسیر پروژه؛ اگر پرده غیرفعال باشد مستقیماً اجرا شود. event از دست‌رفته نباید باعث انتظار بی‌پایان شود؛ readiness state/promise پایدار داشته باش. Loader آرشیو/پلیر مستقل است و curtain را کنترل نمی‌کند.
- SplitText (در نسخه‌های دارای قابلیت): برای خط‌ها پس از آماده‌شدن فونت مرتبط یا `autoSplit` + `onSplit` (API جدید را با نسخه نصب‌شده تطبیق بده). در `onSplit` انیمیشن متن را return کن تا lifecycle دوباره split قابل مدیریت باشد. روی dispose، split و tween قبلی revert شوند. لینک/تأکید داخلی و screen reader را بررسی کن؛ روی نوشته فارسی به‌شکل پیش‌فرض word/line، نه character که ممکن است اتصال حروف را به‌هم بزند. عنوان را دوباره در DOM برای موبایل نساز.
- انیمیشن rotator: یک ناحیه بصری با اندازه پایدار، مکث کافی، متن ثابت توصیفی و بدون live-region پرحرف. اگر کنترل توقف لازم است، دکمه واقعی و قابل دسترس داشته باشد؛ برای reduced motion ثابت نمایش داده شود.
- انیمیت عناصر تعاملی مخفی ممکن است focus را نامرئی کند. دکمه/لینک فعال را در visibility/opacity نامناسب رها نکن؛ `aria-hidden` برای محتوای صرفاً تزئینی، نه برای محتوای معنادار یا فوکوس‌پذیر. حالت منو/dialog با semantics مستقل از motion.
- `prefers-reduced-motion: reduce`: حذف parallax/smooth scroll/pin تزئینی/loop، نمایش فوری یا تغییر کوتاه ساده. تغییر preference هنگام بازبودن صفحه نیز درست کار کند. حرکت خودکار بیش از 5 ثانیه همراه محتوای دیگر مطابق WCAG 2.2.2 نیاز به توقف/مکث/پنهان‌سازی دارد مگر ضروری باشد؛ از flash خطرناک اجتناب شود.

## 5. پروفایل حرکت (پیش‌فرض تیم، نه قانون GSAP یا گوگل)

| افکت | Desktop | Mobile / Touch |
|---|---|---|
| hover ساده | CSS 120–220ms | حذف حرکت وابسته به hover؛ feedback لمس |
| ورود کارت غیرحیاتی | 300–600ms, y: 12–24px | 200–400ms, y: 8–16px یا fade ساده |
| stagger | 40–70ms | 20–50ms، گروه کوچک |
| scroll reveal | once، تعداد محدود | once، بدون scrub سنگین |
| sticky storytelling | در صورت نیاز و تست | natural flow پیش‌فرض |
| parallax | محدود با دامنه کم | خاموش یا سبک پس از تست |
| smoother / cursor / magnetic | فقط قابلیت‌محور pointer:fine و hover:hover | خاموش پیش‌فرض |
| curtain | طبق قرارداد لودینگ مسیر پروژه | مختصر یا خاموش |

- قابلیت pointer/hover را از عرض viewport جدا بسنج؛ لپ‌تاپ لمسی و تبلت با ماوس وجود دارند. breakpoint از CSS پروژه خوانده/همگام شود. اعداد و ease براساس طراحی تغییر کنند، اما UX معطل نشود.

<a id="customer-content-entrance"></a>

### پروفایل ورود عنوان و متن مشتری (مرجع Hub / رحیمی)

این پروفایل برای عنوان و متن معرفیِ انتخاب‌شده در طراحی مشتری است. جدول، شعر، ردیف کارت‌ها، عددهای مستقل و کنترل‌های تعاملی را فقط با درخواست مشخص وارد دامنهٔ افکت کن. مقادیر زیر از تنظیمات Hub ارسالی ثبت شده‌اند.

| تنظیم | مقدار |
|---|---|
| Animation Presets | `Custom` |
| Ease / Direction | `power4.out` / `From Start` |
| Duration / Stagger / Start Delay | `1.6s` برای هر هدف / `0.16s` بین هدف‌ها / `0.2s` پس از محرک |
| Animate from | Translate ‏X/Y/Z ‏`0/30/0px`، Scale ‏X/Y ‏`1/1`، Rotate ‏X/Y/Z ‏`30/0/0deg`، Opacity ‏`0`، Transform Origin ‏X/Y ‏`50%/50%` |
| Animate to | مقصد اجرایی استاندارد، جابه‌جایی و چرخش `0`، Scale ‏`1`، Opacity ‏`1`، مبدأ تبدیل در مرکز. اسکرین‌شات‌ها فقط مقادیر پنل `Animate from` را نشان می‌دهند. |

- از اولین paint تا لحظهٔ شروع واقعی، هدف آماده‌شده `opacity:0` داشته باشد. پیش‌نمایش کمرنگ، فلش متن کامل و پنهان‌شدن دوباره خطاست. در ورود صفحه، محرک پس از آماده بودن نمایش یا پایان پردهٔ انتخاب‌شده صادر شود. در سکشن پایین صفحه، ورود یک‌بار هنگام رسیدن به viewport اجرا شود. تأخیر شروع بخشی از تایم‌لاین است. محتوا مسیر نمایش عادی در نبود JS، شکست بارگذاری و کاهش حرکت داشته باشد. این پروفایل را روی LCP یا متن حیاتی بدون سنجش اثر اعمال نکن.
- برای فارسی، واژه یا خط کامل واحد حرکت است. اتصال حروف، نیم‌فاصله، نشانه‌گذاری، RTL، اندازه، نام قابل‌دسترس و تگ heading را حفظ کن. متن معرفی را به‌صورت یک بلوک کامل یا همهٔ خطوط همان بلوک حرکت بده. اجرای افکت فقط روی قطعه‌ای از متنِ انتخاب‌شده باعث ناهماهنگی است.
- ورود فقط `transform` و `opacity` را تغییر می‌دهد و پس از یک نوبت اجرا آزاد می‌شود. برای reveal معمولی یک observer مشترک یا trigger یک‌بارهٔ موجود کافی است. دارایی‌ها در مسیرهای مصرف‌شده و روی همان گروه بارگیری شوند.
- **پارالاکس افکت مستقلی است.** موقعیت آن با پیشرفت اسکرول تغییر می‌کند و فقط برای عنصر مشخص فعال می‌شود. در کانتینر پروفایل ورود، Parallax خاموش است. اگر هر دو افکت روی یک بخش لازم‌اند، یک لایه برای ورود و لایهٔ دیگری برای پارالاکس مالک `transform` باشد. هر افکت محرک، cleanup و تنظیم موبایل مستقل دارد.

## 6. الگوی عملی ماژول Reveal (GSAP 3.11+)

نمونه زیر صرفاً برای محتوای تزئینی و غیرحیاتی پایین صفحه است؛ به کل Hero یا عناصر فوکوس‌پذیر اعمال نشود. mount و disposer در orchestration فراخوانی می‌شوند. این نمونه مستقل از WordPress است.

```js
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);
const instances = new WeakMap();

export function mountReveal(root) {
  if (!root) return () => {};
  if (instances.has(root)) return instances.get(root);
  const mm = gsap.matchMedia();
  const targets = [...root.querySelectorAll('[data-motion="reveal"]')];

  mm.add({
    desktop: '(min-width: 1024px)',
    all: 'all', // keep a branch active at every viewport width
    reduce: '(prefers-reduced-motion: reduce)',
  }, ({ conditions }) => {
    if (conditions.reduce) return; // baseline visible, no motion
    const eligible = targets.filter((target) =>
      target.getBoundingClientRect().top >= window.innerHeight);
    // Batch layout reads before creating tweens.
    for (const target of eligible) {
      gsap.from(target, {
        y: conditions.desktop ? 20 : 10,
        opacity: 0,
        duration: conditions.desktop ? 0.45 : 0.28,
        ease: 'power2.out',
        scrollTrigger: { trigger: target, start: 'top 92%', once: true },
      });
    }
  }, root);

  const dispose = () => {
    if (instances.get(root) !== dispose) return;
    mm.revert();
    instances.delete(root);
  };
  instances.set(root, dispose);
  return dispose;
}
```

در پروژه واقعی اندازه‌گیری‌ها بعد از layout پایدار انجام شوند؛ برای لیست بسیار بزرگ از گروه‌بندی/افکت کمتر استفاده کن. روی root نامرتبط فراخوانی نکن. ماژول باید پس از آماده‌شدن پرده/DOM طبق orchestrator mount شود؛ اگر import شکست خورد HTML پایه قابل استفاده باقی بماند. این نمونه جایگزین سیستم عضویت همه کامپوننت‌ها نیست.

## 7. Performance و تحویل

- ترجیح `transform` و `opacity`؛ تغییر width/height/top/left یا filter/blur و shadow بزرگ فقط با نیاز واقعی و اندازه‌گیری. compositor همیشه تضمین‌شده نیست؛ `will-change` دائمی روی همه عناصر هزینه حافظه دارد.
- readهای layout و writeها را دسته‌بندی کن؛ در callback scroll/pointer خواندن و نوشتن متوالی انجام نده. از ticker/`quickTo` برای تعقیب سریع در صورت نیاز، نه ایجاد صدها tween در هر pointermove؛ cleanup ticker الزامی. از GPU acceleration اجباری برای همه عناصر پرهیز کن.
- ماژول سنگین و 3D را conditional import کن؛ منابع hero ضروری را قربانی افکت نکن. تعداد DOM split و trigger و long taskها باید در DevTools سنجیده شود؛ سقف ثابت جهانی وجود ندارد.
- اهداف محصول برای تجربه خوب (داده واقعی صدک 75 موبایل/دسکتاپ): LCP ≤2.5s، INP ≤200ms، CLS ≤0.1؛ تست lab به‌تنهایی تضمین field data یا رتبه نیست. قبل/بعد از motion مقایسه و regression ثبت شود.

### چک‌لیست پذیرش Codex

- [ ] قبل از تغییر، inventory همه GSAP instance، پلاگین‌ها، listenerها، loader و CSS motion؛ مسیر فایل‌های واقعی پروژه را حفظ کن.
- [ ] یک مالک برای هر transform/opacity و یک engine scroll؛ init دوباره باعث افزایش trigger/listener نشود.
- [ ] desktop، tablet، mobile touch، RTL، orientation/resize و تغییر reduced-motion تست شوند.
- [ ] ورود مستقیم، reload، لینک داخلی، anchor، Back/Forward/BFCache، تکرار AJAX و تغییر سریع فیلتر تست شوند؛ محتوای مهم در خطای JS یا slow network قابل مشاهده باشد.
- [ ] با کیبورد، focus، screen reader، توقف loop و منوهای fixed بررسی شود؛ هیچ control پشت پرده یا opacity صفر نماند.
- [ ] pin spacer و inline styles قدیمی پس از dispose حذف/بازگردانده شوند؛ ماژول نامرتبط از بین نرود.
- [ ] Performance/Network/Memory در مرورگر واقعی و موبایل ضعیف؛ اندازه bundle، frame drops، layout shift و long tasks قبل/بعد ثبت شوند.
- [ ] build تولید موفق، بدون console error؛ گزارش تصمیم، فایل‌های تغییرکرده و تست‌های انجام‌نشده را تحویل بده؛ تست ساختگی گزارش نکن.

## منابع رسمی

- GSAP: [Overview](https://gsap.com/docs/v3/)، [context](https://gsap.com/docs/v3/GSAP/gsap.context()/)، [matchMedia](https://gsap.com/docs/v3/GSAP/gsap.matchMedia()/)، [Tween vars](https://gsap.com/docs/v3/GSAP/Tween/vars/).
- GSAP: [ScrollTrigger](https://gsap.com/docs/v3/Plugins/ScrollTrigger/)، [refresh](https://gsap.com/docs/v3/Plugins/ScrollTrigger/static.refresh()/)، [ScrollSmoother](https://gsap.com/docs/v3/Plugins/ScrollSmoother/)، [SplitText](https://gsap.com/docs/v3/Plugins/SplitText/)، [quickTo](https://gsap.com/docs/v3/GSAP/gsap.quickTo()/).
- web.dev: [Animation performance](https://web.dev/articles/animations-guide)، [Layout thrashing](https://web.dev/articles/avoid-large-complex-layouts-and-layout-thrashing)، [Core Web Vitals](https://web.dev/articles/top-cwv)، [Web Vitals thresholds](https://web.dev/articles/vitals).
- MDN: [Reduced motion](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/At-rules/@media/prefers-reduced-motion). W3C: [WCAG 2.2](https://www.w3.org/TR/WCAG22/)، [Carousel animation](https://www.w3.org/WAI/tutorials/carousels/animations/).
- Google Search Central: [JavaScript SEO](https://developers.google.com/search/docs/crawling-indexing/javascript/javascript-seo-basics)، [Mobile-first indexing](https://developers.google.com/search/docs/crawling-indexing/mobile/mobile-sites-mobile-first-indexing).

**تاریخچه:** v1.0 — ایجاد سند مستقل Motion. مثال کد آموزشی/اجرایی مرجع است؛ تست واقعی در هر قالب بر عهده توسعه‌دهنده است.
