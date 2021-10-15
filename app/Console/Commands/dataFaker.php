<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class dataFaker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:faker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            \App\Models\User::factory(30)->create();
            $this->info("data created");
        } catch (\Throwable $th) {
            $this->error('Error msj => '.$th->getMessage().' --//-- Linea => '.$th->getLine().' --//-- file_name => '.$th->getFile());
        }
    }
}
