import React from 'react';
import { Card, Badge, Timeline, Typography, Space, Row, Col } from 'antd';
import {
  CheckCircleOutlined,
  WarningOutlined,
  CloseCircleOutlined,
  SyncOutlined,
  ExclamationCircleOutlined,
} from '@ant-design/icons';
import type { StatusPage, StatusPageComponent, Incident } from '../types';

interface Props {
  statusPage: StatusPage;
  showHeader?: boolean;
  showFooter?: boolean;
}

const { Title, Text } = Typography;

const getComponentStatusIcon = (status: string) => {
  switch (status) {
    case 'operational':
      return <CheckCircleOutlined style={{ color: '#52c41a' }} />;
    case 'degraded':
      return <WarningOutlined style={{ color: '#faad14' }} />;
    case 'partial_outage':
      return <ExclamationCircleOutlined style={{ color: '#fa8c16' }} />;
    case 'major_outage':
    case 'down':
      return <CloseCircleOutlined style={{ color: '#f5222d' }} />;
    case 'maintenance':
      return <SyncOutlined style={{ color: '#1890ff' }} />;
    default:
      return <WarningOutlined style={{ color: '#d9d9d9' }} />;
  }
};

const getComponentStatusColor = (status: string) => {
  switch (status) {
    case 'operational':
      return '#52c41a';
    case 'degraded':
      return '#faad14';
    case 'partial_outage':
      return '#fa8c16';
    case 'major_outage':
    case 'down':
      return '#f5222d';
    case 'maintenance':
      return '#1890ff';
    default:
      return '#d9d9d9';
  }
};

const getOverallStatusColor = (status: string) => {
  switch (status) {
    case 'operational':
      return '#52c41a';
    case 'degraded':
      return '#faad14';
    case 'partial_outage':
      return '#fa8c16';
    case 'major_outage':
    case 'down':
      return '#f5222d';
    case 'maintenance':
      return '#1890ff';
    default:
      return '#d9d9d9';
  }
};

export const StatusPageViewer: React.FC<Props> = ({
  statusPage,
  showHeader = true,
  showFooter = true,
}) => {
  const { components = [], active_incidents = [] } = statusPage;

  return (
    <div className="status-page-viewer">
      {showHeader && statusPage.header_settings && (
        <div
          className="status-page-header"
          style={{ backgroundColor: statusPage.header_settings.backgroundColor }}
        >
          {statusPage.logo_url && (
            <img src={statusPage.logo_url} alt={statusPage.title} className="status-logo" />
          )}
          <Title level={3} style={{ color: statusPage.header_settings.textColor, margin: 0 }}>
            {statusPage.title}
          </Title>
        </div>
      )}

      {/* Overall Status Banner */}
      <div
        className="overall-status-banner"
        style={{ backgroundColor: getOverallStatusColor(statusPage.status) }}
      >
        <Space>
          {getComponentStatusIcon(statusPage.status)}
          <Text strong style={{ color: '#fff' }}>
            {statusPage.status === 'operational'
              ? 'All Systems Operational'
              : statusPage.status === 'maintenance'
              ? 'System Under Maintenance'
              : 'System Experience Issues'}
          </Text>
        </Space>
        <Text style={{ color: '#fff' }}>
          Last updated: {new Date(statusPage.updated_at).toLocaleString()}
        </Text>
      </div>

      {/* Active Incidents */}
      {active_incidents.length > 0 && (
        <Card title="Active Incidents" style={{ marginTop: 16 }}>
          <Timeline>
            {active_incidents.map((incident) => (
              <Timeline.Item
                key={incident.id}
                color={incident.severity === 'critical' ? 'red' : 'orange'}
              >
                <Space direction="vertical" size={0}>
                  <Text strong>{incident.title}</Text>
                  <Text type="secondary">
                    {incident.incident_number} - {incident.severity} severity
                  </Text>
                  <Text type="secondary">
                    Started: {new Date(incident.started_at).toLocaleString()}
                  </Text>
                  {incident.resolved_at && (
                    <Text type="secondary">
                      Resolved: {new Date(incident.resolved_at).toLocaleString()}
                    </Text>
                  )}
                </Space>
              </Timeline.Item>
            ))}
          </Timeline>
        </Card>
      )}

      {/* Components Status */}
      <Card title="Services" style={{ marginTop: 16 }}>
        <Row gutter={[16, 16]}>
          {components.map((component) => (
            <Col xs={24} sm={12} md={8} lg={6} key={component.id}>
              <Card size="small" className="component-card">
                <div className="component-header">
                  {getComponentStatusIcon(component.status)}
                  <Text strong>{component.name}</Text>
                </div>
                {component.description && (
                  <Text type="secondary" className="component-description">
                    {component.description}
                  </Text>
                )}
                <div className="component-status">
                  <Badge
                    color={getComponentStatusColor(component.status)}
                    text={component.status.replace('_', ' ')}
                  />
                </div>
              </Card>
            </Col>
          ))}
        </Row>
      </Card>

      {showFooter && statusPage.footer_settings && (
        <div className="status-page-footer">
          <Text type="secondary">{statusPage.footer_settings.text}</Text>
        </div>
      )}

      <style>{`
        .status-page-viewer .status-page-header {
          display: flex;
          align-items: center;
          gap: 16px;
          padding: 16px 24px;
          border-radius: 8px;
          margin-bottom: 16px;
        }
        .status-page-viewer .status-logo {
          height: 40px;
        }
        .status-page-viewer .overall-status-banner {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 16px 24px;
          border-radius: 8px;
        }
        .status-page-viewer .component-card {
          height: 100%;
        }
        .status-page-viewer .component-header {
          display: flex;
          align-items: center;
          gap: 8px;
          margin-bottom: 8px;
        }
        .status-page-viewer .component-description {
          display: block;
          font-size: 12px;
          margin-bottom: 8px;
        }
        .status-page-viewer .component-status {
          margin-top: 8px;
        }
        .status-page-viewer .status-page-footer {
          text-align: center;
          padding: 16px;
          margin-top: 16px;
        }
      `}</style>
    </div>
  );
};
