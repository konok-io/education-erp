import { create } from 'zustand';
import type {
  HRDashboard,
  Payroll,
  Leave,
  Loan,
  Overtime,
  AdvanceSalary,
  Bonus,
  Increment,
  Promotion,
  EmployeeExit,
  ProvidentFund,
  TaxSlab,
  EmployeeTaxRecord,
  Employee,
  SalaryGrade,
  LeaveType,
  Holiday,
} from '../types';

interface HRState {
  // Data
  dashboard: HRDashboard | null;
  employees: Employee[];
  payrolls: Payroll[];
  leaves: Leave[];
  loans: Loan[];
  overtimes: Overtime[];
  advanceSalaries: AdvanceSalary[];
  bonuses: Bonus[];
  increments: Increment[];
  promotions: Promotion[];
  employeeExits: EmployeeExit[];
  providentFunds: ProvidentFund[];
  taxSlabs: TaxSlab[];
  employeeTaxRecords: EmployeeTaxRecord[];
  salaryGrades: SalaryGrade[];
  leaveTypes: LeaveType[];
  holidays: Holiday[];

  // Selected items
  selectedPayroll: Payroll | null;
  selectedLeave: Leave | null;
  selectedLoan: Loan | null;
  selectedEmployee: Employee | null;

  // Filters
  payrollFilters: {
    month: number;
    year: number;
    status: string;
  };
  leaveFilters: {
    status: string;
    leave_type_id: string;
    date_from: string;
    date_to: string;
  };
  loanFilters: {
    status: string;
    loan_type: string;
  };
  overtimeFilters: {
    status: string;
    month: number;
    year: number;
  };

  // Loading states
  loading: boolean;
  error: string | null;

  // Actions
  setDashboard: (dashboard: HRDashboard | null) => void;
  setEmployees: (employees: Employee[]) => void;
  setPayrolls: (payrolls: Payroll[]) => void;
  setLeaves: (leaves: Leave[]) => void;
  setLoans: (loans: Loan[]) => void;
  setOvertimes: (overtimes: Overtime[]) => void;
  setAdvanceSalaries: (advanceSalaries: AdvanceSalary[]) => void;
  setBonuses: (bonuses: Bonus[]) => void;
  setIncrements: (increments: Increment[]) => void;
  setPromotions: (promotions: Promotion[]) => void;
  setEmployeeExits: (exits: EmployeeExit[]) => void;
  setProvidentFunds: (funds: ProvidentFund[]) => void;
  setTaxSlabs: (slabs: TaxSlab[]) => void;
  setEmployeeTaxRecords: (records: EmployeeTaxRecord[]) => void;
  setSalaryGrades: (grades: SalaryGrade[]) => void;
  setLeaveTypes: (types: LeaveType[]) => void;
  setHolidays: (holidays: Holiday[]) => void;

  setSelectedPayroll: (payroll: Payroll | null) => void;
  setSelectedLeave: (leave: Leave | null) => void;
  setSelectedLoan: (loan: Loan | null) => void;
  setSelectedEmployee: (employee: Employee | null) => void;

  setPayrollFilters: (filters: Partial<HRState['payrollFilters']>) => void;
  setLeaveFilters: (filters: Partial<HRState['leaveFilters']>) => void;
  setLoanFilters: (filters: Partial<HRState['loanFilters']>) => void;
  setOvertimeFilters: (filters: Partial<HRState['overtimeFilters']>) => void;

  setLoading: (loading: boolean) => void;
  setError: (error: string | null) => void;

  // Add new items to lists
  addPayroll: (payroll: Payroll) => void;
  updatePayroll: (uuid: string, data: Partial<Payroll>) => void;
  addLeave: (leave: Leave) => void;
  updateLeave: (uuid: string, data: Partial<Leave>) => void;
  addLoan: (loan: Loan) => void;
  updateLoan: (uuid: string, data: Partial<Loan>) => void;
  addBonus: (bonus: Bonus) => void;
  addIncrement: (increment: Increment) => void;
  addPromotion: (promotion: Promotion) => void;
  addEmployeeExit: (exit: EmployeeExit) => void;

  // Reset
  reset: () => void;
}

const initialFilters = {
  payrollFilters: {
    month: new Date().getMonth() + 1,
    year: new Date().getFullYear(),
    status: '',
  },
  leaveFilters: {
    status: '',
    leave_type_id: '',
    date_from: '',
    date_to: '',
  },
  loanFilters: {
    status: '',
    loan_type: '',
  },
  overtimeFilters: {
    status: '',
    month: new Date().getMonth() + 1,
    year: new Date().getFullYear(),
  },
};

export const useHRStore = create<HRState>((set) => ({
  // Initial data
  dashboard: null,
  employees: [],
  payrolls: [],
  leaves: [],
  loans: [],
  overtimes: [],
  advanceSalaries: [],
  bonuses: [],
  increments: [],
  promotions: [],
  employeeExits: [],
  providentFunds: [],
  taxSlabs: [],
  employeeTaxRecords: [],
  salaryGrades: [],
  leaveTypes: [],
  holidays: [],

  // Selected items
  selectedPayroll: null,
  selectedLeave: null,
  selectedLoan: null,
  selectedEmployee: null,

  // Filters
  ...initialFilters,

  // Loading states
  loading: false,
  error: null,

  // Actions
  setDashboard: (dashboard) => set({ dashboard }),
  setEmployees: (employees) => set({ employees }),
  setPayrolls: (payrolls) => set({ payrolls }),
  setLeaves: (leaves) => set({ leaves }),
  setLoans: (loans) => set({ loans }),
  setOvertimes: (overtimes) => set({ overtimes }),
  setAdvanceSalaries: (advanceSalaries) => set({ advanceSalaries }),
  setBonuses: (bonuses) => set({ bonuses }),
  setIncrements: (increments) => set({ increments }),
  setPromotions: (promotions) => set({ promotions }),
  setEmployeeExits: (employeeExits) => set({ employeeExits }),
  setProvidentFunds: (providentFunds) => set({ providentFunds }),
  setTaxSlabs: (taxSlabs) => set({ taxSlabs }),
  setEmployeeTaxRecords: (employeeTaxRecords) => set({ employeeTaxRecords }),
  setSalaryGrades: (salaryGrades) => set({ salaryGrades }),
  setLeaveTypes: (leaveTypes) => set({ leaveTypes }),
  setHolidays: (holidays) => set({ holidays }),

  setSelectedPayroll: (selectedPayroll) => set({ selectedPayroll }),
  setSelectedLeave: (selectedLeave) => set({ selectedLeave }),
  setSelectedLoan: (selectedLoan) => set({ selectedLoan }),
  setSelectedEmployee: (selectedEmployee) => set({ selectedEmployee }),

  setPayrollFilters: (filters) =>
    set((state) => ({ payrollFilters: { ...state.payrollFilters, ...filters } })),
  setLeaveFilters: (filters) =>
    set((state) => ({ leaveFilters: { ...state.leaveFilters, ...filters } })),
  setLoanFilters: (filters) =>
    set((state) => ({ loanFilters: { ...state.loanFilters, ...filters } })),
  setOvertimeFilters: (filters) =>
    set((state) => ({ overtimeFilters: { ...state.overtimeFilters, ...filters } })),

  setLoading: (loading) => set({ loading }),
  setError: (error) => set({ error }),

  // Add new items
  addPayroll: (payroll) =>
    set((state) => ({ payrolls: [payroll, ...state.payrolls] })),
  updatePayroll: (uuid, data) =>
    set((state) => ({
      payrolls: state.payrolls.map((p) =>
        p.id === uuid ? { ...p, ...data } : p
      ),
    })),
  addLeave: (leave) =>
    set((state) => ({ leaves: [leave, ...state.leaves] })),
  updateLeave: (uuid, data) =>
    set((state) => ({
      leaves: state.leaves.map((l) =>
        l.id === uuid ? { ...l, ...data } : l
      ),
    })),
  addLoan: (loan) =>
    set((state) => ({ loans: [loan, ...state.loans] })),
  updateLoan: (uuid, data) =>
    set((state) => ({
      loans: state.loans.map((l) =>
        l.id === uuid ? { ...l, ...data } : l
      ),
    })),
  addBonus: (bonus) =>
    set((state) => ({ bonuses: [bonus, ...state.bonuses] })),
  addIncrement: (increment) =>
    set((state) => ({ increments: [increment, ...state.increments] })),
  addPromotion: (promotion) =>
    set((state) => ({ promotions: [promotion, ...state.promotions] })),
  addEmployeeExit: (exit) =>
    set((state) => ({ employeeExits: [exit, ...state.employeeExits] })),

  // Reset
  reset: () =>
    set({
      ...initialFilters,
      dashboard: null,
      employees: [],
      payrolls: [],
      leaves: [],
      loans: [],
      overtimes: [],
      advanceSalaries: [],
      bonuses: [],
      increments: [],
      promotions: [],
      employeeExits: [],
      providentFunds: [],
      taxSlabs: [],
      employeeTaxRecords: [],
      salaryGrades: [],
      leaveTypes: [],
      holidays: [],
      selectedPayroll: null,
      selectedLeave: null,
      selectedLoan: null,
      selectedEmployee: null,
      loading: false,
      error: null,
    }),
}));
