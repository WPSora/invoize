import { get } from "svelte/store";
import { xenditKey, xenditPrimaryCurrency, xenditToken } from "$lib/stores/settings-store";
import {
  isBankPaymentChecked,
  selectedBank as selectedBankStore,
  isPaypalChecked,
  selectedPaypal as selectedPaypalStore,
  isXenditChecked,
  totalXendit,
  isWcPaymentChecked,
  wcPayment
} from "$lib/stores/invoice-store";

export const setPayment = () => {
  let payments = [];

  // Bank payment
  if (get(isBankPaymentChecked) && get(selectedBankStore)) {
    const bank = get(selectedBankStore);
    // update from {name: ...,  symbol: ...} to just take the currency name
    const newData = {
      id: bank.value.id,
      currency: bank.value.currency.name,
      detail: bank.value.detail,
      name: bank.value.name,
      type: bank.value.type
    };
    payments.push({ ...newData, method: "bank" });
  }

  // Paypal payment
  if (get(isPaypalChecked) && get(selectedPaypalStore)) {
    payments.push({ ...get(selectedPaypalStore), method: "paypal" });
  }

  // Xendit payment
  if (get(isXenditChecked) && get(xenditKey) && get(xenditToken)) {
    payments.push({ name: "xendit", method: "xendit", total: get(totalXendit), currency: get(xenditPrimaryCurrency) });
  }

  // Woocommerce payment
  if (get(isWcPaymentChecked)) {
    payments.push(get(wcPayment));
  }

  return payments;
};