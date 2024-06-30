<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Exceptions\UnprocessableContentException;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct(public UserService $userService)
    {
    }

    public function show($id)
    {
        return response()->ok(new UserResource($this->userService->getById($id)));
    }

    public function getByEmail(Request $request)
    {
        $user = $this->userService->getByEmail($request->email); 
        if (!$user) {
            throw new UnprocessableContentException('The selected email does not exist in our records.');
        }
        return response()->ok(new UserResource($user));
    }

    public function verifyPrivateKey(Request $request)
    {
        // Kiểm tra xem file đã được upload hay chưa
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        // Đọc nội dung file
        $content = file_get_contents($file->getRealPath());

        // Kiểm tra định dạng file
        if (preg_match('/-----BEGIN PRIVATE KEY-----(.*?)-----END PRIVATE KEY-----/s', $content, $matches)) {
            $privateKey = trim($matches[1]);
            if ($privateKey === auth()->user()->private_key) {
                return response()->ok(['private_key' => $privateKey]);
            } else {
                return response()->error(
                    Response::HTTP_FORBIDDEN,
                    'Private key incorrect.'
                );
            }
        }
        return response()->ok(['error' => 'Invalid file format'], 400);
    }
}
