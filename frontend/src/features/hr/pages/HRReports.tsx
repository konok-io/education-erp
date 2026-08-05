import { useState } from 'react';
import { getPayrollReport, getLeaveReport } from '../services/hrApi';
import type { PayrollReport, LeaveReport } from '../types';

export function HRReports() {
  const [activeTab, setActiveTab] = useState<'payroll' | 'leave'>('payroll');
  const [year, setYear] = useState(new Date().getFullYear());
  const [month, setMonth] = useState(new Date().getMonth() + 1);
  const [payrollReport, setPayrollReport] = useState<PayrollReport | null>(null);
  const [leaveReport, setLeaveReport] = useState<LeaveReport | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const fetchPayrollReport = async () => {
    try {
      setLoading(true);
      setError(null);
      const data = await getPayrollReport({ month, year });
      setPayrollReport(data);
    } catch (err) {
      setError('Failed to load payroll report');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const fetchLeaveReport = async () => {
    try {
      setLoading(true);
      setError(null);
      const data = await getLeaveReport({ year });
      setLeaveReport(data);
    } catch (err) {
      setError('Failed to load leave report');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleGenerate = () => {
    if (activeTab === 'payroll') {
      fetchPayrollReport();
    } else {
      fetchLeaveReport();
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">HR Reports</h1>
      </div>

      {/* Tabs */}
      <div className="border-b border-gray-200">
        <nav className="-mb-px flex space-x-4">
          <button
            onClick={() => setActiveTab('payroll')}
            className={`py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'payroll'
                ? 'border-blue-600 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700'
            }`}
          >
            Payroll Report
          </button>
          <button
            onClick={() => setActiveTab('leave')}
            className={`py-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'leave'
                ? 'border-blue-600 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700'
            }`}
          >
            Leave Report
          </button>
        </nav>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div className="flex flex-wrap items-end gap-4">
          {activeTab === 'payroll' && (
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Month</label>
              <select
                value={month}
                onChange={(e) => setMonth(Number(e.target.value))}
                className="rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
              >
                {Array.from({ length: 12 }, (_, i) => i + 1).map((m) => (
                  <option key={m} value={m}>
                    {new Date(2024, m - 1).toLocaleString('default', { month: 'long' })}
                  </option>
                ))}
              </select>
            </div>
          )}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Year</label>
            <select
              value={year}
              onChange={(e) => setYear(Number(e.target.value))}
              className="rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
            >
              {Array.from({ length: 5 }, (_, i) => new Date().getFullYear() - i).map((y) => (
                <option key={y} value={y}>{y}</option>
              ))}
            </select>
          </div>
          <button
            onClick={handleGenerate}
            disabled={loading}
            className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
          >
            {loading ? 'Generating...' : 'Generate Report'}
          </button>
        </div>
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
          {error}
        </div>
      )}

      {/* Payroll Report */}
      {activeTab === 'payroll' && payrollReport && (
        <div className="space-y-6">
          <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-4">
              Payroll Summary - {new Date(payrollReport.year, payrollReport.month - 1).toLocaleString('default', { month: 'long' })} {payrollReport.year}
            </h2>
            
            {/* Summary Cards */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
              <div className="bg-blue-50 p-4 rounded-lg">
                <p className="text-sm text-gray-500">Total Employees</p>
                <p className="text-2xl font-bold text-blue-600">{payrollReport.total_employees}</p>
              </div>
              <div className="bg-green-50 p-4 rounded-lg">
                <p className="text-sm text-gray-500">Total Gross</p>
                <p className="text-2xl font-bold text-green-600">${payrollReport.total_gross?.toLocaleString()}</p>
              </div>
              <div className="bg-purple-50 p-4 rounded-lg">
                <p className="text-sm text-gray-500">Total Net</p>
                <p className="text-2xl font-bold text-purple-600">${payrollReport.total_net?.toLocaleString()}</p>
              </div>
              <div className="bg-red-50 p-4 rounded-lg">
                <p className="text-sm text-gray-500">Total Deductions</p>
                <p className="text-2xl font-bold text-red-600">${payrollReport.total_deduction?.toLocaleString()}</p>
              </div>
            </div>

            {/* Additional Stats */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
              <div className="bg-yellow-50 p-4 rounded-lg">
                <p className="text-sm text-gray-500">Total Overtime</p>
                <p className="text-xl font-bold text-yellow-600">${payrollReport.total_overtime?.toLocaleString()}</p>
              </div>
              <div className="bg-indigo-50 p-4 rounded-lg">
                <p className="text-sm text-gray-500">Total Bonus</p>
                <p className="text-xl font-bold text-indigo-600">${payrollReport.total_bonus?.toLocaleString()}</p>
              </div>
            </div>

            {/* By Department */}
            {payrollReport.by_department && Object.keys(payrollReport.by_department).length > 0 && (
              <div>
                <h3 className="text-md font-semibold text-gray-900 mb-3">By Department</h3>
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                      <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Employees</th>
                      <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Gross</th>
                      <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Net</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-200">
                    {Object.entries(payrollReport.by_department).map(([dept, data]) => (
                      <tr key={dept}>
                        <td className="px-4 py-2 text-sm text-gray-900">{dept}</td>
                        <td className="px-4 py-2 text-sm text-gray-900">{data.count}</td>
                        <td className="px-4 py-2 text-sm text-gray-900">${data.gross?.toLocaleString()}</td>
                        <td className="px-4 py-2 text-sm text-gray-900">${data.net?.toLocaleString()}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        </div>
      )}

      {/* Leave Report */}
      {activeTab === 'leave' && leaveReport && (
        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">
            Leave Summary - {leaveReport.year}
          </h2>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div className="bg-blue-50 p-4 rounded-lg">
              <p className="text-sm text-gray-500">Total Leave Requests</p>
              <p className="text-2xl font-bold text-blue-600">{leaveReport.total_leaves}</p>
            </div>
            <div className="bg-green-50 p-4 rounded-lg">
              <p className="text-sm text-gray-500">Total Leave Days</p>
              <p className="text-2xl font-bold text-green-600">{leaveReport.total_days}</p>
            </div>
          </div>

          {/* By Type */}
          {leaveReport.by_type && Object.keys(leaveReport.by_type).length > 0 && (
            <div>
              <h3 className="text-md font-semibold text-gray-900 mb-3">By Leave Type</h3>
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Leave Type</th>
                    <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Requests</th>
                    <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Days</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200">
                  {Object.entries(leaveReport.by_type).map(([type, data]) => (
                    <tr key={type}>
                      <td className="px-4 py-2 text-sm text-gray-900">{type}</td>
                      <td className="px-4 py-2 text-sm text-gray-900">{data.count}</td>
                      <td className="px-4 py-2 text-sm text-gray-900">{data.days}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      )}
    </div>
  );
}
