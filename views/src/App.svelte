<script>
  import ProPopup from "$lib/components/upgrade-to-pro/ProPopup.svelte";
  import { Button } from "$lib/components/ui/button";
  import Router from "svelte-spa-router";
  import routes from "./routes";
  import { Toaster } from "svelte-french-toast";
  import { enablePaymentPage, isProPopupOpen } from "$lib/stores/settings-store";
  import { onMount } from "svelte";
  import { createGetRequest } from "$lib/helpers/request";
  import { isWcInstalled } from "$lib/stores/settings-store";
  import { Loader2, Database, LayoutDashboard, ScrollText, Settings, Receipt } from "lucide-svelte";
  import { handleError } from "$lib/helpers/errorHelper";
  import {Alert, AlertTitle, AlertDescription} from "$lib/components/ui/alert";
  import { activeAutomaticPaypalType } from "$lib/stores/settings-store";
  import * as Card from "$lib/components/ui/card";

  
  // Remove sidebar on welcome page
  if (window.location.hash.toLowerCase() == "#/welcome") {
    const sidebar = document.getElementById("adminmenumain");
    const adminbar = document.getElementById("wpadminbar");
    const wpcontent = document.getElementById("wpcontent");
    if (sidebar) {
      sidebar.style.display = "none";
      wpcontent.style.marginLeft = "0";
      wpcontent.style.paddingLeft = "0";
    }
    if (adminbar) {
      adminbar.style.display = "none";
    }
  }

  onMount(() => {
    createGetRequest("settings/wc-status")
      .then((res) => isWcInstalled.set(res.data.installed))
      .catch((err) => {
        handleError(err, "Failed to retrieve Woocommerce status");
      });

    createGetRequest("settings/get?key=payment.activeAutomaticPaypalType")
    .then((res) => {
      $activeAutomaticPaypalType = res.data.value;
    })
    .catch((err) => {
      $activeAutomaticPaypalType = "live";
    });
  });

  const handleActiveNav = (link, index) => {
    location.href = link;
    isActivePage = isActivePage.map(() => false);
    isActivePage[index] = true;
  };


  let isUpdatingDatabase = false;
  let updatingResultMessage = false;
  let updatingDatabaseFailed = false;

  const updateDatabase = () => {
    isUpdatingDatabase = true;
    createGetRequest("misc/update-database", (res) => {
      if(res.status == 200) {
        updatingResultMessage = res.data.message + ", page will be reloaded after 3 seconds";

        setTimeout(() => {
            window.location.reload();
        }, 3000);

      } else {
        updatingResultMessage = res.data.message;
      }
    }).catch((res) => {
        updatingResultMessage = res.message.response.data.message;
        updatingDatabaseFailed = true;
    }).finally((res) => {
        isUpdatingDatabase = false;
              
    })
  }

  let isActivePage = [false, false, false, false];
</script>

{#if invoize.need_update_database}
  <main class="py-3 mx-auto max-w-3xl print:py-0" data-theme="wintry">
    <Card.Root>
      <Card.Header>
        <Card.Title>
          <h2 class="text-lg font-semibold text-gray-800">Update Required</h2>
        </Card.Title>
        <Card.Description>
          <span class="text-sm text-gray-600">Invoize Database Update Required</span>
        </Card.Description>
      </Card.Header>
      <Card.Content>
        <div class="space-y-4">
          <p class="text-base text-gray-700">
            Invoize has been updated! Before you continue, we need to upgrade your database to the latest version.
          </p>
          <div>
            <small class="text-xs text-gray-500">
              We recommend backing up your database to prepare for any potential issues. Click the button below to proceed with the update.
            </small>
          </div>
          {#if updatingResultMessage}
            <div class="{updatingDatabaseFailed ? 'text-red-500' : 'text-success'} text-md">
                {updatingResultMessage}
            </div>
          {/if}
          <Button class="w-full md:w-auto bg-blue-600 text-white px-6 py-3 rounded-md font-medium hover:bg-blue-700 transition duration-300" on:click={updateDatabase} disabled={isUpdatingDatabase}>
            {#if isUpdatingDatabase}
              <Loader2 class="w-4 h-4 animate-spin me-2"/>
              Updating...
            {:else}
              <Database class="w-4 h-4 me-2" />
              Update Database
            {/if}
          </Button>
        </div>
      </Card.Content>
    </Card.Root>
</main> 
{:else}
  <ProPopup bind:open={$isProPopupOpen} />

  <main class="py-3 mx-auto max-w-7xl print:py-0" data-theme="wintry">
    {#if $activeAutomaticPaypalType === 'sandbox'}
      <div class="mb-4 print:hidden">
        <Alert variant="warning">
          <AlertTitle>Your PayPal payment is being processed in Sandbox mode</AlertTitle>
          <AlertDescription>
            Your automatic payment is currently being tested in PayPal's sandbox environment. No real transactions are taking place.
          </AlertDescription>
        </Alert>
      </div>
    {/if}
    <Router {routes} />
    <Toaster
      position="top-right"
      containerStyle=" font-size: 11pt; margin-top: 30px;"
      toastOptions={{
        style: `
          color: #4100ae;
          border: 1px solid #4100ae;
          box-shadow: 6px 6px 12px 0px gray;
        `,
      }} />
  </main>

  <!-- Navbar for small screen -->
  <div
    class="fixed bottom-0 left-0 w-full md:hidden flex flex-nowrap justify-around rounded-t-xl bg-primary-700 text-background z-50 print:hidden">
    <button
      class="w-full duration-75 rounded-tl-xl shadow-none py-4 flex justify-center"
      on:click={() => handleActiveNav("#/", 0)}>
      <div class="p-2 {isActivePage[0] && 'w-fit bg-background rounded-xl'}">
        <LayoutDashboard class={isActivePage[0] && "text-primary"} />
      </div>
    </button>
    <button
      class="w-full duration-75 rounded-none shadow-none py-4 flex justify-center"
      on:click={() => handleActiveNav("#/invoices", 1)}>
      <div class="p-2 {isActivePage[1] && 'w-fit bg-background rounded-xl'}">
        <ScrollText class={isActivePage[1] && "text-primary"} />
      </div>
    </button>
    <button
      class="w-full duration-75 rounded-none shadow-none py-4 flex justify-center"
      on:click={() => handleActiveNav("#/receipts", 2)}>
      <div class="p-2 {isActivePage[2] && 'w-fit bg-background rounded-xl'}">
        <Receipt class={isActivePage[2] && "text-primary"} />
      </div>
    </button>
    <button
      class="w-full duration-75 rounded-tr-xl shadow-none py-4 flex justify-center"
      on:click={() => handleActiveNav("#/setting", 3)}>
      <div class="p-2 {isActivePage[3] && 'w-fit bg-background rounded-xl'}">
        <Settings class={isActivePage[3] && "text-primary"} />
      </div>
    </button>
  </div>
{/if}
