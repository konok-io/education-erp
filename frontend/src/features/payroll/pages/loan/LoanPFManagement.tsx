import React, { useState } from 'react';

interface Loan {
  id: string;
  loanCode: string;
  employeeCode: string;
  employeeName: string;
  loanType: string;
  loanName: string;
  principalAmount: number;
  interestRate: number;
  totalAmount: number;
  monthlyInstallment: number;
  paidAmount: number;
  remainingAmount: number;
  paidInstallments: number;
  totalInstallments: number;
  startDate: string;
  status: string;
}

interface PFAccount {
  id: string;
  pfNumber: string;
  employeeCode: string;
  employeeName: string;
  joiningDate: string;
  employeeContribution: number;
  employerContribution: number;
  totalBalance: number;
  interestEarned: number;
  status: string;
}

const loans: Loan[] = [
  { id: '1', loanCode: 'LON-2026-001', employeeCode: 'EMP-001', employeeName: 'Rahim Ahmed', loanType: 'personal', loanName: 'Personal Loan', principalAmount: 500000, interestRate: 10, totalAmount: 550000, monthlyInstallment: 45833, paidAmount: 183332, remainingAmount: 366668, paidInstallments: 4, totalInstallments: 12, startDate: '2025-10-01', status: 'active' },
  { id: '2', loanCode: 'LON-2026-002', employeeCode: 'EMP-002', employeeName: 'Fatema Begum', loanType: 'car', loanName: 'Car Loan', principalAmount: 1000000, interestRate: 8, totalAmount: 1080000, monthlyInstallment: 90000, paidAmount: 540000, remainingAmount: 540000, paidInstallments: 6, totalInstallments: 12, startDate: '2025-07-01', status: 'active' },
  { id: '3', loanCode: 'LON-2026-003', employeeCode: 'EMP-003', employeeName: 'Kamal Hossain', loanType: 'advance', loanName: 'Salary Advance', principalAmount: 100000, interestRate: 0, totalAmount: 100000, monthlyInstallment: 16667, paidAmount: 83335, remainingAmount: 16665, paidInstallments: 5, totalInstallments: 6, startDate: '2025-08-01', status: 'active' },
  { id: '4', loanCode: 'LON-2025-015', employeeCode: 'EMP-004', employeeName: 'Jamal Uddin', loanType: 'personal', loanName: 'Personal Loan', principalAmount: 200000, interestRate: 10, totalAmount: 220000, monthlyInstallment: 18333, paidAmount: 220000, remainingAmount: 0, paidInstallments: 12, totalInstallments: 12, startDate: '2024-12-01', status: 'closed' },
];

const pfAccounts: PFAccount[] = [
  { id: '1', pfNumber: 'PF-001', employeeCode: 'EMP-001', employeeName: 'Rahim Ahmed', joiningDate: '2020-01-15', employeeContribution: 600000, employerContribution: 600000, totalBalance: 1500000, interestEarned: 300000, status: 'active' },
  { id: '2', pfNumber: 'PF-002', employeeCode: 'EMP-002', employeeName: 'Fatema Begum', joiningDate: '2021-03-01', employeeContribution: 450000, employerContribution: 450000, totalBalance: 1050000, interestEarned: 150000, status: 'active' },
  { id: '3', pfNumber: 'PF-003', employeeCode: 'EMP-003', employeeName: 'Kamal Hossain', joiningDate: '2022-06-15', employeeContribution: 250000, employerContribution: 250000, totalBalance: 550000, interestEarned: 50000, status: 'active' },
  { id: '4', pfNumber: 'PF-004', employeeCode: 'EMP-004', employeeName: 'Jamal Uddin', joiningDate: '2023-01-10', employeeContribution: 150000, employerContribution: 150000, totalBalance: 320000, interestEarned: 20000, status: 'active' },
];

const LoanPFManagement: React.FC = () => {
  const [activeTab, setActiveTab] = useState<'loans' | 'pf'>('loans');
  const [showLoanForm, setShowLoanForm] = useState(false);

  const totalLoanDisbursed = loans.filter(l => l.status !== 'cancelled').reduce((sum, l) => sum + l.principalAmount, 0);
  const totalRemaining = loans.filter(l => l.status === 'active').reduce((sum, l) => sum + l.remainingAmount, 0);
  const totalPF = pfAccounts.reduce((sum, p) => sum + p.totalBalance, 0);

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active': return 'bg-green-100 text-green-800';
      case 'pending': return 'bg-yellow-100 text-yellow-800';
      case 'closed': return 'bg-blue-100 text-blue-800';
      case 'cancelled': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const getLoanTypeColor = (type: string) => {
    switch (type) {
      case 'personal': return 'bg-purple-100 text-purple-800';
      case 'car': return 'bg-blue-100 text-blue-800';
      case 'home': return 'bg-green-100 text-green-800';
      case 'advance': return 'bg-orange-100 text-orange-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Loan & PF Management</h1>
          <p className="text-gray-500">Manage employee loans and provident fund</p>
        </div>
        <button
          onClick={() => setShowLoanForm(true)}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          + New Loan
        </button>
      </div>

      {/* Tabs */}
      <div className="flex border-b border-gray-200">
        <button
          onClick={() => setActiveTab('loans')}
          className={`px-6 py-3 font-medium ${activeTab === 'loans' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'}`}
        >
          Employee Loans
        </button>
        <button
          onClick={() => setActiveTab('pf')}
          className={`px-6 py-3 font-medium ${activeTab === 'pf' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'}`}
        >
          Provident Fund
        </button>
      </div>

      {/* Loans Tab */}
      {activeTab === 'loans' && (
        <>
          {/* Summary Cards */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div className="bg-white p-4 rounded-lg border border-gray-100">
              <p className="text-sm text-gray-500">Total Loans</p>
              <p className="text-2xl font-bold text-blue-600">{loans.filter(l => l.status === 'active').length}</p>
            </div>
            <div className="bg-white p-4 rounded-lg border border-gray-100">
              <p className="text-sm text-gray-500">Total Disbursed</p>
              <p className="text-2xl font-bold text-purple-600">৳{(totalLoanDisbursed / 100000).toFixed(1)}L</p>
            </div>
            <div className="bg-white p-4 rounded-lg border border-gray-100">
              <p className="text-sm text-gray-500">Remaining</p>
              <p className="text-2xl font-bold text-orange-600">৳{(totalRemaining / 100000).toFixed(1)}L</p>
            </div>
            <div className="bg-white p-4 rounded-lg border border-gray-100">
              <p className="text-sm text-gray-500">Monthly Recovery</p>
              <p className="text-2xl font-bold text-green-600">৳{(loans.filter(l => l.status === 'active').reduce((sum, l) => sum + l.monthlyInstallment, 0) / 1000).toFixed(0)}K</p>
            </div>
          </div>

          {/* Loans Table */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table className="w-full">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loan Code</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Principal</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monthly</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Paid</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Remaining</th>
                  <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Progress</th>
                  <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {loans.map((loan) => {
                  const progress = (loan.paidInstallments / loan.totalInstallments) * 100;
                  return (
                    <tr key={loan.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 font-medium text-blue-600">{loan.loanCode}</td>
                      <td className="px-6 py-4">
                        <p className="font-medium text-gray-900">{loan.employeeName}</p>
                        <p className="text-sm text-gray-500">{loan.employeeCode}</p>
                      </td>
                      <td className="px-6 py-4">
                        <span className={`px-2 py-1 text-xs font-medium rounded-full ${getLoanTypeColor(loan.loanType)}`}>
                          {loan.loanName}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-right text-gray-900">৳{loan.principalAmount.toLocaleString()}</td>
                      <td className="px-6 py-4 text-right text-gray-900">৳{loan.monthlyInstallment.toLocaleString()}</td>
                      <td className="px-6 py-4 text-right text-green-600">৳{loan.paidAmount.toLocaleString()}</td>
                      <td className="px-6 py-4 text-right text-orange-600">৳{loan.remainingAmount.toLocaleString()}</td>
                      <td className="px-6 py-4">
                        <div className="flex items-center gap-2">
                          <div className="w-20 bg-gray-200 rounded-full h-2">
                            <div className="bg-green-500 h-2 rounded-full" style={{ width: `${progress}%` }} />
                          </div>
                          <span className="text-xs text-gray-500">{loan.paidInstallments}/{loan.totalInstallments}</span>
                        </div>
                      </td>
                      <td className="px-6 py-4 text-center">
                        <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(loan.status)}`}>
                          {loan.status}
                        </span>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        </>
      )}

      {/* PF Tab */}
      {activeTab === 'pf' && (
        <>
          {/* Summary Cards */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div className="bg-white p-4 rounded-lg border border-gray-100">
              <p className="text-sm text-gray-500">Total Members</p>
              <p className="text-2xl font-bold text-blue-600">{pfAccounts.length}</p>
            </div>
            <div className="bg-white p-4 rounded-lg border border-gray-100">
              <p className="text-sm text-gray-500">Total Balance</p>
              <p className="text-2xl font-bold text-purple-600">৳{(totalPF / 100000).toFixed(1)}L</p>
            </div>
            <div className="bg-white p-4 rounded-lg border border-gray-100">
              <p className="text-sm text-gray-500">Employee Contribution</p>
              <p className="text-2xl font-bold text-green-600">৳{(pfAccounts.reduce((sum, p) => sum + p.employeeContribution, 0) / 100000).toFixed(1)}L</p>
            </div>
            <div className="bg-white p-4 rounded-lg border border-gray-100">
              <p className="text-sm text-gray-500">Interest Earned</p>
              <p className="text-2xl font-bold text-orange-600">৳{(pfAccounts.reduce((sum, p) => sum + p.interestEarned, 0) / 100000).toFixed(1)}L</p>
            </div>
          </div>

          {/* PF Table */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table className="w-full">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PF Number</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joining</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Employee Cont.</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Employer Cont.</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Interest</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Balance</th>
                  <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {pfAccounts.map((pf) => (
                  <tr key={pf.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 font-medium text-blue-600">{pf.pfNumber}</td>
                    <td className="px-6 py-4">
                      <p className="font-medium text-gray-900">{pf.employeeName}</p>
                      <p className="text-sm text-gray-500">{pf.employeeCode}</p>
                    </td>
                    <td className="px-6 py-4 text-gray-600">{pf.joiningDate}</td>
                    <td className="px-6 py-4 text-right text-gray-900">৳{pf.employeeContribution.toLocaleString()}</td>
                    <td className="px-6 py-4 text-right text-green-600">৳{pf.employerContribution.toLocaleString()}</td>
                    <td className="px-6 py-4 text-right text-orange-600">৳{pf.interestEarned.toLocaleString()}</td>
                    <td className="px-6 py-4 text-right font-bold text-blue-600">৳{pf.totalBalance.toLocaleString()}</td>
                    <td className="px-6 py-4 text-center">
                      <button className="text-blue-600 hover:text-blue-800 mr-2">Statement</button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </>
      )}

      {/* Add Loan Modal */}
      {showLoanForm && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg">
            <div className="p-6 border-b border-gray-100">
              <h2 className="text-xl font-bold text-gray-900">New Loan Application</h2>
            </div>
            <div className="p-6 space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                <select className="w-full px-4 py-2 border border-gray-300 rounded-lg">
                  <option value="">Select Employee</option>
                  <option value="EMP-001">Rahim Ahmed (EMP-001)</option>
                  <option value="EMP-002">Fatema Begum (EMP-002)</option>
                </select>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Loan Type</label>
                  <select className="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="personal">Personal Loan</option>
                    <option value="car">Car Loan</option>
                    <option value="home">Home Loan</option>
                    <option value="advance">Salary Advance</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                  <input type="number" className="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="0" />
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Interest Rate (%)</label>
                  <input type="number" defaultValue={10} className="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Tenure (Months)</label>
                  <input type="number" defaultValue={12} className="w-full px-4 py-2 border border-gray-300 rounded-lg" />
                </div>
              </div>
            </div>
            <div className="p-6 border-t border-gray-100 flex justify-end gap-3">
              <button onClick={() => setShowLoanForm(false)} className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
              </button>
              <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Submit Application
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default LoanPFManagement;
