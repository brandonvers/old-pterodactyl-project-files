import http from '@/api/http';
import { BillingResponse } from '@/components/dashboard/billing/forms/UpdateBillingInfoForm';

export default async (): Promise<BillingResponse> => {
    const { data } = await http.get('/api/client/billing');
    return (data.data || []);
};
