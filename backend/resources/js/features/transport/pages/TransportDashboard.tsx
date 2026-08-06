/**
 * Transport Dashboard Page
 */

import React, { useEffect } from 'react';
import { useTransportStore } from '../store/transportStore';
import { Link } from 'react-router-dom';
import { 
  Bus, Users, Route, Calendar, Fuel, Wrench,
  AlertTriangle, Shield, AlertCircle, TrendingUp
} from 'lucide-react';

export const TransportDashboard: React.FC = () => {
  const { dashboard, dashboardLoading, fetchDashboard } = useTransportStore();

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
      title: 'Total Vehicles',
      value: dashboard?.total_vehicles ?? 0,
      icon: Bus,
      color: 'bg-blue-500',
      link: '/transport/vehicles',
    },
    {
      title: 'Active Vehicles',
      value: dashboard?.active_vehicles ?? 0,
      icon: Bus,
      color: 'bg-green-500',
      link: '/transport/vehicles?status=active',
    },
    {
      title: 'Total Drivers',
      value: dashboard?.total_drivers ?? 0,
      icon: Users,
      color: 'bg-purple-500',
      link: '/transport/drivers',
    },
    {
      title: 'Active Routes',
      value: dashboard?.active_routes ?? 0,
      icon: Route,
      color: 'bg-orange-500',
      link: '/transport/routes',
    },
    {
      title: "Today's Trips",
      value: dashboard?.today_trips ?? 0,
      icon: Calendar,
      color: 'bg-indigo-500',
      link: '/transport/trips',
    },
    {
      title: 'Completed Trips',
      value: dashboard?.completed_trips ?? 0,
      icon: Calendar,
      color: 'bg-teal-500',
      link: '/transport/trips?status=completed',
    },
  ];

  const alerts = [
    {
      title: 'Insurance Expiring',
      value: dashboard?.insurance_expiring ?? 0,
      icon: Shield,
      color: 'text-red-600',
      bgColor: 'bg-red-50',
      borderColor: 'border-red-200',
    },
    {
      title: 'License Expiring',
      value: dashboard?.license_expiring ?? 0,
      icon: AlertTriangle,
      color: 'text-yellow-600',
      bgColor: 'bg-yellow-50',
      borderColor: 'border-yellow-200',
    },
    {
      title: 'Maintenance Due',
      value: dashboard?.maintenance_due ?? 0,
      icon: Wrench,
      color: 'text-orange-600',
      bgColor: 'bg-orange-50',
      borderColor: 'border-orange-200',
    },
    {
      title: 'Pending Incidents',
      value: dashboard?.pending_incidents ?? 0,
      icon: AlertCircle,
      color: 'text-purple-600',
      bgColor: 'bg-purple-50',
      borderColor: 'border-purple-200',
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Transport Dashboard</h1>
          <p className="text-gray-500">Overview of transport & vehicle management</p>
        </div>
        <div className="flex gap-3">
          <Link
            to="/transport/vehicles/new"
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
          >
            <Bus className="w-4 h-4" />
            Add Vehicle
          </Link>
          <Link
            to="/transport/drivers/new"
            className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            <Users className="w-4 h-4" />
            Add Driver
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
          <div
            key={alert.title}
            className={`${alert.bgColor} border ${alert.borderColor} rounded-xl p-4`}
          >
            <div className="flex items-center gap-3">
              <alert.icon className={`w-6 h-6 ${alert.color}`} />
              <div>
                <p className="text-2xl font-bold text-gray-900">{alert.value}</p>
                <p className={`text-sm ${alert.color}`}>{alert.title}</p>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Quick Actions & Stats */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Monthly Fuel Cost */}
        <div className="bg-white rounded-xl p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Monthly Fuel Cost</h2>
          <div className="flex items-center justify-between">
            <div>
              <p className="text-4xl font-bold text-gray-900">
                ${(dashboard?.monthly_fuel_cost ?? 0).toLocaleString()}
              </p>
              <p className="text-gray-500">This month</p>
            </div>
            <Link
              to="/transport/fuel"
              className="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700"
            >
              <Fuel className="w-5 h-5" />
              View Details
            </Link>
          </div>
        </div>

        {/* Today's Trip Summary */}
        <div className="bg-white rounded-xl p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Today's Trip Summary</h2>
          <div className="grid grid-cols-2 gap-4">
            <div className="text-center p-4 bg-gray-50 rounded-lg">
              <p className="text-3xl font-bold text-gray-900">{dashboard?.scheduled_trips ?? 0}</p>
              <p className="text-sm text-gray-500">Scheduled</p>
            </div>
            <div className="text-center p-4 bg-green-50 rounded-lg">
              <p className="text-3xl font-bold text-green-600">{dashboard?.completed_trips ?? 0}</p>
              <p className="text-sm text-gray-500">Completed</p>
            </div>
          </div>
          <Link
            to="/transport/trips"
            className="mt-4 flex items-center justify-center gap-2 text-primary-600 hover:text-primary-700"
          >
            <Calendar className="w-5 h-5" />
            View All Trips
          </Link>
        </div>
      </div>

      {/* Vehicle Status */}
      <div className="bg-white rounded-xl p-6 shadow-sm">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Vehicle Status Overview</h2>
        <div className="grid grid-cols-4 gap-4">
          <div className="text-center p-4 bg-green-50 rounded-lg">
            <p className="text-3xl font-bold text-green-600">{dashboard?.active_vehicles ?? 0}</p>
            <p className="text-sm text-gray-500">Active</p>
          </div>
          <div className="text-center p-4 bg-gray-50 rounded-lg">
            <p className="text-3xl font-bold text-gray-600">{dashboard?.inactive_vehicles ?? 0}</p>
            <p className="text-sm text-gray-500">Inactive</p>
          </div>
          <div className="text-center p-4 bg-yellow-50 rounded-lg">
            <p className="text-3xl font-bold text-yellow-600">{dashboard?.under_maintenance ?? 0}</p>
            <p className="text-sm text-gray-500">Maintenance</p>
          </div>
          <div className="text-center p-4 bg-blue-50 rounded-lg">
            <p className="text-3xl font-bold text-blue-600">{dashboard?.total_vehicles ?? 0}</p>
            <p className="text-sm text-gray-500">Total</p>
          </div>
        </div>
      </div>

      {/* Quick Links */}
      <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
        <Link
          to="/transport/vehicles/new"
          className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
        >
          <Bus className="w-5 h-5 text-blue-600" />
          <span className="font-medium">Add Vehicle</span>
        </Link>
        <Link
          to="/transport/drivers/new"
          className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
        >
          <Users className="w-5 h-5 text-purple-600" />
          <span className="font-medium">Add Driver</span>
        </Link>
        <Link
          to="/transport/routes/new"
          className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
        >
          <Route className="w-5 h-5 text-orange-600" />
          <span className="font-medium">Create Route</span>
        </Link>
        <Link
          to="/transport/trips/new"
          className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
        >
          <Calendar className="w-5 h-5 text-indigo-600" />
          <span className="font-medium">Schedule Trip</span>
        </Link>
      </div>
    </div>
  );
};
