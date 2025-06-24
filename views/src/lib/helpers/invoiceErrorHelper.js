import toast from "svelte-french-toast";
import {
  XENDIT_UNSUPPORTED_CURRENCY,
  XENDIT_MISSING_CURRENCY_SETTING,
  INVALID_PAYPAL_KEY,
  PAYPAL_UNSUPPORTED_CURRENCY,
  MISSING_PAYPAL_SETTING,
} from "$lib/common/error-constant";

export const handleSubmitError = (err, message) => {
  toast.dismiss();
  const errMessage = err.message?.response?.data?.message;

  console.error({
    code: err.message?.response?.status,
    message: errMessage,
  });

  if (errMessage === PAYPAL_UNSUPPORTED_CURRENCY) {
    toast.error("Chosen currency is not supported by Paypal. Please change your currency.", { duration: 10000 });
    return;
  }

  if (errMessage === INVALID_PAYPAL_KEY) {
    toast.error("Invalid Paypal key. Please check your Paypal key in the settings.", { duration: 10000 });
    return;
  }

  if (errMessage === MISSING_PAYPAL_SETTING) {
    toast.error("Missing paypal settings. Please check your paypal account in settings.");
    return;
  }

  if (errMessage === XENDIT_UNSUPPORTED_CURRENCY) {
    toast.error(
      "Chosen Xendit primary currency is not supported by your Xendit account. Please change the Xendit primary currency in the settings page.",
      { duration: 10000 },
    );
    return;
  }

  if (errMessage === XENDIT_MISSING_CURRENCY_SETTING) {
    toast.error('Missing Xendit primary currency in setting');
    return;
  }

  toast.error(`Failed to create ${message}. Please check error log for more info.`);
}