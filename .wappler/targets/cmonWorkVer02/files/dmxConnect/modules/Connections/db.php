<?php
// Database Type : "MySQL"
// Database Adapter : "mysql"
$exports = <<<'JSON'
{
    "name": "db",
    "module": "dbconnector",
    "action": "connect",
    "options": {
        "server": "mysql",
        "connectionString": "mysql:host=db;sslverify=false;port=3306;dbname=cmon_work_01;user=db_user;password=0utD00Rs2;charset=utf8"
    }
}
JSON;
?>