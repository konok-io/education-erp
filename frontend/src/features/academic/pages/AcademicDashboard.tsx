import React from 'react';
import {
  Building2,
  Users,
  BookOpen,
  Calendar,
  GraduationCap,
  Layers,
  ChevronRight,
} from 'lucide-react';

const academicStats = [
  { label: 'Academic Levels', value: 4, icon: Layers, color: 'bg-blue-500' },
  { label: 'Faculties', value: 6, icon: Building2, color: 'bg-purple-500' },
  { label: 'Departments', value: 24, icon: Users, color: 'bg-green-500' },
  { label: 'Programs', value: 48, icon: GraduationCap, color: 'bg-orange-500' },
  { label: 'Subjects', value: 320, icon: BookOpen, color: 'bg-teal-500' },
  { label: 'Sessions', value: 2, icon: Calendar, color: 'bg-indigo-500' },
];

const quickLinks = [
  { title: 'Academic Levels', description: 'Manage education levels', icon: Layers, href: '/academic/levels' },
  { title: 'Faculties', description: 'Manage faculties', icon: Building2, href: '/academic/faculties' },
  { title: 'Departments', description: 'Manage departments', icon: Users, href: '/academic/departments' },
  { title: 'Programs', description: 'Manage academic programs', icon: GraduationCap, href: '/academic/programs' },
  { title: 'Subjects', description: 'Manage subjects', icon: BookOpen, href: '/academic/subjects' },
  { title: 'Calendar', description: 'Academic calendar events', icon: Calendar, href: '/academic/calendar' },
];

const recentActivity = [
  { id: 1, action: 'New program added', item: 'BS Computer Science', time: '2 hours ago' },
  { id: 2, action: 'Session updated', item: 'Spring 2026', time: '5 hours ago' },
  { id: 3, action: 'Subject assigned', item: 'Mathematics 101', time: '1 day ago' },
  { id: 4, action: 'Department created', item: 'Data Science', time: '2 days ago' },
];

const AcademicDashboard: React.FC = () => {
  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Academic Dashboard</h1>
          <p className="text-gray-500">Manage academic structure and hierarchy</p>
        </div>
        <div className="flex gap-3">
          <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
            <span>+ New</span>
          </button>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        {academicStats.map((stat, index) => (
          <div
            key={index}
            className="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:shadow-md transition-shadow"
          >
            <div className="flex items-center gap-4">
              <div className={`w-12 h-12 ${stat.color} rounded-lg flex items-center justify-center`}>
                <stat.icon className="w-6 h-6 text-white" />
              </div>
              <div>
                <p className="text-sm text-gray-500">{stat.label}</p>
                <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Quick Links */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 className="text-lg font-semibold mb-4">Quick Access</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {quickLinks.map((link, index) => (
            <button
              key={index}
              className="flex items-center gap-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors text-left group"
            >
              <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <link.icon className="w-5 h-5 text-blue-600" />
              </div>
              <div className="flex-1">
                <p className="font-medium text-gray-900">{link.title}</p>
                <p className="text-sm text-gray-500">{link.description}</p>
              </div>
              <ChevronRight className="w-5 h-5 text-gray-400 group-hover:text-gray-600" />
            </button>
          ))}
        </div>
      </div>

      {/* Bottom Section */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Recent Activity */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold">Recent Activity</h2>
            <button className="text-blue-600 hover:text-blue-700 text-sm">View All</button>
          </div>
          <div className="space-y-4">
            {recentActivity.map((activity) => (
              <div key={activity.id} className="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                <div className="w-2 h-2 bg-blue-500 rounded-full" />
                <div className="flex-1">
                  <p className="font-medium text-gray-900">{activity.action}</p>
                  <p className="text-sm text-gray-500">{activity.item}</p>
                </div>
                <span className="text-xs text-gray-400">{activity.time}</span>
              </div>
            ))}
          </div>
        </div>

        {/* Current Session */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold">Current Session</h2>
            <span className="px-3 py-1 bg-green-100 text-green-700 text-sm rounded-full">Active</span>
          </div>
          <div className="space-y-4">
            <div className="p-4 bg-blue-50 rounded-lg">
              <p className="text-lg font-semibold text-blue-900">Spring 2026</p>
              <p className="text-sm text-blue-600">January 15, 2026 - June 30, 2026</p>
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div className="p-3 bg-gray-50 rounded-lg">
                <p className="text-sm text-gray-500">Semesters</p>
                <p className="text-xl font-bold text-gray-900">2</p>
              </div>
              <div className="p-3 bg-gray-50 rounded-lg">
                <p className="text-sm text-gray-500">Active Programs</p>
                <p className="text-xl font-bold text-gray-900">48</p>
              </div>
              <div className="p-3 bg-gray-50 rounded-lg">
                <p className="text-sm text-gray-500">Total Students</p>
                <p className="text-xl font-bold text-gray-900">2,450</p>
              </div>
              <div className="p-3 bg-gray-50 rounded-lg">
                <p className="text-sm text-gray-500">Total Teachers</p>
                <p className="text-xl font-bold text-gray-900">180</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default AcademicDashboard;
