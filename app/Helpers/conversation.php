<?php
if (!function_exists('conversation')) {
    function conversation($targetUser){
        $conversations = auth()
            ->user()
            ->conversation()
            ->whereHas('conversationUsers', function ($q) use($targetUser){
                $q->where('user_id', $targetUser);
            })->first();
        if (empty($conversations)) {
            $user = \App\Models\User::find($targetUser);
            $conversation = \App\Models\Conversation::create([
                'name' => \Illuminate\Support\Str::random(20),
                'description' => \Illuminate\Support\Str::random(20),
                'creator' => auth()->user()->id,
            ])->conversationUsers()->sync([$user->id, auth()->user()->id]);
            $conversations = auth()
                ->user()
                ->conversation()
                ->whereHas('conversationUsers', function ($q) use($targetUser){
                    $q->where('user_id', $targetUser);
                })->first();
        }
        return $conversations;
    }

    function insertIntoMailBox(Array $data){
        return \App\Models\MailBox::create($data);
    }

    function insertIntoMailBoxAttachment(\App\Models\MailBox $mailBox, Array $data){
        return $mailBox->attachments()->saveMany($data);
    }
}
