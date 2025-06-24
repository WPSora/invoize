<script>
  import { triggerInvoiceTabUpdate } from "$lib/helpers/triggerTabUpdateHelper";
  import { InvoiceListTab, PaymentStatus } from "$lib/common/enum";
  import { createPostRequest } from "$lib/helpers/request";
  import { prefix, startFromNumber } from "$lib/stores/settings-store";
  import {
    selectedBusiness,
    selectedClient,
    createdProductList,
    note,
    termsAndConditionsInvoice,
    internalNote,
    isCreatingNewInvoice,
    currencyPayload,
    selectedDueDateInterval,
    subtotal,
    total,
    totalDiscount,
    selectedDiscount,
    totalTax,
    selectedTax,
    selectedReminderAfter,
    selectedReminderBefore,
    billedTo,
    billedToSameAsClient,
    reminderForAdmin,
    reminderForClient,
  } from "$lib/stores/invoice-store";
  import { handleSubmitError } from "$lib/helpers/invoiceErrorHelper";
  import { checkIsPaymentValid } from "$lib/helpers/paymentChecker";
  import { setPayment } from "$lib/helpers/setPaymentHelper";
  import { isDebug } from "$lib/stores/settings-store";
  import TemplateCreateInvoice from "$lib/components/template/BaseCreateInvoice.svelte";
  import moment from "moment";
  import toast from "svelte-french-toast";

  const nav = [
    { name: "Invoice", link: "invoices" },
    { name: "Create", link: "invoices/create" },
  ];

  let selectedStatus;
  let selectedOrderDate;
  let selectedInvoiceDate;
  let selectedDueDate;
  let selectedDueDateCustom;
  let isSubmitting;
  let isSendMail = false;

  // update due date if user change invoice date
  $: {
    const days = $selectedDueDateInterval?.value;
    selectedDueDate = days ? moment(selectedInvoiceDate).add(days, "days").format("YYYY-MM-DD") : null;
  }

  // update value that need to be sent to the API
  const updateDueDate = (e) => {
    $selectedDueDateInterval = e.detail;
    const daysInterval = parseInt(e.detail.value);
    // 0 means custom interval
    if (daysInterval === 0) {
      // selectedDueDate is now handled by the input
      selectedDueDate = null;
    } else {
      // selectedDueDate is handled here if days is selected
      const days = daysInterval;
      selectedDueDate = moment(selectedInvoiceDate).add(days, "days").format("YYYY-MM-DD");
      selectedDueDateCustom = null;
    }
  };

  const updateSettingStore = () => {
    $startFromNumber++;
  };

  const hasRequiredFields = () => {
    // if any of these true, means the field is missing
    let missingFields = [];

    if (!$selectedBusiness) {
      missingFields.push("Issuer");
    }
    if (!$selectedClient) {
      missingFields.push("Customer");
    }
    if (!selectedStatus) {
      missingFields.push("Status");
    }
    if (!selectedOrderDate) {
      missingFields.push("Order date");
    }
    if (!selectedInvoiceDate) {
      missingFields.push("Invoice date");
    }
    if (!(selectedDueDate || selectedDueDateCustom)) {
      missingFields.push("Due date");
    }
    if ($createdProductList.length === 0) {
      missingFields.push("Product");
    }
    if (!checkIsPaymentValid()) {
      missingFields.push("Payment method");
    }
    if (!$currencyPayload || $currencyPayload.name === "none") {
      missingFields.push("Currency");
    }

    if (!$billedToSameAsClient && !$billedTo.name) {
      missingFields.push("Bill to 'name'");
    }

    if (missingFields.length > 0) {
      toast.error("Please fill missing fields: " + missingFields.join(", ") + ".");
      return false;
    }

    return true;
  };

  const setInvoicePayload = () => {
    return {
      id: $prefix.toString() + $startFromNumber.toString(),
      prefix: $prefix,
      number: $startFromNumber,
      business: $selectedBusiness,
      client: $selectedClient,
      status: selectedStatus.value,
      orderDate: selectedOrderDate,
      invoiceDate: selectedInvoiceDate,
      dueDate: selectedDueDate ?? selectedDueDateCustom,
      products: $createdProductList,
      payments: setPayment(),
      note: $note,
      terms: $termsAndConditionsInvoice,
      internalNote: $internalNote,
      currency: $currencyPayload,
      subtotal: $subtotal,
      total: $total,
      billedTo: $billedTo,
      billedToSameAsClient: $billedToSameAsClient,
      discount: {
        total: $totalDiscount,
        data: $selectedDiscount,
      },
      tax: {
        total: $totalTax,
        data: $selectedTax,
      },
      reminder: {
        before: $selectedReminderBefore,
        after: $selectedReminderAfter,
        forClient: $reminderForClient,
        forAdmin: $reminderForAdmin,
      },
    };
  };

  const handleSubmit = () => {
    if (!hasRequiredFields()) {
      return;
    }
    // to update the invoice list page
    $isCreatingNewInvoice = true;
    const payload = setInvoicePayload();
    $isDebug && console.log(payload);
    submit(payload);
  };

  const submit = (payload) => {
    if (isSubmitting) return;
    isSubmitting = true;
    toast.loading("Saving invoice...");

    createPostRequest(`invoice/create?email=${isSendMail}`, payload, (res) => {
      $isDebug && console.log(res);
      toast.dismiss();
      toast.success("Invoice created successfully", { duration: 5000 });
      if (isSendMail && res.data.emailSent) {
        toast.success("Invoice sent to email successfully", { duration: 5000 });
      } else if (isSendMail && !res.data.emailSent) {
        toast.error("Failed to send email", { duration: 5000 });
      }
      res.data.receiptCreated && toast.success("Receipt created successfully", { duration: 5000 });
      triggerInvoiceTabUpdate(selectedStatus.value, InvoiceListTab.ALL.LOWER_CASE);
      updateSettingStore();
      isSubmitting = false;
      window.location.replace(`#/invoice/${res.data.token}`);
    }).catch((err) => {
      isSubmitting = false;
      handleSubmitError(err, "invoice");
    });
  };

  const createAndSend = () => {
    isSendMail = true;
    handleSubmit();
  };
</script>

<TemplateCreateInvoice
  {nav}
  {isSubmitting}
  bind:selectedStatus
  bind:selectedOrderDate
  bind:selectedInvoiceDate
  bind:selectedDueDate
  bind:selectedDueDateCustom
  on:submit="{handleSubmit}"
  on:createAndSend="{createAndSend}"
  on:updateDueDate="{updateDueDate}" />
