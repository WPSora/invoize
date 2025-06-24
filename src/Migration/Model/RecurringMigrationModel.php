<?php

namespace Invoize\Migration\Model;

use Carbon\Carbon;
use Invoize\Interfaces\MigrationInterface;
use Invoize\Models\Invoice;
use Invoize\Models\Invoice\Business;
use Invoize\Models\Invoice\Client;
use Invoize\Models\Invoice\ActionHistory;
use Invoize\Models\Invoice\Currency;
use Invoize\Models\Invoice\Discounts;
use Invoize\Models\Invoice\Payment\Payments;
use Invoize\Models\Invoice\Products;
use Invoize\Models\Invoice\Reminders;
use Invoize\Models\Invoice\Taxes;
use Invoize\Models\Invoice\User;
use Invoize\Models\Recurring;
use Invoize\Models\Recurring\Recurring as RecurringModel;


class RecurringMigrationModel implements MigrationInterface
{
    private int $primaryKey;
    private string $id;
    private string $token;
    private Business $business;
    private Client $client;
    private int $dueDateInterval;
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
    private RecurringModel $recurring;
    private Payments $payments;
    private ActionHistory $actionHistory;

    private function __construct()
    {
        $this->primaryKey = 0;
        $this->id = '';
        $this->token = '';
        $this->business = Business::instance();
        $this->client = Client::instance();
        $this->dueDateInterval = 7;
        $this->products = Products::instance();
        $this->currency = Currency::instance()->setContent(['name' => 'USD']);
        $this->subtotal = 0;
        $this->total = 0;
        $this->discounts = Discounts::instance();
        $this->taxes = Taxes::instance();
        $this->user = User::instance()->setContent(wp_get_current_user());
        $this->payments = Payments::instance();
        $this->reminders = Reminders::instance();
        $this->recurring = RecurringModel::instance();
        $this->actionHistory = ActionHistory::instance();
    }


    public static function instance(): self
    {
        return new self;
    }


    public function create()
    {
        $res = Recurring::generateInvoiceTemplate($this->getContent());
        return $res['id'];
    }


    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }


    public function getContent()
    {
        return [
            'id' => $this->id,
            'token' => $this->token,
            'business' => $this->business->getContent(),
            'client' => $this->client->getContent(),
            'dueDateInterval' => $this->dueDateInterval,
            'products' => $this->products->getContent(),
            'payments' => $this->payments->getContent(),
            'currency' => $this->currency->getContent(),
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'discounts' => $this->discounts->getContent(),
            'taxes' => $this->taxes->getContent(),
            'reminder' => $this->reminders->getContent(),
            'recurring' => $this->recurring->getContent(),
            'note' => $this->note,
            'terms' => $this->terms,
            'internalNote' => $this->internalNote,
            'user' => $this->user->getContent(),
            'isCreateInvoice' => false,
            'isSendEmail' => false,
            'actionHistory' => $this->actionHistory->getContent(),
        ];
    }

    public function setPrimaryKey(string $key): self
    {
        $this->primaryKey = $key;
        return $this;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
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

    public function setDueDateInterval(int $interval): self
    {
        $this->dueDateInterval = $interval;
        return $this;
    }

    public function setProducts(Products $products): self
    {
        $this->products = $products;
        return $this;
    }

    public function setPayments(Payments $payments): self
    {
        $this->payments = $payments;
        return $this;
    }

    public function setCurrency(string $name, ?string $symbol = null): self
    {
        $this->currency->setName($name)->setSymbol($symbol);
        return $this;
    }

    public function setActionHistory(ActionHistory $actionHistory): self
    {
        $this->actionHistory = $actionHistory;
        return $this;
    }

    public function setUse(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function setReminders(Reminders $reminders): self
    {
        $this->reminders = $reminders;
        return $this;
    }

    public function setRecurring(RecurringModel $recurring): self
    {
        $this->recurring = $recurring;
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
}
