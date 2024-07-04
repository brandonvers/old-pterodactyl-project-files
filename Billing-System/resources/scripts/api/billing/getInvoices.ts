import http from '@/api/http';
import { InvoicesResponse } from '@/components/dashboard/billing/InvoicesContainer';

export default async (): Promise<InvoicesResponse> => {
    const { data } = await http.get('/api/client/billing/invoices');
    return (data.data || []);
};
