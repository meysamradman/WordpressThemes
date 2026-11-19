# تصمیم‌های باز ممیزی مستندات — 2026-11-19
این فایل گزارش بازبینی است، نه دستور تغییر runtime. مبنا سه ZIP ارسالی و منابع رسمی. تصمیم قطعی کاربر درباره تصویر برگه در 07 ثبت شده؛ سایر رفتارهای مشتری بدون مجوز تغییر نمی‌کنند.

| موضوع | شاهد داخل اسناد | پیشنهاد/وضعیت |
|---|---|---|
| تصویر برگه | frontend-standard و architecture: artwork ثابت و covers در attachment جدا | قرارداد عمومی تکمیل شد؛ آزمون صفحه با/بدون thumbnail لازم |
| آپارات خانه | media/scenarios: viewport 25%؛ hosting و release ثبت قدیمی click-only؛ architecture دو بیان داشت | روایت جاری مطابق media مشخص شد؛ پیشنهاد click-only برای کاهش درخواست با توجه به احتمال نیاز دو کلیک. انتخاب UX/TBD؛ Network خانه قبل اسکرول، ورود کارت، click، archive/detail |
| Entrance/curtain | release هم‌زمانی عمدی را ثبت کرده؛ 05/06 شروع با مجوز نمایش | هم‌زمانی فقط readiness state مشخص و بدون از دست رفتن افکت/تاخیر LCP؛ آزمون مستقیم/Reload/Back/scroll سریع؛ تایمر مستقل راه‌حل نیست |
| carousel | scenarios: autoplay 8s و حذف کنترل توقف به درخواست مشتری | autoplay دستی یا کنترل pause/start پیشنهاد؛ تا تصمیم و تست، WCAG PASS ثبت نشود |
| canonical گالری | wordpress/release: canonical پایه برای عکس‌های صفحه‌بندی‌شده | اگر هر URL تصاویر متفاوت دارد، self-canonical و لینک crawlable توصیه گوگل؛ تصمیم مصوب پس از بررسی GSC/HTML؛ مسیر و داده تغییر نکرده |
| PHP | 00-VERSIONS: header PHP 8.0 و محلی 8.4.26 | 8.4+ هدف کد جدید؛ تغییر header فقط با بررسی هاست و کل stack |
| مسیر Skill | .agents ZIP جدا و link ../../../WEB-DEVELOPMENT-STANDARDS | Skill مشترک در ریشه repo قرار گرفت، نه زیر پروژه؛ Skill پیاده‌سازی هر پروژه در ZIP نبود |

## معیار تصمیم
برای هر مورد approve / reject / deferred + مالک + تاریخ + دلیل ثبت شود. نسخه اجرایی و تست‌های واقعاً اجراشده جدا. هیچ تغییر code/DB/SEO/هاست با این سند اجرا نشده. منابع: [17](../../WEB-DEVELOPMENT-STANDARDS/shared/17-IMAGES-AND-MEDIA-v1.0.md)، [20](../../WEB-DEVELOPMENT-STANDARDS/shared/20-TECHNICAL-SEO-SCHEMA-v1.0.md)، https://www.w3.org/WAI/tutorials/carousels/animations/ و https://gsap.com/docs/v3/Plugins/ScrollTrigger/ .
