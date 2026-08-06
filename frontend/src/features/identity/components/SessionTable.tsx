import React from 'react';
import { Table, Badge, Tag, Button, Space, Popconfirm, Tooltip, message } from 'antd';
import {
  DesktopOutlined,
  MobileOutlined,
  GlobalOutlined,
  DeleteOutlined,
} from '@ant-design/icons';
import type { Session } from '../types';
import { sessionApi } from '../utils/api';

interface SessionTableProps {
  sessions: Session[];
  loading?: boolean;
  onRefresh: () => void;
}

const SessionTable: React.FC<SessionTableProps> = ({ sessions, loading = false, onRefresh }) => {
  const handleRevoke = async (sessionId: string) => {
    try {
      await sessionApi.revoke(sessionId);
      message.success('Session revoked successfully');
      onRefresh();
    } catch {
      message.error('Failed to revoke session');
    }
  };

  const handleRevokeAll = async () => {
    try {
      await sessionApi.revokeAll();
      message.success('All other sessions revoked successfully');
      onRefresh();
    } catch {
      message.error('Failed to revoke sessions');
    }
  };

  const getDeviceIcon = (deviceType: string) => {
    switch (deviceType) {
      case 'mobile':
        return <MobileOutlined />;
      case 'desktop':
        return <DesktopOutlined />;
      default:
        return <GlobalOutlined />;
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active':
        return 'success';
      case 'inactive':
        return 'warning';
      case 'revoked':
        return 'error';
      default:
        return 'default';
    }
  };

  const columns = [
    {
      title: 'Device',
      key: 'device',
      render: (_: unknown, record: Session) => (
        <Space>
          {getDeviceIcon(record.device_type)}
          <span>{record.device_name || record.device_type}</span>
        </Space>
      ),
    },
    {
      title: 'Operating System',
      dataIndex: 'device_os',
      key: 'device_os',
      render: (os: string) => os || 'Unknown',
    },
    {
      title: 'Browser',
      dataIndex: 'device_browser',
      key: 'device_browser',
      render: (browser: string) => browser || 'Unknown',
    },
    {
      title: 'IP Address',
      dataIndex: 'ip_address',
      key: 'ip_address',
    },
    {
      title: 'Location',
      dataIndex: 'location',
      key: 'location',
      render: (location: string) => location || 'Unknown',
    },
    {
      title: 'Status',
      dataIndex: 'status',
      key: 'status',
      render: (status: string, record: Session) => (
        <Space>
          <Badge status={getStatusColor(status) as any} text={status} />
          {record.is_current && <Tag color="blue">Current</Tag>}
        </Space>
      ),
    },
    {
      title: 'Last Activity',
      dataIndex: 'last_activity_at',
      key: 'last_activity_at',
      render: (date: string) => {
        const d = new Date(date);
        return (
          <Tooltip title={d.toLocaleString()}>
            {d.toLocaleDateString()} {d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
          </Tooltip>
        );
      },
    },
    {
      title: 'Actions',
      key: 'actions',
      render: (_: unknown, record: Session) => (
        <Space>
          {!record.is_current && (
            <Popconfirm
              title="Revoke this session?"
              onConfirm={() => handleRevoke(record.id)}
            >
              <Button size="small" danger icon={<DeleteOutlined />}>
                Revoke
              </Button>
            </Popconfirm>
          )}
        </Space>
      ),
    },
  ];

  return (
    <div>
      <div style={{ marginBottom: 16, display: 'flex', justifyContent: 'flex-end' }}>
        <Popconfirm
          title="Revoke all other sessions?"
          onConfirm={handleRevokeAll}
        >
          <Button danger>Revoke All Other Sessions</Button>
        </Popconfirm>
      </div>
      <Table
        columns={columns}
        dataSource={sessions}
        rowKey="id"
        loading={loading}
        pagination={false}
      />
    </div>
  );
};

export default SessionTable;
