/**
 * Library Dashboard Page
 */

import React, { useEffect } from 'react';
import { useLibraryStore } from '../store/libraryStore';
import { Link } from 'react-router-dom';
import { 
  BookOpen, Users, BookMarked, AlertTriangle, 
  Clock, DollarSign, Download, ArrowUpRight, ArrowDownRight 
} from 'lucide-react';

export const LibraryDashboard: React.FC = () => {
  const { dashboard, dashboardLoading, fetchDashboard } = useLibraryStore();

  useEffect(() => {
    fetchDashboard();
  }, [fetchDashboard]);

  if (dashboardLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
      </div>
    );
  }

  const stats = [
    {
      title: 'Total Books',
      value: dashboard?.total_books ?? 0,
      icon: BookOpen,
      color: 'bg-blue-500',
      change: '+12%',
      changeType: 'up',
      link: '/library/books',
    },
    {
      title: 'Available Books',
      value: dashboard?.available_books ?? 0,
      icon: BookMarked,
      color: 'bg-green-500',
      change: '+5%',
      changeType: 'up',
      link: '/library/books',
    },
    {
      title: 'Issued Books',
      value: dashboard?.issued_books ?? 0,
      icon: Clock,
      color: 'bg-yellow-500',
      change: '-3%',
      changeType: 'down',
      link: '/library/issues',
    },
    {
      title: 'Active Members',
      value: dashboard?.total_members ?? 0,
      icon: Users,
      color: 'bg-purple-500',
      change: '+8%',
      changeType: 'up',
      link: '/library/members',
    },
    {
      title: 'Overdue Books',
      value: dashboard?.overdue_issues ?? 0,
      icon: AlertTriangle,
      color: 'bg-red-500',
      change: dashboard?.overdue_issues ? `${dashboard.overdue_issues}` : '0',
      changeType: 'neutral',
      link: '/library/issues?status=overdue',
    },
    {
      title: 'Pending Fines',
      value: `৳${(dashboard?.pending_fines ?? 0).toLocaleString()}`,
      icon: DollarSign,
      color: 'bg-orange-500',
      change: '-15%',
      changeType: 'down',
      link: '/library/fines',
    },
    {
      title: 'Digital Books',
      value: dashboard?.digital_books ?? 0,
      icon: Download,
      color: 'bg-indigo-500',
      change: '+25%',
      changeType: 'up',
      link: '/library/digital',
    },
    {
      title: 'Reservations',
      value: dashboard?.pending_reservations ?? 0,
      icon: Clock,
      color: 'bg-teal-500',
      change: '+2',
      changeType: 'up',
      link: '/library/reservations',
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Library Dashboard</h1>
          <p className="text-gray-500">Overview of library operations and statistics</p>
        </div>
        <div className="flex gap-3">
          <Link
            to="/library/books/new"
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
          >
            <BookOpen className="w-4 h-4" />
            Add Book
          </Link>
          <Link
            to="/library/members/new"
            className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            <Users className="w-4 h-4" />
            Add Member
          </Link>
        </div>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {stats.map((stat) => (
          <Link
            key={stat.title}
            to={stat.link}
            className="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow"
          >
            <div className="flex items-start justify-between">
              <div className={`${stat.color} p-3 rounded-lg`}>
                <stat.icon className="w-6 h-6 text-white" />
              </div>
              <div className={`flex items-center gap-1 text-sm ${
                stat.changeType === 'up' ? 'text-green-600' : 
                stat.changeType === 'down' ? 'text-red-600' : 'text-gray-500'
              }`}>
                {stat.changeType === 'up' && <ArrowUpRight className="w-4 h-4" />}
                {stat.changeType === 'down' && <ArrowDownRight className="w-4 h-4" />}
                <span>{stat.change}</span>
              </div>
            </div>
            <div className="mt-4">
              <p className="text-3xl font-bold text-gray-900">{stat.value}</p>
              <p className="text-gray-500 text-sm">{stat.title}</p>
            </div>
          </Link>
        ))}
      </div>

      {/* Today's Activity */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Today's Transactions */}
        <div className="bg-white rounded-xl p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Today's Activity</h2>
          <div className="grid grid-cols-2 gap-4">
            <div className="p-4 bg-green-50 rounded-lg">
              <p className="text-2xl font-bold text-green-600">{dashboard?.today_issues ?? 0}</p>
              <p className="text-sm text-green-700">Books Issued</p>
            </div>
            <div className="p-4 bg-blue-50 rounded-lg">
              <p className="text-2xl font-bold text-blue-600">{dashboard?.today_returns ?? 0}</p>
              <p className="text-sm text-blue-700">Books Returned</p>
            </div>
          </div>
          <div className="mt-4 p-4 bg-gray-50 rounded-lg">
            <p className="text-2xl font-bold text-gray-900">{dashboard?.active_issues ?? 0}</p>
            <p className="text-sm text-gray-600">Active Issues</p>
          </div>
        </div>

        {/* Quick Actions */}
        <div className="bg-white rounded-xl p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
          <div className="space-y-3">
            <Link
              to="/library/issue"
              className="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div className="p-2 bg-green-100 rounded-lg">
                <BookOpen className="w-5 h-5 text-green-600" />
              </div>
              <div>
                <p className="font-medium text-gray-900">Issue Book</p>
                <p className="text-sm text-gray-500">Issue a book to a member</p>
              </div>
            </Link>
            <Link
              to="/library/return"
              className="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div className="p-2 bg-blue-100 rounded-lg">
                <BookMarked className="w-5 h-5 text-blue-600" />
              </div>
              <div>
                <p className="font-medium text-gray-900">Return Book</p>
                <p className="text-sm text-gray-500">Process book return</p>
              </div>
            </Link>
            <Link
              to="/library/opac"
              className="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div className="p-2 bg-purple-100 rounded-lg">
                <BookOpen className="w-5 h-5 text-purple-600" />
              </div>
              <div>
                <p className="font-medium text-gray-900">OPAC Search</p>
                <p className="text-sm text-gray-500">Search library catalog</p>
              </div>
            </Link>
            <Link
              to="/library/reports"
              className="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <div className="p-2 bg-orange-100 rounded-lg">
                <DollarSign className="w-5 h-5 text-orange-600" />
              </div>
              <div>
                <p className="font-medium text-gray-900">Fine Collection</p>
                <p className="text-sm text-gray-500">Manage pending fines</p>
              </div>
            </Link>
          </div>
        </div>
      </div>

      {/* Alerts */}
      {(dashboard?.overdue_issues ?? 0) > 0 && (
        <div className="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
          <div className="flex items-center gap-3">
            <AlertTriangle className="w-5 h-5 text-yellow-600" />
            <div>
              <p className="font-medium text-yellow-800">
                {dashboard?.overdue_issues} books are overdue
              </p>
              <p className="text-sm text-yellow-700">
                Please follow up with members to return overdue books
              </p>
            </div>
            <Link
              to="/library/issues?status=overdue"
              className="ml-auto px-4 py-2 bg-yellow-200 text-yellow-800 rounded-lg hover:bg-yellow-300"
            >
              View Overdue
            </Link>
          </div>
        </div>
      )}
    </div>
  );
};
