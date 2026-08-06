/**
 * Payment & Fee Management API
 */

import { apiClient } from '@/lib/api-client';
import type { 
  FeeCategory, 
  FeeStructure, 
  Invoice, 
  Payment, 
  Receipt, 
  Waiver, 
  Installment, 
  Refund, 
  Fine,
  PaymentDashboard,
  CollectionReport,
  DueReport,
  LedgerEntry
} from '../types';
import type { PaginatedResponse } from '@/types';

// ===================== FEE CATEGORIES =====================

export const getCategories = async (): Promise<FeeCategory[]> => {
  const response = await apiClient.get('/api/v1/payments/categories');
  return response.data.data;
};

export const createCategory = async (data: Partial<FeeCategory>): Promise<FeeCategory> => {
  const response = await apiClient.post('/api/v1/payments/categories', data);
  return response.data.data;
};

// ===================== FEE STRUCTURE =====================

export const getStructures = async (params?: {
  category_id?: string;
  session_id?: string;
  academic_level_id?: string;
  per_page?: number;
}): Promise<PaginatedResponse<FeeStructure>> => {
  const response = await apiClient.get('/api/v1/payments/structures', { params });
  return response.data;
};

export const createStructure = async (data: Partial<FeeStructure>): Promise<FeeStructure> => {
  const response = await apiClient.post('/api/v1/payments/structures', data);
  return response.data.data;
};

export const updateStructure = async (uuid: string, data: Partial<FeeStructure>): Promise<FeeStructure> => {
  const response = await apiClient.put(`/api/v1/payments/structures/${uuid}`, data);
  return response.data.data;
};

// ===================== INVOICES =====================

export const getInvoices = async (params?: {
  student_id?: string;
  category_id?: string;
  session_id?: string;
  status?: string;
  date_from?: string;
  date_to?: string;
  search?: string;
  per_page?: number;
}): Promise<PaginatedResponse<Invoice>> => {
  const response = await apiClient.get('/api/v1/payments/invoices', { params });
  return response.data;
};

export const getInvoice = async (uuid: string): Promise<Invoice> => {
  const response = await apiClient.get(`/api/v1/payments/invoices/${uuid}`);
  return response.data.data;
};

export const createInvoice = async (data: {
  student_id: string;
  category_id: string;
  amount: number;
  discount_amount?: number;
  due_date?: string;
  semester_id?: string;
  billing_month?: string;
  billing_year?: number;
}): Promise<Invoice> => {
  const response = await apiClient.post('/api/v1/payments/invoices', data);
  return response.data.data;
};

export const updateInvoice = async (uuid: string, data: Partial<Invoice>): Promise<Invoice> => {
  const response = await apiClient.put(`/api/v1/payments/invoices/${uuid}`, data);
  return response.data.data;
};

export const deleteInvoice = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/payments/invoices/${uuid}`);
};

export const generateInvoices = async (data: {
  session_id: string;
  class_id: string;
  category_ids: string[];
}): Promise<{ total: number; created: number; errors: string[] }> => {
  const response = await apiClient.post('/api/v1/payments/invoices/generate', data);
  return response.data.data;
};

// ===================== PAYMENTS =====================

export const getPayments = async (params?: {
  student_id?: string;
  invoice_id?: string;
  payment_method?: string;
  status?: string;
  date_from?: string;
  date_to?: string;
  gateway_name?: string;
  per_page?: number;
}): Promise<PaginatedResponse<Payment>> => {
  const response = await apiClient.get('/api/v1/payments', { params });
  return response.data;
};

export const collectPayment = async (data: {
  invoice_id: string;
  amount: number;
  payment_method: string;
  transaction_id?: string;
  gateway_response?: string;
}): Promise<Payment> => {
  const response = await apiClient.post('/api/v1/payments', data);
  return response.data.data;
};

export const verifyPayment = async (uuid: string): Promise<void> => {
  await apiClient.put(`/api/v1/payments/${uuid}/verify`);
};

export const getReceipt = async (uuid: string): Promise<Receipt> => {
  const response = await apiClient.get(`/api/v1/payments/receipt/${uuid}`);
  return response.data.data;
};

// ===================== WAIVERS =====================

export const applyWaiver = async (data: {
  invoice_id: string;
  amount: number;
  waiver_type: string;
  reason: string;
  percentage?: number;
}): Promise<Waiver> => {
  const response = await apiClient.post('/api/v1/payments/waivers', data);
  return response.data.data;
};

// ===================== INSTALLMENTS =====================

export const createInstallmentPlan = async (data: {
  student_id: string;
  total_amount: number;
  installments: { amount: number; due_date: string }[];
}): Promise<{ total_amount: number; installments: Installment[] }> => {
  const response = await apiClient.post('/api/v1/payments/installments', data);
  return response.data.data;
};

// ===================== REFUNDS =====================

export const requestRefund = async (data: {
  payment_id: string;
  amount: number;
  reason: string;
}): Promise<Refund> => {
  const response = await apiClient.post('/api/v1/payments/refunds', data);
  return response.data.data;
};

export const processRefund = async (uuid: string, data: {
  status: 'approved' | 'rejected';
  refund_method?: string;
}): Promise<Refund> => {
  const response = await apiClient.put(`/api/v1/payments/refunds/${uuid}`, data);
  return response.data.data;
};

// ===================== FINES =====================

export const createFine = async (data: {
  student_id: string;
  fine_type: string;
  amount: number;
  reason: string;
  due_date?: string;
}): Promise<Fine> => {
  const response = await apiClient.post('/api/v1/payments/fines', data);
  return response.data.data;
};

// ===================== LEDGER =====================

export const getLedger = async (studentId: string): Promise<LedgerEntry[]> => {
  const response = await apiClient.get('/api/v1/payments/ledger', { params: { student_id: studentId } });
  return response.data.data;
};

// ===================== REPORTS =====================

export const getCollectionReport = async (params?: {
  date_from?: string;
  date_to?: string;
  payment_method?: string;
  session_id?: string;
}): Promise<CollectionReport> => {
  const response = await apiClient.get('/api/v1/payments/reports/collection', { params });
  return response.data.data;
};

export const getDueReport = async (params?: {
  session_id?: string;
  class_id?: string;
}): Promise<DueReport> => {
  const response = await apiClient.get('/api/v1/payments/reports/due', { params });
  return response.data.data;
};

export const getPaymentDashboard = async (): Promise<PaymentDashboard> => {
  const response = await apiClient.get('/api/v1/payments/reports/dashboard');
  return response.data.data;
};

// ===================== EXPORT =====================

export const exportPayments = async (data: {
  format: 'excel' | 'csv' | 'pdf';
  date_from?: string;
  date_to?: string;
}): Promise<string> => {
  const response = await apiClient.get('/api/v1/payments/export', { params: data });
  return response.data.data.url;
};
