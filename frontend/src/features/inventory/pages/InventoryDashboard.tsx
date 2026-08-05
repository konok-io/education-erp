/**
 * Inventory Dashboard Page
 */

import React, { useEffect } from 'react';
import { useInventoryStore } from '../store/inventoryStore';
import { Link } from 'react-router-dom';
import { 
  Package, Warehouse, Truck, Box, AlertTriangle,
  ShoppingCart, ArrowRightLeft, Wrench, ShieldCheck,
  ArrowUpRight, ArrowDownRight, TrendingUp
} from 'lucide-react';

export const InventoryDashboard: React.FC = () => {
  const { dashboard, dashboardLoading, fetchDashboard } = useInventoryStore();

  useEffect(() => {
    fetchDashboard();
  }, [fetchDashboard]);

  if (dashboardLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  const stats = [
    {
      title: 'Total Products',
      value: dashboard?.total_products ?? 0,
      icon: Package,
      color: 'bg-blue-500',
      change: '+12%',
      changeType: 'up',
      link: '/inventory/products',
    },
    {
      title: 'Warehouses',
      value: dashboard?.total_warehouses ?? 0,
      icon: Warehouse,
      color: 'bg-green-500',
      change: '+2',
      changeType: 'up',
      link: '/inventory/warehouses',
    },
    {
      title: 'Suppliers',
      value: dashboard?.total_suppliers ?? 0,
      icon: Truck,
      color: 'bg-purple-500',
      change: '+5',
      changeType: 'up',
      link: '/inventory/suppliers',
    },
    {
      title: 'Total Assets',
      value: dashboard?.total_assets ?? 0,
      icon: Box,
      color: 'bg-orange-500',
      change: '+8%',
      changeType: 'up',
      link: '/inventory/assets',
    },
    {
      title: 'Low Stock',
      value: dashboard?.low_stock_products ?? 0,
      icon: AlertTriangle,
      color: 'bg-yellow-500',
      change: dashboard?.low_stock_products ? `${dashboard.low_stock_products}` : '0',
      changeType: 'neutral',
      link: '/inventory/products?filter=low_stock',
      alert: (dashboard?.low_stock_products ?? 0) > 0,
    },
    {
      title: 'Out of Stock',
      value: dashboard?.out_of_stock_products ?? 0,
      icon: AlertTriangle,
      color: 'bg-red-500',
      change: dashboard?.out_of_stock_products ? `${dashboard.out_of_stock_products}` : '0',
      changeType: 'down',
      link: '/inventory/products?filter=out_of_stock',
      alert: (dashboard?.out_of_stock_products ?? 0) > 0,
    },
    {
      title: 'Pending POs',
      value: dashboard?.pending_purchase_orders ?? 0,
      icon: ShoppingCart,
      color: 'bg-indigo-500',
      change: dashboard?.pending_purchase_orders ? `${dashboard.pending_purchase_orders}` : '0',
      changeType: 'neutral',
      link: '/inventory/purchase-orders?status=pending',
    },
    {
      title: 'Pending Transfers',
      value: dashboard?.pending_asset_transfers ?? 0,
      icon: ArrowRightLeft,
      color: 'bg-teal-500',
      change: dashboard?.pending_asset_transfers ? `${dashboard.pending_asset_transfers}` : '0',
      changeType: 'neutral',
      link: '/inventory/asset-transfers?status=pending',
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Inventory Dashboard</h1>
          <p className="text-gray-500">Overview of inventory, purchase & asset management</p>
        </div>
        <div className="flex gap-3">
          <Link
            to="/inventory/products/new"
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
          >
            <Package className="w-4 h-4" />
            Add Product
          </Link>
          <Link
            to="/inventory/purchase-orders/new"
            className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            <ShoppingCart className="w-4 h-4" />
            Create PO
          </Link>
        </div>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {stats.map((stat) => (
          <Link
            key={stat.title}
            to={stat.link}
            className={`bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow ${
              stat.alert ? 'ring-2 ring-red-200' : ''
            }`}
          >
            <div className="flex items-start justify-between">
              <div className={`${stat.color} p-3 rounded-lg`}>
                <stat.icon className="w-6 h-6 text-white" />
              </div>
              <div className={`flex items-center gap-1 text-sm ${
                stat.changeType === 'up' ? 'text-green-600' : 
                stat.changeType === 'down' ? 'text-red-600' : 'text-gray-500'
              }`}>
                {stat.changeType === 'up' && <ArrowUpRight className="w-4 h-4" />}
                {stat.changeType === 'down' && <ArrowDownRight className="w-4 h-4" />}
                <span>{stat.change}</span>
              </div>
            </div>
            <div className="mt-4">
              <p className="text-3xl font-bold text-gray-900">{stat.value}</p>
              <p className="text-gray-500 text-sm">{stat.title}</p>
            </div>
          </Link>
        ))}
      </div>

      {/* Alerts */}
      {(dashboard?.low_stock_products ?? 0) > 0 || (dashboard?.out_of_stock_products ?? 0) > 0 ? (
        <div className="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
          <div className="flex items-center gap-3">
            <AlertTriangle className="w-5 h-5 text-yellow-600" />
            <div>
              <p className="font-medium text-yellow-800">
                Stock Alert
              </p>
              <p className="text-sm text-yellow-700">
                {(dashboard?.low_stock_products ?? 0) > 0 && `${dashboard?.low_stock_products} products are low on stock. `}
                {(dashboard?.out_of_stock_products ?? 0) > 0 && `${dashboard?.out_of_stock_products} products are out of stock.`}
              </p>
            </div>
            <Link
              to="/inventory/products?filter=low_stock"
              className="ml-auto px-4 py-2 bg-yellow-200 text-yellow-800 rounded-lg hover:bg-yellow-300"
            >
              View Products
            </Link>
          </div>
        </div>
      ) : null}

      {/* Quick Actions & Alerts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Quick Actions */}
        <div className="bg-white rounded-xl p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
          <div className="grid grid-cols-2 gap-4">
            <Link
              to="/inventory/products/new"
              className="flex items-center gap-3 p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors"
            >
              <div className="p-2 bg-blue-100 rounded-lg">
                <Package className="w-5 h-5 text-blue-600" />
              </div>
              <div>
                <p className="font-medium text-gray-900">Add Product</p>
                <p className="text-xs text-gray-500">Create new product</p>
              </div>
            </Link>
            <Link
              to="/inventory/purchase-orders/new"
              className="flex items-center gap-3 p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors"
            >
              <div className="p-2 bg-green-100 rounded-lg">
                <ShoppingCart className="w-5 h-5 text-green-600" />
              </div>
              <div>
                <p className="font-medium text-gray-900">Purchase Order</p>
                <p className="text-xs text-gray-500">Create new PO</p>
              </div>
            </Link>
            <Link
              to="/inventory/stock-transfer"
              className="flex items-center gap-3 p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors"
            >
              <div className="p-2 bg-purple-100 rounded-lg">
                <ArrowRightLeft className="w-5 h-5 text-purple-600" />
              </div>
              <div>
                <p className="font-medium text-gray-900">Stock Transfer</p>
                <p className="text-xs text-gray-500">Transfer between warehouses</p>
              </div>
            </Link>
            <Link
              to="/inventory/assets/new"
              className="flex items-center gap-3 p-4 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors"
            >
              <div className="p-2 bg-orange-100 rounded-lg">
                <Box className="w-5 h-5 text-orange-600" />
              </div>
              <div>
                <p className="font-medium text-gray-900">Register Asset</p>
                <p className="text-xs text-gray-500">Add new asset</p>
              </div>
            </Link>
          </div>
        </div>

        {/* Pending Items */}
        <div className="bg-white rounded-xl p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Pending Items</h2>
          <div className="space-y-4">
            {dashboard?.pending_purchase_orders ? (
              <Link
                to="/inventory/purchase-orders?status=pending"
                className="flex items-center justify-between p-3 rounded-lg border hover:bg-gray-50"
              >
                <div className="flex items-center gap-3">
                  <ShoppingCart className="w-5 h-5 text-indigo-600" />
                  <div>
                    <p className="font-medium text-gray-900">Purchase Orders</p>
                    <p className="text-sm text-gray-500">Awaiting approval</p>
                  </div>
                </div>
                <span className="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-sm font-medium">
                  {dashboard.pending_purchase_orders}
                </span>
              </Link>
            ) : null}
            
            {dashboard?.pending_asset_transfers ? (
              <Link
                to="/inventory/asset-transfers?status=pending"
                className="flex items-center justify-between p-3 rounded-lg border hover:bg-gray-50"
              >
                <div className="flex items-center gap-3">
                  <ArrowRightLeft className="w-5 h-5 text-teal-600" />
                  <div>
                    <p className="font-medium text-gray-900">Asset Transfers</p>
                    <p className="text-sm text-gray-500">Awaiting approval</p>
                  </div>
                </div>
                <span className="px-3 py-1 bg-teal-100 text-teal-700 rounded-full text-sm font-medium">
                  {dashboard.pending_asset_transfers}
                </span>
              </Link>
            ) : null}

            {dashboard?.scheduled_maintenances ? (
              <Link
                to="/inventory/maintenances"
                className="flex items-center justify-between p-3 rounded-lg border hover:bg-gray-50"
              >
                <div className="flex items-center gap-3">
                  <Wrench className="w-5 h-5 text-orange-600" />
                  <div>
                    <p className="font-medium text-gray-900">Scheduled Maintenance</p>
                    <p className="text-sm text-gray-500">Upcoming tasks</p>
                  </div>
                </div>
                <span className="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-medium">
                  {dashboard.scheduled_maintenances}
                </span>
              </Link>
            ) : null}

            {dashboard?.upcoming_warranty_expiry ? (
              <Link
                to="/inventory/assets?filter=warranty_expiry"
                className="flex items-center justify-between p-3 rounded-lg border hover:bg-gray-50"
              >
                <div className="flex items-center gap-3">
                  <ShieldCheck className="w-5 h-5 text-green-600" />
                  <div>
                    <p className="font-medium text-gray-900">Warranty Expiring</p>
                    <p className="text-sm text-gray-500">Next 30 days</p>
                  </div>
                </div>
                <span className="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">
                  {dashboard.upcoming_warranty_expiry}
                </span>
              </Link>
            ) : null}
          </div>
        </div>
      </div>
    </div>
  );
};
