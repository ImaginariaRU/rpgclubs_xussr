<?php

namespace RPGCAtlas\Units;

use Psr\Log\LoggerInterface;
use RPGCAtlas\AbstractClass;

class Mailer extends AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

    public function mailToAdmin($subject, $text)
    {
        $target = '';
    }

    public function mailToApplicant($email)
    {

    }

    public function _()
    {
        $mail = new Message;
        $mail->setFrom('Autobot <admin@ролевыеклубы.рф>')
            ->addTo($destination)
            ->setSubject($subject)
            ->setBody($text);

        $mailer = new SendmailMailer;
        $mailer->send($mail);
    }

}