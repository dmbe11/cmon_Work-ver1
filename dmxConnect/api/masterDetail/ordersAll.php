<?php
require('../../../dmxConnectLib/dmxConnect.php');


$app = new \lib\App();

$app->define(<<<'JSON'
{
  "settings": {
    "options": {}
  },
  "meta": {
    "options": {},
    "$_GET": [
      {
        "type": "text",
        "name": "sort"
      },
      {
        "type": "text",
        "name": "dir"
      }
    ]
  },
  "exec": {
    "steps": [
      "Connections/db",
      {
        "name": "query1",
        "module": "dbconnector",
        "action": "select",
        "options": {
          "connection": "db"
        },
        "output": true,
        "meta": [],
        "outputType": "array"
      }
    ]
  }
}
JSON
);
?>