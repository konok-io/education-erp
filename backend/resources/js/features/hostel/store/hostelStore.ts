/**
 * Hostel Store - State Management
 */

import { create } from 'zustand';
import { hostelApi } from '../services/hostelApi';
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
} from '../types';

interface HostelState {
  // Dashboard
  dashboard: HostelDashboard | null;
  dashboardLoading: boolean;

  // Hostels
  hostels: Hostel[];
  hostelsPagination: { current_page: number; last_page: number; total: number } | null;
  hostelsLoading: boolean;
  selectedHostel: Hostel | null;

  // Buildings
  buildings: Building[];
  buildingsLoading: boolean;

  // Rooms
  rooms: Room[];
  roomsPagination: { current_page: number; last_page: number; total: number } | null;
  roomsLoading: boolean;
  selectedRoom: Room | null;

  // Beds
  beds: Bed[];
  bedsLoading: boolean;

  // Allocations
  allocations: HostelAllocation[];
  allocationsPagination: { current_page: number; last_page: number; total: number } | null;
  allocationsLoading: boolean;

  // Visitors
  visitors: HostelVisitor[];
  visitorsPagination: { current_page: number; last_page: number; total: number } | null;
  visitorsLoading: boolean;

  // Gate Passes
  gatePasses: GatePass[];
  gatePassesLoading: boolean;

  // Complaints
  complaints: HostelComplaint[];
  complaintsPagination: { current_page: number; last_page: number; total: number } | null;
  complaintsLoading: boolean;

  // Maintenance
  maintenanceRequests: HostelMaintenanceRequest[];
  maintenanceLoading: boolean;

  // Attendance
  attendances: HostelAttendance[];
  attendancesLoading: boolean;

  // Actions
  fetchDashboard: () => Promise<void>;
  fetchHostels: (params?: Record<string, any>) => Promise<void>;
  fetchHostel: (uuid: string) => Promise<void>;
  createHostel: (data: Partial<Hostel>) => Promise<Hostel>;
  updateHostel: (uuid: string, data: Partial<Hostel>) => Promise<Hostel>;
  deleteHostel: (uuid: string) => Promise<void>;

  fetchBuildings: (params?: Record<string, any>) => Promise<void>;
  createBuilding: (data: Partial<Building>) => Promise<Building>;

  fetchRooms: (params?: Record<string, any>) => Promise<void>;
  fetchRoom: (uuid: string) => Promise<void>;
  createRoom: (data: Partial<Room>) => Promise<Room>;
  updateRoom: (uuid: string, data: Partial<Room>) => Promise<Room>;

  fetchBeds: (params?: Record<string, any>) => Promise<void>;

  fetchAllocations: (params?: Record<string, any>) => Promise<void>;
  createAllocation: (data: Partial<HostelAllocation>) => Promise<HostelAllocation>;
  approveAllocation: (uuid: string) => Promise<void>;
  checkInAllocation: (uuid: string) => Promise<void>;
  checkOutAllocation: (uuid: string) => Promise<void>;

  fetchVisitors: (params?: Record<string, any>) => Promise<void>;
  createVisitor: (data: Partial<HostelVisitor>) => Promise<HostelVisitor>;
  approveVisitor: (uuid: string) => Promise<void>;
  checkInVisitor: (uuid: string) => Promise<void>;
  checkOutVisitor: (uuid: string) => Promise<void>;

  fetchGatePasses: (params?: Record<string, any>) => Promise<void>;
  createGatePass: (data: Partial<GatePass>) => Promise<GatePass>;
  approveGatePass: (uuid: string) => Promise<void>;

  fetchComplaints: (params?: Record<string, any>) => Promise<void>;
  createComplaint: (data: Partial<HostelComplaint>) => Promise<HostelComplaint>;
  respondToComplaint: (uuid: string, response: string, assignedTo: string) => Promise<void>;
  resolveComplaint: (uuid: string, resolution: string) => Promise<void>;

  fetchMaintenanceRequests: (params?: Record<string, any>) => Promise<void>;
  createMaintenanceRequest: (data: Partial<HostelMaintenanceRequest>) => Promise<HostelMaintenanceRequest>;
  completeMaintenanceRequest: (uuid: string, workDone: string, cost?: number) => Promise<void>;

  fetchAttendances: (params?: Record<string, any>) => Promise<void>;
  recordAttendance: (data: Partial<HostelAttendance>) => Promise<HostelAttendance>;

  resetState: () => void;
}

const initialState = {
  dashboard: null,
  dashboardLoading: false,
  hostels: [],
  hostelsPagination: null,
  hostelsLoading: false,
  selectedHostel: null,
  buildings: [],
  buildingsLoading: false,
  rooms: [],
  roomsPagination: null,
  roomsLoading: false,
  selectedRoom: null,
  beds: [],
  bedsLoading: false,
  allocations: [],
  allocationsPagination: null,
  allocationsLoading: false,
  visitors: [],
  visitorsPagination: null,
  visitorsLoading: false,
  gatePasses: [],
  gatePassesLoading: false,
  complaints: [],
  complaintsPagination: null,
  complaintsLoading: false,
  maintenanceRequests: [],
  maintenanceLoading: false,
  attendances: [],
  attendancesLoading: false,
};

export const useHostelStore = create<HostelState>((set, get) => ({
  ...initialState,

  // Dashboard
  fetchDashboard: async () => {
    set({ dashboardLoading: true });
    try {
      const dashboard = await hostelApi.getDashboard();
      set({ dashboard, dashboardLoading: false });
    } catch (error) {
      set({ dashboardLoading: false });
    }
  },

  // Hostels
  fetchHostels: async (params) => {
    set({ hostelsLoading: true });
    try {
      const response = await hostelApi.getHostels(params);
      set({
        hostels: response.data,
        hostelsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        hostelsLoading: false,
      });
    } catch (error) {
      set({ hostelsLoading: false });
    }
  },

  fetchHostel: async (uuid) => {
    set({ hostelsLoading: true });
    try {
      const hostel = await hostelApi.getHostel(uuid);
      set({ selectedHostel: hostel, hostelsLoading: false });
    } catch (error) {
      set({ hostelsLoading: false });
    }
  },

  createHostel: async (data) => {
    const hostel = await hostelApi.createHostel(data);
    const hostels = [...get().hostels, hostel];
    set({ hostels });
    return hostel;
  },

  updateHostel: async (uuid, data) => {
    const hostel = await hostelApi.updateHostel(uuid, data);
    const hostels = get().hostels.map((h) => (h.id === uuid ? hostel : h));
    set({ hostels, selectedHostel: hostel });
    return hostel;
  },

  deleteHostel: async (uuid) => {
    await hostelApi.deleteHostel(uuid);
    const hostels = get().hostels.filter((h) => h.id !== uuid);
    set({ hostels });
  },

  // Buildings
  fetchBuildings: async (params) => {
    set({ buildingsLoading: true });
    try {
      const response = await hostelApi.getBuildings(params);
      set({ buildings: response.data, buildingsLoading: false });
    } catch (error) {
      set({ buildingsLoading: false });
    }
  },

  createBuilding: async (data) => {
    const building = await hostelApi.createBuilding(data);
    const buildings = [...get().buildings, building];
    set({ buildings });
    return building;
  },

  // Rooms
  fetchRooms: async (params) => {
    set({ roomsLoading: true });
    try {
      const response = await hostelApi.getRooms(params);
      set({
        rooms: response.data,
        roomsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        roomsLoading: false,
      });
    } catch (error) {
      set({ roomsLoading: false });
    }
  },

  fetchRoom: async (uuid) => {
    set({ roomsLoading: true });
    try {
      const room = await hostelApi.getRoom(uuid);
      set({ selectedRoom: room, roomsLoading: false });
    } catch (error) {
      set({ roomsLoading: false });
    }
  },

  createRoom: async (data) => {
    const room = await hostelApi.createRoom(data);
    const rooms = [...get().rooms, room];
    set({ rooms });
    return room;
  },

  updateRoom: async (uuid, data) => {
    const room = await hostelApi.updateRoom(uuid, data);
    const rooms = get().rooms.map((r) => (r.id === uuid ? room : r));
    set({ rooms, selectedRoom: room });
    return room;
  },

  // Beds
  fetchBeds: async (params) => {
    set({ bedsLoading: true });
    try {
      const response = await hostelApi.getBeds(params);
      set({ beds: response.data, bedsLoading: false });
    } catch (error) {
      set({ bedsLoading: false });
    }
  },

  // Allocations
  fetchAllocations: async (params) => {
    set({ allocationsLoading: true });
    try {
      const response = await hostelApi.getAllocations(params);
      set({
        allocations: response.data,
        allocationsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        allocationsLoading: false,
      });
    } catch (error) {
      set({ allocationsLoading: false });
    }
  },

  createAllocation: async (data) => {
    const allocation = await hostelApi.createAllocation(data);
    const allocations = [allocation, ...get().allocations];
    set({ allocations });
    return allocation;
  },

  approveAllocation: async (uuid) => {
    await hostelApi.approveAllocation(uuid);
    get().fetchAllocations();
    get().fetchDashboard();
  },

  checkInAllocation: async (uuid) => {
    await hostelApi.checkInAllocation(uuid);
    get().fetchAllocations();
    get().fetchDashboard();
  },

  checkOutAllocation: async (uuid) => {
    await hostelApi.checkOutAllocation(uuid);
    get().fetchAllocations();
    get().fetchDashboard();
  },

  // Visitors
  fetchVisitors: async (params) => {
    set({ visitorsLoading: true });
    try {
      const response = await hostelApi.getVisitors(params);
      set({
        visitors: response.data,
        visitorsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        visitorsLoading: false,
      });
    } catch (error) {
      set({ visitorsLoading: false });
    }
  },

  createVisitor: async (data) => {
    const visitor = await hostelApi.createVisitor(data);
    const visitors = [visitor, ...get().visitors];
    set({ visitors });
    return visitor;
  },

  approveVisitor: async (uuid) => {
    await hostelApi.approveVisitor(uuid);
    get().fetchVisitors();
  },

  checkInVisitor: async (uuid) => {
    await hostelApi.checkInVisitor(uuid);
    get().fetchVisitors();
  },

  checkOutVisitor: async (uuid) => {
    await hostelApi.checkOutVisitor(uuid);
    get().fetchVisitors();
  },

  // Gate Passes
  fetchGatePasses: async (params) => {
    set({ gatePassesLoading: true });
    try {
      const response = await hostelApi.getGatePasses(params);
      set({ gatePasses: response.data, gatePassesLoading: false });
    } catch (error) {
      set({ gatePassesLoading: false });
    }
  },

  createGatePass: async (data) => {
    const gatePass = await hostelApi.createGatePass(data);
    const gatePasses = [gatePass, ...get().gatePasses];
    set({ gatePasses });
    return gatePass;
  },

  approveGatePass: async (uuid) => {
    await hostelApi.approveGatePass(uuid);
    get().fetchGatePasses();
  },

  // Complaints
  fetchComplaints: async (params) => {
    set({ complaintsLoading: true });
    try {
      const response = await hostelApi.getComplaints(params);
      set({
        complaints: response.data,
        complaintsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        complaintsLoading: false,
      });
    } catch (error) {
      set({ complaintsLoading: false });
    }
  },

  createComplaint: async (data) => {
    const complaint = await hostelApi.createComplaint(data);
    const complaints = [complaint, ...get().complaints];
    set({ complaints });
    return complaint;
  },

  respondToComplaint: async (uuid, response, assignedTo) => {
    await hostelApi.respondToComplaint(uuid, response, assignedTo);
    get().fetchComplaints();
  },

  resolveComplaint: async (uuid, resolution) => {
    await hostelApi.resolveComplaint(uuid, resolution);
    get().fetchComplaints();
  },

  // Maintenance
  fetchMaintenanceRequests: async (params) => {
    set({ maintenanceLoading: true });
    try {
      const response = await hostelApi.getMaintenanceRequests(params);
      set({ maintenanceRequests: response.data, maintenanceLoading: false });
    } catch (error) {
      set({ maintenanceLoading: false });
    }
  },

  createMaintenanceRequest: async (data) => {
    const request = await hostelApi.createMaintenanceRequest(data);
    const maintenanceRequests = [...get().maintenanceRequests, request];
    set({ maintenanceRequests });
    return request;
  },

  completeMaintenanceRequest: async (uuid, workDone, cost) => {
    await hostelApi.completeMaintenanceRequest(uuid, workDone, cost);
    get().fetchMaintenanceRequests();
  },

  // Attendance
  fetchAttendances: async (params) => {
    set({ attendancesLoading: true });
    try {
      const response = await hostelApi.getAttendances(params);
      set({ attendances: response.data, attendancesLoading: false });
    } catch (error) {
      set({ attendancesLoading: false });
    }
  },

  recordAttendance: async (data) => {
    const attendance = await hostelApi.recordAttendance(data);
    const attendances = [...get().attendances, attendance];
    set({ attendances });
    return attendance;
  },

  // Reset
  resetState: () => set(initialState),
}));
