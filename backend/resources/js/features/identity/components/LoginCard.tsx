import React, { useState } from 'react';
import { Card, Form, Input, Button, Checkbox, Alert, Divider, Space, Typography } from 'antd';
import { UserOutlined, LockOutlined, SafetyOutlined } from '@ant-design/icons';
import type { MFAFactor } from '../types';

const { Title, Text } = Typography;

interface LoginCardProps {
  onLogin: (email: string, password: string) => Promise<void>;
  onMFALogin?: (email: string, password: string, factorId: string, code: string) => Promise<void>;
  loading?: boolean;
}

const LoginCard: React.FC<LoginCardProps> = ({ onLogin, onMFALogin, loading = false }) => {
  const [form] = Form.useForm();
  const [showMFA, setShowMFA] = useState(false);
  const [mfaFactors, setMfaFactors] = useState<MFAFactor[]>([]);
  const [selectedFactor, setSelectedFactor] = useState<MFAFactor | null>(null);
  const [mfaCode, setMfaCode] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [remember, setRemember] = useState(false);

  const handleSubmit = async (values: { email: string; password: string; remember?: boolean }) => {
    setEmail(values.email);
    setPassword(values.password);
    setRemember(values.remember || false);

    try {
      await onLogin(values.email, values.password);
    } catch {
      // Error handled by parent
    }
  };

  const handleMFASubmit = async () => {
    if (!selectedFactor || !mfaCode) return;

    try {
      await onMFALogin?.(email, password, selectedFactor.id, mfaCode);
    } catch {
      // Error handled by parent
    }
  };

  if (showMFA && mfaFactors.length > 0) {
    return (
      <Card className="login-card" style={{ maxWidth: 400, margin: '0 auto' }}>
        <Space direction="vertical" size="large" style={{ width: '100%' }}>
          <div style={{ textAlign: 'center' }}>
            <SafetyOutlined style={{ fontSize: 48, color: '#1890ff' }} />
            <Title level={4}>Two-Factor Authentication</Title>
            <Text type="secondary">Enter the verification code from your authenticator app</Text>
          </div>

          <Form layout="vertical">
            <Form.Item label="Select Method">
              <Space direction="vertical" style={{ width: '100%' }}>
                {mfaFactors.map((factor) => (
                  <Button
                    key={factor.id}
                    type={selectedFactor?.id === factor.id ? 'primary' : 'default'}
                    onClick={() => setSelectedFactor(factor)}
                    block
                  >
                    {factor.name} ({factor.type.toUpperCase()})
                  </Button>
                ))}
              </Space>
            </Form.Item>

            <Form.Item label="Verification Code">
              <Input.OTP
                length={6}
                value={mfaCode}
                onChange={setMfaCode}
                disabled={!selectedFactor}
              />
            </Form.Item>

            <Form.Item>
              <Button
                type="primary"
                htmlType="submit"
                block
                loading={loading}
                disabled={!selectedFactor || mfaCode.length !== 6}
                onClick={handleMFASubmit}
              >
                Verify
              </Button>
            </Form.Item>

            <Button type="link" block onClick={() => setShowMFA(false)}>
              Back to Login
            </Button>
          </Form>
        </Space>
      </Card>
    );
  }

  return (
    <Card className="login-card" style={{ maxWidth: 400, margin: '0 auto' }}>
      <div style={{ textAlign: 'center', marginBottom: 24 }}>
        <Title level={4}>Sign In</Title>
        <Text type="secondary">Education ERP System</Text>
      </div>

      <Form
        form={form}
        layout="vertical"
        onFinish={handleSubmit}
        initialValues={{ remember: false }}
      >
        <Form.Item
          name="email"
          rules={[
            { required: true, message: 'Please input your email!' },
            { type: 'email', message: 'Please enter a valid email!' },
          ]}
        >
          <Input
            prefix={<UserOutlined />}
            placeholder="Email address"
            size="large"
          />
        </Form.Item>

        <Form.Item
          name="password"
          rules={[{ required: true, message: 'Please input your password!' }]}
        >
          <Input.Password
            prefix={<LockOutlined />}
            placeholder="Password"
            size="large"
          />
        </Form.Item>

        <Form.Item>
          <Space style={{ width: '100%', justifyContent: 'space-between' }}>
            <Form.Item name="remember" valuePropName="checked" noStyle>
              <Checkbox>Remember me</Checkbox>
            </Form.Item>
            <Button type="link" style={{ padding: 0 }}>
              Forgot password?
            </Button>
          </Space>
        </Form.Item>

        <Form.Item>
          <Button type="primary" htmlType="submit" block size="large" loading={loading}>
            Sign In
          </Button>
        </Form.Item>
      </Form>

      <Divider>Or continue with</Divider>

      <Space style={{ width: '100%', justifyContent: 'center' }}>
        <Button icon={<SafetyOutlined />}>SSO</Button>
        <Button icon={<SafetyOutlined />}>Passkey</Button>
      </Space>
    </Card>
  );
};

export default LoginCard;
