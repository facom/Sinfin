import MySQLdb as mdb
import csv
from sys import exit,argv
from os import system
from datetime import date
import re

###################################################
#CONFIGURACION
###################################################
BASENAME="Sinfin"
DATABASE="Sinfin"
USER="sinfin"
PASSWORD="123"

###################################################
#ROUTINES
###################################################
class dict2obj(object):
    def __init__(self,dic={}):self.__dict__.update(dic)
    def __add__(self,other):
        for attr in other.__dict__.keys():
            exec("self.%s=other.%s"%(attr,attr))
        return self

def loadDatabase(server='localhost',
                 user=USER,
                 password=PASSWORD,
                 database=DATABASE):
    con=mdb.connect(server,user,password,database,charset="utf8")
    with con:
        dbdict=dict()
        db=con.cursor()
        db.execute("show tables;")
        tables=db.fetchall()
        for table in tables:
            table=table[0]
            dbdict[table]=dict()
            
            db.execute("show columns from %s;"%table)
            fields=db.fetchall()
            dbdict[table]['fields']=[]
            for field in fields:
                fieldname=field[0]
                fieldtype=field[3]
                dbdict[table]['fields']+=[fieldname]
                if fieldtype=='PRI':
                    dbdict[table]['primary']=fieldname

            db.execute("select * from %s;"%table)
            rows=db.fetchall()

            dbdict[table]['rows']=dict()
            for row in rows:
                rowdict=dict()
                i=0
                for field in dbdict[table]['fields']:
                    rowdict[field]=row[i]
                    if field==dbdict[table]['primary']:
                        primary=row[i].strip()
                    i+=1
                dbdict[table]['rows'][primary]=rowdict

    return dbdict,con

def updateDatabase(dbdict,con):
    with con:
        db=con.cursor()
        for table in dbdict.keys():
            print "Actualizando tabla ",table
            primary=dbdict[table]['primary']
            fields="("
            duplicate=""
            for field in dbdict[table]['fields']:
                fields+=field+","
                if field!=primary:
                    duplicate+=field+"=values("+field+"),"
            fields=fields.strip(",")+")"
            duplicate=duplicate.strip(",")
            for row in dbdict[table]['rows'].keys():
                values="("
                for field in dbdict[table]['fields']:
                    value=dbdict[table]['rows'][row][field]
                    if value is None:value=''
                    values+="'"+value+"',"
                values=values.strip(",")+")"
                sql="insert into %s %s values %s on duplicate key update %s"%(table,fields,values,duplicate);
                db.execute(sql);
                con.commit()

def notNull(value,default):
    if value=='':
        nvalue=default
    else:
        nvalue=value
    return nvalue

def rmZero(string):
    nstring=string
    if string[0]=='0':nstring=string[1:]
    return nstring
