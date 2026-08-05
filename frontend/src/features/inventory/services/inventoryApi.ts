/**
 * Inventory API Service
 */

import { apiClient } from '@/lib/api-client';
import type {
  Product,
  ProductCategory,
  ProductUnit,
  ProductBrand,
  Warehouse,
  Supplier,
  PurchaseOrder,
  PurchaseRequest,
  GoodsReceivedNote,
  StockMovement,
  Asset,
  AssetTransfer,
  AssetMaintenance,
  InventoryDashboard,
  PaginatedResponse,
} from '../types';

const BASE_URL = '/api/v1/inventory';

export const inventoryApi = {
  // Dashboard
  getDashboard: async (): Promise<InventoryDashboard> => {
    const response = await apiClient.get(`${BASE_URL}/dashboard`);
    return response.data;
  },

  // Products
  getProducts: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    category_id?: string;
    brand_id?: string;
    is_low_stock?: boolean;
    is_out_of_stock?: boolean;
  }): Promise<PaginatedResponse<Product>> => {
    const response = await apiClient.get(`${BASE_URL}/products`, { params });
    return response.data;
  },

  getProduct: async (uuid: string): Promise<Product> => {
    const response = await apiClient.get(`${BASE_URL}/products/${uuid}`);
    return response.data;
  },

  createProduct: async (data: Partial<Product>): Promise<Product> => {
    const response = await apiClient.post(`${BASE_URL}/products`, data);
    return response.data;
  },

  updateProduct: async (uuid: string, data: Partial<Product>): Promise<Product> => {
    const response = await apiClient.put(`${BASE_URL}/products/${uuid}`, data);
    return response.data;
  },

  deleteProduct: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/products/${uuid}`);
  },

  // Categories
  getCategories: async (params?: { per_page?: number }): Promise<PaginatedResponse<ProductCategory>> => {
    const response = await apiClient.get(`${BASE_URL}/categories`, { params });
    return response.data;
  },

  createCategory: async (data: Partial<ProductCategory>): Promise<ProductCategory> => {
    const response = await apiClient.post(`${BASE_URL}/categories`, data);
    return response.data;
  },

  // Units
  getUnits: async (params?: { per_page?: number }): Promise<PaginatedResponse<ProductUnit>> => {
    const response = await apiClient.get(`${BASE_URL}/units`, { params });
    return response.data;
  },

  // Brands
  getBrands: async (params?: { per_page?: number }): Promise<PaginatedResponse<ProductBrand>> => {
    const response = await apiClient.get(`${BASE_URL}/brands`, { params });
    return response.data;
  },

  // Warehouses
  getWarehouses: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    type?: string;
  }): Promise<PaginatedResponse<Warehouse>> => {
    const response = await apiClient.get(`${BASE_URL}/warehouses`, { params });
    return response.data;
  },

  getWarehouse: async (uuid: string): Promise<Warehouse> => {
    const response = await apiClient.get(`${BASE_URL}/warehouses/${uuid}`);
    return response.data;
  },

  createWarehouse: async (data: Partial<Warehouse>): Promise<Warehouse> => {
    const response = await apiClient.post(`${BASE_URL}/warehouses`, data);
    return response.data;
  },

  updateWarehouse: async (uuid: string, data: Partial<Warehouse>): Promise<Warehouse> => {
    const response = await apiClient.put(`${BASE_URL}/warehouses/${uuid}`, data);
    return response.data;
  },

  // Suppliers
  getSuppliers: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
  }): Promise<PaginatedResponse<Supplier>> => {
    const response = await apiClient.get(`${BASE_URL}/suppliers`, { params });
    return response.data;
  },

  getSupplier: async (uuid: string): Promise<Supplier> => {
    const response = await apiClient.get(`${BASE_URL}/suppliers/${uuid}`);
    return response.data;
  },

  createSupplier: async (data: Partial<Supplier>): Promise<Supplier> => {
    const response = await apiClient.post(`${BASE_URL}/suppliers`, data);
    return response.data;
  },

  updateSupplier: async (uuid: string, data: Partial<Supplier>): Promise<Supplier> => {
    const response = await apiClient.put(`${BASE_URL}/suppliers/${uuid}`, data);
    return response.data;
  },

  // Purchase Requests
  getPurchaseRequests: async (params?: {
    page?: number;
    per_page?: number;
    status?: string;
  }): Promise<PaginatedResponse<PurchaseRequest>> => {
    const response = await apiClient.get(`${BASE_URL}/purchase-requests`, { params });
    return response.data;
  },

  getPurchaseRequest: async (uuid: string): Promise<PurchaseRequest> => {
    const response = await apiClient.get(`${BASE_URL}/purchase-requests/${uuid}`);
    return response.data;
  },

  createPurchaseRequest: async (data: Partial<PurchaseRequest>): Promise<PurchaseRequest> => {
    const response = await apiClient.post(`${BASE_URL}/purchase-requests`, data);
    return response.data;
  },

  approvePurchaseRequest: async (uuid: string): Promise<PurchaseRequest> => {
    const response = await apiClient.post(`${BASE_URL}/purchase-requests/${uuid}/approve`);
    return response.data;
  },

  rejectPurchaseRequest: async (uuid: string): Promise<PurchaseRequest> => {
    const response = await apiClient.post(`${BASE_URL}/purchase-requests/${uuid}/reject`);
    return response.data;
  },

  // Purchase Orders
  getPurchaseOrders: async (params?: {
    page?: number;
    per_page?: number;
    supplier_id?: string;
    status?: string;
    date_from?: string;
    date_to?: string;
  }): Promise<PaginatedResponse<PurchaseOrder>> => {
    const response = await apiClient.get(`${BASE_URL}/purchase-orders`, { params });
    return response.data;
  },

  getPurchaseOrder: async (uuid: string): Promise<PurchaseOrder> => {
    const response = await apiClient.get(`${BASE_URL}/purchase-orders/${uuid}`);
    return response.data;
  },

  createPurchaseOrder: async (data: Partial<PurchaseOrder>): Promise<PurchaseOrder> => {
    const response = await apiClient.post(`${BASE_URL}/purchase-orders`, data);
    return response.data;
  },

  updatePurchaseOrder: async (uuid: string, data: Partial<PurchaseOrder>): Promise<PurchaseOrder> => {
    const response = await apiClient.put(`${BASE_URL}/purchase-orders/${uuid}`, data);
    return response.data;
  },

  approvePurchaseOrder: async (uuid: string): Promise<PurchaseOrder> => {
    const response = await apiClient.post(`${BASE_URL}/purchase-orders/${uuid}/approve`);
    return response.data;
  },

  cancelPurchaseOrder: async (uuid: string): Promise<PurchaseOrder> => {
    const response = await apiClient.post(`${BASE_URL}/purchase-orders/${uuid}/cancel`);
    return response.data;
  },

  // Goods Received Notes
  getGoodsReceivedNotes: async (params?: {
    page?: number;
    per_page?: number;
    purchase_order_id?: string;
  }): Promise<PaginatedResponse<GoodsReceivedNote>> => {
    const response = await apiClient.get(`${BASE_URL}/grn`, { params });
    return response.data;
  },

  createGoodsReceivedNote: async (data: Partial<GoodsReceivedNote>): Promise<GoodsReceivedNote> => {
    const response = await apiClient.post(`${BASE_URL}/grn`, data);
    return response.data;
  },

  // Stock Movements
  getStockMovements: async (params?: {
    page?: number;
    per_page?: number;
    product_id?: string;
    warehouse_id?: string;
    movement_type?: string;
    date_from?: string;
    date_to?: string;
  }): Promise<PaginatedResponse<StockMovement>> => {
    const response = await apiClient.get(`${BASE_URL}/stock-movements`, { params });
    return response.data;
  },

  createStockMovement: async (data: Partial<StockMovement>): Promise<StockMovement> => {
    const response = await apiClient.post(`${BASE_URL}/stock-movements`, data);
    return response.data;
  },

  transferStock: async (data: {
    product_id: string;
    from_warehouse_id: string;
    to_warehouse_id: string;
    quantity: number;
    remarks?: string;
  }): Promise<{ success: boolean; message: string }> => {
    const response = await apiClient.post(`${BASE_URL}/stock-transfer`, data);
    return response.data;
  },

  adjustStock: async (data: {
    product_id: string;
    warehouse_id: string;
    quantity: number;
    type: 'increase' | 'decrease';
    remarks?: string;
  }): Promise<{ success: boolean; message: string }> => {
    const response = await apiClient.post(`${BASE_URL}/stock-adjustment`, data);
    return response.data;
  },

  // Assets
  getAssets: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    category?: string;
    status?: string;
  }): Promise<PaginatedResponse<Asset>> => {
    const response = await apiClient.get(`${BASE_URL}/assets`, { params });
    return response.data;
  },

  getAsset: async (uuid: string): Promise<Asset> => {
    const response = await apiClient.get(`${BASE_URL}/assets/${uuid}`);
    return response.data;
  },

  createAsset: async (data: Partial<Asset>): Promise<Asset> => {
    const response = await apiClient.post(`${BASE_URL}/assets`, data);
    return response.data;
  },

  updateAsset: async (uuid: string, data: Partial<Asset>): Promise<Asset> => {
    const response = await apiClient.put(`${BASE_URL}/assets/${uuid}`, data);
    return response.data;
  },

  allocateAsset: async (uuid: string, data: {
    holder_type: string;
    holder_id: number;
    holder_name: string;
  }): Promise<Asset> => {
    const response = await apiClient.post(`${BASE_URL}/assets/${uuid}/allocate`, data);
    return response.data;
  },

  // Asset Transfers
  getAssetTransfers: async (params?: {
    page?: number;
    per_page?: number;
    status?: string;
  }): Promise<PaginatedResponse<AssetTransfer>> => {
    const response = await apiClient.get(`${BASE_URL}/asset-transfers`, { params });
    return response.data;
  },

  createAssetTransfer: async (data: Partial<AssetTransfer>): Promise<AssetTransfer> => {
    const response = await apiClient.post(`${BASE_URL}/asset-transfers`, data);
    return response.data;
  },

  approveAssetTransfer: async (uuid: string): Promise<AssetTransfer> => {
    const response = await apiClient.post(`${BASE_URL}/asset-transfers/${uuid}/approve`);
    return response.data;
  },

  completeAssetTransfer: async (uuid: string): Promise<AssetTransfer> => {
    const response = await apiClient.post(`${BASE_URL}/asset-transfers/${uuid}/complete`);
    return response.data;
  },

  // Asset Maintenance
  getAssetMaintenances: async (params?: {
    page?: number;
    per_page?: number;
    asset_id?: string;
    status?: string;
  }): Promise<PaginatedResponse<AssetMaintenance>> => {
    const response = await apiClient.get(`${BASE_URL}/asset-maintenances`, { params });
    return response.data;
  },

  createAssetMaintenance: async (data: Partial<AssetMaintenance>): Promise<AssetMaintenance> => {
    const response = await apiClient.post(`${BASE_URL}/asset-maintenances`, data);
    return response.data;
  },

  completeMaintenance: async (uuid: string, workDone: string): Promise<AssetMaintenance> => {
    const response = await apiClient.post(`${BASE_URL}/asset-maintenances/${uuid}/complete`, { work_done: workDone });
    return response.data;
  },

  // Reports
  getInventoryReport: async (params?: {
    category_id?: string;
    warehouse_id?: string;
  }): Promise<{
    products: Product[];
    total_value: number;
    low_stock_count: number;
    out_of_stock_count: number;
  }> => {
    const response = await apiClient.get(`${BASE_URL}/reports/inventory`, { params });
    return response.data;
  },

  getStockLedger: async (productId: string, params?: {
    date_from?: string;
    date_to?: string;
  }): Promise<{
    product: Product;
    movements: StockMovement[];
    opening_stock: number;
    closing_stock: number;
  }> => {
    const response = await apiClient.get(`${BASE_URL}/reports/stock-ledger/${productId}`, { params });
    return response.data;
  },
};
