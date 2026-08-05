import React, { useState } from 'react';
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
  Legend,
} from 'recharts';

const monthlyData = [
  { month: 'Jan', revenue: 580000, expense: 420000, profit: 160000 },
  { month: 'Feb', revenue: 620000, expense: 450000, profit: 170000 },
  { month: 'Mar', revenue: 650000, expense: 480000, profit: 170000 },
  { month: 'Apr', revenue: 680000, expense: 460000, profit: 220000 },
  { month: 'May', revenue: 720000, expense: 510000, profit: 210000 },
  { month: 'Jun', revenue: 750000, expense: 520000, profit: 230000 },
  { month: 'Jul', revenue: 780000, expense: 540000, profit: 240000 },
  { month: 'Aug', revenue: 820000, expense: 560000, profit: 260000 },
  { month: 'Sep', revenue: 850000, expense: 580000, profit: 270000 },
  { month: 'Oct', revenue: 880000, expense: 600000, profit: 280000 },
  { month: 'Nov', revenue: 920000, expense: 620000, profit: 300000 },
  { month: 'Dec', revenue: 950000, expense: 630000, profit: 320000 },
];

const revenueData = [
  { name: 'Tuition Fees', amount: 4500000 },
  { name: 'Admission Fees', amount: 1200000 },
  { name: 'Exam Fees', amount: 800000 },
  { name: 'Hostel Fees', amount: 600000 },
  { name: 'Transport Fees', amount: 400000 },
  { name: 'Library Fees', amount: 150000 },
  { name: 'Other Income', amount: 350000 },
];

const expenseData = [
  { name: 'Salary & Allowances', amount: 3200000 },
  { name: 'Utilities', amount: 480000 },
  { name: 'Maintenance', amount: 350000 },
  { name: 'Educational Resources', amount: 600000 },
  { name: 'Transport', amount: 250000 },
  { name: 'Office Supplies', amount: 180000 },
  { name: 'Marketing', amount: 200000 },
  { name: 'Miscellaneous', amount: 420000 },
];

const IncomeStatement: React.FC = () => {
  const [fiscalYear, setFiscalYear] = useState('2026');
  const [periodType, setPeriodType] = useState('yearly');
  const [showDetails, setShowDetails] = useState(true);

  const totalRevenue = revenueData.reduce((sum, item) => sum + item.amount, 0);
  const totalExpense = expenseData.reduce((sum, item) => sum + item.amount, 0);
  const grossProfit = totalRevenue;
  const netProfit = grossProfit - totalExpense;
  const grossMargin = (grossProfit / totalRevenue * 100).toFixed(1);
  const netMargin = (netProfit / totalRevenue * 100).toFixed(1);

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Income Statement</h1>
          <p className="text-gray-500">Statement of Income and Expenditure</p>
        </div>
        <div className="flex gap-3">
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Export to PDF
          </button>
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Export to Excel
          </button>
        </div>
      </div>

      {/* Filters */}
      <div className="bg-white p-4 rounded-lg border border-gray-100 flex flex-wrap gap-4 items-end">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Fiscal Year</label>
          <select
            value={fiscalYear}
            onChange={(e) => setFiscalYear(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg"
          >
            <option value="2026">2026</option>
            <option value="2025">2025</option>
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Period Type</label>
          <select
            value={periodType}
            onChange={(e) => setPeriodType(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg"
          >
            <option value="yearly">Yearly</option>
            <option value="quarterly">Quarterly</option>
            <option value="monthly">Monthly</option>
          </select>
        </div>
        <label className="flex items-center gap-2">
          <input
            type="checkbox"
            checked={showDetails}
            onChange={(e) => setShowDetails(e.target.checked)}
            className="rounded border-gray-300 text-blue-600"
          />
          <span className="text-sm text-gray-600">Show Details</span>
        </label>
      </div>

      {/* Key Metrics */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Revenue</p>
          <p className="text-2xl font-bold text-green-600">৳{totalRevenue.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Expense</p>
          <p className="text-2xl font-bold text-red-600">৳{totalExpense.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Net Profit</p>
          <p className="text-2xl font-bold text-blue-600">৳{netProfit.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Net Margin</p>
          <p className="text-2xl font-bold text-purple-600">{netMargin}%</p>
        </div>
      </div>

      {/* Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Monthly Profit Trend</h3>
          <ResponsiveContainer width="100%" height={300}>
            <LineChart data={monthlyData}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="month" />
              <YAxis />
              <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
              <Legend />
              <Line type="monotone" dataKey="revenue" stroke="#10b981" strokeWidth={2} name="Revenue" />
              <Line type="monotone" dataKey="expense" stroke="#ef4444" strokeWidth={2} name="Expense" />
              <Line type="monotone" dataKey="profit" stroke="#3b82f6" strokeWidth={2} name="Profit" />
            </LineChart>
          </ResponsiveContainer>
        </div>

        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Revenue vs Expense</h3>
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={[{ name: 'Amount', revenue: totalRevenue, expense: totalExpense }]}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="name" />
              <YAxis />
              <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
              <Legend />
              <Bar dataKey="revenue" fill="#10b981" name="Revenue" />
              <Bar dataKey="expense" fill="#ef4444" name="Expense" />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Income Statement Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div className="p-6 border-b border-gray-100 text-center">
          <h2 className="text-xl font-bold text-gray-900">Income Statement</h2>
          <p className="text-gray-500">For the Year Ended December 31, {fiscalYear}</p>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Particulars</th>
                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount (৳)</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {/* REVENUE */}
              <tr className="bg-green-50">
                <td className="px-6 py-3 font-bold text-green-900">A. REVENUE</td>
                <td className="px-6 py-3"></td>
              </tr>
              {showDetails && revenueData.map((item, index) => (
                <tr key={index} className="hover:bg-gray-50">
                  <td className="px-8 py-2 text-gray-700">{item.name}</td>
                  <td className="px-6 py-2 text-right text-gray-900">৳{item.amount.toLocaleString()}</td>
                </tr>
              ))}
              <tr className="bg-green-100 font-bold">
                <td className="px-6 py-3 text-green-900">Total Revenue (A)</td>
                <td className="px-6 py-3 text-right text-green-900">৳{totalRevenue.toLocaleString()}</td>
              </tr>

              {/* GROSS PROFIT */}
              <tr className="bg-blue-50 font-bold">
                <td className="px-6 py-3 text-blue-900">GROSS PROFIT</td>
                <td className="px-6 py-3 text-right text-blue-900">৳{grossProfit.toLocaleString()}</td>
              </tr>
              <tr className="bg-blue-50">
                <td className="px-6 py-3 text-blue-700">Gross Margin</td>
                <td className="px-6 py-3 text-right text-blue-700">{grossMargin}%</td>
              </tr>

              {/* EXPENSES */}
              <tr className="bg-red-50">
                <td className="px-6 py-3 font-bold text-red-900">B. EXPENSES</td>
                <td className="px-6 py-3"></td>
              </tr>
              {showDetails && expenseData.map((item, index) => (
                <tr key={index} className="hover:bg-gray-50">
                  <td className="px-8 py-2 text-gray-700">{item.name}</td>
                  <td className="px-6 py-2 text-right text-gray-900">৳{item.amount.toLocaleString()}</td>
                </tr>
              ))}
              <tr className="bg-red-100 font-bold">
                <td className="px-6 py-3 text-red-900">Total Expenses (B)</td>
                <td className="px-6 py-3 text-right text-red-900">৳{totalExpense.toLocaleString()}</td>
              </tr>

              {/* NET PROFIT */}
              <tr className="bg-purple-100 font-bold text-lg">
                <td className="px-6 py-3 text-purple-900">NET SURPLUS/(DEFICIT) (A - B)</td>
                <td className="px-6 py-3 text-right text-purple-900">৳{netProfit.toLocaleString()}</td>
              </tr>
              <tr className="bg-purple-50">
                <td className="px-6 py-3 text-purple-700">Net Margin</td>
                <td className="px-6 py-3 text-right text-purple-700">{netMargin}%</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      {/* Operating Metrics */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Operating Profit</p>
          <p className="text-xl font-bold text-blue-600">৳{(grossProfit - expenseData.slice(0, 4).reduce((sum, item) => sum + item.amount, 0)).toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">EBITDA</p>
          <p className="text-xl font-bold text-green-600">৳{(netProfit + 450000).toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Year-over-Year Growth</p>
          <p className="text-xl font-bold text-purple-600">12.5%</p>
        </div>
      </div>
    </div>
  );
};

export default IncomeStatement;
