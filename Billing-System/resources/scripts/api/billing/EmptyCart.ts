import http from '@/api/http';

export default (): Promise<void> => {
    return new Promise((resolve, reject) => {
        http.delete(`/api/client/billing/store/empty/cart`)
            .then(() => resolve())
            .catch(reject);
    });
};
