use Sinfin;

drop table if exists Actividades,Boletas;

create table Actividades (
       actid varchar(50),
       fechaini varchar(200),
       fechafin varchar(200),
       horaini varchar(100),
       horafin varchar(100),
       nombre varchar(255),
       lugar varchar(255),
       tipo varchar(100),
       intituto varchar(50),
       semestre varchar(50),
       Boletas_rango varchar(50),
       resumen text,
       primary key (actid)
);

create table Boletas (
       boletaid varchar(30),
       numero varchar(10),
       tipo varchar(100),
       instituto varchar(50),
       Usuarios_documento varchar(100),
       Actividades_actid varchar(100),
       fechahora datetime,
       primary key (boletaid)
);
