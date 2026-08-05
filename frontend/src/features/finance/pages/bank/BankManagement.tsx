import React, { useState } from 'react';
import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
} from 'recharts';

interface BankAccount {
  id: string;
  accountNumber: string;
  accountName: string;
  bankName: string;
  branch: string;
  accountType: string;
  currency: string;
  openingBalance: number;
  currentBalance: number;
  unclearedBalance: number;
  status: string;
}

interface Transaction {
  id: string;
  date: string;
  description: string;
  deposit: number;
  withdrawal: number;
  balance: number;
  status: string;
  chequeNumber?: string;
  isReconciled: boolean;
}

const bankAccounts: BankAccount[] = [
  { id: '1', accountNumber: '2026-001', accountName: 'Main Operating Account', bankName: 'Islami Bank Bangladesh', branch: 'Gulshan Branch', accountType: 'Current', currency: 'BDT', openingBalance: 2000000, currentBalance: 5680000, unclearedBalance: 125000, status: 'active' },
  { id: '2', accountNumber: '2026-002', accountName: 'Student Fees Collection', bankName: 'Dutch-Bangla Bank', branch: 'Dhanmondi Branch', accountType: 'Current', currency: 'BDT', openingBalance: 500000, currentBalance: 2450000, unclearedBalance: 85000, status: 'active' },
  { id: '3', accountNumber: '2026-003', accountName: 'Scholarship Fund', bankName: 'City Bank', branch: 'Motijheel Branch', accountType: 'Savings', currency: 'BDT', openingBalance: 1000000, currentBalance: 1850000, unclearedBalance: 0, status: 'active' },
  { id: '4', accountNumber: '2026-004', accountName: 'Building Fund', bankName: 'BRAC Bank', branch: 'Uttara Branch', accountType: 'Fixed Deposit', currency: 'BDT', openingBalance: 0, currentBalance: 5000000, unclearedBalance: 0, status: 'active' },
];

const transactions: Transaction[] = [
  { id: '1', date: '2026-02-05', description: 'Tuition fees collection - Batch 2026', deposit: 450000, withdrawal: 0, balance: 5680000, status: 'cleared', isReconciled: true },
  { id: '2', date: '2026-02-04', description: 'Salary payment - January 2026', deposit: 0, withdrawal: 2500000, balance: 5230000, status: 'cleared', isReconciled: true },
  { id: '3', date: '2026-02-03', description: 'Electricity bill payment', deposit: 0, withdrawal: 85000, balance: 7730000, status: 'cleared', isReconciled: true },
  { id: '4', date: '2026-02-02', description: 'Admission form fees', deposit: 125000, withdrawal: 0, balance: 7815000, status: 'cleared', isReconciled: true },
  { id: '5', date: '2026-02-01', description: 'Supplier payment - Tech Solutions', deposit: 0, withdrawal: 185000, balance: 7690000, status: 'pending', chequeNumber: 'CHK-2026-001', isReconciled: false },
  { id: '6', date: '2026-01-31', description: 'Library fine collection', deposit: 25000, withdrawal: 0, balance: 7875000, status: 'cleared', isReconciled: false },
  { id: '7', date: '2026-01-30', description: 'Office supplies purchase', deposit: 0, withdrawal: 45000, balance: 7850000, status: 'cleared', isReconciled: false },
  { id: '8', date: '2026-01-29', description: 'Transport fees - January', deposit: 185000, withdrawal: 0, balance: 7895000, status: 'cleared', isReconciled: false },
];

const BankManagement: React.FC = () => {
  const [selectedAccount, setSelectedAccount] = useState<BankAccount>(bankAccounts[0]);
  const [showReconciliation, setShowReconciliation] = useState(false);

  const totalBalance = bankAccounts.reduce((sum, acc) => sum + acc.currentBalance, 0);
  const totalUncleared = bankAccounts.reduce((sum, acc) => sum + acc.unclearedBalance, 0);
  const reconciledCount = transactions.filter(t => t.isReconciled).length;
  const unreconciledCount = transactions.length - reconciledCount;

  const chartData = bankAccounts.map(acc => ({
    name: acc.bankName.split(' ')[0],
    balance: acc.currentBalance,
  }));

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Bank Management</h1>
          <p className="text-gray-500">Manage bank accounts and transactions</p>
        </div>
        <div className="flex gap-3">
          <button
            onClick={() => setShowReconciliation(true)}
            className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            Bank Reconciliation
          </button>
          <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + Add Account
          </button>
        </div>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Bank Balance</p>
          <p className="text-2xl font-bold text-blue-600">৳{totalBalance.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Uncleared Transactions</p>
          <p className="text-2xl font-bold text-orange-600">৳{totalUncleared.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Reconciled</p>
          <p className="text-2xl font-bold text-green-600">{reconciledCount}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Unreconciled</p>
          <p className="text-2xl font-bold text-red-600">{unreconciledCount}</p>
        </div>
      </div>

      {/* Bank Balance Chart */}
      <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 className="text-lg font-semibold mb-4">Bank Balance Distribution</h3>
        <ResponsiveContainer width="100%" height={250}>
          <BarChart data={chartData}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="name" />
            <YAxis />
            <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
            <Bar dataKey="balance" fill="#3b82f6" />
          </BarChart>
        </ResponsiveContainer>
      </div>

      {/* Bank Accounts */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div className="p-4 border-b border-gray-100">
          <h3 className="font-semibold text-gray-900">Bank Accounts</h3>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 p-4">
          {bankAccounts.map((account) => (
            <div
              key={account.id}
              onClick={() => setSelectedAccount(account)}
              className={`p-4 rounded-lg border cursor-pointer transition-all ${
                selectedAccount.id === account.id
                  ? 'border-blue-500 bg-blue-50'
                  : 'border-gray-200 hover:border-gray-300'
              }`}
            >
              <div className="flex items-center justify-between mb-2">
                <span className={`px-2 py-1 text-xs font-medium rounded-full ${
                  account.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                }`}>
                  {account.status}
                </span>
                <span className="text-xs text-gray-500">{account.currency}</span>
              </div>
              <p className="font-semibold text-gray-900">{account.accountName}</p>
              <p className="text-sm text-gray-500">{account.bankName}</p>
              <p className="text-xs text-gray-400">{account.accountNumber}</p>
              <p className="text-xl font-bold text-blue-600 mt-2">
                ৳{account.currentBalance.toLocaleString()}
              </p>
            </div>
          ))}
        </div>
      </div>

      {/* Transactions */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div className="p-4 border-b border-gray-100 flex items-center justify-between">
          <h3 className="font-semibold text-gray-900">
            Recent Transactions - {selectedAccount.accountName}
          </h3>
          <div className="flex gap-2">
            <button className="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50">
              Export
            </button>
            <button className="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
              Import Statement
            </button>
          </div>
        </div>
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Deposit</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Withdrawal</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Reconciled</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {transactions.map((txn) => (
              <tr key={txn.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 text-gray-600">{txn.date}</td>
                <td className="px-6 py-4 text-gray-900">
                  {txn.description}
                  {txn.chequeNumber && (
                    <span className="ml-2 text-xs text-gray-500">({txn.chequeNumber})</span>
                  )}
                </td>
                <td className="px-6 py-4 text-right text-green-600">
                  {txn.deposit > 0 ? `৳${txn.deposit.toLocaleString()}` : '-'}
                </td>
                <td className="px-6 py-4 text-right text-red-600">
                  {txn.withdrawal > 0 ? `৳${txn.withdrawal.toLocaleString()}` : '-'}
                </td>
                <td className="px-6 py-4 text-right text-gray-900">
                  ৳{txn.balance.toLocaleString()}
                </td>
                <td className="px-6 py-4 text-center">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${
                    txn.status === 'cleared' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                  }`}>
                    {txn.status}
                  </span>
                </td>
                <td className="px-6 py-4 text-center">
                  <input
                    type="checkbox"
                    checked={txn.isReconciled}
                    onChange={() => {}}
                    className="rounded border-gray-300 text-blue-600"
                  />
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Reconciliation Modal */}
      {showReconciliation && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
            <div className="p-6 border-b border-gray-100 flex items-center justify-between">
              <h2 className="text-xl font-bold text-gray-900">Bank Reconciliation</h2>
              <button onClick={() => setShowReconciliation(false)} className="text-gray-400 hover:text-gray-600">
                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div className="p-6 overflow-y-auto max-h-[calc(90vh-180px)] space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Bank Statement Balance</label>
                  <input type="number" defaultValue={5680000} className="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">ERP Balance</label>
                  <input type="number" defaultValue={5555000} className="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" readOnly />
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Add: Deposits in Transit</label>
                  <input type="number" defaultValue={85000} className="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Less: Outstanding Cheques</label>
                  <input type="number" defaultValue={125000} className="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                </div>
              </div>
              <div className="p-4 bg-gray-50 rounded-lg">
                <div className="flex justify-between mb-2">
                  <span className="font-medium">Adjusted Bank Balance:</span>
                  <span className="font-bold">৳5,645,000</span>
                </div>
                <div className="flex justify-between">
                  <span className="font-medium">Adjusted ERP Balance:</span>
                  <span className="font-bold">৳5,645,000</span>
                </div>
              </div>
              <div className="p-4 bg-green-50 rounded-lg text-center">
                <p className="text-green-700 font-semibold">✓ Reconciled Successfully</p>
              </div>
            </div>
            <div className="p-6 border-t border-gray-100 flex justify-end gap-3">
              <button onClick={() => setShowReconciliation(false)} className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
              </button>
              <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Complete Reconciliation
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default BankManagement;
