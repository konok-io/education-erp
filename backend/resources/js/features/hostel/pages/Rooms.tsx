/**
 * Rooms Page
 */

import React, { useEffect, useState } from 'react';
import { useHostelStore } from '../store/hostelStore';
import { Link } from 'react-router-dom';
import { Plus, Search, Eye, Edit, DoorOpen, Bed } from 'lucide-react';
import { ROOM_TYPES, ROOM_STATUSES } from '../types';
import { formatCurrency } from '@/lib/utils';

export const Rooms: React.FC = () => {
  const { 
    rooms, roomsPagination, roomsLoading, 
    fetchRooms 
  } = useHostelStore();
  const [search, setSearch] = useState('');
  const [typeFilter, setTypeFilter] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [page, setPage] = useState(1);

  useEffect(() => {
    const params: any = { page };
    if (search) params.search = search;
    if (typeFilter) params.room_type = typeFilter;
    if (statusFilter) params.status = statusFilter;
    fetchRooms(params);
  }, [fetchRooms, page, search, typeFilter, statusFilter]);

  const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
      available: 'bg-green-100 text-green-700',
      partial: 'bg-yellow-100 text-yellow-700',
      full: 'bg-blue-100 text-blue-700',
      maintenance: 'bg-red-100 text-red-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Rooms</h1>
          <p className="text-gray-500">Manage hostel rooms</p>
        </div>
        <Link
          to="/hostel/rooms/new"
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
        >
          <Plus className="w-4 h-4" />
          Add Room
        </Link>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by room number or code..."
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
            {Object.entries(ROOM_TYPES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Status</option>
            {Object.entries(ROOM_STATUSES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Rooms Table */}
      <div className="bg-white rounded-xl shadow-sm overflow-hidden">
        {roomsLoading ? (
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : rooms.length === 0 ? (
          <div className="flex flex-col items-center justify-center h-64">
            <DoorOpen className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No rooms found</p>
            <Link
              to="/hostel/rooms/new"
              className="mt-4 text-primary-600 hover:text-primary-700"
            >
              Add your first room
            </Link>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hostel</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monthly Fee</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {rooms.map((room) => (
                  <tr key={room.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-blue-100 rounded flex items-center justify-center">
                          <DoorOpen className="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                          <p className="font-medium text-gray-900">{room.room_number}</p>
                          <p className="text-sm text-gray-500 font-mono">{room.room_code}</p>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <span className="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded">
                        {ROOM_TYPES[room.room_type as keyof typeof ROOM_TYPES] || room.room_type}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {room.hostel?.hostel_name || '-'}
                    </td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2">
                        <Bed className="w-4 h-4 text-gray-400" />
                        <span className="text-sm text-gray-900">
                          {room.occupied}/{room.capacity}
                        </span>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {formatCurrency(room.monthly_fee)}
                    </td>
                    <td className="px-6 py-4">
                      <span className={`px-2 py-1 text-xs font-medium rounded ${getStatusColor(room.status)}`}>
                        {ROOM_STATUSES[room.status as keyof typeof ROOM_STATUSES] || room.status}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <div className="flex items-center justify-end gap-2">
                        <Link
                          to={`/hostel/rooms/${room.id}`}
                          className="p-2 text-gray-400 hover:text-primary-600"
                        >
                          <Eye className="w-4 h-4" />
                        </Link>
                        <Link
                          to={`/hostel/rooms/${room.id}/edit`}
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
        {roomsPagination && roomsPagination.last_page > 1 && (
          <div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p className="text-sm text-gray-500">
              Showing {((page - 1) * 20) + 1} to {Math.min(page * 20, roomsPagination.total)} of {roomsPagination.total}
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
                disabled={page === roomsPagination.last_page}
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
