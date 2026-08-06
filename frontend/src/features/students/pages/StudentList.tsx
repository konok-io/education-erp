import React, { useState } from 'react';
import { Plus, Search, Filter, Download, Eye, Edit2, Trash2, MoreVertical, QrCode } from 'lucide-react';

interface Student {
  uuid: string;
  student_no: string;
  name: string;
  email: string;
  phone: string;
  program: string;
  batch: string;
  status: 'active' | 'inactive' | 'graduated' | 'suspended';
  admission_date: string;
  photo?: string;
}

const mockStudents: Student[] = [
  { uuid: '1', student_no: 'STU-2022-001', name: 'Rahim Ahmed', email: 'rahim@email.com', phone: '01712345678', program: 'B.Sc. CSE', batch: '2022', status: 'active', admission_date: '2022-01-15' },
  { uuid: '2', student_no: 'STU-2022-002', name: 'Fatema Begum', email: 'fatema@email.com', phone: '01712345679', program: 'BBA', batch: '2022', status: 'active', admission_date: '2022-01-16' },
  { uuid: '3', student_no: 'STU-2023-001', name: 'Kamal Hossain', email: 'kamal@email.com', phone: '01712345680', program: 'B.Sc. EEE', batch: '2023', status: 'active', admission_date: '2023-01-10' },
  { uuid: '4', student_no: 'STU-2023-002', name: 'Nusrat Jahan', email: 'nusrat@email.com', phone: '01712345681', program: 'B.A. English', batch: '2023', status: 'active', admission_date: '2023-01-12' },
  { uuid: '5', student_no: 'STU-2021-001', name: 'Jamal Uddin', email: 'jamal@email.com', phone: '01712345682', program: 'M.Sc. CSE', batch: '2021', status: 'graduated', admission_date: '2021-01-15' },
];

const StudentList: React.FC = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [programFilter, setProgramFilter] = useState('');
  const [selectedStudent, setSelectedStudent] = useState<Student | null>(null);

  const filteredStudents = mockStudents.filter(student => {
    const matchesSearch = student.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         student.student_no.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         student.email.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesStatus = !statusFilter || student.status === statusFilter;
    const matchesProgram = !programFilter || student.program === programFilter;
    return matchesSearch && matchesStatus && matchesProgram;
  });

  const statusColors: Record<string, string> = {
    active: 'bg-green-100 text-green-700',
    inactive: 'bg-gray-100 text-gray-700',
    graduated: 'bg-blue-100 text-blue-700',
    suspended: 'bg-red-100 text-red-700',
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Students</h1>
          <p className="text-gray-500">Manage student records</p>
        </div>
        <div className="flex gap-3">
          <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2">
            <Download className="w-4 h-4" />
            Export
          </button>
          <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
            <Plus className="w-5 h-5" />
            Add Student
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
              placeholder="Search by name, ID, or email..."
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
            <option value="inactive">Inactive</option>
            <option value="graduated">Graduated</option>
            <option value="suspended">Suspended</option>
          </select>
          <select
            value={programFilter}
            onChange={(e) => setProgramFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">All Programs</option>
            <option value="B.Sc. CSE">B.Sc. CSE</option>
            <option value="BBA">BBA</option>
            <option value="B.Sc. EEE">B.Sc. EEE</option>
            <option value="B.A. English">B.A. English</option>
            <option value="M.Sc. CSE">M.Sc. CSE</option>
          </select>
        </div>
      </div>

      {/* Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {filteredStudents.map((student) => (
              <tr key={student.uuid} className="hover:bg-gray-50">
                <td className="px-6 py-4">
                  <div className="flex items-center gap-3">
                    <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                      <span className="text-sm font-bold text-blue-600">
                        {student.name.split(' ').map(n => n[0]).join('')}
                      </span>
                    </div>
                    <span className="font-medium text-gray-900">{student.name}</span>
                  </div>
                </td>
                <td className="px-6 py-4 text-sm text-gray-700 font-mono">{student.student_no}</td>
                <td className="px-6 py-4 text-sm text-gray-700">{student.program}</td>
                <td className="px-6 py-4 text-sm text-gray-700">{student.batch}</td>
                <td className="px-6 py-4 text-sm text-gray-700">
                  <div className="text-xs text-gray-500">{student.email}</div>
                  <div className="text-xs text-gray-500">{student.phone}</div>
                </td>
                <td className="px-6 py-4">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${statusColors[student.status]}`}>
                    {student.status}
                  </span>
                </td>
                <td className="px-6 py-4 text-right">
                  <div className="flex items-center justify-end gap-2">
                    <button className="p-1 text-gray-400 hover:bg-gray-100 rounded" title="View">
                      <Eye className="w-4 h-4" />
                    </button>
                    <button className="p-1 text-blue-600 hover:bg-blue-50 rounded" title="Edit">
                      <Edit2 className="w-4 h-4" />
                    </button>
                    <button className="p-1 text-green-600 hover:bg-green-50 rounded" title="QR Code">
                      <QrCode className="w-4 h-4" />
                    </button>
                    <button className="p-1 text-red-600 hover:bg-red-50 rounded" title="Delete">
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
        <p className="text-sm text-gray-500">Showing {filteredStudents.length} of {mockStudents.length} students</p>
        <div className="flex gap-2">
          <button className="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Previous</button>
          <button className="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">1</button>
          <button className="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">2</button>
          <button className="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">3</button>
          <button className="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Next</button>
        </div>
      </div>
    </div>
  );
};

export default StudentList;
