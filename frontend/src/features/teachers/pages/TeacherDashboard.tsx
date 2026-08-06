import React from 'react';
import {
  Users,
  UserPlus,
  GraduationCap,
  BookOpen,
  Clock,
  ChevronRight,
} from 'lucide-react';

const teacherStats = [
  { label: 'Total Teachers', value: '180', icon: Users, color: 'bg-blue-500', change: '+5%' },
  { label: 'New Hires', value: '15', icon: UserPlus, color: 'bg-green-500', change: '+2%' },
  { label: 'Departments', value: '12', icon: GraduationCap, color: 'bg-purple-500', change: '' },
  { label: 'Subjects Assigned', value: '320', icon: BookOpen, color: 'bg-orange-500', change: '+10%' },
];

const recentHires = [
  { id: 1, name: 'Dr. Rahman Khan', designation: 'Associate Professor', department: 'CSE', joinDate: '2026-01-10' },
  { id: 2, name: 'Prof. Karim Ahmed', designation: 'Professor', department: 'EEE', joinDate: '2026-01-08' },
  { id: 3, name: 'Dr. Fatema Begum', designation: 'Assistant Professor', department: 'English', joinDate: '2026-01-05' },
  { id: 4, name: 'Prof. Hasan Ali', designation: 'Lecturer', department: 'Mathematics', joinDate: '2026-01-03' },
];

const quickLinks = [
  { title: 'All Teachers', description: 'Manage teacher records', href: '/teachers/list' },
  { title: 'Add Teacher', description: 'Register new teacher', href: '/teachers/add' },
  { title: 'Subject Assignments', description: 'Assign subjects to teachers', href: '/teachers/assignments' },
  { title: 'Leave Management', description: 'Manage teacher leaves', href: '/teachers/leaves' },
];

const TeacherDashboard: React.FC = () => {
  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Teacher Dashboard</h1>
          <p className="text-gray-500">Manage teacher information and assignments</p>
        </div>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
          <UserPlus className="w-5 h-5" />
          Add Teacher
        </button>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {teacherStats.map((stat, index) => (
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
        {/* Recent Hires */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold">Recent Hires</h2>
            <button className="text-blue-600 hover:text-blue-700 text-sm">View All</button>
          </div>
          <div className="space-y-4">
            {recentHires.map((teacher) => (
              <div key={teacher.id} className="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                <div className="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                  <span className="text-sm font-bold text-purple-600">
                    {teacher.name.split(' ').map(n => n[0]).join('')}
                  </span>
                </div>
                <div className="flex-1">
                  <p className="font-medium text-gray-900">{teacher.name}</p>
                  <p className="text-sm text-gray-500">{teacher.designation} - {teacher.department}</p>
                </div>
                <span className="text-xs text-gray-400">{teacher.joinDate}</span>
              </div>
            ))}
          </div>
        </div>

        {/* Teachers by Department */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold">Teachers by Department</h2>
            <GraduationCap className="w-5 h-5 text-gray-400" />
          </div>
          <div className="space-y-4">
            {[
              { department: 'Computer Science & Engineering', count: 35 },
              { department: 'Electrical Engineering', count: 28 },
              { department: 'Business Administration', count: 25 },
              { department: 'English', count: 20 },
              { department: 'Mathematics', count: 18 },
            ].map((item, index) => (
              <div key={index} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span className="text-sm text-gray-700">{item.department}</span>
                <span className="font-medium text-gray-900">{item.count}</span>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
};

export default TeacherDashboard;
