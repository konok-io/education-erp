/**
 * Alumni Directory Page
 */

import React, { useEffect, useState } from 'react';
import { useAlumniStore } from '../store/alumniStore';
import { Link } from 'react-router-dom';
import { Plus, Search, Eye, Edit, Trash2, Users, Check, X, UserPlus } from 'lucide-react';
import { EMPLOYMENT_STATUSES, MEMBERSHIP_TYPES } from '../types';

export const AlumniDirectory: React.FC = () => {
  const {
    alumni, alumniPagination, alumniLoading,
    fetchAlumni, deleteAlumniProfile, verifyAlumniProfile
  } = useAlumniStore();
  const [search, setSearch] = useState('');
  const [departmentFilter, setDepartmentFilter] = useState('');
  const [yearFilter, setYearFilter] = useState('');
  const [membershipFilter, setMembershipFilter] = useState('');
  const [employmentFilter, setEmploymentFilter] = useState('');
  const [page, setPage] = useState(1);

  useEffect(() => {
    const params: any = { page, search: search || undefined };
    if (departmentFilter) params.department = departmentFilter;
    if (yearFilter) params.passing_year = yearFilter;
    if (membershipFilter) params.membership_type = membershipFilter;
    if (employmentFilter) params.employment_status = employmentFilter;
    fetchAlumni(params);
  }, [fetchAlumni, page, search, departmentFilter, yearFilter, membershipFilter, employmentFilter]);

  const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
      active: 'bg-green-100 text-green-700',
      inactive: 'bg-gray-100 text-gray-700',
      suspended: 'bg-red-100 text-red-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
  };

  const getEmploymentColor = (status: string) => {
    const colors: Record<string, string> = {
      employed: 'bg-blue-100 text-blue-700',
      self_employed: 'bg-purple-100 text-purple-700',
      unemployed: 'bg-yellow-100 text-yellow-700',
      student: 'bg-teal-100 text-teal-700',
      retired: 'bg-gray-100 text-gray-700',
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
  };

  const handleDelete = async (uuid: string) => {
    if (confirm('Are you sure you want to delete this alumni profile?')) {
      await deleteAlumniProfile(uuid);
    }
  };

  const handleVerify = async (uuid: string) => {
    if (confirm('Verify this alumni profile?')) {
      await verifyAlumniProfile(uuid);
    }
  };

  const getVerificationStatus = (isVerified: boolean) => {
    return isVerified
      ? { bg: 'bg-green-100', text: 'text-green-700', label: 'Verified' }
      : { bg: 'bg-yellow-100', text: 'text-yellow-700', label: 'Pending' };
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Alumni Directory</h1>
          <p className="text-gray-500">Manage alumni profiles and memberships</p>
        </div>
        <Link
          to="/alumni/register"
          className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
        >
          <Plus className="w-4 h-4" />
          Register Alumni
        </Link>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by name, email or membership number..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
            />
          </div>
          <input
            type="text"
            placeholder="Department"
            value={departmentFilter}
            onChange={(e) => setDepartmentFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          />
          <input
            type="number"
            placeholder="Passing Year"
            value={yearFilter}
            onChange={(e) => setYearFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          />
          <select
            value={membershipFilter}
            onChange={(e) => setMembershipFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Membership</option>
            {Object.entries(MEMBERSHIP_TYPES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
          <select
            value={employmentFilter}
            onChange={(e) => setEmploymentFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Status</option>
            {Object.entries(EMPLOYMENT_STATUSES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Alumni Table */}
      <div className="bg-white rounded-xl shadow-sm overflow-hidden">
        {alumniLoading ? (
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : alumni.length === 0 ? (
          <div className="flex flex-col items-center justify-center h-64">
            <Users className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No alumni found</p>
            <Link
              to="/alumni/register"
              className="mt-4 text-primary-600 hover:text-primary-700"
            >
              Register your first alumni
            </Link>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alumni</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Membership</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employment</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Verification</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {alumni.map((person) => {
                  const verification = getVerificationStatus(person.is_verified);
                  return (
                    <tr key={person.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4">
                        <div className="flex items-center gap-3">
                          <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <span className="text-blue-600 font-semibold">
                              {person.full_name.charAt(0).toUpperCase()}
                            </span>
                          </div>
                          <div>
                            <p className="font-medium text-gray-900">{person.full_name}</p>
                            <p className="text-sm text-gray-500">{person.membership_number}</p>
                          </div>
                        </div>
                      </td>
                      <td className="px-6 py-4">
                        <p className="text-sm text-gray-900">{person.department || '-'}</p>
                        <p className="text-xs text-gray-500">Class of {person.passing_year}</p>
                      </td>
                      <td className="px-6 py-4">
                        <p className="text-sm text-gray-900">{person.email}</p>
                        <p className="text-xs text-gray-500">{person.phone || '-'}</p>
                      </td>
                      <td className="px-6 py-4">
                        <span className={`px-2 py-1 text-xs font-medium rounded ${getEmploymentColor(person.employment_status)}`}>
                          {EMPLOYMENT_STATUSES[person.employment_status as keyof typeof EMPLOYMENT_STATUSES] || person.employment_status}
                        </span>
                        {person.current_organization && (
                          <p className="text-xs text-gray-500 mt-1">{person.current_organization}</p>
                        )}
                      </td>
                      <td className="px-6 py-4">
                        <span className={`px-2 py-1 text-xs font-medium rounded ${verification.bg} ${verification.text}`}>
                          {verification.label}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-right">
                        <div className="flex items-center justify-end gap-2">
                          {!person.is_verified && (
                            <button
                              onClick={() => handleVerify(person.id)}
                              className="p-2 text-green-600 hover:bg-green-50 rounded"
                              title="Verify"
                            >
                              <Check className="w-4 h-4" />
                            </button>
                          )}
                          <Link
                            to={`/alumni/${person.id}`}
                            className="p-2 text-gray-400 hover:text-primary-600"
                          >
                            <Eye className="w-4 h-4" />
                          </Link>
                          <Link
                            to={`/alumni/${person.id}/edit`}
                            className="p-2 text-gray-400 hover:text-primary-600"
                          >
                            <Edit className="w-4 h-4" />
                          </Link>
                          <button
                            onClick={() => handleDelete(person.id)}
                            className="p-2 text-gray-400 hover:text-red-600"
                          >
                            <Trash2 className="w-4 h-4" />
                          </button>
                        </div>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        )}

        {/* Pagination */}
        {alumniPagination && alumniPagination.last_page > 1 && (
          <div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p className="text-sm text-gray-500">
              Showing {((page - 1) * 20) + 1} to {Math.min(page * 20, alumniPagination.total)} of {alumniPagination.total}
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
                disabled={page === alumniPagination.last_page}
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
