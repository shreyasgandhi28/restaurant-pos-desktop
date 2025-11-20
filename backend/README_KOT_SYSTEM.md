# Restaurant POS - KOT System

## 🎯 Overview

This POS system now includes a complete **Kitchen Order Ticket (KOT)** implementation that allows:

- ✅ **Incremental ordering** - Add items to a table multiple times
- ✅ **Individual KOT printing** - Each batch gets its own kitchen ticket
- ✅ **Ongoing order tracking** - View all orders and KOTs for active tables
- ✅ **Flexible billing** - Complete orders and generate consolidated bills
- ✅ **Kitchen workflow** - Track order status from pending to served

---

## 📚 Documentation Files

| File | Description |
|------|-------------|
| `KOT_SYSTEM_GUIDE.md` | Complete guide with examples and workflows |
| `API_QUICK_REFERENCE.md` | Quick API endpoint reference |
| `TESTING_GUIDE.md` | Step-by-step testing instructions |
| `KOT_IMPLEMENTATION_SUMMARY.md` | Technical implementation details |

---

## 🚀 Quick Start

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Start Server
```bash
php artisan serve
```

### 3. Test the Flow

**Create first order for Table 1:**
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "restaurant_table_id": 1,
    "items": [
      {"menu_item_id": 1, "quantity": 2, "unit_price": 150.00}
    ]
  }'
```

**Add more items to same table:**
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "restaurant_table_id": 1,
    "items": [
      {"menu_item_id": 2, "quantity": 1, "unit_price": 80.00}
    ]
  }'
```

**View table with all orders:**
```bash
curl -X GET http://localhost:8000/api/tables/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 🔄 Complete Workflow

### For Waiters

1. **Select Table** → `GET /api/tables/{id}`
2. **Add Items** → `POST /api/orders`
3. **Print KOT** → `POST /api/kots/{id}/print`
4. **Add More Items** → `POST /api/orders` (same table)
5. **Print New KOT** → `POST /api/kots/{id}/print`
6. **Complete Order** → `POST /api/orders/{id}/complete`
7. **Generate Bill** → `POST /api/orders/{id}/bill`
8. **Record Payment** → `POST /api/bills/{id}/pay`

### For Kitchen

1. **View Pending KOTs** → `GET /api/kots/pending/all`
2. **Start Preparing** → `PUT /api/kots/{id}/status` (status: preparing)
3. **Mark Ready** → `PUT /api/kots/{id}/status` (status: ready)
4. **Mark Served** → `PUT /api/kots/{id}/status` (status: served)

---

## 📊 Database Schema

### New Tables

**kitchen_order_tickets**
```
id, order_id, kot_number, status, notes, printed_at, created_at, updated_at
```

### Modified Tables

**order_items**
- Added: `kitchen_order_ticket_id` (foreign key)

### Relationships
```
Order (1) ──→ (Many) KitchenOrderTickets
Order (1) ──→ (Many) OrderItems
KitchenOrderTicket (1) ──→ (Many) OrderItems
```

---

## 🎨 Key Features

### 1. Smart Order Management
- Automatically detects if table has active order
- Creates new order or adds to existing
- Recalculates totals on each addition

### 2. Individual KOTs
- Each batch of items gets unique KOT number
- Can be printed separately for kitchen
- Tracks print timestamp

### 3. Kitchen Workflow
- Status tracking: `pending` → `preparing` → `ready` → `served`
- Kitchen display shows all pending KOTs
- Real-time status updates

### 4. Flexible Billing
- Consolidates all KOTs into one bill
- Supports percentage or fixed discounts
- Multiple payment methods (cash, card, UPI, other)
- Table freed only after payment

---

## 📱 API Endpoints Summary

### Tables
```
GET    /api/tables              - List all tables
GET    /api/tables/{id}         - Get table with active orders
PUT    /api/tables/{id}/status  - Update table status
```

### Orders
```
GET    /api/orders              - List all orders
POST   /api/orders              - Create/add to order
GET    /api/orders/{id}         - Get order details
PUT    /api/orders/{id}         - Update order status
POST   /api/orders/{id}/complete - Complete order
DELETE /api/orders/{id}         - Delete order
```

### Kitchen Order Tickets
```
GET    /api/orders/{id}/kots    - List KOTs for order
GET    /api/kots/{id}           - Get KOT details
POST   /api/kots/{id}/print     - Mark as printed
PUT    /api/kots/{id}/status    - Update KOT status
GET    /api/kots/pending/all    - Get all pending KOTs
```

### Bills
```
GET    /api/bills               - List all bills
POST   /api/orders/{id}/bill    - Generate bill
GET    /api/bills/{id}          - Get bill details
POST   /api/bills/{id}/pay      - Pay bill & free table
```

---

## 💡 Example Scenario

**Table 1 - Customer Orders Over 30 Minutes**

| Time  | Action | KOT | Order Total |
|-------|--------|-----|-------------|
| 12:00 | Order Garlic Bread (₹300) | KOT-1 | ₹345 |
| 12:05 | Print KOT-1 for kitchen | KOT-1 | ₹345 |
| 12:15 | Order Coffee (₹80) | KOT-2 | ₹437 |
| 12:20 | Print KOT-2 for kitchen | KOT-2 | ₹437 |
| 12:30 | Order Dessert (₹120) | KOT-3 | ₹575 |
| 12:35 | Print KOT-3 for kitchen | KOT-3 | ₹575 |
| 12:45 | Complete order | All served | ₹575 |
| 12:50 | Generate bill (10% off) | - | ₹517.50 |
| 12:55 | Pay with card | - | PAID ✓ |

**Result:** Table 1 is now available for next customer

---

## ⚙️ Configuration

### Tax & Service Charge
Edit `app/Http/Controllers/Api/OrderController.php`:

```php
$tax = $orderSubtotal * 0.10;           // 10% tax
$serviceCharge = $orderSubtotal * 0.05;  // 5% service charge
```

### KOT Number Format
Edit `app/Models/KitchenOrderTicket.php`:

```php
$kot->kot_number = 'KOT-' . strtoupper(uniqid());
```

---

## 🧪 Testing

Follow the complete testing guide in `TESTING_GUIDE.md` for:
- Step-by-step API testing
- Expected responses
- Success criteria
- Common issues and solutions

---

## 🎯 Frontend Integration

### Display Active Orders
```javascript
const response = await fetch(`/api/tables/${tableId}`);
const table = await response.json();

if (table.orders.length > 0) {
  // Table has active order
  const order = table.orders[0];
  console.log(`Order Total: ${order.total}`);
  
  // Show all KOTs
  order.kitchen_order_tickets.forEach(kot => {
    console.log(`KOT: ${kot.kot_number}`);
    kot.order_items.forEach(item => {
      console.log(`- ${item.menu_item.name} x${item.quantity}`);
    });
  });
}
```

### Add Items (Works for New & Existing)
```javascript
const response = await fetch('/api/orders', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    restaurant_table_id: tableId,
    items: [
      { menu_item_id: 1, quantity: 2, unit_price: 150.00 }
    ]
  })
});

const result = await response.json();
console.log(result.is_new_order ? 'New order' : 'Added to existing');

// Print the KOT
await fetch(`/api/kots/${result.kot.id}/print`, { method: 'POST' });
```

---

## 🔒 Security

All endpoints require authentication:
```
Authorization: Bearer {token}
```

Get token via:
```
POST /api/login
{
  "email": "user@example.com",
  "password": "password"
}
```

---

## 🐛 Troubleshooting

### Issue: Can't add items to table
**Solution:** Check if previous order was completed. System creates new order if previous is served/cancelled.

### Issue: Bill already exists
**Solution:** Each order can only have one bill. Use existing bill or create new order.

### Issue: Table not freed after payment
**Solution:** Ensure you called `/bills/{id}/pay` endpoint, not just updating bill status.

### Issue: KOT not showing items
**Solution:** Ensure you're loading relationships: `KitchenOrderTicket::with('orderItems.menuItem')`

---

## 📈 Benefits

### For Restaurant
- ✅ Faster service with incremental ordering
- ✅ Better kitchen organization with individual KOTs
- ✅ Accurate billing with consolidated totals
- ✅ Improved table turnover tracking

### For Customers
- ✅ Order at their own pace
- ✅ No rush to order everything at once
- ✅ Clear, itemized final bill

### For Kitchen
- ✅ Clear, timestamped order tickets
- ✅ Status tracking for each batch
- ✅ Reduced confusion with multiple orders

---

## 🚀 Next Steps

1. **Read** `KOT_SYSTEM_GUIDE.md` for detailed usage
2. **Test** using `TESTING_GUIDE.md`
3. **Integrate** frontend using API reference
4. **Customize** tax/service charge rates as needed

---

## 📞 Support

For detailed information:
- **System Guide:** `KOT_SYSTEM_GUIDE.md`
- **API Reference:** `API_QUICK_REFERENCE.md`
- **Testing:** `TESTING_GUIDE.md`
- **Implementation:** `KOT_IMPLEMENTATION_SUMMARY.md`

---

## ✅ Implementation Status

- ✅ Database migrations completed
- ✅ Models and relationships configured
- ✅ API endpoints implemented and tested
- ✅ Documentation complete
- ✅ Ready for frontend integration

**System is fully operational and ready to use!**
