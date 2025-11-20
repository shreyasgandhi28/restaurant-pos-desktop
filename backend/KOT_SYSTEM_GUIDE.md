# Kitchen Order Ticket (KOT) System Guide

## Overview
The KOT system allows incremental ordering for restaurant tables with the following workflow:
1. **Add items incrementally** - Continue adding orders to the same table
2. **Print KOTs** - Each batch of items gets a KOT that can be printed for the kitchen
3. **Track order status** - Monitor all ongoing orders for a table
4. **Complete & Bill** - Finalize the order and generate the bill

---

## Database Structure

### Tables
- **orders** - Main order for a table (one active order per table)
- **kitchen_order_tickets** - Individual KOTs (multiple per order)
- **order_items** - Items linked to both order and KOT
- **bills** - Final bill generated from completed order

### Relationships
```
Order (1) -> (Many) KitchenOrderTickets
Order (1) -> (Many) OrderItems
KitchenOrderTicket (1) -> (Many) OrderItems
Order (1) -> (1) Bill
```

---

## API Endpoints

### 1. Create Order / Add Items to Existing Order
**POST** `/api/orders`

**Request:**
```json
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
  "notes": "First order"
}
```

**Response:**
```json
{
  "message": "Order created successfully",
  "order": { ... },
  "kot": { ... },
  "is_new_order": true
}
```

**Behavior:**
- If no active order exists for the table → Creates new order
- If active order exists → Adds items to existing order
- Always creates a new KOT for the items
- Automatically recalculates order totals

---

### 2. View Table with Ongoing Orders
**GET** `/api/tables/{table_id}`

**Response:**
```json
{
  "id": 1,
  "table_number": "T1",
  "status": "occupied",
  "orders": [
    {
      "id": 1,
      "order_number": "ORD-ABC123",
      "status": "pending",
      "total": 450.00,
      "kitchen_order_tickets": [
        {
          "id": 1,
          "kot_number": "KOT-XYZ789",
          "status": "pending",
          "printed_at": null,
          "order_items": [...]
        }
      ]
    }
  ]
}
```

---

### 3. Print KOT (for Kitchen)
**POST** `/api/kots/{kot_id}/print`

**Response:**
```json
{
  "message": "KOT marked as printed",
  "kot": {
    "id": 1,
    "kot_number": "KOT-XYZ789",
    "printed_at": "2025-10-13 13:00:00",
    "order_items": [
      {
        "menu_item": {
          "name": "Garlic Bread",
          "price": 150.00
        },
        "quantity": 2,
        "special_instructions": "Extra spicy"
      }
    ],
    "order": {
      "restaurant_table": {
        "table_number": "T1"
      }
    }
  }
}
```

---

### 4. Update KOT Status (Kitchen Workflow)
**PUT** `/api/kots/{kot_id}/status`

**Request:**
```json
{
  "status": "preparing"
}
```

**Statuses:**
- `pending` - Just created
- `preparing` - Kitchen is working on it
- `ready` - Ready to serve
- `served` - Delivered to customer

---

### 5. Get All Pending KOTs (Kitchen Display)
**GET** `/api/kots/pending/all`

Returns all KOTs with status `pending` or `preparing`.

---

### 6. Complete Order (Ready for Billing)
**POST** `/api/orders/{order_id}/complete`

**Response:**
```json
{
  "message": "Order completed successfully. Ready for billing.",
  "order": { ... }
}
```

**Behavior:**
- Marks all KOTs as `served`
- Sets order status to `served`
- Table remains occupied until bill is paid

---

### 7. Generate Bill
**POST** `/api/orders/{order_id}/bill`

**Request:**
```json
{
  "discount_percentage": 10,
  "discount_amount": null
}
```

**Response:**
```json
{
  "message": "Bill generated successfully",
  "bill": {
    "bill_number": "BILL-ABC123",
    "subtotal": 300.00,
    "tax_amount": 30.00,
    "service_charge": 15.00,
    "discount_amount": 30.00,
    "total_amount": 315.00,
    "status": "pending"
  }
}
```

---

### 8. Pay Bill (Free Table)
**POST** `/api/bills/{bill_id}/pay`

**Request:**
```json
{
  "payment_method": "cash"
}
```

**Payment Methods:**
- `cash`
- `card`
- `upi`
- `other`

**Behavior:**
- Marks bill as `paid`
- Frees the table (status → `available`)

---

## Workflow Example

### Scenario: Table 1 Orders Multiple Times

#### Step 1: First Order (Garlic Bread)
```bash
POST /api/orders
{
  "restaurant_table_id": 1,
  "items": [
    {"menu_item_id": 5, "quantity": 1, "unit_price": 150.00}
  ]
}
```
- Creates Order #1
- Creates KOT #1 with Garlic Bread
- Table status → `occupied`

#### Step 2: Print KOT for Kitchen
```bash
POST /api/kots/1/print
```
- Kitchen receives KOT #1
- Starts preparing Garlic Bread

#### Step 3: Customer Orders Coffee (Same Table)
```bash
POST /api/orders
{
  "restaurant_table_id": 1,
  "items": [
    {"menu_item_id": 12, "quantity": 1, "unit_price": 80.00}
  ]
}
```
- Adds to existing Order #1
- Creates KOT #2 with Coffee
- Order total recalculated

#### Step 4: Print Second KOT
```bash
POST /api/kots/2/print
```
- Kitchen receives KOT #2

#### Step 5: View Table Status
```bash
GET /api/tables/1
```
- Shows Order #1 with 2 KOTs
- Total: ₹230 + tax + service charge

#### Step 6: Complete Order
```bash
POST /api/orders/1/complete
```
- All KOTs marked as `served`
- Order ready for billing

#### Step 7: Generate Bill
```bash
POST /api/orders/1/bill
{
  "discount_percentage": 0
}
```
- Creates Bill with final amount

#### Step 8: Customer Pays
```bash
POST /api/bills/1/pay
{
  "payment_method": "card"
}
```
- Bill marked as `paid`
- Table 1 → `available`

---

## Key Features

✅ **Incremental Ordering** - Add items to active table orders  
✅ **Individual KOTs** - Each batch gets its own ticket  
✅ **Kitchen Tracking** - Monitor KOT status (pending → preparing → ready → served)  
✅ **Auto Totals** - Order totals recalculated on each addition  
✅ **Table Management** - Tables freed only after payment  
✅ **Flexible Billing** - Support for discounts and multiple payment methods  

---

## Tax & Charges Configuration

Current settings (in `OrderController`):
- **Tax:** 10% of subtotal
- **Service Charge:** 5% of subtotal

To modify, edit the `store()` method in:
`app/Http/Controllers/Api/OrderController.php`

```php
$tax = $orderSubtotal * 0.10;  // Change 0.10 for different tax rate
$serviceCharge = $orderSubtotal * 0.05;  // Change 0.05 for different service charge
```

---

## Frontend Integration Tips

### Display Active Orders for a Table
```javascript
// When user selects Table 1
const response = await fetch('/api/tables/1');
const table = await response.json();

// Show all KOTs for this table
table.orders[0].kitchen_order_tickets.forEach(kot => {
  console.log(`KOT: ${kot.kot_number}`);
  kot.order_items.forEach(item => {
    console.log(`- ${item.menu_item.name} x${item.quantity}`);
  });
});
```

### Add New Items to Active Table
```javascript
// User adds Coffee to Table 1 (which already has an order)
const response = await fetch('/api/orders', {
  method: 'POST',
  body: JSON.stringify({
    restaurant_table_id: 1,
    items: [
      { menu_item_id: 12, quantity: 1, unit_price: 80.00 }
    ]
  })
});

const result = await response.json();
if (!result.is_new_order) {
  console.log('Added to existing order');
}

// Print the new KOT
await fetch(`/api/kots/${result.kot.id}/print`, { method: 'POST' });
```

---

## Notes

- Only one active order per table at a time
- Multiple KOTs can exist per order
- KOTs are printed individually but billed together
- Table is freed only after bill payment
- All amounts are in decimal(10,2) format
