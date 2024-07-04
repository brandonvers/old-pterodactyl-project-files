import http from '@/api/http';
import { CategoriesResponse } from '@/components/dashboard/knowledgebase/KnowledgebaseContainer';

export default async (): Promise<CategoriesResponse> => {
    const { data } = await http.get('/api/client/knowledgebase');
    return (data.data || []);
};
