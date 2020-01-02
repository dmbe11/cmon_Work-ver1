<?php
require('../../dmxConnectLib/dmxConnect.php');


$app = new \lib\App();

$app->define(<<<'JSON'
{
  "settings": {
    "options": {}
  },
  "meta": {
    "options": {
      "linkedFile": "/newCustomer.html",
      "linkedForm": "form1"
    },
    "$_POST": [
      {
        "type": "text",
        "name": "Email"
      },
      {
        "type": "text",
        "name": "Name"
      },
      {
        "type": "text",
        "name": "Password"
      },
      {
        "type": "text",
        "name": "StripeID"
      },
      {
        "type": "number",
        "name": "CustomersID"
      }
    ]
  },
  "exec": {}
}
JSON
);
?>