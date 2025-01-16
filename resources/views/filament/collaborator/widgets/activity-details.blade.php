<div>
    <h2 class="text-lg font-bold">Título da Atividade</h2>
    <p>{{ $userActivity->activity->title }}</p>

    <h2 class="mt-4 text-lg font-bold">Descrição</h2>
    <p>{{ $userActivity->activity->description }}</p>

    <h2 class="mt-4 text-lg font-bold">Usuário</h2>
    <p>{{ $userActivity->user->name }}</p>

    <h2 class="mt-4 text-lg font-bold">Data</h2>
    <p><p>{{ $userActivity->assigned_date }}</p></p>

    <h2 class="mt-4 text-lg font-bold">Status</h2>
    <p>{{ $userActivity->status }}</p>

    <h2 class="mt-4 text-lg font-bold">Observação</h2>
    <p>{{ $userActivity->observation }}</p>

    <h2 class="mt-4 text-lg font-bold">Outros Detalhes</h2>
    <p>Adicione aqui mais informações relevantes.</p>

    <h2 class="mt-4 text-lg font-bold">Arquivos</h2>
    @if(!empty($userActivity->attachments))
        <ul class="pl-5 list-disc">
            @foreach ($userActivity->attachments as $file)
                <li>
                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="text-blue-600 underline">
                        {{ basename($file) }}
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p>Não há arquivos anexados.</p>
    @endif
</div>
