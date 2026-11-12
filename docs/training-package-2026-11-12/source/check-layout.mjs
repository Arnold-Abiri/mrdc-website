import { chromium } from '@playwright/test';
import { fileURLToPath } from 'node:url';
import path from 'node:path';
import { mkdir, writeFile } from 'node:fs/promises';
const root=path.resolve(path.dirname(fileURLToPath(import.meta.url)),'..');
await mkdir('/tmp/mrdc-slide-layout-review',{recursive:true});
const browser=await chromium.launch({headless:true,executablePath:'/home/arnold-nyasha/.cache/ms-playwright/chromium-1243/chrome-linux64/chrome'});
const page=await browser.newPage({viewport:{width:1320,height:780},deviceScaleFactor:1});
await page.goto('file://'+path.join(root,'09-evidence/Slide-Layout-Review.html'));
await page.evaluate(()=>document.fonts.ready);
const overflow=await page.locator('.text').evaluateAll(els=>els.map(e=>{
 const lines=[...e.querySelectorAll('p')];
 // Paragraph trailing spacing does not consume text-frame height in native Office.
 const height=lines.reduce((s,p)=>s+p.getBoundingClientRect().height,0);
 return {slide:e.dataset.slide,shape:e.dataset.shape,height,box:e.clientHeight,text:e.innerText};
}).filter(e=>e.height>e.box+4));
for(let i=1;i<=38;i++)await page.locator('#slide-'+i).screenshot({path:'/tmp/mrdc-slide-layout-review/'+String(i).padStart(2,'0')+'.png'});
await writeFile(path.join(root,'09-evidence/Slide-Layout-Review-Results.json'),JSON.stringify({method:'Browser approximation from actual PPTX coordinates, fonts and images; not native Office rendering.',overflow},null,2));
await browser.close();console.log(JSON.stringify({slides:38,overflow},null,2));
