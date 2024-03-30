<?php
    
namespace Database\Seeds;
use Carbon\Carbon;
use Database\AbstractSeeder;

require_once 'vendor/autoload.php';
use Faker;

class ImagesSeeder extends AbstractSeeder {

    // TODO: tableName文字列を割り当ててください。
    protected ?string $tableName = 'images';

    // TODO: tableColumns配列を割り当ててください。
    protected array $tableColumns = [[
        'data_type' => 'int',
        'column_name' => 'id'
    ],
    [
        'data_type' => 'string',
        'column_name' => 'title'
    ],
    [
        'data_type' => 'string',
        'column_name' => 'file_path'
    ],
    [
        'data_type' => 'string',
        'column_name' => 'file_name'
    ],
    [
        'data_type' => 'string',
        'column_name' => 'url'
    ],
    [
        'data_type' => 'string',
        'column_name' => 'delete_url'
    ],
    [
        'data_type' => 'int',
        'column_name' => 'view_count'
    ],
    [
        'data_type' => 'string',
        'column_name' => 'mine_type'
    ],
    [
        'data_type' => 'string',
        'column_name' => 'expired_date'
    ],
    [
        'data_type' => 'int',
        'column_name' => 'size'
    ],
    [
        'data_type' => 'string',
        'column_name' => 'uploaded_at'
    ]
];

    public function createRowData(): array
    {$faker = Faker\Factory::create('ja_JP');
        $data = [];
        for($i=0; $i<10; $i++){
            $data[]=[
                $i+1,
                $faker->name,
                $faker->name,
                $faker->name,
                $faker->name,
                $faker->name,
                3,
                $faker->name,
                $faker->date(),
                3,
                $faker->date()

            ];

        }

        return $data;
    }
}
