# 📡 Restaurant POS - API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication

All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer {your-token-here}
```

---

## Authentication Endpoints

### Register New User
**POST** `/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:** `201 Created`
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "token": "1|abc123xyz..."
}
```

---

### Login
**POST** `/login`

**Request Body:**
```json
{
  "email": "admin@restaurant.com",
  "password": "password"
}
```

**Response:** `200 OK`
```json
{
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@restaurant.com"
  },
  "token": "2|def456uvw..."
}
```

---

### Logout
**POST** `/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "message": "Logged out successfully"
}
```

---

## Menu Endpoints

### Get All Menu Items
**GET** `/menu`

**Query Parameters:**
- `category_id` (optional): Filter by category ID

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
[
  {
    "id": 1,
    "category_id": 1,
    "name": "Caesar Salad",
    "slug": "caesar-salad",
    "description": "Fresh romaine lettuce with Caesar dressing",
    "price": "8.99",
    "image": "menu-items/abc123.jpg",
    "is_available": true,
    "is_featured": false,
    "preparation_time": 15,
    "category": {
      "id": 1,
      "name": "Starters",
      "slug": "starters"
    }
  }
]
```

---

### Get Single Menu Item
**GET** `/menu/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "id": 1,
  "category_id": 1,
  "name": "Caesar Salad",
  "slug": "caesar-salad",
  "description": "Fresh romaine lettuce with Caesar dressing",
  "price": "8.99",
  "image": "menu-items/abc123.jpg",
  "is_available": true,
  "is_featured": false,
  "preparation_time": 15,
  "category": {
    "id": 1,
    "name": "Starters",
    "slug": "starters"
  }
}
```

---

### Get All Categories
**GET** `/categories`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
[
  {
    "id": 1,
    "name": "Starters",
    "slug": "starters",
    "description": "Appetizers and starters",
    "sort_order": 1,
    "is_active": true,
    "menu_items_count": 5
  }
]
```

---

## Table Endpoints

### Get All Tables
**GET** `/tables`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
[
  {
    "id": 1,
    "table_number": "T01",
    "capacity": 4,
    "status": "available",
    "position_x": null,
    "position_y": null,
    "created_at": "2025-10-13T10:00:00.000000Z",
    "updated_at": "2025-10-13T10:00:00.000000Z"
  }
]
```

---

### Get Single Table
**GET** `/tables/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "id": 1,
  "table_number": "T01",
  "capacity": 4,
  "status": "occupied",
  "orders": [
    {
      "id": 1,
      "order_number": "ORD-ABC123",
      "status": "pending",
      "total": "45.50"
    }
  ]
}
```

---

### Update Table Status
**PUT** `/tables/{id}/status`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "status": "available"
}
```

**Valid Status Values:**
- `available`
- `occupied`
- `reserved`

**Response:** `200 OK`
```json
{
  "message": "Table status updated successfully",
  "table": {
    "id": 1,
    "table_number": "T01",
    "capacity": 4,
    "status": "available"
  }
}
```

---

## Order Endpoints

### Get All Orders
**GET** `/orders`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
[
  {
    "id": 1,
    "restaurant_table_id": 1,
    "user_id": 1,
    "order_number": "ORD-ABC123",
    "status": "pending",
    "subtotal": "35.00",
    "tax": "3.50",
    "discount": "0.00",
    "service_charge": "1.75",
    "total": "40.25",
    "notes": "No onions please",
    "restaurant_table": {
      "id": 1,
      "table_number": "T01"
    },
    "order_items": [
      {
        "id": 1,
        "menu_item_id": 1,
        "quantity": 2,
        "unit_price": "8.99",
        "total_price": "17.98",
        "menu_item": {
          "id": 1,
          "name": "Caesar Salad"
        }
      }
    ]
  }
]
```

---

### Create Order
**POST** `/orders`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "restaurant_table_id": 1,
  "items": [
    {
      "menu_item_id": 1,
      "quantity": 2,
      "unit_price": 8.99
    },
    {
      "menu_item_id": 3,
      "quantity": 1,
      "unit_price": 15.99
    }
  ],
  "notes": "No onions please"
}
```

**Response:** `201 Created`
```json
{
  "message": "Order created successfully",
  "order": {
    "id": 1,
    "order_number": "ORD-ABC123",
    "restaurant_table_id": 1,
    "user_id": 1,
    "status": "pending",
    "subtotal": "33.97",
    "tax": "3.40",
    "service_charge": "1.70",
    "total": "39.07",
    "notes": "No onions please",
    "order_items": [
      {
        "id": 1,
        "menu_item_id": 1,
        "quantity": 2,
        "unit_price": "8.99",
        "total_price": "17.98"
      }
    ]
  }
}
```

---

### Get Single Order
**GET** `/orders/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "id": 1,
  "order_number": "ORD-ABC123",
  "restaurant_table_id": 1,
  "user_id": 1,
  "status": "pending",
  "subtotal": "33.97",
  "tax": "3.40",
  "service_charge": "1.70",
  "total": "39.07",
  "notes": "No onions please",
  "restaurant_table": {
    "id": 1,
    "table_number": "T01"
  },
  "user": {
    "id": 1,
    "name": "Admin User"
  },
  "order_items": [
    {
      "id": 1,
      "menu_item_id": 1,
      "quantity": 2,
      "unit_price": "8.99",
      "total_price": "17.98",
      "menu_item": {
        "id": 1,
        "name": "Caesar Salad",
        "price": "8.99"
      }
    }
  ]
}
```

---

### Update Order Status
**PUT** `/orders/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "status": "preparing"
}
```

**Valid Status Values:**
- `pending`
- `preparing`
- `ready`
- `served`
- `cancelled`

**Response:** `200 OK`
```json
{
  "message": "Order updated successfully",
  "order": {
    "id": 1,
    "order_number": "ORD-ABC123",
    "status": "preparing",
    "total": "39.07"
  }
}
```

---

### Delete Order
**DELETE** `/orders/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:** `200 OK`
```json
{
  "message": "Order deleted successfully"
}
```

---

## Error Responses

### Validation Error
**Status:** `422 Unprocessable Entity`
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password field is required."
    ]
  }
}
```

---

### Unauthorized
**Status:** `401 Unauthorized`
```json
{
  "message": "Unauthenticated."
}
```

---

### Not Found
**Status:** `404 Not Found`
```json
{
  "message": "Resource not found."
}
```

---

### Server Error
**Status:** `500 Internal Server Error`
```json
{
  "message": "Server error occurred.",
  "error": "Error details..."
}
```

---

## Rate Limiting

API requests are limited to **60 requests per minute** per user.

When rate limit is exceeded:
**Status:** `429 Too Many Requests`
```json
{
  "message": "Too many requests."
}
```

---

## Example Usage (cURL)

### Complete Workflow Example

**1. Login:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@restaurant.com","password":"password"}'
```

**2. Get Menu:**
```bash
curl http://localhost:8000/api/menu \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**3. Get Available Tables:**
```bash
curl http://localhost:8000/api/tables \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**4. Create Order:**
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "restaurant_table_id": 1,
    "items": [
      {"menu_item_id": 1, "quantity": 2, "unit_price": 8.99}
    ],
    "notes": "Extra sauce"
  }'
```

**5. Update Order Status:**
```bash
curl -X PUT http://localhost:8000/api/orders/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"status":"preparing"}'
```

---

## Postman Collection

Import this base configuration into Postman:

**Base URL:** `http://localhost:8000/api`

**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer {{token}}`

**Environment Variables:**
- `base_url`: `http://localhost:8000/api`
- `token`: (set after login)

---

## WebSocket Support (Future)

Real-time updates for:
- Order status changes
- Table status updates
- New orders notifications

Coming in future versions!

---

**API Version:** 1.0  
**Last Updated:** October 2025
