<script>
  import { triggerInvoiceTabUpdate } from "$lib/helpers/triggerTabUpdateHelper";
  import { InvoiceListTab } from "$lib/common/enum";
  import { createPostRequest } from "$lib/helpers/request";
  import {
    selectedBusiness,
    selectedClient,
    dueDateRecurringCustom,
    selectedDueDateInterval,
    createdProductList,
    currencyPayload,
    note,
    termsAndConditionsInvoice,
    internalNote,
    total,
    subtotal,
    totalDiscount,
    selectedDiscount,
    totalTax,
    selectedTax,
    selectedReminderBefore,
    selectedReminderAfter,
    billedTo,
    billedToSameAsClient,
    reminderForAdmin,
    reminderForClient,
  } from "$lib/stores/invoice-store";
  import { recurring, isCreatingNewRecurring } from "$lib/stores/recurring-store";
  import { onMount, tick } from "svelte";
  import { handleSubmitError } from "$lib/helpers/invoiceErrorHelper";
  import { checkIsPaymentValid } from "$lib/helpers/paymentChecker";
  import { setPayment } from "$lib/helpers/setPaymentHelper";
  import { setContext } from "svelte";
  import { isDebug } from "$lib/stores/settings-store";
  import TemplateCreateRecurring from "$lib/components/template/BaseCreateRecurring.svelte";
  import toast from "svelte-french-toast";
  import { getDefaultRecurring } from "$lib/helpers/setDefaultRecurring";

  export let params = {};

  const nav = [
    { name: "Recurring", link: "recurrings" },
    { name: "Create", link: "" },
  ];

  let selectedDueDate;
  let selectedDueDateBind;
  let isSubmitting = false;
  let isSendEmail = false;

  setContext("clientIdParam", params?.clientId);

  const setRecurring = () => {
    return {
      name: $recurring?.name,
      interval: $recurring?.interval?.value,
      start: $recurring?.start_date,
      end: $recurring?.end?.value,
    };
  };

  const setDueDatePayload = () => {
    return $selectedDueDateInterval?.value !== 0
      ? parseInt($selectedDueDateInterval?.value)
      : parseInt($dueDateRecurringCustom);
  };

  const setRecurringPayload = (payments, recurring, dueDate) => {
    return {
      id: recurring.name,
      business: $selectedBusiness,
      billedTo: $billedTo,
      billedToSameAsClient: $billedToSameAsClient,
      client: $selectedClient,
      dueDateInterval: dueDate,
      products: $createdProductList,
      payments: payments,
      note: $note,
      terms: $termsAndConditionsInvoice,
      internalNote: $internalNote,
      currency: $currencyPayload,
      subtotal: $subtotal,
      total: $total,
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
      recurring: recurring,
    };
  };

  // update value that need to be sent to the API
  const updateDueDate = (e) => {
    $selectedDueDateInterval = e.detail;
    if (e.detail.value === 0) {
      selectedDueDate = null;
      focusCustomDueDateInput();
    } else {
      selectedDueDate = e.detail.value;
      $dueDateRecurringCustom = null;
    }
  };

  const focusCustomDueDateInput = () => {
    tick().then(() => {
      let input = document.getElementById("custom-due-date");
      setTimeout(() => {
        input?.focus();
      }, 250);
    });
  };

  const checkIsRecurringEmpty = () => {
    // must have name
    if (!$recurring?.name?.trim()) {
      return true;
    }
    // must have start_date, end time. and interval value
    if (!$recurring?.interval?.value || !$recurring?.start_date || !$recurring?.end) {
      return true;
    }
    return false;
  };

  const hasRequiredFields = () => {
    // if any of these true, means the field is missing
    let missingFields = [];

    if (!$selectedBusiness) {
      missingFields.push("Issuer");
    }
    if (!$selectedClient || $selectedClient.id === 0) {
      missingFields.push("Customer");
    }
    if ($dueDateRecurringCustom == 0 || ($dueDateRecurringCustom == null && $selectedDueDateInterval?.value == 0)) {
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
    if (checkIsRecurringEmpty()) {
      missingFields.push("Recurring");
    }

    if (missingFields.length > 0) {
      const message = "Please fill missing fields: " + missingFields.join(", ") + ".";
      toast.error(message);
      return false;
    }
    return true;
  };

  const handleSubmit = (eventIsCreateInvoice) => {
    if (!hasRequiredFields()) {
      return;
    }
    $isCreatingNewRecurring = true;
    const payload = setRecurringPayload(setPayment(), setRecurring(), setDueDatePayload());
    submit(payload, eventIsCreateInvoice.detail);
  };

  const submit = (payload, isCreateInvoice) => {
    $isDebug && console.log(payload);
    isSubmitting = true;
    toast.loading("Saving recurring invoice...");
    const isCreate = isCreateInvoice ? true : false;
    const isSend = isSendEmail ? true : false;
    createPostRequest(`recurring/create?create-invoice=${isCreate}&send-email=${isSend}`, payload, (res) => {
      toast.dismiss();
      isSubmitting = false;
      toast.success("Recurring Invoice created successfully", { duration: 5000 });
      if (isCreate && res.data.invoiceCreated) {
        toast.success("Invoice created successfully", { duration: 5000 });
      } else if (isCreate && !res.data.invoiceCreated) {
        toast.error("Failed to create invoice", { duration: 5000 });
      }
      if (isSend && res.data.emailSent) {
        toast.success("Invoice sent to email successfully", { duration: 5000 });
      } else if (isSend && !res.data.emailSent) {
        toast.error("Failed to send email", { duration: 5000 });
      }
      isCreateInvoice && triggerInvoiceTabUpdate(InvoiceListTab.UNPAID.LOWER_CASE, InvoiceListTab.ALL.LOWER_CASE);
      window.location.replace(`#/recurring/${res.data.clientId}/${res.data.token}`);
    }).catch((err) => {
      isSubmitting = false;
      handleSubmitError(err, "recurring invoice");
    });
  };

  const createRecurringAndInvoice = ({ detail }) => {
    isSendEmail = detail.isSendEmail;
    handleSubmit({ detail: true });
  };

  onMount(() => {
    $recurring = {
      ...$recurring,
      ...getDefaultRecurring(),
    };
  });
</script>

<TemplateCreateRecurring
  {nav}
  {isSubmitting}
  bind:selectedDueDateBind
  on:submit="{handleSubmit}"
  on:createInvoice="{createRecurringAndInvoice}"
  on:updateDueDate="{updateDueDate}" />
