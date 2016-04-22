#-*-coding:utf-8-*-
from sinfin import *
from matplotlib import use,font_manager as fm
use('Agg')
from matplotlib import colors,ticker,patches,pylab as plt
from matplotlib.pyplot import cm
from matplotlib.font_manager import FontProperties
from matplotlib.transforms import offset_copy
from numpy import *

# CONNECT TO DATABASE
sinfin,connection=loadDatabase()
db=connection.cursor()

# CONSTANTS
H=1100.0
W=850.0
#FP=fm.FontProperties(family='monospace',size=24)
FP=fm.FontProperties(style='normal',
                     size=24)

# CREATE CANVAS
size=11
fig=plt.figure(figsize=(size,size*W/H),dpi=300)
ax=fig.add_axes([0,0,1,1])
ax.axis('off')
ax.set_xlim((0,1))
ax.set_ylim((0,1))


from matplotlib import patches as pat
from matplotlib import textpath as tp

def textBox(text,c,w,h):
    
    rect=pat.Rectangle(c,w,h,ec='k',fc='w')
    ax.add_artist(rect)
    rx,ry=rect.get_xy()
    cx=rx+rect.get_width()/2.0
    cy=ry+rect.get_height()/2.0
    ax.annotate(text,(cx,cy),color='k',
                ha='center',va='center',
                fontproperties=FP)

def textProps(text):
    p=tp.TextPath((0,0),text,prop=FP)
    b=p.get_extents()
    return b.width/0.95,b.height/0.95



largo="El Perro hace pipi en la call"
textBox(largo,(0.2,0.3),0.4,0.6)
w,h=textProps(largo)
print w/W

text=u"Astronomía\nBuena"
s=len(text)
fs=36
fw=0.67*fs
ax.text(0.5,0.9,text,
        horizontalalignment="left",
        fontproperties=FP,
        bbox=dict(ec='k',fc='w',pad=30),
        transform=ax.transAxes)
p=tp.TextPath((0,0),text,prop=FP)
b=p.get_extents()

"""
for l in linspace(0,1,11):
    print l
    ax.axhline(l,color='k')
"""

ax.axhline(0.9,color='r')
ax.axhline(0.9+b.height/(0.95*H),color='r')
ax.axvline(0.5,color='r')
ax.axvline(0.5+b.width/(0.95*W),color='r')


fig.savefig("pensum.png")

plt.close()
