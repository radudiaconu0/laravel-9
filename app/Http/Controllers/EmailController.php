<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmail;
use App\Jobs\SendEmailJob;
use App\Models\User;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;
use App\Utilities\ElasticsearchHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class EmailController extends Controller
{
    // TODO: finish implementing send method
    public function send(User $user, Request $request)
    {
        $token = $request->input('api_token');
        $this->validateToken($token, $user->id);
        $validator = Validator::make($request->all(), [
            'emails' => 'present|array',
            "emails.0" => 'required',
            'emails.*.body' => 'required|string',
            'emails.*.subject' => 'required|string',
            'emails.*.email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        $emails = $request->input('emails');
        foreach ($emails as $email) {
            SendEmailJob::dispatch($email);
        }
        return response()->json([
                'message' => 'Emails are scheduled to be sent successfully',
            ],
        );
    }

    //  TODO - BONUS: implement list method
    public function list(Request $request)
    {

        $page = $request->input('page') ?? 1;
        $perPage = $request->input('per_page') ?? 15;
        $elasticsearchHelper = new ElasticsearchHelper();
        $results = $elasticsearchHelper->listEmails($page, $perPage);
        return response()->json([
            'message' => 'Emails are listed successfully',
            'data' => $results,
        ]);
    }

    public function validateToken($api_token, $user_id)
    {
        $token = PersonalAccessToken::findToken($api_token);
        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user = $token->tokenable;
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->id != $user_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}
