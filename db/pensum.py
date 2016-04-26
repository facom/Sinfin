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
import re

# ############################################################
# CONNECT TO DATABASE
# ############################################################
sinfin,connection=loadDatabase()
db=connection.cursor()

# ############################################################
# ROUTINES
# ############################################################
def vertexSlot(vertex,iv=0,jv=0,dn=0,dm=0,nc=999):
    """
    Use: 
         i=0,j=0,dn=0,dm=-1 for starting in a course as prerrequisite
         i=0,j=0,dn=+/-1,dm=0 for starting in a course as correquisite
    """
    n,m=vertex.shape

    if dn==0:
        i=jv
        j=jv
        if vertex[i,j]!=0:
            for i in xrange(n):
                if vertex[i,j]==0:break
    if dm==0:
        i=iv
        j=iv
        if vertex[i,j]!=0:
            for j in xrange(n):
                if vertex[i,j]==0:break

    vertex[i,j]=nc
    return i,j

def stepMatrix(n,m,nt,mt,Matrix=[],tstep="pre",verbose=0):
    msgn=sign(mt-m) or +1
    nsgn=sign(nt-n) or -1

    if tstep=="co":
        # %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
        # VERTICAL FIRST
        # %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
        m+=msgn
        if verbose:print "\t"*4,"Trying step to:",n,m

        qvert=False

        # If you are in a course box
        if (n%2 and m%2) and distMatrix(n,m,nt,mt):
            if verbose:print "\t"*4,"This is a course box"
            qvert=True

        if -msgn*(m-mt)<=0 and distMatrix(n,m,nt,mt):
            if verbose:print "\t"*4,"I went too far"
            qvert=True

        if qvert:
            if verbose:print "\t"*4,"Better horizontal"
            m-=msgn
            # %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
            # THEN VERTICAL
            # %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
            n+=nsgn
        else:
            if verbose:print "\t"*4,"Step accepted."
            
    else:

        # %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
        # HORIZONTAL FIRST
        # %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
        n+=nsgn
        if verbose:print "\t"*4,"Trying step to:",n,m

        qvert=False
        # If you are in a course box
        if (n%2 and m%2) and distMatrix(n,m,nt,mt):
            if verbose:print "\t"*4,"This is a course box"
            qvert=True

        if n<=nt and distMatrix(n,m,nt,mt):
            if verbose:print "\t"*4,"I went too far"
            qvert=True

        if qvert:
            if verbose:print "\t"*4,"Better vertical"
            n-=nsgn
            # %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
            # THEN VERTICAL
            # %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
            m+=msgn
        else:
            if verbose:print "\t"*4,"Step accepted."
            
    if verbose:print "\t"*4,"Final step:",n,m

    return n,m

def distMatrix(n,m,nt,mt):
    return abs(n-nt)+abs(m-mt)

def splitField(field):
    values=field.split(";")
    splits=dict()
    for value in values:
        if value=='':continue
        parts=value.split(":")
        if re.match("\w",parts[1]):
            lista=parts[1].strip(",")
            elements=lista.split(",")
            splits[parts[0]]=elements
    return splits

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
Nasi=6 # Number of courses
Nx=2*Nsem+1
Ny=2*Nasi+1
hspace=10 # Horizontal space between boxes
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
# GET THE LIST OF COURSES
# ========================================
sql="select cursoid from Cursos where Planes_planid_s like '%s%%'"%plan
db.execute(sql)
rows=db.fetchall()

# ========================================
# BUILD THE PENSUM MATRIX
# ========================================
semnums=[1]*(Nsem+1)
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

# ////////////////////////////////////////////////////////////
# Size of course box
# ////////////////////////////////////////////////////////////
scur=min(wbox-2*hspace,hbox-2*vspace)

fp=FontProperties(style='normal',size=8)
rect=dict(ec='k',fc='w',lw=0.5)

# ========================================
# FILL COURSE BOXES
# ========================================
Boxes=[]
Locations=dict()
n=1
for i in xrange(1,Nsem+1):
    x=L+(i-1)*wbox
    xc=x+wbox/2.0
    lcursos=[]
    for j in xrange(1,Nasi+1):
        curso=Pensum[i][j]
        if "cursoid" in curso.keys():
            cursoid=curso["cursoid"]
            codigo=cursoid.split("-")[0]
            nombre=curso["nombre"].strip()
            nombre=splitName(nombre)
            Locations[cursoid]=[i,j]
        else:
            codigo=""
            cursoid=""
            nombre=""
        texto=u"%s\n%s"%(nombre,codigo)
        
        y=B+(HC-j*hbox)
        rect["ls"]="dotted"
        drawRectangle(ax,(x,y),wbox,hbox,**rect)

        yc=y+hbox/2.0
        rect["ls"]="solid"
        t=textBox(ax,texto,
                  (xc-scur/2,yc-scur/2),
                  scur,scur,fp,**rect)
        lcursos+=[t]
        n+=1
    Boxes+=[lcursos]

# ========================================
# REMOVE EMPTY BOXES
# ========================================
for i in xrange(Nsem):
    for j in xrange(Nasi):
        box=Boxes[i][j]
        rect=box[0]
        text=box[1].get_text()
        if not re.match(r"\w",text):
            box[1].remove()
            rect.set_ec('none')
            ax.add_artist(rect)

# ========================================
# VERTICES GRID
# ========================================
Vertices=multiList(Nx+1,Ny+1)
for i in xrange(1,Nx+1):
    for j in xrange(1,Ny+1):
        Vertices[i][j]=zeros((5,5))

# ========================================
# PATH TO PREREQUISITES AND CORREQUISITES
# ========================================
verbose=1

# Numbering of conectores
nc=1
Conectores=[dict()]
for i in arange(Nsem+1)[::-1]:
    for j in arange(Nasi+1)[::-1]:
        curso=Pensum[i][j]
        if 'cursoid' in curso.keys():
            cursoid=curso["cursoid"]
            curso["conectores"]=[]
            it,jt=Locations[cursoid]
            
            # Where do we need to start
            nt=2*it-1
            mt=2*jt-1

            if verbose:print "Starting from:",cursoid,curso["nombre"],nt,mt

            # $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
            # PRERREQUISTES
            # $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$

            if verbose:print "\t","Prerrequisitos:",curso['prerrequisito_s']
            pres=splitField(curso['prerrequisito_s'])
            if not plan in pres.keys():continue
            if verbose:print "\t","For plan:",pres[plan]
            
            for pre in pres[plan]:

                # Check if credit
                steps=[]
                if re.match(r"^c\d+",pre):
                    if verbose:print "\t","Skipping"
                    continue
                ib,jb=Locations[pre]

                # Vertex initialization
                iv,jv=vertexSlot(Vertices[it][jt],iv=0,jv=2,dn=0,dm=-1,nc=nc)
                if verbose:print "\t"*2,"Vertice:",iv,jv

                # Where do we need to arrive
                nb=2*ib-1
                mb=2*jb-1

                if verbose:print "\t","Arriving to:",pre,nb,mb

                # Where are we now
                na=nt
                ma=mt
                steps+=[[na,ma]]
                if verbose:print "\t"*2+"Step: (",na,ma,")"
                k=0
                nao=na;mao=ma
                while distMatrix(na,ma,nb,mb)!=0:
                    na,ma=stepMatrix(na,ma,nb,mb,tstep="pre",verbose=verbose)
                    steps+=[[na,ma]]

                    # New vertex
                    iv,jv=vertexSlot(Vertices[na][ma],iv=iv,jv=jv,dn=na-nao,dm=ma-mao)
                    if verbose:print "\t"*4,"Source: (iv=%d,jv=%d;dn=%d,dm=%d); Vertice:"%(iv,jv,na-nao,ma-mao),iv,jv
                    nao=na;mao=ma

                    if verbose:print "\t"*2+"Step: (",na,ma,")"
                    k+=1
                    if k>15:break

                nc+=1
                if verbose:print "\t"*2,"Full steps:",steps
                conector=dict(tipo='pre',steps=steps)
                curso["conectores"]+=[conector]
                Conectores+=[conector]
                
            # $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
            # CORREQUISTES
            # $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
            if verbose:print "\t","Correquisitos:",curso['correquisito_s']
            cos=splitField(curso['correquisito_s'])
            if not plan in cos.keys():continue
            if verbose:print "\t","For plan:",cos[plan]
            
            for co in cos[plan]:

                steps=[]
                if re.match(r"^c\d+",co):
                    if verbose:print "\t","Skipping"
                    continue
                ib,jb=Locations[co]

                # Vertex initialization
                iv,jv=vertexSlot(Vertices[it][jt],iv=3,jv=0,dn=0,dm=-1)
                if verbose:print "\t"*4,"Vertice:",iv,jv

                # Where do we need to arrive
                nb=2*ib-1
                mb=2*jb-1

                if verbose:print "\t","Arriving to:",co,nb,mb

                # Where are we now
                na=nt
                ma=mt
                steps+=[[na,ma]]
                if verbose:print "\t"*2+"Step: (",na,ma,")"
                k=0

                nao=na;mao=ma
                while distMatrix(na,ma,nb,mb)!=0:
                    na,ma=stepMatrix(na,ma,nb,mb,tstep="co",verbose=verbose)
                    steps+=[[na,ma]]
                    
                    # New vertex
                    iv,jv=vertexSlot(Vertices[na][ma],iv=iv,jv=jv,dn=na-nao,dm=ma-mao)
                    if verbose:print "\t"*2,"Vertice:",iv,jv
                    nao=na;mao=ma

                    if verbose:print "\t"*2+"Step: (",na,ma,")"
                    k+=1
                    if k>15:break

                if verbose:print "\t"*2,"Full steps:",steps
                curso["conectores"]+=[dict(tipo='co',steps=steps)]

# ========================================
# JOINING POINTS
# ========================================
"""
for i in arange(Nsem+1)[::-1]:
    for j in arange(Nasi+1)[::-1]:
        curso=Pensum[i][j]
        if 'cursoid' in curso.keys():
            cursoid=curso["cursoid"]
            conectores=curso["conectores"]
            print "Curso:",cursoid
            for conector in conectores:
                print conector,
            print
"""

# ========================================
# TEXT ON GRID
# ========================================
wvert=WC/(Nx-1)
hvert=HC/(Ny-1)
for i in xrange(Nx):
    x=L+i*wvert
    for j in xrange(Ny):
        y=B+(HC-j*hvert)
        ax.text(x,y,"%d,%d"%(i,j),ha='center',va='center')

# ############################################################
# SAVE FIGURE
# ############################################################
fig.savefig("pensum.png")
plt.close()
