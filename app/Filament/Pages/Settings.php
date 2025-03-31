<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use App\Models\Setting;

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
    public ?string $locale = 'en';
    
    protected function getFormStatePath(): ?string
    {
        return 'data';
    }
    
    public function mount(): void
    {
        $this->locale = request()->query('locale', app()->getLocale());
        
        $this->form->fill([
            'about_title' => Setting::getValue('about_title', 'About Me', $this->locale),
            'about_content' => Setting::getValue('about_content', '', $this->locale),
            'locale' => $this->locale,
        ]);
    }
    
    public function form(Form $form): Form
    {
        $localeOptions = config('app.available_locales', ['en' => 'English']);
        
        return $form
            ->schema([
                TextInput::make('about_title')
                    ->label('About Page Title')
                    ->required(),
                MarkdownEditor::make('about_content')
                    ->label('About Page Content')
                    ->required()
                    ->columnSpanFull(),
                \Filament\Forms\Components\Select::make('locale')
                    ->label('Language')
                    ->options($localeOptions)
                    ->default(app()->getLocale())
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        // When locale changes, fetch content for that locale
                        $title = Setting::getValue('about_title', 'About Me', $state);
                        $content = Setting::getValue('about_content', '', $state);
                        
                        $set('about_title', $title);
                        $set('about_content', $content);
                    }),
            ]);
    }
    
    public function save(): void
    {
        $data = $this->form->getState();
        
        Setting::setValue(
            'about_title', 
            $data['about_title'], 
            'string', 
            'About page title',
            $data['locale']
        );
        
        Setting::setValue(
            'about_content', 
            $data['about_content'], 
            'string', 
            'About page content',
            $data['locale']
        );
        
        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
