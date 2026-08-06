import React from 'react';
import {
  Users,
  UserPlus,
  GraduationCap,
  TrendingUp,
  Calendar,
  AlertCircle,
  ChevronRight,
} from 'lucide-react';

const studentStats = [
  { label: 'Total Students', value: '2,450', icon: Users, color: 'bg-blue-500', change: '+12%' },
  { label: 'New Admissions', value: '340', icon: UserPlus, color: 'bg-green-500', change: '+8%' },
  { label: 'Active Sessions', value: '2', icon: Calendar, color: 'bg-purple-500', change: '' },
  { label: 'Graduating', value: '420', icon: GraduationCap, color: 'bg-orange-500', change: '+5%' },
];

const recentAdmissions = [
  { id: 1, name: 'Rahim Ahmed', studentId: 'STU-2026-001', program: 'B.Sc. CSE', date: '2026-01-15' },
  { id: 2, name: 'Fatema Begum', studentId: 'STU-2026-002', program: 'BBA', date: '2026-01-14' },
  { id: 3, name: 'Kamal Hossain', studentId: 'STU-2026-003', program: 'B.Sc. EEE', date: '2026-01-13' },
  { id: 4, name: 'Nusrat Jahan', studentId: 'STU-2026-004', program: 'B.A. English', date: '2026-01-12' },
];

const quickLinks = [
  { title: 'All Students', description: 'Manage student records', href: '/students/list' },
  { title: 'Add Student', description: 'Register new student', href: '/students/add' },
  { title: 'Guardian Info', description: 'Manage guardians', href: '/students/guardians' },
  { title: 'Promotions', description: 'Promote students', href: '/students/promotions' },
];

const StudentDashboard: React.FC = () => {
  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Student Dashboard</h1>
          <p className="text-gray-500">Manage student information and records</p>
        </div>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
          <UserPlus className="w-5 h-5" />
          Add Student
        </button>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {studentStats.map((stat, index) => (
          <div
            key={index}
            className="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow"
          >
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-gray-500">{stat.label}</p>
                <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
                {stat.change && (
                  <p className="text-sm text-green-600 mt-1">{stat.change} from last month</p>
                )}
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
          <button
            key={index}
            className="flex items-center gap-4 p-4 bg-white rounded-xl border border-gray-100 hover:border-blue-300 hover:shadow-sm transition-all text-left group"
          >
            <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <Users className="w-5 h-5 text-blue-600" />
            </div>
            <div className="flex-1">
              <p className="font-medium text-gray-900">{link.title}</p>
              <p className="text-sm text-gray-500">{link.description}</p>
            </div>
            <ChevronRight className="w-5 h-5 text-gray-400 group-hover:text-gray-600" />
          </button>
        ))}
      </div>

      {/* Bottom Section */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Recent Admissions */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold">Recent Admissions</h2>
            <button className="text-blue-600 hover:text-blue-700 text-sm">View All</button>
          </div>
          <div className="space-y-4">
            {recentAdmissions.map((student) => (
              <div key={student.id} className="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                  <span className="text-sm font-bold text-blue-600">
                    {student.name.split(' ').map(n => n[0]).join('')}
                  </span>
                </div>
                <div className="flex-1">
                  <p className="font-medium text-gray-900">{student.name}</p>
                  <p className="text-sm text-gray-500">{student.studentId} - {student.program}</p>
                </div>
                <span className="text-xs text-gray-400">{student.date}</span>
              </div>
            ))}
          </div>
        </div>

        {/* Enrollment by Program */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold">Enrollment by Program</h2>
            <TrendingUp className="w-5 h-5 text-gray-400" />
          </div>
          <div className="space-y-4">
            {[
              { program: 'B.Sc. Computer Science', count: 450, percentage: 18 },
              { program: 'BBA', count: 380, percentage: 16 },
              { program: 'B.Sc. Electrical Engineering', count: 320, percentage: 13 },
              { program: 'B.A. English', count: 280, percentage: 11 },
              { program: 'Others', count: 1020, percentage: 42 },
            ].map((item, index) => (
              <div key={index}>
                <div className="flex justify-between mb-1">
                  <span className="text-sm text-gray-700">{item.program}</span>
                  <span className="text-sm font-medium text-gray-900">{item.count}</span>
                </div>
                <div className="w-full bg-gray-200 rounded-full h-2">
                  <div
                    className="bg-blue-600 h-2 rounded-full"
                    style={{ width: `${item.percentage}%` }}
                  />
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
};

export default StudentDashboard;
