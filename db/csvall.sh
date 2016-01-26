# RESET COURSES TABLE
mysql -u root --password="pum" Sinfin -e "truncate table Cursos"

# INSERT COURSES
cmd="python csv2sql.py"
$cmd planes/astronomia_2.csv 211-2-m1
$cmd planes/fisica_4.csv 210-4-m1
$cmd planes/fisica_5.csv 210-5-m1
$cmd planes/biologia_10.csv 204-10-m1
$cmd planes/biologia_9.csv 204-9-m1
$cmd planes/quimica_5.csv 216-5-m1
$cmd planes/quimica_6.csv 216-6-m1
$cmd planes/tecquimica_5.csv 222-5-m1
$cmd planes/tecquimica_6.csv 222-6-m1
$cmd planes/matematicas_3.csv 213-3-m1
