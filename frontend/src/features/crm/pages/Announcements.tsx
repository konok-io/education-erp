import React, { useState } from 'react';
import { Plus, Search, Bell, Eye, Edit2, Trash2, Send, Pin } from 'lucide-react';

interface Announcement {
  uuid: string;
  title: string;
  type: 'notice' | 'event' | 'holiday' | 'alert';
  content: string;
  targetAudience: string;
  status: 'draft' | 'published' | 'archived';
  publishedAt: string;
  createdBy: string;
  isPinned: boolean;
}

const mockAnnouncements: Announcement[] = [
  { uuid: '1', title: 'Spring Semester Registration', type: 'notice', content: 'Spring semester registration is now open. All students must complete registration by January 20, 2026.', targetAudience: 'All Students', status: 'published', publishedAt: '2026-01-10', createdBy: 'Admin', isPinned: true },
  { uuid: '2', title: 'Eid-ul-Fitr Holiday', type: 'holiday', content: 'University will remain closed from March 20-25 for Eid-ul-Fitr celebration.', targetAudience: 'All', status: 'published', publishedAt: '2026-01-08', createdBy: 'Admin', isPinned: true },
  { uuid: '3', title: 'Mid-term Exam Schedule', type: 'notice', content: 'Mid-term examinations will be held from March 10-15, 2026. Please check your portal for detailed schedule.', targetAudience: 'All Students', status: 'published', publishedAt: '2026-01-05', createdBy: 'Academic Office', isPinned: false },
  { uuid: '4', title: 'Campus Security Alert', type: 'alert', content: 'Please report any suspicious activities to the security office immediately.', targetAudience: 'All', status: 'published', publishedAt: '2026-01-03', createdBy: 'Security', isPinned: false },
  { uuid: '5', title: 'Annual Sports Day', type: 'event', content: 'Annual sports day will be held on February 28, 2026. Registration for events is now open.', targetAudience: 'All Students', status: 'draft', publishedAt: '', createdBy: 'Sports Club', isPinned: false },
];

const typeColors: Record<string, { bg: string; text: string; icon: string }> = {
  notice: { bg: 'bg-blue-100', text: 'text-blue-700', icon: 'Bell' },
  event: { bg: 'bg-purple-100', text: 'text-purple-700', icon: 'Calendar' },
  holiday: { bg: 'bg-green-100', text: 'text-green-700', icon: 'Sun' },
  alert: { bg: 'bg-red-100', text: 'text-red-700', icon: 'AlertTriangle' },
};

const statusColors: Record<string, string> = {
  draft: 'bg-gray-100 text-gray-700',
  published: 'bg-green-100 text-green-700',
  archived: 'bg-yellow-100 text-yellow-700',
};

const Announcements: React.FC = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [typeFilter, setTypeFilter] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [showModal, setShowModal] = useState(false);

  const filteredAnnouncements = mockAnnouncements.filter(ann => {
    const matchesSearch = ann.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         ann.content.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesType = !typeFilter || ann.type === typeFilter;
    const matchesStatus = !statusFilter || ann.status === statusFilter;
    return matchesSearch && matchesType && matchesStatus;
  });

  const pinnedAnnouncements = filteredAnnouncements.filter(ann => ann.isPinned);
  const regularAnnouncements = filteredAnnouncements.filter(ann => !ann.isPinned);

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Announcements</h1>
          <p className="text-gray-500">Manage notices, events, and alerts</p>
        </div>
        <button
          onClick={() => setShowModal(true)}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2"
        >
          <Plus className="w-5 h-5" />
          New Announcement
        </button>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <Bell className="w-5 h-5 text-blue-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Total</p>
              <p className="text-xl font-bold text-gray-900">{mockAnnouncements.length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
              <Send className="w-5 h-5 text-green-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Published</p>
              <p className="text-xl font-bold text-gray-900">{mockAnnouncements.filter(a => a.status === 'published').length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
              <Edit2 className="w-5 h-5 text-yellow-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Draft</p>
              <p className="text-xl font-bold text-gray-900">{mockAnnouncements.filter(a => a.status === 'draft').length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
              <Pin className="w-5 h-5 text-purple-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Pinned</p>
              <p className="text-xl font-bold text-gray-900">{mockAnnouncements.filter(a => a.isPinned).length}</p>
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
              placeholder="Search announcements..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          <select
            value={typeFilter}
            onChange={(e) => setTypeFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">All Types</option>
            <option value="notice">Notice</option>
            <option value="event">Event</option>
            <option value="holiday">Holiday</option>
            <option value="alert">Alert</option>
          </select>
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">All Status</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
            <option value="archived">Archived</option>
          </select>
        </div>
      </div>

      {/* Pinned Announcements */}
      {pinnedAnnouncements.length > 0 && (
        <div className="space-y-4">
          <h3 className="text-lg font-semibold text-gray-900 flex items-center gap-2">
            <Pin className="w-5 h-5 text-blue-600" />
            Pinned Announcements
          </h3>
          <div className="grid gap-4">
            {pinnedAnnouncements.map((announcement) => (
              <div key={announcement.uuid} className="bg-white rounded-xl shadow-sm border border-blue-200 p-6 hover:shadow-md transition-shadow">
                <AnnouncementCard announcement={announcement} typeColors={typeColors} statusColors={statusColors} />
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Regular Announcements */}
      <div className="space-y-4">
        <h3 className="text-lg font-semibold text-gray-900">All Announcements</h3>
        <div className="grid gap-4">
          {regularAnnouncements.map((announcement) => (
            <div key={announcement.uuid} className="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
              <AnnouncementCard announcement={announcement} typeColors={typeColors} statusColors={statusColors} />
            </div>
          ))}
        </div>
      </div>

      {/* Modal */}
      {showModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto">
            <h2 className="text-xl font-bold mb-4">Create New Announcement</h2>
            <form className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input
                  type="text"
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Announcement title..."
                />
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Type</label>
                  <select className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="notice">Notice</option>
                    <option value="event">Event</option>
                    <option value="holiday">Holiday</option>
                    <option value="alert">Alert</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Target Audience</label>
                  <select className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="All">All</option>
                    <option value="All Students">All Students</option>
                    <option value="All Teachers">All Teachers</option>
                    <option value="All Staff">All Staff</option>
                  </select>
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Content</label>
                <textarea
                  rows={5}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Write your announcement..."
                />
              </div>
              <div className="flex items-center gap-4">
                <label className="flex items-center gap-2">
                  <input type="checkbox" className="w-4 h-4 text-blue-600 rounded" />
                  <span className="text-sm text-gray-700">Pin this announcement</span>
                </label>
                <label className="flex items-center gap-2">
                  <input type="checkbox" className="w-4 h-4 text-blue-600 rounded" />
                  <span className="text-sm text-gray-700">Publish immediately</span>
                </label>
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

interface AnnouncementCardProps {
  announcement: Announcement;
  typeColors: Record<string, { bg: string; text: string; icon: string }>;
  statusColors: Record<string, string>;
}

const AnnouncementCard: React.FC<AnnouncementCardProps> = ({ announcement, typeColors, statusColors }) => {
  return (
    <div className="flex gap-4">
      <div className={`w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 ${typeColors[announcement.type].bg}`}>
        <Bell className={`w-6 h-6 ${typeColors[announcement.type].text}`} />
      </div>
      <div className="flex-1">
        <div className="flex items-start justify-between">
          <div>
            <h4 className="font-semibold text-gray-900">{announcement.title}</h4>
            <div className="flex items-center gap-2 mt-1">
              <span className={`px-2 py-0.5 text-xs font-medium rounded ${typeColors[announcement.type].bg} ${typeColors[announcement.type].text}`}>
                {announcement.type}
              </span>
              <span className="text-sm text-gray-500">•</span>
              <span className="text-sm text-gray-500">{announcement.targetAudience}</span>
            </div>
          </div>
          <span className={`px-2 py-1 text-xs font-medium rounded ${statusColors[announcement.status]}`}>
            {announcement.status}
          </span>
        </div>
        <p className="text-sm text-gray-600 mt-2">{announcement.content}</p>
        <div className="flex items-center justify-between mt-3">
          <div className="text-xs text-gray-400">
            By {announcement.createdBy}
            {announcement.publishedAt && ` • ${announcement.publishedAt}`}
          </div>
          <div className="flex items-center gap-2">
            <button className="p-1 text-gray-400 hover:text-blue-600">
              <Eye className="w-4 h-4" />
            </button>
            <button className="p-1 text-gray-400 hover:text-blue-600">
              <Edit2 className="w-4 h-4" />
            </button>
            <button className="p-1 text-gray-400 hover:text-red-600">
              <Trash2 className="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Announcements;
