<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Import;
use App\Models\Supplier;

class OrderSupplierMail extends Mailable
{
    use Queueable, SerializesModels;

    public $import;
    public $supplier;
    public $products;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Import $import, Supplier $supplier, $products)
    {
        $this->import = $import;
        $this->supplier = $supplier;
        $this->products = $products;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Đơn Đặt Hàng Mới - ' . $this->import->code)
                    ->view('emails.order_supplier');
    }
}
