use Sinfin;

drop table if exists Suscripciones;
create table Suscripciones (
       email varchar(50),
       suscripcion varchar(255),
       fecha varchar(50),
       confirma varchar(2),
       -- Extras
       extra1 varchar(255),
       extra2 varchar(255),
       extra3 varchar(255),
       primary key (email)       
);

/*
drop table if exists Resoluciones,Institutos,Comisiones,Empleados;
create table Sinfin.Resoluciones select * from Comisiones.Resoluciones;
alter table Resoluciones add primary key (resolucionid);
create table Sinfin.Institutos select * from Comisiones.Institutos;
alter table Institutos add primary key (institutoid);
create table Sinfin.Comisiones select * from Comisiones.Comisiones;
alter table Comisiones add primary key (comisionid);
create table Sinfin.Empleados select * from Comisiones.Profesores;
alter table Empleados add primary key (cedula);
*/
/*
alter table Boletas add column tarde varchar(2);
alter table Boletas add column semestre varchar(255);
alter table Boletas add column IP varchar(255);
alter table Actividades add column encargado varchar(255);
alter table Actividades drop column intituto;
alter table Actividades add column instituto varchar(50);
alter table Boletas add column tarde varchar(2);
alter table Boletas add column semestre varchar(255);
alter table Boletas add column IP varchar(255);
alter table Boletas add column fechahora datetime;
alter table Boletas add column nombre varchar(255);
alter table Boletas add column email varchar(255);
*/
/*
alter table Actividades add column Boletas_rango varchar(50);
alter table Boletas add column instituto varchar(50);
alter table Actividades modify column horaini varchar(5);
alter table Actividades modify column horafin varchar(5);
alter table Actividades modify column semestre varchar(10);
alter table Actividades drop column hora;
alter table Actividades add column horaini varchar(255);
alter table Actividades add column horafin varchar(255);
alter table Actividades add column semestre varchar(50);
alter table Actividades add column instituto varchar(50);
alter table Actividades drop column fechahora;
alter table Actividades add column fechaini varchar(255);
alter table Actividades add column fechafin varchar(255);
alter table Actividades add column hora varchar(100);
alter table Actividades drop column lugar;
alter table Actividades add column lugar varchar(255);
alter table Usuarios add column documento varchar(50);
alter table Usuarios drop column nivel;
alter table Usuarios add column permisos varchar(2);
*/
/*
alter table Usuarios add column documento varchar(50);
alter table Usuarios add column parametros varchar(1000) default '';
alter table Usuarios drop column nivel;
alter table Usuarios add column permisos varchar(2);
alter table Reconocimientos add column instituto varchar(255);
*/
/*
alter table Usuarios add column activada varchar(2) default '0';
*/
/*alter table Usuarios add column tipo varchar(100) default 'visitante';*/
/*alter table Usuarios add column programa varchar(100) default 'ninguno';*/
/*alter table Movilidad add column duracion varchar(4);*/
/*alter table Movilidad drop column estado;*/
/*alter table Movilidad add column estado varchar(50);*/
/*alter table Movilidad add column nombre varchar(50);*/
/*alter table Movilidad modify column nombre varchar(255) after documento;*/
/*alter table Movilidad modify column estado varchar(50) after movilid;*/
/*alter table Movilidad modify column idioma varchar(50) after evento;*/
/*alter table Movilidad modify column duracion varchar(4) after fechafin;*/
/*alter table Movilidad add column programa varchar(50);*/


/*
alter table Usuarios drop column permisos;
alter table Usuarios add column permisos varchar(2) default '1';
alter table Usuarios add column activada varchar(2) default '0';
*/
/*alter table Reconocimientos add notificado varchar(255);*/
/*alter table Estudiantes add column universidad varchar(255);*/
/*alter table Reconocimientos add responsables varchar(255);*/
/*alter table Reconocimientos drop column acto;*/
/*alter table Reconocimientos add acto varchar(255);*/
/*alter table Reconocimientos add column fechahora varchar(255);*/
/*alter table Reconocimientos add acto varchar(255);*/
/*alter table Reconocimientos add status varchar(10);*/
/*
alter table Cursos add fechaacuerdo varchar(3);
alter table Cursos add semanas varchar(3);
alter table Programas add codigo varchar(5);
alter table Cursos add posicion_s varchar(3);
*/

/*
alter table misiones add cumplido1 varchar(255);
update Comisiones set estado='cumplida',qcumplido=1,cumplido1='empty.pdf',cumplido2='',destinoscumplido='pregradofisica@udea.edu.co;',confirmacumplido='pregradofisica@udea.edu.co::2015-12-18 12:06:56;',infocumplido='Cumplido de comisión otorgada.' where (tipocom='servicios' or tipocom='estudio') and fechafin<now();
*/

/*
insert ignore into Programas (programaid,programa,instituto) values ('210','Física','Física');
insert ignore into Programas (programaid,programa,instituto) values ('211','Astronomía','Física');
insert ignore into Programas (programaid,programa,instituto) values ('204','Biología','Biología');
insert ignore into Programas (programaid,programa,instituto) values ('216','Química','Química');
insert ignore into Programas (programaid,programa,instituto) values ('222','Tecnología Química','Química');
insert ignore into Programas (programaid,programa,instituto) values ('213','Matemáticas','Matemáticas');
insert ignore into Programas (programaid,programa,instituto) values ('207','Estadística','Matemáticas');

-- FISICA
insert ignore into Planes (planid,version,modificacion,Programas_programaid) values ('210-v4-m1','4','1','210');
insert ignore into Planes (planid,version,modificacion,Programas_programaid) values ('210-v5-m1','5','1','210');

-- ASTRONOMIA
insert ignore into Planes (planid,version,modificacion,Programas_programaid) values ('211-v2-m1','2','1','211');

-- BIOLOGIA
insert ignore into Planes (planid,version,modificacion,Programas_programaid) values ('204-v9-m1','9','1','204');
insert ignore into Planes (planid,version,modificacion,Programas_programaid) values ('204-v10-m1','10','1','204');

-- MATEMATICAS
insert ignore into Planes (planid,version,modificacion,Programas_programaid) values ('213-v3-m1','3','1','213');

-- QUIMICA
insert ignore into Planes (planid,version,modificacion,Programas_programaid) values ('216-v5-m1','5','1','216');
insert ignore into Planes (planid,version,modificacion,Programas_programaid) values ('216-v6-m1','6','1','216');

-- TECNOLOGIA QUIMICA
insert ignore into Planes (planid,version,modificacion,Programas_programaid) values ('222-v5-m1','5','1','222');
insert ignore into Planes (planid,version,modificacion,Programas_programaid) values ('222-v6-m1','6','1','222');
*/

/*
create table Reconocimientos (
       -- Basic
       -- e.g. 6 characters string aj0788a
       recid varchar(10),
       fecha varchar(50),

       -- Relation
       -- Plan en el que se realizan reconocimientos
       Planes_planid varchar(10),
       Estudiantes_documento varchar(20),

       -- Extras
       extra1 varchar(255),
       extra2 varchar(255),
       extra3 varchar(255),
       primary key (recid)       
);
*/

/*
create table Usuarios (
       -- Basic
       -- e.g. 6 characters string aj0788a
       email varchar(50),
       nombre varchar(50),
       password varchar(255),

       -- e.g. Nivel de permisos 1, Basico ; 2, Modificacion; 3, Administrador basico; 4, Propietario
       nivel varchar(2),

       -- Extras
       extra1 varchar(255),
       extra2 varchar(255),
       extra3 varchar(255),
       primary key (email)       
);
*/

/*
insert ignore into Usuarios (email,nombre,password,nivel) values ('pregradofisica@udea.edu.co','Pregrado Fisica',MD5('123'),'4');
*/

