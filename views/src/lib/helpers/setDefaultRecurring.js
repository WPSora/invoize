import moment from "moment";

export const getDefaultRecurring = () => ({
  start_date: moment().format("YYYY-MM-DD"),
  end: { value: "never", label: "Never" },
  // interval: { value: 'monthly', label: "Monthly" },
})