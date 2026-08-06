/**
 * Library Management Types
 */

export interface BookCategory {
  id: string;
  name: string;
  name_bn?: string;
  code: string;
  description?: string;
  icon?: string;
  sort_order: number;
  is_active: boolean;
}

export interface Subject {
  id: string;
  name: string;
  name_bn?: string;
  code: string;
  category_id?: string;
  category?: { id: string; name: string };
  description?: string;
  is_active: boolean;
}

export interface Author {
  id: string;
  name: string;
  name_bn?: string;
  email?: string;
  phone?: string;
  country?: string;
  biography?: string;
  photo?: string;
  website?: string;
  is_active: boolean;
}

export interface Publisher {
  id: string;
  name: string;
  name_bn?: string;
  code: string;
  address?: string;
  city?: string;
  country?: string;
  phone?: string;
  email?: string;
  website?: string;
  description?: string;
  is_active: boolean;
}

export interface LibraryShelf {
  id: string;
  name: string;
  code: string;
  building?: string;
  floor?: string;
  room?: string;
  description?: string;
  is_active: boolean;
}

export interface LibraryRack {
  id: string;
  shelf_id: string;
  shelf?: LibraryShelf;
  name: string;
  code: string;
  row?: string;
  column?: string;
  description?: string;
  is_active: boolean;
}

export interface Book {
  id: string;
  isbn?: string;
  title: string;
  title_bn?: string;
  subtitle?: string;
  edition?: string;
  language: string;
  category_id?: string;
  category?: { id: string; name: string };
  subject_id?: string;
  subject?: { id: string; name: string };
  description?: string;
  publication_year?: number;
  pages?: number;
  price?: number;
  currency?: string;
  keywords?: string;
  cover_image?: string;
  is_digital: boolean;
  is_reference_only: boolean;
  total_copies: number;
  available_copies: number;
  is_active: boolean;
  authors?: Array<{ id: string; name: string; is_primary: boolean }>;
  publishers?: Array<{ id: string; name: string }>;
  copies?: BookCopy[];
  author_names?: string;
}

export interface BookCopy {
  id: string;
  book_id: string;
  book?: { id: string; title: string; isbn?: string };
  rack_id?: string;
  rack?: LibraryRack;
  accession_number: string;
  barcode?: string;
  qr_code?: string;
  condition: BookCondition;
  status: BookStatus;
  acquisition_date?: string;
  purchase_price?: number;
  notes?: string;
  is_active: boolean;
}

export interface LibraryMember {
  id: string;
  member_no: string;
  member_type: MemberType;
  name: string;
  email?: string;
  phone?: string;
  photo?: string;
  department?: string;
  student_id?: string;
  employee_id?: string;
  joining_date: string;
  expiry_date?: string;
  status: MemberStatus;
  max_books: number;
  max_days: number;
  fine_rate: number;
  notes?: string;
  is_active: boolean;
  issued_books_count?: number;
  unpaid_fines?: number;
}

export interface BookIssue {
  id: string;
  issue_no: string;
  member_id: string;
  member?: { id: string; member_no: string; name: string };
  book_copy_id: string;
  book_copy?: BookCopy;
  issue_date: string;
  due_date: string;
  return_date?: string;
  status: IssueStatus;
  renewal_count: number;
  max_renewals: number;
  is_overdue: boolean;
  overdue_days: number;
  notes?: string;
}

export interface BookReservation {
  id: string;
  reservation_no: string;
  member_id: string;
  member?: { id: string; member_no: string; name: string };
  book_id: string;
  book?: { id: string; title: string };
  reservation_date: string;
  expiry_date: string;
  status: ReservationStatus;
  fulfilled_date?: string;
  notify_status: string;
  notes?: string;
}

export interface LibraryFine {
  id: string;
  fine_no: string;
  member_id: string;
  member?: { id: string; member_no: string; name: string };
  issue_id?: string;
  fine_type: FineType;
  fine_type_label?: string;
  reason: string;
  amount: number;
  paid_amount: number;
  waived_amount: number;
  remaining_amount: number;
  fine_date: string;
  due_date?: string;
  paid_date?: string;
  status: FineStatus;
  payment_method?: string;
  notes?: string;
}

export interface DigitalBook {
  id: string;
  book_id?: string;
  book?: { id: string; title: string };
  title: string;
  title_bn?: string;
  category_id?: string;
  category?: { id: string; name: string };
  file_type: FileType;
  file_path: string;
  file_size?: string;
  page_count?: number;
  isbn?: string;
  author_name?: string;
  publisher?: string;
  publication_year?: number;
  language: string;
  access_type: AccessType;
  download_permission: DownloadPermission;
  view_count: number;
  download_count: number;
  description?: string;
  cover_image?: string;
  is_featured: boolean;
  is_active: boolean;
}

export interface LibraryDashboard {
  total_books: number;
  available_books: number;
  issued_books: number;
  total_members: number;
  active_issues: number;
  overdue_issues: number;
  pending_reservations: number;
  pending_fines: number;
  digital_books: number;
  today_issues: number;
  today_returns: number;
}

export interface IssueReport {
  total_issues: number;
  total_returns: number;
  total_overdue: number;
  by_member_type: Record<string, number>;
  top_books: Array<{ title: string; count: number }>;
}

export interface FineReport {
  total_fines: number;
  total_amount: number;
  total_collected: number;
  total_pending: number;
  total_waived: number;
  by_type: Record<string, { count: number; amount: number }>;
}

// Enums
export type BookCondition = 'new' | 'good' | 'fair' | 'poor';
export type BookStatus = 'available' | 'issued' | 'reserved' | 'lost' | 'damaged' | 'archived';
export type MemberType = 'student' | 'teacher' | 'employee' | 'researcher' | 'guest';
export type MemberStatus = 'active' | 'expired' | 'blocked' | 'closed';
export type IssueStatus = 'issued' | 'returned' | 'overdue' | 'lost' | 'renewed';
export type ReservationStatus = 'pending' | 'ready' | 'fulfilled' | 'expired' | 'cancelled';
export type FineType = 'late_return' | 'lost_book' | 'damaged_book' | 'membership_violation';
export type FineStatus = 'pending' | 'partial' | 'paid' | 'waived';
export type FileType = 'pdf' | 'epub' | 'docx' | 'audio' | 'video';
export type AccessType = 'public' | 'members' | 'premium' | 'restricted';
export type DownloadPermission = 'allowed' | 'not_allowed' | 'premium';

export const BOOK_CONDITIONS: Record<BookCondition, string> = {
  new: 'New',
  good: 'Good',
  fair: 'Fair',
  poor: 'Poor',
};

export const BOOK_STATUSES: Record<BookStatus, string> = {
  available: 'Available',
  issued: 'Issued',
  reserved: 'Reserved',
  lost: 'Lost',
  damaged: 'Damaged',
  archived: 'Archived',
};

export const MEMBER_TYPES: Record<MemberType, string> = {
  student: 'Student',
  teacher: 'Teacher',
  employee: 'Employee',
  researcher: 'Researcher',
  guest: 'Guest',
};

export const MEMBER_STATUSES: Record<MemberStatus, string> = {
  active: 'Active',
  expired: 'Expired',
  blocked: 'Blocked',
  closed: 'Closed',
};

export const ISSUE_STATUSES: Record<IssueStatus, string> = {
  issued: 'Issued',
  returned: 'Returned',
  overdue: 'Overdue',
  lost: 'Lost',
  renewed: 'Renewed',
};

export const RESERVATION_STATUSES: Record<ReservationStatus, string> = {
  pending: 'Pending',
  ready: 'Ready for Pickup',
  fulfilled: 'Fulfilled',
  expired: 'Expired',
  cancelled: 'Cancelled',
};

export const FINE_TYPES: Record<FineType, string> = {
  late_return: 'Late Return',
  lost_book: 'Lost Book',
  damaged_book: 'Damaged Book',
  membership_violation: 'Membership Violation',
};

export const FINE_STATUSES: Record<FineStatus, string> = {
  pending: 'Pending',
  partial: 'Partial Payment',
  paid: 'Paid',
  waived: 'Waived',
};

export const FILE_TYPES: Record<FileType, string> = {
  pdf: 'PDF',
  epub: 'EPUB',
  docx: 'Word Document',
  audio: 'Audio Book',
  video: 'Video Lecture',
};

export const ACCESS_TYPES: Record<AccessType, string> = {
  public: 'Public',
  members: 'Members Only',
  premium: 'Premium',
  restricted: 'Restricted',
};

// Paginated Response
export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}
