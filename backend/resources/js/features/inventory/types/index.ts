/**
 * Inventory Management Types
 */

export interface ProductCategory {
  id: string;
  name: string;
  name_bn?: string;
  code: string;
  description?: string;
  icon?: string;
  sort_order: number;
  is_active: boolean;
}

export interface ProductUnit {
  id: string;
  name: string;
  code: string;
  short_code?: string;
  description?: string;
  is_active: boolean;
}

export interface ProductBrand {
  id: string;
  name: string;
  name_bn?: string;
  code: string;
  country?: string;
  website?: string;
  logo?: string;
  description?: string;
  is_active: boolean;
}

export interface Warehouse {
  id: string;
  name: string;
  code: string;
  type: WarehouseType;
  type_label?: string;
  address?: string;
  building?: string;
  floor?: string;
  manager_name?: string;
  phone?: string;
  email?: string;
  description?: string;
  is_active: boolean;
}

export interface Supplier {
  id: string;
  code: string;
  company_name: string;
  contact_person?: string;
  phone?: string;
  mobile?: string;
  email?: string;
  address?: string;
  city?: string;
  country?: string;
  trade_license?: string;
  tin?: string;
  bin?: string;
  vat_number?: string;
  website?: string;
  bank_name?: string;
  bank_account?: string;
  credit_limit?: number;
  payment_days: number;
  notes?: string;
  is_active: boolean;
}

export interface Product {
  id: string;
  sku: string;
  barcode?: string;
  qr_code?: string;
  name: string;
  short_name?: string;
  name_bn?: string;
  category_id?: string;
  category?: { id: string; name: string };
  brand_id?: string;
  brand?: { id: string; name: string };
  unit_id?: string;
  unit?: { id: string; name: string; short_code?: string };
  model?: string;
  description?: string;
  specifications?: string;
  image?: string;
  cost_price?: number;
  selling_price?: number;
  min_stock?: number;
  max_stock?: number;
  reorder_level?: number;
  current_stock?: number;
  is_low_stock?: boolean;
  is_out_of_stock?: boolean;
  weight?: string;
  dimensions?: string;
  color?: string;
  size?: string;
  is_trackable: boolean;
  is_sellable: boolean;
  is_purchasable: boolean;
  is_active: boolean;
}

export interface PurchaseRequest {
  id: string;
  pr_no: string;
  department?: string;
  requested_by?: { id: number; name: string };
  purpose?: string;
  remarks?: string;
  estimated_total?: number;
  status: PurchaseRequestStatus;
  approved_by?: { id: number; name: string };
  approved_at?: string;
  items?: PurchaseRequestItem[];
}

export interface PurchaseRequestItem {
  id: number;
  product_id?: string;
  product?: { id: string; name: string; sku: string };
  product_name: string;
  specifications?: string;
  quantity: number;
  unit_id?: string;
  unit?: { id: string; name: string };
  estimated_rate?: number;
  estimated_amount?: number;
  remarks?: string;
}

export interface PurchaseOrder {
  id: string;
  po_no: string;
  supplier_id?: string;
  supplier?: { id: string; code: string; name: string };
  warehouse_id?: string;
  warehouse?: { id: string; name: string };
  purchase_request_id?: string;
  order_date: string;
  expected_delivery_date?: string;
  actual_delivery_date?: string;
  payment_terms: string;
  delivery_terms?: string;
  subtotal: number;
  discount_percent: number;
  discount_amount: number;
  vat_percent: number;
  vat_amount: number;
  shipping_cost: number;
  total: number;
  notes?: string;
  status: PurchaseOrderStatus;
  created_by?: { id: number; name: string };
  approved_by?: { id: number; name: string };
  approved_at?: string;
  items?: PurchaseOrderItem[];
}

export interface PurchaseOrderItem {
  id: number;
  product_id?: string;
  product?: { id: string; name: string; sku: string };
  product_name: string;
  specifications?: string;
  ordered_quantity: number;
  received_quantity: number;
  remaining_quantity?: number;
  rejected_quantity: number;
  unit_price: number;
  discount_percent: number;
  discount_amount: number;
  vat_percent: number;
  vat_amount: number;
  total: number;
  remarks?: string;
}

export interface GoodsReceivedNote {
  id: string;
  grn_no: string;
  purchase_order_id?: string;
  purchase_order?: { id: string; po_no: string };
  supplier_id?: string;
  supplier?: { id: string; name: string };
  warehouse_id?: string;
  warehouse?: { id: string; name: string };
  received_date: string;
  challan_no?: string;
  vehicle_no?: string;
  remarks?: string;
  total: number;
  status: string;
  received_by?: { id: number; name: string };
  verified_by?: { id: number; name: string };
  verified_at?: string;
  items?: GoodsReceivedNoteItem[];
}

export interface GoodsReceivedNoteItem {
  id: number;
  product_id?: string;
  product?: { id: string; name: string };
  purchase_order_item_id?: string;
  product_name: string;
  ordered_quantity?: number;
  received_quantity: number;
  accepted_quantity: number;
  rejected_quantity: number;
  unit_price: number;
  total: number;
  condition: string;
  remarks?: string;
}

export interface StockMovement {
  id: string;
  movement_no: string;
  product_id?: string;
  product?: { id: string; name: string; sku: string };
  warehouse_id?: string;
  warehouse?: { id: string; name: string };
  from_warehouse_id?: string;
  from_warehouse?: { id: string; name: string };
  to_warehouse_id?: string;
  to_warehouse?: { id: string; name: string };
  movement_type: StockMovementType;
  movement_type_label?: string;
  is_incoming?: boolean;
  quantity: number;
  opening_stock: number;
  closing_stock: number;
  unit_cost?: number;
  total_cost?: number;
  movement_date: string;
  reference_type?: string;
  reference_id?: number;
  remarks?: string;
  created_by?: { id: number; name: string };
  created_at: string;
}

export interface Asset {
  id: string;
  asset_code: string;
  product_id?: string;
  product?: { id: string; name: string; sku: string };
  asset_name: string;
  serial_number?: string;
  barcode?: string;
  qr_code?: string;
  category: string;
  category_label?: string;
  warehouse_id?: string;
  warehouse?: { id: string; name: string };
  assigned_to_type?: string;
  assigned_to_id?: number;
  assigned_to_name?: string;
  purchase_date?: string;
  purchase_cost?: number;
  warranty_expiry?: string;
  is_under_warranty?: boolean;
  supplier?: string;
  location?: string;
  condition: string;
  status: AssetStatus;
  status_label?: string;
  description?: string;
  notes?: string;
  depreciation_rate?: number;
  current_value?: number;
  disposal_date?: string;
  disposal_value?: number;
  is_active: boolean;
}

export interface AssetTransfer {
  id: string;
  transfer_no: string;
  asset_id?: string;
  asset?: { id: string; asset_code: string; asset_name: string };
  from_holder_type?: string;
  from_holder_id?: number;
  from_holder_name?: string;
  to_holder_type?: string;
  to_holder_id?: number;
  to_holder_name?: string;
  from_location?: string;
  to_location?: string;
  transfer_date: string;
  reason?: string;
  status: AssetTransferStatus;
  requested_by?: { id: number; name: string };
  approved_by?: { id: number; name: string };
  approved_at?: string;
  transferred_by?: { id: number; name: string };
  transferred_at?: string;
}

export interface AssetMaintenance {
  id: string;
  maintenance_no: string;
  asset_id?: string;
  asset?: { id: string; asset_code: string; asset_name: string };
  maintenance_type: MaintenanceType;
  priority: MaintenancePriority;
  scheduled_date?: string;
  start_date?: string;
  completion_date?: string;
  vendor?: string;
  technician_name?: string;
  cost?: number;
  description?: string;
  work_done?: string;
  status: MaintenanceStatus;
  created_by?: { id: number; name: string };
  assigned_to?: { id: number; name: string };
}

export interface InventoryDashboard {
  total_products: number;
  total_warehouses: number;
  total_suppliers: number;
  total_assets: number;
  low_stock_products: number;
  out_of_stock_products: number;
  pending_purchase_orders: number;
  pending_asset_transfers: number;
  scheduled_maintenances: number;
  upcoming_warranty_expiry: number;
}

// Enums
export type WarehouseType = 'main' | 'department' | 'it' | 'library' | 'laboratory';
export type PurchaseRequestStatus = 'pending' | 'approved' | 'rejected' | 'converted';
export type PurchaseOrderStatus = 'draft' | 'pending' | 'approved' | 'ordered' | 'partially_received' | 'received' | 'cancelled';
export type StockMovementType = 'purchase' | 'sale' | 'stock_in' | 'stock_out' | 'transfer_in' | 'transfer_out' | 'adjustment_in' | 'adjustment_out' | 'return_in' | 'return_out' | 'damage' | 'loss';
export type AssetStatus = 'available' | 'allocated' | 'repair' | 'maintenance' | 'lost' | 'disposed';
export type AssetTransferStatus = 'pending' | 'approved' | 'rejected' | 'completed';
export type MaintenanceType = 'preventive' | 'corrective' | 'predictive';
export type MaintenancePriority = 'low' | 'normal' | 'high' | 'urgent';
export type MaintenanceStatus = 'scheduled' | 'in_progress' | 'completed' | 'cancelled';

export const WAREHOUSE_TYPES: Record<WarehouseType, string> = {
  main: 'Main Store',
  department: 'Department Store',
  it: 'IT Store',
  library: 'Library Store',
  laboratory: 'Laboratory Store',
};

export const PURCHASE_ORDER_STATUSES: Record<PurchaseOrderStatus, string> = {
  draft: 'Draft',
  pending: 'Pending Approval',
  approved: 'Approved',
  ordered: 'Ordered',
  partially_received: 'Partially Received',
  received: 'Received',
  cancelled: 'Cancelled',
};

export const STOCK_MOVEMENT_TYPES: Record<StockMovementType, string> = {
  purchase: 'Purchase',
  sale: 'Sale',
  stock_in: 'Stock In',
  stock_out: 'Stock Out',
  transfer_in: 'Transfer In',
  transfer_out: 'Transfer Out',
  adjustment_in: 'Adjustment In',
  adjustment_out: 'Adjustment Out',
  return_in: 'Return In',
  return_out: 'Return Out',
  damage: 'Damage',
  loss: 'Loss',
};

export const ASSET_STATUSES: Record<AssetStatus, string> = {
  available: 'Available',
  allocated: 'Allocated',
  repair: 'Under Repair',
  maintenance: 'Under Maintenance',
  lost: 'Lost',
  disposed: 'Disposed',
};

export const ASSET_CATEGORIES: Record<string, string> = {
  computer: 'Computer/Laptop',
  printer: 'Printer/Scanner',
  projector: 'Projector',
  furniture: 'Furniture',
  vehicle: 'Vehicle',
  generator: 'Generator/UPS',
  ac: 'Air Conditioner',
  lab: 'Lab Equipment',
  electrical: 'Electrical Equipment',
  other: 'Other',
};

export const MAINTENANCE_TYPES: Record<MaintenanceType, string> = {
  preventive: 'Preventive',
  corrective: 'Corrective',
  predictive: 'Predictive',
};

export const MAINTENANCE_PRIORITIES: Record<MaintenancePriority, string> = {
  low: 'Low',
  normal: 'Normal',
  high: 'High',
  urgent: 'Urgent',
};

export const MAINTENANCE_STATUSES: Record<MaintenanceStatus, string> = {
  scheduled: 'Scheduled',
  in_progress: 'In Progress',
  completed: 'Completed',
  cancelled: 'Cancelled',
};

// Paginated Response
export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}
