from pathlib import Path
import base64
import hashlib
import io
import json
import tarfile
from urllib.request import urlopen

VERSION = '1.43.2'
ROOT = Path(__file__).resolve().parents[1]
metadata = json.load(urlopen(f'https://registry.npmjs.org/ace-builds/{VERSION}'))
archive = urlopen(metadata['dist']['tarball']).read()
integrity = 'sha512-' + base64.b64encode(hashlib.sha512(archive).digest()).decode()
if integrity != metadata['dist']['integrity']:
    raise RuntimeError('Ace archive integrity mismatch')
names = ['ace.js', 'ext-language_tools.js', 'theme-textmate.js', 'theme-merbivore_soft.js']
languages = ['css', 'html', 'javascript', 'json', 'php', 'text']
names += [f'mode-{language}.js' for language in languages]
names += [f'worker-{language}.js' for language in ['css', 'html', 'javascript', 'json']]
target = ROOT / 'radmanct/vendor/ace'
target.mkdir(parents=True, exist_ok=True)
files = {}
with tarfile.open(fileobj=io.BytesIO(archive), mode='r:gz') as package:
    available = set(package.getnames())
    names += [f'snippets/{language}.js' for language in languages if f'package/src-min-noconflict/snippets/{language}.js' in available]
    for name in names:
        contents = package.extractfile(f'package/src-min-noconflict/{name}').read()
        destination = target / name
        destination.parent.mkdir(parents=True, exist_ok=True)
        destination.write_bytes(contents)
        files[name] = hashlib.sha256(contents).hexdigest()
    license_data = package.extractfile('package/LICENSE').read()
    (target / 'LICENSE').write_bytes(license_data)
    files['LICENSE'] = hashlib.sha256(license_data).hexdigest()
(target / 'manifest.json').write_text(json.dumps({'package': 'ace-builds', 'version': VERSION, 'tarball': metadata['dist']['tarball'], 'integrity': integrity, 'files': files}, indent=2) + '\n', encoding='utf-8')
print(f'Vendored Ace {VERSION}: {len(files)} files')
