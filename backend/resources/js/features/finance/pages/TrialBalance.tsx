import React, { useState } from 'react';

interface TrialBalanceAccount {
  code: string;
  name: string;
  type: string;
  openingDebit: number;
  openingCredit: number;
  debit: number;
  credit: number;
  closingDebit: number;
  closingCredit: number;
}

const trialBalanceData: TrialBalanceAccount[] = [
  // Assets
  { code: '1-1-1', name: 'Cash', type: 'asset', openingDebit: 500000, openingCredit: 0, debit: 150000, credit: 365000, closingDebit: 285000, closingCredit: 0 },
  { code: '1-1-2', name: 'Bank - Islami Bank', type: 'asset', openingDebit: 2000000, openingCredit: 0, debit: 100000, credit: 0, closingDebit: 2100000, closingCredit: 0 },
  { code: '1-1-3', name: 'Accounts Receivable', type: 'asset', openingDebit: 800000, openingCredit: 0, debit: 0, credit: 200000, closingDebit: 600000, closingCredit: 0 },
  { code: '1-2-1', name: 'Land & Building', type: 'asset', openingDebit: 2500000, openingCredit: 0, debit: 0, credit: 0, closingDebit: 2500000, closingCredit: 0 },
  { code: '1-2-2', name: 'Furniture & Fixtures', type: 'asset', openingDebit: 850000, openingCredit: 0, debit: 0, credit: 0, closingDebit: 850000, closingCredit: 0 },
  { code: '1-2-3', name: 'Equipment', type: 'asset', openingDebit: 650000, openingCredit: 0, debit: 0, credit: 0, closingDebit: 650000, closingCredit: 0 },
  
  // Liabilities
  { code: '2-1-1', name: 'Accounts Payable', type: 'liability', openingDebit: 0, openingCredit: 500000, debit: 150000, credit: 0, closingDebit: 0, closingCredit: 350000 },
  { code: '2-1-2', name: 'Tax Payable', type: 'liability', openingDebit: 0, openingCredit: 200000, debit: 0, credit: 50000, closingDebit: 0, closingCredit: 250000 },
  { code: '2-2-1', name: 'Bank Loans', type: 'liability', openingDebit: 0, openingCredit: 1400000, debit: 0, credit: 0, closingDebit: 0, closingCredit: 1400000 },
  
  // Equity
  { code: '3-1', name: 'Capital Fund', type: 'equity', openingDebit: 0, openingCredit: 3500000, debit: 0, credit: 0, closingDebit: 0, closingCredit: 3500000 },
  { code: '3-2', name: 'Retained Earnings', type: 'equity', openingDebit: 0, openingCredit: 1800000, debit: 0, credit: 0, closingDebit: 0, closingCredit: 1800000 },
  
  // Income
  { code: '4-1', name: 'Tuition Fees Income', type: 'income', openingDebit: 0, openingCredit: 0, debit: 0, credit: 350000, closingDebit: 0, closingCredit: 350000 },
  { code: '4-2', name: 'Admission Fees', type: 'income', openingDebit: 0, openingCredit: 0, debit: 0, credit: 1200000, closingDebit: 0, closingCredit: 1200000 },
  { code: '4-3', name: 'Exam Fees', type: 'income', openingDebit: 0, openingCredit: 0, debit: 0, credit: 800000, closingDebit: 0, closingCredit: 800000 },
  { code: '4-4', name: 'Miscellaneous Income', type: 'income', openingDebit: 0, openingCredit: 0, debit: 0, credit: 1000000, closingDebit: 0, closingCredit: 1000000 },
  
  // Expenses
  { code: '5-1', name: 'Salary & Allowances', type: 'expense', openingDebit: 0, openingCredit: 0, debit: 250000, credit: 0, closingDebit: 250000, closingCredit: 0 },
  { code: '5-2', name: 'Utilities', type: 'expense', openingDebit: 0, openingCredit: 0, debit: 45000, credit: 0, closingDebit: 45000, closingCredit: 0 },
  { code: '5-3', name: 'Maintenance', type: 'expense', openingDebit: 0, openingCredit: 0, debit: 35000, credit: 0, closingDebit: 35000, closingCredit: 0 },
  { code: '5-4', name: 'Educational Resources', type: 'expense', openingDebit: 0, openingCredit: 0, debit: 60000, credit: 0, closingDebit: 60000, closingCredit: 0 },
  { code: '5-5', name: 'Miscellaneous Expenses', type: 'expense', openingDebit: 0, openingCredit: 0, debit: 30000, credit: 0, closingDebit: 30000, closingCredit: 0 },
];

const TrialBalance: React.FC = () => {
  const [fiscalYear, setFiscalYear] = useState('2026');
  const [period, setPeriod] = useState('January 2026');
  const [dateFrom] = useState('2026-01-01');
  const [dateTo] = useState('2026-01-31');
  const [showDetails, setShowDetails] = useState(true);

  const totalOpeningDebit = trialBalanceData.reduce((sum, item) => sum + item.openingDebit, 0);
  const totalOpeningCredit = trialBalanceData.reduce((sum, item) => sum + item.openingCredit, 0);
  const totalDebit = trialBalanceData.reduce((sum, item) => sum + item.debit, 0);
  const totalCredit = trialBalanceData.reduce((sum, item) => sum + item.credit, 0);
  const totalClosingDebit = trialBalanceData.reduce((sum, item) => sum + item.closingDebit, 0);
  const totalClosingCredit = trialBalanceData.reduce((sum, item) => sum + item.closingCredit, 0);

  const isBalanced = totalDebit === totalCredit;

  const getTypeColor = (type: string) => {
    switch (type) {
      case 'asset': return 'bg-blue-100 text-blue-800';
      case 'liability': return 'bg-red-100 text-red-800';
      case 'equity': return 'bg-purple-100 text-purple-800';
      case 'income': return 'bg-green-100 text-green-800';
      case 'expense': return 'bg-orange-100 text-orange-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const groupedData = {
    asset: trialBalanceData.filter(item => item.type === 'asset'),
    liability: trialBalanceData.filter(item => item.type === 'liability'),
    equity: trialBalanceData.filter(item => item.type === 'equity'),
    income: trialBalanceData.filter(item => item.type === 'income'),
    expense: trialBalanceData.filter(item => item.type === 'expense'),
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Trial Balance</h1>
          <p className="text-gray-500">Financial position as of period end</p>
        </div>
        <div className="flex gap-3">
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Export to PDF
          </button>
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Export to Excel
          </button>
          <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Print
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
            <option value="2024">2024</option>
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">Period</label>
          <select
            value={period}
            onChange={(e) => setPeriod(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg"
          >
            <option value="January 2026">January 2026</option>
            <option value="February 2026">February 2026</option>
            <option value="Q1 2026">Q1 2026</option>
            <option value="2026">Full Year 2026</option>
          </select>
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">From</label>
          <input type="date" value={dateFrom} readOnly className="px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" />
        </div>
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">To</label>
          <input type="date" value={dateTo} readOnly className="px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" />
        </div>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Generate
        </button>
      </div>

      {/* Balance Status */}
      <div className={`p-4 rounded-lg border ${isBalanced ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'}`}>
        <div className="flex items-center justify-between">
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
                {isBalanced ? 'Trial Balance is Balanced' : 'Trial Balance is NOT Balanced!'}
              </p>
              <p className={`text-sm ${isBalanced ? 'text-green-600' : 'text-red-600'}`}>
                Difference: ৳{Math.abs(totalDebit - totalCredit).toLocaleString()}
              </p>
            </div>
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
      </div>

      {/* Trial Balance Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {/* Header */}
        <div className="p-6 border-b border-gray-100 text-center">
          <h2 className="text-xl font-bold text-gray-900">Trial Balance</h2>
          <p className="text-gray-500">As of {period}, {fiscalYear}</p>
        </div>

        {/* Table */}
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase" rowSpan={2}>Account Code</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase" rowSpan={2}>Account Name</th>
                {showDetails && (
                  <>
                    <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase" colSpan={2}>Opening Balance</th>
                  </>
                )}
                <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase" colSpan={2}>Transaction</th>
                {showDetails && (
                  <>
                    <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase" colSpan={2}>Closing Balance</th>
                  </>
                )}
              </tr>
              <tr>
                {showDetails && (
                  <>
                    <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                    <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                  </>
                )}
                <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                {showDetails && (
                  <>
                    <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                    <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                  </>
                )}
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {Object.entries(groupedData).map(([type, accounts]) => (
                accounts.length > 0 && (
                  <React.Fragment key={type}>
                    {/* Group Header */}
                    <tr className="bg-gray-50">
                      <td colSpan={showDetails ? 2 : 2} className="px-4 py-2 font-semibold text-gray-900 capitalize">
                        {type}s
                      </td>
                      {showDetails && (
                        <>
                          <td className="px-4 py-2 text-right"></td>
                          <td className="px-4 py-2 text-right"></td>
                        </>
                      )}
                      <td className="px-4 py-2 text-right"></td>
                      <td className="px-4 py-2 text-right"></td>
                      {showDetails && (
                        <>
                          <td className="px-4 py-2 text-right"></td>
                          <td className="px-4 py-2 text-right"></td>
                        </>
                      )}
                    </tr>
                    {/* Account Rows */}
                    {accounts.map((account, index) => (
                      <tr key={account.code} className="hover:bg-gray-50">
                        <td className="px-4 py-3 text-gray-600">{account.code}</td>
                        <td className="px-4 py-3 text-gray-900">
                          <span className={`inline-block w-2 h-2 rounded-full mr-2 ${
                            account.type === 'asset' ? 'bg-blue-500' :
                            account.type === 'liability' ? 'bg-red-500' :
                            account.type === 'equity' ? 'bg-purple-500' :
                            account.type === 'income' ? 'bg-green-500' : 'bg-orange-500'
                          }`} />
                          {account.name}
                        </td>
                        {showDetails && (
                          <>
                            <td className="px-4 py-3 text-right text-gray-600">
                              {account.openingDebit > 0 ? `৳${account.openingDebit.toLocaleString()}` : '-'}
                            </td>
                            <td className="px-4 py-3 text-right text-gray-600">
                              {account.openingCredit > 0 ? `৳${account.openingCredit.toLocaleString()}` : '-'}
                            </td>
                          </>
                        )}
                        <td className="px-4 py-3 text-right text-green-600">
                          {account.debit > 0 ? `৳${account.debit.toLocaleString()}` : '-'}
                        </td>
                        <td className="px-4 py-3 text-right text-red-600">
                          {account.credit > 0 ? `৳${account.credit.toLocaleString()}` : '-'}
                        </td>
                        {showDetails && (
                          <>
                            <td className="px-4 py-3 text-right text-gray-900 font-medium">
                              {account.closingDebit > 0 ? `৳${account.closingDebit.toLocaleString()}` : '-'}
                            </td>
                            <td className="px-4 py-3 text-right text-gray-900 font-medium">
                              {account.closingCredit > 0 ? `৳${account.closingCredit.toLocaleString()}` : '-'}
                            </td>
                          </>
                        )}
                      </tr>
                    ))}
                  </React.Fragment>
                )
              ))}
            </tbody>
            <tfoot className="bg-gray-100 font-bold">
              <tr>
                <td colSpan={showDetails ? 2 : 2} className="px-4 py-3 text-gray-900">Total</td>
                {showDetails && (
                  <>
                    <td className="px-4 py-3 text-right text-gray-900">৳{totalOpeningDebit.toLocaleString()}</td>
                    <td className="px-4 py-3 text-right text-gray-900">৳{totalOpeningCredit.toLocaleString()}</td>
                  </>
                )}
                <td className="px-4 py-3 text-right text-green-600">৳{totalDebit.toLocaleString()}</td>
                <td className="px-4 py-3 text-right text-red-600">৳{totalCredit.toLocaleString()}</td>
                {showDetails && (
                  <>
                    <td className="px-4 py-3 text-right text-gray-900">৳{totalClosingDebit.toLocaleString()}</td>
                    <td className="px-4 py-3 text-right text-gray-900">৳{totalClosingCredit.toLocaleString()}</td>
                  </>
                )}
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Assets</p>
          <p className="text-xl font-bold text-blue-600">
            ৳{groupedData.asset.reduce((sum, a) => sum + a.closingDebit, 0).toLocaleString()}
          </p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Liabilities</p>
          <p className="text-xl font-bold text-red-600">
            ৳{groupedData.liability.reduce((sum, a) => sum + a.closingCredit, 0).toLocaleString()}
          </p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Equity</p>
          <p className="text-xl font-bold text-purple-600">
            ৳{groupedData.equity.reduce((sum, a) => sum + a.closingCredit, 0).toLocaleString()}
          </p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Net Position</p>
          <p className="text-xl font-bold text-green-600">
            ৳{(groupedData.asset.reduce((sum, a) => sum + a.closingDebit, 0) - 
               groupedData.liability.reduce((sum, a) => sum + a.closingCredit, 0) -
               groupedData.equity.reduce((sum, a) => sum + a.closingCredit, 0)).toLocaleString()}
          </p>
        </div>
      </div>
    </div>
  );
};

export default TrialBalance;
