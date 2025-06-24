// @ts-nocheck

import "./app.postcss";

const searchParams = new URLSearchParams(window.location.search);
const isWelcomePage =
  searchParams.get("page")?.toLowerCase() == "invoize-welcome";
const previewUrl = invoize.base_url + "/invoize-preview";
const quotationPreviewUrl = invoize.base_url + '/invoize-quotation';
const xenditPaymentSuccessUrl = invoize.base_url + "/xendit-payment-success";
const xenditPaymentFailedUrl = invoize.base_url + "/xendit-payment-failed";
const url = window.location.href;

let app;

// Invoize Payment Page
if (url.includes(invoize.base_url + "/invoize/payment")) {
  import("./PaymentPage.svelte").then((module) => {
    const PaymentPage = module.default;
    app = new PaymentPage({
      target: document.getElementById("invoize-payment"),
    });
  });
} else if (url.includes(quotationPreviewUrl)) {
  import("./routes/quotation/PreviewQuotation.svelte").then((module) => {
    const PreviewPage = module.default;
    app = new PreviewPage({
      target: document.getElementById("invoize-quotation"),
    });
  })
} else if (isWelcomePage) {
  // Welcome page
  import("./WelcomePage.svelte").then((module) => {
    const WelcomePage = module.default;
    app = new WelcomePage({ target: document.getElementById("invoize-app") });
  });
} else if (url.includes(previewUrl)) {
  // Preview page
  import("./routes/invoice/preview/Preview.svelte").then((module) => {
    const PreviewPage = module.default;
    app = new PreviewPage({
      target: document.getElementById("invoize-preview"),
    });
  });
} else if (url.includes(xenditPaymentSuccessUrl)) {
  // Payment Success page
  import("./routes/misc/PaymentSuccess.svelte").then((module) => {
    const PaymentSuccessPage = module.default;
    app = new PaymentSuccessPage({
      target: document.getElementById("payment-confirmation"),
    });
  });
} else if (url.includes(xenditPaymentFailedUrl)) {
  // Payment Failed page
  import("./routes/misc/PaymentFailed.svelte").then((module) => {
    const PaymentFailedPage = module.default;
    app = new PaymentFailedPage({
      target: document.getElementById("payment-confirmation"),
    });
  });
} else {
  // App page
  import("./App.svelte").then((module) => {
    const App = module.default;
    app = new App({ target: document.getElementById("invoize-app") });
  });
}

export default app;
