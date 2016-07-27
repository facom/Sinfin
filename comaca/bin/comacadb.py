#!/usr/bin/env python
#-*- coding:utf-8 -*-
from sys import exit
from os import system
import numpy as np

ft=open("total.dat","r")
fs=open("insert.sql","w")
fs.write("use Sinfin;\n")
for line in ft:
    line=line.strip("\n")
    parts=line.split()
    numero=int(parts[0])
    actid=parts[-1]
    tipo=" ".join(parts[1:-1])
    if tipo=="Reunion":tipo="reunion"
    if tipo=="Divulgacion":tipo="divulgacion"
    if tipo=="Seminario":tipo="seminario"
    if tipo=="Club de Revistas":tipo="clubrevistas"

    fs.write("insert into Boletas (boletaid,numero,tipo) values ('%s','%d','%s');\n"%(actid,numero,tipo))
    #print "N=%d,C=%s,T=%s"%(numero,actid,tipo)

fs.close()

