#-*-coding:utf-8-*-
from sinfin import *
from matplotlib import use,font_manager as fm
use('Agg')
from matplotlib import colors,ticker,patches,pylab as plt
from matplotlib.pyplot import cm
from matplotlib.font_manager import FontProperties
from matplotlib.transforms import offset_copy
from matplotlib import patches
from numpy import *

# ############################################################
# CONNECT TO DATABASE
# ############################################################
sinfin,connection=loadDatabase()
db=connection.cursor()

# ############################################################
# ROUTINES
# ############################################################
def drawRectangle(ax,v,w,h,**args):
    rectangle=patches.Rectangle(v,w,h,**args)
    ax.add_artist(rectangle)
    return rectangle

def textBox(ax,text,v,w,h,fp,**args):
    rect=drawRectangle(ax,v,w,h,**args)
    ax.add_artist(rect)
    rx,ry=rect.get_xy()
    cx=rx+rect.get_width()/2.0
    cy=ry+rect.get_height()/2.0
    t=ax.annotate(text,(cx,cy),color='k',
                ha='center',va='center',
                fontproperties=fp)
    return (rect,t)

# ############################################################
# CONSTANTS
# ############################################################
W=1100.0
H=850.0
S=11.0

sim=50.0
L=sim/2
B=sim*2
T=sim*2
R=sim/2
WC=W-L-R
HC=H-B-T

# ############################################################
# CANVAS
# ############################################################
fig=plt.figure(figsize=(S*W/H,S),dpi=72)
ax=fig.add_axes([0,0,1,1])
ax.axis('off')
ax.set_xlim((0,W))
ax.set_ylim((0,H))

# ############################################################
# DRAWING AREA
# ############################################################
rect=dict(ec='k',fc='w',lw=0.5,ls='dashed')
drawRectangle(ax,(L,B),WC,HC,**rect)

# ############################################################
# BOXES
# ############################################################
Nsem=10
Nasi=6
hspace=15
vspace=5

wbox=WC/Nsem
hbox=HC/Nasi

scur=min(wbox-2*hspace,hbox-2*vspace)


fp=FontProperties(style='normal',size=12)
rect=dict(ec='k',fc='w',lw=0.5)

bcursos=[]
n=1
for i in xrange(Nsem):
    x=L+i*wbox
    xc=x+wbox/2.0
    lcursos=[]
    for j in xrange(Nasi):
        y=B+j*hbox
        rect["ls"]="dotted"
        drawRectangle(ax,(x,y),wbox,hbox,**rect)

        yc=y+hbox/2.0
        rect["ls"]="solid"
        t=textBox(ax,"i,j\n%d = %d,%d\nMiddle"%(n,i,j),
                (xc-scur/2,yc-scur/2),
                scur,scur,fp,**rect)
        lcursos+=[t]
        n+=1
    bcursos+=[lcursos]

# REMOVE A BOX
b=bcursos[0][0]
r=b[0]
t=b[1]
r.set_fc('y')
ax.add_artist(r)
#t.remove()

# ############################################################
# SAVE FIGURE
# ############################################################
fig.savefig("pensum.png")
plt.close()
