/**
 * Research Projects Page
 */

import React, { useEffect, useState } from 'react';
import { useResearchStore } from '../store/researchStore';
import { Link } from 'react-router-dom';
import { Plus, Search, Eye, Edit, Trash2, Check, X, BookOpen } from 'lucide-react';
import { PROJECT_STATUSES, RESEARCH_TYPES } from '../types';

export const ResearchProjects: React.FC = () => {
  const {
    projects, projectsPagination, projectsLoading,
    fetchProjects, deleteProject, approveProject, completeProject
  } = useResearchStore();
  const [search, setSearch] = useState('');
  const [researchTypeFilter, setResearchTypeFilter] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [page, setPage] = useState(1);

  useEffect(() => {
    const params: any = { page, search: search || undefined };
    if (researchTypeFilter) params.research_type = researchTypeFilter;
    if (statusFilter) params.status = statusFilter;
    fetchProjects(params);
  }, [fetchProjects, page, search, researchTypeFilter, statusFilter]);

  const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
      draft: 'bg-gray-100 text-gray-700',
      pending: 'bg-yellow-100 text-yellow-700',
      active: 'bg-green-100 text-green-700',
      completed: 'bg-blue-100 text-blue-700',
      terminated: 'bg-red-100 text-red-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
  };

  const handleDelete = async (uuid: string) => {
    if (confirm('Are you sure you want to delete this project?')) {
      await deleteProject(uuid);
    }
  };

  const handleApprove = async (uuid: string) => {
    if (confirm('Approve this project?')) {
      await approveProject(uuid);
    }
  };

  const handleComplete = async (uuid: string) => {
    if (confirm('Mark this project as completed?')) {
      await completeProject(uuid);
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Research Projects</h1>
          <p className="text-gray-500">Manage research projects and proposals</p>
        </div>
        <Link
          to="/research/projects/new"
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
        >
          <Plus className="w-4 h-4" />
          New Project
        </Link>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by project title or code..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
            />
          </div>
          <select
            value={researchTypeFilter}
            onChange={(e) => setResearchTypeFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Types</option>
            {Object.entries(RESEARCH_TYPES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Status</option>
            {Object.entries(PROJECT_STATUSES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Projects Table */}
      <div className="bg-white rounded-xl shadow-sm overflow-hidden">
        {projectsLoading ? (
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : projects.length === 0 ? (
          <div className="flex flex-col items-center justify-center h-64">
            <BookOpen className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No projects found</p>
            <Link
              to="/research/projects/new"
              className="mt-4 text-primary-600 hover:text-primary-700"
            >
              Create your first project
            </Link>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PI</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Budget</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {projects.map((project) => (
                  <tr key={project.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4">
                      <div>
                        <p className="font-medium text-gray-900">{project.project_title}</p>
                        <p className="text-sm text-gray-500">{project.project_code}</p>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <span className="text-sm text-gray-600">
                        {RESEARCH_TYPES[project.research_type as keyof typeof RESEARCH_TYPES] || project.research_type}
                      </span>
                    </td>
                    <td className="px-6 py-4">
                      <p className="text-sm text-gray-900">
                        {project.principal_investigator?.name || '-'}
                      </p>
                    </td>
                    <td className="px-6 py-4">
                      <p className="text-sm text-gray-900">
                        {project.budget ? `${project.budget_currency || '$'}${project.budget.toLocaleString()}` : '-'}
                      </p>
                    </td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2">
                        <div className="w-20 bg-gray-200 rounded-full h-2">
                          <div
                            className="bg-primary-600 h-2 rounded-full"
                            style={{ width: `${project.progress_percentage}%` }}
                          ></div>
                        </div>
                        <span className="text-sm text-gray-600">{project.progress_percentage}%</span>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <span className={`px-2 py-1 text-xs font-medium rounded ${getStatusColor(project.status)}`}>
                        {PROJECT_STATUSES[project.status as keyof typeof PROJECT_STATUSES] || project.status}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <div className="flex items-center justify-end gap-2">
                        {project.status === 'approved' && (
                          <button
                            onClick={() => handleApprove(project.id)}
                            className="p-2 text-green-600 hover:bg-green-50 rounded"
                            title="Approve"
                          >
                            <Check className="w-4 h-4" />
                          </button>
                        )}
                        {project.status === 'active' && (
                          <button
                            onClick={() => handleComplete(project.id)}
                            className="p-2 text-blue-600 hover:bg-blue-50 rounded"
                            title="Mark Complete"
                          >
                            <Check className="w-4 h-4" />
                          </button>
                        )}
                        <Link
                          to={`/research/projects/${project.id}`}
                          className="p-2 text-gray-400 hover:text-primary-600"
                        >
                          <Eye className="w-4 h-4" />
                        </Link>
                        <Link
                          to={`/research/projects/${project.id}/edit`}
                          className="p-2 text-gray-400 hover:text-primary-600"
                        >
                          <Edit className="w-4 h-4" />
                        </Link>
                        <button
                          onClick={() => handleDelete(project.id)}
                          className="p-2 text-gray-400 hover:text-red-600"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        {/* Pagination */}
        {projectsPagination && projectsPagination.last_page > 1 && (
          <div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p className="text-sm text-gray-500">
              Showing {((page - 1) * 20) + 1} to {Math.min(page * 20, projectsPagination.total)} of {projectsPagination.total}
            </p>
            <div className="flex gap-2">
              <button
                onClick={() => setPage(page - 1)}
                disabled={page === 1}
                className="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50"
              >
                Previous
              </button>
              <button
                onClick={() => setPage(page + 1)}
                disabled={page === projectsPagination.last_page}
                className="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50"
              >
                Next
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
