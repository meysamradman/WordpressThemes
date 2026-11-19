# Design System & Reusable UI Components — v1.0

> بازبینی بسته 1.3 — 2026-11-19؛ نام مسیر برای سازگاری ارجاع‌های موجود حفظ شده است. تاریخچه در CHANGELOG-v1.3.md.

**تاریخ:** 2026-11-18 | **دامنه:** استاندارد مشترک تمام سایت‌های مشتریان؛ مستقل از برند و روش رندر. هدف: نقش‌های مرکزی، تغییر آسان، UI یکدست، ماژول‌های قابل استفاده مجدد و هزینه runtime پایین. CSS 02، responsive 03، JS 04، GSAP 05 و PHP 13 مسئول جزئیات خود هستند.

## 1. یک منبع حقیقت برای طراحی

- یک مالک برای مقادیر طراحی در هر پروژه: registry/token file یا ابزار مصوب پروژه. اگر در پنل مدیریت قابل ویرایش‌اند، CSS خروجی از همان داده تولید شود؛ نسخه دستی دوم مستقل نساز. یک source of truth به معنای یک فایل عظیم یا اجبار همه پروژه‌ها به JSON نیست.
- سه سطح: primitive (palette/scale)، semantic (surface/text/accent/border/action/danger/success)، component (button/card/input local variable). تغییر theme یا حالت فقط override نقش لازم. رنگ برند و فونت هر مشتری در Profile؛ مثال‌های زیر نمونه نه پالت اجباری.
- font-family و وزن، اندازه/line-height تیتر و بدنه، spacing، container/gutter، radius، border، shadow، z-index role، motion duration/easing و breakpointهای قرارداد ثبت شوند. برای breakpoints از CSS var در media query استفاده نکن. مقدار ثابت تکراری مرتبط با طراحی را به نقش درست منتقل کن، نه token کردن اجباری هر عدد محلی.

```css
/* نمونه آموزشی؛ مقادیر با طراحی واقعی پروژه جایگزین شوند. */
:root {
  --palette-ink: #18202a;
  --palette-paper: #fff;
  --palette-accent: #214fa0;
  --palette-accent-hover: #193e7d;
  --color-text: var(--palette-ink);
  --color-surface: var(--palette-paper);
  --color-action: var(--palette-accent);
  --color-action-hover: var(--palette-accent-hover);
  --color-on-action: var(--palette-paper);
  --color-focus: var(--palette-accent);
  --color-border: #c9cdd4;
  --color-skeleton: #e3e5e9;
  --font-body: system-ui, sans-serif;
  --font-heading: var(--font-body);
  --text-body: 1rem;
  --line-body: 1.7;
  --space-2: .5rem;
  --space-3: .75rem;
  --space-4: 1rem;
  --radius-control: .5rem;
  --radius-card: .75rem;
  --container-max: 75rem;
  --gutter: clamp(1rem, 3vw, 2rem);
  --duration-fast: 180ms;
  --ease-ui: ease-out;
}
```

- `@font-face` برای هر face/weight/style لازم تنها یک بار در registry CSS؛ WOFF2 در صورت پشتیبانی و مجوز، فایل محلی در صورت نیاز/مجوز، variable font فقط اگر واقعاً سبک‌تر/مناسب‌تر است. range وزن با فایل واقعی منطبق باشد. `font-display:swap` یا `optional` بر اساس خوانایی/CLS؛ preload محدود به face ضروری واقعاً مصرف‌شده با `as=font`, type و `crossorigin` مناسب. از دانلود همه وزن‌ها و فونت‌های صفحه‌ساز/پروژه تکراری پرهیز. فارسی: شکل‌دهی حروف، ZWNJ، اعراب، اعداد و متن لاتین؛ subset نباید حروف لازم را حذف کند. fallback نزدیک از نظر metrics، آزمون CLS. دارایی دارای مجوز؛ مسیر فونت و نام برند واقعی در پروژه.
- نقش تایپوگرافی از سطح heading جدا: H2 یک کارت ممکن است اندازه کوچک‌تر از H2 سکشن داشته باشد؛ semantic tag بر اساس جایگاه محتوا و style از variant/token. نه override عمومی h2 فقط برای یک کارت، نه چند CSS ثابت برای رنگ/فونت مشترک.

## 2. قرارداد کامپوننت (قبل از ساخت)

| جزء | قرارداد |
|---|---|
| Renderer/markup | یک الگوی پایه برای یک خانواده واقعی؛ داده، variant، heading level، media، accessible name ورودی |
| CSS | base + modifier + token محلی؛ صفحه صرفاً layout یا استثنای scope‌شده |
| JS | در صورت نیاز، mount(root) و dispose؛ state هر instance مستقل؛ data hook با نام ثابت |
| States | default، hover (در صورت کاربرد)، focus-visible، disabled، loading، error/empty و selected فقط در صورت ارتباط |
| Asset | نیاز هر خانواده ثبت؛ بارگذاری مشترک یا شرطی طبق مصرف واقعی، تنها یک runtime/library |
| Test | چند نمونه هم‌زمان، RTL/LTR، responsive، keyboard، no-JS fallback مربوط، AJAX remount و حذف instance |

- یک موتور مشترک برای رفتار مشترک واقعی؛ variant به جای copy/paste. در عین حال کارت محصول، مقاله و پلیر را بی‌دلیل در یک renderer عظیم با ده‌ها flag ادغام نکن. اگر قرارداد معنایی/رفتار واقعاً متفاوت است، ترکیب primitives یا family مستقل بهتر است.
- **Button/Link:** link برای navigation با `href` واقعی، button برای action با type صریح؛ label قابل خواندن، icon تزئینی `aria-hidden`، focus واضح. حالات primary/secondary/outline/text، size و disabled/loading بر حسب نیاز. prevent double submit و مدیریت حالت با 04؛ موفقیت فقط بعد از پاسخ واقعی. CSS مشترک بدون JS برای دکمه ساده.
- <a id="ui-card"></a> **Card:** سطح، border، radius، فاصله‌ها و قاب رسانه از CSS پایهٔ خانواده و tokenهای موجود خوانده شوند. آرشیو، Grid و کاروسل چیدمان والد را تعیین می‌کنند و داده، نسبت رسانه، سطح heading و variant را به renderer همان خانواده می‌دهند. لینک تو‌در‌تو ایجاد نشود. hover/zoom با مالک قاب رسانه و فقط برای ورودی مناسب، focus با کیبورد و پخش با اقدام کاربر بررسی شوند. کارت فقط در گروه‌های تأییدشده وارد [افکت ورود](05-GSAP-ANIMATION-STANDARD-v1.1.md#customer-content-entrance) می‌شود.
- <a id="ui-input"></a> **Input و Form:** در هر خانواده از ساختار و CSS مشترک برای label، کنترل، hint/error، border، اندازه، فاصله و حالت‌های focus/invalid/disabled استفاده شود. تفاوت جست‌وجو یا فرم با variant واقعی و محدود اعمال شود. Input/select/textarea/checkbox/radio بومی، id یکتا، autocomplete مناسب و ارتباط راهنما/خطا با کنترل حفظ شوند. اعتبارسنجی سرور و escape خروجی مالک جدا دارند. JS فقط برای رفتار لازم مثل autocomplete یا جست‌وجوی AJAX بارگیری شود و جست‌وجوی بومی بدون JS قابل استفاده باشد.
- **Skeleton:** primitive نمایشی مشترک و variant هندسی مخصوص card/list/media. در دریافت واقعی داده (`aria-busy` روی region، وضعیت خوانا در صورت نیاز)، skeleton تزئینی `aria-hidden=true` و غیرقابل focus؛ ابعاد مشابه خروجی، cleanup در error/abort/empty/success. برای SSR واقعی یا تصویر ساده، متن/کارت واقعی را پنهان نکن؛ جای تصویر و aspect ratio را از ابتدا رزرو کن. shimmer ساده، در reduced motion ثابت. پرده و Skeleton دو مالک مستقل؛ زمان‌بندی طبق قرارداد Loading مسیر پروژه.
- **Modal / Accordion / Tabs / Navigation / Carousel:** الگوهای WAI-ARIA APG و عناصر native مناسب (مثلاً dialog در صورت تناسب). focus/return focus، Escape، keyboard، state و cleanup طبق قابلیت. یک scroll-lock owner، نه چند سیستم موازی. carousel سبک/manual و swipe؛ CSS scroll-snap برای ساده، کتابخانه/GSAP فقط در صورت واقعی نیاز. GSAP فقط motion، نه state/شبکه.
- **Icons/Badge/Status/Empty/Error/Pagination:** مجموعه icon محلی/در دسترس و نام قابل فهم؛ از icon font یا کل library برای چند آیکون اجتناب. رنگ تنها حامل وضعیت نباشد. pagination لینک واقعی، error/retry واقعی و empty مشخص. reusable spinner نه loader جدا برای هر کارت.

```css
/* یک پایه برای link/button؛ modifier فقط متغیر موردنیاز را عوض می‌کند */
.ui-button {
  --btn-bg: var(--color-action);
  --btn-fg: var(--color-on-action);
  --btn-hover: var(--color-action-hover);
  --btn-hover-fg: var(--color-on-action);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  min-block-size: 44px;
  padding: var(--space-2) var(--space-4);
  border: 1px solid transparent;
  border-radius: var(--radius-control);
  background: var(--btn-bg);
  color: var(--btn-fg);
  font: inherit;
  text-decoration: none;
}
.ui-button--outline {
  --btn-bg: transparent;
  --btn-fg: var(--color-action);
  border-color: var(--color-action);
}
@media (hover: hover) and (pointer: fine) {
  .ui-button:not(:disabled):hover { background: var(--btn-hover); color: var(--btn-hover-fg); }
}
.ui-button:focus-visible { outline: 2px solid var(--color-focus); outline-offset: 3px; }
.ui-button:disabled { opacity: .6; cursor: not-allowed; }
.ui-skeleton { background: var(--color-skeleton); border-radius: var(--radius-card); }
.ui-skeleton--media { aspect-ratio: 16 / 9; }
```

```html
<a class="ui-button ui-button--outline" href="/portfolio/">مشاهده نمونه‌کارها</a>
<button class="ui-button" type="submit">ثبت درخواست</button>
```

این کد فقط حداقل نمونه است؛ رنگ/contrast، loading state دکمه و disabled interaction باید در پروژه آزموده شوند. برای لینک غیرفعال از semantics صحیح استفاده شود؛ `aria-disabled` به‌تنهایی جلوی action را نمی‌گیرد.

## 3. منبع ماژولار، خروجی کم‌حجم

- برای هر family، CSS و JS مالک مشخص و markup reusable؛ feature صفحه composition انجام دهد، نه تعریف نسخه دیگر همان button/card. یک registry ساده شامل نام، file owner، variants، assets، consumers و تست‌ها نگه دار؛ ساخت پنل/کتابخانه عظیم اجباری نیست.
- JS پایه فقط bootstrap و رفتار عمومی ضروری؛ import شرطی featureها براساس حضور root و نیاز. CSS ضروری SSR پیشاپیش؛ CSS dependency ماژول async طبق build. چند instance = asset واحد + state مستقل، نه چند بار inject CSS/JS. `WeakMap`/registry و disposer طبق 04. در صورت تغییر AJAX فقط root عوض‌شده و refresh لازم؛ نه init کل سایت.
- فایل‌های source کوچک متناسب با مسئولیت؛ bundleهای production بر اساس نیاز و cache. یک کتابخانه جدید برای button/skeleton ساده لازم نیست. CSS/JS مدیریت یا preview به frontend عادی وارد نشود، و بالعکس. Critical font/style/image فقط به اندازه مصرف واقعی.
- هنگام تغییر یک token یا خانواده، همه مصرف‌کنندگان حساس شناسایی و regression test شوند. از کپی فایل کامل و استفاده از کلاس‌های صفحه برای تغییر global خودداری کن. مستند component به اندازه API عملی؛ مقادیر و تصمیمات پروژه در Profile/Scenario.

## 4. معیار پذیرش

- [ ] رنگ و typography از منبع واحد، face/weights و فایل‌های font در Network بی‌تکرار و لازم؛ CLS بررسی شده.
- [ ] دکمه، کارت، فرم، skeleton در چند صفحه از family واقعی موجود استفاده می‌کنند؛ variant و data مستقل؛ duplicate ID و nested interactive ندارند.
- [ ] در چند instance/AJAX، listener/trigger/request/scroll-lock تکراری ایجاد نمی‌شود؛ absence feature مانع دانلود asset غیرضروری است، مگر bundle مشترکِ اندازه‌گیری‌شده.
- [ ] accessibility (keyboard، focus، contrast، reduced-motion)، RTL/LTR، responsive، data long/empty/error/loading و no-JS مناسب تست شده.
- [ ] build/lint، Network/Coverage و مقایسه bytes و عملکرد قبل/بعد؛ همه نتایج اجرا نشده Not run. هیچ ادعای سرعت صرفاً به‌دلیل ماژولار بودن.

## منابع

- [CSS Custom Properties — MDN](https://developer.mozilla.org/en-US/docs/Web/CSS/Guides/Cascading_variables/Using_custom_properties)
- [Design Tokens Community Group Format 2025.10](https://www.w3.org/community/reports/design-tokens/CG-FINAL-format-20251028/) — گزارش Community Group برای تبادل داده، نه الزام W3C Recommendation و نه الزام استفاده از JSON.
- [web.dev font best practices](https://web.dev/articles/font-best-practices)، [Optimize web fonts](https://web.dev/learn/performance/optimize-web-fonts)، [Code-split JS](https://web.dev/learn/performance/code-split-javascript)
- [WAI APG patterns](https://www.w3.org/WAI/ARIA/apg/patterns/)، [WCAG 2.2](https://www.w3.org/TR/WCAG22/)

مبنای داخلی: radman-doc/engineering/typography.md، components.md، component-library.md، markup-css.md و assets.md؛ اصول مالکیت و reuse استخراج شده‌اند، نام‌ها و نسخه‌های محصول قبلی منتقل نشده‌اند.

**ارجاع:** کامپوننت تصویر/کاور و placeholder از یک قرارداد نقش و اندازه در [17](17-IMAGES-AND-MEDIA-v1.0.md) پیروی کنند. انتخاب renderer موجود و بررسی حذف کد در [19](19-CODE-QUALITY-REFACTORING-v1.0.md).
