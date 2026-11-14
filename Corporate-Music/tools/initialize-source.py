from pathlib import Path
import shutil,re
src=Path('.');dst=Path('../Corporate-Music');lib=Path('../Radman-Design-Library/radman-library')
assert not (dst/'radmanct').exists(),'Destination theme already exists; inspect before copying'
shutil.copytree(src/'radmanct',dst/'radmanct')
for f in ['package.json','package-lock.json']:shutil.copy2(src/f,dst/f)
for f in ['build.mjs','build-translations.mjs']:shutil.copy2(src/'tools'/f,dst/'tools'/f)
for f in ['naming.md','wordpress.md']:shutil.copy2(src/'docs'/f,dst/'docs'/f)
for f in ['compose.yaml','manage.ps1','local-boundaries.php','php.ini']:
 s=(src/'tools/local-wordpress'/f).read_text(encoding='utf-8-sig').replace('WEBTALIK','MUSIC').replace('Webtalik','Corporate Music').replace('webtalik','corporate-music').replace('8095','8098')
 if f=='compose.yaml':s=s.replace('MUSIC_ADMIN_USER: radman','MUSIC_ADMIN_USER: music_owner_26')
 if f=='manage.ps1':s=s.replace("'artifacts/local-wordpress-access.txt'","'.local-admin.txt'").replace('Username: radman','Username: music_owner_26')
 (dst/'tools/local-wordpress'/f).write_text(s,encoding='utf-8')
# Blog module snapshot: adapt only workshop paths and the archive URL.
for directory in ['template-parts/blog','src/css/pages/blog']:
 target=dst/'radmanct'/directory;shutil.copytree(lib/'shared/radmanct'/directory,target,dirs_exist_ok=True)
for file in ['src/css/pages/blog.css','inc/blog.php']:
 shutil.copy2(lib/'shared/radmanct'/file,dst/'radmanct'/file)
for p in (dst/'radmanct/template-parts/blog').glob('*.php'):
 p.write_text(p.read_text(encoding='utf-8-sig').replace('shared/radmanct/',''),encoding='utf-8')
for kind,target in [('archive','home.php'),('single','single.php')]:
 s=(lib/f'shared/radmanct/templates/blog-{kind}.php').read_text(encoding='utf-8-sig').replace('shared/radmanct/','').replace('\\RadmanLibrary\\Blog\\url()',"get_permalink((int)get_option('page_for_posts'))")
 (dst/'radmanct'/target).write_text("<?php defined('ABSPATH')||exit;get_header(); ?>\n<main id=\"main-content\" tabindex=\"-1\">\n"+s+"\n</main><?php get_footer(); ?>\n",encoding='utf-8')
# Standalone native track owner from the library; no workshop renderer/query.
s=(lib/'inc/music.php').read_text(encoding='utf-8-sig').replace('RadmanLibrary\\Music','RadmanCT\\Music').replace('rdl-','rdc-').replace('rdl_','rdc_')
s=re.sub(r'function render\(string \$layout\):void \{.*?\n\}', '',s,flags=re.S)
s=s.replace("wp_enqueue_media();wp_enqueue_script('rdc-track-editor',get_template_directory_uri().'/src/js/admin/music-editor.js',['media-editor'],filemtime(get_template_directory().'/src/js/admin/music-editor.js'),true);","wp_enqueue_media();\\RadmanCT\\Assets\\script('admin/music-editor');")
(dst/'radmanct/inc/music.php').write_text(s,encoding='utf-8')
shutil.copy2(lib/'src/js/admin/music-editor.js',dst/'radmanct/src/js/admin/music-editor.js')
p=dst/'radmanct/src/js/admin/music-editor.js';p.write_text(p.read_text(encoding='utf-8').replace('rdl-','rdc-'),encoding='utf-8')
for kind in ['archive','single']:
 s=(lib/f'modules/music-{kind}/template.php').read_text(encoding='utf-8-sig').replace('\\RadmanLibrary\\Music\\','\\RadmanCT\\Music\\').replace('rdl-','rdc-')
 p=dst/f'radmanct/template-parts/music/{kind}.php';p.parent.mkdir(parents=True,exist_ok=True);p.write_text(s,encoding='utf-8')
 p=dst/f'radmanct/{"archive-track" if kind=="archive" else "single-track"}.php';p.write_text("<?php defined('ABSPATH')||exit;get_header(); ?><main id=\"main-content\" tabindex=\"-1\"><?php get_template_part('template-parts/music/"+kind+"'); ?></main><?php get_footer(); ?>\n",encoding='utf-8')
(dst/'radmanct/taxonomy-music_category.php').write_text("<?php defined('ABSPATH')||exit;require __DIR__.'/archive-track.php';\n",encoding='utf-8')
s=(lib/'src/css/music.css').read_text(encoding='utf-8').replace('rdl-','rdc-').replace('border-radius:1.25rem','border-radius:var(--rdc-rad)').replace('border-radius:1.5rem','border-radius:var(--rdc-rad)')
(dst/'radmanct/src/css/ui/music.css').write_text(s,encoding='utf-8');(dst/'radmanct/src/css/pages/music.css').write_text("@import '../ui/music.css';\n",encoding='utf-8')
p=dst/'radmanct/functions.php';s=p.read_text(encoding='utf-8').replace("'setup','assets'","'setup','music','assets'");p.write_text(s,encoding='utf-8')
p=dst/'radmanct/inc/setup.php';s=p.read_text(encoding='utf-8').replace("if($query->is_search())$query->set('post_type','post');","if($query->is_search()&&$query->get('post_type')!=='track')$query->set('post_type','post');");p.write_text(s,encoding='utf-8')
p=dst/'radmanct/inc/assets.php';s=p.read_text(encoding='utf-8').replace("style('site');script('site');","style('site');script('site');\n if(is_front_page()||\\RadmanCT\\Music\\native())style('pages/music',array('rdc-site'));\n if(\\RadmanCT\\Music\\native())return;");p.write_text(s,encoding='utf-8')
p=dst/'tools/build.mjs';s=p.read_text(encoding='utf-8').replace("entries.push('css/pages/service','js/pages/service');","entries.push('css/pages/service','js/pages/service','css/pages/music','js/admin/music-editor');");p.write_text(s,encoding='utf-8')
p=dst/'radmanct/inc/ui/button.php';s=p.read_text(encoding='utf-8').replace("$attributes='';","$attributes=!empty($args['download'])?' download':'';");p.write_text(s,encoding='utf-8')
p=dst/'radmanct/style.css';s=p.read_text(encoding='utf-8').replace('Theme Name: Radman CT','Theme Name: Radman CT Music').replace('Description: A standalone custom PHP theme for corporate, service and portfolio websites.','Description: Independent music and editorial client edition of the Radman CT custom theme.');p.write_text(s,encoding='utf-8')
p=dst/'.gitignore';p.write_text(p.read_text(encoding='utf-8')+'\nartifacts/\noutput/\n*.sql\n*.sql.gz\n*.zip\n',encoding='utf-8')
print('Independent source copied; music and blog modules adapted. No source databases or credentials copied.')
