create user 'sinfin'@'localhost' identified by '123';
grant all privileges on Sinfin.* to 'sinfin'@'localhost';
flush privileges;
create database Sinfin;
use Sinfin;
drop table if exists Programas,Planes,Cursos,Estudiantes;

create table Programas (
       -- Basic
       --  e.g. 210, 211, 204, etc.
       programaid varchar(5),

       programa varchar(50),
       instituto varchar(50),
       registro varchar(50),
       fecharegistro varchar(50),
       fechavenceregistro varchar(50),
       fechaacreditacion varchar(50),
       fechavenceacreditacion varchar(50),

       -- Extras
       extra1 varchar(255),
       extra2 varchar(255),
       extra3 varchar(255),
       primary key (programaid)       
);

create table Planes (
       -- Basic

       --  e.g. 210-v1-m1 
       planid varchar(10),

       version varchar(5),
       modificacion varchar(5),
       acuerdo varchar(255),
       fechaacuerdo varchar(255),

       -- Relation
       Programas_programaid varchar(5),
       
       -- Extras
       extra1 varchar(255),
       extra2 varchar(255),
       extra3 varchar(255),

       primary key (planid)
);

create table Cursos (

       -- Basic
       --  e.g. 0302120c1 
       cursoid varchar(15),

       codigo varchar(10),
       correccion varchar(5),
       nombre varchar(255),
       creditos varchar(2),
       semanas varchar(3),
       ht varchar(2),
       hp varchar(2),
       htp varchar(2),
       hti varchar(2),
       habilitable varchar(2),
       validable varchar(2),
       clasificable varchar(2),
       faltas varchar(2),
       tipo varchar(20),
       acuerdo varchar(255),
       fechaacuerdo varchar(255),
       banco varchar(10),

       -- Relation
       -- e.g. 210-v1-m1;211-v2-m2;
       Planes_planid_s varchar(255),
       -- e.g. 210-v1-m1:45;211-v2-m2:24;
       consecutivo_s varchar(255),
       -- e.g. 210-v1-m1:1;211-v2-m2:3;
       semestre_s varchar(255),
       -- e.g. 210-v1-m1:Fisica;211-v2-m2:Fisica;
       area_s varchar(255),
       -- e.g. 210-v1-m1:0302120c1;211-v2-m2:0303124c2,0303125c1;
       prerrequisito_s varchar(1000),
       -- e.g. 210-v1-m1:0302120c1;211-v2-m2:0303124c2;
       correquisito_s varchar(1000),

       -- Extra
       extra1 varchar(255),
       extra2 varchar(255),
       extra3 varchar(255),

       primary key (cursoid)
);

create table Estudiantes (

       -- Basic
       --  e.g. 71755174 
       documento varchar(20),

       nombre varchar(100),
       email varchar(100),
       password varchar(50),
       universidad varchar(255),

       -- Relation
       -- Estudiante puede haber estado matriculado en 3 programas

       -- e.g. 211-v1-m1;211-v1-m2
       Planes_planid_1_s varchar(10),
       -- e.g. 210-v1-m1;
       Planes_planid_2_s varchar(10),
       Planes_planid_3_s varchar(10),

       -- e.g. 211-v1-m1:0302120c1,0304130c2,0304560c1;211-v1-m2:0311304c1
       Cursos_cursoid_1_s varchar(1000),
       Cursos_cursoid_2_s varchar(1000),
       Cursos_cursoid_3_s varchar(1000),

       -- Extra
       extra1 varchar(255),
       extra2 varchar(255),
       extra3 varchar(255),
       primary key (documento)
);

create table Reconocimientos (
       -- Basic
       -- e.g. 6 characters string aj0788a
       recid varchar(10),
       fecha varchar(50),
       fechahora varchar(50),
       status varchar(10),
       notificado varchar(255),       

       -- e.g. Acta 23 de 2016, Comité de Pregrado
       acto varchar(255),

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

create table Usuarios (
       -- Basic
       -- e.g. 6 characters string aj0788a
       email varchar(50),
       nombre varchar(50),
       password varchar(255),

       -- e.g. Nivel de permisos 1, Basico ; 2, Profesor; 3, Administrador
       nivel varchar(2),

       -- Extras
       extra1 varchar(255),
       extra2 varchar(255),
       extra3 varchar(255),
       primary key (email)       
);
