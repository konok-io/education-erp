import React, { useState } from 'react';
import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  LineChart,
  Line,
  Legend,
} from 'recharts';

interface Budget {
  id: string;
  code: string;
  name: string;
  type: string;
  department: string;
  fiscalYear: string;
  totalAmount: number;
  allocatedAmount: number;
  spentAmount: number;
  remainingAmount: number;
  utilizationPercent: number;
  status: string;
}

const budgets: Budget[] = [
  { id: '1', code: 'BGT-2026-001', name: 'Academic Department', type: 'Annual', department: 'Academic', fiscalYear: '2026', totalAmount: 2500000, allocatedAmount: 2500000, spentAmount: 1850000, remainingAmount: 650000, utilizationPercent: 74, status: 'approved' },
  { id: '2', code: 'BGT-2026-002', name: 'Administration', type: 'Annual', department: 'Admin', fiscalYear: '2026', totalAmount: 1800000, allocatedAmount: 1800000, spentAmount: 1620000, remainingAmount: 180000, utilizationPercent: 90, status: 'approved' },
  { id: '3', code: 'BGT-2026-003', name: 'IT Infrastructure', type: 'Annual', department: 'IT', fiscalYear: '2026', totalAmount: 1200000, allocatedAmount: 1200000, spentAmount: 840000, remainingAmount: 360000, utilizationPercent: 70, status: 'approved' },
  { id: '4', code: 'BGT-2026-004', name: 'Library Resources', type: 'Annual', department: 'Library', fiscalYear: '2026', totalAmount: 800000, allocatedAmount: 800000, spentAmount: 720000, remainingAmount: 80000, utilizationPercent: 90, status: 'approved' },
  { id: '5', code: 'BGT-2026-005', name: 'Transport Operations', type: 'Annual', department: 'Transport', fiscalYear: '2026', totalAmount: 950000, allocatedAmount: 950000, spentAmount: 665000, remainingAmount: 285000, utilizationPercent: 70, status: 'pending' },
  { id: '6', code: 'BGT-2026-006', name: 'Research & Development', type: 'Project', department: 'Research', fiscalYear: '2026', totalAmount: 1500000, allocatedAmount: 750000, spentAmount: 450000, remainingAmount: 300000, utilizationPercent: 30, status: 'draft' },
];

const monthlyComparison = [
  { month: 'Jan', budget: 583333, actual: 520000 },
  { month: 'Feb', budget: 583333, actual: 480000 },
  { month: 'Mar', budget: 583333, actual: 560000 },
  { month: 'Apr', budget: 583333, actual: 620000 },
  { month: 'May', budget: 583333, actual: 540000 },
  { month: 'Jun', budget: 583333, actual: 580000 },
  { month: 'Jul', budget: 583333, actual: 610000 },
  { month: 'Aug', budget: 583333, actual: 590000 },
  { month: 'Sep', budget: 583333, actual: 630000 },
  { month: 'Oct', budget: 583333, actual: 670000 },
  { month: 'Nov', budget: 583333, actual: 640000 },
  { month: 'Dec', budget: 583333, actual: 700000 },
];

const BudgetManagement: React.FC = () => {
  const [showForm, setShowForm] = useState(false);
  const [selectedBudget, setSelectedBudget] = useState<Budget | null>(null);
  const [filterStatus, setFilterStatus] = useState<string>('all');
  const [filterDepartment, setFilterDepartment] = useState<string>('all');

  const filteredBudgets = budgets.filter(budget => {
    const matchesStatus = filterStatus === 'all' || budget.status === filterStatus;
    const matchesDepartment = filterDepartment === 'all' || budget.department === filterDepartment;
    return matchesStatus && matchesDepartment;
  });

  const totalBudget = budgets.reduce((sum, b) => sum + b.totalAmount, 0);
  const totalSpent = budgets.reduce((sum, b) => sum + b.spentAmount, 0);
  const totalRemaining = budgets.reduce((sum, b) => sum + b.remainingAmount, 0);
  const avgUtilization = (totalSpent / totalBudget * 100).toFixed(1);

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'approved': return 'bg-green-100 text-green-800';
      case 'pending': return 'bg-yellow-100 text-yellow-800';
      case 'draft': return 'bg-gray-100 text-gray-800';
      case 'rejected': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const getUtilizationColor = (percent: number) => {
    if (percent >= 90) return 'text-red-600 bg-red-100';
    if (percent >= 70) return 'text-yellow-600 bg-yellow-100';
    return 'text-green-600 bg-green-100';
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Budget Management</h1>
          <p className="text-gray-500">Plan, track and manage budgets</p>
        </div>
        <button
          onClick={() => setShowForm(true)}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          + Create Budget
        </button>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Budget</p>
          <p className="text-2xl font-bold text-blue-600">৳{totalBudget.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Spent</p>
          <p className="text-2xl font-bold text-green-600">৳{totalSpent.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Remaining</p>
          <p className="text-2xl font-bold text-purple-600">৳{totalRemaining.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Avg. Utilization</p>
          <p className="text-2xl font-bold text-orange-600">{avgUtilization}%</p>
        </div>
      </div>

      {/* Budget vs Actual Chart */}
      <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 className="text-lg font-semibold mb-4">Budget vs Actual Spending</h3>
        <ResponsiveContainer width="100%" height={300}>
          <BarChart data={monthlyComparison}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="month" />
            <YAxis />
            <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
            <Legend />
            <Bar dataKey="budget" fill="#3b82f6" name="Budget" />
            <Bar dataKey="actual" fill="#10b981" name="Actual" />
          </BarChart>
        </ResponsiveContainer>
      </div>

      {/* Filters */}
      <div className="bg-white p-4 rounded-lg border border-gray-100 flex flex-wrap gap-4">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select
            value={filterStatus}
            onChange={(e) => setFilterStatus(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg"
          >
            <option value="all">All Status</option>
            <option value="draft">Draft</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Department</label>
          <select
            value={filterDepartment}
            onChange={(e) => setFilterDepartment(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg"
          >
            <option value="all">All Departments</option>
            <option value="Academic">Academic</option>
            <option value="Admin">Administration</option>
            <option value="IT">IT</option>
            <option value="Library">Library</option>
            <option value="Transport">Transport</option>
            <option value="Research">Research</option>
          </select>
        </div>
      </div>

      {/* Budget Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget Code</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Budget</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Spent</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Remaining</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Utilization</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {filteredBudgets.map((budget) => (
              <tr key={budget.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 font-medium text-blue-600">{budget.code}</td>
                <td className="px-6 py-4 text-gray-900">{budget.name}</td>
                <td className="px-6 py-4 text-gray-600">{budget.department}</td>
                <td className="px-6 py-4 text-right text-gray-900">৳{budget.totalAmount.toLocaleString()}</td>
                <td className="px-6 py-4 text-right text-gray-900">৳{budget.spentAmount.toLocaleString()}</td>
                <td className="px-6 py-4 text-right text-gray-900">৳{budget.remainingAmount.toLocaleString()}</td>
                <td className="px-6 py-4">
                  <div className="flex items-center justify-center gap-2">
                    <div className="w-24 bg-gray-200 rounded-full h-2">
                      <div
                        className={`h-2 rounded-full ${getUtilizationColor(budget.utilizationPercent).replace('text-', 'bg-')}`}
                        style={{ width: `${budget.utilizationPercent}%` }}
                      />
                    </div>
                    <span className={`text-sm font-medium ${getUtilizationColor(budget.utilizationPercent)}`}>
                      {budget.utilizationPercent}%
                    </span>
                  </div>
                </td>
                <td className="px-6 py-4 text-center">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(budget.status)}`}>
                    {budget.status}
                  </span>
                </td>
                <td className="px-6 py-4 text-center">
                  <button
                    onClick={() => setSelectedBudget(budget)}
                    className="text-blue-600 hover:text-blue-800 mr-2"
                  >
                    View
                  </button>
                  {budget.status === 'pending' && (
                    <>
                      <button className="text-green-600 hover:text-green-800 mr-2">Approve</button>
                      <button className="text-red-600 hover:text-red-800">Reject</button>
                    </>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Budget Form Modal */}
      {showForm && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg">
            <div className="p-6 border-b border-gray-100">
              <h2 className="text-xl font-bold text-gray-900">Create New Budget</h2>
            </div>
            <div className="p-6 space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Budget Name</label>
                <input type="text" className="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Enter budget name" />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Department</label>
                  <select className="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Select Department</option>
                    <option value="academic">Academic</option>
                    <option value="admin">Administration</option>
                    <option value="it">IT</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Budget Type</label>
                  <select className="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="annual">Annual</option>
                    <option value="project">Project</option>
                    <option value="quarterly">Quarterly</option>
                  </select>
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Budget Amount</label>
                <input type="number" className="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="0.00" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea className="w-full px-4 py-2 border border-gray-300 rounded-lg" rows={3} placeholder="Enter description" />
              </div>
            </div>
            <div className="p-6 border-t border-gray-100 flex justify-end gap-3">
              <button onClick={() => setShowForm(false)} className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
              </button>
              <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Create Budget
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default BudgetManagement;
