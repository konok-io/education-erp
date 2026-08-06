// API Response Types
export interface ApiResponse<T = unknown> {
  data: T
  message?: string
  status: number
}

export interface HealthResponse {
  status: 'healthy' | 'degraded'
  timestamp: string
  version: string
  checks: {
    database?: {
      status: 'up' | 'down'
      driver?: string
      error?: string
    }
  }
}

// User Types
export interface User {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  created_at: string
  updated_at: string
}

// Pagination Types
export interface PaginationParams {
  page?: number
  per_page?: number
  sort_by?: string
  sort_order?: 'asc' | 'desc'
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

// Form Types
export interface LoginForm {
  email: string
  password: string
}

export interface RegisterForm {
  name: string
  email: string
  password: string
  password_confirmation: string
}
