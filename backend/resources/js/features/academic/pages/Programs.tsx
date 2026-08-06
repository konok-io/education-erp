import React, { useState } from 'react';
import { Plus, Search, Edit2, Trash2, GraduationCap } from 'lucide-react';

interface Program {
  uuid: string;
  name: string;
  code: string;
  level: string;
  department: string;
  duration: number;
  total_credits: number;
  status: 'active' | 'inactive';
}

const mockPrograms: Program[] = [
  { uuid: '1', name: 'B.Sc. in Computer Science', code: 'BSC-CSE', level: 'Bachelor', department: 'CSE', duration: 4, total_credits: 140, status: 'active' },
  { uuid: '2', name: 'B.Sc. in Electrical Engineering', code: 'BSC-EEE', level: 'Bachelor', department: 'EEE', duration: 4, total_credits: 145, status: 'active' },
  { uuid: '3', name: 'BBA', code: 'BBA-GEN', level: 'Bachelor', department: 'BBA', duration: 4, total_credits: 130, status: 'active' },
  { uuid: '4', name: 'M.Sc. in Computer Science', code: 'MSC-CSE', level: 'Master', department: 'CSE', duration: 2, total_credits: 60, status: 'active' },
  { uuid: '5', name: 'B.A. in English', code: 'BA-ENG', level: 'Bachelor', department: 'ENG', duration: 4, total_credits: 120, status: 'active' },
];

const Programs: React.FC = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [showModal, setShowModal] = useState(false);

  const filteredPrograms = mockPrograms.filter(program =>
    program.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    program.code.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Programs</h1>
          <p className="text-gray-500">Manage academic programs and courses</p>
        </div>
        <button
          onClick={() => setShowModal(true)}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2"
        >
          <Plus className="w-5 h-5" />
          Add Program
        </button>
      </div>

      {/* Search */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div className="relative">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
          <input
            type="text"
            placeholder="Search programs..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
      </div>

      {/* Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {filteredPrograms.map((program) => (
          <div key={program.uuid} className="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
            <div className="flex items-start gap-4">
              <div className="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <GraduationCap className="w-6 h-6 text-orange-600" />
              </div>
              <div className="flex-1">
                <div className="flex items-start justify-between">
                  <div>
                    <h3 className="font-semibold text-gray-900">{program.name}</h3>
                    <p className="text-sm text-gray-500">{program.code}</p>
                  </div>
                  <span className={`px-2 py-1 text-xs font-medium rounded-full ${
                    program.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'
                  }`}>
                    {program.status}
                  </span>
                </div>
              </div>
            </div>
            <div className="mt-4 pt-4 border-t border-gray-100 grid grid-cols-3 gap-4 text-center">
              <div>
                <p className="text-xs text-gray-500">Level</p>
                <p className="font-medium text-sm text-gray-900">{program.level}</p>
              </div>
              <div>
                <p className="text-xs text-gray-500">Duration</p>
                <p className="font-medium text-sm text-gray-900">{program.duration} Years</p>
              </div>
              <div>
                <p className="text-xs text-gray-500">Credits</p>
                <p className="font-medium text-sm text-gray-900">{program.total_credits}</p>
              </div>
            </div>
            <div className="mt-4 flex items-center justify-end gap-2">
              <button className="p-1 text-blue-600 hover:bg-blue-50 rounded">
                <Edit2 className="w-4 h-4" />
              </button>
              <button className="p-1 text-red-600 hover:bg-red-50 rounded">
                <Trash2 className="w-4 h-4" />
              </button>
            </div>
          </div>
        ))}
      </div>

      {/* Modal */}
      {showModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
            <h2 className="text-xl font-bold mb-4">Add Program</h2>
            <form className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Program Name</label>
                <input
                  type="text"
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="e.g., B.Sc. in Computer Science"
                />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Code</label>
                  <input
                    type="text"
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="e.g., BSC-CSE"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Level</label>
                  <select className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select Level</option>
                    <option value="Diploma">Diploma</option>
                    <option value="Bachelor">Bachelor</option>
                    <option value="Master">Master</option>
                    <option value="PhD">PhD</option>
                  </select>
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Department</label>
                  <select className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select Department</option>
                    <option value="CSE">CSE</option>
                    <option value="EEE">EEE</option>
                    <option value="BBA">BBA</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Duration (Years)</label>
                  <input
                    type="number"
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="e.g., 4"
                  />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Total Credits</label>
                <input
                  type="number"
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="e.g., 140"
                />
              </div>
              <div className="flex justify-end gap-3 pt-4">
                <button
                  type="button"
                  onClick={() => setShowModal(false)}
                  className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button type="submit" className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                  Create
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default Programs;
