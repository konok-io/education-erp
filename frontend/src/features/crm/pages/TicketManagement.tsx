/**
 * Phase 035 - Enterprise CRM System
 * Ticket Management Page
 */

import { useState, useEffect } from 'react';
import { getTickets, updateTicketStatus, assignTicket } from '../services/crmApi';
import type { CrmTicket, TicketStatus, Priority } from '../types';
import { TICKET_CATEGORIES, TICKET_STATUSES, PRIORITIES } from '../types';

export function TicketManagement() {
  const [tickets, setTickets] = useState<CrmTicket[]>([]);
  const [loading, setLoading] = useState(true);
  const [selectedStatus, setSelectedStatus] = useState<TicketStatus | ''>('');
  const [selectedPriority, setSelectedPriority] = useState<Priority | ''>('');

  useEffect(() => {
    fetchTickets();
  }, [selectedStatus, selectedPriority]);

  const fetchTickets = async () => {
    try {
      setLoading(true);
      const params: any = {};
      if (selectedStatus) params.status = selectedStatus;
      if (selectedPriority) params.priority = selectedPriority;
      
      const response = await getTickets({ ...params, per_page: 50 });
      setTickets(response.data);
    } catch (error) {
      console.error('Failed to fetch tickets:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleStatusChange = async (uuid: string, status: TicketStatus) => {
    try {
      await updateTicketStatus(uuid, status);
      fetchTickets();
    } catch (error) {
      console.error('Failed to update ticket status:', error);
    }
  };

  const getPriorityColor = (priority: Priority) => {
    if (priority === 'critical' || priority === 'urgent') return 'bg-red-100 text-red-800';
    if (priority === 'high') return 'bg-orange-100 text-orange-800';
    if (priority === 'medium') return 'bg-yellow-100 text-yellow-800';
    return 'bg-gray-100 text-gray-800';
  };

  const getStatusColor = (status: TicketStatus) => {
    switch (status) {
      case 'open':
        return 'bg-blue-100 text-blue-800';
      case 'assigned':
        return 'bg-purple-100 text-purple-800';
      case 'in_progress':
        return 'bg-indigo-100 text-indigo-800';
      case 'waiting':
        return 'bg-yellow-100 text-yellow-800';
      case 'resolved':
        return 'bg-green-100 text-green-800';
      case 'closed':
        return 'bg-gray-100 text-gray-800';
      default:
        return 'bg-gray-100 text-gray-800';
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
        <h1 className="text-2xl font-bold text-gray-900">Helpdesk - Tickets</h1>
        <button className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
          Create Ticket
        </button>
      </div>

      {/* Filters */}
      <div className="flex gap-4">
        <select
          value={selectedStatus}
          onChange={(e) => setSelectedStatus(e.target.value as TicketStatus | '')}
          className="px-4 py-2 border border-gray-300 rounded-lg"
        >
          <option value="">All Statuses</option>
          {(Object.keys(TICKET_STATUSES) as TicketStatus[]).map((status) => (
            <option key={status} value={status}>
              {TICKET_STATUSES[status]}
            </option>
          ))}
        </select>

        <select
          value={selectedPriority}
          onChange={(e) => setSelectedPriority(e.target.value as Priority | '')}
          className="px-4 py-2 border border-gray-300 rounded-lg"
        >
          <option value="">All Priorities</option>
          {(Object.keys(PRIORITIES) as Priority[]).map((priority) => (
            <option key={priority} value={priority}>
              {PRIORITIES[priority]}
            </option>
          ))}
        </select>
      </div>

      {/* Tickets List */}
      <div className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ticket No</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assignee</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {tickets.map((ticket) => (
              <tr key={ticket.id}>
                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {ticket.ticket_no}
                </td>
                <td className="px-6 py-4 text-sm text-gray-500">
                  <div>
                    <p className="font-medium text-gray-900">{ticket.subject}</p>
                    <p className="text-xs text-gray-400 line-clamp-1">
                      {ticket.description}
                    </p>
                  </div>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {TICKET_CATEGORIES[ticket.category]}
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <span className={`px-2 py-1 text-xs rounded-full ${getPriorityColor(ticket.priority)}`}>
                    {PRIORITIES[ticket.priority]}
                  </span>
                </td>
                <td className="px-6 py-4 whitespace-nowrap">
                  <select
                    value={ticket.status}
                    onChange={(e) => handleStatusChange(ticket.uuid, e.target.value as TicketStatus)}
                    className={`px-2 py-1 text-xs rounded-full border-0 ${getStatusColor(ticket.status)}`}
                  >
                    {(Object.keys(TICKET_STATUSES) as TicketStatus[]).map((status) => (
                      <option key={status} value={status}>
                        {TICKET_STATUSES[status]}
                      </option>
                    ))}
                  </select>
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {ticket.assignee?.name || 'Unassigned'}
                </td>
                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <div className="flex gap-2">
                    <button className="text-blue-600 hover:text-blue-800">View</button>
                    {!ticket.assigned_to && (
                      <button className="text-green-600 hover:text-green-800">Assign</button>
                    )}
                  </div>
                </td>
              </tr>
            ))}
            {tickets.length === 0 && (
              <tr>
                <td colSpan={7} className="px-6 py-8 text-center text-gray-500">
                  No tickets found.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
