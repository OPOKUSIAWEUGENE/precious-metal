<?php

namespace App\Http\Controllers;

use App\Support\ApiDocs\ApiDocumentationRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ApiDocumentationController extends Controller
{
    public function __construct(private readonly ApiDocumentationRepository $documentations)
    {
    }

    public function index(): View
    {
        return view('docs.index', [
            'documentations' => $this->documentations->all(),
        ]);
    }

    public function show(string $documentation): View|RedirectResponse
    {
        $doc = $this->documentations->find($documentation);

        abort_if(! $doc, 404);

        return view('docs.show', [
            'documentations' => $this->documentations->all(),
            'documentation' => $doc,
        ]);
    }
}
