-- create database
CREATE DATABASE todo COLLATE 'utf8mb4_general_ci';

-- application db user
CREATE USER 'todo_admin'@'localhost' IDENTIFIED BY PASSWORD '*538FE471351B4B8AEB7955E34A4AD980DEFF452D';

-- application db user privileges
GRANT ALL PRIVILEGES ON todo_.* TO 'todo_admin'@'localhost' WITH GRANT OPTION;

-- flush
FLUSH PRIVILEGES;
