import { useState, useEffect } from 'react';
import { getLeaves, approveLeave, rejectLeave, getLeaveTypes } from '../services/hrApi';
import type { Leave, LeaveType } from '../types';

export function LeaveManagement() {
  const [leaves, setLeaves] = useState<Leave[]>([]);
  const [leaveTypes, setLeaveTypes] = useState<LeaveType[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [filters, setFilters] = useState({
    status: 'pending',
  });
  const [showRejectModal, setShowRejectModal] = useState(false);
  const [selectedLeave, setSelectedLeave] = useState<Leave | null>(null);
  const [rejectReason, setRejectReason] = useState('');

  useEffect(() => {
    fetchData();
  }, [filters]);

  const fetchData = async () => {
    try {
      setLoading(true);
      const [leavesResponse, typesResponse] = await Promise.all([
        getLeaves(filters),
        getLeaveTypes(),
      ]);
      setLeaves(leavesResponse.data);
      setLeaveTypes(typesResponse);
      setError(null);
    } catch (err) {
      setError('Failed to load leave data');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleApprove = async (uuid: string) => {
    if (!confirm('Approve this leave request?')) return;
    try {
      await approveLeave(uuid);
      fetchData();
    } catch (err) {
      alert('Failed to approve leave');
      console.error(err);
    }
  };

  const handleRejectClick = (leave: Leave) => {
    setSelectedLeave(leave);
    setShowRejectModal(true);
  };

  const handleReject = async () => {
    if (!selectedLeave || !rejectReason) return;
    try {
      await rejectLeave(selectedLeave.id, rejectReason);
      setShowRejectModal(false);
      setSelectedLeave(null);
      setRejectReason('');
      fetchData();
    } catch (err) {
      alert('Failed to reject leave');
      console.error(err);
    }
  };

  const getStatusBadge = (status: string) => {
    const badges = {
      pending: 'bg-yellow-100 text-yellow-800',
      approved: 'bg-green-100 text-green-800',
      rejected: 'bg-red-100 text-red-800',
      cancelled: 'bg-gray-100 text-gray-800',
    };
    return `px-2 py-1 text-xs font-medium rounded-full ${badges[status as keyof typeof badges] || 'bg-gray-100'}`;
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">Leave Management</h1>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select
              value={filters.status}
              onChange={(e) => setFilters({ ...filters, status: e.target.value })}
              className="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
          <div className="flex items-end">
            <button
              onClick={fetchData}
              className="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
            >
              Apply Filters
            </button>
          </div>
        </div>
      </div>

      {/* Leave Types Legend */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <h3 className="text-sm font-medium text-gray-700 mb-2">Leave Types</h3>
        <div className="flex flex-wrap gap-2">
          {leaveTypes.map((type) => (
            <span
              key={type.id}
              className="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded"
            >
              {type.code}: {type.leave_days} days
            </span>
          ))}
        </div>
      </div>

      {/* Leave Table */}
      {loading ? (
        <div className="flex items-center justify-center h-64">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>
      ) : error ? (
        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
          {error}
        </div>
      ) : (
        <div className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Leave No</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {leaves.map((leave) => (
                <tr key={leave.id} className="hover:bg-gray-50">
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-sm font-medium text-gray-900">
                      {leave.employee?.name || 'N/A'}
                    </div>
                    <div className="text-sm text-gray-500">
                      {leave.employee?.employee_no}
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {leave.leave_no}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                      {leave.leave_type?.name || 'N/A'}
                    </span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {leave.start_date} - {leave.end_date}
                    <span className="ml-1 text-gray-400">({leave.total_days} days)</span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className={getStatusBadge(leave.status)}>
                      {leave.status}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                    {leave.reason}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm">
                    {leave.status === 'pending' && (
                      <div className="flex gap-2">
                        <button
                          onClick={() => handleApprove(leave.id)}
                          className="text-green-600 hover:text-green-700"
                        >
                          Approve
                        </button>
                        <button
                          onClick={() => handleRejectClick(leave)}
                          className="text-red-600 hover:text-red-700"
                        >
                          Reject
                        </button>
                      </div>
                    )}
                  </td>
                </tr>
              ))}
              {leaves.length === 0 && (
                <tr>
                  <td colSpan={7} className="px-6 py-4 text-center text-gray-500">
                    No leave records found
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      )}

      {/* Reject Modal */}
      {showRejectModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 className="text-lg font-semibold mb-4">Reject Leave Request</h3>
            <textarea
              value={rejectReason}
              onChange={(e) => setRejectReason(e.target.value)}
              placeholder="Enter rejection reason..."
              className="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 mb-4 p-2 border"
              rows={4}
            />
            <div className="flex justify-end gap-2">
              <button
                onClick={() => {
                  setShowRejectModal(false);
                  setSelectedLeave(null);
                  setRejectReason('');
                }}
                className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
              >
                Cancel
              </button>
              <button
                onClick={handleReject}
                disabled={!rejectReason}
                className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
              >
                Reject
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
