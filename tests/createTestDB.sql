CREATE DATABASE wb_db_tests;
CREATE USER 'wb_db_tester'@'localhost' IDENTIFIED BY 'mysql_is_evil';
GRANT ALL PRIVILEGES ON wb_db_tests.* TO 'wb_db_tester'@'localhost';