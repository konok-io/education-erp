import React, { useState } from 'react';

interface Account {
  id: string;
  code: string;
  name: string;
  type: string;
  group: string;
  nature: string;
  balance: number;
  parent?: string;
  children?: Account[];
  isExpanded?: boolean;
}

const accounts: Account[] = [
  {
    id: '1',
    code: '1',
    name: 'Assets',
    type: 'asset',
    group: 'Assets',
    nature: 'Debit',
    balance: 8500000,
    isExpanded: true,
    children: [
      {
        id: '1-1',
        code: '1-1',
        name: 'Current Assets',
        type: 'asset',
        group: 'Current Assets',
        nature: 'Debit',
        balance: 4500000,
        parent: '1',
        children: [
          { id: '1-1-1', code: '1-1-1', name: 'Cash', type: 'asset', group: 'Cash', nature: 'Debit', balance: 2450000, parent: '1-1' },
          { id: '1-1-2', code: '1-1-2', name: 'Bank Accounts', type: 'asset', group: 'Bank', nature: 'Debit', balance: 5680000, parent: '1-1' },
          { id: '1-1-3', code: '1-1-3', name: 'Accounts Receivable', type: 'asset', group: 'Receivable', nature: 'Debit', balance: 1250000, parent: '1-1' },
        ],
      },
      {
        id: '1-2',
        code: '1-2',
        name: 'Fixed Assets',
        type: 'asset',
        group: 'Fixed Assets',
        nature: 'Debit',
        balance: 4000000,
        parent: '1',
        children: [
          { id: '1-2-1', code: '1-2-1', name: 'Land & Building', type: 'asset', group: 'Fixed Assets', nature: 'Debit', balance: 2500000, parent: '1-2' },
          { id: '1-2-2', code: '1-2-2', name: 'Furniture & Fixtures', type: 'asset', group: 'Fixed Assets', nature: 'Debit', balance: 850000, parent: '1-2' },
          { id: '1-2-3', code: '1-2-3', name: 'Equipment', type: 'asset', group: 'Fixed Assets', nature: 'Debit', balance: 650000, parent: '1-2' },
        ],
      },
    ],
  },
  {
    id: '2',
    code: '2',
    name: 'Liabilities',
    type: 'liability',
    group: 'Liabilities',
    nature: 'Credit',
    balance: 3200000,
    isExpanded: true,
    children: [
      {
        id: '2-1',
        code: '2-1',
        name: 'Current Liabilities',
        type: 'liability',
        group: 'Current Liability',
        nature: 'Credit',
        balance: 1800000,
        parent: '2',
        children: [
          { id: '2-1-1', code: '2-1-1', name: 'Accounts Payable', type: 'liability', group: 'Payable', nature: 'Credit', balance: 890000, parent: '2-1' },
          { id: '2-1-2', code: '2-1-2', name: 'Tax Payable', type: 'liability', group: 'Tax Payable', nature: 'Credit', balance: 450000, parent: '2-1' },
          { id: '2-1-3', code: '2-1-3', name: 'Security Deposits', type: 'liability', group: 'Current Liability', nature: 'Credit', balance: 460000, parent: '2-1' },
        ],
      },
      {
        id: '2-2',
        code: '2-2',
        name: 'Long Term Liabilities',
        type: 'liability',
        group: 'Long Term Liability',
        nature: 'Credit',
        balance: 1400000,
        parent: '2',
        children: [
          { id: '2-2-1', code: '2-2-1', name: 'Bank Loans', type: 'liability', group: 'Loans', nature: 'Credit', balance: 1400000, parent: '2-2' },
        ],
      },
    ],
  },
  {
    id: '3',
    code: '3',
    name: 'Equity',
    type: 'equity',
    group: 'Equity',
    nature: 'Credit',
    balance: 5300000,
    isExpanded: true,
    children: [
      { id: '3-1', code: '3-1', name: 'Capital Fund', type: 'equity', group: 'Capital', nature: 'Credit', balance: 3500000, parent: '3' },
      { id: '3-2', code: '3-2', name: 'Retained Earnings', type: 'equity', group: 'Retained Earnings', nature: 'Credit', balance: 1800000, parent: '3' },
    ],
  },
  {
    id: '4',
    code: '4',
    name: 'Income',
    type: 'income',
    group: 'Income',
    nature: 'Credit',
    balance: 6500000,
    isExpanded: true,
    children: [
      { id: '4-1', code: '4-1', name: 'Tuition Fees', type: 'income', group: 'Tuition Fee', nature: 'Credit', balance: 3500000, parent: '4' },
      { id: '4-2', code: '4-2', name: 'Admission Fees', type: 'income', group: 'Admission Fee', nature: 'Credit', balance: 1200000, parent: '4' },
      { id: '4-3', code: '4-3', name: 'Exam Fees', type: 'income', group: 'Exam Fee', nature: 'Credit', balance: 800000, parent: '4' },
      { id: '4-4', code: '4-4', name: 'Miscellaneous Income', type: 'income', group: 'Misc Income', nature: 'Credit', balance: 1000000, parent: '4' },
    ],
  },
  {
    id: '5',
    code: '5',
    name: 'Expenses',
    type: 'expense',
    group: 'Expenses',
    nature: 'Debit',
    balance: 4200000,
    isExpanded: true,
    children: [
      { id: '5-1', code: '5-1', name: 'Salary & Allowances', type: 'expense', group: 'Salary', nature: 'Debit', balance: 2500000, parent: '5' },
      { id: '5-2', code: '5-2', name: 'Utilities', type: 'expense', group: 'Utilities', nature: 'Debit', balance: 450000, parent: '5' },
      { id: '5-3', code: '5-3', name: 'Maintenance', type: 'expense', group: 'Maintenance', nature: 'Debit', balance: 350000, parent: '5' },
      { id: '5-4', code: '5-4', name: 'Educational Resources', type: 'expense', group: 'Academic', nature: 'Debit', balance: 600000, parent: '5' },
      { id: '5-5', code: '5-5', name: 'Miscellaneous Expenses', type: 'expense', group: 'Misc Expense', nature: 'Debit', balance: 300000, parent: '5' },
    ],
  },
];

const accountTypes = [
  { value: 'asset', label: 'Assets', color: 'bg-blue-500' },
  { value: 'liability', label: 'Liabilities', color: 'bg-red-500' },
  { value: 'equity', label: 'Equity', color: 'bg-purple-500' },
  { value: 'income', label: 'Income', color: 'bg-green-500' },
  { value: 'expense', label: 'Expenses', color: 'bg-orange-500' },
];

const ChartOfAccounts: React.FC = () => {
  const [expandedAccounts, setExpandedAccounts] = useState<Set<string>>(new Set(['1', '2', '3', '4', '5']));
  const [selectedType, setSelectedType] = useState<string>('all');
  const [searchTerm, setSearchTerm] = useState('');

  const toggleExpand = (id: string) => {
    const newExpanded = new Set(expandedAccounts);
    if (newExpanded.has(id)) {
      newExpanded.delete(id);
    } else {
      newExpanded.add(id);
    }
    setExpandedAccounts(newExpanded);
  };

  const renderAccount = (account: Account, level: number = 0) => {
    const hasChildren = account.children && account.children.length > 0;
    const isExpanded = expandedAccounts.has(account.id);

    return (
      <div key={account.id}>
        <div
          className={`flex items-center justify-between p-3 hover:bg-gray-50 cursor-pointer ${
            level > 0 ? 'border-l border-gray-200' : ''
          }`}
          style={{ paddingLeft: `${level * 24 + 12}px` }}
          onClick={() => hasChildren && toggleExpand(account.id)}
        >
          <div className="flex items-center gap-3">
            {hasChildren && (
              <svg
                className={`w-4 h-4 text-gray-400 transition-transform ${isExpanded ? 'rotate-90' : ''}`}
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
              </svg>
            )}
            {!hasChildren && <div className="w-4" />}
            <div className={`w-3 h-3 rounded-full ${
              account.type === 'asset' ? 'bg-blue-500' :
              account.type === 'liability' ? 'bg-red-500' :
              account.type === 'equity' ? 'bg-purple-500' :
              account.type === 'income' ? 'bg-green-500' : 'bg-orange-500'
            }`} />
            <div>
              <p className="font-medium text-gray-900">{account.name}</p>
              <p className="text-sm text-gray-500">{account.code}</p>
            </div>
          </div>
          <div className="text-right">
            <p className={`font-medium ${
              account.nature === 'Debit' ? 'text-red-600' : 'text-green-600'
            }`}>
              ৳{account.balance.toLocaleString()}
            </p>
          </div>
        </div>
        {hasChildren && isExpanded && (
          <div>
            {account.children!.map(child => renderAccount(child, level + 1))}
          </div>
        )}
      </div>
    );
  };

  const filteredAccounts = accounts.filter(account => {
    const matchesType = selectedType === 'all' || account.type === selectedType;
    const matchesSearch = account.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                          account.code.includes(searchTerm);
    return matchesType && matchesSearch;
  });

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Chart of Accounts</h1>
          <p className="text-gray-500">Manage your organization's account structure</p>
        </div>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          + Add Account
        </button>
      </div>

      {/* Filters */}
      <div className="flex flex-wrap gap-4">
        <div className="flex-1 min-w-[200px]">
          <input
            type="text"
            placeholder="Search accounts..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
        <div className="flex gap-2">
          <button
            onClick={() => setSelectedType('all')}
            className={`px-4 py-2 rounded-lg ${
              selectedType === 'all' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            }`}
          >
            All
          </button>
          {accountTypes.map(type => (
            <button
              key={type.value}
              onClick={() => setSelectedType(type.value)}
              className={`px-4 py-2 rounded-lg ${
                selectedType === type.value ? `${type.color} text-white` : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              }`}
            >
              {type.label}
            </button>
          ))}
        </div>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
        {accountTypes.map(type => {
          const typeAccounts = accounts.filter(a => a.type === type.value);
          const total = typeAccounts.reduce((sum, a) => sum + a.balance, 0);
          return (
            <div key={type.value} className="bg-white rounded-lg p-4 border border-gray-100">
              <div className="flex items-center gap-2 mb-2">
                <div className={`w-3 h-3 rounded-full ${type.color}`} />
                <span className="text-sm text-gray-500">{type.label}</span>
              </div>
              <p className="text-xl font-bold text-gray-900">৳{total.toLocaleString()}</p>
              <p className="text-xs text-gray-500">{typeAccounts.length} accounts</p>
            </div>
          );
        })}
      </div>

      {/* Account List */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100">
        <div className="p-4 border-b border-gray-100">
          <div className="flex items-center justify-between">
            <h3 className="font-semibold text-gray-900">Account Hierarchy</h3>
            <button className="text-blue-600 hover:text-blue-700 text-sm">Expand All</button>
          </div>
        </div>
        <div className="divide-y divide-gray-100">
          {filteredAccounts.map(account => renderAccount(account))}
        </div>
      </div>
    </div>
  );
};

export default ChartOfAccounts;
