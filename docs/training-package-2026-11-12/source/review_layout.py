"""Office-independent slide layout approximation for review, not a native render."""
from pathlib import Path
from pptx import Presentation
from pptx.enum.shapes import MSO_SHAPE_TYPE
from html import escape
from PIL import Image
import base64,json,subprocess,xml.etree.ElementTree as ET
ROOT=Path(__file__).resolve().parents[1]
prs=Presentation(ROOT/'02-presentation/MRDC-Website-Training.pptx')
def px(v):return v/914400*96
parts=['<!doctype html><html><head><meta charset="utf-8"><title>MRDC slide layout review — approximation, not native PowerPoint</title><style>body{margin:0;background:#ccd5dc;font-family:Arial,sans-serif}.slide{width:1280px;height:720px;background:white;position:relative;margin:20px 0;overflow:hidden}.shape{position:absolute;box-sizing:border-box}.text{white-space:normal;overflow:visible}.text p{margin:0;padding:0;line-height:1.12}</style></head><body>']
for i,slide in enumerate(prs.slides,1):
 parts.append(f'<section class="slide" id="slide-{i}">')
 for n,sh in enumerate(slide.shapes):
  style=f'left:{px(sh.left)}px;top:{px(sh.top)}px;width:{px(sh.width)}px;height:{px(sh.height)}px;'
  if sh.shape_type==MSO_SHAPE_TYPE.PICTURE:
   encoded=base64.b64encode(sh.image.blob).decode();parts.append(f'<img class="shape" style="{style}" src="data:{sh.image.content_type};base64,{encoded}">')
  elif sh.has_text_frame and sh.text.strip():
   parts.append(f'<div data-slide="{i}" data-shape="{n}" class="shape text" style="{style}">')
   for p in sh.text_frame.paragraphs:
    f=p.font
    try:color=str(f.color.rgb)
    except:color='0B2D4D'
    size=(f.size.pt if f.size else 22)*96/72
    parts.append(f'<p style="font-size:{size}px;color:#{color};font-weight:{700 if f.bold else 400};margin-bottom:{(p.space_after.pt if p.space_after else 0)*96/72}px">{escape(p.text)}</p>')
   parts.append('</div>')
  else:
   try:fill=str(sh.fill.fore_color.rgb)
   except:fill='FFFFFF'
   parts.append(f'<div class="shape" style="{style}background:#{fill}"></div>')
 parts.append('</section>')
parts.append('</body></html>')
(ROOT/'09-evidence/Slide-Layout-Review.html').write_text(''.join(parts))
print('Layout approximation written; no native PowerPoint rendering claimed.')
