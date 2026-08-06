import React from 'react';
import { Card, Statistic, Row, Col, Progress, Badge, Table, Tag, Space, Button } from 'antd';
import {
  CloudUploadOutlined,
  CloudSyncOutlined,
  CheckCircleOutlined,
  CloseCircleOutlined,
  ClockCircleOutlined,
  DatabaseOutlined,
  SafetyOutlined,
} from '@ant-design/icons';
import type { BackupSummary } from '../types';

interface BackupDashboardProps {
  summary: BackupSummary;
  recentBackups?: Array<{
    id: string;
    name: string;
    type: string;
    status: string;
    formatted_size: string;
    created_at: string;
  }>;
  loading?: boolean;
}

const BackupDashboard: React.FC<BackupDashboardProps> = ({
  summary,
  recentBackups = [],
  loading = false,
}) => {
  const getStatusColor = (status: string) => {
    switch (status) {
      case 'completed':
      case 'healthy':
        return 'success';
      case 'running':
      case 'in_progress':
        return 'processing';
      case 'failed':
        return 'error';
      case 'pending':
      case 'standby':
        return 'warning';
      default:
        return 'default';
    }
  };

  const backupColumns = [
    { title: 'Name', dataIndex: 'name', key: 'name' },
    { title: 'Type', dataIndex: 'type', key: 'type' },
    {
      title: 'Status',
      dataIndex: 'status',
      key: 'status',
      render: (status: string) => (
        <Badge status={getStatusColor(status) as any} text={status} />
      ),
    },
    { title: 'Size', dataIndex: 'formatted_size', key: 'formatted_size' },
    { title: 'Created', dataIndex: 'created_at', key: 'created_at' },
  ];

  return (
    <div className="backup-dashboard">
      <Row gutter={[16, 16]}>
        <Col xs={24} sm={12} lg={6}>
          <Card>
            <Statistic
              title="Total Backups"
              value={summary.total_backups}
              prefix={<CloudUploadOutlined />}
              loading={loading}
            />
          </Card>
        </Col>
        <Col xs={24} sm={12} lg={6}>
          <Card>
            <Statistic
              title="Successful"
              value={summary.successful_backups}
              prefix={<CheckCircleOutlined />}
              valueStyle={{ color: '#3f8600' }}
              loading={loading}
            />
          </Card>
        </Col>
        <Col xs={24} sm={12} lg={6}>
          <Card>
            <Statistic
              title="Failed"
              value={summary.failed_backups}
              prefix={<CloseCircleOutlined />}
              valueStyle={{ color: '#cf1322' }}
              loading={loading}
            />
          </Card>
        </Col>
        <Col xs={24} sm={12} lg={6}>
          <Card>
            <Statistic
              title="Verified"
              value={summary.verified_backups}
              prefix={<SafetyOutlined />}
              valueStyle={{ color: '#1890ff' }}
              loading={loading}
            />
          </Card>
        </Col>
      </Row>

      <Row gutter={[16, 16]} style={{ marginTop: 16 }}>
        <Col xs={24} sm={12} lg={6}>
          <Card>
            <Statistic
              title="Running"
              value={summary.running_backups}
              prefix={<ClockCircleOutlined />}
              loading={loading}
            />
          </Card>
        </Col>
        <Col xs={24} sm={12} lg={6}>
          <Card>
            <Statistic
              title="Total Recoveries"
              value={summary.total_recoveries}
              prefix={<DatabaseOutlined />}
              loading={loading}
            />
          </Card>
        </Col>
        <Col xs={24} sm={12} lg={6}>
          <Card>
            <Statistic
              title="Active Replications"
              value={summary.active_replications}
              prefix={<CloudSyncOutlined />}
              loading={loading}
            />
          </Card>
        </Col>
        <Col xs={24} sm={12} lg={6}>
          <Card>
            <Statistic
              title="Healthy Replications"
              value={summary.healthy_replications}
              prefix={<CheckCircleOutlined />}
              valueStyle={{ color: summary.healthy_replications === summary.active_replications ? '#3f8600' : '#faad14' }}
              loading={loading}
            />
          </Card>
        </Col>
      </Row>

      <Row gutter={[16, 16]} style={{ marginTop: 16 }}>
        <Col xs={24} lg={12}>
          <Card title="Storage Usage" loading={loading}>
            <Progress
              percent={summary.storage_usage_percentage}
              status="active"
              format={(percent) => `${percent?.toFixed(1)}%`}
            />
            <Space style={{ marginTop: 16 }} split="|">
              <span>Used: {formatBytes(summary.total_storage_used_bytes)}</span>
              <span>Available: {formatBytes(summary.total_storage_available_bytes)}</span>
            </Space>
          </Card>
        </Col>
        <Col xs={24} lg={12}>
          <Card title="Backup by Type" loading={loading}>
            <Row gutter={[16, 8]}>
              {Object.entries(summary.backup_by_type).map(([type, count]) => (
                <Col span={12} key={type}>
                  <Tag color="blue">{type}</Tag>: {count}
                </Col>
              ))}
            </Row>
          </Card>
        </Col>
      </Row>

      <Row gutter={[16, 16]} style={{ marginTop: 16 }}>
        <Col xs={24}>
          <Card
            title="Recent Backups"
            extra={<Button type="link">View All</Button>}
            loading={loading}
          >
            <Table
              columns={backupColumns}
              dataSource={recentBackups}
              rowKey="id"
              pagination={false}
              size="small"
            />
          </Card>
        </Col>
      </Row>
    </div>
  );
};

function formatBytes(bytes: number): string {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

export default BackupDashboard;
