// @ts-nocheck
import { createRender, createTable } from "svelte-headless-table";

import ActionCell from "$lib/components/quotation-list/cell/action-cell.svelte";
import ActionHeader from "$lib/components/quotation-list/header/action-header.svelte";
import DateCell from "$lib/components/quotation-list/cell/date-cell.svelte";
import DateHeader from "$lib/components/quotation-list/header/date-header.svelte";
import IdCell from "$lib/components/quotation-list/cell/id-cell.svelte";
import IdHeader from "$lib/components/quotation-list/header/id-header.svelte";
import InformationCell from "$lib/components/quotation-list/cell/information-cell.svelte";
import { InvoiceListTab } from "$lib/common/enum";
import PaymentCell from "$lib/components/quotation-list/cell/payment-cell.svelte";
import PaymentHeader from "$lib/components/quotation-list/header/payment-header.svelte";
import PriceCell from "$lib/components/quotation-list/cell/price-cell.svelte";
import PriceHeader from "$lib/components/quotation-list/header/price-header.svelte";
import QuotationDateCell from "$lib/components/quotation-list/cell/quotation-date-cell.svelte";
import QuotationDateHeader from "$lib/components/quotation-list/header/quotation-date-header.svelte";
import TypeCell from "$lib/components/quotation-list/cell/type-cell.svelte";
import TypeHeader from "$lib/components/quotation-list/header/type-header.svelte";
import { addResizedColumns } from "svelte-headless-table/plugins";
import { get } from "svelte/store";
import { isDebug } from "$lib/stores/settings-store";

export const setTableColumns = (
  paginationStore,
  tableStore,
  updateTabList,
  params
) => {
  // tableStore is store, but here we don't need to subscribe to it (using subscribe or $)
  // because it only need the name of the store, not the value
  // subscribing will cause error
  const table = createTable(tableStore, {
    resize: addResizedColumns(),
  });
  
  const columns = table.createColumns([
    // #
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
          const id =
            get(tableStore).findIndex((data) => data.id === item.value) + 1;
          const position = (pagination.page - 1) * pagination.perPage + id;
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
      accessor: "quotationNumber",
      cell: (item) => {
        return createRender(InformationCell, {
          quotationNumber: item.value,
          token: item.row.original.token,
          customer: item.row.original.client.name,
          isSent: false,
        });
      },
      plugins: {
        resize: {
          initialWidth: 400,
          minWidth: 300,
        },
      },
    }),

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

    table.column({
      header: () => {
        return createRender(QuotationDateHeader);
      },
      accessor: "quotationDate",
      cell: (item) => {
        return createRender(QuotationDateCell, {
          date: item.value,
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

    table.column({
      header: (item) => {
        return createRender(DateHeader, {
          label: "Expired Date",
        });
      },
      accessor: "dueDate",
      cell: (item) => {
        return createRender(DateCell, {
          date: item.value,
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
          initialWidth: 150,
          minWidth: 130,
        },
      },
    }),

    table.column({
      header: () => createRender(ActionHeader),
      accessor: ({ id }) => id,
      cell: (item) => {
        return createRender(ActionCell, {
          id: item.value,
          token: item.row.original.token,
          status: item.row.original.status,
          clientName: item.row.original.client,
          updateTabList: updateTabList,
          dueDate: item.row.original.dueDate,
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

  const { headerRows, rows, tableAttrs, tableBodyAttrs } =
    table.createViewModel(columns);
  return { headerRows, rows, tableAttrs, tableBodyAttrs };
};
