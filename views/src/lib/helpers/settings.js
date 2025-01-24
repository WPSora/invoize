import { createGetRequest } from "./request";
import { saveInvoiceTabToSettingsStore } from "./saveToStoreHelper";

/**
 * Load Invoice Setting and save it to the store
 */
export const loadInvoiceSetting = async () => {
    const res = await createGetRequest(`settings/retrieve?tab=invoice`);
    saveInvoiceTabToSettingsStore(res.data.data);
};