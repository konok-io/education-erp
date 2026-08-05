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

interface Receivable {
  id: string;
  invoiceNumber: string;
  customerName: string;
  customerType: string;
  invoiceDate: string;
  dueDate: string;
  totalAmount: number;
  paidAmount: number;
  outstandingAmount: number;
  status: string;
  daysOverdue: number;
}

const receivables: Receivable[] = [
  { id: '1', invoiceNumber: 'AR-2026-001', customerName: 'Rahim Ahmed', customerType: 'Student', invoiceDate: '2026-01-01', dueDate: '2026-01-31', totalAmount: 50000, paidAmount: 0, outstandingAmount: 50000, status: 'overdue', daysOverdue: 35 },
  { id: '2', invoiceNumber: 'AR-2026-002', customerName: 'Fatema Begum', customerType: 'Student', invoiceDate: '2026-01-15', dueDate: '2026-02-15', totalAmount: 75000, paidAmount: 25000, outstandingAmount: 50000, status: 'partial', daysOverdue: 0 },
  { id: '3', invoiceNumber: 'AR-2026-003', customerName: 'Kamal Hossain', customerType: 'Student', invoiceDate: '2026-01-20', dueDate: '2026-02-20', totalAmount: 60000, paidAmount: 60000, outstandingAmount: 0, status: 'paid', daysOverdue: 0 },
  { id: '4', invoiceNumber: 'AR-2026-004', customerName: 'ABC Corporation', customerType: 'Corporate', invoiceDate: '2026-01-10', dueDate: '2026-02-10', totalAmount: 250000, paidAmount: 0, outstandingAmount: 250000, status: 'pending', daysOverdue: 15 },
  { id: '5', invoiceNumber: 'AR-2026-005', customerName: 'Jamal Uddin', customerType: 'Student', invoiceDate: '2026-01-25', dueDate: '2026-02-25', totalAmount: 45000, paidAmount: 0, outstandingAmount: 45000, status: 'pending', daysOverdue: 0 },
  { id: '6', invoiceNumber: 'AR-2026-006', customerName: 'XYZ Ltd', customerType: 'Corporate', invoiceDate: '2026-01-05', dueDate: '2026-02-05', totalAmount: 180000, paidAmount: 180000, outstandingAmount: 0, status: 'paid', daysOverdue: 0 },
  { id: '7', invoiceNumber: 'AR-2026-007', customerName: 'Sana Islam', customerType: 'Student', invoiceDate: '2025-12-15', dueDate: '2026-01-15', totalAmount: 80000, paidAmount: 0, outstandingAmount: 80000, status: 'overdue', daysOverdue: 51 },
  { id: '8', invoiceNumber: 'AR-2026-008', customerName: 'Def Enterprises', customerType: 'Corporate', invoiceDate: '2026-01-28', dueDate: '2026-02-28', totalAmount: 350000, paidAmount: 100000, outstandingAmount: 250000, status: 'partial', daysOverdue: 0 },
];

const agingData = [
  { name: '0-30 Days', value: 345000, color: '#10b981' },
  { name: '31-60 Days', value: 150000, color: '#f59e0b' },
  { name: '61-90 Days', value: 80000, color: '#f97316' },
  { name: '90+ Days', value: 50000, color: '#ef4444' },
];

const Receivables: React.FC = () => {
  const [filterStatus, setFilterStatus] = useState<string>('all');
  const [filterType, setFilterType] = useState<string>('all');
  const [searchTerm, setSearchTerm] = useState('');

  const filteredReceivables = receivables.filter(rec => {
    const matchesStatus = filterStatus === 'all' || rec.status === filterStatus;
    const matchesType = filterType === 'all' || rec.customerType === filterType;
    const matchesSearch = rec.invoiceNumber.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         rec.customerName.toLowerCase().includes(searchTerm.toLowerCase());
    return matchesStatus && matchesType && matchesSearch;
  });

  const totalReceivable = receivables.reduce((sum, r) => sum + r.totalAmount, 0);
  const totalPaid = receivables.reduce((sum, r) => sum + r.paidAmount, 0);
  const totalOutstanding = receivables.reduce((sum, r) => sum + r.outstandingAmount, 0);
  const overdueAmount = receivables.filter(r => r.status === 'overdue').reduce((sum, r) => sum + r.outstandingAmount, 0);

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
          <h1 className="text-2xl font-bold text-gray-900">Accounts Receivable</h1>
          <p className="text-gray-500">Manage customer invoices and payments</p>
        </div>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          + Create Invoice
        </button>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Receivable</p>
          <p className="text-2xl font-bold text-blue-600">৳{totalReceivable.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Amount Received</p>
          <p className="text-2xl font-bold text-green-600">৳{totalPaid.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Outstanding</p>
          <p className="text-2xl font-bold text-orange-600">৳{totalOutstanding.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Overdue</p>
          <p className="text-2xl font-bold text-red-600">৳{overdueAmount.toLocaleString()}</p>
        </div>
      </div>

      {/* Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Receivable Aging</h3>
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
          <h3 className="text-lg font-semibold mb-4">Outstanding by Customer Type</h3>
          <ResponsiveContainer width="100%" height={250}>
            <BarChart data={[
              { name: 'Student', amount: 225000 },
              { name: 'Corporate', amount: 500000 },
            ]}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="name" />
              <YAxis />
              <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
              <Bar dataKey="amount" fill="#3b82f6" />
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Filters */}
      <div className="bg-white p-4 rounded-lg border border-gray-100 flex flex-wrap gap-4">
        <div className="flex-1 min-w-[200px]">
          <input
            type="text"
            placeholder="Search by invoice or customer..."
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
          <option value="Student">Student</option>
          <option value="Corporate">Corporate</option>
        </select>
      </div>

      {/* Receivables Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
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
            {filteredReceivables.map((rec) => (
              <tr key={rec.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 font-medium text-blue-600">{rec.invoiceNumber}</td>
                <td className="px-6 py-4 text-gray-900">{rec.customerName}</td>
                <td className="px-6 py-4 text-gray-600">{rec.customerType}</td>
                <td className="px-6 py-4 text-right text-gray-900">৳{rec.totalAmount.toLocaleString()}</td>
                <td className="px-6 py-4 text-right text-green-600">৳{rec.paidAmount.toLocaleString()}</td>
                <td className={`px-6 py-4 text-right font-medium ${rec.outstandingAmount > 0 ? 'text-orange-600' : 'text-gray-600'}`}>
                  ৳{rec.outstandingAmount.toLocaleString()}
                </td>
                <td className="px-6 py-4 text-center text-gray-600">{rec.dueDate}</td>
                <td className="px-6 py-4 text-center">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(rec.status)}`}>
                    {rec.status}
                    {rec.daysOverdue > 0 && ` (${rec.daysOverdue}d)`}
                  </span>
                </td>
                <td className="px-6 py-4 text-center">
                  <button className="text-blue-600 hover:text-blue-800 mr-2">View</button>
                  {rec.outstandingAmount > 0 && (
                    <button className="text-green-600 hover:text-green-800">Receive</button>
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

export default Receivables;
