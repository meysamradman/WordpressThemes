"""Generate a portable source map; no runtime registry, downloads or customer writes."""
from pathlib import Path
import json,re,posixpath
root=Path(__file__).resolve().parent.parent
theme=root/'radman-library'
registry=json.loads((theme/'inc/samples.json').read_text(encoding='utf-8'))
def relative_graph(start):
    seen=set()
    def visit(rel):
        if rel in seen or not (theme/rel).is_file():return
        seen.add(rel);text=(theme/rel).read_text(encoding='utf-8-sig')
        if rel.endswith(('.js','.css')):
            for ref in re.findall(r'''(?:from\s*|import\s*\(\s*|import\s*|@import\s*)["']([^"']+)["']''',text):
                if ref.startswith('.'):visit(posixpath.normpath(str(Path(rel).parent/ref).replace('\\','/')))
        if rel.endswith('.php'):
            for ref in re.findall(r'''get_template_part\s*\(\s*["']([^"']+)["']''',text):visit(ref+'.php')
            for ref in re.findall(r'''get_template_directory\(\)\s*\.\s*["']/([^"']+)["']''',text):visit(ref)
            for ref in re.findall(r'''__DIR__\s*\.\s*["']/([^"']+)["']''',text):visit(str(Path(rel).parent/ref).replace('\\','/'))
            if rel.endswith('offering-panel.php'):
                for p in (theme/'shared/radmanct/template-parts/sections/home').glob('offering-*.php'):visit(p.relative_to(theme).as_posix())
    for item in start:visit(item)
    return sorted(seen)
out={}
for slug,module in registry.items():
    entries=[p.relative_to(theme).as_posix() for p in (theme/'modules'/slug).glob('*') if p.is_file() and p.suffix in ['.php','.css','.js']]
    helpers=[]
    if slug.startswith('home-') or slug=='page-home':helpers+=['shared/radmanct/inc/home.php']
    if slug in ['portfolio-archive','portfolio-single']:
        helpers+=['shared/radmanct/inc/content/projects.php','shared/radmanct/inc/media.php','shared/radmanct/inc/content/continuation.php','shared/radmanct/templates/portfolio-'+slug.split('-')[1]+'.php']
    if slug.startswith('blog-'):helpers+=['shared/radmanct/inc/blog.php','shared/radmanct/inc/media.php','shared/radmanct/inc/content/continuation.php','shared/radmanct/templates/blog-'+slug.split('-')[1]+'.php']
    if slug in ['column-gallery','media-stretch','sticky-cards','text-marquee','text-loop']:helpers+=['shared/radmanct/inc/page-title.php']
    if slug in ['image-accordion','card-accordion']:helpers+=['shared/radmanct/inc/page-title.php']
    if slug.startswith('catalog-'):helpers+=['inc/catalog.php','src/js/admin/catalog-editor.js']
    if slug.startswith('music-'):helpers+=['inc/music.php','src/js/admin/music-editor.js']
    if slug in ['header-radman','footer-radman','page-home','page-services']:helpers+=['shared/radmanct/inc/brand.php','shared/radmanct/inc/navigation.php']
    if slug in ['services-cover','services-strip','page-services','wellness-scroll','project-marquee']:helpers+=['shared/radmanct/inc/page-title.php']
    if slug in ['home-hero','home-services','services-cover','page-home','page-services']:helpers+=['shared/radmanct/inc/assets.php']
    if slug!='robot':helpers+=['shared/radmanct/inc/ui/button.php','shared/radmanct/inc/icons.php','shared/radmanct/inc/icons/catalog.php']
    out[slug]={'kind':module.get('kind','section'),'family':module['group'],'effects':module.get('effects',[]),'entries':entries,'source_files':relative_graph(entries+helpers+['src/css/client-base.css']),'css_base':'src/css/client-base.css','php_contracts':['Native WordPress APIs','Use existing RadmanCT helpers once; replace workshop fixture adapters with client data'],'preview_only':['inc/samples.php','inc/portfolio.php','preview.php','assets/bar.css'],'vendor':'local GSAP 3.15.0 only if consumed; never one vendor copy per sample'}
    if slug=='robot':
        out[slug].update(vendor='Local Three.js (exact version in package-lock.json); dynamic viewer only, no GSAP or Spline',assets=['modules/robot/models/robot.glb','modules/robot/poster.svg'],build_tools=['tools/build-robot.mjs'],php_contracts=['Native WordPress APIs','rdl_robot_data supplies per-client config; no workshop data required'])
(root/'docs/module-map.json').write_text(json.dumps(out,ensure_ascii=False,indent=2),encoding='utf-8')
print(f'{len(out)} source maps generated; workshop glue is labeled separately.')
