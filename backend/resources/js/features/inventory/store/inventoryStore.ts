/**
 * Inventory Store - State Management
 */

import { create } from 'zustand';
import { inventoryApi } from '../services/inventoryApi';
import type {
  Product,
  ProductCategory,
  ProductUnit,
  Warehouse,
  Supplier,
  PurchaseOrder,
  PurchaseRequest,
  StockMovement,
  Asset,
  AssetTransfer,
  AssetMaintenance,
  InventoryDashboard,
} from '../types';

interface InventoryState {
  // Dashboard
  dashboard: InventoryDashboard | null;
  dashboardLoading: boolean;
  dashboardError: string | null;

  // Products
  products: Product[];
  productsPagination: { current_page: number; last_page: number; total: number } | null;
  productsLoading: boolean;
  productsError: string | null;
  selectedProduct: Product | null;

  // Categories
  categories: ProductCategory[];
  categoriesLoading: boolean;

  // Units
  units: ProductUnit[];
  unitsLoading: boolean;

  // Warehouses
  warehouses: Warehouse[];
  warehousesLoading: boolean;

  // Suppliers
  suppliers: Supplier[];
  suppliersPagination: { current_page: number; last_page: number; total: number } | null;
  suppliersLoading: boolean;

  // Purchase Orders
  purchaseOrders: PurchaseOrder[];
  purchaseOrdersPagination: { current_page: number; last_page: number; total: number } | null;
  purchaseOrdersLoading: boolean;

  // Stock Movements
  stockMovements: StockMovement[];
  stockMovementsPagination: { current_page: number; last_page: number; total: number } | null;
  stockMovementsLoading: boolean;

  // Assets
  assets: Asset[];
  assetsPagination: { current_page: number; last_page: number; total: number } | null;
  assetsLoading: boolean;
  selectedAsset: Asset | null;

  // Asset Transfers
  assetTransfers: AssetTransfer[];
  assetTransfersLoading: boolean;

  // Asset Maintenances
  assetMaintenances: AssetMaintenance[];
  assetMaintenancesLoading: boolean;

  // Actions
  fetchDashboard: () => Promise<void>;
  fetchProducts: (params?: Record<string, any>) => Promise<void>;
  fetchProduct: (uuid: string) => Promise<void>;
  createProduct: (data: Partial<Product>) => Promise<Product>;
  updateProduct: (uuid: string, data: Partial<Product>) => Promise<Product>;
  deleteProduct: (uuid: string) => Promise<void>;
  fetchCategories: () => Promise<void>;
  fetchUnits: () => Promise<void>;
  fetchWarehouses: () => Promise<void>;
  createWarehouse: (data: Partial<Warehouse>) => Promise<Warehouse>;
  fetchSuppliers: (params?: Record<string, any>) => Promise<void>;
  createSupplier: (data: Partial<Supplier>) => Promise<Supplier>;
  fetchPurchaseOrders: (params?: Record<string, any>) => Promise<void>;
  createPurchaseOrder: (data: Partial<PurchaseOrder>) => Promise<PurchaseOrder>;
  approvePurchaseOrder: (uuid: string) => Promise<PurchaseOrder>;
  fetchStockMovements: (params?: Record<string, any>) => Promise<void>;
  transferStock: (data: { product_id: string; from_warehouse_id: string; to_warehouse_id: string; quantity: number; remarks?: string }) => Promise<void>;
  adjustStock: (data: { product_id: string; warehouse_id: string; quantity: number; type: 'increase' | 'decrease'; remarks?: string }) => Promise<void>;
  fetchAssets: (params?: Record<string, any>) => Promise<void>;
  fetchAsset: (uuid: string) => Promise<void>;
  createAsset: (data: Partial<Asset>) => Promise<Asset>;
  allocateAsset: (uuid: string, data: { holder_type: string; holder_id: number; holder_name: string }) => Promise<Asset>;
  fetchAssetTransfers: (params?: Record<string, any>) => Promise<void>;
  createAssetTransfer: (data: Partial<AssetTransfer>) => Promise<AssetTransfer>;
  approveAssetTransfer: (uuid: string) => Promise<void>;
  completeAssetTransfer: (uuid: string) => Promise<void>;
  fetchAssetMaintenances: (params?: Record<string, any>) => Promise<void>;
  createAssetMaintenance: (data: Partial<AssetMaintenance>) => Promise<AssetMaintenance>;
  completeMaintenance: (uuid: string, workDone: string) => Promise<AssetMaintenance>;
  resetState: () => void;
}

const initialState = {
  dashboard: null,
  dashboardLoading: false,
  dashboardError: null,
  products: [],
  productsPagination: null,
  productsLoading: false,
  productsError: null,
  selectedProduct: null,
  categories: [],
  categoriesLoading: false,
  units: [],
  unitsLoading: false,
  warehouses: [],
  warehousesLoading: false,
  suppliers: [],
  suppliersPagination: null,
  suppliersLoading: false,
  purchaseOrders: [],
  purchaseOrdersPagination: null,
  purchaseOrdersLoading: false,
  stockMovements: [],
  stockMovementsPagination: null,
  stockMovementsLoading: false,
  assets: [],
  assetsPagination: null,
  assetsLoading: false,
  selectedAsset: null,
  assetTransfers: [],
  assetTransfersLoading: false,
  assetMaintenances: [],
  assetMaintenancesLoading: false,
};

export const useInventoryStore = create<InventoryState>((set, get) => ({
  ...initialState,

  // Dashboard
  fetchDashboard: async () => {
    set({ dashboardLoading: true, dashboardError: null });
    try {
      const dashboard = await inventoryApi.getDashboard();
      set({ dashboard, dashboardLoading: false });
    } catch (error: any) {
      set({ dashboardError: error.message, dashboardLoading: false });
    }
  },

  // Products
  fetchProducts: async (params) => {
    set({ productsLoading: true, productsError: null });
    try {
      const response = await inventoryApi.getProducts(params);
      set({
        products: response.data,
        productsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        productsLoading: false,
      });
    } catch (error: any) {
      set({ productsError: error.message, productsLoading: false });
    }
  },

  fetchProduct: async (uuid) => {
    set({ productsLoading: true, productsError: null });
    try {
      const product = await inventoryApi.getProduct(uuid);
      set({ selectedProduct: product, productsLoading: false });
    } catch (error: any) {
      set({ productsError: error.message, productsLoading: false });
    }
  },

  createProduct: async (data) => {
    const product = await inventoryApi.createProduct(data);
    const products = [...get().products, product];
    set({ products });
    return product;
  },

  updateProduct: async (uuid, data) => {
    const product = await inventoryApi.updateProduct(uuid, data);
    const products = get().products.map((p) => (p.id === uuid ? product : p));
    set({ products, selectedProduct: product });
    return product;
  },

  deleteProduct: async (uuid) => {
    await inventoryApi.deleteProduct(uuid);
    const products = get().products.filter((p) => p.id !== uuid);
    set({ products });
  },

  // Categories
  fetchCategories: async () => {
    set({ categoriesLoading: true });
    try {
      const response = await inventoryApi.getCategories({ per_page: 100 });
      set({ categories: response.data, categoriesLoading: false });
    } catch (error) {
      set({ categoriesLoading: false });
    }
  },

  // Units
  fetchUnits: async () => {
    set({ unitsLoading: true });
    try {
      const response = await inventoryApi.getUnits({ per_page: 100 });
      set({ units: response.data, unitsLoading: false });
    } catch (error) {
      set({ unitsLoading: false });
    }
  },

  // Warehouses
  fetchWarehouses: async () => {
    set({ warehousesLoading: true });
    try {
      const response = await inventoryApi.getWarehouses({ per_page: 100 });
      set({ warehouses: response.data, warehousesLoading: false });
    } catch (error) {
      set({ warehousesLoading: false });
    }
  },

  createWarehouse: async (data) => {
    const warehouse = await inventoryApi.createWarehouse(data);
    const warehouses = [...get().warehouses, warehouse];
    set({ warehouses });
    return warehouse;
  },

  // Suppliers
  fetchSuppliers: async (params) => {
    set({ suppliersLoading: true });
    try {
      const response = await inventoryApi.getSuppliers(params);
      set({
        suppliers: response.data,
        suppliersPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        suppliersLoading: false,
      });
    } catch (error) {
      set({ suppliersLoading: false });
    }
  },

  createSupplier: async (data) => {
    const supplier = await inventoryApi.createSupplier(data);
    const suppliers = [...get().suppliers, supplier];
    set({ suppliers });
    return supplier;
  },

  // Purchase Orders
  fetchPurchaseOrders: async (params) => {
    set({ purchaseOrdersLoading: true });
    try {
      const response = await inventoryApi.getPurchaseOrders(params);
      set({
        purchaseOrders: response.data,
        purchaseOrdersPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        purchaseOrdersLoading: false,
      });
    } catch (error) {
      set({ purchaseOrdersLoading: false });
    }
  },

  createPurchaseOrder: async (data) => {
    const order = await inventoryApi.createPurchaseOrder(data);
    const purchaseOrders = [order, ...get().purchaseOrders];
    set({ purchaseOrders });
    return order;
  },

  approvePurchaseOrder: async (uuid) => {
    const order = await inventoryApi.approvePurchaseOrder(uuid);
    const purchaseOrders = get().purchaseOrders.map((o) => (o.id === uuid ? order : o));
    set({ purchaseOrders });
    return order;
  },

  // Stock Movements
  fetchStockMovements: async (params) => {
    set({ stockMovementsLoading: true });
    try {
      const response = await inventoryApi.getStockMovements(params);
      set({
        stockMovements: response.data,
        stockMovementsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        stockMovementsLoading: false,
      });
    } catch (error) {
      set({ stockMovementsLoading: false });
    }
  },

  transferStock: async (data) => {
    await inventoryApi.transferStock(data);
    get().fetchStockMovements();
  },

  adjustStock: async (data) => {
    await inventoryApi.adjustStock(data);
    get().fetchStockMovements();
  },

  // Assets
  fetchAssets: async (params) => {
    set({ assetsLoading: true });
    try {
      const response = await inventoryApi.getAssets(params);
      set({
        assets: response.data,
        assetsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        assetsLoading: false,
      });
    } catch (error) {
      set({ assetsLoading: false });
    }
  },

  fetchAsset: async (uuid) => {
    set({ assetsLoading: true });
    try {
      const asset = await inventoryApi.getAsset(uuid);
      set({ selectedAsset: asset, assetsLoading: false });
    } catch (error) {
      set({ assetsLoading: false });
    }
  },

  createAsset: async (data) => {
    const asset = await inventoryApi.createAsset(data);
    const assets = [...get().assets, asset];
    set({ assets });
    return asset;
  },

  allocateAsset: async (uuid, data) => {
    const asset = await inventoryApi.allocateAsset(uuid, data);
    const assets = get().assets.map((a) => (a.id === uuid ? asset : a));
    set({ assets, selectedAsset: asset });
    return asset;
  },

  // Asset Transfers
  fetchAssetTransfers: async (params) => {
    set({ assetTransfersLoading: true });
    try {
      const response = await inventoryApi.getAssetTransfers(params);
      set({ assetTransfers: response.data, assetTransfersLoading: false });
    } catch (error) {
      set({ assetTransfersLoading: false });
    }
  },

  createAssetTransfer: async (data) => {
    const transfer = await inventoryApi.createAssetTransfer(data);
    const assetTransfers = [...get().assetTransfers, transfer];
    set({ assetTransfers });
    return transfer;
  },

  approveAssetTransfer: async (uuid) => {
    await inventoryApi.approveAssetTransfer(uuid);
    get().fetchAssetTransfers();
  },

  completeAssetTransfer: async (uuid) => {
    await inventoryApi.completeAssetTransfer(uuid);
    get().fetchAssetTransfers();
    get().fetchAssets();
  },

  // Asset Maintenances
  fetchAssetMaintenances: async (params) => {
    set({ assetMaintenancesLoading: true });
    try {
      const response = await inventoryApi.getAssetMaintenances(params);
      set({ assetMaintenances: response.data, assetMaintenancesLoading: false });
    } catch (error) {
      set({ assetMaintenancesLoading: false });
    }
  },

  createAssetMaintenance: async (data) => {
    const maintenance = await inventoryApi.createAssetMaintenance(data);
    const assetMaintenances = [...get().assetMaintenances, maintenance];
    set({ assetMaintenances });
    return maintenance;
  },

  completeMaintenance: async (uuid, workDone) => {
    const maintenance = await inventoryApi.completeMaintenance(uuid, workDone);
    const assetMaintenances = get().assetMaintenances.map((m) => (m.id === uuid ? maintenance : m));
    set({ assetMaintenances });
    return maintenance;
  },

  // Reset
  resetState: () => set(initialState),
}));
