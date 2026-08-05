/**
 * Phase 035 - Enterprise CRM System
 * CRM Dashboard
 */

import { useState, useEffect } from 'react';
import { getCrmDashboard } from '../services/crmApi';
import type { CrmDashboardStats } from '../types';

export function CrmDashboard() {
  const [stats, setStats] = useState<CrmDashboardStats | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetchDashboard();
  }, []);

  const fetchDashboard = async () => {
    try {
      setLoading(true);
      const data = await getCrmDashboard();
      setStats(data);
      setError(null);
    } catch (err) {
      setError('Failed to load CRM dashboard');
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

  const contactStats = [
    { label: 'Total Contacts', value: stats?.contacts.total ?? 0, icon: '👥', color: 'bg-blue-500' },
    { label: 'Active', value: stats?.contacts.active ?? 0, icon: '✅', color: 'bg-green-500' },
    { label: 'Inactive', value: stats?.contacts.inactive ?? 0, icon: '⏸️', color: 'bg-yellow-500' },
    { label: 'Blocked', value: stats?.contacts.blocked ?? 0, icon: '🚫', color: 'bg-red-500' },
  ];

  const leadStats = [
    { label: 'Total Leads', value: stats?.leads.total ?? 0, icon: '🎯', color: 'bg-purple-500' },
    { label: 'Active', value: stats?.leads.active ?? 0, icon: '🔥', color: 'bg-orange-500' },
    { label: 'Converted', value: stats?.leads.converted ?? 0, icon: '✨', color: 'bg-emerald-500' },
    { label: 'Follow-up Due', value: stats?.leads.followup_due ?? 0, icon: '⏰', color: 'bg-cyan-500' },
  ];

  const ticketStats = [
    { label: 'Open Tickets', value: stats?.open_tickets ?? 0, icon: '🎫', color: 'bg-indigo-500' },
    { label: 'Closed', value: stats?.closed_tickets ?? 0, icon: '✅', color: 'bg-green-500' },
    { label: 'Pending Follow-ups', value: stats?.pending_followups ?? 0, icon: '📋', color: 'bg-yellow-500' },
    { label: 'Today Inquiries', value: stats?.today_inquiries ?? 0, icon: '📝', color: 'bg-pink-500' },
  ];

  const campaignStats = [
    { label: 'Total Campaigns', value: stats?.campaigns.total ?? 0, icon: '📢', color: 'bg-teal-500' },
    { label: 'Draft', value: stats?.campaigns.draft ?? 0, icon: '📝', color: 'bg-gray-500' },
    { label: 'Scheduled', value: stats?.campaigns.scheduled ?? 0, icon: '⏳', color: 'bg-yellow-500' },
    { label: 'Running', value: stats?.campaigns.running ?? 0, icon: '🚀', color: 'bg-blue-500' },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">CRM Dashboard</h1>
        <button
          onClick={fetchDashboard}
          className="px-4 py-2 text-sm text-blue-600 hover:text-blue-700"
        >
          Refresh
        </button>
      </div>

      {/* Contacts Stats */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Contacts</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {contactStats.map((stat) => (
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

      {/* Leads Stats */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Lead Management</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {leadStats.map((stat) => (
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

      {/* Tickets Stats */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Helpdesk</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {ticketStats.map((stat) => (
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

      {/* Campaigns Stats */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Campaigns</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {campaignStats.map((stat) => (
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

      {/* Quick Actions */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
          {[
            { label: 'Add Contact', icon: '👤', link: '/crm/contacts/create' },
            { label: 'New Lead', icon: '🎯', link: '/crm/leads/create' },
            { label: 'Create Ticket', icon: '🎫', link: '/crm/tickets/create' },
            { label: 'New Campaign', icon: '📢', link: '/crm/campaigns/create' },
            { label: 'Send Email', icon: '✉️', link: '/crm/communications/email' },
            { label: 'Send SMS', icon: '💬', link: '/crm/communications/sms' },
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
    </div>
  );
}
