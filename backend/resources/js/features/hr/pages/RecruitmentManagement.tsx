/**
 * Phase 034 - Enterprise HRM System
 * Recruitment Management Page
 */

import { useState, useEffect } from 'react';
import {
  getJobCirculars,
  createJobCircular,
  publishJobCircular,
  closeJobCircular,
  getJobApplications,
  updateApplicationStatus,
} from '../services/hrApi';
import type { JobCircular, JobApplication } from '../types';
import { JOB_CIRCULAR_STATUSES, APPLICANT_STATUSES } from '../types';

export function RecruitmentManagement() {
  const [circulars, setCirculars] = useState<JobCircular[]>([]);
  const [applications, setApplications] = useState<JobApplication[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<'circulars' | 'applications' | 'interviews'>('circulars');

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      setLoading(true);
      const [circularsData, applicationsData] = await Promise.all([
        getJobCirculars({ per_page: 50 }),
        getJobApplications({ per_page: 50 }),
      ]);
      setCirculars(circularsData.data);
      setApplications(applicationsData.data);
    } catch (error) {
      console.error('Failed to fetch recruitment data:', error);
    } finally {
      setLoading(false);
    }
  };

  const handlePublish = async (uuid: string) => {
    try {
      await publishJobCircular(uuid);
      fetchData();
    } catch (error) {
      console.error('Failed to publish circular:', error);
    }
  };

  const handleClose = async (uuid: string) => {
    try {
      await closeJobCircular(uuid);
      fetchData();
    } catch (error) {
      console.error('Failed to close circular:', error);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">Recruitment Management</h1>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Create Job Circular
        </button>
      </div>

      {/* Tabs */}
      <div className="border-b border-gray-200">
        <nav className="-mb-px flex space-x-8">
          {[
            { key: 'circulars', label: 'Job Circulars', count: circulars.length },
            { key: 'applications', label: 'Applications', count: applications.length },
            { key: 'interviews', label: 'Interviews', count: 0 },
          ].map((tab) => (
            <button
              key={tab.key}
              onClick={() => setActiveTab(tab.key as any)}
              className={`pb-4 px-1 border-b-2 font-medium text-sm ${
                activeTab === tab.key
                  ? 'border-blue-500 text-blue-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              }`}
            >
              {tab.label} ({tab.count})
            </button>
          ))}
        </nav>
      </div>

      {/* Job Circulars Table */}
      {activeTab === 'circulars' && (
        <div className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Circular No</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vacancy</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {circulars.map((circular) => (
                <tr key={circular.id}>
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {circular.circular_no}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {circular.title}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {circular.department?.name || '-'}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {circular.vacancy}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {circular.application_deadline || '-'}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className={`px-2 py-1 text-xs font-medium rounded-full ${
                      circular.status === 'published' ? 'bg-green-100 text-green-800' :
                      circular.status === 'draft' ? 'bg-yellow-100 text-yellow-800' :
                      'bg-gray-100 text-gray-800'
                    }`}>
                      {JOB_CIRCULAR_STATUSES[circular.status]}
                    </span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div className="flex gap-2">
                      {circular.status === 'draft' && (
                        <button
                          onClick={() => handlePublish(circular.uuid)}
                          className="text-green-600 hover:text-green-800"
                        >
                          Publish
                        </button>
                      )}
                      {circular.status === 'published' && (
                        <button
                          onClick={() => handleClose(circular.uuid)}
                          className="text-red-600 hover:text-red-800"
                        >
                          Close
                        </button>
                      )}
                      <button className="text-blue-600 hover:text-blue-800">View</button>
                    </div>
                  </td>
                </tr>
              ))}
              {circulars.length === 0 && (
                <tr>
                  <td colSpan={7} className="px-6 py-8 text-center text-gray-500">
                    No job circulars found. Create your first circular.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      )}

      {/* Applications Table */}
      {activeTab === 'applications' && (
        <div className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Application No</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mobile</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {applications.map((application) => (
                <tr key={application.id}>
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {application.application_no}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {application.full_name}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {application.email || '-'}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {application.mobile || '-'}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className={`px-2 py-1 text-xs font-medium rounded-full ${
                      application.applicant_status === 'selected' ? 'bg-green-100 text-green-800' :
                      application.applicant_status === 'rejected' ? 'bg-red-100 text-red-800' :
                      application.applicant_status === 'shortlisted' ? 'bg-blue-100 text-blue-800' :
                      'bg-yellow-100 text-yellow-800'
                    }`}>
                      {APPLICANT_STATUSES[application.applicant_status]}
                    </span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <button className="text-blue-600 hover:text-blue-800">View</button>
                  </td>
                </tr>
              ))}
              {applications.length === 0 && (
                <tr>
                  <td colSpan={6} className="px-6 py-8 text-center text-gray-500">
                    No applications found.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      )}

      {/* Interviews Section */}
      {activeTab === 'interviews' && (
        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center text-gray-500">
          Interview scheduling will be available here.
        </div>
      )}
    </div>
  );
}
