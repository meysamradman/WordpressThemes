# Responsive Design & RTL — v1.0

**تاریخ:** 2026-11-18 | مشترک؛ مکمل 01 و 02، بدون نسخه جداگانه موبایل. Breakpoint، فونت و عرض کانتینر تصمیم Profile هر مشتری است.

## قرارداد

- یک URL، یک HTML اصلی و محتوای معادل برای همه viewportها؛ layout متفاوت با CSS. استثنای محدود مثل کنترل منو/تصویر art direction باید ثبت شود؛ دو نسخه کامل hidden desktop/mobile نساز. دسترسی Googlebot و کاربر به محتوا، لینک، metadata و robots از 01.
- mobile-first برای کد جدید مگر دلیل سازگاری؛ `meta viewport` صحیح، zoom آزاد. ابتدا طرح طبیعی بدون media query؛ Grid/Flex، wrapping و `minmax`، سپس breakpoint در نقطه شکستن واقعی طراحی. نمونه‌های تست 320، 360، 390، 768، 1024، 1440px و دستگاه واقعی، نه اجبار به ایجاد شش breakpoint.
- `max-inline-size` و gutter مشترک؛ padding/gap با token، container queries در کامپوننت وابسته به عرض محلی. عدم استفاده از vh ثابت برای متن متغیر؛ `svh/dvh` به‌صورت هدفمند با fallback. عرض 100vw داخل صفحه دارای scrollbar می‌تواند overflow ایجاد کند؛ 100% یا اندازه منطقی در صورت تناسب.
- RTL با `lang` و `dir` واقعی و logical CSS؛ ساخت DOM به ترتیب خواندن و فوکوس. اسکرول کاروسل، کلیدها، آیکون جهت‌دار، mixed Persian/English و اعداد آزمایش شوند. در layout متن طولانی، فضای کارت طبیعی؛ بریده‌شدن متن ضروری ممنوع.
- فونت خوانا و تغییر zoom تا 200%؛ reflow در عرض 320 CSS px در موارد قابل اعمال. هدف لمسی داخلی حدود 44×44 CSS px، نه قانون سئو؛ WCAG 2.5.8 سطح AA حداقل 24×24 با استثناهای خودش دارد. focus visible، برچسب، hover مستقل از دسترسی؛ استفاده از `@media (hover:hover) and (pointer:fine)` برای hover تزئینی.
- `srcset/sizes` درست، `picture` تنها برای source/art direction لازم؛ عرض/ارتفاع ذاتی و ratio. تصویر LCP معمولاً eager و priority در صورت نیاز؛ پایین صفحه lazy. توجه به حجم فایل واقعی، نه فقط کوچک شدن CSS.
- اسکرول طبیعی موبایل پیش‌فرض؛ حرکت کوتاه و سبک، عدم دانلود افکت سنگین فقط با مخفی کردن CSS تضمین نمی‌شود. Dynamic import واقعی براساس قابلیت/نیاز؛ reduced motion با 05؛ liveness/cleanup با 04. چرخه لودینگ طبق مسیر پروژه.
- تغییر عرض/چرخش، باز و بسته شدن منو، فرم و keyboard، sticky header، footer، safe-area در صورت نیاز، carousel swipe و keyboard، تصاویر و فونت فارسی/لاتین تست شوند. هر breakpoint باید رفتار UI را نگه دارد؛ تغییر layout، focus یا scroll ناخواسته نسازد.

## پذیرش

- [ ] هیچ markup کامل تکراری مخصوص موبایل نیست و محتوای مهم/language/links مشترک است.
- [ ] no horizontal overflow ناخواسته؛ خوانایی، zoom و Tab order صحیح؛ modal/منو/scroll lock در resize آزاد می‌شود.
- [ ] منابع غیرضروری دسکتاپ واقعاً در Network موبایل دریافت نمی‌شوند، در صورت نیاز با دلیل ثبت شده‌اند. تست دستگاه واقعی + Lighthouse/DevTools؛ گزارش Pass/Fail/Not run، بدون تضمین رتبه.

## منابع رسمی

- [Google mobile-first indexing](https://developers.google.com/search/docs/crawling-indexing/mobile/mobile-sites-mobile-first-indexing)
- [MDN Logical properties](https://developer.mozilla.org/en-US/docs/Web/CSS/Guides/Logical_properties_and_values)، [Container queries](https://developer.mozilla.org/en-US/docs/Web/CSS/Guides/Containment/Container_queries)، [Grid accessibility](https://developer.mozilla.org/en-US/docs/Web/CSS/Guides/Grid_layout/Accessibility)
- [WCAG 2.2](https://www.w3.org/TR/WCAG22/)؛ [web.dev vitals](https://web.dev/articles/vitals)

اصول داخلی از راهنمای 01 و radman-doc/engineering/markup-css.md و layout.md؛ اعداد طراحی پروژه قبلی کپی نشده‌اند.
