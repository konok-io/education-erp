import React, { useState } from 'react';

interface SalarySlip {
  id: string;
  slipNumber: string;
  employeeCode: string;
  employeeName: string;
  designation: string;
  department: string;
  payrollMonth: string;
  payrollYear: number;
  basicSalary: number;
  grossSalary: number;
  houseRent: number;
  medicalAllowance: number;
  transportAllowance: number;
  otherAllowance: number;
  totalAllowance: number;
  overtimeAmount: number;
  bonusAmount: number;
  totalEarning: number;
  incomeTax: number;
  pfDeduction: number;
  loanDeduction: number;
  absentDeduction: number;
  otherDeduction: number;
  totalDeduction: number;
  netSalary: number;
  paymentDate: string;
  bankAccount: string;
  status: string;
}

const salarySlips: SalarySlip[] = [
  { id: '1', slipNumber: 'SS-2026-01-001', employeeCode: 'EMP-001', employeeName: 'Rahim Ahmed', designation: 'Professor', department: 'Academic', payrollMonth: 'January', payrollYear: 2026, basicSalary: 200000, grossSalary: 320000, houseRent: 50000, medicalAllowance: 20000, transportAllowance: 20000, otherAllowance: 30000, totalAllowance: 120000, overtimeAmount: 5000, bonusAmount: 0, totalEarning: 325000, incomeTax: 25000, pfDeduction: 10000, loanDeduction: 15000, absentDeduction: 0, otherDeduction: 0, totalDeduction: 50000, netSalary: 275000, paymentDate: '2026-02-05', bankAccount: '****4521', status: 'paid' },
  { id: '2', slipNumber: 'SS-2026-01-002', employeeCode: 'EMP-002', employeeName: 'Fatema Begum', designation: 'Associate Professor', department: 'Academic', payrollMonth: 'January', payrollYear: 2026, basicSalary: 150000, grossSalary: 240000, houseRent: 37500, medicalAllowance: 15000, transportAllowance: 15000, otherAllowance: 22500, totalAllowance: 90000, overtimeAmount: 2500, bonusAmount: 20000, totalEarning: 262500, incomeTax: 18000, pfDeduction: 7500, loanDeduction: 0, absentDeduction: 0, otherDeduction: 0, totalDeduction: 25500, netSalary: 237000, paymentDate: '2026-02-05', bankAccount: '****7832', status: 'paid' },
  { id: '3', slipNumber: 'SS-2026-01-003', employeeCode: 'EMP-003', employeeName: 'Kamal Hossain', designation: 'Manager', department: 'IT', payrollMonth: 'January', payrollYear: 2026, basicSalary: 100000, grossSalary: 160000, houseRent: 25000, medicalAllowance: 10000, transportAllowance: 10000, otherAllowance: 15000, totalAllowance: 60000, overtimeAmount: 7500, bonusAmount: 0, totalEarning: 167500, incomeTax: 12000, pfDeduction: 5000, loanDeduction: 8000, absentDeduction: 6000, otherDeduction: 0, totalDeduction: 31000, netSalary: 136500, paymentDate: '2026-02-05', bankAccount: '****2156', status: 'paid' },
];

const SalarySlip: React.FC = () => {
  const [selectedSlip, setSelectedSlip] = useState<SalarySlip | null>(null);
  const [searchTerm, setSearchTerm] = useState('');
  const [filterMonth, setFilterMonth] = useState('January 2026');

  const filteredSlips = salarySlips.filter(slip => {
    const matchesSearch = slip.employeeName.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         slip.employeeCode.toLowerCase().includes(searchTerm.toLowerCase());
    return matchesSearch;
  });

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Salary Slip</h1>
          <p className="text-gray-500">Generate and view employee salary slips</p>
        </div>
        <div className="flex gap-3">
          <select
            value={filterMonth}
            onChange={(e) => setFilterMonth(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg"
          >
            <option value="January 2026">January 2026</option>
            <option value="December 2025">December 2025</option>
          </select>
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Download All (PDF)
          </button>
        </div>
      </div>

      {/* Search */}
      <div className="bg-white p-4 rounded-lg border border-gray-100">
        <input
          type="text"
          placeholder="Search by employee name or code..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="w-full px-4 py-2 border border-gray-300 rounded-lg"
        />
      </div>

      {/* Salary Slips Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slip #</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Designation</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gross</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Deduction</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net Salary</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {filteredSlips.map((slip) => (
              <tr key={slip.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 font-medium text-blue-600">{slip.slipNumber}</td>
                <td className="px-6 py-4">
                  <p className="font-medium text-gray-900">{slip.employeeName}</p>
                  <p className="text-sm text-gray-500">{slip.employeeCode}</p>
                </td>
                <td className="px-6 py-4 text-gray-600">{slip.designation}</td>
                <td className="px-6 py-4 text-right text-gray-900">৳{slip.totalEarning.toLocaleString()}</td>
                <td className="px-6 py-4 text-right text-red-600">৳{slip.totalDeduction.toLocaleString()}</td>
                <td className="px-6 py-4 text-right font-bold text-green-600">৳{slip.netSalary.toLocaleString()}</td>
                <td className="px-6 py-4 text-center">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${
                    slip.status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                  }`}>
                    {slip.status}
                  </span>
                </td>
                <td className="px-6 py-4 text-center">
                  <button
                    onClick={() => setSelectedSlip(slip)}
                    className="text-blue-600 hover:text-blue-800 mr-2"
                  >
                    View
                  </button>
                  <button className="text-green-600 hover:text-green-800 mr-2">PDF</button>
                  <button className="text-gray-600 hover:text-gray-800">Email</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Salary Slip Detail Modal */}
      {selectedSlip && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
            <div className="p-6 border-b border-gray-100 flex items-center justify-between">
              <div>
                <h2 className="text-xl font-bold text-gray-900">Salary Slip</h2>
                <p className="text-gray-500">{selectedSlip.slipNumber}</p>
              </div>
              <div className="flex gap-2">
                <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                  Download PDF
                </button>
                <button onClick={() => setSelectedSlip(null)} className="text-gray-400 hover:text-gray-600">
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
            <div className="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
              {/* Company Header */}
              <div className="text-center mb-6 border-b pb-4">
                <h1 className="text-2xl font-bold text-gray-900">Education ERP</h1>
                <p className="text-gray-500">123 Academic Street, Dhaka 1000</p>
                <p className="text-gray-500">Pay Slip - {selectedSlip.payrollMonth} {selectedSlip.payrollYear}</p>
              </div>

              {/* Employee Info */}
              <div className="grid grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                <div>
                  <p className="text-sm text-gray-500">Employee Name</p>
                  <p className="font-medium text-gray-900">{selectedSlip.employeeName}</p>
                </div>
                <div>
                  <p className="text-sm text-gray-500">Employee ID</p>
                  <p className="font-medium text-gray-900">{selectedSlip.employeeCode}</p>
                </div>
                <div>
                  <p className="text-sm text-gray-500">Designation</p>
                  <p className="font-medium text-gray-900">{selectedSlip.designation}</p>
                </div>
                <div>
                  <p className="text-sm text-gray-500">Department</p>
                  <p className="font-medium text-gray-900">{selectedSlip.department}</p>
                </div>
              </div>

              {/* Earnings */}
              <div className="mb-6">
                <h3 className="font-semibold text-gray-900 mb-2">Earnings</h3>
                <table className="w-full">
                  <tbody className="divide-y divide-gray-100">
                    <tr>
                      <td className="py-2 text-gray-600">Basic Salary</td>
                      <td className="py-2 text-right text-gray-900">৳{selectedSlip.basicSalary.toLocaleString()}</td>
                    </tr>
                    <tr>
                      <td className="py-2 text-gray-600">House Rent</td>
                      <td className="py-2 text-right text-gray-900">৳{selectedSlip.houseRent.toLocaleString()}</td>
                    </tr>
                    <tr>
                      <td className="py-2 text-gray-600">Medical Allowance</td>
                      <td className="py-2 text-right text-gray-900">৳{selectedSlip.medicalAllowance.toLocaleString()}</td>
                    </tr>
                    <tr>
                      <td className="py-2 text-gray-600">Transport Allowance</td>
                      <td className="py-2 text-right text-gray-900">৳{selectedSlip.transportAllowance.toLocaleString()}</td>
                    </tr>
                    <tr>
                      <td className="py-2 text-gray-600">Other Allowance</td>
                      <td className="py-2 text-right text-gray-900">৳{selectedSlip.otherAllowance.toLocaleString()}</td>
                    </tr>
                    {selectedSlip.overtimeAmount > 0 && (
                      <tr>
                        <td className="py-2 text-gray-600">Overtime</td>
                        <td className="py-2 text-right text-blue-600">৳{selectedSlip.overtimeAmount.toLocaleString()}</td>
                      </tr>
                    )}
                    {selectedSlip.bonusAmount > 0 && (
                      <tr>
                        <td className="py-2 text-gray-600">Bonus</td>
                        <td className="py-2 text-right text-green-600">৳{selectedSlip.bonusAmount.toLocaleString()}</td>
                      </tr>
                    )}
                    <tr className="font-bold bg-green-50">
                      <td className="py-2 text-gray-900">Total Earnings</td>
                      <td className="py-2 text-right text-green-600">৳{selectedSlip.totalEarning.toLocaleString()}</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              {/* Deductions */}
              <div className="mb-6">
                <h3 className="font-semibold text-gray-900 mb-2">Deductions</h3>
                <table className="w-full">
                  <tbody className="divide-y divide-gray-100">
                    <tr>
                      <td className="py-2 text-gray-600">Income Tax</td>
                      <td className="py-2 text-right text-red-600">৳{selectedSlip.incomeTax.toLocaleString()}</td>
                    </tr>
                    <tr>
                      <td className="py-2 text-gray-600">Provident Fund</td>
                      <td className="py-2 text-right text-red-600">৳{selectedSlip.pfDeduction.toLocaleString()}</td>
                    </tr>
                    {selectedSlip.loanDeduction > 0 && (
                      <tr>
                        <td className="py-2 text-gray-600">Loan Recovery</td>
                        <td className="py-2 text-right text-red-600">৳{selectedSlip.loanDeduction.toLocaleString()}</td>
                      </tr>
                    )}
                    {selectedSlip.absentDeduction > 0 && (
                      <tr>
                        <td className="py-2 text-gray-600">Absent Deduction</td>
                        <td className="py-2 text-right text-red-600">৳{selectedSlip.absentDeduction.toLocaleString()}</td>
                      </tr>
                    )}
                    <tr className="font-bold bg-red-50">
                      <td className="py-2 text-gray-900">Total Deductions</td>
                      <td className="py-2 text-right text-red-600">৳{selectedSlip.totalDeduction.toLocaleString()}</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              {/* Net Salary */}
              <div className="p-4 bg-blue-600 text-white rounded-lg">
                <div className="flex justify-between items-center">
                  <div>
                    <p className="text-sm opacity-80">Net Salary</p>
                    <p className="text-2xl font-bold">৳{selectedSlip.netSalary.toLocaleString()}</p>
                    <p className="text-sm opacity-80 mt-1">Payment Date: {selectedSlip.paymentDate}</p>
                  </div>
                  <div className="text-right">
                    <p className="text-sm opacity-80">Bank Account</p>
                    <p className="font-medium">{selectedSlip.bankAccount}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default SalarySlip;
