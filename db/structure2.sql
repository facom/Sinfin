/*
create user 'sinfin'@'localhost' identified by '123';
grant all privileges on Sinfin.* to 'sinfin'@'localhost';
flush privileges;
*/
drop database if exists Sinfin2;
create database Sinfin2;
grant all privileges on Sinfin2.* to 'sinfin'@'localhost';


/* Crear otras tablas */

/* SISTEMA DE COMISIONES */
create table Sinfin2.Comisiones_Solicitudes select * from Comisiones.Comisiones;
alter table Sinfin2.Comisiones_Solicitudes add primary key (comisionid);
create table Sinfin2.Comisiones_Resoluciones select * from Comisiones.Resoluciones;
alter table Sinfin2.Comisiones_Resoluciones add primary key (resolucionid);

/* MOVILIDAD */
create table Sinfin2.Movilidad_Solicitudes select * from Sinfin.Movilidad;
alter table Sinfin2.Movilidad_Solicitudes add primary key (movilid);

/* RECONOCIMIENTOS */
create table Sinfin2.Reconocimientos_Solicitudes select * from Sinfin.Reconocimientos;
alter table Sinfin2.Reconocimientos_Solicitudes add primary key (recid);

/* MICROCURRICULOS */
create table Sinfin2.Microcurriculos_Cursos select * from Curriculo.MicroCurriculos;
alter table Sinfin2.Microcurriculos_Cursos add primary key (F100_Codigo);
create table Sinfin2.Microcurriculos_Publicos select * from Curriculo.MicroCurriculos_Publicos;
alter table Sinfin2.Microcurriculos_Publicos add primary key (F000_AUTO_Codigoid);
create table Sinfin2.Microcurriculos_Reciclados select * from Curriculo.MicroCurriculos_Recycle;
alter table Sinfin2.Microcurriculos_Reciclados add primary key (F100_Codigo);

/* PROGRAMAS */
create table Sinfin2.Programas_Programas select * from Sinfin.Programas;
alter table Sinfin2.Programas_Programas add primary key (programaid);
create table Sinfin2.Programas_Planes select * from Sinfin.Planes;
alter table Sinfin2.Programas_Planes add primary key (planid);
create table Sinfin2.Programas_Cursos select * from Sinfin.Cursos;
alter table Sinfin2.Programas_Cursos add primary key (cursoid);

/* COMACA */
create table Sinfin2.Comaca_Actividades select * from Sinfin.Actividades;
alter table Sinfin2.Comaca_Actividades add primary key (actid);
create table Sinfin2.Comaca_Boletas select * from Sinfin.Boletas;
alter table Sinfin2.Comaca_Boletas add primary key (boletaid);

/* DEPENDENCIAS */
create table Sinfin2.Dependencias select institutoid as dependenciaid,instituto as dependencia from Comisiones.Institutos;
alter table Sinfin2.Dependencias add primary key (dependenciaid);


/* TABLA DE USUARIOS */
create table Sinfin2.Usuarios (
       -- Basicos
       email varchar(50),
       nombre varchar(50),
       password varchar(255),
       tipoid varchar(50) default 'cedula',
       documento varchar(255),

       -- Tipos: Externo, Visitante, Estudiante, Empleado, Vinculado, Ocasional, Secretario, Coordinador
       tipo varchar(255) default 'EXTERNO',

       -- Dependencia a la que esta adscrito o matriculado o que tiene su plaza
       -- Dependencias: fisica, biologia, quimica, matematicas, decanato, vicedecanato, facultad
       dependenciaid varchar(20) default 'facultad',

       -- <cargo> <dependenciaid>
       -- Cargos: director,coordinador,secretario 
       -- Ej. director decanato (decano), director vicedecanato (vicedecano), secretario fisica
       cargo varchar(255) default '',
       dedicacion varchar(10) default 'No',

       -- Nivel de permisos:
       -- Basicos: 0-Visitante/Anonimo, 1-Estudiante/Usuario, 2-Profesor/Empleado, 
       -- Avanzados: 3-Coordinador o Secretaria, 4-Superusuario/Vicedecano/Decano
       permisos varchar(2) default '1',

       -- Cuenta activda 
       activada varchar(2) default '0',

       -- Extras
       -- Para el caso de los profesores este campo dice si tienen dedicación exclusiva	
       extra1 varchar(255),
       extra2 varchar(255),
       extra3 varchar(255),
       extra4 varchar(255),
       extra5 varchar(255),

       primary key (email)       
);

/* COPIA DE LA INFORMACIÓN DE USUARIOS DE OTRAS BASES DE DATOS */
insert into Sinfin2.Usuarios 
       (
       tipoid,
       documento,
       nombre,
       tipo,
       email,
       password,
       activada,
       dependenciaid,
       dedicacion,
       permisos
       )
       select 
       'cedula',
       documento,
       upper(nombre),
       'ESTUDIANTE',
       email,
       password,
       activada,
       'facultad',
       'No',
       '1'
       from Sinfin.Usuarios;

/* DATOS DE USUARIOS DESDE EL SISTEMA DE COMISIONES */
insert into Sinfin2.Usuarios 
       (
       tipoid,
       documento,
       nombre,
       tipo,
       email,
       password,
       dedicacion,
       extra1,
       activada,
       dependenciaid,
       permisos
       ) 
       select 
       tipoid,
       cedula,
       upper(nombre),
       upper(tipo),
       email,
       pass,
       dedicacion,
       extra1,
       '1',
       institutoid,
       '4'
       from Comisiones.Profesores on duplicate key update 
       tipoid=values(tipoid),
       documento=values(documento),
       nombre=values(nombre),
       tipo=values(tipo),
       email=values(email),
       password=values(password),
       extra1=values(extra1),
       activada=values(activada),
       dependenciaid=values(dependenciaid),
       permisos=values(permisos);

/* VALORES FALTANTES */
update Sinfin2.Dependencias
       set dependenciaid='decanato',dependencia='Decanato' 
       where dependenciaid='decanatura';

insert into Sinfin2.Dependencias
       (dependenciaid,dependencia)
       values ('vicedecanato','Vicedecanato');

insert into Sinfin2.Dependencias
       (dependenciaid,dependencia)
       values ('facultad','Facultad');

update Sinfin2.Usuarios set tipo='EMPLEADO' where tipo='EMPLEADA';
update Sinfin2.Usuarios set tipo='SECRETARIO' where tipo='SECRETARIA';

/* SUPERUSUARIO */
update Sinfin2.Usuarios set permisos='6' where email='pregradofisica@udea.edu.co';
update Sinfin2.Usuarios set tipo='COORDINADOR',dependenciaid='fisica',cargo='coordinador fisica' where email='pregradofisica@udea.edu.co';

/* FIJA PERMISOS*/
update Sinfin2.Usuarios set permisos='4' where email='jorge.zuluaga@udea.edu.co';
update Sinfin2.Usuarios set permisos='1',tipo='EXTERNO' where email='zuluagajorge@gmail.com';
update Sinfin2.Usuarios set permisos='1',tipo='EXTERNO' where email not like '%udea.edu.co';
update Sinfin2.Usuarios set permisos='2' where email like '%udea.edu.co' and permisos+0<4;
