import { readFile, writeFile } from 'node:fs/promises';
import { fileURLToPath, pathToFileURL } from 'node:url';
import { resolve } from 'node:path';

const root = fileURLToPath(new URL('../', import.meta.url));
const renderer = process.argv[2] ? pathToFileURL(resolve(process.argv[2])).href : '@resvg/resvg-js';
const { Resvg } = await import(renderer);
const logoSource = await readFile(resolve(root, 'radmanct/images/radman_logo.svg'), 'utf8');
const logo = logoSource.slice(logoSource.indexOf('<svg')).replace(/<svg([^>]+)>/, (_, attributes) =>
  `<svg${attributes.replace(/\s(?:width|height)="[^"]*"/g, '')} x="390" y="240" width="420" height="303">`);

for (const [folder, title, kind, subtitle] of [
  ['radmanct', 'RADMAN CT', 'WORDPRESS THEME', 'CORPORATE / PORTFOLIO / CATALOG'],
  ['radmanct-child', 'RADMAN CT CHILD', 'WORDPRESS CHILD THEME', 'CLIENT TEMPLATES / CUSTOM DESIGN'],
]) {
const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="900" viewBox="0 0 1200 900">
  <rect width="1200" height="900" fill="#f7f5f1"/>
  <rect x="36" y="36" width="1128" height="828" rx="24" fill="#fffefa" stroke="#ded8cf"/>
  <path d="M96 144H1104 M96 716H1104" stroke="#e6e0d7"/>
  <text x="96" y="106" fill="#706a61" font-family="Arial" font-size="19" letter-spacing="4">${kind}</text>
  <circle cx="1089" cy="99" r="7" fill="#afa18e"/>
  ${logo}
  <text x="600" y="614" text-anchor="middle" fill="#706a61" font-family="Arial" font-size="17" letter-spacing="3">${subtitle}</text>
  <text x="96" y="790" fill="#17191d" font-family="Arial" font-size="22">${title}</text>
</svg>`;
const preview = new Resvg(svg, { font: { loadSystemFonts: true, defaultFontFamily: 'Arial' } }).render();
if (preview.width !== 1200 || preview.height !== 900) throw new Error('Unexpected preview dimensions');
const png = preview.asPng();
await writeFile(resolve(root, folder, 'screenshot.png'), png);
console.log(`Created ${folder}/screenshot.png: 1200 x 900, ${png.length} bytes`);
}
