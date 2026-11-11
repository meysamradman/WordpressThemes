# سند معماری و نیازمندی‌های Webtalik Framework

> **آرشیو تاریخی؛ جایگزین‌شده:** این متن مرجع تصمیم جاری نیست. تصمیم قطعی ۲۰۲۶-۱۰-۱۸ در [معماری نهایی](../../engineering/architecture.md) بر تمام وضعیت‌ها و پیشنهادهای این فایل مقدم است. متن قدیمی فقط برای سابقه حفظ شده است.

> **نسخه:** `0.1.0-draft`  
> **وضعیت:** پیش‌نویس برای بررسی و تکمیل با Codex؛ هنوز دستور شروع پیاده‌سازی نیست.  
> **زبان پیش‌فرض محصولات:** فارسی، راست‌به‌چپ (RTL)؛ معماری باید برای ترجمه به زبان‌های دیگر آماده باشد.  
> **کاربرد:** توسعه یک اکوسیستم قالب/افزونه وردپرس قابل‌استفاده مجدد برای پروژه‌های مختلف مشتریان وبتالیک.

---

## 1. چشم‌انداز محصول

هدف ساخت **یک پایه حرفه‌ای، ماژولار، سریع و قابل‌گسترش وردپرس** است که برای طراحی سایت‌های شرکتی، خدماتی، نمونه‌کار، کاتالوگ محصولات بدون فروش آنلاین و در صورت نیاز فروشگاه به کار برود.

این پروژه **کپی قالب HUB، WoodMart یا BeTheme نیست**؛ از تجربه و قابلیت‌های مفید آن‌ها، به‌خصوص Header/Footer Builder، برای طراحی یک پیاده‌سازی مستقل الهام می‌گیرد.

### اهداف اصلی

1. **سرعت و خروجی تمیز:** HTML سمت سرور، CSS سبک، JS شرطی و کمترین وابستگی اجباری.
2. **قابلیت استفاده مجدد:** یک هسته ثابت برای مشتری‌های مختلف با تنظیمات متفاوت.
3. **هدر و فوتر بصری و منعطف:** Builder اختصاصی شبیه رویکرد WoodMart/BeTheme، بدون نیاز اجباری به Elementor.
4. **کاتالوگ داخلی بدون JetEngine:** امکان محصولات غیرقابل‌خرید همراه با دسته‌بندی، مشخصات، گالری، جستجو و استعلام.
5. **صفحه‌سازی استاندارد:** تکیه بر Gutenberg و بلوک‌های اختصاصی؛ Elementor صرفاً در صورت نیاز.
6. **مدیریت مدرن:** استفاده از React برای پنل‌های مدیریتی و ابزارهای Drag & Drop؛ نه لزوماً کل Frontend.
7. **Self-hosted Frontend:** فونت، SVG، CSS، JS، کتابخانه‌ها، تصاویر و مدل‌ها از هاست همان سایت بارگذاری شوند؛ بدون CDN اجباری.
8. **سازگاری افزونه‌ای:** کار با افزونه‌های متداول وردپرس بدون دخالت غیرضروری در CSS، JS و API آن‌ها.
9. **قابل‌نگهداری و آپدیت:** نسخه‌بندی، مهاجرت تنظیمات، تست و جداسازی سفارشی‌سازی هر مشتری از هسته.

### خارج از محدوده نسخه اولیه (Non-goals)

- ساخت یک جایگزین کامل Elementor با تمام ویجت‌ها و امکاناتش.
- بازسازی تمام قابلیت‌های JetEngine یا WooCommerce از صفر.
- تبدیل همه صفحات به React SPA یا پیاده‌سازی Headless/Next.js به‌عنوان پیش‌فرض.
- عرضه قالب با صدها دمو و امکانات غیرضروری از روز اول.
- کپی یا بازتوزیع سورس و دارایی‌های اختصاصی قالب‌ها یا افزونه‌های ثالث.

---

## 2. دسته‌بندی سایت‌های هدف

| نوع پروژه | امکانات فعال | افزونه‌های اجباری |
|---|---|---|
| شرکتی / خدماتی | صفحات، خدمات، فرم تماس، نمونه‌کار | هیچ صفحه‌ساز ثالثی اجباری نباشد |
| نمونه‌کار / آژانس | پروژه‌ها، دسته‌بندی، جزئیات پروژه | بدون JetEngine اجباری |
| کاتالوگ بدون فروش | محصول، برند، ویژگی، گالری، PDF، فیلتر، استعلام قیمت | بدون WooCommerce و JetEngine اجباری |
| فروشگاهی | نمایش و خرید محصول، حساب مشتری، سبد و پرداخت | WooCommerce، فقط در سایت‌های فروشگاهی |
| محتوایی / وبلاگی | نوشته‌ها، دسته‌ها، آرشیوها | افزونه‌های محتوایی اختیاری |

**اصل طراحی:** هر قابلیت فقط زمانی بارگذاری شود که فعال و موردنیاز است. غیرفعال‌بودن یک ماژول باید واقعاً هزینه پردازشی و فایل‌های آن را حذف کند.

---

## 3. تصمیم معماری اولیه

### 3.1 هسته قالب: Classic/Hybrid WordPress Theme

**انتخاب اولیه، مشروط به آزمایش:** قالب PHP کلاسیک/هیبرید با `theme.json`، خروجی SSR و پشتیبانی کامل از Gutenberg برای محتوای صفحات.

دلایل:
- کنترل مستقیم روی Header/Footer Renderer و قالب‌های صفحات.
- امکان پیاده‌سازی Builder سفارشی مستقل از Elementor.
- انعطاف برای هماهنگی با افزونه‌های متداول و خروجی PHP.
- امکان استفاده از `theme.json` برای رنگ‌ها، تایپوگرافی و تنظیمات بلوک‌ها.

> **نکته:** داشتن `theme.json` در قالب Classic به‌معنای فعال‌بودن تمام قابلیت‌های Full Site Editing نیست. انتخاب نهایی Classic/Hybrid در مقابل Block Theme باید با یک نمونه فنی (PoC) تأیید شود.

### 3.2 تقسیم مسئولیت‌ها

**Theme**: رندر صفحات، Template Parts، Design Tokens، Header/Footer، استایل و الگوهای ظاهری.  
**Core Plugin**: داده‌ها و قابلیت‌هایی که با تعویض قالب نباید از بین بروند؛ شامل CPT، Taxonomy، فیلدهای ساختاریافته و منطق کاتالوگ.  
**Optional Integrations**: اتصال به Elementor و WooCommerce، بدون نیاز اجباری به نصب آن‌ها.

### 3.3 React کجا استفاده شود؟

| محل | روش ترجیحی |
|---|---|
| Header/Footer Builder در مدیریت | React + WordPress Components + کتابخانه Drag & Drop محلی |
| تنظیمات قالب و مدیریت فیلدهای کاتالوگ | React در Admin در صورت نیاز |
| صفحات عمومی سایت | PHP → HTML سمت سرور + CSS |
| بخش‌های تعاملی خاص | Vanilla JS / GSAP و در صورت نیاز React جزیره‌ای (island) |
| ویرایش محتوای صفحات | Gutenberg + بلوک‌های اختصاصی |

**قانون:** نسخه دوم React یا کتابخانه‌های تکراری را بدون نیاز به Frontend تحمیل نکنیم. در پنل وردپرس حتی‌الامکان از پکیج‌های رسمی `@wordpress/*` و وابستگی‌های موجود در مدیریت استفاده شود.

---

## 4. Webtalik Header Builder (اولویت اصلی)

هدف: تجربه‌ای نزدیک به Header Builder قالب‌های حرفه‌ای، اما مستقل، سریع و مناسب انواع مشتری.

### امکانات MVP

- ساخت، نام‌گذاری، ذخیره، Duplicate و حذف چند هدر.
- سه ناحیه قابل‌تنظیم: `Top Bar`، `Main Header` و `Bottom Bar`.
- در هر ردیف: ستون‌های قابل‌افزودن/تغییر عرض، Drop Zone و مرتب‌سازی Drag & Drop.
- کامپوننت‌های پایه: Logo، WordPress Menu، CTA Button، Text، Icon/Social Links، Search و Menu Toggle.
- تنظیمات ردیف و عنصر: پس‌زمینه، رنگ، Border، Radius، Shadow سبک، ارتفاع، Padding/Gap، Alignment، Typography.
- هدر اختصاصی Desktop و Mobile؛ Tablet حداقل با قوانین Responsive مشخص.
- هدر Sticky، شفاف (Overlay)، حالت عادی، تغییر ظاهر بعد از اسکرول به‌صورت اختیاری.
- منوی موبایل Drawer/Off-canvas با عملکرد صحیح لمس و کیبورد.
- پیش‌نمایش زنده و Undo/Redo در صورت امکان بعد از MVP پایه.
- تخصیص هدر به «کل سایت»، نوع محتوا، گروه صفحات و یک صفحه خاص با اولویت‌بندی مشخص.
- Import/Export تنظیمات هدر با نسخه Schema.
- شرایط ناسازگار یا عنصر غیرموجود باید Fallback مشخص داشته باشند.

### امکانات مراحل بعدی

- مگامنو، هدر چندردیفه پیشرفته، جستجوی Ajax و جستجوی کاتالوگ.
- سبد خرید و حساب کاربری فقط وقتی WooCommerce فعال است.
- Presetهای متنوع و کتابخانه طرح‌های آماده.
- قوانین شرطی پیشرفته برای کاربران واردشده، دسته‌ها و صفحات خاص.
- انیمیشن‌های ظریف GSAP به‌صورت اختیاری و قابل‌خاموش‌کردن.

### مدل اجرا و ذخیره

1. **مدیریت:** React Drag & Drop و کنترل‌های استایل.
2. **داده:** Schema نسخه‌دار، ساختاریافته و اعتبارسنجی‌شده؛ نه HTML دلخواهِ بدون کنترل.
3. **رندر عمومی:** PHP Components با HTML معنایی؛ فقط CSS/JS همان هدر فعال بارگذاری شود.
4. **استایل‌ها:** CSS scoped و بهینه؛ ترجیحاً Style Generation همراه کش و عدم تولید مجدد در هر Request.
5. **اولویت تخصیص:** صفحه خاص → نوع محتوا/قاعده تعریف‌شده → هدر پیش‌فرض (ترتیب نهایی قابل بررسی).

### نمونه Schema مفهومی (قرارداد نهایی نیست)

```json
{
  "schemaVersion": 1,
  "id": "header-corporate",
  "mode": "desktop",
  "rows": [
    {
      "id": "main",
      "columns": [
        {"id": "right", "width": 3, "items": [{"type": "logo", "props": {}}]},
        {"id": "center", "width": 6, "items": [{"type": "menu", "props": {"location": "primary"}}]},
        {"id": "left", "width": 3, "items": [{"type": "button", "props": {"label": "مشاوره"}}]}
      ]
    }
  ]
}
```

> ذخیره‌سازی دقیق (CPT و Post Meta در برابر جدول/Option اختصاصی)، قرارداد React/PHP و مدیریت Migration پیش از پیاده‌سازی باید نهایی شود.

---

## 5. Webtalik Footer Builder

- ساخت و نگهداری چند فوتر با امکان Assign به صفحات/سایت.
- ردیف و ستون‌های Responsive با Drag & Drop.
- اجزای لوگو، توضیح شرکت، فهرست‌ها، اطلاعات تماس، شبکه‌های اجتماعی، کپی‌رایت، تصویر/آیکون و فرم (از طریق ادغام امن).
- تنظیمات مشابه هدر: Typography، Spacing، Background، Border، Responsive و Live Preview.
- Template Library و Import/Export نسخه‌دار.
- خروجی PHP/HTML؛ هیچ Runtime صفحه‌ساز سنگینی در فرانت‌اند الزامی نباشد.

---

## 6. Webtalik Catalog بدون JetEngine

**هدف:** مدیریت محصولاتِ بدون فرایند خرید، برای شرکت‌های صنعتی، مبلمان، تجهیزات، خدمات محصول‌محور و موارد مشابه.

### امکانات نسخه اولیه

- `Custom Post Type` مخصوص محصول کاتالوگی (نام/Slug نهایی باید ثابت و مستند شود).
- Taxonomyهای دسته‌بندی محصول، برند و برچسب اختیاری.
- تصویر شاخص، گالری، توضیح کوتاه و کامل، فایل PDF/بروشور، وضعیت نمایش و مدل/کد محصول.
- ساخت «گروه فیلدهای مشخصات» متناسب با دسته یا نوع محصول (مثلاً ولتاژ برای تجهیزات و ابعاد برای مبلمان).
- Field Types مرحله اول: Text، Textarea، Number، Select، Checkbox، Image، File، URL؛ انواع پیچیده بعداً.
- Validation، Sanitization، Permissions، REST Schema و نمایش ساختاریافته در Admin.
- صفحه آرشیو و Single Product با Layout قابل‌تنظیم و بلوک‌های اختصاصی.
- Pagination، Search، Category/Brand Filters و URLهای مناسب.
- دکمه «استعلام قیمت»، «درخواست مشاوره» یا «دریافت کاتالوگ» به‌جای Add to Cart.
- CSV Import/Export در فاز بعد از تثبیت Schema.

### نکات فنی مهم

- CPT/Taxonomy/Field Logic در **Webtalik Core Plugin** باشد، نه در قالب.
- داده‌ها باید با تعویض قالب باقی بمانند.
- برای فیلترهای پرتکرار از Taxonomy و Queryهای بهینه استفاده شود؛ از `meta_query`های سنگین و بدون سنجش اجتناب شود.
- عملکرد با داده آزمایشی حداقل ۵۰۰ محصول اندازه‌گیری شود؛ تعداد محصولات به‌تنهایی تضمین سرعت نیست.
- در صورت نیاز آینده به فروشگاه، انتقال محصولات کاتالوگ به WooCommerce **خودکار فرض نشود**؛ قرارداد Migration جدا لازم است.
- امنیت فرم استعلام، ضداسپم، اعلان‌ها و ذخیره درخواست‌ها در مشخصات مستقل تعریف شوند.

---

## 7. صفحات و طراحی محتوا

### ابزار پیش‌فرض

- Gutenberg با Block Patterns و بلوک‌های اختصاصی Webtalik.
- بلوک‌های ضروری: Hero، Services، Portfolio/Projects، CTA، FAQ، Tabs، Pricing، Testimonials، Contact، Product Grid و Product Detail.
- بلوک‌ها در صورت امکان Dynamic/Server-rendered باشند و JS فرانت فقط برای تعاملات لازم اضافه شود.
- مشتری بتواند متن، عکس، چیدمان و رنگ‌ها را از پنل ویرایش کند؛ جزئیات پیشرفته برای توسعه‌دهنده قابل‌کنترل باشد.

### Elementor

- **اختیاری**؛ بدون وابستگی اجرای قالب به Elementor.
- در صورت نصب Elementor، سازگاری با صفحات و استایل عمومی آزمایش شود.
- اگر نیاز مشتری به ویرایشگر Elementor قطعی است، ادغام و ویجت‌های اختصاصی در یک ماژول/افزونه جدا بررسی شود.
- استفاده از Elementor برای هدر و فوتر **پیش‌فرض نباشد**؛ سازنده اصلی، Header/Footer Builder اختصاصی است.

---

## 8. WooCommerce و دیگر افزونه‌ها

- WooCommerce فقط در پروژه‌های فروشگاهی لازم باشد.
- هنگام فعال‌بودن WooCommerce، Header Builder بتواند عناصر سبد خرید، حساب کاربری و جستجوی محصول را ارائه کند.
- سازگاری با Hookها، مسیر Template و Styles ووکامرس تست شود؛ کمترین Override ممکن انجام شود.
- افزونه‌های SEO، فرم تماس، کش، امنیت، چندزبانه و Elementor باید در ماتریس سازگاری تست شوند.
- از بارگذاری مجدد نسخه‌های کتابخانه‌هایی مانند jQuery، Swiper، React یا GSAP که توسط سیستم دیگری فعال شده‌اند جلوگیری شود.
- به‌جای تغییر رفتار داخلی افزونه‌ها، از API/Hookهای رسمی آن‌ها استفاده شود.

---

## 9. Design System و شخصی‌سازی برای هر مشتری

### هسته طراحی

- CSS اختصاصی + CSS Custom Properties/Design Tokens.
- Typography، Font Scale، Colors، Spacing، Container Width، Radius، Border، Shadow، Breakpoints.
- Grid/Flexbox/Container Queries برای Responsive.
- RTL و LTR به‌صورت اصولی با Logical Properties مانند `margin-inline` و `padding-inline`.
- Scoped Classes با Prefix مثل `wt-` برای کاهش تداخل با افزونه‌ها.
- تم‌های روشن/تیره و Preset رنگ برند به‌صورت قابل‌تعریف.

### Tailwind یا Bootstrap؟

**پیشنهاد اولیه:** وابستگی نداشتن خروجی نهایی به CSS Framework عمومی. اگر Tailwind برای سرعت توسعه انتخاب شد، خروجی Purge/Tree-shake شود، Prefix و تداخل Reset/Preflight کنترل شود. Bootstrap اجباری نباشد.

### پنل تنظیمات

- مدیریت Logo، Favicon، رنگ، فونت، Container، Header/Footer پیش‌فرض، Sticky، Blog/Archive، Catalog و انیمیشن‌ها.
- پنل Admin می‌تواند با React ساخته شود؛ تنظیمات از WordPress Settings/REST API و کنترل مجوزها استفاده کنند.
- امکانات زیاد اما **گزینه‌های واقعاً موردنیاز**؛ نه ده‌ها Toggle مبهم و بدون کاربرد.

---

## 10. GSAP، React و کتابخانه‌ها

- GSAP کاملاً **اختیاری**؛ فقط برای سکشن/صفحه‌ای که نیاز به انیمیشن دارد enqueue شود.
- ScrollTrigger، SplitText و سایر Pluginها تنها در صورت نیاز بارگذاری شوند و وضعیت مجوز استفاده/بسته آن‌ها بررسی شود.
- انیمیشن‌ها `prefers-reduced-motion` را رعایت کنند؛ روی موبایل نسخه سبک‌تر داشته باشند.
- انیمیشن خارج از Viewport در صورت امکان Pause شود.
- Frontend React فقط برای کامپوننت‌های پیچیده‌ای که با HTML/Vanilla JS دشوارند.
- یک صفحه معمولی شرکتی برای کارکرد صحیح نباید منتظر Hydration یک SPA بماند.
- اگر برای Hero افکت WebGL/Three.js لازم شد، آن ویژگی مستقل، شرطی و دارای Fallback تصویری باشد؛ در هسته عمومی اجبار نشود.

---

## 11. اصل Self-hosted / Local-first

**تعریف هدف:** هیچ Font، Icon، CSS، JavaScript، تصویر یا Model ضروریِ فرانت‌اند از CDN خارجی بارگذاری نشود.

- فونت‌ها با مجوز مناسب در پروژه بسته‌بندی یا روی سایت مشتری self-host شوند.
- آیکون‌ها SVG محلی باشند؛ نه Icon Font سنگین سراسری.
- GSAP و دیگر کتابخانه‌های مجاز از Assets محلی به‌شکل نسخه‌بندی‌شده بارگذاری شوند.
- Built Assets با Cache Busting مناسب و Vite Manifest/معادل سازگار با وردپرس منتشر شوند.
- درخواست به Google Fonts، CDNهای عمومی یا سرویس تحلیل خارجی به‌صورت پیش‌فرض وجود نداشته باشد.
- **استثنا:** سرویس‌های اختیاری مانند درگاه پرداخت، نقشه، ایمیل خارجی، لایسنس افزونه ثالث و آپدیت‌های وردپرس ممکن است اتصال سرور یا مرورگر به اینترنت بخواهند. «کاملاً آفلاین» نیاز جداگانه است.

---

## 12. کارایی، SEO و دسترس‌پذیری

### معیارهای هدف (باید با تست سنجیده شوند؛ تضمین پیشاپیش نیستند)

- Core Web Vitals برای صفحات واقعی: `LCP ≤ 2.5s`، `INP ≤ 200ms` و `CLS ≤ 0.1` در صدک ۷۵ داده‌های واقعی، در صورت داشتن داده کافی.
- حجم JS و CSS هر صفحه پایش شود؛ بودجه نهایی پس از Benchmark با تست نمونه تعیین گردد.
- ترجیح با SSR، حذف JS بی‌نیاز، Responsive Images (`srcset/sizes`)، AVIF/WebP در صورت امکان و Lazy Load تصاویر غیر LCP.
- فونت محلی WOFF2، تعداد وزن محدود و راهبرد مناسب نمایش فونت.
- Queryهای دارای Pagination/Cache و عدم بارگذاری همه محصولات یکجا.
- Cache-compatible بودن خروجی؛ عدم اجرای محاسبات سنگین CSS/Builder در هر درخواست.
- HTML معنایی، Heading صحیح، Breadcrumb، Meta/Schema متناسب با محتوا و سازگاری با افزونه‌های SEO.
- دسترس‌پذیری مطابق هدف `WCAG 2.2 AA`: کیبورد، Focus، Contrast، ARIA صحیح برای Drawer/Mega Menu، Reduced Motion و Touch Targets.

### تست‌های عملکرد پیشنهادی

1. سایت شرکتی بدون افزونه صفحه‌ساز.
2. سایت کاتالوگی با حداقل ۵۰۰ محصول و فیلترها.
3. فروشگاه با WooCommerce.
4. سایت دارای Elementor (اختیاری) در کنار قالب.
5. موبایل ضعیف/شبکه محدود و دسکتاپ.

ابزارهای ارزیابی: Lighthouse/PageSpeed Insights، DevTools Performance/Network، Query Monitor، ابزارهای PHP profiling و تست‌های میدانی در صورت امکان.

---

## 13. امنیت، پایداری و استانداردهای وردپرس

- رعایت WordPress Coding Standards برای PHP و JS در حد مناسب پروژه.
- `sanitize_*`، `esc_*`، Capability Checks، REST Permissions و Nonce در عملیات مدیریتی.
- جلوگیری از XSS در تنظیمات استایل/HTML؛ Props/Styles فقط از فهرست مجاز و Validate‌شده.
- Prefix و Namespace برای توابع، کلاس‌ها، REST Routes و CSS (`Webtalik\...`، `wt-`، `webtalik/v1`).
- سازگاری با ترجمه، RTL، کش و روش صحیح enqueue فایل‌ها.
- Migration نسخه‌دار برای Schema فیلدها، Header Layoutها و گزینه‌ها؛ امکان Backup/Export قبل از مهاجرت.
- رعایت قوانین مجوز وردپرس و کتابخانه‌ها؛ از کپی فایل/کد اختصاصی HUB، WoodMart، BeTheme یا افزونه‌های تجاری اجتناب شود. تنها بررسی طراحی/رفتار و توسعه مستقل مجاز است، مگر مجوز صریح داشته باشیم.

---

## 14. ساختار پیشنهادی Repository

```text
webtalik-framework/
├── apps/
│   └── demo-wordpress/              # Optional local test environment
├── packages/
│   ├── webtalik-theme/              # WordPress classic/hybrid theme
│   │   ├── style.css
│   │   ├── functions.php
│   │   ├── theme.json
│   │   ├── header.php
│   │   ├── footer.php
│   │   ├── front-page.php
│   │   ├── page.php
│   │   ├── single.php
│   │   ├── inc/
│   │   │   ├── setup/
│   │   │   ├── assets/
│   │   │   ├── header-footer/
│   │   │   ├── options/
│   │   │   └── integrations/
│   │   ├── template-parts/
│   │   ├── src/
│   │   └── dist/
│   ├── webtalik-core/               # Catalog, CPTs, fields, APIs
│   │   ├── includes/
│   │   ├── admin/
│   │   └── blocks/
│   └── webtalik-elementor/          # Optional, future integration
├── tooling/
├── tests/
├── docs/
├── composer.json                    # If needed
├── package.json
└── README.md
```

**نکته:** این ساختار پیشنهادی است، نه دستور نصب چند افزونه برای همه مشتری‌ها. هسته قالب و Core می‌توانند به‌صورت ZIP مستقل منتشر شوند؛ Integrationهای غیرضروری نصب/فعال نشوند.

### ابزار ساخت

- **Vite** برای Assets اختصاصی قالب و کامپوننت‌های Frontend.
- **`@wordpress/scripts`** (یا تنظیم Build معادل با Dependency Extraction درست) برای بلوک‌ها و React پنل‌های وردپرس.
- جلوگیری از Bundle کردن چندباره `react` و `react-dom` در Admin وقتی WordPress Packages فراهم هستند.
- مخزن Git، Version Tags، Changelog و CI برای Lint/Test/Build ZIP.
- نسخه دقیق WordPress/PHP/Node/Browser پیش از شروع MVP تعیین و با مستندات روز بررسی شود.

---

## 15. استراتژی توسعه با Codex

### مرحله 0 — تحلیل و تصمیم‌گیری (بدون کدنویسی تولیدی)

- مرور این سند و استخراج ناسازگاری‌ها یا ابهام‌ها.
- مقایسه دو معماری Classic/Hybrid + Custom Builder و Block Theme + FSE با توجه به نیاز واقعی مشتری‌ها.
- مشخص‌کردن رابطه Theme/Core و محل ذخیره Header/Footer Layout.
- ارائه RFC کوتاه شامل Trade-off، خطرات، Dependencies و Test Plan.
- طراحی Data Contract هدر، فوتر و Catalog قبل از توسعه UI.

### مرحله 1 — MVP قالب پایه

- نصب قابل‌تست قالب، `theme.json`، استایل RTL/LTR، Design Tokens و الگوهای اولیه صفحه.
- Header/Footer ساده PHP، منوی واکنش‌گرا، Typography/Color Options و Asset Loader شرطی.
- Starter Templates برای شرکتی و خدماتی.

### مرحله 2 — Header Builder MVP

- React Admin UI برای ساخت ردیف/ستون/آیتم، ذخیره Schema و Renderer سمت سرور.
- Desktop/Mobile، Rule Assignment و Preview.
- امنیت و تست Keyboard/Drawer و Style Isolation.

### مرحله 3 — Footer Builder

- استفاده مجدد از Schema/Renderer قابل‌اشتراک با Header در حد ممکن.
- قالب‌های ذخیره‌شده و Import/Export.

### مرحله 4 — Webtalik Catalog

- CPT، Taxonomy، Field Schema، Admin Forms، Product Grid/Detail، Filter، Inquiry.
- آزمایش با ۵۰۰ محصول و برنامه مدیریت Schema Migration.

### مرحله 5 — Integrations و Performance

- تست و ادغام اختیاری WooCommerce و Elementor.
- Gutenberg Custom Blocks، GSAP شرطی، SEO، Cache، Accessibility و تست روی موبایل.
- Packaging و Release Process برای چند پروژه مشتری.

### تعریف اتمام MVP (Definition of Done)

- نصب قالب روی وردپرس تمیز بدون نیاز به Elementor/JetEngine.
- یک هدر دسکتاپ/موبایل قابل‌ویرایش و اختصاص به صفحات، بدون خرابی بعد از رفرش یا آپدیت.
- یک فوتر قابل‌ویرایش.
- صفحات پایه با SEO و RTL صحیح.
- Backend Schema Validation، Role/Permission Check و تست‌های اولیه.
- بارگذاری فایل‌های فرانت‌اند از همان هاست، بدون CDN اجباری.
- گزارش Lighthouse/Performance روی سناریوی تست همراه روش اندازه‌گیری، نه صرف ادعای «سریع».

---

## 16. سؤال‌های باز — قبل از شروع فاز کدنویسی باید تعیین شوند

| موضوع | وضعیت فعلی | سؤال تصمیم‌گیری |
|---|---|---|
| معماری قالب | Classic/Hybrid پیشنهاد اولیه | آیا PoC در برابر Block Theme مزیت قابل‌اندازه‌گیری دارد؟ |
| سطح آزادی ویرایش مشتری | نامشخص | فقط Preset/تنظیمات یا Drag & Drop آزاد در هدر/فوتر؟ |
| ساختار Layout Builder | در حال طراحی | ذخیره در CPT، Options یا مکان دیگر و چرخه نسخه‌برداری؟ |
| ادغام Elementor | اختیاری | صرفاً سازگاری صفحه، یا ویجت‌های اختصاصی هم لازم است؟ |
| Catalog | جزو امکانات پایه | فیلدها/فیلترهای MVP دقیقاً کدام‌اند؟ |
| فروشگاه | WooCommerce اختیاری | آیا Migration کاتالوگ به فروشگاه لازم است؟ |
| استایل‌ها | Design System مستقل | آیا Tailwind فقط ابزار توسعه باشد یا حذف شود؟ |
| بسته‌بندی | Theme + Core | سیستم لایسنس و آپدیت اختصاصی موردنیاز است؟ |
| کاربران | فارسی RTL در اولویت | چندزبانه/LTR در MVP یا نسخه بعدی؟ |
| محیط توسعه | Codex + Git | Docker / wp-env / Local WP / CI کدام استاندارد تیم باشد؟ |

---

## 17. دستور آماده برای Codex

> متن زیر را همراه این فایل به Codex بده. هدف فعلاً **تکمیل معماری و داکیومنت** است؛ نه تولید یک‌باره هزاران خط کد.

```text
You are the principal WordPress architect for the Webtalik Framework.
Read webtalik-framework-spec-v0.1.md fully.
The goal is a reusable, high-performance, self-hosted WordPress theme ecosystem for client websites, with a custom React-based Header/Footer Builder in wp-admin, PHP server-rendered frontend, Gutenberg support, optional Elementor/WooCommerce integrations, and a native non-commerce product catalog without JetEngine.

FIRST TASK: documentation and architecture only. DO NOT start implementation until I approve the architecture.

1. Review the specification and identify conflicts, assumptions, missing requirements, security/performance risks, and licensing pitfalls.
2. Compare Classic/Hybrid WordPress theme vs Block Theme/FSE for this particular product. Make a reasoned choice, not a generic recommendation.
3. Propose final boundaries between theme, core plugin, optional integrations, and reusable UI components.
4. Design versioned JSON schemas for Header Builder, Footer Builder, and Catalog Field Groups, including responsive rules and migrations.
5. Define WordPress APIs/hooks, enqueue strategy, React admin architecture, PHP SSR components, CSS scoping, local assets, and accessible interactive menus.
6. Propose an MVP backlog with priorities, acceptance criteria, testing strategy, and measurable performance targets.
7. Create docs/architecture.md, docs/requirements.md, docs/mvp-plan.md, and docs/open-questions.md as proposals.
8. Ask only questions that materially change architecture. Mark proposed choices as PROPOSED, not DECIDED.
9. Do not copy proprietary HUB/WoodMart/BeTheme source or assets. Implement original code inspired only by documented features and UX concepts.
10. Do not add mandatory external APIs, CDNs, or proprietary runtime services.

Communicate in Persian. Keep identifiers and source code in English. Do not claim performance or compatibility without measurement.
Wait for my explicit approval before coding.
```

---

## 18. خلاصه تصمیم‌های فعلی

**تصمیم‌های نسبتاً روشن:**
- سیستم قابل‌استفاده برای چند مشتری، نه فقط سایت وبتالیک.
- Header/Footer Builder اختصاصی، مستقل از Elementor و قابل‌شخصی‌سازی.
- React برای پنل مدیریت؛ PHP/HTML سمت سرور برای نمایش عمومی.
- محصولات کاتالوگی داخلی بدون JetEngine و بدون نیاز به فروشگاه.
- WooCommerce و Elementor اختیاری باشند.
- GSAP/React/Three.js در Frontend تنها در صورت نیاز و به‌صورت شرطی.
- Self-hosted Assets و طراحی RTL/فارسی با سازگاری بین‌المللی.

**هنوز نیازمند تصمیم/PoC:**
- Classic/Hybrid در برابر Block Theme.
- جزئیات دقیق Drag & Drop و Schema/Storage.
- محدودیت‌های Field Builder و امکانات Import/Export.
- محل دقیق کد Builder و مرز Theme/Core.
- بودجه‌های حجمی، Baseline واقعی و نسخه‌های حداقل پشتیبانی‌شده.

> **اصل نهایی:** معماری را طوری انتخاب کنیم که قالبی سریع، قابل‌ارتقا و قابل‌استفاده در پروژه‌های متعدد بسازیم؛ نه یک قالب سنگین که صرفاً تعداد تنظیمات بیشتری دارد.
