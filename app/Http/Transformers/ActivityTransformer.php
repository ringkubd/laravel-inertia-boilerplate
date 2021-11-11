<?php

namespace App\Http\Transformers;

class ActivityTransformer extends \League\Fractal\TransformerAbstract
{
    public function transform(Activity $activity)
    {
        return [
            "description" => call_user_func_array([$this, $activity->name], [$activity]),
            "lapse" => $activity->created_at->diffForHumans(),
            "user" => $activity->user,
        ];
    }
}
