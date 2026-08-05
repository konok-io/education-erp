/**
 * Certificates Page
 */

import React, { useEffect, useState } from 'react';
import { useCertificateStore } from '../store/certificateStore';
import { Link } from 'react-router-dom';
import { Plus, Search, Eye, Edit, Trash2, FileText, Check, X, Download } from 'lucide-react';
import { CERTIFICATE_TYPES, CERTIFICATE_STATUSES } from '../types';

export const Certificates: React.FC = () => {
  const {
    certificates, certificatesPagination, certificatesLoading,
    fetchCertificates, deleteCertificate, approveCertificate, issueCertificate, rejectCertificate
  } = useCertificateStore();
  const [search, setSearch] = useState('');
  const [typeFilter, setTypeFilter] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [page, setPage] = useState(1);

  useEffect(() => {
    const params: any = { page, search: search || undefined };
    if (typeFilter) params.certificate_type = typeFilter;
    if (statusFilter) params.status = statusFilter;
    fetchCertificates(params);
  }, [fetchCertificates, page, search, typeFilter, statusFilter]);

  const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
      draft: 'bg-gray-100 text-gray-700',
      pending_approval: 'bg-yellow-100 text-yellow-700',
      approved: 'bg-blue-100 text-blue-700',
      issued: 'bg-green-100 text-green-700',
      rejected: 'bg-red-100 text-red-700',
      revoked: 'bg-orange-100 text-orange-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
  };

  const handleDelete = async (uuid: string) => {
    if (confirm('Are you sure you want to delete this certificate?')) {
      await deleteCertificate(uuid);
    }
  };

  const handleApprove = async (uuid: string) => {
    if (confirm('Approve this certificate?')) {
      await approveCertificate(uuid);
    }
  };

  const handleIssue = async (uuid: string) => {
    if (confirm('Issue this certificate?')) {
      await issueCertificate(uuid);
    }
  };

  const handleReject = async (uuid: string) => {
    const reason = prompt('Enter rejection reason:');
    if (reason) {
      await rejectCertificate(uuid, reason);
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Certificates</h1>
          <p className="text-gray-500">Manage academic certificates</p>
        </div>
        <Link
          to="/certificates/new"
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
        >
          <Plus className="w-4 h-4" />
          Generate Certificate
        </Link>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by number, name or roll..."
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
            {Object.entries(CERTIFICATE_TYPES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Status</option>
            {Object.entries(CERTIFICATE_STATUSES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Certificates Table */}
      <div className="bg-white rounded-xl shadow-sm overflow-hidden">
        {certificatesLoading ? (
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : certificates.length === 0 ? (
          <div className="flex flex-col items-center justify-center h-64">
            <FileText className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No certificates found</p>
            <Link
              to="/certificates/new"
              className="mt-4 text-primary-600 hover:text-primary-700"
            >
              Generate your first certificate
            </Link>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Certificate</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Issue Date</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {certificates.map((cert) => (
                  <tr key={cert.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 bg-blue-100 rounded flex items-center justify-center">
                          <FileText className="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                          <p className="font-medium text-gray-900">{cert.certificate_number}</p>
                          <p className="text-sm text-gray-500 font-mono">{cert.student_roll}</p>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      <span className="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded">
                        {CERTIFICATE_TYPES[cert.certificate_type as keyof typeof CERTIFICATE_TYPES] || cert.certificate_type}
                      </span>
                    </td>
                    <td className="px-6 py-4">
                      <p className="text-sm text-gray-900">{cert.student_name}</p>
                      <p className="text-xs text-gray-500">{cert.department}</p>
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-500">
                      {cert.issue_date || '-'}
                    </td>
                    <td className="px-6 py-4">
                      <span className={`px-2 py-1 text-xs font-medium rounded ${getStatusColor(cert.status)}`}>
                        {CERTIFICATE_STATUSES[cert.status as keyof typeof CERTIFICATE_STATUSES] || cert.status}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <div className="flex items-center justify-end gap-2">
                        {cert.status === 'pending_approval' && (
                          <>
                            <button
                              onClick={() => handleApprove(cert.id)}
                              className="p-2 text-green-600 hover:bg-green-50 rounded"
                              title="Approve"
                            >
                              <Check className="w-4 h-4" />
                            </button>
                            <button
                              onClick={() => handleReject(cert.id)}
                              className="p-2 text-red-600 hover:bg-red-50 rounded"
                              title="Reject"
                            >
                              <X className="w-4 h-4" />
                            </button>
                          </>
                        )}
                        {cert.status === 'approved' && (
                          <button
                            onClick={() => handleIssue(cert.id)}
                            className="p-2 text-blue-600 hover:bg-blue-50 rounded"
                            title="Issue"
                          >
                            <Download className="w-4 h-4" />
                          </button>
                        )}
                        <Link
                          to={`/certificates/${cert.id}`}
                          className="p-2 text-gray-400 hover:text-primary-600"
                        >
                          <Eye className="w-4 h-4" />
                        </Link>
                        <Link
                          to={`/certificates/${cert.id}/edit`}
                          className="p-2 text-gray-400 hover:text-primary-600"
                        >
                          <Edit className="w-4 h-4" />
                        </Link>
                        <button
                          onClick={() => handleDelete(cert.id)}
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
        {certificatesPagination && certificatesPagination.last_page > 1 && (
          <div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p className="text-sm text-gray-500">
              Showing {((page - 1) * 20) + 1} to {Math.min(page * 20, certificatesPagination.total)} of {certificatesPagination.total}
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
                disabled={page === certificatesPagination.last_page}
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
