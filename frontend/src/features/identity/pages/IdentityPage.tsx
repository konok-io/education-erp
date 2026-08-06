import React, { useState, useEffect, useCallback } from 'react';
import {
  Tabs,
  Card,
  Button,
  Space,
  Typography,
  Badge,
  Tag,
  Modal,
  message,
} from 'antd';
import {
  SafetyOutlined,
  KeyOutlined,
  MobileOutlined,
  MailOutlined,
  LockOutlined,
} from '@ant-design/icons';
import {
  SessionTable,
  MFASetupWizard,
  mfaApi,
  sessionApi,
} from '../index';
import type { Session, MFAFactor } from '../types';

const { Title } = Typography;

const IdentityPage: React.FC = () => {
  const [activeTab, setActiveTab] = useState('sessions');
  const [sessions, setSessions] = useState<Session[]>([]);
  const [mfaFactors, setMfaFactors] = useState<MFAFactor[]>([]);
  const [loading, setLoading] = useState(false);
  const [showMFASetup, setShowMFASetup] = useState(false);

  const fetchSessions = useCallback(async () => {
    setLoading(true);
    try {
      const response = await sessionApi.list();
      if (response.data?.success) {
        setSessions(response.data.data);
      }
    } catch {
      message.error('Failed to fetch sessions');
    } finally {
      setLoading(false);
    }
  }, []);

  const fetchMFAFactors = useCallback(async () => {
    setLoading(true);
    try {
      const response = await mfaApi.list();
      if (response.data?.success) {
        setMfaFactors(response.data.data);
      }
    } catch {
      message.error('Failed to fetch MFA factors');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchSessions();
    fetchMFAFactors();
  }, [fetchSessions, fetchMFAFactors]);

  const handleMFADelete = async (factorId: string) => {
    try {
      await mfaApi.delete(factorId);
      message.success('MFA factor removed successfully');
      fetchMFAFactors();
    } catch {
      message.error('Failed to remove MFA factor');
    }
  };

  const getMFATypeIcon = (type: string) => {
    switch (type) {
      case 'totp':
        return <SafetyOutlined />;
      case 'sms':
        return <MobileOutlined />;
      case 'email':
        return <MailOutlined />;
      default:
        return <KeyOutlined />;
    }
  };

  const tabItems = [
    {
      key: 'sessions',
      label: (
        <span>
          <KeyOutlined />
          Active Sessions
        </span>
      ),
      children: (
        <Card
          title="Manage Sessions"
          extra={
            <Button onClick={fetchSessions}>Refresh</Button>
          }
        >
          <SessionTable
            sessions={sessions}
            loading={loading}
            onRefresh={fetchSessions}
          />
        </Card>
      ),
    },
    {
      key: 'mfa',
      label: (
        <span>
          <SafetyOutlined />
          Two-Factor Authentication
          {mfaFactors.length > 0 && (
            <Badge count={mfaFactors.length} size="small" style={{ marginLeft: 8 }} />
          )}
        </span>
      ),
      children: (
        <Card
          title="Two-Factor Authentication"
          extra={
            mfaFactors.length === 0 && (
              <Button
                type="primary"
                icon={<SafetyOutlined />}
                onClick={() => setShowMFASetup(true)}
              >
                Enable 2FA
              </Button>
            )
          }
        >
          {mfaFactors.length > 0 ? (
            <Space direction="vertical" size="large" style={{ width: '100%' }}>
              <Alert
                message="2FA is enabled"
                description="Your account is protected with two-factor authentication."
                type="success"
                showIcon
              />
              <Space direction="vertical" style={{ width: '100%' }}>
                {mfaFactors.map((factor) => (
                  <Card
                    key={factor.id}
                    size="small"
                    style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}
                  >
                    <Space>
                      {getMFATypeIcon(factor.type)}
                      <div>
                        <div style={{ fontWeight: 500 }}>{factor.name}</div>
                        <div style={{ fontSize: 12, color: '#999' }}>
                          {factor.type.toUpperCase()} {factor.default && '(Default)'} {factor.backup && '(Backup)'}
                        </div>
                      </div>
                    </Space>
                    <Space>
                      <Tag color={factor.verified ? 'green' : 'orange'}>
                        {factor.verified ? 'Verified' : 'Pending'}
                      </Tag>
                      {!factor.backup && (
                        <Button
                          size="small"
                          danger
                          onClick={() => handleMFADelete(factor.id)}
                        >
                          Remove
                        </Button>
                      )}
                    </Space>
                  </Card>
                ))}
              </Space>
              <Button onClick={() => setShowMFASetup(true)}>
                Add Another Method
              </Button>
            </Space>
          ) : (
            <EmptyState2FA onSetup={() => setShowMFASetup(true)} />
          )}
        </Card>
      ),
    },
    {
      key: 'password',
      label: (
        <span>
          <LockOutlined />
          Password
        </span>
      ),
      children: (
        <Card title="Change Password">
          <Alert
            message="Password Management"
            description="Update your password regularly to keep your account secure."
            type="info"
            showIcon
            style={{ marginBottom: 16 }}
          />
          <Button type="primary">Change Password</Button>
        </Card>
      ),
    },
  ];

  return (
    <div className="identity-page" style={{ padding: 24 }}>
      <div style={{ marginBottom: 24 }}>
        <Title level={4}>Security Settings</Title>
      </div>
      <Tabs
        activeKey={activeTab}
        onChange={setActiveTab}
        items={tabItems}
      />
      <Modal
        title="Setup Two-Factor Authentication"
        open={showMFASetup}
        onCancel={() => setShowMFASetup(false)}
        footer={null}
        width={600}
        destroyOnClose
      >
        <MFASetupWizard
          onComplete={() => {
            setShowMFASetup(false);
            fetchMFAFactors();
          }}
          onCancel={() => setShowMFASetup(false)}
        />
      </Modal>
    </div>
  );
};

const EmptyState2FA: React.FC<{ onSetup: () => void }> = ({ onSetup }) => (
  <div style={{ textAlign: 'center', padding: 40 }}>
    <SafetyOutlined style={{ fontSize: 64, color: '#ccc' }} />
    <Title level={4} style={{ marginTop: 16 }}>Two-Factor Authentication</Title>
    <p style={{ color: '#999' }}>
      Add an extra layer of security to your account by enabling two-factor authentication.
    </p>
    <Button type="primary" onClick={onSetup}>
      Enable 2FA
    </Button>
  </div>
);

export default IdentityPage;
