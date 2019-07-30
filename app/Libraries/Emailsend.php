<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Email send using phpmailer library
 *
 * @author     Arpan Tanna <arpan.ec24@gmail.com>
 *
 */
class Emailsend {

    private $mail;

    function __construct(){
        $this->mail = new PHPMailer();
    }

    /*
     * Get email content from view file
     *
     * @params \App\Models\Lead (array) $lead
     * @params \App\Models\Emailinfo $emailinfo_data
     * @params string $content
     * @params string $view
     * @return string
     */
    public function getcontent($lead, $emailinfo_data, $content, $view)
    {
        $email_data = ['ei_key' => $emailinfo_data->ukey, 'lead_key' => $lead['ukey'], 'content' => $content];
        $email_content = view($view, $email_data)->render();

        return $email_content;
    }

    /*
     * Send Email
     *
     * @params string $subject
     * @params string $content
     * @params string $to - email address
     * @return boolean $return
     */
    public function send($subject, $content, $to)
    {
        $return = 0;

        try{
            $this->mail->isSMTP();
            $this->mail->CharSet = 'utf-8';
            //$this->mail->SMTPDebug = 3;
            $this->mail->SMTPAuth =true;
            $this->mail->Host = env('MAIL_HOST');
            $this->mail->SMTPSecure = env('MAIL_ENCRYPTION');
            $this->mail->Port = env('MAIL_PORT');
            $this->mail->Username = env('MAIL_USERNAME');
            $this->mail->Password = env('MAIL_PASSWORD');
            $this->mail->Subject = $subject;
            $this->mail->MsgHTML($content);
            $this->mail->setFrom(env('MY_EMAIL'), env('MY_NAME'));
            $this->mail->addAddress($to);
            if($this->mail->send()) {
                $return = 1;
            }
        }
        catch(phpmailerException $e){}
        catch(Exception $e){}

        return $return;
    }


}

?>
