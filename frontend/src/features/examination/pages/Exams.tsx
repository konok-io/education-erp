/**
 * Exams Page
 */

import React, { useEffect, useState } from 'react';
import { useExaminationStore } from '../store/examinationStore';
import { Link } from 'react-router-dom';
import { Plus, Search, Eye, Edit, Trash2, FileText, Send } from 'lucide-react';
import { EXAM_TYPES, EXAM_STATUSES } from '../types';

export const Exams: React.FC = () => {
  const { 
    exams, examsPagination, examsLoading, 
    fetchExams, deleteExam, publishExam 
  } = useExaminationStore();
  const [search, setSearch] = useState('');
  const [typeFilter, setTypeFilter] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [page, setPage] = useState(1);

  useEffect(() => {
    const params: any = { page, search: search || undefined };
    if (typeFilter) params.exam_type = typeFilter;
    if (statusFilter) params.status = statusFilter;
    fetchExams(params);
  }, [fetchExams, page, search, typeFilter, statusFilter]);

  const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
      scheduled: 'bg-blue-100 text-blue-700',
      ongoing: 'bg-yellow-100 text-yellow-700',
      completed: 'bg-green-100 text-green-700',
      cancelled: 'bg-red-100 text-red-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
  };

  const handleDelete = async (uuid: string) => {
    if (confirm('Are you sure you want to delete this exam?')) {
      await deleteExam(uuid);
    }
  };

  const handlePublish = async (uuid: string) => {
    if (confirm('Are you sure you want to publish this exam?')) {
      await publishExam(uuid);
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Examinations</h1>
          <p className="text-gray-500">Manage academic examinations</p>
        </div>
        <Link
          to="/examination/exams/new"
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
        >
          <Plus className="w-4 h-4" />
          Create Exam
        </Link>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by name or code..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
            />
          </div>
          <select
            value={typeFilter}
            onChange={(e) => setTypeFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Types</option>
            {Object.entries(EXAM_TYPES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Status</option>
            {Object.entries(EXAM_STATUSES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Exams Table */}
      <div className="bg-white rounded-xl shadow-sm overflow-hidden">
        {examsLoading ? (
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : exams.length === 0 ? (
          <div className="flex flex-col items-center justify-center h-64">
            <FileText className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No exams found</p>
            <Link
              to="/examination/exams/new"
              className="mt-4 text-primary-600 hover:text-primary-700"
            >
              Create your first exam
            </Link>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Session</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {exams.map((exam) => (
                  <tr key={exam.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-blue-100 rounded flex items-center justify-center">
                          <FileText className="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                          <p className="font-medium text-gray-900">{exam.exam_name}</p>
                          <p className="text-sm text-gray-500 font-mono">{exam.exam_code}</p>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <span className="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded">
                        {EXAM_TYPES[exam.exam_type as keyof typeof EXAM_TYPES] || exam.exam_type}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {exam.exam_session?.session_name || '-'}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      <p>{exam.start_date}</p>
                      <p className="text-xs">to {exam.end_date}</p>
                    </td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-2">
                        <span className={`px-2 py-1 text-xs font-medium rounded ${getStatusColor(exam.status)}`}>
                          {EXAM_STATUSES[exam.status as keyof typeof EXAM_STATUSES] || exam.status}
                        </span>
                        {exam.is_published && (
                          <span className="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded">
                            Published
                          </span>
                        )}
                      </div>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <div className="flex items-center justify-end gap-2">
                        {!exam.is_published && (
                          <button
                            onClick={() => handlePublish(exam.id)}
                            className="p-2 text-green-600 hover:bg-green-50 rounded"
                            title="Publish"
                          >
                            <Send className="w-4 h-4" />
                          </button>
                        )}
                        <Link
                          to={`/examination/exams/${exam.id}`}
                          className="p-2 text-gray-400 hover:text-primary-600"
                        >
                          <Eye className="w-4 h-4" />
                        </Link>
                        <Link
                          to={`/examination/exams/${exam.id}/edit`}
                          className="p-2 text-gray-400 hover:text-primary-600"
                        >
                          <Edit className="w-4 h-4" />
                        </Link>
                        <button
                          onClick={() => handleDelete(exam.id)}
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
        {examsPagination && examsPagination.last_page > 1 && (
          <div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p className="text-sm text-gray-500">
              Showing {((page - 1) * 20) + 1} to {Math.min(page * 20, examsPagination.total)} of {examsPagination.total}
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
                disabled={page === examsPagination.last_page}
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
