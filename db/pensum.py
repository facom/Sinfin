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
def splitName(name):
    words=name.split(" ")
    nwords=len(words)
    nname=""
    for i in xrange(nwords-1):
        if words[i]==u"Fundamentación" or\
           words[i]==u"Fundamentos":words[i]="Fund."
        if len(words[i+1])<=3:nname+=words[i]+" "
        else:nname+=words[i]+"\n"
    nname+=words[nwords-1]
    return nname

def multiList(N,M):
    L=[]
    for i in xrange(N+1):
        linea=[]
        for j in xrange(M+1):
            linea+=[dict()]
        L+=[linea]
    return L

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

# Plan
plan="211-v2-m1"

# Size of canvas
W=1100.0
H=850.0
S=11.0

# Number of course boxes
Nsem=10 # Number of semesters
Nasi=5 # Number of courses
hspace=15 # Horizontal space between boxes
vspace=5 # Vertical space between boxes

# Dimensions of canvas
sim=50.0
L=sim/2
B=sim*2
T=sim*2
R=sim/2
WC=W-L-R
HC=H-B-T

# ############################################################
# COURSES
# ############################################################

# Pensum matrix
Pensum=multiList(Nsem,Nasi)

# ========================================
# Get the list of courses
# ========================================
sql="select cursoid from Cursos where Planes_planid_s like '%s%%'"%plan
db.execute(sql)
rows=db.fetchall()

# ========================================
# Build the Pensum matrix
# ========================================
semnums=[0]*(Nsem+1)
for row in rows:
    codigo=row[0]
    curso=sinfin["Cursos"]["rows"][codigo]
    semestres=curso["semestre_s"].split(";")
    for semestre in semestres:
        if plan in semestre:break
    semestre=int(semestre.split(":")[1])
    numasi=semnums[semestre]
    semnums[semestre]+=1
    Pensum[semestre][numasi]=curso

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
rect=dict(ec='k',fc='none',lw=0.5,ls='solid',zorder=10)
drawRectangle(ax,(L,B),WC,HC,**rect)

# ############################################################
# BOXES
# ############################################################
wbox=WC/Nsem # Boxes width
hbox=HC/Nasi # Boxes height

"""
# Matrix of tree vertices
Nx=2*Nsem+1
Ny=2*Nasi+1
Matrix=[]
for i in xrange(Nx):
    linea=[]
    for j in xrange(Ny):
        linea+=[dict()]
    Matrix+=[linea]

wvert=WC/(Nx-1)
hvert=HC/(Ny-1)
for i in xrange(Nx):
    x=L+i*wvert
    for j in xrange(Ny):
        y=B+(HC-j*hvert)
        ax.text(x,y,"%d,%d"%(i,j),ha='center',va='center')
"""

# ////////////////////////////////////////////////////////////
# Size of course box
# ////////////////////////////////////////////////////////////
"""
import sys
reload(sys)
sys.setdefaultencoding("utf-8")
"""
scur=min(wbox-2*hspace,hbox-2*vspace)

fp=FontProperties(style='normal',size=8)
rect=dict(ec='k',fc='w',lw=0.5)

Boxes=[]
n=1
for i in xrange(1,Nsem+1):
    x=L+(i-1)*wbox
    xc=x+wbox/2.0
    lcursos=[]
    for j in xrange(1,Nasi+1):

        curso=Pensum[i][j]
        if "cursoid" in curso.keys():
            cursoid=curso["cursoid"]
            nombre=curso["nombre"]
            nombre=splitName(nombre)
        else:
            cursoid=""
            nombre=""
        
        y=B+(HC-j*hbox)
        rect["ls"]="dotted"
        drawRectangle(ax,(x,y),wbox,hbox,**rect)

        yc=y+hbox/2.0
        rect["ls"]="solid"
        #t=textBox(ax,"i,j\n%d = %d,%d\nMiddle"%(n,i,j),
        t=textBox(ax,u"%s"%nombre,
                  (xc-scur/2,yc-scur/2),
                  scur,scur,fp,**rect)
        lcursos+=[t]
        n+=1
    Boxes+=[lcursos]

# REMOVE EMPTY BOXES
for i in xrange(Nsem):
    for j in xrange(Nasi):
        box=Boxes[i][j]
        rect=box[0]
        text=box[1].get_text()
        if text=='':
            box[1].remove()
            rect.set_ec('none')
            ax.add_artist(rect)
"""
b=bcursos[0][0]
r=b[0]
t=b[1]
r.set_fc('y')
ax.add_artist(r)
"""
#t.remove()

# ############################################################
# SAVE FIGURE
# ############################################################
fig.savefig("pensum.png")
plt.close()
