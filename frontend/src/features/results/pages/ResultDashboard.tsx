import React from 'react';
import { FileText, Award, BarChart3, CheckCircle, AlertCircle, ChevronRight } from 'lucide-react';

const resultStats = [
  { label: 'Published Results', value: '48', icon: FileText, color: 'bg-blue-500' },
  { label: 'Pending Results', value: '12', icon: AlertCircle, color: 'bg-yellow-500' },
  { label: 'Grade A Students', value: '320', icon: Award, color: 'bg-green-500' },
  { label: 'Avg Pass Rate', value: '87%', icon: BarChart3, color: 'bg-purple-500' },
];

const recentResults = [
  { id: 1, exam: 'Mid-term Exam - Spring 2026', program: 'B.Sc. CSE', date: '2026-03-15', status: 'published', passRate: 92 },
  { id: 2, exam: 'Quiz 2 - Mathematics', program: 'BBA', date: '2026-03-10', status: 'pending', passRate: 0 },
  { id: 3, exam: 'Final Exam - Fall 2025', program: 'B.Sc. EEE', date: '2025-12-20', status: 'published', passRate: 88 },
  { id: 4, exam: 'Class Test 3 - Physics', program: 'B.Sc. CSE', date: '2026-03-08', status: 'published', passRate: 85 },
];

const quickLinks = [
  { title: 'All Results', description: 'View all exam results', href: '/results/all' },
  { title: 'Publish Result', description: 'Upload and publish results', href: '/results/publish' },
  { title: 'Grade Setup', description: 'Configure grading rules', href: '/results/grades' },
  { title: 'Result Reports', description: 'Generate result reports', href: '/results/reports' },
];

const statusColors: Record<string, string> = {
  published: 'bg-green-100 text-green-700',
  pending: 'bg-yellow-100 text-yellow-700',
  draft: 'bg-gray-100 text-gray-700',
};

const ResultDashboard: React.FC = () => {
  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Results Dashboard</h1>
          <p className="text-gray-500">Manage exam results and grading</p>
        </div>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
          <FileText className="w-5 h-5" />
          Publish Result
        </button>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {resultStats.map((stat, index) => (
          <div key={index} className="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-gray-500">{stat.label}</p>
                <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
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

      {/* Recent Results */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-lg font-semibold">Recent Results</h2>
          <button className="text-blue-600 hover:text-blue-700 text-sm">View All</button>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pass Rate</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {recentResults.map((result) => (
                <tr key={result.id} className="hover:bg-gray-50">
                  <td className="px-4 py-3 text-sm font-medium text-gray-900">{result.exam}</td>
                  <td className="px-4 py-3 text-sm text-gray-700">{result.program}</td>
                  <td className="px-4 py-3 text-sm text-gray-700">{result.date}</td>
                  <td className="px-4 py-3">
                    <span className={`px-2 py-1 text-xs font-medium rounded-full ${statusColors[result.status]}`}>
                      {result.status}
                    </span>
                  </td>
                  <td className="px-4 py-3 text-sm font-medium text-gray-900">
                    {result.passRate > 0 ? `${result.passRate}%` : '-'}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default ResultDashboard;
