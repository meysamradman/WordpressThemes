# استاندارد کدنویسی Vanilla JavaScript اختصاصی — v1.1

**بازبینی منابع:** 2026-11-18 | **مخاطب:** Codex / توسعه‌دهنده فرانت‌اند
**دامنه:** کد JavaScript متعلق به تیم در وب‌سایت‌های مشتریان (WordPress/PHP SSR و پروژه‌های HTML/CSS/JS)، با یا بدون Vite. برای React/Next.js سند lifecycle جدا لازم است؛ این سند توصیه به حذف فریم‌ورک از پروژه‌ای که به آن نیاز دارد نیست.

> MUST = قرارداد اجرایی تیم؛ SHOULD = پیش‌فرض قابل تغییر با دلیل و تست. هدف: خوانایی، امنیت، عملکرد، progressive enhancement و نگهداری آسان. قواعد حرکت در سند 05 و فرآیند loading/navigation در قرارداد مسیر پروژه تعریف می‌شود (06 برای قالب PHP اختصاصی). قواعد عمومی CSS، responsive و کامپوننت در 02، 03 و 16 هستند.

## 1. معماری و سبک کد

- Vanilla JS یعنی استفاده از APIهای استاندارد مرورگر، نه بازنویسی غیرضروری همه ابزارهای تست‌شده. jQuery، UI framework یا کتابخانه جدید فقط با نیاز روشن و تأیید؛ GSAP برای حرکت مجاز، نه مدیریت state یا API.
- ES Modules (`import/export`) و `const`/`let`؛ `var`، globalهای پراکنده، inline `onclick`، `eval`/`new Function` و string-based timers در کد جدید استفاده نشوند. متغیر mutable تنها برای state لازم؛ نام‌های توصیفی انگلیسی و ثابت. از ESLint/Prettier متناسب با پروژه استفاده کن.
- تابع کوچک با مسئولیت مشخص؛ pure function برای تبدیل داده در صورت امکان؛ DOM و شبکه در لایه مناسب. از utility عمومی تنها برای رفتار واقعی مشترک استفاده کن، نه abstraction بی‌دلیل. JSDoc برای contract پیچیده، نوع ورودی/خروجی و cleanup؛ توضیح دلیل تصمیم نه تکرار خود کد.
- هر feature مالک root، state، listener، request و disposer خود باشد. یک entry point orchestration؛ از import دَوَری و side effect سراسری غیرشفاف بپرهیز. ساختار پیشنهادی (مسیرهای موجود را بی‌دلیل عوض نکن):

```text
src/js/
  main.js
  core/             # bootstrap, lifecycle, utilities
  components/       # navigation, dialog, carousel
  features/         # archives, search, forms, player
  services/         # API client, request helpers
  motion/           # GSAP only (document 05)
```

- UI با DOM markup واقعی SSR؛ CSS مسئول layout، responsive و استایل؛ JS برای behavior. سایت را بدون ضرورت به SPA تبدیل نکن. یک HTML مشترک دسکتاپ/موبایل. از `data-*` برای hook رفتار استفاده کن، کلاس CSS و متن قابل ترجمه را شناسه کنترل نکن.

## 2. Bootstrap و lifecycle

- یک entry point به صورت `type=module` یا script defer (براساس build)؛ `async` برای کد وابسته به ترتیب اجرا پیش‌فرض نیست. برای late/dynamic import از `document.readyState` و `DOMContentLoaded` در صورت نیاز استفاده کن؛ `window.load` فقط در صورت نیاز به منبع خاص، نه شرط نمایش صفحه.
- `mount(root) -> dispose()` برای featureهای قابل تغییر؛ mount دوباره idempotent؛ یک WeakMap/registry برای مالکیت. Dispose همان root؛ event listener (AbortController signal یا remove)، observer.disconnect، timers، rAF و درخواست لغو شوند. اگر import asynchronous است، پس از resolve زنده‌بودن root/نسخه کار را بررسی کن تا feature روی DOM حذف‌شده زنده نشود.
- `AbortSignal` یک‌بار مصرف است؛ controller جدید برای mount/درخواست جدید. `once:true` فقط برای event یک‌بار مصرف. callback ناشناس تکراری بدون cleanup نساز.
- Event delegation روی root پایدار وقتی آیتم‌ها پویا هستند؛ `closest()` و بررسی containment. listener scroll/touch/wheel هنگامی که لغو لازم نیست passive؛ `preventDefault` فقط برای رفتار شناخته‌شده و لازم. از document-level listener عمومی برای همه کلیک‌ها بدون ضرورت دوری کن.
- BFCache: `pageshow` با `event.persisted` برای بازیابی state/curtain طبق قرارداد لودینگ مسیر پروژه؛ re-init کورکورانه ممنوع. `pagehide` برای تعلیق/cleanup مناسب، بدون `unload` سراسری. اگر pagehide با persisted رخ داد، feature را بی‌دلیل destroy نکن؛ در صورت توقف منابع، resume متناظر داشته باش. `visibilitychange` برای توقف کار تزئینی در تب مخفی؛ reset ناگهانی UI ممنوع.

### نمونه ماژول کوچک (منوی disclosure، نه dialog/mega-menu کامل)

HTML پایه باید fallback قابل استفاده داشته باشد؛ در این مثال لینک‌ها در نبود JS باز و قابل مشاهده‌اند و دکمه کنترل با hidden پنهان است تا mount انجام شود. Escape، focus و طراحی منوهای پیچیده در سند Accessibility تکمیل می‌شوند.

```js
const mounted = new WeakMap();

export function mountDisclosure(root) {
  if (!root) return () => {};
  if (mounted.has(root)) return mounted.get(root);
  const button = root.querySelector('[data-disclosure-button]');
  const panel = root.querySelector('[data-disclosure-panel]');
  if (!button || !panel) return () => {};
  // HTML: native button, aria-controls points to panel's unique id.
  const events = new AbortController();
  const initial = {
    buttonHidden: button.hidden,
    panelHidden: panel.hidden,
    expanded: button.getAttribute('aria-expanded'),
  };
  const setOpen = (open) => {
    panel.hidden = !open;
    button.setAttribute('aria-expanded', String(open));
  };
  button.addEventListener('click', () => {
    setOpen(button.getAttribute('aria-expanded') !== 'true');
  }, { signal: events.signal });
  root.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !panel.hidden) {
      setOpen(false);
      button.focus();
    }
  }, { signal: events.signal });
  setOpen(false);
  button.hidden = false;

  const dispose = () => {
    if (mounted.get(root) !== dispose) return;
    events.abort();
    button.hidden = initial.buttonHidden;
    panel.hidden = initial.panelHidden;
    if (initial.expanded === null) button.removeAttribute('aria-expanded');
    else button.setAttribute('aria-expanded', initial.expanded);
    mounted.delete(root);
  };
  mounted.set(root, dispose);
  return dispose;
}
```

## 3. DOM، فرم و دسترس‌پذیری

- از عناصر native (`button`, `a href`, `input`, `label`, `details/summary`) استفاده کن؛ Div-clickable جایگزین button نیست. برای کاروسل و dialog کنترل keyboard، focus، Escape و نام قابل‌فهم؛ در modal focus trap و return focus. منوی ساده nav را بی‌دلیل نقش ARIA menu نده.
- برای داده متنی غیرقابل اعتماد از `textContent` روی عنصر متنی عادی استفاده کن؛ برای DOM از `createElement` و attributeهای کنترل‌شده؛ URLها validate/allowlist شوند. `innerHTML` با داده خام API/کاربر ممنوع؛ اگر HTML لازم است، از خروجی HTML escaped/sanitized با قرارداد trust مشخص یا sanitizer معتبر استفاده کن. `insertAdjacentHTML` و template string خام نیز همان ریسک را دارند.
- فرم با label، native constraints، پیام خطا قابل دسترس؛ اعتبارسنجی سمت client فقط UX است؛ validation و authorization سمت server الزامی. وضعیت `aria-busy` روی محدوده مربوط، اعلان status مناسب، پاسخ خطا و retry؛ focus را هنگام update جزئی بی‌دلیل نپراند.
- برای متن/داده پیچیده از `Intl` و استانداردهای locale استفاده کن؛ RTL با CSS logical properties و API متناسب با RTL؛ از محاسبه scrollLeft با فرض ثابت علامت/مبدأ بین مرورگرها اجتناب کن.
- هر قابلیت مهم باید با شبکه ضعیف/خطای JS fallback مناسب داشته باشد. محتوای اصلی/URLهای pagination/لینک‌ها واقعی و SSR باشند؛ enhancement AJAX نباید تنها مسیر رسیدن به محتوای indexable باشد.

## 4. API، AJAX، هم‌زمانی و امنیت

| نیاز | قاعده |
|---|---|
| fetch | بررسی `response.ok`، content type/schema طبق قرارداد، مدیریت خطای HTTP/شبکه/parse. |
| cancel | AbortController برای superseded request و dispose؛ timer timeout پاک شود. |
| race | latest-wins برای فیلتر جستجو؛ فقط درخواست فعال حق commit/clear busy دارد. |
| form mutation | جلوگیری از double submit، retry فقط در صورت idempotency طراحی‌شده؛ success واقعی server. |
| state | idle/loading/success/empty/error؛ مدیریت مستقل از motion و curtain. |
| URL | URL/URLSearchParams، History API و popstate هماهنگ با فیلتر قابل لینک؛ Back/Forward تست شود. |
| data | داده را قبل از مصرف validate؛ token/secret در bundle، log یا URL عمومی قرار نده. |

- درخواست جدید قدیمی را لغو می‌کند؛ abort کردن fetch به معنی توقف قطعی پردازش سمت server نیست. برای write حساس idempotency و کنترل سمت سرور ضروری است.
- timeout پیش‌فرض پیشنهادی برای لیست عمومی 10–15s، متناسب با عملیات؛ timeout/abort/HTTP error را جدا مدیریت کن. retry با backoff محدود فقط برای عملیات امن/idempotent و خطای مناسب؛ retry خودکار روی POST مالی/ثبت سفارش بدون تضمین idempotency ممنوع.
- پس از دریافت موفق، schema و شکل داده را کنترل کن؛ قبل از replace DOM، ماژول root قدیمی dispose، DOM امن replace و سپس root جدید mount. refresh ScrollTrigger در مالک motion؛ نه در هر fetch.
- لودینگ برای عملیات واقعی؛ Skeleton فقط داده نرسیده. محتوای SSR اولیه به skeleton تبدیل نشود. در filter سریع می‌توان نتایج قبلی را نگه داشت و pending/retry را اعلام کرد. محتوا و پیام خطا توسط motion پنهان نمانند.

### نمونه reusable latest-wins GET

این helper برای JSON عمومی same-origin است؛ UI و schema مال caller هستند. `onState` باید synchronous و بدون throw و reentrant call باشد؛ caller پس از dispose نتیجه را commit نکند. نتیجه فقط در callback state همان درخواست جاری commit می‌شود، نه از Promise قدیمی.

```js
export function createLatestGet(onState, timeoutMs = 12000) {
  let active = null;
  let disposed = false;

  async function run(url) {
    if (disposed) return;
    const target = new URL(url, location.href);
    if (target.origin !== location.origin) throw new Error('Unexpected origin');
    active?.abort();
    const controller = new AbortController();
    active = controller;
    const isCurrent = () => !disposed && active === controller;
    let timedOut = false;
    const timer = setTimeout(() => {
      timedOut = true;
      controller.abort();
    }, timeoutMs);
    try {
      onState({ status: 'loading' });
      const response = await fetch(target, {
        signal: controller.signal,
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
      });
      if (!response.ok) throw new Error(`HTTP ${response.status}`);
      const data = await response.json();
      if (isCurrent() && !controller.signal.aborted) {
        onState({ status: 'success', data });
      }
    } catch (error) {
      if (!isCurrent()) return;
      if (timedOut) onState({ status: 'error', reason: 'timeout' });
      else if (!controller.signal.aborted) {
        onState({ status: 'error', reason: 'request-failed' });
      }
    } finally {
      clearTimeout(timer);
      if (active === controller) active = null;
    }
  }

  function dispose() {
    disposed = true;
    active?.abort();
    active = null;
  }
  return { run, dispose };
}
```

برای endpoints احراز هویت‌شده، token/nonce و روش درخواست مطابق بخش 5 تنظیم شود؛ logger امن و پیام قابل‌فهم، empty state و schema validation به caller اضافه شوند. اگر callback می‌تواند throw کند، exceptionهای UI را در طبقه‌بندی جدا مدیریت کن. این نمونه قرارداد مکمل UI است، نه سیستم آماده همه انواع درخواست.

## 5. اتصال WordPress و Build (کد متعلق به تیم)

- PHP مسئول SSR، `wp_head()`، `wp_footer()` و enqueue استاندارد. `wp_enqueue_scripts` با `wp_enqueue_script()` و dependency/strategy مناسب؛ برای WP 6.3+ strategy `defer`/`async` طبق نیاز و dependency. برای ES module در نسخه سازگار (WP 6.5+) `wp_enqueue_script_module()` یا integration سازگار با manifest؛ نسخه هدف را بررسی کن. script را از چند محل دوباره inject نکن.
- Vite اختیاری: یک entry، manifest در production، hashed files و مسیر assets درست با root/subdirectory. CSS وابسته به imports طبق manifest اضافه شود؛ static imports لازم و dynamic imports واقعاً مشروط. Development server در prod نباشد. از نام hardcoded bundle یا enqueue همه chunkها جلوگیری کن. cache busting و انتشار اتمیک/حفظ asset‌های نسخه قبلی لحاظ شود.
- داده تنظیمات از PHP با `wp_json_encode` و escaping متناسب context یا data attribute امن ارسال شود؛ REST URL (از `rest_url()`)، AJAX URL (از `admin_url()` در صورت استفاده) و nonce به شکل مناسب. `/wp-json/` را برای همه نصب‌ها hardcode نکن؛ secrets سمت client ممنوع.
- REST endpoint اختصاصی روی `rest_api_init` با `register_rest_route`، schema/validation/sanitize و `permission_callback` درست. endpoint عمومی فقط اگر واقعاً عمومی است؛ در درخواست authenticated با cookie از `X-WP-Nonce` مطابق WordPress استفاده کن. nonce جای permission/capability check نیست. AJAX سنتی با action، nonce و capability مناسب؛ headers و status صحیح؛ escaping هنگام خروجی.
- GET عمومی قابل cache فقط بدون داده شخصی؛ response خصوصی cache عمومی نشود. protection سرور (CSRF/authorization/rate limit برحسب ریسک)، نه صرفاً disable button. برای درخواست unauthenticated حساس کنترل‌های ضد سوءاستفاده سمت server ضروری‌اند.

## 6. Performance و Responsive

- JS پایه کوچک و لازم، import شرطی براساس feature موجود در DOM و media/capability؛ lazy import غیرحیاتی قابل fail باشد. CSS responsive، نه دو DOM کامل. هر break point یک‌بار مدیریت شود، نه listener بی‌شمار resize.
- IntersectionObserver برای visibility/near-viewport؛ ResizeObserver برای ابعاد واقعی در حد نیاز با جلوگیری از feedback loop. `requestAnimationFrame` برای هماهنگی writeهای بصری و cancel در dispose؛ debounce/throttle مناسب برای درخواست یا رویداد پرتکرار؛ rAF به‌تنهایی جای debounce جستجو نیست.
- DOM query و layout read را خارج حلقه write جمع کن؛ avoid layout thrashing. از event delegation / به‌روزرسانی محدود DOM استفاده کن؛ `querySelectorAll('*')` برای نصب رفتار عمومی یا polling دائمی ممنوع.
- CPU-heavy work را تقسیم، و در صورت مناسب Web Worker / yielding استفاده کن. API جدید را برحسب browser support feature-detect کن. loop بی‌پایان در tab مخفی یا detached nodes ممنوع. `console.log` انبوه و payloads حساس در production حذف شوند.
- اهداف Core Web Vitals تجربه خوب (صدک 75 داده میدانی): LCP ≤2.5s، INP ≤200ms، CLS ≤0.1. long taskهای بیش از 50ms را در trace بررسی و کاهش بده؛ هیچ سقف JS ثابتی برای تمام پروژه‌ها وجود ندارد. اندازه bundle و تعداد درخواست را قبل/بعد ثبت کن.

## 7. قرارداد پذیرش و گزارش Codex

- [ ] پیش از تغییر inventory ماژول‌ها، entry points، وابستگی‌ها، hookهای PHP، مرورگرهای هدف و fallbackها؛ از بازنویسی نامرتبط خودداری کن.
- [ ] build، lint/format و تست مربوط به پروژه اجرا شوند؛ کد جدید syntax error و unhandled rejection نداشته باشد.
- [ ] mount/dispose تکراری، 10 بار filter سریع، abort، timeout، HTTP 4xx/5xx، JSON خراب، empty و retry، race و double click تست شوند.
- [ ] no-JS / JS error، slow network، cached/uncached navigation، BFCache، hash و back/forward، موبایل/دسکتاپ/RTL/keyboard تست شوند.
- [ ] API authorization، validation، CSRF و XSS payload تست شوند؛ داده حساس در console/HTML/bundle نباشد.
- [ ] DOM mutation فقط در محدوده feature؛ listener/observer/timer/GSAP قدیمی باقی نماند. اندازه bundle، request و Performance/Memory در صفحات واقعی مقایسه شود.
- [ ] در گزارش نهایی، فایل تغییرکرده، علت، مقایسه اندازه/عملکرد در صورت اندازه‌گیری، تست اجراشده و موارد باقی‌مانده ثبت شوند؛ نتیجه‌ای که اندازه‌گیری نشده «تأیید نشده» بنویس.

## منابع رسمی

- MDN: [Modules](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Modules)، [Dynamic import](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Operators/import)، [DOMContentLoaded](https://developer.mozilla.org/en-US/docs/Web/API/Document/DOMContentLoaded_event)، [addEventListener](https://developer.mozilla.org/en-US/docs/Web/API/EventTarget/addEventListener)، [Fetch](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch)، [AbortSignal](https://developer.mozilla.org/en-US/docs/Web/API/AbortSignal)، [innerHTML](https://developer.mozilla.org/en-US/docs/Web/API/Element/innerHTML).
- MDN: [pagehide](https://developer.mozilla.org/en-US/docs/Web/API/Window/pagehide_event)، [pageshow](https://developer.mozilla.org/en-US/docs/Web/API/Window/pageshow_event)، [requestAnimationFrame](https://developer.mozilla.org/en-US/docs/Web/API/Window/requestAnimationFrame).
- WordPress: [Including assets](https://developer.wordpress.org/themes/core-concepts/including-assets/)، [wp_enqueue_script](https://developer.wordpress.org/reference/functions/wp_enqueue_script/)، [REST endpoints](https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/)، [REST authentication](https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/)، [Escaping](https://developer.wordpress.org/apis/security/escaping/).
- Vite: [Backend integration](https://vite.dev/guide/backend-integration).
- web.dev: [Long tasks](https://web.dev/articles/optimize-long-tasks)، [Script evaluation/code splitting](https://web.dev/articles/script-evaluation-and-long-tasks)، [Layout thrashing](https://web.dev/articles/avoid-large-complex-layouts-and-layout-thrashing)، [BFCache](https://web.dev/articles/bfcache)، [Core Web Vitals](https://web.dev/articles/top-cwv)، [Web Vitals thresholds](https://web.dev/articles/vitals).
- Google: [JavaScript SEO](https://developers.google.com/search/docs/crawling-indexing/javascript/javascript-seo-basics)، [Pagination](https://developers.google.com/search/docs/specialty/ecommerce/pagination-and-incremental-page-loading).

**تاریخچه:** v1.0 — ایجاد سند مستقل Vanilla JS و نمونه‌های lifecycle/API. تست مرورگر و یکپارچگی پروژه قبل از استفاده تولیدی لازم است.
