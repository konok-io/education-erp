/**
 * OPAC Search Page
 */

import React, { useEffect, useState } from 'react';
import { useLibraryStore } from '../store/libraryStore';
import { libraryApi } from '../services/libraryApi';
import { Search, BookOpen, MapPin, Check, X } from 'lucide-react';
import type { Book } from '../types';

export const OPACSearch: React.FC = () => {
  const { fetchCategories, categories } = useLibraryStore();
  const [query, setQuery] = useState('');
  const [results, setResults] = useState<Book[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [filters, setFilters] = useState({
    category_id: '',
    publication_year: '',
    language: '',
  });
  const [selectedBook, setSelectedBook] = useState<Book | null>(null);

  useEffect(() => {
    fetchCategories();
  }, [fetchCategories]);

  const handleSearch = async () => {
    if (!query.trim() && !filters.category_id && !filters.publication_year && !filters.language) {
      return;
    }

    setIsLoading(true);
    try {
      const response = await libraryApi.opacSearch({
        q: query || undefined,
        category_id: filters.category_id || undefined,
        publication_year: filters.publication_year ? parseInt(filters.publication_year) : undefined,
        language: filters.language || undefined,
      });
      setResults(response.data);
    } catch (error) {
      console.error('Search failed:', error);
    }
    setIsLoading(false);
  };

  useEffect(() => {
    const debounce = setTimeout(() => {
      handleSearch();
    }, 500);
    return () => clearTimeout(debounce);
  }, [query, filters]);

  return (
    <div className="space-y-6">
      <div className="text-center">
        <h1 className="text-3xl font-bold text-gray-900">Library Catalog</h1>
        <p className="text-gray-500 mt-2">Search for books, journals, and digital resources</p>
      </div>

      {/* Search Box */}
      <div className="bg-white rounded-xl shadow-sm p-6">
        <div className="flex gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              type="text"
              placeholder="Search by title, author, ISBN, or keyword..."
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              className="w-full pl-12 pr-4 py-3 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            />
          </div>
          <button
            onClick={handleSearch}
            className="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
          >
            Search
          </button>
        </div>

        {/* Filters */}
        <div className="flex flex-wrap gap-4 mt-4">
          <select
            value={filters.category_id}
            onChange={(e) => setFilters({ ...filters, category_id: e.target.value })}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Categories</option>
            {categories.map((cat) => (
              <option key={cat.id} value={cat.id}>{cat.name}</option>
            ))}
          </select>
          <select
            value={filters.language}
            onChange={(e) => setFilters({ ...filters, language: e.target.value })}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          >
            <option value="">All Languages</option>
            <option value="English">English</option>
            <option value="Bangla">Bangla</option>
            <option value="Bengali">Bengali</option>
          </select>
          <input
            type="number"
            placeholder="Publication Year"
            value={filters.publication_year}
            onChange={(e) => setFilters({ ...filters, publication_year: e.target.value })}
            className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 w-40"
          />
        </div>
      </div>

      {/* Results */}
      <div className="bg-white rounded-xl shadow-sm p-6">
        {isLoading ? (
          <div className="flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : results.length === 0 ? (
          <div className="text-center py-12">
            <BookOpen className="w-16 h-16 text-gray-400 mx-auto mb-4" />
            <p className="text-gray-500 text-lg">
              {query ? 'No books found matching your search' : 'Enter a search term to find books'}
            </p>
          </div>
        ) : (
          <>
            <p className="text-gray-500 mb-4">{results.length} books found</p>
            <div className="space-y-4">
              {results.map((book) => (
                <div
                  key={book.id}
                  className="border border-gray-200 rounded-lg p-4 hover:border-primary-300 hover:bg-gray-50 transition-colors cursor-pointer"
                  onClick={() => setSelectedBook(book)}
                >
                  <div className="flex gap-4">
                    <div className="w-16 h-24 bg-gray-200 rounded flex items-center justify-center flex-shrink-0">
                      {book.cover_image ? (
                        <img src={book.cover_image} alt={book.title} className="w-full h-full object-cover rounded" />
                      ) : (
                        <BookOpen className="w-8 h-8 text-gray-400" />
                      )}
                    </div>
                    <div className="flex-1">
                      <div className="flex items-start justify-between">
                        <div>
                          <h3 className="font-semibold text-gray-900 text-lg">{book.title}</h3>
                          {book.subtitle && (
                            <p className="text-gray-500">{book.subtitle}</p>
                          )}
                        </div>
                        <div className={`px-3 py-1 rounded-full text-sm font-medium ${
                          book.available_copies > 0 
                            ? 'bg-green-100 text-green-700' 
                            : 'bg-red-100 text-red-700'
                        }`}>
                          {book.available_copies > 0 ? (
                            <span className="flex items-center gap-1">
                              <Check className="w-4 h-4" />
                              Available ({book.available_copies})
                            </span>
                          ) : (
                            <span className="flex items-center gap-1">
                              <X className="w-4 h-4" />
                              Not Available
                            </span>
                          )}
                        </div>
                      </div>

                      <p className="text-gray-600 mt-2">
                        {book.authors?.map((a) => a.name).join(', ') || book.author_names || 'Unknown Author'}
                      </p>

                      <div className="flex flex-wrap gap-4 mt-3 text-sm text-gray-500">
                        {book.isbn && <span>ISBN: {book.isbn}</span>}
                        {book.publication_year && <span>Year: {book.publication_year}</span>}
                        {book.pages && <span>{book.pages} pages</span>}
                        <span>{book.language}</span>
                        {book.category && <span>Category: {book.category.name}</span>}
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </>
        )}
      </div>

      {/* Book Detail Modal */}
      {selectedBook && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div className="p-6">
              <div className="flex gap-6">
                <div className="w-32 h-48 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                  {selectedBook.cover_image ? (
                    <img src={selectedBook.cover_image} alt={selectedBook.title} className="w-full h-full object-cover rounded-lg" />
                  ) : (
                    <BookOpen className="w-12 h-12 text-gray-400" />
                  )}
                </div>
                <div className="flex-1">
                  <h2 className="text-xl font-bold text-gray-900">{selectedBook.title}</h2>
                  {selectedBook.subtitle && <p className="text-gray-500">{selectedBook.subtitle}</p>}
                  <p className="text-gray-600 mt-2">
                    {selectedBook.authors?.map((a) => a.name).join(', ') || selectedBook.author_names}
                  </p>

                  <div className={`mt-4 inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium ${
                    selectedBook.available_copies > 0 
                      ? 'bg-green-100 text-green-700' 
                      : 'bg-red-100 text-red-700'
                  }`}>
                    {selectedBook.available_copies > 0 ? (
                      <>
                        <Check className="w-4 h-4" />
                        Available ({selectedBook.available_copies} copies)
                      </>
                    ) : (
                      <>
                        <X className="w-4 h-4" />
                        Currently Unavailable
                      </>
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
                {selectedBook.isbn && (
                  <div>
                    <span className="text-gray-500">ISBN:</span>
                    <span className="ml-2 text-gray-900">{selectedBook.isbn}</span>
                  </div>
                )}
                {selectedBook.publication_year && (
                  <div>
                    <span className="text-gray-500">Publication Year:</span>
                    <span className="ml-2 text-gray-900">{selectedBook.publication_year}</span>
                  </div>
                )}
                {selectedBook.edition && (
                  <div>
                    <span className="text-gray-500">Edition:</span>
                    <span className="ml-2 text-gray-900">{selectedBook.edition}</span>
                  </div>
                )}
                {selectedBook.pages && (
                  <div>
                    <span className="text-gray-500">Pages:</span>
                    <span className="ml-2 text-gray-900">{selectedBook.pages}</span>
                  </div>
                )}
                {selectedBook.price && (
                  <div>
                    <span className="text-gray-500">Price:</span>
                    <span className="ml-2 text-gray-900">৳{selectedBook.price}</span>
                  </div>
                )}
                <div>
                  <span className="text-gray-500">Language:</span>
                  <span className="ml-2 text-gray-900">{selectedBook.language}</span>
                </div>
              </div>

              {selectedBook.keywords && (
                <div className="mt-6">
                  <h3 className="font-medium text-gray-900">Keywords</h3>
                  <div className="flex flex-wrap gap-2 mt-2">
                    {selectedBook.keywords.split(',').map((keyword, index) => (
                      <span key={index} className="px-2 py-1 bg-gray-100 text-gray-600 rounded text-sm">
                        {keyword.trim()}
                      </span>
                    ))}
                  </div>
                </div>
              )}

              {selectedBook.copies && selectedBook.copies.length > 0 && (
                <div className="mt-6">
                  <h3 className="font-medium text-gray-900">Available Copies</h3>
                  <div className="mt-2 space-y-2">
                    {selectedBook.copies
                      .filter(c => c.status === 'available')
                      .map((copy) => (
                        <div key={copy.id} className="flex items-center justify-between p-3 bg-gray-50 rounded">
                          <div className="flex items-center gap-3">
                            <MapPin className="w-4 h-4 text-gray-400" />
                            <div>
                              <p className="text-sm font-medium">{copy.accession_number}</p>
                              <p className="text-xs text-gray-500">{copy.rack?.name || 'Not assigned'}</p>
                            </div>
                          </div>
                          <span className="text-sm text-green-600">Available</span>
                        </div>
                      ))}
                  </div>
                </div>
              )}
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
