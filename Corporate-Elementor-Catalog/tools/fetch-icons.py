from pathlib import Path
import base64
import hashlib
import io
import json
import tarfile
import urllib.request

root = Path(__file__).resolve().parent.parent
manifest = json.loads((root / 'tools/icons.json').read_text(encoding='utf-8'))
destination = root / 'shared/icons/images'
sources = {}
for key, source in manifest['sources'].items():
    metadata_url = 'https://registry.npmjs.org/' + source['package'] + '/' + source['version']
    with urllib.request.urlopen(metadata_url, timeout=30) as response:
        metadata = json.load(response)
    with urllib.request.urlopen(metadata['dist']['tarball'], timeout=30) as response:
        payload = response.read()
    algorithm, digest = metadata['dist']['integrity'].split('-', 1)
    if algorithm != 'sha512' or base64.b64encode(hashlib.sha512(payload).digest()).decode() != digest:
        raise ValueError('Package integrity failed: ' + key)
    archive = tarfile.open(fileobj=io.BytesIO(payload))
    files = {}
    for entry in manifest['icons'].values():
        if entry['file'].startswith(key + '/'):
            filename = Path(entry['file']).name
            files[filename] = archive.extractfile(source['prefix'] + filename).read()
    files['LICENSE'] = archive.extractfile('package/LICENSE').read()
    for filename, data in files.items():
        target = destination / key / filename
        target.parent.mkdir(parents=True, exist_ok=True)
        target.write_bytes(data)
    sources[key] = {**source, 'tarball': metadata['dist']['tarball'], 'integrity': metadata['dist']['integrity'],
                    'files': {name: hashlib.sha256(data).hexdigest() for name, data in files.items()}}
(destination / 'sources.json').write_text(json.dumps(sources, indent=2) + '\n', encoding='utf-8')
print('Downloaded selected SVG files and licenses from verified npm archives.')
