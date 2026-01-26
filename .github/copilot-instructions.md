# AI Coding Agent Instructions for Aescala Laravel Project

## Project Overview
This is a Laravel 11 project management system for construction/home improvement projects. It manages clients, projects, deliverables, payments, dispatches, tools, materials, and generates contracts/PDFs.

## Architecture
- **Backend**: Laravel 11 with Eloquent models for projects, deliverables, users, etc.
- **Frontend**: Blade templates with Tailwind CSS, Alpine.js for interactivity
- **Database**: MySQL with custom timestamps (`createdAt`, `updatedAt`)
- **Document Generation**: DomPDF for PDFs, PhpSpreadsheet/PhpWord for Excel/Word, Python script with docxtpl + LibreOffice for contracts
- **Testing**: Pest framework
- **Build**: Vite for asset compilation

## Key Conventions
- **Routes**: Grouped by prefix (e.g., `Route::prefix('proyecto')->name('proyecto.')`) with standard CRUD patterns
- **Models**: Use custom timestamps; relationships like `hasMany` for deliverables/payments; accessors for formatted data (e.g., `getSpanEstadoAttribute`)
- **Controllers**: Handle CRUD, PDF generation, email sending; use transactions for saves
- **Views**: Extend `layouts.app`; use Tailwind classes; forms with CSRF; iterate with `@forelse`
- **Config**: Stored in models like `ConfigAdicionales`, `ValorArea` with year-based versioning
- **Emails**: Use `Mail` classes (e.g., `FirmaContratoMail`) for notifications
- **Python Integration**: Call `scripts/generar_contrato.py` via subprocess for contract generation, passing JSON data

## Development Workflow
- **Setup**: `composer install`, `npm install`, `npm run dev` for assets, `php artisan serve`
- **Database**: Use migrations; seeders in `database/seeders/`
- **Testing**: Run `vendor/bin/pest` or `./vendor/bin/pest`
- **Build**: `npm run build` compiles assets; Docker builds include asset compilation
- **Deployment**: Uses `start.sh` for Render; clears config/cache on start

## Examples
- **Model Relationship**: `Proyecto::hasMany(EntregableProye::class)` for project deliverables
- **Route Definition**: `Route::get('/contratoPdf/{id}', [ProyectoController::class, 'contratoPdf'])`
- **View Form**: `<x-text-input name="items[{{ $loop->index }}][area_min]" :value="$item['area_min']" />`
- **PDF Generation**: Use `DomPDF` facade in controllers for reports
- **Contract Creation**: Pipe JSON to Python script: `echo $json | python scripts/generar_contrato.py`

## Integration Points
- **External APIs**: None apparent; uses local JSON files (e.g., `storage/json/jsonCityColombia.json`)
- **Email**: Laravel Mail with custom templates
- **File Handling**: Store in `storage/`; generate temp files for conversions
- **Cross-Component**: Projects link to users (commercial, obra_blanca, carpinteria), areas, phases

Focus on maintaining data integrity with transactions, proper error handling, and following Laravel conventions for new features.