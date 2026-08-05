import React, { useState } from 'react';
import {
  AreaChart,
  Area,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
  BarChart,
  Bar,
} from 'recharts';

const monthlyCashFlow = [
  { month: 'Jan', operating: 160000, investing: -50000, financing: 20000, closing: 2850000 },
  { month: 'Feb', operating: 170000, investing: -30000, financing: 0, closing: 2990000 },
  { month: 'Mar', operating: 180000, investing: -80000, financing: 50000, closing: 3140000 },
  { month: 'Apr', operating: 220000, investing: -40000, financing: 0, closing: 3320000 },
  { month: 'May', operating: 210000, investing: -60000, financing: 30000, closing: 3500000 },
  { month: 'Jun', operating: 230000, investing: -20000, financing: 0, closing: 3710000 },
  { month: 'Jul', operating: 240000, investing: -70000, financing: 10000, closing: 3890000 },
  { month: 'Aug', operating: 260000, investing: -50000, financing: 20000, closing: 4100000 },
  { month: 'Sep', operating: 270000, investing: -30000, financing: 0, closing: 4340000 },
  { month: 'Oct', operating: 280000, investing: -40000, financing: 15000, closing: 4580000 },
  { month: 'Nov', operating: 300000, investing: -60000, financing: 0, closing: 4820000 },
  { month: 'Dec', operating: 320000, investing: -20000, financing: 0, closing: 5020000 },
];

const CashFlow: React.FC = () => {
  const [fiscalYear, setFiscalYear] = useState('2026');
  const [showDetails, setShowDetails] = useState(true);

  const operatingInflow = 2850000;
  const operatingOutflow = 2150000;
  const netOperating = operatingInflow - operatingOutflow;

  const investingInflow = 150000;
  const investingOutflow = 480000;
  const netInvesting = investingInflow - investingOutflow;

  const financingInflow = 125000;
  const financingOutflow = 350000;
  const netFinancing = financingInflow - financingOutflow;

  const openingCash = 2650000;
  const closingCash = 5020000;
  const netChange = closingCash - openingCash;

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Cash Flow Statement</h1>
          <p className="text-gray-500">Statement of Cash Flows</p>
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
          <p className="text-sm text-gray-500">Opening Cash</p>
          <p className="text-2xl font-bold text-gray-600">৳{openingCash.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Net Change</p>
          <p className="text-2xl font-bold text-blue-600">৳{netChange.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Closing Cash</p>
          <p className="text-2xl font-bold text-green-600">৳{closingCash.toLocaleString()}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Cash Growth</p>
          <p className="text-2xl font-bold text-purple-600">89.4%</p>
        </div>
      </div>

      {/* Cash Flow Trend Chart */}
      <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 className="text-lg font-semibold mb-4">Cash Flow Trend</h3>
        <ResponsiveContainer width="100%" height={350}>
          <AreaChart data={monthlyCashFlow}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="month" />
            <YAxis />
            <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
            <Area type="monotone" dataKey="closing" stackId="1" stroke="#3b82f6" fill="#3b82f6" fillOpacity={0.6} name="Closing Balance" />
          </AreaChart>
        </ResponsiveContainer>
      </div>

      {/* Activity Breakdown */}
      <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 className="text-lg font-semibold mb-4">Cash Flow by Activity</h3>
        <ResponsiveContainer width="100%" height={300}>
          <BarChart data={[
            { name: 'Operating', inflow: operatingInflow, outflow: operatingOutflow },
            { name: 'Investing', inflow: investingInflow, outflow: investingOutflow },
            { name: 'Financing', inflow: financingInflow, outflow: financingOutflow },
          ]}>
            <CartesianGrid strokeDasharray="3 3" />
            <XAxis dataKey="name" />
            <YAxis />
            <Tooltip formatter={(value: number) => `৳${value.toLocaleString()}`} />
            <Bar dataKey="inflow" fill="#10b981" name="Inflow" />
            <Bar dataKey="outflow" fill="#ef4444" name="Outflow" />
          </BarChart>
        </ResponsiveContainer>
      </div>

      {/* Cash Flow Statement Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div className="p-6 border-b border-gray-100 text-center">
          <h2 className="text-xl font-bold text-gray-900">Cash Flow Statement</h2>
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
              {/* OPERATING ACTIVITIES */}
              <tr className="bg-blue-50">
                <td className="px-6 py-3 font-bold text-blue-900">A. CASH FLOW FROM OPERATING ACTIVITIES</td>
                <td className="px-6 py-3"></td>
              </tr>
              {showDetails && (
                <>
                  <tr className="hover:bg-gray-50">
                    <td className="px-8 py-2 text-gray-700">Cash received from students</td>
                    <td className="px-6 py-2 text-right text-green-600">৳{operatingInflow.toLocaleString()}</td>
                  </tr>
                  <tr className="hover:bg-gray-50">
                    <td className="px-8 py-2 text-gray-700">Cash paid to suppliers & employees</td>
                    <td className="px-6 py-2 text-right text-red-600">৳({operatingOutflow.toLocaleString()})</td>
                  </tr>
                </>
              )}
              <tr className="bg-blue-100 font-bold">
                <td className="px-6 py-3 text-blue-900">Net Cash from Operating Activities (A)</td>
                <td className="px-6 py-3 text-right text-blue-900">৳{netOperating.toLocaleString()}</td>
              </tr>

              {/* INVESTING ACTIVITIES */}
              <tr className="bg-purple-50">
                <td className="px-6 py-3 font-bold text-purple-900">B. CASH FLOW FROM INVESTING ACTIVITIES</td>
                <td className="px-6 py-3"></td>
              </tr>
              {showDetails && (
                <>
                  <tr className="hover:bg-gray-50">
                    <td className="px-8 py-2 text-gray-700">Sale of fixed assets</td>
                    <td className="px-6 py-2 text-right text-green-600">৳{investingInflow.toLocaleString()}</td>
                  </tr>
                  <tr className="hover:bg-gray-50">
                    <td className="px-8 py-2 text-gray-700">Purchase of fixed assets</td>
                    <td className="px-6 py-2 text-right text-red-600">৳({investingOutflow.toLocaleString()})</td>
                  </tr>
                </>
              )}
              <tr className="bg-purple-100 font-bold">
                <td className="px-6 py-3 text-purple-900">Net Cash from Investing Activities (B)</td>
                <td className="px-6 py-3 text-right text-purple-900">৳{netInvesting.toLocaleString()}</td>
              </tr>

              {/* FINANCING ACTIVITIES */}
              <tr className="bg-orange-50">
                <td className="px-6 py-3 font-bold text-orange-900">C. CASH FLOW FROM FINANCING ACTIVITIES</td>
                <td className="px-6 py-3"></td>
              </tr>
              {showDetails && (
                <>
                  <tr className="hover:bg-gray-50">
                    <td className="px-8 py-2 text-gray-700">Proceeds from loans</td>
                    <td className="px-6 py-2 text-right text-green-600">৳{financingInflow.toLocaleString()}</td>
                  </tr>
                  <tr className="hover:bg-gray-50">
                    <td className="px-8 py-2 text-gray-700">Loan repayments</td>
                    <td className="px-6 py-2 text-right text-red-600">৳({financingOutflow.toLocaleString()})</td>
                  </tr>
                </>
              )}
              <tr className="bg-orange-100 font-bold">
                <td className="px-6 py-3 text-orange-900">Net Cash from Financing Activities (C)</td>
                <td className="px-6 py-3 text-right text-orange-900">৳{netFinancing.toLocaleString()}</td>
              </tr>

              {/* NET CHANGE */}
              <tr className="bg-green-100 font-bold text-lg">
                <td className="px-6 py-3 text-green-900">Net Change in Cash (A + B + C)</td>
                <td className="px-6 py-3 text-right text-green-900">৳{netChange.toLocaleString()}</td>
              </tr>

              {/* OPENING & CLOSING */}
              <tr className="bg-gray-100 font-bold">
                <td className="px-6 py-3 text-gray-900">Opening Cash Balance</td>
                <td className="px-6 py-3 text-right text-gray-900">৳{openingCash.toLocaleString()}</td>
              </tr>
              <tr className="bg-gray-900 text-white font-bold text-lg">
                <td className="px-6 py-3">Closing Cash Balance</td>
                <td className="px-6 py-3 text-right">৳{closingCash.toLocaleString()}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default CashFlow;
