import { useState, useEffect } from 'react';
import { getHolidays, createHoliday } from '../services/hrApi';
import type { Holiday } from '../types';
import { HOLIDAY_TYPES } from '../types';

export function HolidayCalendar() {
  const [holidays, setHolidays] = useState<Holiday[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [showForm, setShowForm] = useState(false);
  const [year, setYear] = useState(new Date().getFullYear());
  const [formData, setFormData] = useState({
    name: '',
    holiday_date: '',
    holiday_type: 'national' as const,
    is_repeating: false,
    description: '',
  });

  useEffect(() => {
    fetchHolidays();
  }, [year]);

  const fetchHolidays = async () => {
    try {
      setLoading(true);
      const data = await getHolidays(year);
      setHolidays(data);
      setError(null);
    } catch (err) {
      setError('Failed to load holidays');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await createHoliday(formData);
      setShowForm(false);
      setFormData({
        name: '',
        holiday_date: '',
        holiday_type: 'national',
        is_repeating: false,
        description: '',
      });
      fetchHolidays();
    } catch (err) {
      alert('Failed to create holiday');
      console.error(err);
    }
  };

  const getTypeBadge = (type: string) => {
    const badges: Record<string, string> = {
      weekly: 'bg-gray-100 text-gray-800',
      national: 'bg-red-100 text-red-800',
      religious: 'bg-purple-100 text-purple-800',
      institution: 'bg-blue-100 text-blue-800',
      emergency: 'bg-orange-100 text-orange-800',
    };
    return `px-2 py-1 text-xs font-medium rounded-full ${badges[type] || 'bg-gray-100'}`;
  };

  const months = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];

  const holidaysByMonth = months.map((month, index) => ({
    month,
    holidays: holidays.filter(h => {
      const date = new Date(h.holiday_date);
      return date.getMonth() === index;
    }),
  }));

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">Holiday Calendar</h1>
        <div className="flex gap-2">
          <select
            value={year}
            onChange={(e) => setYear(Number(e.target.value))}
            className="rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
          >
            {Array.from({ length: 5 }, (_, i) => new Date().getFullYear() - 2 + i).map((y) => (
              <option key={y} value={y}>{y}</option>
            ))}
          </select>
          <button
            onClick={() => setShowForm(true)}
            className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            Add Holiday
          </button>
        </div>
      </div>

      {/* Holiday Types */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <h3 className="text-sm font-medium text-gray-700 mb-2">Holiday Types</h3>
        <div className="flex flex-wrap gap-2">
          {Object.entries(HOLIDAY_TYPES).map(([key, label]) => (
            <span key={key} className={getTypeBadge(key)}>
              {label}
            </span>
          ))}
        </div>
      </div>

      {/* Calendar View */}
      {loading ? (
        <div className="flex items-center justify-center h-64">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>
      ) : error ? (
        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
          {error}
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
          {holidaysByMonth.map(({ month, holidays: monthHolidays }) => (
            <div key={month} className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
              <div className="bg-gray-50 px-4 py-2 border-b border-gray-200">
                <h3 className="font-semibold text-gray-900">{month}</h3>
              </div>
              <div className="p-4">
                {monthHolidays.length === 0 ? (
                  <p className="text-sm text-gray-400 text-center">No holidays</p>
                ) : (
                  <div className="space-y-2">
                    {monthHolidays.map((holiday) => (
                      <div key={holiday.id} className="flex items-start gap-2 p-2 bg-gray-50 rounded">
                        <div className="flex-1">
                          <div className="text-sm font-medium text-gray-900">{holiday.name}</div>
                          <div className="text-xs text-gray-500">
                            {new Date(holiday.holiday_date).toLocaleDateString()}
                            {holiday.is_repeating && ' (Yearly)'}
                          </div>
                        </div>
                        <span className={getTypeBadge(holiday.holiday_type)}>
                          {HOLIDAY_TYPES[holiday.holiday_type as keyof typeof HOLIDAY_TYPES]?.slice(0, 3)}
                        </span>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Add Holiday Modal */}
      {showForm && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 className="text-lg font-semibold mb-4">Add Holiday</h3>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input
                  type="text"
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  className="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input
                  type="date"
                  value={formData.holiday_date}
                  onChange={(e) => setFormData({ ...formData, holiday_date: e.target.value })}
                  className="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select
                  value={formData.holiday_type}
                  onChange={(e) => setFormData({ ...formData, holiday_type: e.target.value as any })}
                  className="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                >
                  {Object.entries(HOLIDAY_TYPES).map(([key, label]) => (
                    <option key={key} value={key}>{label}</option>
                  ))}
                </select>
              </div>
              <div className="flex items-center gap-2">
                <input
                  type="checkbox"
                  id="is_repeating"
                  checked={formData.is_repeating}
                  onChange={(e) => setFormData({ ...formData, is_repeating: e.target.checked })}
                  className="rounded border-gray-300"
                />
                <label htmlFor="is_repeating" className="text-sm text-gray-700">
                  Repeats yearly
                </label>
              </div>
              <div className="flex justify-end gap-2 pt-4">
                <button
                  type="button"
                  onClick={() => setShowForm(false)}
                  className="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                  Save
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
