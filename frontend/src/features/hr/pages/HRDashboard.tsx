import { useState, useEffect } from 'react';
import { getHRDashboard } from '../services/hrApi';
import type { HRDashboard } from '../types';

export function HRDashboard() {
  const [dashboard, setDashboard] = useState<HRDashboard | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetchDashboard();
  }, []);

  const fetchDashboard = async () => {
    try {
      setLoading(true);
      const data = await getHRDashboard();
      setDashboard(data);
      setError(null);
    } catch (err) {
      setError('Failed to load dashboard data');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
        {error}
      </div>
    );
  }

  const stats = [
    { label: 'Total Employees', value: dashboard?.employees ?? 0, icon: '👥', color: 'bg-blue-500' },
    { label: 'Pending Leaves', value: dashboard?.pending_leaves ?? 0, icon: '📋', color: 'bg-yellow-500' },
    { label: 'Pending Loans', value: dashboard?.pending_loans ?? 0, icon: '💰', color: 'bg-purple-500' },
    { label: 'Pending Overtimes', value: dashboard?.pending_overtimes ?? 0, icon: '⏰', color: 'bg-green-500' },
  ];

  const payrollStats = [
    { label: 'Total Payslips', value: dashboard?.month_payroll?.total ?? 0, icon: '📄', color: 'bg-indigo-500' },
    { label: 'Total Gross', value: `$${(dashboard?.month_payroll?.gross ?? 0).toLocaleString()}`, icon: '💵', color: 'bg-teal-500' },
    { label: 'Total Net', value: `$${(dashboard?.month_payroll?.net ?? 0).toLocaleString()}`, icon: '💰', color: 'bg-emerald-500' },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">HR Dashboard</h1>
        <button
          onClick={fetchDashboard}
          className="px-4 py-2 text-sm text-blue-600 hover:text-blue-700"
        >
          Refresh
        </button>
      </div>

      {/* Main Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {stats.map((stat) => (
          <div
            key={stat.label}
            className="bg-white rounded-lg shadow-sm border border-gray-200 p-6"
          >
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-gray-500">{stat.label}</p>
                <p className="text-3xl font-bold text-gray-900 mt-1">{stat.value}</p>
              </div>
              <div className={`${stat.color} p-3 rounded-lg text-white text-2xl`}>
                {stat.icon}
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Payroll Stats */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">This Month's Payroll</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {payrollStats.map((stat) => (
            <div
              key={stat.label}
              className="flex items-center gap-4 p-4 bg-gray-50 rounded-lg"
            >
              <div className={`${stat.color} p-3 rounded-lg text-white text-xl`}>
                {stat.icon}
              </div>
              <div>
                <p className="text-sm text-gray-500">{stat.label}</p>
                <p className="text-xl font-bold text-gray-900">{stat.value}</p>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Quick Actions */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
          {[
            { label: 'Add Employee', icon: '👤', link: '/hr/employees/create' },
            { label: 'Process Payroll', icon: '💵', link: '/hr/payroll' },
            { label: 'Approve Leave', icon: '✅', link: '/hr/leaves' },
            { label: 'Add Loan', icon: '🏦', link: '/hr/loans' },
            { label: 'Record OT', icon: '⏱️', link: '/hr/overtime' },
            { label: 'View Reports', icon: '📊', link: '/hr/reports' },
          ].map((action) => (
            <a
              key={action.label}
              href={action.link}
              className="flex flex-col items-center justify-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors"
            >
              <span className="text-2xl mb-2">{action.icon}</span>
              <span className="text-sm text-gray-700 text-center">{action.label}</span>
            </a>
          ))}
        </div>
      </div>

      {/* Recent Activity */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Pending Approvals</h2>
        <div className="space-y-3">
          {dashboard?.pending_leaves ? (
            <div className="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
              <div className="flex items-center gap-3">
                <span className="text-xl">📋</span>
                <span className="text-gray-700">Pending Leave Requests</span>
              </div>
              <span className="px-3 py-1 bg-yellow-500 text-white text-sm font-medium rounded-full">
                {dashboard.pending_leaves}
              </span>
            </div>
          ) : null}
          
          {dashboard?.pending_loans ? (
            <div className="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
              <div className="flex items-center gap-3">
                <span className="text-xl">💰</span>
                <span className="text-gray-700">Pending Loan Applications</span>
              </div>
              <span className="px-3 py-1 bg-purple-500 text-white text-sm font-medium rounded-full">
                {dashboard.pending_loans}
              </span>
            </div>
          ) : null}
          
          {dashboard?.pending_overtimes ? (
            <div className="flex items-center justify-between p-3 bg-green-50 rounded-lg">
              <div className="flex items-center gap-3">
                <span className="text-xl">⏰</span>
                <span className="text-gray-700">Pending Overtime Records</span>
              </div>
              <span className="px-3 py-1 bg-green-500 text-white text-sm font-medium rounded-full">
                {dashboard.pending_overtimes}
              </span>
            </div>
          ) : null}

          {!dashboard?.pending_leaves && !dashboard?.pending_loans && !dashboard?.pending_overtimes && (
            <p className="text-gray-500 text-center py-4">No pending approvals</p>
          )}
        </div>
      </div>
    </div>
  );
}
