from pathlib import Path
import json
import subprocess
import sys
import tempfile

root = Path(__file__).resolve().parent.parent
with tempfile.TemporaryDirectory(prefix='rdct-icons-') as temporary:
    folder = Path(temporary)
    svg = folder / 'factory.svg'
    manifest = folder / 'icons.json'
    output = folder / 'catalog.php'
    manifest.write_text(json.dumps({'icons': {'client-factory': {'file': 'factory.svg', 'label': 'Factory', 'directional': False}}}), encoding='utf-8')
    command = [sys.executable, str(root / 'tools/build-icons.py'), '--source', str(folder), '--manifest', str(manifest), '--output', str(output)]
    svg.write_text('<svg viewBox="0 0 32 16" fill="currentColor"><path d="M0 0h32v16H0z"/></svg>', encoding='utf-8')
    subprocess.run(command, check=True, capture_output=True)
    good = output.read_bytes()
    assert b'client-factory' in good and b'0 0 32 16' in good
    for invalid in ('<svg viewBox="0 0 24 24"><script>alert(1)</script></svg>',
                    '<svg viewBox="0 0 24 24"><path d="M0 0" onload="alert(1)"/></svg>',
                    '<svg viewBox="0 0 24 24"><path fill="url(https://example.test/a)"/></svg>'):
        svg.write_text(invalid, encoding='utf-8')
        result = subprocess.run(command, capture_output=True)
        assert result.returncode != 0 and output.read_bytes() == good
    manifest.write_text(json.dumps({'icons': {'client-factory': {'file': '../outside.svg', 'label': 'Factory', 'directional': False}}}), encoding='utf-8')
    assert subprocess.run(command, capture_output=True).returncode != 0
    assert output.read_bytes() == good
    manifest.write_text(json.dumps({'icons': {'real-estate-building': {'file': 'factory.svg', 'label': 'Building', 'directional': False}}}), encoding='utf-8')
    svg.write_text('<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="3" d="M2 2h20v20H2z"/></svg>', encoding='utf-8')
    subprocess.run(command + ['--pack', 'real-estate'], check=True, capture_output=True)
    packed = output.read_bytes()
    assert b"'pack' => 'real-estate'" in packed and b'stroke-width="3"' not in packed
    assert subprocess.run(command + ['--pack', 'retail'], capture_output=True).returncode != 0
    assert output.read_bytes() == packed
    svg.write_text('<svg viewBox="0 0 32 16" fill="none" stroke="currentColor"><path d="M0 0h32v16H0z"/></svg>', encoding='utf-8')
    assert subprocess.run(command + ['--pack', 'real-estate'], capture_output=True).returncode != 0
    assert output.read_bytes() == packed
print('Passed project SVG generation, non-24 viewBox, script/events/external references/path rejection and preservation of last valid catalog.')
