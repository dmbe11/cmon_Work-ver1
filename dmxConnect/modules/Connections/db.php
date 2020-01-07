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
        "connectionString": "mysql:host=db;sslverify=false;port=3306;dbname=cmon_work_01;user=db_user;password=3BS0pXAX;charset=utf8",
        "limit" : 1000,
        "debug" : false,
        "meta"  : {"allTables":["cars","countries","images","users"],"allViews":[],"tables":{"cars":{"columns":{"id":{"type":"int","primary":true},"make":{"type":"varchar","size":50,"nullable":true},"model":{"type":"varchar","size":50,"nullable":true},"year":{"type":"varchar","size":50,"nullable":true}}}}}
    }
}
JSON;
?>