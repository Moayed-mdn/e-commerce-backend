<?php

$baseUrl  = 'http://localhost:8000';
$email    = 'super@test.com';
$password = 'password';
$cookieJar = sys_get_temp_dir() . '/me-test-cookies.txt';

if (file_exists($cookieJar)) unlink($cookieJar);

echo "=== /me Endpoint Behavior Test ===\n\n";

// Step 1 — Get CSRF cookie
echo "Step 1: CSRF cookie...\n";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => "$baseUrl/sanctum/csrf-cookie",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER         => true,
    CURLOPT_COOKIEJAR      => $cookieJar,
    CURLOPT_COOKIEFILE     => $cookieJar,
    CURLOPT_HTTPHEADER     => [
        'Accept: application/json',
        'Origin: http://localhost:3000',
        'Referer: http://localhost:3000/',
    ],
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$xsrfToken = '';
if (file_exists($cookieJar)) {
    $content = file_get_contents($cookieJar);
    if (preg_match('/XSRF-TOKEN\s+([^\s]+)/', $content, $matches)) {
        $xsrfToken = urldecode($matches[1]);
        echo "  XSRF Token: found ✅\n";
    }
}

// Step 2 — Login
echo "\nStep 2: Login...\n";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => "$baseUrl/api/v1/users/auth/login",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER         => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode(['email' => $email, 'password' => $password]),
    CURLOPT_COOKIEJAR      => $cookieJar,
    CURLOPT_COOKIEFILE     => $cookieJar,
    CURLOPT_HTTPHEADER     => [
        'Accept: application/json',
        'Content-Type: application/json',
        'Origin: http://localhost:3000',
        'Referer: http://localhost:3000/',
        "X-XSRF-TOKEN: $xsrfToken",
    ],
]);
$response   = curl_exec($ch);
$httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body       = substr($response, $headerSize);
curl_close($ch);

if ($httpCode === 200) {
    echo "  Login: ✅\n";
} else {
    echo "  Login failed ❌: $body\n";
    exit(1);
}

// Step 3 — Call /me WITH cookie (simulates browser client call)
echo "\nStep 3: GET /me WITH session cookie (simulates Axios client)...\n";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => "$baseUrl/api/v1/users/auth/me",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER         => true,
    CURLOPT_COOKIEJAR      => $cookieJar,
    CURLOPT_COOKIEFILE     => $cookieJar,
    CURLOPT_HTTPHEADER     => [
        'Accept: application/json',
        'Origin: http://localhost:3000',
        'Referer: http://localhost:3000/',
        "X-XSRF-TOKEN: $xsrfToken",
    ],
]);
$response   = curl_exec($ch);
$httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body       = substr($response, $headerSize);
curl_close($ch);

if ($httpCode === 200) {
    echo "  /me WITH cookie: ✅ (backend correct)\n";
    $user = json_decode($body, true);
    echo "  User: " . $user['data']['email'] . "\n";
} else {
    echo "  /me WITH cookie: ❌ BACKEND BUG — got $httpCode\n";
    echo "  Response: $body\n";
    exit(1);
}

// Step 4 — Call /me WITHOUT cookie (simulates RSC server call)
echo "\nStep 4: GET /me WITHOUT cookie (simulates RSC without forwarding)...\n";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => "$baseUrl/api/v1/users/auth/me",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER         => true,
    CURLOPT_HTTPHEADER     => [
        'Accept: application/json',
        'Origin: http://localhost:3000',
    ],
]);
$response   = curl_exec($ch);
$httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$body       = substr($response, $headerSize);
curl_close($ch);

if ($httpCode === 401) {
    $decoded = json_decode($body, true);
    if (isset($decoded['status']) && $decoded['status'] === false) {
        echo "  /me WITHOUT cookie returns 401 JSON: ✅ (correct behavior)\n";
        echo "  This confirms: backend is fine.\n";
        echo "  The frontend RSC must forward cookies manually.\n";
    }
} else {
    echo "  Unexpected status: $httpCode ❌\n";
    echo "  Response: $body\n";
}

// Cleanup
unlink($cookieJar);

echo "\n=== Verdict ===\n";
echo "If Step 3 passed and Step 4 returned 401:\n";
echo "  Backend is 100% correct.\n";
echo "  The fix belongs entirely in the frontend.\n";
echo "  RSC must use cookies() from next/headers to forward session.\n";
