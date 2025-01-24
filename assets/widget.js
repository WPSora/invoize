let invoiceId = 0;
let isUpdating = false;

const closeConfirmModal = () => {
  handleCloseConfirmModal();
  handleCloseModalWrapper();
};

const openConfirmModal = (data) => {
  invoiceId = data.getAttribute("data-invoice-id");
  handleOpenConfirmModal();
  const modalMessage = document.querySelector(
    ".invoize-confirm-paid-modal-message"
  );
  const name = data.getAttribute("data-invoice-name");
  modalMessage.textContent = `Are you sure you want to set invoice ${name} to PAID?`;
};

const updateInvoiceToPaid = () => {
  handleDisplayLoading();
  const api = invoize.api_url;
  const payload = {
    id: invoiceId,
    paymentStatus: "paid",
  };
  const isChecked = document.querySelector("#is-send-email").checked;

  fetch(`${api}/invoice/update?email=${isChecked}`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(payload),
  })
    .then((response) => {
      if (!response.ok) {
        handleFailedModal();
      }
      handleSuccessModal();
    })
    .catch((err) => {
      handleFailedModal();
    });
};

const handleCloseConfirmModal = () => {
  const modal = document.querySelector(".invoize-confirm-paid-modal");
  modal.style.display = "none";
};

const handleOpenConfirmModal = () => {
  const modal = document.querySelector(".invoize-confirm-paid-modal");
  modal.style.display = "flex";

  const modalWrapper = document.querySelector(
    ".invoize-confirm-paid-modal-wrapper"
  );
  modalWrapper.style.display = "flex";
};

const handleDisplayLoading = () => {
  const confirmModal = document.querySelector(".invoize-confirm-paid-modal");
  confirmModal.style.display = "none";

  const loadingModal = document.querySelector(".invoize-loading-modal");
  loadingModal.style.display = "flex";

  const loadingIcon = document.querySelector(".invoize-loading-icon");
  loadingIcon.style.display = "flex";

  const loadingMessage = document.querySelector(
    ".invoize-loading-modal-message"
  );
  loadingMessage.textContent = "Processing...";
};

const handleCloseLoadingModal = () => {
  const modal = document.querySelector(".invoize-loading-modal");
  modal.style.display = "none";

  const finishButton = document.querySelector(".invoize-confirm-finish-button");
  finishButton.style.display = "none";
  handleCloseModalWrapper();
  updatePaidStatus();
};

const handleCloseModalWrapper = () => {
  const modalWrapper = document.querySelector(
    ".invoize-confirm-paid-modal-wrapper"
  );
  modalWrapper.style.display = "none";
};

const handleSuccessModal = () => {
  const modal = document.querySelector(".invoize-loading-modal-message");
  modal.textContent = "Success";

  const finishButton = document.querySelector(".invoize-confirm-finish-button");
  finishButton.style.display = "flex";

  const loadingIcon = document.querySelector(".invoize-loading-icon");
  loadingIcon.style.display = "none";
};

const handleFailedModal = () => {
  const modal = document.querySelector(".invoize-loading-modal-message");
  modal.textContent = "Failed to update invoice status to paid.";

  const finishButton = document.querySelector(".invoize-confirm-finish-button");
  finishButton.style.display = "flex";

  const loadingIcon = document.querySelector(".invoize-loading-icon");
  loadingIcon.style.display = "none";
};

const updatePaidStatus = () => {
  const checkInput = document.querySelector("#is-send-email");
  checkInput.checked = false;
  const statusForUnpaid = document.querySelector(
    `.invoize-unpaid-set-to-paid-${invoiceId}`
  );
  const statusForExpired = document.querySelector(
    `.invoize-expired-set-to-paid-${invoiceId}`
  );
  const newDiv1 = document.createElement("div");
  const newDiv2 = document.createElement("div");
  newDiv1.setAttribute("class", "invoize-paid-status");
  newDiv2.setAttribute("class", "invoize-paid-status");
  newDiv1.innerHTML = "Success";
  newDiv2.innerHTML = "Success";

  if (statusForUnpaid) {
    statusForUnpaid.parentNode.replaceChild(newDiv1, statusForUnpaid);
  }
  if (statusForExpired) {
    statusForExpired.parentNode.replaceChild(newDiv2, statusForExpired);
  }
};
