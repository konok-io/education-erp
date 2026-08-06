/**
 * Certificate Dashboard Page
 */

import React, { useEffect } from 'react';
import { useCertificateStore } from '../store/certificateStore';
import { Link } from 'react-router-dom';
import {
  FileText, Award, BookOpen, ScrollText,
  CheckCircle, Clock, AlertTriangle, Download,
  QrCode, Archive, Settings, Users
} from 'lucide-react';

export const CertificateDashboard: React.FC = () => {
  const { dashboard, dashboardLoading, fetchDashboard } = useCertificateStore();

  useEffect(() => {
    fetchDashboard();
  }, [fetchDashboard]);

  if (dashboardLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  const stats = [
    {
      title: 'Total Certificates',
      value: dashboard?.total_certificates ?? 0,
      icon: FileText,
      color: 'bg-blue-500',
      link: '/certificates',
    },
    {
      title: 'Certificates Issued',
      value: dashboard?.certificates_issued ?? 0,
      icon: Award,
      color: 'bg-green-500',
      link: '/certificates?status=issued',
    },
    {
      title: 'Transcripts',
      value: dashboard?.total_transcripts ?? 0,
      icon: ScrollText,
      color: 'bg-purple-500',
      link: '/certificates/transcripts',
    },
    {
      title: 'Marksheets',
      value: dashboard?.total_marksheets ?? 0,
      icon: BookOpen,
      color: 'bg-yellow-500',
      link: '/certificates/marksheets',
    },
    {
      title: 'Today Downloads',
      value: dashboard?.today_downloads ?? 0,
      icon: Download,
      color: 'bg-teal-500',
      link: '/certificates/verifications',
    },
    {
      title: 'Verifications',
      value: dashboard?.verifications_today ?? 0,
      icon: QrCode,
      color: 'bg-orange-500',
      link: '/certificates/verifications',
    },
  ];

  const alerts = [
    {
      title: 'Pending Approval',
      value: dashboard?.pending_approval ?? 0,
      icon: Clock,
      color: 'text-yellow-600',
      bgColor: 'bg-yellow-50',
      borderColor: 'border-yellow-200',
      link: '/certificates?status=pending_approval',
    },
    {
      title: 'Duplicate Requests',
      value: dashboard?.pending_duplicates ?? 0,
      icon: AlertTriangle,
      color: 'text-red-600',
      bgColor: 'bg-red-50',
      borderColor: 'border-red-200',
      link: '/certificates/duplicate-requests',
    },
    {
      title: 'Active Templates',
      value: dashboard?.active_templates ?? 0,
      icon: FileText,
      color: 'text-blue-600',
      bgColor: 'bg-blue-50',
      borderColor: 'border-blue-200',
      link: '/certificates/templates',
    },
    {
      title: 'Signatures',
      value: dashboard?.active_signatures ?? 0,
      icon: Users,
      color: 'text-green-600',
      bgColor: 'bg-green-50',
      borderColor: 'border-green-200',
      link: '/certificates/signatures',
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Certificate Dashboard</h1>
          <p className="text-gray-500">Manage certificates, transcripts & documents</p>
        </div>
        <div className="flex gap-3">
          <Link
            to="/certificates/new"
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
          >
            <FileText className="w-4 h-4" />
            Generate Certificate
          </Link>
          <Link
            to="/certificates/templates"
            className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            <Settings className="w-4 h-4" />
            Settings
          </Link>
        </div>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
        {stats.map((stat) => (
          <Link
            key={stat.title}
            to={stat.link}
            className="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow"
          >
            <div className={`${stat.color} p-3 rounded-lg w-fit`}>
              <stat.icon className="w-6 h-6 text-white" />
            </div>
            <div className="mt-4">
              <p className="text-3xl font-bold text-gray-900">{stat.value}</p>
              <p className="text-gray-500 text-sm">{stat.title}</p>
            </div>
          </Link>
        ))}
      </div>

      {/* Alerts Section */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {alerts.map((alert) => (
          <Link
            key={alert.title}
            to={alert.link || '#'}
            className={`${alert.bgColor} border ${alert.borderColor} rounded-xl p-4`}
          >
            <div className="flex items-center gap-3">
              <alert.icon className={`w-6 h-6 ${alert.color}`} />
              <div>
                <p className="text-2xl font-bold text-gray-900">{alert.value}</p>
                <p className={`text-sm ${alert.color}`}>{alert.title}</p>
              </div>
            </div>
          </Link>
        ))}
      </div>

      {/* Quick Actions */}
      <div className="bg-white rounded-xl p-6 shadow-sm">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          <Link
            to="/certificates/new?type=transfer"
            className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <FileText className="w-5 h-5 text-blue-600" />
            <span className="font-medium">Transfer Certificate</span>
          </Link>
          <Link
            to="/certificates/new?type=character"
            className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <Award className="w-5 h-5 text-green-600" />
            <span className="font-medium">Character Certificate</span>
          </Link>
          <Link
            to="/certificates/transcripts/new"
            className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <ScrollText className="w-5 h-5 text-purple-600" />
            <span className="font-medium">Generate Transcript</span>
          </Link>
          <Link
            to="/certificates/verify"
            className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <QrCode className="w-5 h-5 text-orange-600" />
            <span className="font-medium">Verify Certificate</span>
          </Link>
        </div>
      </div>

      {/* Overview Section */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-xl p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Certificate Overview</h2>
          <div className="grid grid-cols-3 gap-4">
            <div className="text-center p-4 bg-green-50 rounded-lg">
              <p className="text-3xl font-bold text-green-600">{dashboard?.certificates_issued ?? 0}</p>
              <p className="text-sm text-gray-500">Issued</p>
            </div>
            <div className="text-center p-4 bg-yellow-50 rounded-lg">
              <p className="text-3xl font-bold text-yellow-600">{dashboard?.pending_approval ?? 0}</p>
              <p className="text-sm text-gray-500">Pending</p>
            </div>
            <div className="text-center p-4 bg-blue-50 rounded-lg">
              <p className="text-3xl font-bold text-blue-600">{dashboard?.active_templates ?? 0}</p>
              <p className="text-sm text-gray-500">Templates</p>
            </div>
          </div>
        </div>

        <div className="bg-white rounded-xl p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Academic Documents</h2>
          <div className="grid grid-cols-3 gap-4">
            <div className="text-center p-4 bg-purple-50 rounded-lg">
              <p className="text-3xl font-bold text-purple-600">{dashboard?.transcripts_issued ?? 0}</p>
              <p className="text-sm text-gray-500">Transcripts</p>
            </div>
            <div className="text-center p-4 bg-yellow-50 rounded-lg">
              <p className="text-3xl font-bold text-yellow-600">{dashboard?.marksheets_issued ?? 0}</p>
              <p className="text-sm text-gray-500">Marksheets</p>
            </div>
            <div className="text-center p-4 bg-teal-50 rounded-lg">
              <p className="text-3xl font-bold text-teal-600">{dashboard?.verifications_today ?? 0}</p>
              <p className="text-sm text-gray-500">Verifications</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
