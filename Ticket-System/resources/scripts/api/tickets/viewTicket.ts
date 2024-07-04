import http from '@/api/http';
import { ViewTicketResponse } from '@/components/dashboard/tickets/ViewTicket';

export default async (id: string): Promise<ViewTicketResponse> => {
    const { data } = await http.get(`/api/client/tickets/${id}`);
    return (data.data || []);
};
