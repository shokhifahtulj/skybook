#!/bin/bash
# Railway Health Check for SkyBook

PORT=${PORT:-8000}
MAX_RETRIES=5
RETRY_DELAY=2

echo "🏥 Checking SkyBook health at http://127.0.0.1:$PORT/up"

for i in $(seq 1 $MAX_RETRIES); do
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:$PORT/up || echo "000")
    
    if [ "$HTTP_CODE" = "200" ]; then
        echo "✅ Health check PASSED (HTTP $HTTP_CODE)"
        exit 0
    fi
    
    echo "⏳ Attempt $i/$MAX_RETRIES failed (HTTP $HTTP_CODE), retrying in ${RETRY_DELAY}s..."
    sleep $RETRY_DELAY
done

echo "❌ Health check FAILED after $MAX_RETRIES attempts"
exit 1
