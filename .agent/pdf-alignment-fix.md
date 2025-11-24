# PDF Bill Alignment Fix

## Issue
The PDF bill had misaligned amounts in the items table. Specifically:
- QTY column was not centered
- RATE and TOTAL columns were not right-aligned
- Column widths didn't match the print bill layout

## Root Cause
The `pdf.blade.php` template had incorrect CSS styling:
- All table headers (`<th>`) had `text-align: left`
- Column widths were different from the print template
- Missing specific alignment rules for numeric columns

## Solution
Updated `backend/resources/views/bills/pdf.blade.php` to match the print bill styling:

### Changes Made:
1. **Added specific header alignment rules:**
   - `.items-table th.qty` → `text-align: center`
   - `.items-table th.price` and `.items-table th.total` → `text-align: right`

2. **Adjusted column widths to match print template:**
   - Item Name: 50% (was 45%)
   - QTY: 12% (was 10%)
   - RATE: 18% (was 20%)
   - TOTAL: 20% (was 25%)

3. **Improved padding consistency:**
   - Item name: `padding-right: 5px` (was 2px)
   - Price: `padding-right: 8px` (was 4px)

## Result
The PDF bill now matches the print bill layout with:
- ✅ Centered QTY column
- ✅ Right-aligned RATE column
- ✅ Right-aligned TOTAL column
- ✅ Consistent column widths
- ✅ Proper spacing and alignment

## Testing
To test the fix:
1. Generate a bill from the POS or Dashboard
2. Click "Download PDF" button
3. Verify that amounts are properly aligned in the PDF
