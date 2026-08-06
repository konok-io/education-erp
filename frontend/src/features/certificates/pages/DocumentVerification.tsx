import React, { useState } from 'react';
import { Search, FileCheck, Clock, AlertCircle, CheckCircle, XCircle, Eye } from 'lucide-react';

interface Verification {
  uuid: string;
  documentType: string;
  documentNumber: string;
  applicantName: string;
  applicantEmail: string;
  submittedAt: string;
  status: 'pending' | 'verified' | 'rejected';
  verifiedAt?: string;
  verifiedBy?: string;
  notes?: string;
}

const mockVerifications: Verification[] = [
  { uuid: '1', documentType: 'Certificate', documentNumber: 'CERT-2024-001', applicantName: 'Rahim Ahmed', applicantEmail: 'rahim@email.com', submittedAt: '2026-01-15', status: 'verified', verifiedAt: '2026-01-16', verifiedBy: 'Admin', notes: 'All documents verified' },
  { uuid: '2', documentType: 'Transcript', documentNumber: 'TRANS-2024-002', applicantName: 'Fatema Begum', applicantEmail: 'fatema@email.com', submittedAt: '2026-01-14', status: 'pending' },
  { uuid: '3', documentType: 'Marksheet', documentNumber: 'MARK-2024-003', applicantName: 'Kamal Hossain', applicantEmail: 'kamal@email.com', submittedAt: '2026-01-13', status: 'pending' },
  { uuid: '4', documentType: 'Certificate', documentNumber: 'CERT-2024-004', applicantName: 'Nusrat Jahan', applicantEmail: 'nusrat@email.com', submittedAt: '2026-01-12', status: 'rejected', verifiedAt: '2026-01-13', verifiedBy: 'Admin', notes: 'Invalid documents' },
  { uuid: '5', documentType: 'Transcript', documentNumber: 'TRANS-2024-005', applicantName: 'Ali Khan', applicantEmail: 'ali@email.com', submittedAt: '2026-01-11', status: 'verified', verifiedAt: '2026-01-12', verifiedBy: 'Admin' },
];

const statusIcons: Record<string, React.ReactNode> = {
  pending: <Clock className="w-5 h-5 text-yellow-600" />,
  verified: <CheckCircle className="w-5 h-5 text-green-600" />,
  rejected: <XCircle className="w-5 h-5 text-red-600" />,
};

const statusColors: Record<string, string> = {
  pending: 'bg-yellow-100 text-yellow-700',
  verified: 'bg-green-100 text-green-700',
  rejected: 'bg-red-100 text-red-700',
};

const DocumentVerification: React.FC = () => {
  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [showModal, setShowModal] = useState(false);
  const [selectedDoc, setSelectedDoc] = useState<Verification | null>(null);

  const filteredVerifications = mockVerifications.filter(doc => {
    const matchesSearch = doc.applicantName.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         doc.documentNumber.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesStatus = !statusFilter || doc.status === statusFilter;
    return matchesSearch && matchesStatus;
  });

  const handleView = (doc: Verification) => {
    setSelectedDoc(doc);
    setShowModal(true);
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Document Verification</h1>
          <p className="text-gray-500">Verify and manage document requests</p>
        </div>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
          <FileCheck className="w-5 h-5" />
          Verify New Document
        </button>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <FileCheck className="w-5 h-5 text-blue-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Total Requests</p>
              <p className="text-xl font-bold text-gray-900">{mockVerifications.length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
              <Clock className="w-5 h-5 text-yellow-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Pending</p>
              <p className="text-xl font-bold text-gray-900">{mockVerifications.filter(d => d.status === 'pending').length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
              <CheckCircle className="w-5 h-5 text-green-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Verified</p>
              <p className="text-xl font-bold text-gray-900">{mockVerifications.filter(d => d.status === 'verified').length}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
              <AlertCircle className="w-5 h-5 text-red-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Rejected</p>
              <p className="text-xl font-bold text-gray-900">{mockVerifications.filter(d => d.status === 'rejected').length}</p>
            </div>
          </div>
        </div>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div className="flex flex-wrap items-center gap-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by name or document number..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="verified">Verified</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>
      </div>

      {/* Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applicant</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Verified By</th>
              <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {filteredVerifications.map((doc) => (
              <tr key={doc.uuid} className="hover:bg-gray-50">
                <td className="px-6 py-4">
                  <div className="flex items-center gap-3">
                    <div className="w-8 h-8 bg-blue-100 rounded flex items-center justify-center">
                      <FileCheck className="w-4 h-4 text-blue-600" />
                    </div>
                    <div>
                      <p className="text-sm font-medium text-gray-900">{doc.documentType}</p>
                      <p className="text-xs text-gray-500 font-mono">{doc.documentNumber}</p>
                    </div>
                  </div>
                </td>
                <td className="px-6 py-4">
                  <p className="text-sm font-medium text-gray-900">{doc.applicantName}</p>
                  <p className="text-xs text-gray-500">{doc.applicantEmail}</p>
                </td>
                <td className="px-6 py-4 text-sm text-gray-700">{doc.submittedAt}</td>
                <td className="px-6 py-4">
                  <div className="flex items-center gap-2">
                    {statusIcons[doc.status]}
                    <span className={`px-2 py-1 text-xs font-medium rounded ${statusColors[doc.status]}`}>
                      {doc.status}
                    </span>
                  </div>
                </td>
                <td className="px-6 py-4 text-sm text-gray-700">
                  {doc.verifiedBy ? (
                    <div>
                      <p>{doc.verifiedBy}</p>
                      <p className="text-xs text-gray-400">{doc.verifiedAt}</p>
                    </div>
                  ) : (
                    <span className="text-gray-400">-</span>
                  )}
                </td>
                <td className="px-6 py-4 text-right">
                  <div className="flex items-center justify-end gap-2">
                    <button
                      onClick={() => handleView(doc)}
                      className="p-1 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded"
                    >
                      <Eye className="w-4 h-4" />
                    </button>
                    {doc.status === 'pending' && (
                      <>
                        <button className="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200">
                          Verify
                        </button>
                        <button className="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">
                          Reject
                        </button>
                      </>
                    )}
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Modal */}
      {showModal && selectedDoc && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
            <div className="flex items-center justify-between mb-4">
              <h2 className="text-xl font-bold">Document Details</h2>
              <button onClick={() => setShowModal(false)} className="text-gray-400 hover:text-gray-600">
                ✕
              </button>
            </div>
            <div className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="text-xs text-gray-500">Document Type</label>
                  <p className="font-medium">{selectedDoc.documentType}</p>
                </div>
                <div>
                  <label className="text-xs text-gray-500">Document Number</label>
                  <p className="font-medium font-mono">{selectedDoc.documentNumber}</p>
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="text-xs text-gray-500">Applicant Name</label>
                  <p className="font-medium">{selectedDoc.applicantName}</p>
                </div>
                <div>
                  <label className="text-xs text-gray-500">Email</label>
                  <p className="font-medium">{selectedDoc.applicantEmail}</p>
                </div>
              </div>
              <div>
                <label className="text-xs text-gray-500">Status</label>
                <div className="flex items-center gap-2 mt-1">
                  {statusIcons[selectedDoc.status]}
                  <span className={`px-2 py-1 text-xs font-medium rounded ${statusColors[selectedDoc.status]}`}>
                    {selectedDoc.status}
                  </span>
                </div>
              </div>
              {selectedDoc.notes && (
                <div>
                  <label className="text-xs text-gray-500">Notes</label>
                  <p className="text-sm text-gray-700">{selectedDoc.notes}</p>
                </div>
              )}
              {selectedDoc.status === 'pending' && (
                <div className="flex justify-end gap-3 pt-4 border-t">
                  <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Reject
                  </button>
                  <button className="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Verify Document
                  </button>
                </div>
              )}
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default DocumentVerification;
