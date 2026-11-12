"""Build editable training documents; writes only inside this package."""
from pathlib import Path
import argparse, json, shutil, hashlib, re, zipfile
from datetime import datetime, timezone
from xml.etree import ElementTree as ET
from html import escape
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from pptx import Presentation
from pptx.util import Inches as PI, Pt as PP
from pptx.dml.color import RGBColor as PC
from pptx.enum.shapes import MSO_SHAPE
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, Image, PageBreak, KeepTogether
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib import colors
from reportlab.lib.enums import TA_LEFT
from PIL import Image as PILImage, ImageDraw, ImageFont
from openpyxl import Workbook, load_workbook
from openpyxl.styles import Font, PatternFill, Alignment
from openpyxl.worksheet.datavalidation import DataValidation
from content import *

ROOT = Path(__file__).resolve().parents[1]
REPO = ROOT.parents[1]
GREEN='0B8F62'; NAVY='0B2D4D'; GOLD='B45309'; MUTED='526371'
for d in ['01-curriculum','02-presentation','03-participant-manual','04-practical-workbook','05-quick-references','06-assessments','07-registers','08-handover','09-evidence','assets/screenshots','assets/training-data']:
    (ROOT/d).mkdir(parents=True, exist_ok=True)
LOGO=ROOT/'assets/council-crest.png'
if not LOGO.exists():
    PILImage.open(REPO/'public/images/council-crest-dark.webp').convert('RGBA').save(LOGO)

def p(text): return ('p',text)
def h(text): return ('h',text)
def sub(text): return ('sub',text)
def bullet(text): return ('b',text)
def table(headers,rows): return ('table',headers,rows)
def pending(text): return ('pending',text)
def shot(key): return ('shot',key)
def br(): return ('break',)

def shots():
    f=ROOT/'09-evidence/screenshot-register.json'
    return json.loads(f.read_text()) if f.exists() else []
def shot_info(key): return next((x for x in shots() if x['id']==key),None)

def pagefield(par):
    run=par.add_run(); fld=OxmlElement('w:fldSimple'); fld.set(qn('w:instr'),'PAGE'); run._r.addnext(fld)

def docfile(rel,title,subtitle,blocks,pdf=False):
    dest=ROOT/rel
    doc=Document(); sec=doc.sections[0]
    sec.top_margin=Inches(.65);sec.bottom_margin=Inches(.65);sec.left_margin=Inches(.7);sec.right_margin=Inches(.7)
    sec.page_width=Inches(8.27);sec.page_height=Inches(11.69)
    for name in ['Normal','Body Text','List Bullet','List Number']:
        doc.styles[name].font.name='Calibri';doc.styles[name].font.size=Pt(10.5)
        doc.styles[name].paragraph_format.space_after=Pt(6)
    for name,size in [('Title',29),('Heading 1',18),('Heading 2',13)]:
        doc.styles[name].font.name='Calibri';doc.styles[name].font.size=Pt(size);doc.styles[name].font.color.rgb=RGBColor.from_string(NAVY)
    for part in doc.part.package.parts:
        if str(part.partname).endswith('/theme1.xml'):
            theme=ET.fromstring(part.blob)
            accent=theme.find('.//{http://schemas.openxmlformats.org/drawingml/2006/main}accent1/{http://schemas.openxmlformats.org/drawingml/2006/main}srgbClr')
            if accent is not None:accent.set('val',GREEN)
            part._blob=ET.tostring(theme,encoding='utf-8',xml_declaration=True)
    header=sec.header.paragraphs[0];header.text='MUTOKO RURAL DISTRICT COUNCIL  |  WEBSITE TRAINING';header.runs[0].font.size=Pt(8);header.runs[0].font.color.rgb=RGBColor.from_string(GREEN)
    footer=sec.footer.paragraphs[0];footer.text='Training development edition · 12 Nov 2026  |  Page ';pagefield(footer)
    doc.add_picture(str(LOGO),width=Inches(.78))
    doc.add_heading(title,0);doc.add_paragraph(subtitle)
    doc.add_paragraph('One-Day Practical Training Programme | Mutoko, Zimbabwe | 2 ICT · 3 HR · 2 Finance')
    doc.add_paragraph('Prepared from the approved discovery baseline. Training delivery, attendance, competence and Council acceptance remain unrecorded until conducted.')
    doc.add_page_break()
    for block in blocks:
        typ=block[0]
        if typ=='break': doc.add_page_break()
        elif typ in ['p','b','pending']:
            par=doc.add_paragraph(block[1],style='List Bullet' if typ=='b' else None)
            if typ=='pending':
                for r in par.runs:r.font.color.rgb=RGBColor.from_string(GOLD);r.bold=True
        elif typ=='h':doc.add_heading(block[1],1)
        elif typ=='sub':doc.add_heading(block[1],2)
        elif typ=='table':
            t=doc.add_table(rows=1,cols=len(block[1]));t.style='Light Shading Accent 1'
            for c,txt in zip(t.rows[0].cells,block[1]):c.text=str(txt)
            rep=OxmlElement('w:tblHeader');t.rows[0]._tr.get_or_add_trPr().append(rep)
            for row in block[2]:
                for c,txt in zip(t.add_row().cells,row):c.text=str(txt)
            for row in t.rows:
                for c in row.cells:
                    for par in c.paragraphs:
                        for r in par.runs:r.font.size=Pt(9)
        elif typ=='shot':
            info=shot_info(block[1]);img=ROOT/info['file'] if info and info.get('file') else None
            if img and img.exists() and info.get('status')=='verified':
                pic=doc.add_picture(str(img),width=Inches(6.7));pic._inline.docPr.set('descr',f"Actual public {info['id']} screen. For visual orientation; numbered instructions provide the text equivalent.");doc.add_paragraph(f"Verified public screenshot {info['id']}: {info['url']} | {info['captured_at']} | {info['viewport']}",style='Caption')
            else:doc.add_paragraph('SCREENSHOT PENDING — '+block[1]+'. Capture from approved training build with the intended role; do not substitute a sample dashboard.')
    doc.core_properties.title=title;doc.core_properties.author='Mutoko Rural District Council | Training development';doc.core_properties.subject=subtitle
    doc.save(dest)
    if pdf: pdffile(dest.with_suffix('.pdf'),title,subtitle,blocks)
    return dest

def pdffile(dest,title,subtitle,blocks):
    ss=getSampleStyleSheet(); ss.add(ParagraphStyle(name='MRDCBody',fontName='Helvetica',fontSize=10,leading=14,spaceAfter=7))
    ss.add(ParagraphStyle(name='MRDCH',fontName='Helvetica-Bold',fontSize=17,leading=21,textColor=colors.HexColor('#'+NAVY),spaceBefore=14,spaceAfter=9,keepWithNext=True))
    ss.add(ParagraphStyle(name='MRDCSub',fontName='Helvetica-Bold',fontSize=12,leading=16,textColor=colors.HexColor('#'+GREEN),spaceBefore=8,spaceAfter=6,keepWithNext=True))
    def para(t,style='MRDCBody'): return Paragraph(escape(str(t)).replace('\n','<br/>'),ss[style])
    story=[Image(str(LOGO),width=54,height=65,kind='proportional'),para('MUTOKO RURAL DISTRICT COUNCIL','MRDCH'),para(title,'MRDCH'),para(subtitle),para('One-Day Practical Training Programme | Mutoko, Zimbabwe'),para('Development edition • 12 November 2026. Delivery and acceptance pending.'),PageBreak()]
    for b in blocks:
        typ=b[0]
        if typ=='break':story.append(PageBreak())
        elif typ in ['p','b','pending']:story.append(para(('• ' if typ=='b' else 'PENDING: ' if typ=='pending' else '')+b[1]))
        elif typ=='h':story.append(para(b[1],'MRDCH'))
        elif typ=='sub':story.append(para(b[1],'MRDCSub'))
        elif typ=='table':
            data=[[para(x) for x in b[1]]]+[[para(x) for x in row] for row in b[2]]
            t=Table(data,colWidths=[481/len(b[1])]*len(b[1]),repeatRows=1,hAlign='LEFT')
            t.setStyle(TableStyle([('BACKGROUND',(0,0),(-1,0),colors.HexColor('#EAF4F8')),('VALIGN',(0,0),(-1,-1),'TOP'),('GRID',(0,0),(-1,-1),.3,colors.HexColor('#D9E2E8')),('LEFTPADDING',(0,0),(-1,-1),6),('RIGHTPADDING',(0,0),(-1,-1),6)]));story.extend([t,Spacer(1,9)])
        elif typ=='shot':
            info=shot_info(b[1]); img=ROOT/info['file'] if info and info.get('file') else None
            if img and img.exists() and info.get('status')=='verified':
                with PILImage.open(img) as im:w,hh=im.size
                story.extend([Image(str(img),width=481,height=481*hh/w),para(f"Verified public screenshot {info['id']}: {info['url']} | {info['captured_at']}.")])
            else:story.append(para('SCREENSHOT PENDING — '+b[1]+'. Capture the actual authorized training screen.'))
    def decor(c,d):
        c.saveState();c.setStrokeColor(colors.HexColor('#'+GREEN));c.line(36,35,559,35);c.setFont('Helvetica',8);c.setFillColor(colors.HexColor('#'+MUTED));c.drawString(36,23,'MRDC | Website administration training | Development edition');c.drawRightString(559,23,str(d.page));c.restoreState()
    SimpleDocTemplate(str(dest),pagesize=(595.28,841.89),leftMargin=57,rightMargin=57,topMargin=45,bottomMargin=50,title=title,author='Mutoko Rural District Council').build(story,onFirstPage=decor,onLaterPages=decor)

def common_end():
    return [h('Evidence limits that must remain visible')]+[bullet(x) for x in LIMITS]+[h('Source references')]+[p(' | '.join(x)) for x in SOURCES]

def curriculum():
    b=[h('Contract, audience and measurable outcomes'),p('The original ToR §§3.5 and 5 requires one working day in Mutoko for two ICT, three HR and two Finance participants, covering CMS, basic system administration, cybersecurity/data protection and ongoing maintenance. No additional days are assumed.'),p('By close, each participant should demonstrate their eight track checkpoints, answer the 10-item post-test, locate their role reference and identify an escalation route. Access-blocked tasks remain pending. Proposed competency rule: 70/100 overall with critical safety checks satisfied, subject to Council approval.'),h('Timetable: 08:30–16:30'),table(['Time','Session','Minutes'],[(s['time'],s['title'],s['mins']) for s in SESSIONS]),p('Total 480 minutes; tea 15 and lunch 45; 420 remaining minutes include registration, teaching, practice, assessment and handover. Shared and departmental guided-practice sessions together occupy 150 minutes; individual assessment occupies 45 minutes.'),h('Readiness checklist and fallback'),table(['Gate','Required evidence','Owner'],GATES),p('Check gates before delivery. If G1 or G3 fails, do no system writes. If a particular permission or screenshot is unavailable, teach the source-verified sequence with the worksheet and record not demonstrated. Do not award practical completion for watching a demonstration. If gates cannot be resolved on the day, deliver the safe theory/observation components, log outstanding competence and arrange Council-approved remediation.'),p('Facilitation: one facilitator with two ICT participants supporting navigation during guided practice only. Assess all seven individually. Use a projector plus seven devices; provide large-print PDFs and read questions aloud on request without giving answers. Pair practice, but score individually. No participant is appointed a trainer or assessor merely by being ICT staff.')]
    for s in SESSIONS:
        b += [br(),h(f"{s['id']} | {s['time']} | {s['title']}"),p(f"Duration: {s['mins']} minutes. Target audience: {s['aud']}.")]
        for key,label in [('objectives','Learning objectives'),('topics','Topics covered'),('method','Teaching method and minute budget'),('demo','Live demonstration / verified fallback'),('practice','Practical exercise'),('materials','Required materials'),('competency','Expected competency'),('assessment','Assessment method')]:b += [sub(label),p(s[key])]
    b += [br(),h('Role track alignment'),table(['Track','Priority outcomes','Access limitation'],[('ICT (2)','Roles/scopes, recovery, audit limits, monitoring evidence, backups and incident escalation','Host configuration, user changes, retries, restores and deployment are not authorized by this package.'),('HR (3)','Vacancy dates, drafts, authorized publication, advert dependencies and privacy','Default Department Content Owner has no vacancy/official authoring rights; rehearse G2.'),('Finance (2)','Budget category, reference_date, PDF review, version-preserving replacement and confidentiality','Default department role lacks documents.update/replacement; procurement/bank rights separate.')]),h('Assessment and remediation'),p('S7: 5-minute briefing, 25-minute individual track task, 10-minute written post-test, 5-minute evidence capture. Practical has eight 10-point criteria scored 0/5/10; written test has ten 2-point items. Blank means not assessed. If any criterion is blocked, outcome is Pending rather than a completed score. Critical unsafe acts require remediation even if the aggregate reaches 70.'),p('Keep the baseline separate from the final score. Record adjustments/accommodations and individual evidence without secrets. Results, training date, attendance and signatures remain blank until delivery. Council approves the threshold and any retest arrangements.')]+common_end()
    docfile('01-curriculum/Detailed-One-Day-Curriculum.docx','Detailed one-day curriculum','08:30–16:30 | Contract-aligned, practical and role-specific',b,pdf=True)
    (ROOT/'09-evidence/curriculum-sequence.json').write_text(json.dumps({'stage':'curriculum completed before presentation','created_at':datetime.now(timezone.utc).isoformat(),'minutes':sum(s['mins'] for s in SESSIONS)},indent=2))
    print('CURRICULUM COMPLETE')

def ppt_text(slide,x,y,w,hh,text,size=22,color=NAVY,bold=False):
    sh=slide.shapes.add_textbox(PI(x),PI(y),PI(w),PI(hh));tf=sh.text_frame;tf.word_wrap=True
    tf.margin_left=0;tf.margin_right=0;tf.margin_top=0;tf.margin_bottom=0
    for i,line in enumerate(text.split('\n')):
        par=tf.paragraphs[0] if i==0 else tf.add_paragraph();par.text=line;par.font.name='Aptos';par.font.size=PP(size);par.font.bold=bold;par.font.color.rgb=PC.from_string(color);par.space_after=PP(12)
    return sh
def rect(slide,x,y,w,hh,fill):
    sh=slide.shapes.add_shape(MSO_SHAPE.RECTANGLE,PI(x),PI(y),PI(w),PI(hh));sh.fill.solid();sh.fill.fore_color.rgb=PC.from_string(fill);sh.line.fill.background();return sh
def picture_fit(slide,path,x,y,w,hh):
    with PILImage.open(path) as im:iw,ih=im.size
    scale=min(w/iw,hh/ih);ww=iw*scale;he=ih*scale
    return slide.shapes.add_picture(str(path),PI(x+(w-ww)/2),PI(y+(hh-he)/2),width=PI(ww),height=PI(he))

def presentation():
    assert (ROOT/'01-curriculum/Detailed-One-Day-Curriculum.docx').exists(),'Curriculum must be completed first'
    prs=Presentation();prs.slide_width=PI(13.333);prs.slide_height=PI(7.5)
    handout=[]
    for i,(section,title,points,key,notes) in enumerate(SLIDES,1):
        sl=prs.slides.add_slide(prs.slide_layouts[6]);rect(sl,0,0,13.333,.13,GREEN)
        if i==1:
            rect(sl,0,.13,13.333,7.37,NAVY);picture_fit(sl,LOGO,10.85,.6,1.4,1.6)
            ppt_text(sl,.7,.65,9.6,.5,'MUTOKO RURAL DISTRICT COUNCIL',22,'FFFFFF',True)
            ppt_text(sl,.7,1.6,10.5,3.1,'WEBSITE ADMINISTRATION,\nCONTENT MANAGEMENT &\nCAPACITY BUILDING TRAINING',31,'FFFFFF',True)
            rect(sl,.7,5,1.0,.07,'F59E0B');ppt_text(sl,.7,5.35,11,1.2,'One-Day Practical Training Programme\n2 ICT · 3 HR · 2 Finance | Mutoko, Zimbabwe',22,'FFFFFF')
        else:
            rect(sl,0,.13,13.333,.55,'EAF4F8');ppt_text(sl,.5,.22,11,.3,'MRDC  /  '+section.upper(),11,GREEN,True);picture_fit(sl,LOGO,12.25,.19,.45,.45)
            ppt_text(sl,.6,.96,12.1,1.05,title,30,NAVY,True)
            info=shot_info(key) if key else None
            if info and info.get('status')=='verified' and (ROOT/info['file']).exists():
                for j,point in enumerate(points):
                    ppt_text(sl,.65,2.12+j*1.24,4.0,1.08,f'{j+1:02d}  '+point,20)
                picture_fit(sl,ROOT/info['file'],4.95,2.05,7.7,4.45)
                ppt_text(sl,4.95,6.55,7.7,.35,'VERIFIED PUBLIC CAPTURE • '+info['id']+' • 12 NOV 2026',10,MUTED)
                # Editable callout identifies the verified public view without inventing UI controls.
                rect(sl,10.55,2.12,1.65,.34,GREEN);ppt_text(sl,10.6,2.16,1.55,.24,'PUBLIC VIEW',10,'FFFFFF',True)
            elif i in [10,22]:
                flow=['Draft','Review¹','Publishable','Publish','Citizen check'] if i==10 else ['Current PDF','Replace file','Version +1','Draft','Review / publish']
                for k,label in enumerate(flow):
                    x=.65+k*2.52;rect(sl,x,2.17,2.13,.78,GREEN if k!=1 else NAVY);ppt_text(sl,x+.1,2.34,1.93,.45,label,16,'FFFFFF',True)
                    if k<4:ppt_text(sl,x+2.2,2.36,.3,.35,'→',19,GREEN,True)
                for j,point in enumerate(points):ppt_text(sl,.8,3.35+j*.76,11.9,.65,point,22)
                ppt_text(sl,.8,5.93,11.5,.28,'¹ Organizational approval is governance; the application does not force a separate reviewer.' if i==10 else 'Use the explicit Replace file action. Ordinary editing does not establish retained version history.',12,GOLD)
                ppt_text(sl,.7,6.45,11.8,.32,'AUTHENTICATED SCREENSHOT PENDING • diagram describes the verified source workflow',11,GOLD)
            else:
                for j,point in enumerate(points):
                    rect(sl,.7,2.15+j*1.15,.5,.5,GREEN);ppt_text(sl,.79,2.23,.35,.3,str(j+1),16,'FFFFFF',True);ppt_text(sl,1.45,2.12+j*1.15,10.9,.95,point,24)
                if section in ['Administration','Content management','HR workflow','Finance workflow','Enquiries','ICT operations']:
                    ppt_text(sl,.7,6.22,11.8,.4,'AUTHENTICATED SCREENSHOT PENDING • demonstrate only in approved training environment',11,GOLD)
            rect(sl,.6,7.04,12.1,.015,'D9E2E8');ppt_text(sl,.65,7.15,10.6,.22,'12 NOV 2026 BASELINE  |  DEVELOPMENT EDITION • LIVE DELIVERY AND ACCEPTANCE PENDING',9,MUTED);ppt_text(sl,12,7.1,.6,.3,f'{i:02d}',12,GREEN,True)
        sn=sl.notes_slide.notes_text_frame
        sn.text=f'Slide {i} | {section}\nFACILITATOR NOTES\n{notes}\nEVIDENCE: Approved discovery audit; source/content.py source list; current public screenshot register.\nSAFETY: No production writes, permissions, host changes or real emails. Missing access/screenshot stays pending.\nTIMING: Follow the detailed curriculum; keep this slide to its relevant session. Do not spend guided-practice time reading every bullet.'
        if key:
            sn.text += '\nSCREENSHOT: '+(json.dumps(info,ensure_ascii=False) if info else 'PENDING: '+key)
        handout += [h(f'{i:02d}. {title}')]+[bullet(t) for t in points]+[sub('Facilitator notes'),p(notes)]
    prs.core_properties.title='MUTOKO RURAL DISTRICT COUNCIL — '+TITLE
    prs.core_properties.author='Mutoko Rural District Council';prs.save(ROOT/'02-presentation/MRDC-Website-Training.pptx')
    docfile('02-presentation/Facilitator-Slide-Notes.docx','Facilitator slide notes','Editable companion to the PowerPoint; follow the curriculum minute budgets',handout+common_end())
    (ROOT/'09-evidence/presentation-sequence.json').write_text(json.dumps({'stage':'presentation completed after curriculum and before supporting documents','slides':len(SLIDES),'created_at':datetime.now(timezone.utc).isoformat()},indent=2))
    print('PRESENTATION COMPLETE',len(SLIDES),'slides')

def sample_data():
    for version,amount in [('v1','120'),('v2','125')]:
        b=[h('TRAINING ONLY — FICTIONAL BUDGET 2099'),p('Not a Council-approved budget. Not for production upload, real procurement, payment or public reliance.'),table(['Fictional activity','Illustrative units'],[('Training notice-board exercise',amount),('Training print exercise','30')]),p('Correction in v2: illustrative training units changed from 120 to 125. No actual Council financial figures or bank details are included.')]
        pdffile(ROOT/f'assets/training-data/TRAINING-ONLY-Budget-2099-{version}.pdf','TRAINING ONLY — Fictional budget 2099',f'Exercise version {version}',b)
    pdffile(ROOT/'assets/training-data/TRAINING-ONLY-Vacancy-Advert.pdf','TRAINING ONLY — Records Assistant advert','Fictional exercise; no applications accepted',[p('This is fictional training material. Opening date: facilitator fills the training day. Closing date: training day + 7 calendar days. Duties: maintain fictional records; prepare a training index. Requirements: basic document handling. Do not publish on production or collect applications.')])
    im=PILImage.new('RGB',(1200,700),'#EAF4F8');dr=ImageDraw.Draw(im)
    fontpath='/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf'
    font=ImageFont.truetype(fontpath,44);small=ImageFont.truetype(fontpath,27)
    dr.rectangle((60,60,1140,640),fill='#FFFFFF',outline='#0B8F62',width=12)
    dr.text((110,120),'TRAINING ONLY',fill='#0B2D4D',font=font);dr.text((110,225),'Fictional community notice board',fill='#0B2D4D',font=small)
    dr.text((110,310),'Workshop practice • No real Council notice',fill='#526371',font=small)
    dr.text((110,420),'Use descriptive alt text in the Media library.',fill='#0B8F62',font=small)
    im.save(ROOT/'assets/training-data/TRAINING-ONLY-Notice-Board.png')
    (ROOT/'assets/training-data/Fixture-Text.txt').write_text('TRAINING ONLY — fictional data, isolated environment only.\nParticipant ID: ____\nTraining day: ____; closing date = training day + 7 calendar days.\nPage/news title: TRAINING ONLY — Community Records Workshop [ID].\nSlug: training-only-records-workshop-[id].\nBody: This fictional workshop helps participants practise safe website content management. It is not a Council announcement.\nVacancy: TRAINING ONLY — Records Assistant [ID]. Reference: TRAINING-HR-[ID].\nBudget category: Budget; reference_date 2099-01-01.\nAlt text: Fictional training notice board for a website content-management workshop.\nNo real names, recipients, bank details, dates of birth or applications.\n')

def manual():
    b=[h('How to use this manual'),p('Use W1–W17 below. Public screenshots show actual read-only website captures, not training records. Every authenticated screen is pending until approved training access is provided. Follow the numbered sequence only where authorized; stop at any missing permission. Browser completion with each participant role has not yet been verified.'),p('Required before hands-on work: G1 isolated training URL, G2 tested role access, G3 controlled mail/no external AI, G4 supplied fictional files. Organizational approval and software publication are separate.'),h('Workflow contents')]+[p(f"{w['id']} — {w['title']} ({w['audience']})") for w in WORKFLOWS]
    for w in WORKFLOWS:
        b += [br(),h(w['id']+' | '+w['title']),sub('Purpose'),p(w['purpose']),sub('Required permissions and prerequisites'),p(w['permission']),p('Use only an approved training fixture and address for changes. Confirm named responsibility and role/scope with ICT before opening Create or changing state.'),sub('Navigation path'),p(w['path']),sub('Numbered instructions')]
        b += [p(f'{i}. {s}') for i,s in enumerate(w['steps'],1)]
        b += [sub('Actual screen or pending evidence'),shot(w['shot']),sub('Expected result'),p(w['result']),sub('Common errors and troubleshooting'),p(w['errors']),sub('Security and governance'),p(w['security'])]
    glossary=[('CMS','The administration system used to maintain website content.'),('Resource','A CMS module such as Documents or Vacancies.'),('Draft','Saved content awaiting review/publication; normally not public.'),('Publishable','Verification state allowing publication where the module requires it.'),('Published','Publication state; dates, visibility and dependencies can still affect public availability.'),('Scope','The global, department or own-record boundary attached to a role.'),('Slug','The readable part of a URL; changing it may break links.'),('Alt text','A useful text alternative describing an image’s information.'),('Media','Uploaded image/PDF and its stored metadata.'),('Reference date','Document date from which the reporting year is derived.'),('Version','A retained predecessor when Replace file supersedes a document.'),('MFA','Multi-factor authentication; authenticator plus password, where enrolled.'),('Queue','Stored work waiting for a worker; a count does not prove progress.'),('Scheduler','Laravel’s planned tasks, requiring actual host cron execution.'),('SMTP','The email transport used to deliver messages.'),('Audit event','Recorded actor/action/time/subject; coverage is not universal.'),('RPO / RTO','Proposed recovery-data-loss/time targets requiring approval.'),('TLS / HTTPS','Encrypted web connection; certificate renewal still needs ownership.'),('Pending','Not yet verified or assessed, not proof of success.')]
    b += [br(),h('Glossary'),table(['Term','Plain-English meaning'],glossary)]+common_end()
    docfile('03-participant-manual/Participant-User-Manual.docx','Participant user manual','Step-by-step reference for ICT, HR and Finance',b,pdf=True)

def exercises():
    items=[
      ('E1','All','Public navigation and help','20 minutes in S2','W1–W3','Find a service, a document and a vacancy; state date/status; use language/accessibility controls; locate permitted CMS/help. Do not submit production forms.','Three correct public URLs, explanation of fallback and one help location.'),
      ('E2','All, where pages rights granted','Draft and edit content','Part of 45-minute S3 practice','W4–W5','Use assigned Page draft; add TRAINING ONLY title/paragraph/internal CTA; save; edit; show draft state. If blocked, complete sequence on paper and record pending practical.','Saved draft ID/slug and explanation that core edits need review/publication again.'),
      ('E3','All with media rights','Image and alt text','Part of 45-minute S3 practice','W6','Upload TRAINING-ONLY-Notice-Board.png; add title, department and supplied descriptive alt text; inspect Preview; explain upload versus publication.','Valid image and meaningful alt text, no private content; blocked tasks pending.'),
      ('E4','HR (3)','Vacancy draft, edit and close','45-minute S4 guided track','W10–W11','Each HR participant uses individual suffix; opening training day, closing +7 days; attach fictional advert; save/edit; request authorized review/publish; explain closure.','Correct fictional vacancy, date interval, draft, dependency and citizen check where authorized.'),
      ('E5','Finance (2)','Budget classification and replacement','45-minute S4 guided track','W7–W9','Create Budget category, reference_date 2099-01-01, department and intended visibility; attach v1; save. Upload v2 and use Replace file only if authorized. Observe review/publish then training download.','Year/category correct; v1 retained, version increments, record draft; corrected download only after authorized publication.'),
      ('E6','ICT (2)','Read-only permissions and account tabletop','During S4/S5','W14','Read assigned redacted/training user and compare duties to permissions/scope. Identify missing HR vacancy or Finance replacement rights. Draft an approval request; explain disable/reset/MFA and last-admin protections. Make no account change.','Completed access review with specific permission/scope and owner; no secrets or actual changes.'),
      ('E7','All','Phishing and disclosure scenarios','20 minutes in S5','W2, W13–W15','Message: “Urgent—your budget publication is blocked; send your MFA recovery code to training-help@example.invalid.” Identify three red flags and verify via known ICT channel. Scenario 2: private complaint pasted into a public draft—stop release, preserve evidence and report.','No codes shared/no link followed; immediate internal escalation with time, record and safe evidence.'),
      ('E8','ICT leads; HR/Finance triage','Monitoring, backup and incident tabletop','20 minutes in S6','W15–W16','Classify missing PDF, jobs not completing, Redis red on database drivers, stale backup and suspected disclosure. For each list evidence/owner/next action. Explain DB/files/manifest/off-server/restore checks.','Useful triage evidence; no false readiness claim; scratch restore and approved escalation described.'),
    ]
    b=[h('Exercise safety and materials'),p('Use fictional data only, marked TRAINING ONLY. Prerequisites G1–G4 must pass. No production writes, real submissions, external AI, actual user changes, mail retries, deployments or restores. Reference the supplied Fixture-Text.txt and sample PDFs/PNG.'),p('Training URL: ____________________   Training date: __________   Participant ID/track: __________'),p('Opening date = training date; closing date = opening +7 calendar days. HR reference and all slugs must include participant suffix. Finance sample year is 2099. Do not invent real Council financial figures.'),table(['Exercise','Track','Time allocation'],[(a,b_,d) for a,b_,_,d,_,_,_ in items])]
    for id,track,title,dur,refs,task,outcome in items:
        b += [br(),h(id+' | '+title),p('Audience: '+track+' | Allocation: '+dur+' | Manual: '+refs),sub('Purpose and prerequisites'),p('Practise the matching source-verified workflow. Required rights are those listed in the manual. If access is not ready, record the blocker; do not claim a system task was completed.'),sub('Instructions'),p(task),sub('Expected outcome'),p(outcome),sub('Evidence and reflection'),table(['Field','Participant record'],[('Training URL / participant ID',''),('Record ID / slug / version',''),('Observed saved/public state',''),('Privacy check and attachment check',''),('Access blocker / error / owner',''),('What I would do next','')]),sub('Practice rubric'),table(['Level','Evidence'],[('Independent','Correct task, safe data and clear evidence; ready for individual assessment.'),('Supported','Completed with prompts; repeat independently during assessment.'),('Pending','Blocked, unavailable or not performed. Observation alone does not establish competence.')])]
    b += [br(),h('Facilitator setup and individual variant'),p('Use the same fictional fixture files for demonstration, but individual assessment titles/slugs must contain ASSESS-[ID] and a different paragraph/requirement from guided practice. Preassign separate disposable records for replacement. All changes remain isolated. Cleanup requires the designated staging owner after evidence capture; do not delete application tests or production content.'),p('Recommended E7 responses: credential request + urgency + unverified sender/link are red flags; do not share anything, preserve message, contact known ICT route. For disclosure, pause release, record evidence and report immediately to ICT/privacy lead; authorized leads handle containment and legal notification decisions.')]+common_end()
    docfile('04-practical-workbook/Practical-Exercise-Workbook.docx','Practical exercise workbook','Guided work for seven staff; fictional fixtures supplied',b)

def quickrefs():
    settings={
      'HR':('HR quick reference',['W2','W10','W11','W13'],'Vacancy access must be explicitly approved; default Department Content Owner has no vacancy rights. Publication is a separate right.'),
      'Finance':('Finance quick reference',['W7','W8','W9','W12'],'Confirm documents.update and media access before Replace file. Finance title does not grant procurement awards or bank-account changes.'),
      'ICT':('ICT administrator quick reference',['W14','W15','W16'],'Confirm actual database/Redis drivers, workers, cron and backup evidence. No production changes are authorized by this package.')
    }
    for track,(title,ids,gate) in settings.items():
        b=[h('Start here'),p(gate),p('Training writes: isolated approved environment only. Missing access/evidence stays pending. Use the manual for full numbered instructions.')]
        for id in ids:
            w=next(w for w in WORKFLOWS if w['id']==id)
            b += [sub(id+' | '+w['title']),p(w['path']),p('Rights: '+w['permission']),p(w['result']),p('Check: '+w['errors'])]
        if track=='ICT':
            b += [h('Read-only command reference — approved ICT environment only'),table(['Command / evidence','Meaning and limit'],[('php artisan schedule:list --no-interaction','Intended schedule, not execution history.'),('php artisan queue:failed --no-interaction','Lists failures; redact payloads/recipient data.'),('php artisan production:check --no-interaction','Read-only but assumes Redis; not definitive database-host readiness.'),('Public GET /up','Liveness only; verify dependency/worker/backup evidence separately.')]),p('Change-controlled commands: backup:create, queue:restart, cache commands, migrate --force and any restore/deployment require approved operator/runbook/environment. queue:restart signals existing workers; it does not start one. NEVER migrate:fresh on production. Do not print .env or paste secret-bearing logs into support materials.')]
        b += [h('Escalation contacts — fill before delivery'),p('Content owner: __________ | ICT: __________ | Hosting/software support: __________ | Privacy/security lead: __________'),p('Report exact environment, URL/time, symptom, safe error text and record ID. No passwords, recovery codes, session cookies or private complaint data.'),h('Known limits'),p('Authenticated screenshots/role walkthroughs, production rights, host operations, complete audit coverage, approved translations/accessibility and legal compliance remain unverified. See the included approved discovery baseline for all limitations.')]
        docfile(f'05-quick-references/{track}-Quick-Reference.docx',title,'Desk reference; editable source and PDF',b,pdf=True)

def assessments():
    participant=[h('Instructions'),p('Participant ID: ______  Track: ______  Actual training date: ______. Choose A, B or C. Each test takes ten minutes. Pre-test is diagnostic; post-test contributes 20 points. No actual answers or scores have been filled. Keep the two completed tests separate and return them to the facilitator.')]
    for label in ['Pre-training diagnostic test','Post-training knowledge test']:
        participant += [br(),h(label),p('Participant ID: ______  Date/time: ______')]
        for i,(q,opts,ans,why) in enumerate(QUESTIONS,1):participant += [sub(f'{i}. {q}')]+[p(f'{chr(65+j)}. {o}') for j,o in enumerate(opts)]+[p('Answer: ____')]
    docfile('06-assessments/Participant-Knowledge-Tests-No-Answers.docx','Participant knowledge tests','Print/distribute this file; facilitator key is separate',participant)
    b=[h('Administration and proposed scoring'),p('Council approval required: 70/100 overall plus all critical safety checks satisfied. Practical 80 points (8 × 10), written post-test 20 points (10 × 2). Use 0=not demonstrated/incorrect after opportunity; 5=partially correct or prompted; 10=independent and correct. Blank=not assessed/blocked. Do not convert missing evidence to a pass or silently to zero.'),p('Critical checks: approved isolated environment, no credential sharing/disclosure, no real private-data publication/AI submission, and no unauthorized send/account/host action. A critical unsafe act requires “Remediation required” regardless of total. Verbal explanation can be assessed where the track explicitly says explain; observation does not replace a required system action.'),p('S7 allocation: 5-minute briefing, 25-minute individual task, 10-minute post-test, 5-minute evidence capture. Facilitator observes each person at defined checkpoints; ICT peers cannot score their own work. If all seven cannot be observed, remaining checks stay pending and the completion report lists follow-up.'),p('Individual task variant: prefix titles/slugs ASSESS-[ID]; HR changes a different requirement; Finance replaces a separate assigned document; ICT reviews a different fictional user/event/evidence set. Record actual state/ID. Do not run production commands.'),h('Pre-training knowledge assessment'),p('Participant ID: ______  Date: ______  Track: ______. Choose A/B/C for each question. Ten minutes; diagnostic only.')]
    for i,(q,opts,ans,why) in enumerate(QUESTIONS,1):b += [sub(f'{i}. {q}')]+[p(f'{chr(65+j)}. {o}') for j,o in enumerate(opts)]+[p('Answer: ____')]
    b += [br(),h('Post-training knowledge assessment'),p('Participant ID: ______  Date: ______  Track: ______. Choose A/B/C. Ten minutes. Each correct answer earns 2 points. Same question set enables a simple baseline comparison; facilitator collects pre-tests before the post-test.')]
    for i,(q,opts,ans,why) in enumerate(QUESTIONS,1):b += [sub(f'{i}. {q}')]+[p(f'{chr(65+j)}. {o}') for j,o in enumerate(opts)]+[p('Answer: ____')]
    for track,criteria in TRACKS.items():
        b += [br(),h(track+' practical competency checklist'),table(['#','10-point criterion','Evidence / 0,5,10 or blank'],[(i,c,'') for i,c in enumerate(criteria,1)]),p('Critical environment check: ____  Credentials: ____  Privacy: ____  Authorized actions: ____'),p('Practical total /80: ____   Written /20: ____   Total /100: ____   Outcome: ____   Assessor/date: ____')]
    for i,track in enumerate(['ICT','ICT','HR','HR','HR','Finance','Finance'],1):
        b += [br(),h(f'Individual assessment sheet — P{i:02d} ({track} planned track)'),p('Name: ____________________   Actual role/track: __________   Training date: __________'),p('Pre-test /20: ____  Post-test /20: ____  Practical /80: ____  Total /100: ____'),table(['Checkpoint','Score 0/5/10 or blank','Observed evidence / blocker'],[(str(j+1), '',c) for j,c in enumerate(TRACKS[track])]),table(['Critical safety check','Met / Not met / Pending'],[('Approved isolated environment',''),('Credentials protected',''),('No private-data disclosure',''),('No unauthorized external/admin/host action','')]),p('Outcome: Pending / Competent / Remediation required (circle after assessment).'),p('Remediation task, owner and date: __________________________________________'),p('Assessor: ____________________  Participant acknowledgment: ____________________'),p('Acknowledgment is not Council acceptance. Leave signature/name/date fields blank until the activity occurs.')]
    b += [br(),h('FACILITATOR ONLY — answer key'),p('Keep this section separate from participant test copies. Two points per correct answer; no negative marking.')]
    for i,(q,opts,ans,why) in enumerate(QUESTIONS,1):b += [p(f'{i}: {ans}. {why}')]
    b += [h('Critical incident scenario scoring'),p('Immediate internal reporting, protecting credentials/private information and preserving evidence are expected. The Council privacy/legal lead handles statutory deadlines and forms. Do not credit a response that deletes evidence or sends data to an unapproved AI service.')]+common_end()
    docfile('06-assessments/Assessment-Materials-and-Individual-Sheets.docx','Assessment materials and individual sheets','Blank pre/post tests, practical rubrics, seven sheets and facilitator answer key',b)

def stylewb(wb):
    for ws in wb:
        ws.freeze_panes='A2';ws.auto_filter.ref=ws.dimensions
        for c in ws[1]:c.fill=PatternFill('solid',fgColor=GREEN);c.font=Font(color='FFFFFF',bold=True);c.alignment=Alignment(wrap_text=True,vertical='top')
        for row in ws.iter_rows(min_row=2):
            for c in row:c.alignment=Alignment(wrap_text=True,vertical='top')
        for col in ws.columns:
            length=max(len(str(c.value or '')) for c in col)
            ws.column_dimensions[col[0].column_letter].width=min(65,max(16,length*.6))
        ws.sheet_properties.pageSetUpPr.fitToPage=True;ws.page_setup.orientation='landscape';ws.page_setup.paperSize=ws.PAPERSIZE_A4;ws.page_setup.fitToWidth=1;ws.page_setup.fitToHeight=0;ws.print_title_rows='1:1'
    return wb

def registers():
    wb=Workbook();ins=wb.active;ins.title='Instructions'
    for row in [('Field','Guidance'),('Status','Blank templates only. No actual attendance, results, signatures or acceptance recorded.'),('Audience','Planned P01–P02 ICT, P03–P05 HR, P06–P07 Finance; names and actual tracks remain blank until assigned.'),('Scoring','Council approval pending. Proposed 70/100 with critical checks Met; practical 8 × 10 + written /20.'),('Blank means','Not assessed. Formula totals appear only after all criteria and written score are recorded. Never score a blocked task as complete.'),('Privacy','Use participant IDs in shared reports; keep name/signature sheets and individual scores with authorized Council custodian.'),('Date/location','Actual training date and Mutoko venue to be filled at delivery.'),('Critical status','Met / Not met / Pending, recorded after observation only.')]:ins.append(row)
    ws=wb.create_sheet('Attendance');ws.append(['ID','Planned track','Actual name','Actual role','Date','AM signature','PM signature','Attendance confirmed by'])
    for i,track in enumerate(['ICT','ICT','HR','HR','HR','Finance','Finance'],1):ws.append([f'P{i:02d}',track])
    ws=wb.create_sheet('Individual Scores');ws.append(['ID','Actual track','Pre /20','Post /20']+[f'Practical {i} /10' for i in range(1,9)]+['Practical /80','Total /100','Critical status','Outcome','Assessor','Evidence / blocker'])
    for i in range(2,9):
        ws.cell(i,1,f'P{i-1:02d}');ws.cell(i,13,f'=IF(COUNT(E{i}:L{i})=8,SUM(E{i}:L{i}),"")');ws.cell(i,14,f'=IF(AND(COUNT(E{i}:L{i})=8,ISNUMBER(D{i})),SUM(E{i}:L{i})+D{i},"")');ws.cell(i,16,f'=IF(OR(O{i}="Not met",AND(ISNUMBER(N{i}),N{i}<70)),"Remediation required",IF(AND(ISNUMBER(N{i}),O{i}="Met"),"Proposed threshold met - Council confirmation","Pending"))')
    dv=DataValidation(type='list',formula1='"0,5,10"');ws.add_data_validation(dv);dv.add('E2:L8');dv.errorTitle='Use rubric values';dv.error='Use 0, 5 or 10, or leave blank if pending.';dv.showErrorMessage=True
    dv2=DataValidation(type='decimal',operator='between',formula1=0,formula2=20);ws.add_data_validation(dv2);dv2.add('C2:D8');dv2.showErrorMessage=True
    crit=DataValidation(type='list',formula1='"Met,Not met,Pending"');ws.add_data_validation(crit);crit.add('O2:O8');crit.showErrorMessage=True
    ws=wb.create_sheet('Participant Feedback');ws.append(['ID optional','Date','Track optional','Pace 1-5','Clarity 1-5','Practice 1-5','Relevance 1-5','Confidence before 1-5','Confidence after 1-5','Most useful','Still need help','Accessibility / improvement','Consent to follow-up'])
    for i in range(7):ws.append([None]*13)
    rating=DataValidation(type='whole',operator='between',formula1=1,formula2=5);ws.add_data_validation(rating);rating.add('D2:I8');rating.showErrorMessage=True
    ws=wb.create_sheet('Facilitator Evaluation');ws.append(['Criterion','Observation','Evidence','Improvement / owner / due'])
    for t in ['Readiness G1–G7','Timetable and breaks','Individual practice opportunity','Role access and blocked tasks','Privacy/safety','Screenshot/build match','Assessment fairness','Language/accessibility needs','Unresolved questions','Follow-up support']:ws.append([t])
    ws=wb.create_sheet('Completion Register');ws.append(['ID','Actual name','Actual training date','Attendance evidence','Assessment evidence','Outcome entered after review','Remediation','Reviewer / date','Council confirmation'])
    for i in range(1,8):ws.append([f'P{i:02d}'])
    ws=wb.create_sheet('Action Register');ws.append(['Action ID','Issue / prerequisite','Owner','Due date','Evidence','Status'])
    for gate,txt,owner in GATES:ws.append([gate,txt,owner,None,None,'Pending'])
    stylewb(wb).save(ROOT/'07-registers/Attendance-Evaluation-and-Completion-Templates.xlsx')

def handover():
    b=[h('Completion report — fill only after training'),p('Training date: __________  Venue in Mutoko: __________  Facilitator: __________  Council sponsor: __________'),p('Package edition/build used: __________  Actual training URL: __________  Approval of scoring rule: __________'),h('Delivery summary'),table(['Session','Actual start/end','Delivered / adapted / pending','Evidence'],[(s['id']+' '+s['title'],'','','') for s in SESSIONS if not s['id'].startswith('B')]),h('Actual participation and competence'),table(['Measure','Actual count / evidence'],[('ICT attended of planned 2',''),('HR attended of planned 3',''),('Finance attended of planned 2',''),('Competence assessed',''),('Proposed threshold met and confirmed',''),('Remediation required',''),('Not assessed / blocked','')]),p('Do not infer competence from attendance or feedback. Attach individual evidence by participant ID; keep names/scores in authorized custody.'),h('Results, gaps and feedback'),p('Pre/post comparison: ____________________\nPractical observations: ____________________\nCritical safety issues: ____________________\nFeedback summary: ____________________\nRole/staging/hosting blockers: ____________________'),h('Follow-up actions'),table(['Action','Owner','Due date','Evidence needed','Status'],[('','','','','') for _ in range(6)]),h('Handover and approvals'),p('Documents provided and location: ____________________\nCustodian/support contact: ____________________\nCouncil review comments: ____________________\nPrepared by/date: ____________________\nCouncil receipt acknowledgment/date: ____________________'),p('Receipt does not certify system acceptance, legal compliance or unperformed competencies. No signatures or results have been prefilled.')]
    docfile('08-handover/Training-Completion-Report-Template.docx','Training completion report template','Blank delivery, results, remediation and ownership record',b)
    b=[h('Final handover checklist'),p('Use Pending / Met with evidence / Not applicable and justification. This template records future checks; it does not declare acceptance.'),table(['Item','Owner','Status / evidence / date'],[(t,o,'') for t,o in [('Actual venue/date/seven participants confirmed','Council sponsor'),('Curriculum and scoring rule approved','Council sponsor'),('G1 training environment safe and disposable','ICT'),('G2 role rights and create-policy checks rehearsed','ICT / owners'),('G3 mail sink and AI/data boundary confirmed','ICT'),('Verified screenshots match intended build and role','Facilitator'),('Participants receive manual/workbook/role reference','Facilitator'),('Actual attendance and individual evidence recorded','Facilitator'),('Results reviewed; pending tasks/remediation assigned','Council / facilitator'),('Feedback and facilitator evaluation completed','Facilitator'),('No secrets/private data in shared materials','ICT / privacy lead'),('Actual host drivers/worker/cron evidence supplied','ICT / hosting'),('Current backups/off-server/restore evidence supplied','ICT / hosting'),('Support/privacy/escalation contacts filled','Council sponsor'),('Source and editable artifacts transferred','Council custodian'),('Legal/translation/accessibility decisions recorded','Council owners'),('Completion report reviewed separately from system acceptance','Council sponsor')]]),h('Evidence limitations'),p('Retain the approved discovery audit and package QA report. Do not mark a limitation resolved without current evidence, owner and date.'),h('Receipt and acceptance are separate'),p('Package received by: ____________________  Date: __________\nReview/acceptance decision and conditions: ____________________\nAuthorized signatory: ____________________  Date: __________')]
    docfile('08-handover/Final-Handover-Checklist.docx','Final handover checklist','Blank evidence-based receipt and review checklist',b)
    # Training-focused ToR compliance workbook: retain all detailed audit rows.
    wb=Workbook();ws=wb.active;ws.title='ToR Training Compliance';ws.append(['ToR requirement','Implemented module','Verification evidence','Training topic / material','Responsible role','Status'])
    audit=(REPO/'docs/SYSTEM-DISCOVERY-TRAINING-AUDIT.md').read_text()
    in_table=False
    for line in audit.splitlines():
        if line.startswith('| ToR requirement |'):in_table=True;continue
        if in_table and line.startswith('|---'):continue
        if in_table and line.startswith('|'):
            vals=[v.strip() for v in line.strip().strip('|').split('|')]
            if len(vals)==6:ws.append(vals)
        elif in_table:break
    ws.append(['3.5 — CMS training','Pages, editorial, media, documents, vacancies','Approved audit; source-verified instructions; authenticated checks pending','S2–S4; slides 5–24; Manual W1–W12; E1–E5','All / role-specific','Developed; delivery and hands-on readiness pending'])
    ws.append(['3.5 — basic system administration','Users/scopes/MFA/audit','Current sources; no actual user changes','S5–S6; Manual W14–W16; E6/E8','2 ICT; awareness all','Developed; host/access evidence pending'])
    ws.append(['3.5 — data protection/cybersecurity','Private enquiry and access controls','Legal source search; full regulator guidance fetch restricted','S5; slides 25–30; E7; post-test','All seven','Developed; Council legal/privacy validation pending'])
    ws.append(['3.5 — ongoing maintenance','Health/queue/scheduler/backup/runbooks','Source verified; production readiness not certified','S6; ICT quick reference; E8','ICT / hosting; triage all','Developed; current operations evidence pending'])
    ws.append(['§5 — one working day in Mutoko; seven staff','08:30–16:30; 2 ICT, 3 HR, 2 Finance','Curriculum minute sum 480, breaks 60','Detailed curriculum; attendance/completion templates','Council / facilitator','Developed; actual delivery, venue and attendance pending'])
    sh=wb.create_sheet('Conditions');sh.append(['Item','Required action'])
    for x in LIMITS:sh.append(['Unresolved discovery limitation',x])
    sh.append(['Acceptance','No Deliverable Developed entry means Council acceptance, attendance or participant competence. All original implementation statuses remain qualified.'])
    stylewb(wb).save(ROOT/'08-handover/ToR-Training-Compliance-Matrix.xlsx')

def supporting():
    assert (ROOT/'02-presentation/MRDC-Website-Training.pptx').exists(),'Presentation must be completed first'
    sample_data();manual();exercises();quickrefs();assessments();registers();handover()
    shutil.copy2(REPO/'docs/SYSTEM-DISCOVERY-TRAINING-AUDIT.md',ROOT/'09-evidence/Approved-System-Discovery-Audit.md')
    (ROOT/'09-evidence/Source-References.json').write_text(json.dumps([dict(title=a,source=b,qualification=c) for a,b,c in SOURCES],indent=2))
    print('SUPPORTING MATERIALS COMPLETE')

def qa():
    issues=[];stats={};ns={'w':'http://schemas.openxmlformats.org/wordprocessingml/2006/main'}
    for path in ROOT.rglob('*.docx'):
        with zipfile.ZipFile(path) as z:
            assert z.testzip() is None;ET.fromstring(z.read('word/document.xml'))
        d=Document(path);assert d.paragraphs;stats[str(path.relative_to(ROOT))]={'paragraphs':len(d.paragraphs),'tables':len(d.tables)}
    prs=Presentation(ROOT/'02-presentation/MRDC-Website-Training.pptx');assert 30<=len(prs.slides)<=40
    assert all(s.notes_slide.notes_text_frame.text for s in prs.slides)
    for i,s in enumerate(prs.slides,1):
        for sh in s.shapes:
            if sh.left<0 or sh.top<0 or sh.left+sh.width>prs.slide_width+10 or sh.top+sh.height>prs.slide_height+10:issues.append(f'Slide {i}: shape outside canvas')
    for path in ROOT.rglob('*.xlsx'):
        wb=load_workbook(path);assert wb.sheetnames
        if 'Individual Scores' in wb.sheetnames:
            ws=wb['Individual Scores'];assert all(ws.cell(r,c).value is None for r in range(2,9) for c in range(3,13));assert 'COUNT' in ws['M2'].value;assert 'Pending' in ws['P2'].value
    assert sum(s['mins'] for s in SESSIONS)==480
    assert sum(s['mins'] for s in SESSIONS if s['id'].startswith('B'))==60
    assert len(TRACKS['HR'])==len(TRACKS['Finance'])==len(TRACKS['ICT'])==8
    assert len(QUESTIONS)==10
    for path in ROOT.rglob('*.pdf'):assert path.read_bytes().startswith(b'%PDF-')
    layout_path=ROOT/'09-evidence/Slide-Layout-Review-Results.json'
    layout=json.loads(layout_path.read_text()) if layout_path.exists() else {'method':'Pending'}
    evidence={'checked_at':datetime.now(timezone.utc).isoformat(),'curriculum_minutes':480,'break_minutes':60,'slides':len(prs.slides),'workflows':len(WORKFLOWS),'docx_structure':stats,'shape_bounds_issues':issues,'score_templates_blank':True,'slide_browser_layout_review':layout,'native_office_render':'Not performed: no local LibreOffice/PowerPoint renderer; open in target Office before delivery.','authenticated_workflows':'Pending; no new application workflow tests run for document-only development.'}
    (ROOT/'09-evidence/Structural-QA-Results.json').write_text(json.dumps(evidence,indent=2))
    b=[h('QA verdict: developed, conditionally ready for facilitator preparation'),p('All requested document types have been generated. Safe hands-on delivery remains gated by staging, role access, screenshot/build checks and Council approvals. This report is not system acceptance or a participant completion record.'),h('Checks actually performed'),table(['Check','Result'],[('Timetable arithmetic','480 minutes; 60 breaks; 420 remaining including opening/assessment/handover'),('Document structure','DOCX archives/XML parse and reopen; PDF headers validated; spreadsheet structure and formulas checked'),('Presentation','38 slides; speaker notes on every slide; editable text/shapes; canvas-bound check; browser approximation reviewed separately'),('Assessment integrity','Eight practical checkpoints per track, ten written items; score cells and actual records blank; pending-aware formulas; separate participant test file has no key'),('Evidence consistency','Approved discovery included; source references and all limitations retained; screenshot register identifies actual captures and pending administration views'),('Execution order','Curriculum generated before presentation; supporting material stage required presentation file'),('Application safety','Only package files created; no application/user/hosting/production changes made'),('Runtime application tests','Not rerun for documentation-only work. Prior audit records 89 pass/1 skip; not a fresh certification')]),h('Visual/readability checks'),p('Public screenshots are visually inspected separately; actual capture details are in the register. Substantive slide text uses 20–31 pt, process nodes 16 pt, and smaller provenance/footer text. Manual uses 10.5 pt body and headings. Word tables repeat header rows; PDFs have page numbers. See Visual-Review-Record.md, PDF-Text-Review.json and Slide-Layout-Review-Results.json for actual QA evidence; the HTML slide view is an approximation, not a native render.'),h('Unresolved rendering / accessibility validation'),p('No native PowerPoint/Word renderer is available locally. Structural/browser-approximation validation does not prove zero clipping or optimal Office pagination. Native Office pagination/font substitution and final projection/print readability require target-machine review. Full tagged-PDF/PDF-UA accessibility, assistive-technology review and linguistic approval are not certified.'),h('Outstanding prerequisites'),table(['Gate','Required evidence','Owner'],GATES)]+common_end()
    docfile('09-evidence/Final-Quality-Assurance-Report.docx','Final quality assurance report','Checks performed, conditional readiness and unresolved dependencies',b)
    # Manifest is written last and lists every delivered file, including source and evidence.
    purposes={
      '01-curriculum':'Detailed schedule, measurable outcomes and facilitation plan',
      '02-presentation':'Council-branded slides or editable speaker-note companion',
      '03-participant-manual':'Numbered task reference with permissions, errors, screenshots/pending markers and glossary',
      '04-practical-workbook':'Fictional guided exercises and evidence worksheets',
      '05-quick-references':'Role-specific desk reference',
      '06-assessments':'Blank pre/post tests, rubrics, seven individual sheets and answer key',
      '07-registers':'Blank attendance, feedback, evaluation, assessment and completion workbook',
      '08-handover':'Compliance mapping or blank completion/handover template',
      '09-evidence':'Approved baseline, provenance, generation sequence or QA evidence',
      'assets':'Verified public visual/crest or clearly labelled fictional exercise fixture',
      'source':'Editable reproducible content/build or read-only screenshot capture source',
    }
    rows=[]
    for path in sorted(ROOT.rglob('*')):
        if not path.is_file() or '__pycache__' in path.parts or path.name in ['DELIVERY-REPORT.md','Deliverable-Manifest.json']:continue
        rel=str(path.relative_to(ROOT));group=rel.split('/')[0];status='Generated; structurally checked where applicable; delivery pending'
        if group=='assets':status='Actual capture/brand asset or fictional fixture; see screenshot provenance'
        if 'Approved-System' in rel:status='Approved discovery baseline copied unchanged'
        if group=='source':status='Rebuild source; no application mutation functions'
        rows.append(dict(file=rel,purpose=purposes.get(group,'Package artifact'),verification=status,sha256=hashlib.sha256(path.read_bytes()).hexdigest()))
    (ROOT/'09-evidence/Deliverable-Manifest.json').write_text(json.dumps(rows,indent=2))
    report=['# MRDC training package — delivery report','',f'Prepared {DATE}. Approved discovery baseline; training development authorized.','',f'Produced {len(prs.slides)} slides with facilitator notes, {len(WORKFLOWS)} manual workflows, a 480-minute curriculum, eight guided exercises and seven blank assessment sheets. No training delivery, results or acceptance is claimed.','','## Files, purposes and verification','', '| File | Purpose | Verification status |','|---|---|---|']
    report += [f"| [{x['file']}]({x['file']}) | {x['purpose']} | {x['verification']} |" for x in rows]
    report += ['','The manifest itself is `09-evidence/Deliverable-Manifest.json` (file sizes/hashes may be regenerated); this delivery report is the package index.','', '## Outstanding prerequisites','']+[f'- {g}: {t} Owner: {o}.' for g,t,o in GATES]
    report += ['','## Verification limits','']+['- '+x for x in LIMITS]
    report += ['','## QA and use','',f'Structural checks passed; slide canvas issues: {len(issues)}. No native Office rendering or full accessibility certification. Open DOCX/PPTX/XLSX in the Council’s target software, refresh pagination/field display and visually check before printing/presenting. PDFs are independently generated from the same source content, so pagination can differ from DOCX.','', 'For course delivery: start with the curriculum, present the slides, distribute the manual/workbook/role references, then use blank assessment/register templates. Keep facilitator answer keys separate from test copies. All prerequisites not evidenced remain pending.','', 'Application code, production records, roles and hosting settings were not changed. The only application interaction in development was read-only public screenshot capture.']
    (ROOT/'DELIVERY-REPORT.md').write_text('\n'.join(report)+'\n')
    print(json.dumps({'files':len(rows),'slides':len(prs.slides),'issues':issues,'report':str(ROOT/'DELIVERY-REPORT.md')}))

if __name__=='__main__':
    parser=argparse.ArgumentParser();parser.add_argument('stage',choices=['curriculum','presentation','supporting','qa']);args=parser.parse_args()
    {'curriculum':curriculum,'presentation':presentation,'supporting':supporting,'qa':qa}[args.stage]()
