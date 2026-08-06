import React, { useState } from 'react';

interface JournalEntry {
  id: string;
  number: string;
  date: string;
  voucherType: string;
  description: string;
  totalAmount: number;
  status: string;
  createdBy: string;
  approvedBy?: string;
  postedBy?: string;
}

interface JournalItem {
  id: string;
  accountCode: string;
  accountName: string;
  debit: number;
  credit: number;
  description: string;
}

const journalEntries: JournalEntry[] = [
  { id: '1', number: 'JE-2026-001', date: '2026-01-15', voucherType: 'Receipt', description: 'Cash received from tuition fees', totalAmount: 50000, status: 'posted', createdBy: 'Admin', postedBy: 'Admin' },
  { id: '2', number: 'JE-2026-002', date: '2026-01-14', voucherType: 'Payment', description: 'Payment for electricity bill', totalAmount: 15000, status: 'posted', createdBy: 'Admin', postedBy: 'Admin' },
  { id: '3', number: 'JE-2026-003', date: '2026-01-13', voucherType: 'Journal', description: 'Purchase of office supplies', totalAmount: 8500, status: 'pending', createdBy: 'Accountant' },
  { id: '4', number: 'JE-2026-004', date: '2026-01-12', voucherType: 'Payment', description: 'Salary payment for January', totalAmount: 250000, status: 'posted', createdBy: 'Admin', postedBy: 'Admin' },
  { id: '5', number: 'JE-2026-005', date: '2026-01-11', voucherType: 'Journal', description: 'Library book purchase', totalAmount: 12000, status: 'approved', createdBy: 'Librarian', approvedBy: 'Manager' },
  { id: '6', number: 'JE-2026-006', date: '2026-01-10', voucherType: 'Contra', description: 'Cash deposited to bank', totalAmount: 100000, status: 'posted', createdBy: 'Admin', postedBy: 'Admin' },
];

const accounts = [
  { code: '1-1-1', name: 'Cash' },
  { code: '1-1-2', name: 'Bank - Islami Bank' },
  { code: '1-1-3', name: 'Accounts Receivable' },
  { code: '4-1', name: 'Tuition Fees Income' },
  { code: '4-2', name: 'Admission Fees' },
  { code: '5-1', name: 'Salary Expense' },
  { code: '5-2', name: 'Utilities Expense' },
];

const voucherTypes = ['Receipt', 'Payment', 'Journal', 'Contra', 'Opening', 'Adjustment'];
const statuses = ['draft', 'pending', 'approved', 'posted', 'rejected'];

const JournalEntryPage: React.FC = () => {
  const [showForm, setShowForm] = useState(false);
  const [selectedEntry, setSelectedEntry] = useState<JournalEntry | null>(null);
  const [filterStatus, setFilterStatus] = useState<string>('all');
  const [filterVoucher, setFilterVoucher] = useState<string>('all');
  const [searchTerm, setSearchTerm] = useState('');

  const [formData, setFormData] = useState({
    voucherType: 'Journal',
    date: new Date().toISOString().split('T')[0],
    description: '',
    reference: '',
    costCenter: '',
    items: [
      { id: '1', accountCode: '', accountName: '', debit: 0, credit: 0, description: '' },
      { id: '2', accountCode: '', accountName: '', debit: 0, credit: 0, description: '' },
    ] as JournalItem[],
  });

  const addItem = () => {
    setFormData({
      ...formData,
      items: [
        ...formData.items,
        { id: Date.now().toString(), accountCode: '', accountName: '', debit: 0, credit: 0, description: '' },
      ],
    });
  };

  const removeItem = (id: string) => {
    if (formData.items.length > 2) {
      setFormData({
        ...formData,
        items: formData.items.filter(item => item.id !== id),
      });
    }
  };

  const updateItem = (id: string, field: keyof JournalItem, value: string | number) => {
    setFormData({
      ...formData,
      items: formData.items.map(item =>
        item.id === id ? { ...item, [field]: value } : item
      ),
    });
  };

  const totalDebit = formData.items.reduce((sum, item) => sum + item.debit, 0);
  const totalCredit = formData.items.reduce((sum, item) => sum + item.credit, 0);
  const isBalanced = totalDebit === totalCredit;

  const handleSubmit = () => {
    if (!isBalanced) {
      alert('Journal entry must be balanced! Debit and Credit must be equal.');
      return;
    }
    alert('Journal entry created successfully!');
    setShowForm(false);
  };

  const filteredEntries = journalEntries.filter(entry => {
    const matchesStatus = filterStatus === 'all' || entry.status === filterStatus;
    const matchesVoucher = filterVoucher === 'all' || entry.voucherType === filterVoucher;
    const matchesSearch = entry.number.toLowerCase().includes(searchTerm.toLowerCase()) ||
                          entry.description.toLowerCase().includes(searchTerm.toLowerCase());
    return matchesStatus && matchesVoucher && matchesSearch;
  });

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'posted': return 'bg-green-100 text-green-800';
      case 'approved': return 'bg-blue-100 text-blue-800';
      case 'pending': return 'bg-yellow-100 text-yellow-800';
      case 'rejected': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Journal Entry</h1>
          <p className="text-gray-500">Create and manage journal entries</p>
        </div>
        <button
          onClick={() => setShowForm(true)}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          + New Journal Entry
        </button>
      </div>

      {/* Filters */}
      <div className="flex flex-wrap gap-4 bg-white p-4 rounded-lg border border-gray-100">
        <div className="flex-1 min-w-[200px]">
          <input
            type="text"
            placeholder="Search by number or description..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
        <select
          value={filterStatus}
          onChange={(e) => setFilterStatus(e.target.value)}
          className="px-4 py-2 border border-gray-300 rounded-lg"
        >
          <option value="all">All Status</option>
          {statuses.map(status => (
            <option key={status} value={status}>{status.charAt(0).toUpperCase() + status.slice(1)}</option>
          ))}
        </select>
        <select
          value={filterVoucher}
          onChange={(e) => setFilterVoucher(e.target.value)}
          className="px-4 py-2 border border-gray-300 rounded-lg"
        >
          <option value="all">All Vouchers</option>
          {voucherTypes.map(type => (
            <option key={type} value={type}>{type}</option>
          ))}
        </select>
      </div>

      {/* Journal Entries Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Journal #</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Voucher Type</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {filteredEntries.map(entry => (
              <tr key={entry.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 font-medium text-blue-600">{entry.number}</td>
                <td className="px-6 py-4 text-gray-600">{entry.date}</td>
                <td className="px-6 py-4 text-gray-600">{entry.voucherType}</td>
                <td className="px-6 py-4 text-gray-900">{entry.description}</td>
                <td className="px-6 py-4 text-right font-medium text-gray-900">
                  ৳{entry.totalAmount.toLocaleString()}
                </td>
                <td className="px-6 py-4 text-center">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(entry.status)}`}>
                    {entry.status}
                  </span>
                </td>
                <td className="px-6 py-4 text-center">
                  <button
                    onClick={() => setSelectedEntry(entry)}
                    className="text-blue-600 hover:text-blue-800 mr-2"
                  >
                    View
                  </button>
                  {entry.status === 'pending' && (
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

      {/* Journal Entry Form Modal */}
      {showForm && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
            <div className="p-6 border-b border-gray-100 flex items-center justify-between">
              <h2 className="text-xl font-bold text-gray-900">New Journal Entry</h2>
              <button onClick={() => setShowForm(false)} className="text-gray-400 hover:text-gray-600">
                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div className="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
              <div className="grid grid-cols-2 gap-4 mb-6">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Voucher Type</label>
                  <select
                    value={formData.voucherType}
                    onChange={(e) => setFormData({ ...formData, voucherType: e.target.value })}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                  >
                    {voucherTypes.map(type => (
                      <option key={type} value={type}>{type}</option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Transaction Date</label>
                  <input
                    type="date"
                    value={formData.date}
                    onChange={(e) => setFormData({ ...formData, date: e.target.value })}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                  />
                </div>
                <div className="col-span-2">
                  <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                  <input
                    type="text"
                    value={formData.description}
                    onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                    placeholder="Enter journal description"
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
                  <input
                    type="text"
                    value={formData.reference}
                    onChange={(e) => setFormData({ ...formData, reference: e.target.value })}
                    placeholder="Optional reference"
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Cost Center</label>
                  <select
                    value={formData.costCenter}
                    onChange={(e) => setFormData({ ...formData, costCenter: e.target.value })}
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                  >
                    <option value="">Select Cost Center</option>
                    <option value="academic">Academic</option>
                    <option value="admin">Administration</option>
                    <option value="library">Library</option>
                    <option value="transport">Transport</option>
                  </select>
                </div>
              </div>

              {/* Journal Items */}
              <div className="mb-6">
                <div className="flex items-center justify-between mb-4">
                  <h3 className="font-semibold text-gray-900">Journal Entries</h3>
                  <button
                    onClick={addItem}
                    className="text-sm text-blue-600 hover:text-blue-800"
                  >
                    + Add Line
                  </button>
                </div>
                <table className="w-full border border-gray-200 rounded-lg overflow-hidden">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="px-4 py-2 text-left text-xs font-medium text-gray-500">Account</th>
                      <th className="px-4 py-2 text-right text-xs font-medium text-gray-500">Debit</th>
                      <th className="px-4 py-2 text-right text-xs font-medium text-gray-500">Credit</th>
                      <th className="px-4 py-2 text-left text-xs font-medium text-gray-500">Description</th>
                      <th className="px-4 py-2 w-12"></th>
                    </tr>
                  </thead>
                  <tbody>
                    {formData.items.map(item => (
                      <tr key={item.id} className="border-t border-gray-100">
                        <td className="px-4 py-2">
                          <select
                            value={item.accountCode}
                            onChange={(e) => {
                              const account = accounts.find(a => a.code === e.target.value);
                              updateItem(item.id, 'accountCode', e.target.value);
                              updateItem(item.id, 'accountName', account?.name || '');
                            }}
                            className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                          >
                            <option value="">Select Account</option>
                            {accounts.map(acc => (
                              <option key={acc.code} value={acc.code}>{acc.code} - {acc.name}</option>
                            ))}
                          </select>
                        </td>
                        <td className="px-4 py-2">
                          <input
                            type="number"
                            value={item.debit || ''}
                            onChange={(e) => {
                              updateItem(item.id, 'debit', parseFloat(e.target.value) || 0);
                              if (parseFloat(e.target.value)) updateItem(item.id, 'credit', 0);
                            }}
                            className="w-full px-2 py-1 border border-gray-300 rounded text-right text-sm"
                            placeholder="0.00"
                          />
                        </td>
                        <td className="px-4 py-2">
                          <input
                            type="number"
                            value={item.credit || ''}
                            onChange={(e) => {
                              updateItem(item.id, 'credit', parseFloat(e.target.value) || 0);
                              if (parseFloat(e.target.value)) updateItem(item.id, 'debit', 0);
                            }}
                            className="w-full px-2 py-1 border border-gray-300 rounded text-right text-sm"
                            placeholder="0.00"
                          />
                        </td>
                        <td className="px-4 py-2">
                          <input
                            type="text"
                            value={item.description}
                            onChange={(e) => updateItem(item.id, 'description', e.target.value)}
                            className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                            placeholder="Optional"
                          />
                        </td>
                        <td className="px-4 py-2 text-center">
                          <button
                            onClick={() => removeItem(item.id)}
                            className="text-red-500 hover:text-red-700"
                          >
                            ×
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                  <tfoot className="bg-gray-50 font-medium">
                    <tr>
                      <td className="px-4 py-2 text-right">Total:</td>
                      <td className="px-4 py-2 text-right text-green-600">৳{totalDebit.toLocaleString()}</td>
                      <td className="px-4 py-2 text-right text-red-600">৳{totalCredit.toLocaleString()}</td>
                      <td colSpan={2}></td>
                    </tr>
                  </tfoot>
                </table>
                {!isBalanced && (
                  <p className="text-red-600 text-sm mt-2">⚠️ Journal is not balanced! Debit (৳{totalDebit}) ≠ Credit (৳{totalCredit})</p>
                )}
              </div>
            </div>
            <div className="p-6 border-t border-gray-100 flex justify-end gap-3">
              <button
                onClick={() => setShowForm(false)}
                className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                onClick={handleSubmit}
                disabled={!isBalanced}
                className={`px-4 py-2 rounded-lg ${
                  isBalanced ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-300 text-gray-500 cursor-not-allowed'
                }`}
              >
                Save Journal Entry
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default JournalEntryPage;
