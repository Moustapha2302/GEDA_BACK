*<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use App\Models\User;
    use Illuminate\Support\Facades\Hash;

    class UsersSeeder extends Seeder
    {
        public function run()
        {
            $users = [
                [
                    'name' => 'Maire de Ziguinchor',
                    'email' => 'maire@ziguinchor.sn',
                    'password' => Hash::make('123456'),
                    'role' => 'maire',
                    'service_code' => null,
                ],
                [
                    'name' => 'Secrétaire Général',
                    'email' => 'sg@ziguinchor.sn',
                    'password' => Hash::make('123456'),
                    'role' => 'sg',
                    'service_code' => null,
                ],
                [
                    'name' => 'Chef Service État Civil',
                    'email' => 'chef.s01@ziguinchor.sn',
                    'password' => Hash::make('123456'),
                    'role' => 'chef_service',
                    'service_code' => 'S01',
                ],
                [
                    'name' => 'Agent État Civil',
                    'email' => 'agent.s01@ziguinchor.sn',
                    'password' => Hash::make('123456'),
                    'role' => 'agent',
                    'service_code' => 'S01',
                ],
            ];

            foreach ($users as $userData) {
                User::updateOrCreate(
                    ['email' => $userData['email']],
                    $userData
                );
            }

            $this->command->info('✅ Utilisateurs créés avec succès !');
        }
    }
