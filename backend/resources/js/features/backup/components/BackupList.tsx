import React from 'react';
import { Table, Badge, Tag, Space, Button, Dropdown, Modal, message } from 'antd';
import {
  PlayCircleOutlined,
  PauseCircleOutlined,
  CheckCircleOutlined,
  CloseCircleOutlined,
  DeleteOutlined,
  EyeOutlined,
  MoreOutlined,
} from '@ant-design/icons';
import type { BackupJob, PaginationMeta } from '../types';
import { backupApi } from '../utils/api';

interface BackupListProps {
  backups: BackupJob[];
  meta: PaginationMeta;
  loading?: boolean;
  onRefresh: () => void;
  onView: (backup: BackupJob) => void;
  onEdit: (backup: BackupJob) => void;
}

const BackupList: React.FC<BackupListProps> = ({
  backups,
  meta,
  loading = false,
  onRefresh,
  onView,
  onEdit,
}) => {
  const handleStart = async (backup: BackupJob) => {
    try {
      await backupApi.start(backup.id);
      message.success('Backup started successfully');
      onRefresh();
    } catch {
      message.error('Failed to start backup');
    }
  };

  const handleCancel = async (backup: BackupJob) => {
    try {
      await backupApi.cancel(backup.id);
      message.success('Backup cancelled successfully');
      onRefresh();
    } catch {
      message.error('Failed to cancel backup');
    }
  };

  const handleVerify = async (backup: BackupJob) => {
    try {
      await backupApi.verify(backup.id);
      message.success('Backup verified successfully');
      onRefresh();
    } catch {
      message.error('Failed to verify backup');
    }
  };

  const handleDelete = (backup: BackupJob) => {
    Modal.confirm({
      title: 'Delete Backup',
      content: `Are you sure you want to delete backup "${backup.name}"?`,
      okText: 'Delete',
      okType: 'danger',
      onOk: async () => {
        try {
          await backupApi.delete(backup.id);
          message.success('Backup deleted successfully');
          onRefresh();
        } catch {
          message.error('Failed to delete backup');
        }
      },
    });
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'completed':
        return 'success';
      case 'running':
        return 'processing';
      case 'failed':
        return 'error';
      case 'pending':
        return 'warning';
      case 'cancelled':
      case 'paused':
        return 'default';
      default:
        return 'default';
    }
  };

  const getTypeColor = (type: string) => {
    switch (type) {
      case 'full':
        return 'blue';
      case 'incremental':
        return 'green';
      case 'differential':
        return 'orange';
      case 'snapshot':
        return 'purple';
      default:
        return 'default';
    }
  };

  const columns = [
    {
      title: 'Name',
      dataIndex: 'name',
      key: 'name',
      render: (text: string, record: BackupJob) => (
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
        <Badge status={getStatusColor(status) as any} text={status} />
      ),
    },
    {
      title: 'Size',
      dataIndex: 'formatted_size',
      key: 'formatted_size',
    },
    {
      title: 'Files',
      dataIndex: 'file_count',
      key: 'file_count',
    },
    {
      title: 'Verified',
      dataIndex: 'verified',
      key: 'verified',
      render: (verified: boolean) =>
        verified ? (
          <CheckCircleOutlined style={{ color: '#3f8600' }} />
        ) : (
          <CloseCircleOutlined style={{ color: '#cf1322' }} />
        ),
    },
    {
      title: 'Immutable',
      dataIndex: 'is_immutable',
      key: 'is_immutable',
      render: (immutable: boolean) =>
        immutable ? (
          <Tag color="gold">Immutable</Tag>
        ) : null,
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
      render: (_: unknown, record: BackupJob) => (
        <Space>
          {record.status === 'pending' && (
            <Button
              type="text"
              icon={<PlayCircleOutlined />}
              onClick={() => handleStart(record)}
            />
          )}
          {record.status === 'running' && (
            <Button
              type="text"
              danger
              icon={<PauseCircleOutlined />}
              onClick={() => handleCancel(record)}
            />
          )}
          {record.status === 'completed' && !record.verified && (
            <Button
              type="text"
              icon={<CheckCircleOutlined style={{ color: '#3f8600' }} />}
              onClick={() => handleVerify(record)}
            />
          )}
          <Dropdown
            menu={{
              items: [
                {
                  key: 'view',
                  icon: <EyeOutlined />,
                  label: 'View Details',
                  onClick: () => onView(record),
                },
                {
                  key: 'edit',
                  icon: <EditOutlined />,
                  label: 'Edit',
                  onClick: () => onEdit(record),
                },
                { type: 'divider' },
                {
                  key: 'delete',
                  icon: <DeleteOutlined />,
                  label: 'Delete',
                  danger: true,
                  disabled: record.is_immutable,
                  onClick: () => handleDelete(record),
                },
              ],
            }}
          >
            <Button type="text" icon={<MoreOutlined />} />
          </Dropdown>
        </Space>
      ),
    },
  ];

  return (
    <Table
      columns={columns}
      dataSource={backups}
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

export default BackupList;
