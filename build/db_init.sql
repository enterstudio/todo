-- create database
CREATE DATABASE todo COLLATE 'utf8mb4_general_ci';

-- application db user
CREATE USER 'todo_admin'@'localhost' IDENTIFIED BY 'todo_admin_pass';

-- application db user privileges
GRANT ALL PRIVILEGES ON todo.* TO 'todo_admin'@'localhost' WITH GRANT OPTION;

-- flush
FLUSH PRIVILEGES;
