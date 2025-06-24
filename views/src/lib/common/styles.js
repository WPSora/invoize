// Create Invoice
export const removeArrowOnInputStyle =
  "[appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none";

export const invoiceFormStyle = "flex flex-col md:max-w-xs w-full gap-1.5 mb-4";

// Settings
export const settingsTabStyle =
  "w-full p-2 h-12 justify-start data-[state=active]:shadow-none data-[state=active]:bg-background data-[state=active]:text-primary data-[state=active]:text-base rounded-none hover:text-gray-800 rounded-xl"

const baseTabClass =
  "data-[state=active]:shadow-none data-[state=active]:bg-background data-[state=active]:text-primary data-[state=active]:text-base rounded-none";

export const invoiceTabSettingStyle = baseTabClass;
export const emailTabSettingStyle = baseTabClass;
export const paymentTabSettingStyle = baseTabClass;

// Invoice list
export const invoiceListBaseTabStyle =
  "w-[14%] data-[state=active]:shadow-none data-[state=active]:font-bold data-[state=active]:text-sm data-[state=active]:bg-background data-[state=active]:border-b-2 p-3 rounded-none duration-100";

export const allTabClass = "data-[state=active]:text-primary data-[state=active]:border-b-primary";
export const paidTabClass = "data-[state=active]:text-green-600 data-[state=active]:border-b-green-600";
export const unpaidTabClass = "data-[state=active]:text-red-600 data-[state=active]:border-b-red-600";
export const expiredTabClass = "data-[state=active]:text-slate-500 data-[state=active]:border-b-slate-500";
export const archiveTabClass = "data-[state=active]:text-yellow-500 data-[state=active]:border-b-yellow-500";
export const cancelTabClass = "data-[state=active]:text-slate-800 data-[state=active]:border-b-slate-800";
export const trashTabClass = "data-[state=active]:text-red-700 data-[state=active]:border-b-red-700";
export const searchTabClass = "data-[state=active]:text-cyan-600 data-[state=active]:border-b-cyan-600";

export const baseSelectTabClass = "w-full flex data-[state=active]:shadow-none data-[state=active]:font-bold data-[state=active]:text-sm data-[state=active]:bg-background data-[state=active]:text-primary p-3 rounded-none duration-100";

// Receipt list
export const receiptListBaseTabStyle =
  "w-[33%] data-[state=active]:shadow-none data-[state=active]:font-bold data-[state=active]:text-sm data-[state=active]:bg-background data-[state=active]:border-b-2 p-3 rounded-none duration-100";
