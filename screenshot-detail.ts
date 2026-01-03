import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage({ viewport: { width: 1280, height: 900 } });
  
  // 正しいスラッグで作品詳細ページへ
  await page.goto('http://localhost:4324/works/princessession-orchestra/');
  await page.waitForTimeout(1500);
  await page.screenshot({ path: 'screenshot-work-detail-desktop.png', fullPage: true });
  console.log('Saved: screenshot-work-detail-desktop.png');
  
  // Mobile
  const mobilePage = await browser.newPage({ viewport: { width: 375, height: 667 } });
  await mobilePage.goto('http://localhost:4324/works/princessession-orchestra/');
  await mobilePage.waitForTimeout(1500);
  await mobilePage.screenshot({ path: 'screenshot-work-detail-mobile.png', fullPage: true });
  console.log('Saved: screenshot-work-detail-mobile.png');
  
  await browser.close();
})();
