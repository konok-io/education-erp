import React from 'react';
import { CreditCard, DollarSign, Receipt, AlertCircle, ChevronRight, Download } from 'lucide-react';

const paymentStats = [
  { label: 'Total Collected', value: '৳4,250,000', icon: DollarSign, color: 'bg-green-500', change: '+15%' },
  { label: 'Pending Payments', value: '৳850,000', icon: AlertCircle, color: 'bg-yellow-500', change: '-8%' },
  { label: 'Total Invoices', value: '2,450', icon: Receipt, color: 'bg-blue-500', change: '+5%' },
  { label: 'Online Payments', value: '৳1,200,000', icon: CreditCard, color: 'bg-purple-500', change: '+22%' },
];

const recentPayments = [
  { id: 1, student: 'Rahim Ahmed', invoice: 'INV-2026-001', amount: '৳25,000', method: 'bKash', date: '2026-01-15', status: 'paid' },
  { id: 2, student: 'Fatema Begum', invoice: 'INV-2026-002', amount: '৳30,000', method: 'Bank', date: '2026-01-14', status: 'paid' },
  { id: 3, student: 'Kamal Hossain', invoice: 'INV-2026-003', amount: '৳25,000', method: 'bKash', date: '2026-01-13', status: 'pending' },
  { id: 4, student: 'Nusrat Jahan', invoice: 'INV-2026-004', amount: '৳20,000', method: 'Cash', date: '2026-01-12', status: 'paid' },
];

const quickLinks = [
  { title: 'All Payments', description: 'View payment records', href: '/payments/all' },
  { title: 'Create Invoice', description: 'Generate new invoice', href: '/payments/invoice' },
  { title: 'Fee Collection', description: 'Collect student fees', href: '/payments/collect' },
  { title: 'Payment Reports', description: 'Generate financial reports', href: '/payments/reports' },
];

const statusColors: Record<string, string> = {
  paid: 'bg-green-100 text-green-700',
  pending: 'bg-yellow-100 text-yellow-700',
  overdue: 'bg-red-100 text-red-700',
};

const PaymentsDashboard: React.FC = () => {
  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Payments Dashboard</h1>
          <p className="text-gray-500">Manage student fees and payments</p>
        </div>
        <div className="flex gap-3">
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2">
            <Download className="w-4 h-4" />
            Export
          </button>
          <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
            <Receipt className="w-5 h-5" />
            Create Invoice
          </button>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {paymentStats.map((stat, index) => (
          <div key={index} className="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-gray-500">{stat.label}</p>
                <p className="text-xl font-bold text-gray-900">{stat.value}</p>
                {stat.change && <p className="text-sm text-green-600 mt-1">{stat.change} from last month</p>}
              </div>
              <div className={`w-12 h-12 ${stat.color} rounded-lg flex items-center justify-center`}>
                <stat.icon className="w-6 h-6 text-white" />
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Quick Links */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {quickLinks.map((link, index) => (
          <button key={index} className="flex items-center gap-4 p-4 bg-white rounded-xl border border-gray-100 hover:border-blue-300 hover:shadow-sm transition-all text-left group">
            <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <CreditCard className="w-5 h-5 text-blue-600" />
            </div>
            <div className="flex-1">
              <p className="font-medium text-gray-900">{link.title}</p>
              <p className="text-sm text-gray-500">{link.description}</p>
            </div>
            <ChevronRight className="w-5 h-5 text-gray-400" />
          </button>
        ))}
      </div>

      {/* Recent Payments */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-lg font-semibold">Recent Payments</h2>
          <button className="text-blue-600 hover:text-blue-700 text-sm">View All</button>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {recentPayments.map((payment) => (
                <tr key={payment.id} className="hover:bg-gray-50">
                  <td className="px-4 py-3 text-sm font-medium text-gray-900">{payment.student}</td>
                  <td className="px-4 py-3 text-sm text-gray-700 font-mono">{payment.invoice}</td>
                  <td className="px-4 py-3 text-sm font-medium text-gray-900">{payment.amount}</td>
                  <td className="px-4 py-3 text-sm text-gray-700">{payment.method}</td>
                  <td className="px-4 py-3 text-sm text-gray-700">{payment.date}</td>
                  <td className="px-4 py-3">
                    <span className={`px-2 py-1 text-xs font-medium rounded-full ${statusColors[payment.status]}`}>
                      {payment.status}
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default PaymentsDashboard;
