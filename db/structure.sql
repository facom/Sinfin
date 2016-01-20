create user 'sinfin'@'localhost' identified by '123';
create database Sinfin;
grant all privileges on Sinfin.* to 'sinfin'@'localhost';
flush privileges;
use sinfin;

drop table if exists Pensums,Cursos;

create table Programas (
       /*Basic*/
       /* e.g. 210, 211, 204, etc.*/
       programaid varchar(5),

       programa varchar(50);
       instituto varchar(50),
       registro varchar(50),
       fecharegistro varchar(50),
       fechavence varchar(50),
       acreditado varchar(2),

       /*Extras*/
       extra1 varchar(255),
       extra2 varchar(255),
       extra3 varchar(255),
       primary key (programaid)       
);

create table Planes (
       /*Basic*/
       /* e.g. 210-v1-c1 */
       planid varchar(10),

       version varchar(5),
       correccion varchar(5),
       acuerdos varchar(255),
       fechaacuerdos varchar(255),

       /*Relation*/
       programaid varchar(5),
       
       /*Extras*/
       extra1 varchar(255),
       extra2 varchar(255),
       extra3 varchar(255),

       primary key (planid)
);

create table Cursos (

       /*Basic*/
       /* e.g. 0302120c1 */
       cursoid varchar(15),

       codigo varchar(10),
       correccion varchar(5),
       nombre varchar(255),
       creditos varchar(2),
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
       banco varchar(10),

       /*Relation*/
       planids varchar(255),
       consecutivos varchar(255),
       semestres varchar(2),
       areas varchar(255),
       prerrequisitos varchar(1000),
       correquisitos varchar(1000),

       /*Extra*/
       extra1 varchar(255),
       extra2 varchar(255),
       extra3 varchar(255),

       primary key (cursoid)
);

create table Estudiantes (

       /*Basic*/
       /* e.g. 71755174 */
       documento varchar(20),

       nombre varchar(100),
       email varchar(100),
       password varchar(50),

       /*Relation*/
       /*Estudiante puede haber estado matriculado en 3 programas*/
       planids_1 varchar(10),
       planids_2 varchar(10),
       planids_3 varchar(10),
       cursoids_1 varchar(1000),
       cursoids_2 varchar(1000),
       cursoids_3 varchar(1000),

       /*Extra*/
       extra1 varchar(255),
       extra2 varchar(255),
       extra3 varchar(255),
       primary key (documento)
);
