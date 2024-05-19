<?php

namespace App\Console\Commands;

use App\Models\Signature;
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
        $id = 2;
        $params = [
            'email' => [
                'content' => 'content',
                'expire_date' => 'Mon Apr 08 2024 00:00:00 GMT+0700 (GMT+07:00)',
                'subject' => "SENDER_NAME (SENDER_EMAIL_ID) Needs Your Signature for the DOCUMENT_NAME"
            ],
            'signatures' => [
                [
                    'data' => '123',
                    'position' => [
                        'height' => 60,
                        'left' => 676,
                        'top' => 270,
                        'width' => 100
                    ],
                    'type' => Signature::TYPE_TEXT,
                    'page' => 1,
                    'scale' => 1.5
                ],
            ],
            'users' => [
                'name' => 'Nguyen Huu Huan',
                'email' => 'gundamakp01@gmail.com',
                'type' => 9,
            ],
            'canvas' => [
                'width' => 918,
                'height' => 1188,
            ]
        ];
        $documentService->sign(9, $params['signatures'], $params['canvas']);
    }
}
