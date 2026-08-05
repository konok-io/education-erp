import React, { useState } from 'react';

interface Bonus {
  id: string;
  bonusCode: string;
  employeeCode: string;
  employeeName: string;
  bonusType: string;
  bonusName: string;
  amount: number;
  percentage?: number;
  month?: string;
  year: number;
  effectiveDate: string;
  status: string;
}

const bonuses: Bonus[] = [
  { id: '1', bonusCode: 'BON-2026-001', employeeCode: 'EMP-002', employeeName: 'Fatema Begum', bonusType: 'performance', bonusName: 'Performance Bonus Q4', amount: 20000, percentage: 10, month: 'December', year: 2025, effectiveDate: '2026-01-15', status: 'approved' },
  { id: '2', bonusCode: 'BON-2026-002', employeeCode: 'EMP-003', employeeName: 'Kamal Hossain', bonusType: 'performance', bonusName: 'Project Completion Bonus', amount: 15000, effectiveDate: '2026-01-10', status: 'approved' },
  { id: '3', bonusCode: 'BON-2026-003', employeeCode: 'EMP-001', employeeName: 'Rahim Ahmed', bonusType: 'festival', bonusName: 'Eid Bonus', amount: 200000, percentage: 100, month: 'December', year: 2025, effectiveDate: '2026-01-05', status: 'paid' },
  { id: '4', bonusCode: 'BON-2026-004', employeeCode: 'EMP-004', employeeName: 'Jamal Uddin', bonusType: 'festival', bonusName: 'Eid Bonus', amount: 50000, percentage: 100, month: 'December', year: 2025, effectiveDate: '2026-01-05', status: 'paid' },
  { id: '5', bonusCode: 'BON-2026-005', employeeCode: 'EMP-005', employeeName: 'Sana Islam', bonusType: 'special', bonusName: 'Special Recognition', amount: 10000, effectiveDate: '2026-02-01', status: 'pending' },
  { id: '6', bonusCode: 'BON-2026-006', employeeCode: 'EMP-006', employeeName: 'Tariq Rahman', bonusType: 'research', bonusName: 'Research Publication', amount: 25000, effectiveDate: '2026-01-20', status: 'approved' },
];

const bonusTypes = [
  { value: 'all', label: 'All Types' },
  { value: 'festival', label: 'Festival Bonus' },
  { value: 'performance', label: 'Performance Bonus' },
  { value: 'special', label: 'Special Bonus' },
  { value: 'research', label: 'Research Bonus' },
  { value: 'project', label: 'Project Bonus' },
];

const BonusManagement: React.FC = () => {
  const [showForm, setShowForm] = useState(false);
  const [filterType, setFilterType] = useState<string>('all');
  const [filterStatus, setFilterStatus] = useState<string>('all');
  const [selectedBonus, setSelectedBonus] = useState<Bonus | null>(null);

  const filteredBonuses = bonuses.filter(bonus => {
    const matchesType = filterType === 'all' || bonus.bonusType === filterType;
    const matchesStatus = filterStatus === 'all' || bonus.status === filterStatus;
    return matchesType && matchesStatus;
  });

  const totalBonus = bonuses.reduce((sum, b) => sum + b.amount, 0);
  const pendingBonus = bonuses.filter(b => b.status === 'pending').reduce((sum, b) => sum + b.amount, 0);
  const paidBonus = bonuses.filter(b => b.status === 'paid').reduce((sum, b) => sum + b.amount, 0);

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'approved': return 'bg-green-100 text-green-800';
      case 'pending': return 'bg-yellow-100 text-yellow-800';
      case 'paid': return 'bg-blue-100 text-blue-800';
      case 'rejected': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const getTypeColor = (type: string) => {
    switch (type) {
      case 'festival': return 'bg-purple-100 text-purple-800';
      case 'performance': return 'bg-blue-100 text-blue-800';
      case 'special': return 'bg-orange-100 text-orange-800';
      case 'research': return 'bg-green-100 text-green-800';
      case 'project': return 'bg-teal-100 text-teal-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Bonus Management</h1>
          <p className="text-gray-500">Manage employee bonuses and incentives</p>
        </div>
        <button
          onClick={() => setShowForm(true)}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          + Add Bonus
        </button>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Bonuses</p>
          <p className="text-2xl font-bold text-blue-600">৳{(totalBonus / 100000).toFixed(1)}L</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Pending</p>
          <p className="text-2xl font-bold text-yellow-600">৳{(pendingBonus / 1000).toFixed(0)}K</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Approved</p>
          <p className="text-2xl font-bold text-green-600">৳{(totalBonus - pendingBonus - paidBonus) / 1000}K</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Paid</p>
          <p className="text-2xl font-bold text-purple-600">৳{(paidBonus / 100000).toFixed(1)}L</p>
        </div>
      </div>

      {/* Filters */}
      <div className="bg-white p-4 rounded-lg border border-gray-100 flex flex-wrap gap-4">
        <select
          value={filterType}
          onChange={(e) => setFilterType(e.target.value)}
          className="px-4 py-2 border border-gray-300 rounded-lg"
        >
          {bonusTypes.map(type => (
            <option key={type.value} value={type.value}>{type.label}</option>
          ))}
        </select>
        <select
          value={filterStatus}
          onChange={(e) => setFilterStatus(e.target.value)}
          className="px-4 py-2 border border-gray-300 rounded-lg"
        >
          <option value="all">All Status</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="paid">Paid</option>
        </select>
      </div>

      {/* Bonuses Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bonus Code</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {filteredBonuses.map((bonus) => (
              <tr key={bonus.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 font-medium text-blue-600">{bonus.bonusCode}</td>
                <td className="px-6 py-4">
                  <p className="font-medium text-gray-900">{bonus.employeeName}</p>
                  <p className="text-sm text-gray-500">{bonus.employeeCode}</p>
                </td>
                <td className="px-6 py-4">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${getTypeColor(bonus.bonusType)}`}>
                    {bonus.bonusType}
                  </span>
                </td>
                <td className="px-6 py-4 text-gray-600">{bonus.bonusName}</td>
                <td className="px-6 py-4 text-right font-medium text-gray-900">৳{bonus.amount.toLocaleString()}</td>
                <td className="px-6 py-4 text-gray-600">{bonus.effectiveDate}</td>
                <td className="px-6 py-4 text-center">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(bonus.status)}`}>
                    {bonus.status}
                  </span>
                </td>
                <td className="px-6 py-4 text-center">
                  <button
                    onClick={() => setSelectedBonus(bonus)}
                    className="text-blue-600 hover:text-blue-800 mr-2"
                  >
                    View
                  </button>
                  {bonus.status === 'pending' && (
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

      {/* Add Bonus Modal */}
      {showForm && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg">
            <div className="p-6 border-b border-gray-100">
              <h2 className="text-xl font-bold text-gray-900">Add Bonus</h2>
            </div>
            <div className="p-6 space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                <select className="w-full px-4 py-2 border border-gray-300 rounded-lg">
                  <option value="">Select Employee</option>
                  <option value="EMP-001">Rahim Ahmed (EMP-001)</option>
                  <option value="EMP-002">Fatema Begum (EMP-002)</option>
                  <option value="EMP-003">Kamal Hossain (EMP-003)</option>
                </select>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Bonus Type</label>
                  <select className="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="festival">Festival Bonus</option>
                    <option value="performance">Performance Bonus</option>
                    <option value="special">Special Bonus</option>
                    <option value="research">Research Bonus</option>
                    <option value="project">Project Bonus</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                  <input type="number" className="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="0" />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <input type="text" className="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Bonus description" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Effective Date</label>
                <input type="date" className="w-full px-4 py-2 border border-gray-300 rounded-lg" />
              </div>
            </div>
            <div className="p-6 border-t border-gray-100 flex justify-end gap-3">
              <button onClick={() => setShowForm(false)} className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
              </button>
              <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Submit for Approval
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default BonusManagement;
