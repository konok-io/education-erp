/**
 * Hostel Dashboard Page
 */

import React, { useEffect } from 'react';
import { useHostelStore } from '../store/hostelStore';
import { Link } from 'react-router-dom';
import { 
  Building2, Bed, Users, DoorOpen, UserCheck,
  UserX, AlertTriangle, Wrench, Calendar, UserPlus
} from 'lucide-react';

export const HostelDashboard: React.FC = () => {
  const { dashboard, dashboardLoading, fetchDashboard } = useHostelStore();

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
      title: 'Total Hostels',
      value: dashboard?.total_hostels ?? 0,
      icon: Building2,
      color: 'bg-blue-500',
      link: '/hostel/hostels',
    },
    {
      title: 'Total Rooms',
      value: dashboard?.total_rooms ?? 0,
      icon: DoorOpen,
      color: 'bg-green-500',
      link: '/hostel/rooms',
    },
    {
      title: 'Total Beds',
      value: dashboard?.total_beds ?? 0,
      icon: Bed,
      color: 'bg-purple-500',
      link: '/hostel/beds',
    },
    {
      title: 'Occupied Beds',
      value: dashboard?.occupied_beds ?? 0,
      icon: UserCheck,
      color: 'bg-indigo-500',
      link: '/hostel/allocations',
    },
    {
      title: 'Available Beds',
      value: dashboard?.available_beds ?? 0,
      icon: UserX,
      color: 'bg-teal-500',
      link: '/hostel/beds?status=available',
    },
    {
      title: 'Today Visitors',
      value: dashboard?.today_visitors ?? 0,
      icon: Users,
      color: 'bg-orange-500',
      link: '/hostel/visitors',
    },
  ];

  const alerts = [
    {
      title: 'Pending Complaints',
      value: dashboard?.pending_complaints ?? 0,
      icon: AlertTriangle,
      color: 'text-red-600',
      bgColor: 'bg-red-50',
      borderColor: 'border-red-200',
      link: '/hostel/complaints?status=pending',
    },
    {
      title: 'Maintenance Due',
      value: dashboard?.pending_maintenance ?? 0,
      icon: Wrench,
      color: 'text-yellow-600',
      bgColor: 'bg-yellow-50',
      borderColor: 'border-yellow-200',
      link: '/hostel/maintenance?status=pending',
    },
    {
      title: "Today's Check-ins",
      value: dashboard?.today_check_ins ?? 0,
      icon: UserPlus,
      color: 'text-green-600',
      bgColor: 'bg-green-50',
      borderColor: 'border-green-200',
      link: '/hostel/allocations',
    },
    {
      title: "Today's Check-outs",
      value: dashboard?.today_check_outs ?? 0,
      icon: UserX,
      color: 'text-blue-600',
      bgColor: 'bg-blue-50',
      borderColor: 'border-blue-200',
      link: '/hostel/allocations',
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Hostel Dashboard</h1>
          <p className="text-gray-500">Overview of hostel & accommodation management</p>
        </div>
        <div className="flex gap-3">
          <Link
            to="/hostel/hostels/new"
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
          >
            <Building2 className="w-4 h-4" />
            Add Hostel
          </Link>
          <Link
            to="/hostel/allocations/new"
            className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            <UserPlus className="w-4 h-4" />
            New Allocation
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

      {/* Alerts Section */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {alerts.map((alert) => (
          <Link
            key={alert.title}
            to={alert.link}
            className={`${alert.bgColor} border ${alert.borderColor} rounded-xl p-4`}
          >
            <div className="flex items-center gap-3">
              <alert.icon className={`w-6 h-6 ${alert.color}`} />
              <div>
                <p className="text-2xl font-bold text-gray-900">{alert.value}</p>
                <p className={`text-sm ${alert.color}`}>{alert.title}</p>
              </div>
            </div>
          </Link>
        ))}
      </div>

      {/* Bed Occupancy */}
      <div className="bg-white rounded-xl p-6 shadow-sm">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Bed Occupancy Overview</h2>
        <div className="grid grid-cols-3 gap-4">
          <div className="text-center p-4 bg-green-50 rounded-lg">
            <p className="text-3xl font-bold text-green-600">{dashboard?.occupied_beds ?? 0}</p>
            <p className="text-sm text-gray-500">Occupied</p>
          </div>
          <div className="text-center p-4 bg-gray-50 rounded-lg">
            <p className="text-3xl font-bold text-gray-600">{dashboard?.available_beds ?? 0}</p>
            <p className="text-sm text-gray-500">Available</p>
          </div>
          <div className="text-center p-4 bg-blue-50 rounded-lg">
            <p className="text-3xl font-bold text-blue-600">{dashboard?.total_beds ?? 0}</p>
            <p className="text-sm text-gray-500">Total</p>
          </div>
        </div>
        <div className="mt-4">
          <div className="w-full bg-gray-200 rounded-full h-4">
            <div
              className="bg-green-500 h-4 rounded-full"
              style={{
                width: `${dashboard?.total_beds ? Math.round((dashboard.occupied_beds / dashboard.total_beds) * 100) : 0}%`,
              }}
            ></div>
          </div>
          <p className="text-sm text-gray-500 mt-2 text-center">
            {dashboard?.total_beds ? Math.round((dashboard.occupied_beds / dashboard.total_beds) * 100) : 0}% Occupancy Rate
          </p>
        </div>
      </div>

      {/* Quick Actions */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        <Link
          to="/hostel/rooms/new"
          className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
        >
          <DoorOpen className="w-5 h-5 text-blue-600" />
          <span className="font-medium">Add Room</span>
        </Link>
        <Link
          to="/hostel/allocations/new"
          className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
        >
          <UserPlus className="w-5 h-5 text-green-600" />
          <span className="font-medium">New Allocation</span>
        </Link>
        <Link
          to="/hostel/visitors/new"
          className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
        >
          <Users className="w-5 h-5 text-orange-600" />
          <span className="font-medium">Register Visitor</span>
        </Link>
        <Link
          to="/hostel/complaints/new"
          className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
        >
          <AlertTriangle className="w-5 h-5 text-red-600" />
          <span className="font-medium">New Complaint</span>
        </Link>
      </div>
    </div>
  );
};
