import React, { useState } from 'react';
import { Plus, Search, Download, Eye, Edit2, Trash2, QrCode } from 'lucide-react';

interface Teacher {
  uuid: string;
  teacher_no: string;
  name: string;
  email: string;
  phone: string;
  designation: string;
  department: string;
  status: 'active' | 'on_leave' | 'inactive';
  join_date: string;
}

const mockTeachers: Teacher[] = [
  { uuid: '1', teacher_no: 'TCH-2018-001', name: 'Dr. Rahman Khan', email: 'rahman@edu.com', phone: '01712345601', designation: 'Professor', department: 'CSE', status: 'active', join_date: '2018-01-15' },
  { uuid: '2', teacher_no: 'TCH-2019-002', name: 'Prof. Karim Ahmed', email: 'karim@edu.com', phone: '01712345602', designation: 'Associate Professor', department: 'EEE', status: 'active', join_date: '2019-01-10' },
  { uuid: '3', teacher_no: 'TCH-2020-003', name: 'Dr. Fatema Begum', email: 'fatema@edu.com', phone: '01712345603', designation: 'Assistant Professor', department: 'English', status: 'active', join_date: '2020-01-12' },
  { uuid: '4', teacher_no: 'TCH-2021-004', name: 'Prof. Hasan Ali', email: 'hasan@edu.com', phone: '01712345604', designation: 'Lecturer', department: 'Mathematics', status: 'active', join_date: '2021-01-08' },
  { uuid: '5', teacher_no: 'TCH-2022-005', name: 'Dr. Nusrat Jahan', email: 'nusrat@edu.com', phone: '01712345605', designation: 'Lecturer', department: 'Physics', status: 'on_leave', join_date: '2022-01-05' },
];

const TeacherList: React.FC = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [deptFilter, setDeptFilter] = useState('');

  const filteredTeachers = mockTeachers.filter(teacher => {
    const matchesSearch = teacher.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         teacher.teacher_no.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesStatus = !statusFilter || teacher.status === statusFilter;
    const matchesDept = !deptFilter || teacher.department === deptFilter;
    return matchesSearch && matchesStatus && matchesDept;
  });

  const statusColors: Record<string, string> = {
    active: 'bg-green-100 text-green-700',
    on_leave: 'bg-yellow-100 text-yellow-700',
    inactive: 'bg-gray-100 text-gray-700',
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Teachers</h1>
          <p className="text-gray-500">Manage teacher records</p>
        </div>
        <div className="flex gap-3">
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2">
            <Download className="w-4 h-4" />
            Export
          </button>
          <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
            <Plus className="w-5 h-5" />
            Add Teacher
          </button>
        </div>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div className="flex flex-wrap items-center gap-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by name or ID..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="on_leave">On Leave</option>
            <option value="inactive">Inactive</option>
          </select>
          <select
            value={deptFilter}
            onChange={(e) => setDeptFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">All Departments</option>
            <option value="CSE">CSE</option>
            <option value="EEE">EEE</option>
            <option value="English">English</option>
            <option value="Mathematics">Mathematics</option>
            <option value="Physics">Physics</option>
          </select>
        </div>
      </div>

      {/* Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Designation</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {filteredTeachers.map((teacher) => (
              <tr key={teacher.uuid} className="hover:bg-gray-50">
                <td className="px-6 py-4">
                  <div className="flex items-center gap-3">
                    <div className="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                      <span className="text-sm font-bold text-purple-600">
                        {teacher.name.split(' ').map(n => n[0]).join('')}
                      </span>
                    </div>
                    <span className="font-medium text-gray-900">{teacher.name}</span>
                  </div>
                </td>
                <td className="px-6 py-4 text-sm text-gray-700 font-mono">{teacher.teacher_no}</td>
                <td className="px-6 py-4 text-sm text-gray-700">{teacher.designation}</td>
                <td className="px-6 py-4 text-sm text-gray-700">{teacher.department}</td>
                <td className="px-6 py-4 text-sm text-gray-700">
                  <div className="text-xs text-gray-500">{teacher.email}</div>
                  <div className="text-xs text-gray-500">{teacher.phone}</div>
                </td>
                <td className="px-6 py-4">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${statusColors[teacher.status]}`}>
                    {teacher.status.replace('_', ' ')}
                  </span>
                </td>
                <td className="px-6 py-4 text-right">
                  <div className="flex items-center justify-end gap-2">
                    <button className="p-1 text-gray-400 hover:bg-gray-100 rounded">
                      <Eye className="w-4 h-4" />
                    </button>
                    <button className="p-1 text-blue-600 hover:bg-blue-50 rounded">
                      <Edit2 className="w-4 h-4" />
                    </button>
                    <button className="p-1 text-green-600 hover:bg-green-50 rounded">
                      <QrCode className="w-4 h-4" />
                    </button>
                    <button className="p-1 text-red-600 hover:bg-red-50 rounded">
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Pagination */}
      <div className="flex items-center justify-between">
        <p className="text-sm text-gray-500">Showing {filteredTeachers.length} of {mockTeachers.length} teachers</p>
        <div className="flex gap-2">
          <button className="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Previous</button>
          <button className="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">1</button>
          <button className="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">2</button>
          <button className="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Next</button>
        </div>
      </div>
    </div>
  );
};

export default TeacherList;
