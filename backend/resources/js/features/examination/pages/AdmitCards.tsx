/**
 * Admit Cards Page
 */

import React, { useEffect, useState } from 'react';
import { useExaminationStore } from '../store/examinationStore';
import { Link } from 'react-router-dom';
import { Plus, Search, Eye, Download, Award, QrCode } from 'lucide-react';
import { ADMIT_CARD_STATUSES } from '../types';

export const AdmitCards: React.FC = () => {
  const { 
    admitCards, admitCardsPagination, admitCardsLoading, 
    fetchAdmitCards 
  } = useExaminationStore();
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [examFilter, setExamFilter] = useState('');
  const [page, setPage] = useState(1);

  useEffect(() => {
    const params: any = { page };
    if (search) params.search = search;
    if (statusFilter) params.status = statusFilter;
    if (examFilter) params.exam_id = examFilter;
    fetchAdmitCards(params);
  }, [fetchAdmitCards, page, search, statusFilter, examFilter]);

  const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
      issued: 'bg-blue-100 text-blue-700',
      downloaded: 'bg-yellow-100 text-yellow-700',
      used: 'bg-green-100 text-green-700',
      expired: 'bg-gray-100 text-gray-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Admit Cards</h1>
          <p className="text-gray-500">Manage examination admit cards</p>
        </div>
        <Link
          to="/examination/admit-cards/generate"
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
        >
          <Plus className="w-4 h-4" />
          Generate Admit Cards
        </Link>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by name, roll or admit card no..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
            />
          </div>
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Status</option>
            {Object.entries(ADMIT_CARD_STATUSES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Admit Cards Table */}
      <div className="bg-white rounded-xl shadow-sm overflow-hidden">
        {admitCardsLoading ? (
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : admitCards.length === 0 ? (
          <div className="flex flex-col items-center justify-center h-64">
            <Award className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No admit cards found</p>
            <Link
              to="/examination/admit-cards/generate"
              className="mt-4 text-primary-600 hover:text-primary-700"
            >
              Generate admit cards
            </Link>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Admit Card</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Issue Date</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {admitCards.map((admitCard) => (
                  <tr key={admitCard.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-green-100 rounded flex items-center justify-center">
                          <Award className="w-5 h-5 text-green-600" />
                        </div>
                        <div>
                          <p className="font-medium text-gray-900">{admitCard.admit_card_no}</p>
                          <p className="text-sm text-gray-500 font-mono">{admitCard.student_roll}</p>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <p className="text-sm text-gray-900">{admitCard.student_name}</p>
                      <p className="text-xs text-gray-500">{admitCard.class_name} {admitCard.section}</p>
                    </td>
                    <td className="px-6 py-4">
                      <p className="text-sm text-gray-900">{admitCard.exam?.exam_name}</p>
                      <p className="text-xs text-gray-500 font-mono">{admitCard.exam?.exam_code}</p>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {admitCard.issue_date}
                    </td>
                    <td className="px-6 py-4">
                      <span className={`px-2 py-1 text-xs font-medium rounded ${getStatusColor(admitCard.status)}`}>
                        {ADMIT_CARD_STATUSES[admitCard.status as keyof typeof ADMIT_CARD_STATUSES] || admitCard.status}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <div className="flex items-center justify-end gap-2">
                        <button
                          className="p-2 text-gray-400 hover:text-primary-600"
                          title="View QR"
                        >
                          <QrCode className="w-4 h-4" />
                        </button>
                        <Link
                          to={`/examination/admit-cards/${admitCard.id}`}
                          className="p-2 text-gray-400 hover:text-primary-600"
                        >
                          <Eye className="w-4 h-4" />
                        </Link>
                        <button
                          className="p-2 text-gray-400 hover:text-green-600"
                          title="Download"
                        >
                          <Download className="w-4 h-4" />
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
        {admitCardsPagination && admitCardsPagination.last_page > 1 && (
          <div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p className="text-sm text-gray-500">
              Showing {((page - 1) * 20) + 1} to {Math.min(page * 20, admitCardsPagination.total)} of {admitCardsPagination.total}
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
                disabled={page === admitCardsPagination.last_page}
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
