<?php

namespace App\Http\Controllers\Registration;

use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

use Validator;
use Hash;

class PasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth/register/password/reset');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        extract($_GET);

        // Verify if url contain login and token
        //--------------------------------------
        if(isset($login) && isset($token))
        {

            // Check in DB if exist
            //---------------------
            $member = Member::where('login', $login)->where('token', $token)->count();


            // If validator pass, show the page to define password, and store login in session
            //--------------------------------------------------------------------------------
            if(!empty($member))
            {

                $request->session()->put('login', $login);


                return view('auth/register/password/definePassword');
            }
            else
            {
                dd('Nope.jpg');
            }
        }
        else
        {
            dd('Nope.gif');
        }


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        extract($_POST);


        // Check form
        //-----------
        $validator = Validator::make($request->all(),
            [
                'email'     => 'required'
            ]);
        /////////////////////////////////////////////


        // Verify if login and activation code are corresponding with the DB
        //------------------------------------------------------------------
        $validator->after(function($validator)
        {

            extract($_POST);

            $member = Member::where('email', $email)->count();

            if(empty($member))
            {
                $validator->errors()->add('email', "Cet e-mail n'existe pas.");
            }
        });
        /////////////////////////////////////////////


        // Display errors messages, return to password page
        //-------------------------------------------------
        if($validator->fails())
        {
            return back()->withInput()->withErrors($validator);
        }
        /////////////////////////////////////////////


        // If validator pass, add token in database and send email
        //--------------------------------------------------------------------------------

        $member = Member::where('email', $email)->get();
        $member = $member[0];
        //Generate the token
        $validationCode     = str_random(20);

        $member->token = $validationCode;
        $member->save();

        // Send email to the user to choose password
        //-------------------------------------------------
        $mailMember = $member->email;
        Mail::send('emails.user.passwordReset', ['last_name'  => $member->last_name,
            'first_name' => $member->first_name,
            'login'      => $member->login,
            'token'      => $member->token],
            function ($message) use($mailMember)
            {
                $message->to($mailMember)->subject('Réinitialisation du mot de passe');
            });
        /////////////////////////////////////////////

        return view('auth/login/login')->with('message', 'Un email vous a été envoyé.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return view('/auth/passwords/reset');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        extract($_POST);

        if(!$request->session()->has('login'))
        {
            dd('Bien essayé :)');
        }

        // Check form
        //-----------
        $validator = Validator::make($request->all(),
            [
                'password'                  => 'required|confirmed|min:6',
                'password_confirmation'     => 'required'
            ]);
        /////////////////////////////////////////////


        // Verify if password are strong enough
        //------------------------------------------------------------------
        $validator->after(function($validator)
        {
            extract($_POST);

            if(!preg_match('/(([A-Z]+|[a-z]+)[0-9]+)|([0-9]+([a-z]+|[A-Z]))/', $password))
            {
                $validator->errors()->add("password", "Le mot de passe n'est pas assez fort.");
            }
        });
        /////////////////////////////////////////////


        // Display errors messages, return to password page
        //-------------------------------------------------
        if($validator->fails())
        {
            return back()->withInput()->withErrors($validator);
        }
        /////////////////////////////////////////////


        //Update the member in the DB with the password and remove the token
        //------------------------------------------------------------------
        $member = Member::where('login', $request->session()->get('login'))->get();


        // Test if the member exists
        //--------------------------
        if(empty($member))
        {
            return back();
        }

        // Insert the password
        //--------------------
        $member[0]->UpdatePassword($password);
        $member[0]->save();
        ///////////////////////


        // Disabled possibility to redifine password through to /password/create
        //----------------------------------------------------------------------
        $request->session()->forget('login');

        return view("auth/register/password/passwordDefined");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
