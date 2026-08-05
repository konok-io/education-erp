/**
 * Hostels Page
 */

import React, { useEffect, useState } from 'react';
import { useHostelStore } from '../store/hostelStore';
import { Link } from 'react-router-dom';
import { Plus, Search, Eye, Edit, Building2 } from 'lucide-react';
import { HOSTEL_TYPES } from '../types';

export const Hostels: React.FC = () => {
  const { 
    hostels, hostelsPagination, hostelsLoading, 
    fetchHostels 
  } = useHostelStore();
  const [search, setSearch] = useState('');
  const [typeFilter, setTypeFilter] = useState('');
  const [genderFilter, setGenderFilter] = useState('');
  const [page, setPage] = useState(1);

  useEffect(() => {
    const params: any = { page, search: search || undefined };
    if (typeFilter) params.hostel_type = typeFilter;
    if (genderFilter) params.gender = genderFilter;
    fetchHostels(params);
  }, [fetchDashboard, page, search, typeFilter, genderFilter]);

  const getOccupancyColor = (rate: number) => {
    if (rate >= 80) return 'text-green-600';
    if (rate >= 50) return 'text-yellow-600';
    return 'text-red-600';
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Hostels</h1>
          <p className="text-gray-500">Manage hostel buildings</p>
        </div>
        <Link
          to="/hostel/hostels/new"
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
        >
          <Plus className="w-4 h-4" />
          Add Hostel
        </Link>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by name or code..."
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
            {Object.entries(HOSTEL_TYPES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
          <select
            value={genderFilter}
            onChange={(e) => setGenderFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Gender</option>
            <option value="boys">Boys</option>
            <option value="girls">Girls</option>
            <option value="co-ed">Co-Ed</option>
          </select>
        </div>
      </div>

      {/* Hostels Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {hostelsLoading ? (
          <div className="col-span-full flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : hostels.length === 0 ? (
          <div className="col-span-full flex flex-col items-center justify-center h-64">
            <Building2 className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No hostels found</p>
            <Link
              to="/hostel/hostels/new"
              className="mt-4 text-primary-600 hover:text-primary-700"
            >
              Add your first hostel
            </Link>
          </div>
        ) : (
          hostels.map((hostel) => (
            <div key={hostel.id} className="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
              <div className="flex items-start justify-between">
                <div className="flex items-center gap-3">
                  <div className="p-3 bg-blue-100 rounded-lg">
                    <Building2 className="w-6 h-6 text-blue-600" />
                  </div>
                  <div>
                    <h3 className="font-semibold text-gray-900">{hostel.hostel_name}</h3>
                    <p className="text-sm text-gray-500 font-mono">{hostel.hostel_code}</p>
                  </div>
                </div>
                <span className={`px-2 py-1 text-xs font-medium rounded ${
                  hostel.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'
                }`}>
                  {hostel.status === 'active' ? 'Active' : 'Inactive'}
                </span>
              </div>

              <div className="mt-4 space-y-2">
                <div className="flex items-center justify-between text-sm">
                  <span className="text-gray-500">Type:</span>
                  <span className="text-gray-900">{HOSTEL_TYPES[hostel.hostel_type as keyof typeof HOSTEL_TYPES]}</span>
                </div>
                <div className="flex items-center justify-between text-sm">
                  <span className="text-gray-500">Gender:</span>
                  <span className="text-gray-900 capitalize">{hostel.gender}</span>
                </div>
              </div>

              <div className="mt-4 pt-4 border-t grid grid-cols-2 gap-2 text-sm">
                <div className="text-center">
                  <p className="text-2xl font-bold text-gray-900">{hostel.total_rooms}</p>
                  <p className="text-gray-500">Rooms</p>
                </div>
                <div className="text-center">
                  <p className="text-2xl font-bold text-gray-900">{hostel.total_beds}</p>
                  <p className="text-gray-500">Beds</p>
                </div>
              </div>

              <div className="mt-4">
                <div className="flex items-center justify-between text-sm mb-1">
                  <span className="text-gray-500">Occupancy:</span>
                  <span className={`font-medium ${getOccupancyColor(hostel.occupancy_rate ?? 0)}`}>
                    {hostel.occupancy_rate ?? 0}%
                  </span>
                </div>
                <div className="w-full bg-gray-200 rounded-full h-2">
                  <div
                    className="bg-green-500 h-2 rounded-full"
                    style={{ width: `${hostel.occupancy_rate ?? 0}%` }}
                  ></div>
                </div>
              </div>

              <div className="mt-4 pt-4 border-t flex justify-end gap-2">
                <Link
                  to={`/hostel/hostels/${hostel.id}`}
                  className="p-2 text-gray-400 hover:text-primary-600"
                >
                  <Eye className="w-4 h-4" />
                </Link>
                <Link
                  to={`/hostel/hostels/${hostel.id}/edit`}
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
      {hostelsPagination && hostelsPagination.last_page > 1 && (
        <div className="bg-white rounded-xl p-4 shadow-sm flex items-center justify-between">
          <p className="text-sm text-gray-500">
            Showing {((page - 1) * 20) + 1} to {Math.min(page * 20, hostelsPagination.total)} of {hostelsPagination.total}
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
              disabled={page === hostelsPagination.last_page}
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
