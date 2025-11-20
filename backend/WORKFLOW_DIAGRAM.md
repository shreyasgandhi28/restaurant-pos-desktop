# KOT System Workflow Diagram

## Visual Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                         TABLE SELECTION                          │
│                    GET /api/tables/{id}                          │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
                    ┌────────────────┐
                    │ Table Status?  │
                    └────────┬───────┘
                             │
                ┌────────────┴────────────┐
                │                         │
                ▼                         ▼
        ┌──────────────┐          ┌──────────────┐
        │  AVAILABLE   │          │   OCCUPIED   │
        │ (No Orders)  │          │ (Has Orders) │
        └──────┬───────┘          └──────┬───────┘
               │                         │
               │                         ▼
               │              ┌─────────────────────┐
               │              │ Show Active Orders  │
               │              │   & All KOTs        │
               │              └──────┬──────────────┘
               │                     │
               │                     ▼
               │              ┌─────────────────────┐
               │              │  "Add More Items"   │
               │              └──────┬──────────────┘
               │                     │
               └─────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                      CREATE/ADD ORDER                            │
│                    POST /api/orders                              │
│  { restaurant_table_id, items: [...] }                          │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
                    ┌────────────────┐
                    │ Order Exists?  │
                    └────────┬───────┘
                             │
                ┌────────────┴────────────┐
                │                         │
                ▼                         ▼
        ┌──────────────┐          ┌──────────────┐
        │   NEW ORDER  │          │  ADD TO      │
        │  Create New  │          │  EXISTING    │
        └──────┬───────┘          └──────┬───────┘
               │                         │
               └─────────────┬───────────┘
                             │
                             ▼
                    ┌────────────────┐
                    │  CREATE KOT    │
                    │  (New Batch)   │
                    └────────┬───────┘
                             │
                             ▼
                    ┌────────────────┐
                    │ Recalculate    │
                    │ Order Totals   │
                    └────────┬───────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                        PRINT KOT                                 │
│                  POST /api/kots/{id}/print                       │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
                    ┌────────────────┐
                    │  KOT Printed   │
                    │  (Timestamp)   │
                    └────────┬───────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                      KITCHEN WORKFLOW                            │
│                  PUT /api/kots/{id}/status                       │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
        ┌────────────────────────────────────┐
        │  pending → preparing → ready       │
        └────────────────┬───────────────────┘
                         │
                         ▼
                ┌────────────────┐
                │ Items Served   │
                └────────┬───────┘
                         │
                         ▼
        ┌────────────────────────────────┐
        │  Customer Wants More Items?    │
        └────────┬───────────────────────┘
                 │
        ┌────────┴────────┐
        │                 │
        ▼                 ▼
    ┌───────┐      ┌──────────┐
    │  YES  │      │    NO    │
    └───┬───┘      └─────┬────┘
        │                │
        │                ▼
        │      ┌─────────────────┐
        │      │ Ready for Bill? │
        │      └─────┬───────────┘
        │            │
        └────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│                      COMPLETE ORDER                              │
│              POST /api/orders/{id}/complete                      │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
                    ┌────────────────┐
                    │ All KOTs →     │
                    │   "served"     │
                    └────────┬───────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                      GENERATE BILL                               │
│              POST /api/orders/{id}/bill                          │
│              { discount_percentage: 10 }                         │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
                    ┌────────────────┐
                    │  Bill Created  │
                    │  - Subtotal    │
                    │  - Tax (10%)   │
                    │  - Service (5%)│
                    │  - Discount    │
                    │  = Total       │
                    └────────┬───────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                        PAY BILL                                  │
│              POST /api/bills/{id}/pay                            │
│              { payment_method: "cash" }                          │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
                    ┌────────────────┐
                    │ Bill → "paid"  │
                    └────────┬───────┘
                             │
                             ▼
                    ┌────────────────┐
                    │ Table → FREE   │
                    │  "available"   │
                    └────────────────┘
```

---

## State Transitions

### Table States
```
available → occupied → available
    ↑          ↓           ↑
    └──────────┴───────────┘
```

### Order States
```
pending → preparing → ready → served
                              ↓
                          cancelled
```

### KOT States
```
pending → preparing → ready → served
```

### Bill States
```
pending → paid
```

---

## Data Relationships

```
RestaurantTable (1) ──────────────────┐
                                      │
                                      ▼
                              Order (1) ────────────┐
                                │                   │
                                │                   ▼
                                │              Bill (1)
                                │
                                ▼
                    KitchenOrderTicket (Many)
                                │
                                ▼
                         OrderItem (Many)
                                │
                                ▼
                          MenuItem (1)
```

---

## Example Timeline

```
Time    Action                          KOTs    Order Total
─────────────────────────────────────────────────────────────
12:00   Customer sits at Table 1        -       -
12:05   Order Garlic Bread              KOT-1   ₹345
12:10   Print KOT-1 for kitchen         KOT-1   ₹345
12:20   Order Coffee                    KOT-2   ₹437
12:25   Print KOT-2 for kitchen         KOT-2   ₹437
12:40   Order Dessert                   KOT-3   ₹575
12:45   Print KOT-3 for kitchen         KOT-3   ₹575
13:00   Complete Order                  All     ₹575
13:05   Generate Bill (10% discount)    -       ₹517.50
13:10   Pay Bill (Card)                 -       PAID
13:10   Table 1 → Available             -       -
```

---

## Kitchen Display Flow

```
┌─────────────────────────────────────┐
│      KITCHEN DISPLAY SCREEN         │
│   GET /api/kots/pending/all         │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  KOT-1 | Table 1 | 12:10 | PENDING  │
│  - Garlic Bread x2                  │
│    "Extra garlic"                   │
│  [Start Preparing]                  │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  KOT-2 | Table 1 | 12:25 | PREPARING│
│  - Coffee x1                        │
│  [Mark Ready]                       │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  KOT-3 | Table 1 | 12:45 | READY    │
│  - Chocolate Cake x1                │
│  [Mark Served]                      │
└─────────────────────────────────────┘
```

---

## Error Handling

### Scenario: Try to add items to completed order
```
POST /api/orders
{ restaurant_table_id: 1, items: [...] }

Response: 201 Created
→ Creates NEW order (previous was completed)
→ is_new_order: true
```

### Scenario: Try to generate bill twice
```
POST /api/orders/1/bill

Response: 400 Bad Request
{
  "message": "Bill already exists for this order"
}
```

### Scenario: Try to add items to cancelled order
```
POST /api/orders
{ restaurant_table_id: 1, items: [...] }

Response: 201 Created
→ Creates NEW order
→ Previous cancelled order ignored
```

---

## Integration Checklist

Frontend developers should implement:

- [ ] Table selection view
- [ ] Active order display with all KOTs
- [ ] Add items form (works for new & existing orders)
- [ ] KOT print view/modal
- [ ] Kitchen display screen
- [ ] KOT status update buttons
- [ ] Order completion confirmation
- [ ] Bill generation with discount input
- [ ] Payment method selection
- [ ] Receipt/bill print view

---

## Performance Considerations

1. **Eager Loading**
   - Always load relationships to avoid N+1 queries
   - Example: `Order::with('kitchenOrderTickets.orderItems.menuItem')`

2. **Caching**
   - Cache menu items (rarely change)
   - Cache table list (rarely change)
   - Don't cache orders/KOTs (frequently change)

3. **Real-time Updates**
   - Consider WebSockets for kitchen display
   - Polling interval: 5-10 seconds for order updates

4. **Indexing**
   - Index `restaurant_table_id` in orders table
   - Index `order_id` in kitchen_order_tickets table
   - Index `status` fields for filtering
