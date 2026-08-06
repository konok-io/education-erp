/**
 * Phase 034 - Enterprise HRM System
 * Service Book Page
 */

import { useState, useEffect } from 'react';
import { getServiceBooks, getServiceBookTimeline } from '../services/hrApi';
import type { ServiceBookEntry, ServiceBookTimeline } from '../types';
import { SERVICE_BOOK_EVENT_TYPES } from '../types';

interface ServiceBookProps {
  employeeId?: string;
}

export function ServiceBook({ employeeId }: ServiceBookProps) {
  const [entries, setEntries] = useState<ServiceBookEntry[]>([]);
  const [timeline, setTimeline] = useState<ServiceBookTimeline[]>([]);
  const [loading, setLoading] = useState(true);
  const [viewMode, setViewMode] = useState<'table' | 'timeline'>('timeline');
  const [selectedEventType, setSelectedEventType] = useState<string>('');

  useEffect(() => {
    if (employeeId) {
      fetchEmployeeServiceBook();
    } else {
      fetchServiceBooks();
    }
  }, [employeeId]);

  const fetchServiceBooks = async () => {
    try {
      setLoading(true);
      const params: any = {};
      if (selectedEventType) {
        params.event_type = selectedEventType;
      }
      const response = await getServiceBooks({ ...params, per_page: 100 });
      setEntries(response.data);
    } catch (error) {
      console.error('Failed to fetch service books:', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchEmployeeServiceBook = async () => {
    if (!employeeId) return;
    try {
      setLoading(true);
      const [entriesData, timelineData] = await Promise.all([
        getServiceBooks({ employee_id: employeeId, per_page: 100 }),
        getServiceBookTimeline(employeeId),
      ]);
      setEntries(entriesData.data);
      setTimeline(timelineData);
    } catch (error) {
      console.error('Failed to fetch employee service book:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  const getEventIcon = (eventType: string): string => {
    const icons: Record<string, string> = {
      joining: '🎉',
      promotion: '⬆️',
      transfer: '🔄',
      salary_revision: '💰',
      leave: '🏖️',
      award: '🏆',
      punishment: '⚠️',
      training: '📚',
      performance_review: '📊',
      confirmation: '✅',
      resignation: '👋',
      retirement: '🌅',
      termination: '❌',
      other: '📝',
    };
    return icons[eventType] || '📝';
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">
          {employeeId ? 'Employee Service Book' : 'Service Book Records'}
        </h1>
        <div className="flex gap-2">
          <select
            value={selectedEventType}
            onChange={(e) => setSelectedEventType(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg"
          >
            <option value="">All Events</option>
            {Object.entries(SERVICE_BOOK_EVENT_TYPES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
          <div className="flex border border-gray-300 rounded-lg overflow-hidden">
            <button
              onClick={() => setViewMode('timeline')}
              className={`px-4 py-2 ${viewMode === 'timeline' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'}`}
            >
              Timeline
            </button>
            <button
              onClick={() => setViewMode('table')}
              className={`px-4 py-2 ${viewMode === 'table' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'}`}
            >
              Table
            </button>
          </div>
        </div>
      </div>

      {/* Timeline View */}
      {viewMode === 'timeline' && (
        <div className="relative">
          {timeline.length > 0 ? (
            <div className="ml-8 border-l-2 border-gray-200 space-y-8">
              {timeline.map((item, index) => (
                <div key={item.id} className="relative pl-8">
                  <div className="absolute left-[-2.6rem] w-10 h-10 rounded-full bg-white border-2 border-blue-500 flex items-center justify-center text-xl">
                    {item.icon}
                  </div>
                  <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div className="flex items-center justify-between mb-2">
                      <span className="text-sm font-medium text-blue-600">{item.event_label}</span>
                      <span className="text-sm text-gray-500">{item.date}</span>
                    </div>
                    {item.title && (
                      <h3 className="text-lg font-semibold text-gray-900 mb-1">{item.title}</h3>
                    )}
                    {item.description && (
                      <p className="text-gray-600 mb-2">{item.description}</p>
                    )}
                    {item.approved_by && (
                      <p className="text-sm text-gray-500">Approved by: {item.approved_by}</p>
                    )}
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center text-gray-500">
              No service book entries found.
            </div>
          )}
        </div>
      )}

      {/* Table View */}
      {viewMode === 'table' && (
        <div className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entry No</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event Type</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {entries.map((entry) => (
                <tr key={entry.id}>
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {entry.entry_no}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {entry.entry_date}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className="flex items-center gap-2">
                      <span>{getEventIcon(entry.event_type)}</span>
                      <span className="text-sm text-gray-700">
                        {SERVICE_BOOK_EVENT_TYPES[entry.event_type]}
                      </span>
                    </span>
                  </td>
                  <td className="px-6 py-4 text-sm text-gray-500">
                    {entry.title || '-'}
                  </td>
                  <td className="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                    {entry.description || '-'}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <button className="text-blue-600 hover:text-blue-800">View</button>
                  </td>
                </tr>
              ))}
              {entries.length === 0 && (
                <tr>
                  <td colSpan={6} className="px-6 py-8 text-center text-gray-500">
                    No service book entries found.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
