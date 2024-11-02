<?php

namespace RPGCAtlas\Units;

use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use Psr\Log\LoggerInterface;
use RPGCAtlas\AbstractClass;

class Mailer extends AbstractClass
{
    private string $to = '';
    private string $subject = '';
    private string $text = '';

    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

    public function mailToAdmin($from_email = '')
    {
        $this->to = _env('MAILER.ADMIN_MAIL', '');
        $this->subject = sprintf("Создана заявка на добавление POI (Email: %s)", $from_email);
        $this->text = $this->makeMessageToAdmin();
    }

    public function mailToApplicant($email)
    {
        $this->to = $email;
        $this->subject = "Ваша заявка на добавление клуба принята";
        $this->text = $this->makeMessageToApplicant();
    }

    private function makeMessageToAdmin()
    {
        return '';
    }

    private function makeMessageToApplicant()
    {
        return '';
    }

    public function send()
    {
        $mail = new Message;
        $mail->setFrom('NoReplay <noreply@ролевыеклубы.рф>')
            ->addTo($this->to)
            ->setSubject($this->subject)
            ->setHtmlBody($this->text);

        $mailer = new SmtpMailer([
            'host'      =>  'smtp.gmail.com',
            'username'  =>  _env('MAILER.SMTP.USERNAME', ''),
            'password'  =>  _env('MAILER.SMTP.PASSWORD', ''),
            'secure'    =>  true,
        ]);
        $mailer->send($mail);
    }

}