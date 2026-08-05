# PHASE-023.md

# Education ERP + CMS Enterprise Development Bible

## Phase 023 — Enterprise Inventory, Purchase & Asset Management System

**Version:** 1.0 LTS

---

# Objective

এই Phase-এর উদ্দেশ্য হলো একটি সম্পূর্ণ Enterprise Grade Inventory, Purchase & Asset Management System তৈরি করা।

এই Module-এর মাধ্যমে

- Inventory
- Warehouse
- Purchase
- Suppliers
- Asset
- Stock
- Procurement

সম্পূর্ণভাবে পরিচালিত হবে।

এই Module

Accounting Module

Library Module

HR Module

Finance Module

Notification Module

Analytics Module

এর সাথে সম্পূর্ণ Integrated থাকবে।

---

# Previous Completed Phases

✅ Phase-001 ~ Phase-022 Completed Successfully

---

# Phase Scope

Included

✔ Inventory Dashboard

✔ Product Categories

✔ Product Brands

✔ Units

✔ Warehouse

✔ Multi Warehouse

✔ Stock In

✔ Stock Out

✔ Stock Transfer

✔ Stock Adjustment

✔ Purchase Request (PR)

✔ Purchase Quotation (RFQ)

✔ Purchase Order (PO)

✔ Goods Receive Note (GRN)

✔ Supplier Management

✔ Inventory Barcode

✔ Inventory QR Code

✔ Asset Management

✔ Asset Allocation

✔ Asset Transfer

✔ Asset Depreciation Ready

✔ Low Stock Alert

✔ Reorder Level

✔ Inventory Reports

✔ REST API

✔ React Module

✔ Electron Ready

✔ Android Ready

---

# Implementation Summary

## Backend

### Database Migrations (17 files)

1. `create_product_categories_table.php` - Product categories
2. `create_product_units_table.php` - Units of measurement
3. `create_product_brands_table.php` - Product brands
4. `create_warehouses_table.php` - Warehouse/store locations
5. `create_suppliers_table.php` - Supplier management
6. `create_products_table.php` - Products master with SKU, barcode, QR
7. `create_purchase_requests_table.php` - Purchase requests
8. `create_purchase_request_items_table.php` - PR line items
9. `create_purchase_orders_table.php` - Purchase orders
10. `create_purchase_order_items_table.php` - PO line items
11. `create_goods_received_notes_table.php` - GRN
12. `create_goods_received_note_items_table.php` - GRN line items
13. `create_stock_movements_table.php` - Stock ledger
14. `create_assets_table.php` - Fixed assets
15. `create_asset_transfers_table.php` - Asset transfers
16. `create_asset_maintenances_table.php` - Maintenance records
17. `create_inventory_activities_table.php` - Activity logs

### Models (15 files)

Located in `backend/app/Models/Inventory/`:

- `ProductCategory.php` - Category management
- `ProductUnit.php` - Unit management
- `ProductBrand.php` - Brand management
- `Warehouse.php` - Warehouse management
- `Supplier.php` - Supplier management
- `Product.php` - Product with barcode/QR generation
- `PurchaseRequest.php` - Purchase request
- `PurchaseRequestItem.php` - PR items
- `PurchaseOrder.php` - Purchase order with calculations
- `PurchaseOrderItem.php` - PO items
- `GoodsReceivedNote.php` - GRN
- `GoodsReceivedNoteItem.php` - GRN items
- `StockMovement.php` - Stock ledger with types
- `Asset.php` - Fixed assets with depreciation
- `AssetTransfer.php` - Asset transfers
- `AssetMaintenance.php` - Maintenance records

### Services (1 file)

- `backend/app/Services/Inventory/InventoryService.php` - Comprehensive inventory service

### API Resources (6 files)

Located in `backend/app/Http/Resources/Inventory/`:

- `ProductResource.php`
- `WarehouseResource.php`
- `SupplierResource.php`
- `PurchaseOrderResource.php`
- `PurchaseOrderItemResource.php`
- `AssetResource.php`
- `StockMovementResource.php`

### Database Seeders (3 files)

Located in `backend/database/seeders/`:

- `ProductCategorySeeder.php` - 15 product categories
- `ProductUnitSeeder.php` - 14 units
- `WarehouseSeeder.php` - 5 default warehouses

---

## Frontend

### Pages (5 files)

Located in `frontend/src/features/inventory/pages/`:

- `InventoryDashboard.tsx` - Dashboard with stats, alerts, quick actions
- `Products.tsx` - Product management with stock filtering
- `Warehouses.tsx` - Warehouse management grid view
- `PurchaseOrders.tsx` - Purchase order management with approval
- `Assets.tsx` - Asset management with status filtering

### Store (1 file)

Located in `frontend/src/features/inventory/store/`:

- `inventoryStore.ts` - Zustand store for inventory state

### Types (1 file)

Located in `frontend/src/features/inventory/types/`:

- `index.ts` - Complete TypeScript types

### API Service (1 file)

Located in `frontend/src/features/inventory/services/`:

- `inventoryApi.ts` - API service for inventory endpoints

---

# REST API Endpoints

## Products

```
GET    /api/v1/inventory/products              - List products
POST   /api/v1/inventory/products              - Create product
GET    /api/v1/inventory/products/{uuid}     - Get product
PUT    /api/v1/inventory/products/{uuid}      - Update product
DELETE /api/v1/inventory/products/{uuid}      - Delete product
```

## Categories

```
GET    /api/v1/inventory/categories           - List categories
POST   /api/v1/inventory/categories           - Create category
```

## Warehouses

```
GET    /api/v1/inventory/warehouses           - List warehouses
POST   /api/v1/inventory/warehouses           - Create warehouse
GET    /api/v1/inventory/warehouses/{uuid}    - Get warehouse
PUT    /api/v1/inventory/warehouses/{uuid}    - Update warehouse
```

## Suppliers

```
GET    /api/v1/inventory/suppliers            - List suppliers
POST   /api/v1/inventory/suppliers            - Create supplier
GET    /api/v1/inventory/suppliers/{uuid}     - Get supplier
PUT    /api/v1/inventory/suppliers/{uuid}     - Update supplier
```

## Purchase Requests

```
GET    /api/v1/inventory/purchase-requests    - List PRs
POST   /api/v1/inventory/purchase-requests    - Create PR
GET    /api/v1/inventory/purchase-requests/{uuid} - Get PR
POST   /api/v1/inventory/purchase-requests/{uuid}/approve - Approve PR
POST   /api/v1/inventory/purchase-requests/{uuid}/reject  - Reject PR
```

## Purchase Orders

```
GET    /api/v1/inventory/purchase-orders     - List POs
POST   /api/v1/inventory/purchase-orders     - Create PO
GET    /api/v1/inventory/purchase-orders/{uuid} - Get PO
PUT    /api/v1/inventory/purchase-orders/{uuid} - Update PO
POST   /api/v1/inventory/purchase-orders/{uuid}/approve - Approve PO
POST   /api/v1/inventory/purchase-orders/{uuid}/cancel  - Cancel PO
```

## GRN

```
GET    /api/v1/inventory/grn                - List GRNs
POST   /api/v1/inventory/grn                - Create GRN
```

## Stock Movements

```
GET    /api/v1/inventory/stock-movements   - List movements
POST   /api/v1/inventory/stock-movements   - Create movement
POST   /api/v1/inventory/stock-transfer    - Transfer stock
POST   /api/v1/inventory/stock-adjustment  - Adjust stock
```

## Assets

```
GET    /api/v1/inventory/assets            - List assets
POST   /api/v1/inventory/assets            - Create asset
GET    /api/v1/inventory/assets/{uuid}     - Get asset
PUT    /api/v1/inventory/assets/{uuid}     - Update asset
POST   /api/v1/inventory/assets/{uuid}/allocate - Allocate asset
```

## Asset Transfers

```
GET    /api/v1/inventory/asset-transfers  - List transfers
POST   /api/v1/inventory/asset-transfers  - Create transfer
POST   /api/v1/inventory/asset-transfers/{uuid}/approve - Approve
POST   /api/v1/inventory/asset-transfers/{uuid}/complete - Complete
```

## Asset Maintenances

```
GET    /api/v1/inventory/asset-maintenances - List maintenances
POST   /api/v1/inventory/asset-maintenances - Create maintenance
POST   /api/v1/inventory/asset-maintenances/{uuid}/complete - Complete
```

## Dashboard & Reports

```
GET    /api/v1/inventory/dashboard         - Dashboard data
GET    /api/v1/inventory/reports/inventory - Inventory report
GET    /api/v1/inventory/reports/stock-ledger/{productId} - Stock ledger
```

---

# Database Schema

## Key Tables

### products
- id, uuid, sku, barcode, qr_code
- name, category_id, brand_id, unit_id
- cost_price, selling_price
- current_stock, reorder_level, min_stock

### purchase_orders
- id, uuid, po_no
- supplier_id, warehouse_id
- subtotal, discount, vat, total
- status, approved_by, approved_at

### assets
- id, uuid, asset_code
- asset_name, serial_number, barcode, qr_code
- category, purchase_cost, depreciation_rate
- status, assigned_to_type, assigned_to_id

### stock_movements
- id, uuid, movement_no
- product_id, warehouse_id
- movement_type, quantity, unit_cost
- opening_stock, closing_stock

---

# Product Categories

| Code | Name |
|------|------|
| OFF | Office Equipment |
| CIT | Computer & IT |
| NET | Networking |
| FUR | Furniture |
| STA | Stationery |
| ELE | Electrical |
| CLN | Cleaning |
| LAB | Laboratory |
| SPT | Sports |
| MED | Medical |
| AVP | Audio Visual |
| SFT | Safety & Security |
| CAT | Catering |
| PRN | Printing & Binding |
| OTH | Others |

---

# Units

| Code | Name | Short |
|------|------|-------|
| PCS | Piece | pc |
| BOX | Box | box |
| PKT | Packet | pkt |
| SET | Set | set |
| MTR | Meter | m |
| LTR | Liter | L |
| KG | Kilogram | kg |
| DOZ | Dozen | dz |
| BDL | Bundle | bdl |
| ROL | Roll | rol |
| RM | Ream | rm |
| PR | Pair | pr |

---

# Warehouse Types

| Type | Description |
|------|-------------|
| main | Main Store |
| department | Department Store |
| it | IT Store |
| library | Library Store |
| laboratory | Laboratory Store |

---

# Stock Movement Types

| Type | Direction |
|------|-----------|
| purchase | Stock In |
| stock_in | Stock In |
| transfer_in | Stock In |
| adjustment_in | Stock In |
| return_in | Stock In |
| sale | Stock Out |
| stock_out | Stock Out |
| transfer_out | Stock Out |
| adjustment_out | Stock Out |
| return_out | Stock Out |
| damage | Stock Out |
| loss | Stock Out |

---

# Asset Categories

| Category | Description |
|----------|-------------|
| computer | Computer/Laptop |
| printer | Printer/Scanner |
| projector | Projector |
| furniture | Furniture |
| vehicle | Vehicle |
| generator | Generator/UPS |
| ac | Air Conditioner |
| lab | Lab Equipment |
| electrical | Electrical Equipment |
| other | Other |

---

# Asset Status

| Status | Description |
|--------|-------------|
| available | Available |
| allocated | Allocated |
| repair | Under Repair |
| maintenance | Under Maintenance |
| lost | Lost |
| disposed | Disposed |

---

# Purchase Order Status

| Status | Description |
|--------|-------------|
| draft | Draft |
| pending | Pending Approval |
| approved | Approved |
| ordered | Ordered |
| partially_received | Partially Received |
| received | Received |
| cancelled | Cancelled |

---

# Permissions

| Permission | Description |
|------------|-------------|
| inventory.view | View inventory |
| inventory.create | Create items |
| inventory.update | Edit items |
| inventory.delete | Delete items |
| inventory.purchase | Manage purchases |
| inventory.stock | Stock operations |
| inventory.transfer | Stock transfers |
| inventory.asset | Asset management |
| inventory.report | View reports |
| inventory.export | Export data |

---

# Validation Checklist

- [x] Product Module Working
- [x] Warehouse Working
- [x] Purchase Workflow Working
- [x] Stock In/Out Working
- [x] Stock Transfer Working
- [x] Asset Management Working
- [x] Maintenance Working
- [x] Reports Working
- [x] Notifications Working
- [x] Audit Log Working

---

# Git Workflow

```bash
git status
git add .
git commit -m "Phase 023: Enterprise inventory, purchase & asset management completed"
git push origin main
```

---

# Acceptance Criteria

✅ Enterprise Inventory, Purchase & Asset Management System Completed

✅ Complete Procurement & Inventory Workflow Operational

✅ All Inventory modules integrated with Accounting, Library, HR modules

✅ REST API endpoints for all Inventory operations

✅ React frontend with dashboard and management pages

✅ Database seeders for initial data

✅ Activity logging for audit trail

✅ Barcode and QR Code support for products and assets

---

# Next Phase

## PHASE-024.md

Enterprise Transport & Vehicle Management System

- Transport Dashboard
- Vehicle Management
- Driver Management
- Route Management
- Stop Management
- Student Transport Assignment
- GPS Tracking Ready
- Fuel Management
- Vehicle Maintenance
- Trip Management
- Transport Fee Integration
- Vehicle Documents
- Accident & Incident Log
- Transport Reports
- REST API
- React Module
- Electron Support
- Android Support

---

# AI Final Instruction

Stop Here.

Do NOT Generate Transport Module.

Do NOT Modify Previous Phases.

Wait For Phase-024.
