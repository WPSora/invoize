<?php

namespace Invoize\Migration\Model;

use Carbon\Carbon;
use Invoize\Interfaces\MigrationInterface;
use Invoize\Models\Invoice;
use Invoize\Models\Invoice\ActionHistory;
use Invoize\Models\Invoice\Business;
use Invoize\Models\Invoice\Client;
use Invoize\Models\Invoice\Currency;
use Invoize\Models\Invoice\Discounts;
use Invoize\Models\Invoice\Payment\Payments;
use Invoize\Models\Invoice\Products;
use Invoize\Models\Invoice\Reminders;
use Invoize\Models\Invoice\Taxes;
use Invoize\Models\Invoice\User;

class InvoiceMigrationModel implements MigrationInterface
{
    private int $primaryKey; // id from previous invoice
    private string $id;
    private string $prefix;
    private int $number;
    private string $token;
    private Business $business;
    private Client $client;
    private string $tab;
    private string $paymentStatus;
    private string $invoiceStatus;
    private string $invoiceDate;
    private string $orderDate;
    private string $dueDate;
    private Products $products;
    private Currency $currency;
    private ?string $note;
    private ?string $terms;
    private ?string $internalNote;
    private float $subtotal;
    private float $total;
    private ?Taxes $taxes;
    private ?Discounts $discounts;
    private ?Reminders $reminders;
    private ?User $user;
    private Payments $payments;
    private ActionHistory $actionHistory;
    private ?int $wcOrderId;
    private bool $isEmailSent;

    public function __construct()
    {
        $this->primaryKey = 0;
        $this->id = '';
        $this->prefix = '';
        $this->number = 1;
        $this->token = '';
        $this->business = Business::instance();
        $this->client = Client::instance();
        $this->tab = Invoice::UNPAID;
        $this->paymentStatus = Invoice::UNPAID;
        $this->invoiceStatus = Invoice::ACTIVE;
        $this->invoiceDate = Carbon::now()->format('Y-m-d');
        $this->orderDate = Carbon::now()->format('Y-m-d');
        $this->dueDate = Carbon::now()->addDays(7)->format('Y-m-d');
        $this->products = Products::instance();
        $this->currency = Currency::instance()->setContent(['name' => 'USD']);
        $this->subtotal = 0;
        $this->total = 0;
        $this->discounts = Discounts::instance();
        $this->taxes = Taxes::instance();
        $this->user = User::instance()->setContent(wp_get_current_user());
        $this->payments = Payments::instance();
        $this->reminders = Reminders::instance();
        $this->actionHistory = ActionHistory::instance();
        $this->note = null;
        $this->terms = null;
        $this->internalNote = null;
        $this->wcOrderId = null;
        $this->isEmailSent = false;
    }

    public static function instance(): self
    {
        return new static;
    }

    public function create(): int
    {
        $id = Invoice::generateInvoice($this->getContent(), false);
        return $id;
    }

    public function getContent(): array
    {
        return [
            'id' => $this->id,
            'prefix' => $this->prefix,
            'number' => $this->number,
            'token' => $this->token,
            'business' => $this->business->getContent(),
            'client' => $this->client->getContent(),
            'paymentStatus' => $this->paymentStatus,
            'invoiceStatus' => $this->invoiceStatus,
            'tab' => $this->tab,
            'orderDate' => $this->orderDate,
            'invoiceDate' => $this->invoiceDate,
            'dueDate' => $this->dueDate,
            'products' => $this->products->getContent(),
            'payments' => $this->payments->getContent(),
            'currency' => $this->currency->getContent(),
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'discounts' => $this->discounts->getContent(),
            'taxes' => $this->taxes->getContent(),
            'reminders' => $this->reminders->getContent(),
            'note' => $this->note,
            'terms' => $this->terms,
            'internalNote' => $this->internalNote,
            'user' => $this->user->getContent(),
            'actionHistory' => $this->actionHistory->getContent(),
            'wc_order_id' => $this->wcOrderId,
            'is_email_sent' => $this->isEmailSent,
        ];
    }

    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    public function setPrimaryKey($primaryKey): self
    {
        $this->primaryKey = $primaryKey;
        return $this;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setToken(string $token): self
    {
        if ($token) {
            $this->token = $token;
        }
        return $this;
    }

    public function setBusiness(Business $business): self
    {
        $this->business = $business;
        return $this;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function setPaymentStatus(string $paymentStatus): self
    {
        $this->paymentStatus = $paymentStatus;
        return $this;
    }

    public function setInvoiceStatus(string $invoiceStatus): self
    {
        $this->invoiceStatus = $invoiceStatus;
        return $this;
    }

    public function setTab(string $tab): self
    {
        $this->tab = $tab;
        return $this;
    }

    public function setInvoiceDate(string $invoiceDate): self
    {
        $this->invoiceDate = $invoiceDate;
        return $this;
    }

    public function setOrderDate(string $orderDate): self
    {
        $this->orderDate = $orderDate;
        return $this;
    }

    public function setDueDate(string $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function setDiscounts(Discounts $discounts): self
    {
        $this->discounts = $discounts;
        return $this;
    }

    public function setTaxes(Taxes $taxes): self
    {
        $this->taxes = $taxes;
        return $this;
    }

    public function setSubtotal(float $subtotal): self
    {
        $this->subtotal = $subtotal;
        return $this;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function setTerms(string $terms): self
    {
        $this->terms = $terms;
        return $this;
    }

    public function setInternalNote(string $internalNote): self
    {
        $this->internalNote = $internalNote;
        return $this;
    }

    public function setReminders(Reminders $reminders): self
    {
        $this->reminders = $reminders;
        return $this;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function setPayments(Payments $payments): self
    {
        $this->payments = $payments;
        return $this;
    }

    public function setActionHistory(ActionHistory $actionHistory): self
    {
        $this->actionHistory = $actionHistory;
        return $this;
    }

    public function setProducts(Products $products): self
    {
        $this->products = $products;
        return $this;
    }

    public function setCurrency(string $name, ?string $symbol = null): self
    {
        $this->currency->setName($name)->setSymbol($symbol);
        return $this;
    }

    public function setWcOrderId(?int $wcOrderId = null): self
    {
        $this->wcOrderId = $wcOrderId;
        return $this;
    }

    public function setIsEmailSent(bool $isEmailSent): self
    {
        $this->isEmailSent = $isEmailSent;
        return $this;
    }
}
