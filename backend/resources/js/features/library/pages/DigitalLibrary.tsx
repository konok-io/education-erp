/**
 * Digital Library Page
 */

import React, { useEffect, useState } from 'react';
import { useLibraryStore } from '../store/libraryStore';
import { Download, Eye, Search, Monitor, Headphones, FileText } from 'lucide-react';
import { FILE_TYPES, ACCESS_TYPES } from '../types';

export const DigitalLibrary: React.FC = () => {
  const { 
    digitalBooks, digitalBooksPagination, digitalBooksLoading,
    fetchDigitalBooks, fetchCategories, categories 
  } = useLibraryStore();
  const [search, setSearch] = useState('');
  const [typeFilter, setTypeFilter] = useState('');
  const [accessFilter, setAccessFilter] = useState('');
  const [categoryFilter, setCategoryFilter] = useState('');
  const [page, setPage] = useState(1);
  const [selectedBook, setSelectedBook] = useState<any>(null);

  useEffect(() => {
    fetchCategories();
  }, [fetchCategories]);

  useEffect(() => {
    fetchDigitalBooks({ 
      page, 
      search: search || undefined,
      file_type: typeFilter || undefined,
      access_type: accessFilter || undefined,
      category_id: categoryFilter || undefined,
    });
  }, [fetchDigitalBooks, page, search, typeFilter, accessFilter, categoryFilter]);

  const getFileIcon = (fileType: string) => {
    switch (fileType) {
      case 'pdf':
      case 'docx':
        return FileText;
      case 'audio':
        return Headphones;
      case 'video':
        return Monitor;
      default:
        return FileText;
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Digital Library</h1>
          <p className="text-gray-500">Access e-books, audio books, and video lectures</p>
        </div>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by title, author, or ISBN..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            />
          </div>
          <select
            value={typeFilter}
            onChange={(e) => setTypeFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Types</option>
            {Object.entries(FILE_TYPES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
          <select
            value={accessFilter}
            onChange={(e) => setAccessFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Access</option>
            {Object.entries(ACCESS_TYPES).map(([key, label]) => (
              <option key={key} value={key}>{label}</option>
            ))}
          </select>
          <select
            value={categoryFilter}
            onChange={(e) => setCategoryFilter(e.target.value)}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Categories</option>
            {categories.map((cat) => (
              <option key={cat.id} value={cat.id}>{cat.name}</option>
            ))}
          </select>
        </div>
      </div>

      {/* Books Grid */}
      <div className="bg-white rounded-xl shadow-sm p-6">
        {digitalBooksLoading ? (
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : digitalBooks.length === 0 ? (
          <div className="flex flex-col items-center justify-center h-64">
            <FileText className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No digital books found</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {digitalBooks.map((book) => {
              const FileIcon = getFileIcon(book.file_type);
              return (
                <div
                  key={book.id}
                  className="bg-gray-50 rounded-xl p-4 hover:shadow-md transition-shadow cursor-pointer"
                  onClick={() => setSelectedBook(book)}
                >
                  <div className="aspect-[3/4] bg-white rounded-lg mb-4 flex items-center justify-center overflow-hidden">
                    {book.cover_image ? (
                      <img
                        src={book.cover_image}
                        alt={book.title}
                        className="w-full h-full object-cover"
                      />
                    ) : (
                      <FileIcon className="w-16 h-16 text-gray-400" />
                    )}
                  </div>
                  <h3 className="font-medium text-gray-900 line-clamp-2">{book.title}</h3>
                  <p className="text-sm text-gray-500 mt-1">{book.author_name || 'Unknown Author'}</p>
                  <div className="flex items-center gap-2 mt-3">
                    <span className="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded">
                      {FILE_TYPES[book.file_type as keyof typeof FILE_TYPES] || book.file_type}
                    </span>
                    <span className={`px-2 py-1 text-xs font-medium rounded ${
                      book.access_type === 'public' ? 'bg-green-100 text-green-700' :
                      book.access_type === 'members' ? 'bg-blue-100 text-blue-700' :
                      'bg-purple-100 text-purple-700'
                    }`}>
                      {ACCESS_TYPES[book.access_type as keyof typeof ACCESS_TYPES] || book.access_type}
                    </span>
                  </div>
                  <div className="flex items-center justify-between mt-4 text-sm text-gray-500">
                    <span>{book.view_count} views</span>
                    <span>{book.download_count} downloads</span>
                  </div>
                </div>
              );
            })}
          </div>
        )}

        {/* Pagination */}
        {digitalBooksPagination && digitalBooksPagination.last_page > 1 && (
          <div className="mt-6 flex items-center justify-between">
            <p className="text-sm text-gray-500">
              Showing {((page - 1) * 20) + 1} to {Math.min(page * 20, digitalBooksPagination.total)} of {digitalBooksPagination.total}
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
                disabled={page === digitalBooksPagination.last_page}
                className="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50"
              >
                Next
              </button>
            </div>
          </div>
        )}
      </div>

      {/* Book Detail Modal */}
      {selectedBook && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div className="p-6">
              <div className="flex gap-6">
                <div className="w-32 h-44 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                  {selectedBook.cover_image ? (
                    <img
                      src={selectedBook.cover_image}
                      alt={selectedBook.title}
                      className="w-full h-full object-cover rounded-lg"
                    />
                  ) : (
                    <FileText className="w-12 h-12 text-gray-400" />
                  )}
                </div>
                <div className="flex-1">
                  <h2 className="text-xl font-bold text-gray-900">{selectedBook.title}</h2>
                  <p className="text-gray-500 mt-1">{selectedBook.author_name}</p>
                  <div className="flex gap-2 mt-3">
                    <span className="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded">
                      {FILE_TYPES[selectedBook.file_type as keyof typeof FILE_TYPES]}
                    </span>
                    <span className="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded">
                      {selectedBook.page_count ? `${selectedBook.page_count} pages` : ''}
                    </span>
                    {selectedBook.file_size && (
                      <span className="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded">
                        {selectedBook.file_size}
                      </span>
                    )}
                  </div>
                </div>
              </div>

              {selectedBook.description && (
                <div className="mt-6">
                  <h3 className="font-medium text-gray-900">Description</h3>
                  <p className="text-gray-600 mt-2">{selectedBook.description}</p>
                </div>
              )}

              <div className="mt-6 grid grid-cols-2 gap-4">
                <div className="text-sm">
                  <span className="text-gray-500">Publisher:</span>
                  <span className="ml-2 text-gray-900">{selectedBook.publisher || '-'}</span>
                </div>
                <div className="text-sm">
                  <span className="text-gray-500">Year:</span>
                  <span className="ml-2 text-gray-900">{selectedBook.publication_year || '-'}</span>
                </div>
                <div className="text-sm">
                  <span className="text-gray-500">Language:</span>
                  <span className="ml-2 text-gray-900">{selectedBook.language}</span>
                </div>
                <div className="text-sm">
                  <span className="text-gray-500">ISBN:</span>
                  <span className="ml-2 text-gray-900">{selectedBook.isbn || '-'}</span>
                </div>
              </div>

              <div className="flex gap-3 mt-6">
                <button className="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex items-center justify-center gap-2">
                  <Eye className="w-4 h-4" />
                  Read / View
                </button>
                {selectedBook.can_download && (
                  <button className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center justify-center gap-2">
                    <Download className="w-4 h-4" />
                    Download
                  </button>
                )}
              </div>
            </div>
            <div className="px-6 py-4 bg-gray-50 border-t flex justify-end">
              <button
                onClick={() => setSelectedBook(null)}
                className="px-4 py-2 text-gray-600 hover:text-gray-900"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};
