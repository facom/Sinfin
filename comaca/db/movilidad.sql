use Sinfin;
create table Movilidad (

       /* Basico */
       movilid varchar(5),
       documento varchar(20),
       email varchar(255),
       nombre varchar(50),
       programa varchar(50),

       /* Estado */
       estado varchar(50),
       fechaestado varchar(255),
       fechapresenta varchar(255),
       fechaini varchar(255),
       fechafin varchar(255),
       duracion varchar(4),
       idioma varchar(50),

       /* Evento */       
       tipoevento varchar(50),
       lugar varchar(255),
       evento varchar(1000),

       /* Presupuesto */
       item1 varchar(100),value1 varchar(50),fuente1 varchar(100),
       item2 varchar(100),value2 varchar(50),fuente2 varchar(100),
       item3 varchar(100),value3 varchar(50),fuente3 varchar(100),
       item4 varchar(100),value4 varchar(50),fuente4 varchar(100),
       item5 varchar(100),value5 varchar(50),fuente5 varchar(100),
       total varchar(100),
       valor varchar(100),

       /* Profesor */
       documento_profesor varchar(20),
       profesor varchar(255),
       email_profesor varchar(255),

       /* Archivos */
       historia varchar(255),
       carta varchar(255),
       cumplido varchar(255),
       compromiso varchar(255),

       observaciones text,
       observacionesadmin text,

       /* Aprobación */
       respuesta varchar(2),
       tipoapoyo varchar(50),
       monto varchar(255),
       acto varchar(255),
      
       /* Aprobación */
       extra1 varchar(255),
       extra2 varchar(255),
       extra3 varchar(255),

       primary key(movilid)
);
