# Modern CSS & Minimal DOM — v1.0

**تاریخ:** 2026-11-18 | **دامنه:** مشترک؛ CSS و HTML متعلق به تیم در پروژه‌های مشتریان. مستقل از برند و فناوری رندر. این سند روش کدنویسی است، نه پالت، فونت یا breakpoint ثابت برای همه مشتری‌ها.

**MUST** = قانون داخلی تیم؛ **SHOULD** = پیش‌فرض قابل تغییر با دلیل. سند 16 مالک Design System؛ 03 مالک responsive؛ 04 رفتار؛ 05 حرکت. CSS مسئول چیدمان و ظاهر است، نه دریافت داده یا مدیریت وضعیت business.

## 1. معماری و مالکیت فایل

- MUST ابتدا استایل و کامپوننت موجود را پیدا کن. برای تغییر یک دکمه یا کارت، کلاس هم‌وظیفه و فایل موازی نساز؛ variant یا token محلی کافی است. از حذف استایل لازم برای کاهش تعداد خطوط خودداری کن.
- لایه‌های tokens، base/reset، layout، components، features/pages و utilities مستقل باشند. مسیرها نمونه‌اند؛ ساختار سالم فعلی را به‌زور تغییر نده.

```text
src/css/
  tokens.css
  base.css
  layout.css
  components/
    button.css
    card.css
    form.css
    skeleton.css
  features/
    carousel.css
    archive.css
  pages/
    home.css
  utilities.css
```

- `@layer` اختیاری؛ در صورت استفاده ترتیب از ابتدا مشخص شود، مانند `@layer reset, tokens, base, layout, components, features, utilities;`. قواعد عادی بدون layer بر قواعد عادی لایه‌دار مقدم‌اند؛ لایه‌گذاری بدون ممیزی CSS شخص ثالث انجام نشود. `!important` فقط استثنای مستند و محدود.
- کلاس کوتاه، قابل فهم و یکدست؛ BEM یا قرارداد هم‌ارز. از ID selector برای استایل، specificity بالا، nesting عمیق و selector وابسته به ساختار تصادفی پرهیز شود. JS با `data-*` hook، نه کلاس purely visual؛ CSS با کلاس یا state مثل `[aria-expanded="true"]`. CSS فرانت و مدیریت/ویرایشگر مستقل.
- مقادیر ثابت در CSS و token؛ inline style تنها برای مقدار واقعاً پویا و اعتبارسنجی‌شده مثل عرض پیشرفت یا token محلی. style attribute تکراری و `@font-face` تکراری ممنوع. CSS سراسری برای ویژگی فقط یک صفحه ممنوع مگر هزینه/اشتراک اندازه‌گیری‌شده توجیه کند.

## 2. DOM و چیدمان

| ابزار | جای مناسب | جای نامناسب |
|---|---|---|
| Flexbox | منو، دکمه و آیکون، یک ردیف یا ستون، داخل کارت | هم‌تراز کردن شبکه واقعی در دو محور وقتی Grid مناسب‌تر است |
| Grid | گالری، کارت‌های محصول، ستون‌های محتوا و sidebar | یک ردیف ساده که Flex کافی است |
| Flow / block | متن، مقاله و فرم ساده | ساخت wrapper اضافه فقط برای فاصله |
| absolute/fixed | overlay، badge، dialog یا تزئین واقعی | جایگزین layout عادی و ریسپانسیو |
| sticky | هدر یا بخش لازم با محدوده مشخص | pin طولانی روی موبایل بدون تست |

- هر node دلیل معنایی، چیدمان، تعامل، دسترس‌پذیری یا افکت مشخص داشته باشد. wrapper فقط برای gap بی‌دلیل نساز. حذف کور wrapperهای ساختاری، hookهای JS یا semantics ممنوع. تگ‌های معنایی 01 را حفظ کن؛ `span` جای heading و `div` جای button/link نیست.
- `gap` برای فاصله فرزندان، padding برای فضای داخلی، margin برای فاصله مستقل. از چند منبع هم‌زمان فاصله (مثل gap + margin تکراری) اجتناب کن. ابعاد طبیعی محتوا، `minmax(0, 1fr)`، `min-inline-size: 0` برای عضوهای لازم؛ overflow مخفی سراسری درمان layout نیست.
- از `height` ثابت برای محتوای ناشناخته یا متن چندزبانه اجتناب؛ `min-block-size` فقط با هدف. `aspect-ratio` و ابعاد ذاتی تصویر برای جلوگیری از layout shift، `object-fit` فقط برای crop موردنیاز. `position:relative` و z-index تنها در scope لازم، stacking context ناشی از transform را بررسی کن.
- ترتیب DOM باید با ترتیب خواندن و Tab هماهنگ باشد. `order` و grid-placement جای تغییر ترتیب معنایی نیستند. از transform/JS برای layout معمولی پرهیز کن.

```css
/* نمونه عمومی؛ رنگ و spacing از سند 16 و توکن‌های پروژه */
.layout-container { max-inline-size: var(--container-max); margin-inline: auto; padding-inline: var(--gutter); }
.card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(min(100%, 18rem), 1fr));
  gap: var(--space-4);
}
.card-grid > * { min-inline-size: 0; }
.action-row { display: flex; flex-wrap: wrap; align-items: center; gap: var(--space-2); }
```

## 3. RTL و CSS مدرن

- `margin-inline`, `padding-inline`, `inset-inline-start`, `border-inline-start`, `text-align:start` را برای جهت منطقی ترجیح بده. جهت از HTML (`dir`)؛ فقط آیکون جهت‌دار لازم mirror شود؛ لوگو و تصاویر عمومی نه. محتوای عدد/لاتین با جهت مناسب و `bdi` در جای لازم.
- `clamp()` برای اندازه سیالِ قابل zoom، rem برای تایپوگرافی و spacing، درصد/واحد مناسب برای layout؛ استفاده مکانیکی یا مقادیر حدسی ممنوع. `var()` در property value است، نه شرط `@media`؛ breakpoint را در قرارداد یا ابزار build واحد نگه دار.
- Container Query برای کامپوننتی که به فضای خودش پاسخ می‌دهد، Media Query برای viewport/capability. ویژگی‌های مدرن با browser matrix و fallback لازم؛ `@supports` در صورت کاربرد. CSS Grid و Flex مکمل هم هستند.
- در CSS پایه focus-visible واضح، hover فقط برای دستگاه مناسب، `prefers-reduced-motion` برای حرکت؛ contrast و stateها از 16. outline بدون جایگزین حذف نشود؛ لینک متن مقاله باید از متن عادی قابل تشخیص بماند.

## 4. ماژولار بدون کاهش سرعت

- سورس ماژولار، تحویل browser بهینه: CSS پایه کوچک و مشترک cacheable؛ گروه componentهای واقعاً پرمصرف مشترک؛ asset صفحه یا feature نادر جدا و فقط مصرف‌کننده آن را بارگذاری کند. نه bundle عظیم سراسری، نه صدها فایل ریز اجباری. تعداد درخواست، bytes، cache و critical path را اندازه بگیر.
- SSR markup مهم قبل از حرکت باید استایل لازم را داشته باشد؛ CSS بالای صفحه را تا بعد از JS موکول نکن. از زنجیره `@import` در runtime اجتناب (import در build متفاوت است). Minify و compression در production؛ سورس خوانا و dist تولیدی، تغییر دستی ممنوع.
- CSS حذف‌شده را با DevTools Coverage در چند نوع صفحه، حالت‌های باز/بسته، responsive، AJAX و ویرایش محتوا بررسی کن. unused در یک trace لزوماً مرده نیست. کلاس‌های پویا/قالب سرور در purge safelist یا scan درست پوشش داده شوند. Library جدید برای چند utility ساده اضافه نشود.
- CSS transition سبک برای افکت ساده؛ GSAP طبق 05 برای choreography. از تغییر پیوسته layout و blur/shadow سنگین، `will-change` سراسری، انیمیشن نامرئی دائمی اجتناب کن. Layout shift و paint/CPU در Performance trace بررسی شود.

## 5. معیار پذیرش Codex

- [ ] تکرار token، font-face، selector هم‌وظیفه، DOM wrapper و فایل مشترک قبل/بعد ثبت و بررسی شده؛ رفتار/طراحی مصوب حفظ شده.
- [ ] RTL/LTR، متن طولانی، keyboard/focus، zoom، mobile/desktop، loading/error/empty و reduced-motion بررسی شده.
- [ ] در Network هیچ CSS/JS نامرتبط یا نسخه دوم library بدون دلیل دریافت نمی‌شود؛ CSS ضروری بدون FOUC؛ build و lint واقعی اجرا شده یا Not run.
- [ ] با همان محیطِ قبل/بعد، bytes انتقالی، request count، layout/paint و LCP/INP/CLS مناسب بررسی شوند. کم‌کردن کلاس یا تعداد فایل به‌تنهایی اثبات افزایش سرعت نیست.

## منابع

- MDN — [Cascade layers](https://developer.mozilla.org/en-US/docs/Learn_web_development/Core/Styling_basics/Cascade_layers)، [CSS variables](https://developer.mozilla.org/en-US/docs/Web/CSS/Guides/Cascading_variables/Using_custom_properties)، [Flex vs Grid](https://developer.mozilla.org/en-US/docs/Web/CSS/Guides/Grid_layout/Relationship_with_other_layout_methods)، [Grid accessibility](https://developer.mozilla.org/en-US/docs/Web/CSS/Guides/Grid_layout/Accessibility)، [Logical properties](https://developer.mozilla.org/en-US/docs/Web/CSS/Guides/Logical_properties_and_values)، [Container queries](https://developer.mozilla.org/en-US/docs/Web/CSS/Guides/Containment/Container_queries).
- [MDN CSS performance](https://developer.mozilla.org/en-US/docs/Learn_web_development/Extensions/Performance/CSS)، [web.dev resource loading](https://web.dev/learn/performance/optimize-resource-loading)، [Vite features](https://vite.dev/guide/features.html).

مبنای قواعد داخلی: radman-doc/engineering/markup-css.md، assets.md و naming.md (اصول عمومی استخراج شده؛ تصمیمات هویتی و API محصول قبلی تعمیم داده نشده‌اند).
