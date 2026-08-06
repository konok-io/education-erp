/**
 * Phase 034 - Enterprise HRM System
 * Enhanced HR Dashboard
 */

import { useState, useEffect } from 'react';
import { getHRDashboard } from '../services/hrApi';
import type { HRDashboard, HRDashboardStats } from '../types';

export function HRDashboard() {
  const [dashboard, setDashboard] = useState<HRDashboard | null>(null);
  const [stats, setStats] = useState<HRDashboardStats | null>(null);
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

  const employeeStats = [
    { label: 'Total Employees', value: dashboard?.employees ?? 0, icon: '👥', color: 'bg-blue-500' },
    { label: 'Active', value: stats?.employees.active ?? 0, icon: '✅', color: 'bg-green-500' },
    { label: 'New Joinings', value: stats?.employees.new_joining ?? 0, icon: '🎉', color: 'bg-purple-500' },
    { label: 'Resigned', value: stats?.employees.resigned ?? 0, icon: '👋', color: 'bg-orange-500' },
  ];

  const workflowStats = [
    { label: 'Pending Interviews', value: stats?.recruitment.pending_interviews ?? 0, icon: '📋', color: 'bg-indigo-500' },
    { label: 'Pending Confirmation', value: stats?.workflow.pending_confirmation ?? 0, icon: '⏳', color: 'bg-yellow-500' },
    { label: 'Pending Transfer', value: stats?.workflow.pending_transfer ?? 0, icon: '🔄', color: 'bg-cyan-500' },
    { label: 'Pending Exit Clearance', value: stats?.workflow.pending_exit_clearance ?? 0, icon: '🚪', color: 'bg-red-500' },
  ];

  const payrollStats = [
    { label: 'Pending Leaves', value: dashboard?.pending_leaves ?? 0, icon: '📋', color: 'bg-yellow-500' },
    { label: 'Pending Loans', value: dashboard?.pending_loans ?? 0, icon: '💰', color: 'bg-purple-500' },
    { label: 'Pending Overtimes', value: dashboard?.pending_overtimes ?? 0, icon: '⏰', color: 'bg-green-500' },
  ];

  const payrollSummary = [
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

      {/* Employee Stats Grid */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Employee Overview</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {employeeStats.map((stat) => (
            <div
              key={stat.label}
              className="flex items-center gap-4 p-4 bg-gray-50 rounded-lg"
            >
              <div className={`${stat.color} p-3 rounded-lg text-white text-xl`}>
                {stat.icon}
              </div>
              <div>
                <p className="text-sm text-gray-500">{stat.label}</p>
                <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Main Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {workflowStats.map((stat) => (
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
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Payroll & Deductions</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
              </div>
            </div>
          ))}
          {payrollSummary.map((stat) => (
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
        <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
          {[
            { label: 'Employees', icon: '👤', link: '/hr/employees' },
            { label: 'Recruitment', icon: '📢', link: '/hr/recruitment' },
            { label: 'Job Circular', icon: '📋', link: '/hr/recruitment/circulars' },
            { label: 'Applicants', icon: '📝', link: '/hr/recruitment/applicants' },
            { label: 'Interviews', icon: '🎤', link: '/hr/recruitment/interviews' },
            { label: 'Payroll', icon: '💵', link: '/hr/payroll' },
            { label: 'Leaves', icon: '🏖️', link: '/hr/leaves' },
            { label: 'Reports', icon: '📊', link: '/hr/reports' },
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
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Workflow Summary</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div className="p-4 bg-blue-50 rounded-lg">
            <div className="flex items-center gap-3 mb-2">
              <span className="text-2xl">📢</span>
              <span className="font-semibold text-gray-900">Recruitment</span>
            </div>
            <div className="space-y-1 text-sm text-gray-600">
              <p>Active Circulars: {stats?.recruitment.active_circulars ?? 0}</p>
              <p>Total Applications: {stats?.recruitment.total_applications ?? 0}</p>
              <p>Selected: {stats?.recruitment.selected ?? 0}</p>
            </div>
          </div>
          <div className="p-4 bg-purple-50 rounded-lg">
            <div className="flex items-center gap-3 mb-2">
              <span className="text-2xl">📈</span>
              <span className="font-semibold text-gray-900">This Month</span>
            </div>
            <div className="space-y-1 text-sm text-gray-600">
              <p>New Employees: {stats?.employees.new_joining ?? 0}</p>
              <p>Gross Salary: ${(stats?.payroll.month_gross ?? 0).toLocaleString()}</p>
              <p>Net Salary: ${(stats?.payroll.month_net ?? 0).toLocaleString()}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
