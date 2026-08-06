import React, { useState, useEffect } from 'react';
import { devsecopsApi, DevSecOpsDashboard } from '../../api/devsecops';
import { 
  Activity, 
  Server, 
  GitBranch, 
  Rocket, 
  Package, 
  Shield, 
  AlertTriangle,
  CheckCircle,
  XCircle,
  Clock
} from 'lucide-react';

export const DevSecOpsDashboard: React.FC = () => {
  const [dashboard, setDashboard] = useState<DevSecOpsDashboard | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    loadDashboard();
  }, []);

  const loadDashboard = async () => {
    try {
      setLoading(true);
      const response = await devsecopsApi.getDashboard();
      setDashboard(response.data);
    } catch (err: any) {
      setError(err.message || 'Failed to load dashboard');
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="bg-red-50 border border-red-200 rounded-lg p-4">
        <div className="flex items-center gap-2 text-red-600">
          <XCircle className="w-5 h-5" />
          <span>{error}</span>
        </div>
      </div>
    );
  }

  if (!dashboard) return null;

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">DevSecOps Dashboard</h1>
        <button
          onClick={loadDashboard}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          Refresh
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard
          title="Environments"
          value={dashboard.environments.total}
          subtitle={`${dashboard.environments.active} active`}
          icon={<Server className="w-6 h-6" />}
          color="blue"
        />
        <StatCard
          title="Pipelines"
          value={dashboard.pipelines.total}
          subtitle={`${dashboard.pipelines.active} active`}
          icon={<GitBranch className="w-6 h-6" />}
          color="green"
        />
        <StatCard
          title="Active Deployments"
          value={dashboard.deployments.active}
          subtitle="Running"
          icon={<Rocket className="w-6 h-6" />}
          color="purple"
        />
        <StatCard
          title="Artifacts"
          value={dashboard.artifacts.total}
          subtitle={`${dashboard.artifacts.vulnerable} vulnerable`}
          icon={<Package className="w-6 h-6" />}
          color={dashboard.artifacts.vulnerable > 0 ? 'orange' : 'green'}
        />
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center gap-2 mb-4">
            <Activity className="w-5 h-5 text-gray-600" />
            <h2 className="text-lg font-semibold">Releases</h2>
          </div>
          <div className="space-y-3">
            <div className="flex justify-between items-center">
              <span className="text-gray-600">Total</span>
              <span className="font-semibold">{dashboard.releases.total}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-gray-600">Published</span>
              <span className="font-semibold">{dashboard.releases.published}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-gray-600">LTS</span>
              <span className="font-semibold text-blue-600">{dashboard.releases.lts}</span>
            </div>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center gap-2 mb-4">
            <Shield className="w-5 h-5 text-gray-600" />
            <h2 className="text-lg font-semibold">Security</h2>
          </div>
          <div className="space-y-3">
            <div className="flex justify-between items-center">
              <span className="text-gray-600">Total Scans</span>
              <span className="font-semibold">{dashboard.security.total_scans}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-gray-600">Success Rate</span>
              <span className="font-semibold text-green-600">
                {dashboard.security.success_rate}%
              </span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-gray-600">Critical</span>
              <span className="font-semibold text-red-600">
                {dashboard.security.critical_vulnerabilities}
              </span>
            </div>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center gap-2 mb-4">
            <Clock className="w-5 h-5 text-gray-600" />
            <h2 className="text-lg font-semibold">Activity (7 days)</h2>
          </div>
          <div className="space-y-3">
            <div className="flex justify-between items-center">
              <span className="text-gray-600">Total</span>
              <span className="font-semibold">{dashboard.activity.total_actions}</span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-gray-600">Success</span>
              <span className="font-semibold text-green-600">
                {dashboard.activity.successful_actions}
              </span>
            </div>
            <div className="flex justify-between items-center">
              <span className="text-gray-600">Failed</span>
              <span className="font-semibold text-red-600">
                {dashboard.activity.failed_actions}
              </span>
            </div>
          </div>
        </div>
      </div>

      {dashboard.artifacts.vulnerable > 0 && (
        <div className="bg-orange-50 border border-orange-200 rounded-lg p-4">
          <div className="flex items-center gap-2 text-orange-800">
            <AlertTriangle className="w-5 h-5" />
            <span className="font-semibold">
              {dashboard.artifacts.vulnerable} artifact(s) have vulnerabilities
            </span>
          </div>
        </div>
      )}
    </div>
  );
};

interface StatCardProps {
  title: string;
  value: number;
  subtitle: string;
  icon: React.ReactNode;
  color: 'blue' | 'green' | 'purple' | 'orange' | 'red';
}

const colorClasses = {
  blue: 'bg-blue-50 text-blue-600',
  green: 'bg-green-50 text-green-600',
  purple: 'bg-purple-50 text-purple-600',
  orange: 'bg-orange-50 text-orange-600',
  red: 'bg-red-50 text-red-600',
};

const StatCard: React.FC<StatCardProps> = ({ title, value, subtitle, icon, color }) => {
  return (
    <div className="bg-white rounded-lg shadow p-6">
      <div className="flex items-center justify-between">
        <div className={`p-3 rounded-lg ${colorClasses[color]}`}>
          {icon}
        </div>
      </div>
      <div className="mt-4">
        <h3 className="text-sm font-medium text-gray-600">{title}</h3>
        <p className="text-2xl font-bold text-gray-900">{value}</p>
        <p className="text-sm text-gray-500">{subtitle}</p>
      </div>
    </div>
  );
};

export default DevSecOpsDashboard;
