<?php

namespace App\Auth\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.guest', ['title' => 'Login'])]
class Login extends Component
{
    #[Validate('required|email')]
    public $email = '';

    #[Validate('required')]
    public $password = '';

    public $remember = false;

    public function mount()
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }
    }

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            return $this->redirect('/contents', navigate: true);
        }

        $this->addError('email', trans('auth.failed'));
        flash()->error('Email atau password salah.');
    }

    public function render()
    {
        return view('auth.login');
    }
}
