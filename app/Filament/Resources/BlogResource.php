<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use Filament\Forms\Get;
use Illuminate\Support\Str;
use App\Models\Blog;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Set;
use Filament\Tables;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static $formActionsAlignment = Alignment::Right;

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
                TagsInput::make('tag')->required()
                    ->hint("Masukkan Setidaknya 3 tag")
                    ->nestedRecursiveRules([
                        'max:255',
                    ]),
                Select::make('category')
                    ->required()
                    ->searchable()
                    ->multiple()
                    ->relationship(name: 'categories', titleAttribute: 'category_name')
                    ->createOptionForm([
                        TextInput::make('category_name')
                            ->required(),
                    ])->columnSpanFull(),


                FileUpload::make('img')
                    ->label("Main Image For Blog")
                    ->image()
                    ->required()
                    ->disk('public')
                    ->directory('blog-thumbnail')
                    ->columnSpanFull()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ]),
                RichEditor::make('content')
                    ->label("Blog Content")
                    ->required()

                    ->fileAttachmentsDirectory('attachments')
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('author')
                    ->required()
                    ->maxLength(255)->columnSpanFull(),
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
                    ]),
                TextInput::make('pic')->required()->default(fn() => Filament::auth()->user()->id)->hidden(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('img'),
                TextColumn::make('title'),
                TextColumn::make('author'),
                TextColumn::make('categories.category_name'),
                TextColumn::make('createdBy.name')->label("Created By"),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'reviewing' => 'warning',
                        'published' => 'success',
                        'rejected' => 'danger',
                    })->icons([
                        'heroicon-o-x',
                        'heroicon-o-document' => static fn($state): bool => $state === 'draft',
                        'heroicon-o-refresh' => static fn($state): bool => $state === 'reviewing',
                        'heroicon-o-truck' => static fn($state): bool => $state === 'published',
                    ]),

                TextColumn::make('created_at')
                    ->since()
                    ->dateTimeTooltip(),
                TextColumn::make('date_published_at')
                    ->label('Published At')
                    ->since()
                    ->dateTimeTooltip(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Table\Actions\::make().
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->after(function (Blog $record) {
                    // delete single
                    if ($record->img) {
                        Storage::disk('public')->delete($record->img);
                    }
                    // delete multiple
                    if ($record->galery) {
                        foreach ($record->galery as $ph) Storage::disk('public')->delete($ph);
                    }
                }),
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
            // CategoriesRelationManager::class
        
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
