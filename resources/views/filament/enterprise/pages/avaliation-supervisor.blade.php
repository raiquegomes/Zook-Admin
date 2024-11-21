<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Informações
        </x-slot>
        <form wire:submit.prevent="updatedSelectedUser">
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.blur="selectedUser" id="selectedUser">
                    <option value="">Selecione um Colaborador</option>
                    @foreach (App\Models\User::all() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </form>
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">
            Atividades Feitas no dia {{ \Carbon\Carbon::yesterday()->format('d/m/Y') }}
        </x-slot>
        @if ($activitiesMade && count($activitiesMade) > 0)
            <div class="overflow-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr>
                            <th class="p-2 text-left">Nome da Atividade</th>
                            <th class="p-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activitiesMade as $activity)
                            <tr>
                                <td class="p-2">{{ $activity->activity->title }}</td>
                                <td class="p-2">Concluída</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>Nenhuma atividade feita no dia anterior.</p>
        @endif
    </x-filament::section>
</x-filament-panels::page>
