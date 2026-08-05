import React, { useState } from 'react';
import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  AreaChart,
  Area,
  BarChart,
  Bar,
  PieChart,
  Pie,
  Cell,
  Legend,
} from 'recharts';

const monthlyTrends = [
  { month: 'Jan', revenue: 580000, expense: 420000, profit: 160000 },
  { month: 'Feb', revenue: 620000, expense: 450000, profit: 170000 },
  { month: 'Mar', revenue: 650000, expense: 480000, profit: 170000 },
  { month: 'Apr', revenue: 680000, expense: 460000, profit: 220000 },
  { month: 'May', revenue: 720000, expense: 510000, profit: 210000 },
  { month: 'Jun', revenue: 750000, expense: 520000, profit: 230000 },
];

const kpiData = {
  currentRatio: 2.45,
  quickRatio: 1.85,
  debtRatio: 0.35,
  profitMargin: 28.5,
  operatingMargin: 32.1,
  cashRatio: 0.85,
  workingCapital: 4250000,
  receivableTurnover: 5.2,
  payableTurnover: 4.8,
};

const revenueBreakdown = [
  { name: 'Tuition', value: 4500000, color: '#3b82f6' },
  { name: 'Admission', value: 1200000, color: '#10b981' },
  { name: 'Exam', value: 800000, color: '#f59e0b' },
  { name: 'Hostel', value: 600000, color: '#ef4444' },
  { name: 'Transport', value: 400000, color: '#8b5cf6' },
  { name: 'Other', value: 350000, color: '#06b6d4' },
];

const expenseBreakdown = [
  { name: 'Salary', value: 3200000, color: '#3b82f6' },
  { name: 'Utilities', value: 480000, color: '#10b981' },
  { name: 'Maintenance', value: 350000, color: '#f59e0b' },
  { name: 'Education', value: 600000, color: '#ef4444' },
  { name: 'Transport', value: 250000, color: '#8b5cf6' },
  { name: 'Other', value: 420000, color: '#06b6d4' },
];

const FinancialAnalytics: React.FC = () => {
  const [selectedPeriod, setSelectedPeriod] = useState('6months');

  const getKpiStatus = (value: number, ideal: number, isHigherBetter: boolean) => {
    if (isHigherBetter) {
      if (value >= ideal) return { color: 'text-green-600', bg: 'bg-green-100', status: 'Good' };
      if (value >= ideal * 0.8) return { color: 'text-yellow-600', bg: 'bg-yellow-100', status: 'Fair' };
      return { color: 'text-red-600', bg: 'bg-red-100', status: 'Poor' };
    } else {
      if (value <= ideal) return { color: 'text-green-600', bg: 'bg-green-100', status: 'Good' };
      if (value <= ideal * 1.2) return { color: 'text-yellow-600', bg: 'bg-yellow-100', status: 'Fair' };
      return { color: 'text-red-600', bg: 'bg-red-100', status: 'Poor' };
    }
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Financial Analytics</h1>
          <p className="text-gray-500">Financial KPIs and insights</p>
        </div>
        <div className="flex gap-3">
          <select
            value={selectedPeriod}
            onChange={(e) => setSelectedPeriod(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg"
          >
            <option value="3months">Last 3 Months</option>
            <option value="6months">Last 6 Months</option>
            <option value="12months">Last 12 Months</option>
          </select>
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Export Report
          </button>
        </div>
      </div>

      {/* KPIs Grid */}
      <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div className={`p-4 rounded-lg border ${getKpiStatus(kpiData.currentRatio, 2.0, true).bg}`}>
          <p className="text-sm text-gray-500">Current Ratio</p>
          <p className={`text-2xl font-bold ${getKpiStatus(kpiData.currentRatio, 2.0, true).color}`}>
            {kpiData.currentRatio.toFixed(2)}
          </p>
          <p className="text-xs text-gray-500">Ideal: &gt;2.0</p>
        </div>
        <div className={`p-4 rounded-lg border ${getKpiStatus(kpiData.quickRatio, 1.0, true).bg}`}>
          <p className="text-sm text-gray-500">Quick Ratio</p>
          <p className={`text-2xl font-bold ${getKpiStatus(kpiData.quickRatio, 1.0, true).color}`}>
            {kpiData.quickRatio.toFixed(2)}
          </p>
          <p className="text-xs text-gray-500">Ideal: &gt;1.0</p>
        </div>
        <div className={`p-4 rounded-lg border ${getKpiStatus(kpiData.debtRatio, 0.4, false).bg}`}>
          <p className="text-sm text-gray-500">Debt Ratio</p>
          <p className={`text-2xl font-bold ${getKpiStatus(kpiData.debtRatio, 0.4, false).color}`}>
            {(kpiData.debtRatio * 100).toFixed(1)}%
          </p>
          <p className="text-xs text-gray-500">Ideal: &lt;40%</p>
        </div>
        <div className={`p-4 rounded-lg border ${getKpiStatus(kpiData.profitMargin, 20, true).bg}`}>
          <p className="text-sm text-gray-500">Profit Margin</p>
          <p className={`text-2xl font-bold ${getKpiStatus(kpiData.profitMargin, 20, true).color}`}>
            {kpiData.profitMargin.toFixed(1)}%
          </p>
          <p className="text-xs text-gray-500">Ideal: &gt;20%</p>
        </div>
        <div className={`p-4 rounded-lg border ${getKpiStatus(kpiData.cashRatio, 0.5, true).bg}`}>
          <p className="text-sm text-gray-500">Cash Ratio</p>
          <p className={`text-2xl font-bold ${getKpiStatus(kpiData.cashRatio, 0.5, true).color}`}>
            {kpiData.cashRatio.toFixed(2)}
          </p>
          <p className="text-xs text-gray-500">Ideal: &gt;0.5</p>
        </div>
      </div>

      {/* Working Capital & Turnover */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Working Capital</p>
          <p className="text-2xl font-bold text-blue-600">৳{kpiData.workingCapital.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Receivable Turnover</p>
          <p className="text-2xl font-bold text-green-600">{kpiData.receivableTurnover}x</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Payable Turnover</p>
          <p className="text-2xl font-bold text-purple-600">{kpiData.payableTurnover}x</p>
        </div>
      </div>

      {/* Charts Row */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Revenue vs Expense Trend */}
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Revenue vs Expense Trend</h3>
          <ResponsiveContainer width="100%" height={300}>
            <AreaChart data={monthlyTrends}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="month" />
              <YAxis />
              <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
              <Legend />
              <Area type="monotone" dataKey="revenue" stackId="1" stroke="#10b981" fill="#10b981" fillOpacity={0.6} name="Revenue" />
              <Area type="monotone" dataKey="expense" stackId="2" stroke="#ef4444" fill="#ef4444" fillOpacity={0.6} name="Expense" />
            </AreaChart>
          </ResponsiveContainer>
        </div>

        {/* Profit Trend */}
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Monthly Profit</h3>
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={monthlyTrends}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="month" />
              <YAxis />
              <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
              <Bar dataKey="profit" fill="#3b82f6" name="Profit" />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Revenue & Expense Breakdown */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Revenue Breakdown */}
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Revenue Breakdown</h3>
          <div className="flex items-center">
            <ResponsiveContainer width="50%" height={250}>
              <PieChart>
                <Pie
                  data={revenueBreakdown}
                  cx="50%"
                  cy="50%"
                  innerRadius={60}
                  outerRadius={80}
                  paddingAngle={5}
                  dataKey="value"
                >
                  {revenueBreakdown.map((entry, index) => (
                    <Cell key={`cell-${index}`} fill={entry.color} />
                  ))}
                </Pie>
                <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
              </PieChart>
            </ResponsiveContainer>
            <div className="w-1/2 space-y-2">
              {revenueBreakdown.map((item) => (
                <div key={item.name} className="flex items-center justify-between">
                  <div className="flex items-center gap-2">
                    <div className="w-3 h-3 rounded-full" style={{ backgroundColor: item.color }} />
                    <span className="text-sm text-gray-700">{item.name}</span>
                  </div>
                  <span className="text-sm font-medium text-gray-900">
                    ৳{(item.value / 1000000).toFixed(1)}M
                  </span>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Expense Breakdown */}
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Expense Breakdown</h3>
          <div className="flex items-center">
            <ResponsiveContainer width="50%" height={250}>
              <PieChart>
                <Pie
                  data={expenseBreakdown}
                  cx="50%"
                  cy="50%"
                  innerRadius={60}
                  outerRadius={80}
                  paddingAngle={5}
                  dataKey="value"
                >
                  {expenseBreakdown.map((entry, index) => (
                    <Cell key={`cell-${index}`} fill={entry.color} />
                  ))}
                </Pie>
                <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
              </PieChart>
            </ResponsiveContainer>
            <div className="w-1/2 space-y-2">
              {expenseBreakdown.map((item) => (
                <div key={item.name} className="flex items-center justify-between">
                  <div className="flex items-center gap-2">
                    <div className="w-3 h-3 rounded-full" style={{ backgroundColor: item.color }} />
                    <span className="text-sm text-gray-700">{item.name}</span>
                  </div>
                  <span className="text-sm font-medium text-gray-900">
                    ৳{(item.value / 1000000).toFixed(1)}M
                  </span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      {/* Key Insights */}
      <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 className="text-lg font-semibold mb-4">Key Financial Insights</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div className="p-4 bg-green-50 rounded-lg">
            <div className="flex items-center gap-2 mb-2">
              <svg className="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
              <span className="font-semibold text-green-800">Strong Profitability</span>
            </div>
            <p className="text-sm text-green-700">
              Net profit margin of 28.5% exceeds industry benchmark of 20%
            </p>
          </div>
          <div className="p-4 bg-blue-50 rounded-lg">
            <div className="flex items-center gap-2 mb-2">
              <svg className="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span className="font-semibold text-blue-800">Healthy Liquidity</span>
            </div>
            <p className="text-sm text-blue-700">
              Current ratio of 2.45 indicates strong ability to meet short-term obligations
            </p>
          </div>
          <div className="p-4 bg-yellow-50 rounded-lg">
            <div className="flex items-center gap-2 mb-2">
              <svg className="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <span className="font-semibold text-yellow-800">Collection Needed</span>
            </div>
            <p className="text-sm text-yellow-700">
              ৳130,000 overdue receivables require immediate follow-up
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default FinancialAnalytics;
