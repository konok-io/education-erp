/**
 * Finance & Accounting API
 */

import { apiClient } from '@/lib/api-client';
import type {
  Account,
  JournalEntry,
  JournalEntryDetail,
  FiscalYear,
  CostCenter,
  Asset,
  Budget,
  LedgerReport,
  TrialBalanceReport,
  ProfitLossReport,
  BalanceSheetReport,
  CashBookReport,
  FinanceDashboard,
} from '../types';
import type { PaginatedResponse } from '@/types';

// ===================== ACCOUNTS =====================

export const getAccounts = async (params?: {
  account_type?: string;
  parent_id?: string;
  is_active?: boolean;
  per_page?: number;
}): Promise<PaginatedResponse<Account>> => {
  const response = await apiClient.get('/api/v1/finance/accounts', { params });
  return response.data;
};

export const createAccount = async (data: Partial<Account>): Promise<Account> => {
  const response = await apiClient.post('/api/v1/finance/accounts', data);
  return response.data.data;
};

export const updateAccount = async (uuid: string, data: Partial<Account>): Promise<Account> => {
  const response = await apiClient.put(`/api/v1/finance/accounts/${uuid}`, data);
  return response.data.data;
};

export const deleteAccount = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/finance/accounts/${uuid}`);
};

// ===================== JOURNAL ENTRIES =====================

export const getJournalEntries = async (params?: {
  voucher_type?: string;
  status?: string;
  date_from?: string;
  date_to?: string;
  per_page?: number;
}): Promise<PaginatedResponse<JournalEntry>> => {
  const response = await apiClient.get('/api/v1/finance/journal', { params });
  return response.data;
};

export const createJournalEntry = async (data: {
  voucher_type: string;
  entry_date: string;
  description: string;
  reference?: string;
  details: { account_id: string; dr_cr: string; amount: number; narration?: string }[];
  remarks?: string;
}): Promise<JournalEntry> => {
  const response = await apiClient.post('/api/v1/finance/journal', data);
  return response.data.data;
};

export const updateJournalEntry = async (uuid: string, data: Partial<JournalEntry>): Promise<JournalEntry> => {
  const response = await apiClient.put(`/api/v1/finance/journal/${uuid}`, data);
  return response.data.data;
};

export const deleteJournalEntry = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/finance/journal/${uuid}`);
};

export const postJournalEntry = async (uuid: string): Promise<void> => {
  await apiClient.post(`/api/v1/finance/journal/${uuid}/post`);
};

export const approveJournalEntry = async (uuid: string): Promise<void> => {
  await apiClient.post(`/api/v1/finance/journal/${uuid}/approve`);
};

// ===================== LEDGER =====================

export const getLedger = async (data: {
  account_id: string;
  date_from: string;
  date_to: string;
}): Promise<LedgerReport> => {
  const response = await apiClient.get('/api/v1/finance/ledger', { params: data });
  return response.data.data;
};

export const getAccountSummary = async (uuid: string): Promise<any> => {
  const response = await apiClient.get(`/api/v1/finance/accounts/${uuid}/summary`);
  return response.data.data;
};

// ===================== REPORTS =====================

export const getTrialBalance = async (date: string): Promise<TrialBalanceReport> => {
  const response = await apiClient.get('/api/v1/finance/reports/trial-balance', { params: { date } });
  return response.data.data;
};

export const getProfitLoss = async (data: {
  date_from: string;
  date_to: string;
}): Promise<ProfitLossReport> => {
  const response = await apiClient.get('/api/v1/finance/reports/profit-loss', { params: data });
  return response.data.data;
};

export const getBalanceSheet = async (date: string): Promise<BalanceSheetReport> => {
  const response = await apiClient.get('/api/v1/finance/reports/balance-sheet', { params: { date } });
  return response.data.data;
};

export const getCashBook = async (data: {
  account_id: string;
  date_from: string;
  date_to: string;
}): Promise<CashBookReport> => {
  const response = await apiClient.get('/api/v1/finance/reports/cash-book', { params: data });
  return response.data.data;
};

export const getBankBook = async (data: {
  account_id: string;
  date_from: string;
  date_to: string;
}): Promise<CashBookReport> => {
  const response = await apiClient.get('/api/v1/finance/reports/bank-book', { params: data });
  return response.data.data;
};

export const getIncomeReport = async (data: {
  date_from: string;
  date_to: string;
}): Promise<any> => {
  const response = await apiClient.get('/api/v1/finance/reports/income', { params: data });
  return response.data.data;
};

export const getExpenseReport = async (data: {
  date_from: string;
  date_to: string;
}): Promise<any> => {
  const response = await apiClient.get('/api/v1/finance/reports/expense', { params: data });
  return response.data.data;
};

// ===================== FISCAL YEAR =====================

export const getFiscalYears = async (): Promise<FiscalYear[]> => {
  const response = await apiClient.get('/api/v1/finance/fiscal-years');
  return response.data.data;
};

export const createFiscalYear = async (data: Partial<FiscalYear>): Promise<FiscalYear> => {
  const response = await apiClient.post('/api/v1/finance/fiscal-years', data);
  return response.data.data;
};

export const closeFiscalYear = async (uuid: string): Promise<void> => {
  await apiClient.post(`/api/v1/finance/fiscal-years/${uuid}/close`);
};

// ===================== COST CENTERS =====================

export const getCostCenters = async (): Promise<CostCenter[]> => {
  const response = await apiClient.get('/api/v1/finance/cost-centers');
  return response.data.data;
};

export const createCostCenter = async (data: Partial<CostCenter>): Promise<CostCenter> => {
  const response = await apiClient.post('/api/v1/finance/cost-centers', data);
  return response.data.data;
};

// ===================== ASSETS =====================

export const getAssets = async (params?: { per_page?: number }): Promise<PaginatedResponse<Asset>> => {
  const response = await apiClient.get('/api/v1/finance/assets', { params });
  return response.data;
};

export const createAsset = async (data: Partial<Asset>): Promise<Asset> => {
  const response = await apiClient.post('/api/v1/finance/assets', data);
  return response.data.data;
};

export const calculateDepreciation = async (uuid: string): Promise<void> => {
  await apiClient.post(`/api/v1/finance/assets/${uuid}/depreciation`);
};

// ===================== BUDGETS =====================

export const getBudgets = async (params?: { per_page?: number }): Promise<PaginatedResponse<Budget>> => {
  const response = await apiClient.get('/api/v1/finance/budgets', { params });
  return response.data;
};

export const createBudget = async (data: Partial<Budget>): Promise<Budget> => {
  const response = await apiClient.post('/api/v1/finance/budgets', data);
  return response.data.data;
};

// ===================== DASHBOARD =====================

export const getFinanceDashboard = async (): Promise<FinanceDashboard> => {
  const response = await apiClient.get('/api/v1/finance/dashboard');
  return response.data.data;
};

// ===================== EXPORT =====================

export const exportFinanceReport = async (data: {
  report_type: 'trial_balance' | 'profit_loss' | 'balance_sheet' | 'ledger';
  format: 'excel' | 'csv' | 'pdf';
  date?: string;
  date_from?: string;
  date_to?: string;
  account_id?: string;
}): Promise<string> => {
  const response = await apiClient.get('/api/v1/finance/export', { params: data });
  return response.data.data.url;
};
