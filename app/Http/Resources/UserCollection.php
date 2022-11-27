<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->fname??"",
            'last_name'=> $this->lname??"",
            'email'=> $this->email??"",
            'phone'=> $this->phone??"",
            'address'=> $this->address??"",
            'date_of_birth'=> $this->dob??"",
            'profile_image'=> $this->img?? "",
            'category' => $this->category,
            'designation' => $this->designation,
        ];
    }
}
