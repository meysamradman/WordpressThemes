# 19 — Code Quality, Reuse & Refactoring v1.0
**تاریخ:** 2026-11-19 | مشترک؛ روش جلوگیری از کد تکراری، dead code، dependency و regression. روش syntax در 02/04/05/13 و component در 16. Scope هر کار و approval در AGENTS/Scenario.

## قبل از ساخت و تغییر
1. مالک واقعی را پیدا کن: entry/manifest، template hierarchy، PHP hooks، CSS imports، module imports، event/data hooks، AJAX و backend. نام مشابه دلیل تکرار نیست؛ DOM/CSS/JS و رفتار واقعی را مقایسه کن.
2. مالک واحد برای token، فونت، دکمه، کارت، input، modal، skeleton، carousel، image renderer، scroll/loader و SEO. base/variant، args یا data contract؛ از یک component عظیم با شرط‌های متعدد هم اجتناب کن. طراحی و داده هر مشتری مستقل بمانند. HTML معنایی و ترتیب focus حفظ شوند.
3. تغییر کوچک‌ترین محدوده لازم؛ no new framework، dependency، polyfill یا upgrade بدون نیاز/تصویب. از ابزار موجود در پروژه بهره ببر؛ کد غیرضروری به معنی حذف hook، fallback، a11y، type guard یا امنیت نیست.

## شیوه نوشتن
- سورس خوانا، function/ماژول با مسئولیت روشن، early return برای guard، نام توصیفی طبق قرارداد پروژه، ثابت برای مقادیر قراردادی و enum/allowlist. magic numbers و log/debug/dead code بی‌دلیل حذف؛ comment دلیل/قید، نه تکرار کد. validation/escape در مرز درست نه در چند لایه مشکوک؛ side effect صریح. فایل runtime minified فقط build generated؛ vendor/core دست نخورند.
- CSS: tokens، base، layout، component، feature/page، utility. Flex یک‌بعد، Grid دوبعد؛ نه wrapper برای هر gap. style/JS inline ثابت تکراری ممنوع؛ critical CSS بر اساس نیاز واقعی. JS: import ماژول، init یک‌بار، destroy listener/timer/observer/animation scopes؛ event delegation فقط در root مرتبط. GSAP فقط motion، ownership transform و state مشخص. PHP: view-model و template part، API بومی، بدون query در renderer کارت.
- تعداد خطوط، فایل‌ها یا class هدف سرعت نیست. سورس ماژولار، build متناسب مصرف؛ Network/Coverage و trace واقعی. Bundle مشترک کوچک، feature شرطی؛ از بارگیری فایل یک صفحه در تمام صفحات خودداری. یک فایل کوچک مشترک ممکن است بهتر از چند request باشد؛ تصمیم بر مبنای اندازه/کش/مسیر.

## حذف کد با شاهد
- پیش از حذف identifier و paths را با جست‌وجو، import graph، PHP dynamic handles، template lookup، admin options، block/widget registrations، generated chunks، manifest، callback، shortcode/CPT و data persistence بررسی کن. CSS Coverage یک trace اثبات مرده بودن نیست؛ UI حالت‌های hidden/focus/error/loading/responsive و editor بررسی شوند.
- تغییر identifier ذخیره‌شده، slug، post meta، URL، hook عمومی یا API قرارداد داده است؛ mapping/migration/backward compatibility و مجوز لازم. حذف archive/source/bak فقط در مسیر تعیین‌شده و پس از تأیید backup/مالکیت. فایلی که در release ZIP لازم است با blanket ignore حذف نشود.
- تست diff و snapshot UI/HTML/HTTP، lint ابزار موجود و build فقط برای تغییر مؤثر در خروجی. اگر فایل docs تنها تغییر است build نکن. گزارش Pass/Fail/Not run، باقی ریسک، کاهش واقعی resource در صورت اندازه‌گیری؛ هیچ درصد بهبود ساختگی.

## پذیرش Codex
- [ ] مصرف‌کننده‌ها و مالک ثبت شده‌اند؛ duplication واقعی یا رفتار مشترک با variant حل شده، نام و کامپوننت موازی بی‌دلیل ندارد.
- [ ] محتوا/URL/طراحی/API/داده و دسترسی حفظ شده؛ fallback بدون JS و mobile/RTL در موارد مرتبط؛ source و dist همگام.
- [ ] کد حذف‌شده شواهد عدم مصرف دارد؛ تست متناسب و evidence ثبت شده؛ commit/push/deploy فقط با مجوز.

## منابع
- https://developer.wordpress.org/coding-standards/wordpress-coding-standards/
- https://developer.wordpress.org/themes/templates/template-hierarchy/
- https://developer.wordpress.org/themes/core-concepts/including-assets/
- https://vite.dev/guide/backend-integration.html
- https://developers.openai.com/blog/rethinking-skills-and-prompts-for-gpt-6-astra
