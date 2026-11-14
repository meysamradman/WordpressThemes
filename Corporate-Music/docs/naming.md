# هویت Radman و نام‌گذاری نسخهٔ سفارشی

این مخزن نسخهٔ **سفارشیِ شرکتی، خدماتی و نمونه‌کار Radman CT** است. برای مشتری‌های مختلف استفاده می‌شود و نام مشتری، نام فنی قالب نیست. پیاده‌سازی مستقل با PHP، Vanilla JS و GSAP محلی است؛ Elementor، Child، پنل قالب، سازندهٔ هدر/فوتر، کاتالوگ محصول یا وابستگی به افزونه‌های Corporate-Catalog ندارد. نام یکسان، مجوز کپی‌کردن معماری یا وابستگی‌های آن مخزن نیست.

| کاربرد | قرارداد |
|---|---|
| برند و لوگوی مشترک | `Radman` |
| عنوان بستهٔ فعلی در WordPress | `Radman CT`؛ جدا از قرارداد نام کلاس‌ها |
| پوشه و text domain | `radmanct` |
| namespace PHP | `RadmanCT` و زیرنام‌های ماژول |
| کلاس، ID، CSS variable | `rdc-`، `--rdc-` |
| marker رفتاری اختصاصی | `data-rdc-*` |
| رویداد سفارشی | `rdc:*` |
| تنظیمات عمومی JS | `rdc…`، مانند `rdcGal` |
| hook توسعه | `radmanct/…` |
| نام مؤلف | `Radman` |

پیشوند نسخهٔ سفارشی `rdc` است: `rd` برای Radman و `c` برای Custom. پیشوند پیشنهادی نسخهٔ مستقل Elementor برابر `rde` است؛ این قرارداد مجوز تغییر آن پروژه نیست. نام مشتری، کاتالوگ یا فروشگاهی‌بودن بخشی از پیشوند نیست. پوشه/text domain فعلی `radmanct`، namespace برابر `RadmanCT` و hookهای `radmanct/…` برای سازگاری حفظ می‌شوند؛ کوتاه‌سازی DOM نیازمند تغییر نام بسته، فعال‌سازی قالب یا مهاجرت داده نیست.

الگوی کلاس `rdc-{module}__{part}` و modifier برابر `--{variant}` است. کلاس، ID، custom property، data marker، keyframe و رویداد اختصاصی هم‌زمان در PHP/CSS/JS و تست تغییر کنند. هر مفهوم فقط یک مخفف ثابت دارد؛ مخفف تازهٔ موردی یا نام تصادفی build ممنوع است. نام فایل و متغیر/تابع داخلی PHP/JS بر اساس نقش و خوانا می‌ماند؛ minifier نام‌های داخلی JS را فشرده می‌کند. property مشترک PHP/JS فقط با هماهنگی تولیدکننده و مصرف‌کننده تغییر کند.

| مخفف | کاربرد |
|---|---|
| `btn` | Button |
| `gal` | Gallery |
| `adm` | Admin |
| `hdr` / `ftr` | Header / Footer |
| `ctr` / `cnt` | Container / Content |
| `prj` / `prjs` | Project / Projects |
| `svc` / `svcs` | Service / Services |
| `prj-mq` | Continuous bottom-aligned gallery with project CTA; `trk` is its moving track |
| `faq` | Shared frequently asked questions |
| `ctc` | Floating contact launcher and its channel panel |
| `pnl` | Menu panel |
| `nav` / `cat` | Navigation / Categories |
| `pre` / `im` / `ico` | Preloader / Image / Icon |
| `pg` / `lb` | Page or pagination context / Lightbox |
| `ss` | Sticky sections |
| `pt` | Page Title؛ فقط انواع مستقل `cover` و `split` |
| `sgl-more` / `rel` | بخش مشترک ادامهٔ مطالعه/مشاهده در جزئیات / Related |

مثال‌ها: `rdc-btn__lbl`، `rdc-prj__ttl`، `rdc-adm-gal__itm`، `rdc-hero__desc`، `rdc-tx-en__wd` و `rdc-scr-wrp`. نام‌های روشن و کوتاه مثل `hero`، `menu`، `card`، `blog`، `work`، `logo`، `grid`، `row` و `meta` حفظ می‌شوند؛ `text` به `tx` و بخش‌های بلند طبق واژه‌نامهٔ زیر کوتاه می‌شوند.

نام‌های WordPress/GSAP/افزونه‌ها، ویژگی‌های HTML/ARIA، propertyهای CSS و API مرورگر تغییر نمی‌کنند. modifierهایی که از قرارداد دادهٔ renderer ساخته می‌شوند، مثل `outline`، `ghost`، `compact`، `feature` و `wordpress`، مقادیر معنایی فعلی را حفظ می‌کنند؛ برای چند حرف صرفه‌جویی، نگاشت زمان اجرا یا API موازی ساخته نشود. شناسهٔ فایل وردپرس از handle و پسوند بومی مثل `-js-module` ساخته می‌شود؛ نام مسیرهای `pages/blog` و پسوند بومی مخفف نشوند. نام nonce/form با زیرخط و متغیرهای داخلی `$rdct_…` قرارداد سرورند و بخشی از تغییر کلاس‌ها نیستند.

شناسه‌های داخلی SVG رسانه، مثل نام لایه یا gradient، با حفظ ارجاع‌های همان asset و پیشوند یکتای `rdc-logo-…` مصرف می‌شوند؛ نام فایل/لایهٔ رسانهٔ ذخیره‌شده برای کوتاه‌کردن کلاس تغییر نکند. شناسه‌های hashدار asset نیز ثابت فرض نشوند و همیشه از Manifest خوانده شوند.

نگاشت ایستای شناسه‌های قبلی `wt-*` و `rdct-*` به نام نهایی در [tools/css-class-map.json](../tools/css-class-map.json) است؛ فقط ابزار توسعه/بازبینی است و به فرانت‌اند ارسال یا در runtime مصرف نمی‌شود. alias سراسری CSS، دو پیشوند موازی و بازنویسی DOM هنگام اجرا نداریم. کوتاه‌سازی اسم به‌تنهایی اثبات افزایش FPS یا سرعت محسوس نیست؛ اندازهٔ خام/Gzip، تعداد فایل و رفتار واقعی جدا سنجیده شوند.

حالت‌های داخلی: `is-enh`، `is-out`، `is-load`، `is-play`، `is-tx-rot`، `is-vis` و `is-tall`. dataset متناظر باید دقیقاً از نام attribute به دست آید؛ مثلاً `data-rdc-ent` برابر `dataset.rdcEnt` و `data-rdc-svc-im` برابر `dataset.rdcSvcIm` است. کلید Session موجود `rdct:preloader-seen` برای جلوگیری از پخش دوبارهٔ پری‌لودر پس از تغییر نام حفظ می‌شود؛ این استثنا الگوی نام تازه نیست.

<!-- name-parts:start -->

| مفهوم | مخفف | مفهوم | مخفف | مفهوم | مخفف |
|---|---|---|---|---|---|
| `accent` | `acc` | `actions` | `acts` | `active` | `act` |
| `align` | `aln` | `archive` | `arc` | `article` | `artc` |
| `background` | `bg` | `badge` | `bdg` | `black` | `blk` |
| `border` | `bd` | `bottom` | `btm` | `branch` | `br` |
| `brand` | `brd` | `canvas` | `cvs` | `categories` | `cats` |
| `category` | `cat` | `client` | `clt` | `close` | `cls` |
| `color` | `clr` | `columns` | `cols` | `contact` | `ctc` |
| `content` | `cnt` | `count` | `num` | `cover` | `cvr` |
| `cream` | `crm` | `cursor` | `cur` | `curve` | `crv` |
| `description` | `desc` | `desktop` | `desk` | `details` | `dtl` |
| `dialog` | `dlg` | `diamond` | `dmnd` | `direction` | `dir` |
| `display` | `dsp` | `duration` | `dur` | `editor` | `ed` |
| `email` | `mail` | `empty` | `emp` | `enter` | `en` |
| `entrance` | `ent` | `excerpt` | `exc` | `expire` | `exp` |
| `eyebrow` | `eb` | `fallback` | `fb` | `features` | `fts` |
| `fixed` | `fix` | `focus` | `fcs` | `frame` | `frm` |
| `front` | `fr` | `gallery` | `gal` | `group` | `grp` |
| `gutter` | `gut` | `header` | `hdr` | `heading` | `hdg` |
| `height` | `ht` | `highlight` | `hi` | `hours` | `hrs` |
| `hover` | `hov` | `icon` | `ico` | `image` | `im` |
| `indicator` | `ind` | `inner` | `inr` | `intro` | `intr` |
| `inverse` | `inv` | `item` | `itm` | `journal` | `jrn` |
| `label` | `lbl` | `layout` | `ly` | `light` | `lt` |
| `line` | `ln` | `link` | `lnk` | `links` | `lnks` |
| `media` | `md` | `mobile` | `mob` | `motion` | `mot` |
| `muted` | `mut` | `navigation` | `nav` | `network` | `net` |
| `number` | `num` | `offer` | `ofr` | `offset` | `off` |
| `orbit` | `orb` | `overlay` | `ovl` | `owner` | `own` |
| `padding` | `pad` | `paint` | `pnt` | `panel` | `pnl` |
| `paper` | `ppr` | `parallax` | `px` | `placeholder` | `ph` |
| `poster` | `pst` | `preloader` | `pre` | `preview` | `pv` |
| `project` | `prj` | `radius` | `rad` | `ratio` | `rat` |
| `ready` | `rdy` | `results` | `res` | `reveal` | `rvl` |
| `ribbon` | `rbn` | `rotate` | `rot` | `rotator` | `rtr` |
| `scroll` | `scr` | `search` | `srch` | `secondary` | `snd` |
| `section` | `sec` | `sections` | `secs` | `selected` | `sel` |
| `service` | `svc` | `services` | `svcs` | `shadow` | `shd` |
| `sheen` | `shn` | `sidebar` | `sb` | `single` | `sgl` |
| `size` | `sz` | `small` | `sm` | `smooth` | `smth` |
| `social` | `soc` | `socials` | `socs` | `space` | `sp` |
| `speed` | `spd` | `stage` | `stg` | `start` | `st` |
| `status` | `sts` | `sticky` | `stk` | `stroke` | `sw` |
| `subtitle` | `sttl` | `surface` | `srf` | `taxonomy` | `tax` |
| `text` | `tx` | `thumb` | `thm` | `title` | `ttl` |
| `toggle` | `tgl` | `toolbar` | `bar` | `tooltip` | `tip` |
| `trail` | `trl` | `update` | `upd` | `updated` | `upd` |
| `visual` | `vis` | `weight` | `wt` | `white` | `wht` |
| `widget` | `wdg` | `width` | `w` | `word` | `wd` |
| `words` | `wds` | `wrapper` | `wrp` |  |  |

<!-- name-parts:end -->

## قراردادهای ذخیره‌شده و محیط محلی

برای حفظ سایت موجود، کلیدهای `_webtalik_project`، `_webtalik_card_video`، `_webtalik_card_mode` و option رسانهٔ `webtalik_media` ثابت‌اند. این‌ها قرارداد داده‌اند و الگوی نام جدید نیستند. CPT برابر `project` و taxonomy برابر `project_category` باقی می‌مانند. بازنویسی خودکار محتوا، متا، رسانه یا شناسه‌ها به بهانهٔ تغییر نام ممنوع است. نام و محتوای واقعی سایت فعلی «وبتالیک» نیز محتوای مشتری است.

مخزن در پوشهٔ `Corporate-Webtalik` و Docker محلی همچنان با پروژهٔ `webtalik`، پورت `8095`، volumeها، دیتابیس و credentials مستقل قبلی کار می‌کند؛ این نام‌ها شناسهٔ محیط موجودند و نباید برای تغییر برند حذف یا reset شوند. فقط مسیر mount قالب به `radmanct/` اشاره می‌کند. مسیر و نام skill محلی `webtalik-work` نیز شناسهٔ گردش کار این مخزن است.

در نصب موجود، تغییر پوشهٔ قالب باید با فعال‌سازی `radmanct` همراه باشد. تنظیمات `theme_mods_webtalik` را به `theme_mods_radmanct` منتقل کنید؛ مقدار موجود مقصد برنده است و اصل تنظیمات قدیمی حذف نمی‌شود. اجرای محلی این انتقال، مجوز تغییر خودکار قالب روی سرور یا تغییر مخزن Corporate-Catalog نیست.

## گالری مدیریت نمونه‌کار

گالری در متاباکس مستقلِ ستون اصلی ویرایش پروژه است؛ در سایدبار رسانهٔ کارت قرار نگیرد. ریزعکس‌ها، مشاهدهٔ اصل تصویر، حذف تکی و جابه‌جایی با کیبورد در همان کادر فراهم باشند. افزودن/مرتب‌سازی با پنجرهٔ بومی گالری WordPress انجام شود؛ برای این کار کتابخانهٔ انتخابگر یا مرتب‌سازی موازی اضافه نشود. CSS فقط در ویرایش پروژه و JS رسانه فقط در ویرایش نوشته/پروژه بارگذاری می‌شود. ذخیرهٔ گالری nonce مستقل دارد، ترتیب attachment IDها و سایر فیلدهای `_webtalik_project` را حفظ می‌کند؛ حذف از گالری، فایل رسانه را حذف نمی‌کند.
