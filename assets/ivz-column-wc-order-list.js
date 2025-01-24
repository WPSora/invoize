function createInvoiceNow(attr) {
  const orderId = attr.getAttribute("data-order-id");
  const api = attr.getAttribute("data-api").trim();
  const baseUrl = attr.getAttribute("data-base-url").trim();

  // update button to loading
  const createButton = document.querySelector(
    `.create-invoize-button-${orderId}`
  );

  const loading = document.createElement("p");
  loading.setAttribute("style", "font-weight: 400");
  loading.setAttribute("class", "invoize-loading-create");
  loading.innerHTML = "Creating invoice...";
  createButton.parentNode.replaceChild(loading, createButton);

  fetch(`${api}/invoice/create-from-wc?orderId=${orderId}`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-WP-Nonce": invoize.wp_rest_nonce,
    },
  })
    .then((res) => {
      return res.json();
    })
    .then((res) => {
      if (res.message === "success") {
        // update button to be link of the invoice
        const loading = document.querySelector(".invoize-loading-create");
        const invoiceLink = document.createElement("a");
        invoiceLink.innerHTML = res.id;
        invoiceLink.setAttribute("class", `invoize-invoice-link-${res.id}`);
        invoiceLink.setAttribute("href", `${baseUrl}${res.token}`);
        invoiceLink.setAttribute("style", "font-weight: 600; padding: 20px;");
        loading.parentNode.replaceChild(invoiceLink, loading);
      } else {
        const loading = document.querySelector(".invoize-loading-create");
        console.error(res);
        alert(`Failed to Create Invoice. Error message: ${res.message}`);
        loading.parentNode.replaceChild(createButton, loading);
      }
    })
    .catch((err) => {
      console.error(err.message);
    });
}
