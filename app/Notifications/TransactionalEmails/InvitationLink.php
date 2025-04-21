<?php

namespace App\Notifications\TransactionalEmails;

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Notifications\Channels\TransactionalEmailChannel;
use App\Notifications\CustomEmailNotification;
use Illuminate\Notifications\Messages\MailMessage;

class InvitationLink extends CustomEmailNotification
{
    public function via(): array
    {
        return [TransactionalEmailChannel::class];
    }

    public function __construct(public User $user, public bool $isTransactionalEmail = true)
    {
        $this->onQueue('high');
    }

    public function toMail(): MailMessage
    {
        $invitation      = TeamInvitation::whereEmail($this->user->email)->first();
        $invitationTeam  = Team::find($invitation->team->id);

        $mail = new MailMessage;
        $mail->subject('T2iD PaaS: Convite para '.$invitationTeam->name);
        $mail->view('emails.invitation-link', [
            'team'            => $invitationTeam->name,
            'email'           => $this->user->email,
            'invitation_link' => $invitation->link,
        ]);

        return $mail;
    }
}
