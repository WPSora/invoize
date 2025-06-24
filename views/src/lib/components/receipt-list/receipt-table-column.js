// @ts-nocheck
import { createTable, createRender } from "svelte-headless-table";
import { addResizedColumns } from "svelte-headless-table/plugins";
import { isDebug } from "$lib/stores/settings-store";
import { get } from "svelte/store";
import DateCell from "$lib/components/receipt-list/cell/date-cell.svelte";
import DateHeader from "$lib/components/receipt-list/header/date-header.svelte";
import StatusCell from "$lib/components/receipt-list/cell/status-cell.svelte";
import StatusHeader from "$lib/components/receipt-list/header/status-header.svelte";
import InformationHeader from "$lib/components/receipt-list/header/information-header.svelte";
import InformationCell from "$lib/components/receipt-list/cell/information-cell.svelte";
import IdCell from "$lib/components/receipt-list/cell/id-cell.svelte";
import IdHeader from "$lib/components/receipt-list/header/id-header.svelte";
import InvoiceHeader from "$lib/components/receipt-list/header/invoice-header.svelte";
import InvoiceCell from "$lib/components/receipt-list/cell/invoice-cell.svelte";

// invoiceStore is store, but here we don't need to subscribe to it (using subscribe or $)
// because it only need the name of the store, not the value
// subscribing will cause error
export const setTableColumns = (paginationStore, tableStore, updateTabList) => {
  const table = createTable(tableStore, {
    resize: addResizedColumns(),
  });

  const columns = table.createColumns([
    // id column
    table.column({
      header: () => createRender(IdHeader),
      accessor: "receiptId",
      cell: (item) => {
        if (get(isDebug)) {
          return createRender(IdCell, { id: item.value });
        } else {
          const pagination = get(paginationStore);
          const id = get(tableStore).findIndex((data) => data.receiptId === item.value);
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

    // information column
    table.column({
      header: () => createRender(InformationHeader),
      accessor: "receiptNumber",
      cell: (item) => {
        const client = item.row.original.client;
        const token = item.row.original.token;
        return createRender(InformationCell, { token, name: `${item.value} ${client}` });
      },
      plugins: {
        resize: {
          initialWidth: 400,
          minWidth: 300,
        },
      },
    }),

    // createed date column
    table.column({
      header: () => createRender(DateHeader),
      accessor: "createdDate",
      cell: (item) => {
        return createRender(DateCell, { date: item.value });
      },
      plugins: {
        resize: {
          initialWidth: 160,
          minWidth: 140,
          maxWidth: 180,
        },
      },
    }),

    // invoice id column
    table.column({
      header: () => createRender(InvoiceHeader),
      accessor: "invoiceNumber",
      cell: (item) => {
        const token = item.row.original.token;
        return createRender(InvoiceCell, { invoiceId: item.value, token });
      },
      plugins: {
        resize: {
          initialWidth: 130,
          minWidth: 120,
          maxWidth: 150,
        },
      },
    }),

    // status column
    table.column({
      header: () => createRender(StatusHeader),
      accessor: "receiptTab",
      cell: (item) => {
        return createRender(StatusCell, { status: item.value });
      },
      plugins: {
        resize: {
          initialWidth: 130,
          minWidth: 120,
          maxWidth: 150,
        },
      },
    }),
  ]);

  const { headerRows, rows, tableAttrs, tableBodyAttrs } = table.createViewModel(columns);
  return { headerRows, rows, tableAttrs, tableBodyAttrs };
};