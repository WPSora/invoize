import axios, { AxiosError, type AxiosInstance } from "axios";

const requestInstance: AxiosInstance = axios.create({
  baseURL: invoize.api_url,
  headers: {
    "X-WP-Nonce": invoize.nonce,
  },
});

const createPostRequest = async (
  url: string,
  data: any,
  callback: ((request: any) => void) | null = null
): Promise<any> => {
  try {
    const request = await requestInstance
      .post(url, data, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });
    if (callback) {
      callback(request);
    }
    return request;
  } catch (error) {
    throw new AxiosError(error);
  }
};

const createGetRequest = async (
  url: string,
  callback: ((request: any) => void) | null = null,
  params: any = {}
): Promise<any> => {
  try {
    const request = await requestInstance.get(url,  params );

    if (callback) {
      callback(request);
    }

    return request;
  } catch (error) {
    throw new AxiosError(error);
  }
};

export { createPostRequest, createGetRequest, requestInstance };
