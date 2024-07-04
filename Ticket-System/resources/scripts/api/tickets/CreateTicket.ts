import http from '@/api/http';

export default (title: string, category: string, priority: string, message: string): Promise<any> => {
    return new Promise((resolve, reject) => {
        http.post('/api/client/tickets/create', {
            title, category, priority, message,
        }).then((data) => {
            resolve(data.data || []);
        }).catch(reject);
    });
};
