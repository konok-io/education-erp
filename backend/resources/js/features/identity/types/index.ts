export interface User {
  id: string;
  name: string;
  email: string;
  role: string;
  tenant_id?: string;
  avatar?: string;
}

export interface AuthTokens {
  access_token: string;
  refresh_token: string;
  token_type: string;
  expires_in: number;
}

export interface AuthResponse {
  success: boolean;
  message: string;
  data?: {
    access_token: string;
    refresh_token: string;
    token_type: string;
    expires_in: number;
    user: User;
  };
  mfa_required?: {
    factors: MFAFactor[];
    user_id: string;
  };
}

export interface Session {
  id: string;
  user_id: string;
  name: string | null;
  device_type: string;
  device_name: string | null;
  device_os: string | null;
  device_browser: string | null;
  ip_address: string | null;
  location: string | null;
  status: string;
  is_current: boolean;
  login_at: string;
  last_activity_at: string;
  token_expires_at?: string;
}

export interface MFAFactor {
  id: string;
  user_id: string;
  name: string;
  type: 'totp' | 'sms' | 'email' | 'push' | 'security_key' | 'biometric' | 'backup_code';
  factor_type: 'authenticator' | 'backup' | 'primary';
  status: 'active' | 'inactive' | 'compromised';
  phone_number?: string;
  email?: string;
  verified: boolean;
  verified_at?: string;
  last_used_at?: string;
  default: boolean;
  backup: boolean;
}

export interface TOTPSetupResponse {
  success: boolean;
  data?: {
    factor_id: string;
    secret: string;
    qr_code_url: string;
  };
}

export interface LoginCredentials {
  email: string;
  password: string;
  device_name?: string;
}

export interface MFAVerification {
  email: string;
  password: string;
  mfa_code: string;
  mfa_factor_id: string;
}

export interface PaginatedResponse<T> {
  success: boolean;
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}

export interface ApiResponse<T> {
  success: boolean;
  data?: T;
  message?: string;
}
