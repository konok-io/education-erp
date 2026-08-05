/**
 * Research Dashboard Page
 */

import React, { useEffect } from 'react';
import { useResearchStore } from '../store/researchStore';
import { Link } from 'react-router-dom';
import {
  BookOpen, Users, DollarSign, Award, FileText,
  Lightbulb, GitBranch, TrendingUp, CheckCircle, Clock
} from 'lucide-react';

export const ResearchDashboard: React.FC = () => {
  const { dashboard, dashboardLoading, fetchDashboard } = useResearchStore();

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

  const projectStats = [
    {
      title: 'Total Projects',
      value: dashboard?.total_projects ?? 0,
      icon: BookOpen,
      color: 'bg-blue-500',
      link: '/research/projects',
    },
    {
      title: 'Active Projects',
      value: dashboard?.active_projects ?? 0,
      icon: Clock,
      color: 'bg-green-500',
      link: '/research/projects?status=active',
    },
    {
      title: 'Completed',
      value: dashboard?.completed_projects ?? 0,
      icon: CheckCircle,
      color: 'bg-purple-500',
      link: '/research/projects?status=completed',
    },
    {
      title: 'Researchers',
      value: dashboard?.research_students ?? 0,
      icon: Users,
      color: 'bg-teal-500',
      link: '/research/projects',
    },
  ];

  const publicationStats = [
    {
      title: 'Publications',
      value: dashboard?.total_publications ?? 0,
      icon: FileText,
      color: 'bg-orange-500',
      link: '/research/publications',
    },
    {
      title: 'Citations',
      value: dashboard?.total_citations ?? 0,
      icon: TrendingUp,
      color: 'bg-yellow-500',
      link: '/research/publications',
    },
    {
      title: 'Patents',
      value: dashboard?.total_patents ?? 0,
      icon: Award,
      color: 'bg-red-500',
      link: '/research/patents',
    },
    {
      title: 'Grants',
      value: dashboard?.total_grants ?? 0,
      icon: DollarSign,
      color: 'bg-indigo-500',
      link: '/research/grants',
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Research Dashboard</h1>
          <p className="text-gray-500">Manage research projects, publications & grants</p>
        </div>
        <div className="flex gap-3">
          <Link
            to="/research/projects/new"
            className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
          >
            <BookOpen className="w-4 h-4" />
            New Project
          </Link>
          <Link
            to="/research/publications/new"
            className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
          >
            <FileText className="w-4 h-4" />
            Add Publication
          </Link>
        </div>
      </div>

      {/* Project Stats */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {projectStats.map((stat) => (
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

      {/* Publication Stats */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {publicationStats.map((stat) => (
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

      {/* Quick Actions */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="bg-white rounded-xl p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
          <div className="space-y-3">
            <Link
              to="/research/projects/new"
              className="flex items-center gap-3 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
            >
              <BookOpen className="w-5 h-5 text-blue-600" />
              <span className="font-medium">Create Project</span>
            </Link>
            <Link
              to="/research/grants/new"
              className="flex items-center gap-3 p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
            >
              <DollarSign className="w-5 h-5 text-green-600" />
              <span className="font-medium">Add Grant</span>
            </Link>
            <Link
              to="/research/publications/new"
              className="flex items-center gap-3 p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors"
            >
              <FileText className="w-5 h-5 text-orange-600" />
              <span className="font-medium">Add Publication</span>
            </Link>
            <Link
              to="/research/patents/new"
              className="flex items-center gap-3 p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors"
            >
              <Award className="w-5 h-5 text-purple-600" />
              <span className="font-medium">Register Patent</span>
            </Link>
          </div>
        </div>

        <div className="bg-white rounded-xl p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Research Overview</h2>
          <div className="space-y-4">
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Active Projects</span>
              <span className="font-semibold">{dashboard?.active_projects ?? 0}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Total Funding</span>
              <span className="font-semibold">${(dashboard?.total_funding ?? 0).toLocaleString()}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Published</span>
              <span className="font-semibold">{dashboard?.published_publications ?? 0}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Granted Patents</span>
              <span className="font-semibold">{dashboard?.granted_patents ?? 0}</span>
            </div>
          </div>
        </div>

        <div className="bg-white rounded-xl p-6 shadow-sm">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Innovation & Repository</h2>
          <div className="space-y-4">
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Innovations</span>
              <span className="font-semibold">{dashboard?.total_innovations ?? 0}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Repository Items</span>
              <span className="font-semibold">{dashboard?.repository_items ?? 0}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Theses</span>
              <span className="font-semibold">{dashboard?.total_theses ?? 0}</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-gray-600">Active Grants</span>
              <span className="font-semibold">{dashboard?.active_grants ?? 0}</span>
            </div>
          </div>
        </div>
      </div>

      {/* Navigation Links */}
      <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <Link
          to="/research/projects"
          className="flex flex-col items-center gap-2 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow"
        >
          <BookOpen className="w-8 h-8 text-blue-600" />
          <span className="text-sm font-medium text-gray-700">Projects</span>
        </Link>
        <Link
          to="/research/grants"
          className="flex flex-col items-center gap-2 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow"
        >
          <DollarSign className="w-8 h-8 text-green-600" />
          <span className="text-sm font-medium text-gray-700">Grants</span>
        </Link>
        <Link
          to="/research/publications"
          className="flex flex-col items-center gap-2 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow"
        >
          <FileText className="w-8 h-8 text-orange-600" />
          <span className="text-sm font-medium text-gray-700">Publications</span>
        </Link>
        <Link
          to="/research/patents"
          className="flex flex-col items-center gap-2 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow"
        >
          <Award className="w-8 h-8 text-purple-600" />
          <span className="text-sm font-medium text-gray-700">Patents</span>
        </Link>
        <Link
          to="/research/theses"
          className="flex flex-col items-center gap-2 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow"
        >
          <GitBranch className="w-8 h-8 text-teal-600" />
          <span className="text-sm font-medium text-gray-700">Theses</span>
        </Link>
        <Link
          to="/research/innovations"
          className="flex flex-col items-center gap-2 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow"
        >
          <Lightbulb className="w-8 h-8 text-yellow-600" />
          <span className="text-sm font-medium text-gray-700">Innovations</span>
        </Link>
      </div>
    </div>
  );
};
