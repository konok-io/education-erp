import React, { useState } from 'react';
import { Card, Steps, Button, Space, Form, Input, Alert, Image, message } from 'antd';
import {
  SafetyOutlined,
  MobileOutlined,
  MailOutlined,
  KeyOutlined,
} from '@ant-design/icons';
import { mfaApi } from '../utils/api';

interface MFASetupWizardProps {
  onComplete: () => void;
  onCancel: () => void;
}

const MFASetupWizard: React.FC<MFASetupWizardProps> = ({ onComplete, onCancel }) => {
  const [currentStep, setCurrentStep] = useState(0);
  const [method, setMethod] = useState<'totp' | 'sms' | null>(null);
  const [totpSetup, setTotpSetup] = useState<{ factor_id: string; secret: string; qr_code_url: string } | null>(null);
  const [verificationCode, setVerificationCode] = useState('');
  const [loading, setLoading] = useState(false);
  const [phoneNumber, setPhoneNumber] = useState('');
  const [factorName, setFactorName] = useState('My Device');

  const steps = [
    { title: 'Choose Method', icon: <SafetyOutlined /> },
    { title: 'Configure', icon: <KeyOutlined /> },
    { title: 'Verify', icon: <SafetyOutlined /> },
  ];

  const handleSelectMethod = (selectedMethod: 'totp' | 'sms') => {
    setMethod(selectedMethod);
    setCurrentStep(1);
  };

  const handleSetupTOTP = async () => {
    setLoading(true);
    try {
      const response = await mfaApi.setupTOTP(factorName);
      if (response.data?.success) {
        setTotpSetup(response.data.data);
        setCurrentStep(2);
      }
    } catch {
      message.error('Failed to setup TOTP');
    } finally {
      setLoading(false);
    }
  };

  const handleVerify = async () => {
    if (!totpSetup || !verificationCode) return;

    setLoading(true);
    try {
      await mfaApi.verifySetup(totpSetup.factor_id, verificationCode);
      message.success('MFA enabled successfully!');
      onComplete();
    } catch {
      message.error('Invalid verification code');
    } finally {
      setLoading(false);
    }
  };

  const renderStepContent = () => {
    switch (currentStep) {
      case 0:
        return (
          <Space direction="vertical" size="large" style={{ width: '100%' }}>
            <Alert
              message="Add an extra layer of security"
              description="Two-factor authentication (2FA) helps protect your account by requiring a verification code in addition to your password."
              type="info"
              showIcon
            />
            <Space style={{ width: '100%', justifyContent: 'center' }}>
              <Card
                hoverable
                style={{ width: 200, textAlign: 'center' }}
                onClick={() => handleSelectMethod('totp')}
                cover={<SafetyOutlined style={{ fontSize: 48, margin: 20 }} />}
              >
                <Card.Meta title="Authenticator App" description="Use Google Authenticator, Authy, etc." />
              </Card>
              <Card
                hoverable
                style={{ width: 200, textAlign: 'center' }}
                onClick={() => handleSelectMethod('sms')}
                cover={<MobileOutlined style={{ fontSize: 48, margin: 20 }} />}
              >
                <Card.Meta title="SMS" description="Receive code via text message" />
              </Card>
            </Space>
          </Space>
        );

      case 1:
        return (
          <Space direction="vertical" size="large" style={{ width: '100%' }}>
            <Form layout="vertical">
              <Form.Item label="Device Name">
                <Input
                  value={factorName}
                  onChange={(e) => setFactorName(e.target.value)}
                  placeholder="e.g., My iPhone"
                />
              </Form.Item>
              {method === 'sms' && (
                <Form.Item label="Phone Number">
                  <Input
                    value={phoneNumber}
                    onChange={(e) => setPhoneNumber(e.target.value)}
                    placeholder="+1234567890"
                  />
                </Form.Item>
              )}
            </Form>
            <Button type="primary" onClick={handleSetupTOTP} loading={loading} block>
              Continue
            </Button>
          </Space>
        );

      case 2:
        return (
          <Space direction="vertical" size="large" style={{ width: '100%' }}>
            {totpSetup && (
              <>
                <div style={{ textAlign: 'center' }}>
                  <Image src={totpSetup.qr_code_url} alt="QR Code" width={200} />
                </div>
                <Alert
                  message="Manual entry key"
                  description={totpSetup.secret}
                  type="warning"
                  style={{ fontFamily: 'monospace' }}
                />
              </>
            )}
            <Form layout="vertical">
              <Form.Item label="Enter the 6-digit code from your authenticator app">
                <Input.OTP
                  length={6}
                  value={verificationCode}
                  onChange={setVerificationCode}
                />
              </Form.Item>
            </Form>
            <Button
              type="primary"
              onClick={handleVerify}
              loading={loading}
              disabled={verificationCode.length !== 6}
              block
            >
              Verify & Enable
            </Button>
          </Space>
        );

      default:
        return null;
    }
  };

  return (
    <Card title="Setup Two-Factor Authentication">
      <Steps
        current={currentStep}
        items={steps}
        style={{ marginBottom: 24 }}
      />
      {renderStepContent()}
      <div style={{ marginTop: 24, textAlign: 'center' }}>
        <Button onClick={currentStep === 0 ? onCancel : () => setCurrentStep(currentStep - 1)}>
          {currentStep === 0 ? 'Cancel' : 'Back'}
        </Button>
      </div>
    </Card>
  );
};

export default MFASetupWizard;
