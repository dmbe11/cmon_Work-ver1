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
    ],
    "$_POST": [
      {
        "type": "text",
        "name": "stripeToken"
      },
      {
        "type": "text",
        "name": "productid"
      },
      {
        "type": "text",
        "name": "quantity"
      },
      {
        "type": "number",
        "name": "data_amount"
      },
      {
        "type": "number",
        "name": "data_amount_refunded"
      },
      {
        "type": "text",
        "name": "data_application"
      },
      {
        "type": "text",
        "name": "data_application_fee"
      },
      {
        "type": "text",
        "name": "data_application_fee_amount"
      },
      {
        "type": "text",
        "name": "data_balance_transaction"
      },
      {
        "type": "number",
        "name": "data_captured"
      },
      {
        "type": "text",
        "name": "data_created"
      },
      {
        "type": "text",
        "name": "data_currency"
      },
      {
        "type": "text",
        "name": "data_customer"
      },
      {
        "type": "text",
        "name": "data_description"
      },
      {
        "type": "text",
        "name": "data_destination"
      },
      {
        "type": "text",
        "name": "data_dispute"
      },
      {
        "type": "text",
        "name": "data_failure_code"
      },
      {
        "type": "text",
        "name": "data_failure_message"
      },
      {
        "type": "text",
        "name": "data_fraud_details"
      },
      {
        "type": "text",
        "name": "data_invoice"
      },
      {
        "type": "number",
        "name": "data_livemode"
      },
      {
        "type": "text",
        "name": "data_metadata_orderid"
      },
      {
        "type": "text",
        "name": "data_object"
      },
      {
        "type": "text",
        "name": "data_on_behalf_of"
      },
      {
        "type": "text",
        "name": "data_order"
      },
      {
        "type": "text",
        "name": "outcome_network_status"
      },
      {
        "type": "text",
        "name": "outcome_reason"
      },
      {
        "type": "text",
        "name": "outcome_risk_level"
      },
      {
        "type": "number",
        "name": "outcome_risk_score"
      },
      {
        "type": "text",
        "name": "outcome_seller_message"
      },
      {
        "type": "text",
        "name": "outcome_type"
      },
      {
        "type": "number",
        "name": "paid"
      },
      {
        "type": "text",
        "name": "payment_intent"
      },
      {
        "type": "text",
        "name": "receipt_email"
      },
      {
        "type": "text",
        "name": "receipt_number"
      },
      {
        "type": "text",
        "name": "receipt_url"
      },
      {
        "type": "number",
        "name": "refunded"
      },
      {
        "type": "text",
        "name": "refunds_data"
      },
      {
        "type": "number",
        "name": "refunds_has_more"
      },
      {
        "type": "text",
        "name": "refunds_object"
      },
      {
        "type": "text",
        "name": "refunds_total_count"
      },
      {
        "type": "text",
        "name": "refunds_url"
      },
      {
        "type": "text",
        "name": "review"
      },
      {
        "type": "text",
        "name": "shipping"
      },
      {
        "type": "text",
        "name": "source_address_city"
      },
      {
        "type": "text",
        "name": "source_address_line1"
      },
      {
        "type": "text",
        "name": "source_address_line1_check"
      },
      {
        "type": "text",
        "name": "source_address_state"
      },
      {
        "type": "text",
        "name": "source_address_zip"
      },
      {
        "type": "text",
        "name": "source_address_zip_check"
      },
      {
        "type": "text",
        "name": "source_brand"
      },
      {
        "type": "text",
        "name": "source_country"
      },
      {
        "type": "text",
        "name": "source_customer"
      },
      {
        "type": "text",
        "name": "source_cvc_check"
      },
      {
        "type": "text",
        "name": "source_dynamic_last4"
      },
      {
        "type": "text",
        "name": "source_id"
      },
      {
        "type": "text",
        "name": "source_line2"
      },
      {
        "type": "text",
        "name": "source_metadata"
      },
      {
        "type": "text",
        "name": "source_name"
      },
      {
        "type": "text",
        "name": "source_object"
      },
      {
        "type": "text",
        "name": "source_tokeniization_method"
      },
      {
        "type": "text",
        "name": "source_transfer"
      },
      {
        "type": "text",
        "name": "statement_descriptor"
      },
      {
        "type": "text",
        "name": "status"
      },
      {
        "type": "text",
        "name": "transfer_data"
      },
      {
        "type": "text",
        "name": "transfer_group"
      }
    ]
  },
  "exec": {
    "steps": [
      {
        "name": "",
        "options": {
          "comment": "Make server connection"
        }
      },
      "Connections/db",
      {
        "name": "",
        "options": {
          "comment": "inirtialise variables"
        }
      },
      {
        "name": "itemprice",
        "module": "core",
        "action": "setvalue",
        "options": {
          "value": 0
        },
        "outputType": "text"
      },
      {
        "name": "description",
        "module": "core",
        "action": "setvalue",
        "options": {
          "value": "undefined"
        },
        "outputType": "text"
      },
      {
        "name": "get_price",
        "module": "dbconnector",
        "action": "select",
        "options": {
          "connection": "db",
          "sql": {
            "type": "SELECT",
            "columns": [
              {
                "table": "products",
                "column": "Cost"
              },
              {
                "table": "products",
                "column": "ItemName"
              }
            ],
            "table": {
              "name": "products"
            },
            "joins": [],
            "orders": [],
            "wheres": {
              "condition": "AND",
              "rules": [
                {
                  "id": "products.ItemID",
                  "field": "products.ItemID",
                  "type": "double",
                  "operator": "equal",
                  "value": "{{$_POST.productid}}",
                  "data": {
                    "table": "products",
                    "column": "ItemID",
                    "type": "number"
                  },
                  "operation": "="
                }
              ],
              "conditional": null,
              "valid": true
            },
            "query": "SELECT Cost, ItemName\nFROM products\nWHERE ItemID = :P1 /* {{$_POST.productid}} */",
            "params": [
              {
                "operator": "equal",
                "type": "expression",
                "name": ":P1",
                "value": "{{$_POST.productid}}"
              }
            ]
          }
        },
        "output": true,
        "meta": [
          {
            "name": "Cost",
            "type": "number"
          },
          {
            "name": "ItemName",
            "type": "text"
          }
        ],
        "outputType": "array"
      },
      {
        "name": "repeat1",
        "module": "core",
        "action": "repeat",
        "options": {
          "repeat": "{{get_price}}",
          "exec": {
            "steps": [
              {
                "name": "itemprice",
                "module": "core",
                "action": "setvalue",
                "options": {
                  "value": "{{Cost}}"
                },
                "outputType": "text"
              },
              {
                "name": "description",
                "module": "core",
                "action": "setvalue",
                "options": {
                  "value": "{{ItemName}}"
                },
                "outputType": "text"
              }
            ]
          }
        },
        "meta": [
          {
            "name": "$index",
            "type": "number"
          },
          {
            "name": "$number",
            "type": "number"
          },
          {
            "name": "$name",
            "type": "text"
          },
          {
            "name": "$value",
            "type": "object"
          },
          {
            "name": "Cost",
            "type": "number"
          },
          {
            "name": "ItemName",
            "type": "text"
          }
        ],
        "outputType": "array",
        "output": true
      },
      {
        "name": "",
        "options": {
          "comment": "setup api"
        }
      },
      {
        "name": "api1",
        "module": "api",
        "action": "send",
        "options": {
          "url": "https://api.stripe.com/v1/charges",
          "method": "POST",
          "params": {
            "currency": "usd",
            "description": "{{description}}",
            "source": "{{$_POST.stripeToken}}",
            "amount": "{{itemprice}}"
          },
          "headers": {
            "authorization": "Bearer sk_test_jJ14InI56ViQUovM7Tr9IAZP006LpDq1R6"
          }
        },
        "output": true
      },
      {
        "name": "insert_transaction",
        "module": "dbupdater",
        "action": "insert",
        "options": {
          "connection": "db",
          "sql": {
            "type": "insert",
            "values": [
              {
                "table": "stripe_payment",
                "column": "data_amount",
                "type": "number",
                "value": "{{$_POST.data_amount}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_amount_refunded",
                "type": "number",
                "value": "{{$_POST.data_amount_refunded}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_application",
                "type": "text",
                "value": "{{$_POST.data_application}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_application_fee",
                "type": "text",
                "value": "{{$_POST.data_application_fee}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_application_fee_amount",
                "type": "text",
                "value": "{{$_POST.data_application_fee_amount}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_balance_transaction",
                "type": "text",
                "value": "{{$_POST.data_balance_transaction}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_captured",
                "type": "number",
                "value": "{{$_POST.data_captured}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_created",
                "type": "text",
                "value": "{{$_POST.data_created}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_currency",
                "type": "text",
                "value": "{{$_POST.data_currency}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_customer",
                "type": "text",
                "value": "{{$_POST.data_customer}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_description",
                "type": "text",
                "value": "{{$_POST.data_description}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_destination",
                "type": "text",
                "value": "{{$_POST.data_destination}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_dispute",
                "type": "text",
                "value": "{{$_POST.data_dispute}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_failure_code",
                "type": "text",
                "value": "{{$_POST.data_failure_code}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_failure_message",
                "type": "text",
                "value": "{{$_POST.data_failure_message}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_fraud_details",
                "type": "text",
                "value": "{{$_POST.data_fraud_details}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_invoice",
                "type": "text",
                "value": "{{$_POST.data_invoice}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_livemode",
                "type": "number",
                "value": "{{$_POST.data_livemode}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_metadata_orderid",
                "type": "text",
                "value": "{{$_POST.data_metadata_orderid}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_object",
                "type": "text",
                "value": "{{$_POST.data_object}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_on_behalf_of",
                "type": "text",
                "value": "{{$_POST.data_on_behalf_of}}"
              },
              {
                "table": "stripe_payment",
                "column": "data_order",
                "type": "text",
                "value": "{{$_POST.data_order}}"
              },
              {
                "table": "stripe_payment",
                "column": "outcome_network_status",
                "type": "text",
                "value": "{{$_POST.outcome_network_status}}"
              },
              {
                "table": "stripe_payment",
                "column": "outcome_reason",
                "type": "text",
                "value": "{{$_POST.outcome_reason}}"
              },
              {
                "table": "stripe_payment",
                "column": "outcome_risk_level",
                "type": "text",
                "value": "{{$_POST.outcome_risk_level}}"
              },
              {
                "table": "stripe_payment",
                "column": "outcome_risk_score",
                "type": "number",
                "value": "{{$_POST.outcome_risk_score}}"
              },
              {
                "table": "stripe_payment",
                "column": "outcome_seller_message",
                "type": "text",
                "value": "{{$_POST.outcome_seller_message}}"
              },
              {
                "table": "stripe_payment",
                "column": "outcome_type",
                "type": "text",
                "value": "{{$_POST.outcome_type}}"
              },
              {
                "table": "stripe_payment",
                "column": "paid",
                "type": "number",
                "value": "{{$_POST.paid}}"
              },
              {
                "table": "stripe_payment",
                "column": "payment_intent",
                "type": "text",
                "value": "{{$_POST.payment_intent}}"
              },
              {
                "table": "stripe_payment",
                "column": "receipt_email",
                "type": "text",
                "value": "{{$_POST.receipt_email}}"
              },
              {
                "table": "stripe_payment",
                "column": "receipt_number",
                "type": "text",
                "value": "{{$_POST.receipt_number}}"
              },
              {
                "table": "stripe_payment",
                "column": "receipt_url",
                "type": "text",
                "value": "{{$_POST.receipt_url}}"
              },
              {
                "table": "stripe_payment",
                "column": "refunded",
                "type": "number",
                "value": "{{$_POST.refunded}}"
              },
              {
                "table": "stripe_payment",
                "column": "refunds_data",
                "type": "text",
                "value": "{{$_POST.refunds_data}}"
              },
              {
                "table": "stripe_payment",
                "column": "refunds_has_more",
                "type": "number",
                "value": "{{$_POST.refunds_has_more}}"
              },
              {
                "table": "stripe_payment",
                "column": "refunds_object",
                "type": "text",
                "value": "{{$_POST.refunds_object}}"
              },
              {
                "table": "stripe_payment",
                "column": "refunds_total_count",
                "type": "text",
                "value": "{{$_POST.refunds_total_count}}"
              },
              {
                "table": "stripe_payment",
                "column": "refunds_url",
                "type": "text",
                "value": "{{$_POST.refunds_url}}"
              },
              {
                "table": "stripe_payment",
                "column": "review",
                "type": "text",
                "value": "{{$_POST.review}}"
              },
              {
                "table": "stripe_payment",
                "column": "shipping",
                "type": "text",
                "value": "{{$_POST.shipping}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_address_city",
                "type": "text",
                "value": "{{$_POST.source_address_city}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_address_line1",
                "type": "text",
                "value": "{{$_POST.source_address_line1}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_address_line1_check",
                "type": "text",
                "value": "{{$_POST.source_address_line1_check}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_address_state",
                "type": "text",
                "value": "{{$_POST.source_address_state}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_address_zip",
                "type": "text",
                "value": "{{$_POST.source_address_zip}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_address_zip_check",
                "type": "text",
                "value": "{{$_POST.source_address_zip_check}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_brand",
                "type": "text",
                "value": "{{$_POST.source_brand}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_country",
                "type": "text",
                "value": "{{$_POST.source_country}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_customer",
                "type": "text",
                "value": "{{$_POST.source_customer}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_cvc_check",
                "type": "text",
                "value": "{{$_POST.source_cvc_check}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_dynamic_last4",
                "type": "text",
                "value": "{{$_POST.source_dynamic_last4}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_id",
                "type": "text",
                "value": "{{$_POST.source_id}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_line2",
                "type": "text",
                "value": "{{$_POST.source_line2}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_metadata",
                "type": "text",
                "value": "{{$_POST.source_metadata}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_name",
                "type": "text",
                "value": "{{$_POST.source_name}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_object",
                "type": "text",
                "value": "{{$_POST.source_object}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_tokeniization_method",
                "type": "text",
                "value": "{{$_POST.source_tokeniization_method}}"
              },
              {
                "table": "stripe_payment",
                "column": "source_transfer",
                "type": "text",
                "value": "{{$_POST.source_transfer}}"
              },
              {
                "table": "stripe_payment",
                "column": "statement_descriptor",
                "type": "text",
                "value": "{{$_POST.statement_descriptor}}"
              },
              {
                "table": "stripe_payment",
                "column": "status",
                "type": "text",
                "value": "{{$_POST.status}}"
              },
              {
                "table": "stripe_payment",
                "column": "transfer_data",
                "type": "text",
                "value": "{{$_POST.transfer_data}}"
              },
              {
                "table": "stripe_payment",
                "column": "transfer_group",
                "type": "text",
                "value": "{{$_POST.transfer_group}}"
              }
            ],
            "table": "stripe_payment",
            "query": "INSERT INTO stripe_payment\n(data_amount, data_amount_refunded, data_application, data_application_fee, data_application_fee_amount, data_balance_transaction, data_captured, data_created, data_currency, data_customer, data_description, data_destination, data_dispute, data_failure_code, data_failure_message, data_fraud_details, data_invoice, data_livemode, data_metadata_orderid, data_object, data_on_behalf_of, data_order, outcome_network_status, outcome_reason, outcome_risk_level, outcome_risk_score, outcome_seller_message, outcome_type, paid, payment_intent, receipt_email, receipt_number, receipt_url, refunded, refunds_data, refunds_has_more, refunds_object, refunds_total_count, refunds_url, review, shipping, source_address_city, source_address_line1, source_address_line1_check, source_address_state, source_address_zip, source_address_zip_check, source_brand, source_country, source_customer, source_cvc_check, source_dynamic_last4, source_id, source_line2, source_metadata, source_name, source_object, source_tokeniization_method, source_transfer, statement_descriptor, status, transfer_data, transfer_group) VALUES (:P1 /* {{$_POST.data_amount}} */, :P2 /* {{$_POST.data_amount_refunded}} */, :P3 /* {{$_POST.data_application}} */, :P4 /* {{$_POST.data_application_fee}} */, :P5 /* {{$_POST.data_application_fee_amount}} */, :P6 /* {{$_POST.data_balance_transaction}} */, :P7 /* {{$_POST.data_captured}} */, :P8 /* {{$_POST.data_created}} */, :P9 /* {{$_POST.data_currency}} */, :P10 /* {{$_POST.data_customer}} */, :P11 /* {{$_POST.data_description}} */, :P12 /* {{$_POST.data_destination}} */, :P13 /* {{$_POST.data_dispute}} */, :P14 /* {{$_POST.data_failure_code}} */, :P15 /* {{$_POST.data_failure_message}} */, :P16 /* {{$_POST.data_fraud_details}} */, :P17 /* {{$_POST.data_invoice}} */, :P18 /* {{$_POST.data_livemode}} */, :P19 /* {{$_POST.data_metadata_orderid}} */, :P20 /* {{$_POST.data_object}} */, :P21 /* {{$_POST.data_on_behalf_of}} */, :P22 /* {{$_POST.data_order}} */, :P23 /* {{$_POST.outcome_network_status}} */, :P24 /* {{$_POST.outcome_reason}} */, :P25 /* {{$_POST.outcome_risk_level}} */, :P26 /* {{$_POST.outcome_risk_score}} */, :P27 /* {{$_POST.outcome_seller_message}} */, :P28 /* {{$_POST.outcome_type}} */, :P29 /* {{$_POST.paid}} */, :P30 /* {{$_POST.payment_intent}} */, :P31 /* {{$_POST.receipt_email}} */, :P32 /* {{$_POST.receipt_number}} */, :P33 /* {{$_POST.receipt_url}} */, :P34 /* {{$_POST.refunded}} */, :P35 /* {{$_POST.refunds_data}} */, :P36 /* {{$_POST.refunds_has_more}} */, :P37 /* {{$_POST.refunds_object}} */, :P38 /* {{$_POST.refunds_total_count}} */, :P39 /* {{$_POST.refunds_url}} */, :P40 /* {{$_POST.review}} */, :P41 /* {{$_POST.shipping}} */, :P42 /* {{$_POST.source_address_city}} */, :P43 /* {{$_POST.source_address_line1}} */, :P44 /* {{$_POST.source_address_line1_check}} */, :P45 /* {{$_POST.source_address_state}} */, :P46 /* {{$_POST.source_address_zip}} */, :P47 /* {{$_POST.source_address_zip_check}} */, :P48 /* {{$_POST.source_brand}} */, :P49 /* {{$_POST.source_country}} */, :P50 /* {{$_POST.source_customer}} */, :P51 /* {{$_POST.source_cvc_check}} */, :P52 /* {{$_POST.source_dynamic_last4}} */, :P53 /* {{$_POST.source_id}} */, :P54 /* {{$_POST.source_line2}} */, :P55 /* {{$_POST.source_metadata}} */, :P56 /* {{$_POST.source_name}} */, :P57 /* {{$_POST.source_object}} */, :P58 /* {{$_POST.source_tokeniization_method}} */, :P59 /* {{$_POST.source_transfer}} */, :P60 /* {{$_POST.statement_descriptor}} */, :P61 /* {{$_POST.status}} */, :P62 /* {{$_POST.transfer_data}} */, :P63 /* {{$_POST.transfer_group}} */)",
            "params": [
              {
                "name": ":P1",
                "type": "expression",
                "value": "{{$_POST.data_amount}}"
              },
              {
                "name": ":P2",
                "type": "expression",
                "value": "{{$_POST.data_amount_refunded}}"
              },
              {
                "name": ":P3",
                "type": "expression",
                "value": "{{$_POST.data_application}}"
              },
              {
                "name": ":P4",
                "type": "expression",
                "value": "{{$_POST.data_application_fee}}"
              },
              {
                "name": ":P5",
                "type": "expression",
                "value": "{{$_POST.data_application_fee_amount}}"
              },
              {
                "name": ":P6",
                "type": "expression",
                "value": "{{$_POST.data_balance_transaction}}"
              },
              {
                "name": ":P7",
                "type": "expression",
                "value": "{{$_POST.data_captured}}"
              },
              {
                "name": ":P8",
                "type": "expression",
                "value": "{{$_POST.data_created}}"
              },
              {
                "name": ":P9",
                "type": "expression",
                "value": "{{$_POST.data_currency}}"
              },
              {
                "name": ":P10",
                "type": "expression",
                "value": "{{$_POST.data_customer}}"
              },
              {
                "name": ":P11",
                "type": "expression",
                "value": "{{$_POST.data_description}}"
              },
              {
                "name": ":P12",
                "type": "expression",
                "value": "{{$_POST.data_destination}}"
              },
              {
                "name": ":P13",
                "type": "expression",
                "value": "{{$_POST.data_dispute}}"
              },
              {
                "name": ":P14",
                "type": "expression",
                "value": "{{$_POST.data_failure_code}}"
              },
              {
                "name": ":P15",
                "type": "expression",
                "value": "{{$_POST.data_failure_message}}"
              },
              {
                "name": ":P16",
                "type": "expression",
                "value": "{{$_POST.data_fraud_details}}"
              },
              {
                "name": ":P17",
                "type": "expression",
                "value": "{{$_POST.data_invoice}}"
              },
              {
                "name": ":P18",
                "type": "expression",
                "value": "{{$_POST.data_livemode}}"
              },
              {
                "name": ":P19",
                "type": "expression",
                "value": "{{$_POST.data_metadata_orderid}}"
              },
              {
                "name": ":P20",
                "type": "expression",
                "value": "{{$_POST.data_object}}"
              },
              {
                "name": ":P21",
                "type": "expression",
                "value": "{{$_POST.data_on_behalf_of}}"
              },
              {
                "name": ":P22",
                "type": "expression",
                "value": "{{$_POST.data_order}}"
              },
              {
                "name": ":P23",
                "type": "expression",
                "value": "{{$_POST.outcome_network_status}}"
              },
              {
                "name": ":P24",
                "type": "expression",
                "value": "{{$_POST.outcome_reason}}"
              },
              {
                "name": ":P25",
                "type": "expression",
                "value": "{{$_POST.outcome_risk_level}}"
              },
              {
                "name": ":P26",
                "type": "expression",
                "value": "{{$_POST.outcome_risk_score}}"
              },
              {
                "name": ":P27",
                "type": "expression",
                "value": "{{$_POST.outcome_seller_message}}"
              },
              {
                "name": ":P28",
                "type": "expression",
                "value": "{{$_POST.outcome_type}}"
              },
              {
                "name": ":P29",
                "type": "expression",
                "value": "{{$_POST.paid}}"
              },
              {
                "name": ":P30",
                "type": "expression",
                "value": "{{$_POST.payment_intent}}"
              },
              {
                "name": ":P31",
                "type": "expression",
                "value": "{{$_POST.receipt_email}}"
              },
              {
                "name": ":P32",
                "type": "expression",
                "value": "{{$_POST.receipt_number}}"
              },
              {
                "name": ":P33",
                "type": "expression",
                "value": "{{$_POST.receipt_url}}"
              },
              {
                "name": ":P34",
                "type": "expression",
                "value": "{{$_POST.refunded}}"
              },
              {
                "name": ":P35",
                "type": "expression",
                "value": "{{$_POST.refunds_data}}"
              },
              {
                "name": ":P36",
                "type": "expression",
                "value": "{{$_POST.refunds_has_more}}"
              },
              {
                "name": ":P37",
                "type": "expression",
                "value": "{{$_POST.refunds_object}}"
              },
              {
                "name": ":P38",
                "type": "expression",
                "value": "{{$_POST.refunds_total_count}}"
              },
              {
                "name": ":P39",
                "type": "expression",
                "value": "{{$_POST.refunds_url}}"
              },
              {
                "name": ":P40",
                "type": "expression",
                "value": "{{$_POST.review}}"
              },
              {
                "name": ":P41",
                "type": "expression",
                "value": "{{$_POST.shipping}}"
              },
              {
                "name": ":P42",
                "type": "expression",
                "value": "{{$_POST.source_address_city}}"
              },
              {
                "name": ":P43",
                "type": "expression",
                "value": "{{$_POST.source_address_line1}}"
              },
              {
                "name": ":P44",
                "type": "expression",
                "value": "{{$_POST.source_address_line1_check}}"
              },
              {
                "name": ":P45",
                "type": "expression",
                "value": "{{$_POST.source_address_state}}"
              },
              {
                "name": ":P46",
                "type": "expression",
                "value": "{{$_POST.source_address_zip}}"
              },
              {
                "name": ":P47",
                "type": "expression",
                "value": "{{$_POST.source_address_zip_check}}"
              },
              {
                "name": ":P48",
                "type": "expression",
                "value": "{{$_POST.source_brand}}"
              },
              {
                "name": ":P49",
                "type": "expression",
                "value": "{{$_POST.source_country}}"
              },
              {
                "name": ":P50",
                "type": "expression",
                "value": "{{$_POST.source_customer}}"
              },
              {
                "name": ":P51",
                "type": "expression",
                "value": "{{$_POST.source_cvc_check}}"
              },
              {
                "name": ":P52",
                "type": "expression",
                "value": "{{$_POST.source_dynamic_last4}}"
              },
              {
                "name": ":P53",
                "type": "expression",
                "value": "{{$_POST.source_id}}"
              },
              {
                "name": ":P54",
                "type": "expression",
                "value": "{{$_POST.source_line2}}"
              },
              {
                "name": ":P55",
                "type": "expression",
                "value": "{{$_POST.source_metadata}}"
              },
              {
                "name": ":P56",
                "type": "expression",
                "value": "{{$_POST.source_name}}"
              },
              {
                "name": ":P57",
                "type": "expression",
                "value": "{{$_POST.source_object}}"
              },
              {
                "name": ":P58",
                "type": "expression",
                "value": "{{$_POST.source_tokeniization_method}}"
              },
              {
                "name": ":P59",
                "type": "expression",
                "value": "{{$_POST.source_transfer}}"
              },
              {
                "name": ":P60",
                "type": "expression",
                "value": "{{$_POST.statement_descriptor}}"
              },
              {
                "name": ":P61",
                "type": "expression",
                "value": "{{$_POST.status}}"
              },
              {
                "name": ":P62",
                "type": "expression",
                "value": "{{$_POST.transfer_data}}"
              },
              {
                "name": ":P63",
                "type": "expression",
                "value": "{{$_POST.transfer_group}}"
              }
            ]
          }
        },
        "meta": [
          {
            "name": "identity",
            "type": "text"
          },
          {
            "name": "affected",
            "type": "number"
          }
        ]
      }
    ]
  }
}
JSON
);
?>