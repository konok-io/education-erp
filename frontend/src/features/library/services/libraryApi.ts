/**
 * Library API Service
 */

import { apiClient } from '@/lib/api-client';
import type {
  Book,
  BookCopy,
  BookCategory,
  Author,
  Publisher,
  LibraryMember,
  BookIssue,
  BookReservation,
  LibraryFine,
  DigitalBook,
  LibraryDashboard,
  IssueReport,
  FineReport,
  PaginatedResponse,
} from '../types';

const BASE_URL = '/api/v1/library';

export const libraryApi = {
  // Dashboard
  getDashboard: async (): Promise<LibraryDashboard> => {
    const response = await apiClient.get(`${BASE_URL}/dashboard`);
    return response.data;
  },

  // Books
  getBooks: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    category_id?: string;
    author_id?: string;
    availability?: boolean;
  }): Promise<PaginatedResponse<Book>> => {
    const response = await apiClient.get(`${BASE_URL}/books`, { params });
    return response.data;
  },

  getBook: async (uuid: string): Promise<Book> => {
    const response = await apiClient.get(`${BASE_URL}/books/${uuid}`);
    return response.data;
  },

  createBook: async (data: Partial<Book>): Promise<Book> => {
    const response = await apiClient.post(`${BASE_URL}/books`, data);
    return response.data;
  },

  updateBook: async (uuid: string, data: Partial<Book>): Promise<Book> => {
    const response = await apiClient.put(`${BASE_URL}/books/${uuid}`, data);
    return response.data;
  },

  deleteBook: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/books/${uuid}`);
  },

  // Book Copies
  getBookCopies: async (bookId: string, params?: { page?: number; per_page?: number }): Promise<PaginatedResponse<BookCopy>> => {
    const response = await apiClient.get(`${BASE_URL}/books/${bookId}/copies`, { params });
    return response.data;
  },

  createBookCopy: async (bookId: string, data: Partial<BookCopy>): Promise<BookCopy> => {
    const response = await apiClient.post(`${BASE_URL}/books/${bookId}/copies`, data);
    return response.data;
  },

  // Categories
  getCategories: async (params?: { page?: number; per_page?: number }): Promise<PaginatedResponse<BookCategory>> => {
    const response = await apiClient.get(`${BASE_URL}/categories`, { params });
    return response.data;
  },

  getCategory: async (uuid: string): Promise<BookCategory> => {
    const response = await apiClient.get(`${BASE_URL}/categories/${uuid}`);
    return response.data;
  },

  createCategory: async (data: Partial<BookCategory>): Promise<BookCategory> => {
    const response = await apiClient.post(`${BASE_URL}/categories`, data);
    return response.data;
  },

  updateCategory: async (uuid: string, data: Partial<BookCategory>): Promise<BookCategory> => {
    const response = await apiClient.put(`${BASE_URL}/categories/${uuid}`, data);
    return response.data;
  },

  deleteCategory: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/categories/${uuid}`);
  },

  // Authors
  getAuthors: async (params?: { page?: number; per_page?: number; search?: string }): Promise<PaginatedResponse<Author>> => {
    const response = await apiClient.get(`${BASE_URL}/authors`, { params });
    return response.data;
  },

  createAuthor: async (data: Partial<Author>): Promise<Author> => {
    const response = await apiClient.post(`${BASE_URL}/authors`, data);
    return response.data;
  },

  updateAuthor: async (uuid: string, data: Partial<Author>): Promise<Author> => {
    const response = await apiClient.put(`${BASE_URL}/authors/${uuid}`, data);
    return response.data;
  },

  // Publishers
  getPublishers: async (params?: { page?: number; per_page?: number; search?: string }): Promise<PaginatedResponse<Publisher>> => {
    const response = await apiClient.get(`${BASE_URL}/publishers`, { params });
    return response.data;
  },

  createPublisher: async (data: Partial<Publisher>): Promise<Publisher> => {
    const response = await apiClient.post(`${BASE_URL}/publishers`, data);
    return response.data;
  },

  updatePublisher: async (uuid: string, data: Partial<Publisher>): Promise<Publisher> => {
    const response = await apiClient.put(`${BASE_URL}/publishers/${uuid}`, data);
    return response.data;
  },

  // Members
  getMembers: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    member_type?: string;
    status?: string;
  }): Promise<PaginatedResponse<LibraryMember>> => {
    const response = await apiClient.get(`${BASE_URL}/members`, { params });
    return response.data;
  },

  getMember: async (uuid: string): Promise<LibraryMember> => {
    const response = await apiClient.get(`${BASE_URL}/members/${uuid}`);
    return response.data;
  },

  getMemberStats: async (uuid: string): Promise<{
    total_issues: number;
    current_issues: number;
    returned_books: number;
    overdue_books: number;
    total_fines: number;
    unpaid_fines: number;
    active_reservations: number;
  }> => {
    const response = await apiClient.get(`${BASE_URL}/members/${uuid}/stats`);
    return response.data;
  },

  createMember: async (data: Partial<LibraryMember>): Promise<LibraryMember> => {
    const response = await apiClient.post(`${BASE_URL}/members`, data);
    return response.data;
  },

  updateMember: async (uuid: string, data: Partial<LibraryMember>): Promise<LibraryMember> => {
    const response = await apiClient.put(`${BASE_URL}/members/${uuid}`, data);
    return response.data;
  },

  blockMember: async (uuid: string): Promise<LibraryMember> => {
    const response = await apiClient.post(`${BASE_URL}/members/${uuid}/block`);
    return response.data;
  },

  unblockMember: async (uuid: string): Promise<LibraryMember> => {
    const response = await apiClient.post(`${BASE_URL}/members/${uuid}/unblock`);
    return response.data;
  },

  // Issues
  getIssues: async (params?: {
    page?: number;
    per_page?: number;
    member_id?: string;
    status?: string;
    date_from?: string;
    date_to?: string;
  }): Promise<PaginatedResponse<BookIssue>> => {
    const response = await apiClient.get(`${BASE_URL}/issues`, { params });
    return response.data;
  },

  getOverdueIssues: async (params?: { page?: number; per_page?: number }): Promise<PaginatedResponse<BookIssue>> => {
    const response = await apiClient.get(`${BASE_URL}/issues/overdue`, { params });
    return response.data;
  },

  issueBook: async (data: { member_id: string; book_copy_id: string }): Promise<BookIssue> => {
    const response = await apiClient.post(`${BASE_URL}/issues`, data);
    return response.data;
  },

  returnBook: async (issueId: string): Promise<{ issue: BookIssue; fine?: LibraryFine; overdue_days: number }> => {
    const response = await apiClient.post(`${BASE_URL}/issues/${issueId}/return`);
    return response.data;
  },

  renewBook: async (issueId: string): Promise<BookIssue> => {
    const response = await apiClient.post(`${BASE_URL}/issues/${issueId}/renew`);
    return response.data;
  },

  // Reservations
  getReservations: async (params?: {
    page?: number;
    per_page?: number;
    status?: string;
  }): Promise<PaginatedResponse<BookReservation>> => {
    const response = await apiClient.get(`${BASE_URL}/reservations`, { params });
    return response.data;
  },

  createReservation: async (data: { member_id: string; book_id: string }): Promise<BookReservation> => {
    const response = await apiClient.post(`${BASE_URL}/reservations`, data);
    return response.data;
  },

  fulfillReservation: async (reservationId: string): Promise<BookReservation> => {
    const response = await apiClient.post(`${BASE_URL}/reservations/${reservationId}/fulfill`);
    return response.data;
  },

  cancelReservation: async (reservationId: string): Promise<BookReservation> => {
    const response = await apiClient.post(`${BASE_URL}/reservations/${reservationId}/cancel`);
    return response.data;
  },

  // Fines
  getFines: async (params?: {
    page?: number;
    per_page?: number;
    member_id?: string;
    status?: string;
  }): Promise<PaginatedResponse<LibraryFine>> => {
    const response = await apiClient.get(`${BASE_URL}/fines`, { params });
    return response.data;
  },

  payFine: async (uuid: string, data: { amount: number; payment_method?: string }): Promise<LibraryFine> => {
    const response = await apiClient.post(`${BASE_URL}/fines/${uuid}/pay`, data);
    return response.data;
  },

  waiveFine: async (uuid: string, data: { amount: number }): Promise<LibraryFine> => {
    const response = await apiClient.post(`${BASE_URL}/fines/${uuid}/waive`, data);
    return response.data;
  },

  // Digital Books
  getDigitalBooks: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    category_id?: string;
    file_type?: string;
    access_type?: string;
  }): Promise<PaginatedResponse<DigitalBook>> => {
    const response = await apiClient.get(`${BASE_URL}/digital-books`, { params });
    return response.data;
  },

  getDigitalBook: async (uuid: string): Promise<DigitalBook> => {
    const response = await apiClient.get(`${BASE_URL}/digital-books/${uuid}`);
    return response.data;
  },

  viewDigitalBook: async (uuid: string): Promise<string> => {
    const response = await apiClient.get(`${BASE_URL}/digital-books/${uuid}/view`);
    return response.data.url;
  },

  downloadDigitalBook: async (uuid: string): Promise<string> => {
    const response = await apiClient.get(`${BASE_URL}/digital-books/${uuid}/download`);
    return response.data.url;
  },

  // OPAC Search
  opacSearch: async (params: {
    q?: string;
    category_id?: string;
    author_id?: string;
    publication_year?: number;
    language?: string;
    per_page?: number;
  }): Promise<PaginatedResponse<Book>> => {
    const response = await apiClient.get(`${BASE_URL}/opac/search`, { params });
    return response.data;
  },

  // Reports
  getIssueReport: async (params?: {
    date_from?: string;
    date_to?: string;
    member_id?: string;
  }): Promise<IssueReport> => {
    const response = await apiClient.get(`${BASE_URL}/reports/issues`, { params });
    return response.data;
  },

  getFineReport: async (params?: {
    date_from?: string;
    date_to?: string;
  }): Promise<FineReport> => {
    const response = await apiClient.get(`${BASE_URL}/reports/fines`, { params });
    return response.data;
  },

  // Barcode & QR
  generateBarcode: (accessionNumber: string): string => {
    return `LIB-${accessionNumber}`;
  },

  generateQRContent: (book: Book): object => ({
    id: book.id,
    isbn: book.isbn,
    title: book.title,
    available: book.available_copies > 0,
  }),
};
