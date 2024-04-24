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
        $id = 25;
        $params = [
            'email' => [
                'content' => 'content',
                'expire_date' => 'Mon Apr 08 2024 00:00:00 GMT+0700 (GMT+07:00)',
                'subject' => "SENDER_NAME (SENDER_EMAIL_ID) Needs Your Signature for the DOCUMENT_NAME"
            ],
            'signatures' => [
                [
                    'data' => [
                        'id' => 62,
                        'path' => 'http://localhost:8868/signatures/iwtk6ph2UY8YGUlKZsXB8NwFx6g4hrD0zcuSDWKl.png',
                        'name' => 'storage/signature.png'
                    ],
                    'position' => [
                        'height' => 60,
                        'left' => 638,
                        'top' => 112,
                        'width' => 100
                    ],
                    'type' => Signature::TYPE_IMAGE,
                    'page' => 1,
                    'scale' => 1.5
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
