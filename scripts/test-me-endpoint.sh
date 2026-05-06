#!/bin/bash

BASE_URL="http://localhost:8000"
EMAIL="super@test.com"
PASSWORD="password"
COOKIE_JAR="/tmp/me-test-cookies.txt"

# Clean up
rm -f "$COOKIE_JAR"

echo "=== /me Endpoint Behavior Test ==="
echo ""

# Step 1 — Get CSRF cookie
echo "Step 1: CSRF cookie..."
curl -s -c "$COOKIE_JAR" -b "$COOKIE_JAR" \
  -H "Accept: application/json" \
  -H "Origin: http://localhost:3000" \
  -H "Referer: http://localhost:3000/" \
  "$BASE_URL/sanctum/csrf-cookie" > /dev/null

XSRF_TOKEN=""
if [ -f "$COOKIE_JAR" ]; then
    XSRF_TOKEN=$(grep "XSRF-TOKEN" "$COOKIE_JAR" | tail -1 | awk '{print $7}')
    if [ -n "$XSRF_TOKEN" ]; then
        echo "  XSRF Token: found ✅"
        # URL decode
        XSRF_TOKEN=$(echo "$XSRF_TOKEN" | python3 -c "import sys,urllib.parse; print(urllib.parse.unquote(sys.stdin.read().strip()))" 2>/dev/null || echo "$XSRF_TOKEN")
    else
        echo "  XSRF Token: NOT FOUND ❌"
        exit 1
    fi
else
    echo "  Cookie jar not created ❌"
    exit 1
fi

# Step 2 — Login
echo ""
echo "Step 2: Login..."
LOGIN_RESPONSE=$(curl -s -c "$COOKIE_JAR" -b "$COOKIE_JAR" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Origin: http://localhost:3000" \
  -H "Referer: http://localhost:3000/" \
  -H "X-XSRF-TOKEN: $XSRF_TOKEN" \
  -X POST \
  -d "{\"email\":\"$EMAIL\",\"password\":\"$PASSWORD\"}" \
  "$BASE_URL/api/v1/users/auth/login")

if echo "$LOGIN_RESPONSE" | grep -q '"status":true'; then
    echo "  Login: ✅"
else
    echo "  Login failed ❌"
    echo "  Response: $LOGIN_RESPONSE"
    exit 1
fi

# Step 3 — Call /me WITH cookie
echo ""
echo "Step 3: GET /me WITH session cookie (simulates Axios client)..."
ME_RESPONSE=$(curl -s -c "$COOKIE_JAR" -b "$COOKIE_JAR" \
  -H "Accept: application/json" \
  -H "Origin: http://localhost:3000" \
  -H "Referer: http://localhost:3000/" \
  -H "X-XSRF-TOKEN: $XSRF_TOKEN" \
  "$BASE_URL/api/v1/users/auth/me")

if echo "$ME_RESPONSE" | grep -q '"status":true'; then
    echo "  /me WITH cookie: ✅ (backend correct)"
    EMAIL=$(echo "$ME_RESPONSE" | grep -o '"email":"[^"]*"' | head -1 | cut -d'"' -f4)
    echo "  User: $EMAIL"
else
    echo "  /me WITH cookie: ❌ BACKEND BUG"
    echo "  Response: $ME_RESPONSE"
    exit 1
fi

# Step 4 — Call /me WITHOUT cookie
echo ""
echo "Step 4: GET /me WITHOUT cookie (simulates RSC without forwarding)..."
ME_NO_COOKIE=$(curl -s -w "\nHTTP_CODE:%{http_code}" \
  -H "Accept: application/json" \
  -H "Origin: http://localhost:3000" \
  "$BASE_URL/api/v1/users/auth/me")

HTTP_CODE=$(echo "$ME_NO_COOKIE" | grep "HTTP_CODE:" | cut -d: -f2)
BODY=$(echo "$ME_NO_COOKIE" | grep -v "HTTP_CODE:")

if [ "$HTTP_CODE" = "401" ]; then
    if echo "$BODY" | grep -q '"status":false'; then
        echo "  /me WITHOUT cookie returns 401 JSON: ✅ (correct behavior)"
        echo "  This confirms: backend is fine."
        echo "  The frontend RSC must forward cookies manually."
    else
        echo "  Got 401 but wrong format ❌"
        echo "  Response: $BODY"
    fi
else
    echo "  Unexpected status: $HTTP_CODE ❌"
    echo "  Response: $BODY"
fi

# Cleanup
rm -f "$COOKIE_JAR"

echo ""
echo "=== Verdict ==="
echo "If Step 3 passed and Step 4 returned 401:"
echo "  Backend is 100% correct."
echo "  The fix belongs entirely in the frontend."
echo "  RSC must use cookies() from next/headers to forward session."
