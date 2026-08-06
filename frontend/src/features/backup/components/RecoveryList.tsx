import React from 'react';
import { Table, Badge, Tag, Space, Button, Modal, message } from 'antd';
import {
  PlayCircleOutlined,
  CheckCircleOutlined,
  CloseCircleOutlined,
  EyeOutlined,
  DeleteOutlined,
} from '@ant-design/icons';
import type { RecoveryJob, PaginationMeta } from '../types';
import { recoveryApi } from '../utils/api';

interface RecoveryListProps {
  recoveries: RecoveryJob[];
  meta: PaginationMeta;
  loading?: boolean;
  onRefresh: () => void;
  onView: (recovery: RecoveryJob) => void;
}

const RecoveryList: React.FC<RecoveryListProps> = ({
  recoveries,
  meta,
  loading = false,
  onRefresh,
  onView,
}) => {
  const handleStart = async (recovery: RecoveryJob) => {
    try {
      await recoveryApi.start(recovery.id);
      message.success('Recovery started successfully');
      onRefresh();
    } catch {
      message.error('Failed to start recovery');
    }
  };

  const handleVerify = async (recovery: RecoveryJob) => {
    try {
      await recoveryApi.verify(recovery.id);
      message.success('Recovery verified successfully');
      onRefresh();
    } catch {
      message.error('Failed to verify recovery');
    }
  };

  const handleDelete = (recovery: RecoveryJob) => {
    Modal.confirm({
      title: 'Delete Recovery Job',
      content: `Are you sure you want to delete "${recovery.name}"?`,
      okText: 'Delete',
      okType: 'danger',
      onOk: async () => {
        try {
          await recoveryApi.delete(recovery.id);
          message.success('Recovery job deleted successfully');
          onRefresh();
        } catch {
          message.error('Failed to delete recovery job');
        }
      },
    });
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'completed':
      case 'verified':
        return 'success';
      case 'running':
        return 'processing';
      case 'failed':
        return 'error';
      case 'pending':
        return 'warning';
      case 'cancelled':
        return 'default';
      default:
        return 'default';
    }
  };

  const getTypeColor = (type: string) => {
    switch (type) {
      case 'full':
        return 'blue';
      case 'partial':
        return 'cyan';
      case 'file':
        return 'green';
      case 'database':
        return 'orange';
      case 'point_in_time':
        return 'purple';
      case 'table':
        return 'magenta';
      default:
        return 'default';
    }
  };

  const columns = [
    {
      title: 'Name',
      dataIndex: 'name',
      key: 'name',
      render: (text: string, record: RecoveryJob) => (
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
        <Tag color={getTypeColor(type)}>{type.replace('_', ' ')}</Tag>
      ),
    },
    {
      title: 'Status',
      dataIndex: 'status',
      key: 'status',
      render: (status: string) => (
        <Badge status={getStatusColor(status) as any} text={status} />
      ),
    },
    {
      title: 'Size Restored',
      dataIndex: 'formatted_size',
      key: 'formatted_size',
    },
    {
      title: 'Files',
      dataIndex: 'files_restored',
      key: 'files_restored',
    },
    {
      title: 'Records',
      dataIndex: 'records_restored',
      key: 'records_restored',
    },
    {
      title: 'Duration',
      dataIndex: 'duration_seconds',
      key: 'duration_seconds',
      render: (seconds: number) => formatDuration(seconds),
    },
    {
      title: 'Environment',
      dataIndex: 'environment',
      key: 'environment',
    },
    {
      title: 'Created',
      dataIndex: 'created_at',
      key: 'created_at',
    },
    {
      title: 'Actions',
      key: 'actions',
      render: (_: unknown, record: RecoveryJob) => (
        <Space>
          {record.status === 'pending' && (
            <Button
              type="text"
              icon={<PlayCircleOutlined />}
              onClick={() => handleStart(record)}
            />
          )}
          {record.status === 'completed' && (
            <Button
              type="text"
              icon={<CheckCircleOutlined style={{ color: '#3f8600' }} />}
              onClick={() => handleVerify(record)}
            />
          )}
          <Button
            type="text"
            icon={<EyeOutlined />}
            onClick={() => onView(record)}
          />
          <Button
            type="text"
            danger
            icon={<DeleteOutlined />}
            onClick={() => handleDelete(record)}
          />
        </Space>
      ),
    },
  ];

  return (
    <Table
      columns={columns}
      dataSource={recoveries}
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

export default RecoveryList;
