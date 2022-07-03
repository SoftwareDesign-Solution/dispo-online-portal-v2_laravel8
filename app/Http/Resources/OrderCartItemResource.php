<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderCartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $original = parent::toArray($request);

        if ($this->Order !== null) {

            $original = array_merge($original, [
                'Auftragsdatum' => $this->Order->Auftragsdatum,
                'PaketNr' => $this->Order->PaketNr,
                'FahrtNr' => $this->Order->FahrtNr,
                'Ab_Zeit' => $this->Order->Ab_Zeit,
                'Ab_Ort' => $this->Order->Ab_Ort,
                'An_Zeit' => $this->Order->An_Zeit,
                'An_Ort' => $this->Order->An_Ort,
                'Bs' => $this->Order->Bs,
                'Ab_DatumUhrzeit' => $this->Order->Auftragsdatum . 'T' . $this->Order->Ab_Zeit,
                'An_DatumUhrzeit' => $this->Order->Auftragsdatum . 'T' . $this->Order->An_Zeit,
            ]);

        }

        return $original;

        /*
         * "id":31676,
         * "Knr":1425860,
         * "PaketNr":1281512,
         * "Vorschlagsdatum":"2022-02-23",
         * "Auslagen":"5.00",
         * "Honorar":"21.250000",
         * "Type":"PastOrders",
         * "ordercart_id":7097
         */
    }
}
