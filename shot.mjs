import { chromium } from 'playwright';
const b = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium' });
const p = await b.newPage({ viewport: { width: 1280, height: 900 } });
await p.goto('http://127.0.0.1:8123/en', { waitUntil: 'networkidle' });
await p.screenshot({ path: '/tmp/claude-0/en-chrome.png' });
await p.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
await p.waitForTimeout(1200);
await p.screenshot({ path: '/tmp/claude-0/en-footer.png' });
await b.close();
