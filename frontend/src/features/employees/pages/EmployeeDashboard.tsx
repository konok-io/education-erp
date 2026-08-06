import React from 'react';
import { Users, UserPlus, Briefcase, TrendingUp, ChevronRight, Download } from 'lucide-react';

const employeeStats = [
  { label: 'Total Employees', value: '250', icon: Users, color: 'bg-blue-500', change: '+5%' },
  { label: 'New Hires', value: '18', icon: UserPlus, color: 'bg-green-500', change: '+2%' },
  { label: 'Departments', value: '12', icon: Briefcase, color: 'bg-purple-500', change: '' },
  { label: 'Active Rate', value: '94%', icon: TrendingUp, color: 'bg-orange-500', change: '+1%' },
];

const recentHires = [
  { id: 1, name: 'Rahman Khan', designation: 'Accountant', department: 'Finance', joinDate: '2026-01-15' },
  { id: 2, name: 'Fatema Begum', designation: 'Admin Officer', department: 'Administration', joinDate: '2026-01-12' },
  { id: 3, name: 'Kamal Hossain', designation: 'IT Support', department: 'IT', joinDate: '2026-01-10' },
  { id: 4, name: 'Nusrat Jahan', designation: 'Receptionist', department: 'Front Office', joinDate: '2026-01-08' },
];

const departmentWise = [
  { department: 'Administration', count: 25 },
  { department: 'Finance', count: 30 },
  { department: 'IT', count: 20 },
  { department: 'HR', count: 15 },
  { department: 'Front Office', count: 18 },
];

const quickLinks = [
  { title: 'All Employees', description: 'View employee records', href: '/employees/list' },
  { title: 'Add Employee', description: 'Register new employee', href: '/employees/add' },
  { title: 'Departments', description: 'Manage departments', href: '/employees/departments' },
  { title: 'Reports', description: 'Generate HR reports', href: '/employees/reports' },
];

const EmployeeDashboard: React.FC = () => {
  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Employee Dashboard</h1>
          <p className="text-gray-500">Manage staff and personnel records</p>
        </div>
        <div className="flex gap-3">
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2">
            <Download className="w-4 h-4" />
            Export
          </button>
          <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
            <UserPlus className="w-5 h-5" />
            Add Employee
          </button>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {employeeStats.map((stat, index) => (
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
              <Users className="w-5 h-5 text-blue-600" />
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
        {/* Recent Hires */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold">Recent Hires</h2>
            <button className="text-blue-600 hover:text-blue-700 text-sm">View All</button>
          </div>
          <div className="space-y-3">
            {recentHires.map((emp) => (
              <div key={emp.id} className="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                  <span className="text-sm font-bold text-blue-600">
                    {emp.name.split(' ').map(n => n[0]).join('')}
                  </span>
                </div>
                <div className="flex-1">
                  <p className="font-medium text-gray-900">{emp.name}</p>
                  <p className="text-sm text-gray-500">{emp.designation} - {emp.department}</p>
                </div>
                <span className="text-xs text-gray-400">{emp.joinDate}</span>
              </div>
            ))}
          </div>
        </div>

        {/* Department-wise */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold">Employees by Department</h2>
            <button className="text-blue-600 hover:text-blue-700 text-sm">View Report</button>
          </div>
          <div className="space-y-3">
            {departmentWise.map((item, index) => (
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

export default EmployeeDashboard;
