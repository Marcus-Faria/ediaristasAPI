<?php

namespace App\Http\Controllers;

use App\Models\Diarista;
use App\Services\ViaCEP;
use Illuminate\Http\Request;

class BuscaDiaristaCep extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, ViaCEP $viaCEP)
    {
        //entrada -> cep
        //saida -> codigo ibge
        
        $endereço = $viaCEP->buscar($request->cep);

        //Verificação

        if ($endereço === false) {
            return response()->json(['erro'=>'CEP inválido'], 400);
        }

        //entrada -> código ibge
        //saida -> lista de diaristas filtradas pelo código

        return [
            'diaristas' => Diarista::buscaPorCodigoIbge($endereço['ibge']),
            'quantidade_diarista' => Diarista::quantidadePorCodigoIbge($endereço['ibge'])
        ];
    }
}
