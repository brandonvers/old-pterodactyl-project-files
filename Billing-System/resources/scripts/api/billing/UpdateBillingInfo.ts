import http from '@/api/http';

export default (first_name: string, last_name: string, address: string, city: string, country: string, zip: string): Promise<any> => {
    return new Promise((resolve, reject) => {
        http.post(`/api/client/billing/update`, {
            first_name, last_name, address, city, country, zip,
        }).then((data) => {
            resolve(data.data || []);
        }).catch(reject);
    });
};
