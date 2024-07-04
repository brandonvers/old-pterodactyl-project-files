import http from '@/api/http';
import { StoreResponse } from '@/components/dashboard/billing/StoreContainer';

export default async (): Promise<StoreResponse> => {
    const { data } = await http.get('/api/client/billing');
    return (data.data || []);
};
