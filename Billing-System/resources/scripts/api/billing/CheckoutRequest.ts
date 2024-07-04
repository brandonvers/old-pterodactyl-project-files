import http from '@/api/http';

export default (): Promise<void> => {
    return new Promise((resolve, reject) => {
        http.post(`/api/client/billing/buy`)
            .then(() => resolve())
            .catch(reject);
    });
};
