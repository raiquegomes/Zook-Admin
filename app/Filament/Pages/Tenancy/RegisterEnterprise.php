<?php

namespace App\Filament\Pages\Tenancy;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Models\EnterpriseUser;
use App\Models\Enterprise;
use App\Models\User;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Filament\Forms\Components\TextInput;

use JaOcero\RadioDeck\Forms\Components\RadioDeck;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
 
class RegisterEnterprise extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Cadastro da Empresa';
    }
 
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                RadioDeck::make('user_type')
                ->label('Selecione o tipo de usuario')
                ->options([
                    'enterprise' => 'Empresa',
                    'collaborator' => 'Colaborador',
                ])
                ->descriptions([
                    'enterprise' => 'Desejo gerenciar atividades e pessoas para minha empresa.',
                    'collaborator' => 'Vou participar de uma equipe',
                ])
                ->icons([
                    'enterprise' => 'heroicon-m-building-office-2',
                    'collaborator' => 'heroicon-m-user-group',
                ])
                ->required()
                ->iconSize(IconSize::Large) // Small | Medium | Large | (string - sm | md | lg)
                ->iconSizes([ // Customize the values for each icon size
                    'sm' => 'h-12 w-12',
                    'md' => 'h-14 w-14',
                    'lg' => 'h-16 w-16',
                ])
                ->alignment(Alignment::Center) // Start | Center | End | (string - start | center | end)
                ->gap('gap-6') // Gap between Icon and Description (Any TailwindCSS gap-* utility)
                ->padding('px-6') // Padding around the deck (Any TailwindCSS padding utility)
                ->direction('row') // Column | Row (Allows to place the Icon on top)
                ->extraCardsAttributes([ // Extra Attributes to add to the card HTML element
                    'class' => 'flex flex-col items-center text-center rounded-xl'
                ])
                ->extraOptionsAttributes([ // Extra Attributes to add to the option HTML element
                    'class' => 'text-3xl p-4'
                ])
                ->extraDescriptionsAttributes([ // Extra Attributes to add to the description HTML element
                    'class' => 'text-sm font-light text-center'
                ])
                ->color('primary') // supports all color custom or not
                ->reactive() // Faz com que o campo seja reativo a mudanças
                ->afterStateUpdated(fn ($state, callable $set) => $set('user_type', $state)),

                // Campos que serão exibidos dependendo da escolha do 'user_type'
                TextInput::make('company_name')
                    ->label('Nome da Empresa')
                    ->required()
                    ->visible(fn (Get $get) => $get('user_type') === 'ds'), // Usando 'Get' em vez de 'Closure'
    
                TextInput::make('company_address')
                    ->label('Endereço da Empresa')
                    ->required()
                    ->visible(fn (Get $get) => $get('user_type') === 'ds'),
    
                TextInput::make('token')
                    ->label('Digite o codigo do convite da empresa')
                    ->required()
                    ->visible(fn (Get $get) => $get('user_type') === 'collaborator'),
                ]);
    }
 
    protected function handleRegistration(array $data): Enterprise
    {
        if ($data['user_type'] === 'enterprise') {
            // 1. Criar a empresa
            $enterprise = Enterprise::create([
                'cnpj' => $data['cnpj'],
                'name' => $data['name'],
                'fantasy_name' => $data['fantasy_name'],
                'email' => $data['email'],
            ]);
    
            // 2. Criar o usuário mestre
            $masterUser = User::create([
                'name' => $data['master_user_name'],
                'email' => $data['master_user_email'],
                'password' => Hash::make($data['master_user_password']),
                'enterprise_id' => $enterprise->id, // Associando o usuário à empresa diretamente
            ]);
    
            // 3. Verificar e criar as permissões
            Permission::firstOrCreate(['name' => 'manage_users', 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => 'view_reports', 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => 'edit_enterprise_details', 'guard_name' => 'web']);
    
            // 4. Atribuir o papel de administrador ao usuário mestre
            $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
            $masterUser->assignRole($role);
    
            // 5. Dar permissões especiais ao usuário mestre
            $masterUser->givePermissionTo(['manage_users', 'view_reports', 'edit_enterprise_details']);
    
            // 6. Vincular o usuário à empresa através da relação many-to-many, se necessário
            $enterprise->members()->attach($masterUser);
    

            $this->redirect('/enterprise');
        } elseif ($data['user_type'] === 'collaborator') {
            // 1. Verificar se existe um convite válido
            $enterprise = Enterprise::where('token_invitation', $data['token'])->first();
    
            if (!$enterprise) {
                Notification::make()
                    ->title('Convite Inválido')
                    ->danger()
                    ->body('O código de convite fornecido é inválido.')
                    ->send();
            }

            $user = auth()->user();
            $enterprise->members()->attach($user->id);
    
            Notification::make()
            ->title('Convite Aceito')
            ->success()
            ->body('Você agora faz parte da empresa!')
            ->send();

            $this->redirect('/collaborator');
        }
        return $enterprise;
    }
}