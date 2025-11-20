# POS System - User Guide

## ✅ KOT System Now Fully Functional!

The POS page now supports the complete KOT workflow you requested.

---

## 🎯 How to Use

### 1. **Select a Table**
- Go to `/pos` page
- Select ANY table from the dropdown (available or occupied)
- If table is **occupied**, you'll see all ongoing orders and KOTs
- If table is **available**, you can start a new order

### 2. **View Ongoing Orders** (For Occupied Tables)
When you select an occupied table, you'll see:
- **Order Number** and **Status**
- **All KOTs** with their items
- **Current Total** amount
- **Print buttons** for unprinted KOTs
- **Complete Order** button
- **Generate Bill** button (after order is completed)

### 3. **Add Items to Order**
- Browse menu items or filter by category
- Click on items to add to cart
- Adjust quantities with +/- buttons
- Add special instructions in the notes field
- Click **"Add Items to Order"** or **"Place Order"**

### 4. **Print KOT**
After adding items:
- A popup will ask "Do you want to print the KOT now?"
- Click **Yes** to open print dialog
- KOT shows:
  - Table number
  - KOT number
  - Time
  - Items with quantities
  - Special instructions
- Give printed KOT to kitchen

### 5. **Add More Items** (Incremental Ordering)
- Keep the same table selected
- Add more items from menu
- Click **"Add Items to Order"** again
- New KOT is created automatically
- Print the new KOT for kitchen

### 6. **Complete Order**
When customer is done ordering:
- Click **"Complete Order"** button
- Confirms all items are served
- Prepares order for billing

### 7. **Generate Bill**
After order is completed:
- Click **"Generate Bill"** button
- Enter discount percentage (optional)
- Bill is generated and you're redirected to bill page
- Customer can pay from bill page

---

## 📋 Complete Workflow Example

### Scenario: Table 5 - Customer Orders in 3 Batches

**Step 1: First Order (12:00 PM)**
1. Select "Table 5" from dropdown
2. Add "Garlic Bread" (2x) to cart
3. Click "Place Order"
4. Popup: "Order created successfully! KOT Number: KOT-ABC123"
5. Click "Yes" to print KOT
6. KOT prints → Give to kitchen
7. Table 5 status → **Occupied**

**Step 2: View Ongoing Order**
1. Table 5 still selected
2. See "Ongoing Orders" section showing:
   - Order #ORD-XYZ789
   - KOT-ABC123 with Garlic Bread x2
   - Current Total: ₹345.00

**Step 3: Add More Items (12:15 PM)**
1. Table 5 still selected
2. Add "Coffee" (1x) to cart
3. Click "Add Items to Order"
4. Popup: "Items added to order! KOT Number: KOT-DEF456"
5. Click "Yes" to print KOT
6. New KOT prints → Give to kitchen
7. Now see 2 KOTs in ongoing orders
8. Current Total updated: ₹437.00

**Step 4: Add Third Batch (12:30 PM)**
1. Table 5 still selected
2. Add "Chocolate Cake" (1x) to cart
3. Click "Add Items to Order"
4. New KOT created and printed
5. Now see 3 KOTs total
6. Current Total: ₹575.00

**Step 5: Complete Order (12:45 PM)**
1. Customer done ordering
2. Click "Complete Order" button
3. Confirm: "Yes"
4. All KOTs marked as served
5. "Generate Bill" button appears

**Step 6: Generate Bill (12:50 PM)**
1. Click "Generate Bill"
2. Enter discount: "10" (10% off)
3. Bill generated
4. Redirected to bill page
5. Final amount: ₹517.50 (after discount)

**Step 7: Payment**
1. On bill page, select payment method
2. Click "Mark as Paid"
3. Table 5 → **Available** again

---

## 🖨️ KOT Print Features

Each KOT shows:
```
KITCHEN ORDER TICKET
KOT-ABC123

Table: T5
Time: 10/14/2025, 12:00:00 PM
Order: ORD-XYZ789

Qty | Item          | Instructions
----|---------------|-------------
 2  | Garlic Bread  | Extra spicy
 1  | Coffee        | -
```

---

## 💡 Key Features

### ✅ Incremental Ordering
- Add items multiple times to same table
- Each batch creates new KOT
- Order total updates automatically

### ✅ Print KOTs Individually
- Print button for each unprinted KOT
- Opens in new window for printing
- Marks KOT as printed with timestamp

### ✅ View Ongoing Orders
- See all KOTs for active table
- Color-coded status badges
- Real-time total calculation

### ✅ Complete & Bill
- Complete order when done
- Generate bill with discount
- Redirect to payment page

### ✅ Table Status
- Available tables: Green
- Occupied tables: Can still add items
- Automatic status updates

---

## 🎨 UI Elements

### Table Selection
- Dropdown shows all tables
- Status indicator for each table
- Can select any table (not just available)

### Ongoing Orders Section
- Shows when occupied table selected
- Lists all KOTs with items
- Print buttons for unprinted KOTs
- Status badges (pending/preparing/ready/served)
- Complete Order button
- Generate Bill button (after completion)

### Cart Section
- Add/remove items
- Adjust quantities
- See subtotal, tax, service charge
- Button text changes:
  - "Place Order" (new table)
  - "Add Items to Order" (occupied table)

---

## 🔧 Technical Details

### API Integration
- Uses `/api/tables/{id}` to load ongoing orders
- Uses `/api/orders` to create/add items
- Uses `/api/kots/{id}/print` to print KOTs
- Uses `/api/orders/{id}/complete` to complete
- Uses `/api/orders/{id}/bill` to generate bill

### Authentication
- Auto-generates API token for current user
- Includes in all API requests
- No manual token management needed

### Real-time Updates
- Reloads table data after each action
- Updates ongoing orders display
- Refreshes KOT list automatically

---

## 📱 Access the POS

**URL:** `/pos`

**Requirements:**
- Must be logged in
- User must have POS access permissions

---

## ✅ What's Working Now

1. ✅ Select any table (available or occupied)
2. ✅ View ongoing orders for occupied tables
3. ✅ Add items to new or existing orders
4. ✅ Print individual KOTs
5. ✅ See all KOTs with status
6. ✅ Complete orders
7. ✅ Generate bills with discount
8. ✅ Automatic table status management

---

## 🎉 You're All Set!

The POS system now works exactly as you described:
- Order garlic bread → Print KOT
- Order coffee later → Print new KOT
- Complete order → Generate bill
- See ongoing orders anytime

**Go to `/pos` and try it out!**
