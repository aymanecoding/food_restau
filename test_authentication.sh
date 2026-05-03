#!/bin/bash
# Test Script for Authentication System
# Dar EL Idrissi - Food App

API_URL="http://localhost:8000/api"
echo "🧪 Testing Authentication System..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test 1: Register
echo -e "\n${YELLOW}Test 1: Registration${NC}"
REGISTER_RESPONSE=$(curl -s -X POST "$API_URL/register" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test'$(date +%s)'@example.com",
    "password": "testpass123",
    "password_confirmation": "testpass123"
  }')

echo "$REGISTER_RESPONSE" | jq .

# Extract token from response
REGISTER_TOKEN=$(echo "$REGISTER_RESPONSE" | jq -r '.token // empty')

if [ -z "$REGISTER_TOKEN" ]; then
  echo -e "${RED}❌ Registration failed${NC}"
else
  echo -e "${GREEN}✅ Registration successful${NC}"
  echo "Token: $REGISTER_TOKEN"
fi

# Test 2: Login
echo -e "\n${YELLOW}Test 2: Login${NC}"
LOGIN_RESPONSE=$(curl -s -X POST "$API_URL/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@test.com",
    "password": "password123"
  }')

echo "$LOGIN_RESPONSE" | jq .

LOGIN_TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.token // empty')

if [ -z "$LOGIN_TOKEN" ]; then
  echo -e "${RED}❌ Login failed${NC}"
  exit 1
else
  echo -e "${GREEN}✅ Login successful${NC}"
  echo "Token: $LOGIN_TOKEN"
fi

# Test 3: Get current user
echo -e "\n${YELLOW}Test 3: Get Current User${NC}"
curl -s -X GET "$API_URL/me" \
  -H "Authorization: Bearer $LOGIN_TOKEN" \
  -H "Content-Type: application/json" | jq .

# Test 4: Get all categories (public)
echo -e "\n${YELLOW}Test 4: Get Categories (Public)${NC}"
curl -s -X GET "$API_URL/categories" \
  -H "Content-Type: application/json" | jq . | head -20

# Test 5: Get all dishes (public)
echo -e "\n${YELLOW}Test 5: Get Dishes (Public)${NC}"
curl -s -X GET "$API_URL/dishes" \
  -H "Content-Type: application/json" | jq . | head -20

# Test 6: Get orders (protected)
echo -e "\n${YELLOW}Test 6: Get Orders (Protected)${NC}"
curl -s -X GET "$API_URL/orders" \
  -H "Authorization: Bearer $LOGIN_TOKEN" \
  -H "Content-Type: application/json" | jq . | head -30

# Test 7: Create a dish (admin)
echo -e "\n${YELLOW}Test 7: Create Dish (Admin)${NC}"
CREATE_DISH=$(curl -s -X POST "$API_URL/dishes" \
  -H "Authorization: Bearer $LOGIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Pastilla Test",
    "description": "Pastilla test from API",
    "price": 85,
    "category_id": 1,
    "image": ""
  }')

echo "$CREATE_DISH" | jq .

DISH_ID=$(echo "$CREATE_DISH" | jq -r '.id // .data.id // empty')

# Test 8: Test 401 protection
echo -e "\n${YELLOW}Test 8: Test 401 Protection (Invalid Token)${NC}"
curl -s -X GET "$API_URL/orders" \
  -H "Authorization: Bearer invalid_token" \
  -H "Content-Type: application/json" | jq .

# Test 9: Logout
echo -e "\n${YELLOW}Test 9: Logout${NC}"
LOGOUT_RESPONSE=$(curl -s -X POST "$API_URL/logout" \
  -H "Authorization: Bearer $LOGIN_TOKEN" \
  -H "Content-Type: application/json")

echo "$LOGOUT_RESPONSE" | jq .

# Test 10: Try accessing protected route after logout
echo -e "\n${YELLOW}Test 10: Access Protected Route After Logout${NC}"
curl -s -X GET "$API_URL/orders" \
  -H "Authorization: Bearer $LOGIN_TOKEN" \
  -H "Content-Type: application/json" | jq .

echo -e "\n${GREEN}✅ All tests completed!${NC}\n"
