import React, { useState, useEffect, useCallback } from 'react';
import {
  Tabs,
  Card,
  Button,
  Space,
  Typography,
  Drawer,
  Descriptions,
  Timeline,
  Tag,
  message,
} from 'antd';
import {
  CloudUploadOutlined,
  DatabaseOutlined,
  SwapOutlined,
  PlusOutlined,
  ReloadOutlined,
} from '@ant-design/icons';
import {
  BackupDashboard,
  BackupList,
  RecoveryList,
  FailoverList,
  backupApi,
  recoveryApi,
  failoverApi,
  drApi,
} from '../index';
import type { BackupJob, RecoveryJob, FailoverEvent, BackupSummary } from '../types';

const { Title } = Typography;

const BackupPage: React.FC = () => {
  const [loading, setLoading] = useState(false);
  const [summary, setSummary] = useState<BackupSummary | null>(null);
  const [backups, setBackups] = useState<BackupJob[]>([]);
  const [backupMeta, setBackupMeta] = useState({ current_page: 1, last_page: 1, per_page: 15, total: 0 });
  const [recoveries, setRecoveries] = useState<RecoveryJob[]>([]);
  const [recoveryMeta, setRecoveryMeta] = useState({ current_page: 1, last_page: 1, per_page: 15, total: 0 });
  const [failovers, setFailovers] = useState<FailoverEvent[]>([]);
  const [failoverMeta, setFailoverMeta] = useState({ current_page: 1, last_page: 1, per_page: 15, total: 0 });
  const [activeTab, setActiveTab] = useState('dashboard');
  const [viewingItem, setViewingItem] = useState<{ type: string; data: unknown } | null>(null);
  const [drawerVisible, setDrawerVisible] = useState(false);

  const fetchSummary = useCallback(async () => {
    try {
      const response = await drApi.getSummary();
      if (response.data.success) {
        setSummary(response.data.data);
      }
    } catch {
      message.error('Failed to fetch summary');
    }
  }, []);

  const fetchBackups = useCallback(async (page = 1, perPage = 15) => {
    setLoading(true);
    try {
      const response = await backupApi.list({ page, per_page: perPage });
      if (response.data.success) {
        setBackups(response.data.data);
        setBackupMeta(response.data.meta);
      }
    } catch {
      message.error('Failed to fetch backups');
    } finally {
      setLoading(false);
    }
  }, []);

  const fetchRecoveries = useCallback(async (page = 1, perPage = 15) => {
    setLoading(true);
    try {
      const response = await recoveryApi.list({ page, per_page: perPage });
      if (response.data.success) {
        setRecoveries(response.data.data);
        setRecoveryMeta(response.data.meta);
      }
    } catch {
      message.error('Failed to fetch recoveries');
    } finally {
      setLoading(false);
    }
  }, []);

  const fetchFailovers = useCallback(async (page = 1, perPage = 15) => {
    setLoading(true);
    try {
      const response = await failoverApi.list({ page, per_page: perPage });
      if (response.data.success) {
        setFailovers(response.data.data);
        setFailoverMeta(response.data.meta);
      }
    } catch {
      message.error('Failed to fetch failovers');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchSummary();
  }, [fetchSummary]);

  useEffect(() => {
    if (activeTab === 'backups') {
      fetchBackups();
    } else if (activeTab === 'recoveries') {
      fetchRecoveries();
    } else if (activeTab === 'failovers') {
      fetchFailovers();
    }
  }, [activeTab, fetchBackups, fetchRecoveries, fetchFailovers]);

  const handleViewBackup = (backup: BackupJob) => {
    setViewingItem({ type: 'backup', data: backup });
    setDrawerVisible(true);
  };

  const handleViewRecovery = (recovery: RecoveryJob) => {
    setViewingItem({ type: 'recovery', data: recovery });
    setDrawerVisible(true);
  };

  const handleViewFailover = (failover: FailoverEvent) => {
    setViewingItem({ type: 'failover', data: failover });
    setDrawerVisible(true);
  };

  const handleRefresh = () => {
    fetchSummary();
    if (activeTab === 'backups') fetchBackups();
    else if (activeTab === 'recoveries') fetchRecoveries();
    else if (activeTab === 'failovers') fetchFailovers();
  };

  const renderDrawerContent = () => {
    if (!viewingItem) return null;

    switch (viewingItem.type) {
      case 'backup': {
        const backup = viewingItem.data as BackupJob;
        return (
          <Descriptions column={1} bordered>
            <Descriptions.Item label="Name">{backup.name}</Descriptions.Item>
            <Descriptions.Item label="Type">{backup.type}</Descriptions.Item>
            <Descriptions.Item label="Status">{backup.status}</Descriptions.Item>
            <Descriptions.Item label="Source Type">{backup.source_type}</Descriptions.Item>
            <Descriptions.Item label="Destination Type">{backup.destination_type}</Descriptions.Item>
            <Descriptions.Item label="Size">{backup.formatted_size}</Descriptions.Item>
            <Descriptions.Item label="Files">{backup.file_count}</Descriptions.Item>
            <Descriptions.Item label="Verified">{backup.verified ? 'Yes' : 'No'}</Descriptions.Item>
            <Descriptions.Item label="Immutable">{backup.is_immutable ? 'Yes' : 'No'}</Descriptions.Item>
            <Descriptions.Item label="Environment">{backup.environment}</Descriptions.Item>
            <Descriptions.Item label="Scheduled">{backup.scheduled_at || 'N/A'}</Descriptions.Item>
            <Descriptions.Item label="Started">{backup.started_at || 'N/A'}</Descriptions.Item>
            <Descriptions.Item label="Completed">{backup.completed_at || 'N/A'}</Descriptions.Item>
            <Descriptions.Item label="Duration">{backup.duration_seconds}s</Descriptions.Item>
            {backup.error_message && (
              <Descriptions.Item label="Error">{backup.error_message}</Descriptions.Item>
            )}
          </Descriptions>
        );
      }
      case 'recovery': {
        const recovery = viewingItem.data as RecoveryJob;
        return (
          <>
            <Descriptions column={1} bordered>
              <Descriptions.Item label="Name">{recovery.name}</Descriptions.Item>
              <Descriptions.Item label="Type">{recovery.type}</Descriptions.Item>
              <Descriptions.Item label="Status">{recovery.status}</Descriptions.Item>
              <Descriptions.Item label="Destination">{recovery.destination_type}</Descriptions.Item>
              <Descriptions.Item label="Size">{recovery.formatted_size}</Descriptions.Item>
              <Descriptions.Item label="Files">{recovery.files_restored}</Descriptions.Item>
              <Descriptions.Item label="Records">{recovery.records_restored}</Descriptions.Item>
              <Descriptions.Item label="Duration">{recovery.duration_seconds}s</Descriptions.Item>
              {recovery.error_message && (
                <Descriptions.Item label="Error">{recovery.error_message}</Descriptions.Item>
              )}
            </Descriptions>
            {recovery.logs && recovery.logs.length > 0 && (
              <Card title="Logs" size="small" style={{ marginTop: 16 }}>
                <Timeline
                  items={recovery.logs.map((log) => ({
                    color: log.level === 'error' ? 'red' : log.level === 'warning' ? 'yellow' : 'blue',
                    children: `[${log.timestamp}] ${log.message}`,
                  }))}
                />
              </Card>
            )}
          </>
        );
      }
      case 'failover': {
        const failover = viewingItem.data as FailoverEvent;
        return (
          <Descriptions column={1} bordered>
            <Descriptions.Item label="Name">{failover.name}</Descriptions.Item>
            <Descriptions.Item label="Type">
              <Tag color={failover.type === 'automatic' ? 'red' : 'blue'}>{failover.type}</Tag>
            </Descriptions.Item>
            <Descriptions.Item label="Status">{failover.status}</Descriptions.Item>
            <Descriptions.Item label="Source">{failover.source_site}</Descriptions.Item>
            <Descriptions.Item label="Destination">{failover.destination_site}</Descriptions.Item>
            <Descriptions.Item label="Affected Users">{failover.affected_users}</Descriptions.Item>
            <Descriptions.Item label="Downtime">{failover.downtime_seconds}s</Descriptions.Item>
            <Descriptions.Item label="Recovery Time">{failover.recovery_time_seconds}s</Descriptions.Item>
            <Descriptions.Item label="Trigger Reason">{failover.trigger_reason || 'N/A'}</Descriptions.Item>
            <Descriptions.Item label="Initiated">{failover.initiated_at}</Descriptions.Item>
            <Descriptions.Item label="Completed">{failover.completed_at || 'N/A'}</Descriptions.Item>
            {failover.error_message && (
              <Descriptions.Item label="Error">{failover.error_message}</Descriptions.Item>
            )}
          </Descriptions>
        );
      }
      default:
        return null;
    }
  };

  const tabItems = [
    {
      key: 'dashboard',
      label: (
        <span>
          <CloudUploadOutlined />
          Dashboard
        </span>
      ),
      children: summary ? (
        <BackupDashboard
          summary={summary}
          recentBackups={backups.slice(0, 5)}
          loading={loading}
        />
      ) : null,
    },
    {
      key: 'backups',
      label: (
        <span>
          <CloudUploadOutlined />
          Backups
        </span>
      ),
      children: (
        <Card
          title="Backup Jobs"
          extra={
            <Space>
              <Button icon={<ReloadOutlined />} onClick={() => fetchBackups()}>
                Refresh
              </Button>
              <Button type="primary" icon={<PlusOutlined />}>
                New Backup
              </Button>
            </Space>
          }
        >
          <BackupList
            backups={backups}
            meta={backupMeta}
            loading={loading}
            onRefresh={fetchBackups}
            onView={handleViewBackup}
            onEdit={() => {}}
          />
        </Card>
      ),
    },
    {
      key: 'recoveries',
      label: (
        <span>
          <DatabaseOutlined />
          Recoveries
        </span>
      ),
      children: (
        <Card
          title="Recovery Jobs"
          extra={
            <Space>
              <Button icon={<ReloadOutlined />} onClick={() => fetchRecoveries()}>
                Refresh
              </Button>
              <Button type="primary" icon={<PlusOutlined />}>
                New Recovery
              </Button>
            </Space>
          }
        >
          <RecoveryList
            recoveries={recoveries}
            meta={recoveryMeta}
            loading={loading}
            onRefresh={fetchRecoveries}
            onView={handleViewRecovery}
          />
        </Card>
      ),
    },
    {
      key: 'failovers',
      label: (
        <span>
          <SwapOutlined />
          Failovers
        </span>
      ),
      children: (
        <Card
          title="Failover Events"
          extra={
            <Space>
              <Button icon={<ReloadOutlined />} onClick={() => fetchFailovers()}>
                Refresh
              </Button>
              <Button type="primary" icon={<PlusOutlined />}>
                Initiate Failover
              </Button>
            </Space>
          }
        >
          <FailoverList
            failovers={failovers}
            meta={failoverMeta}
            loading={loading}
            onRefresh={fetchFailovers}
            onView={handleViewFailover}
          />
        </Card>
      ),
    },
  ];

  return (
    <div className="backup-page" style={{ padding: 24 }}>
      <div style={{ marginBottom: 24 }}>
        <Title level={4}>Backup & Disaster Recovery</Title>
      </div>
      <Tabs
        activeKey={activeTab}
        onChange={setActiveTab}
        items={tabItems}
      />
      <Drawer
        title={`View ${viewingItem?.type || ''}`}
        placement="right"
        width={600}
        onClose={() => setDrawerVisible(false)}
        open={drawerVisible}
      >
        {renderDrawerContent()}
      </Drawer>
    </div>
  );
};

export default BackupPage;
