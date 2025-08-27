<script>
  import { dueDateList } from "$lib/common/options";
  import TemplateCreateRecurring from "$lib/components/template/BaseCreateRecurring.svelte";
  import { handleError } from "$lib/helpers/errorHelper";
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import {
    selectedBusiness,
    selectedBusinessInput,
    selectedClient,
    selectedClientBind,
    dueDateRecurringCustom,
    selectedDueDateInterval,
    createdProductList,
    isBankPaymentChecked,
    isPaypalChecked,
    isXenditChecked,
    selectedBank,
    selectedPaypal,
    selectedPaypalBind,
    currencyPayload,
    note,
    termsAndConditionsInvoice,
    internalNote,
    selectedDiscount,
    selectedTax,
    selectedReminderBefore,
    selectedReminderAfter,
    subtotal,
    total,
    totalDiscount,
    totalTax,
    checkedDiscount,
    checkedTax,
    billedTo,
    billedToSameAsClient,
    reminderForAdmin,
    reminderForClient,
  } from "$lib/stores/invoice-store";
  import { taxes, discounts } from "$lib/stores/settings-store";
  import { recurring } from "$lib/stores/recurring-store";
  import { PaymentMethod } from "$lib/common/enum";
  import { onMount } from "svelte";
  import { capitalizeFirstLetter } from "$lib/helpers/capitalHelper";
  import { setPayment } from "$lib/helpers/setPaymentHelper";
  import { checkIsPaymentValid } from "$lib/helpers/paymentChecker";
  import { isDebug } from "$lib/stores/settings-store";
  import toast from "svelte-french-toast";

  export let params = {};

  const nav = [
    { name: "Recurring", link: "recurrings" },
    { name: "Edit", link: "" },
  ];

  let id;
  let recurringData;
  let selectedDueDate;
  let selectedDueDateBind;
  let recurringReminderBefore;
  let recurringReminderAfter;
  let recurringCurrency;
  let isFinishFetchSetting = false;
  let isFinishFetchRecurring = false;
  let isFinishSetOptions = false;
  let isSubmitting = false;

  $: {
    if (isFinishFetchSetting && isFinishFetchRecurring && !isFinishSetOptions) {
      $selectedReminderBefore = recurringReminderBefore;
      $selectedReminderAfter = recurringReminderAfter;
      $currencyPayload = recurringCurrency;
      $checkedTax = setTaxDiscount($taxes, $selectedTax);
      $checkedDiscount = setTaxDiscount($discounts, $selectedDiscount);
      saveToInput(recurringData);
      isFinishSetOptions = true;
    }
  }

  const setTaxDiscount = (list, selectedList) => {
    return list.map((item) => {
      const isSelected = selectedList.find((selectedItem) => selectedItem.name === item.name);
      if (isSelected) return true;
      return false;
    });
  };

  const setClient = (client) => {
    const data = { label: client.name, value: client };
    if (data.value.isWcClient === "true" || data.value.isWcClient === true) {
      data.value.isWcClient = true;
    } else {
      data.value.isWcClient = false;
    }
    data.value.id = parseInt(data.value.id);
    return data;
  };

  const setDueDate = (dueDate) => {
    const option = dueDateList.find((item) => item.id === parseInt(dueDate));
    // empty means custom
    if (!option) {
      const selected = { value: 0, label: "Custom" };
      $selectedDueDateInterval = selected;
      $dueDateRecurringCustom = parseInt(dueDate);
      return selected;
    }
    const selected = { value: option.id, label: option.name };
    $selectedDueDateInterval = selected;
    return selected;
  };

  const setRecurring = (recurring) => {
    return {
      name: recurring.name,
      interval: { value: recurring.interval, label: capitalizeFirstLetter(recurring.interval) },
      start_date: recurring.nextInvoiceDate,
      end: { value: recurring.end, label: capitalizeFirstLetter(recurring.end) },
    };
  };

  /**
   * @param {Array<Object>} reminder
   * @return {Array<string>}
   */
  const setReminder = (reminder) => {
    return reminder?.map((item) => {
      return `${item.value} ${item.interval}`;
    });
  };

  const saveToInput = (data) => {
    id = data.id;
    $selectedBusinessInput = { label: data.invoice.business.business_name, value: data.invoice.business };
    $selectedClientBind = setClient(data.invoice.client);
    selectedDueDateBind = setDueDate(data.invoice.dueDateInterval);
    $createdProductList = data.invoice.products.map((item) => {
      return {
        id: parseInt(item.id),
        name: item.name,
        quantity: parseInt(item.quantity),
        unitPrice: parseFloat(item.unitPrice),
        amount: parseFloat(item.amount),
        description: item.description,
        note: item.note,
      };
    });
    data.invoice.payments.map((item) => {
      if (item.method === PaymentMethod.BANK) {
        $isBankPaymentChecked = true;
        $selectedBank = {
          label: item.name,
          value: {
            id: item.id,
            name: item.name,
            type: item.type,
            detail: item.detail,
            currency: { name: item.currency },
          },
        };
      } else if (item.method === PaymentMethod.PAYPAL) {
        $isPaypalChecked = true;
        $selectedPaypalBind = { label: item.type, value: { name: item.name, type: item.type } };
        $selectedPaypal = { name: item.name, type: item.type };
      } else if (item.method === PaymentMethod.XENDIT) {
        $isXenditChecked = true;
      }
    });
    
    $billedTo = { name: data.invoice?.billedTo?.name, detail: data.invoice?.billedTo?.detail } ;
    $billedToSameAsClient = data.invoice.billedToSameAsClient;
    $note = data.invoice.invoiceNote.note;
    $termsAndConditionsInvoice = data.invoice.invoiceNote.terms;
    $internalNote = data.invoice.invoiceNote.internalNote;
    $selectedDiscount = data.invoice.discount.data;
    $selectedTax = data.invoice.tax.data;
    recurringCurrency = data.invoice.currency;
    recurringReminderBefore = setReminder(data.invoice?.reminders?.before) ?? [];
    recurringReminderAfter = setReminder(data.invoice?.reminders?.after) ?? [];
    $recurring = setRecurring(data.recurring);
    const hasReminder = data?.reminders?.before || data?.reminders?.after;
    $reminderForAdmin = data.invoice.reminders?.forAdmin ?? false;
    $reminderForClient = data.invoice.reminders?.forClient ?? hasReminder;
  };

  const getRecurringDetail = async () => {
    try {
      const res = await createGetRequest(`recurring/detail?token=${params.invoizeToken}`);
      $isDebug && console.log(res.data);
      recurringData = res.data;
      saveToInput(res.data);
      isFinishFetchRecurring = true;
    } catch (err) {
      handleError(err, "Failed to retrieve recurring data");
    }
  };

  const updateDueDate = (e) => {
    $selectedDueDateInterval = e.detail;
    if (e.detail.value === 0) {
      selectedDueDate = null;
    } else {
      selectedDueDate = e.detail.value;
      $dueDateRecurringCustom = null;
    }
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

    if (!$billedToSameAsClient && !$billedTo.name) {
      missingFields.push("Bill to 'name'");
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

  const setRecurringPayload = () => {
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

  const setPayload = (payments, recurring, dueDate) => {
    // console.log($billedTo);
    return {
      id: id,
      business: $selectedBusiness,
      client: $selectedClient,
      dueDateInterval: dueDate,
      products: $createdProductList,
      billedTo: $billedTo,
      billedToSameAsClient: $billedToSameAsClient,
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

  const handleSubmit = () => {
    if (!hasRequiredFields()) return;
    submit(setPayload(setPayment(), setRecurringPayload(), setDueDatePayload()));
  };

  const submit = (payload) => {
    $isDebug && console.log(payload);
    isSubmitting = true;
    toast.loading("Updating recurring invoice...");

    createPostRequest("recurring/edit", payload, (res) => {
      toast.dismiss();
      isSubmitting = false;
      toast.success("Recurring Invoice updated successfully");
      window.location.replace(`#/recurring/${$selectedClient.id}/${res.data.token}`);
    }).catch((err) => {
      isSubmitting = false;
      handleError(err, "Failed to update recurring invoice");
    });
  };

  const handleSetOptions = () => {
    isFinishFetchSetting = true;
  };

  onMount(() => {
    getRecurringDetail();
  });
</script>

<TemplateCreateRecurring
  isEdit="{true}"
  {nav}
  {isSubmitting}
  bind:selectedDueDateBind
  on:settingsFetched="{handleSetOptions}"
  on:submit="{handleSubmit}"
  on:updateDueDate="{updateDueDate}" />
