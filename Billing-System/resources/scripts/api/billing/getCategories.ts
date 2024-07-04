import http from '@/api/http';
import { CategoriesResponse } from '@/components/dashboard/billing/CategoriesContainer';

export default async (): Promise<CategoriesResponse> => {
    const { data } = await http.get('/api/client/billing/categories');
    return (data.data || []);
};
