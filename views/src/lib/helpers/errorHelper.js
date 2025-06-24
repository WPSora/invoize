import toast from "svelte-french-toast";

export const handleError = (err, toastMessage = null, showToast = true, useErrorMessageAsToast = false) => {
  toast.dismiss();
  // error from client
  if (!err.message?.response?.data) {
    console.error(err);
    return;
  }

  const message = err.message.response?.data?.message ?? err.message.response?.data?.data;

  // error from server
  console.error({
    code: err.message.response?.status,
    message: message,
  });

  if (useErrorMessageAsToast) {
    showToast && toast.error(message);
  } else {
    showToast && toastMessage && toast.error(toastMessage);
  }

  return toastMessage;
}