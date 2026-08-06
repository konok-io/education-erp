import React, { useState } from 'react';
import { Plus, Calendar, ChevronLeft, ChevronRight } from 'lucide-react';

interface CalendarEvent {
  uuid: string;
  title: string;
  type: 'class' | 'exam' | 'holiday' | 'event' | 'registration';
  start_date: string;
  end_date: string;
  description: string;
}

const mockEvents: CalendarEvent[] = [
  { uuid: '1', title: 'Spring Semester Begins', type: 'class', start_date: '2026-01-15', end_date: '2026-01-15', description: 'First day of spring semester' },
  { uuid: '2', title: 'Mid-term Exam Week', type: 'exam', start_date: '2026-03-10', end_date: '2026-03-15', description: 'Mid-term examinations' },
  { uuid: '3', title: 'Eid-ul-Fitr Holiday', type: 'holiday', start_date: '2026-03-20', end_date: '2026-03-25', description: 'Religious holiday' },
  { uuid: '4', title: 'Orientation Program', type: 'event', start_date: '2026-02-01', end_date: '2026-02-02', description: 'New student orientation' },
  { uuid: '5', title: 'Course Registration', type: 'registration', start_date: '2026-01-10', end_date: '2026-01-14', description: 'Course registration for spring semester' },
];

const eventTypeColors: Record<string, string> = {
  class: 'bg-blue-100 text-blue-700 border-blue-200',
  exam: 'bg-red-100 text-red-700 border-red-200',
  holiday: 'bg-green-100 text-green-700 border-green-200',
  event: 'bg-purple-100 text-purple-700 border-purple-200',
  registration: 'bg-orange-100 text-orange-700 border-orange-200',
};

const AcademicCalendar: React.FC = () => {
  const [currentDate, setCurrentDate] = useState(new Date());
  const [showModal, setShowModal] = useState(false);

  const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

  const prevMonth = () => {
    setCurrentDate(new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1));
  };

  const nextMonth = () => {
    setCurrentDate(new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1));
  };

  const getDaysInMonth = (date: Date) => {
    const year = date.getFullYear();
    const month = date.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDay = firstDay.getDay();
    return { daysInMonth, startingDay };
  };

  const { daysInMonth, startingDay } = getDaysInMonth(currentDate);

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Academic Calendar</h1>
          <p className="text-gray-500">View and manage academic events</p>
        </div>
        <button
          onClick={() => setShowModal(true)}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2"
        >
          <Plus className="w-5 h-5" />
          Add Event
        </button>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Calendar */}
        <div className="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          {/* Month Navigation */}
          <div className="flex items-center justify-between mb-6">
            <button onClick={prevMonth} className="p-2 hover:bg-gray-100 rounded-lg">
              <ChevronLeft className="w-5 h-5" />
            </button>
            <h2 className="text-xl font-semibold">
              {monthNames[currentDate.getMonth()]} {currentDate.getFullYear()}
            </h2>
            <button onClick={nextMonth} className="p-2 hover:bg-gray-100 rounded-lg">
              <ChevronRight className="w-5 h-5" />
            </button>
          </div>

          {/* Days Header */}
          <div className="grid grid-cols-7 gap-1 mb-2">
            {['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map((day) => (
              <div key={day} className="text-center text-sm font-medium text-gray-500 py-2">
                {day}
              </div>
            ))}
          </div>

          {/* Calendar Grid */}
          <div className="grid grid-cols-7 gap-1">
            {Array.from({ length: startingDay }).map((_, i) => (
              <div key={`empty-${i}`} className="h-24 border border-gray-100" />
            ))}
            {Array.from({ length: daysInMonth }).map((_, i) => {
              const day = i + 1;
              const hasEvent = mockEvents.some(event => {
                const eventDate = new Date(event.start_date);
                return eventDate.getDate() === day && eventDate.getMonth() === currentDate.getMonth();
              });
              return (
                <div
                  key={day}
                  className={`h-24 border border-gray-100 p-1 ${hasEvent ? 'bg-blue-50' : ''}`}
                >
                  <span className="text-sm font-medium text-gray-700">{day}</span>
                </div>
              );
            })}
          </div>
        </div>

        {/* Events List */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
          <h3 className="text-lg font-semibold mb-4">Upcoming Events</h3>
          <div className="space-y-4">
            {mockEvents.map((event) => (
              <div
                key={event.uuid}
                className={`p-4 rounded-lg border ${eventTypeColors[event.type]}`}
              >
                <div className="flex items-start justify-between">
                  <div>
                    <p className="font-medium">{event.title}</p>
                    <p className="text-sm opacity-80">{event.description}</p>
                  </div>
                  <Calendar className="w-4 h-4 opacity-50" />
                </div>
                <div className="mt-2 text-sm opacity-80">
                  {event.start_date === event.end_date
                    ? event.start_date
                    : `${event.start_date} - ${event.end_date}`}
                </div>
              </div>
            ))}
          </div>

          {/* Legend */}
          <div className="mt-6 pt-6 border-t border-gray-100">
            <h4 className="text-sm font-medium text-gray-700 mb-3">Legend</h4>
            <div className="space-y-2">
              {Object.entries(eventTypeColors).map(([type, color]) => (
                <div key={type} className="flex items-center gap-2">
                  <div className={`w-4 h-4 rounded ${color}`} />
                  <span className="text-sm text-gray-600 capitalize">{type}</span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      {/* Modal */}
      {showModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
            <h2 className="text-xl font-bold mb-4">Add Calendar Event</h2>
            <form className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Event Title</label>
                <input
                  type="text"
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="e.g., Mid-term Exam"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
                <select className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                  <option value="class">Class</option>
                  <option value="exam">Exam</option>
                  <option value="holiday">Holiday</option>
                  <option value="event">Event</option>
                  <option value="registration">Registration</option>
                </select>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                  <input
                    type="date"
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                  <input
                    type="date"
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea
                  rows={3}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Event description..."
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

export default AcademicCalendar;
