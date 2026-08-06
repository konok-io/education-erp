import React from 'react';
import { Table, Badge, Tag, Space, Button, Modal, message } from 'antd';
import {
  SwapOutlined,
  CheckCircleOutlined,
  CloseCircleOutlined,
  UndoOutlined,
  EyeOutlined,
} from '@ant-design/icons';
import type { FailoverEvent, PaginationMeta } from '../types';
import { failoverApi } from '../utils/api';

interface FailoverListProps {
  failovers: FailoverEvent[];
  meta: PaginationMeta;
  loading?: boolean;
  onRefresh: () => void;
  onView: (failover: FailoverEvent) => void;
}

const FailoverList: React.FC<FailoverListProps> = ({
  failovers,
  meta,
  loading = false,
  onRefresh,
  onView,
}) => {
  const handleComplete = async (failover: FailoverEvent) => {
    try {
      await failoverApi.complete(failover.id);
      message.success('Failover completed successfully');
      onRefresh();
    } catch {
      message.error('Failed to complete failover');
    }
  };

  const handleRollback = (failover: FailoverEvent) => {
    Modal.confirm({
      title: 'Rollback Failover',
      content: `Are you sure you want to rollback failover "${failover.name}"?`,
      okText: 'Rollback',
      okType: 'warning',
      onOk: async () => {
        try {
          await failoverApi.rollback(failover.id);
          message.success('Failover rolled back successfully');
          onRefresh();
        } catch {
          message.error('Failed to rollback failover');
        }
      },
    });
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'completed':
        return 'success';
      case 'in_progress':
        return 'processing';
      case 'failed':
        return 'error';
      case 'initiated':
        return 'blue';
      case 'rolled_back':
        return 'warning';
      case 'cancelled':
        return 'default';
      default:
        return 'default';
    }
  };

  const getTypeColor = (type: string) => {
    switch (type) {
      case 'automatic':
        return 'red';
      case 'manual':
        return 'blue';
      case 'planned':
        return 'green';
      case 'emergency':
        return 'orange';
      default:
        return 'default';
    }
  };

  const columns = [
    {
      title: 'Name',
      dataIndex: 'name',
      key: 'name',
      render: (text: string, record: FailoverEvent) => (
        <Button type="link" onClick={() => onView(record)}>
          {text}
        </Button>
      ),
    },
    {
      title: 'Type',
      dataIndex: 'type',
      key: 'type',
      render: (type: string) => (
        <Tag color={getTypeColor(type)}>{type}</Tag>
      ),
    },
    {
      title: 'Status',
      dataIndex: 'status',
      key: 'status',
      render: (status: string) => (
        <Badge status={getStatusColor(status) as any} text={status.replace('_', ' ')} />
      ),
    },
    {
      title: 'Source',
      dataIndex: 'source_site',
      key: 'source_site',
      render: (site: string) => <Tag>{site}</Tag>,
    },
    {
      title: 'Destination',
      dataIndex: 'destination_site',
      key: 'destination_site',
      render: (site: string) => <Tag>{site}</Tag>,
    },
    {
      title: 'Affected Users',
      dataIndex: 'affected_users',
      key: 'affected_users',
    },
    {
      title: 'Downtime',
      dataIndex: 'downtime_seconds',
      key: 'downtime_seconds',
      render: (seconds: number) => formatDuration(seconds),
    },
    {
      title: 'Recovery Time',
      dataIndex: 'recovery_time_seconds',
      key: 'recovery_time_seconds',
      render: (seconds: number) => formatDuration(seconds),
    },
    {
      title: 'Initiated',
      dataIndex: 'initiated_at',
      key: 'initiated_at',
    },
    {
      title: 'Actions',
      key: 'actions',
      render: (_: unknown, record: FailoverEvent) => (
        <Space>
          {record.status === 'in_progress' && (
            <Button
              type="text"
              icon={<CheckCircleOutlined style={{ color: '#3f8600' }} />}
              onClick={() => handleComplete(record)}
            />
          )}
          {record.status === 'completed' && !record.recovery_time_seconds && (
            <Button
              type="text"
              icon={<UndoOutlined style={{ color: '#faad14' }} />}
              onClick={() => handleRollback(record)}
            />
          )}
          <Button
            type="text"
            icon={<EyeOutlined />}
            onClick={() => onView(record)}
          />
        </Space>
      ),
    },
  ];

  return (
    <Table
      columns={columns}
      dataSource={failovers}
      rowKey="id"
      loading={loading}
      pagination={{
        current: meta.current_page,
        pageSize: meta.per_page,
        total: meta.total,
        showSizeChanger: true,
        showTotal: (total, range) => `${range[0]}-${range[1]} of ${total}`,
      }}
      onChange={(pagination) => {
        onRefresh(pagination.current, pagination.pageSize);
      }}
    />
  );
};

function formatDuration(seconds: number): string {
  if (seconds < 60) {
    return `${seconds}s`;
  }
  if (seconds < 3600) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}m ${secs}s`;
  }
  const hours = Math.floor(seconds / 3600);
  const mins = Math.floor((seconds % 3600) / 60);
  return `${hours}h ${mins}m`;
}

export default FailoverList;
