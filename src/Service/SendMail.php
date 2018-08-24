<?php

namespace App\Service;

class SendMail
{

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function mail($subject, $admin, $member, $message)
    {
        $mail = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($admin)
            ->setTo($member)
            ->setBody($message,'text/html')
        ;
        try {
            $this->mailer->send($mail);
//            dump($this->mailer->send($mail));exit;
        }
        catch (\Exception $e){
            die($e);
        }
    }

}