import http from '@/api/http';
import { ProductsResponse } from '@/components/dashboard/billing/ProductsContainer';

export default async (): Promise<ProductsResponse> => {
    const { data } = await http.get('/api/client/billing/products');
    return (data.data || []);
};
