import { create } from 'zustand';

export interface Account {
  id: string;
  uuid: string;
  code: string;
  name: string;
  type: string;
  group: string;
  nature: string;
  openingBalance: number;
  currentBalance: number;
  isActive: boolean;
  parentId?: string;
  children?: Account[];
}

export interface JournalEntry {
  id: string;
  uuid: string;
  number: string;
  voucherType: string;
  referenceNumber?: string;
  transactionDate: string;
  description: string;
  totalDebit: number;
  totalCredit: number;
  currency: string;
  status: string;
  costCenterId?: string;
  department?: string;
  project?: string;
  fiscalYearId: string;
  createdBy: string;
  approvedBy?: string;
  approvedAt?: string;
  postedBy?: string;
  postedAt?: string;
  items: JournalEntryItem[];
}

export interface JournalEntryItem {
  id: string;
  uuid: string;
  accountId: string;
  accountCode: string;
  accountName: string;
  entryType: 'debit' | 'credit';
  amount: number;
  description?: string;
  costCenterId?: string;
}

export interface FiscalYear {
  id: string;
  uuid: string;
  yearName: string;
  startDate: string;
  endDate: string;
  status: string;
  isLocked: boolean;
  isActive: boolean;
}

export interface LedgerEntry {
  id: string;
  date: string;
  voucherNumber: string;
  voucherType: string;
  description: string;
  debit: number;
  credit: number;
  balance: number;
}

export interface TrialBalanceEntry {
  accountCode: string;
  accountName: string;
  accountType: string;
  openingDebit: number;
  openingCredit: number;
  debit: number;
  credit: number;
  closingDebit: number;
  closingCredit: number;
}

interface FinanceState {
  // Accounts
  accounts: Account[];
  selectedAccount: Account | null;
  accountTree: Account[];
  
  // Journal Entries
  journalEntries: JournalEntry[];
  currentJournalEntry: JournalEntry | null;
  
  // Fiscal Years
  fiscalYears: FiscalYear[];
  currentFiscalYear: FiscalYear | null;
  
  // Ledger
  ledgerEntries: LedgerEntry[];
  
  // Trial Balance
  trialBalance: TrialBalanceEntry[];
  
  // Filters
  filters: {
    fiscalYearId: string;
    dateFrom: string;
    dateTo: string;
    status: string;
    voucherType: string;
    accountType: string;
  };
  
  // Loading states
  isLoading: boolean;
  error: string | null;
  
  // Actions
  setAccounts: (accounts: Account[]) => void;
  setSelectedAccount: (account: Account | null) => void;
  setJournalEntries: (entries: JournalEntry[]) => void;
  setCurrentJournalEntry: (entry: JournalEntry | null) => void;
  setFiscalYears: (years: FiscalYear[]) => void;
  setCurrentFiscalYear: (year: FiscalYear | null) => void;
  setLedgerEntries: (entries: LedgerEntry[]) => void;
  setTrialBalance: (entries: TrialBalanceEntry[]) => void;
  setFilters: (filters: Partial<FinanceState['filters']>) => void;
  setLoading: (isLoading: boolean) => void;
  setError: (error: string | null) => void;
  
  // Computed
  getAccountsByType: (type: string) => Account[];
  getJournalEntriesByStatus: (status: string) => JournalEntry[];
  calculateTotalDebit: () => number;
  calculateTotalCredit: () => number;
  isJournalBalanced: (entry: JournalEntry) => boolean;
}

export const useFinanceStore = create<FinanceState>((set, get) => ({
  // Initial state
  accounts: [],
  selectedAccount: null,
  accountTree: [],
  journalEntries: [],
  currentJournalEntry: null,
  fiscalYears: [],
  currentFiscalYear: null,
  ledgerEntries: [],
  trialBalance: [],
  
  filters: {
    fiscalYearId: '',
    dateFrom: '',
    dateTo: '',
    status: 'all',
    voucherType: 'all',
    accountType: 'all',
  },
  
  isLoading: false,
  error: null,
  
  // Actions
  setAccounts: (accounts) => set({ accounts }),
  setSelectedAccount: (account) => set({ selectedAccount: account }),
  setJournalEntries: (entries) => set({ journalEntries: entries }),
  setCurrentJournalEntry: (entry) => set({ currentJournalEntry: entry }),
  setFiscalYears: (years) => set({ fiscalYears: years }),
  setCurrentFiscalYear: (year) => set({ currentFiscalYear: year }),
  setLedgerEntries: (entries) => set({ ledgerEntries: entries }),
  setTrialBalance: (entries) => set({ trialBalance: entries }),
  setFilters: (filters) => set((state) => ({ filters: { ...state.filters, ...filters } })),
  setLoading: (isLoading) => set({ isLoading }),
  setError: (error) => set({ error }),
  
  // Computed
  getAccountsByType: (type) => {
    const { accounts } = get();
    return accounts.filter(account => account.type === type);
  },
  
  getJournalEntriesByStatus: (status) => {
    const { journalEntries } = get();
    return journalEntries.filter(entry => entry.status === status);
  },
  
  calculateTotalDebit: () => {
    const { currentJournalEntry } = get();
    if (!currentJournalEntry) return 0;
    return currentJournalEntry.items
      .filter(item => item.entryType === 'debit')
      .reduce((sum, item) => sum + item.amount, 0);
  },
  
  calculateTotalCredit: () => {
    const { currentJournalEntry } = get();
    if (!currentJournalEntry) return 0;
    return currentJournalEntry.items
      .filter(item => item.entryType === 'credit')
      .reduce((sum, item) => sum + item.amount, 0);
  },
  
  isJournalBalanced: (entry) => {
    const totalDebit = entry.items
      .filter(item => item.entryType === 'debit')
      .reduce((sum, item) => sum + item.amount, 0);
    const totalCredit = entry.items
      .filter(item => item.entryType === 'credit')
      .reduce((sum, item) => sum + item.amount, 0);
    return totalDebit === totalCredit;
  },
}));

export default useFinanceStore;
