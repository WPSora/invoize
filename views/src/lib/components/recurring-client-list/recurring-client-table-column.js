// @ts-nocheck
import { createTable, createRender } from "svelte-headless-table";
import { isDebug } from "$lib/stores/settings-store";
import { get } from "svelte/store";
import IdHeader from "$lib/components/recurring-client-list/header/id-header.svelte";
import IdCell from "$lib/components/recurring-client-list/cell/id-cell.svelte";
import CustomerHeader from "$lib/components/recurring-client-list/header/customer-header.svelte";
import CustomerCell from "$lib/components/recurring-client-list/cell/customer-cell.svelte";
import LastInvoiceHeader from "$lib/components/recurring-client-list/header/last-invoice-header.svelte";
import LastInvoiceCell from "$lib/components/recurring-client-list/cell/last-invoice-cell.svelte";
import TotalHeader from "$lib/components/recurring-client-list/header/total-header.svelte";
import TotalCell from "$lib/components/recurring-client-list/cell/total-cell.svelte";
import UpcomingInvoiceHeader from "$lib/components/recurring-client-list/header/upcoming-invoice-header.svelte";
import UpcomingInvoiceCell from "$lib/components/recurring-client-list/cell/upcoming-invoice-cell.svelte";
import ActiveHeader from "$lib/components/recurring-client-list/header/active-header.svelte";
import ActiveCell from "$lib/components/recurring-client-list/cell/active-cell.svelte";
import InactiveHeader from "$lib/components/recurring-client-list/header/inactive-header.svelte";
import InactiveCell from "$lib/components/recurring-client-list/cell/inactive-cell.svelte";

export const setTableColumns = (paginationStore, tableStore) => {
  const table = createTable(tableStore);

  const columns = table.createColumns([
    // Id
    table.column({
      header: () => createRender(IdHeader),
      accessor: "id",
      cell: (item) => {
        if (get(isDebug)) {
          return createRender(IdCell, { id: item.value })
        } else {
          const pagination = get(paginationStore);
          const id = get(tableStore).findIndex((data) => data.id === item.value);
          const position = (pagination.page - 1) * pagination.perPage + id + 1;
          return createRender(IdCell, { id: position });
        }
      },
    }),

    // Customer
    table.column({
      header: () => createRender(CustomerHeader),
      accessor: "name",
      cell: (item) => {
        const id = item.row.original.id;
        return createRender(CustomerCell, { id, name: item.value });
      },
    }),

    // Latest
    table.column({
      header: () => createRender(LastInvoiceHeader),
      accessor: 'last_invoice',
      cell: (item) => {
        return createRender(LastInvoiceCell, { lastInvoice: item.value });
      },
    }),

    // Upcoming
    table.column({
      header: () => createRender(UpcomingInvoiceHeader),
      accessor: 'next_invoice',
      cell: (item) => {
        return createRender(UpcomingInvoiceCell, { upcomingInvoiceDate: item.value });
      },
    }),

    // Total
    table.column({
      header: () => createRender(TotalHeader),
      accessor: 'amount',
      cell: (item) => {
        const currency = item.row.original.currency;
        return createRender(TotalCell, { total: item.value, currency });
      },
    }),

    // Active
    table.column({
      header: () => createRender(ActiveHeader),
      cell: (item) => {
        const totalActive = item.row.original.total_active
        return createRender(ActiveCell, { totalActive });
      },
    }),

    // Inactive
    table.column({
      header: () => createRender(InactiveHeader),
      cell: (item) => {
        const totalInactive = item.row.original.total_inactive
        return createRender(InactiveCell, { totalInactive });
      },
    }),
  ]);

  const { headerRows, rows, tableAttrs, tableBodyAttrs } = table.createViewModel(columns);
  return { headerRows, rows, tableAttrs, tableBodyAttrs };
}