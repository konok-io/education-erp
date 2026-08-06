import React from 'react';
import { Table, Badge, Button, Space, Tag, Tooltip } from 'antd';
import { PlayCircleOutlined, PauseOutlined, ReloadOutlined, DeleteOutlined } from '@ant-design/icons';
import type { HealthCheck, HealthCheckStatus } from '../types';

interface Props {
  healthChecks: HealthCheck[];
  loading?: boolean;
  onExecute?: (id: string) => void;
  onToggle?: (id: string) => void;
  onDelete?: (id: string) => void;
  onViewResults?: (id: string) => void;
  onEdit?: (id: string) => void;
  pagination?: {
    current: number;
    pageSize: number;
    total: number;
    onChange: (page: number, pageSize: number) => void;
  };
}

const getStatusColor = (status: HealthCheckStatus) => {
  switch (status) {
    case 'healthy':
      return '#52c41a';
    case 'degraded':
      return '#faad14';
    case 'unhealthy':
      return '#f5222d';
    default:
      return '#d9d9d9';
  }
};

export const HealthCheckList: React.FC<Props> = ({
  healthChecks,
  loading = false,
  onExecute,
  onToggle,
  onDelete,
  onViewResults,
  onEdit,
  pagination,
}) => {
  const columns = [
    {
      title: 'Name',
      dataIndex: 'name',
      key: 'name',
      render: (name: string, record: HealthCheck) => (
        <a onClick={() => onViewResults?.(record.id)}>{name}</a>
      ),
    },
    {
      title: 'Type',
      dataIndex: 'type',
      key: 'type',
      width: 120,
      render: (type: string) => <Tag>{type}</Tag>,
    },
    {
      title: 'Status',
      dataIndex: 'status',
      key: 'status',
      width: 120,
      render: (status: HealthCheckStatus) => (
        <Badge color={getStatusColor(status)} text={status} />
      ),
    },
    {
      title: 'Endpoint',
      dataIndex: 'endpoint',
      key: 'endpoint',
      width: 200,
      ellipsis: true,
    },
    {
      title: 'Interval',
      dataIndex: 'check_interval_seconds',
      key: 'interval',
      width: 100,
      render: (seconds: number) => {
        if (seconds < 60) return `${seconds}s`;
        return `${Math.floor(seconds / 60)}m`;
      },
    },
    {
      title: 'Last Check',
      dataIndex: 'last_check_at',
      key: 'last_check_at',
      width: 180,
      render: (date: string | null) => {
        if (!date) return '-';
        return new Date(date).toLocaleString();
      },
    },
    {
      title: 'Response Time',
      dataIndex: 'last_response_time_ms',
      key: 'response_time',
      width: 120,
      render: (time: number | null) => {
        if (time === null) return '-';
        return `${time.toFixed(0)}ms`;
      },
    },
    {
      title: 'Active',
      dataIndex: 'is_active',
      key: 'is_active',
      width: 80,
      render: (isActive: boolean) => (
        <Badge status={isActive ? 'success' : 'default'} text={isActive ? 'Yes' : 'No'} />
      ),
    },
    {
      title: 'Actions',
      key: 'actions',
      width: 180,
      render: (_: any, record: HealthCheck) => (
        <Space size="small">
          <Tooltip title="Execute Now">
            <Button
              type="link"
              size="small"
              icon={<PlayCircleOutlined />}
              onClick={() => onExecute?.(record.id)}
            />
          </Tooltip>
          <Tooltip title="Toggle Active">
            <Button
              type="link"
              size="small"
              icon={record.is_active ? <PauseOutlined /> : <PlayCircleOutlined />}
              onClick={() => onToggle?.(record.id)}
            />
          </Tooltip>
          <Tooltip title="View Results">
            <Button
              type="link"
              size="small"
              icon={<ReloadOutlined />}
              onClick={() => onViewResults?.(record.id)}
            />
          </Tooltip>
          <Tooltip title="Delete">
            <Button
              type="link"
              size="small"
              danger
              icon={<DeleteOutlined />}
              onClick={() => onDelete?.(record.id)}
            />
          </Tooltip>
        </Space>
      ),
    },
  ];

  return (
    <Table
      columns={columns}
      dataSource={healthChecks}
      rowKey="id"
      loading={loading}
      pagination={pagination ? {
        current: pagination.current,
        pageSize: pagination.pageSize,
        total: pagination.total,
        onChange: pagination.onChange,
      } : false}
      size="middle"
    />
  );
};
