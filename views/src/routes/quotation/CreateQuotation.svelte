<script>
  import { triggerInvoiceTabUpdate } from "$lib/helpers/triggerTabUpdateHelper";
  import { InvoiceListTab } from "$lib/common/enum";
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
  import FormQuotation from "$lib/components/template/BaseCreateQuotation.svelte";
  import moment from "moment";
  import toast from "svelte-french-toast";

  const nav = [
    { name: "Quotation", link: "quotations" },
    { name: "Create", link: "quotation/create" },
  ];

  let selectedQuotationDate;
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

    if (!selectedQuotationDate) {
      missingFields.push("Quotation date");
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

  const setQuotationPayload = () => {
    return {
      id: $prefix.toString() + $startFromNumber.toString(),
      prefix: $prefix,
      number: $startFromNumber,
      business: $selectedBusiness,
      client: $selectedClient,
      status: "active",
      quotationDate: selectedQuotationDate,
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
        forClient: $reminderForClient,
        forAdmin: $reminderForAdmin,
        before: $selectedReminderBefore,
        after: $selectedReminderAfter,
      },
    };
  };

  const handleSubmit = () => {
    if (!hasRequiredFields()) {
      return;
    }
    // to update the invoice list page
    $isCreatingNewInvoice = true;
    const payload = setQuotationPayload();
    submit(payload);
  };

  const submit = (payload) => {
    isSubmitting = true;
    toast.loading("Creating quotation...");

    createPostRequest(`quotation/create?email=${isSendMail}`, payload, (res) => {
      toast.dismiss();
      toast.success("Quotation created successfully", { duration: 5000 });
      if (isSendMail && res.data.emailSent) {
        toast.success("Quotation sent to email successfully", {
          duration: 5000,
        });
      } else if (isSendMail && !res.data.emailSent) {
        toast.error("Failed to send email", { duration: 5000 });
      }

      triggerInvoiceTabUpdate(InvoiceListTab.ALL.LOWER_CASE);
      updateSettingStore();
      isSubmitting = false;
      window.location.replace(`#/quotation/${res.data.token}`);
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

<FormQuotation
  {nav}
  {isSubmitting}
  bind:selectedQuotationDate
  bind:selectedDueDate
  bind:selectedDueDateCustom
  on:submit="{handleSubmit}"
  on:createAndSend="{createAndSend}"
  on:updateDueDate="{updateDueDate}" />
