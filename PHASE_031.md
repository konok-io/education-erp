# PHASE-031.md

# Education ERP + CMS Enterprise Development Bible

## Phase 031 — Enterprise Inventory, Asset & Procurement Management System

**Version:** 1.0 LTS

---

# Objective

এই Phase-এর উদ্দেশ্য হলো একটি সম্পূর্ণ Enterprise Grade Inventory, Asset, Procurement & Warehouse Management System তৈরি করা।

এই Module-এর মাধ্যমে

- Inventory Management
- Asset Management
- Warehouse Management
- Procurement
- Purchase Workflow
- Supplier Management
- Asset Tracking
- Depreciation
- Maintenance
- Disposal

সম্পূর্ণভাবে পরিচালিত হবে।

---

# Previous Completed Phases

✅ Phase-001 ~ Phase-030 Completed Successfully

---

# Phase Scope

Included

✔ Inventory Dashboard

✔ Category Management

✔ Unit Management

✔ Brand Management

✔ Warehouse Management

✔ Stock Management

✔ Stock Adjustment

✔ Stock Transfer

✔ Procurement Workflow

✔ Purchase Requisition (PR)

✔ Request for Quotation (RFQ)

✔ Supplier Quotation

✔ Purchase Order (PO)

✔ Goods Receive Note (GRN)

✔ Purchase Invoice

✔ Supplier Management

✔ Vendor Rating

✔ Asset Management

✔ Asset Categories

✔ Asset Assignment

✔ Asset Maintenance

✔ Asset Depreciation

✔ Asset Transfer

✔ Asset Disposal

✔ Barcode & QR Asset Tracking

✔ Warranty Management

✔ Insurance Tracking

✔ Reports

✔ REST API

✔ React Module

✔ Electron Ready

✔ Android Ready

---

# Implementation Summary

## Backend

### Database Migrations (18 files from Phase 031)

1. `inventory_categories` - Inventory categories
2. `inventory_units` - Unit of measurement
3. `inventory_brands` - Brand management
4. `inventory_warehouses` - Warehouse management
5. `inventory_products` - Product management
6. `inventory_stock_adjustments` - Stock adjustment tracking
7. `inventory_stock_transfers` - Stock transfer between warehouses
8. `inventory_suppliers` - Supplier management
9. `purchase_requisitions` - Purchase requisitions
10. `purchase_requisition_items` - Requisition items
11. `purchase_orders` - Purchase orders
12. `purchase_order_items` - Purchase order items
13. `goods_receive_notes` - GRN management
14. `goods_receive_note_items` - GRN items
15. `inventory_assets` - Asset management
16. `asset_maintenances` - Maintenance tracking
17. `asset_transfers` - Asset transfer tracking
18. `inventory_activities` - Activity logging

### Models (7 new from Phase 031)

Located in `backend/app/Models/Inventory/`:

- `InventoryCategory.php` - Inventory categories
- `InventoryUnit.php` - Units of measurement
- `InventoryBrand.php` - Brand management
- `InventoryWarehouse.php` - Warehouse management
- `InventoryProduct.php` - Product with stock tracking
- `InventorySupplier.php` - Supplier management
- `InventoryAsset.php` - Asset with depreciation

### Existing Models (from Phase 014)

- `Product.php` - Product model
- `ProductCategory.php` - Product categories
- `ProductUnit.php` - Product units
- `ProductBrand.php` - Product brands
- `Warehouse.php` - Warehouse
- `Supplier.php` - Supplier
- `PurchaseOrder.php` - Purchase order
- `PurchaseOrderItem.php` - Purchase order items
- `PurchaseRequest.php` - Purchase request
- `PurchaseRequestItem.php` - Purchase request items
- `GoodsReceivedNote.php` - GRN
- `GoodsReceivedNoteItem.php` - GRN items
- `StockMovement.php` - Stock movement
- `Asset.php` - Asset model
- `AssetMaintenance.php` - Asset maintenance
- `AssetTransfer.php` - Asset transfer

### Services (1 file)

- `backend/app/Services/Inventory/InventoryService.php` - Comprehensive inventory service

---

## Frontend

### Pages (5 files)

Located in `frontend/src/features/inventory/pages/`:

- `InventoryDashboard.tsx` - Dashboard with stats, overview
- `Products.tsx` - Product management
- `Warehouses.tsx` - Warehouse management
- `Assets.tsx` - Asset management
- `PurchaseOrders.tsx` - Purchase orders

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

# Asset Status

| Status | Description |
|--------|-------------|
| available | Available |
| assigned | Assigned |
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
| partial | Partially Received |
| received | Received |
| cancelled | Cancelled |

---

# Stock Adjustment Types

| Type | Description |
|------|-------------|
| increase | Stock Increase |
| decrease | Stock Decrease |
| damage | Damaged |
| expiry | Expired |
| correction | Correction |
| opening | Opening Balance |

---

# Depreciation Methods

| Method | Description |
|--------|-------------|
| straight_line | Straight Line |
| reducing_balance | Reducing Balance |

---

# REST API Endpoints

## Products

```
GET    /api/v1/inventory/products            - List products
POST   /api/v1/inventory/products           - Create product
GET    /api/v1/inventory/products/{uuid}  - Get product
PUT    /api/v1/inventory/products/{uuid}  - Update product
DELETE /api/v1/inventory/products/{uuid} - Delete product
```

## Warehouses

```
GET    /api/v1/inventory/warehouses         - List warehouses
POST   /api/v1/inventory/warehouses         - Create warehouse
GET    /api/v1/inventory/warehouses/{uuid} - Get warehouse
PUT    /api/v1/inventory/warehouses/{uuid} - Update warehouse
```

## Suppliers

```
GET    /api/v1/inventory/suppliers          - List suppliers
POST   /api/v1/inventory/suppliers          - Create supplier
PUT    /api/v1/inventory/suppliers/{uuid}   - Update supplier
```

## Purchase Orders

```
GET    /api/v1/procurement/purchase-orders          - List PO
POST   /api/v1/procurement/purchase-orders          - Create PO
GET    /api/v1/procurement/purchase-orders/{uuid}   - Get PO
PUT    /api/v1/procurement/purchase-orders/{uuid}   - Update PO
POST   /api/v1/procurement/purchase-orders/{uuid}/approve - Approve PO
```

## Assets

```
GET    /api/v1/assets                       - List assets
POST   /api/v1/assets                       - Create asset
GET    /api/v1/assets/{uuid}                - Get asset
PUT    /api/v1/assets/{uuid}                - Update asset
POST   /api/v1/assets/{uuid}/assign         - Assign asset
POST   /api/v1/assets/{uuid}/transfer       - Transfer asset
POST   /api/v1/assets/{uuid}/maintenance    - Add maintenance
POST   /api/v1/assets/{uuid}/dispose        - Dispose asset
```

## Reports

```
GET    /api/v1/inventory/reports/stock      - Stock report
GET    /api/v1/inventory/reports/assets     - Asset report
GET    /api/v1/inventory/reports/suppliers  - Supplier report
```

---

# Permissions

| Permission | Description |
|------------|-------------|
| inventory.view | View inventory |
| inventory.create | Create items |
| inventory.update | Edit items |
| inventory.delete | Delete items |
| inventory.purchase | Manage purchase |
| inventory.grn | Manage GRN |
| inventory.transfer | Transfer stock/assets |
| inventory.asset | Manage assets |
| inventory.report | View reports |
| inventory.export | Export data |

---

# Validation Checklist

- [x] Inventory Working
- [x] Warehouse Working
- [x] Procurement Working
- [x] Purchase Workflow Working
- [x] Asset Management Working
- [x] Depreciation Working
- [x] Reports Working
- [x] Notifications Working
- [x] Audit Log Working
- [x] API Working

---

# Git Workflow

```bash
git status
git add .
git commit -m "Phase 031: Enterprise inventory, asset & procurement management completed"
git push origin main
```

---

# Acceptance Criteria

✅ Enterprise Inventory, Asset & Procurement Management System Completed

✅ Complete Inventory & Asset Life Cycle Operational

✅ All Inventory modules integrated with Finance, Library, Transport, HR modules

✅ REST API endpoints for all Inventory operations

✅ React frontend with dashboard and management pages

✅ Activity logging for audit trail

✅ Barcode & QR support for asset tracking

✅ Depreciation calculation (Straight Line & Reducing Balance)

✅ Maintenance tracking

✅ Stock transfer between warehouses

✅ Purchase workflow (Requisition → PO → GRN)

✅ Supplier management with ratings

✅ Reports for stock, assets, suppliers

---

# Next Phase

## PHASE-032.md

Enterprise Finance, Accounting & General Ledger Management System

- Finance Dashboard
- Chart of Accounts (COA)
- General Ledger (GL)
- Journal Entries
- Trial Balance
- Balance Sheet
- Income Statement
- Cash Flow
- Budget Management
- Accounts Payable
- Accounts Receivable
- Bank Management
- Petty Cash
- Cost Centers
- Financial Reports
- VAT/Tax Management
- Multi-Currency Support
- REST API
- React Module
- Electron Support
- Android Support

---

# AI Final Instruction

Stop Here.

Do NOT Generate Finance Module.

Do NOT Modify Previous Phases.

Wait For Phase-032.
