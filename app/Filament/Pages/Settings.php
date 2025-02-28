<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Site Settings';
    protected static ?string $title = 'Site Settings';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 90;

    protected static string $view = 'filament.pages.settings';
    
    public ?string $about_title = '';
    public ?string $about_content = '';
    
    public function mount(): void
    {
        $this->form->fill([
            'about_title' => Cache::get('about_title', 'About Me'),
            'about_content' => Cache::get('about_content', ''),
        ]);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('about_title')
                    ->label('About Page Title')
                    ->required(),
                MarkdownEditor::make('about_content')
                    ->label('About Page Content')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
    
    public function save(): void
    {
        $data = $this->form->getState();
        
        Cache::forever('about_title', $data['about_title']);
        Cache::forever('about_content', $data['about_content']);
        
        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
