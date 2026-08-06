import React, { useState } from 'react';
import { Plus, Search, Edit2, Trash2, MoreVertical } from 'lucide-react';

interface Faculty {
  uuid: string;
  name: string;
  code: string;
  dean: string;
  description: string;
  department_count: number;
  status: 'active' | 'inactive';
}

const mockFaculties: Faculty[] = [
  { uuid: '1', name: 'Faculty of Science', code: 'FOS', dean: 'Dr. Rahman Ahmed', description: 'Natural and Applied Sciences', department_count: 8, status: 'active' },
  { uuid: '2', name: 'Faculty of Arts', code: 'FOA', dean: 'Prof. Karim Hassan', description: 'Humanities and Social Sciences', department_count: 6, status: 'active' },
  { uuid: '3', name: 'Faculty of Business', code: 'FOB', dean: 'Dr. Nasir Uddin', description: 'Business Administration', department_count: 4, status: 'active' },
  { uuid: '4', name: 'Faculty of Engineering', code: 'FOE', dean: 'Prof. Alamgir', description: 'Engineering and Technology', department_count: 5, status: 'active' },
  { uuid: '5', name: 'Faculty of Medicine', code: 'FOM', dean: 'Dr. Shahidullah', description: 'Medical Sciences', department_count: 10, status: 'active' },
  { uuid: '6', name: 'Faculty of Law', code: 'FOL', dean: 'Prof. Haque', description: 'Legal Studies', department_count: 3, status: 'active' },
];

const Faculties: React.FC = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [showModal, setShowModal] = useState(false);
  const [selectedFaculty, setSelectedFaculty] = useState<Faculty | null>(null);

  const filteredFaculties = mockFaculties.filter(faculty =>
    faculty.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    faculty.code.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Faculties</h1>
          <p className="text-gray-500">Manage academic faculties and schools</p>
        </div>
        <button
          onClick={() => {
            setSelectedFaculty(null);
            setShowModal(true);
          }}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2"
        >
          <Plus className="w-5 h-5" />
          Add Faculty
        </button>
      </div>

      {/* Search */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div className="relative">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
          <input
            type="text"
            placeholder="Search faculties..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
      </div>

      {/* Grid View */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {filteredFaculties.map((faculty) => (
          <div key={faculty.uuid} className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
            <div className="p-6">
              <div className="flex items-start justify-between mb-4">
                <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                  <span className="text-lg font-bold text-purple-600">{faculty.code}</span>
                </div>
                <span className={`px-2 py-1 text-xs font-medium rounded-full ${
                  faculty.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'
                }`}>
                  {faculty.status}
                </span>
              </div>
              <h3 className="text-lg font-semibold text-gray-900 mb-1">{faculty.name}</h3>
              <p className="text-sm text-gray-500 mb-4">{faculty.description}</p>
              <div className="space-y-2 text-sm">
                <div className="flex justify-between">
                  <span className="text-gray-500">Dean:</span>
                  <span className="font-medium text-gray-900">{faculty.dean}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-500">Departments:</span>
                  <span className="font-medium text-gray-900">{faculty.department_count}</span>
                </div>
              </div>
            </div>
            <div className="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-2">
              <button className="p-1 text-blue-600 hover:bg-blue-100 rounded">
                <Edit2 className="w-4 h-4" />
              </button>
              <button className="p-1 text-red-600 hover:bg-red-100 rounded">
                <Trash2 className="w-4 h-4" />
              </button>
            </div>
          </div>
        ))}
      </div>

      {/* Modal */}
      {showModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
            <h2 className="text-xl font-bold mb-4">{selectedFaculty ? 'Edit' : 'Add'} Faculty</h2>
            <form className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Faculty Name</label>
                <input
                  type="text"
                  defaultValue={selectedFaculty?.name || ''}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="e.g., Faculty of Science"
                />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Code</label>
                  <input
                    type="text"
                    defaultValue={selectedFaculty?.code || ''}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="e.g., FOS"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Dean Name</label>
                  <input
                    type="text"
                    defaultValue={selectedFaculty?.dean || ''}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="e.g., Dr. Name"
                  />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea
                  rows={3}
                  defaultValue={selectedFaculty?.description || ''}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Brief description..."
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
                <button
                  type="submit"
                  className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                  {selectedFaculty ? 'Update' : 'Create'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default Faculties;
