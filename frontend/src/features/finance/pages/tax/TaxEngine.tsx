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
} from 'recharts';

interface Tax {
  id: string;
  taxCode: string;
  taxName: string;
  taxType: string;
  rate: number;
  calculationMethod: string;
  effectiveDate: string;
  expiryDate?: string;
  status: string;
}

interface TaxReturn {
  id: string;
  period: string;
  taxType: string;
  taxableAmount: number;
  taxAmount: number;
  status: string;
  dueDate: string;
  paidDate?: string;
}

const taxes: Tax[] = [
  { id: '1', taxCode: 'VAT-STD', taxName: 'Standard VAT', taxType: 'VAT', rate: 15, calculationMethod: 'exclusive', effectiveDate: '2024-01-01', status: 'active' },
  { id: '2', taxCode: 'VAT-RED', taxName: 'Reduced VAT', taxType: 'VAT', rate: 7.5, calculationMethod: 'exclusive', effectiveDate: '2024-01-01', status: 'active' },
  { id: '3', taxCode: 'TAX-AIT', taxName: 'Advance Income Tax', taxType: 'Income Tax', rate: 5, calculationMethod: 'exclusive', effectiveDate: '2024-01-01', status: 'active' },
  { id: '4', taxCode: 'TAX-WHT', taxName: 'Withholding Tax', taxType: 'Withholding Tax', rate: 10, calculationMethod: 'inclusive', effectiveDate: '2024-01-01', status: 'active' },
  { id: '5', taxCode: 'GST-STD', taxName: 'Standard GST', taxType: 'GST', rate: 18, calculationMethod: 'exclusive', effectiveDate: '2024-01-01', status: 'active' },
  { id: '6', taxCode: 'TAX-SRV', taxName: 'Service Tax', taxType: 'Service Tax', rate: 5, calculationMethod: 'exclusive', effectiveDate: '2024-01-01', status: 'active' },
];

const taxReturns: TaxReturn[] = [
  { id: '1', period: 'January 2026', taxType: 'VAT', taxableAmount: 2500000, taxAmount: 375000, status: 'paid', dueDate: '2026-02-15', paidDate: '2026-02-10' },
  { id: '2', period: 'February 2026', taxType: 'VAT', taxableAmount: 2800000, taxAmount: 420000, status: 'pending', dueDate: '2026-03-15' },
  { id: '3', period: 'Q1 2026', taxType: 'Income Tax', taxableAmount: 8500000, taxAmount: 425000, status: 'pending', dueDate: '2026-04-30' },
  { id: '4', period: 'Q4 2025', taxType: 'Withholding Tax', taxableAmount: 3200000, taxAmount: 320000, status: 'paid', dueDate: '2026-01-31', paidDate: '2026-01-28' },
];

const monthlyTaxData = [
  { month: 'Jul', vat: 320000, tax: 280000 },
  { month: 'Aug', vat: 350000, tax: 310000 },
  { month: 'Sep', vat: 380000, tax: 290000 },
  { month: 'Oct', vat: 340000, tax: 320000 },
  { month: 'Nov', vat: 410000, tax: 350000 },
  { month: 'Dec', vat: 420000, tax: 380000 },
];

const TaxEngine: React.FC = () => {
  const [showForm, setShowForm] = useState(false);
  const [selectedTax, setSelectedTax] = useState<Tax | null>(null);
  const [filterType, setFilterType] = useState<string>('all');

  const filteredTaxes = taxes.filter(tax => {
    return filterType === 'all' || tax.taxType === filterType;
  });

  const totalTaxLiability = taxReturns.filter(t => t.status === 'pending').reduce((sum, t) => sum + t.taxAmount, 0);
  const totalTaxPaid = taxReturns.filter(t => t.status === 'paid').reduce((sum, t) => sum + t.taxAmount, 0);

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active': return 'bg-green-100 text-green-800';
      case 'inactive': return 'bg-gray-100 text-gray-800';
      case 'pending': return 'bg-yellow-100 text-yellow-800';
      case 'paid': return 'bg-blue-100 text-blue-800';
      case 'overdue': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Tax Engine</h1>
          <p className="text-gray-500">VAT, Tax Configuration & Returns</p>
        </div>
        <button
          onClick={() => setShowForm(true)}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          + Add Tax
        </button>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Taxes</p>
          <p className="text-2xl font-bold text-blue-600">{taxes.length}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Tax Liability</p>
          <p className="text-2xl font-bold text-orange-600">৳{totalTaxLiability.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Tax Paid (YTD)</p>
          <p className="text-2xl font-bold text-green-600">৳{totalTaxPaid.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Pending Returns</p>
          <p className="text-2xl font-bold text-red-600">{taxReturns.filter(t => t.status === 'pending').length}</p>
        </div>
      </div>

      {/* Tax Liability Chart */}
      <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 className="text-lg font-semibold mb-4">Monthly Tax Liability</h3>
        <ResponsiveContainer width="100%" height={300}>
          <BarChart data={monthlyTaxData}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="month" />
            <YAxis />
            <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
            <Bar dataKey="vat" fill="#3b82f6" name="VAT" />
            <Bar dataKey="tax" fill="#ef4444" name="Income Tax" />
          </BarChart>
        </ResponsiveContainer>
      </div>

      {/* Filters */}
      <div className="bg-white p-4 rounded-lg border border-gray-100 flex flex-wrap gap-4">
        <select
          value={filterType}
          onChange={(e) => setFilterType(e.target.value)}
          className="px-4 py-2 border border-gray-300 rounded-lg"
        >
          <option value="all">All Types</option>
          <option value="VAT">VAT</option>
          <option value="Income Tax">Income Tax</option>
          <option value="Withholding Tax">Withholding Tax</option>
          <option value="GST">GST</option>
          <option value="Service Tax">Service Tax</option>
        </select>
      </div>

      {/* Tax Configuration Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div className="p-4 border-b border-gray-100">
          <h3 className="font-semibold text-gray-900">Tax Configuration</h3>
        </div>
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tax Code</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tax Name</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Rate</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Effective Date</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {filteredTaxes.map((tax) => (
              <tr key={tax.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 font-medium text-blue-600">{tax.taxCode}</td>
                <td className="px-6 py-4 text-gray-900">{tax.taxName}</td>
                <td className="px-6 py-4 text-gray-600">{tax.taxType}</td>
                <td className="px-6 py-4 text-right font-medium text-gray-900">{tax.rate}%</td>
                <td className="px-6 py-4 text-gray-600 capitalize">{tax.calculationMethod}</td>
                <td className="px-6 py-4 text-center text-gray-600">{tax.effectiveDate}</td>
                <td className="px-6 py-4 text-center">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(tax.status)}`}>
                    {tax.status}
                  </span>
                </td>
                <td className="px-6 py-4 text-center">
                  <button
                    onClick={() => setSelectedTax(tax)}
                    className="text-blue-600 hover:text-blue-800 mr-2"
                  >
                    Edit
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Tax Returns Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div className="p-4 border-b border-gray-100 flex items-center justify-between">
          <h3 className="font-semibold text-gray-900">Tax Returns</h3>
          <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
            File Return
          </button>
        </div>
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tax Type</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Taxable Amount</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tax Amount</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {taxReturns.map((ret) => (
              <tr key={ret.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 text-gray-900">{ret.period}</td>
                <td className="px-6 py-4 text-gray-600">{ret.taxType}</td>
                <td className="px-6 py-4 text-right text-gray-900">৳{ret.taxableAmount.toLocaleString()}</td>
                <td className="px-6 py-4 text-right font-medium text-gray-900">৳{ret.taxAmount.toLocaleString()}</td>
                <td className="px-6 py-4 text-gray-600">{ret.dueDate}</td>
                <td className="px-6 py-4 text-center">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(ret.status)}`}>
                    {ret.status}
                  </span>
                </td>
                <td className="px-6 py-4 text-center">
                  <button className="text-blue-600 hover:text-blue-800 mr-2">View</button>
                  {ret.status === 'pending' && (
                    <button className="text-green-600 hover:text-green-800">Pay</button>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Add Tax Form Modal */}
      {showForm && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg">
            <div className="p-6 border-b border-gray-100">
              <h2 className="text-xl font-bold text-gray-900">Add New Tax</h2>
            </div>
            <div className="p-6 space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Tax Code</label>
                <input type="text" className="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="e.g., VAT-STD" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Tax Name</label>
                <input type="text" className="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="e.g., Standard VAT" />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Tax Type</label>
                  <select className="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="VAT">VAT</option>
                    <option value="Income Tax">Income Tax</option>
                    <option value="Withholding Tax">Withholding Tax</option>
                    <option value="GST">GST</option>
                    <option value="Service Tax">Service Tax</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Rate (%)</label>
                  <input type="number" className="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="15" />
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Calculation Method</label>
                  <select className="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="exclusive">Exclusive</option>
                    <option value="inclusive">Inclusive</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Effective Date</label>
                  <input type="date" className="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                </div>
              </div>
            </div>
            <div className="p-6 border-t border-gray-100 flex justify-end gap-3">
              <button onClick={() => setShowForm(false)} className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
              </button>
              <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Save Tax
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default TaxEngine;
