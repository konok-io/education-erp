/**
 * Finance & Accounting Types
 */

export interface Account {
  id: string;
  account_code: string;
  account_name: string;
  account_name_bn?: string;
  parent_id?: string;
  account_type: AccountType;
  account_group?: AccountGroup;
  opening_balance: number;
  current_balance: number;
  dr_cr: DrCr;
  is_bank: boolean;
  is_cash: boolean;
  bank_name?: string;
  account_number?: string;
  routing_number?: string;
  is_active: boolean;
  is_system: boolean;
  parent?: { id: string; name: string };
  children?: { id: string; name: string; code: string }[];
}

export interface JournalEntry {
  id: string;
  voucher_no: string;
  voucher_type: VoucherType;
  entry_date: string;
  reference?: string;
  description: string;
  total_amount: number;
  status: JournalStatus;
  is_posted: boolean;
  posted_at?: string;
  approved_at?: string;
  remarks?: string;
  fiscal_year?: { id: string; name: string };
  details?: JournalEntryDetail[];
  creator?: { id: string; name: string };
  created_at: string;
}

export interface JournalEntryDetail {
  id: string;
  account_id: string;
  cost_center_id?: string;
  dr_cr: DrCr;
  amount: number;
  cheque_no?: string;
  cheque_date?: string;
  narration?: string;
  account?: { id: string; name: string; code: string };
  cost_center?: { id: string; name: string };
}

export interface FiscalYear {
  id: string;
  name: string;
  start_date: string;
  end_date: string;
  is_current: boolean;
  is_closed: boolean;
  closed_at?: string;
  status: FiscalYearStatus;
}

export interface CostCenter {
  id: string;
  name: string;
  name_bn?: string;
  code: string;
  center_type: CostCenterType;
  parent_id?: string;
  budget_amount: number;
  spent_amount: number;
  is_active: boolean;
}

export interface Asset {
  id: string;
  asset_code: string;
  name: string;
  name_bn?: string;
  account_id: string;
  asset_type: AssetType;
  purchase_date: string;
  purchase_cost: number;
  current_value: number;
  salvage_value: number;
  useful_life: number;
  depreciation_method: DepreciationMethod;
  depreciation_rate: number;
  accumulated_depreciation: number;
  supplier?: string;
  location?: string;
  status: AssetStatus;
}

export interface Budget {
  id: string;
  budget_code: string;
  name: string;
  fiscal_year_id: string;
  cost_center_id?: string;
  account_id?: string;
  budget_type: BudgetType;
  amount: number;
  allocated_amount: number;
  spent_amount: number;
  start_date: string;
  end_date: string;
  status: BudgetStatus;
}

export interface LedgerEntry {
  date: string;
  voucher_no: string;
  description: string;
  dr: number;
  cr: number;
  balance: number;
  cost_center?: string;
}

export interface LedgerReport {
  account: { id: string; name: string; code: string; type: string };
  opening_balance: number;
  opening_dr_cr: DrCr;
  entries: LedgerEntry[];
  totals: { dr: number; cr: number; balance: number };
}

export interface TrialBalanceReport {
  date: string;
  accounts: { account_code: string; account_name: string; dr: number; cr: number }[];
  totals: { dr: number; cr: number; is_balanced: boolean };
}

export interface ProfitLossReport {
  date_from: string;
  date_to: string;
  income: { total: number; details: { account: string; amount: number }[] };
  expense: { total: number; details: { account: string; amount: number }[] };
  net_profit: number;
  net_loss: number;
}

export interface BalanceSheetReport {
  date: string;
  assets: { total: number; details: { account: string; amount: number }[] };
  liabilities: { total: number; details: { account: string; amount: number }[] };
  equity: { total: number; details: { account: string; amount: number }[] };
  check: { assets: number; liabilities_equity: number; is_balanced: boolean };
}

export interface CashBookReport {
  account: string;
  date_from: string;
  date_to: string;
  opening_balance: number;
  cash_in: number;
  cash_out: number;
  closing_balance: number;
  entries: { date: string; voucher: string; description: string; dr: number; cr: number }[];
}

export interface FinanceDashboard {
  cash_balance: number;
  bank_balance: number;
  total_balance: number;
  today: { income: number; expense: number };
  month: { income: number; expense: number; net: number };
  pending_vouchers: number;
}

// Enums
export type AccountType = 'asset' | 'liability' | 'equity' | 'income' | 'expense';

export type AccountGroup = 'cash' | 'bank' | 'receivable' | 'payable' | 'capital' | 'sales' | 'purchase' | 'salary' | 'utility' | 'tax' | 'inventory' | 'fixed_asset';

export type VoucherType = 'journal' | 'payment' | 'receipt' | 'contra' | 'adjustment' | 'opening' | 'closing';

export type JournalStatus = 'draft' | 'verified' | 'approved' | 'posted' | 'locked';

export type DrCr = 'dr' | 'cr';

export type FiscalYearStatus = 'open' | 'active' | 'closed';

export type CostCenterType = 'department' | 'campus' | 'project' | 'event' | 'research';

export type AssetType = 'land' | 'building' | 'furniture' | 'computer' | 'vehicle' | 'equipment' | 'library' | 'other';

export type DepreciationMethod = 'straight_line' | 'wdv';

export type AssetStatus = 'active' | 'disposed' | 'sold';

export type BudgetType = 'annual' | 'monthly' | 'quarterly' | 'project';

export type BudgetStatus = 'draft' | 'approved' | 'active' | 'exceeded' | 'closed';

// Constants
export const ACCOUNT_TYPES: Record<AccountType, string> = {
  asset: 'Assets',
  liability: 'Liabilities',
  equity: 'Equity',
  income: 'Income',
  expense: 'Expense',
};

export const VOUCHER_TYPES: Record<VoucherType, string> = {
  journal: 'Journal Voucher',
  payment: 'Payment Voucher',
  receipt: 'Receipt Voucher',
  contra: 'Contra Voucher',
  adjustment: 'Adjustment Voucher',
  opening: 'Opening Voucher',
  closing: 'Closing Voucher',
};

export const ASSET_TYPES: Record<AssetType, string> = {
  land: 'Land',
  building: 'Building',
  furniture: 'Furniture',
  computer: 'Computer',
  vehicle: 'Vehicle',
  equipment: 'Equipment',
  library: 'Library Books',
  other: 'Other',
};
