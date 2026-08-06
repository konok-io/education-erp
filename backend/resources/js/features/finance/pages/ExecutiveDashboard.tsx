import React, { useState } from 'react';
import {
  LineChart,
  Line,
  AreaChart,
  Area,
  BarChart,
  Bar,
  PieChart,
  Pie,
  Cell,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  Legend,
} from 'recharts';

const revenueData = [
  { month: 'Jul', revenue: 5800000, target: 5500000 },
  { month: 'Aug', revenue: 6200000, target: 5800000 },
  { month: 'Sep', revenue: 6500000, target: 6000000 },
  { month: 'Oct', revenue: 6800000, target: 6500000 },
  { month: 'Nov', revenue: 7200000, target: 7000000 },
  { month: 'Dec', revenue: 8500000, target: 7500000 },
];

const kpiData = {
  totalRevenue: 41000000,
  totalExpense: 32000000,
  netProfit: 9000000,
  cashPosition: 8530000,
  totalReceivable: 625000,
  totalPayable: 380000,
  totalAssets: 15000000,
  totalLiabilities: 4000000,
  currentRatio: 2.45,
  profitMargin: 22.0,
  roi: 15.5,
  roe: 18.2,
};

const budgetStatus = [
  { name: 'Academic', budget: 2500000, spent: 1850000, color: '#3b82f6' },
  { name: 'Admin', budget: 1800000, spent: 1620000, color: '#10b981' },
  { name: 'IT', budget: 1200000, spent: 840000, color: '#f59e0b' },
  { name: 'Library', budget: 800000, spent: 720000, color: '#ef4444' },
  { name: 'Transport', budget: 950000, spent: 665000, color: '#8b5cf6' },
];

const recentTransactions = [
  { id: 1, date: 'Feb 05', description: 'Tuition Fees Collection', amount: 450000, type: 'income' },
  { id: 2, date: 'Feb 05', description: 'Salary Payment - January', amount: -2500000, type: 'expense' },
  { id: 3, date: 'Feb 04', description: 'Electricity Bill', amount: -85000, type: 'expense' },
  { id: 4, date: 'Feb 03', description: 'Exam Fee Collection', amount: 125000, type: 'income' },
  { id: 5, date: 'Feb 02', description: 'Supplier Payment', amount: -185000, type: 'expense' },
];

const ExecutiveDashboard: React.FC = () => {
  const [timeRange, setTimeRange] = useState('6months');

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Executive Dashboard</h1>
          <p className="text-gray-500">Financial Overview & KPIs</p>
        </div>
        <div className="flex gap-3">
          <select
            value={timeRange}
            onChange={(e) => setTimeRange(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg"
          >
            <option value="3months">Last 3 Months</option>
            <option value="6months">Last 6 Months</option>
            <option value="12months">Last 12 Months</option>
          </select>
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Download Report
          </button>
        </div>
      </div>

      {/* KPI Cards */}
      <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <div className="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white">
          <p className="text-sm opacity-80">Total Revenue</p>
          <p className="text-2xl font-bold">৳{(kpiData.totalRevenue / 1000000).toFixed(1)}M</p>
          <p className="text-xs opacity-80 mt-1">↑ 12.5% YoY</p>
        </div>
        <div className="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
          <p className="text-sm opacity-80">Net Profit</p>
          <p className="text-2xl font-bold">৳{(kpiData.netProfit / 1000000).toFixed(1)}M</p>
          <p className="text-xs opacity-80 mt-1">↑ 18.2% YoY</p>
        </div>
        <div className="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
          <p className="text-sm opacity-80">Profit Margin</p>
          <p className="text-2xl font-bold">{kpiData.profitMargin}%</p>
          <p className="text-xs opacity-80 mt-1">Target: 20%</p>
        </div>
        <div className="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white">
          <p className="text-sm opacity-80">Cash Position</p>
          <p className="text-2xl font-bold">৳{(kpiData.cashPosition / 1000000).toFixed(1)}M</p>
          <p className="text-xs opacity-80 mt-1">Strong</p>
        </div>
        <div className="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl p-4 text-white">
          <p className="text-sm opacity-80">Current Ratio</p>
          <p className="text-2xl font-bold">{kpiData.currentRatio}</p>
          <p className="text-xs opacity-80 mt-1">Target: >2.0</p>
        </div>
        <div className="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl p-4 text-white">
          <p className="text-sm opacity-80">ROI</p>
          <p className="text-2xl font-bold">{kpiData.roi}%</p>
          <p className="text-xs opacity-80 mt-1">Target: 15%</p>
        </div>
      </div>

      {/* Charts Row */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Revenue vs Target */}
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Revenue vs Target</h3>
          <ResponsiveContainer width="100%" height={300}>
            <AreaChart data={revenueData}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="month" />
              <YAxis />
              <Tooltip formatter={(value: number) => `৳${(value / 1000000).toFixed(1)}M`} />
              <Area type="monotone" dataKey="target" stackId="1" stroke="#e5e7eb" fill="#f3f4f6" name="Target" />
              <Area type="monotone" dataKey="revenue" stackId="2" stroke="#3b82f6" fill="#3b82f6" fillOpacity={0.6} name="Revenue" />
            </AreaChart>
          </ResponsiveContainer>
        </div>

        {/* Budget Utilization */}
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Budget Utilization</h3>
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={budgetStatus} layout="vertical">
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis type="number" />
              <YAxis dataKey="name" type="category" />
              <Tooltip formatter={(value: number) => `৳${(value / 1000000).toFixed(1)}M`} />
              <Bar dataKey="spent" fill="#3b82f6" name="Spent" />
              <Bar dataKey="budget" fill="#e5e7eb" name="Budget" />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Financial Position Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white rounded-xl p-6 border border-gray-100">
          <p className="text-sm text-gray-500">Total Assets</p>
          <p className="text-2xl font-bold text-blue-600">৳{(kpiData.totalAssets / 1000000).toFixed(1)}M</p>
        </div>
        <div className="bg-white rounded-xl p-6 border border-gray-100">
          <p className="text-sm text-gray-500">Total Liabilities</p>
          <p className="text-2xl font-bold text-red-600">৳{(kpiData.totalLiabilities / 1000000).toFixed(1)}M</p>
        </div>
        <div className="bg-white rounded-xl p-6 border border-gray-100">
          <p className="text-sm text-gray-500">Receivables</p>
          <p className="text-2xl font-bold text-orange-600">৳{(kpiData.totalReceivable / 1000).toFixed(0)}K</p>
        </div>
        <div className="bg-white rounded-xl p-6 border border-gray-100">
          <p className="text-sm text-gray-500">Payables</p>
          <p className="text-2xl font-bold text-purple-600">৳{(kpiData.totalPayable / 1000).toFixed(0)}K</p>
        </div>
      </div>

      {/* Recent Transactions & Quick Stats */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Recent Transactions */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 lg:col-span-2">
          <div className="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 className="font-semibold text-gray-900">Recent Transactions</h3>
            <button className="text-blue-600 hover:text-blue-800 text-sm">View All</button>
          </div>
          <div className="divide-y divide-gray-100">
            {recentTransactions.map((txn) => (
              <div key={txn.id} className="p-4 flex items-center justify-between hover:bg-gray-50">
                <div className="flex items-center gap-3">
                  <div className={`w-10 h-10 rounded-full flex items-center justify-center ${
                    txn.type === 'income' ? 'bg-green-100' : 'bg-red-100'
                  }`}>
                    <span className={txn.type === 'income' ? 'text-green-600' : 'text-red-600'}>
                      {txn.type === 'income' ? '↑' : '↓'}
                    </span>
                  </div>
                  <div>
                    <p className="font-medium text-gray-900">{txn.description}</p>
                    <p className="text-sm text-gray-500">{txn.date}</p>
                  </div>
                </div>
                <p className={`font-medium ${txn.type === 'income' ? 'text-green-600' : 'text-red-600'}`}>
                  {txn.amount > 0 ? '+' : ''}৳{Math.abs(txn.amount).toLocaleString()}
                </p>
              </div>
            ))}
          </div>
        </div>

        {/* Quick Stats */}
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="font-semibold text-gray-900 mb-4">Quick Stats</h3>
          <div className="space-y-4">
            <div className="p-4 bg-gray-50 rounded-lg">
              <p className="text-sm text-gray-500">This Month Income</p>
              <p className="text-xl font-bold text-green-600">৳8.5M</p>
            </div>
            <div className="p-4 bg-gray-50 rounded-lg">
              <p className="text-sm text-gray-500">This Month Expense</p>
              <p className="text-xl font-bold text-red-600">৳3.2M</p>
            </div>
            <div className="p-4 bg-gray-50 rounded-lg">
              <p className="text-sm text-gray-500">This Month Surplus</p>
              <p className="text-xl font-bold text-blue-600">৳5.3M</p>
            </div>
            <div className="p-4 bg-green-50 rounded-lg">
              <p className="text-sm text-green-600">YoY Growth</p>
              <p className="text-xl font-bold text-green-600">+18.2%</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ExecutiveDashboard;
