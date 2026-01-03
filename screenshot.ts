import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: 1280, height: 2400 } });
  await page.goto('http://localhost:4321');
  await page.waitForTimeout(2000);
  await page.screenshot({ path: 'screenshot-home-full.png', fullPage: true });
  console.log('Screenshot saved: screenshot-home-full.png');
  await browser.close();
})();
