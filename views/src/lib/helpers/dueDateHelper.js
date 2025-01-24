import { dueDateList } from "$lib/common/options";

// from "After 7 days" to "7 days" to save on postmeta
export const formatDueDate = (e) => {
  return e.value.toString() + " days";
};

// from "7 days" to "After 7 days" to display on the select
export const setDueDate = (interval) => {
  const dueDateValue = parseInt(interval.split(" ")[0]);
  const item = dueDateList.find(item => item.id === dueDateValue);
  return item ? { value: item.id, label: item.name } : undefined;
};