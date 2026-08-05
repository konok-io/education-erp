/**
 * Trips Page
 */

import React, { useEffect, useState } from 'react';
import { useTransportStore } from '../store/transportStore';
import { Link } from 'react-router-dom';
import { Plus, Search, Eye, Calendar, Bus, Users, Play, CheckCircle, X } from 'lucide-react';
import { TRIP_TYPES, TRIP_STATUSES } from '../types';

export const Trips: React.FC = () => {
  const { 
    trips, tripsPagination, tripsLoading, 
    fetchTrips, startTrip, completeTrip, cancelTrip 
  } = useTransportStore();
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [tripDate, setTripDate] = useState('');
  const [page, setPage] = useState(1);

  useEffect(() => {
    const params: any = { page };
    if (search) params.search = search;
    if (statusFilter) params.status = statusFilter;
    if (tripDate) params.trip_date = tripDate;
    else params.trip_date = new Date().toISOString().split('T')[0];
    fetchTrips(params);
  }, [fetchTrips, page, search, statusFilter, tripDate]);

  const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
      scheduled: 'bg-blue-100 text-blue-700',
      started: 'bg-yellow-100 text-yellow-700',
      in_progress: 'bg-yellow-100 text-yellow-700',
      completed: 'bg-green-100 text-green-700',
      cancelled: 'bg-red-100 text-red-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
  };

  const handleStartTrip = async (uuid: string) => {
    const odometer = prompt('Enter start odometer reading:');
    if (odometer) {
      await startTrip(uuid, odometer);
    }
  };

  const handleCompleteTrip = async (uuid: string) => {
    const endOdometer = prompt('Enter end odometer reading:');
    if (endOdometer) {
      const distance = prompt('Enter distance covered (km):', '0');
      const passengers = prompt('Enter passenger count:', '0');
      await completeTrip(uuid, {
        end_odometer: endOdometer,
        distance: parseFloat(distance || '0'),
        passenger_count: parseInt(passengers || '0'),
      });
    }
  };

  const handleCancelTrip = async (uuid: string) => {
    if (confirm('Are you sure you want to cancel this trip?')) {
      await cancelTrip(uuid);
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Trips</h1>
          <p className="text-gray-500">Manage transport trips</p>
        </div>
        <Link
          to="/transport/trips/new"
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
        >
          <Plus className="w-4 h-4" />
          Schedule Trip
        </Link>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by trip number..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
            />
          </div>
          <input
            type="date"
            value={tripDate}
            onChange={(e) => setTripDate(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          />
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Status</option>
            {Object.entries(TRIP_STATUSES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Trips Table */}
      <div className="bg-white rounded-xl shadow-sm overflow-hidden">
        {tripsLoading ? (
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : trips.length === 0 ? (
          <div className="flex flex-col items-center justify-center h-64">
            <Calendar className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No trips found</p>
            <Link
              to="/transport/trips/new"
              className="mt-4 text-primary-600 hover:text-primary-700"
            >
              Schedule your first trip
            </Link>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trip No</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehicle</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Driver</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Route</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {trips.map((trip) => (
                  <tr key={trip.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4">
                      <span className="font-mono font-medium text-gray-900">{trip.trip_no}</span>
                      <p className="text-xs text-gray-500">{TRIP_TYPES[trip.trip_type as keyof typeof TRIP_TYPES] || trip.trip_type}</p>
                    </td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2">
                        <Bus className="w-4 h-4 text-gray-400" />
                        <span className="text-sm text-gray-900">{trip.vehicle?.vehicle_number || '-'}</span>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2">
                        <Users className="w-4 h-4 text-gray-400" />
                        <span className="text-sm text-gray-900">{trip.driver?.full_name || '-'}</span>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <span className="text-sm text-gray-900">{trip.route?.route_name || '-'}</span>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      <p>{trip.start_time || '-'}</p>
                      <p className="text-xs text-gray-400">{trip.end_time || '-'}</p>
                    </td>
                    <td className="px-6 py-4">
                      <span className={`px-2 py-1 text-xs font-medium rounded ${getStatusColor(trip.status)}`}>
                        {TRIP_STATUSES[trip.status as keyof typeof TRIP_STATUSES] || trip.status}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <div className="flex items-center justify-end gap-2">
                        {trip.status === 'scheduled' && (
                          <>
                            <button
                              onClick={() => handleStartTrip(trip.id)}
                              className="p-2 text-green-600 hover:bg-green-50 rounded"
                              title="Start Trip"
                            >
                              <Play className="w-4 h-4" />
                            </button>
                            <button
                              onClick={() => handleCancelTrip(trip.id)}
                              className="p-2 text-red-600 hover:bg-red-50 rounded"
                              title="Cancel Trip"
                            >
                              <X className="w-4 h-4" />
                            </button>
                          </>
                        )}
                        {trip.status === 'started' && (
                          <button
                            onClick={() => handleCompleteTrip(trip.id)}
                            className="p-2 text-green-600 hover:bg-green-50 rounded"
                            title="Complete Trip"
                          >
                            <CheckCircle className="w-4 h-4" />
                          </button>
                        )}
                        <Link
                          to={`/transport/trips/${trip.id}`}
                          className="p-2 text-gray-400 hover:text-primary-600"
                        >
                          <Eye className="w-4 h-4" />
                        </Link>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        {/* Pagination */}
        {tripsPagination && tripsPagination.last_page > 1 && (
          <div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p className="text-sm text-gray-500">
              Showing {((page - 1) * 20) + 1} to {Math.min(page * 20, tripsPagination.total)} of {tripsPagination.total}
            </p>
            <div className="flex gap-2">
              <button
                onClick={() => setPage(page - 1)}
                disabled={page === 1}
                className="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50"
              >
                Previous
              </button>
              <button
                onClick={() => setPage(page + 1)}
                disabled={page === tripsPagination.last_page}
                className="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50"
              >
                Next
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
