"""Explicit, selective source snapshot; never modifies the source checkout."""
import sys, re, json, hashlib, posixpath
from pathlib import Path

root=Path(__file__).resolve().parent.parent
source=Path(sys.argv[1]).resolve()/ 'radmanct'
theme=root/'radman-library'
target=theme/'shared'/'radmanct'
copied={}
def copy(rel):
    src=source/rel; dst=target/rel
    dst.parent.mkdir(parents=True,exist_ok=True)
    dst.write_bytes(src.read_bytes())
    copied[rel]=hashlib.sha256(src.read_bytes()).hexdigest()

php=['inc/home.php','inc/home/art.php','inc/home/projects.php','inc/icons.php','inc/icons/catalog.php','inc/ui/button.php','inc/page-title.php']
php += [str(p.relative_to(source)).replace('\\','/') for p in (source/'template-parts/sections/home').glob('*.php')]
php += ['template-parts/sections/services/showcase.php','template-parts/sections/services/strip.php','template-parts/ui/faq.php','template-parts/layout/titles/cover.php']
php += ['inc/media.php','inc/content/projects.php','inc/content/media.php','inc/content/continuation.php','inc/brand.php','inc/navigation.php','inc/navigation/panel-walker.php','template-parts/cards/project.php','template-parts/blog/preview.php','template-parts/blog/meta.php','template-parts/ui/content-heading.php','template-parts/layout/continuation.php','template-parts/layout/header.php','template-parts/layout/footer.php','template-parts/layout/menu-panel.php','template-parts/layout/menu-panel-desktop.php','template-parts/layout/menu-panel-info.php']
for rel in php: copy(rel)
copy('languages/fa_IR.mo')

# Only traverse imports consumed by the selected entry points.
visited=set()
def graph(rel):
    rel=Path(rel).as_posix()
    if rel in visited:return
    visited.add(rel);copy(rel)
    content=(source/rel).read_text(encoding='utf-8-sig')
    refs=re.findall(r'''(?:from\s*|import\s*\(\s*|import\s*|@import\s*)["']([^"']+)["']''',content)
    for ref in refs:
        if ref.startswith('.'):
            graph(posixpath.normpath(str(Path(rel).parent/ref).replace('\\','/')))

js=['runtime/scroll.js','runtime/lifecycle.js','motion.js','pages/home/offering-art.js','pages/home/slides.js','pages/home/projects.js','ui/rotator.js','ui/text-motion.js','ui/services-showcase.js','runtime/sticky.js','pages/services-strip.js','pages/page-title.js','gsap-vendor.js','animation-vendor.js']
js += ['pages/portfolio.js','pages/archive.js','ui/gallery.js','ui/card-preview.js','ui/header.js','ui/brand.js','ui/footer.js','admin/media-editor.js']
for entry in js:graph('src/js/'+entry)
css=['tokens.css','base.css','ui/button.css','ui/card-text.css','ui/text-motion.css','ui/faq.css','pages/home/hero.css','pages/home/offerings.css','pages/home/projects.css','layout/page-title.css']
css += ['pages/portfolio/archive.css','pages/portfolio/single.css','layout/navigation.css','layout/menu-panel.css','layout/footer.css','ui/brand.css','admin/gallery.css']
for entry in css:graph('src/css/'+entry)

# Shared asset resolver only; do not copy the customer's enqueue hooks.
assets=(source/'inc/assets.php').read_text(encoding='utf-8-sig').split("add_action('wp_enqueue_scripts'")[0]
assets=assets.replace("$entry = $manifest[ $key ] ?? array();", "if(!isset($manifest[$key])&&str_starts_with($key,'src/'))$key='shared/radmanct/'.$key;\n    $entry = $manifest[ $key ] ?? array();")
(target/'inc/assets.php').write_text(assets,encoding='utf-8')
copied['inc/assets.php']='adapter: resolver functions only, enqueue hooks omitted'

mods={
 'home-hero':('هیرو با ورود متن و عبارت چرخشی · Radman','hero','hero','template-parts/sections/home/hero','hero','GSAP Core · ورود متن، چرخش عبارت و حرکت جزئیات تصویر'),
 'home-services':('پنل‌های اسکرولی با پین · Radman','panels','offerings','template-parts/sections/home/offerings','offering-web','GSAP Core + ScrollTrigger + ScrollSmoother · پنل‌های پین‌شده و جزئیات گرافیکی'),
 'home-projects':('گالری با متن استیکی و ورود تصاویر · Radman','gallery','projects','template-parts/sections/home/projects','projects','GSAP Core + ScrollTrigger + ScrollSmoother · متن استیکی و ورود تصاویر'),
 'services-cover':('عنوان صفحه با تصویر یا ویدئو · Radman','page-title','cover','template-parts/layout/titles/cover','hero','ScrollTrigger + ScrollSmoother · پارالاکس و زوم تصویر/ویدئو'),
 'services-list':('فهرست با پیش‌نمایش تصویر · Radman','showcase','showcase','template-parts/sections/services/showcase','offering-wordpress','Vanilla JS + sticky مشترک · تغییر تصویر با انتخاب خدمت'),
 'services-strip':('نوار پیوستهٔ تصاویر · Radman','gallery','strip','template-parts/sections/services/strip','services-strip-48','GSAP Core + ScrollTrigger · حرکت افقی پیوسته و کشیدن'),
 'services-faq':('آکاردئون شماره‌دار · Radman','accordion','faq','template-parts/ui/faq','none','CSS + details بومی · باز و بسته شدن؛ بدون GSAP'),
}
registry={}
for slug,(title,group,key,template,image,motion) in mods.items():
    d=theme/'modules'/slug;d.mkdir(parents=True,exist_ok=True)
    args=''
    if key=='cover':args=",null,\\RadmanLibrary\\Samples\\cover_data()"
    if key=='faq':args=",null,['items'=>\\RadmanLibrary\\Samples\\fixture()['faq']]"
    (d/'view.php').write_text("<?php defined('ABSPATH')||exit; get_template_part('shared/radmanct/"+template+"'"+args+");\n",encoding='utf-8')
    registry[slug]={'title':title,'slug':slug,'group':group,'key':key,'image':image,'motion':motion,'source':template+'.php'}

# Module CSS entries preserve the original owners; common resets never touch the shell.
prefix="../../shared/radmanct/src/css/"
imports={
 'home-hero':['ui/text-motion.css','pages/home/hero.css'],
 'home-services':['ui/text-motion.css','pages/home/offerings.css'],
 'home-projects':['ui/text-motion.css','pages/home/projects.css'],
 'services-cover':['layout/page-title.css'],
 'services-faq':['ui/faq.css'],
}
home=(source/'src/css/pages/home.css').read_text(encoding='utf-8-sig')
home_rules=re.sub(r"@import\s+[^;]+;",'',home)
services=(source/'src/css/pages/services.css').read_text(encoding='utf-8-sig')
show,strip=services.split('.rdc-svc-strip {',1)
for slug in mods:
    text=''.join("@import '"+prefix+x+"';\n" for x in imports.get(slug,[]))
    if slug in ['home-hero','home-services']:text+=home_rules
    if slug=='services-list':text+=re.sub(r"@import\s+[^;]+;",'',show)
    if slug=='services-strip':text+='.rdc-svc-strip {'+strip
    (theme/'modules'/slug/'style.css').write_text(text,encoding='utf-8')

registry_path=theme/'inc/samples.json'
existing=json.loads(registry_path.read_text(encoding='utf-8')) if registry_path.exists() else {}
existing.update(registry)
registry_path.write_text(json.dumps(existing,ensure_ascii=False,indent=2),encoding='utf-8')
(root/'docs').mkdir(exist_ok=True)
(root/'docs/source-snapshot.json').write_text(json.dumps({'source':str(source),'files':copied},ensure_ascii=False,indent=2),encoding='utf-8')
# Deliberate library adapters: local nested paths, fixture count and archive link.
for path in target.rglob('*.php'):
    text=path.read_text(encoding='utf-8-sig')
    text=text.replace("'template-parts/", "'shared/radmanct/template-parts/")
    text=text.replace("get_template_directory().'/inc/navigation/panel-walker.php'", "get_template_directory().'/shared/radmanct/inc/navigation/panel-walker.php'")
    if path.name=='home.php':text=text.replace("if(!is_front_page()||post_password_required())", "if(!\\RadmanLibrary\\Samples\\raw()||!in_array(\\RadmanLibrary\\module(get_queried_object_id())['slug']??'',['home-hero','page-home'],true)||post_password_required())")
    path.write_text(text,encoding='utf-8')
# Keep the actual archive/detail markup; the workshop supplies only document chrome/query.
for filename,name in [('archive-project.php','archive'),('single-project.php','single')]:
    text=(source/filename).read_text(encoding='utf-8-sig')
    text=text.replace('get_header();','').replace('get_footer();','')
    text=text.replace("get_template_part('template-parts/layout/page-title');",'')
    text=re.sub(r'<main\b[^>]*>','',text,count=1).replace('</main>','')
    text=text.replace("'template-parts/", "'shared/radmanct/template-parts/")
    dest=target/'templates'/('portfolio-'+name+'.php');dest.parent.mkdir(exist_ok=True);dest.write_text(text,encoding='utf-8')
print(f'Selected {len(copied)} source files for {len(mods)} modules; source unchanged.')
