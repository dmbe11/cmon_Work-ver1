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
      },
      {
        "type": "text",
        "name": "filter"
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
              },
              {
                "table": "cars",
                "column": "model"
              },
              {
                "table": "cars",
                "column": "year"
              }
            ],
            "table": {
              "name": "cars"
            },
            "joins": [],
            "wheres": {
              "condition": "AND",
              "rules": [
                {
                  "id": "cars.id",
                  "field": "cars.id",
                  "type": "double",
                  "operator": "equal",
                  "value": "{{$_GET.filter}}",
                  "data": {
                    "table": "cars",
                    "column": "id",
                    "type": "number"
                  },
                  "operation": "="
                }
              ],
              "conditional": null,
              "valid": true
            },
            "query": "SELECT id, make, model, year\nFROM cars\nWHERE id = :P1 /* {{$_GET.filter}} */",
            "params": [
              {
                "operator": "equal",
                "type": "expression",
                "name": ":P1",
                "value": "{{$_GET.filter}}"
              }
            ]
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
          },
          {
            "name": "model",
            "type": "text"
          },
          {
            "name": "year",
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