import React from 'react';
import { Table, Badge, Button, Space, Tag, Tooltip } from 'antd';
import { CheckCircleOutlined, UserAddOutlined, FileTextOutlined, DeleteOutlined } from '@ant-design/icons';
import type { Incident, IncidentSeverity, IncidentStatus } from '../types';

interface Props {
  incidents: Incident[];
  loading?: boolean;
  onAcknowledge?: (id: string) => void;
  onResolve?: (id: string) => void;
  onClose?: (id: string) => void;
  onAssign?: (id: string) => void;
  onAddPostmortem?: (id: string) => void;
  onDelete?: (id: string) => void;
  onViewDetails?: (id: string) => void;
  pagination?: {
    current: number;
    pageSize: number;
    total: number;
    onChange: (page: number, pageSize: number) => void;
  };
}

const getSeverityColor = (severity: IncidentSeverity) => {
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

const getStatusColor = (status: IncidentStatus) => {
  switch (status) {
    case 'triggered':
      return '#f5222d';
    case 'acknowledged':
      return '#fa8c16';
    case 'investigating':
      return '#1890ff';
    case 'resolved':
      return '#52c41a';
    case 'closed':
      return '#8c8c8c';
    default:
      return '#d9d9d9';
  }
};

export const IncidentList: React.FC<Props> = ({
  incidents,
  loading = false,
  onAcknowledge,
  onResolve,
  onClose,
  onAssign,
  onAddPostmortem,
  onDelete,
  onViewDetails,
  pagination,
}) => {
  const columns = [
    {
      title: 'Incident #',
      dataIndex: 'incident_number',
      key: 'incident_number',
      width: 150,
      render: (number: string) => (
        <a onClick={() => onViewDetails?.(number)}>{number}</a>
      ),
    },
    {
      title: 'Severity',
      dataIndex: 'severity',
      key: 'severity',
      width: 100,
      render: (severity: IncidentSeverity) => (
        <Badge color={getSeverityColor(severity)} text={severity.toUpperCase()} />
      ),
    },
    {
      title: 'Status',
      dataIndex: 'status',
      key: 'status',
      width: 120,
      render: (status: IncidentStatus) => (
        <Tag color={getStatusColor(status)}>{status}</Tag>
      ),
    },
    {
      title: 'Title',
      dataIndex: 'title',
      key: 'title',
      render: (title: string, record: Incident) => (
        <a onClick={() => onViewDetails?.(record.id)}>{title}</a>
      ),
    },
    {
      title: 'Started',
      dataIndex: 'started_at',
      key: 'started_at',
      width: 180,
      render: (date: string) => new Date(date).toLocaleString(),
    },
    {
      title: 'Duration',
      key: 'duration',
      width: 120,
      render: (_: any, record: Incident) => {
        const start = new Date(record.started_at);
        const end = record.resolved_at ? new Date(record.resolved_at) : new Date();
        const durationMs = end.getTime() - start.getTime();
        const hours = Math.floor(durationMs / (1000 * 60 * 60));
        const minutes = Math.floor((durationMs % (1000 * 60 * 60)) / (1000 * 60));
        return `${hours}h ${minutes}m`;
      },
    },
    {
      title: 'Actions',
      key: 'actions',
      width: 200,
      render: (_: any, record: Incident) => (
        <Space size="small">
          {record.status === 'triggered' && (
            <Tooltip title="Acknowledge">
              <Button
                type="link"
                size="small"
                icon={<CheckCircleOutlined />}
                onClick={() => onAcknowledge?.(record.id)}
              />
            </Tooltip>
          )}
          {['acknowledged', 'investigating'].includes(record.status) && (
            <Tooltip title="Resolve">
              <Button
                type="link"
                size="small"
                icon={<CheckCircleOutlined />}
                onClick={() => onResolve?.(record.id)}
              />
            </Tooltip>
          )}
          {record.status === 'resolved' && (
            <Tooltip title="Close">
              <Button
                type="link"
                size="small"
                icon={<CheckCircleOutlined />}
                onClick={() => onClose?.(record.id)}
              />
            </Tooltip>
          )}
          <Tooltip title="Assign">
            <Button
              type="link"
              size="small"
              icon={<UserAddOutlined />}
              onClick={() => onAssign?.(record.id)}
            />
          </Tooltip>
          {record.status === 'resolved' && (
            <Tooltip title="Add Postmortem">
              <Button
                type="link"
                size="small"
                icon={<FileTextOutlined />}
                onClick={() => onAddPostmortem?.(record.id)}
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
      dataSource={incidents}
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
