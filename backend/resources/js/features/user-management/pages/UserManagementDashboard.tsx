import React from 'react';
import { Users, Shield, Key, Activity, ChevronRight, AlertCircle } from 'lucide-react';

const userStats = [
  { label: 'Total Users', value: '2,850', icon: Users, color: 'bg-blue-500' },
  { label: 'Active Sessions', value: '145', icon: Activity, color: 'bg-green-500' },
  { label: 'Roles & Permissions', value: '12', icon: Shield, color: 'bg-purple-500' },
  { label: 'API Keys', value: '28', icon: Key, color: 'bg-orange-500' },
];

const recentSessions = [
  { id: 1, user: 'Admin User', email: 'admin@edu.com', ip: '192.168.1.1', lastActive: '2 mins ago', status: 'active' },
  { id: 2, user: 'Rahman Khan', email: 'rahman@edu.com', ip: '192.168.1.45', lastActive: '15 mins ago', status: 'active' },
  { id: 3, user: 'Fatema Begum', email: 'fatema@edu.com', ip: '192.168.1.78', lastActive: '1 hour ago', status: 'inactive' },
  { id: 4, user: 'Kamal Hossain', email: 'kamal@edu.com', ip: '192.168.1.92', lastActive: '2 hours ago', status: 'inactive' },
];

const roles = [
  { name: 'Super Admin', users: 3, color: 'bg-red-100 text-red-700' },
  { name: 'Admin', users: 12, color: 'bg-orange-100 text-orange-700' },
  { name: 'Teacher', users: 180, color: 'bg-blue-100 text-blue-700' },
  { name: 'Student', users: 2450, color: 'bg-green-100 text-green-700' },
  { name: 'Accountant', users: 8, color: 'bg-purple-100 text-purple-700' },
];

const quickLinks = [
  { title: 'All Users', description: 'Manage user accounts', href: '/users/list' },
  { title: 'Roles & Permissions', description: 'Configure access control', href: '/users/roles' },
  { title: 'Sessions', description: 'View active sessions', href: '/users/sessions' },
  { title: 'Audit Log', description: 'View activity logs', href: '/users/audit' },
];

const UserManagementDashboard: React.FC = () => {
  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">User Management</h1>
          <p className="text-gray-500">Manage users, roles, and permissions</p>
        </div>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
          <Users className="w-5 h-5" />
          Add User
        </button>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {userStats.map((stat, index) => (
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
              <Shield className="w-5 h-5 text-blue-600" />
            </div>
            <div className="flex-1">
              <p className="font-medium text-gray-900">{link.title}</p>
              <p className="text-sm text-gray-500">{link.description}</p>
            </div>
            <ChevronRight className="w-5 h-5 text-gray-400" />
          </button>
        ))}
      </div>

      {/* Bottom Section */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Active Sessions */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold">Recent Sessions</h2>
            <button className="text-blue-600 hover:text-blue-700 text-sm">View All</button>
          </div>
          <div className="space-y-3">
            {recentSessions.map((session) => (
              <div key={session.id} className="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                <div className={`w-2 h-2 rounded-full ${session.status === 'active' ? 'bg-green-500' : 'bg-gray-400'}`} />
                <div className="flex-1">
                  <p className="font-medium text-gray-900">{session.user}</p>
                  <p className="text-sm text-gray-500">{session.email}</p>
                </div>
                <div className="text-right text-xs">
                  <p className="text-gray-500">{session.ip}</p>
                  <p className="text-gray-400">{session.lastActive}</p>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Roles */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold">User Roles</h2>
            <button className="text-blue-600 hover:text-blue-700 text-sm">Manage</button>
          </div>
          <div className="space-y-3">
            {roles.map((role, index) => (
              <div key={index} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span className={`px-2 py-1 text-xs font-medium rounded ${role.color}`}>
                  {role.name}
                </span>
                <span className="font-medium text-gray-900">{role.users} users</span>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
};

export default UserManagementDashboard;
