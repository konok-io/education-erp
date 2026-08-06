/**
 * Hostel API Service
 */

import { apiClient } from '@/lib/api-client';
import type {
  Hostel,
  Building,
  Room,
  Bed,
  HostelAllocation,
  HostelVisitor,
  GatePass,
  HostelComplaint,
  HostelMaintenanceRequest,
  HostelAttendance,
  HostelDashboard,
  PaginatedResponse,
} from '../types';

const BASE_URL = '/api/v1/hostels';

export const hostelApi = {
  // Dashboard
  getDashboard: async (): Promise<HostelDashboard> => {
    const response = await apiClient.get(`${BASE_URL}/dashboard`);
    return response.data;
  },

  // Hostels
  getHostels: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    hostel_type?: string;
    gender?: string;
    status?: string;
  }): Promise<PaginatedResponse<Hostel>> => {
    const response = await apiClient.get(`${BASE_URL}`, { params });
    return response.data;
  },

  getHostel: async (uuid: string): Promise<Hostel> => {
    const response = await apiClient.get(`${BASE_URL}/${uuid}`);
    return response.data;
  },

  createHostel: async (data: Partial<Hostel>): Promise<Hostel> => {
    const response = await apiClient.post(BASE_URL, data);
    return response.data;
  },

  updateHostel: async (uuid: string, data: Partial<Hostel>): Promise<Hostel> => {
    const response = await apiClient.put(`${BASE_URL}/${uuid}`, data);
    return response.data;
  },

  deleteHostel: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/${uuid}`);
  },

  // Buildings
  getBuildings: async (params?: {
    page?: number;
    per_page?: number;
    hostel_id?: string;
    search?: string;
  }): Promise<PaginatedResponse<Building>> => {
    const response = await apiClient.get(`${BASE_URL}/buildings`, { params });
    return response.data;
  },

  createBuilding: async (data: Partial<Building>): Promise<Building> => {
    const response = await apiClient.post(`${BASE_URL}/buildings`, data);
    return response.data;
  },

  // Rooms
  getRooms: async (params?: {
    page?: number;
    per_page?: number;
    hostel_id?: string;
    building_id?: string;
    room_type?: string;
    status?: string;
    available?: boolean;
  }): Promise<PaginatedResponse<Room>> => {
    const response = await apiClient.get(`${BASE_URL}/rooms`, { params });
    return response.data;
  },

  getRoom: async (uuid: string): Promise<Room> => {
    const response = await apiClient.get(`${BASE_URL}/rooms/${uuid}`);
    return response.data;
  },

  createRoom: async (data: Partial<Room>): Promise<Room> => {
    const response = await apiClient.post(`${BASE_URL}/rooms`, data);
    return response.data;
  },

  updateRoom: async (uuid: string, data: Partial<Room>): Promise<Room> => {
    const response = await apiClient.put(`${BASE_URL}/rooms/${uuid}`, data);
    return response.data;
  },

  // Beds
  getBeds: async (params?: {
    page?: number;
    per_page?: number;
    room_id?: string;
    hostel_id?: string;
    status?: string;
    available?: boolean;
  }): Promise<PaginatedResponse<Bed>> => {
    const response = await apiClient.get(`${BASE_URL}/beds`, { params });
    return response.data;
  },

  getAvailableBeds: async (roomId: string): Promise<Bed[]> => {
    const response = await apiClient.get(`${BASE_URL}/beds/available`, {
      params: { room_id: roomId },
    });
    return response.data;
  },

  // Allocations
  getAllocations: async (params?: {
    page?: number;
    per_page?: number;
    hostel_id?: string;
    status?: string;
    allocatable_type?: string;
    allocatable_id?: number;
  }): Promise<PaginatedResponse<HostelAllocation>> => {
    const response = await apiClient.get(`${BASE_URL}/allocations`, { params });
    return response.data;
  },

  createAllocation: async (data: Partial<HostelAllocation>): Promise<HostelAllocation> => {
    const response = await apiClient.post(`${BASE_URL}/allocations`, data);
    return response.data;
  },

  approveAllocation: async (uuid: string): Promise<HostelAllocation> => {
    const response = await apiClient.post(`${BASE_URL}/allocations/${uuid}/approve`);
    return response.data;
  },

  checkInAllocation: async (uuid: string): Promise<HostelAllocation> => {
    const response = await apiClient.post(`${BASE_URL}/allocations/${uuid}/check-in`);
    return response.data;
  },

  checkOutAllocation: async (uuid: string): Promise<HostelAllocation> => {
    const response = await apiClient.post(`${BASE_URL}/allocations/${uuid}/check-out`);
    return response.data;
  },

  // Visitors
  getVisitors: async (params?: {
    page?: number;
    per_page?: number;
    hostel_id?: string;
    visit_date?: string;
    status?: string;
  }): Promise<PaginatedResponse<HostelVisitor>> => {
    const response = await apiClient.get(`${BASE_URL}/visitors`, { params });
    return response.data;
  },

  createVisitor: async (data: Partial<HostelVisitor>): Promise<HostelVisitor> => {
    const response = await apiClient.post(`${BASE_URL}/visitors`, data);
    return response.data;
  },

  approveVisitor: async (uuid: string): Promise<HostelVisitor> => {
    const response = await apiClient.post(`${BASE_URL}/visitors/${uuid}/approve`);
    return response.data;
  },

  checkInVisitor: async (uuid: string): Promise<HostelVisitor> => {
    const response = await apiClient.post(`${BASE_URL}/visitors/${uuid}/check-in`);
    return response.data;
  },

  checkOutVisitor: async (uuid: string): Promise<HostelVisitor> => {
    const response = await apiClient.post(`${BASE_URL}/visitors/${uuid}/check-out`);
    return response.data;
  },

  // Gate Passes
  getGatePasses: async (params?: {
    page?: number;
    per_page?: number;
    hostel_id?: string;
    pass_type?: string;
    status?: string;
  }): Promise<PaginatedResponse<GatePass>> => {
    const response = await apiClient.get(`${BASE_URL}/gate-passes`, { params });
    return response.data;
  },

  createGatePass: async (data: Partial<GatePass>): Promise<GatePass> => {
    const response = await apiClient.post(`${BASE_URL}/gate-passes`, data);
    return response.data;
  },

  approveGatePass: async (uuid: string): Promise<GatePass> => {
    const response = await apiClient.post(`${BASE_URL}/gate-passes/${uuid}/approve`);
    return response.data;
  },

  // Complaints
  getComplaints: async (params?: {
    page?: number;
    per_page?: number;
    hostel_id?: string;
    complaint_type?: string;
    priority?: string;
    status?: string;
  }): Promise<PaginatedResponse<HostelComplaint>> => {
    const response = await apiClient.get(`${BASE_URL}/complaints`, { params });
    return response.data;
  },

  createComplaint: async (data: Partial<HostelComplaint>): Promise<HostelComplaint> => {
    const response = await apiClient.post(`${BASE_URL}/complaints`, data);
    return response.data;
  },

  respondToComplaint: async (uuid: string, response: string, assignedTo: string): Promise<HostelComplaint> => {
    const res = await apiClient.post(`${BASE_URL}/complaints/${uuid}/respond`, {
      response,
      assigned_to: assignedTo,
    });
    return res.data;
  },

  resolveComplaint: async (uuid: string, resolution: string): Promise<HostelComplaint> => {
    const response = await apiClient.post(`${BASE_URL}/complaints/${uuid}/resolve`, { resolution });
    return response.data;
  },

  // Maintenance Requests
  getMaintenanceRequests: async (params?: {
    page?: number;
    per_page?: number;
    hostel_id?: string;
    request_type?: string;
    status?: string;
  }): Promise<PaginatedResponse<HostelMaintenanceRequest>> => {
    const response = await apiClient.get(`${BASE_URL}/maintenance`, { params });
    return response.data;
  },

  createMaintenanceRequest: async (data: Partial<HostelMaintenanceRequest>): Promise<HostelMaintenanceRequest> => {
    const response = await apiClient.post(`${BASE_URL}/maintenance`, data);
    return response.data;
  },

  completeMaintenanceRequest: async (uuid: string, workDone: string, cost?: number): Promise<HostelMaintenanceRequest> => {
    const response = await apiClient.post(`${BASE_URL}/maintenance/${uuid}/complete`, {
      work_done: workDone,
      cost,
    });
    return response.data;
  },

  // Attendance
  getAttendances: async (params?: {
    page?: number;
    per_page?: number;
    hostel_id?: string;
    attendance_date?: string;
    attendance_type?: string;
    status?: string;
  }): Promise<PaginatedResponse<HostelAttendance>> => {
    const response = await apiClient.get(`${BASE_URL}/attendance`, { params });
    return response.data;
  },

  recordAttendance: async (data: Partial<HostelAttendance>): Promise<HostelAttendance> => {
    const response = await apiClient.post(`${BASE_URL}/attendance`, data);
    return response.data;
  },

  bulkRecordAttendance: async (records: Partial<HostelAttendance>[]): Promise<void> => {
    await apiClient.post(`${BASE_URL}/attendance/bulk`, { records });
  },
};
