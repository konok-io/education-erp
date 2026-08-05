/**
 * Issue & Return Page
 */

import React, { useEffect, useState } from 'react';
import { useLibraryStore } from '../store/libraryStore';
import { BookOpen, RotateCcw, Search, Check, AlertTriangle, Clock } from 'lucide-react';

type TabType = 'issue' | 'return';

export const IssueReturn: React.FC = () => {
  const { 
    issues, issuesLoading, fetchIssues, 
    overdueIssues, overdueLoading, fetchOverdueIssues,
    issueBook, returnBook, renewBook,
    fetchMembers, members, 
    fetchBooks, books 
  } = useLibraryStore();
  
  const [activeTab, setActiveTab] = useState<TabType>('issue');
  const [memberSearch, setMemberSearch] = useState('');
  const [bookSearch, setBookSearch] = useState('');
  const [selectedMember, setSelectedMember] = useState<any>(null);
  const [selectedBookCopy, setSelectedBookCopy] = useState<any>(null);
  const [isProcessing, setIsProcessing] = useState(false);
  const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);

  useEffect(() => {
    fetchIssues();
    fetchOverdueIssues();
    fetchMembers({ per_page: 50 });
    fetchBooks({ per_page: 50 });
  }, [fetchIssues, fetchOverdueIssues, fetchMembers, fetchBooks]);

  const handleIssue = async () => {
    if (!selectedMember || !selectedBookCopy) {
      setMessage({ type: 'error', text: 'Please select both member and book' });
      return;
    }

    setIsProcessing(true);
    try {
      await issueBook({
        member_id: selectedMember.id,
        book_copy_id: selectedBookCopy.id,
      });
      setMessage({ type: 'success', text: 'Book issued successfully!' });
      setSelectedMember(null);
      setSelectedBookCopy(null);
      setMemberSearch('');
      setBookSearch('');
      fetchIssues();
      fetchOverdueIssues();
    } catch (error: any) {
      setMessage({ type: 'error', text: error.response?.data?.message || 'Failed to issue book' });
    }
    setIsProcessing(false);
  };

  const handleReturn = async (issueId: string) => {
    setIsProcessing(true);
    try {
      const result = await returnBook(issueId);
      if (result.fine) {
        setMessage({ 
          type: 'success', 
          text: `Book returned! Fine of ৳${result.fine.amount} has been generated.` 
        });
      } else {
        setMessage({ type: 'success', text: 'Book returned successfully!' });
      }
      fetchIssues();
      fetchOverdueIssues();
    } catch (error: any) {
      setMessage({ type: 'error', text: error.response?.data?.message || 'Failed to return book' });
    }
    setIsProcessing(false);
  };

  const handleRenew = async (issueId: string) => {
    setIsProcessing(true);
    try {
      await renewBook(issueId);
      setMessage({ type: 'success', text: 'Book renewed successfully!' });
      fetchIssues();
    } catch (error: any) {
      setMessage({ type: 'error', text: error.response?.data?.message || 'Failed to renew book' });
    }
    setIsProcessing(false);
  };

  const filteredMembers = members.filter(m => 
    m.name.toLowerCase().includes(memberSearch.toLowerCase()) ||
    m.member_no.toLowerCase().includes(memberSearch.toLowerCase())
  );

  const availableCopies = books.flatMap(b => 
    (b.copies || []).filter(c => c.status === 'available').map(c => ({
      ...c,
      bookTitle: b.title,
    }))
  ).filter(c => 
    c.bookTitle.toLowerCase().includes(bookSearch.toLowerCase()) ||
    c.accession_number.toLowerCase().includes(bookSearch.toLowerCase())
  );

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Issue & Return</h1>
          <p className="text-gray-500">Manage book issuance and returns</p>
        </div>
      </div>

      {/* Tabs */}
      <div className="bg-white rounded-xl shadow-sm">
        <div className="border-b border-gray-200">
          <nav className="flex">
            <button
              onClick={() => setActiveTab('issue')}
              className={`px-6 py-4 text-sm font-medium ${
                activeTab === 'issue'
                  ? 'border-b-2 border-primary-600 text-primary-600'
                  : 'text-gray-500 hover:text-gray-700'
              }`}
            >
              <BookOpen className="w-4 h-4 inline mr-2" />
              Issue Book
            </button>
            <button
              onClick={() => setActiveTab('return')}
              className={`px-6 py-4 text-sm font-medium ${
                activeTab === 'return'
                  ? 'border-b-2 border-primary-600 text-primary-600'
                  : 'text-gray-500 hover:text-gray-700'
              }`}
            >
              <RotateCcw className="w-4 h-4 inline mr-2" />
              Return Book
            </button>
          </nav>
        </div>

        {/* Message */}
        {message && (
          <div className={`p-4 m-4 rounded-lg ${
            message.type === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'
          }`}>
            {message.text}
          </div>
        )}

        <div className="p-6">
          {activeTab === 'issue' ? (
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
              {/* Member Selection */}
              <div>
                <h3 className="text-lg font-medium text-gray-900 mb-4">Select Member</h3>
                <div className="relative mb-4">
                  <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                  <input
                    type="text"
                    placeholder="Search by name or member ID..."
                    value={memberSearch}
                    onChange={(e) => setMemberSearch(e.target.value)}
                    className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg"
                  />
                </div>
                <div className="space-y-2 max-h-64 overflow-y-auto">
                  {filteredMembers.slice(0, 10).map((member) => (
                    <button
                      key={member.id}
                      onClick={() => setSelectedMember(member)}
                      className={`w-full p-3 text-left rounded-lg border ${
                        selectedMember?.id === member.id
                          ? 'border-primary-500 bg-primary-50'
                          : 'border-gray-200 hover:border-primary-300'
                      }`}
                    >
                      <p className="font-medium">{member.name}</p>
                      <p className="text-sm text-gray-500">{member.member_no} • {member.member_type}</p>
                    </button>
                  ))}
                </div>
                {selectedMember && (
                  <div className="mt-4 p-4 bg-green-50 rounded-lg">
                    <p className="font-medium text-green-800">Selected: {selectedMember.name}</p>
                    <p className="text-sm text-green-600">
                      Max Books: {selectedMember.max_books} | Days: {selectedMember.max_days} | Fine/Day: ৳{selectedMember.fine_rate}
                    </p>
                  </div>
                )}
              </div>

              {/* Book Selection */}
              <div>
                <h3 className="text-lg font-medium text-gray-900 mb-4">Select Book</h3>
                <div className="relative mb-4">
                  <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                  <input
                    type="text"
                    placeholder="Search by title or accession number..."
                    value={bookSearch}
                    onChange={(e) => setBookSearch(e.target.value)}
                    className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg"
                  />
                </div>
                <div className="space-y-2 max-h-64 overflow-y-auto">
                  {availableCopies.slice(0, 10).map((copy) => (
                    <button
                      key={copy.id}
                      onClick={() => setSelectedBookCopy(copy)}
                      className={`w-full p-3 text-left rounded-lg border ${
                        selectedBookCopy?.id === copy.id
                          ? 'border-primary-500 bg-primary-50'
                          : 'border-gray-200 hover:border-primary-300'
                      }`}
                    >
                      <p className="font-medium">{copy.bookTitle}</p>
                      <p className="text-sm text-gray-500">
                        Accession: {copy.accession_number} | Barcode: {copy.barcode}
                      </p>
                    </button>
                  ))}
                </div>
                {selectedBookCopy && (
                  <div className="mt-4 p-4 bg-blue-50 rounded-lg">
                    <p className="font-medium text-blue-800">Selected: {selectedBookCopy.bookTitle}</p>
                    <p className="text-sm text-blue-600">
                      {selectedBookCopy.accession_number}
                    </p>
                  </div>
                )}
              </div>
            </div>
          ) : (
            <div>
              {/* Overdue Alert */}
              {overdueIssues.length > 0 && (
                <div className="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                  <div className="flex items-center gap-2 text-yellow-800">
                    <AlertTriangle className="w-5 h-5" />
                    <span className="font-medium">{overdueIssues.length} books are overdue</span>
                  </div>
                </div>
              )}

              {/* Current Issues */}
              <div className="space-y-4">
                {issuesLoading ? (
                  <div className="flex items-center justify-center h-32">
                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                  </div>
                ) : issues.filter(i => i.status !== 'returned').length === 0 ? (
                  <div className="text-center py-12 text-gray-500">
                    <Clock className="w-12 h-12 mx-auto mb-4 text-gray-400" />
                    <p>No active issues</p>
                  </div>
                ) : (
                  issues.filter(i => i.status !== 'returned').map((issue) => (
                    <div
                      key={issue.id}
                      className={`p-4 rounded-lg border ${
                        issue.is_overdue ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-white'
                      }`}
                    >
                      <div className="flex items-center justify-between">
                        <div>
                          <p className="font-medium">{issue.book_copy?.book?.title}</p>
                          <p className="text-sm text-gray-500">
                            Issued to: {issue.member?.name} ({issue.member?.member_no})
                          </p>
                          <div className="flex gap-4 mt-2 text-sm">
                            <span>Issue Date: {new Date(issue.issue_date).toLocaleDateString()}</span>
                            <span>Due Date: {new Date(issue.due_date).toLocaleDateString()}</span>
                            {issue.is_overdue && (
                              <span className="text-red-600 font-medium">
                                Overdue by {issue.overdue_days} days
                              </span>
                            )}
                          </div>
                        </div>
                        <div className="flex gap-2">
                          {issue.canRenew && !issue.is_overdue && (
                            <button
                              onClick={() => handleRenew(issue.id)}
                              disabled={isProcessing}
                              className="px-3 py-1 text-sm border border-blue-300 text-blue-600 rounded hover:bg-blue-50 disabled:opacity-50"
                            >
                              Renew
                            </button>
                          )}
                          <button
                            onClick={() => handleReturn(issue.id)}
                            disabled={isProcessing}
                            className="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50"
                          >
                            Return
                          </button>
                        </div>
                      </div>
                    </div>
                  ))
                )}
              </div>
            </div>
          )}
        </div>

        {/* Issue Button */}
        {activeTab === 'issue' && selectedMember && selectedBookCopy && (
          <div className="p-6 border-t border-gray-200 bg-gray-50">
            <button
              onClick={handleIssue}
              disabled={isProcessing}
              className="w-full px-4 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 flex items-center justify-center gap-2"
            >
              {isProcessing ? (
                <>
                  <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                  Processing...
                </>
              ) : (
                <>
                  <Check className="w-5 h-5" />
                  Issue Book
                </>
              )}
            </button>
          </div>
        )}
      </div>
    </div>
  );
};
