import React, { useState } from 'react';
import { Building, Calendar, Users, Wrench, Plus, Search, Eye, Edit2 } from 'lucide-react';

interface Facility {
  uuid: string;
  name: string;
  type: string;
  capacity: number;
  location: string;
  status: 'available' | 'booked' | 'maintenance';
}

interface Booking {
  uuid: string;
  facility: string;
  bookedBy: string;
  date: string;
  time: string;
  status: 'pending' | 'approved' | 'rejected';
}

const mockFacilities: Facility[] = [
  { uuid: '1', name: 'Conference Room A', type: 'Meeting Room', capacity: 50, location: 'Admin Building, 3rd Floor', status: 'available' },
  { uuid: '2', name: 'Seminar Hall', type: 'Auditorium', capacity: 200, location: 'Academic Block', status: 'available' },
  { uuid: '3', name: 'Computer Lab 1', type: 'Lab', capacity: 40, location: 'IT Building', status: 'booked' },
  { uuid: '4', name: 'Sports Complex', type: 'Sports', capacity: 500, location: 'Sports Ground', status: 'maintenance' },
  { uuid: '5', name: 'Auditorium', type: 'Auditorium', capacity: 1000, location: 'Main Building', status: 'available' },
];

const mockBookings: Booking[] = [
  { uuid: '1', facility: 'Conference Room A', bookedBy: 'Dr. Rahman Khan', date: '2026-01-20', time: '10:00 AM - 12:00 PM', status: 'approved' },
  { uuid: '2', facility: 'Seminar Hall', bookedBy: 'Prof. Karim Ahmed', date: '2026-01-21', time: '02:00 PM - 05:00 PM', status: 'pending' },
  { uuid: '3', facility: 'Auditorium', bookedBy: 'Dean - CSE', date: '2026-01-25', time: '09:00 AM - 04:00 PM', status: 'approved' },
];

const statusColors: Record<string, string> = {
  available: 'bg-green-100 text-green-700',
  booked: 'bg-blue-100 text-blue-700',
  maintenance: 'bg-red-100 text-red-700',
};

const bookingStatusColors: Record<string, string> = {
  pending: 'bg-yellow-100 text-yellow-700',
  approved: 'bg-green-100 text-green-700',
  rejected: 'bg-red-100 text-red-700',
};

const FacilityDashboard: React.FC = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [showModal, setShowModal] = useState(false);

  const filteredFacilities = mockFacilities.filter(facility =>
    facility.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    facility.type.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Facility Management</h1>
          <p className="text-gray-500">Manage facilities and bookings</p>
        </div>
        <button
          onClick={() => setShowModal(true)}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2"
        >
          <Plus className="w-5 h-5" />
          Add Facility
        </button>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <Building className="w-5 h-5 text-blue-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Total Facilities</p>
              <p className="text-xl font-bold text-gray-900">{mockFacilities.length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
              <Calendar className="w-5 h-5 text-green-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Available</p>
              <p className="text-xl font-bold text-gray-900">{mockFacilities.filter(f => f.status === 'available').length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
              <Users className="w-5 h-5 text-yellow-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Pending Bookings</p>
              <p className="text-xl font-bold text-gray-900">{mockBookings.filter(b => b.status === 'pending').length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
              <Wrench className="w-5 h-5 text-red-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Maintenance</p>
              <p className="text-xl font-bold text-gray-900">{mockFacilities.filter(f => f.status === 'maintenance').length}</p>
            </div>
          </div>
        </div>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div className="flex flex-wrap items-center gap-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search facilities..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
        </div>
      </div>

      {/* Facilities Grid */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 className="text-lg font-semibold mb-4">All Facilities</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {filteredFacilities.map((facility) => (
            <div key={facility.uuid} className="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
              <div className="flex items-start justify-between">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <Building className="w-5 h-5 text-blue-600" />
                  </div>
                  <div>
                    <h3 className="font-semibold text-gray-900">{facility.name}</h3>
                    <p className="text-sm text-gray-500">{facility.type}</p>
                  </div>
                </div>
                <span className={`px-2 py-1 text-xs font-medium rounded ${statusColors[facility.status]}`}>
                  {facility.status}
                </span>
              </div>
              <div className="mt-4 space-y-2">
                <div className="flex justify-between text-sm">
                  <span className="text-gray-500">Capacity:</span>
                  <span className="font-medium">{facility.capacity} persons</span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-gray-500">Location:</span>
                  <span className="font-medium">{facility.location}</span>
                </div>
              </div>
              <div className="mt-4 flex gap-2">
                <button className="flex-1 px-3 py-2 text-sm bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100">
                  <Eye className="w-4 h-4 inline mr-1" />
                  View
                </button>
                <button className="flex-1 px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                  <Edit2 className="w-4 h-4 inline mr-1" />
                  Edit
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Recent Bookings */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-lg font-semibold">Recent Bookings</h2>
          <button className="text-blue-600 hover:text-blue-700 text-sm">View All</button>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Facility</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booked By</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {mockBookings.map((booking) => (
                <tr key={booking.uuid} className="hover:bg-gray-50">
                  <td className="px-4 py-3 text-sm font-medium text-gray-900">{booking.facility}</td>
                  <td className="px-4 py-3 text-sm text-gray-700">{booking.bookedBy}</td>
                  <td className="px-4 py-3 text-sm text-gray-700">
                    <div>{booking.date}</div>
                    <div className="text-xs text-gray-500">{booking.time}</div>
                  </td>
                  <td className="px-4 py-3">
                    <span className={`px-2 py-1 text-xs font-medium rounded ${bookingStatusColors[booking.status]}`}>
                      {booking.status}
                    </span>
                  </td>
                  <td className="px-4 py-3 text-right">
                    {booking.status === 'pending' && (
                      <div className="flex justify-end gap-2">
                        <button className="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200">
                          Approve
                        </button>
                        <button className="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">
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
            <h2 className="text-xl font-bold mb-4">Add New Facility</h2>
            <form className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Facility Name</label>
                <input
                  type="text"
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="e.g., Conference Room B"
                />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Type</label>
                  <select className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>Meeting Room</option>
                    <option>Auditorium</option>
                    <option>Lab</option>
                    <option>Sports</option>
                    <option>Other</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                  <input
                    type="number"
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="100"
                  />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <input
                  type="text"
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Building name, Floor"
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

export default FacilityDashboard;
