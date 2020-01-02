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
        "name": "cus"
      }
    ]
  },
  "exec": {
    "steps": {
      "name": "api1",
      "module": "api",
      "action": "send",
      "options": {
        "headers": {
          "authorization": "Bearer sk_test_jJ14InI56ViQUovM7Tr9IAZP006LpDq1R6"
        },
        "url": "https://api.stripe.com/v1/customers/{{$_GET.cus}}",
        "schema": []
      },
      "output": true,
      "meta": [
        {
          "type": "array",
          "name": "data",
          "sub": [
            {
              "type": "text",
              "name": "id"
            },
            {
              "type": "text",
              "name": "object"
            },
            {
              "type": "text",
              "name": "address"
            },
            {
              "type": "number",
              "name": "balance"
            },
            {
              "type": "number",
              "name": "created"
            },
            {
              "type": "text",
              "name": "currency"
            },
            {
              "type": "text",
              "name": "default_source"
            },
            {
              "type": "boolean",
              "name": "delinquent"
            },
            {
              "type": "text",
              "name": "description"
            },
            {
              "type": "text",
              "name": "discount"
            },
            {
              "type": "text",
              "name": "email"
            },
            {
              "type": "text",
              "name": "invoice_prefix"
            },
            {
              "type": "object",
              "name": "invoice_settings",
              "sub": [
                {
                  "type": "text",
                  "name": "custom_fields"
                },
                {
                  "type": "text",
                  "name": "default_payment_method"
                },
                {
                  "type": "text",
                  "name": "footer"
                }
              ]
            },
            {
              "type": "boolean",
              "name": "livemode"
            },
            {
              "type": "object",
              "name": "metadata"
            },
            {
              "type": "text",
              "name": "name"
            },
            {
              "type": "text",
              "name": "phone"
            },
            {
              "type": "array",
              "name": "preferred_locales"
            },
            {
              "type": "text",
              "name": "shipping"
            },
            {
              "type": "object",
              "name": "sources",
              "sub": [
                {
                  "type": "text",
                  "name": "object"
                },
                {
                  "type": "array",
                  "name": "data"
                },
                {
                  "type": "boolean",
                  "name": "has_more"
                },
                {
                  "type": "number",
                  "name": "total_count"
                },
                {
                  "type": "text",
                  "name": "url"
                }
              ]
            },
            {
              "type": "object",
              "name": "subscriptions",
              "sub": [
                {
                  "type": "text",
                  "name": "object"
                },
                {
                  "type": "array",
                  "name": "data"
                },
                {
                  "type": "boolean",
                  "name": "has_more"
                },
                {
                  "type": "number",
                  "name": "total_count"
                },
                {
                  "type": "text",
                  "name": "url"
                }
              ]
            },
            {
              "type": "text",
              "name": "tax_exempt"
            },
            {
              "type": "object",
              "name": "tax_ids",
              "sub": [
                {
                  "type": "text",
                  "name": "object"
                },
                {
                  "type": "array",
                  "name": "data"
                },
                {
                  "type": "boolean",
                  "name": "has_more"
                },
                {
                  "type": "number",
                  "name": "total_count"
                },
                {
                  "type": "text",
                  "name": "url"
                }
              ]
            },
            {
              "type": "text",
              "name": "tax_info"
            },
            {
              "type": "text",
              "name": "tax_info_verification"
            }
          ]
        },
        {
          "type": "object",
          "name": "headers",
          "sub": [
            {
              "type": "text",
              "name": "date"
            },
            {
              "type": "text",
              "name": "server"
            },
            {
              "type": "text",
              "name": "stripe-version"
            },
            {
              "type": "text",
              "name": "access-control-allow-origin"
            },
            {
              "type": "text",
              "name": "access-control-max-age"
            },
            {
              "type": "text",
              "name": "access-control-allow-methods"
            },
            {
              "type": "text",
              "name": "content-type"
            },
            {
              "type": "text",
              "name": "status"
            },
            {
              "type": "text",
              "name": "access-control-expose-headers"
            },
            {
              "type": "text",
              "name": "cache-control"
            },
            {
              "type": "text",
              "name": "access-control-allow-credentials"
            },
            {
              "type": "text",
              "name": "strict-transport-security"
            },
            {
              "type": "text",
              "name": "request-id"
            },
            {
              "type": "text",
              "name": "content-length"
            }
          ]
        }
      ],
      "outputType": "object"
    }
  }
}
JSON
);
?>