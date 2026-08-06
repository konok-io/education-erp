/**
 * Payment & Fee Management Types
 */

export interface FeeCategory {
  id: string;
  name: string;
  name_bn?: string;
  code: string;
  category_type: FeeCategoryType;
  is_system: boolean;
  is_active: boolean;
}

export interface FeeStructure {
  id: string;
  category_id: string;
  session_id?: string;
  academic_level_id?: string;
  program_id?: string;
  semester_id?: string;
  name: string;
  amount: number;
  frequency: FeeFrequency;
  effective_date: string;
  expiry_date?: string;
  is_mandatory: boolean;
  is_active: boolean;
  category?: { id: string; name: string };
}

export interface Invoice {
  id: string;
  invoice_no: string;
  student_id: string;
  category_id: string;
  total_amount: number;
  discount_amount: number;
  fine_amount: number;
  waiver_amount: number;
  net_amount: number;
  paid_amount: number;
  due_amount: number;
  due_date: string;
  status: InvoiceStatus;
  billing_month?: string;
  billing_year?: number;
  remarks?: string;
  student?: { id: string; student_no: string; name: string; class?: string };
  category?: { id: string; name: string };
  payments?: Payment[];
  waivers?: Waiver[];
  created_at: string;
}

export interface Payment {
  id: string;
  payment_no: string;
  receipt_no: string;
  invoice_id: string;
  student_id: string;
  amount: number;
  payment_type: string;
  payment_method: PaymentMethod;
  gateway_name?: string;
  transaction_id?: string;
  payment_date: string;
  collected_by?: number;
  collected_by_name?: string;
  status: PaymentStatus;
  bank_name?: string;
  branch_name?: string;
}

export interface Receipt {
  receipt_no: string;
  payment_no: string;
  date: string;
  student: { name: string; student_no: string };
  invoice: { no: string };
  amount: number;
  amount_in_words: string;
  payment_method: string;
  transaction_id?: string;
  collected_by: string;
}

export interface Waiver {
  id: string;
  invoice_id: string;
  student_id: string;
  waiver_type: WaiverType;
  amount: number;
  percentage?: number;
  reason: string;
  approved_at?: string;
  status: string;
}

export interface Installment {
  id: string;
  student_id: string;
  installment_no: number;
  amount: number;
  due_date: string;
  paid_date?: string;
  paid_amount: number;
  status: InstallmentStatus;
}

export interface Refund {
  id: string;
  refund_no: string;
  payment_id: string;
  student_id: string;
  invoice_id?: string;
  amount: number;
  reason: string;
  payment_method: string;
  refund_method?: string;
  transaction_id?: string;
  requested_at: string;
  approved_at?: string;
  processed_at?: string;
  status: RefundStatus;
}

export interface Fine {
  id: string;
  student_id: string;
  fine_type: FineType;
  amount: number;
  reason: string;
  due_date: string;
  paid_date?: string;
  paid_amount: number;
  status: FineStatus;
}

export interface PaymentDashboard {
  today_collection: number;
  month_collection: number;
  total_due: number;
  pending_invoices: number;
  overdue_invoices: number;
}

export interface CollectionReport {
  total_collection: number;
  by_method: Record<string, number>;
  daily: Record<string, number>;
  total_transactions: number;
}

export interface DueReport {
  total_due: number;
  total_invoices: number;
  by_category: Record<string, { count: number; amount: number }>;
  overdue: number;
}

export interface LedgerEntry {
  date: string;
  type: string;
  description: string;
  amount: number;
  balance: number;
}

// Enums
export type FeeCategoryType = 
  | 'admission' 
  | 'registration' 
  | 'tuition' 
  | 'exam' 
  | 'library' 
  | 'laboratory' 
  | 'sports' 
  | 'transport' 
  | 'hostel' 
  | 'certificate' 
  | 'development' 
  | 'fine' 
  | 'miscellaneous';

export type FeeFrequency = 'one_time' | 'monthly' | 'quarterly' | 'half_yearly' | 'yearly' | 'custom';

export type InvoiceStatus = 'draft' | 'pending' | 'partial' | 'paid' | 'overdue' | 'cancelled';

export type PaymentMethod = 'cash' | 'bank' | 'cheque' | 'bkash' | 'nagad' | 'rocket' | 'sslcommerz' | 'stripe' | 'paypal';

export type PaymentStatus = 'pending' | 'paid' | 'failed' | 'refunded' | 'cancelled';

export type WaiverType = 'employee' | 'sibling' | 'special' | 'scholarship' | 'manual';

export type InstallmentStatus = 'pending' | 'paid' | 'overdue' | 'cancelled';

export type RefundStatus = 'pending' | 'approved' | 'processing' | 'completed' | 'rejected';

export type FineType = 'late_payment' | 'late_registration' | 'late_exam_fee' | 'damage' | 'library' | 'other';

export type FineStatus = 'pending' | 'paid' | 'waived' | 'cancelled';

// Constants
export const PAYMENT_METHODS: Record<PaymentMethod, string> = {
  cash: 'Cash',
  bank: 'Bank Transfer',
  cheque: 'Cheque',
  bkash: 'bKash',
  nagad: 'Nagad',
  rocket: 'Rocket',
  sslcommerz: 'SSLCommerz',
  stripe: 'Stripe',
  paypal: 'PayPal',
};

export const INVOICE_STATUSES: Record<InvoiceStatus, string> = {
  draft: 'Draft',
  pending: 'Pending',
  partial: 'Partial',
  paid: 'Paid',
  overdue: 'Overdue',
  cancelled: 'Cancelled',
};

export const FEE_CATEGORY_TYPES: Record<FeeCategoryType, string> = {
  admission: 'Admission Fee',
  registration: 'Registration Fee',
  tuition: 'Tuition Fee',
  exam: 'Exam Fee',
  library: 'Library Fee',
  laboratory: 'Laboratory Fee',
  sports: 'Sports Fee',
  transport: 'Transport Fee',
  hostel: 'Hostel Fee',
  certificate: 'Certificate Fee',
  development: 'Development Fee',
  fine: 'Fine',
  miscellaneous: 'Miscellaneous',
};
