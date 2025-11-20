# Frontend Implementation Examples

## React/Vue Component Examples

### 1. Table Selection Component

```javascript
// TableGrid.jsx
import { useState, useEffect } from 'react';

function TableGrid() {
  const [tables, setTables] = useState([]);

  useEffect(() => {
    fetchTables();
  }, []);

  const fetchTables = async () => {
    const response = await fetch('/api/tables', {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    const data = await response.json();
    setTables(data);
  };

  const selectTable = async (tableId) => {
    const response = await fetch(`/api/tables/${tableId}`, {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    const table = await response.json();
    
    // Navigate to order screen with table data
    navigateToOrderScreen(table);
  };

  return (
    <div className="table-grid">
      {tables.map(table => (
        <div 
          key={table.id}
          className={`table-card ${table.status}`}
          onClick={() => selectTable(table.id)}
        >
          <h3>{table.table_number}</h3>
          <span className="status">{table.status}</span>
          <span className="capacity">Seats: {table.capacity}</span>
        </div>
      ))}
    </div>
  );
}
```

**CSS:**
```css
.table-card {
  padding: 20px;
  border-radius: 8px;
  cursor: pointer;
  transition: transform 0.2s;
}

.table-card.available {
  background: #10b981;
  color: white;
}

.table-card.occupied {
  background: #ef4444;
  color: white;
}

.table-card:hover {
  transform: scale(1.05);
}
```

---

### 2. Order Management Component

```javascript
// OrderScreen.jsx
import { useState, useEffect } from 'react';

function OrderScreen({ table }) {
  const [menuItems, setMenuItems] = useState([]);
  const [cart, setCart] = useState([]);
  const [activeOrder, setActiveOrder] = useState(null);

  useEffect(() => {
    fetchMenuItems();
    loadActiveOrder();
  }, [table.id]);

  const fetchMenuItems = async () => {
    const response = await fetch('/api/menu', {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    const data = await response.json();
    setMenuItems(data);
  };

  const loadActiveOrder = () => {
    // Check if table has active order
    if (table.orders && table.orders.length > 0) {
      setActiveOrder(table.orders[0]);
    }
  };

  const addToCart = (menuItem) => {
    const existing = cart.find(item => item.menu_item_id === menuItem.id);
    if (existing) {
      setCart(cart.map(item => 
        item.menu_item_id === menuItem.id 
          ? { ...item, quantity: item.quantity + 1 }
          : item
      ));
    } else {
      setCart([...cart, {
        menu_item_id: menuItem.id,
        quantity: 1,
        unit_price: menuItem.price,
        name: menuItem.name
      }]);
    }
  };

  const submitOrder = async () => {
    if (cart.length === 0) return;

    const response = await fetch('/api/orders', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        restaurant_table_id: table.id,
        items: cart.map(item => ({
          menu_item_id: item.menu_item_id,
          quantity: item.quantity,
          unit_price: item.unit_price
        }))
      })
    });

    const result = await response.json();
    
    // Print KOT
    await printKOT(result.kot.id);
    
    // Clear cart and reload
    setCart([]);
    setActiveOrder(result.order);
    
    alert(result.is_new_order 
      ? 'Order created successfully!' 
      : 'Items added to order!'
    );
  };

  const printKOT = async (kotId) => {
    const response = await fetch(`/api/kots/${kotId}/print`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}` }
    });
    const kot = await response.json();
    
    // Open print dialog or send to printer
    openKOTPrintDialog(kot.kot);
  };

  return (
    <div className="order-screen">
      {/* Active Order Display */}
      {activeOrder && (
        <div className="active-order">
          <h2>Active Order - {table.table_number}</h2>
          <p>Order #: {activeOrder.order_number}</p>
          <p>Total: ₹{activeOrder.total}</p>
          
          <div className="kots">
            <h3>Kitchen Order Tickets</h3>
            {activeOrder.kitchen_order_tickets.map(kot => (
              <div key={kot.id} className="kot-card">
                <h4>{kot.kot_number}</h4>
                <span className={`status ${kot.status}`}>{kot.status}</span>
                <ul>
                  {kot.order_items.map(item => (
                    <li key={item.id}>
                      {item.menu_item.name} x{item.quantity}
                      {item.special_instructions && (
                        <span className="note">({item.special_instructions})</span>
                      )}
                    </li>
                  ))}
                </ul>
                {!kot.printed_at && (
                  <button onClick={() => printKOT(kot.id)}>Print KOT</button>
                )}
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Menu Items */}
      <div className="menu-section">
        <h2>Menu</h2>
        <div className="menu-grid">
          {menuItems.map(item => (
            <div key={item.id} className="menu-item" onClick={() => addToCart(item)}>
              <h3>{item.name}</h3>
              <p>{item.description}</p>
              <span className="price">₹{item.price}</span>
            </div>
          ))}
        </div>
      </div>

      {/* Cart */}
      <div className="cart">
        <h2>Current Cart</h2>
        {cart.map((item, index) => (
          <div key={index} className="cart-item">
            <span>{item.name}</span>
            <span>x{item.quantity}</span>
            <span>₹{item.unit_price * item.quantity}</span>
          </div>
        ))}
        <button 
          onClick={submitOrder}
          disabled={cart.length === 0}
          className="submit-order-btn"
        >
          {activeOrder ? 'Add to Order' : 'Create Order'}
        </button>
      </div>
    </div>
  );
}
```

---

### 3. Kitchen Display Component

```javascript
// KitchenDisplay.jsx
import { useState, useEffect } from 'react';

function KitchenDisplay() {
  const [pendingKOTs, setPendingKOTs] = useState([]);

  useEffect(() => {
    fetchPendingKOTs();
    
    // Poll every 10 seconds
    const interval = setInterval(fetchPendingKOTs, 10000);
    return () => clearInterval(interval);
  }, []);

  const fetchPendingKOTs = async () => {
    const response = await fetch('/api/kots/pending/all', {
      headers: { 'Authorization': `Bearer ${token}` }
    });
    const data = await response.json();
    setPendingKOTs(data);
  };

  const updateKOTStatus = async (kotId, status) => {
    await fetch(`/api/kots/${kotId}/status`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ status })
    });
    
    fetchPendingKOTs();
  };

  const getTimeSince = (timestamp) => {
    const minutes = Math.floor((Date.now() - new Date(timestamp)) / 60000);
    return `${minutes} min ago`;
  };

  return (
    <div className="kitchen-display">
      <h1>Kitchen Display System</h1>
      
      <div className="kot-columns">
        {/* Pending Column */}
        <div className="kot-column pending">
          <h2>Pending ({pendingKOTs.filter(k => k.status === 'pending').length})</h2>
          {pendingKOTs
            .filter(kot => kot.status === 'pending')
            .map(kot => (
              <KOTCard 
                key={kot.id} 
                kot={kot} 
                onStatusChange={updateKOTStatus}
              />
            ))}
        </div>

        {/* Preparing Column */}
        <div className="kot-column preparing">
          <h2>Preparing ({pendingKOTs.filter(k => k.status === 'preparing').length})</h2>
          {pendingKOTs
            .filter(kot => kot.status === 'preparing')
            .map(kot => (
              <KOTCard 
                key={kot.id} 
                kot={kot} 
                onStatusChange={updateKOTStatus}
              />
            ))}
        </div>

        {/* Ready Column */}
        <div className="kot-column ready">
          <h2>Ready ({pendingKOTs.filter(k => k.status === 'ready').length})</h2>
          {pendingKOTs
            .filter(kot => kot.status === 'ready')
            .map(kot => (
              <KOTCard 
                key={kot.id} 
                kot={kot} 
                onStatusChange={updateKOTStatus}
              />
            ))}
        </div>
      </div>
    </div>
  );
}

function KOTCard({ kot, onStatusChange }) {
  const getNextStatus = (currentStatus) => {
    const flow = { pending: 'preparing', preparing: 'ready', ready: 'served' };
    return flow[currentStatus];
  };

  return (
    <div className={`kot-card ${kot.status}`}>
      <div className="kot-header">
        <h3>{kot.kot_number}</h3>
        <span className="table">Table: {kot.order.restaurant_table.table_number}</span>
        <span className="time">{getTimeSince(kot.created_at)}</span>
      </div>
      
      <ul className="kot-items">
        {kot.order_items.map(item => (
          <li key={item.id}>
            <strong>{item.quantity}x</strong> {item.menu_item.name}
            {item.special_instructions && (
              <div className="instructions">📝 {item.special_instructions}</div>
            )}
          </li>
        ))}
      </ul>

      <button 
        className="status-btn"
        onClick={() => onStatusChange(kot.id, getNextStatus(kot.status))}
      >
        Mark as {getNextStatus(kot.status)}
      </button>
    </div>
  );
}
```

**CSS:**
```css
.kitchen-display {
  padding: 20px;
  background: #1f2937;
  color: white;
  min-height: 100vh;
}

.kot-columns {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.kot-column {
  background: #374151;
  padding: 15px;
  border-radius: 8px;
}

.kot-card {
  background: white;
  color: black;
  padding: 15px;
  margin-bottom: 15px;
  border-radius: 8px;
  border-left: 4px solid;
}

.kot-card.pending { border-color: #f59e0b; }
.kot-card.preparing { border-color: #3b82f6; }
.kot-card.ready { border-color: #10b981; }

.instructions {
  background: #fef3c7;
  padding: 5px;
  margin-top: 5px;
  border-radius: 4px;
  font-size: 0.9em;
}
```

---

### 4. Billing Component

```javascript
// BillingScreen.jsx
import { useState } from 'react';

function BillingScreen({ order }) {
  const [discount, setDiscount] = useState(0);
  const [paymentMethod, setPaymentMethod] = useState('cash');
  const [bill, setBill] = useState(null);

  const generateBill = async () => {
    // First complete the order
    await fetch(`/api/orders/${order.id}/complete`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${token}` }
    });

    // Then generate bill
    const response = await fetch(`/api/orders/${order.id}/bill`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        discount_percentage: discount
      })
    });

    const result = await response.json();
    setBill(result.bill);
  };

  const processPay = async () => {
    const response = await fetch(`/api/bills/${bill.id}/pay`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        payment_method: paymentMethod
      })
    });

    if (response.ok) {
      alert('Payment successful! Table is now available.');
      // Navigate back to table selection
      navigateToTables();
    }
  };

  return (
    <div className="billing-screen">
      <h1>Bill - {order.restaurant_table.table_number}</h1>
      
      {/* Order Summary */}
      <div className="order-summary">
        <h2>Order Summary</h2>
        {order.kitchen_order_tickets.map(kot => (
          <div key={kot.id} className="kot-summary">
            <h3>{kot.kot_number}</h3>
            {kot.order_items.map(item => (
              <div key={item.id} className="item-row">
                <span>{item.menu_item.name} x{item.quantity}</span>
                <span>₹{item.total_price}</span>
              </div>
            ))}
          </div>
        ))}
      </div>

      {/* Bill Generation */}
      {!bill && (
        <div className="bill-generation">
          <label>
            Discount (%):
            <input 
              type="number" 
              value={discount}
              onChange={(e) => setDiscount(e.target.value)}
              min="0"
              max="100"
            />
          </label>
          <button onClick={generateBill}>Generate Bill</button>
        </div>
      )}

      {/* Bill Display */}
      {bill && (
        <div className="bill-details">
          <h2>Bill #{bill.bill_number}</h2>
          <div className="bill-row">
            <span>Subtotal:</span>
            <span>₹{bill.subtotal}</span>
          </div>
          <div className="bill-row">
            <span>Tax ({bill.tax_percentage}%):</span>
            <span>₹{bill.tax_amount}</span>
          </div>
          <div className="bill-row">
            <span>Service Charge:</span>
            <span>₹{bill.service_charge}</span>
          </div>
          {bill.discount_amount > 0 && (
            <div className="bill-row discount">
              <span>Discount ({bill.discount_percentage}%):</span>
              <span>-₹{bill.discount_amount}</span>
            </div>
          )}
          <div className="bill-row total">
            <span>Total:</span>
            <span>₹{bill.total_amount}</span>
          </div>

          {/* Payment */}
          <div className="payment-section">
            <h3>Payment Method</h3>
            <select value={paymentMethod} onChange={(e) => setPaymentMethod(e.target.value)}>
              <option value="cash">Cash</option>
              <option value="card">Card</option>
              <option value="upi">UPI</option>
              <option value="other">Other</option>
            </select>
            <button onClick={processPay} className="pay-btn">Process Payment</button>
          </div>
        </div>
      )}
    </div>
  );
}
```

---

### 5. KOT Print Template

```javascript
// KOTPrintTemplate.jsx
function KOTPrintTemplate({ kot }) {
  const printKOT = () => {
    window.print();
  };

  return (
    <div className="kot-print-template">
      <div className="print-header">
        <h1>KITCHEN ORDER TICKET</h1>
        <h2>{kot.kot_number}</h2>
      </div>

      <div className="print-info">
        <div>Table: <strong>{kot.order.restaurant_table.table_number}</strong></div>
        <div>Time: <strong>{new Date(kot.created_at).toLocaleTimeString()}</strong></div>
        <div>Order: <strong>{kot.order.order_number}</strong></div>
      </div>

      <div className="print-items">
        <table>
          <thead>
            <tr>
              <th>Qty</th>
              <th>Item</th>
              <th>Instructions</th>
            </tr>
          </thead>
          <tbody>
            {kot.order_items.map(item => (
              <tr key={item.id}>
                <td className="qty">{item.quantity}</td>
                <td className="item-name">{item.menu_item.name}</td>
                <td className="instructions">{item.special_instructions || '-'}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {kot.notes && (
        <div className="print-notes">
          <strong>Notes:</strong> {kot.notes}
        </div>
      )}

      <button onClick={printKOT} className="print-btn">Print</button>
    </div>
  );
}
```

**Print CSS:**
```css
@media print {
  .print-btn { display: none; }
  
  .kot-print-template {
    font-family: monospace;
    max-width: 80mm;
  }
  
  .print-header {
    text-align: center;
    border-bottom: 2px dashed black;
    padding-bottom: 10px;
  }
  
  .print-items table {
    width: 100%;
    border-collapse: collapse;
  }
  
  .print-items th,
  .print-items td {
    border-bottom: 1px solid #ddd;
    padding: 8px;
    text-align: left;
  }
  
  .qty {
    font-weight: bold;
    font-size: 1.2em;
  }
}
```

---

## State Management (Redux/Vuex Example)

```javascript
// store/orderSlice.js
import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';

export const fetchTables = createAsyncThunk('orders/fetchTables', async () => {
  const response = await fetch('/api/tables');
  return response.json();
});

export const createOrder = createAsyncThunk(
  'orders/createOrder',
  async ({ tableId, items }) => {
    const response = await fetch('/api/orders', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        restaurant_table_id: tableId,
        items
      })
    });
    return response.json();
  }
);

const orderSlice = createSlice({
  name: 'orders',
  initialState: {
    tables: [],
    currentOrder: null,
    loading: false
  },
  reducers: {
    clearCurrentOrder: (state) => {
      state.currentOrder = null;
    }
  },
  extraReducers: (builder) => {
    builder
      .addCase(fetchTables.fulfilled, (state, action) => {
        state.tables = action.payload;
      })
      .addCase(createOrder.fulfilled, (state, action) => {
        state.currentOrder = action.payload.order;
      });
  }
});

export const { clearCurrentOrder } = orderSlice.actions;
export default orderSlice.reducer;
```

---

## Mobile-Responsive Design Tips

```css
/* Mobile First Approach */
.table-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}

@media (min-width: 768px) {
  .table-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (min-width: 1024px) {
  .table-grid {
    grid-template-columns: repeat(6, 1fr);
  }
}

/* Kitchen Display - Stack on mobile */
.kot-columns {
  display: flex;
  flex-direction: column;
}

@media (min-width: 1024px) {
  .kot-columns {
    flex-direction: row;
  }
}
```

---

## WebSocket Integration (Optional)

For real-time updates:

```javascript
// useWebSocket.js
import { useEffect } from 'react';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.REACT_APP_PUSHER_KEY,
  cluster: process.env.REACT_APP_PUSHER_CLUSTER,
  forceTLS: true
});

export function useKitchenUpdates(callback) {
  useEffect(() => {
    window.Echo.channel('kitchen')
      .listen('KOTCreated', (e) => {
        callback('created', e.kot);
      })
      .listen('KOTStatusUpdated', (e) => {
        callback('updated', e.kot);
      });

    return () => {
      window.Echo.leaveChannel('kitchen');
    };
  }, [callback]);
}
```

---

These examples provide a solid foundation for building the frontend. Adapt them to your specific framework and design requirements!
