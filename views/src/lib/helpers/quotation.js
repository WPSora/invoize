import { createGetRequest, createPostRequest } from "$lib/helpers/request";

export const quotation = {
    convertToInvoice: async(token) => {
        const response = await createPostRequest(`quotation/to-invoice`, {
            token: token,
        });
        return response.data;
    },
    detail: async(token) => {
        const response = await createGetRequest(`quotation/detail?token=${token}`);
        return response.data
    },
    archive: async(token) => {
        const response = await createPostRequest(`quotation/archive`, {
            token: token
        });
        return response.data
    },
    send: async(token) => {
        const response = await createPostRequest(`quotation/send-mail`, {
            token: token
        });
        return response.data
    },
    
}