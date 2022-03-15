<?php

namespace App\Console\Commands;

use App\Events\AdminMailEvent;
use App\Events\MailReceivedEvent;
use App\Models\MailBox;
use App\Models\MailBoxAttachment;
use Illuminate\Console\Command;
use Webklex\IMAP\Commands\ImapIdleCommand;
use Webklex\PHPIMAP\Message;

class NewMailCommand extends ImapIdleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailbox:received';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Receive new mail';

    protected $account = "default";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @author MD ANWAR JAHID <ajr.jahid@gmail.com>
     * Callback used for the idle command and triggered for every new received message
     * @param Message $message
     */
    public function onNewMessage(Message $message){
        $this->info('New Message Appeared');
        $attachments = $message->getAttachments();
        $sender = explode('<',$message->sender);
        $mail = [
            'to' => $message->to,
            'sender' => rtrim($sender[0], ' '),
            'replay_to' => rtrim($sender[1], '>'),
            'cc' => $message->cc,
            'bcc' => $message->bcc,
            'from' => $message->from,
            'folder' => $message->getFolderPath(),
            'subject' => $message->subject,
            'html_body' => $message->getHTMLBody(),
            'text_body' => $message->getTextBody(),
            'received_at' => $message->date
        ];
        $mailBox = MailBox::create($mail);
        $this->info($message->sender);
        $attachmentsArray = [];
        foreach ($attachments as $attachment){
            $attributes = $attachment->getAttributes();
            $fileName = $attributes['id'].'.'.$attachment->getExtension();
            $this->info($fileName);
            $attachment->save(public_path('mail_attachment').'/', $fileName);
            $attachmentsArray[] = new MailBoxAttachment([
                'content_type' => $attributes['content_type'],
                'content' => "mail_attachment/{$fileName}",
                'type' => $attributes['type'],
                'img_src' => $attributes['img_src'],
                'size' => $attributes['size'],
            ]);
        }
        if (count($attachmentsArray) > 0) {
            $mailBox->attachments()->saveMany($attachmentsArray);
        }
        broadcast(new MailReceivedEvent($mailBox));
        broadcast(new AdminMailEvent($mailBox));
    }

}
