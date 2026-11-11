"""Package this reviewed local build. Does not apply patches or touch a new update."""
from pathlib import Path
import difflib
import hashlib
import json
import zipfile

root = Path(__file__).resolve().parents[1]
releases = root / 'releases'
releases.mkdir(exist_ok=True)
sha = lambda data: hashlib.sha256(data).hexdigest()
names = json.loads((root / 'changed-files.json').read_text(encoding='utf-8'))
manifest = []
patch = []
with zipfile.ZipFile(root / 'backups/hub-6.0.3-before-local.zip') as original:
    assert len(original.namelist()) == len(set(original.namelist())), 'Duplicate backup entries'
    for name in names:
        before = original.read(name)
        after = (root / name).read_bytes()
        if before == after:
            continue
        manifest.append({'file': name, 'supplied_sha256': sha(before), 'local_sha256': sha(after)})
        patch.extend(difflib.unified_diff(before.decode('utf-8').splitlines(keepends=True), after.decode('utf-8').splitlines(keepends=True), fromfile='supplied/' + name, tofile='local/' + name))
    for name in ['hub-core/landing-hub-core.php', 'hub-core/extras/mailchimp/mailchimp.php', 'hub-core/extras/class/tmhOAuth.php', 'hub/liquid/admin/views/liquid-features.php', 'hub-elementor-addons/hub-elementor-addons.php']:
        assert original.read(name) == (root / name).read_bytes(), 'Restoration mismatch: ' + name
    name = 'hub-elementor-addons/elementor/template-library/template-library.php'
    before = original.read(name).decode('utf-8').replace('\r\n', '\n')
    expected = before.replace("\t\tif ( 'valid' != get_option( 'hub_purchase_code_status', false ) ) {\n\t\t\treturn new \\WP_Error( 'no_license', esc_html__( 'Please Active The Hub Theme') );\n\t\t}\n", '\t\t// Local build: retain the original collection API without a license gate.\n')
    assert expected == (root / name).read_text(encoding='utf-8'), 'Unexpected library changes'

(root / 'docs/change-manifest.json').write_text(json.dumps({'theme_version': '6.0.3.2', 'addon_api_version': '6.0.3', 'scope': 'license removal; vendor update discovery preserved with cache; original library retained', 'files': manifest}, indent=2), encoding='utf-8')
(root / 'docs/license-changes.patch').write_text(''.join(patch), encoding='utf-8', newline='\n')
packages = []
for folder, filename in [('hub', 'hub-6.0.3.2.zip'), ('hub-elementor-addons', 'hub-elementor-addons-6.0.3-local1.zip'), ('hub-core', 'hub-core-6.0.3-original.zip')]:
    target = releases / filename
    with zipfile.ZipFile(target, 'w', zipfile.ZIP_DEFLATED) as z:
        for path in sorted((root / folder).rglob('*')):
            if path.is_file():
                z.write(path, path.relative_to(root).as_posix())
    with zipfile.ZipFile(target) as z:
        assert z.testzip() is None
        assert all(n.startswith(folder + '/') for n in z.namelist())
        count = len(z.namelist())
    packages.append({'file': filename, 'sha256': sha(target.read_bytes()), 'bytes': target.stat().st_size, 'entries': count})
(releases / 'manifest.json').write_text(json.dumps(packages, indent=2), encoding='utf-8')
(root / 'changed-files.json').write_text(json.dumps([entry['file'] for entry in manifest], indent=2), encoding='utf-8')
print(f'PASS: {len(manifest)} changed files; original core restored; library diff limited to license guard.')
for package in packages:
    print(f"{package['file']}: {package['bytes'] // 1024 // 1024} MiB; ZIP integrity passed")
