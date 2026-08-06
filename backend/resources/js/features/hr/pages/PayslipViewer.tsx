import { useState, useEffect } from 'react';
import { getPayslip, exportPayslips } from '../services/hrApi';
import type { Payslip } from '../types';

export function PayslipViewer() {
  const [payslip, setPayslip] = useState<Payslip | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [payrollId, setPayrollId] = useState('');
  const [exporting, setExporting] = useState(false);

  const handleFetchPayslip = async () => {
    if (!payrollId) return;
    try {
      setLoading(true);
      setError(null);
      const data = await getPayslip(payrollId);
      setPayslip(data);
    } catch (err) {
      setError('Failed to load payslip');
      setPayslip(null);
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleExport = async (format: 'pdf' | 'excel') => {
    if (!payslip) return;
    try {
      setExporting(true);
      const url = await exportPayslips({
        month: payslip.payroll.month,
        year: payslip.payroll.year,
        format,
      });
      window.open(url, '_blank');
    } catch (err) {
      alert('Failed to export');
      console.error(err);
    } finally {
      setExporting(false);
    }
  };

  if (!payslip) {
    return (
      <div className="space-y-6">
        <div className="flex items-center justify-between">
          <h1 className="text-2xl font-bold text-gray-900">Payslip Viewer</h1>
        </div>

        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 className="text-lg font-medium text-gray-900 mb-4">Enter Payroll ID</h3>
          <div className="flex gap-4">
            <input
              type="text"
              value={payrollId}
              onChange={(e) => setPayrollId(e.target.value)}
              placeholder="Enter payroll UUID"
              className="flex-1 rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
            />
            <button
              onClick={handleFetchPayslip}
              disabled={!payrollId || loading}
              className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
            >
              {loading ? 'Loading...' : 'View Payslip'}
            </button>
          </div>
          {error && (
            <p className="mt-2 text-sm text-red-600">{error}</p>
          )}
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">Payslip Viewer</h1>
        <div className="flex gap-2">
          <button
            onClick={() => setPayslip(null)}
            className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
          >
            Back
          </button>
          <button
            onClick={() => handleExport('pdf')}
            disabled={exporting}
            className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
          >
            Export PDF
          </button>
          <button
            onClick={() => handleExport('excel')}
            disabled={exporting}
            className="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50"
          >
            Export Excel
          </button>
        </div>
      </div>

      {/* Payslip Card */}
      <div className="bg-white rounded-lg shadow-lg border border-gray-200 p-8 max-w-3xl mx-auto">
        {/* Header */}
        <div className="text-center border-b-2 border-gray-200 pb-6 mb-6">
          <h2 className="text-2xl font-bold text-gray-900">EDUCATION ERP</h2>
          <p className="text-gray-500">Salary Payslip</p>
        </div>

        {/* Employee Info */}
        <div className="grid grid-cols-2 gap-6 mb-6">
          <div>
            <h4 className="text-sm font-medium text-gray-500 mb-1">Employee Name</h4>
            <p className="text-gray-900">{payslip.employee.name}</p>
          </div>
          <div>
            <h4 className="text-sm font-medium text-gray-500 mb-1">Employee ID</h4>
            <p className="text-gray-900">{payslip.employee.employee_no}</p>
          </div>
          <div>
            <h4 className="text-sm font-medium text-gray-500 mb-1">Department</h4>
            <p className="text-gray-900">{payslip.employee.department}</p>
          </div>
          <div>
            <h4 className="text-sm font-medium text-gray-500 mb-1">Designation</h4>
            <p className="text-gray-900">{payslip.employee.designation}</p>
          </div>
        </div>

        {/* Payroll Period */}
        <div className="bg-gray-50 p-4 rounded-lg mb-6">
          <div className="flex justify-between items-center">
            <div>
              <h4 className="text-sm font-medium text-gray-500">Payroll No</h4>
              <p className="text-gray-900">{payslip.payroll.no}</p>
            </div>
            <div>
              <h4 className="text-sm font-medium text-gray-500">Period</h4>
              <p className="text-gray-900">
                {new Date(payslip.payroll.year, payslip.payroll.month - 1).toLocaleString('default', { month: 'long' })}{' '}
                {payslip.payroll.year}
              </p>
            </div>
          </div>
        </div>

        {/* Attendance */}
        <div className="mb-6">
          <h4 className="text-sm font-medium text-gray-500 mb-2">Attendance Summary</h4>
          <div className="grid grid-cols-3 gap-4">
            <div className="bg-blue-50 p-3 rounded-lg text-center">
              <p className="text-2xl font-bold text-blue-600">{payslip.attendance.working_days}</p>
              <p className="text-sm text-gray-500">Working Days</p>
            </div>
            <div className="bg-green-50 p-3 rounded-lg text-center">
              <p className="text-2xl font-bold text-green-600">{payslip.attendance.present_days}</p>
              <p className="text-sm text-gray-500">Present</p>
            </div>
            <div className="bg-red-50 p-3 rounded-lg text-center">
              <p className="text-2xl font-bold text-red-600">{payslip.attendance.absent_days}</p>
              <p className="text-sm text-gray-500">Absent</p>
            </div>
          </div>
        </div>

        {/* Earnings & Deductions */}
        <div className="grid grid-cols-2 gap-6 mb-6">
          {/* Earnings */}
          <div>
            <h4 className="text-sm font-medium text-gray-500 mb-2 border-b pb-2">Earnings</h4>
            <div className="space-y-2">
              {payslip.earnings.map((earning, i) => (
                <div key={i} className="flex justify-between text-sm">
                  <span className="text-gray-700">{earning.name}</span>
                  <span className="font-medium text-gray-900">${earning.amount.toLocaleString()}</span>
                </div>
              ))}
              <div className="flex justify-between text-sm font-bold border-t pt-2 mt-2">
                <span>Gross Salary</span>
                <span>${payslip.totals.gross.toLocaleString()}</span>
              </div>
            </div>
          </div>

          {/* Deductions */}
          <div>
            <h4 className="text-sm font-medium text-gray-500 mb-2 border-b pb-2">Deductions</h4>
            <div className="space-y-2">
              {payslip.deductions.map((deduction, i) => (
                <div key={i} className="flex justify-between text-sm">
                  <span className="text-gray-700">{deduction.name}</span>
                  <span className="font-medium text-red-600">-${deduction.amount.toLocaleString()}</span>
                </div>
              ))}
              <div className="flex justify-between text-sm font-bold border-t pt-2 mt-2">
                <span>Total Deductions</span>
                <span className="text-red-600">$${payslip.totals.total_deduction.toLocaleString()}</span>
              </div>
            </div>
          </div>
        </div>

        {/* Net Salary */}
        <div className="bg-blue-600 text-white p-6 rounded-lg mb-6">
          <div className="flex justify-between items-center">
            <div>
              <p className="text-sm opacity-80">Net Salary</p>
              <p className="text-sm opacity-80">In Words</p>
            </div>
            <div className="text-right">
              <p className="text-3xl font-bold">${payslip.totals.net.toLocaleString()}</p>
              <p className="text-sm opacity-80">{payslip.net_in_words}</p>
            </div>
          </div>
        </div>

        {/* Footer */}
        <div className="flex justify-between items-end text-sm text-gray-500 border-t pt-6">
          <div>
            <p>Generated on: {new Date().toLocaleDateString()}</p>
          </div>
          <div className="text-right">
            <div className="border-t-2 border-gray-400 w-40 mb-1"></div>
            <p>Authorized Signature</p>
          </div>
        </div>
      </div>
    </div>
  );
}
