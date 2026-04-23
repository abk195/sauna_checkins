<?php

namespace Database\Seeders;

use App\Models\Manifest;
use Illuminate\Database\Seeder;

class ManifestSeeder extends Seeder
{
    /**
     * Default manifests (previously in config/periode.php).
     *
     * @var list<array{manifest_id: string, name: string}>
     */
    protected array $rows = [
        ['manifest_id' => 'Lf2AceUrOd5cs5smm7gs', 'name' => '1 time privat badstue'],
        ['manifest_id' => 'yorjeR1kN9n0mDvptsBn', 'name' => 'Aufguss'],
        ['manifest_id' => 'FzOu845QA8Bg806Cjhng', 'name' => 'Badsturituale'],
        ['manifest_id' => '1z0bfo5tQKnXPGXNPsNM', 'name' => 'Badstuyoga'],
        ['manifest_id' => 'STIn4fLdu0ix8rX0Dy4o', 'name' => 'Eventbadstumester'],
        ['manifest_id' => 'dqrMODfWL7itkphEu7Rl', 'name' => 'Felles badstue'],
        ['manifest_id' => 'fCXdO6fFZOWZuy8ZTiFw', 'name' => 'Issvømming'],
        ['manifest_id' => 'LU7ODLjxA0gxUmB5MKTR', 'name' => 'Privat 1,5 time'],
    ];

    public function run(): void
    {
        foreach ($this->rows as $row) {
            Manifest::query()->updateOrCreate(
                ['manifest_id' => $row['manifest_id']],
                ['name' => $row['name']]
            );
        }
    }
}
