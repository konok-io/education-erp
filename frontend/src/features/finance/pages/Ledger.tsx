import React, { useState } from 'react';

interface LedgerEntry {
  id: string;
  date: string;
  voucherNumber: string;
  voucherType: string;
  description: string;
  debit: number;
  credit: number;
  balance: number;
  runningBalance: number;
}

const accounts = [
  { code: '1-1-1', name: 'Cash', type: 'asset' },
  { code: '1-1-2', name: 'Bank - Islami Bank', type: 'asset' },
  { code: '4-1', name: 'Tuition Fees Income', type: 'income' },
  { code: '5-1', name: 'Salary Expense', type: 'expense' },
  { code: '1-1-3', name: 'Accounts Receivable', type: 'asset' },
  { code: '2-1-1', name: 'Accounts Payable', type: 'liability' },
];

const sampleLedgerData: { [key: string]: LedgerEntry[] } = {
  '1-1-1': [
    { id: '1', date: '2026-01-01', voucherNumber: 'OB-2026', voucherType: 'Opening', description: 'Opening Balance', debit: 500000, credit: 0, balance: 500000, runningBalance: 500000 },
    { id: '2', date: '2026-01-05', voucherNumber: 'JE-2026-001', voucherType: 'Receipt', description: 'Cash received from fees', debit: 150000, credit: 0, balance: 650000, runningBalance: 650000 },
    { id: '3', date: '2026-01-10', voucherNumber: 'JE-2026-002', voucherType: 'Payment', description: 'Electricity bill payment', debit: 0, credit: 15000, balance: 635000, runningBalance: 635000 },
    { id: '4', date: '2026-01-15', voucherNumber: 'JE-2026-004', voucherType: 'Payment', description: 'Salary payment', debit: 0, credit: 250000, balance: 385000, runningBalance: 385000 },
    { id: '5', date: '2026-01-20', voucherNumber: 'JE-2026-006', voucherType: 'Contra', description: 'Deposited to bank', debit: 0, credit: 100000, balance: 285000, runningBalance: 285000 },
  ],
  '4-1': [
    { id: '1', date: '2026-01-01', voucherNumber: 'OB-2026', voucherType: 'Opening', description: 'Opening Balance', debit: 0, credit: 0, balance: 0, runningBalance: 0 },
    { id: '2', date: '2026-01-05', voucherNumber: 'JE-2026-001', voucherType: 'Receipt', description: 'Tuition fees received', debit: 0, credit: 150000, balance: 150000, runningBalance: 150000 },
    { id: '3', date: '2026-01-15', voucherNumber: 'JE-2026-001', voucherType: 'Receipt', description: 'Tuition fees received', debit: 0, credit: 200000, balance: 350000, runningBalance: 350000 },
  ],
};

const Ledger: React.FC = () => {
  const [selectedAccount, setSelectedAccount] = useState(accounts[0].code);
  const [dateFrom, setDateFrom] = useState('2026-01-01');
  const [dateTo, setDateTo] = useState('2026-01-31');
  const [showAccountPicker, setShowAccountPicker] = useState(false);

  const currentAccount = accounts.find(a => a.code === selectedAccount);
  const ledgerData = sampleLedgerData[selectedAccount] || [];

  const totalDebit = ledgerData.reduce((sum, entry) => sum + entry.debit, 0);
  const totalCredit = ledgerData.reduce((sum, entry) => sum + entry.credit, 0);

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">General Ledger</h1>
          <p className="text-gray-500">View account transactions and balances</p>
        </div>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Export to PDF
        </button>
      </div>

      {/* Filters */}
      <div className="bg-white p-4 rounded-lg border border-gray-100 space-y-4">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          {/* Account Selector */}
          <div className="md:col-span-2">
            <label className="block text-sm font-medium text-gray-700 mb-1">Select Account</label>
            <div
              onClick={() => setShowAccountPicker(!showAccountPicker)}
              className="w-full px-4 py-2 border border-gray-300 rounded-lg cursor-pointer flex items-center justify-between"
            >
              <span>
                {currentAccount ? `${currentAccount.code} - ${currentAccount.name}` : 'Select an account'}
              </span>
              <svg className="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
              </svg>
            </div>
            {showAccountPicker && (
              <div className="mt-1 border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                {accounts.map(account => (
                  <div
                    key={account.code}
                    onClick={() => {
                      setSelectedAccount(account.code);
                      setShowAccountPicker(false);
                    }}
                    className={`px-4 py-2 cursor-pointer hover:bg-gray-50 ${
                      selectedAccount === account.code ? 'bg-blue-50' : ''
                    }`}
                  >
                    <span className="font-medium">{account.code}</span>
                    <span className="text-gray-500 ml-2">{account.name}</span>
                  </div>
                ))}
              </div>
            )}
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">From Date</label>
            <input
              type="date"
              value={dateFrom}
              onChange={(e) => setDateFrom(e.target.value)}
              className="w-full px-4 py-2 border border-gray-300 rounded-lg"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">To Date</label>
            <input
              type="date"
              value={dateTo}
              onChange={(e) => setDateTo(e.target.value)}
              className="w-full px-4 py-2 border border-gray-300 rounded-lg"
            />
          </div>
        </div>
      </div>

      {/* Account Summary */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div className="p-6 border-b border-gray-100">
          <div className="flex items-center justify-between">
            <div>
              <h2 className="text-xl font-bold text-gray-900">{currentAccount?.name}</h2>
              <p className="text-gray-500">Account Code: {currentAccount?.code}</p>
            </div>
            <div className="text-right">
              <p className="text-sm text-gray-500">Current Balance</p>
              <p className="text-2xl font-bold text-blue-600">
                ৳{(ledgerData[ledgerData.length - 1]?.runningBalance || 0).toLocaleString()}
              </p>
            </div>
          </div>
        </div>

        {/* Ledger Table */}
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Voucher #</th>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
              <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
              <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
              <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
              <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {ledgerData.map((entry, index) => (
              <tr key={entry.id} className={index === 0 ? 'bg-gray-50' : 'hover:bg-gray-50'}>
                <td className="px-4 py-3 text-gray-600">{entry.date}</td>
                <td className="px-4 py-3 font-medium text-blue-600">{entry.voucherNumber}</td>
                <td className="px-4 py-3 text-gray-600">{entry.voucherType}</td>
                <td className="px-4 py-3 text-gray-900">{entry.description}</td>
                <td className="px-4 py-3 text-right text-green-600">
                  {entry.debit > 0 ? `৳${entry.debit.toLocaleString()}` : '-'}
                </td>
                <td className="px-4 py-3 text-right text-red-600">
                  {entry.credit > 0 ? `৳${entry.credit.toLocaleString()}` : '-'}
                </td>
                <td className="px-4 py-3 text-right font-medium text-gray-900">
                  ৳{entry.balance.toLocaleString()}
                </td>
              </tr>
            ))}
          </tbody>
          <tfoot className="bg-gray-50 font-medium">
            <tr>
              <td colSpan={4} className="px-4 py-3 text-right">Total:</td>
              <td className="px-4 py-3 text-right text-green-600">৳{totalDebit.toLocaleString()}</td>
              <td className="px-4 py-3 text-right text-red-600">৳{totalCredit.toLocaleString()}</td>
              <td className="px-4 py-3 text-right text-gray-900">
                ৳{(ledgerData[ledgerData.length - 1]?.runningBalance || 0).toLocaleString()}
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      {/* Quick Account List */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 className="font-semibold text-gray-900 mb-4">Quick Access</h3>
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
          {accounts.map(account => (
            <button
              key={account.code}
              onClick={() => setSelectedAccount(account.code)}
              className={`p-3 rounded-lg border text-left hover:shadow-sm transition-all ${
                selectedAccount === account.code
                  ? 'border-blue-500 bg-blue-50'
                  : 'border-gray-200 hover:border-gray-300'
              }`}
            >
              <p className="text-xs text-gray-500">{account.code}</p>
              <p className="text-sm font-medium text-gray-900 truncate">{account.name}</p>
            </button>
          ))}
        </div>
      </div>
    </div>
  );
};

export default Ledger;
