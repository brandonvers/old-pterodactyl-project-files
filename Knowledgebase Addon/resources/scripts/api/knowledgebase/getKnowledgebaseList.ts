import http from '@/api/http';
import { KnowledgebaseListResponse } from '@/components/dashboard/knowledgebase/KnowledgebaseList';

export default async (id: string): Promise<KnowledgebaseListResponse> => {
    const { data } = await http.get(`/api/client/knowledgebase/list/${id}`);
    return (data.data || []);
};
