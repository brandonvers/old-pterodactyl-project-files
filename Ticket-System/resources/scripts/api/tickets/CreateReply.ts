import http from '@/api/http';

export default (id: string, message: string): Promise<any> => {
    return new Promise((resolve, reject) => {
        http.post(`/api/client/tickets/reply`, {
            id, message,
        }).then((data) => {
            resolve(data.data || []);
        }).catch(reject);
    });
};
