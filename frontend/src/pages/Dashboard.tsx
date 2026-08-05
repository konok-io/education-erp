import { useQuery } from '@tanstack/react-query'
import axios from 'axios'

interface HealthResponse {
  status: string
  timestamp: string
  version: string
  checks: {
    database?: {
      status: string
      driver?: string
    }
  }
}

export default function Dashboard() {
  const { data, isLoading, error } = useQuery<HealthResponse>({
    queryKey: ['health'],
    queryFn: async () => {
      const response = await axios.get('/api/health')
      return response.data
    },
  })

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h2 className="text-2xl font-bold text-gray-900">Dashboard</h2>
        <span className="text-sm text-gray-500">
          Education ERP & CMS Platform
        </span>
      </div>

      {/* Status Cards */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-500">API Status</p>
              {isLoading ? (
                <p className="text-xl font-semibold text-gray-400">Loading...</p>
              ) : error ? (
                <p className="text-xl font-semibold text-red-500">Offline</p>
              ) : (
                <p className="text-xl font-semibold text-green-500">
                  {data?.status === 'healthy' ? 'Online' : 'Degraded'}
                </p>
              )}
            </div>
            <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
              <span className="text-blue-600 text-xl">⚡</span>
            </div>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-500">Version</p>
              <p className="text-xl font-semibold text-gray-900">
                {data?.version || 'v1.0.0'}
              </p>
            </div>
            <div className="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
              <span className="text-green-600 text-xl">📦</span>
            </div>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-sm text-gray-500">Database</p>
              <p className="text-xl font-semibold text-gray-900">
                {data?.checks?.database?.status === 'up' ? 'Connected' : 'N/A'}
              </p>
            </div>
            <div className="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
              <span className="text-purple-600 text-xl">🗄️</span>
            </div>
          </div>
        </div>
      </div>

      {/* Welcome Section */}
      <div className="bg-white rounded-lg shadow p-6">
        <h3 className="text-lg font-semibold text-gray-900 mb-4">
          Welcome to Education ERP
        </h3>
        <p className="text-gray-600">
          This is the main dashboard of the Education ERP & CMS platform.
          The React frontend is successfully connected to the Laravel backend.
        </p>
      </div>
    </div>
  )
}
