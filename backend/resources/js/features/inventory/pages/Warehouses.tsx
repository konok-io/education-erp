/**
 * Warehouses Page
 */

import React, { useEffect, useState } from 'react';
import { useInventoryStore } from '../store/inventoryStore';
import { Link } from 'react-router-dom';
import { Plus, Search, Eye, Edit, Warehouse as WarehouseIcon, Building } from 'lucide-react';
import { WAREHOUSE_TYPES } from '../types';

export const Warehouses: React.FC = () => {
  const { warehouses, warehousesLoading, fetchWarehouses } = useInventoryStore();
  const [search, setSearch] = useState('');

  useEffect(() => {
    fetchWarehouses();
  }, [fetchWarehouses]);

  const filteredWarehouses = warehouses.filter(w =>
    w.name.toLowerCase().includes(search.toLowerCase()) ||
    w.code.toLowerCase().includes(search.toLowerCase())
  );

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Warehouses</h1>
          <p className="text-gray-500">Manage inventory storage locations</p>
        </div>
        <button className="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
          <Plus className="w-4 h-4" />
          Add Warehouse
        </button>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl p-4 shadow-sm">
        <div className="relative max-w-md">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
          <input
            type="text"
            placeholder="Search warehouses..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
          />
        </div>
      </div>

      {/* Warehouses Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {warehousesLoading ? (
          <div className="col-span-full flex items-center justify-center h-64">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
          </div>
        ) : filteredWarehouses.length === 0 ? (
          <div className="col-span-full flex flex-col items-center justify-center h-64">
            <WarehouseIcon className="w-12 h-12 text-gray-400 mb-4" />
            <p className="text-gray-500">No warehouses found</p>
          </div>
        ) : (
          filteredWarehouses.map((warehouse) => (
            <div key={warehouse.id} className="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
              <div className="flex items-start justify-between">
                <div className="flex items-center gap-3">
                  <div className="p-3 bg-blue-100 rounded-lg">
                    <Building className="w-6 h-6 text-blue-600" />
                  </div>
                  <div>
                    <h3 className="font-semibold text-gray-900">{warehouse.name}</h3>
                    <p className="text-sm text-gray-500 font-mono">{warehouse.code}</p>
                  </div>
                </div>
                <span className="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded">
                  {WAREHOUSE_TYPES[warehouse.type as keyof typeof WAREHOUSE_TYPES] || warehouse.type}
                </span>
              </div>

              <div className="mt-4 space-y-2 text-sm">
                {warehouse.manager_name && (
                  <p className="text-gray-600">
                    <span className="font-medium">Manager:</span> {warehouse.manager_name}
                  </p>
                )}
                {warehouse.phone && (
                  <p className="text-gray-600">
                    <span className="font-medium">Phone:</span> {warehouse.phone}
                  </p>
                )}
                {warehouse.building && (
                  <p className="text-gray-600">
                    <span className="font-medium">Location:</span> {warehouse.building}
                    {warehouse.floor && `, Floor ${warehouse.floor}`}
                  </p>
                )}
              </div>

              <div className="mt-4 pt-4 border-t flex justify-end gap-2">
                <button className="p-2 text-gray-400 hover:text-primary-600">
                  <Eye className="w-4 h-4" />
                </button>
                <button className="p-2 text-gray-400 hover:text-primary-600">
                  <Edit className="w-4 h-4" />
                </button>
              </div>
            </div>
          ))
        )}
      </div>
    </div>
  );
};
