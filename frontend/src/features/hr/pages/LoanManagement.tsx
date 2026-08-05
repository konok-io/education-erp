import { useState, useEffect } from 'react';
import { getLoans, approveLoan, getLoanBalance } from '../services/hrApi';
import type { Loan, LoanBalance } from '../types';
import { LOAN_TYPES } from '../types';

export function LoanManagement() {
  const [loans, setLoans] = useState<Loan[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [filters, setFilters] = useState({
    status: 'pending',
  });
  const [selectedEmployee, setSelectedEmployee] = useState<string | null>(null);
  const [loanBalance, setLoanBalance] = useState<LoanBalance | null>(null);

  useEffect(() => {
    fetchLoans();
  }, [filters]);

  const fetchLoans = async () => {
    try {
      setLoading(true);
      const response = await getLoans(filters);
      setLoans(response.data);
      setError(null);
    } catch (err) {
      setError('Failed to load loans');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const fetchLoanBalance = async (employeeId: string) => {
    try {
      const balance = await getLoanBalance(employeeId);
      setLoanBalance(balance);
      setSelectedEmployee(employeeId);
    } catch (err) {
      console.error(err);
    }
  };

  const handleApprove = async (uuid: string) => {
    if (!confirm('Approve this loan?')) return;
    try {
      await approveLoan(uuid);
      fetchLoans();
    } catch (err) {
      alert('Failed to approve loan');
      console.error(err);
    }
  };

  const getStatusBadge = (status: string) => {
    const badges = {
      pending: 'bg-yellow-100 text-yellow-800',
      approved: 'bg-blue-100 text-blue-800',
      active: 'bg-green-100 text-green-800',
      completed: 'bg-emerald-100 text-emerald-800',
      rejected: 'bg-red-100 text-red-800',
      cancelled: 'bg-gray-100 text-gray-800',
    };
    return `px-2 py-1 text-xs font-medium rounded-full ${badges[status as keyof typeof badges] || 'bg-gray-100'}`;
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">Loan Management</h1>
      </div>

      {/* Loan Types */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <h3 className="text-sm font-medium text-gray-700 mb-2">Loan Types</h3>
        <div className="flex flex-wrap gap-2">
          {Object.entries(LOAN_TYPES).map(([key, label]) => (
            <span key={key} className="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded">
              {label}
            </span>
          ))}
        </div>
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
              <option value="active">Active</option>
              <option value="completed">Completed</option>
            </select>
          </div>
          <div className="flex items-end">
            <button
              onClick={fetchLoans}
              className="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
            >
              Apply Filters
            </button>
          </div>
        </div>
      </div>

      {/* Loan Table */}
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
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loan No</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Principal</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monthly</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remaining</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {loans.map((loan) => (
                <tr key={loan.id} className="hover:bg-gray-50">
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-sm font-medium text-gray-900">
                      {loan.employee?.name || 'N/A'}
                    </div>
                    <div className="text-sm text-gray-500">
                      {loan.employee?.employee_no}
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {loan.loan_no}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded">
                      {LOAN_TYPES[loan.loan_type as keyof typeof LOAN_TYPES] || loan.loan_type}
                    </span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${loan.principal_amount?.toLocaleString()}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${loan.monthly_installment?.toLocaleString()}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    ${loan.remaining_amount?.toLocaleString()}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className={getStatusBadge(loan.status)}>
                      {loan.status}
                    </span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm">
                    {loan.status === 'pending' && (
                      <button
                        onClick={() => handleApprove(loan.id)}
                        className="text-green-600 hover:text-green-700"
                      >
                        Approve
                      </button>
                    )}
                  </td>
                </tr>
              ))}
              {loans.length === 0 && (
                <tr>
                  <td colSpan={8} className="px-6 py-4 text-center text-gray-500">
                    No loan records found
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      )}

      {/* Loan Balance Modal */}
      {selectedEmployee && loanBalance && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 className="text-lg font-semibold mb-4">Employee Loan Summary</h3>
            <div className="space-y-3">
              <div className="flex justify-between">
                <span className="text-gray-600">Active Loans</span>
                <span className="font-medium">{loanBalance.total_loans}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-gray-600">Total Remaining</span>
                <span className="font-medium">${loanBalance.total_remaining?.toLocaleString()}</span>
              </div>
              {loanBalance.active_loans?.map((loan, i) => (
                <div key={i} className="bg-gray-50 p-3 rounded">
                  <div className="text-sm font-medium">{loan.loan_no}</div>
                  <div className="text-sm text-gray-500">
                    Monthly: ${loan.monthly?.toLocaleString()} | Remaining: ${loan.remaining?.toLocaleString()}
                  </div>
                </div>
              ))}
            </div>
            <button
              onClick={() => {
                setSelectedEmployee(null);
                setLoanBalance(null);
              }}
              className="mt-4 w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
            >
              Close
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
