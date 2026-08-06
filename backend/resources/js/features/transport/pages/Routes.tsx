/**
 * Routes Page
 */

import React, { useEffect, useState } from 'react';
import { useTransportStore } from '../store/transportStore';
import { Link } from 'react-router-dom';
import { Plus, Search, Eye, Edit, Route as RouteIcon, MapPin } from 'lucide-react';
import { formatCurrency } from '@/lib/utils';

export const Routes: React.FC = () => {
  const { 
    routes, routesPagination, routesLoading, 
    fetchRoutes 
  } = useTransportStore();
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [page, setPage] = useState(1);

  useEffect(() => {
    const params: any = { page, search: search || undefined };
    if (statusFilter) params.status = statusFilter;
    fetchRoutes(params);
  }, [fetchRoutes, page, search, statusFilter]);

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Routes</h1>
          <p className="text-gray-500">Manage transport routes</p>
        </div>
        <Link
          to="/transport/routes/new"
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
        >
          <Plus className="w-4 h-4" />
          Create Route
        </Link>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by route code or name..."
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
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>

      {/* Routes Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {routesLoading ? (
          <div className="col-span-full flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : routes.length === 0 ? (
          <div className="col-span-full flex flex-col items-center justify-center h-64">
            <RouteIcon className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No routes found</p>
            <Link
              to="/transport/routes/new"
              className="mt-4 text-primary-600 hover:text-primary-700"
            >
              Create your first route
            </Link>
          </div>
        ) : (
          routes.map((route) => (
            <div key={route.id} className="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
              <div className="flex items-start justify-between">
                <div className="flex items-center gap-3">
                  <div className="p-3 bg-orange-100 rounded-lg">
                    <RouteIcon className="w-6 h-6 text-orange-600" />
                  </div>
                  <div>
                    <h3 className="font-semibold text-gray-900">{route.route_name}</h3>
                    <p className="text-sm text-gray-500 font-mono">{route.route_code}</p>
                  </div>
                </div>
                <span className={`px-2 py-1 text-xs font-medium rounded ${
                  route.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'
                }`}>
                  {route.status === 'active' ? 'Active' : 'Inactive'}
                </span>
              </div>

              <div className="mt-4 space-y-2">
                <div className="flex items-center gap-2 text-sm">
                  <MapPin className="w-4 h-4 text-gray-400" />
                  <span className="text-gray-600">From: </span>
                  <span className="text-gray-900">{route.starting_point}</span>
                </div>
                <div className="flex items-center gap-2 text-sm">
                  <MapPin className="w-4 h-4 text-gray-400" />
                  <span className="text-gray-600">To: </span>
                  <span className="text-gray-900">{route.ending_point}</span>
                </div>
              </div>

              <div className="mt-4 pt-4 border-t grid grid-cols-3 gap-2 text-sm">
                <div className="text-center">
                  <p className="text-2xl font-bold text-gray-900">{route.distance || 0}</p>
                  <p className="text-gray-500">KM</p>
                </div>
                <div className="text-center">
                  <p className="text-2xl font-bold text-gray-900">{route.total_stops || 0}</p>
                  <p className="text-gray-500">Stops</p>
                </div>
                <div className="text-center">
                  <p className="text-2xl font-bold text-gray-900">{formatCurrency(route.monthly_fee)}</p>
                  <p className="text-gray-500">Fee</p>
                </div>
              </div>

              <div className="mt-4 pt-4 border-t flex justify-end gap-2">
                <Link
                  to={`/transport/routes/${route.id}`}
                  className="p-2 text-gray-400 hover:text-primary-600"
                >
                  <Eye className="w-4 h-4" />
                </Link>
                <Link
                  to={`/transport/routes/${route.id}/edit`}
                  className="p-2 text-gray-400 hover:text-primary-600"
                >
                  <Edit className="w-4 h-4" />
                </Link>
              </div>
            </div>
          ))
        )}
      </div>

      {/* Pagination */}
      {routesPagination && routesPagination.last_page > 1 && (
        <div className="bg-white rounded-xl p-4 shadow-sm flex items-center justify-between">
          <p className="text-sm text-gray-500">
            Showing {((page - 1) * 20) + 1} to {Math.min(page * 20, routesPagination.total)} of {routesPagination.total}
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
              disabled={page === routesPagination.last_page}
              className="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50"
            >
              Next
            </button>
          </div>
        </div>
      )}
    </div>
  );
};
