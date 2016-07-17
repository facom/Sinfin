#!/usr/bin/env python
#-*- coding:utf-8 -*-
import matplotlib.pyplot as plt
import md5
from sys import exit
import random,string

logo=plt.imread("logo.png")



def randomStr(N):
    stri=''.join(random.SystemRandom().choice(string.ascii_uppercase + string.digits) for _ in range(N))
    return stri

f=8.0/6.0
W=12.0
H=f*W

nx=5
ny=int(H/W*nx)
print ny

Nstr=10

dx=1.0/nx
dy=1.0/ny
y=0

strings=[]
fsize=10*f
offs=3
for nt in xrange(3):
    fig=plt.figure(figsize=(W,H),dpi=70)
    y=0
    for i in xrange(ny):
        x=0
        for j in xrange(nx):
            seq=randomStr(Nstr)
            if seq in strings:continue
            ax=fig.add_axes([x,y,dx,dy])
            axi=fig.add_axes([x,y+dy/2,dx,dy/2],anchor="NE",zorder=100)
            ax.set_xticks([])
            ax.set_yticks([])
            msg="Comunidad Academica\n%s"%seq
            ax.text(0.5,0.5,msg,transform=ax.transAxes,fontsize=fsize,ha='center',va='top')
            axi.set_xticks([])
            axi.set_yticks([])
            axi.imshow(logo)
            x+=dx
            strings+=[seq]
        y+=dy
    fig.savefig("talonario-%03d.png"%(nt+offs))
    plt.close("all")

fl=open("strings.dat","a")
for stri in strings:
    fl.write("%s\n"%stri)
fl.close()
