import hashlib
import io
import json
from pathlib import Path
from urllib.request import Request, urlopen
from zipfile import ZipFile

VERSION = '1.1.1'
REFERENCE = '1976ff93c8d8a234cdd3ff99626bd43ecf11bf49'
URL = f'https://api.github.com/repos/darylldoyle/svg-sanitizer/zipball/{REFERENCE}'
target = Path(__file__).resolve().parent.parent / 'radmanct/vendor/svg-sanitize'
payload = urlopen(Request(URL, headers={'User-Agent': 'RadmanCT-local-vendor-build'}), timeout=30).read()
files = {}
with ZipFile(io.BytesIO(payload)) as archive:
    for name in archive.namelist():
        parts = name.split('/', 1)
        if len(parts) != 2:
            continue
        relative = parts[1]
        if relative == 'LICENSE' or (relative.startswith('src/') and relative.endswith('.php')):
            path = target / relative
            if not path.resolve().is_relative_to(target.resolve()):
                raise RuntimeError('Invalid vendor path')
            data = archive.read(name)
            if relative.endswith('.php'):
                data = data.replace(b'enshrined\\svgSanitize', b'RadmanCT\\Vendor\\SvgSanitize')
            path.parent.mkdir(parents=True, exist_ok=True)
            path.write_bytes(data)
            files[relative] = hashlib.sha256(data).hexdigest()
(target / 'manifest.json').write_text(json.dumps({'package': 'enshrined/svg-sanitize', 'version': VERSION, 'reference': REFERENCE, 'source': URL, 'namespace': 'RadmanCT\\Vendor\\SvgSanitize', 'archive_sha256': hashlib.sha256(payload).hexdigest(), 'files': files}, indent=2) + '\n', encoding='utf-8')
print(f'Vendored svg-sanitize {VERSION}: {len(files)} runtime/license files.')
