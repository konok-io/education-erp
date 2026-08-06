/**
 * Vehicles Page
 */

import React, { useEffect, useState } from 'react';
import { useTransportStore } from '../store/transportStore';
import { Link } from 'react-router-dom';
import { Plus, Search, Eye, Edit, Trash2, Bus, AlertTriangle } from 'lucide-react';
import { VEHICLE_TYPES, VEHICLE_STATUSES } from '../types';
import { formatCurrency } from '@/lib/utils';

export const Vehicles: React.FC = () => {
  const { 
    vehicles, vehiclesPagination, vehiclesLoading, 
    fetchVehicles, updateVehicleStatus 
  } = useTransportStore();
  const [search, setSearch] = useState('');
  const [typeFilter, setTypeFilter] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [page, setPage] = useState(1);

  useEffect(() => {
    const params: any = { page, search: search || undefined };
    if (typeFilter) params.vehicle_type = typeFilter;
    if (statusFilter) params.status = statusFilter;
    fetchVehicles(params);
  }, [fetchVehicles, page, search, typeFilter, statusFilter]);

  const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
      active: 'bg-green-100 text-green-700',
      inactive: 'bg-gray-100 text-gray-700',
      maintenance: 'bg-yellow-100 text-yellow-700',
      reserved: 'bg-blue-100 text-blue-700',
      disposed: 'bg-red-100 text-red-700',
      accident: 'bg-red-100 text-red-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Vehicles</h1>
          <p className="text-gray-500">Manage transport vehicles</p>
        </div>
        <Link
          to="/transport/vehicles/new"
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
        >
          <Plus className="w-4 h-4" />
          Add Vehicle
        </Link>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by vehicle number or registration..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
            />
          </div>
          <select
            value={typeFilter}
            onChange={(e) => setTypeFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Types</option>
            {Object.entries(VEHICLE_TYPES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Status</option>
            {Object.entries(VEHICLE_STATUSES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Vehicles Table */}
      <div className="bg-white rounded-xl shadow-sm overflow-hidden">
        {vehiclesLoading ? (
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : vehicles.length === 0 ? (
          <div className="flex flex-col items-center justify-center h-64">
            <Bus className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No vehicles found</p>
            <Link
              to="/transport/vehicles/new"
              className="mt-4 text-primary-600 hover:text-primary-700"
            >
              Add your first vehicle
            </Link>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehicle</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fuel</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Odometer</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {vehicles.map((vehicle) => (
                  <tr key={vehicle.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-blue-100 rounded flex items-center justify-center">
                          <Bus className="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                          <p className="font-medium text-gray-900">{vehicle.vehicle_number}</p>
                          <p className="text-sm text-gray-500">{vehicle.registration_number}</p>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <span className="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded">
                        {VEHICLE_TYPES[vehicle.vehicle_type as keyof typeof VEHICLE_TYPES] || vehicle.vehicle_type}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {vehicle.seat_capacity} seats
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {vehicle.fuel_type}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {vehicle.current_odometer || '-'}
                    </td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2">
                        <span className={`px-2 py-1 text-xs font-medium rounded ${getStatusColor(vehicle.status)}`}>
                          {VEHICLE_STATUSES[vehicle.status as keyof typeof VEHICLE_STATUSES] || vehicle.status}
                        </span>
                        {vehicle.maintenance_due && (
                          <Wrench className="w-4 h-4 text-yellow-500" title="Maintenance Due" />
                        )}
                      </div>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <div className="flex items-center justify-end gap-2">
                        <Link
                          to={`/transport/vehicles/${vehicle.id}`}
                          className="p-2 text-gray-400 hover:text-primary-600"
                        >
                          <Eye className="w-4 h-4" />
                        </Link>
                        <Link
                          to={`/transport/vehicles/${vehicle.id}/edit`}
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
        {vehiclesPagination && vehiclesPagination.last_page > 1 && (
          <div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p className="text-sm text-gray-500">
              Showing {((page - 1) * 20) + 1} to {Math.min(page * 20, vehiclesPagination.total)} of {vehiclesPagination.total}
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
                disabled={page === vehiclesPagination.last_page}
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
