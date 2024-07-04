import http from '@/api/http';

export default (product_id: number): Promise<void> => {
    return new Promise((resolve, reject) => {
        http.delete(`/api/client/billing/store/product/delete/${product_id}`)
            .then(() => resolve())
            .catch(reject);
    });
};
