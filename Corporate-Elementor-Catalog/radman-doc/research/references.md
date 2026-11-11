# منابع و شواهد؛ مطالعه اختیاری

این سند دستور کار یا نیاز قطعی نیست. در ۲۰۲۶-۱۰-۱۸ سورس محلی Hub `6.0.3` و WoodMart `8.6.1` محدود بررسی شد؛ نسخه‌ها از style.css همین کپی‌ها هستند، نه تأیید اصالت یا نسخه رسمی ناشر.

مقایسه‌های قبلی React SSR/Next.js فقط سابقه تحقیق‌اند؛ تصمیم نهایی محصول PHP SSR با React مدیریت و Core مستقل است. برای مبنای کار مستقیم به [معماری قطعی](../engineering/architecture.md) بروید؛ این منابع دلیل بازکردن دوباره استک نیستند.

WoodMart سازنده را به manager/factory/elements/styles/frontend/backend تفکیک می‌کند و registry وابستگی JS دارد؛ Hub راه‌اندازی مرکزی، استایل پویا، ادغام‌ها و تنظیمات متصل به Redux دارد. این‌ها الهام برای مرزبندی‌اند، نه الزام تکرار معماری. تعداد فایل اثبات سرعت یا بارگذاری واقعی نیست. هیچ‌کدام اجرا نشده‌اند.

شرح شواهد قبلی: [بررسی نمونه‌ها](../archive/2026-10-18-initial-drafts/reference-review.md). سند ورودی تاریخی: [Webtalik](../archive/2026-10-18-initial-drafts/webtalik-framework-spec-v0.1.md). سندهای آرشیو ممکن است پیشنهادهای ردشده داشته باشند؛ به نیاز جاری تبدیل نشوند.

## منابع رسمی بررسی‌شده قبلی با Context7

- [React در پروژه موجود](https://react.dev/learn/add-react-to-an-existing-project) و [مالکیت root](https://react.dev/reference/react-dom/client/createRoot).
- [React server rendering](https://react.dev/reference/react-dom/server): نیاز مدل اجرای JavaScript؛ هاست PHP به‌تنهایی آن را تأمین نمی‌کند.
- [Elementor content area](https://elementor.com/help/the-content-area-was-not-found-error/) و [فایل‌های frontend](https://developers.elementor.com/docs/scripts-styles/).
- [فونت پیش‌فرض Elementor](https://elementor.com/help/disable-elementors-default-fonts-and-colors/): غیرفعال‌سازی پیش‌فرض به‌تنهایی توقف تمام overrideها/دانلودهای فونت را اثبات نمی‌کند.
- [WPML custom fields](https://wpml.org/documentation/support/language-configuration-files/custom-fields-translation-options/) و [سازگاری قالب](https://wpml.org/documentation/support/achieving-wpml-compatibility-for-your-themes-and-plugins/).
- [WordPress theme types](https://developer.wordpress.org/themes/core-concepts/theme-types/) و [block metadata](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/).

برای تصمیم وابسته به نسخه، منابع روز و نسخه واقعی پروژه دوباره بررسی شوند؛ این فهرست نتیجه تست رادمان نیست.
