<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderFilterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'Projekt_Knr' => $this->Projekt_Knr,
            'PaketNr' => $this->PaketNr,
            'Auftragsdatum' => $this->Auftragsdatum,
            'Ab_Ort' => $this->Ab_Ort,
            'Ab_Zeit' => $this->Ab_Zeit,
            'An_Ort' => $this->An_Ort,
            'An_Zeit' => $this->An_Zeit,
            'Ab_DatumUhrzeit' => $this->Auftragsdatum . 'T' . $this->Ab_Zeit,
            'An_DatumUhrzeit' => $this->Auftragsdatum . 'T' . $this->An_Zeit,
        ];
    }
}
