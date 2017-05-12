<?php
////////////////////////////////////////////////////////////////////////
//MICROCURRICULOS
////////////////////////////////////////////////////////////////////////
$MIC_DIR="data/microcurriculos";

$MIC_FIELDS=array("F010_AUTO_Fecha_Actualizacion","F015_AUTO_Usuario_Actualizacion","F020_AUTH_Autorizacion_Vicedecano","F025_AUTH_Version","F030_AUTH_Acta_Numero","F040_AUTH_Acta_Fecha","F050_Nombre_Actualiza","F060_AUTH_Publica_Curso","F100_Codigo","F110_Nombre_Asignatura","F120_Tipo_Curso","F130_Asistencia","F140_Creditos","F150_Intensidad_HDD","F160_Intensidad_HDA","F170_Intensidad_TI","F180_Horas_Teoricas_Semanales","F183_Horas_Practicas_Semanales","F186_Horas_Teorico_Practicas_Semanales","F190_Horas_Teoricas_Semestrales","F193_Horas_Practicas_Semestrales","F196_Horas_TeoricoPracticas_Semestrales","F200_Semanas","F210_Teorico","F220_Practico","F230_Teorico_Practico","F240_Habilitable","F250_Validable","F260_Clasificable","F270_Facultad","F280_Instituto","F290_Programas_Academicos","F300_Area_Academica","F310_Campo_Formacion","F320_Ciclo","F330_Semestre","F330_Semestre_Plan","F335_Notas","F340_Horario_clase","F350_Requisitos","F360_Correquisitos","F370_Sede","F380_Profesores_Responsables","F390_Profesores_Oficinas","F400_Horario_atencion","F410_Profesores_Elaboran","F420_Correos_Electronicos","F430_Descripcion","F440_Proposito","F450_Justificacion","F460_Objetivo_General","F470_Objetivos_Especificos_Conceptuales","F480_Objetivos_Especificos_Procedimentales","F490_Objetivos_Especificos_Actitudinales","F500_Estrategia_Metodologica","F510_Evaluacion","F515_Evaluacion_Especifica","F520_Actividades_Obligatorias","F530_Contenido_Resumido","F540_Bibliografia_General","F600_Unidad1_Titulo","F601_Unidad1_Conceptual","F602_Unidad1_Procedimental","F603_Unidad1_Actitudinal","F604_Unidad1_Bibliografia","F605_Unidad1_Semanas","F610_Unidad2_Titulo","F611_Unidad2_Conceptual","F612_Unidad2_Procedimental","F613_Unidad2_Actitudinal","F614_Unidad2_Bibliografia","F615_Unidad2_Semanas","F620_Unidad3_Titulo","F621_Unidad3_Conceptual","F622_Unidad3_Procedimental","F623_Unidad3_Actitudinal","F624_Unidad3_Bibliografia","F625_Unidad3_Semanas","F630_Unidad4_Titulo","F631_Unidad4_Conceptual","F632_Unidad4_Procedimental","F633_Unidad4_Actitudinal","F634_Unidad4_Bibliografia","F635_Unidad4_Semanas","F640_Unidad5_Titulo","F641_Unidad5_Conceptual","F642_Unidad5_Procedimental","F643_Unidad5_Actitudinal","F644_Unidad5_Bibliografia","F645_Unidad5_Semanas","F650_Unidad6_Titulo","F651_Unidad6_Conceptual","F652_Unidad6_Procedimental","F653_Unidad6_Actitudinal","F654_Unidad6_Bibliografia","F655_Unidad6_Semanas","F660_Unidad7_Titulo","F661_Unidad7_Conceptual","F662_Unidad7_Procedimental","F663_Unidad7_Actitudinal","F664_Unidad7_Bibliografia","F665_Unidad7_Semanas","F670_Unidad8_Titulo","F671_Unidad8_Conceptual","F672_Unidad8_Procedimental","F673_Unidad8_Actitudinal","F674_Unidad8_Bibliografia","F675_Unidad8_Semanas","F680_Unidad9_Titulo","F681_Unidad9_Conceptual","F682_Unidad9_Procedimental","F683_Unidad9_Actitudinal","F684_Unidad9_Bibliografia","F685_Unidad9_Semanas","F690_Unidad10_Titulo","F691_Unidad10_Conceptual","F692_Unidad10_Procedimental","F693_Unidad10_Actitudinal","F694_Unidad10_Bibliografia","F695_Unidad10_Semanas",);

$MIC_DBASE=array(
    'F010_AUTO_Fecha_Actualizacion'=>array('query'=>'Fecha de actualización','type'=>'varchar(30)','default'=>'','values'=>'','help'=>'Fecha en la que se realiza la actualización. Este campo es automático','ejemplo'=>'Este campo es automático.'),
    
    'F015_AUTO_Usuario_Actualizacion'=>array('query'=>'Usuario que realiza la actualización','type'=>'varchar(30)','default'=>'','values'=>'','help'=>'Este es el usuario administrativo que esta realizando esta actualización. Este campo es automático.','ejemplo'=>'Este campo es automático.'),
    
    'F020_AUTH_Autorizacion_Vicedecano'=>array('query'=>'Autorización Vicedecano','type'=>'varchar(30)','default'=>'No','values'=>'Si,No','help'=>'Una vez el vicedecano autoriza el curso no puede ser editado por ningún otro usuario autorizado.  El curso solo puede volverse a editar cuando el vicedecano cambie este campo a No.','ejemplo'=>'Si'),
    
    'F025_AUTH_Version'=>array('query'=>'Última versión del curso','type'=>'varchar(3)','default'=>'','values'=>'','help'=>'Última versión del curso.','ejemplo'=>'1'),
    
    'F030_AUTH_Acta_Numero'=>array('query'=>'Número de Acta del Consejo de Facultad','type'=>'varchar(4)','default'=>'','values'=>'','help'=>'Número de acta en el que el curso fue aprobado.  Si el número de acta es 00 el curso nunca ha sido aprobado. Si es distinto pero el Vicedecano no lo ha aprobado esta acta corresponde a la versión anterior del curso.','ejemplo'=>'123'),
    
    'F040_AUTH_Acta_Fecha'=>array('query'=>'Fecha del Acta del Consejo de Facultad','type'=>'varchar(30)','default'=>'','values'=>'','help'=>'Fecha del acta del Consejo de Facultad. Si la fecha es MM/DD/CCYY el curso nunca ha sido aprobado.  Si es distinto pero el Vicedecano no lo ha aprobado esta acta corresponde a la versión anterior del curso.','ejemplo'=>'11/01/2014'),
    
    'F050_Nombre_Actualiza'=>array('query'=>'Nombre de quien modifica esta última versión','type'=>'varchar(30)','default'=>'','values'=>'','help'=>'Indique el nombre de quien esta modificando esta última versión del curso.','ejemplo'=>'Jorge I. Zuluaga'),
    
    'F060_AUTH_Publica_Curso'=>array('query'=>'Publica curso','type'=>'varchar(3)','default'=>'--','values'=>'--,Si,No','help'=>'Si coloca *Si* el curso será visible por usuarios no autorizados.','ejemplo'=>'No'),
    
    'F100_Codigo'=>array('query'=>'Codigo Curso','type'=>'varchar(7)','default'=>'0300000','values'=>'','help'=>'El código del curso tiene 6 dígitos: FFPPNNN, donde FF es la Facultad (03, FCEN), PP es el Programa (11, Astronomía), NNN es el número del curso','ejemplo'=>'0311150'),
    
    'F110_Nombre_Asignatura'=>array('query'=>'Nombre de la Asignatura','type'=>'varchar(100)','default'=>'Nombre Asignatura','values'=>'','help'=>'El nombre completo del curso debe coincidir con el que esta en el sistema Mares','ejemplo'=>'Introducción a la Informática'),
    
    'F120_Tipo_Curso'=>array('query'=>'Tipo de Curso','type'=>'varchar(20)','default'=>'--','values'=>'--,Básico,Profesional,Profundización','help'=>'Tipo de curso deacuerdo a su ubicación en el pensum.','ejemplo'=>'Básico'),
    
    'F130_Asistencia'=>array('query'=>'Tipo de Asistencia','type'=>'varchar(50)','default'=>'--','values'=>'--,Obligatoria,No obligatoria','help'=>'Indique el tipo de asistencia. El Comité de Curriculo define normalmente qué tipo de cursos son de asistencia obligatoria.','ejemplo'=>'Obligatoria'),
    
    'F140_Creditos'=>array('query'=>'Numero de Creditos','type'=>'varchar(3)','default'=>'--','values'=>'--,0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15','help'=>'Indique el número de créditos.  De acuerdo al 1295 cada crédito corresponde a 3 horas de trabajo en el curso.','ejemplo'=>'4'),
    
    'F150_Intensidad_HDD'=>array('query'=>'Horas de Docencia Directa (HDD)','type'=>'varchar(3)','default'=>'--','values'=>'--,0,16,32,48,64,80,96,112,128,144,160,176','help'=>'Indique el número de horas de docencia directa por semestre. Las horas de docencia directa son aquellas que realiza el profesor en actividades magistrales o presentación de contenidos. Normalmente equivalen al número de horas teóricas por semana multiplicado por 16. 0, 1:16, 2:32, 3:48, 4:64, 5:80, 6:96, 7:112, 8:128, 9:144, 10:160,11:176','ejemplo'=>'64'),
    
    'F160_Intensidad_HDA'=>array('query'=>'Horas de Docencia Asistida (HDA)','type'=>'varchar(3)','default'=>'--','values'=>'--,0,16,32,48,64,80,96,112,128,144,160,176','help'=>'Indique el número de horas de docencia asistida por semana. Las horas de docencias asistida son aquellas que se relacionan con actividades realizadas directamente por el estudiante pero con el acompañamiento presencial del profesor.  Este tipo de modalidad se utiliza especialmente en cursos prácticos o teórico prácticos.  Normalmente equivalen al número de horas prácticas o teórico-prácticas por semana (en los que las prácticas se hacen asistidas por el profesor) multiplicado por 16.  0, 1:16, 2:32, 3:48, 4:64, 5:80, 6:96, 7:112, 8:128, 9:144, 10:160,11:176','ejemplo'=>'32'),
    
    'F170_Intensidad_TI'=>array('query'=>'Horas de Trabajo Independiente (TI)','type'=>'varchar(3)','default'=>'--','values'=>'--,0,16,32,48,64,80,96,112,128,144,160,176','help'=>'Indique el número de horas de trabajo independiente por semana. Las horas de trabajo independientes son las que realiza el estudiante por fuera de clase.  El valor por semana se calcula multiplicando por 3 el número de crédios y restando al resultado el número de horas en las que el estudiante esta acompañado por el profesor (teóricas, prácticas o teórico prácticas.  Ejemplo: si un curso tiene 4 créditos (12 horas por semana totales) y 4 horas son en actividades en clase (acompañadas por el docente) entonces habrán 8 horas de trabajo independiente.  El número a reportar aquí debe ser el número de horas por semestre que es igual a lo que se obtuvo multiplicado por 16. 0, 1:16, 2:32, 3:48, 4:64, 5:80, 6:96, 7:112, 8:128, 9:144, 10:160,11:176','ejemplo'=>'32'),
    
    'F180_Horas_Teoricas_Semanales'=>array('query'=>'Horas teóricas semanales','type'=>'varchar(3)','default'=>'--','values'=>'--,0,1,2,3,4,5,6,7,8,9,10,11,12','help'=>'Indique el número de horas teóricas por semana.','ejemplo'=>'4'),
    
    'F183_Horas_Practicas_Semanales'=>array('query'=>'Horas Prácticas Semanales','type'=>'varchar(3)','default'=>'--','values'=>'--,0,1,2,3,4,5,6,7,8,9,10,11,12','help'=>'Indique el número de horas prácticas por semana.','ejemplo'=>'2'),
    
    'F186_Horas_Teorico_Practicas_Semanales'=>array('query'=>'Horas Teórico-Prácticas Semanales','type'=>'varchar(3)','default'=>'--','values'=>'--,0,1,2,3,4,5,6,7,8,9,10,11,12','help'=>'Indique el número de horas teórico-prácticas por semana.','ejemplo'=>'0'),
    
    'F190_Horas_Teoricas_Semestrales'=>array('query'=>'Horas teóricas semestrales','type'=>'varchar(3)','default'=>'--','values'=>'--,0,16,32,48,64,80,96,112,128,144,160,176','help'=>'Indique el número de horas teóricas por semestre.  0, 1:16, 2:32, 3:48, 4:64, 5:80, 6:96, 7:112, 8:128, 9:144, 10:160,11:176','ejemplo'=>'64'),
    
    'F193_Horas_Practicas_Semestrales'=>array('query'=>'Horas prácticas semestrales','type'=>'varchar(3)','default'=>'--','values'=>'--,0,16,32,48,64,80,96,112,128,144,160,176','help'=>'Indique el número de horas prácticas por semestre. 0, 1:16, 2:32, 3:48, 4:64, 5:80, 6:96, 7:112, 8:128, 9:144, 10:160,11:176','ejemplo'=>'0'),
    
    'F196_Horas_TeoricoPracticas_Semestrales'=>array('query'=>'Horas teórico-prácticas semestrales','type'=>'varchar(3)','default'=>'--','values'=>'--,0,16,32,48,64,80,96,112,128,144,160,176','help'=>'Indique el número de horas teórico-prácticas por semestre. 0, 1:16, 2:32, 3:48, 4:64, 5:80, 6:96, 7:112, 8:128, 9:144, 10:160,11:176','ejemplo'=>'0'),
    
    'F200_Semanas'=>array('query'=>'Número de semanas','type'=>'varchar(3)','default'=>'--','values'=>'--,16','help'=>'Número de semanas por semestre.','ejemplo'=>'16'),
    
    'F210_Teorico'=>array('query'=>'Curso teórico','type'=>'varchar(2)','default'=>'--','values'=>'--,Si,No','help'=>'Indique si es un curso teórico.','ejemplo'=>'Si'),
    
    'F220_Practico'=>array('query'=>'Curso práctico','type'=>'varchar(2)','default'=>'--','values'=>'--,Si,No','help'=>'Indique si es un curso práctico.','ejemplo'=>'No'),
    
    'F230_Teorico_Practico'=>array('query'=>'Curso teórico-práctico','type'=>'varchar(2)','default'=>'--','values'=>'--,Si,No','help'=>'Indique si es un curso teórico-práctico.','ejemplo'=>'No'),
    
    'F240_Habilitable'=>array('query'=>'Curso habilitable','type'=>'varchar(2)','default'=>'--','values'=>'--,Si,No','help'=>'Indique si es un curso habilitable. No aplica normalmente para cursos prácticos.','ejemplo'=>'No'),
    
    'F250_Validable'=>array('query'=>'Curso validable','type'=>'varchar(2)','default'=>'--','values'=>'--,Si,No','help'=>'Indique si es un curso validable.','ejemplo'=>'No'),
    
    'F260_Clasificable'=>array('query'=>'Curso clasificable','type'=>'varchar(2)','default'=>'--','values'=>'--,Si,No','help'=>'Indique si es un curso clasificable.  Aplica normalmente para cursos de primer semestre.','ejemplo'=>'No'),
    
    'F270_Facultad'=>array('query'=>'Facultad','type'=>'varchar(50)','default'=>'Facultad de Ciencias Exactas y Naturales','values'=>'','help'=>'Facultad','ejemplo'=>'Facultad de Ciencias Exactas y Naturales'),
    
    'F280_Instituto'=>array('query'=>'Instituto','type'=>'varchar(50)','default'=>'--','values'=>'--,Instituto de Física,Instituto de Química,Instituto de Biología,Instituto de Matemáticas,Facultad','help'=>'Instituto o Dependencia al que pertenece','ejemplo'=>'Instituto de Física'),
    
    'F290_Programas_Academicos'=>array('query'=>'Programas académicos a los que se ofrece','type'=>'varchar(80)','default'=>'','values'=>'','help'=>'Programas académicos a los que se ofrece','ejemplo'=>'Astronomía, Física'),
    
    'F300_Area_Academica'=>array('query'=>'Área académica','type'=>'varchar(50)','default'=>'--','values'=>'--,Astronomía,Biología,Química,Física,Matemáticas,Sociohumanística,Inglés,Ciencias,Computación','help'=>'Indique el área específica en la que se enmarca el curso.  El comité de currículo define un número límitado de áreas en la Facultad.','ejemplo'=>'Astronomía'),
    
    'F310_Campo_Formacion'=>array('query'=>'Campo de formación','type'=>'varchar(50)','default'=>'','values'=>'--Física--,Física Básica,Física Experimental,Física Teórica,Física Computacional,Física Matemática,Investigación,--Astronomía--,Astronomía Básica,Astronomía Práctica,Astrofísica y Comología,Didáctica','help'=>'Indique el área de formación dentro de la disciplina. Este campo va en el formato de la vicerrectoría de Docencia.','ejemplo'=>'Astronomía Práctica'),
    
    'F320_Ciclo'=>array('query'=>'Ciclo','type'=>'varchar(30)','default'=>'--','values'=>'--,Fundamentación,Profesionalización,Profundización','help'=>'Ciclo de formación de acuerdo al Documento Rector de la Transformación Curricular.','ejemplo'=>'Fundamentación'),
    
    'F330_Semestre'=>array('query'=>'Semestre actual','type'=>'varchar(10)','default'=>'','values'=>'','help'=>'Indique el último semestre en el que se ofrece el programa.','ejemplo'=>'2014-1'),
    
    'F330_Semestre_Plan'=>array('query'=>'Semestre en el Plan de Formación','type'=>'varchar(50)','default'=>'','values'=>'','help'=>'Indique el semestre en el plan de formación.  Si el curso se ofrece en varios programas y el semestre en cada uno de ellos es distinto use el nombre del programa en paréntesis para distinguirlo (ver ejemplo).  Si se trata de una electiva use 10','ejemplo'=>'1 (Física), 2 (Astronomía)'),
    
    'F335_Notas'=>array('query'=>'Notas','type'=>'text','default'=>'','values'=>'','help'=>'Notas para el curso','ejemplo'=>'Este curso es valido entre el semestre 2002-1 y 2014-1.'),
    
    'F340_Horario_clase'=>array('query'=>'Horario de clase','type'=>'varchar(20)','default'=>'','values'=>'','help'=>'Horario u horarios en los que se ofrece el curso en el último semestre.  Para múltiples horarios use ',', e.g. MJ12-14, L16-18','ejemplo'=>'L14-16, MJ8-10'),
    
    'F350_Requisitos'=>array('query'=>'Prerrequisitos','type'=>'varchar(100)','default'=>'','values'=>'','help'=>'Prerrequisitos del curso.  Indique el código de los prerrequisito de acuerdo a la última versión del pensum aprobada. Si no tiene prerrequisito use *(Ninguno)*. Si el curso tiene prerrequisitos específicos en otro programa ponga el nombre del programa entre paréntesis antes del prerrequisito (vea el ejemplo)','ejemplo'=>'0311101, 0311305, (Física) 0302133'),
    
    'F360_Correquisitos'=>array('query'=>'Correquisitos','type'=>'varchar(100)','default'=>'','values'=>'','help'=>'Correquisitos del curso.  Indique el código de los correquisito de acuerdo a la última versión del pensum aprobada. Si no tiene correquisito use *(Ninguno)*','ejemplo'=>'0311101, 0311305'),
    
    'F370_Sede'=>array('query'=>'Sede en el que se ofrece','type'=>'varchar(100)','default'=>'Ciudad Universitaria Medellín','values'=>'','help'=>'Indique las sedes de la Universidad en las que se ofrece el curso.','ejemplo'=>'Ciudad Universitaria Medellín y regiones donde se ofrece el programa'),
    
    'F380_Profesores_Responsables'=>array('query'=>'Profesores Responsables','type'=>'varchar(100)','default'=>'','values'=>'','help'=>'Indique el(los) profesor(es) que ofrecieron el curso en el último semestre.','ejemplo'=>'Jorge I. Zuluaga, Nelsón Vanegas'),
    
    'F390_Profesores_Oficinas'=>array('query'=>'Oficina de Profesores','type'=>'varchar(50)','default'=>'','values'=>'','help'=>'Indique las oficinas de los profesores que ofrecieron el curso en el último semestre.','ejemplo'=>'6-414, 6-212'),
    
    'F400_Horario_atencion'=>array('query'=>'Horario de atención de los profesores','type'=>'varchar(50)','default'=>'','values'=>'','help'=>'Indique el horario de atención de los profesores que ofrecieron el curso en el presente semestre.','ejemplo'=>'Jorge Zuluaga: MJ16-18, Nelsón Vanegas: MJ8-10'),
    
    'F410_Profesores_Elaboran'=>array('query'=>'Profesores que elaboran este plan de asignatura','type'=>'varchar(100)','default'=>'','values'=>'','help'=>'Indique el nombre de los profesores que contribuyeron con la elaboración de esta versión del plan de asignatura.','ejemplo'=>'Pablo Cuartas, Ignacio Ferrín'),
    
    'F420_Correos_Electronicos'=>array('query'=>'Correos electronicos de profesores que elaboran','type'=>'varchar(100)','default'=>'','values'=>'','help'=>'Lista de correos electrónicos de los profresores que elaboran esta versión del programa.','ejemplo'=>'pablo.cuartas@udea.edu.co, ignacio.ferrin@udea.edu.co'),
    
    'F430_Descripcion'=>array('query'=>'Descripción general del curso','type'=>'text','default'=>'','values'=>'','help'=>'Corresponde a una síntesis de los principales elementos que caracterizan la asignatura a la luz de los contenidos,  problemas y preguntas. Cuando se describe se da respuesta a: qué es, cómo es, cómo se comporta, que partes lo constituyen, para qué sirve, qué hace, cómo se define; en este caso, en el contexto del campo de la ciencia y/o disciplina.','ejemplo'=>'Este curso presenta algunas temáticas básicas de la informática requeridas específicamente para el trabajo científico o técnico.  El curso comienza con la descripción del funcionamiento del computador, las redes de computadores y el uso de la Internet con propósitos académicos y científicos (Internet Científica).  Se presenta una introducción general a por lo menos 2 lenguajes de programación (Python y C o C++) partiendo inicialmente desde el desarrollo de competencias algorítmicas y finalizando con la exploración de la sintaxis específica de cada lenguaje.  El curso también aborda la temática de la representación gráfica de los datos introduciendo para ello algunas herramientas de acceso libre (Matplotlib y Gnuplot).  Finalmente se introduce al estudiante en el uso del LaTeX como herramienta para la presentación de resultados científicos en la forma de reportes y artículos técnicos.  En síntesis, el curso hace un recorrido por los problemas y las herramientas utilizadas para la gestión completa de los datos científicos, incluyendo, su generación, procesamiento (programación), representación gráfica y presentación final en la forma, por ejemplos, de reportes y artículos.'),
    
    'F440_Proposito'=>array('query'=>'Propósito del curso es:','type'=>'text','default'=>'','values'=>'DEPRECATED','help'=>'Normalmente se puede usar para este campo el mismo que la Descripción.  También se puede dejar en blanco.','ejemplo'=>'Este curso presenta algunas temáticas básicas de la informática requeridas específicamente para el trabajo científico o técnico.  El curso comienza con la descripción del funcionamiento del computador, las redes de computadores y el uso de la Internet con propósitos académicos y científicos (Internet Científica).  Se presenta una introducción general a por lo menos 2 lenguajes de programación (Python y C o C++) partiendo inicialmente desde el desarrollo de competencias algorítmicas y finalizando con la exploración de la sintaxis específica de cada lenguaje.  El curso también aborda la temática de la representación gráfica de los datos introduciendo para ello algunas herramientas de acceso libre (Matplotlib y Gnuplot).  Finalmente se introduce al estudiante en el uso del LaTeX como herramienta para la presentación de resultados científicos en la forma de reportes y artículos técnicos.  En síntesis, el curso hace un recorrido por los problemas y las herramientas utilizadas para la gestión completa de los datos científicos, incluyendo, su generación, procesamiento (programación), representación gráfica y presentación final en la forma, por ejemplos, de reportes y artículos.'),
    
    'F450_Justificacion'=>array('query'=>'Justificación del curso','type'=>'text','default'=>'','values'=>'','help'=>'Debe incluir: (1) La pertinencia de la asignatura en el plan de formación en relación con: (a) El objetivo y los propósitos de formación del respectivo programa de pregrado de ciencias exactas y naturales, (b) La(s) relación(es) de formación entre el ciclo anterior y el posterior, (c) Los saberes y experiencias previas en las asignaturas ya cursadas y las que se desarrollan de modo paralelo.  (2) El aporte al desarrollo de las competencias genéricas y específicas propias de la formación del profesional en el respectivo programa de ciencias exactas y naturales: cognitivas, de comunicación y representación, así como   procedimentales y actitudinales. (3) La actualidad e importancia científica, cultural y social de las problemáticas específicas que serán tratadas  en la asignatura. (4) Las relaciones disciplinares e interdisciplinares con otras asignaturas del plan de formación del respectivo programa. (5) La proyección académica y social de los contenidos de la asignatura en relación con el desarrollo del individuo, de la sociedad y de la profesión.','ejemplo'=>'En el quehacer académico y científico los datos juegan un papel fundamental.  Su obtención, manipulación, almacenamiento, representación gráfica y presentación en forma de reportes, artículos, entre otros, constituyen tareas muy comunes de la actividad científica.  Para esta labor existen y se desarrollan constantemente herramientas computacionales que facilitan estas operaciones y que el científico en formación debe conocer y manipular adecuadamente. Entre estas herramientas se pueden enumerar los lenguajes de programación, las herramientas para la edición y manipulación de archivos o los paquetes y bibliotecas numéricas orientadas a la programación científica.

Manejar adecuadamente herramientas computacionales le permite al científico solucionar problemas mediante procesos automatizados, economizando tiempo e incrementando su capacidad para abordar problemas muy complejos. Las competencias informáticas le permiten además verificar modelos teóricos a través por ejemplo de simulaciones.  Los computadores, además, son herramientas fundamentales para la gestión de la información científica. El estudiante en formación debe conocer las posibilidades que le ofrece el computador, al igual que sus limitaciones.

La programación, en particular, es fundamental para el desarrollo del pensamiento analítico y algorítmico, habilidades imprescindibles para desarrollar otras competencias científicas tanto en el ámbito de la computación misma como en otros ámbitos específicos de la disciplina.

Muchas de las asignaturas del plan de estudios en los programas en los que se ofrece este curso (física y astronomía), requieren competencias importantes en el uso y programación de computadores.  Este es el caso por ejemplo de los cursos de naturaleza práctica tales como la física experimental (3 cursos) y la astronomía observacional (3 cursos).  En un mundo con problemas cada vez más complejos, incluso los cursos teóricos se están valiendo de la computación como herramienta didáctica y de investigación.  Así pues, la formación de los estudiantes en competencias computacionales desde el primer nivel de los programas en los que se ofrece, es condición fundamental para los retos académicos que enfrentarán en el resto de sus carreras.'),
    
    'F460_Objetivo_General'=>array('query'=>'Objetivo General','type'=>'text','default'=>'','values'=>'','help'=>'Se refiere a la concreción de las intenciones educativas en la asignatura según el ciclo de formación (fundamentación, profesionalización o profundización); se expresa en términos de las competencias que los estudiantes  deben desarrollar, lo cual implica proyectar los avances de aprendizaje esperados en los ámbitos conceptual, procedimental y actitudinal.
','ejemplo'=>'Adquirir competencias básicas en informática y programación de computadores, incluyendo el manejo de herramientas computacionales para la manipulación, procesamiento y representación de datos científicos y para su presentación en la forma de reportes, artículos entre otros.'),
    
    'F470_Objetivos_Especificos_Conceptuales'=>array('query'=>'Objetivos específicos conceptuales','type'=>'text','default'=>'','values'=>'','help'=>'Teorías, conceptos y leyes, representaciones de diversos tipos y  lenguaje científico entre otros.

Verbos generales: Analizar Formular Calcular Fundamentar Categorizar
Generar Comparar Identificar Compilar Inferir Concretar Mostrar
Contrastar Orientar Crear Oponer Definir Reconstruir Demostrar Relatar
Desarrollar Replicar Describir Reproducir Diagnosticar Revelar
Discriminar Planear Diseñar Presentar Efectuar Probar Enumerar
Producir Establecer Proponer Evaluar Situar Explicar Tasar Examinar
Trazar Exponer Valuar.

Verbos específicos: Advertir Enunciar Analizar Enumerar Basar
Especificar Calcular Estimar Calificar Examinar Categorizar Explicar
Comparar Fraccionar Componer Identificar Conceptuar Indicar Considerar
Interpretar Contrastar Justificar Deducir Mencionar Definir Mostrar
Demostrar Operacionalizar Detallar Organizar Determinar Registrar
Designar Relacionar Descomponer Resumir Descubrir Seleccionar
Discriminar Separar Distinguir Sintetizar Establecer Sugerir
','ejemplo'=>'Identificar y enumerar las componentes de hardware y software de un computador.
Describir las funciones de las componente del hardware de un computador.
Enumerar los más importantes sistemas operativos utilizados por computadores de escritorio.
Definir lo que es un protocolo de comunicación y enumerar algunos protocolos de comunicación básicos (IP, http, etc.)
Definir lo que es un lenguaje de programación interpretado y uno compilado.
Enumerar las diferencias, pros y contras de los lenguajes de programación interpretados y compilados.
'),
    
    'F480_Objetivos_Especificos_Procedimentales'=>array('query'=>'Objetivos específicos procedimentales','type'=>'text','default'=>'','values'=>'','help'=>'Metodologías experimentales, resolución de problemas, producción de textos referidos a informes, reseñas, resúmenes, comentarios, presentaciones y ensayos entre otras opciones.

Verbos específicos: Advertir Enunciar Analizar Enumerar Basar
Especificar Calcular Estimar Calificar Examinar Categorizar Explicar
Comparar Fraccionar Componer Identificar Conceptuar Indicar Considerar
Interpretar Contrastar Justificar Deducir Mencionar Definir Mostrar
Demostrar Operacionalizar Detallar Organizar Determinar Registrar
Designar Relacionar Descomponer Resumir Descubrir Seleccionar
Discriminar Separar Distinguir Sintetizar Establecer Sugerir
','ejemplo'=>'Reconocer la diferencia en prestaciones de distintas configuraciones de hardware y software en un computador.
Utilizar buscadores de Internet usando opciones no triviales.
Buscar literatura especializada usando herramientas de búsqueda propias de su disciplina (Google Scholar, ADS, inSpires, arXiv).
Instalar el sistema operativo Linux en un computador de escritorio.
Manipular archivos y directorios utilizando la línea de comandos de Linux.
Editar archivos de texto plano utilizando editores simples en el sistema operativo Linux.'),
    
    'F490_Objetivos_Especificos_Actitudinales'=>array('query'=>'Objetivos específicos actitudinales','type'=>'text','default'=>'','values'=>'','help'=>'Entusiasmo y pasión por el estudio y el conocimiento científico, cumplimiento de tareas y su reelaboración, responsabilidad por el aprendizaje, respeto por autores citados y los cánones de la publicación científica, entre otros.

Verbos específicos: Advertir Enunciar Analizar Enumerar Basar
Especificar Calcular Estimar Calificar Examinar Categorizar Explicar
Comparar Fraccionar Componer Identificar Conceptuar Indicar Considerar
Interpretar Contrastar Justificar Deducir Mencionar Definir Mostrar
Demostrar Operacionalizar Detallar Organizar Determinar Registrar
Designar Relacionar Descomponer Resumir Descubrir Seleccionar
Discriminar Separar Distinguir Sintetizar Establecer Sugerir
','ejemplo'=>'Reconocer la computación como un área fundamental en la formación del científico y demostrar compromiso para conocer y asimilar nuevas herramientas.
Describir la importancia de la representación gráfica de los datos para el trabajo científico.
Valorar el trabajo realizado por desarrolladores de software e ingenieros en la creación de herramientas que facilitan el trabajo científico.'),
    
    'F500_Estrategia_Metodologica'=>array('query'=>'Estrategia metodológica','type'=>'text','default'=>'','values'=>'','help'=>'Se plantea aquí la o las modalidades de trabajo académico desde las cuales se enseñan los contenidos, los procedimientos y las actitudes, mediante: seminarios, talleres, cátedra magistral, aprendizaje basado en problemas (ABP) u otras que se consideren convenientes. Igualmente, se hace mención a la disponibilidad de la asignatura en formato virtual y a la posibilidad de trabajo a partir de ambientes virtuales.

Se trata de la explicitación del camino por el cual el estudiante logra integrar los contenidos y desarrollar sus competencias en relación con los saberes  que son objeto de enseñanza por parte del profesor. Se refiere a los métodos, al cómo propiciar de manera adecuada el aprendizaje de los conceptos, los procedimientos y las actitudes, y por lo tanto, requiere de la reflexión acerca de la construcción de las ciencias; es decir, se fundamenta en la comprensión de la historiay de la epistemología de los respectivos campos científicos. 

El aspecto metodológico hace referencia a las múltiples formas (técnicas y procedimientos) en las cuales, en un lugar y tiempo determinados,  se relacionan los integrantes del grupo. Al respecto, es importante tener en cuenta que este aspecto permite la concreción de los procesos creativos de cada profesor y, por lo tanto, sólo se describen algunas sugerencias, las cuales serán enriquecidas en los casos y contextos particulares.

Es importante tener en cuenta la definición y enunciación de las actividades según el sistema de créditos vigente que rige para todas las asignaturas, para lo cual demanda la elaboración del cronograma que exprese en el:

•Ciclo de fundamentación: actividades presenciales, de acompañamiento directo y/o indirecto y de trabajo independiente.
•Ciclo de profesionalización: actividades presenciales y de trabajo independiente.
•Ciclo de profundización: actividades presenciales y de trabajo independiente con mayor autonomía del estudiante.
','ejemplo'=>'Este curso es de naturaleza teórico-práctica.  Por la misma razón se requiere la participación activa de los estudiantes en todas las actividades de clase.  

Para conseguir este objetivo se sugiere utilizar las siguientes estrategias metodológicas:

Para la presentación de los contenidos teóricos se recomienda restringirse a exposiciones cortas que involucren ejercicios rápidos de parte de los estudiantes.  Los ejercicios pueden incluir la solución a preguntas abiertas, la búsqueda de material en Internet o la solución a pequeños problemas.

Para las sesiones de carácter práctico con acompañamiento directo del Profesor se sugiere involucrar siempre a los estudiantes en el proceso.  Para ello se puede hacer pasar a un estudiante al computador del profesor, resolver partes del problema práctico y realizar una revisión permanente del proceso de solución.

La evaluación de carácter formativo es fundamental en el curso.  Para ello es importante promover la participación de los estudiantes en la solución de preguntas o la realización de encuestas sencillas sobre el avance del proceso en clase.'),
    
    'F510_Evaluacion'=>array('query'=>'Evaluacion General','type'=>'text','default'=>'','values'=>'','help'=>'Según la profesora Salinas en conferencia presentada a la facultad de Ciencias Exactas y Naturales en el 2010, la evaluación, en su sentido general, está articulada a una valoración de los procesos formativos consignados en el Documento Rector de la Facultad para todos los programas de pregrado. Desde allí se brinda el marco de referencia sobre el cual descansan las políticas de evaluación. Para el caso de cada uno de las asignaturas, se espera que se expliciten los criterios que orientarán la evaluación en su sentido integral y las pautas desde las cuales se llevará a cabo el seguimiento y promoción de los estudiantes. Ello supone aclarar y mencionar cuáles son los procedimientos para la calificación, la agenda para la presentación de pruebas, las condiciones para la presentación de sustentaciones y exposiciones orales, documentos y trabajos escritos, la distribución de porcentajes relacionados con las notas parciales u otros aspectos que se consideren pertinentes; todo ello en concordancia con el Reglamento Estudiantil.

La evaluación, como proceso inherente e inseparable  de la enseñanza y del aprendizaje, cumple una función formativa, en la búsqueda de un permanente mejoramiento y, una función social, que implica la certificación académica del logro de los objetivos, el desarrollo de competencias, la adquisición de conocimientos y la incorporación de actitudes y valores.

Las estrategias de participación evaluativa son:

•La autoevaluación, implica que el estudiante se examine y reconozca los logros, dificultades y participación en el proceso, entre otros. Para ello, el estudiante se confronta con el programa de la asignatura, y en conversaciones con el profesor y sus compañeros.
•La heteroevaluación, son los criterios del profesor en términos de lo aprendido y los aspectos que falten por mejorar, depurar o realizar.
•La coevaluación, se refiere a la valoración de los aprendizajes en forma colectiva, apoyada en los criterios del área, del profesor y de los compañeros de la asignatura.

La evaluación requiere de la implementación de procesos de autorregulación de los aprendizajes tanto en actividades presenciales, de acompañamiento y en aquellas que no exigen presencialidad.

Es importante describir las formas y los instrumentos de evaluación que el plan de asignatura va a privilegiar. En este aspecto, es necesario definir con claridad las reglas que rigen el proceso en términos, por ejemplo, de asignación de porcentajes, tiempo y fechas entre otros.

Es importante  construir con los estudiantes estrategias para la valoración del curso en términos de sus fortalezas y debilidades.
','ejemplo'=>'Dada la naturaleza e intensidad del curso se sugieren los siguientes mecanismos evaluativos:

Evaluación formativa permanente durante las actividades de docencia directa.  Esta evaluación se puede realizar con ejercicios cortos durante las presentaciones teóricas o mediante controles de avance durante las actividades prácticas orientadas por el profesor.

Al menos una evaluación sumativa semanal.  De nuevo, por la naturaleza del curso, es necesario garantizar la puesta en práctica de las competencias enseñadas dentro y fuera del aula de clase.  Para ello se sugiere realizar una evaluación corta semanal que evidencie claramente el desarrollo de las competencias.  Para su corrección se sugiere usar las modalidades de auto o coevaluación que contribuyan además a hacer participe a los mismos estudiantes del proceso evaluativo.

Adicionalmente y por lo menos en dos oportunidades durante el desarrollo del curso, se sugiere realizar evaluaciones sumativas más complejas.  Estas evaluaciones tendrán como propósito evaluar el desarrollo de las competencias en el mediano plazo.'),
    
    'F515_Evaluacion_Especifica'=>array('query'=>'Actividades de Evaluación Específicas','type'=>'text','default'=>'','values'=>'','help'=>'Detalle aquí la lista de actividades de evaluación específicas indicando, nombre de la actividad, porcentaje total que cada actividad representa en el total de la evaluación y fechas específicas de las actividades de evaluación.','ejemplo'=>'Evaluación semanal, 70%, 1 vez cada semana
Evaluación de Competencia Final, 30%, Semana de evaluaciones finales'),
    
    'F520_Actividades_Obligatorias'=>array('query'=>'Actividades de asistencia obligatoria','type'=>'text','default'=>'','values'=>'','help'=>'','ejemplo'=>'Dada la naturaleza permanente de la evaluación formativa y sumativa en este curso además de su carácter práctico, todas las actividades del curso son de asistencia obligatoria.'),
    
    'F530_Contenido_Resumido'=>array('query'=>'Contenido Resumido','type'=>'text','default'=>'','values'=>'DEPRECATED','help'=>'Indique el contenido resumido.  Si deja en blanco el título de las unidades indicadas abajo será usado para construir este campo en el formato de salida.','ejemplo'=>''),
    
    'F540_Bibliografia_General'=>array('query'=>'Bibliografía General del Curso','type'=>'text','default'=>'','values'=>'','help'=>'Tenga en cuenta:
•Que sea suficiente, pertinente, actualizada y en los casos propicios acudir a los textos científicos clásicos.
•Que incluya textos básicos y de referencia para ampliar y profundizar las problemáticas tratadas, para superar el solo estudio con notas de clase.
•Que se apoye con textos impresos y de formato digital. 
•Que se recurra a fuentes primarias, especialmente a textos producidos por los profesores, los artículos de revistas especializadas y a informes de investigación.
•Que se incluyan textos en otro idioma.

Nota: La autonomía intelectual implica que los estudiantes puedan acceder a información actualizada, comprenderla y utilizarla según sus necesidades formativas. Por ello cada docente de la Universidad debe conocer las revistas especializadas de su área y apoyarse en la Biblioteca Central para que les colabore a los estudiantes con el uso adecuado de las bases de datos y las búsquedas avanzadas en internet.
','ejemplo'=>'Arquitectura de Computadores. P. Quiroga. Alfaomega. 2010.
Computación Básica para Adultos, 2da Ed. C. Veloso. Marcombo S.A. 2010.
Artículos de Wikipedia sobre los dispositivos de Hardware del Computador.
'),
    
    'F600_Unidad1_Titulo'=>array('query'=>'Título de la Unidad 1','type'=>'varchar(50)','default'=>'','values'=>'','help'=>'Título de la Unidad.  Use un título abreviado e informativo.','ejemplo'=>'El Computador'),
    
    'F601_Unidad1_Conceptual'=>array('query'=>'Unidad 1 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos conceptuales específicos de la unidad.  Teorías, conceptos y leyes, representaciones de diversos tipos y  lenguaje científico entre otros','ejemplo'=>'Breve historia de la computación
Descripción general del computador
Componentes básicas de hardware
Configuraciones de hardware'),
    
    'F602_Unidad1_Procedimental'=>array('query'=>'Unidad 1 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos procedimentales específicos de la unidad.  Tienen que ver con metodologías experimentales, resolución de problemas, producción de textos referidos a informes, reseñas, resúmenes, comentarios, presentaciones y ensayos entre otras opciones.','ejemplo'=>'Instalación de un sistema operativo en una máquina virtual.
Instalación del sistema operativo Linux.
Navegación en el sistema de archivos de Linux.
Manipulación de archivos usando la línea de comandos de Linux.'),
    
    'F603_Unidad1_Actitudinal'=>array('query'=>'Unidad 1 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos actitudinales específicos de la unidad. Tienen que ver con entusiasmo y pasión por el estudio y el conocimiento científico, cumplimiento de tareas y su reelaboración, responsabilidad por el aprendizaje, respeto por autores citados y los cánones de la publicación científica, entre otros.','ejemplo'=>'Pensamiento analítico y algorítmico como competencias fundamentales para el trabajo científico.
La importancia de la elaboración completa de algoritmos previo a implementación como programas de computadora.
La importancia de las pruebas en el desarrollo de algoritmos.'),
    
    'F604_Unidad1_Bibliografia'=>array('query'=>'Unidad 1 - Bibliografia Específica','type'=>'text','default'=>'','values'=>'','help'=>'Bibliografía específica de la Unidad.  Si es la misma que la general deje en blanco.','ejemplo'=>'Arquitectura de Computadores. P. Quiroga. Alfaomega. 2010.
Computación Básica para Adultos, 2da Ed. C. Veloso. Marcombo S.A. 2010.
Artículos de Wikipedia sobre los dispositivos de Hardware del Computador.
'),
    
    'F605_Unidad1_Semanas'=>array('query'=>'Semanas para la Unidad 1','type'=>'varchar(3)','default'=>'','values'=>'','help'=>'Semanas requeridas para el desarrollo de la unidad incluyendo todas las posibles actividades evaluativas.','ejemplo'=>'3'),
    
    'F610_Unidad2_Titulo'=>array('query'=>'Título de la Unidad 2','type'=>'varchar(50)','default'=>'','values'=>'','help'=>'Título de la Unidad.  Use un título abreviado e informativo.','ejemplo'=>'El Computador'),
    
    'F611_Unidad2_Conceptual'=>array('query'=>'Unidad 2 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos conceptuales específicos de la unidad.  Teorías, conceptos y leyes, representaciones de diversos tipos y  lenguaje científico entre otros','ejemplo'=>'Breve historia de la computación
Descripción general del computador
Componentes básicas de hardware
Configuraciones de hardware'),
    
    'F612_Unidad2_Procedimental'=>array('query'=>'Unidad 2 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos procedimentales específicos de la unidad.  Tienen que ver con metodologías experimentales, resolución de problemas, producción de textos referidos a informes, reseñas, resúmenes, comentarios, presentaciones y ensayos entre otras opciones.','ejemplo'=>'Instalación de un sistema operativo en una máquina virtual.
Instalación del sistema operativo Linux.
Navegación en el sistema de archivos de Linux.
Manipulación de archivos usando la línea de comandos de Linux.'),
    
    'F613_Unidad2_Actitudinal'=>array('query'=>'Unidad 2 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos actitudinales específicos de la unidad. Tienen que ver con entusiasmo y pasión por el estudio y el conocimiento científico, cumplimiento de tareas y su reelaboración, responsabilidad por el aprendizaje, respeto por autores citados y los cánones de la publicación científica, entre otros.','ejemplo'=>'Pensamiento analítico y algorítmico como competencias fundamentales para el trabajo científico.
La importancia de la elaboración completa de algoritmos previo a implementación como programas de computadora.
La importancia de las pruebas en el desarrollo de algoritmos.'),
    
    'F614_Unidad2_Bibliografia'=>array('query'=>'Unidad 2 - Bibliografia Específica','type'=>'text','default'=>'','values'=>'','help'=>'Bibliografía específica de la Unidad.  Si es la misma que la general deje en blanco.','ejemplo'=>'Arquitectura de Computadores. P. Quiroga. Alfaomega. 2010.
Computación Básica para Adultos, 2da Ed. C. Veloso. Marcombo S.A. 2010.
Artículos de Wikipedia sobre los dispositivos de Hardware del Computador.
'),
    
    'F615_Unidad2_Semanas'=>array('query'=>'Semanas para la Unidad 2','type'=>'varchar(3)','default'=>'','values'=>'','help'=>'Semanas requeridas para el desarrollo de la unidad incluyendo todas las posibles actividades evaluativas.','ejemplo'=>'3'),
    
    'F620_Unidad3_Titulo'=>array('query'=>'Título de la Unidad 3','type'=>'varchar(50)','default'=>'','values'=>'','help'=>'Título de la Unidad.  Use un título abreviado e informativo.','ejemplo'=>'El Computador'),
    
    'F621_Unidad3_Conceptual'=>array('query'=>'Unidad 3 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos conceptuales específicos de la unidad.  Teorías, conceptos y leyes, representaciones de diversos tipos y  lenguaje científico entre otros','ejemplo'=>'Breve historia de la computación
Descripción general del computador
Componentes básicas de hardware
Configuraciones de hardware'),
    
    'F622_Unidad3_Procedimental'=>array('query'=>'Unidad 3 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos procedimentales específicos de la unidad.  Tienen que ver con metodologías experimentales, resolución de problemas, producción de textos referidos a informes, reseñas, resúmenes, comentarios, presentaciones y ensayos entre otras opciones.','ejemplo'=>'Instalación de un sistema operativo en una máquina virtual.
Instalación del sistema operativo Linux.
Navegación en el sistema de archivos de Linux.
Manipulación de archivos usando la línea de comandos de Linux.'),
    
    'F623_Unidad3_Actitudinal'=>array('query'=>'Unidad 3 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos actitudinales específicos de la unidad. Tienen que ver con entusiasmo y pasión por el estudio y el conocimiento científico, cumplimiento de tareas y su reelaboración, responsabilidad por el aprendizaje, respeto por autores citados y los cánones de la publicación científica, entre otros.','ejemplo'=>'Pensamiento analítico y algorítmico como competencias fundamentales para el trabajo científico.
La importancia de la elaboración completa de algoritmos previo a implementación como programas de computadora.
La importancia de las pruebas en el desarrollo de algoritmos.'),
    
    'F624_Unidad3_Bibliografia'=>array('query'=>'Unidad 3 - Bibliografia Específica','type'=>'text','default'=>'','values'=>'','help'=>'Bibliografía específica de la Unidad.  Si es la misma que la general deje en blanco.','ejemplo'=>'Arquitectura de Computadores. P. Quiroga. Alfaomega. 2010.
Computación Básica para Adultos, 2da Ed. C. Veloso. Marcombo S.A. 2010.
Artículos de Wikipedia sobre los dispositivos de Hardware del Computador.
'),
    
    'F625_Unidad3_Semanas'=>array('query'=>'Semanas para la Unidad 3','type'=>'varchar(3)','default'=>'','values'=>'','help'=>'Semanas requeridas para el desarrollo de la unidad incluyendo todas las posibles actividades evaluativas.','ejemplo'=>'3'),
    
    'F630_Unidad4_Titulo'=>array('query'=>'Título de la Unidad 4','type'=>'varchar(50)','default'=>'','values'=>'','help'=>'Título de la Unidad.  Use un título abreviado e informativo.','ejemplo'=>'El Computador'),
    
    'F631_Unidad4_Conceptual'=>array('query'=>'Unidad 4 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos conceptuales específicos de la unidad.  Teorías, conceptos y leyes, representaciones de diversos tipos y  lenguaje científico entre otros','ejemplo'=>'Breve historia de la computación
Descripción general del computador
Componentes básicas de hardware
Configuraciones de hardware'),
    
    'F632_Unidad4_Procedimental'=>array('query'=>'Unidad 4 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos procedimentales específicos de la unidad.  Tienen que ver con metodologías experimentales, resolución de problemas, producción de textos referidos a informes, reseñas, resúmenes, comentarios, presentaciones y ensayos entre otras opciones.','ejemplo'=>'Instalación de un sistema operativo en una máquina virtual.
Instalación del sistema operativo Linux.
Navegación en el sistema de archivos de Linux.
Manipulación de archivos usando la línea de comandos de Linux.'),
    
    'F633_Unidad4_Actitudinal'=>array('query'=>'Unidad 4 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos actitudinales específicos de la unidad. Tienen que ver con entusiasmo y pasión por el estudio y el conocimiento científico, cumplimiento de tareas y su reelaboración, responsabilidad por el aprendizaje, respeto por autores citados y los cánones de la publicación científica, entre otros.','ejemplo'=>'Pensamiento analítico y algorítmico como competencias fundamentales para el trabajo científico.
La importancia de la elaboración completa de algoritmos previo a implementación como programas de computadora.
La importancia de las pruebas en el desarrollo de algoritmos.'),
    
    'F634_Unidad4_Bibliografia'=>array('query'=>'Unidad 4 - Bibliografia Específica','type'=>'text','default'=>'','values'=>'','help'=>'Bibliografía específica de la Unidad.  Si es la misma que la general deje en blanco.','ejemplo'=>'Arquitectura de Computadores. P. Quiroga. Alfaomega. 2010.
Computación Básica para Adultos, 2da Ed. C. Veloso. Marcombo S.A. 2010.
Artículos de Wikipedia sobre los dispositivos de Hardware del Computador.
'),
    
    'F635_Unidad4_Semanas'=>array('query'=>'Semanas para la Unidad 4','type'=>'varchar(3)','default'=>'','values'=>'','help'=>'Semanas requeridas para el desarrollo de la unidad incluyendo todas las posibles actividades evaluativas.','ejemplo'=>'3'),
    
    'F640_Unidad5_Titulo'=>array('query'=>'Título de la Unidad 5','type'=>'varchar(50)','default'=>'','values'=>'','help'=>'Título de la Unidad.  Use un título abreviado e informativo.','ejemplo'=>'El Computador'),
    
    'F641_Unidad5_Conceptual'=>array('query'=>'Unidad 5 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos conceptuales específicos de la unidad.  Teorías, conceptos y leyes, representaciones de diversos tipos y  lenguaje científico entre otros','ejemplo'=>'Breve historia de la computación
Descripción general del computador
Componentes básicas de hardware
Configuraciones de hardware'),
    
    'F642_Unidad5_Procedimental'=>array('query'=>'Unidad 5 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos procedimentales específicos de la unidad.  Tienen que ver con metodologías experimentales, resolución de problemas, producción de textos referidos a informes, reseñas, resúmenes, comentarios, presentaciones y ensayos entre otras opciones.','ejemplo'=>'Instalación de un sistema operativo en una máquina virtual.
Instalación del sistema operativo Linux.
Navegación en el sistema de archivos de Linux.
Manipulación de archivos usando la línea de comandos de Linux.'),
    
    'F643_Unidad5_Actitudinal'=>array('query'=>'Unidad 5 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos actitudinales específicos de la unidad. Tienen que ver con entusiasmo y pasión por el estudio y el conocimiento científico, cumplimiento de tareas y su reelaboración, responsabilidad por el aprendizaje, respeto por autores citados y los cánones de la publicación científica, entre otros.','ejemplo'=>'Pensamiento analítico y algorítmico como competencias fundamentales para el trabajo científico.
La importancia de la elaboración completa de algoritmos previo a implementación como programas de computadora.
La importancia de las pruebas en el desarrollo de algoritmos.'),
    
    'F644_Unidad5_Bibliografia'=>array('query'=>'Unidad 5 - Bibliografia Específica','type'=>'text','default'=>'','values'=>'','help'=>'Bibliografía específica de la Unidad.  Si es la misma que la general deje en blanco.','ejemplo'=>'Arquitectura de Computadores. P. Quiroga. Alfaomega. 2010.
Computación Básica para Adultos, 2da Ed. C. Veloso. Marcombo S.A. 2010.
Artículos de Wikipedia sobre los dispositivos de Hardware del Computador.
'),
    
    'F645_Unidad5_Semanas'=>array('query'=>'Semanas para la Unidad 5','type'=>'varchar(3)','default'=>'','values'=>'','help'=>'Semanas requeridas para el desarrollo de la unidad incluyendo todas las posibles actividades evaluativas.','ejemplo'=>'3'),
    
    'F650_Unidad6_Titulo'=>array('query'=>'Título de la Unidad 6','type'=>'varchar(50)','default'=>'','values'=>'','help'=>'Título de la Unidad.  Use un título abreviado e informativo.','ejemplo'=>'El Computador'),
    
    'F651_Unidad6_Conceptual'=>array('query'=>'Unidad 6 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos conceptuales específicos de la unidad.  Teorías, conceptos y leyes, representaciones de diversos tipos y  lenguaje científico entre otros','ejemplo'=>'Breve historia de la computación
Descripción general del computador
Componentes básicas de hardware
Configuraciones de hardware'),
    
    'F652_Unidad6_Procedimental'=>array('query'=>'Unidad 6 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos procedimentales específicos de la unidad.  Tienen que ver con metodologías experimentales, resolución de problemas, producción de textos referidos a informes, reseñas, resúmenes, comentarios, presentaciones y ensayos entre otras opciones.','ejemplo'=>'Instalación de un sistema operativo en una máquina virtual.
Instalación del sistema operativo Linux.
Navegación en el sistema de archivos de Linux.
Manipulación de archivos usando la línea de comandos de Linux.'),
    
    'F653_Unidad6_Actitudinal'=>array('query'=>'Unidad 6 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos actitudinales específicos de la unidad. Tienen que ver con entusiasmo y pasión por el estudio y el conocimiento científico, cumplimiento de tareas y su reelaboración, responsabilidad por el aprendizaje, respeto por autores citados y los cánones de la publicación científica, entre otros.','ejemplo'=>'Pensamiento analítico y algorítmico como competencias fundamentales para el trabajo científico.
La importancia de la elaboración completa de algoritmos previo a implementación como programas de computadora.
La importancia de las pruebas en el desarrollo de algoritmos.'),
    
    'F654_Unidad6_Bibliografia'=>array('query'=>'Unidad 6 - Bibliografia Específica','type'=>'text','default'=>'','values'=>'','help'=>'Bibliografía específica de la Unidad.  Si es la misma que la general deje en blanco.','ejemplo'=>'Arquitectura de Computadores. P. Quiroga. Alfaomega. 2010.
Computación Básica para Adultos, 2da Ed. C. Veloso. Marcombo S.A. 2010.
Artículos de Wikipedia sobre los dispositivos de Hardware del Computador.
'),
    
    'F655_Unidad6_Semanas'=>array('query'=>'Semanas para la Unidad 6','type'=>'varchar(3)','default'=>'','values'=>'','help'=>'Semanas requeridas para el desarrollo de la unidad incluyendo todas las posibles actividades evaluativas.','ejemplo'=>'3'),
    
    'F660_Unidad7_Titulo'=>array('query'=>'Título de la Unidad 7','type'=>'varchar(50)','default'=>'','values'=>'','help'=>'Título de la Unidad.  Use un título abreviado e informativo.','ejemplo'=>'El Computador'),
    
    'F661_Unidad7_Conceptual'=>array('query'=>'Unidad 7 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos conceptuales específicos de la unidad.  Teorías, conceptos y leyes, representaciones de diversos tipos y  lenguaje científico entre otros','ejemplo'=>'Breve historia de la computación
Descripción general del computador
Componentes básicas de hardware
Configuraciones de hardware'),
    
    'F662_Unidad7_Procedimental'=>array('query'=>'Unidad 7 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos procedimentales específicos de la unidad.  Tienen que ver con metodologías experimentales, resolución de problemas, producción de textos referidos a informes, reseñas, resúmenes, comentarios, presentaciones y ensayos entre otras opciones.','ejemplo'=>'Instalación de un sistema operativo en una máquina virtual.
Instalación del sistema operativo Linux.
Navegación en el sistema de archivos de Linux.
Manipulación de archivos usando la línea de comandos de Linux.'),
    
    'F663_Unidad7_Actitudinal'=>array('query'=>'Unidad 7 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos actitudinales específicos de la unidad. Tienen que ver con entusiasmo y pasión por el estudio y el conocimiento científico, cumplimiento de tareas y su reelaboración, responsabilidad por el aprendizaje, respeto por autores citados y los cánones de la publicación científica, entre otros.','ejemplo'=>'Pensamiento analítico y algorítmico como competencias fundamentales para el trabajo científico.
La importancia de la elaboración completa de algoritmos previo a implementación como programas de computadora.
La importancia de las pruebas en el desarrollo de algoritmos.'),
    
    'F664_Unidad7_Bibliografia'=>array('query'=>'Unidad 7 - Bibliografia Específica','type'=>'text','default'=>'','values'=>'','help'=>'Bibliografía específica de la Unidad.  Si es la misma que la general deje en blanco.','ejemplo'=>'Arquitectura de Computadores. P. Quiroga. Alfaomega. 2010.
Computación Básica para Adultos, 2da Ed. C. Veloso. Marcombo S.A. 2010.
Artículos de Wikipedia sobre los dispositivos de Hardware del Computador.
'),
    
    'F665_Unidad7_Semanas'=>array('query'=>'Semanas para la Unidad 7','type'=>'varchar(3)','default'=>'','values'=>'','help'=>'Semanas requeridas para el desarrollo de la unidad incluyendo todas las posibles actividades evaluativas.','ejemplo'=>'3'),
    
    'F670_Unidad8_Titulo'=>array('query'=>'Título de la Unidad 8','type'=>'varchar(50)','default'=>'','values'=>'','help'=>'Título de la Unidad.  Use un título abreviado e informativo.','ejemplo'=>'El Computador'),
    
    'F671_Unidad8_Conceptual'=>array('query'=>'Unidad 8 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos conceptuales específicos de la unidad.  Teorías, conceptos y leyes, representaciones de diversos tipos y  lenguaje científico entre otros','ejemplo'=>'Breve historia de la computación
Descripción general del computador
Componentes básicas de hardware
Configuraciones de hardware'),
    
    'F672_Unidad8_Procedimental'=>array('query'=>'Unidad 8 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos procedimentales específicos de la unidad.  Tienen que ver con metodologías experimentales, resolución de problemas, producción de textos referidos a informes, reseñas, resúmenes, comentarios, presentaciones y ensayos entre otras opciones.','ejemplo'=>'Instalación de un sistema operativo en una máquina virtual.
Instalación del sistema operativo Linux.
Navegación en el sistema de archivos de Linux.
Manipulación de archivos usando la línea de comandos de Linux.'),
    
    'F673_Unidad8_Actitudinal'=>array('query'=>'Unidad 8 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos actitudinales específicos de la unidad. Tienen que ver con entusiasmo y pasión por el estudio y el conocimiento científico, cumplimiento de tareas y su reelaboración, responsabilidad por el aprendizaje, respeto por autores citados y los cánones de la publicación científica, entre otros.','ejemplo'=>'Pensamiento analítico y algorítmico como competencias fundamentales para el trabajo científico.
La importancia de la elaboración completa de algoritmos previo a implementación como programas de computadora.
La importancia de las pruebas en el desarrollo de algoritmos.'),
    
    'F674_Unidad8_Bibliografia'=>array('query'=>'Unidad 8 - Bibliografia Específica','type'=>'text','default'=>'','values'=>'','help'=>'Bibliografía específica de la Unidad.  Si es la misma que la general deje en blanco.','ejemplo'=>'Arquitectura de Computadores. P. Quiroga. Alfaomega. 2010.
Computación Básica para Adultos, 2da Ed. C. Veloso. Marcombo S.A. 2010.
Artículos de Wikipedia sobre los dispositivos de Hardware del Computador.
'),
    
    'F675_Unidad8_Semanas'=>array('query'=>'Semanas para la Unidad 8','type'=>'varchar(3)','default'=>'','values'=>'','help'=>'Semanas requeridas para el desarrollo de la unidad incluyendo todas las posibles actividades evaluativas.','ejemplo'=>'3'),
    
    'F680_Unidad9_Titulo'=>array('query'=>'Título de la Unidad 9','type'=>'varchar(50)','default'=>'','values'=>'','help'=>'Título de la Unidad.  Use un título abreviado e informativo.','ejemplo'=>'El Computador'),
    
    'F681_Unidad9_Conceptual'=>array('query'=>'Unidad 9 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos conceptuales específicos de la unidad.  Teorías, conceptos y leyes, representaciones de diversos tipos y  lenguaje científico entre otros','ejemplo'=>'Breve historia de la computación
Descripción general del computador
Componentes básicas de hardware
Configuraciones de hardware'),
    
    'F682_Unidad9_Procedimental'=>array('query'=>'Unidad 9 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos procedimentales específicos de la unidad.  Tienen que ver con metodologías experimentales, resolución de problemas, producción de textos referidos a informes, reseñas, resúmenes, comentarios, presentaciones y ensayos entre otras opciones.','ejemplo'=>'Instalación de un sistema operativo en una máquina virtual.
Instalación del sistema operativo Linux.
Navegación en el sistema de archivos de Linux.
Manipulación de archivos usando la línea de comandos de Linux.'),
    
    'F683_Unidad9_Actitudinal'=>array('query'=>'Unidad 9 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos actitudinales específicos de la unidad. Tienen que ver con entusiasmo y pasión por el estudio y el conocimiento científico, cumplimiento de tareas y su reelaboración, responsabilidad por el aprendizaje, respeto por autores citados y los cánones de la publicación científica, entre otros.','ejemplo'=>'Pensamiento analítico y algorítmico como competencias fundamentales para el trabajo científico.
La importancia de la elaboración completa de algoritmos previo a implementación como programas de computadora.
La importancia de las pruebas en el desarrollo de algoritmos.'),
    
    'F684_Unidad9_Bibliografia'=>array('query'=>'Unidad 9 - Bibliografia Específica','type'=>'text','default'=>'','values'=>'','help'=>'Bibliografía específica de la Unidad.  Si es la misma que la general deje en blanco.','ejemplo'=>'Arquitectura de Computadores. P. Quiroga. Alfaomega. 2010.
Computación Básica para Adultos, 2da Ed. C. Veloso. Marcombo S.A. 2010.
Artículos de Wikipedia sobre los dispositivos de Hardware del Computador.
'),
    
    'F685_Unidad9_Semanas'=>array('query'=>'Semanas para la Unidad 9','type'=>'varchar(3)','default'=>'','values'=>'','help'=>'Semanas requeridas para el desarrollo de la unidad incluyendo todas las posibles actividades evaluativas.','ejemplo'=>'3'),
    
    'F690_Unidad10_Titulo'=>array('query'=>'Título de la Unidad 10','type'=>'varchar(50)','default'=>'','values'=>'','help'=>'Título de la Unidad.  Use un título abreviado e informativo.','ejemplo'=>'El Computador'),
    
    'F691_Unidad10_Conceptual'=>array('query'=>'Unidad 10 - Contenidos Conceptuales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos conceptuales específicos de la unidad.  Teorías, conceptos y leyes, representaciones de diversos tipos y  lenguaje científico entre otros','ejemplo'=>'Breve historia de la computación
Descripción general del computador
Componentes básicas de hardware
Configuraciones de hardware'),
    
    'F692_Unidad10_Procedimental'=>array('query'=>'Unidad 10 - Contenidos Procedimentales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos procedimentales específicos de la unidad.  Tienen que ver con metodologías experimentales, resolución de problemas, producción de textos referidos a informes, reseñas, resúmenes, comentarios, presentaciones y ensayos entre otras opciones.','ejemplo'=>'Instalación de un sistema operativo en una máquina virtual.
Instalación del sistema operativo Linux.
Navegación en el sistema de archivos de Linux.
Manipulación de archivos usando la línea de comandos de Linux.'),
    
    'F693_Unidad10_Actitudinal'=>array('query'=>'Unidad 10 - Contenidos Actitudinales','type'=>'text','default'=>'','values'=>'','help'=>'Contenidos actitudinales específicos de la unidad. Tienen que ver con entusiasmo y pasión por el estudio y el conocimiento científico, cumplimiento de tareas y su reelaboración, responsabilidad por el aprendizaje, respeto por autores citados y los cánones de la publicación científica, entre otros.','ejemplo'=>'Pensamiento analítico y algorítmico como competencias fundamentales para el trabajo científico.
La importancia de la elaboración completa de algoritmos previo a implementación como programas de computadora.
La importancia de las pruebas en el desarrollo de algoritmos.'),
    
    'F694_Unidad10_Bibliografia'=>array('query'=>'Unidad 10 - Bibliografia Específica','type'=>'text','default'=>'','values'=>'','help'=>'Bibliografía específica de la Unidad.  Si es la misma que la general deje en blanco.','ejemplo'=>'Arquitectura de Computadores. P. Quiroga. Alfaomega. 2010.
Computación Básica para Adultos, 2da Ed. C. Veloso. Marcombo S.A. 2010.
Artículos de Wikipedia sobre los dispositivos de Hardware del Computador.
'),
    
    'F695_Unidad10_Semanas'=>array('query'=>'Semanas para la Unidad 10','type'=>'varchar(3)','default'=>'','values'=>'','help'=>'Semanas requeridas para el desarrollo de la unidad incluyendo todas las posibles actividades evaluativas.','ejemplo'=>'3'),
    );
?>