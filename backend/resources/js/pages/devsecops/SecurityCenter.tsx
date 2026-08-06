import React, { useState, useEffect } from 'react';
import { devsecopsApi, SecurityScan, Artifact } from '../../api/devsecops';
import { 
  Shield, 
  AlertTriangle, 
  CheckCircle, 
  XCircle, 
  Loader,
  Search
} from 'lucide-react';

export const SecurityCenter: React.FC = () => {
  const [scans, setScans] = useState<SecurityScan[]>([]);
  const [vulnerableArtifacts, setVulnerableArtifacts] = useState<Artifact[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<'scans' | 'artifacts'>('scans');
  const [filter, setFilter] = useState('');

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      setLoading(true);
      const [scansResponse, artifactsResponse] = await Promise.all([
        devsecopsApi.getSecurityScans({ per_page: 50 }),
        devsecopsApi.getVulnerableArtifacts(),
      ]);
      setScans(scansResponse.data.data);
      setVulnerableArtifacts(artifactsResponse.data);
    } catch (err) {
      console.error('Failed to load security data:', err);
    } finally {
      setLoading(false);
    }
  };

  const getSeverityColor = (severity: SecurityScan['severity']) => {
    switch (severity) {
      case 'critical':
        return 'bg-red-100 text-red-700 border-red-200';
      case 'high':
        return 'bg-orange-100 text-orange-700 border-orange-200';
      case 'medium':
        return 'bg-yellow-100 text-yellow-700 border-yellow-200';
      case 'low':
        return 'bg-blue-100 text-blue-700 border-blue-200';
      default:
        return 'bg-gray-100 text-gray-700 border-gray-200';
    }
  };

  const getStatusIcon = (status: SecurityScan['status']) => {
    switch (status) {
      case 'completed':
        return <CheckCircle className="w-5 h-5 text-green-500" />;
      case 'failed':
        return <XCircle className="w-5 h-5 text-red-500" />;
      case 'running':
        return <Loader className="w-5 h-5 text-blue-500 animate-spin" />;
      case 'pending':
        return <Shield className="w-5 h-5 text-gray-400" />;
      default:
        return <Shield className="w-5 h-5 text-gray-400" />;
    }
  };

  const getScanTypeLabel = (type: SecurityScan['type']) => {
    const labels: Record<string, string> = {
      sast: 'Static Analysis',
      dast: 'Dynamic Analysis',
      sca: 'Dependency Check',
      secret: 'Secret Scanning',
      container: 'Container Scan',
      iac: 'IaC Scan',
      sbom: 'SBOM Generation',
      license: 'License Audit',
    };
    return labels[type] || type;
  };

  const filteredScans = scans.filter((scan) => {
    if (!filter) return true;
    return (
      scan.type.toLowerCase().includes(filter.toLowerCase()) ||
      scan.tool.toLowerCase().includes(filter.toLowerCase())
    );
  });

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">Security Center</h1>
        <button
          onClick={loadData}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          Refresh Scan Data
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white rounded-lg shadow p-4">
          <div className="text-2xl font-bold text-gray-900">{scans.length}</div>
          <div className="text-sm text-gray-500">Total Scans</div>
        </div>
        <div className="bg-white rounded-lg shadow p-4">
          <div className="text-2xl font-bold text-red-600">
            {scans.filter((s) => s.severity === 'critical').length}
          </div>
          <div className="text-sm text-gray-500">Critical Issues</div>
        </div>
        <div className="bg-white rounded-lg shadow p-4">
          <div className="text-2xl font-bold text-orange-600">
            {scans.filter((s) => s.severity === 'high').length}
          </div>
          <div className="text-sm text-gray-500">High Issues</div>
        </div>
        <div className="bg-white rounded-lg shadow p-4">
          <div className="text-2xl font-bold text-green-600">
            {scans.filter((s) => s.severity === 'none').length}
          </div>
          <div className="text-sm text-gray-500">Passed Scans</div>
        </div>
      </div>

      <div className="border-b border-gray-200">
        <nav className="flex space-x-8">
          <button
            onClick={() => setActiveTab('scans')}
            className={`pb-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'scans'
                ? 'border-blue-600 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700'
            }`}
          >
            Security Scans
          </button>
          <button
            onClick={() => setActiveTab('artifacts')}
            className={`pb-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'artifacts'
                ? 'border-blue-600 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700'
            }`}
          >
            Vulnerable Artifacts ({vulnerableArtifacts.length})
          </button>
        </nav>
      </div>

      {activeTab === 'scans' && (
        <div className="relative">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
          <input
            type="text"
            placeholder="Search scans..."
            value={filter}
            onChange={(e) => setFilter(e.target.value)}
            className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
      )}

      {activeTab === 'scans' && (
        <div className="bg-white rounded-lg shadow overflow-hidden">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tool</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Severity</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vulnerabilities</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {filteredScans.map((scan) => (
                <tr key={scan.id} className="hover:bg-gray-50">
                  <td className="px-6 py-4 whitespace-nowrap">{getStatusIcon(scan.status)}</td>
                  <td className="px-6 py-4 whitespace-nowrap">{getScanTypeLabel(scan.type)}</td>
                  <td className="px-6 py-4 whitespace-nowrap">{scan.tool}</td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className={`px-2 py-1 text-xs font-medium rounded border ${getSeverityColor(scan.severity)}`}>
                      {scan.severity.toUpperCase()}
                    </span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="flex items-center gap-2">
                      <AlertTriangle className="w-4 h-4 text-red-500" />
                      <span className="font-medium">{scan.vulnerability_count}</span>
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {scan.completed_at ? new Date(scan.completed_at).toLocaleDateString() : 'Pending'}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {activeTab === 'artifacts' && (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          {vulnerableArtifacts.map((artifact) => (
            <div key={artifact.id} className="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
              <div className="flex items-start justify-between">
                <div>
                  <h3 className="font-semibold text-gray-900">{artifact.name}</h3>
                  <p className="text-sm text-gray-500">{artifact.version}</p>
                </div>
                {artifact.signed ? (
                  <CheckCircle className="w-5 h-5 text-green-500" />
                ) : (
                  <AlertTriangle className="w-5 h-5 text-yellow-500" />
                )}
              </div>
              <div className="mt-4 flex items-center gap-4">
                <div>
                  <div className="text-2xl font-bold text-red-600">{artifact.critical_vulnerabilities}</div>
                  <div className="text-xs text-gray-500">Critical</div>
                </div>
                <div>
                  <div className="text-2xl font-bold text-orange-600">{artifact.vulnerability_count}</div>
                  <div className="text-xs text-gray-500">Total</div>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default SecurityCenter;
