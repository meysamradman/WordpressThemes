# Documentation revision 1.3 — 2026-11-19
مبنا: WordpressTheme.zip، docs.zip و .agents.zip. هر سه بسته اصلی مستقل باقی بمانند. این خروجی مستندات است، نه ZIP نصب قالب یا نسخه کامل Git repository. هیچ source runtime، ابزار Save-Project، پروژه دیگر، credentials، DB، plugins یا assets اضافه/تغییر داده نشده‌اند.

- Add: 17 Images/Media, 18 Query, 19 Refactoring, 20 Technical SEO, 21 Deployment. هر سند مستقل و عمومی؛ 07 مالک سیاست رندر برگه. 15 مسیر مستقل خودش را دارد.
- Update: index v1.3، pointer مسیر v1.2، AGENTS، README links، markers، استانداردهای 01/02/06/07/08/09/10/11/12/13/14/15/16، Profile و Skill؛ فایل‌های باقی بدون بازنویسی حفظ شده‌اند.
- Corporate-Music/docs: اسناد مشتری در مسیر پروژه، قرارداد تصاویر و تعارض تاریخی آپارات روشن، review-decisions برای Canonical گالری/Carousel/curtain/PHP. هیچ سیاست runtime بدون تأیید نهایی نشده. نسخه‌های عددی محیط به صورت گزارش تاریخی ورودی باقی مانده‌اند.
- Skill مشترک در .agents/skills ریشه قرار داده شد چون description عمومی و relative links به استاندارد ریشه دارد. اگر ساختار واقعی مخزن متفاوت است ابتدا diff و مسیر را بررسی کنید.
- نسخه فایل‌های قبلاً لینک‌شده حفظ شده، marker تغییر بسته به آن‌ها اضافه شده؛ index جدید با pointer قدیمی. این تصمیم برای جلوگیری از شکستن لینک در پروژه‌های دریافت‌نشده است. قبل از جایگزینی در repo از diff/path-scoped commit استفاده شود؛ فایل‌های خارج این بسته را حذف نکنید.

## تحویل
پوشه WEB-DEVELOPMENT-STANDARDS و .agents در ریشه مخزن، Corporate-Music/docs در همان پروژه، AGENTS/README و سایر فایل‌های ریشه فقط با بررسی diff. این بسته شامل کل مخزن نیست. .gitignore و .gitattributes ورودی صرفاً حفظ شده‌اند. از source runtime و پروژه‌های دیگر نسخه‌برداری/حذف نشود. ارجاع به فایل‌های پروژه دیگر یا tools که در آرشیو اصل موجود نبودند نیازمند بررسی در مخزن واقعی است. گزارش صحت بسته: VALIDATION.md.
