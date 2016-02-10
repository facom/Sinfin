use Sinfin;

/*
alter table Usuarios add column documento varchar(50);
alter table Usuarios drop column nivel;
alter table Usuarios add column permisos varchar(2);
*/
alter table Usuarios add column documento varchar(50);
alter table Usuarios add column parametros varchar(1000) default '';
alter table Usuarios drop column nivel;
alter table Usuarios add column permisos varchar(2);
alter table Reconocimientos add column instituto varchar(255);

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
