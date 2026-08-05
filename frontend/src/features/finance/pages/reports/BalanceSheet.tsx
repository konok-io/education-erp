import React, { useState } from 'react';
import {
  PieChart,
  Pie,
  Cell,
  ResponsiveContainer,
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
} from 'recharts';

interface BalanceSheetData {
  currentAssets: { name: string; amount: number }[];
  fixedAssets: { name: string; amount: number }[];
  currentLiabilities: { name: string; amount: number }[];
  longTermLiabilities: { name: string; amount: number }[];
  equity: { name: string; amount: number }[];
}

const balanceSheetData: BalanceSheetData = {
  currentAssets: [
    { name: 'Cash', amount: 2850000 },
    { name: 'Bank Accounts', amount: 5680000 },
    { name: 'Accounts Receivable', amount: 1250000 },
    { name: 'Inventory', amount: 450000 },
    { name: 'Prepaid Expenses', amount: 120000 },
  ],
  fixedAssets: [
    { name: 'Land & Building', amount: 5000000 },
    { name: 'Furniture & Fixtures', amount: 850000 },
    { name: 'Equipment', amount: 1200000 },
    { name: 'Vehicles', amount: 450000 },
    { name: 'Less: Accumulated Depreciation', amount: -450000 },
  ],
  currentLiabilities: [
    { name: 'Accounts Payable', amount: 890000 },
    { name: 'Tax Payable', amount: 250000 },
    { name: 'Salary Payable', amount: 350000 },
    { name: 'Security Deposits', amount: 460000 },
    { name: 'Other Current Liabilities', amount: 180000 },
  ],
  longTermLiabilities: [
    { name: 'Bank Loans', amount: 1400000 },
    { name: 'Deferred Revenue', amount: 300000 },
  ],
  equity: [
    { name: 'Capital Fund', amount: 5000000 },
    { name: 'Retained Earnings', amount: 2150000 },
    { name: 'Current Year Surplus', amount: 850000 },
  ],
};

const pieColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];

const BalanceSheet: React.FC = () => {
  const [fiscalYear, setFiscalYear] = useState('2026');
  const [period, setPeriod] = useState('December 2026');
  const [showDetails, setShowDetails] = useState(true);

  const totalCurrentAssets = balanceSheetData.currentAssets.reduce((sum, item) => sum + item.amount, 0);
  const totalFixedAssets = balanceSheetData.fixedAssets.reduce((sum, item) => sum + item.amount, 0);
  const totalAssets = totalCurrentAssets + totalFixedAssets;

  const totalCurrentLiabilities = balanceSheetData.currentLiabilities.reduce((sum, item) => sum + item.amount, 0);
  const totalLongTermLiabilities = balanceSheetData.longTermLiabilities.reduce((sum, item) => sum + item.amount, 0);
  const totalLiabilities = totalCurrentLiabilities + totalLongTermLiabilities;

  const totalEquity = balanceSheetData.equity.reduce((sum, item) => sum + item.amount, 0);

  const isBalanced = Math.abs((totalLiabilities + totalEquity) - totalAssets) < 1;

  const assetDistribution = [
    { name: 'Current Assets', value: totalCurrentAssets },
    { name: 'Fixed Assets', value: totalFixedAssets },
  ];

  const liabilityDistribution = [
    { name: 'Current Liabilities', value: totalCurrentLiabilities },
    { name: 'Long Term Liabilities', value: totalLongTermLiabilities },
    { name: 'Equity', value: totalEquity },
  ];

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Balance Sheet</h1>
          <p className="text-gray-500">Statement of Financial Position</p>
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
          <label className="block text-sm font-medium text-gray-700 mb-1">Period</label>
          <select
            value={period}
            onChange={(e) => setPeriod(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg"
          >
            <option value="December 2026">December 2026</option>
            <option value="November 2026">November 2026</option>
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

      {/* Balance Status */}
      <div className={`p-4 rounded-lg border ${isBalanced ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'}`}>
        <div className="flex items-center gap-3">
          <div className={`w-10 h-10 rounded-full flex items-center justify-center ${isBalanced ? 'bg-green-100' : 'bg-red-100'}`}>
            {isBalanced ? (
              <svg className="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
              </svg>
            ) : (
              <svg className="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
              </svg>
            )}
          </div>
          <div>
            <p className={`font-semibold ${isBalanced ? 'text-green-800' : 'text-red-800'}`}>
              {isBalanced ? 'Balance Sheet is Balanced' : 'Balance Sheet is NOT Balanced!'}
            </p>
            <p className={`text-sm ${isBalanced ? 'text-green-600' : 'text-red-600'}`}>
              Assets = ৳{totalAssets.toLocaleString()} | Liabilities + Equity = ৳{(totalLiabilities + totalEquity).toLocaleString()}
            </p>
          </div>
        </div>
      </div>

      {/* Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Asset Distribution</h3>
          <ResponsiveContainer width="100%" height={250}>
            <PieChart>
              <Pie
                data={assetDistribution}
                cx="50%"
                cy="50%"
                labelLine={false}
                label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                outerRadius={80}
                fill="#8884d8"
                dataKey="value"
              >
                {assetDistribution.map((entry, index) => (
                  <Cell key={`cell-${index}`} fill={pieColors[index % pieColors.length]} />
                ))}
              </Pie>
              <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
            </PieChart>
          </ResponsiveContainer>
        </div>

        <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
          <h3 className="text-lg font-semibold mb-4">Liabilities & Equity Distribution</h3>
          <ResponsiveContainer width="100%" height={250}>
            <PieChart>
              <Pie
                data={liabilityDistribution}
                cx="50%"
                cy="50%"
                labelLine={false}
                label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                outerRadius={80}
                fill="#8884d8"
                dataKey="value"
              >
                {liabilityDistribution.map((entry, index) => (
                  <Cell key={`cell-${index}`} fill={pieColors[index % pieColors.length]} />
                ))}
              </Pie>
              <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
            </PieChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Balance Sheet Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div className="p-6 border-b border-gray-100 text-center">
          <h2 className="text-xl font-bold text-gray-900">Balance Sheet</h2>
          <p className="text-gray-500">As of {period}, {fiscalYear}</p>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Particulars</th>
                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Notes</th>
                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount (৳)</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {/* ASSETS */}
              <tr className="bg-blue-50">
                <td colSpan={3} className="px-6 py-3 font-bold text-blue-900">A. ASSETS</td>
              </tr>
              
              {/* Current Assets */}
              <tr className="bg-gray-50">
                <td className="px-6 py-3 font-semibold text-gray-900">Current Assets</td>
                <td></td>
                <td className="px-6 py-3 text-right font-semibold text-gray-900">৳{totalCurrentAssets.toLocaleString()}</td>
              </tr>
              {showDetails && balanceSheetData.currentAssets.map((item, index) => (
                <tr key={index} className="hover:bg-gray-50">
                  <td className="px-8 py-2 text-gray-700">{item.name}</td>
                  <td className="px-6 py-2 text-right text-gray-500">1</td>
                  <td className="px-6 py-2 text-right text-gray-900">৳{item.amount.toLocaleString()}</td>
                </tr>
              ))}

              {/* Fixed Assets */}
              <tr className="bg-gray-50">
                <td className="px-6 py-3 font-semibold text-gray-900">Fixed Assets (Net)</td>
                <td></td>
                <td className="px-6 py-3 text-right font-semibold text-gray-900">৳{totalFixedAssets.toLocaleString()}</td>
              </tr>
              {showDetails && balanceSheetData.fixedAssets.map((item, index) => (
                <tr key={index} className="hover:bg-gray-50">
                  <td className={`px-8 py-2 ${item.amount < 0 ? 'text-red-600' : 'text-gray-700'}`}>{item.name}</td>
                  <td className="px-6 py-2 text-right text-gray-500">2</td>
                  <td className={`px-6 py-2 text-right ${item.amount < 0 ? 'text-red-600' : 'text-gray-900'}`}>৳{Math.abs(item.amount).toLocaleString()}</td>
                </tr>
              ))}

              {/* Total Assets */}
              <tr className="bg-blue-100 font-bold">
                <td className="px-6 py-3 text-blue-900">TOTAL ASSETS (A)</td>
                <td></td>
                <td className="px-6 py-3 text-right text-blue-900">৳{totalAssets.toLocaleString()}</td>
              </tr>

              {/* LIABILITIES */}
              <tr className="bg-red-50">
                <td colSpan={3} className="px-6 py-3 font-bold text-red-900">B. LIABILITIES</td>
              </tr>
              
              {/* Current Liabilities */}
              <tr className="bg-gray-50">
                <td className="px-6 py-3 font-semibold text-gray-900">Current Liabilities</td>
                <td></td>
                <td className="px-6 py-3 text-right font-semibold text-gray-900">৳{totalCurrentLiabilities.toLocaleString()}</td>
              </tr>
              {showDetails && balanceSheetData.currentLiabilities.map((item, index) => (
                <tr key={index} className="hover:bg-gray-50">
                  <td className="px-8 py-2 text-gray-700">{item.name}</td>
                  <td className="px-6 py-2 text-right text-gray-500">3</td>
                  <td className="px-6 py-2 text-right text-gray-900">৳{item.amount.toLocaleString()}</td>
                </tr>
              ))}

              {/* Long Term Liabilities */}
              <tr className="bg-gray-50">
                <td className="px-6 py-3 font-semibold text-gray-900">Long Term Liabilities</td>
                <td></td>
                <td className="px-6 py-3 text-right font-semibold text-gray-900">৳{totalLongTermLiabilities.toLocaleString()}</td>
              </tr>
              {showDetails && balanceSheetData.longTermLiabilities.map((item, index) => (
                <tr key={index} className="hover:bg-gray-50">
                  <td className="px-8 py-2 text-gray-700">{item.name}</td>
                  <td className="px-6 py-2 text-right text-gray-500">4</td>
                  <td className="px-6 py-2 text-right text-gray-900">৳{item.amount.toLocaleString()}</td>
                </tr>
              ))}

              {/* Total Liabilities */}
              <tr className="bg-red-100 font-bold">
                <td className="px-6 py-3 text-red-900">TOTAL LIABILITIES (B)</td>
                <td></td>
                <td className="px-6 py-3 text-right text-red-900">৳{totalLiabilities.toLocaleString()}</td>
              </tr>

              {/* EQUITY */}
              <tr className="bg-purple-50">
                <td colSpan={3} className="px-6 py-3 font-bold text-purple-900">C. EQUITY</td>
              </tr>
              {showDetails && balanceSheetData.equity.map((item, index) => (
                <tr key={index} className="hover:bg-gray-50">
                  <td className="px-8 py-2 text-gray-700">{item.name}</td>
                  <td className="px-6 py-2 text-right text-gray-500">5</td>
                  <td className="px-6 py-2 text-right text-gray-900">৳{item.amount.toLocaleString()}</td>
                </tr>
              ))}

              {/* Total Equity */}
              <tr className="bg-purple-100 font-bold">
                <td className="px-6 py-3 text-purple-900">TOTAL EQUITY (C)</td>
                <td></td>
                <td className="px-6 py-3 text-right text-purple-900">৳{totalEquity.toLocaleString()}</td>
              </tr>

              {/* Total */}
              <tr className="bg-gray-900 text-white font-bold">
                <td className="px-6 py-3">TOTAL LIABILITIES & EQUITY (B + C)</td>
                <td></td>
                <td className="px-6 py-3 text-right">৳{(totalLiabilities + totalEquity).toLocaleString()}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      {/* Summary */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Assets</p>
          <p className="text-2xl font-bold text-blue-600">৳{totalAssets.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Liabilities</p>
          <p className="text-2xl font-bold text-red-600">৳{totalLiabilities.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Equity</p>
          <p className="text-2xl font-bold text-purple-600">৳{totalEquity.toLocaleString()}</p>
        </div>
      </div>
    </div>
  );
};

export default BalanceSheet;
