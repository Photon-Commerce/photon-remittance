<div align="center">
  <img src="https://images.squarespace-cdn.com/content/v1/607861b10c0e3b4816f56581/3eea3ca7-58ca-402d-9edf-b8e9e72eca3c/lightning.png?format=300w" alt="Photon Commerce">

  <h1>REMITTANCE</h1>
  <p><strong>Extract structured data from remittance advice documents with 99.9%+ accuracy</strong></p>
  <p><strong>Powered by <a href="https://www.photoncommerce.com">Photon Commerce</a> — Managed AI Agents with data verification and validation</strong></p>

  <p>
    <a href="https://www.photoncommerce.com"><img src="https://img.shields.io/badge/SOC%202-Compliant-blue?style=for-the-badge" alt="SOC 2"></a>
    &nbsp;&nbsp;
    <a href="https://www.photoncommerce.com"><img src="https://img.shields.io/badge/GDPR-Attested-blue?style=for-the-badge" alt="GDPR"></a>
    &nbsp;&nbsp;
    <a href="https://www.photoncommerce.com/pricing"><img src="https://img.shields.io/badge/Accuracy-99.9%25%2B-00C853?style=for-the-badge" alt="Accuracy"></a>
    &nbsp;&nbsp;
    <a href="https://www.photoncommerce.com/platform"><img src="https://img.shields.io/badge/Languages-25%2B-FF6D00?style=for-the-badge" alt="Languages"></a>
  </p>

  [Website](https://www.photoncommerce.com) &nbsp;·&nbsp; [API Docs](https://apidocs.photoncommerce.com) &nbsp;·&nbsp; [Pricing](https://www.photoncommerce.com/pricing) &nbsp;·&nbsp; [Free Trial](https://app.photoncommerce.com) &nbsp;·&nbsp;[Try in Claude](https://claude.ai/directory/connectors/photoncommerce) &nbsp;·&nbsp;[Try in ChatGPT](https://chatgpt.com/plugins/plugin_asdk_app_696685b735588191b9f25f976cfda7b2) 
</div>

---

## Overview

This repository contains ready-to-run code samples for extracting structured data from remittance advice documents using the **Photon PRO API**.

Submit any remittance advice — emailed, faxed, or mailed — and receive a structured JSON response with header fields and a full line-item breakdown, verified to 99.9%+ accuracy by Photon's Managed Agents network of 2,300+ expert reviewers across 7+ countries.

---

## Extracted Fields

| Category | Fields |
|----------|--------|
| **Payee** | Name, address, city, state, zip, country |
| **Payer** | Name, address, city, state, zip, country, phone, bank details |
| **Payment** | Date, method, check number, invoice number, purchase order number |
| **Amounts** | Total, subtotal, tax, discount, currency |
| **Line Items** | Invoice number, description, date, quantity, unit price, amount, discount |
| **Metadata** | Duplicate flag, memo, document type, pages, fraud/risk score |

40+ standardised fields · Line-item detail for every invoice · 25+ document languages

---

## Quick Start

```python
import requests

HEADERS = {
    "CLIENT-ID":     "YOUR_CLIENT_ID",
    "AUTHORIZATION": "apikey YOUR_USERNAME:YOUR_API_KEY",
    "PASSWORD":      "YOUR_PASSWORD",
    "SECRET-KEY":    "YOUR_SECRET_KEY",
}

# Step 1 — Submit
response = requests.post(
    "https://sandbox-api.photoncommerce.com/api/pro",
    headers=HEADERS,
    params={"doctype": "remittance"},
    files={"pdf": open("remittance.pdf", "rb")},
)
photon_key = response.json()["photon_key"]

# Step 2 — Retrieve
result = requests.get(
    "https://sandbox-api.photoncommerce.com/api/v4/json",
    headers=HEADERS,
    params={"photon_key": photon_key},
).json()["data"]

print(result["payeeName"])     # CITY ELECTRIC SUPPLY
print(result["payerName"])     # THE BROOKSTONE SCHOOL, INC.
print(result["documentDate"])  # 2021-04-20
print(result["total"])         # 196.8
print(result["checkNumber"])   # 24415
```

[Get your free sandbox credentials →](https://sandbox-api.photoncommerce.com/api/v4/register)

---

## Code Examples

| Language | File | Library |
|----------|------|---------|
| ![Python](https://img.shields.io/badge/Python-3776AB?style=flat-square&logo=python&logoColor=white) | [extract_remittance.py](extract_remittance.py) | requests |
| ![JavaScript](https://img.shields.io/badge/Node.js-339933?style=flat-square&logo=nodedotjs&logoColor=white) | [extract_remittance.js](extract_remittance.js) | fetch + form-data |
| ![cURL](https://img.shields.io/badge/cURL-073551?style=flat-square&logo=curl&logoColor=white) | [extract_remittance.sh](extract_remittance.sh) | curl |
| ![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white) | [extract_remittance.php](extract_remittance.php) | Guzzle |

Every example supports both **local file upload** and **URL-based submission**, plus optional webhook callbacks.

---

## How It Works

```
Remittance Advice In (PDF / image)
    ↓
Photon Engine  —  AI + OCR + NLP
    ↓
Managed Agents QA  —  2,300+ expert reviewers
    ↓
Structured JSON Out  —  30+ verified fields + line items
```

### Submission

```
POST /api/pro?doctype=remittance
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `doctype` | string | `remittance` |
| `url` | string | URL of a publicly accessible document (alternative to file upload) |
| `webhook_url` | string | Receive a callback when extraction is complete |
| `auth_token` | string | Token to verify the webhook callback |
| `ID` | string | Your own reference ID for this submission |
| `subaccount` | string | Route to a subaccount |
| `page_start` | integer | First page to process (multi-page PDFs) |
| `page_end` | integer | Last page to process (multi-page PDFs) |

### Retrieval

```
GET /api/v4/json?photon_key=YOUR_PHOTON_KEY
```

Or pass `webhook_url` at submission and Photon will POST the result to your endpoint when ready.

---

## Processing Times

| Account Type | Turnaround |
|-------------|------------|
| Trial | Up to 24 hours (Managed Agents included) |
| Production | seconds to 24 hours (Managed Agents included) |

> **Instant Managed Agent:** To activate, contact [support@photoncommerce.com](mailto:support@photoncommerce.com). Once activated, submit to `/api/v4` instead of `/api/pro` for near-instant results.

---

## Authentication

All requests require four headers:

```
CLIENT-ID:     your-client-id
AUTHORIZATION: apikey your-username:your-api-key
PASSWORD:      your-password
SECRET-KEY:    your-secret-key
```

Get credentials: [Register a free sandbox account](https://sandbox-api.photoncommerce.com/api/v4/register) (20 free calls, no credit card required) or sign up at [app.photoncommerce.com](https://app.photoncommerce.com).

---

## Sample Response

```json
{
  "data": {
    "total": 196.8,
    "subtotal": 196.8,
    "tax": 0,
    "discount": 0,
    "documentType": "Remittance",
    "invoiceNumber": "24415",
    "checkNumber": "24415",
    "purchaseOrderNumber": "",
    "documentDate": "2021-04-20",
    "documentUploadCreatedDate": "2022-04-27 21:35:20",
    "transactionOrderDate": "",
    "serviceStartDate": "",
    "serviceEndDate": "",
    "currencyCode": "USD",
    "payerAccountNumber": "",
    "payeeName": "CITY ELECTRIC SUPPLY",
    "payeeAddress": "",
    "payeeAddressLine": "",
    "payeeCity": "",
    "payeeState": "",
    "payeeZipcode": "",
    "payeeCountry": "US",
    "payeeVATNumber": "",
    "payeeEmail": "",
    "paymentMethod": "Check",
    "payerName": "THE BROOKSTONE SCHOOL, INC.",
    "payerEmail": "",
    "payerAddress": "440 BRADLEY PARK DRIVE, COLUMBUS, GEORGIA 31904-2989",
    "payerAddressLine": "440 BRADLEY PARK DRIVE",
    "payerCity": "COLUMBUS",
    "payerState": "GEORGIA",
    "payerZipcode": "31904-2989",
    "payerCountry": "US",
    "payerPhone": "",
    "payerFax": "",
    "payerWebsite": "",
    "payerABNNumber": "",
    "payerBankName": "",
    "payerBankNumber": "",
    "payerBankAccountNumber": "",
    "payerBankSWIFTCode": "",
    "payerIBAN": "",
    "pages": 1,
    "isDuplicate": 0,
    "memo": "",
    "lineItems": [
      {
        "line": 1,
        "invoiceNumber": "COL/064811",
        "description": "LED BULBS",
        "date": "2021-03-31",
        "quantity": 1,
        "unitOfMeasure": "",
        "purchaseOrderNumber": "",
        "referenceNumber": "",
        "taxRate": 0,
        "taxAmount": 0,
        "type": "",
        "unitPrice": 295.2,
        "discount": 0,
        "amount": 295.2,
        "flexField": []
      },
      {
        "line": 2,
        "invoiceNumber": "COL/064884",
        "description": "LED BULBS",
        "date": "2021-04-06",
        "quantity": 1,
        "unitOfMeasure": "",
        "purchaseOrderNumber": "",
        "referenceNumber": "",
        "taxRate": 0,
        "taxAmount": 0,
        "type": "",
        "unitPrice": 196.8,
        "discount": 0,
        "amount": 196.8,
        "flexField": ["PAINTED"]
      }
    ],
    "referenceNumber": "",
    "fraudScore": 5,
    "riskScore": 5,
    "anomalyScore": 5,
    "photonKey": "data/johndoe@abc.com/2022-04-27/21-35-15-258869_00000763.json",
    "docPath": "data/johndoe@abc.com/2022-04-27/21-35-15-258869_00000763.pdf"
  },
  "message": "success",
  "status": "success"
}
```

---

## Other Document Types

| Repo | Document |
|------|----------|
| [`photon-invoice`](https://github.com/Photon-Commerce/photon-invoice) | Invoices |
| [`photon-receipt`](https://github.com/Photon-Commerce/photon-receipt) | Receipts |
| [`photon-check`](https://github.com/Photon-Commerce/photon-check) | Checks |
| [`photon-bank-statement`](https://github.com/Photon-Commerce/photon-bank-statement) | Bank & card statements |
| [`photon-bill-of-lading`](https://github.com/Photon-Commerce/photon-bill-of-lading) | Bills of lading |

---

## Links

| | |
|-|-|
| **Try in Claude** | [Photon Commerce on Claude](https://claude.ai/directory/connectors/photoncommerce) |
| **Try in ChatGPT** | [Photon Commerce on ChatGPT](https://chatgpt.com/plugins/plugin_asdk_app_696685b735588191b9f25f976cfda7b2) |
| **API Docs** | [apidocs.photoncommerce.com](https://apidocs.photoncommerce.com) |
| **Free Trial** | [app.photoncommerce.com](https://app.photoncommerce.com) |
| **Pricing** | [photoncommerce.com/pricing](https://www.photoncommerce.com/pricing) |
| **Support** | [support@photoncommerce.com](mailto:support@photoncommerce.com) |
| **Enterprise** | [developers@photoncommerce.com](mailto:developers@photoncommerce.com) |
