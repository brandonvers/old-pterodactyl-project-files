import http from '@/api/http';

export default (product_id: string): Promise<any> => {
    return new Promise((resolve, reject) => {
        http.post(`/api/client/billing/store/product/add/${product_id}`, {
            product_id,
        }).then((data) => {
            resolve(data.data || []);
        }).catch(reject);
    });
};
