import React, { useState } from 'react';
import { Plus, Search, Calendar, Users, CheckCircle, Clock, GraduationCap } from 'lucide-react';

interface Convocation {
  uuid: string;
  name: string;
  date: string;
  venue: string;
  status: 'upcoming' | 'registration_open' | 'registration_closed' | 'completed';
  totalSeats: number;
  registered: number;
}

interface Registration {
  uuid: string;
  alumniName: string;
  studentId: string;
  program: string;
  year: number;
  status: 'pending' | 'confirmed' | 'attended';
  registrationDate: string;
}

const mockConvocation: Convocation[] = [
  { uuid: '1', name: '12th Annual Convocation 2026', date: '2026-03-15', venue: 'Main Auditorium', status: 'upcoming', totalSeats: 500, registered: 0 },
  { uuid: '2', name: '11th Annual Convocation 2025', date: '2025-03-20', venue: 'Main Auditorium', status: 'registration_open', totalSeats: 450, registered: 320 },
  { uuid: '3', name: '10th Annual Convocation 2024', date: '2024-03-18', venue: 'Main Auditorium', status: 'completed', totalSeats: 400, registered: 385 },
];

const mockRegistrations: Registration[] = [
  { uuid: '1', alumniName: 'Rahim Ahmed', studentId: 'STU-2018-001', program: 'B.Sc. CSE', year: 2018, status: 'confirmed', registrationDate: '2025-01-10' },
  { uuid: '2', alumniName: 'Fatema Begum', studentId: 'STU-2019-002', program: 'BBA', year: 2019, status: 'pending', registrationDate: '2025-01-12' },
  { uuid: '3', alumniName: 'Kamal Hossain', studentId: 'STU-2020-003', program: 'B.Sc. EEE', year: 2020, status: 'confirmed', registrationDate: '2025-01-08' },
  { uuid: '4', alumniName: 'Nusrat Jahan', studentId: 'STU-2021-004', program: 'B.A. English', year: 2021, status: 'attended', registrationDate: '2024-01-15' },
];

const statusColors: Record<string, string> = {
  upcoming: 'bg-blue-100 text-blue-700',
  registration_open: 'bg-green-100 text-green-700',
  registration_closed: 'bg-yellow-100 text-yellow-700',
  completed: 'bg-gray-100 text-gray-700',
};

const registrationStatusColors: Record<string, string> = {
  pending: 'bg-yellow-100 text-yellow-700',
  confirmed: 'bg-green-100 text-green-700',
  attended: 'bg-blue-100 text-blue-700',
};

const ConvocationManagement: React.FC = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [showModal, setShowModal] = useState(false);

  const filteredRegistrations = mockRegistrations.filter(reg =>
    reg.alumniName.toLowerCase().includes(searchTerm.toLowerCase()) ||
    reg.studentId.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const upcomingConvs = mockConvocation.filter(c => c.status === 'upcoming' || c.status === 'registration_open');

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Convocation Management</h1>
          <p className="text-gray-500">Manage convocation events and alumni registrations</p>
        </div>
        <button
          onClick={() => setShowModal(true)}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2"
        >
          <Plus className="w-5 h-5" />
          New Convocation
        </button>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <Calendar className="w-5 h-5 text-blue-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Total Convocations</p>
              <p className="text-xl font-bold text-gray-900">{mockConvocation.length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
              <CheckCircle className="w-5 h-5 text-green-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Active Registrations</p>
              <p className="text-xl font-bold text-gray-900">{mockConvocation.filter(c => c.status === 'registration_open').length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
              <Users className="w-5 h-5 text-purple-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Total Registered</p>
              <p className="text-xl font-bold text-gray-900">{mockConvocation.reduce((sum, c) => sum + c.registered, 0)}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
              <GraduationCap className="w-5 h-5 text-yellow-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Pending Review</p>
              <p className="text-xl font-bold text-gray-900">{mockRegistrations.filter(r => r.status === 'pending').length}</p>
            </div>
          </div>
        </div>
      </div>

      {/* Upcoming Convocations */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 className="text-lg font-semibold mb-4">Upcoming Convocations</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {upcomingConvs.map((conv) => (
            <div key={conv.uuid} className="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
              <div className="flex items-start justify-between">
                <div>
                  <h3 className="font-semibold text-gray-900">{conv.name}</h3>
                  <p className="text-sm text-gray-500 mt-1">
                    <Calendar className="w-4 h-4 inline mr-1" />
                    {conv.date} • {conv.venue}
                  </p>
                </div>
                <span className={`px-2 py-1 text-xs font-medium rounded ${statusColors[conv.status]}`}>
                  {conv.status.replace('_', ' ')}
                </span>
              </div>
              <div className="mt-4">
                <div className="flex justify-between text-sm mb-1">
                  <span className="text-gray-500">Registration Progress</span>
                  <span className="font-medium">{conv.registered}/{conv.totalSeats}</span>
                </div>
                <div className="w-full bg-gray-200 rounded-full h-2">
                  <div
                    className="bg-blue-600 h-2 rounded-full"
                    style={{ width: `${(conv.registered / conv.totalSeats) * 100}%` }}
                  />
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Registrations Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-lg font-semibold">Registration Requests</h2>
          <div className="relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input
              type="text"
              placeholder="Search alumni..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alumni</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student ID</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registered</th>
                <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {filteredRegistrations.map((reg) => (
                <tr key={reg.uuid} className="hover:bg-gray-50">
                  <td className="px-4 py-3 text-sm font-medium text-gray-900">{reg.alumniName}</td>
                  <td className="px-4 py-3 text-sm text-gray-700 font-mono">{reg.studentId}</td>
                  <td className="px-4 py-3 text-sm text-gray-700">{reg.program}</td>
                  <td className="px-4 py-3 text-sm text-gray-700">{reg.year}</td>
                  <td className="px-4 py-3">
                    <span className={`px-2 py-1 text-xs font-medium rounded ${registrationStatusColors[reg.status]}`}>
                      {reg.status}
                    </span>
                  </td>
                  <td className="px-4 py-3 text-sm text-gray-700">{reg.registrationDate}</td>
                  <td className="px-4 py-3 text-right">
                    {reg.status === 'pending' && (
                      <div className="flex justify-end gap-2">
                        <button className="px-3 py-1 text-sm bg-green-100 text-green-700 rounded hover:bg-green-200">
                          Confirm
                        </button>
                        <button className="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200">
                          Reject
                        </button>
                      </div>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Modal */}
      {showModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
            <h2 className="text-xl font-bold mb-4">Schedule New Convocation</h2>
            <form className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Convocation Name</label>
                <input
                  type="text"
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="e.g., 13th Annual Convocation 2026"
                />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Date</label>
                  <input
                    type="date"
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Venue</label>
                  <input
                    type="text"
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="e.g., Main Auditorium"
                  />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Total Seats</label>
                <input
                  type="number"
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="500"
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

export default ConvocationManagement;
