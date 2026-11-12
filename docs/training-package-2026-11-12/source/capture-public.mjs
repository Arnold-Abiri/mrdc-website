import { chromium } from '@playwright/test';
import { mkdir, writeFile } from 'node:fs/promises';
import { fileURLToPath } from 'node:url';
import path from 'node:path';
const root=path.resolve(path.dirname(fileURLToPath(import.meta.url)),'..');
await mkdir(path.join(root,'assets/screenshots'),{recursive:true});
const browser=await chromium.launch({headless:true,executablePath:'/home/arnold-nyasha/.cache/ms-playwright/chromium-1243/chrome-linux64/chrome'});
const context=await browser.newContext({viewport:{width:1440,height:1000},deviceScaleFactor:1});
const register=[];
for(const [id,url,expected] of [
 ['home','https://www.mutokordc.co.zw/en','Home'],
 ['documents','https://www.mutokordc.co.zw/en/documents','Documents'],
 ['vacancies','https://www.mutokordc.co.zw/en/vacancies','Vacancies'],
 ['transparency','https://www.mutokordc.co.zw/en/transparency','Transparency'],
 ['login','https://www.mutokordc.co.zw/admin/login','login'],
]){
 const page=await context.newPage();
 try{
  const response=await page.goto(url,{waitUntil:'networkidle',timeout:45000});
  await page.locator('body').waitFor();await page.evaluate(()=>document.fonts.ready);
  if(response.status()!==200)throw new Error('HTTP '+response.status());
  if(expected==='login')await page.getByText('Sign in to your account',{exact:true}).waitFor({timeout:10000});
  else {const component=await page.locator('script[type="application/json"][data-page]').evaluate(el=>JSON.parse(el.textContent).component);if(component!==expected)throw new Error('Unexpected component '+component);await page.locator('main').waitFor();}
  const file='assets/screenshots/'+id+'.png';await page.screenshot({path:path.join(root,file),fullPage:false});
  register.push({id,url,status:'captured-awaiting-visual-review',file,captured_at:new Date().toISOString(),viewport:'1440 × 1000',evidence:'Live browser response 200 and expected component/login heading; no form submitted.'});
 }catch(e){register.push({id,url,status:'pending',reason:String(e),viewport:'1440 × 1000'});}
 await page.close();
}
register.push({id:'admin-pending',status:'pending',reason:'No authorized isolated authenticated session provided. Required views: dashboard/navigation/profile; Page/editorial/media/document forms and actions; HR vacancy form; audit/users/health/email. No invented UI screenshots.'});
await writeFile(path.join(root,'09-evidence/screenshot-register.json'),JSON.stringify(register,null,2));
await browser.close();console.log(JSON.stringify(register.map(({id,status,reason})=>({id,status,reason}))));
