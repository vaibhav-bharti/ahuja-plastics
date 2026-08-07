<?php

namespace App\Support;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class EmployeeCredentials
{
    public static function resetAction(): Action
    {
        return Action::make('resetCredentials')
            ->label('Login Credentials')
            ->icon('heroicon-o-key')
            ->color('warning')
            ->modalHeading('Generate employee login credentials')
            ->modalDescription('The password is shown only now. Copy it or open WhatsApp before saving.')
            ->schema([
                TextInput::make('email')
                    ->email()
                    ->default(fn (User $record): ?string => $record->email)
                    ->required()
                    ->unique('users', 'email', ignoreRecord: true),

                TextInput::make('password')
                    ->label('New Password')
                    ->default(fn (): string => Str::password(12, true, true, false))
                    ->password()
                    ->revealable()
                    ->copyable()
                    ->required()
                    ->minLength(8)
                    ->helperText('Copy this password now. It cannot be viewed again after saving.'),
            ])
            ->action(function (User $record, array $data): void {
                $record->update([
                    'email' => $data['email'],
                    'password' => $data['password'],
                ]);

                self::sendReadyNotification($record->fresh(), $data['password']);
            });
    }

    public static function whatsAppAction(): Action
    {
        return self::resetAction()
            ->label('WhatsApp')
            ->icon('heroicon-o-chat-bubble-left-right')
            ->color('success')
            ->modalHeading('Share login credentials on WhatsApp')
            ->modalSubmitActionLabel('Save & open WhatsApp');
    }

    public static function sendReadyNotification(User $employee, string $password): void
    {
        $notification = Notification::make()
            ->success()
            ->title('Login credentials are ready')
            ->body('Password has been saved securely. Copy it now or open WhatsApp to share the credentials.');

        if ($url = self::whatsAppUrl($employee, $password)) {
            $notification->actions([
                Action::make('openWhatsApp')
                    ->label('Open WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->url($url, shouldOpenInNewTab: true),
            ]);
        } else {
            $notification->body('Password has been saved securely. Add a valid phone number to enable WhatsApp sharing.');
        }

        $notification->send();
    }

    private static function whatsAppUrl(User $employee, string $password): ?string
    {
        $phone = preg_replace('/\D+/', '', (string) $employee->phone);

        if (blank($phone)) {
            return null;
        }

        if (strlen($phone) === 10) {
            $phone = '91'.$phone;
        }

        $message = sprintf(
            "Hello %s,\n\nYour Ahuja Plastics login credentials are:\nEmail: %s\nPassword: %s\n\nPlease change your password after your first login.",
            $employee->name,
            $employee->email,
            $password,
        );

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($message);
    }
}
