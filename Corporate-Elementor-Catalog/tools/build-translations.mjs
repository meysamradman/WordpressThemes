import { mkdir, readFile, writeFile } from 'node:fs/promises';
import { resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const workspace = fileURLToPath(new URL('../', import.meta.url));
const catalogs = JSON.parse(await readFile(resolve(workspace, 'tools/translations/fa_IR.json'), 'utf8'));

function compile(messages) {
  const entries = Object.entries(messages).sort(([left], [right]) => Buffer.compare(Buffer.from(left), Buffer.from(right)));
  const strings = [];
  const header = Buffer.alloc(28 + entries.length * 16);
  header.writeUInt32LE(0x950412de, 0);
  header.writeUInt32LE(entries.length, 8);
  header.writeUInt32LE(28, 12);
  header.writeUInt32LE(28 + entries.length * 8, 16);
  header.writeUInt32LE(header.length, 24);
  let offset = header.length;
  for (const column of [0, 1]) {
    entries.forEach((entry, index) => {
      const value = Buffer.from(entry[column], 'utf8');
      const position = 28 + column * entries.length * 8 + index * 8;
      header.writeUInt32LE(value.length, position);
      header.writeUInt32LE(offset, position + 4);
      strings.push(value, Buffer.from([0]));
      offset += value.length + 1;
    });
  }
  return Buffer.concat([header, ...strings]);
}

for (const [domain, strings] of Object.entries(catalogs)) {
  const directory = resolve(workspace, domain, 'languages');
  const header = await readFile(resolve(workspace, domain, ['radmanct-content', 'radmanct-mcp', 'radmanct-components'].includes(domain) ? `${domain}.php` : 'style.css'), 'utf8');
  const version = header.match(/^[\t /*#@]*Version:[\t ]*([0-9A-Za-z.+-]+)/m)?.[1];
  if (!version) throw new Error(`Missing version: ${domain}`);
  const metadata = `Project-Id-Version: ${domain} ${version}\nLanguage: fa_IR\nMIME-Version: 1.0\nContent-Type: text/plain; charset=UTF-8\nContent-Transfer-Encoding: 8bit\nPlural-Forms: nplurals=2; plural=(n > 1);\n`;
  const messages = { '': metadata, ...strings };
  const po = Object.entries(messages).map(([key, value]) => `msgid ${JSON.stringify(key)}\nmsgstr ${JSON.stringify(value)}\n`).join('\n');
  const pot = Object.keys(messages).map((key) => `msgid ${JSON.stringify(key)}\nmsgstr ${JSON.stringify(key ? '' : metadata.replace('Language: fa_IR\n', 'Language: \n'))}\n`).join('\n');
  await mkdir(directory, { recursive: true });
  const localeName = ['radmanct-content', 'radmanct-mcp', 'radmanct-components'].includes(domain) ? `${domain}-fa_IR` : 'fa_IR';
  await writeFile(resolve(directory, `${localeName}.po`), po);
  await writeFile(resolve(directory, `${localeName}.mo`), compile(messages));
  await writeFile(resolve(directory, `${domain}.pot`), pot);
}
