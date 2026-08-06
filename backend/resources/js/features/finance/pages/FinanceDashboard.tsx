import React from 'react';
import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  BarChart,
  Bar,
} from 'recharts';

const monthlyData = [
  { month: 'Jan', income: 45000, expense: 32000 },
  { month: 'Feb', income: 48000, expense: 35000 },
  { month: 'Mar', income: 52000, expense: 38000 },
  { month: 'Apr', income: 49000, expense: 36000 },
  { month: 'May', income: 55000, expense: 40000 },
  { month: 'Jun', income: 58000, expense: 42000 },
];

const recentJournals = [
  { id: 1, number: 'JE-2026-001', date: '2026-01-15', description: 'Cash received from tuition fees', amount: 50000, status: 'posted' },
  { id: 2, number: 'JE-2026-002', date: '2026-01-14', description: 'Payment for electricity bill', amount: 15000, status: 'posted' },
  { id: 3, number: 'JE-2026-003', date: '2026-01-13', description: 'Purchase of office supplies', amount: 8500, status: 'pending' },
  { id: 4, number: 'JE-2026-004', date: '2026-01-12', description: 'Salary payment', amount: 250000, status: 'posted' },
  { id: 5, number: 'JE-2026-005', date: '2026-01-11', description: 'Library book purchase', amount: 12000, status: 'approved' },
];

const FinanceDashboard: React.FC = () => {
  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Finance Dashboard</h1>
          <p className="text-gray-500">Financial overview and management</p>
        </div>
        <div className="flex gap-3">
          <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + New Journal Entry
          </button>
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Generate Report
          </button>
        </div>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-500">Today's Income</p>
              <p className="text-2xl font-bold text-green-600">৳125,000</p>
            </div>
            <div className="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
              <svg className="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
              </svg>
            </div>
          </div>
          <p className="text-sm text-gray-500 mt-2">↑ 12% from yesterday</p>
        </div>

        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-500">Today's Expense</p>
              <p className="text-2xl font-bold text-red-600">৳85,000</p>
            </div>
            <div className="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
              <svg className="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 12H4" />
              </svg>
            </div>
          </div>
          <p className="text-sm text-gray-500 mt-2">↓ 8% from yesterday</p>
        </div>

        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-500">Current Cash</p>
              <p className="text-2xl font-bold text-blue-600">৳2,450,000</p>
            </div>
            <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
              <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
              </svg>
            </div>
          </div>
          <p className="text-sm text-gray-500 mt-2">Across all accounts</p>
        </div>

        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-500">Bank Balance</p>
              <p className="text-2xl font-bold text-purple-600">৳5,680,000</p>
            </div>
            <div className="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
              <svg className="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
          </div>
          <p className="text-sm text-gray-500 mt-2">5 bank accounts</p>
        </div>
      </div>

      {/* Charts Row */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Income vs Expense Chart */}
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Income vs Expense</h3>
          <ResponsiveContainer width="100%" height={300}>
            <LineChart data={monthlyData}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="month" />
              <YAxis />
              <Tooltip />
              <Line type="monotone" dataKey="income" stroke="#10b981" strokeWidth={2} name="Income" />
              <Line type="monotone" dataKey="expense" stroke="#ef4444" strokeWidth={2} name="Expense" />
            </LineChart>
          </ResponsiveContainer>
        </div>

        {/* Account Balance Chart */}
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Account Balances</h3>
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={[
              { name: 'Cash', amount: 2450000 },
              { name: 'Bank', amount: 5680000 },
              { name: 'Receivable', amount: 1250000 },
              { name: 'Payable', amount: 890000 },
            ]}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="name" />
              <YAxis />
              <Tooltip />
              <Bar dataKey="amount" fill="#3b82f6" />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Bottom Row */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Recent Journal Entries */}
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div className="flex items-center justify-between mb-4">
            <h3 className="text-lg font-semibold">Recent Journal Entries</h3>
            <button className="text-blue-600 hover:text-blue-700 text-sm">View All</button>
          </div>
          <div className="space-y-4">
            {recentJournals.map((journal) => (
              <div key={journal.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div className="flex items-center gap-3">
                  <div className={`w-2 h-2 rounded-full ${
                    journal.status === 'posted' ? 'bg-green-500' :
                    journal.status === 'pending' ? 'bg-yellow-500' : 'bg-blue-500'
                  }`} />
                  <div>
                    <p className="font-medium text-gray-900">{journal.number}</p>
                    <p className="text-sm text-gray-500">{journal.description}</p>
                  </div>
                </div>
                <div className="text-right">
                  <p className="font-medium text-gray-900">৳{journal.amount.toLocaleString()}</p>
                  <p className={`text-xs ${
                    journal.status === 'posted' ? 'text-green-600' :
                    journal.status === 'pending' ? 'text-yellow-600' : 'text-blue-600'
                  }`}>{journal.status}</p>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Trial Balance Summary */}
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <div className="flex items-center justify-between mb-4">
            <h3 className="text-lg font-semibold">Trial Balance Summary</h3>
            <button className="text-blue-600 hover:text-blue-700 text-sm">Generate</button>
          </div>
          <div className="space-y-4">
            <div className="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
              <span className="text-gray-600">Total Debit</span>
              <span className="text-xl font-bold text-gray-900">৳8,450,000</span>
            </div>
            <div className="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
              <span className="text-gray-600">Total Credit</span>
              <span className="text-xl font-bold text-gray-900">৳8,450,000</span>
            </div>
            <div className="flex justify-between items-center p-4 bg-green-50 rounded-lg">
              <span className="text-green-600 font-medium">Balance Status</span>
              <span className="text-green-600 font-bold">✓ Balanced</span>
            </div>
          </div>
        </div>
      </div>

      {/* Quick Actions */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        <button className="p-4 bg-white rounded-xl border border-gray-100 hover:border-blue-300 hover:shadow-sm transition-all text-center">
          <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
            <svg className="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <p className="text-sm font-medium text-gray-900">New Journal</p>
        </button>
        <button className="p-4 bg-white rounded-xl border border-gray-100 hover:border-green-300 hover:shadow-sm transition-all text-center">
          <div className="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
            <svg className="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
          </div>
          <p className="text-sm font-medium text-gray-900">Chart of Accounts</p>
        </button>
        <button className="p-4 bg-white rounded-xl border border-gray-100 hover:border-purple-300 hover:shadow-sm transition-all text-center">
          <div className="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
            <svg className="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <p className="text-sm font-medium text-gray-900">Trial Balance</p>
        </button>
        <button className="p-4 bg-white rounded-xl border border-gray-100 hover:border-orange-300 hover:shadow-sm transition-all text-center">
          <div className="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-2">
            <svg className="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
          </div>
          <p className="text-sm font-medium text-gray-900">Ledger</p>
        </button>
      </div>
    </div>
  );
};

export default FinanceDashboard;
