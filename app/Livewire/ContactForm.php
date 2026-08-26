<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $message = '';

    public string $website = '';

    public function submit(): void
    {
        if ($this->website !== '') {
            return;
        }

        $data = $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        Mail::raw(
            "From: {$data['name']} <{$data['email']}>\n\n{$data['message']}",
            function ($mail) use ($data) {
                $mail->to(config('mail.from.address', 'hello@jpxstudios.com'))
                    ->replyTo($data['email'], $data['name'])
                    ->subject('jenyapix inquiry — '.$data['name']);
            }
        );

        $this->reset(['name', 'email', 'message']);
        session()->flash('status', 'Got it. I will reply within a day.');
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
