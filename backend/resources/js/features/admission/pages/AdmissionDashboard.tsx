import React from 'react';
import { FileText, Users, CheckCircle, Clock, AlertCircle, ChevronRight } from 'lucide-react';

const admissionStats = [
  { label: 'Total Applications', value: '850', icon: FileText, color: 'bg-blue-500', change: '+12%' },
  { label: 'Pending Review', value: '125', icon: Clock, color: 'bg-yellow-500', change: '' },
  { label: 'Shortlisted', value: '420', icon: Users, color: 'bg-purple-500', change: '+8%' },
  { label: 'Admitted', value: '305', icon: CheckCircle, color: 'bg-green-500', change: '+15%' },
];

const recentApplications = [
  { id: 1, name: 'Rahim Ahmed', program: 'B.Sc. CSE', appliedDate: '2026-01-15', status: 'shortlisted' },
  { id: 2, name: 'Fatema Begum', program: 'BBA', appliedDate: '2026-01-14', status: 'pending' },
  { id: 3, name: 'Kamal Hossain', program: 'B.Sc. EEE', appliedDate: '2026-01-13', status: 'admitted' },
  { id: 4, name: 'Nusrat Jahan', program: 'B.A. English', appliedDate: '2026-01-12', status: 'pending' },
];

const quickLinks = [
  { title: 'All Applications', description: 'View and manage applications', href: '/admission/applications' },
  { title: 'New Application', description: 'Start new admission process', href: '/admission/apply' },
  { title: 'Eligibility Criteria', description: 'Set admission requirements', href: '/admission/criteria' },
  { title: 'Admission Report', description: 'View admission statistics', href: '/admission/reports' },
];

const statusColors: Record<string, string> = {
  pending: 'bg-yellow-100 text-yellow-700',
  shortlisted: 'bg-blue-100 text-blue-700',
  admitted: 'bg-green-100 text-green-700',
  rejected: 'bg-red-100 text-red-700',
};

const AdmissionDashboard: React.FC = () => {
  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Admission Dashboard</h1>
          <p className="text-gray-500">Manage admission applications and campaigns</p>
        </div>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
          <FileText className="w-5 h-5" />
          New Campaign
        </button>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {admissionStats.map((stat, index) => (
          <div key={index} className="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-gray-500">{stat.label}</p>
                <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
                {stat.change && <p className="text-sm text-green-600 mt-1">{stat.change} from last month</p>}
              </div>
              <div className={`w-12 h-12 ${stat.color} rounded-lg flex items-center justify-center`}>
                <stat.icon className="w-6 h-6 text-white" />
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Quick Links */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {quickLinks.map((link, index) => (
          <button key={index} className="flex items-center gap-4 p-4 bg-white rounded-xl border border-gray-100 hover:border-blue-300 hover:shadow-sm transition-all text-left group">
            <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <FileText className="w-5 h-5 text-blue-600" />
            </div>
            <div className="flex-1">
              <p className="font-medium text-gray-900">{link.title}</p>
              <p className="text-sm text-gray-500">{link.description}</p>
            </div>
            <ChevronRight className="w-5 h-5 text-gray-400" />
          </button>
        ))}
      </div>

      {/* Recent Applications */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-lg font-semibold">Recent Applications</h2>
          <button className="text-blue-600 hover:text-blue-700 text-sm">View All</button>
        </div>
        <div className="space-y-3">
          {recentApplications.map((app) => (
            <div key={app.id} className="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
              <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <span className="text-sm font-bold text-blue-600">
                  {app.name.split(' ').map(n => n[0]).join('')}
                </span>
              </div>
              <div className="flex-1">
                <p className="font-medium text-gray-900">{app.name}</p>
                <p className="text-sm text-gray-500">{app.program}</p>
              </div>
              <span className="text-sm text-gray-400">{app.appliedDate}</span>
              <span className={`px-3 py-1 text-xs font-medium rounded-full ${statusColors[app.status]}`}>
                {app.status}
              </span>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default AdmissionDashboard;
