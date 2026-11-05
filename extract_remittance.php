<?php
/**
 * Extract structured data from remittance advice documents using the Photon Commerce API.
 *
 * Submits a remittance advice (PDF or image) and returns 30+ structured fields including
 * vendor name, customer name, payment date, payment method, reference number, total,
 * and a full line-item breakdown for every invoice.
 *
 * Processing times (Managed Agents):
 *   Trial accounts:  up to 24 hours
 *   Production:      5 minutes to 24 hours
 *
 * AI extraction (seconds, no Managed Agents):
 *   Contact support@photoncommerce.com to activate.
 *   Once active, submit to /api/v4 instead of /api/pro.
 *
 * Requires: guzzlehttp/guzzle (composer require guzzlehttp/guzzle)
 *
 * Docs:    https://apidocs.photoncommerce.com
 * Sandbox: https://sandbox-api.photoncommerce.com/api/v4/register (20 free calls)
 */

require 'vendor/autoload.php';

use GuzzleHttp\Client;

define('CLIENT_ID',  'YOUR_CLIENT_ID');
define('USERNAME',   'YOUR_USERNAME');
define('API_KEY',    'YOUR_API_KEY');
define('PASSWORD',   'YOUR_PASSWORD');
define('SECRET_KEY', 'YOUR_SECRET_KEY');

// Sandbox: https://sandbox-api.photoncommerce.com  (20 free calls, no card needed)
// Production: https://api.photoncommerce.com
define('BASE_URL', 'https://sandbox-api.photoncommerce.com');

$client = new Client([
    'base_uri' => BASE_URL,
    'headers'  => [
        'CLIENT-ID'     => CLIENT_ID,
        'AUTHORIZATION' => 'apikey ' . USERNAME . ':' . API_KEY,
        'PASSWORD'      => PASSWORD,
        'SECRET-KEY'    => SECRET_KEY,
    ],
]);

function submitRemittance(
    Client $client,
    string $filePath = null,
    string $url = null,
    string $webhookUrl = null,
    string $authToken = null,
    string $id = null,
    string $subaccount = null,
    int    $pageStart = null,
    int    $pageEnd = null
): string {
    if (!$filePath && !$url) throw new InvalidArgumentException('Provide either filePath or url.');

    $query = ['doctype' => 'remittance'];
    if ($url)        $query['url']         = $url;
    if ($webhookUrl) $query['webhook_url'] = $webhookUrl;
    if ($authToken)  $query['auth_token']  = $authToken;
    if ($id)         $query['ID']          = $id;
    if ($subaccount) $query['subaccount']  = $subaccount;
    if ($pageStart !== null) $query['page_start'] = $pageStart;
    if ($pageEnd   !== null) $query['page_end']   = $pageEnd;

    $options = ['query' => $query];
    if ($filePath) {
        $options['multipart'] = [
            ['name' => 'pdf', 'contents' => fopen($filePath, 'r'), 'filename' => basename($filePath)],
        ];
    }

    // For AI extraction (seconds), replace /api/pro with /api/v4 — contact support@photoncommerce.com to activate.
    $response = $client->post('/api/pro', $options);
    $data = json_decode($response->getBody(), true);
    return $data['photon_key'];
}

function fetchResult(Client $client, string $photonKey): array
{
    $response = $client->get('/api/v4/json', ['query' => ['photon_key' => $photonKey]]);
    $data = json_decode($response->getBody(), true);
    return $data['data'] ?? [];
}

function waitForResult(Client $client, string $photonKey, int $pollInterval = 20, int $timeout = 3600): array
{
    $deadline = time() + $timeout;
    while (time() < $deadline) {
        $result = fetchResult($client, $photonKey);
        $status = $result['Status'] ?? null;
        if ($status && $status !== 'pending' && $status !== 'processing') return $result;
        echo "  Status: " . ($status ?? 'pending') . " — retrying in {$pollInterval}s...\n";
        sleep($pollInterval);
    }
    throw new RuntimeException("Extraction not complete after {$timeout}s");
}

// --- Option A: submit from a local file ---
$photonKey = submitRemittance($client, filePath: 'remittance.pdf');

// --- Option B: submit via a publicly accessible URL ---
// $photonKey = submitRemittance($client, url: 'https://example.com/remittance.pdf');

echo "Submitted. photon_key: $photonKey\n";
echo "Waiting for extraction to complete...\n";

$result = waitForResult($client, $photonKey);

echo "\n--- Remittance Data ---\n";
echo "Payee:          " . ($result['payeeName']     ?? '') . "\n";
echo "Payer:          " . ($result['payerName']     ?? '') . "\n";
echo "Date:           " . ($result['documentDate']  ?? '') . "\n";
echo "Payment Method: " . ($result['paymentMethod'] ?? '') . "\n";
echo "Check Number:   " . ($result['checkNumber']   ?? '') . "\n";
echo "Total:          " . ($result['total']         ?? '') . " " . ($result['currencyCode'] ?? '') . "\n";

$lineItems = $result['lineItems'] ?? [];
if (!empty($lineItems)) {
    echo "\n--- Line Items (" . count($lineItems) . ") ---\n";
    foreach ($lineItems as $item) {
        echo "  Invoice " . ($item['invoiceNumber'] ?? '') . ": "
           . ($item['description'] ?? '') . ", "
           . "amount " . ($item['amount'] ?? '') . ", "
           . "discount " . ($item['discount'] ?? '') . "\n";
    }
}
