# API Quick Reference - KOT System

## Authentication
All endpoints require `Authorization: Bearer {token}` header.

---

## Tables

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tables` | List all tables |
| GET | `/api/tables/{id}` | Get table with active orders & KOTs |
| PUT | `/api/tables/{id}/status` | Update table status |

---

## Orders

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/orders` | List all orders |
| POST | `/api/orders` | Create order OR add items to existing order |
| GET | `/api/orders/{id}` | Get order details |
| PUT | `/api/orders/{id}` | Update order status |
| POST | `/api/orders/{id}/complete` | Complete order (ready for billing) |
| DELETE | `/api/orders/{id}` | Delete order |

---

## Kitchen Order Tickets (KOT)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/orders/{order_id}/kots` | Get all KOTs for an order |
| GET | `/api/kots/{id}` | Get KOT details |
| POST | `/api/kots/{id}/print` | Mark KOT as printed |
| PUT | `/api/kots/{id}/status` | Update KOT status |
| GET | `/api/kots/pending/all` | Get all pending KOTs (kitchen view) |

---

## Bills

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/bills` | List all bills |
| POST | `/api/orders/{order_id}/bill` | Generate bill for order |
| GET | `/api/bills/{id}` | Get bill details |
| POST | `/api/bills/{id}/pay` | Mark bill as paid & free table |

---

## Common Request Bodies

### Create/Add Order
```json
POST /api/orders
{
  "restaurant_table_id": 1,
  "items": [
    {
      "menu_item_id": 5,
      "quantity": 2,
      "unit_price": 150.00,
      "special_instructions": "Extra spicy"
    }
  ],
  "notes": "Optional order notes"
}
```

### Update KOT Status
```json
PUT /api/kots/{id}/status
{
  "status": "preparing"
}
```
**Valid statuses:** `pending`, `preparing`, `ready`, `served`

### Generate Bill
```json
POST /api/orders/{id}/bill
{
  "discount_percentage": 10,
  "discount_amount": null
}
```

### Pay Bill
```json
POST /api/bills/{id}/pay
{
  "payment_method": "cash"
}
```
**Valid methods:** `cash`, `card`, `upi`, `other`

---

## Response Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 404 | Not Found |
| 500 | Server Error |

---

## Workflow Summary

```
1. POST /api/orders → Create order + KOT #1
2. POST /api/kots/{id}/print → Print KOT #1 for kitchen
3. POST /api/orders → Add items (creates KOT #2)
4. POST /api/kots/{id}/print → Print KOT #2
5. POST /api/orders/{id}/complete → Mark order complete
6. POST /api/orders/{id}/bill → Generate bill
7. POST /api/bills/{id}/pay → Pay & free table
```
