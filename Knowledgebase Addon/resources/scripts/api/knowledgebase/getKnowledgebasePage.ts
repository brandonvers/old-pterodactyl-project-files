import http from '@/api/http';
import { KnowledgebasePageResponse } from '@/components/dashboard/knowledgebase/KnowledgebasePage';

export default async (id: string): Promise<KnowledgebasePageResponse> => {
    const { data } = await http.get(`/api/client/knowledgebase/page/${id}`);
    return (data.data || []);
};
