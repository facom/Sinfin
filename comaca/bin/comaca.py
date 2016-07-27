#!/usr/bin/env python
#-*- coding:utf-8 -*-
from __future__ import unicode_literals
from matplotlib import pyplot as plt
from matplotlib.font_manager import FontProperties
from sys import exit
from os import system
import random,string
import numpy as np

#########################################
# ROUTINES
#########################################
def randomStr(N):
    stri=''.join(random.SystemRandom().choice(string.ascii_uppercase + string.digits) for _ in range(N))
    return stri

def readFile(filename):
    lines = [line.rstrip('\n') for line in open(filename)]
    return lines

#########################################
# READ LOGO
#########################################
logo=plt.imread("logo.png")

#########################################
# SIZE
#########################################
# RATIO
f=8.0/6.0

# SIZE
W=8
H=f*W

# GRID
nx=2
ny=4
nbol=nx*ny
dx=1.0/nx
dy=1.0/ny

# TEXT
fsize=6*f #10*f
offs=3

#########################################
# INFO
#########################################
ntot=100

# Nombre, Numero de hojas
activities=[
    [u"Reunion",0,ntot,1],
    [u"Seminario",0,ntot,1],
    [u"Club de Revistas",0,ntot,1],
    [u"Divulgacion",0,ntot,1]
]
Nstr=6

#########################################
# GRID
#########################################
directory="talonarios"
nsheets=250

lines=[]
counter=int(readFile("numbers.dat")[0])
strings=readFile("strings.dat",)

#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
#SHEETS
#%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
p=1
for n in xrange(ntot):
    
    fig=plt.figure(figsize=(W,H),dpi=300)
    iname="%05d-"
    y=0
    k=0

    for i in xrange(ny):
        
        x=0
        for j in xrange(nx):

            #==============================
            #ACTIVIDAD
            #==============================
            activity=activities[k%4]
            actividad=activity[0]
            ns=activity[activity[3]]
            activity[activity[3]]+=1

            if activity[3]==1:activity[3]=2
            else:activity[3]=1

            #==============================
            #UNIQUE STRING
            #==============================
            seq=randomStr(Nstr)
            if seq in strings:continue
            
            #==============================
            #INNER REGION
            #==============================
            ax=fig.add_axes([x,y,dx,dy])
            ax.set_xticks([])
            ax.set_yticks([])
            ax.axis("off")
            
            header="Comunidad Académica de Astronomía"
            fontheader=FontProperties()
            fontheader.set_size(fsize)
            fontheader.set_weight("bold")
            
            fontactividad=FontProperties()
            fontactividad.set_size(fsize-0*f)
            
            ident=seq
            fontid=FontProperties()
            fontid.set_size(fsize+2*f)
            fontid.set_weight("bold")
            
            num="%05d"%(ns+counter)
            
            fontnum=FontProperties()
            fontnum.set_size(fsize-2*f)
            
            info="""Actividad:______________________________________________
Fecha:______________________________________________
Nombre:______________________________________________
Documento:______________________________________________
"""
            fontinfo=FontProperties()
            fontinfo.set_size(fsize-2*f)
            fontinfo.set_style("italic")
            
            ax.text(0.5,0.55,header,transform=ax.transAxes,
                    fontproperties=fontheader,
                    ha='center',va='top')
            
            ax.text(0.5,0.50,actividad,transform=ax.transAxes,
                    fontproperties=fontactividad,
                    ha='center',va='top')
            
            ax.text(0.5,0.40,seq,transform=ax.transAxes,
                    fontproperties=fontid,
                    ha='center',va='top')
            
            ax.text(0.5,0.2,info,transform=ax.transAxes,
                    fontproperties=fontinfo,
                    bbox=dict(ec='k',fc='w',pad=20),
                    ha='center',va='center')
            
            ax.text(0.95,0.05,num,transform=ax.transAxes,
                    fontproperties=fontnum,
                    ha='right',va='bottom')
            
            #==============================
            #LOGO
            #==============================
            axi=fig.add_axes([x-dx/7,y+3*dy/5,dx,dy/3],anchor="NE",zorder=100)
            axi.axis("off")
            axi.set_xticks([])
            axi.set_yticks([])
            axi.imshow(logo)
            
            x+=dx

            strings+=[seq]
            lines+=[u"%s %s %s"%(num,actividad,seq)]

            k+=1
            #break
        y+=dy
        #break

    print "Página %s... Done."%n
    fig.savefig("%s/talonario-%03d.pdf"%(directory,n))
    plt.close("all")
    del(fig)

fs=open("strings.dat","w")
for string in strings:
    fs.write(u"%s\n"%string)
fs.close()

ft=open("total.dat","w")
for line in lines:
    ft.write(u"%s\n"%line)
ft.close()

fn=open("numbers.dat","w")
fn.write(u"%d\n"%(activities[0][1]+1))
fn.close()

print "Joining talonarios..."
system("gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=talonarios.pdf %s/talonario-*.pdf"%directory)
