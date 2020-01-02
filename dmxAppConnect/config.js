dmx.config({
  "admin_info01": {
    "query": [
      {
        "type": "text",
        "name": "offsett"
      }
    ]
  },
  "stripeCustomerObject": {
    "repeat1": {
      "meta": [
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
      ],
      "outputType": "array"
    },
    "tableRepeat1": {
      "meta": null,
      "outputType": "text"
    }
  },
  "viewStripeCustomerData": {
    "tableRepeat1": {
      "meta": [
        {
          "name": "$index",
          "type": "number"
        },
        {
          "name": "$key",
          "type": "text"
        },
        {
          "name": "$value",
          "type": "object"
        },
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
      ],
      "outputType": "object"
    }
  },
  "stripeMaster": {
    "repeat1": {
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
    },
    "tableRepeat2": {
      "meta": null,
      "outputType": "text"
    }
  },
  "stripeAPIMaster": {
    "tableRepeat2": {
      "meta": null,
      "outputType": "text"
    },
    "query": [
      {
        "type": "text",
        "name": "cus"
      }
    ],
    "tableRepeat1": {
      "meta": null,
      "outputType": "text"
    }
  },
  "showcustomer": {
    "cus": {
      "meta": null,
      "outputType": "text"
    },
    "query": [
      {
        "type": "text",
        "name": "cus"
      }
    ],
    "repeat1": {
      "meta": [
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
      ],
      "outputType": "object"
    },
    "data_detail1": {
      "meta": [
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
      ],
      "outputType": "object"
    },
    "data_view1": {
      "meta": [
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
      ],
      "outputType": "object"
    }
  }
});
