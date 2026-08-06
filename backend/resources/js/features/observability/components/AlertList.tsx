import React from 'react';
import { Table, Badge, Button, Space, Tag, Tooltip } from 'antd';
import { CheckCircleOutlined, PauseOutlined, DeleteOutlined } from '@ant-design/icons';
import type { Alert, AlertSeverity, AlertStatus } from '../types';

interface Props {
  alerts: Alert[];
  loading?: boolean;
  onAcknowledge?: (id: string) => void;
  onResolve?: (id: string) => void;
  onSilence?: (id: string) => void;
  onDelete?: (id: string) => void;
  onViewDetails?: (id: string) => void;
  pagination?: {
    current: number;
    pageSize: number;
    total: number;
    onChange: (page: number, pageSize: number) => void;
  };
}

const getSeverityColor = (severity: AlertSeverity) => {
  switch (severity) {
    case 'critical':
      return '#f5222d';
    case 'high':
      return '#fa8c16';
    case 'medium':
      return '#faad14';
    case 'low':
      return '#52c41a';
    default:
      return '#d9d9d9';
  }
};

const getStatusColor = (status: AlertStatus) => {
  switch (status) {
    case 'active':
      return '#f5222d';
    case 'acknowledged':
      return '#fa8c16';
    case 'resolved':
      return '#52c41a';
    case 'silenced':
      return '#8c8c8c';
    default:
      return '#d9d9d9';
  }
};

export const AlertList: React.FC<Props> = ({
  alerts,
  loading = false,
  onAcknowledge,
  onResolve,
  onSilence,
  onDelete,
  onViewDetails,
  pagination,
}) => {
  const columns = [
    {
      title: 'Severity',
      dataIndex: 'severity',
      key: 'severity',
      width: 100,
      render: (severity: AlertSeverity) => (
        <Badge color={getSeverityColor(severity)} text={severity.toUpperCase()} />
      ),
    },
    {
      title: 'Status',
      dataIndex: 'status',
      key: 'status',
      width: 120,
      render: (status: AlertStatus) => (
        <Tag color={getStatusColor(status)}>{status}</Tag>
      ),
    },
    {
      title: 'Name',
      dataIndex: 'name',
      key: 'name',
      render: (name: string, record: Alert) => (
        <a onClick={() => onViewDetails?.(record.id)}>{name}</a>
      ),
    },
    {
      title: 'Current Value',
      dataIndex: 'current_value',
      key: 'current_value',
      width: 150,
      render: (value: number | null, record: Alert) => {
        if (value === null) return '-';
        return (
          <span>
            {value.toFixed(2)} {record.threshold && (
              <span style={{ color: '#8c8c8c' }}>
                (threshold: {record.threshold})
              </span>
            )}
          </span>
        );
      },
    },
    {
      title: 'Triggered',
      dataIndex: 'triggered_at',
      key: 'triggered_at',
      width: 180,
      render: (date: string | null) => {
        if (!date) return '-';
        return new Date(date).toLocaleString();
      },
    },
    {
      title: 'Actions',
      key: 'actions',
      width: 200,
      render: (_: any, record: Alert) => (
        <Space size="small">
          {record.status === 'active' && (
            <Tooltip title="Acknowledge">
              <Button
                type="link"
                size="small"
                icon={<CheckCircleOutlined />}
                onClick={() => onAcknowledge?.(record.id)}
              />
            </Tooltip>
          )}
          {record.status !== 'resolved' && (
            <Tooltip title="Resolve">
              <Button
                type="link"
                size="small"
                icon={<CheckCircleOutlined />}
                onClick={() => onResolve?.(record.id)}
              />
            </Tooltip>
          )}
          {record.status === 'active' && (
            <Tooltip title="Silence">
              <Button
                type="link"
                size="small"
                icon={<PauseOutlined />}
                onClick={() => onSilence?.(record.id)}
              />
            </Tooltip>
          )}
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
      dataSource={alerts}
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
