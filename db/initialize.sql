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

insert ignore into Usuarios (email,nombre,password,nivel) values ('pregradofisica@udea.edu.co','Pregrado Fisica',MD5('123'),'4');
