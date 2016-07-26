create user 'comaca'@'localhost' identified by 'ComAca';
create database ComAca;
grant all privileges on ComAca.* to 'comaca'@'localhost';
flush privileges;
