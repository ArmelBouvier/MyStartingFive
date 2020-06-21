<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\Register;
use App\Model\Nbateam;
use App\Providers\RouteServiceProvider;
use App\Model\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;


    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $nbaTeams = Nbateam::all();
        return view('auth.register')->with('nbaTeams', $nbaTeams);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));


        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new Response('', 201)
            : redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        // Vérification du formualire
        return Validator::make($data, [

            'lastname'          => ['nullable', 'string', 'max:255'],
            'firstname'         => ['nullable', 'string', 'max:255', 'nullable'],
            'pseudo'            => ['required', 'string', 'max:255', 'unique:users'],
            'email'             => ['required', 'string', 'max:255', 'email', 'unique:users'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
            'birthday'          => ['nullable', 'date',],
            'nbateam_id'        => ['nullable', 'string', 'max:255', ],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return bool
     */
    protected function create(array $data)
    {
        $user = User::create([
            'lastname'      => $data['lastname'],
            'firstname'     => $data['firstname'],
            'pseudo'        => $data['pseudo'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'birthday'      => $data['birthday'],
            'nbateam_id'    => $data['nbateam_id'],
        ]);


        // On ajoute le role
        $user->roles()->sync([2]);


        // Envoi de l'email via la fonction mail()

        $title = 'Confirmation d\'inscription';

        $content = 'Hello ' . '<b>' . $user['pseudo'] . '!' . '</b>' . '<br>' . '<br>' .
            'Bienvenue dans la grande famille des Fivers!<br> <br> Tu as utilisé l\'adresse mail :' . '<br> <br>' . '<b>'  . $user['email'] . '</b>' . '<br> <br>'
            . 'pour t\'inscrire, et nous t\'en remercions 😉<br> <br> Bonne route vers la gloire ! ⛹🏻⛹🏽‍ 🏆';

        Mail::to($user['email'])->send(new Register($title, $content));

        return true;
    }
}
