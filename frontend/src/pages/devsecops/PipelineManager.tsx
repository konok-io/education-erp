import React, { useState, useEffect } from 'react';
import { devsecopsApi, Pipeline, PipelineRun } from '../../api/devsecops';
import { 
  Play, 
  Plus, 
  CheckCircle, 
  XCircle, 
  Clock,
  Loader,
  MoreVertical
} from 'lucide-react';

export const PipelineManager: React.FC = () => {
  const [pipelines, setPipelines] = useState<Pipeline[]>([]);
  const [loading, setLoading] = useState(true);
  const [selectedPipeline, setSelectedPipeline] = useState<Pipeline | null>(null);
  const [runs, setRuns] = useState<PipelineRun[]>([]);

  useEffect(() => {
    loadPipelines();
  }, []);

  const loadPipelines = async () => {
    try {
      setLoading(true);
      const response = await devsecopsApi.getPipelines();
      setPipelines(response.data.data);
    } catch (err) {
      console.error('Failed to load pipelines:', err);
    } finally {
      setLoading(false);
    }
  };

  const loadRuns = async (pipelineId: string) => {
    try {
      const response = await devsecopsApi.getPipelineRuns(pipelineId);
      setRuns(response.data.data);
    } catch (err) {
      console.error('Failed to load runs:', err);
    }
  };

  const handleTrigger = async (pipelineId: string) => {
    try {
      await devsecopsApi.triggerPipeline(pipelineId);
      loadRuns(pipelineId);
    } catch (err) {
      console.error('Failed to trigger pipeline:', err);
    }
  };

  const handleSelectPipeline = (pipeline: Pipeline) => {
    setSelectedPipeline(pipeline);
    loadRuns(pipeline.id);
  };

  const getStatusIcon = (status: PipelineRun['status']) => {
    switch (status) {
      case 'success':
        return <CheckCircle className="w-5 h-5 text-green-500" />;
      case 'failed':
        return <XCircle className="w-5 h-5 text-red-500" />;
      case 'running':
        return <Loader className="w-5 h-5 text-blue-500 animate-spin" />;
      case 'pending':
        return <Clock className="w-5 h-5 text-gray-400" />;
      default:
        return <Clock className="w-5 h-5 text-gray-400" />;
    }
  };

  const getProviderColor = (provider: Pipeline['provider']) => {
    switch (provider) {
      case 'github':
        return 'bg-gray-900 text-white';
      case 'gitlab':
        return 'bg-orange-500 text-white';
      case 'jenkins':
        return 'bg-black text-white';
      case 'azure':
        return 'bg-blue-600 text-white';
      default:
        return 'bg-gray-500 text-white';
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
        <h1 className="text-2xl font-bold text-gray-900">Pipeline Manager</h1>
        <button className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          <Plus className="w-5 h-5" />
          Create Pipeline
        </button>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-1 space-y-4">
          {pipelines.map((pipeline) => (
            <div
              key={pipeline.id}
              onClick={() => handleSelectPipeline(pipeline)}
              className={`p-4 bg-white rounded-lg shadow cursor-pointer transition-all ${
                selectedPipeline?.id === pipeline.id
                  ? 'ring-2 ring-blue-500'
                  : 'hover:shadow-md'
              }`}
            >
              <div className="flex items-start justify-between">
                <div>
                  <h3 className="font-semibold text-gray-900">{pipeline.name}</h3>
                  <div className="flex items-center gap-2 mt-1">
                    <span className={`px-2 py-0.5 text-xs font-medium rounded ${getProviderColor(pipeline.provider)}`}>
                      {pipeline.provider}
                    </span>
                    <span className="text-xs text-gray-500">
                      {pipeline.type.toUpperCase()}
                    </span>
                  </div>
                </div>
                <div className="flex items-center gap-1">
                  {pipeline.is_active ? (
                    <span className="px-2 py-0.5 text-xs font-medium rounded bg-green-100 text-green-700">
                      Active
                    </span>
                  ) : (
                    <span className="px-2 py-0.5 text-xs font-medium rounded bg-gray-100 text-gray-600">
                      Inactive
                    </span>
                  )}
                </div>
              </div>
              <div className="mt-2 text-sm text-gray-500">
                {pipeline.repository || 'No repository'} / {pipeline.branch}
              </div>
            </div>
          ))}
        </div>

        <div className="lg:col-span-2">
          {selectedPipeline ? (
            <div className="bg-white rounded-lg shadow p-6">
              <div className="flex items-center justify-between mb-6">
                <div>
                  <h2 className="text-xl font-bold text-gray-900">{selectedPipeline.name}</h2>
                  <p className="text-gray-500">{selectedPipeline.description}</p>
                </div>
                <div className="flex items-center gap-2">
                  <button
                    onClick={() => handleTrigger(selectedPipeline.id)}
                    className="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                  >
                    <Play className="w-4 h-4" />
                    Trigger
                  </button>
                  <button className="p-2 hover:bg-gray-100 rounded-lg">
                    <MoreVertical className="w-5 h-5" />
                  </button>
                </div>
              </div>
              <div>
                <h3 className="text-lg font-semibold mb-4">Recent Runs</h3>
                <div className="space-y-3">
                  {runs.map((run) => (
                    <div key={run.id} className="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                      {getStatusIcon(run.status)}
                      <div className="flex-1">
                        <div className="flex items-center gap-2">
                          <span className="font-medium">#{run.run_number}</span>
                          <span className="text-sm text-gray-500">
                            {run.branch || selectedPipeline.branch}
                          </span>
                        </div>
                        <div className="text-sm text-gray-500">
                          {run.author && `by ${run.author}`}
                          {run.commit_sha && ` • ${run.commit_sha.slice(0, 7)}`}
                        </div>
                      </div>
                      <div className="text-sm text-gray-500">
                        {run.duration && `${Math.floor(run.duration / 60)}m ${run.duration % 60}s`}
                      </div>
                    </div>
                  ))}
                  {runs.length === 0 && (
                    <div className="text-center text-gray-500 py-8">
                      No runs yet
                    </div>
                  )}
                </div>
              </div>
            </div>
          ) : (
            <div className="bg-gray-50 rounded-lg p-8 text-center text-gray-500">
              Select a pipeline to view details
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default PipelineManager;
