<?php

namespace App\Console\Commands;

use App\Services\DocumentService;
use Illuminate\Console\Command;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(DocumentService $documentService)
    {
        $id = 25;
        $params = [
            'email' => [
                'content' => 'content',
                'expire_date' => 'Mon Apr 08 2024 00:00:00 GMT+0700 (GMT+07:00)',
                'subject' => "SENDER_NAME (SENDER_EMAIL_ID) Needs Your Signature for the DOCUMENT_NAME"
            ],
            'signatures' => [
                [
                    'height' => 40,
                    'left' => 1035,
                    'top' => 90,
                    'type' => 'name',
                    'width' => 100
                ],
                [
                    'height' => 40,
                    'left' => 1047,
                    'top' => 983,
                    'type' => 'name',
                    'width' => 100
                ],
            ],
            'users' => [
                'name' => 'Nguyen Huu Huan',
                'email' => 'gundamakp01@gmail.com',
                'type' => 9,
            ]
        ];
        $documentService->sign($id, $params);
    }
}
