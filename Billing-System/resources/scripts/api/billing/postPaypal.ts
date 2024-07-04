import http from '@/api/http';

export default (data: any[], order: any[]): Promise<any> => {
    return new Promise((resolve, reject) => {
        http.post(`/api/client/billing/paypal`, {
            data, order,
        }).then((data) => {
            resolve(data.data || []);
        }).catch(reject);
    });
};
