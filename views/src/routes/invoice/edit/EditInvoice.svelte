<script>
  import { handleError } from "$lib/helpers/errorHelper";
  import { createGetRequest, createPostRequest } from "$lib/helpers/request";
  import { onMount } from "svelte";
  import {
    selectedBusiness,
    selectedBusinessInput,
    selectedClientBind,
    selectedDueDateInterval,
    createdProductList,
    isBankPaymentChecked,
    isPaypalChecked,
    isXenditChecked,
    selectedBank,
    selectedPaypal,
    selectedPaypalBind,
    wcPayment,
    currencyPayload,
    note,
    termsAndConditionsInvoice,
    internalNote,
    subtotal,
    total,
    totalDiscount,
    totalTax,
    selectedDiscount,
    selectedTax,
    selectedReminderBefore,
    selectedReminderAfter,
    reminderForAdmin,
    reminderForClient,
    selectedClient,
    invoiceNumber,
    checkedTax,
    billedTo,
    billedToSameAsClient,
    checkedDiscount,
  } from "$lib/stores/invoice-store";
  import { taxes, discounts } from "$lib/stores/settings-store";
  import { PaymentMethod, PaymentStatus } from "$lib/common/enum";
  import { dueDateList } from "$lib/common/options";
  import { setPayment } from "$lib/helpers/setPaymentHelper";
  import { checkIsPaymentValid } from "$lib/helpers/paymentChecker";
  import { triggerInvoiceTabUpdate } from "$lib/helpers/triggerTabUpdateHelper";
  import { InvoiceListTab } from "$lib/common/enum";
  import { handleSubmitError } from "$lib/helpers/invoiceErrorHelper";
  import { isDebug } from "$lib/stores/settings-store";
  import TemplateCreateInvoice from "$lib/components/template/BaseCreateInvoice.svelte";
  import moment from "moment";
  import toast from "svelte-french-toast";

  export let params = {};

  const nav = [
    { name: "Invoice", link: "invoices" },
    { name: "Edit", link: "" },
  ];

  let id;
  let tab;
  let invoice;
  let selectedStatus;
  let selectedOrderDate;
  let selectedInvoiceDate;
  let selectedDueDate;
  let selectedDueDateBind;
  let selectedDueDateCustom;
  let isSubmitting = false;
  let isFinishFetchSetting = false;
  let isFinishFetchInvoice = false;
  let isFinishSetOptions = false;
  let isWoocommerce = false;

  /** @type {{name: string, symbol: string}}*/
  let invoiceCurrency;

  /** @type {Array<string>} */
  let invoiceReminderBefore;

  /** @type {Array<string>} */
  let invoiceReminderAfter;

  /* This will only run if both setting data and invoice data already fetched,
  then after that, this wont ever run again afterward */
  $: {
    if (isFinishFetchSetting && isFinishFetchInvoice && !isFinishSetOptions) {
      $selectedReminderBefore = invoiceReminderBefore;
      $selectedReminderAfter = invoiceReminderAfter;
      $currencyPayload = invoiceCurrency;
      $checkedTax = setTaxDiscount($taxes, $selectedTax);
      $checkedDiscount = setTaxDiscount($discounts, $selectedDiscount);
      saveToInput(invoice);
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
    data.value.isWcClient = data.value.isWcClient === "true" || data.value.isWcClient === true ? true : false;
    data.value.id = parseInt(data.value.id);
    return data;
  };

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

  /**
   * @param {Array<Object>} reminder
   * @return {Array<string>}
   */
  const setReminder = (reminder) => {
    return reminder?.map((item) => {
      return `${item.value} ${item.interval}`;
    });
  };

  /**
   * @param {string} dueDate
   * @param {string} invoiceDate
   */
  const setDueDate = (dueDate, invoiceDate) => {
    const due = moment(dueDate);
    const inv = moment(invoiceDate);
    const diff = due.diff(inv, "days");
    let option = dueDateList.find((item) => item.id === diff);
    selectedDueDate = dueDate;
    // empty means custom due date
    if (!option) {
      option = dueDateList.find((item) => item.id === 0);
      // set this to set the custom input value
      selectedDueDateCustom = dueDate;
      // set this to show the custom date input
      $selectedDueDateInterval = { value: option.id, label: option.name };
    }
    selectedDueDateBind = { value: option.id, label: option.name };
  };

  const saveToInput = (data) => {
    id = data.id;
    tab = data.tab;
    $invoiceNumber = data.invoiceNumber;
    $selectedBusinessInput = { label: data.business.business_name, value: data.business };
    $selectedClientBind = setClient(data.client);
    $billedToSameAsClient = data.billedToSameAsClient;
    $billedTo = { name: data.billedTo.name, detail: data.billedTo.detail };
    invoiceReminderBefore = setReminder(data?.reminders?.before) ?? [];
    invoiceReminderAfter = setReminder(data?.reminders?.after) ?? [];
    invoiceCurrency = data.currency;
    selectedStatus = { label: PaymentStatus.UNPAID.toUpperCase(), value: PaymentStatus.UNPAID };
    selectedOrderDate = data.orderDate;
    selectedInvoiceDate = data.invoiceDate;
    setDueDate(data.dueDate, data.invoiceDate);
    $createdProductList = data.products.map((item) => {
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
    data.payments.map((item) => {
      if (item.method === PaymentMethod.BANK) {
        $isBankPaymentChecked = true;
        $selectedBank = {
          label: item.name,
          value: {
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
      } else if (item.method == PaymentMethod.WOOCOMMERCE) {
        $wcPayment = item;
      }
    });
    $note = data.invoiceNote.note;
    $termsAndConditionsInvoice = data.invoiceNote.terms;
    $internalNote = data.invoiceNote.internalNote;
    $selectedDiscount = data.discount.data;
    $selectedTax = data.tax.data;
    isWoocommerce = data.isWoocommerce;
    const hasReminder = data?.reminders?.before || data?.reminders?.after;
    $reminderForAdmin = data?.reminders?.forAdmin ?? false;
    $reminderForClient = data?.reminders?.forClient ?? hasReminder;
  };

  const getInvoiceDetail = async () => {
    try {
      const res = await createGetRequest(`invoice/detail?invoizeToken=${params.invoizeToken}`);
      $isDebug && console.log(res.data);
      invoice = res.data;
      saveToInput(res.data);
      isFinishFetchInvoice = true;
    } catch (err) {
      handleError(err, "Failed to retrieve invoice detail");
    }
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
      const message = "Please fill missing fields: " + missingFields.join(", ") + ".";
      toast.error(message);
      return false;
    }

    return true;
  };

  const setInvoicePayload = () => {
    return {
      business: $selectedBusiness,
      client: $selectedClient,
      orderDate: selectedOrderDate,
      billedTo: $billedTo,
      billedToSameAsClient: $billedToSameAsClient,
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
    const payload = setInvoicePayload();
    $isDebug && console.log(payload);
    submit(payload);
  };

  const submit = (payload) => {
    if (isSubmitting) return;
    isSubmitting = true;
    toast.loading("Updating invoice...");

    createPostRequest(`invoice/edit?id=${id}`, payload, (res) => {
      toast.dismiss();
      toast.success("Invoice updated successfully");
      triggerInvoiceTabUpdate(tab, InvoiceListTab.ALL.LOWER_CASE);
      isSubmitting = false;
      window.location.replace(`#/invoice/${res.data.token}`);
    }).catch((err) => {
      isSubmitting = false;
      handleSubmitError(err, "invoice");
    });
  };

  const handleSetOptions = () => {
    isFinishFetchSetting = true;
  };

  onMount(() => {
    getInvoiceDetail();
  });
</script>

<TemplateCreateInvoice
  isEdit="{true}"
  {nav}
  {isSubmitting}
  {selectedDueDateBind}
  {isWoocommerce}
  bind:selectedStatus
  bind:selectedOrderDate
  bind:selectedInvoiceDate
  bind:selectedDueDate
  bind:selectedDueDateCustom
  on:settingsFetched="{handleSetOptions}"
  on:submit="{handleSubmit}"
  on:updateDueDate="{updateDueDate}" />
