<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiaristaRequest;
use App\Models\Diarista;
use App\Services\ViaCEP;
use Illuminate\Http\Request;

class DiaristasController extends Controller
{
    public function __construct(
        protected ViaCEP $viaCEP
    ) {
    }

    /**
     * lista as diaristas
     *
     * @return void
     */
    public function index()
    {
        $diaristas = Diarista::get();

        return view('index', ['diaristas' => $diaristas]);
    }

    /**
     * Mostra o formulário de criação
     *
     * @return void
     */
    public function create()
    {
        return view('create');
    }

    /**
     * Cria uma diarista no Banco de Dados
     *
     * @param Request $request
     * @return void
     */
    public function store(DiaristaRequest $request)
    {
        $dados = $request->except('_token');
        $dados['foto_usuario'] = $request->foto_usuario->store('public');

        $dados['cpf'] = str_replace(['.', '-'], '', $dados['cpf']);
        $dados['cep'] = str_replace(['-'], '', $dados['cep']);
        $dados['telefone'] = str_replace(['(', ')', ' ', '-'], '', $dados['telefone']);
        $dados['codigo_ibge'] = $this->viaCEP->buscar($dados['cep'])['ibge'];

        Diarista::create($dados);

        return redirect()->route('diaristas.index');
    }

    /**
     * Mostra o formulario de edição populado
     *
     * @param integer $id
     * @return void
     */
    public function edit(int $id)
    {
        $diarista = Diarista::findOrFail($id);

        return view('edit', [
            'diarista' => $diarista
        ]);
    }

    /**
     * Atualiza uma diarista no banco de dados
     *
     * @param integer $id
     * @param Request $request
     * @return void
     */
    public function update(int $id, DiaristaRequest $request)
    {
        $diarista = Diarista::findOrFail($id);

        $dados = $request->except(['_token', '_metod']);

        $dados['cpf'] = str_replace(['.', '-'], '', $dados['cpf']);
        $dados['cep'] = str_replace(['-'], '', $dados['cep']);
        $dados['telefone'] = str_replace(['(', ')', ' ', '-'], '', $dados['telefone']);
        $dados['codigo_ibge'] = $this->viaCEP->buscar($dados['cep'])['ibge'];

        if ($request->hasFile('foto_usuario')) {
            $dados['foto_usuario'] = $request->foto_usuario->store('public');
        }

        $diarista->update($dados);

        return redirect()->route('diarista.index');
    }

    /**
     * Apaga uma diarista no banco de dados
     *
     * @param integer $id
     * @return void
     */
    public function destroy(int $id)
    {
        $diarista = Diarista::findOrFail($id);

        $diarista->delete();

        return redirect()->route('diaristas.index');
    }
}
