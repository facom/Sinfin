#-*-coding:utf-8-*-
from sinfin import *
sinfin,connection=loadDatabase()
db=connection.cursor()

# ############################################################
# LOAD CSV FILE WITH PROGRAMA
# ############################################################
filecsv=argv[1]
planid=argv[2]
csvfile=open("%s"%filecsv,"rU")
content=csv.DictReader(csvfile,dialect="excel",delimiter=",")

# GET CODIGOS
codigos=[]
cursos=sinfin['Cursos']['rows']
codigos=dict()
for cursoid in cursos.keys():
    codigos[cursos[cursoid]['codigo']]=cursoid

# GET INFORMATION FROM TABLE
start=1
i=0
bancos=dict()
programa=planid.split('-')[0]
for row in content:
    if start:
        fields=row.keys()
        start=0

    curso=dict()
    codigo=row['codigo']

    if codigo not in codigos.keys():
        
        # IF COURSE HAS NOT BEEN ALREADY ADDED
        if len(codigo)!=6:
            if codigo not in bancos.keys():bancos[codigo]=1;
            else:bancos[codigo]+=1
            curso['codigo']=programa+'_'+codigo+'_'+str(bancos[codigo])
            curso['banco']=codigo
        else:
            curso['codigo']=codigo
            curso['banco']=''

        curso['cursoid']=curso['codigo']+'-c1'
        curso['correccion']='1'
        curso['nombre']=row['nombre']
        curso['creditos']=row['cr']
        curso['ht']=notNull(row['ht'],'0')
        curso['hp']=notNull(row['hp'],'0')
        curso['htp']=notNull(row['htp'],'0')
        curso['hti']=notNull(row['hti'],'0')
        curso['faltas']=notNull(row['fal'],'0')

        hvc=row['HVC']
        for x in hvc:
            if x=='H':curso['habilitable']=1
            if x=='V':curso['validable']=1
            if x=='C':curso['clasificable']=1
        for x in 'habilitable','validable','clasificable':
            if x not in curso.keys():curso[x]=0

        tipos=dict(bas='basico',prof='profesional',compl='complementario')
        for x in 'bas','prof','compl':
            if row[x].upper()=='SI':curso['tipo']=tipos[x]
        if 'tipo' not in curso.keys():curso['tipo']='complementario'

        curso['Planes_planid_s']=planid+";"

        curso['consecutivo_s']=''
        curso['semestre_s']=''
        curso['area_s']=''
        curso['semanas']=''
        curso['prerrequisito_s']=''
        curso['correquisito_s']=''

        qnew=1
    else:
        cursoid=codigos[codigo]
        curso=cursos[cursoid]
        qnew=0

    # COMUNES
    curso['consecutivo_s']+=planid+":"+row['cons']+";"
    curso['semestre_s']+=planid+":"+row['semest']+";"
    curso['area_s']+=planid+":"+row['area']+";"
    curso['semanas']+=row['sem']

    # PRE REQUISITES
    preyco=row['preyco']
    prerrequisites=planid+":"
    corequisites=planid+":"
    if re.search('\(',preyco):
        preyco=re.sub("\s+","",preyco)
        parts=preyco.split("(")
        if len(parts)>1:
            for part in parts[1:]:
                if part[0:2]=='PR':
                    prerrequisites+=part[3:]+"-c1;"
                elif part[0:2]=='CO':
                    corequisites+=part[3:]+"-c1;"
                elif part[0:2]=='CR':
                    prerrequisites+='c'+part[3:]+";"
    else:
        curso['prerrequisito_s']+=planid+":;"
        curso['correquisito_s']+=planid+":;"

    prerrequisites+=";"
    corequisites+=";"
    
    curso['prerrequisito_s']+=prerrequisites
    curso['correquisito_s']+=corequisites

    print curso
    exit(0)
    
    #print curso
    #if i>10:break

    for x in 'acuerdo','fechaacuerdo','extra1','extra2','extra3':
        curso[x]=''

    sinfin['Cursos']['rows'][curso['cursoid']]=curso
    i+=1
    #print curso
    #break

updateDatabase(sinfin,connection)
