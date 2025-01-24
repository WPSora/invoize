import { selectedBank, selectedPaypal, isXenditChecked, isWcPaymentChecked } from "$lib/stores/invoice-store";
import { xenditKey } from "$lib/stores/settings-store";
import { get } from "svelte/store";

export const checkIsPaymentValid = () => {
  const bank = get(selectedBank);
  const paypal = get(selectedPaypal);
  const xendit1 = get(isXenditChecked);
  const xendit2 = get(xenditKey);
  const wcPayment = get(isWcPaymentChecked);

  if (!(bank || paypal || (xendit1 && xendit2) || wcPayment)) return false;

  return true;
}