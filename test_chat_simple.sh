#!/bin/bash

# Jednoduchý test chatu - scénář 1
BASE_URL="http://localhost:8086"

echo "=== Test 1: Standardní objednávka ==="
echo ""

# Vytvoř session
echo "1. Vytváření session..."
SESSION_RESPONSE=$(curl -s -X POST "$BASE_URL/chat/sessions" \
  -H "Content-Type: application/json" \
  -d '{"language":"cs-CZ"}')

echo "Response: $SESSION_RESPONSE"
echo ""

SESSION_ID=$(echo $SESSION_RESPONSE | grep -o '"sessionId":"[^"]*"' | cut -d'"' -f4)
GREETING=$(echo $SESSION_RESPONSE | grep -o '"greeting":"[^"]*"' | cut -d'"' -f4)

echo "Session ID: $SESSION_ID"
echo "Pozdrav: $GREETING"
echo ""

if [ -z "$SESSION_ID" ]; then
  echo "CHYBA: Nepodařilo se vytvořit session"
  exit 1
fi

# Zpráva 1
echo "2. Uživatel: Ahoj, potřebuju výměnu oleje"
MSG1=$(curl -s -X POST "$BASE_URL/chat/sessions/$SESSION_ID/messages" \
  -H "Content-Type: application/json" \
  -d '{"language":"cs-CZ","message":"Ahoj, potřebuju výměnu oleje"}')

echo "Asistent:"
echo $MSG1 | grep -o '"content":"[^"]*"' | head -1 | cut -d'"' -f4 | sed 's/\\n/\n/g'
echo ""
sleep 1

# Zpráva 2
echo "3. Uživatel: Jan Novák, 777123456, jan@email.cz, Škoda Octavia, 3A12345, Praha 9, Vysočanská 123"
MSG2=$(curl -s -X POST "$BASE_URL/chat/sessions/$SESSION_ID/messages" \
  -H "Content-Type: application/json" \
  -d '{"language":"cs-CZ","message":"Jan Novák, 777123456, jan@email.cz, Škoda Octavia, 3A12345, Praha 9, Vysočanská 123"}')

echo "Asistent:"
echo $MSG2 | grep -o '"content":"[^"]*"' | head -1 | cut -d'"' -f4 | sed 's/\\n/\n/g'
echo ""
sleep 1

# Zpráva 3
echo "4. Uživatel: Za 3 dny dopoledne"
MSG3=$(curl -s -X POST "$BASE_URL/chat/sessions/$SESSION_ID/messages" \
  -H "Content-Type: application/json" \
  -d '{"language":"cs-CZ","message":"Za 3 dny dopoledne"}')

echo "Asistent:"
echo $MSG3 | grep -o '"content":"[^"]*"' | head -1 | cut -d'"' -f4 | sed 's/\\n/\n/g'
echo ""

echo "=== Test dokončen ==="
