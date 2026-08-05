/**
 * Visitors Page
 */

import React, { useEffect, useState } from 'react';
import { useHostelStore } from '../store/hostelStore';
import { Link } from 'react-router-dom';
import { Plus, Search, Eye, Users, Check, LogIn, LogOut } from 'lucide-react';
import { VISITOR_STATUSES } from '../types';

export const Visitors: React.FC = () => {
  const { 
    visitors, visitorsPagination, visitorsLoading, 
    fetchVisitors, approveVisitor, checkInVisitor, checkOutVisitor 
  } = useHostelStore();
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [dateFilter, setDateFilter] = useState('');
  const [page, setPage] = useState(1);

  useEffect(() => {
    const params: any = { page };
    if (search) params.search = search;
    if (statusFilter) params.status = statusFilter;
    if (dateFilter) params.visit_date = dateFilter;
    fetchVisitors(params);
  }, [fetchVisitors, page, search, statusFilter, dateFilter]);

  const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
      pending: 'bg-yellow-100 text-yellow-700',
      approved: 'bg-blue-100 text-blue-700',
      checked_in: 'bg-green-100 text-green-700',
      checked_out: 'bg-gray-100 text-gray-700',
      cancelled: 'bg-red-100 text-red-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
  };

  const handleApprove = async (uuid: string) => {
    await approveVisitor(uuid);
  };

  const handleCheckIn = async (uuid: string) => {
    await checkInVisitor(uuid);
  };

  const handleCheckOut = async (uuid: string) => {
    await checkOutVisitor(uuid);
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Visitors</h1>
          <p className="text-gray-500">Manage hostel visitors</p>
        </div>
        <Link
          to="/hostel/visitors/new"
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
        >
          <Plus className="w-4 h-4" />
          Register Visitor
        </Link>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by visitor name or ID..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
            />
          </div>
          <input
            type="date"
            value={dateFilter}
            onChange={(e) => setDateFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          />
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Status</option>
            {Object.entries(VISITOR_STATUSES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Visitors Table */}
      <div className="bg-white rounded-xl shadow-sm overflow-hidden">
        {visitorsLoading ? (
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : visitors.length === 0 ? (
          <div className="flex flex-col items-center justify-center h-64">
            <Users className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No visitors found</p>
            <Link
              to="/hostel/visitors/new"
              className="mt-4 text-primary-600 hover:text-primary-700"
            >
              Register your first visitor
            </Link>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Visitor</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purpose</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {visitors.map((visitor) => (
                  <tr key={visitor.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                          <Users className="w-5 h-5 text-orange-600" />
                        </div>
                        <div>
                          <p className="font-medium text-gray-900">{visitor.visitor_name}</p>
                          <p className="text-sm text-gray-500 font-mono">{visitor.visitor_no}</p>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {visitor.purpose_label || visitor.purpose}
                    </td>
                    <td className="px-6 py-4">
                      <p className="text-sm text-gray-900">{visitor.student_name || '-'}</p>
                      <p className="text-xs text-gray-500">{visitor.student_class || ''}</p>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {visitor.visit_date ? new Date(visitor.visit_date).toLocaleDateString() : '-'}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      <p>In: {visitor.check_in_time || '-'}</p>
                      <p className="text-xs">Out: {visitor.check_out_time || '-'}</p>
                    </td>
                    <td className="px-6 py-4">
                      <span className={`px-2 py-1 text-xs font-medium rounded ${getStatusColor(visitor.status)}`}>
                        {VISITOR_STATUSES[visitor.status as keyof typeof VISITOR_STATUSES] || visitor.status}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <div className="flex items-center justify-end gap-2">
                        {visitor.status === 'pending' && (
                          <button
                            onClick={() => handleApprove(visitor.id)}
                            className="p-2 text-green-600 hover:bg-green-50 rounded"
                            title="Approve"
                          >
                            <Check className="w-4 h-4" />
                          </button>
                        )}
                        {visitor.status === 'approved' && (
                          <button
                            onClick={() => handleCheckIn(visitor.id)}
                            className="p-2 text-blue-600 hover:bg-blue-50 rounded"
                            title="Check In"
                          >
                            <LogIn className="w-4 h-4" />
                          </button>
                        )}
                        {visitor.status === 'checked_in' && (
                          <button
                            onClick={() => handleCheckOut(visitor.id)}
                            className="p-2 text-red-600 hover:bg-red-50 rounded"
                            title="Check Out"
                          >
                            <LogOut className="w-4 h-4" />
                          </button>
                        )}
                        <Link
                          to={`/hostel/visitors/${visitor.id}`}
                          className="p-2 text-gray-400 hover:text-primary-600"
                        >
                          <Eye className="w-4 h-4" />
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
        {visitorsPagination && visitorsPagination.last_page > 1 && (
          <div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p className="text-sm text-gray-500">
              Showing {((page - 1) * 20) + 1} to {Math.min(page * 20, visitorsPagination.total)} of {visitorsPagination.total}
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
                disabled={page === visitorsPagination.last_page}
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
