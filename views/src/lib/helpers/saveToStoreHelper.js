import {
  activeAutomaticPaypalType,
  automaticPaypals,
  banks,
  currencies,
  defaultBank,
  defaultCurrency,
  defaultPayment,
  defaultReminderGroup,
  directPaypals,
  discounts,
  downloadPaperSize,
  dueDateInterval,
  enablePaymentPage,
  note,
  paymentOnWoocommerce,
  prefix,
  reminderGroups,
  reminders,
  startFromNumber,
  taxes,
  termsAndConditions,
  woocommerceIntegration,
  xenditCurrencyConverter,
  xenditKey,
  xenditToken,
  xenditSuccessRedirectUrl,
  xenditFailedRedirectUrl,
  xenditPrimaryCurrency,
  otherSettings,
} from "$lib/stores/settings-store";
import {
  checkedDiscount,
  checkedTax,
  currencyPayload,
  note as invoiceNote,
  selectedReminderAfter,
  selectedReminderBefore,
  termsAndConditionsInvoice,
} from "$lib/stores/invoice-store";

// this helper file to save data from API to store

export const savePaymentTabToSettingsStore = (res) => {
  let settings = {};
  res.map((item) => {
    settings[item.name] = item.value;
  });

  settings?.banks && banks.set(settings.banks);
  settings?.defaultBank && defaultBank.set(parseInt(settings.defaultBank));
  settings?.default && defaultPayment.set(settings.default);
  settings?.enablePaymentPage &&
    enablePaymentPage.set(settings.enablePaymentPage == "true");
  settings?.paymentOnWoocommerce &&
    paymentOnWoocommerce.set(settings.paymentOnWoocommerce);
  settings?.automaticPaypals && automaticPaypals.set(settings.automaticPaypals);
  settings?.directPaypals && directPaypals.set(settings.directPaypals);
  settings?.activeAutomaticPaypalType &&
    activeAutomaticPaypalType.set(settings.activeAutomaticPaypalType);
  settings?.xenditKey && xenditKey.set(settings.xenditKey);
  settings?.xenditToken && xenditToken.set(settings.xenditToken);
  settings?.xenditSuccessRedirectUrl && xenditSuccessRedirectUrl.set(settings.xenditSuccessRedirectUrl);
  settings?.xenditFailedRedirectUrl && xenditFailedRedirectUrl.set(settings.xenditFailedRedirectUrl);
  settings?.xenditPrimaryCurrency &&
    xenditPrimaryCurrency.set(settings.xenditPrimaryCurrency);
  xenditCurrencyConverter.update((val) => {
    const USD = settings?.xenditCurrencyConverter_USD
      ? parseFloat(settings.xenditCurrencyConverter_USD)
      : val.USD;
    const EUR = settings?.xenditCurrencyConverter_EUR
      ? parseFloat(settings.xenditCurrencyConverter_EUR)
      : val.EUR;
    const GBP = settings?.xenditCurrencyConverter_GBP
      ? parseFloat(settings.xenditCurrencyConverter_GBP)
      : val.GBP;
    return { USD, EUR, GBP };
  });
};

export const saveInvoiceTabToSettingsStore = (
  res,
  isSaveToInvoiceStore = false,
  isEdit = false
) => {
  const settings = {};
  res.map((item) => {
    settings[item.name] = item.value;
  });

  // parse string to float & add checked value
  settings?.discounts?.forEach((item) => {
    item.value = parseFloat(item.value);
    checkedDiscount.update((val) => [...val, false]);
  });
  settings?.taxes?.forEach((item) => {
    item.value = parseFloat(item.value);
    checkedTax.update((val) => [...val, false]);
  });

  settings?.currencies && currencies.set(settings.currencies);
  settings?.defaultCurrency && defaultCurrency.set(settings.defaultCurrency);
  settings?.discounts && discounts.set(settings.discounts);
  settings?.taxes && taxes.set(settings.taxes);
  settings?.prefix && prefix.set(settings.prefix);
  settings?.dueDateInterval && dueDateInterval.set(settings.dueDateInterval);
  settings?.startFromNumber && startFromNumber.set(settings.startFromNumber);
  if (!isEdit) {
    settings?.note && note.set(settings.note);
    settings?.termsAndConditions &&
      termsAndConditions.set(settings.termsAndConditions);
  }
  settings?.reminders && reminders.set(settings.reminders);
  settings?.reminderGroups && reminderGroups.set(settings.reminderGroups);
  settings?.defaultReminderGroup &&
    defaultReminderGroup.set(settings.defaultReminderGroup);
  settings?.downloadPaperSize &&
    downloadPaperSize.set(settings.downloadPaperSize);

  if (isSaveToInvoiceStore) {
    saveToInvoiceStore(settings);
  }
};

// we use different store because when user update the value in invoice page,
// it shouldn't update the settings store value. So we need different store
export const saveToInvoiceStore = (settings) => {
  settings?.note && invoiceNote.set(settings.note);
  settings?.termsAndConditions &&
    termsAndConditionsInvoice.set(settings.termsAndConditions);
  settings?.defaultReminderGroup?.value?.after &&
    selectedReminderAfter.set(settings.defaultReminderGroup.value.after);
  settings?.defaultReminderGroup?.value?.before &&
    selectedReminderBefore.set(settings.defaultReminderGroup.value.before);
  currencyPayload.update((val) => {
    return settings?.defaultCurrency ? settings.defaultCurrency : val;
  });
};

export const saveIntegrationTabToSettingsStore = (res) => {
  const settings = {};

  res.map((item) => {
    settings[item.name] = item.value;
  });

  settings?.woocommerce && woocommerceIntegration.set(settings.woocommerce);
};


export const saveOtherTabToSettingsStore = (res) => {
  const settings = {};
  res.map((item) => {
    settings[item.name] = item.value;
  })

  settings?.log && otherSettings.update((val) => {
    return {
      ...val,
      log: settings?.log,
    }
  })
}
