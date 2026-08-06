/**
 * Library Store - State Management
 */

import { create } from 'zustand';
import { libraryApi } from '../services/libraryApi';
import type {
  Book,
  BookCategory,
  Author,
  Publisher,
  LibraryMember,
  BookIssue,
  BookReservation,
  LibraryFine,
  DigitalBook,
  LibraryDashboard,
} from '../types';

interface LibraryState {
  // Dashboard
  dashboard: LibraryDashboard | null;
  dashboardLoading: boolean;
  dashboardError: string | null;

  // Books
  books: Book[];
  booksPagination: { current_page: number; last_page: number; total: number } | null;
  booksLoading: boolean;
  booksError: string | null;
  selectedBook: Book | null;

  // Categories
  categories: BookCategory[];
  categoriesLoading: boolean;

  // Members
  members: LibraryMember[];
  membersPagination: { current_page: number; last_page: number; total: number } | null;
  membersLoading: boolean;
  membersError: string | null;
  selectedMember: LibraryMember | null;

  // Issues
  issues: BookIssue[];
  issuesPagination: { current_page: number; last_page: number; total: number } | null;
  issuesLoading: boolean;
  overdueIssues: BookIssue[];
  overdueLoading: boolean;

  // Reservations
  reservations: BookReservation[];
  reservationsLoading: boolean;

  // Fines
  fines: LibraryFine[];
  finesPagination: { current_page: number; last_page: number; total: number } | null;
  finesLoading: boolean;

  // Digital Books
  digitalBooks: DigitalBook[];
  digitalBooksPagination: { current_page: number; last_page: number; total: number } | null;
  digitalBooksLoading: boolean;

  // Actions
  fetchDashboard: () => Promise<void>;
  fetchBooks: (params?: Record<string, any>) => Promise<void>;
  fetchBook: (uuid: string) => Promise<void>;
  createBook: (data: Partial<Book>) => Promise<Book>;
  updateBook: (uuid: string, data: Partial<Book>) => Promise<Book>;
  deleteBook: (uuid: string) => Promise<void>;
  fetchCategories: () => Promise<void>;
  fetchMembers: (params?: Record<string, any>) => Promise<void>;
  fetchMember: (uuid: string) => Promise<void>;
  createMember: (data: Partial<LibraryMember>) => Promise<LibraryMember>;
  updateMember: (uuid: string, data: Partial<LibraryMember>) => Promise<LibraryMember>;
  blockMember: (uuid: string) => Promise<void>;
  unblockMember: (uuid: string) => Promise<void>;
  fetchIssues: (params?: Record<string, any>) => Promise<void>;
  fetchOverdueIssues: () => Promise<void>;
  issueBook: (data: { member_id: string; book_copy_id: string }) => Promise<BookIssue>;
  returnBook: (issueId: string) => Promise<{ issue: BookIssue; fine?: LibraryFine; overdue_days: number }>;
  renewBook: (issueId: string) => Promise<BookIssue>;
  fetchReservations: (params?: Record<string, any>) => Promise<void>;
  createReservation: (data: { member_id: string; book_id: string }) => Promise<BookReservation>;
  fulfillReservation: (reservationId: string) => Promise<void>;
  cancelReservation: (reservationId: string) => Promise<void>;
  fetchFines: (params?: Record<string, any>) => Promise<void>;
  payFine: (uuid: string, data: { amount: number; payment_method?: string }) => Promise<LibraryFine>;
  waiveFine: (uuid: string, data: { amount: number }) => Promise<LibraryFine>;
  fetchDigitalBooks: (params?: Record<string, any>) => Promise<void>;
  resetState: () => void;
}

const initialState = {
  dashboard: null,
  dashboardLoading: false,
  dashboardError: null,
  books: [],
  booksPagination: null,
  booksLoading: false,
  booksError: null,
  selectedBook: null,
  categories: [],
  categoriesLoading: false,
  members: [],
  membersPagination: null,
  membersLoading: false,
  membersError: null,
  selectedMember: null,
  issues: [],
  issuesPagination: null,
  issuesLoading: false,
  overdueIssues: [],
  overdueLoading: false,
  reservations: [],
  reservationsLoading: false,
  fines: [],
  finesPagination: null,
  finesLoading: false,
  digitalBooks: [],
  digitalBooksPagination: null,
  digitalBooksLoading: false,
};

export const useLibraryStore = create<LibraryState>((set, get) => ({
  ...initialState,

  // Dashboard
  fetchDashboard: async () => {
    set({ dashboardLoading: true, dashboardError: null });
    try {
      const dashboard = await libraryApi.getDashboard();
      set({ dashboard, dashboardLoading: false });
    } catch (error: any) {
      set({ dashboardError: error.message, dashboardLoading: false });
    }
  },

  // Books
  fetchBooks: async (params) => {
    set({ booksLoading: true, booksError: null });
    try {
      const response = await libraryApi.getBooks(params);
      set({
        books: response.data,
        booksPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        booksLoading: false,
      });
    } catch (error: any) {
      set({ booksError: error.message, booksLoading: false });
    }
  },

  fetchBook: async (uuid) => {
    set({ booksLoading: true, booksError: null });
    try {
      const book = await libraryApi.getBook(uuid);
      set({ selectedBook: book, booksLoading: false });
    } catch (error: any) {
      set({ booksError: error.message, booksLoading: false });
    }
  },

  createBook: async (data) => {
    const book = await libraryApi.createBook(data);
    const books = [...get().books, book];
    set({ books });
    return book;
  },

  updateBook: async (uuid, data) => {
    const book = await libraryApi.updateBook(uuid, data);
    const books = get().books.map((b) => (b.id === uuid ? book : b));
    set({ books, selectedBook: book });
    return book;
  },

  deleteBook: async (uuid) => {
    await libraryApi.deleteBook(uuid);
    const books = get().books.filter((b) => b.id !== uuid);
    set({ books });
  },

  fetchCategories: async () => {
    set({ categoriesLoading: true });
    try {
      const response = await libraryApi.getCategories({ per_page: 100 });
      set({ categories: response.data, categoriesLoading: false });
    } catch (error) {
      set({ categoriesLoading: false });
    }
  },

  // Members
  fetchMembers: async (params) => {
    set({ membersLoading: true, membersError: null });
    try {
      const response = await libraryApi.getMembers(params);
      set({
        members: response.data,
        membersPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        membersLoading: false,
      });
    } catch (error: any) {
      set({ membersError: error.message, membersLoading: false });
    }
  },

  fetchMember: async (uuid) => {
    set({ membersLoading: true, membersError: null });
    try {
      const member = await libraryApi.getMember(uuid);
      set({ selectedMember: member, membersLoading: false });
    } catch (error: any) {
      set({ membersError: error.message, membersLoading: false });
    }
  },

  createMember: async (data) => {
    const member = await libraryApi.createMember(data);
    const members = [...get().members, member];
    set({ members });
    return member;
  },

  updateMember: async (uuid, data) => {
    const member = await libraryApi.updateMember(uuid, data);
    const members = get().members.map((m) => (m.id === uuid ? member : m));
    set({ members, selectedMember: member });
    return member;
  },

  blockMember: async (uuid) => {
    const member = await libraryApi.blockMember(uuid);
    const members = get().members.map((m) => (m.id === uuid ? member : m));
    set({ members, selectedMember: member });
  },

  unblockMember: async (uuid) => {
    const member = await libraryApi.unblockMember(uuid);
    const members = get().members.map((m) => (m.id === uuid ? member : m));
    set({ members, selectedMember: member });
  },

  // Issues
  fetchIssues: async (params) => {
    set({ issuesLoading: true });
    try {
      const response = await libraryApi.getIssues(params);
      set({
        issues: response.data,
        issuesPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        issuesLoading: false,
      });
    } catch (error) {
      set({ issuesLoading: false });
    }
  },

  fetchOverdueIssues: async () => {
    set({ overdueLoading: true });
    try {
      const response = await libraryApi.getOverdueIssues();
      set({ overdueIssues: response.data, overdueLoading: false });
    } catch (error) {
      set({ overdueLoading: false });
    }
  },

  issueBook: async (data) => {
    const issue = await libraryApi.issueBook(data);
    const issues = [issue, ...get().issues];
    set({ issues });
    return issue;
  },

  returnBook: async (issueId) => {
    const result = await libraryApi.returnBook(issueId);
    const issues = get().issues.map((i) => (i.id === issueId ? result.issue : i));
    set({ issues });
    return result;
  },

  renewBook: async (issueId) => {
    const issue = await libraryApi.renewBook(issueId);
    const issues = get().issues.map((i) => (i.id === issueId ? issue : i));
    set({ issues });
    return issue;
  },

  // Reservations
  fetchReservations: async (params) => {
    set({ reservationsLoading: true });
    try {
      const response = await libraryApi.getReservations(params);
      set({ reservations: response.data, reservationsLoading: false });
    } catch (error) {
      set({ reservationsLoading: false });
    }
  },

  createReservation: async (data) => {
    const reservation = await libraryApi.createReservation(data);
    const reservations = [...get().reservations, reservation];
    set({ reservations });
    return reservation;
  },

  fulfillReservation: async (reservationId) => {
    await libraryApi.fulfillReservation(reservationId);
    const reservations = get().reservations.filter((r) => r.id !== reservationId);
    set({ reservations });
  },

  cancelReservation: async (reservationId) => {
    await libraryApi.cancelReservation(reservationId);
    const reservations = get().reservations.filter((r) => r.id !== reservationId);
    set({ reservations });
  },

  // Fines
  fetchFines: async (params) => {
    set({ finesLoading: true });
    try {
      const response = await libraryApi.getFines(params);
      set({
        fines: response.data,
        finesPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        finesLoading: false,
      });
    } catch (error) {
      set({ finesLoading: false });
    }
  },

  payFine: async (uuid, data) => {
    const fine = await libraryApi.payFine(uuid, data);
    const fines = get().fines.map((f) => (f.id === uuid ? fine : f));
    set({ fines });
    return fine;
  },

  waiveFine: async (uuid, data) => {
    const fine = await libraryApi.waiveFine(uuid, data);
    const fines = get().fines.map((f) => (f.id === uuid ? fine : f));
    set({ fines });
    return fine;
  },

  // Digital Books
  fetchDigitalBooks: async (params) => {
    set({ digitalBooksLoading: true });
    try {
      const response = await libraryApi.getDigitalBooks(params);
      set({
        digitalBooks: response.data,
        digitalBooksPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        digitalBooksLoading: false,
      });
    } catch (error) {
      set({ digitalBooksLoading: false });
    }
  },

  // Reset
  resetState: () => set(initialState),
}));
