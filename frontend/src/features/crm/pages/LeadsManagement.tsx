/**
 * Phase 035 - Enterprise CRM System
 * Lead Management Page
 */

import { useState, useEffect } from 'react';
import { getLeads, updateLeadStage, assignLeadCounselor, getLeadPipeline } from '../services/crmApi';
import type { CrmLead, PipelineStage } from '../types';
import { PIPELINE_STAGES, LEAD_SOURCES, PRIORITIES } from '../types';

export function LeadsManagement() {
  const [leads, setLeads] = useState<CrmLead[]>([]);
  const [pipeline, setPipeline] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<'list' | 'pipeline'>('pipeline');
  const [selectedStage, setSelectedStage] = useState<PipelineStage | ''>('');

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      setLoading(true);
      const [leadsData, pipelineData] = await Promise.all([
        getLeads({ per_page: 100 }),
        getLeadPipeline(),
      ]);
      setLeads(leadsData.data);
      setPipeline(pipelineData);
    } catch (error) {
      console.error('Failed to fetch leads:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleStageChange = async (uuid: string, stage: PipelineStage) => {
    try {
      await updateLeadStage(uuid, stage);
      fetchData();
    } catch (error) {
      console.error('Failed to update lead stage:', error);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-gray-900">Lead Management</h1>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Create Lead
        </button>
      </div>

      {/* Tabs */}
      <div className="border-b border-gray-200">
        <nav className="-mb-px flex space-x-8">
          <button
            onClick={() => setActiveTab('pipeline')}
            className={`pb-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'pipeline'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            Pipeline View
          </button>
          <button
            onClick={() => setActiveTab('list')}
            className={`pb-4 px-1 border-b-2 font-medium text-sm ${
              activeTab === 'list'
                ? 'border-blue-500 text-blue-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
            }`}
          >
            List View
          </button>
        </nav>
      </div>

      {/* Pipeline View */}
      {activeTab === 'pipeline' && (
        <div className="overflow-x-auto">
          <div className="flex gap-4 min-w-max pb-4">
            {(Object.keys(PIPELINE_STAGES) as PipelineStage[]).map((stage) => {
              const stageLeads = leads.filter((l) => l.pipeline_stage === stage);
              const pipelineData = pipeline.find((p: any) => p[stage]);

              return (
                <div key={stage} className="w-80 flex-shrink-0">
                  <div className="bg-gray-100 rounded-t-lg p-3">
                    <div className="flex items-center justify-between">
                      <h3 className="font-semibold text-gray-900">
                        {PIPELINE_STAGES[stage]}
                      </h3>
                      <span className="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                        {stageLeads.length}
                      </span>
                    </div>
                  </div>
                  <div className="bg-gray-50 rounded-b-lg p-3 min-h-[400px] space-y-3">
                    {stageLeads.map((lead) => (
                      <div
                        key={lead.id}
                        className="bg-white rounded-lg shadow-sm p-4 border border-gray-200"
                      >
                        <div className="flex items-start justify-between mb-2">
                          <div>
                            <h4 className="font-medium text-gray-900">{lead.full_name}</h4>
                            <p className="text-sm text-gray-500">{lead.mobile || lead.email}</p>
                          </div>
                          <span className={`px-2 py-1 text-xs rounded-full ${
                            lead.priority === 'urgent' || lead.priority === 'critical'
                              ? 'bg-red-100 text-red-800'
                              : lead.priority === 'high'
                              ? 'bg-orange-100 text-orange-800'
                              : 'bg-gray-100 text-gray-800'
                          }`}>
                            {PRIORITIES[lead.priority]}
                          </span>
                        </div>
                        <div className="flex items-center justify-between text-sm text-gray-500">
                          <span>{LEAD_SOURCES[lead.lead_source]}</span>
                          <span>Score: {lead.lead_score}</span>
                        </div>
                        {lead.assigned_counselor && (
                          <p className="text-xs text-gray-500 mt-2">
                            Counselor: {lead.assigned_counselor.name}
                          </p>
                        )}
                      </div>
                    ))}
                    {stageLeads.length === 0 && (
                      <div className="text-center text-gray-400 py-8">
                        No leads in this stage
                      </div>
                    )}
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      )}

      {/* List View */}
      {activeTab === 'list' && (
        <div className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lead No</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stage</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {leads.map((lead) => (
                <tr key={lead.id}>
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {lead.lead_no}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div>
                      <p>{lead.full_name}</p>
                      <p className="text-xs text-gray-400">{lead.mobile || lead.email}</p>
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {LEAD_SOURCES[lead.lead_source]}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className={`px-2 py-1 text-xs rounded-full ${
                      lead.priority === 'urgent' || lead.priority === 'critical'
                        ? 'bg-red-100 text-red-800'
                        : lead.priority === 'high'
                        ? 'bg-orange-100 text-orange-800'
                        : 'bg-gray-100 text-gray-800'
                    }`}>
                      {PRIORITIES[lead.priority]}
                    </span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <select
                      value={lead.pipeline_stage}
                      onChange={(e) => handleStageChange(lead.uuid, e.target.value as PipelineStage)}
                      className="text-sm border-gray-300 rounded-md"
                    >
                      {(Object.keys(PIPELINE_STAGES) as PipelineStage[]).map((stage) => (
                        <option key={stage} value={stage}>
                          {PIPELINE_STAGES[stage]}
                        </option>
                      ))}
                    </select>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {lead.lead_score}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <button className="text-blue-600 hover:text-blue-800">View</button>
                  </td>
                </tr>
              ))}
              {leads.length === 0 && (
                <tr>
                  <td colSpan={7} className="px-6 py-8 text-center text-gray-500">
                    No leads found. Create your first lead.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
