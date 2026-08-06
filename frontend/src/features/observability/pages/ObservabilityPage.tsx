import React, { useState, useEffect } from 'react';
import {
  Tabs,
  Card,
  Spin,
  message,
  Row,
  Col,
  Select,
  Button,
  Space,
  Modal,
  Form,
  Input,
  InputNumber,
  List,
  Typography,
  Drawer,
  Timeline,
  Empty,
} from 'antd';
import {
  DashboardOutlined,
  AlertOutlined,
  FireOutlined,
  SafetyOutlined,
  GlobalOutlined,
  PlusOutlined,
  ReloadOutlined,
} from '@ant-design/icons';
import { observabilityApi } from '../utils/api';
import { ObservabilityDashboard } from '../components/ObservabilityDashboard';
import { AlertList } from '../components/AlertList';
import { IncidentList } from '../components/IncidentList';
import { HealthCheckList } from '../components/HealthCheckList';
import { StatusPageViewer } from '../components/StatusPageViewer';
import type { DashboardSummary, Alert, Incident, HealthCheck, StatusPage } from '../types';

const { TabPane } = Tabs;
const { TextArea } = Input;
const { Option } = Select;
const { Text } = Typography;

export const ObservabilityPage: React.FC = () => {
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('dashboard');
  const [environment, setEnvironment] = useState('production');

  // Data states
  const [dashboardData, setDashboardData] = useState<DashboardSummary | null>(null);
  const [alerts, setAlerts] = useState<Alert[]>([]);
  const [incidents, setIncidents] = useState<Incident[]>([]);
  const [healthChecks, setHealthChecks] = useState<HealthCheck[]>([]);
  const [statusPages, setStatusPages] = useState<StatusPage[]>([]);
  const [selectedStatusPage, setSelectedStatusPage] = useState<StatusPage | null>(null);

  // Pagination
  const [alertsPagination, setAlertsPagination] = useState({ current: 1, pageSize: 15, total: 0 });
  const [incidentsPagination, setIncidentsPagination] = useState({ current: 1, pageSize: 15, total: 0 });
  const [healthChecksPagination, setHealthChecksPagination] = useState({ current: 1, pageSize: 15, total: 0 });

  // Modal states
  const [alertModalVisible, setAlertModalVisible] = useState(false);
  const [incidentModalVisible, setIncidentModalVisible] = useState(false);
  const [healthCheckModalVisible, setHealthCheckModalVisible] = useState(false);
  const [incidentDetailVisible, setIncidentDetailVisible] = useState(false);
  const [selectedIncident, setSelectedIncident] = useState<Incident | null>(null);
  const [statusPageDrawerVisible, setStatusPageDrawerVisible] = useState(false);

  const [alertForm] = Form.useForm();
  const [incidentForm] = Form.useForm();
  const [healthCheckForm] = Form.useForm();

  useEffect(() => {
    loadDashboardData();
  }, [environment]);

  const loadDashboardData = async () => {
    setLoading(true);
    try {
      const response = await observabilityApi.getDashboard(environment);
      setDashboardData(response.data);
      await loadAlerts();
      await loadIncidents();
      await loadHealthChecks();
      await loadStatusPages();
    } catch (error) {
      message.error('Failed to load observability data');
    } finally {
      setLoading(false);
    }
  };

  const loadAlerts = async (page = 1) => {
    try {
      const response = await observabilityApi.getAlerts({
        environment,
        per_page: alertsPagination.pageSize,
      });
      setAlerts(response.data);
      setAlertsPagination((prev) => ({ ...prev, current: page, total: response.meta?.total || 0 }));
    } catch (error) {
      message.error('Failed to load alerts');
    }
  };

  const loadIncidents = async (page = 1) => {
    try {
      const response = await observabilityApi.getIncidents({
        environment,
        per_page: incidentsPagination.pageSize,
      });
      setIncidents(response.data);
      setIncidentsPagination((prev) => ({ ...prev, current: page, total: response.meta?.total || 0 }));
    } catch (error) {
      message.error('Failed to load incidents');
    }
  };

  const loadHealthChecks = async (page = 1) => {
    try {
      const response = await observabilityApi.getHealthChecks({
        environment,
        per_page: healthChecksPagination.pageSize,
      });
      setHealthChecks(response.data);
      setHealthChecksPagination((prev) => ({ ...prev, current: page, total: response.meta?.total || 0 }));
    } catch (error) {
      message.error('Failed to load health checks');
    }
  };

  const loadStatusPages = async () => {
    try {
      const response = await observabilityApi.getActiveStatusPages();
      setStatusPages(response.data);
    } catch (error) {
      message.error('Failed to load status pages');
    }
  };

  const handleAcknowledgeAlert = async (id: string) => {
    try {
      await observabilityApi.acknowledgeAlert(id, 'current-user-id');
      message.success('Alert acknowledged');
      loadAlerts();
    } catch (error) {
      message.error('Failed to acknowledge alert');
    }
  };

  const handleResolveAlert = async (id: string) => {
    try {
      await observabilityApi.resolveAlert(id);
      message.success('Alert resolved');
      loadAlerts();
    } catch (error) {
      message.error('Failed to resolve alert');
    }
  };

  const handleSilenceAlert = async (id: string) => {
    try {
      await observabilityApi.silenceAlert(id);
      message.success('Alert silenced');
      loadAlerts();
    } catch (error) {
      message.error('Failed to silence alert');
    }
  };

  const handleDeleteAlert = async (id: string) => {
    try {
      await observabilityApi.deleteAlert(id);
      message.success('Alert deleted');
      loadAlerts();
    } catch (error) {
      message.error('Failed to delete alert');
    }
  };

  const handleAcknowledgeIncident = async (id: string) => {
    try {
      await observabilityApi.acknowledgeIncident(id, 'current-user-id');
      message.success('Incident acknowledged');
      loadIncidents();
    } catch (error) {
      message.error('Failed to acknowledge incident');
    }
  };

  const handleResolveIncident = async (id: string) => {
    try {
      await observabilityApi.resolveIncident(id, 'current-user-id');
      message.success('Incident resolved');
      loadIncidents();
    } catch (error) {
      message.error('Failed to resolve incident');
    }
  };

  const handleExecuteHealthCheck = async (id: string) => {
    try {
      await observabilityApi.executeHealthCheck(id);
      message.success('Health check executed');
      loadHealthChecks();
    } catch (error) {
      message.error('Failed to execute health check');
    }
  };

  const handleToggleHealthCheck = async (id: string) => {
    try {
      await observabilityApi.toggleHealthCheck(id);
      message.success('Health check toggled');
      loadHealthChecks();
    } catch (error) {
      message.error('Failed to toggle health check');
    }
  };

  const viewIncidentDetails = async (id: string) => {
    try {
      const response = await observabilityApi.getIncident(id);
      setSelectedIncident(response.data);
      setIncidentDetailVisible(true);
    } catch (error) {
      message.error('Failed to load incident details');
    }
  };

  const viewStatusPage = async (slug: string) => {
    try {
      const response = await observabilityApi.getPublicStatusPage(slug);
      setSelectedStatusPage(response.data);
      setStatusPageDrawerVisible(true);
    } catch (error) {
      message.error('Failed to load status page');
    }
  };

  const createAlert = async (values: any) => {
    try {
      await observabilityApi.createAlert({ ...values, environment });
      message.success('Alert created');
      setAlertModalVisible(false);
      alertForm.resetFields();
      loadAlerts();
    } catch (error) {
      message.error('Failed to create alert');
    }
  };

  const createIncident = async (values: any) => {
    try {
      await observabilityApi.createIncident({ ...values, environment });
      message.success('Incident created');
      setIncidentModalVisible(false);
      incidentForm.resetFields();
      loadIncidents();
    } catch (error) {
      message.error('Failed to create incident');
    }
  };

  const createHealthCheck = async (values: any) => {
    try {
      await observabilityApi.createHealthCheck({ ...values, environment });
      message.success('Health check created');
      setHealthCheckModalVisible(false);
      healthCheckForm.resetFields();
      loadHealthChecks();
    } catch (error) {
      message.error('Failed to create health check');
    }
  };

  if (loading && !dashboardData) {
    return (
      <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '100vh' }}>
        <Spin size="large" />
      </div>
    );
  }

  return (
    <div className="observability-page" style={{ padding: 24 }}>
      <div style={{ marginBottom: 16, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <h1>Observability Dashboard</h1>
        <Space>
          <Select value={environment} onChange={setEnvironment} style={{ width: 150 }}>
            <Option value="production">Production</Option>
            <Option value="staging">Staging</Option>
            <Option value="development">Development</Option>
          </Select>
          <Button icon={<ReloadOutlined />} onClick={loadDashboardData}>
            Refresh
          </Button>
        </Space>
      </div>

      <Tabs activeKey={activeTab} onChange={setActiveTab}>
        <TabPane
          tab={
            <span>
              <DashboardOutlined />
              Dashboard
            </span>
          }
          key="dashboard"
        >
          {dashboardData && <ObservabilityDashboard data={dashboardData} />}
        </TabPane>

        <TabPane
          tab={
            <span>
              <AlertOutlined />
              Alerts ({dashboardData?.active_alerts || 0})
            </span>
          }
          key="alerts"
        >
          <Card
            title="Alerts"
            extra={
              <Button type="primary" icon={<PlusOutlined />} onClick={() => setAlertModalVisible(true)}>
                Create Alert
              </Button>
            }
          >
            <AlertList
              alerts={alerts}
              onAcknowledge={handleAcknowledgeAlert}
              onResolve={handleResolveAlert}
              onSilence={handleSilenceAlert}
              onDelete={handleDeleteAlert}
              onViewDetails={(id) => message.info(`View alert ${id}`)}
              pagination={{
                ...alertsPagination,
                onChange: (page, pageSize) => {
                  setAlertsPagination((prev) => ({ ...prev, current: page, pageSize }));
                  loadAlerts(page);
                },
              }}
            />
          </Card>
        </TabPane>

        <TabPane
          tab={
            <span>
              <FireOutlined />
              Incidents ({dashboardData?.active_incidents || 0})
            </span>
          }
          key="incidents"
        >
          <Card
            title="Incidents"
            extra={
              <Button type="primary" icon={<PlusOutlined />} onClick={() => setIncidentModalVisible(true)}>
                Create Incident
              </Button>
            }
          >
            <IncidentList
              incidents={incidents}
              onAcknowledge={handleAcknowledgeIncident}
              onResolve={handleResolveIncident}
              onViewDetails={viewIncidentDetails}
              pagination={{
                ...incidentsPagination,
                onChange: (page, pageSize) => {
                  setIncidentsPagination((prev) => ({ ...prev, current: page, pageSize }));
                  loadIncidents(page);
                },
              }}
            />
          </Card>
        </TabPane>

        <TabPane
          tab={
            <span>
              <SafetyOutlined />
              Health Checks
            </span>
          }
          key="health-checks"
        >
          <Card
            title="Health Checks"
            extra={
              <Button type="primary" icon={<PlusOutlined />} onClick={() => setHealthCheckModalVisible(true)}>
                Create Health Check
              </Button>
            }
          >
            <HealthCheckList
              healthChecks={healthChecks}
              onExecute={handleExecuteHealthCheck}
              onToggle={handleToggleHealthCheck}
              onViewResults={(id) => message.info(`View results for health check ${id}`)}
              pagination={{
                ...healthChecksPagination,
                onChange: (page, pageSize) => {
                  setHealthChecksPagination((prev) => ({ ...prev, current: page, pageSize }));
                  loadHealthChecks(page);
                },
              }}
            />
          </Card>
        </TabPane>

        <TabPane
          tab={
            <span>
              <GlobalOutlined />
              Status Pages
            </span>
          }
          key="status-pages"
        >
          <Card title="Public Status Pages">
            {statusPages.length === 0 ? (
              <Empty description="No status pages configured" />
            ) : (
              <List
                grid={{ gutter: 16, xs: 1, sm: 2, md: 3 }}
                dataSource={statusPages}
                renderItem={(page) => (
                  <List.Item>
                    <Card
                      hoverable
                      onClick={() => viewStatusPage(page.slug)}
                      cover={
                        <div
                          style={{
                            height: 100,
                            backgroundColor: page.status === 'operational' ? '#52c41a' : '#faad14',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            color: '#fff',
                          }}
                        >
                          <GlobalOutlined style={{ fontSize: 40 }} />
                        </div>
                      }
                    >
                      <Card.Meta title={page.title} description={page.status} />
                    </Card>
                  </List.Item>
                )}
              />
            )}
          </Card>
        </TabPane>
      </Tabs>

      {/* Create Alert Modal */}
      <Modal
        title="Create Alert"
        open={alertModalVisible}
        onCancel={() => setAlertModalVisible(false)}
        onOk={() => alertForm.submit()}
      >
        <Form form={alertForm} layout="vertical" onFinish={createAlert}>
          <Form.Item name="name" label="Name" rules={[{ required: true }]}>
            <Input />
          </Form.Item>
          <Form.Item name="severity" label="Severity" rules={[{ required: true }]}>
            <Select>
              <Option value="critical">Critical</Option>
              <Option value="high">High</Option>
              <Option value="medium">Medium</Option>
              <Option value="low">Low</Option>
              <Option value="info">Info</Option>
            </Select>
          </Form.Item>
          <Form.Item name="description" label="Description">
            <Input.TextArea />
          </Form.Item>
          <Form.Item name="metric_name" label="Metric Name">
            <Input />
          </Form.Item>
          <Form.Item name="condition" label="Condition">
            <Select>
              <Option value="gt">Greater than</Option>
              <Option value="lt">Less than</Option>
              <Option value="gte">Greater than or equal</Option>
              <Option value="lte">Less than or equal</Option>
              <Option value="eq">Equal</Option>
            </Select>
          </Form.Item>
          <Form.Item name="threshold" label="Threshold">
            <InputNumber style={{ width: '100%' }} />
          </Form.Item>
        </Form>
      </Modal>

      {/* Create Incident Modal */}
      <Modal
        title="Create Incident"
        open={incidentModalVisible}
        onCancel={() => setIncidentModalVisible(false)}
        onOk={() => incidentForm.submit()}
      >
        <Form form={incidentForm} layout="vertical" onFinish={createIncident}>
          <Form.Item name="title" label="Title" rules={[{ required: true }]}>
            <Input />
          </Form.Item>
          <Form.Item name="severity" label="Severity" rules={[{ required: true }]}>
            <Select>
              <Option value="critical">Critical</Option>
              <Option value="high">High</Option>
              <Option value="medium">Medium</Option>
              <Option value="low">Low</Option>
            </Select>
          </Form.Item>
          <Form.Item name="description" label="Description">
            <Input.TextArea rows={4} />
          </Form.Item>
        </Form>
      </Modal>

      {/* Create Health Check Modal */}
      <Modal
        title="Create Health Check"
        open={healthCheckModalVisible}
        onCancel={() => setHealthCheckModalVisible(false)}
        onOk={() => healthCheckForm.submit()}
      >
        <Form form={healthCheckForm} layout="vertical" onFinish={createHealthCheck}>
          <Form.Item name="name" label="Name" rules={[{ required: true }]}>
            <Input />
          </Form.Item>
          <Form.Item name="type" label="Type" rules={[{ required: true }]}>
            <Select>
              <Option value="api">API</Option>
              <Option value="database">Database</Option>
              <Option value="queue">Queue</Option>
              <Option value="cache">Cache</Option>
              <Option value="storage">Storage</Option>
              <Option value="smtp">SMTP</Option>
              <Option value="sms">SMS</Option>
              <Option value="payment">Payment</Option>
              <Option value="custom">Custom</Option>
            </Select>
          </Form.Item>
          <Form.Item name="endpoint" label="Endpoint" rules={[{ required: true }]}>
            <Input />
          </Form.Item>
          <Form.Item name="method" label="Method" initialValue="GET">
            <Select>
              <Option value="GET">GET</Option>
              <Option value="POST">POST</Option>
              <Option value="PUT">PUT</Option>
              <Option value="PATCH">PATCH</Option>
              <Option value="HEAD">HEAD</Option>
            </Select>
          </Form.Item>
          <Form.Item name="check_interval_seconds" label="Check Interval (seconds)" initialValue={60}>
            <InputNumber min={10} style={{ width: '100%' }} />
          </Form.Item>
          <Form.Item name="timeout_seconds" label="Timeout (seconds)" initialValue={30}>
            <InputNumber min={1} style={{ width: '100%' }} />
          </Form.Item>
        </Form>
      </Modal>

      {/* Incident Detail Drawer */}
      <Drawer
        title={`Incident ${selectedIncident?.incident_number}`}
        placement="right"
        width={600}
        open={incidentDetailVisible}
        onClose={() => setIncidentDetailVisible(false)}
      >
        {selectedIncident && (
          <div>
            <h3>{selectedIncident.title}</h3>
            <p><strong>Severity:</strong> {selectedIncident.severity}</p>
            <p><strong>Status:</strong> {selectedIncident.status}</p>
            <p><strong>Started:</strong> {new Date(selectedIncident.started_at).toLocaleString()}</p>
            {selectedIncident.description && (
              <p><strong>Description:</strong> {selectedIncident.description}</p>
            )}

            <h4 style={{ marginTop: 24 }}>Timeline</h4>
            <Timeline>
              {selectedIncident.timeline?.map((event) => (
                <Timeline.Item key={event.id}>
                  <Text strong>{event.title}</Text>
                  <br />
                  <Text type="secondary">{new Date(event.occurred_at).toLocaleString()}</Text>
                  {event.description && <p>{event.description}</p>}
                </Timeline.Item>
              )) || <Empty description="No timeline events" />}
            </Timeline>
          </div>
        )}
      </Drawer>

      {/* Status Page Drawer */}
      <Drawer
        title="Status Page"
        placement="right"
        width={800}
        open={statusPageDrawerVisible}
        onClose={() => setStatusPageDrawerVisible(false)}
      >
        {selectedStatusPage && <StatusPageViewer statusPage={selectedStatusPage} />}
      </Drawer>
    </div>
  );
};
