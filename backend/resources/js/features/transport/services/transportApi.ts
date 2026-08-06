/**
 * Transport API Service
 */

import { apiClient } from '@/lib/api-client';
import type {
  Vehicle,
  Driver,
  Route,
  Trip,
  FuelRecord,
  VehicleMaintenance,
  TransportDashboard,
  PaginatedResponse,
} from '../types';

const BASE_URL = '/api/v1/transport';

export const transportApi = {
  // Dashboard
  getDashboard: async (): Promise<TransportDashboard> => {
    const response = await apiClient.get(`${BASE_URL}/dashboard`);
    return response.data;
  },

  // Vehicles
  getVehicles: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    vehicle_type?: string;
    status?: string;
    fuel_type?: string;
  }): Promise<PaginatedResponse<Vehicle>> => {
    const response = await apiClient.get(`${BASE_URL}/vehicles`, { params });
    return response.data;
  },

  getVehicle: async (uuid: string): Promise<Vehicle> => {
    const response = await apiClient.get(`${BASE_URL}/vehicles/${uuid}`);
    return response.data;
  },

  createVehicle: async (data: Partial<Vehicle>): Promise<Vehicle> => {
    const response = await apiClient.post(`${BASE_URL}/vehicles`, data);
    return response.data;
  },

  updateVehicle: async (uuid: string, data: Partial<Vehicle>): Promise<Vehicle> => {
    const response = await apiClient.put(`${BASE_URL}/vehicles/${uuid}`, data);
    return response.data;
  },

  deleteVehicle: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/vehicles/${uuid}`);
  },

  updateVehicleStatus: async (uuid: string, status: string): Promise<Vehicle> => {
    const response = await apiClient.put(`${BASE_URL}/vehicles/${uuid}/status`, { status });
    return response.data;
  },

  // Drivers
  getDrivers: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    status?: string;
    license_expiring?: number;
  }): Promise<PaginatedResponse<Driver>> => {
    const response = await apiClient.get(`${BASE_URL}/drivers`, { params });
    return response.data;
  },

  getDriver: async (uuid: string): Promise<Driver> => {
    const response = await apiClient.get(`${BASE_URL}/drivers/${uuid}`);
    return response.data;
  },

  createDriver: async (data: Partial<Driver>): Promise<Driver> => {
    const response = await apiClient.post(`${BASE_URL}/drivers`, data);
    return response.data;
  },

  updateDriver: async (uuid: string, data: Partial<Driver>): Promise<Driver> => {
    const response = await apiClient.put(`${BASE_URL}/drivers/${uuid}`, data);
    return response.data;
  },

  deleteDriver: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/drivers/${uuid}`);
  },

  // Routes
  getRoutes: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    status?: string;
  }): Promise<PaginatedResponse<Route>> => {
    const response = await apiClient.get(`${BASE_URL}/routes`, { params });
    return response.data;
  },

  getRoute: async (uuid: string): Promise<Route> => {
    const response = await apiClient.get(`${BASE_URL}/routes/${uuid}`);
    return response.data;
  },

  createRoute: async (data: Partial<Route>): Promise<Route> => {
    const response = await apiClient.post(`${BASE_URL}/routes`, data);
    return response.data;
  },

  updateRoute: async (uuid: string, data: Partial<Route>): Promise<Route> => {
    const response = await apiClient.put(`${BASE_URL}/routes/${uuid}`, data);
    return response.data;
  },

  deleteRoute: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/routes/${uuid}`);
  },

  // Trips
  getTrips: async (params?: {
    page?: number;
    per_page?: number;
    trip_date?: string;
    vehicle_id?: string;
    driver_id?: string;
    trip_type?: string;
    status?: string;
  }): Promise<PaginatedResponse<Trip>> => {
    const response = await apiClient.get(`${BASE_URL}/trips`, { params });
    return response.data;
  },

  getTrip: async (uuid: string): Promise<Trip> => {
    const response = await apiClient.get(`${BASE_URL}/trips/${uuid}`);
    return response.data;
  },

  createTrip: async (data: Partial<Trip>): Promise<Trip> => {
    const response = await apiClient.post(`${BASE_URL}/trips`, data);
    return response.data;
  },

  updateTrip: async (uuid: string, data: Partial<Trip>): Promise<Trip> => {
    const response = await apiClient.put(`${BASE_URL}/trips/${uuid}`, data);
    return response.data;
  },

  startTrip: async (uuid: string, startOdometer: string): Promise<Trip> => {
    const response = await apiClient.post(`${BASE_URL}/trips/${uuid}/start`, { start_odometer: startOdometer });
    return response.data;
  },

  completeTrip: async (uuid: string, data: {
    end_odometer: string;
    distance?: number;
    passenger_count?: number;
  }): Promise<Trip> => {
    const response = await apiClient.post(`${BASE_URL}/trips/${uuid}/complete`, data);
    return response.data;
  },

  cancelTrip: async (uuid: string): Promise<Trip> => {
    const response = await apiClient.post(`${BASE_URL}/trips/${uuid}/cancel`);
    return response.data;
  },

  // Fuel
  getFuelRecords: async (params?: {
    page?: number;
    per_page?: number;
    vehicle_id?: string;
    date_from?: string;
    date_to?: string;
  }): Promise<PaginatedResponse<FuelRecord>> => {
    const response = await apiClient.get(`${BASE_URL}/fuel`, { params });
    return response.data;
  },

  createFuelRecord: async (data: Partial<FuelRecord>): Promise<FuelRecord> => {
    const response = await apiClient.post(`${BASE_URL}/fuel`, data);
    return response.data;
  },

  getMonthlyFuelCost: async (vehicleId?: string): Promise<{
    total_cost: number;
    total_quantity: number;
    record_count: number;
  }> => {
    const response = await apiClient.get(`${BASE_URL}/fuel/monthly-cost`, {
      params: vehicleId ? { vehicle_id: vehicleId } : {},
    });
    return response.data;
  },

  // Maintenance
  getMaintenances: async (params?: {
    page?: number;
    per_page?: number;
    vehicle_id?: string;
    status?: string;
    maintenance_type?: string;
  }): Promise<PaginatedResponse<VehicleMaintenance>> => {
    const response = await apiClient.get(`${BASE_URL}/maintenances`, { params });
    return response.data;
  },

  createMaintenance: async (data: Partial<VehicleMaintenance>): Promise<VehicleMaintenance> => {
    const response = await apiClient.post(`${BASE_URL}/maintenances`, data);
    return response.data;
  },

  completeMaintenance: async (uuid: string, workDone: string, cost?: number): Promise<VehicleMaintenance> => {
    const response = await apiClient.post(`${BASE_URL}/maintenances/${uuid}/complete`, {
      work_done: workDone,
      cost,
    });
    return response.data;
  },
};
