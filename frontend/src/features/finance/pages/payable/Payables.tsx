import React, { useState } from 'react';
import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  PieChart,
  Pie,
  Cell,
} from 'recharts';

interface Payable {
  id: string;
  billNumber: string;
  supplierName: string;
  billType: string;
  billDate: string;
  dueDate: string;
  totalAmount: number;
  paidAmount: number;
  outstandingAmount: number;
  status: string;
  daysOverdue: number;
}

const payables: Payable[] = [
  { id: '1', billNumber: 'AP-2026-001', supplierName: 'DESCO', billType: 'Utility', billDate: '2026-01-01', dueDate: '2026-01-15', totalAmount: 85000, paidAmount: 0, outstandingAmount: 85000, status: 'overdue', daysOverdue: 21 },
  { id: '2', billNumber: 'AP-2026-002', supplierName: 'Bangladesh Telecom', billType: 'Utility', billDate: '2026-01-10', dueDate: '2026-01-25', totalAmount: 45000, paidAmount: 45000, outstandingAmount: 0, status: 'paid', daysOverdue: 0 },
  { id: '3', billNumber: 'AP-2026-003', supplierName: 'Office Supplies Co.', billType: 'Purchase', billDate: '2026-01-15', dueDate: '2026-02-15', totalAmount: 120000, paidAmount: 0, outstandingAmount: 120000, status: 'pending', daysOverdue: 0 },
  { id: '4', billNumber: 'AP-2026-004', supplierName: 'Tech Solutions Ltd', billType: 'Service', billDate: '2026-01-05', dueDate: '2026-02-05', totalAmount: 250000, paidAmount: 100000, outstandingAmount: 150000, status: 'partial', daysOverdue: 0 },
  { id: '5', billNumber: 'AP-2026-005', supplierName: 'City Corporation', billType: 'Tax', billDate: '2026-01-01', dueDate: '2026-03-31', totalAmount: 180000, paidAmount: 0, outstandingAmount: 180000, status: 'pending', daysOverdue: 0 },
  { id: '6', billNumber: 'AP-2026-006', supplierName: 'Clean Services Ltd', billType: 'Service', billDate: '2026-01-20', dueDate: '2026-02-20', totalAmount: 35000, paidAmount: 0, outstandingAmount: 35000, status: 'pending', daysOverdue: 0 },
  { id: '7', billNumber: 'AP-2026-007', supplierName: 'Internet Provider', billType: 'Utility', billDate: '2025-12-28', dueDate: '2026-01-12', totalAmount: 25000, paidAmount: 0, outstandingAmount: 25000, status: 'overdue', daysOverdue: 24 },
  { id: '8', billNumber: 'AP-2026-008', supplierName: 'Furniture House', billType: 'Purchase', billDate: '2026-01-25', dueDate: '2026-02-25', totalAmount: 180000, paidAmount: 180000, outstandingAmount: 0, status: 'paid', daysOverdue: 0 },
];

const agingData = [
  { name: 'Current', value: 380000, color: '#10b981' },
  { name: '1-30 Days', value: 150000, color: '#f59e0b' },
  { name: '31-60 Days', value: 85000, color: '#f97316' },
  { name: '60+ Days', value: 25000, color: '#ef4444' },
];

const Payables: React.FC = () => {
  const [filterStatus, setFilterStatus] = useState<string>('all');
  const [filterType, setFilterType] = useState<string>('all');
  const [searchTerm, setSearchTerm] = useState('');

  const filteredPayables = payables.filter(pay => {
    const matchesStatus = filterStatus === 'all' || pay.status === filterStatus;
    const matchesType = filterType === 'all' || pay.billType === filterType;
    const matchesSearch = pay.billNumber.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         pay.supplierName.toLowerCase().includes(searchTerm.toLowerCase());
    return matchesStatus && matchesType && matchesSearch;
  });

  const totalPayable = payables.reduce((sum, p) => sum + p.totalAmount, 0);
  const totalPaid = payables.reduce((sum, p) => sum + p.paidAmount, 0);
  const totalOutstanding = payables.reduce((sum, p) => sum + p.outstandingAmount, 0);
  const dueSoon = payables.filter(p => p.status === 'pending' && new Date(p.dueDate) <= new Date(Date.now() + 7 * 24 * 60 * 60 * 1000)).reduce((sum, p) => sum + p.outstandingAmount, 0);

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'paid': return 'bg-green-100 text-green-800';
      case 'partial': return 'bg-blue-100 text-blue-800';
      case 'pending': return 'bg-yellow-100 text-yellow-800';
      case 'overdue': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Accounts Payable</h1>
          <p className="text-gray-500">Manage supplier bills and payments</p>
        </div>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          + Create Bill
        </button>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Payable</p>
          <p className="text-2xl font-bold text-blue-600">৳{totalPayable.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Amount Paid</p>
          <p className="text-2xl font-bold text-green-600">৳{totalPaid.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Outstanding</p>
          <p className="text-2xl font-bold text-orange-600">৳{totalOutstanding.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Due Soon (7 days)</p>
          <p className="text-2xl font-bold text-red-600">৳{dueSoon.toLocaleString()}</p>
        </div>
      </div>

      {/* Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Payable Aging</h3>
          <ResponsiveContainer width="100%" height={250}>
            <PieChart>
              <Pie
                data={agingData}
                cx="50%"
                cy="50%"
                labelLine={false}
                label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                outerRadius={80}
                fill="#8884d8"
                dataKey="value"
              >
                {agingData.map((entry, index) => (
                  <Cell key={`cell-${index}`} fill={entry.color} />
                ))}
              </Pie>
              <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
            </PieChart>
          </ResponsiveContainer>
        </div>

        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Payable by Category</h3>
          <ResponsiveContainer width="100%" height={250}>
            <BarChart data={[
              { name: 'Utility', amount: 155000 },
              { name: 'Purchase', amount: 300000 },
              { name: 'Service', amount: 185000 },
              { name: 'Tax', amount: 180000 },
            ]}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="name" />
              <YAxis />
              <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
              <Bar dataKey="amount" fill="#ef4444" />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Filters */}
      <div className="bg-white p-4 rounded-lg border border-gray-100 flex flex-wrap gap-4">
        <div className="flex-1 min-w-[200px]">
          <input
            type="text"
            placeholder="Search by bill or supplier..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full px-4 py-2 border border-gray-300 rounded-lg"
          />
        </div>
        <select
          value={filterStatus}
          onChange={(e) => setFilterStatus(e.target.value)}
          className="px-4 py-2 border border-gray-300 rounded-lg"
        >
          <option value="all">All Status</option>
          <option value="pending">Pending</option>
          <option value="partial">Partial</option>
          <option value="paid">Paid</option>
          <option value="overdue">Overdue</option>
        </select>
        <select
          value={filterType}
          onChange={(e) => setFilterType(e.target.value)}
          className="px-4 py-2 border border-gray-300 rounded-lg"
        >
          <option value="all">All Types</option>
          <option value="Utility">Utility</option>
          <option value="Purchase">Purchase</option>
          <option value="Service">Service</option>
          <option value="Tax">Tax</option>
        </select>
      </div>

      {/* Payables Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bill #</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Paid</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Outstanding</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Due Date</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {filteredPayables.map((pay) => (
              <tr key={pay.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 font-medium text-blue-600">{pay.billNumber}</td>
                <td className="px-6 py-4 text-gray-900">{pay.supplierName}</td>
                <td className="px-6 py-4 text-gray-600">{pay.billType}</td>
                <td className="px-6 py-4 text-right text-gray-900">৳{pay.totalAmount.toLocaleString()}</td>
                <td className="px-6 py-4 text-right text-green-600">৳{pay.paidAmount.toLocaleString()}</td>
                <td className={`px-6 py-4 text-right font-medium ${pay.outstandingAmount > 0 ? 'text-orange-600' : 'text-gray-600'}`}>
                  ৳{pay.outstandingAmount.toLocaleString()}
                </td>
                <td className="px-6 py-4 text-center text-gray-600">{pay.dueDate}</td>
                <td className="px-6 py-4 text-center">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(pay.status)}`}>
                    {pay.status}
                    {pay.daysOverdue > 0 && ` (${pay.daysOverdue}d)`}
                  </span>
                </td>
                <td className="px-6 py-4 text-center">
                  <button className="text-blue-600 hover:text-blue-800 mr-2">View</button>
                  {pay.outstandingAmount > 0 && (
                    <button className="text-green-600 hover:text-green-800">Pay</button>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default Payables;
