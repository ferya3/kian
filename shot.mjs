import { chromium } from 'playwright';
const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium' });
for (const loc of ['en']) {
  const p = await b.newPage({ viewport: { width: 1280, height: 900 } });
  await p.goto(`http://127.0.0.1:8123/${loc}`, { waitUntil: 'networkidle' });
  // scroll through so reveal animations fire, then capture full page
  await p.evaluate(async () => { for (let y = 0; y < document.body.scrollHeight; y += 600) { window.scrollTo(0, y); await new Promise(r => setTimeout(r, 60)); } });
  await p.waitForTimeout(900);
  await p.screenshot({ path: `/tmp/claude-0/${loc}-full.png`, fullPage: true });
  await p.close();
}
await b.close();
