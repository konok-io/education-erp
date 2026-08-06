/**
 * Transport Store - State Management
 */

import { create } from 'zustand';
import { transportApi } from '../services/transportApi';
import type {
  Vehicle,
  Driver,
  Route,
  Trip,
  FuelRecord,
  VehicleMaintenance,
  TransportDashboard,
} from '../types';

interface TransportState {
  // Dashboard
  dashboard: TransportDashboard | null;
  dashboardLoading: boolean;
  dashboardError: string | null;

  // Vehicles
  vehicles: Vehicle[];
  vehiclesPagination: { current_page: number; last_page: number; total: number } | null;
  vehiclesLoading: boolean;
  selectedVehicle: Vehicle | null;

  // Drivers
  drivers: Driver[];
  driversPagination: { current_page: number; last_page: number; total: number } | null;
  driversLoading: boolean;
  selectedDriver: Driver | null;

  // Routes
  routes: Route[];
  routesPagination: { current_page: number; last_page: number; total: number } | null;
  routesLoading: boolean;
  selectedRoute: Route | null;

  // Trips
  trips: Trip[];
  tripsPagination: { current_page: number; last_page: number; total: number } | null;
  tripsLoading: boolean;

  // Fuel
  fuelRecords: FuelRecord[];
  fuelRecordsPagination: { current_page: number; last_page: number; total: number } | null;
  fuelRecordsLoading: boolean;

  // Maintenance
  maintenances: VehicleMaintenance[];
  maintenancesPagination: { current_page: number; last_page: number; total: number } | null;
  maintenancesLoading: boolean;

  // Actions
  fetchDashboard: () => Promise<void>;
  fetchVehicles: (params?: Record<string, any>) => Promise<void>;
  fetchVehicle: (uuid: string) => Promise<void>;
  createVehicle: (data: Partial<Vehicle>) => Promise<Vehicle>;
  updateVehicle: (uuid: string, data: Partial<Vehicle>) => Promise<Vehicle>;
  deleteVehicle: (uuid: string) => Promise<void>;
  updateVehicleStatus: (uuid: string, status: string) => Promise<void>;

  fetchDrivers: (params?: Record<string, any>) => Promise<void>;
  fetchDriver: (uuid: string) => Promise<void>;
  createDriver: (data: Partial<Driver>) => Promise<Driver>;
  updateDriver: (uuid: string, data: Partial<Driver>) => Promise<Driver>;
  deleteDriver: (uuid: string) => Promise<void>;

  fetchRoutes: (params?: Record<string, any>) => Promise<void>;
  fetchRoute: (uuid: string) => Promise<void>;
  createRoute: (data: Partial<Route>) => Promise<Route>;
  updateRoute: (uuid: string, data: Partial<Route>) => Promise<Route>;
  deleteRoute: (uuid: string) => Promise<void>;

  fetchTrips: (params?: Record<string, any>) => Promise<void>;
  createTrip: (data: Partial<Trip>) => Promise<Trip>;
  startTrip: (uuid: string, startOdometer: string) => Promise<void>;
  completeTrip: (uuid: string, data: { end_odometer: string; distance?: number; passenger_count?: number }) => Promise<void>;
  cancelTrip: (uuid: string) => Promise<void>;

  fetchFuelRecords: (params?: Record<string, any>) => Promise<void>;
  createFuelRecord: (data: Partial<FuelRecord>) => Promise<FuelRecord>;

  fetchMaintenances: (params?: Record<string, any>) => Promise<void>;
  createMaintenance: (data: Partial<VehicleMaintenance>) => Promise<VehicleMaintenance>;
  completeMaintenance: (uuid: string, workDone: string, cost?: number) => Promise<void>;

  resetState: () => void;
}

const initialState = {
  dashboard: null,
  dashboardLoading: false,
  dashboardError: null,
  vehicles: [],
  vehiclesPagination: null,
  vehiclesLoading: false,
  selectedVehicle: null,
  drivers: [],
  driversPagination: null,
  driversLoading: false,
  selectedDriver: null,
  routes: [],
  routesPagination: null,
  routesLoading: false,
  selectedRoute: null,
  trips: [],
  tripsPagination: null,
  tripsLoading: false,
  fuelRecords: [],
  fuelRecordsPagination: null,
  fuelRecordsLoading: false,
  maintenances: [],
  maintenancesPagination: null,
  maintenancesLoading: false,
};

export const useTransportStore = create<TransportState>((set, get) => ({
  ...initialState,

  // Dashboard
  fetchDashboard: async () => {
    set({ dashboardLoading: true, dashboardError: null });
    try {
      const dashboard = await transportApi.getDashboard();
      set({ dashboard, dashboardLoading: false });
    } catch (error: any) {
      set({ dashboardError: error.message, dashboardLoading: false });
    }
  },

  // Vehicles
  fetchVehicles: async (params) => {
    set({ vehiclesLoading: true });
    try {
      const response = await transportApi.getVehicles(params);
      set({
        vehicles: response.data,
        vehiclesPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        vehiclesLoading: false,
      });
    } catch (error) {
      set({ vehiclesLoading: false });
    }
  },

  fetchVehicle: async (uuid) => {
    set({ vehiclesLoading: true });
    try {
      const vehicle = await transportApi.getVehicle(uuid);
      set({ selectedVehicle: vehicle, vehiclesLoading: false });
    } catch (error) {
      set({ vehiclesLoading: false });
    }
  },

  createVehicle: async (data) => {
    const vehicle = await transportApi.createVehicle(data);
    const vehicles = [...get().vehicles, vehicle];
    set({ vehicles });
    return vehicle;
  },

  updateVehicle: async (uuid, data) => {
    const vehicle = await transportApi.updateVehicle(uuid, data);
    const vehicles = get().vehicles.map((v) => (v.id === uuid ? vehicle : v));
    set({ vehicles, selectedVehicle: vehicle });
    return vehicle;
  },

  deleteVehicle: async (uuid) => {
    await transportApi.deleteVehicle(uuid);
    const vehicles = get().vehicles.filter((v) => v.id !== uuid);
    set({ vehicles });
  },

  updateVehicleStatus: async (uuid, status) => {
    await transportApi.updateVehicleStatus(uuid, status);
    get().fetchVehicles();
  },

  // Drivers
  fetchDrivers: async (params) => {
    set({ driversLoading: true });
    try {
      const response = await transportApi.getDrivers(params);
      set({
        drivers: response.data,
        driversPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        driversLoading: false,
      });
    } catch (error) {
      set({ driversLoading: false });
    }
  },

  fetchDriver: async (uuid) => {
    set({ driversLoading: true });
    try {
      const driver = await transportApi.getDriver(uuid);
      set({ selectedDriver: driver, driversLoading: false });
    } catch (error) {
      set({ driversLoading: false });
    }
  },

  createDriver: async (data) => {
    const driver = await transportApi.createDriver(data);
    const drivers = [...get().drivers, driver];
    set({ drivers });
    return driver;
  },

  updateDriver: async (uuid, data) => {
    const driver = await transportApi.updateDriver(uuid, data);
    const drivers = get().drivers.map((d) => (d.id === uuid ? driver : d));
    set({ drivers, selectedDriver: driver });
    return driver;
  },

  deleteDriver: async (uuid) => {
    await transportApi.deleteDriver(uuid);
    const drivers = get().drivers.filter((d) => d.id !== uuid);
    set({ drivers });
  },

  // Routes
  fetchRoutes: async (params) => {
    set({ routesLoading: true });
    try {
      const response = await transportApi.getRoutes(params);
      set({
        routes: response.data,
        routesPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        routesLoading: false,
      });
    } catch (error) {
      set({ routesLoading: false });
    }
  },

  fetchRoute: async (uuid) => {
    set({ routesLoading: true });
    try {
      const route = await transportApi.getRoute(uuid);
      set({ selectedRoute: route, routesLoading: false });
    } catch (error) {
      set({ routesLoading: false });
    }
  },

  createRoute: async (data) => {
    const route = await transportApi.createRoute(data);
    const routes = [...get().routes, route];
    set({ routes });
    return route;
  },

  updateRoute: async (uuid, data) => {
    const route = await transportApi.updateRoute(uuid, data);
    const routes = get().routes.map((r) => (r.id === uuid ? route : r));
    set({ routes, selectedRoute: route });
    return route;
  },

  deleteRoute: async (uuid) => {
    await transportApi.deleteRoute(uuid);
    const routes = get().routes.filter((r) => r.id !== uuid);
    set({ routes });
  },

  // Trips
  fetchTrips: async (params) => {
    set({ tripsLoading: true });
    try {
      const response = await transportApi.getTrips(params);
      set({
        trips: response.data,
        tripsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        tripsLoading: false,
      });
    } catch (error) {
      set({ tripsLoading: false });
    }
  },

  createTrip: async (data) => {
    const trip = await transportApi.createTrip(data);
    const trips = [trip, ...get().trips];
    set({ trips });
    return trip;
  },

  startTrip: async (uuid, startOdometer) => {
    await transportApi.startTrip(uuid, startOdometer);
    get().fetchTrips();
  },

  completeTrip: async (uuid, data) => {
    await transportApi.completeTrip(uuid, data);
    get().fetchTrips();
  },

  cancelTrip: async (uuid) => {
    await transportApi.cancelTrip(uuid);
    get().fetchTrips();
  },

  // Fuel
  fetchFuelRecords: async (params) => {
    set({ fuelRecordsLoading: true });
    try {
      const response = await transportApi.getFuelRecords(params);
      set({
        fuelRecords: response.data,
        fuelRecordsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        fuelRecordsLoading: false,
      });
    } catch (error) {
      set({ fuelRecordsLoading: false });
    }
  },

  createFuelRecord: async (data) => {
    const record = await transportApi.createFuelRecord(data);
    const fuelRecords = [record, ...get().fuelRecords];
    set({ fuelRecords });
    return record;
  },

  // Maintenance
  fetchMaintenances: async (params) => {
    set({ maintenancesLoading: true });
    try {
      const response = await transportApi.getMaintenances(params);
      set({
        maintenances: response.data,
        maintenancesPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        maintenancesLoading: false,
      });
    } catch (error) {
      set({ maintenancesLoading: false });
    }
  },

  createMaintenance: async (data) => {
    const maintenance = await transportApi.createMaintenance(data);
    const maintenances = [...get().maintenances, maintenance];
    set({ maintenances });
    return maintenance;
  },

  completeMaintenance: async (uuid, workDone, cost) => {
    await transportApi.completeMaintenance(uuid, workDone, cost);
    get().fetchMaintenances();
  },

  // Reset
  resetState: () => set(initialState),
}));
