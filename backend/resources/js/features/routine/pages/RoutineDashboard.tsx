import React from 'react';
import { Calendar, Clock, Users, BookOpen, ChevronRight, Download } from 'lucide-react';

const routineStats = [
  { label: 'Total Timetables', value: '24', icon: Calendar, color: 'bg-blue-500' },
  { label: 'Active Periods', value: '8', icon: Clock, color: 'bg-green-500' },
  { label: 'Teachers Assigned', value: '120', icon: Users, color: 'bg-purple-500' },
  { label: 'Subjects Scheduled', value: '180', icon: BookOpen, color: 'bg-orange-500' },
];

const weekdays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'];

const sampleTimetable = [
  { time: '08:00 - 09:00', subjects: ['Mathematics', 'Physics', 'Chemistry', 'English', 'Math'] },
  { time: '09:00 - 10:00', subjects: ['Physics', 'Mathematics', 'Biology', 'Physics', 'English'] },
  { time: '10:00 - 11:00', subjects: ['English', 'Chemistry', 'Mathematics', 'Computer', 'Biology'] },
  { time: '11:00 - 12:00', subjects: ['Computer', 'Biology', 'Physics', 'Mathematics', 'Chemistry'] },
  { time: '12:00 - 01:00', subjects: ['Break', 'Break', 'Break', 'Break', 'Break'] },
  { time: '01:00 - 02:00', subjects: ['Chemistry', 'English', 'Computer', 'Chemistry', 'Physics'] },
];

const quickLinks = [
  { title: 'View Timetable', description: 'Browse class schedules', href: '/routine/timetable' },
  { title: 'Create Timetable', description: 'Generate new timetable', href: '/routine/create' },
  { title: 'Manage Periods', description: 'Configure class periods', href: '/routine/periods' },
  { title: 'Export Schedule', description: 'Download timetable PDF', href: '/routine/export' },
];

const RoutineDashboard: React.FC = () => {
  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Routine Dashboard</h1>
          <p className="text-gray-500">Manage class schedules and timetables</p>
        </div>
        <div className="flex gap-3">
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2">
            <Download className="w-4 h-4" />
            Export
          </button>
          <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
            <Calendar className="w-5 h-5" />
            Create Timetable
          </button>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {routineStats.map((stat, index) => (
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
              <Calendar className="w-5 h-5 text-blue-600" />
            </div>
            <div className="flex-1">
              <p className="font-medium text-gray-900">{link.title}</p>
              <p className="text-sm text-gray-500">{link.description}</p>
            </div>
            <ChevronRight className="w-5 h-5 text-gray-400" />
          </button>
        ))}
      </div>

      {/* Sample Timetable */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-lg font-semibold">B.Sc. CSE - A (Sample)</h2>
          <button className="text-blue-600 hover:text-blue-700 text-sm">View Full</button>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="bg-gray-50">
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Time</th>
                {weekdays.map((day) => (
                  <th key={day} className="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                    {day}
                  </th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {sampleTimetable.map((row, index) => (
                <tr key={index} className="hover:bg-gray-50">
                  <td className="px-4 py-3 text-sm font-medium text-gray-900">{row.time}</td>
                  {row.subjects.map((subject, i) => (
                    <td key={i} className="px-4 py-3 text-center">
                      <span className={`px-2 py-1 text-sm rounded ${
                        subject === 'Break' ? 'bg-gray-100 text-gray-500' : 'bg-blue-50 text-blue-700'
                      }`}>
                        {subject}
                      </span>
                    </td>
                  ))}
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default RoutineDashboard;
