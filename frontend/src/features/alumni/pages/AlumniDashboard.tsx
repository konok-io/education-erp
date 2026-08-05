/**
 * Alumni Dashboard Page
 */

import React, { useEffect } from 'react';
import { useAlumniStore } from '../store/alumniStore';
import { Link } from 'react-router-dom';
import {
  Users, Briefcase, Building2, Calendar, Heart,
  TrendingUp, Award, CheckCircle, Clock, DollarSign
} from 'lucide-react';

export const AlumniDashboard: React.FC = () => {
  const { dashboard, dashboardLoading, fetchDashboard } = useAlumniStore();

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
      title: 'Total Alumni',
      value: dashboard?.total_alumni ?? 0,
      icon: Users,
      color: 'bg-blue-500',
      link: '/alumni',
    },
    {
      title: 'Verified Alumni',
      value: dashboard?.verified_alumni ?? 0,
      icon: CheckCircle,
      color: 'bg-green-500',
      link: '/alumni?is_verified=true',
    },
    {
      title: 'Active Members',
      value: dashboard?.active_members ?? 0,
      icon: Award,
      color: 'bg-purple-500',
      link: '/alumni?status=active',
    },
    {
      title: 'Open Jobs',
      value: dashboard?.open_jobs ?? 0,
      icon: Briefcase,
      color: 'bg-teal-500',
      link: '/alumni/jobs',
    },
    {
      title: 'Open Internships',
      value: dashboard?.open_internships ?? 0,
      icon: TrendingUp,
      color: 'bg-orange-500',
      link: '/alumni/internships',
    },
    {
      title: 'Total Placements',
      value: dashboard?.total_placements ?? 0,
      icon: Building2,
      color: 'bg-yellow-500',
      link: '/alumni/placements',
    },
  ];

  const overview = [
    {
      title: 'Employers',
      value: dashboard?.total_employers ?? 0,
      icon: Building2,
      color: 'bg-blue-50 text-blue-600',
      link: '/alumni/employers',
    },
    {
      title: 'Events',
      value: dashboard?.total_events ?? 0,
      icon: Calendar,
      color: 'bg-green-50 text-green-600',
      link: '/alumni/events',
    },
    {
      title: 'Upcoming Events',
      value: dashboard?.upcoming_events ?? 0,
      icon: Clock,
      color: 'bg-yellow-50 text-yellow-600',
      link: '/alumni/events?status=upcoming',
    },
    {
      title: 'Total Donations',
      value: `$${(dashboard?.total_donations ?? 0).toLocaleString()}`,
      icon: DollarSign,
      color: 'bg-teal-50 text-teal-600',
      link: '/alumni/donations',
    },
    {
      title: 'Active Campaigns',
      value: dashboard?.active_campaigns ?? 0,
      icon: Heart,
      color: 'bg-red-50 text-red-600',
      link: '/alumni/campaigns',
    },
    {
      title: 'Internships',
      value: dashboard?.total_internships ?? 0,
      icon: Briefcase,
      color: 'bg-purple-50 text-purple-600',
      link: '/alumni/internships',
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Alumni Dashboard</h1>
          <p className="text-gray-500">Manage alumni, careers & placement</p>
        </div>
        <div className="flex gap-3">
          <Link
            to="/alumni/register"
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
          >
            <Users className="w-4 h-4" />
            Register Alumni
          </Link>
          <Link
            to="/alumni/employers/new"
            className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            <Building2 className="w-4 h-4" />
            Add Employer
          </Link>
        </div>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
        {stats.map((stat) => (
          <Link
            key={stat.title}
            to={stat.link}
            className="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow"
          >
            <div className={`${stat.color} p-3 rounded-lg w-fit`}>
              <stat.icon className="w-6 h-6 text-white" />
            </div>
            <div className="mt-4">
              <p className="text-3xl font-bold text-gray-900">{stat.value}</p>
              <p className="text-gray-500 text-sm">{stat.title}</p>
            </div>
          </Link>
        ))}
      </div>

      {/* Overview Section */}
      <div className="bg-white rounded-xl p-6 shadow-sm">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Quick Overview</h2>
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          {overview.map((item) => (
            <Link
              key={item.title}
              to={item.link || '#'}
              className="flex items-center gap-3 p-4 rounded-lg hover:bg-gray-50 transition-colors"
            >
              <item.icon className={`w-6 h-6 ${item.color}`} />
              <div>
                <p className="text-xl font-bold text-gray-900">{item.value}</p>
                <p className="text-sm text-gray-500">{item.title}</p>
              </div>
            </Link>
          ))}
        </div>
      </div>

      {/* Quick Actions */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-xl p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Career Management</h2>
          <div className="grid grid-cols-2 gap-4">
            <Link
              to="/alumni/jobs/new"
              className="flex items-center gap-3 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
            >
              <Briefcase className="w-5 h-5 text-blue-600" />
              <span className="font-medium">Post Job</span>
            </Link>
            <Link
              to="/alumni/internships/new"
              className="flex items-center gap-3 p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
            >
              <TrendingUp className="w-5 h-5 text-green-600" />
              <span className="font-medium">Post Internship</span>
            </Link>
            <Link
              to="/alumni/placements/new"
              className="flex items-center gap-3 p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors"
            >
              <Award className="w-5 h-5 text-yellow-600" />
              <span className="font-medium">Add Placement</span>
            </Link>
            <Link
              to="/alumni/employers"
              className="flex items-center gap-3 p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors"
            >
              <Building2 className="w-5 h-5 text-purple-600" />
              <span className="font-medium">Manage Employers</span>
            </Link>
          </div>
        </div>

        <div className="bg-white rounded-xl p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Events & Engagement</h2>
          <div className="grid grid-cols-2 gap-4">
            <Link
              to="/alumni/events/new"
              className="flex items-center gap-3 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
            >
              <Calendar className="w-5 h-5 text-blue-600" />
              <span className="font-medium">Create Event</span>
            </Link>
            <Link
              to="/alumni/mentorship"
              className="flex items-center gap-3 p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
            >
              <Users className="w-5 h-5 text-green-600" />
              <span className="font-medium">Mentorship</span>
            </Link>
            <Link
              to="/alumni/donations/new"
              className="flex items-center gap-3 p-4 bg-red-50 rounded-lg hover:bg-red-100 transition-colors"
            >
              <Heart className="w-5 h-5 text-red-600" />
              <span className="font-medium">Add Donation</span>
            </Link>
            <Link
              to="/alumni/campaigns/new"
              className="flex items-center gap-3 p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors"
            >
              <TrendingUp className="w-5 h-5 text-orange-600" />
              <span className="font-medium">Create Campaign</span>
            </Link>
          </div>
        </div>
      </div>

      {/* Alumni Statistics */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="bg-white rounded-xl p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Alumni Statistics</h2>
          <div className="space-y-4">
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Total Alumni</span>
              <span className="font-semibold">{dashboard?.total_alumni ?? 0}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Verified Alumni</span>
              <span className="font-semibold">{dashboard?.verified_alumni ?? 0}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Active Members</span>
              <span className="font-semibold">{dashboard?.active_members ?? 0}</span>
            </div>
            <div className="w-full bg-gray-200 rounded-full h-2">
              <div
                className="bg-primary-600 h-2 rounded-full"
                style={{ width: `${dashboard?.total_alumni ? (dashboard.verified_alumni / dashboard.total_alumni) * 100 : 0}%` }}
              ></div>
            </div>
            <p className="text-sm text-gray-500">
              {dashboard?.total_alumni ? Math.round((dashboard.verified_alumni / dashboard.total_alumni) * 100) : 0}% verified
            </p>
          </div>
        </div>

        <div className="bg-white rounded-xl p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Career Opportunities</h2>
          <div className="space-y-4">
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Open Jobs</span>
              <span className="font-semibold">{dashboard?.open_jobs ?? 0}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Open Internships</span>
              <span className="font-semibold">{dashboard?.open_internships ?? 0}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Total Placements</span>
              <span className="font-semibold">{dashboard?.total_placements ?? 0}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Active Employers</span>
              <span className="font-semibold">{dashboard?.total_employers ?? 0}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
