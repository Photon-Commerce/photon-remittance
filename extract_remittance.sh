#!/bin/bash
# Extract structured data from remittance advice documents using the Photon Commerce API.
#
# Submits a remittance advice (PDF or image) and returns 30+ structured fields including
# vendor name, customer name, payment date, payment method, reference number, total,
# and a full line-item breakdown for every invoice.
#
# Processing times (Managed Agents):
#   Trial accounts:  up to 24 hours
#   Production:      5 minutes to 24 hours
#
# AI extraction (seconds, no Managed Agents):
#   Contact support@photoncommerce.com to activate.
#   Once active, submit to /api/v4 instead of /api/pro.
#
# Docs:    https://apidocs.photoncommerce.com
# Sandbox: https://sandbox-api.photoncommerce.com/api/v4/register (20 free calls)

CLIENT_ID="YOUR_CLIENT_ID"
USERNAME="YOUR_USERNAME"
API_KEY="YOUR_API_KEY"
PASSWORD="YOUR_PASSWORD"
SECRET_KEY="YOUR_SECRET_KEY"

# Sandbox: https://sandbox-api.photoncommerce.com  (20 free calls, no card needed)
# Production: https://api.photoncommerce.com
BASE_URL="https://sandbox-api.photoncommerce.com"

# For AI extraction (seconds), replace /api/pro with /api/v4 — contact support@photoncommerce.com to activate.

# --- Option A: submit from a local file ---
RESPONSE=$(curl -s -X POST "$BASE_URL/api/pro?doctype=remittance" \
  -H "CLIENT-ID: $CLIENT_ID" \
  -H "AUTHORIZATION: apikey $USERNAME:$API_KEY" \
  -H "PASSWORD: $PASSWORD" \
  -H "SECRET-KEY: $SECRET_KEY" \
  -F "pdf=@remittance.pdf")

# --- Option B: submit via a publicly accessible URL ---
# RESPONSE=$(curl -s -X POST "$BASE_URL/api/pro?doctype=remittance&url=https://example.com/remittance.pdf" \
#   -H "CLIENT-ID: $CLIENT_ID" \
#   -H "AUTHORIZATION: apikey $USERNAME:$API_KEY" \
#   -H "PASSWORD: $PASSWORD" \
#   -H "SECRET-KEY: $SECRET_KEY")

# Optional parameters (append to the query string as needed):
#   &webhook_url=https://your-server.com/webhook
#   &auth_token=YOUR_TOKEN
#   &ID=YOUR_CUSTOM_ID
#   &subaccount=SUBACCOUNT_NAME
#   &page_start=1&page_end=1

PHOTON_KEY=$(echo "$RESPONSE" | grep -o '"photon_key":"[^"]*"' | cut -d'"' -f4)
echo "Submitted. photon_key: $PHOTON_KEY"
echo "Waiting for extraction to complete..."

POLL_INTERVAL=20
while true; do
  RESULT=$(curl -s "$BASE_URL/api/v4/json?photon_key=$PHOTON_KEY" \
    -H "CLIENT-ID: $CLIENT_ID" \
    -H "AUTHORIZATION: apikey $USERNAME:$API_KEY" \
    -H "PASSWORD: $PASSWORD" \
    -H "SECRET-KEY: $SECRET_KEY")

  STATUS=$(echo "$RESULT" | grep -o '"Status":"[^"]*"' | cut -d'"' -f4)
  if [ "$STATUS" != "pending" ] && [ "$STATUS" != "processing" ] && [ -n "$STATUS" ]; then
    break
  fi
  echo "  Status: ${STATUS:-pending} — retrying in ${POLL_INTERVAL}s..."
  sleep $POLL_INTERVAL
done

echo ""
echo "--- Remittance Data ---"
echo "$RESULT" | python3 -c "
import sys, json
data = json.load(sys.stdin).get('data', {})
print('Payee:          ', data.get('payeeName'))
print('Payer:          ', data.get('payerName'))
print('Date:           ', data.get('documentDate'))
print('Payment Method: ', data.get('paymentMethod'))
print('Check Number:   ', data.get('checkNumber'))
print('Total:          ', data.get('total'), data.get('currencyCode'))
items = data.get('lineItems', [])
if items:
    print(f'\n--- Line Items ({len(items)}) ---')
    for item in items:
        print(f'  Invoice {item.get(\"invoiceNumber\")}: {item.get(\"description\")}, amount {item.get(\"amount\")}, discount {item.get(\"discount\")}')
"
