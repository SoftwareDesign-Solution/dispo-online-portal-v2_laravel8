<?php

namespace App\Http\Resources;

use App\Models\ShiftDay;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $project = null;


        $project = $this->Project;

        $original = parent::toArray($request);

        $oShiftDays = ShiftDay::where('Projekt_Nr', '=', $this->Projekt_Knr)
            ->where('TeilnetzNr', '=', $this->TeilnetzNr)
            ->where('Schichttag', '=', $this->Schichttag)
            ->whereDate('Datum', '>=', Carbon::today()->addDays($project->Delta))
            ->orderBy('Datum')
            ->get();
            //->unique('Datum')
            //->pluck('Datum');

        return [
            'id' => $this->id,
            'Knr' => $this->Knr,
            'Projekt_Knr' => $this->Projekt_Knr,
            'Projekt' => $this->Projekt,
            'PaketNr' => $this->PaketNr,
            'Auftragsdatum' => $this->Auftragsdatum,
            'Schichttag' => $this->Schichttag,
            'Ab_Zeit' => $this->Ab_Zeit,
            'Ab_Ort' => $this->Ab_Ort,
            'An_Zeit' => $this->An_Zeit,
            'An_Ort' => $this->An_Ort,
            'Bs' => $this->Bs,
            'Dauer' => $this->Dauer,
            'AnzahlPers' => $this->AnzahlPers,
            'Datumsvorschlag' => $project->Datumsvorschlag,
            'Delta' => $project->Delta,
            'Delta_Vorlauf' => $project->Delta_Vorlauf,
            'ObergrenzeAuslagen' => $project->ObergrenzeAuslagen,
            'Auslagen' => 0,
            'Vorschlagsdatum' => '',
            'Ab_DatumUhrzeit' => $this->Auftragsdatum . 'T' . $this->Ab_Zeit,
            'An_DatumUhrzeit' => $this->Auftragsdatum . 'T' . $this->An_Zeit,
            'Schichttage' => ShiftDayResource::collection($oShiftDays),
            'OrderFilter' => new OrderFilterResource($this->OrderFilter)
        ];

        /*
        return array_merge($original, [
            'Datumsvorschlag' => $project->Datumsvorschlag,
            'Delta' => $project->Delta,
            'Delta_Vorlauf' => $project->Delta_Vorlauf,
            'ObergrenzeAuslagen' => $project->ObergrenzeAuslagen,
            'Auslagen' => 0,
            'Vorschlagsdatum' => '',
            'Ab_DatumUhrzeit' => $this->Auftragsdatum . 'T' . $this->Ab_Zeit,
            'An_DatumUhrzeit' => $this->Auftragsdatum . 'T' . $this->An_Zeit,
            'Schichttage' => ShiftDayResource::collection($oShiftDays)
        ]);
        */

    }

}
