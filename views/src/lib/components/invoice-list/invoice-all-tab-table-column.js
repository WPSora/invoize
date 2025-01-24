// @ts-nocheck
import { createTable, createRender } from "svelte-headless-table";
import { addResizedColumns } from "svelte-headless-table/plugins";
import { isDebug } from "$lib/stores/settings-store";
import { get } from "svelte/store";
import DateCell from "$lib/components/invoice-list/cell/date-cell.svelte";
import DateHeader from "$lib/components/invoice-list/header/date-header.svelte";
import InvoiceDateCell from "$lib/components/invoice-list/cell/invoice-date-cell.svelte";
import InvoiceDateHeader from "$lib/components/invoice-list/header/invoice-date-header.svelte";
import StatusCell from "$lib/components/invoice-list/cell/status-cell.svelte";
import StatusHeader from "$lib/components/invoice-list/header/status-header.svelte";
import ActionCell from "$lib/components/invoice-list/cell/action-cell.svelte";
import ActionHeader from "$lib/components/invoice-list/header/action-header.svelte";
import PaymentHeader from "$lib/components/invoice-list/header/payment-header.svelte";
import PriceHeader from "$lib/components/invoice-list/header/price-header.svelte";
import TypeCell from "$lib/components/invoice-list/cell/type-cell.svelte";
import TypeHeader from "$lib/components/invoice-list/header/type-header.svelte";
import InformationCell from "$lib/components/invoice-list/cell/information-cell.svelte";
import IdCell from "$lib/components/invoice-list/cell/id-cell.svelte";
import IdHeader from "$lib/components/invoice-list/header/id-header.svelte";
import PriceCell from "$lib/components/invoice-list/cell/price-cell.svelte";
import PaymentCell from "$lib/components/invoice-list/cell/payment-cell.svelte";

export const setAllTabTableColumns = (paginationStore, tableStore, updateTabList) => {
  // tableStore is store, but here we don't need to subscribe to it (using subscribe or $)
  // because it only need the name of the store, not the value
  // subscribing will cause error
  const table = createTable(tableStore, {
    resize: addResizedColumns(),
  });

  // #
  const columns = table.createColumns([
    table.column({
      header: () => {
        return createRender(IdHeader);
      },
      accessor: "id",
      cell: (item) => {
        if (get(isDebug)) {
          return createRender(IdCell, { id: item.value });
        } else {
          const pagination = get(paginationStore);
          const id = get(tableStore).findIndex((data) => data.id === item.value);
          const position = (pagination.page - 1) * pagination.perPage + id + 1;
          return createRender(IdCell, { id: position });
        }
      },
      plugins: {
        resize: {
          initialWidth: 40,
          minWidth: 30,
          maxWidth: 50,
        },
      },
    }),

    // Information
    table.column({
      header: "Information",
      accessor: "invoiceId",
      cell: (item) => {
        return createRender(InformationCell, {
          token: item.row.original.token,
          invoiceId: item.value,
          customer: item.row.original.client,
          isSent: item.row.original.isSent,
          isWoocommerce: item.row.original.isWoocommerce,
        });
      },
      plugins: {
        resize: {
          initialWidth: 400,
          minWidth: 300,
        },
      },
    }),

    // Type
    table.column({
      header: () => createRender(TypeHeader),
      accessor: "recurring",
      cell: (item) => {
        return createRender(TypeCell, { type: item.value });
      },
      plugins: {
        resize: {
          initialWidth: 130,
          minWidth: 120,
          maxWidth: 150,
        },
      },
    }),

    // Status
    table.column({
      header: () => createRender(StatusHeader),
      accessor: "paymentStatus",
      cell: (item) => {
        return createRender(StatusCell, {
          paymentStatus: item.value,
          invoiceStatus: item.row.original.invoiceStatus,
        });
      },
      plugins: {
        resize: {
          initialWidth: 100,
          minWidth: 100,
          maxWidth: 150,
        },
      },
    }),

    // Payment Method
    table.column({
      header: () => createRender(PaymentHeader),
      accessor: "payment",
      cell: (item) => {
        return createRender(PaymentCell, { payments: item.value });
      },
      plugins: {
        resize: {
          initialWidth: 130,
          minWidth: 100,
          maxWidth: 150,
        },
      },
    }),

    // Invoice Date
    table.column({
      header: () => {
        return createRender(InvoiceDateHeader);
      },
      accessor: "invoiceDate",
      cell: (item) => {
        return createRender(InvoiceDateCell, {
          invoiceDate: item.value,
        });
      },
      plugins: {
        resize: {
          initialWidth: 130,
          minWidth: 100,
          maxWidth: 150,
        },
      },
    }),

    // Due Date
    table.column({
      header: () => {
        return createRender(DateHeader);
      },
      accessor: "dueDate",
      cell: (item) => {
        return createRender(DateCell, {
          dueDate: item.value,
        });
      },
      plugins: {
        resize: {
          initialWidth: 130,
          minWidth: 100,
          maxWidth: 150,
        },
      },
    }),

    // Total
    table.column({
      header: () => createRender(PriceHeader),
      accessor: "total",
      cell: (item) => {
        return createRender(PriceCell, {
          name: item.row.original.currency.name,
          total: item.value,
        });
      },
      plugins: {
        resize: {
          initialWidth: 130,
          minWidth: 130,
        },
      },
    }),

    // Actions
    table.column({
      header: () => createRender(ActionHeader),
      accessor: ({ id }) => id,
      cell: (item) => {
        return createRender(ActionCell, {
          id: item.value,
          token: item.row.original.token,
          prefix: item.row.original.invoiceId,
          clientName: item.row.original.client,
          paymentStatus: item.row.original.paymentStatus,
          updateTabList: updateTabList,
          tab: item.row.original.tab,
          dueDate: item.row.original.dueDate,
          isWoocommerce: item.row.original.isWoocommerce,
        });
      },
      plugins: {
        resize: {
          initialWidth: 80,
          minWidth: 80,
          maxWidth: 80,
        },
      },
    }),
  ]);

  const { headerRows, rows, tableAttrs, tableBodyAttrs } = table.createViewModel(columns);
  return { headerRows, rows, tableAttrs, tableBodyAttrs };
};