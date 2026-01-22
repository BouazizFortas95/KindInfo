# Using `astrotomic/laravel-translatable` with Filament

## Can it work?
**YES.** You can use this library with Filament.

## ⚠️ Important Consideration
Filament has **native support** for `spatie/laravel-translatable` (which uses JSON columns). If you use Spatie, Filament gives you a magical language switcher menu right above your form fields.

If you use **Astrotomic** (which uses separate translation tables like `post_translations`), Filament **does NOT** support the automatic field switcher out of the box.

## How to use Astrotomic in Filament
If you choose Astrotomic, the best practice is to use **Tabs** in your Form or a Plugin:

### Option 1: Tabs (Manual Setup)
You create tabs for each language in your Resource Form:
```php
Tabs::make('Translations')->tabs([
    Tabs\Tab::make('English')->schema([
        TextInput::make('en.title')->label('Title'),
        Textarea::make('en.description')->label('Description'),
    ]),
    Tabs\Tab::make('Arabic')->schema([
        TextInput::make('ar.title')->label('العنوان'),
        Textarea::make('ar.description')->label('الوصف'),
    ]),
])
```

### Option 2: Community Plugins
There are plugins (like `outerweb/filament-translatable-fields`) that add support for separate-table translations to Filament.

## My Recommendation
- **Use Spatie (JSON)**: If you want the easiest, cleanest Filament UI (Language dropdown per field).
- **Use Astrotomic (Tables)**: If you strictly need normalized database tables for SQL performance/structure.

**Do you want me to install one of these and set up a demo?**
