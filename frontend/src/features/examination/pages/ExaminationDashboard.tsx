/**
 * Examination Dashboard Page
 */

import React, { useEffect } from 'react';
import { useExaminationStore } from '../store/examinationStore';
import { Link } from 'react-router-dom';
import { 
  FileText, BookOpen, Users, Building2, 
  Calendar, CheckCircle, Clock, AlertTriangle,
  ClipboardCheck, Award
} from 'lucide-react';

export const ExaminationDashboard: React.FC = () => {
  const { dashboard, dashboardLoading, fetchDashboard } = useExaminationStore();

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
      title: 'Total Exams',
      value: dashboard?.total_exams ?? 0,
      icon: FileText,
      color: 'bg-blue-500',
      link: '/examination/exams',
    },
    {
      title: 'Ongoing Exams',
      value: dashboard?.ongoing_exams ?? 0,
      icon: Clock,
      color: 'bg-yellow-500',
      link: '/examination/exams?status=ongoing',
    },
    {
      title: 'Upcoming Exams',
      value: dashboard?.upcoming_exams ?? 0,
      icon: Calendar,
      color: 'bg-purple-500',
      link: '/examination/exams?status=scheduled',
    },
    {
      title: 'Completed Exams',
      value: dashboard?.completed_exams ?? 0,
      icon: CheckCircle,
      color: 'bg-green-500',
      link: '/examination/exams?status=completed',
    },
    {
      title: 'Exam Halls',
      value: dashboard?.total_halls ?? 0,
      icon: Building2,
      color: 'bg-indigo-500',
      link: '/examination/halls',
    },
    {
      title: 'Total Seats',
      value: dashboard?.total_seats ?? 0,
      icon: Users,
      color: 'bg-teal-500',
      link: '/examination/halls',
    },
  ];

  const alerts = [
    {
      title: 'Today Exams',
      value: dashboard?.today_exams ?? 0,
      icon: BookOpen,
      color: 'text-blue-600',
      bgColor: 'bg-blue-50',
      borderColor: 'border-blue-200',
    },
    {
      title: 'Pending Marks',
      value: dashboard?.pending_marks ?? 0,
      icon: ClipboardCheck,
      color: 'text-yellow-600',
      bgColor: 'bg-yellow-50',
      borderColor: 'border-yellow-200',
      link: '/examination/marks?status=draft',
    },
    {
      title: 'Pending Admit Cards',
      value: dashboard?.pending_admit_cards ?? 0,
      icon: Award,
      color: 'text-orange-600',
      bgColor: 'bg-orange-50',
      borderColor: 'border-orange-200',
      link: '/examination/admit-cards',
    },
    {
      title: 'Invigilators Today',
      value: dashboard?.total_invigilators ?? 0,
      icon: Users,
      color: 'text-green-600',
      bgColor: 'bg-green-50',
      borderColor: 'border-green-200',
      link: '/examination/invigilators',
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Examination Dashboard</h1>
          <p className="text-gray-500">Overview of examination management</p>
        </div>
        <div className="flex gap-3">
          <Link
            to="/examination/exams/new"
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
          >
            <FileText className="w-4 h-4" />
            Create Exam
          </Link>
          <Link
            to="/examination/halls/new"
            className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            <Building2 className="w-4 h-4" />
            Add Hall
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
            to={alert.link || '#'}
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

      {/* Quick Actions */}
      <div className="bg-white rounded-xl p-6 shadow-sm">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          <Link
            to="/examination/seat-plan/new"
            className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <FileText className="w-5 h-5 text-blue-600" />
            <span className="font-medium">Generate Seat Plan</span>
          </Link>
          <Link
            to="/examination/admit-cards/new"
            className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <Award className="w-5 h-5 text-green-600" />
            <span className="font-medium">Generate Admit Cards</span>
          </Link>
          <Link
            to="/examination/marks/new"
            className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <ClipboardCheck className="w-5 h-5 text-purple-600" />
            <span className="font-medium">Enter Marks</span>
          </Link>
          <Link
            to="/examination/attendance/new"
            className="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <CheckCircle className="w-5 h-5 text-orange-600" />
            <span className="font-medium">Record Attendance</span>
          </Link>
        </div>
      </div>

      {/* Exam Status Overview */}
      <div className="bg-white rounded-xl p-6 shadow-sm">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Exam Overview</h2>
        <div className="grid grid-cols-3 gap-4">
          <div className="text-center p-4 bg-green-50 rounded-lg">
            <p className="text-3xl font-bold text-green-600">{dashboard?.completed_exams ?? 0}</p>
            <p className="text-sm text-gray-500">Completed</p>
          </div>
          <div className="text-center p-4 bg-yellow-50 rounded-lg">
            <p className="text-3xl font-bold text-yellow-600">{dashboard?.ongoing_exams ?? 0}</p>
            <p className="text-sm text-gray-500">Ongoing</p>
          </div>
          <div className="text-center p-4 bg-blue-50 rounded-lg">
            <p className="text-3xl font-bold text-blue-600">{dashboard?.upcoming_exams ?? 0}</p>
            <p className="text-sm text-gray-500">Upcoming</p>
          </div>
        </div>
      </div>
    </div>
  );
};
