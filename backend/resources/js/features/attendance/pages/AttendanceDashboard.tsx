import React from 'react';
import {
  Users,
  Clock,
  CheckCircle,
  AlertCircle,
  QrCode,
  Download,
  TrendingUp,
} from 'lucide-react';

const attendanceStats = [
  { label: 'Total Present', value: '2,180', icon: CheckCircle, color: 'bg-green-500', percentage: 89 },
  { label: 'Total Absent', value: '270', icon: AlertCircle, color: 'bg-red-500', percentage: 11 },
  { label: 'Late Arrivals', value: '125', icon: Clock, color: 'bg-yellow-500', percentage: 5 },
  { label: 'On Time', value: '2,055', icon: TrendingUp, color: 'bg-blue-500', percentage: 84 },
];

const recentAttendance = [
  { id: 1, name: 'Rahim Ahmed', id_no: 'STU-2022-001', time: '08:30 AM', status: 'present', type: 'on_time' },
  { id: 2, name: 'Fatema Begum', id_no: 'STU-2022-002', time: '08:45 AM', status: 'present', type: 'late' },
  { id: 3, name: 'Kamal Hossain', id_no: 'STU-2023-001', time: '08:25 AM', status: 'present', type: 'on_time' },
  { id: 4, name: 'Nusrat Jahan', id_no: 'STU-2023-002', time: '09:15 AM', status: 'absent', type: 'absent' },
];

const classAttendance = [
  { class: 'B.Sc. CSE - A', present: 42, absent: 3, percentage: 93 },
  { class: 'B.Sc. CSE - B', present: 40, absent: 5, percentage: 89 },
  { class: 'BBA - A', present: 38, absent: 2, percentage: 95 },
  { class: 'BBA - B', present: 35, absent: 5, percentage: 88 },
];

const AttendanceDashboard: React.FC = () => {
  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Attendance Dashboard</h1>
          <p className="text-gray-500">Track and manage attendance records</p>
        </div>
        <div className="flex gap-3">
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2">
            <QrCode className="w-4 h-4" />
            Scan QR
          </button>
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2">
            <Download className="w-4 h-4" />
            Export Report
          </button>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {attendanceStats.map((stat, index) => (
          <div
            key={index}
            className="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow"
          >
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm text-gray-500">{stat.label}</p>
                <p className="text-2xl font-bold text-gray-900">{stat.value}</p>
                <p className="text-sm text-gray-500">{stat.percentage}% of total</p>
              </div>
              <div className={`w-12 h-12 ${stat.color} rounded-lg flex items-center justify-center`}>
                <stat.icon className="w-6 h-6 text-white" />
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Quick Actions */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <button className="flex items-center gap-4 p-4 bg-white rounded-xl border border-gray-100 hover:border-blue-300 hover:shadow-sm transition-all">
          <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <Users className="w-6 h-6 text-blue-600" />
          </div>
          <div className="text-left">
            <p className="font-medium text-gray-900">Mark Student Attendance</p>
            <p className="text-sm text-gray-500">Take daily attendance</p>
          </div>
        </button>
        <button className="flex items-center gap-4 p-4 bg-white rounded-xl border border-gray-100 hover:border-purple-300 hover:shadow-sm transition-all">
          <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <Users className="w-6 h-6 text-purple-600" />
          </div>
          <div className="text-left">
            <p className="font-medium text-gray-900">Mark Teacher Attendance</p>
            <p className="text-sm text-gray-500">Track teacher presence</p>
          </div>
        </button>
        <button className="flex items-center gap-4 p-4 bg-white rounded-xl border border-gray-100 hover:border-green-300 hover:shadow-sm transition-all">
          <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
            <Clock className="w-6 h-6 text-green-600" />
          </div>
          <div className="text-left">
            <p className="font-medium text-gray-900">Correction Requests</p>
            <p className="text-sm text-gray-500">Review attendance corrections</p>
          </div>
        </button>
      </div>

      {/* Bottom Section */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Recent Attendance */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold">Today's Attendance</h2>
            <button className="text-blue-600 hover:text-blue-700 text-sm">View All</button>
          </div>
          <div className="space-y-3">
            {recentAttendance.map((record) => (
              <div key={record.id} className="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                <div className={`w-10 h-10 rounded-full flex items-center justify-center ${
                  record.status === 'present' ? 'bg-green-100' : 'bg-red-100'
                }`}>
                  <span className={`text-sm font-bold ${
                    record.status === 'present' ? 'text-green-600' : 'text-red-600'
                  }`}>
                    {record.name.split(' ').map(n => n[0]).join('')}
                  </span>
                </div>
                <div className="flex-1">
                  <p className="font-medium text-gray-900">{record.name}</p>
                  <p className="text-sm text-gray-500">{record.id_no}</p>
                </div>
                <div className="text-right">
                  <p className="text-sm font-medium text-gray-900">{record.time}</p>
                  <p className={`text-xs ${
                    record.type === 'on_time' ? 'text-green-600' :
                    record.type === 'late' ? 'text-yellow-600' : 'text-red-600'
                  }`}>
                    {record.type.replace('_', ' ')}
                  </p>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Class-wise Attendance */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold">Class-wise Attendance</h2>
            <button className="text-blue-600 hover:text-blue-700 text-sm">View Report</button>
          </div>
          <div className="space-y-4">
            {classAttendance.map((item, index) => (
              <div key={index} className="p-4 bg-gray-50 rounded-lg">
                <div className="flex justify-between items-center mb-2">
                  <span className="font-medium text-gray-900">{item.class}</span>
                  <span className="text-sm font-medium text-gray-700">{item.percentage}%</span>
                </div>
                <div className="w-full bg-gray-200 rounded-full h-2 mb-2">
                  <div
                    className={`h-2 rounded-full ${
                      item.percentage >= 90 ? 'bg-green-500' :
                      item.percentage >= 80 ? 'bg-yellow-500' : 'bg-red-500'
                    }`}
                    style={{ width: `${item.percentage}%` }}
                  />
                </div>
                <div className="flex justify-between text-xs text-gray-500">
                  <span>Present: {item.present}</span>
                  <span>Absent: {item.absent}</span>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
};

export default AttendanceDashboard;
