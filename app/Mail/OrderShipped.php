<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderShipped extends Mailable
{
    use Queueable, SerializesModels;

    protected $companyPermissionData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $companyPermissionData)
    {
        $this->companyPermissionData = $companyPermissionData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('example@example.com')
                    ->view('emails.company')
                    ->with([
                        'companyPermission' => $this->companyPermissionData['companyPermission'],
                        'userPermission' => $this->companyPermissionData['userPermission'],
                        'userPermissionLog' => $this->companyPermissionData['userPermissionLog'],
                    ]);
    }
}
