import http from '@/api/http';
import { CheckoutResponse } from '@/components/dashboard/billing/CheckoutContainer';

export default async (): Promise<CheckoutResponse> => {
    const { data } = await http.get('/api/client/billing/store/checkout');
    return (data.data || []);
};
