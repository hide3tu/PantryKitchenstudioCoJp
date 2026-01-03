import { chromium } from 'playwright';

const pages = [
  { path: '/', name: 'home' },
  { path: '/staff/', name: 'staff' },
  { path: '/works/', name: 'works' },
  { path: '/contact/', name: 'contact' },
];

(async () => {
  const browser = await chromium.launch();
  
  // Desktop screenshots
  console.log('--- Desktop (1280x900) ---');
  const desktopPage = await browser.newPage({ viewport: { width: 1280, height: 900 } });
  
  for (const p of pages) {
    await desktopPage.goto(`http://localhost:4324${p.path}`, { waitUntil: 'networkidle' });
    await desktopPage.waitForTimeout(500);
    await desktopPage.screenshot({ path: `screenshot-${p.name}-desktop.png`, fullPage: true });
    console.log(`Saved: screenshot-${p.name}-desktop.png`);
  }
  
  // Mobile screenshots
  console.log('\n--- Mobile (375x667) ---');
  const mobilePage = await browser.newPage({ viewport: { width: 375, height: 667 } });
  
  for (const p of pages) {
    await mobilePage.goto(`http://localhost:4324${p.path}`, { waitUntil: 'networkidle' });
    await mobilePage.waitForTimeout(500);
    await mobilePage.screenshot({ path: `screenshot-${p.name}-mobile.png`, fullPage: true });
    console.log(`Saved: screenshot-${p.name}-mobile.png`);
  }
  
  // Check a work detail page
  console.log('\n--- Work Detail Page ---');
  await desktopPage.goto('http://localhost:4324/works/');
  const firstWorkLink = await desktopPage.$('a[href^="/works/"]');
  if (firstWorkLink) {
    const href = await firstWorkLink.getAttribute('href');
    if (href && href !== '/works/') {
      await desktopPage.goto(`http://localhost:4324${href}`);
      await desktopPage.waitForTimeout(1500);
      await desktopPage.screenshot({ path: 'screenshot-work-detail-desktop.png', fullPage: true });
      console.log('Saved: screenshot-work-detail-desktop.png');
      
      await mobilePage.goto(`http://localhost:4324${href}`);
      await mobilePage.waitForTimeout(1500);
      await mobilePage.screenshot({ path: 'screenshot-work-detail-mobile.png', fullPage: true });
      console.log('Saved: screenshot-work-detail-mobile.png');
    }
  }
  
  await browser.close();
  console.log('\nDone!');
})();
