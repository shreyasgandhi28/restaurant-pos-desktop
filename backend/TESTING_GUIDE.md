# Testing Guide - KOT System

## Prerequisites
1. Database migrated: `php artisan migrate`
2. Server running: `php artisan serve`
3. User registered and authenticated

---

## Test Scenario: Table 1 - Multiple Orders

### Setup
Ensure you have:
- At least one table (ID: 1)
- At least two menu items (IDs: 1, 2)
- Valid auth token

---

## Step-by-Step Test

### 1. Check Available Tables
```bash
curl -X GET http://localhost:8000/api/tables \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected:** List of tables with status `available`

---

### 2. Create First Order (Garlic Bread)
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "restaurant_table_id": 1,
    "items": [
      {
        "menu_item_id": 1,
        "quantity": 2,
        "unit_price": 150.00,
        "special_instructions": "Extra garlic"
      }
    ],
    "notes": "First order"
  }'
```

**Expected Response:**
```json
{
  "message": "Order created successfully",
  "is_new_order": true,
  "order": {
    "id": 1,
    "order_number": "ORD-...",
    "subtotal": 300.00,
    "tax": 30.00,
    "service_charge": 15.00,
    "total": 345.00
  },
  "kot": {
    "id": 1,
    "kot_number": "KOT-...",
    "status": "pending"
  }
}
```

**Verify:**
- ✅ Order created
- ✅ KOT created
- ✅ Table status → `occupied`

---

### 3. Print KOT #1
```bash
curl -X POST http://localhost:8000/api/kots/1/print \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected:**
```json
{
  "message": "KOT marked as printed",
  "kot": {
    "printed_at": "2025-10-13 13:00:00",
    "order_items": [...]
  }
}
```

---

### 4. Check Table Status
```bash
curl -X GET http://localhost:8000/api/tables/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected:**
```json
{
  "id": 1,
  "status": "occupied",
  "orders": [
    {
      "id": 1,
      "kitchen_order_tickets": [
        {
          "id": 1,
          "kot_number": "KOT-...",
          "printed_at": "2025-10-13 13:00:00",
          "order_items": [...]
        }
      ]
    }
  ]
}
```

**Verify:**
- ✅ Table shows active order
- ✅ KOT visible with items
- ✅ KOT marked as printed

---

### 5. Add Second Order (Coffee) - Same Table
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "restaurant_table_id": 1,
    "items": [
      {
        "menu_item_id": 2,
        "quantity": 1,
        "unit_price": 80.00
      }
    ],
    "notes": "Second order - coffee"
  }'
```

**Expected Response:**
```json
{
  "message": "Items added to existing order",
  "is_new_order": false,
  "order": {
    "id": 1,
    "subtotal": 380.00,
    "tax": 38.00,
    "service_charge": 19.00,
    "total": 437.00
  },
  "kot": {
    "id": 2,
    "kot_number": "KOT-..."
  }
}
```

**Verify:**
- ✅ Same order ID (1)
- ✅ New KOT created (ID: 2)
- ✅ Order total updated
- ✅ `is_new_order: false`

---

### 6. Print KOT #2
```bash
curl -X POST http://localhost:8000/api/kots/2/print \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

### 7. View All KOTs for Order
```bash
curl -X GET http://localhost:8000/api/orders/1/kots \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected:**
```json
[
  {
    "id": 1,
    "kot_number": "KOT-...",
    "order_items": [...]
  },
  {
    "id": 2,
    "kot_number": "KOT-...",
    "order_items": [...]
  }
]
```

**Verify:**
- ✅ Two KOTs returned
- ✅ Each has different items

---

### 8. Update KOT Status (Kitchen)
```bash
curl -X PUT http://localhost:8000/api/kots/1/status \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "preparing"
  }'
```

**Expected:**
```json
{
  "message": "KOT status updated successfully",
  "kot": {
    "status": "preparing"
  }
}
```

---

### 9. Get Pending KOTs (Kitchen Display)
```bash
curl -X GET http://localhost:8000/api/kots/pending/all \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected:** List of KOTs with status `pending` or `preparing`

---

### 10. Complete Order
```bash
curl -X POST http://localhost:8000/api/orders/1/complete \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected:**
```json
{
  "message": "Order completed successfully. Ready for billing.",
  "order": {
    "status": "served"
  }
}
```

**Verify:**
- ✅ Order status → `served`
- ✅ All KOTs → `served`
- ✅ Table still `occupied`

---

### 11. Generate Bill
```bash
curl -X POST http://localhost:8000/api/orders/1/bill \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "discount_percentage": 10
  }'
```

**Expected:**
```json
{
  "message": "Bill generated successfully",
  "bill": {
    "bill_number": "BILL-...",
    "subtotal": 380.00,
    "tax_amount": 38.00,
    "service_charge": 19.00,
    "discount_amount": 38.00,
    "total_amount": 399.00,
    "status": "pending"
  }
}
```

**Verify:**
- ✅ Bill created
- ✅ Discount applied
- ✅ Total calculated correctly

---

### 12. Pay Bill
```bash
curl -X POST http://localhost:8000/api/bills/1/pay \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "payment_method": "card"
  }'
```

**Expected:**
```json
{
  "message": "Payment recorded successfully",
  "bill": {
    "status": "paid",
    "paid_at": "2025-10-13 13:30:00",
    "payment_method": "card"
  }
}
```

**Verify:**
- ✅ Bill status → `paid`
- ✅ Table status → `available`

---

### 13. Verify Table is Free
```bash
curl -X GET http://localhost:8000/api/tables/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected:**
```json
{
  "id": 1,
  "status": "available",
  "orders": []
}
```

---

## Success Criteria

✅ **Incremental Ordering:** Can add items to existing table order  
✅ **Multiple KOTs:** Each batch creates separate KOT  
✅ **KOT Printing:** Can mark KOTs as printed  
✅ **Status Tracking:** KOT status updates work  
✅ **Total Calculation:** Order totals recalculate correctly  
✅ **Billing:** Bill generation with discounts  
✅ **Payment:** Table freed after payment  

---

## Common Issues

### Issue: "Bill already exists for this order"
**Solution:** Each order can only have one bill. Delete the existing bill or create a new order.

### Issue: "No active order for table"
**Solution:** The order was completed/cancelled. Create a new order.

### Issue: Table not freed after payment
**Solution:** Ensure you called `/bills/{id}/pay`, not just updating the bill status.

---

## Database Verification

```sql
-- Check orders
SELECT * FROM orders WHERE restaurant_table_id = 1;

-- Check KOTs
SELECT * FROM kitchen_order_tickets WHERE order_id = 1;

-- Check order items
SELECT * FROM order_items WHERE order_id = 1;

-- Check bills
SELECT * FROM bills WHERE order_id = 1;
```
