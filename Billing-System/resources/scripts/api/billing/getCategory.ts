import http from '@/api/http';
import { CategoryResponse } from '@/components/dashboard/billing/CategoryContainer';

export default async (id: string): Promise<CategoryResponse> => {
    const { data } = await http.get(`/api/client/billing/category/${id}`);
    return (data.data || []);
};
