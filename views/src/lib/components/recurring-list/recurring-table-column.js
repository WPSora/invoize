// @ts-nocheck
import { createTable, createRender } from "svelte-headless-table";
import { isDebug } from "$lib/stores/settings-store";
import { get } from "svelte/store";
import ActionHeader from "$lib/components/recurring-list/header/action-header.svelte"
import ActionCell from "$lib/components/recurring-list/cell/action-cell.svelte"
import NextInvoiceHeader from "$lib/components/recurring-list/header/next-invoice-header.svelte"
import NextInvoiceCell from "$lib/components/recurring-list/cell/next-invoice-cell.svelte"
import IdHeader from "$lib/components/recurring-list/header/id-header.svelte";
import IdCell from "$lib/components/recurring-list/cell/id-cell.svelte";
import NameHeader from "$lib/components/recurring-list/header/name-header.svelte";
import NameCell from "$lib/components/recurring-list/cell/name-cell.svelte";
import PaymentHeader from "$lib/components/recurring-list/header/payment-header.svelte";
import PaymentCell from "$lib/components/recurring-list/cell/payment-cell.svelte";
import ScheduleHeader from "$lib/components/recurring-list/header/schedule-header.svelte";
import ScheduleCell from "$lib/components/recurring-list/cell/schedule-cell.svelte";
import TotalHeader from "$lib/components/recurring-list/header/total-header.svelte";
import TotalCell from "$lib/components/recurring-list/cell/total-cell.svelte";
import LastInvoiceHeader from "$lib/components/recurring-list/header/last-invoice-header.svelte"
import LastInvoiceCell from "$lib/components/recurring-list/cell/last-invoice-cell.svelte"

export const setTableColumns = (paginationStore, tableStore, updateTabList, params) => {
  const table = createTable(tableStore);

  const columns = table.createColumns([
    table.column({
      header: () => createRender(IdHeader),
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
    }),

    table.column({
      header: () => createRender(NameHeader),
      accessor: "name",
      cell: (item) => {
        const token = item.row.original.token;
        return createRender(NameCell, { name: item.value, token, id: params.clientId })
      },
    }),

    table.column({
      header: () => createRender(PaymentHeader),
      accessor: 'payment',
      cell: (item) => {
        return createRender(PaymentCell, { payments: item.value })
      },
    }),

    table.column({
      header: () => createRender(LastInvoiceHeader),
      accessor: 'lastInvoice',
      cell: (item) => {
        return createRender(LastInvoiceCell, { lastInvoice: item.value })
      },
    }),

    table.column({
      header: () => createRender(ScheduleHeader),
      accessor: 'interval',
      cell: (item) => {
        return createRender(ScheduleCell, { interval: item.value })
      },
    }),

    table.column({
      header: () => createRender(NextInvoiceHeader),
      accessor: 'nextInvoice',
      cell: (item) => {
        return createRender(NextInvoiceCell, { date: item.value })
      },
    }),

    table.column({
      header: () => createRender(TotalHeader),
      accessor: 'total',
      cell: (item) => {
        const currency = item.row.original.currency;
        return createRender(TotalCell, { total: item.value, currency })
      },
    }),


    table.column({
      header: () => createRender(ActionHeader),
      accessor: 'token',
      cell: (item) => {
        const recurringStatus = item.row.original.recurringStatus;
        const name = item.row.original.name;
        const id = item.row.original.id;
        const clientId = item.row.original.clientId
        return createRender(ActionCell, {
          id,
          clientId,
          name,
          recurringStatus,
          token: item.value,
          updateTabList: updateTabList
        })
      },
    }),
  ]);

  const { headerRows, rows, tableAttrs, tableBodyAttrs } = table.createViewModel(columns);
  return { headerRows, rows, tableAttrs, tableBodyAttrs };
}