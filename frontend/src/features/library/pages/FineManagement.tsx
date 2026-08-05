/**
 * Fine Management Page
 */

import React, { useEffect, useState } from 'react';
import { useLibraryStore } from '../store/libraryStore';
import { DollarSign, Search, Check, X, AlertTriangle } from 'lucide-react';
import { FINE_TYPES, FINE_STATUSES } from '../types';
import { formatCurrency } from '@/lib/utils';

export const FineManagement: React.FC = () => {
  const { fines, finesPagination, finesLoading, fetchFines, payFine, waiveFine } = useLibraryStore();
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [page, setPage] = useState(1);
  const [isProcessing, setIsProcessing] = useState(false);
  const [selectedFine, setSelectedFine] = useState<any>(null);
  const [paymentAmount, setPaymentAmount] = useState('');
  const [paymentMethod, setPaymentMethod] = useState('');

  useEffect(() => {
    fetchFines({ page, status: statusFilter || undefined });
  }, [fetchFines, page, statusFilter]);

  const handlePay = async () => {
    if (!selectedFine || !paymentAmount) return;
    
    setIsProcessing(true);
    try {
      await payFine(selectedFine.id, {
        amount: parseFloat(paymentAmount),
        payment_method: paymentMethod || undefined,
      });
      setSelectedFine(null);
      setPaymentAmount('');
      setPaymentMethod('');
      fetchFines({ page, status: statusFilter || undefined });
    } catch (error) {
      console.error('Failed to pay fine:', error);
    }
    setIsProcessing(false);
  };

  const handleWaive = async () => {
    if (!selectedFine || !paymentAmount) return;
    
    setIsProcessing(true);
    try {
      await waiveFine(selectedFine.id, { amount: parseFloat(paymentAmount) });
      setSelectedFine(null);
      setPaymentAmount('');
      setPaymentMethod('');
      fetchFines({ page, status: statusFilter || undefined });
    } catch (error) {
      console.error('Failed to waive fine:', error);
    }
    setIsProcessing(false);
  };

  const totalPending = fines.filter(f => f.status === 'pending' || f.status === 'partial')
    .reduce((sum, f) => sum + f.remaining_amount, 0);

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Fine Management</h1>
          <p className="text-gray-500">Manage library fines and collections</p>
        </div>
        <div className="bg-red-50 px-4 py-2 rounded-lg">
          <span className="text-sm text-red-600">Pending Fines: </span>
          <span className="font-bold text-red-700">{formatCurrency(totalPending)}</span>
        </div>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        {Object.entries(FINE_TYPES).map(([key, label]) => {
          const count = fines.filter(f => f.fine_type === key).length;
          const amount = fines.filter(f => f.fine_type === key)
            .reduce((sum, f) => sum + f.remaining_amount, 0);
          return (
            <div key={key} className="bg-white rounded-xl p-4 shadow-sm">
              <p className="text-sm text-gray-500">{label}</p>
              <p className="text-2xl font-bold text-gray-900">{count}</p>
              <p className="text-sm text-gray-500">{formatCurrency(amount)} pending</p>
            </div>
          );
        })}
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by fine number or member name..."
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
            {Object.entries(FINE_STATUSES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Fines Table */}
      <div className="bg-white rounded-xl shadow-sm overflow-hidden">
        {finesLoading ? (
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : fines.length === 0 ? (
          <div className="flex flex-col items-center justify-center h-64">
            <DollarSign className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No fines found</p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fine No</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remaining</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {fines.map((fine) => (
                  <tr key={fine.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 text-sm font-medium text-gray-900">{fine.fine_no}</td>
                    <td className="px-6 py-4">
                      <p className="text-sm font-medium text-gray-900">{fine.member?.name}</p>
                      <p className="text-xs text-gray-500">{fine.member?.member_no}</p>
                    </td>
                    <td className="px-6 py-4">
                      <span className="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded">
                        {FINE_TYPES[fine.fine_type as keyof typeof FINE_TYPES]}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{fine.reason}</td>
                    <td className="px-6 py-4 text-sm font-medium text-gray-900">
                      {formatCurrency(fine.amount)}
                    </td>
                    <td className="px-6 py-4">
                      <span className={`font-medium ${
                        fine.remaining_amount > 0 ? 'text-red-600' : 'text-green-600'
                      }`}>
                        {formatCurrency(fine.remaining_amount)}
                      </span>
                    </td>
                    <td className="px-6 py-4">
                      <span className={`px-2 py-1 text-xs font-medium rounded ${
                        fine.status === 'paid' ? 'bg-green-100 text-green-700' :
                        fine.status === 'partial' ? 'bg-yellow-100 text-yellow-700' :
                        fine.status === 'waived' ? 'bg-gray-100 text-gray-700' :
                        'bg-red-100 text-red-700'
                      }`}>
                        {FINE_STATUSES[fine.status as keyof typeof FINE_STATUSES]}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {new Date(fine.fine_date).toLocaleDateString()}
                    </td>
                    <td className="px-6 py-4 text-right">
                      {fine.remaining_amount > 0 && (
                        <button
                          onClick={() => {
                            setSelectedFine(fine);
                            setPaymentAmount(fine.remaining_amount.toString());
                          }}
                          className="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700"
                        >
                          Collect
                        </button>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        {/* Pagination */}
        {finesPagination && finesPagination.last_page > 1 && (
          <div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p className="text-sm text-gray-500">
              Showing {((page - 1) * 20) + 1} to {Math.min(page * 20, finesPagination.total)} of {finesPagination.total}
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
                disabled={page === finesPagination.last_page}
                className="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50"
              >
                Next
              </button>
            </div>
          </div>
        )}
      </div>

      {/* Payment Modal */}
      {selectedFine && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-xl max-w-md w-full">
            <div className="p-6">
              <h2 className="text-xl font-bold text-gray-900 mb-4">Collect Fine</h2>
              
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Fine Amount</label>
                  <input
                    type="text"
                    value={formatCurrency(selectedFine.amount)}
                    disabled
                    className="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg"
                  />
                </div>
                
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Remaining Amount</label>
                  <input
                    type="text"
                    value={formatCurrency(selectedFine.remaining_amount)}
                    disabled
                    className="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg"
                  />
                </div>
                
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Payment Amount</label>
                  <input
                    type="number"
                    value={paymentAmount}
                    onChange={(e) => setPaymentAmount(e.target.value)}
                    max={selectedFine.remaining_amount}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                  />
                </div>
                
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                  <select
                    value={paymentMethod}
                    onChange={(e) => setPaymentMethod(e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                  >
                    <option value="">Select Method</option>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="mobile_banking">Mobile Banking</option>
                    <option value="bank">Bank Transfer</option>
                  </select>
                </div>
              </div>

              <div className="flex gap-3 mt-6">
                <button
                  onClick={handlePay}
                  disabled={isProcessing || !paymentAmount}
                  className="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 flex items-center justify-center gap-2"
                >
                  {isProcessing ? (
                    <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                  ) : (
                    <Check className="w-5 h-5" />
                  )}
                  Collect Payment
                </button>
                <button
                  onClick={handleWaive}
                  disabled={isProcessing || !paymentAmount}
                  className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
                >
                  Waive
                </button>
                <button
                  onClick={() => {
                    setSelectedFine(null);
                    setPaymentAmount('');
                    setPaymentMethod('');
                  }}
                  className="px-4 py-2 text-gray-600 hover:text-gray-900"
                >
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
