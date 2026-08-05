import React, { useState } from 'react';
import {
  Shield,
  AlertTriangle,
  CheckCircle,
  Clock,
  FileText,
  TrendingUp,
} from 'lucide-react';

interface Audit {
  id: string;
  code: string;
  type: string;
  name: string;
  startDate: string;
  endDate?: string;
  status: string;
  scope: string;
  riskLevel: string;
  complianceStatus: string;
  findings?: string;
}

interface FraudAlert {
  id: string;
  code: string;
  type: string;
  severity: string;
  status: string;
  description: string;
  amount?: number;
  date: string;
}

const audits: Audit[] = [
  { id: '1', code: 'AUD-2026-Q1', type: 'Internal', name: 'Q1 2026 Financial Audit', startDate: '2026-01-15', endDate: '2026-01-30', status: 'completed', scope: 'Full Financial Review', riskLevel: 'Medium', complianceStatus: 'compliant', findings: 'All controls operating effectively' },
  { id: '2', code: 'AUD-2026-TAX', type: 'Tax', name: 'VAT Compliance Audit', startDate: '2026-02-01', status: 'in_progress', scope: 'Tax Compliance Review', riskLevel: 'High', complianceStatus: 'partial' },
  { id: '3', code: 'AUD-2025-AN', type: 'External', name: 'Annual Audit FY 2025', startDate: '2025-12-01', endDate: '2025-12-31', status: 'completed', scope: 'Full External Audit', riskLevel: 'Medium', complianceStatus: 'compliant', findings: 'Clean audit opinion issued' },
  { id: '4', code: 'AUD-2025-IT', type: 'IT', name: 'IT Systems Audit', startDate: '2025-11-01', endDate: '2025-11-15', status: 'completed', scope: 'System Controls', riskLevel: 'Medium', complianceStatus: 'compliant', findings: 'Minor recommendations for improvement' },
];

const fraudAlerts: FraudAlert[] = [
  { id: '1', code: 'FRA-001', type: 'Duplicate Payment', severity: 'high', status: 'investigating', description: 'Potential duplicate payment detected for supplier invoice #INV-2026-045', amount: 25000, date: '2026-02-03' },
  { id: '2', code: 'FRA-002', type: 'Large Transaction', severity: 'medium', status: 'resolved', description: 'Large cash withdrawal exceeding threshold', amount: 500000, date: '2026-02-01' },
  { id: '3', code: 'FRA-003', type: 'Unauthorized Posting', severity: 'high', status: 'investigating', description: 'Journal entry posted outside business hours', date: '2026-01-28' },
  { id: '4', code: 'FRA-004', type: 'Suspicious Pattern', severity: 'low', status: 'false_alarm', description: 'Unusual payment pattern detected for student fees', amount: 15000, date: '2026-01-25' },
];

const AuditCenter: React.FC = () => {
  const [activeTab, setActiveTab] = useState<'audits' | 'fraud' | 'compliance'>('audits');

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'completed': return 'bg-green-100 text-green-800';
      case 'in_progress': return 'bg-yellow-100 text-yellow-800';
      case 'planned': return 'bg-blue-100 text-blue-800';
      case 'investigating': return 'bg-orange-100 text-orange-800';
      case 'resolved': return 'bg-green-100 text-green-800';
      case 'false_alarm': return 'bg-gray-100 text-gray-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const getSeverityColor = (severity: string) => {
    switch (severity) {
      case 'high': return 'bg-red-100 text-red-800 border-red-200';
      case 'medium': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
      case 'low': return 'bg-blue-100 text-blue-800 border-blue-200';
      default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Audit Center</h1>
          <p className="text-gray-500">Internal, External & Compliance Audits</p>
        </div>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Schedule Audit
        </button>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
              <CheckCircle className="w-5 h-5 text-green-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Completed Audits</p>
              <p className="text-2xl font-bold text-green-600">{audits.filter(a => a.status === 'completed').length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
              <Clock className="w-5 h-5 text-yellow-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">In Progress</p>
              <p className="text-2xl font-bold text-yellow-600">{audits.filter(a => a.status === 'in_progress').length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
              <AlertTriangle className="w-5 h-5 text-red-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Fraud Alerts</p>
              <p className="text-2xl font-bold text-red-600">{fraudAlerts.filter(f => f.status === 'investigating').length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
              <Shield className="w-5 h-5 text-blue-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Compliance Rate</p>
              <p className="text-2xl font-bold text-blue-600">95%</p>
            </div>
          </div>
        </div>
      </div>

      {/* Tabs */}
      <div className="flex border-b border-gray-200">
        <button
          onClick={() => setActiveTab('audits')}
          className={`px-6 py-3 font-medium ${activeTab === 'audits' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'}`}
        >
          Audits
        </button>
        <button
          onClick={() => setActiveTab('fraud')}
          className={`px-6 py-3 font-medium ${activeTab === 'fraud' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'}`}
        >
          Fraud Detection
        </button>
        <button
          onClick={() => setActiveTab('compliance')}
          className={`px-6 py-3 font-medium ${activeTab === 'compliance' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700'}`}
        >
          Compliance
        </button>
      </div>

      {/* Audits Tab */}
      {activeTab === 'audits' && (
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <table className="w-full">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Audit Code</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scope</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Risk Level</th>
                <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {audits.map((audit) => (
                <tr key={audit.id} className="hover:bg-gray-50">
                  <td className="px-6 py-4 font-medium text-blue-600">{audit.code}</td>
                  <td className="px-6 py-4 text-gray-600">{audit.type}</td>
                  <td className="px-6 py-4 text-gray-900">{audit.name}</td>
                  <td className="px-6 py-4 text-gray-600">{audit.scope}</td>
                  <td className="px-6 py-4">
                    <span className={`px-2 py-1 text-xs font-medium rounded-full border ${getSeverityColor(audit.riskLevel)}`}>
                      {audit.riskLevel}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-center">
                    <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(audit.status)}`}>
                      {audit.status.replace('_', ' ')}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-center">
                    <button className="text-blue-600 hover:text-blue-800 mr-2">View</button>
                    <button className="text-green-600 hover:text-green-800">Report</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {/* Fraud Detection Tab */}
      {activeTab === 'fraud' && (
        <div className="space-y-4">
          <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div className="p-4 border-b border-gray-100 flex items-center justify-between">
              <h3 className="font-semibold text-gray-900">Fraud Alerts</h3>
              <button className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                Configure Rules
              </button>
            </div>
            <table className="w-full">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alert Code</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                  <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Severity</th>
                  <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100">
                {fraudAlerts.map((alert) => (
                  <tr key={alert.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 font-medium text-red-600">{alert.code}</td>
                    <td className="px-6 py-4 text-gray-600">{alert.type}</td>
                    <td className="px-6 py-4 text-gray-900">{alert.description}</td>
                    <td className="px-6 py-4 text-right text-gray-900">
                      {alert.amount ? `৳${alert.amount.toLocaleString()}` : '-'}
                    </td>
                    <td className="px-6 py-4 text-center">
                      <span className={`px-2 py-1 text-xs font-medium rounded-full border ${getSeverityColor(alert.severity)}`}>
                        {alert.severity}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-center">
                      <span className={`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(alert.status)}`}>
                        {alert.status.replace('_', ' ')}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-center">
                      <button className="text-blue-600 hover:text-blue-800 mr-2">Investigate</button>
                      {alert.status === 'investigating' && (
                        <button className="text-green-600 hover:text-green-800">Resolve</button>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Fraud Detection Rules */}
          <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 className="font-semibold text-gray-900 mb-4">Active Detection Rules</h3>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div className="p-4 bg-green-50 rounded-lg border border-green-200">
                <CheckCircle className="w-5 h-5 text-green-600 mb-2" />
                <p className="font-medium text-green-800">Duplicate Payment Detection</p>
                <p className="text-sm text-green-600">Active</p>
              </div>
              <div className="p-4 bg-green-50 rounded-lg border border-green-200">
                <CheckCircle className="w-5 h-5 text-green-600 mb-2" />
                <p className="font-medium text-green-800">Large Transaction Alert</p>
                <p className="text-sm text-green-600">Active - Threshold: ৳100,000</p>
              </div>
              <div className="p-4 bg-green-50 rounded-lg border border-green-200">
                <CheckCircle className="w-5 h-5 text-green-600 mb-2" />
                <p className="font-medium text-green-800">After-Hours Posting</p>
                <p className="text-sm text-green-600">Active</p>
              </div>
              <div className="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <TrendingUp className="w-5 h-5 text-yellow-600 mb-2" />
                <p className="font-medium text-yellow-800">Suspicious Pattern</p>
                <p className="text-sm text-yellow-600">Monitoring</p>
              </div>
              <div className="p-4 bg-green-50 rounded-lg border border-green-200">
                <CheckCircle className="w-5 h-5 text-green-600 mb-2" />
                <p className="font-medium text-green-800">Manual Override Alert</p>
                <p className="text-sm text-green-600">Active</p>
              </div>
              <div className="p-4 bg-green-50 rounded-lg border border-green-200">
                <CheckCircle className="w-5 h-5 text-green-600 mb-2" />
                <p className="font-medium text-green-800">Negative Cash Warning</p>
                <p className="text-sm text-green-600">Active</p>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Compliance Tab */}
      {activeTab === 'compliance' && (
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <h3 className="font-semibold text-gray-900 mb-4">Compliance Standards</h3>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div className="p-4 bg-green-50 rounded-lg border border-green-200 text-center">
              <CheckCircle className="w-8 h-8 text-green-600 mx-auto mb-2" />
              <p className="font-semibold text-green-800">IAS/IFRS Ready</p>
              <p className="text-sm text-green-600">Fully Compliant</p>
            </div>
            <div className="p-4 bg-green-50 rounded-lg border border-green-200 text-center">
              <CheckCircle className="w-8 h-8 text-green-600 mx-auto mb-2" />
              <p className="font-semibold text-green-800">GAAP Ready</p>
              <p className="text-sm text-green-600">Fully Compliant</p>
            </div>
            <div className="p-4 bg-yellow-50 rounded-lg border border-yellow-200 text-center">
              <Clock className="w-8 h-8 text-yellow-600 mx-auto mb-2" />
              <p className="font-semibold text-yellow-800">Tax Compliance</p>
              <p className="text-sm text-yellow-600">In Progress</p>
            </div>
            <div className="p-4 bg-green-50 rounded-lg border border-green-200 text-center">
              <CheckCircle className="w-8 h-8 text-green-600 mx-auto mb-2" />
              <p className="font-semibold text-green-800">Audit Ready</p>
              <p className="text-sm text-green-600">All Controls Active</p>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default AuditCenter;
