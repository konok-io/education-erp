/**
 * Purchase Orders Page
 */

import React, { useEffect, useState } from 'react';
import { useInventoryStore } from '../store/inventoryStore';
import { Link } from 'react-router-dom';
import { Plus, Search, Eye, Edit, ShoppingCart, Check, X } from 'lucide-react';
import { PURCHASE_ORDER_STATUSES } from '../types';
import { formatCurrency } from '@/lib/utils';

export const PurchaseOrders: React.FC = () => {
  const { 
    purchaseOrders, purchaseOrdersPagination, purchaseOrdersLoading,
    fetchPurchaseOrders, approvePurchaseOrder 
  } = useInventoryStore();
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [page, setPage] = useState(1);

  useEffect(() => {
    fetchPurchaseOrders({ 
      page, 
      status: statusFilter || undefined 
    });
  }, [fetchPurchaseOrders, page, statusFilter]);

  const handleApprove = async (uuid: string) => {
    await approvePurchaseOrder(uuid);
    fetchPurchaseOrders({ page, status: statusFilter || undefined });
  };

  const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
      draft: 'bg-gray-100 text-gray-700',
      pending: 'bg-yellow-100 text-yellow-700',
      approved: 'bg-blue-100 text-blue-700',
      ordered: 'bg-indigo-100 text-indigo-700',
      partially_received: 'bg-purple-100 text-purple-700',
      received: 'bg-green-100 text-green-700',
      cancelled: 'bg-red-100 text-red-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Purchase Orders</h1>
          <p className="text-gray-500">Manage purchase orders and suppliers</p>
        </div>
        <Link
          to="/inventory/purchase-orders/new"
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
        >
          <Plus className="w-4 h-4" />
          Create PO
        </Link>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by PO number or supplier..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
            />
          </div>
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Status</option>
            {Object.entries(PURCHASE_ORDER_STATUSES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Purchase Orders Table */}
      <div className="bg-white rounded-xl shadow-sm overflow-hidden">
        {purchaseOrdersLoading ? (
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : purchaseOrders.length === 0 ? (
          <div className="flex flex-col items-center justify-center h-64">
            <ShoppingCart className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No purchase orders found</p>
            <Link
              to="/inventory/purchase-orders/new"
              className="mt-4 text-primary-600 hover:text-primary-700"
            >
              Create your first PO
            </Link>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PO No</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order Date</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {purchaseOrders.map((po) => (
                  <tr key={po.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4">
                      <span className="font-mono font-medium text-gray-900">{po.po_no}</span>
                    </td>
                    <td className="px-6 py-4">
                      <p className="font-medium text-gray-900">{po.supplier?.name || '-'}</p>
                      <p className="text-sm text-gray-500">{po.supplier?.code || ''}</p>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {new Date(po.order_date).toLocaleDateString()}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {po.items?.length || 0} items
                    </td>
                    <td className="px-6 py-4 text-sm font-medium text-gray-900">
                      {formatCurrency(po.total)}
                    </td>
                    <td className="px-6 py-4">
                      <span className={`px-2 py-1 text-xs font-medium rounded ${getStatusColor(po.status)}`}>
                        {PURCHASE_ORDER_STATUSES[po.status as keyof typeof PURCHASE_ORDER_STATUSES] || po.status}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <div className="flex items-center justify-end gap-2">
                        {po.status === 'pending' && (
                          <button
                            onClick={() => handleApprove(po.id)}
                            className="p-2 text-green-600 hover:bg-green-50 rounded"
                            title="Approve"
                          >
                            <Check className="w-4 h-4" />
                          </button>
                        )}
                        <Link
                          to={`/inventory/purchase-orders/${po.id}`}
                          className="p-2 text-gray-400 hover:text-primary-600"
                        >
                          <Eye className="w-4 h-4" />
                        </Link>
                        {po.status === 'draft' && (
                          <Link
                            to={`/inventory/purchase-orders/${po.id}/edit`}
                            className="p-2 text-gray-400 hover:text-primary-600"
                          >
                            <Edit className="w-4 h-4" />
                          </Link>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        {/* Pagination */}
        {purchaseOrdersPagination && purchaseOrdersPagination.last_page > 1 && (
          <div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p className="text-sm text-gray-500">
              Showing {((page - 1) * 20) + 1} to {Math.min(page * 20, purchaseOrdersPagination.total)} of {purchaseOrdersPagination.total}
            </p>
            <div className="flex gap-2">
              <button
                onClick={() => setPage(page - 1)}
                disabled={page === 1}
                className="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50"
              >
                Previous
              </button>
              <button
                onClick={() => setPage(page + 1)}
                disabled={page === purchaseOrdersPagination.last_page}
                className="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50"
              >
                Next
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
