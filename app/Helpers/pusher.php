<?php
if (!function_exists('pusherInstance')) {
    /**
     * @throws \Pusher\PusherException
     */
    function pusherInstance(){
        return new \Pusher\Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'host' => gethostname(),
                'port' => 6001
            ],
        );
    }
}
if (!function_exists('onlineUsers')) {
    function onlineUsers($channelName="presence-user_online_status"){
        $pusher = pusherInstance();
        try {
            $presenceChannel = $pusher->get_users_info($channelName);
            return collect($presenceChannel->users);
        }catch (\Pusher\PusherException $exception){
            return false;
        }
    }
}
