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
        "name": "queryMaster",
        "module": "dbconnector",
        "action": "select",
        "options": {
          "connection": "db",
          "sql": {
            "type": "SELECT",
            "columns": [
              {
                "table": "cars",
                "column": "id"
              },
              {
                "table": "cars",
                "column": "make"
              }
            ],
            "table": {
              "name": "cars"
            },
            "joins": [],
            "wheres": null,
            "query": "SELECT id, make\nFROM cars",
            "params": []
          }
        },
        "output": true,
        "meta": [
          {
            "name": "id",
            "type": "number"
          },
          {
            "name": "make",
            "type": "text"
          }
        ],
        "outputType": "array"
      }
    ]
  }
}
JSON
);
?>