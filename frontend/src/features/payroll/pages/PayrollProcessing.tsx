import React, { useState } from 'react';

interface PayrollItem {
  id: string;
  employeeCode: string;
  employeeName: string;
  department: string;
  basicSalary: number;
  grossSalary: number;
  overtimeHours: number;
  overtimeAmount: number;
  bonusAmount: number;
  totalEarning: number;
  incomeTax: number;
  pfDeduction: number;
  loanDeduction: number;
  absentDeduction: number;
  totalDeduction: number;
  netSalary: number;
  workingDays: number;
  presentDays: number;
  absentDays: number;
  lateDays: number;
  status: string;
}

const payrollItems: PayrollItem[] = [
  { id: '1', employeeCode: 'EMP-001', employeeName: 'Rahim Ahmed', department: 'Academic', basicSalary: 200000, grossSalary: 320000, overtimeHours: 10, overtimeAmount: 5000, bonusAmount: 0, totalEarning: 325000, incomeTax: 25000, pfDeduction: 10000, loanDeduction: 15000, absentDeduction: 0, totalDeduction: 50000, netSalary: 275000, workingDays: 26, presentDays: 26, absentDays: 0, lateDays: 0, status: 'calculated' },
  { id: '2', employeeCode: 'EMP-002', employeeName: 'Fatema Begum', department: 'Academic', basicSalary: 150000, grossSalary: 240000, overtimeHours: 5, overtimeAmount: 2500, bonusAmount: 20000, totalEarning: 262500, incomeTax: 18000, pfDeduction: 7500, loanDeduction: 0, absentDeduction: 0, totalDeduction: 25500, netSalary: 237000, workingDays: 26, presentDays: 26, absentDays: 0, lateDays: 1, status: 'calculated' },
  { id: '3', employeeCode: 'EMP-003', employeeName: 'Kamal Hossain', department: 'IT', basicSalary: 100000, grossSalary: 160000, overtimeHours: 15, overtimeAmount: 7500, bonusAmount: 0, totalEarning: 167500, incomeTax: 12000, pfDeduction: 5000, loanDeduction: 8000, absentDeduction: 6000, totalDeduction: 31000, netSalary: 136500, workingDays: 26, presentDays: 24, absentDays: 1, lateDays: 2, status: 'calculated' },
  { id: '4', employeeCode: 'EMP-004', employeeName: 'Jamal Uddin', department: 'Admin', basicSalary: 50000, grossSalary: 80000, overtimeHours: 0, overtimeAmount: 0, bonusAmount: 0, totalEarning: 80000, incomeTax: 5000, pfDeduction: 2500, loanDeduction: 5000, absentDeduction: 0, totalDeduction: 12500, netSalary: 67500, workingDays: 26, presentDays: 26, absentDays: 0, lateDays: 0, status: 'calculated' },
];

const PayrollProcessing: React.FC = () => {
  const [selectedMonth, setSelectedMonth] = useState('January 2026');
  const [processingStep, setProcessingStep] = useState(1);
  const [selectedItems, setSelectedItems] = useState<string[]>([]);

  const totalEmployees = payrollItems.length;
  const totalGross = payrollItems.reduce((sum, item) => sum + item.grossSalary, 0);
  const totalEarning = payrollItems.reduce((sum, item) => sum + item.totalEarning, 0);
  const totalDeduction = payrollItems.reduce((sum, item) => sum + item.totalDeduction, 0);
  const totalNet = payrollItems.reduce((sum, item) => sum + item.netSalary, 0);

  const toggleSelect = (id: string) => {
    if (selectedItems.includes(id)) {
      setSelectedItems(selectedItems.filter(item => item !== id));
    } else {
      setSelectedItems([...selectedItems, id]);
    }
  };

  const selectAll = () => {
    if (selectedItems.length === payrollItems.length) {
      setSelectedItems([]);
    } else {
      setSelectedItems(payrollItems.map(item => item.id));
    }
  };

  const nextStep = () => {
    if (processingStep < 5) setProcessingStep(processingStep + 1);
  };

  const prevStep = () => {
    if (processingStep > 1) setProcessingStep(processingStep - 1);
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Payroll Processing</h1>
          <p className="text-gray-500">Process monthly payroll for employees</p>
        </div>
        <div className="flex gap-3">
          <select
            value={selectedMonth}
            onChange={(e) => setSelectedMonth(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg"
          >
            <option value="January 2026">January 2026</option>
            <option value="December 2025">December 2025</option>
          </select>
        </div>
      </div>

      {/* Processing Steps */}
      <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <div className="flex items-center justify-between">
          {[
            { step: 1, name: 'Attendance' },
            { step: 2, name: 'Leave' },
            { step: 3, name: 'Overtime' },
            { step: 4, name: 'Calculation' },
            { step: 5, name: 'Approval' },
          ].map((item, index) => (
            <React.Fragment key={item.step}>
              <div className="flex items-center">
                <div className={`w-10 h-10 rounded-full flex items-center justify-center font-bold ${
                  processingStep >= item.step
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 text-gray-500'
                }`}>
                  {processingStep > item.step ? '✓' : item.step}
                </div>
                <p className={`ml-3 text-sm font-medium ${
                  processingStep >= item.step ? 'text-gray-900' : 'text-gray-500'
                }`}>{item.name}</p>
              </div>
              {index < 4 && (
                <div className={`flex-1 h-1 mx-4 ${
                  processingStep > item.step ? 'bg-blue-600' : 'bg-gray-200'
                }`} />
              )}
            </React.Fragment>
          ))}
        </div>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Employees</p>
          <p className="text-2xl font-bold text-blue-600">{totalEmployees}</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Gross Salary</p>
          <p className="text-2xl font-bold text-purple-600">৳{(totalGross / 100000).toFixed(1)}L</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Earning</p>
          <p className="text-2xl font-bold text-green-600">৳{(totalEarning / 100000).toFixed(1)}L</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Total Deduction</p>
          <p className="text-2xl font-bold text-red-600">৳{(totalDeduction / 100000).toFixed(1)}L</p>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <p className="text-sm text-gray-500">Net Payable</p>
          <p className="text-2xl font-bold text-orange-600">৳{(totalNet / 100000).toFixed(1)}L</p>
        </div>
      </div>

      {/* Payroll Items Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div className="p-4 border-b border-gray-100 flex items-center justify-between">
          <h3 className="font-semibold text-gray-900">Payroll Items - {selectedMonth}</h3>
          <div className="flex gap-2">
            <button
              onClick={selectAll}
              className="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50"
            >
              {selectedItems.length === totalEmployees ? 'Deselect All' : 'Select All'}
            </button>
            <button className="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700">
              Calculate Selected
            </button>
          </div>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-4 py-3 text-left">
                  <input
                    type="checkbox"
                    checked={selectedItems.length === totalEmployees}
                    onChange={selectAll}
                    className="rounded border-gray-300"
                  />
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Attendance</th>
                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gross</th>
                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">OT</th>
                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Bonus</th>
                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Deductions</th>
                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net Salary</th>
                <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {payrollItems.map((item) => (
                <tr key={item.id} className={`hover:bg-gray-50 ${selectedItems.includes(item.id) ? 'bg-blue-50' : ''}`}>
                  <td className="px-4 py-3">
                    <input
                      type="checkbox"
                      checked={selectedItems.includes(item.id)}
                      onChange={() => toggleSelect(item.id)}
                      className="rounded border-gray-300"
                    />
                  </td>
                  <td className="px-6 py-4">
                    <p className="font-medium text-gray-900">{item.employeeName}</p>
                    <p className="text-sm text-gray-500">{item.employeeCode}</p>
                  </td>
                  <td className="px-6 py-4 text-gray-600">{item.department}</td>
                  <td className="px-6 py-4 text-center">
                    <div className="text-sm">
                      <span className="text-green-600">{item.presentDays}</span>
                      <span className="text-gray-400">/</span>
                      <span className="text-gray-600">{item.workingDays}</span>
                      {item.absentDays > 0 && <span className="text-red-600 ml-1">(-{item.absentDays})</span>}
                    </div>
                  </td>
                  <td className="px-6 py-4 text-right text-gray-900">৳{item.grossSalary.toLocaleString()}</td>
                  <td className="px-6 py-4 text-right text-blue-600">৳{item.overtimeAmount.toLocaleString()}</td>
                  <td className="px-6 py-4 text-right text-green-600">৳{item.bonusAmount.toLocaleString()}</td>
                  <td className="px-6 py-4 text-right text-red-600">৳{item.totalDeduction.toLocaleString()}</td>
                  <td className="px-6 py-4 text-right font-bold text-gray-900">৳{item.netSalary.toLocaleString()}</td>
                  <td className="px-6 py-4 text-center">
                    <button className="text-blue-600 hover:text-blue-800 mr-2">Edit</button>
                  </td>
                </tr>
              ))}
            </tbody>
            <tfoot className="bg-gray-50 font-bold">
              <tr>
                <td colSpan={4} className="px-6 py-3 text-right">Total:</td>
                <td className="px-6 py-3 text-right text-gray-900">৳{totalGross.toLocaleString()}</td>
                <td className="px-6 py-3 text-right text-blue-600">৳{payrollItems.reduce((sum, i) => sum + i.overtimeAmount, 0).toLocaleString()}</td>
                <td className="px-6 py-3 text-right text-green-600">৳{payrollItems.reduce((sum, i) => sum + i.bonusAmount, 0).toLocaleString()}</td>
                <td className="px-6 py-3 text-right text-red-600">৳{totalDeduction.toLocaleString()}</td>
                <td className="px-6 py-3 text-right text-gray-900">৳{totalNet.toLocaleString()}</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      {/* Action Buttons */}
      <div className="flex items-center justify-between">
        <button
          onClick={prevStep}
          disabled={processingStep === 1}
          className="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
        >
          Previous
        </button>
        <div className="flex gap-3">
          <button className="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
            Save as Draft
          </button>
          <button
            onClick={nextStep}
            className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            {processingStep === 5 ? 'Submit for Approval' : 'Next'}
          </button>
        </div>
      </div>
    </div>
  );
};

export default PayrollProcessing;
