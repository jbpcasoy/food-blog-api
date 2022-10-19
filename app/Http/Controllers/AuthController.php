<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a tenant user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate(
            [
                "name" => "required|string",
                "email" => "required|string",
                "password" => "required|string|confirmed",
                "domain" => "required|string|unique:tenants,id"
            ]
        );

        $domain = $request->get("domain");
        $tenant = Tenant::create(['id' => $domain]);
        $tenant->domains()->create(['domain' => $domain . "." . env('HOSTNAME')]);

        $user_data = $request->only(["name", "email", "password"]);
        $user_data["password"] = bcrypt($user_data["password"]);

        $tenant->run(function () use ($user_data) {
            User::create($user_data);
        });

        return response(["success" => true, "data" => ["tenant" => $tenant,], "errorMessage" => null]);
    }

    /**
     * Login a user to get a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $fields = $request->validate(
            [
                "email" => "required|string",
                "password" => "required|string",
            ]
        );

        // Get user with matching email
        $user = User::where("email", $fields["email"])->first();

        // Check password
        if (!$user || !Hash::check($fields["password"], $user->password)) {
            return response(["success" => false, "data" => null, "errorMessage" => "Wrong credentials"]);
        }

        $token = $user->createToken(env("TOKEN_SECRET"))->plainTextToken;

        $response = [
            "success" => true,
            "data" => [
                "user" => $user,
                "token" => $token
            ],
            "errorMessage" => null
        ];

        return response($response);
    }

    /**
     * Get currently logged in user.
     */
    public function user(Request $request)
    {
        $user =  $request->user();
        return $user;
    }
}
