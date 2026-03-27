<?php 

namespace App\Modules\v0\Operator\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class OperatorResource extends JsonResource{
	/**
	* Transform the resource into an array. 
	*
	* @param \Illuminate\Http\Request $request 
	*
                    @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	*/
	public function toArray($request)
	{
		return [
            'id' => $this->id,
            'name' => $this->name,
            'name_alias' => $this->name_alias,
            'operator_type' => $this->operator_type,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'logo_url' => $this->logo_url,
            'profile' => $this->profile,
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
	}
}