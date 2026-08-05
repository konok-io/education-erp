import React, { useState } from 'react';
import {
  CheckCircle,
  Clock,
  Lock,
  AlertTriangle,
  ChevronRight,
} from 'lucide-react';

interface Closing {
  id: string;
  code: string;
  type: string;
  period: string;
  startDate: string;
  endDate: string;
  closingDate: string;
  status: string;
  isLocked: boolean;
  checklist: { item: string; completed: boolean }[];
}

const closings: Closing[] = [
  {
    id: '1',
    code: 'CLS-2026-01',
    type: 'Monthly',
    period: 'January 2026',
    startDate: '2026-01-01',
    endDate: '2026-01-31',
    closingDate: '2026-02-05',
    status: 'closed',
    isLocked: true,
    checklist: [
      { item: 'All journals posted', completed: true },
      { item: 'Trial balance verified', completed: true },
      { item: 'Bank reconciliation complete', completed: true },
      { item: 'Tax returns filed', completed: true },
    ],
  },
  {
    id: '2',
    code: 'CLS-2026-02',
    type: 'Monthly',
    period: 'February 2026',
    startDate: '2026-02-01',
    endDate: '2026-02-28',
    closingDate: '',
    status: 'in_progress',
    isLocked: false,
    checklist: [
      { item: 'All journals posted', completed: true },
      { item: 'Trial balance verified', completed: true },
      { item: 'Bank reconciliation complete', completed: false },
      { item: 'Tax returns filed', completed: false },
    ],
  },
  {
    id: '3',
    code: 'CLS-2025-Q4',
    type: 'Quarterly',
    period: 'Q4 2025',
    startDate: '2025-10-01',
    endDate: '2025-12-31',
    closingDate: '2026-01-15',
    status: 'closed',
    isLocked: true,
    checklist: [
      { item: 'All journals posted', completed: true },
      { item: 'Trial balance verified', completed: true },
      { item: 'Financial statements prepared', completed: true },
      { item: 'Audit completed', completed: true },
    ],
  },
  {
    id: '4',
    code: 'CLS-2025-AN',
    type: 'Annual',
    period: 'FY 2025',
    startDate: '2025-01-01',
    endDate: '2025-12-31',
    closingDate: '2026-01-31',
    status: 'closed',
    isLocked: true,
    checklist: [
      { item: 'All journals posted', completed: true },
      { item: 'Trial balance verified', completed: true },
      { item: 'Financial statements prepared', completed: true },
      { item: 'Tax returns filed', completed: true },
      { item: 'Audit completed', completed: true },
      { item: 'Fiscal year locked', completed: true },
    ],
  },
];

const FinancialClosing: React.FC = () => {
  const [selectedClosing, setSelectedClosing] = useState<Closing | null>(null);
  const [showChecklist, setShowChecklist] = useState(false);

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'closed':
        return <CheckCircle className="w-5 h-5 text-green-600" />;
      case 'in_progress':
        return <Clock className="w-5 h-5 text-yellow-600" />;
      case 'pending':
        return <AlertTriangle className="w-5 h-5 text-orange-600" />;
      default:
        return <Clock className="w-5 h-5 text-gray-400" />;
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'closed': return 'bg-green-100 text-green-800 border-green-200';
      case 'in_progress': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
      case 'pending': return 'bg-orange-100 text-orange-800 border-orange-200';
      default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Financial Closing</h1>
          <p className="text-gray-500">Period Lock & Fiscal Year Closing</p>
        </div>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Start New Closing
        </button>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
              <Lock className="w-5 h-5 text-green-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Closed Periods</p>
              <p className="text-2xl font-bold text-green-600">3</p>
            </div>
          </div>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
              <Clock className="w-5 h-5 text-yellow-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">In Progress</p>
              <p className="text-2xl font-bold text-yellow-600">1</p>
            </div>
          </div>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
              <CheckCircle className="w-5 h-5 text-blue-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Locked Fiscal Years</p>
              <p className="text-2xl font-bold text-blue-600">1</p>
            </div>
          </div>
        </div>
        <div className="bg-white p-4 rounded-lg border border-gray-100">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
              <AlertTriangle className="w-5 h-5 text-purple-600" />
            </div>
            <div>
              <p className="text-sm text-gray-500">Pending Actions</p>
              <p className="text-2xl font-bold text-purple-600">2</p>
            </div>
          </div>
        </div>
      </div>

      {/* Workflow Diagram */}
      <div className="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
        <h3 className="text-lg font-semibold mb-6">Closing Workflow</h3>
        <div className="flex items-center justify-between">
          {['Verify\nJournals', 'Verify\nLedger', 'Trial\nBalance', 'Financial\nStatements', 'Tax\nValidation', 'Close\nPeriod', 'Lock\nFiscal Year'].map((step, index) => (
            <div key={index} className="flex items-center">
              <div className={`w-20 h-20 rounded-full flex items-center justify-center text-center ${
                index < 3 ? 'bg-green-100 border-2 border-green-500' :
                index === 3 ? 'bg-yellow-100 border-2 border-yellow-500' :
                'bg-gray-100 border-2 border-gray-300'
              }`}>
                <span className={`text-xs font-medium ${
                  index < 3 ? 'text-green-700' :
                  index === 3 ? 'text-yellow-700' :
                  'text-gray-500'
                }`}>{step}</span>
              </div>
              {index < 6 && <ChevronRight className="w-6 h-6 text-gray-400 mx-2" />}
            </div>
          ))}
        </div>
      </div>

      {/* Closing Table */}
      <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div className="p-4 border-b border-gray-100">
          <h3 className="font-semibold text-gray-900">Financial Closings</h3>
        </div>
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Range</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Closing Date</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Locked</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-100">
            {closings.map((closing) => (
              <tr key={closing.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 font-medium text-blue-600">{closing.code}</td>
                <td className="px-6 py-4 text-gray-600">{closing.type}</td>
                <td className="px-6 py-4 text-gray-900">{closing.period}</td>
                <td className="px-6 py-4 text-gray-600">{closing.startDate} - {closing.endDate}</td>
                <td className="px-6 py-4 text-gray-600">{closing.closingDate || '-'}</td>
                <td className="px-6 py-4 text-center">
                  {closing.isLocked ? (
                    <Lock className="w-5 h-5 text-green-600 mx-auto" />
                  ) : (
                    <Lock className="w-5 h-5 text-gray-300 mx-auto" />
                  )}
                </td>
                <td className="px-6 py-4 text-center">
                  <span className={`px-2 py-1 text-xs font-medium rounded-full border ${getStatusColor(closing.status)}`}>
                    {closing.status.replace('_', ' ')}
                  </span>
                </td>
                <td className="px-6 py-4 text-center">
                  <button
                    onClick={() => {
                      setSelectedClosing(closing);
                      setShowChecklist(true);
                    }}
                    className="text-blue-600 hover:text-blue-800 mr-2"
                  >
                    Checklist
                  </button>
                  {closing.status === 'in_progress' && (
                    <button className="text-green-600 hover:text-green-800">Complete</button>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Checklist Modal */}
      {showChecklist && selectedClosing && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg">
            <div className="p-6 border-b border-gray-100">
              <h2 className="text-xl font-bold text-gray-900">Closing Checklist</h2>
              <p className="text-gray-500">{selectedClosing.code} - {selectedClosing.period}</p>
            </div>
            <div className="p-6 space-y-4">
              {selectedClosing.checklist.map((item, index) => (
                <div key={index} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                  <div className="flex items-center gap-3">
                    {item.completed ? (
                      <CheckCircle className="w-5 h-5 text-green-600" />
                    ) : (
                      <div className="w-5 h-5 border-2 border-gray-300 rounded-full" />
                    )}
                    <span className={item.completed ? 'text-gray-600 line-through' : 'text-gray-900'}>
                      {item.item}
                    </span>
                  </div>
                  {!selectedClosing.isLocked && (
                    <button className="text-sm text-blue-600 hover:text-blue-800">
                      Mark Done
                    </button>
                  )}
                </div>
              ))}
            </div>
            <div className="p-6 border-t border-gray-100 flex justify-end gap-3">
              <button
                onClick={() => setShowChecklist(false)}
                className="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
              >
                Close
              </button>
              {selectedClosing.status === 'in_progress' && (
                <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                  Complete Closing
                </button>
              )}
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default FinancialClosing;
