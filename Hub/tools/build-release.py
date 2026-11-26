"""Package the reviewed source and regenerate its maintenance manifest."""

import argparse
import difflib
import hashlib
import json
import re
import tempfile
import zipfile
from pathlib import Path


def file_version(path):
    return re.search(r"(?m)^\s*\*?\s*Version:\s*([\d.]+)", path.read_text(encoding="utf-8")).group(1)


def patch_lines(data):
    lines = data.decode("utf-8").splitlines(keepends=True)
    return [line.replace("\r\n", "\n") for line in lines]


def write_json(path, data):
    path.write_text(json.dumps(data, indent=2) + "\n", encoding="utf-8")


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--root", type=Path, default=Path(__file__).resolve().parents[1])
    parser.add_argument("--baseline", type=Path)
    args = parser.parse_args()
    root = args.root
    theme_version = file_version(root / "hub/style.css")
    upstream = theme_version.rsplit(".", 1)[0]
    addon_version = file_version(root / "hub-elementor-addons/hub-elementor-addons.php")
    core_version = file_version(root / "hub-core/landing-hub-core.php")
    baseline = args.baseline or root / "backups" / f"hub-{upstream}-before-local.zip"
    records, patch = [], []
    names = json.loads((root / "changed-files.json").read_text(encoding="utf-8"))
    if len(set(names)) != len(names):
        raise ValueError("Duplicate entries in changed-files.json")

    with zipfile.ZipFile(baseline) as original:
        for name in names:
            before, after = original.read(name), (root / name).read_bytes()
            if before == after:
                raise ValueError(f"No changes in {name}")
            records.append({
                "file": name,
                "supplied_sha256": hashlib.sha256(before).hexdigest(),
                "local_sha256": hashlib.sha256(after).hexdigest(),
            })
            for line in difflib.unified_diff(patch_lines(before), patch_lines(after), f"supplied/{name}", f"local/{name}"):
                patch.append(line if line.endswith("\n") else line + "\n\\ No newline at end of file\n")

    docs = root / "docs"
    releases = root / "releases"
    docs.mkdir(exist_ok=True)
    releases.mkdir(exist_ok=True)
    write_json(docs / "change-manifest.json", {
        "theme_version": theme_version, "upstream_version": upstream, "addon_api_version": addon_version,
        "core_version": core_version, "scope": "license removal with vendor updates and original Collections retained",
        "files": records,
    })
    (docs / "license-changes.patch").write_text("".join(patch), encoding="utf-8", newline="\n")
    with tempfile.TemporaryDirectory(dir=releases) as work:
        work = Path(work)
        packages = []
        for folder, filename in (
            ("hub", f"hub-{theme_version}.zip"),
            ("hub-elementor-addons", f"hub-elementor-addons-{addon_version}-local1.zip"),
            ("hub-core", f"hub-core-{core_version}-original.zip"),
        ):
            target = work / filename
            with zipfile.ZipFile(target, "w", zipfile.ZIP_DEFLATED) as archive:
                for path in sorted((root / folder).rglob("*")):
                    if path.is_file():
                        archive.write(path, path.relative_to(root).as_posix())
            with zipfile.ZipFile(target) as archive:
                if archive.testzip() is not None:
                    raise ValueError(f"Invalid archive {filename}")
                count = len(archive.namelist())
            with target.open("rb") as file:
                checksum = hashlib.file_digest(file, "sha256").hexdigest()
            packages.append({"file": filename, "sha256": checksum, "bytes": target.stat().st_size, "entries": count})
            print(f"PASS: {filename}, {count} files")
        write_json(work / "manifest.json", packages)
        bundle = work / f"Hub-{theme_version}-package.zip"
        with zipfile.ZipFile(bundle, "w") as archive:
            for filename in [row["file"] for row in packages] + ["manifest.json"]:
                archive.write(work / filename, filename)
        with zipfile.ZipFile(bundle) as archive:
            if archive.testzip() is not None:
                raise ValueError(f"Invalid delivery package {bundle.name}")
        bundle.replace(releases / bundle.name)


if __name__ == "__main__":
    main()
