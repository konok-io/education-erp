import React, { useState } from 'react';
import {
  BarChart,
  Bar,
  LineChart,
  Line,
  PieChart,
  Pie,
  Cell,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
} from 'recharts';

const monthlyPayroll = [
  { month: 'Jul', gross: 8500000, net: 7200000 },
  { month: 'Aug', gross: 8800000, net: 7450000 },
  { month: 'Sep', gross: 8500000, net: 7200000 },
  { month: 'Oct', gross: 9200000, net: 7800000 },
  { month: 'Nov', gross: 9500000, net: 8050000 },
  { month: 'Dec', gross: 12500000, net: 9800000 },
];

const departmentPayroll = [
  { name: 'Academic', amount: 4500000, color: '#3b82f6' },
  { name: 'Admin', amount: 1800000, color: '#10b981' },
  { name: 'IT', amount: 1200000, color: '#f59e0b' },
  { name: 'Library', amount: 650000, color: '#ef4444' },
  { name: 'Transport', amount: 450000, color: '#8b5cf6' },
];

const recentPayroll = [
  { id: 1, month: 'January 2026', employees: 150, gross: 9500000, net: 8050000, status: 'approved' },
  { id: 2, month: 'December 2025', employees: 148, gross: 12500000, net: 9800000, status: 'paid' },
  { id: 3, month: 'November 2025', employees: 147, gross: 9500000, net: 8050000, status: 'paid' },
  { id: 4, month: 'October 2025', employees: 146, gross: 9200000, net: 7800000, status: 'paid' },
];

const PayrollDashboard: React.FC = () => {
  const [selectedMonth, setSelectedMonth] = useState('January 2026');

  const totalEmployees = 150;
  const pendingPayroll = 1;
  const approvedPayroll = 1;
  const paidPayroll = 12;
  const totalSalary = 12500000;
  const bonusAmount = 2500000;
  const taxAmount = 850000;
  const pfAmount = 650000;
  const netPayroll = 9800000;

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'paid': return 'bg-green-100 text-green-800';
      case 'approved': return 'bg-blue-100 text-blue-800';
      case 'pending': return 'bg-yellow-100 text-yellow-800';
      case 'draft': return 'bg-gray-100 text-gray-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Payroll Dashboard</h1>
          <p className="text-gray-500">Employee Compensation & Benefits Management</p>
        </div>
        <div className="flex gap-3">
          <select
            value={selectedMonth}
            onChange={(e) => setSelectedMonth(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg"
          >
            <option value="January 2026">January 2026</option>
            <option value="December 2025">December 2025</option>
          </select>
          <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Process Payroll
          </button>
        </div>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <div className="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white">
          <p className="text-sm opacity-80">Total Employees</p>
          <p className="text-2xl font-bold">{totalEmployees}</p>
        </div>
        <div className="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white">
          <p className="text-sm opacity-80">Pending</p>
          <p className="text-2xl font-bold">{pendingPayroll}</p>
        </div>
        <div className="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
          <p className="text-sm opacity-80">Approved</p>
          <p className="text-2xl font-bold">{approvedPayroll}</p>
        </div>
        <div className="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
          <p className="text-sm opacity-80">Paid</p>
          <p className="text-2xl font-bold">{paidPayroll}</p>
        </div>
        <div className="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white">
          <p className="text-sm opacity-80">Bonus</p>
          <p className="text-2xl font-bold">৳{(bonusAmount / 1000000).toFixed(1)}M</p>
        </div>
        <div className="bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl p-4 text-white">
          <p className="text-sm opacity-80">Net Payroll</p>
          <p className="text-2xl font-bold">৳{(netPayroll / 1000000).toFixed(1)}M</p>
        </div>
      </div>

      {/* Quick Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Gross Salary</p>
          <p className="text-xl font-bold text-blue-600">৳{(totalSalary / 1000000).toFixed(1)}M</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Tax Deduction</p>
          <p className="text-xl font-bold text-red-600">৳{(taxAmount / 100000).toFixed(1)}L</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">PF Contribution</p>
          <p className="text-xl font-bold text-purple-600">৳{(pfAmount / 100000).toFixed(1)}L</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Avg. Net Salary</p>
          <p className="text-xl font-bold text-green-600">৳{Math.round(netPayroll / totalEmployees / 1000)}K</p>
        </div>
      </div>

      {/* Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Monthly Payroll Trend */}
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Monthly Payroll Trend</h3>
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={monthlyPayroll}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="month" />
              <YAxis />
              <Tooltip formatter={(value: number) => `৳${(value / 1000000).toFixed(1)}M`} />
              <Bar dataKey="gross" fill="#3b82f6" name="Gross" />
              <Bar dataKey="net" fill="#10b981" name="Net" />
            </BarChart>
          </ResponsiveContainer>
        </div>

        {/* Department Distribution */}
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Payroll by Department</h3>
          <div className="flex items-center">
            <ResponsiveContainer width="60%" height={300}>
              <PieChart>
                <Pie
                  data={departmentPayroll}
                  cx="50%"
                  cy="50%"
                  labelLine={false}
                  label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                  outerRadius={100}
                  fill="#8884d8"
                  dataKey="amount"
                >
                  {departmentPayroll.map((entry, index) => (
                    <Cell key={`cell-${index}`} fill={entry.color} />
                  ))}
                </Pie>
                <Tooltip formatter={(value: number) => `৳${(value / 1000000).toFixed(1)}M`} />
              </PieChart>
            </ResponsiveContainer>
            <div className="w-40 space-y-2">
              {departmentPayroll.map((dept) => (
                <div key={dept.name} className="flex items-center justify-between">
                  <div className="flex items-center gap-2">
                    <div className="w-3 h-3 rounded-full" style={{ backgroundColor: dept.color }} />
                    <span className="text-sm text-gray-700">{dept.name}</span>
                  </div>
                  <span className="text-sm font-medium text-gray-900">
                    ৳{(dept.amount / 1000000).toFixed(1)}M
                  </span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      {/* Recent Payroll */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div className="p-4 border-b border-gray-100 flex items-center justify-between">
          <h3 className="font-semibold text-gray-900">Recent Payroll</h3>
          <button className="text-blue-600 hover:text-blue-800 text-sm">View All</button>
        </div>
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Employees</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gross</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {recentPayroll.map((payroll) => (
              <tr key={payroll.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 text-gray-900">{payroll.month}</td>
                <td className="px-6 py-4 text-right text-gray-900">{payroll.employees}</td>
                <td className="px-6 py-4 text-right text-gray-900">৳{payroll.gross.toLocaleString()}</td>
                <td className="px-6 py-4 text-right text-gray-900">৳{payroll.net.toLocaleString()}</td>
                <td className="px-6 py-4 text-center">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(payroll.status)}`}>
                    {payroll.status}
                  </span>
                </td>
                <td className="px-6 py-4 text-center">
                  <button className="text-blue-600 hover:text-blue-800 mr-2">View</button>
                  <button className="text-green-600 hover:text-green-800">Payslip</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Quick Actions */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        <button className="p-4 bg-white rounded-xl border border-gray-100 hover:border-blue-300 hover:shadow-sm transition-all">
          <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
            <svg className="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
          </div>
          <p className="text-sm font-medium text-gray-900 text-center">Process Payroll</p>
        </button>
        <button className="p-4 bg-white rounded-xl border border-gray-100 hover:border-green-300 hover:shadow-sm transition-all">
          <div className="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
            <svg className="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <p className="text-sm font-medium text-gray-900 text-center">Approve Payroll</p>
        </button>
        <button className="p-4 bg-white rounded-xl border border-gray-100 hover:border-purple-300 hover:shadow-sm transition-all">
          <div className="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
            <svg className="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <p className="text-sm font-medium text-gray-900 text-center">Salary Slip</p>
        </button>
        <button className="p-4 bg-white rounded-xl border border-gray-100 hover:border-orange-300 hover:shadow-sm transition-all">
          <div className="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-2">
            <svg className="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
          </div>
          <p className="text-sm font-medium text-gray-900 text-center">Bank Transfer</p>
        </button>
      </div>
    </div>
  );
};

export default PayrollDashboard;
