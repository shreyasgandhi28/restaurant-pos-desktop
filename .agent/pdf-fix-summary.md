# PDF Download Fix - Devanagari Font Rendering

## Problem
The downloaded PDF bills were showing garbled Devanagari text instead of proper characters.

## Root Cause
DomPDF (the library used to generate PDFs) has limited support for custom fonts like Noto Sans Devanagari. The font loading via `file://` protocol doesn't work reliably in DomPDF.

## Solution
Switched to **DejaVu Sans** font, which:
- Comes built-in with DomPDF
- Has excellent Devanagari character support
- Doesn't require custom font loading
- Works reliably across all platforms

## Changes Made

### 1. Updated `bills/pdf.blade.php`
- Changed font-family from 'Noto Sans Devanagari' to 'DejaVu Sans'
- Simplified the CSS to use float-based layout (better DomPDF compatibility)
- Removed custom font-face declarations
- Kept all the styling improvements for thermal receipt appearance

### 2. Updated `BillController.php`
- Changed `defaultFont` option from 'Noto Sans Devanagari' to 'DejaVu Sans'
- Removed custom font directory options (not needed for built-in fonts)
- Set `isRemoteEnabled` to false for better security

## Testing
1. Generate a bill for an order with Devanagari text
2. Click "Download" button
3. Open the PDF - Devanagari text should now render correctly
4. Verify the styling matches the thermal receipt design

## Note
The print functionality already works perfectly because it uses HTML rendering in the browser, which has full font support. This fix specifically addresses the PDF download issue.
