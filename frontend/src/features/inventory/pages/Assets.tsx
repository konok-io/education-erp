/**
 * Assets Page
 */

import React, { useEffect, useState } from 'react';
import { useInventoryStore } from '../store/inventoryStore';
import { Link } from 'react-router-dom';
import { Plus, Search, Eye, Edit, Box, Shield, AlertTriangle, CheckCircle } from 'lucide-react';
import { ASSET_STATUSES, ASSET_CATEGORIES } from '../types';
import { formatCurrency } from '@/lib/utils';

export const Assets: React.FC = () => {
  const { 
    assets, assetsPagination, assetsLoading,
    fetchAssets 
  } = useInventoryStore();
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [categoryFilter, setCategoryFilter] = useState('');
  const [page, setPage] = useState(1);

  useEffect(() => {
    const params: any = { page };
    if (search) params.search = search;
    if (statusFilter) params.status = statusFilter;
    if (categoryFilter) params.category = categoryFilter;
    fetchAssets(params);
  }, [fetchAssets, page, search, statusFilter, categoryFilter]);

  const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
      available: 'bg-green-100 text-green-700',
      allocated: 'bg-blue-100 text-blue-700',
      repair: 'bg-yellow-100 text-yellow-700',
      maintenance: 'bg-orange-100 text-orange-700',
      lost: 'bg-red-100 text-red-700',
      disposed: 'bg-gray-100 text-gray-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Assets</h1>
          <p className="text-gray-500">Manage organization assets</p>
        </div>
        <Link
          to="/inventory/assets/new"
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
        >
          <Plus className="w-4 h-4" />
          Register Asset
        </Link>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by name, code, or serial..."
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
            {Object.entries(ASSET_STATUSES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
          <select
            value={categoryFilter}
            onChange={(e) => setCategoryFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Categories</option>
            {Object.entries(ASSET_CATEGORIES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Assets Table */}
      <div className="bg-white rounded-xl shadow-sm overflow-hidden">
        {assetsLoading ? (
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : assets.length === 0 ? (
          <div className="flex flex-col items-center justify-center h-64">
            <Box className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No assets found</p>
            <Link
              to="/inventory/assets/new"
              className="mt-4 text-primary-600 hover:text-primary-700"
            >
              Register your first asset
            </Link>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchase Cost</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {assets.map((asset) => (
                  <tr key={asset.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-primary-100 rounded flex items-center justify-center">
                          <Box className="w-5 h-5 text-primary-600" />
                        </div>
                        <div>
                          <p className="font-medium text-gray-900">{asset.asset_name}</p>
                          <p className="text-sm text-gray-500">{asset.serial_number || '-'}</p>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-sm font-mono text-gray-500">{asset.asset_code}</td>
                    <td className="px-6 py-4">
                      <span className="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded">
                        {ASSET_CATEGORIES[asset.category] || asset.category}
                      </span>
                    </td>
                    <td className="px-6 py-4">
                      {asset.assigned_to_name ? (
                        <p className="text-sm text-gray-900">{asset.assigned_to_name}</p>
                      ) : (
                        <span className="text-sm text-gray-400">Unassigned</span>
                      )}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-900">
                      {asset.purchase_cost ? formatCurrency(asset.purchase_cost) : '-'}
                    </td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2">
                        <span className={`px-2 py-1 text-xs font-medium rounded ${getStatusColor(asset.status)}`}>
                          {ASSET_STATUSES[asset.status as keyof typeof ASSET_STATUSES] || asset.status}
                        </span>
                        {asset.is_under_warranty && (
                          <Shield className="w-4 h-4 text-green-500" title="Under Warranty" />
                        )}
                      </div>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <div className="flex items-center justify-end gap-2">
                        <Link
                          to={`/inventory/assets/${asset.id}`}
                          className="p-2 text-gray-400 hover:text-primary-600"
                        >
                          <Eye className="w-4 h-4" />
                        </Link>
                        <Link
                          to={`/inventory/assets/${asset.id}/edit`}
                          className="p-2 text-gray-400 hover:text-primary-600"
                        >
                          <Edit className="w-4 h-4" />
                        </Link>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        {/* Pagination */}
        {assetsPagination && assetsPagination.last_page > 1 && (
          <div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p className="text-sm text-gray-500">
              Showing {((page - 1) * 20) + 1} to {Math.min(page * 20, assetsPagination.total)} of {assetsPagination.total}
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
                disabled={page === assetsPagination.last_page}
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
