import React from 'react';
import { Card, Row, Col, Badge, Progress } from 'antd';
import {
  CheckCircleOutlined,
  WarningOutlined,
  CloseCircleOutlined,
  AlertOutlined,
  FireOutlined,
} from '@ant-design/icons';
import type { DashboardSummary } from '../types';

interface Props {
  data: DashboardSummary;
}

const getStatusColor = (status: string) => {
  switch (status) {
    case 'healthy':
      return '#52c41a';
    case 'degraded':
      return '#faad14';
    case 'down':
      return '#f5222d';
    default:
      return '#d9d9d9';
  }
};

const getSeverityColor = (severity: string) => {
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

export const ObservabilityDashboard: React.FC<Props> = ({ data }) => {
  return (
    <div className="observability-dashboard">
      {/* Service Health Overview */}
      <Row gutter={[16, 16]}>
        <Col xs={24} sm={12} md={6}>
          <Card>
            <div className="stat-card">
              <CheckCircleOutlined style={{ fontSize: 24, color: '#52c41a' }} />
              <div className="stat-content">
                <div className="stat-value">{data.healthy_services}</div>
                <div className="stat-label">Healthy Services</div>
              </div>
            </div>
          </Card>
        </Col>
        <Col xs={24} sm={12} md={6}>
          <Card>
            <div className="stat-card">
              <WarningOutlined style={{ fontSize: 24, color: '#faad14' }} />
              <div className="stat-content">
                <div className="stat-value">{data.degraded_services}</div>
                <div className="stat-label">Degraded Services</div>
              </div>
            </div>
          </Card>
        </Col>
        <Col xs={24} sm={12} md={6}>
          <Card>
            <div className="stat-card">
              <CloseCircleOutlined style={{ fontSize: 24, color: '#f5222d' }} />
              <div className="stat-content">
                <div className="stat-value">{data.down_services}</div>
                <div className="stat-label">Down Services</div>
              </div>
            </div>
          </Card>
        </Col>
        <Col xs={24} sm={12} md={6}>
          <Card>
            <div className="stat-card">
              <AlertOutlined style={{ fontSize: 24, color: '#1890ff' }} />
              <div className="stat-content">
                <div className="stat-value">{data.active_alerts}</div>
                <div className="stat-label">Active Alerts</div>
              </div>
            </div>
          </Card>
        </Col>
      </Row>

      {/* Incidents and Availability */}
      <Row gutter={[16, 16]} style={{ marginTop: 16 }}>
        <Col xs={24} md={8}>
          <Card title="Active Incidents">
            <div className="incident-summary">
              <div className="incident-count">
                <FireOutlined style={{ color: '#f5222d' }} />
                <span className="count">{data.active_incidents}</span>
                <span className="label">Active</span>
              </div>
              <div className="incident-breakdown">
                {Object.entries(data.incident_severity_breakdown).map(([severity, count]) => (
                  <div key={severity} className="breakdown-item">
                    <Badge color={getSeverityColor(severity)} />
                    <span>{severity}: {count}</span>
                  </div>
                ))}
              </div>
            </div>
          </Card>
        </Col>
        <Col xs={24} md={8}>
          <Card title="Average Availability">
            <div className="availability-card">
              <Progress
                type="circle"
                percent={data.average_availability}
                format={(percent) => `${percent}%`}
                strokeColor={data.average_availability >= 99 ? '#52c41a' : data.average_availability >= 95 ? '#faad14' : '#f5222d'}
              />
            </div>
          </Card>
        </Col>
        <Col xs={24} md={8}>
          <Card title="Alert Severity Breakdown">
            <div className="alert-breakdown">
              {Object.entries(data.alert_severity_breakdown).map(([severity, count]) => (
                <div key={severity} className="breakdown-item">
                  <Badge color={getSeverityColor(severity)} />
                  <span>{severity}: {count}</span>
                </div>
              ))}
            </div>
          </Card>
        </Col>
      </Row>

      {/* Recent Incidents */}
      <Row gutter={[16, 16]} style={{ marginTop: 16 }}>
        <Col xs={24} lg={12}>
          <Card title="Recent Incidents">
            <div className="recent-list">
              {data.recent_incidents.length === 0 ? (
                <div className="empty-state">No recent incidents</div>
              ) : (
                data.recent_incidents.map((incident) => (
                  <div key={incident.id} className="list-item">
                    <Badge color={getSeverityColor(incident.severity)} />
                    <div className="item-content">
                      <div className="item-title">{incident.title}</div>
                      <div className="item-meta">
                        <span>{incident.incident_number}</span>
                        <span>-</span>
                        <span>{new Date(incident.started_at).toLocaleString()}</span>
                      </div>
                    </div>
                    <Badge status={incident.status === 'resolved' ? 'success' : 'error'} text={incident.status} />
                  </div>
                ))
              )}
            </div>
          </Card>
        </Col>
        <Col xs={24} lg={12}>
          <Card title="Top Alerts">
            <div className="recent-list">
              {data.top_alerts.length === 0 ? (
                <div className="empty-state">No active alerts</div>
              ) : (
                data.top_alerts.map((alert) => (
                  <div key={alert.id} className="list-item">
                    <Badge color={getSeverityColor(alert.severity)} />
                    <div className="item-content">
                      <div className="item-title">{alert.name}</div>
                      <div className="item-meta">
                        {alert.triggered_at && (
                          <span>Triggered: {new Date(alert.triggered_at).toLocaleString()}</span>
                        )}
                      </div>
                    </div>
                    <Badge status={alert.status === 'active' ? 'error' : 'warning'} text={alert.status} />
                  </div>
                ))
              )}
            </div>
          </Card>
        </Col>
      </Row>

      {/* SLO Summary */}
      {data.slo_summary.length > 0 && (
        <Row gutter={[16, 16]} style={{ marginTop: 16 }}>
          <Col xs={24}>
            <Card title="SLO Summary">
              <Row gutter={[16, 16]}>
                {data.slo_summary.map((slo) => (
                  <Col xs={24} sm={12} md={8} lg={6} key={slo.id}>
                    <Card size="small" className="slo-card">
                      <div className="slo-header">
                        <span className="slo-name">{slo.name}</span>
                        <Badge color={slo.is_breached ? '#f5222d' : '#52c41a'} />
                      </div>
                      <div className="slo-target">
                        Target: {slo.target}%
                      </div>
                      {slo.current !== null && (
                        <Progress
                          percent={Math.min(slo.current, 100)}
                          strokeColor={slo.is_breached ? '#f5222d' : '#52c41a'}
                          format={(percent) => `${percent}%`}
                        />
                      )}
                    </Card>
                  </Col>
                ))}
              </Row>
            </Card>
          </Col>
        </Row>
      )}

      <style>{`
        .observability-dashboard .stat-card {
          display: flex;
          align-items: center;
          gap: 16px;
        }
        .observability-dashboard .stat-content {
          flex: 1;
        }
        .observability-dashboard .stat-value {
          font-size: 24px;
          font-weight: bold;
        }
        .observability-dashboard .stat-label {
          color: #8c8c8c;
          font-size: 14px;
        }
        .observability-dashboard .availability-card {
          display: flex;
          justify-content: center;
          padding: 20px;
        }
        .observability-dashboard .incident-summary,
        .observability-dashboard .alert-breakdown {
          display: flex;
          flex-direction: column;
          gap: 12px;
        }
        .observability-dashboard .incident-count {
          display: flex;
          align-items: center;
          gap: 12px;
          font-size: 20px;
        }
        .observability-dashboard .incident-count .count {
          font-weight: bold;
          font-size: 32px;
        }
        .observability-dashboard .breakdown-item {
          display: flex;
          align-items: center;
          gap: 8px;
        }
        .observability-dashboard .recent-list {
          display: flex;
          flex-direction: column;
          gap: 12px;
        }
        .observability-dashboard .list-item {
          display: flex;
          align-items: flex-start;
          gap: 12px;
          padding: 8px;
          border-bottom: 1px solid #f0f0f0;
        }
        .observability-dashboard .item-content {
          flex: 1;
        }
        .observability-dashboard .item-title {
          font-weight: 500;
        }
        .observability-dashboard .item-meta {
          font-size: 12px;
          color: #8c8c8c;
          display: flex;
          gap: 8px;
        }
        .observability-dashboard .slo-card {
          background: #fafafa;
        }
        .observability-dashboard .slo-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 8px;
        }
        .observability-dashboard .slo-name {
          font-weight: 500;
        }
        .observability-dashboard .slo-target {
          font-size: 12px;
          color: #8c8c8c;
          margin-bottom: 8px;
        }
        .observability-dashboard .empty-state {
          text-align: center;
          color: #8c8c8c;
          padding: 20px;
        }
      `}</style>
    </div>
  );
};
