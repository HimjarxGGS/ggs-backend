<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use Filament\Forms\Get;
use Illuminate\Support\Str;

use App\Filament\Resources\BlogResource\RelationManagers\CategoriesRelationManager;
use App\Models\Blog;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Set;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['pic'] = Auth::id();

        return $data;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255),

                Select::make('category')
                    ->multiple()
                    ->relationship(name: 'categories', titleAttribute: 'category_name')
                    ->createOptionForm([
                        TextInput::make('category_name')
                            ->required(),
                    ]),



                FileUpload::make('image')
                    ->label("Main Image For Blog")
                    ->image()
                    ->disk('public')
                    ->directory('blog-thumbnail')
                    ->columnSpanFull(),
                MarkdownEditor::make('content')
                    ->label("Blog Content")
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Section::make('Publishing')
                    ->description('Settings for publishing this post.')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'reviewing' => 'Reviewing',
                                'published' => 'Published',
                            ])->required()->live(),
                        DateTimePicker::make('date_published_at')
                            ->hidden(fn(Get $get) => $get('status') !== 'published'),
                    ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            CategoriesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}
