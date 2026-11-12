# نام‌گذاری کتابخانهٔ Radman

واژه‌نامه از `Corporate-Webtalik/docs/naming.md` گرفته شده است. نام تجاری Radman باقی می‌ماند؛ نام مشتری بخشی از شناسهٔ فنی نیست.

- قالب کارگاه: `radman-library`؛ text domain همان؛ namespace رابط `RadmanLibrary` و کلاس/ID/data/CSS custom property/event رابط `rdl-` است.
- ماژول‌های واردشده: namespace `RadmanCT`، text domain `radmanct` و پیشوند `rdc-` حفظ می‌شوند. آن‌ها یک مجموعهٔ مستقل داخل `shared/radmanct` هستند؛ برای اتصال به رابط کارگاه rename یا alias زمان اجرا نکنید.
- واژه‌های کوتاه روشن مثل `hero`، `menu`، `card`، `work`، `logo`، `grid`، `row`، `meta`، `demo` و `info` مجازند. بخش‌های بلند از جدول پایین کوتاه شوند؛ مثل `tx`، `ttl`، `desc`، `im`، `thm`، `cnt`، `inr`، `wrp`، `scr`، `pv` و `intr`.
- مثال رابط: `rdl-card__cnt`، `rdl-card__im`، `rdl-pv-bar`، `rdl-demo-info`، `rdl-demo-thm`، `rdl-bar__ttl`؛ مثال طراحی مصرف‌شده: `rdc-svc-strip__inr`، `rdc-hero__desc` و `rdc-tx-en__wd`.
- نام‌گذاری BEM با module/part/modifier است. تغییر کلاس باید هم‌زمان در renderer، CSS، JS، dataset، ARIA و بررسی مرتبط انجام شود؛ alias قدیمی یا mapping در مرورگر نسازید.
- نام فایل بر اساس نقش است؛ تکرار برند در هر فایل لازم نیست. URL/slugهای ثبت‌شدهٔ نمونه‌ها مانند `home-services` و modifier مشتق از همان slug قرارداد داده‌اند؛ برای کوتاه‌کردن چند حرف، مسیرها و داده‌های ذخیره‌شده را نشکنید.
- CPT برابر `rdl_design`، taxonomy برابر `rdl_group` و metaهای `_rdl_module`، `_rdl_webtalik_id` و option `rdl_sample_data` قرارداد ذخیره‌شده‌اند. نام‌های WordPress، HTML/ARIA، GSAP و propertyهای استاندارد CSS تغییر نکنند.
- hash و پسوند asset از manifest خوانده شود. کلاس‌های کوتاه به‌تنهایی اثبات سرعت بیشتر نیستند؛ بارگذاری انتخابی و گراف مشترک اهمیت عملی دارند.

ماژول‌های مرجع جدید با نام‌های scoped `rdl-steps` (مراحل) و `rdl-mq` (marquee) هستند. بخش‌های آن‌ها از همان `hdr`، `eb`، `ttl`، `itm`، `num`، `desc` و `grp` استفاده می‌کنند؛ کلمات کوتاه روشن مثل `list`، `time`، `play` و `track` مجازند. data-rdl-steps / data-rdl-mq و data-rdl-mq-play باید با selectorهای JS یکسان بمانند. نام module slug یا فیلتر داده به خاطر چند حرف کوتاه‌ترکردن تغییر نکند.

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

واژه‌های ماژول جدید: `rdl-lib` فهرست، `rdl-sb` سایدبار، `rdl-wdg` ابزارک، `rdl-sts` وضعیت، `rdl-fx` کنترل فناوری، `rdl-well` کارت افقی، `rdl-tx-demo` موشن متن. taxonomyهای `rdl_group`، `rdl_kind` و `rdl_fx` و optionهای داده، قرارداد ذخیره‌شده‌اند؛ برای کوتاه‌کردن کلاس تغییر نکنند.

کاتالوگ: DOM کوتاه `rdl-prd-*`؛ کلیدهای ماندگار منبع `radman_product`، taxonomyهای آن و `_radman_content_data` حفظ می‌شوند. `Catalog\Model` مالک مدل است، نه یک alias زمان اجرا.

`iac` = image accordion؛ `itm/md/tgl/row/box/lbl/desc` بخش‌های کوتاه طرح هستند. آکاردئون تصویری و کارت متنی زیر خانوادهٔ accordion با زیرشاخهٔ مستقل قرار می‌گیرند؛ carousel/slider نام نگیرند.

ماژول‌های جدید: `cgal` گالری ستونی، `phs` مراحل، `str` قاب بازشونده، `spl` معرفی/کارت دو ستون و `tmq` نوار متن. خانواده بر اساس فرم/حرکت است؛ مسیر منبع services یا نام مشتری دستهٔ طرح نیست.

mtx مالک مشترک نوار متن، tmq ترکیب متن/تصویر و rail/dot مسیر و نقطهٔ پیشرفت است. icon نام قرارداد renderer محلی است؛ کتابخانهٔ آیکون موازی اضافه نشود.

عنوان نمایشی همهٔ طرح‌ها با نام نوع طراحی و پسوند « · Radman» نوشته شود؛ نام سایت مرجع فقط در provenance/لینک منبع بماند، نه عنوان کارت/مدیریت/هدر نمونه. slugها با این تغییر برند عوض نشوند.

- `tx-im` عنوان دارای تصویر داخل خط است؛ `part` عبارت کامل و `md` قاب تزئینی تصویر را مشخص می‌کند. متن فارسی به حروف شکسته نمی‌شود.
